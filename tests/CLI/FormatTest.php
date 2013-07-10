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

    /**
     * @covers go\Request\CLI\Format::loadOptions
     */
    public function testLoadOptions()
    {
        $options = array(
            'short' => array(
                't' => true,
            ),
            'long' => array(
                'one' => 'value',
            ),
        );
        $format = array(
            'options' => array(
                'one' => true,
                'two' => array(
                    'short' => 't',
                ),
            ),
        );
        $format = new Format($format);
        $options = $format->loadOptions($options);
        $this->assertInstanceOf('go\Request\CLI\Options', $options);
        $expected = array(
            'one' => 'value',
            'two' => true,
        );
        $this->assertEquals($expected, $options->getOptions());
    }

    /**
     * @covers go\Request\CLI\Format::getHelp
     */
    public function testGetHelp()
    {
        $config = array(
            'title' => 'Test cmd',
            'version' => '0.1',
            'copyright' => 'go',
            'usage' => 'cmd [options] arguments',
            'options' => array(
                'one' => array(
                    'title' => 'One option',
                ),
                'two' => array(
                    'title' => 'Two option',
                    'short' => 't',
                ),
            ),
        );
        $format = new Format($config);
        $expected = \file_get_contents(__DIR__.'/help.txt');
        $this->assertEquals(\trim($expected), \trim($format->getHelp("\n")));
    }
}
