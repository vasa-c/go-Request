<?php
/**
 * Test of Format class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI;

use go\Request\CLI\Format;

/**
 * @covers go\Request\CLI\Format
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\CLI\Format::normalizeConfig
     * @covers go\Request\CLI\Format::getConfig
     */
    public function testNormalizeConfig()
    {
        $config = array(
            'title' => 'Command',
            'allow_unknown' => false,
            'options' => array(
                'one' => array(
                    'title' => 'One option',
                    'short' => 'o',
                    'required' => false,
                ),
                'two' => array(
                    'required' => true,
                    'filters' => array('One', 'Two'),
                ),
                'three' => true,
            ),
        );
        $expected = array(
            'title' => 'Command',
            'version' => null,
            'copyright' => null,
            'usage' => null,
            'allow_unknown' => false,
            'short_parsing' => 'list',
            'options' => array(
                'one' => array(
                    'title' => 'One option',
                    'short' => 'o',
                    'default' => null,
                    'required' => false,
                    'filter' => null,
                    'filters' => null,
                ),
                'two' => array(
                    'title' => null,
                    'short' => null,
                    'default' => null,
                    'required' => true,
                    'filter' => null,
                    'filters' => array('One', 'Two'),
                ),
                'three' => array(
                    'title' => null,
                    'short' => null,
                    'default' => null,
                    'required' => false,
                    'filter' => null,
                    'filters' => null,
                ),
            ),
        );
        $format = new Format($config);
        $this->assertEquals($expected, $format->getConfig());
    }
}
