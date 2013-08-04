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

        $expected = array(
            'empty' => '',
            'scalar' => 'This is string',
            'float' => '-123.456',
            'int' => '-123',
            'uint' => '0',
            'id' => '123',
        );
        $this->assertEquals($expected, $storage->getAllVars(true));
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

    /**
     * @covers go\Request\HTTP\Storage::get
     */
    public function testChild()
    {
        $storage = new Storage($this->vars);

        $this->assertNull($storage->child('int'));
        $this->assertNull($storage->child('unk'));

        $child = $storage->child('dict-tree');
        $this->assertInstanceOf('go\Request\HTTP\Storage', $child);
        $this->assertSame(1, $child->get('x', 'int'));

        $this->assertSame($child, $storage->child('dict-tree', true));

        $this->setExpectedException('InvalidArgumentException');
        $storage->child('int', true);
    }

    /**
     * @covers go\Request\HTTP\Storage::__get
     * @covers go\Request\HTTP\Storage::__isset
     * @covers go\Request\HTTP\Storage::__set
     * @covers go\Request\HTTP\Storage::__unset
     */
    public function testMagic()
    {
        $storage = new Storage($this->vars);

        $this->assertSame('', $storage->empty);
        $this->assertSame('This is string', $storage->scalar);
        $this->assertSame('123', $storage->id);
        $this->assertNull($storage->list);
        $this->assertNull($storage->dict);
        $this->assertNull($storage->unknown);

        $this->assertTrue(isset($storage->empty));
        $this->assertTrue(isset($storage->scalar));
        $this->assertTrue(isset($storage->id));
        $this->assertFalse(isset($storage->list));
        $this->assertFalse(isset($storage->dict));
        $this->assertFalse(isset($storage->unknown));

        $this->setExpectedException('LogicException');
        $storage->id = '456';
    }

    /**
     * @covers go\Request\HTTP\Storage::offsetGet
     * @covers go\Request\HTTP\Storage::offsetExists
     * @covers go\Request\HTTP\Storage::offsetSet
     * @covers go\Request\HTTP\Storage::offsetUnset
     */
    public function testArrayAccess()
    {
        $storage = new Storage($this->vars);

        $this->assertSame('', $storage['empty']);
        $this->assertSame('This is string', $storage['scalar']);
        $this->assertSame('123', $storage['id']);
        $this->assertNull($storage['list']);
        $this->assertNull($storage['dict']);
        $this->assertNull($storage['unknown']);

        $this->assertTrue(isset($storage['empty']));
        $this->assertTrue(isset($storage['scalar']));
        $this->assertTrue(isset($storage['id']));
        $this->assertFalse(isset($storage['list']));
        $this->assertFalse(isset($storage['dict']));
        $this->assertFalse(isset($storage['unknown']));

        $this->setExpectedException('LogicException');
        $storage['id'] = '456';
    }

    /**
     * @covers go\Request\HTTP\Storage::count
     */
    public function testCountable()
    {
        $storage = new Storage($this->vars);
        $this->assertCount(6, $storage);
    }

    /**
     * @covers go\Request\HTTP\Storage::getIterator
     */
    public function testIterator()
    {
        $storage = new Storage($this->vars);
        $actual = array();
        foreach ($storage as $k => $v) {
            $actual[$k] = $v;
        }
        $expected = array(
            'empty' => '',
            'scalar' => 'This is string',
            'float' => '-123.456',
            'int' => '-123',
            'uint' => '0',
            'id' => '123',
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers go\Request\HTTP\Storage::__call
     */
    public function testMagicMethods()
    {
        $storage = new Storage($this->vars);
        $this->assertSame('This is string', $storage->scalar('scalar'));
        $this->assertSame(-123, $storage->int('int'));
        $this->assertEquals(array('1', '2', '3'), $storage->list('list'));
        $this->assertEquals(array('1', '2', '3'), $storage->array('list'));
        $this->assertNull($storage->uint('int'));
        $this->assertEquals('xx', $storage->uint('int', 'xx'));
        $this->assertNull($storage->mixed('unknown'));
    }

    /**
     * @covers go\Request\HTTP\Storage::getEnum
     */
    public function testGetEnum()
    {
        $vars = array(
            'one' => 'One',
            'two' => 'Two',
            'three' => array('One', 'Two'),
        );
        $storage = new Storage($vars);
        $allowed = array('Three', 'One', 'Four');
        $this->assertEquals('One', $storage->getEnum('one', $allowed));
        $this->assertNull($storage->getEnum('two', $allowed));
        $this->assertNull($storage->getEnum('three', $allowed));
        $this->assertNull($storage->getEnum('four', $allowed));
    }
}
