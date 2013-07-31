<?php
/**
 * Test of Helper\Auth class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\Auth;

/**
 * @covers go\Request\HTTP\Helpers\Auth
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Helpers\Auth::makeHeaderParams
     * @param array $params
     * @param string $header
     * @dataProvider providerMakeHeaderParams
     */
    public function testMakeHeaderParams($params, $header)
    {
        $this->assertSame($header, Auth::makeHeaderParams($params));
    }

    /**
     * @return array
     */
    public function providerMakeHeaderParams()
    {
        return array(
            array(array(), ''),
            array(array('one' => 'qwe'), 'one="qwe"'),
            array(array('one' => 'qwe', 'two' => 'rty'), 'one="qwe", two="rty"'),
            array(array('one' => 'qwe', 'two' => 'r,t"y', 'three' => ''), 'one="qwe", two="r,t\"y", three=""'),
        );
    }

    /**
     * @covers go\Request\HTTP\Helpers\Auth::parseHeaderParams
     * @param string $header
     * @param array $params
     * @dataProvider providerParseHeaderParams
     */
    public function testParseHeaderParams($header, $params)
    {
        $this->assertSame($params, Auth::parseHeaderParams($header));
    }

    /**
     * @return array
     */
    public function providerParseHeaderParams()
    {
        return array(
            array('', array()),
            array('one="qwe", two="rty"', array('one' => 'qwe', 'two' => 'rty')),
            array('one="qwe", two="r,t\\"y\\\\", three=""', array('one' => 'qwe', 'two' => 'r,t"y\\', 'three' => '')),
        );
    }
}
