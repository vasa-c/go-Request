<?php
/**
 * Test of Helper\LoadFormFormat class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\LoadFormFormat;

/**
 * @covers go\Request\HTTP\Helpers\LoadFormFormat
 */
class LoadFormFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Helpers\LoadFormFormat::normalizeParams
     * @dataProvider providerNormalizeParams
     * @param mixed $params
     * @param array $expected
     */
    public function testNormalizeParams($params, $expected)
    {
        $this->assertEquals($expected, LoadFormFormat::normalizeParams($params));
    }

    /**
     * @return array
     */
    public function providerNormalizeParams()
    {
        return array(
            array(
                true,
                array(
                    'type' => 'scalar',
                ),
            ),
            array(
                'int',
                array(
                    'type' => 'int',
                ),
            ),
            array(
                array(
                    'trim' => true,
                ),
                array(
                    'type' => 'scalar',
                    'trim' => true,
                ),
            ),
            array(
                array(
                    'format' => array(),
                ),
                array(
                    'type' => 'array',
                    'format' => array(),
                ),
            ),
            array(
                array(
                    'type' => 'int',
                    'format' => array(),
                ),
                array(
                    'type' => 'int',
                    'format' => array(),
                ),
            ),
        );
    }

}
