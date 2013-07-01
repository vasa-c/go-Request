<?php
/**
 * Test of Params helper (CLI)
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Helpers;

use go\Request\CLI\Helpers\Params;

/**
 * @covers go\Request\CLI\Helpers\Params
 */
class ParamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $opt
     * @param string $f
     * @param array $params
     * @covers go\Request\CLI\Helpers\Params::getBlockShortOptions
     * @dataProvider providerGetBlockShortOptions
     */
    public function testGetBlockShortOptions($opt, $f, $params)
    {
        $this->assertEquals($params, Params::getBlockShortOptions($opt, $f));
    }

    /**
     * @return array
     */
    public function providerGetBlockShortOptions()
    {
        return array(
            array(
                'opt=1',
                'mixed',
                array(
                    'opt=1' => true,
                ),
            ),
            array(
                'opt=1',
                'equal',
                array(
                    'opt' => '1',
                ),
            ),
            array(
                'opt=1',
                'value',
                array(
                    'o' => 'pt=1',
                ),
            ),
            array(
                'opt=1',
                'list',
                array(
                    'o' => true,
                    'p' => true,
                    't' => true,
                    '=' => true,
                    '1' => true,
                ),
            ),
            array(
                'opt=',
                'equal',
                array(
                    'opt' => '',
                ),
            ),
            array(
                'opt',
                'equal',
                array(
                    'opt' => true,
                ),
            ),
            array(
                'o',
                'value',
                array(
                    'o' => true,
                ),
            ),
            array(
                'abac',
                'list',
                array(
                    'a' => true,
                    'b' => true,
                    'c' => true,
                ),
            ),
        );
    }

    /**
     * @covers go\Request\CLI\Helpers\Params::getParamsForArgs
     */
    public function testGetParamsForArgs()
    {
        $args = array(
            './script.php',
            '-abc',
            '--opt=value',
            'arg1',
            'arg2',
        );
        $expected = array(
            array(
                'option' => false,
                'value' => './script.php',
            ),
            array(
                'option' => true,
                'short' => true,
                'name' => 'a',
                'value' => true,
            ),
            array(
                'option' => true,
                'short' => true,
                'name' => 'b',
                'value' => true,
            ),
            array(
                'option' => true,
                'short' => true,
                'name' => 'c',
                'value' => true,
            ),
            array(
                'option' => true,
                'short' => false,
                'name' => 'opt',
                'value' => 'value',
            ),
            array(
                'option' => false,
                'value' => 'arg1',
            ),
            array(
                'option' => false,
                'value' => 'arg2',
            ),
        );
        $this->assertEquals($expected, Params::getParamsForArgs($args, 'list'));
    }
}
