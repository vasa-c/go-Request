<?php
/**
 * Test of basic filters class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Filters;

/**
 * @covers go\Request\CLI\Filters\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\CLI\Filters\Base::__construct
     */
    public function testConstructor()
    {
        $filter1 = new Mock('opt1');
        $this->assertEquals('opt1', $filter1->getOption());
        $this->assertEquals(array(), $filter1->getParams());

        $filter2 = new Mock('opt2', array('value' => 5));
        $this->assertEquals('opt2', $filter2->getOption());
        $this->assertEquals(array('value' => 5), $filter2->getParams());
    }

    /**
     * @covers go\Request\CLI\Filters\Base::process
     */
    public function testValid()
    {
        $filter1 = new Mock('opt', array('valid' => true));
        $this->assertEquals(5, $filter1->filter(5));

        $filter2 = new Mock('opt', array('valid' => true, 'new' => 4));
        $this->assertEquals(4, $filter2->filter(5));

        $filter3 = new Mock('opt');
        try {
            $filter3->filter(5);
            $this->fail();
        } catch (\go\Request\CLI\Filters\Error $e) {
            $this->assertEquals('Option opt is not valid', $e->getMessage());
            $this->assertEquals('opt', $e->getOption());
            $this->assertEquals(5, $e->getValue());
        }

        $filter4 = new Mock('opt', array('value' => 5));
        $this->assertEquals(5, $filter4->filter(5));

        $filter5 = new Mock('opt', array('value' => 6));
        try {
            $filter5->filter(5);
            $this->fail();
        } catch (\go\Request\CLI\Filters\Error $e) {
            $this->assertEquals('Invalid value for opt', $e->getMessage());
            $this->assertEquals('opt', $e->getOption());
            $this->assertEquals(5, $e->getValue());
        }
    }

    /**
     * @covers go\Request\CLI\Filters\Base::process
     */
    public function testValue()
    {

    }
}
