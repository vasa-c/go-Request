<?php
/**
 * Test of Filters static functions
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Filters;

use go\Request\CLI\Filters\Filters;

/**
 * @covers go\Request\CLI\Filters\Filters
 */
class FiltersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\CLI\Filters\Filters::createFilter
     */
    public function testCreateFilter()
    {
        $f = new Mock('qwerty');

        $filter = Filters::createFilter('opt', $f);
        $this->assertSame($f, $filter);
        $this->assertEquals('qwerty', $filter->getOption());

        $f = '\go\Tests\Request\CLI\Filters\Mock';
        $filter = Filters::createFilter('opt', $f);
        $this->assertInstanceOf('\go\Tests\Request\CLI\Filters\Mock', $filter);
        $this->assertEquals('opt', $filter->getOption());
        $this->assertEquals(array(), $filter->getParams());

        $params = array('value' => 5);
        $f = array('\go\Tests\Request\CLI\Filters\Mock', $params);
        $filter = Filters::createFilter('opt2', $f);
        $this->assertInstanceOf('\go\Tests\Request\CLI\Filters\Mock', $filter);
        $this->assertEquals('opt2', $filter->getOption());
        $this->assertEquals(array('value' => 5), $filter->getParams());
    }
}
