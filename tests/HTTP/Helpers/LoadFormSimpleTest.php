<?php
/**
 * Test of Helper\LoadFormSimple class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\LoadFormSimple;

/**
 * @covers go\Request\HTTP\Helpers\LoadFormSimple
 */
class LoadFormSimpleTest extends \PHPUnit_Framework_TestCase
{

    private $vars = array(
        'one' => '1',
        'two' => '2',
        'three' => '3',
        'five' => '5',
    );

    public function testFields()
    {
        $fields = array('one', 'two', 'three');
        $expected = array(
            'one' => '1',
            'two' => '2',
            'three' => '3',
        );
        $actual = LoadFormSimple::load($this->vars, $fields);
        $this->assertEquals($expected, $actual);
    }

    public function testChecks()
    {
        $fields = array('one', 'two');
        $checks = array('three', 'four');
        $expected = array(
            'one' => '1',
            'two' => '2',
            'three' => true,
            'four' => false,
        );
        $actual = LoadFormSimple::load($this->vars, $fields, $checks);
        $this->assertEquals($expected, $actual);
    }

    public function testError()
    {
        $fields = array('one', 'two', 'three', 'four');
        $actual = LoadFormSimple::load($this->vars, $fields);
        $this->assertNull($actual);
    }

    public function testStrict()
    {
        $fields1 = array('one', 'two', 'three');
        $actual1 = LoadFormSimple::load($this->vars, $fields1, null, true);
        $this->assertNull($actual1);

        $fields2 = array('one', 'two', 'three', 'five');
        $actual2 = LoadFormSimple::load($this->vars, $fields2, null, true);
        $expected2 = array(
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'five' => '5',
        );
        $this->assertEquals($expected2, $actual2);

        $fields3 = array('one', 'two', 'three');
        $checks3 = array('four', 'five');
        $actual3 = LoadFormSimple::load($this->vars, $fields3, $checks3, true);
        $expected3 = array(
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => false,
            'five' => true,
        );
        $this->assertEquals($expected3, $actual3);
    }
}
