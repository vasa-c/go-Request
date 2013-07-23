<?php
/**
 * Test of Storage class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Storage;

/**
 * @covers go\Request\HTTP\Storage
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $vars = array(
        'empty' => '',
        'scalar' => 'This is string',
        'float' => '-123.456',
        'int' => '-123',
        'uint' => '0',
        'id' => '123',
        'list' => array('1', '2', '3'),
        'list-tree' => array('1', array('2'), '3'),
        'dict' => array('x' => '1', 'y' => '2'),
        'dict-tree' => array('x' => '1', 'y' => array('2')),
    );

    /**
     * @covers go\Request\HTTP\Storage::getAllVars
     */
    public function testGetAllVars()
    {
        $storage = new Storage($this->vars);
        $this->assertEquals($this->vars, $storage->getAllVars());
    }

    /**
     * @covers go\Request\HTTP\Storage::exists
     */
    public function testExists()
    {
        $storage = new Storage($this->vars);

        $this->assertTrue($storage->exists('empty'));
        $this->assertTrue($storage->exists('scalar'));
        $this->assertTrue($storage->exists('float'));
        $this->assertTrue($storage->exists('id'));
        $this->assertFalse($storage->exists('list'));
        $this->assertFalse($storage->exists('dict'));
        $this->assertFalse($storage->exists('unk'));

        $this->assertFalse($storage->exists('empty', 'float'));
        $this->assertFalse($storage->exists('scalar', 'float'));
        $this->assertTrue($storage->exists('float', 'float'));
        $this->assertTrue($storage->exists('int', 'float'));
        $this->assertTrue($storage->exists('uint', 'float'));
        $this->assertTrue($storage->exists('id', 'float'));
        $this->assertFalse($storage->exists('list', 'float'));
        $this->assertFalse($storage->exists('dict', 'float'));
        $this->assertFalse($storage->exists('unk', 'float'));

        $this->assertFalse($storage->exists('empty', 'int'));
        $this->assertFalse($storage->exists('scalar', 'int'));
        $this->assertFalse($storage->exists('float', 'int'));
        $this->assertTrue($storage->exists('int', 'int'));
        $this->assertTrue($storage->exists('uint', 'int'));
        $this->assertTrue($storage->exists('id', 'int'));
        $this->assertFalse($storage->exists('list', 'int'));
        $this->assertFalse($storage->exists('dict', 'int'));
        $this->assertFalse($storage->exists('unk', 'int'));

        $this->assertFalse($storage->exists('empty', 'uint'));
        $this->assertFalse($storage->exists('scalar', 'uint'));
        $this->assertFalse($storage->exists('float', 'uint'));
        $this->assertFalse($storage->exists('int', 'uint'));
        $this->assertTrue($storage->exists('uint', 'uint'));
        $this->assertTrue($storage->exists('id', 'uint'));
        $this->assertFalse($storage->exists('list', 'uint'));
        $this->assertFalse($storage->exists('dict', 'uint'));
        $this->assertFalse($storage->exists('unk', 'uint'));

        $this->assertFalse($storage->exists('empty', 'id'));
        $this->assertFalse($storage->exists('scalar', 'id'));
        $this->assertFalse($storage->exists('float', 'id'));
        $this->assertFalse($storage->exists('int', 'id'));
        $this->assertFalse($storage->exists('uint', 'id'));
        $this->assertTrue($storage->exists('id', 'id'));
        $this->assertFalse($storage->exists('list', 'id'));
        $this->assertFalse($storage->exists('dict', 'id'));
        $this->assertFalse($storage->exists('unk', 'id'));

        $this->assertFalse($storage->exists('empty', 'list'));
        $this->assertFalse($storage->exists('int', 'list'));
        $this->assertTrue($storage->exists('list', 'list'));
        $this->assertFalse($storage->exists('list-tree', 'list'));
        $this->assertFalse($storage->exists('dict', 'list'));
        $this->assertFalse($storage->exists('dict-tree', 'list'));
        $this->assertFalse($storage->exists('unk', 'list'));

        $this->assertFalse($storage->exists('empty', 'dict'));
        $this->assertFalse($storage->exists('int', 'dict'));
        $this->assertTrue($storage->exists('list', 'dict'));
        $this->assertFalse($storage->exists('list-tree', 'dict'));
        $this->assertTrue($storage->exists('dict', 'dict'));
        $this->assertFalse($storage->exists('dict-tree', 'dict'));
        $this->assertFalse($storage->exists('unk', 'dict'));

        $this->assertFalse($storage->exists('empty', 'array'));
        $this->assertFalse($storage->exists('int', 'array'));
        $this->assertTrue($storage->exists('list', 'array'));
        $this->assertTrue($storage->exists('list-tree', 'array'));
        $this->assertTrue($storage->exists('dict', 'array'));
        $this->assertTrue($storage->exists('dict-tree', 'array'));
        $this->assertFalse($storage->exists('unk', 'array'));

        $this->assertTrue($storage->exists('empty', 'mixed'));
        $this->assertTrue($storage->exists('int', 'mixed'));
        $this->assertTrue($storage->exists('list', 'mixed'));
        $this->assertTrue($storage->exists('list-tree', 'mixed'));
        $this->assertTrue($storage->exists('dict', 'mixed'));
        $this->assertTrue($storage->exists('dict-tree', 'mixed'));
        $this->assertFalse($storage->exists('unk', 'mixed'));

        $this->setExpectedException('InvalidArgumentException');
        $storage->exists('empty', 'invalid');
    }

    /**
     * @covers go\Request\HTTP\Storage::get
     */
    public function testGet()
    {
        $storage = new Storage($this->vars);

        $this->assertSame('-123', $storage->get('int', 'scalar'));
        $this->assertSame(-123, $storage->get('int', 'int'));
        $this->assertSame(-123, $storage->get('int', 'int', 11));
        $this->assertSame(null, $storage->get('int', 'uint'));
        $this->assertSame(11, $storage->get('int', 'uint', 11));

        $this->assertSame(array('1', '2', '3'), $storage->get('list', 'list'));
        $this->assertSame(null, $storage->get('dict', 'list'));

        $this->assertSame('', $storage->get('empty'));
        $this->assertSame(true, $storage->get('empty', 'check'));
        $this->assertSame(false, $storage->get('unk', 'check'));
        $this->assertSame(null, $storage->get('unk'));
        $this->assertSame('unkdef', $storage->get('unk', null, 'unkdef'));

        $this->setExpectedException('InvalidArgumentException');
        $storage->get('int', 'invalid');
    }
}
