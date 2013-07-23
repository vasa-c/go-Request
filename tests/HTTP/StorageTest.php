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
    );

    /**
     * @covers go\Request\HTTP\Storage::getAllVars
     */
    public function testGetAllVars()
    {
        $storage = new Storage($this->vars);
        $this->assertEquals($this->vars, $storage->getAllVars());
    }
}
