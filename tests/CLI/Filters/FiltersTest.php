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

        $filter = Filters::createFilter($f, 'opt');
        $this->assertSame($f, $filter);
        $this->assertEquals('qwerty', $filter->getOption());

        $f = '\go\Tests\Request\CLI\Filters\Mock';
        $filter = Filters::createFilter($f, 'opt');
        $this->assertInstanceOf('\go\Tests\Request\CLI\Filters\Mock', $filter);
        $this->assertEquals('opt', $filter->getOption());
        $this->assertEquals(array(), $filter->getParams());

        $params = array('value' => 5);
        $f = array('\go\Tests\Request\CLI\Filters\Mock', $params);
        $filter = Filters::createFilter($f, 'opt2');
        $this->assertInstanceOf('\go\Tests\Request\CLI\Filters\Mock', $filter);
        $this->assertEquals('opt2', $filter->getOption());
        $this->assertEquals(array('value' => 5), $filter->getParams());
    }

    /**
     * @covers go\Request\CLI\Filters\Filters::runChainFilters
     */
    public function testRunChainFilters()
    {
        $params = array(
            array('\go\Tests\Request\CLI\Filters\Mock', array('valid' => true)),
            array('\go\Tests\Request\CLI\Filters\Mock', array('value' => 5, 'new' => 4)),
            array('\go\Tests\Request\CLI\Filters\Mock', array('value' => 4, 'new' => 3)),
        );
        $this->assertEquals(3, Filters::runChainFilters($params, 'opt', 5));
        $this->setExpectedException('go\Request\CLI\Filters\Error', 'Invalid value for opt');
        Filters::runChainFilters($params, 'opt', 4);
    }
}
