<?php
/**
 * Test of Options class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI;

use go\Request\CLI\Options;
use go\Request\CLI\Format;
use go\Request\CLI\Argv;

/**
 * @covers go\Request\CLI\Options
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array|string $options
     * @param array $format
     * @return \go\Request\CLI\Options
     */
    private function create($options, $format)
    {
        $format = Format::normalizeConfig($format);
        if (\is_string($options)) {
            $argv =  Argv::createFromString($options, $format['short_parsing']);
            $options = $argv->getOptionsByTypes();
        }
        return new Options($options, $format);
    }

    public function testSuccess()
    {
        $format = array(
            'options' => array(
                'one' => array(
                ),
                'two' => true,
            ),
        );
        $cmd = '--one --two=value';
        $expected = array(
            'one' => true,
            'two' => 'value',
        );
        $options = $this->create($cmd, $format);
        $this->assertTrue($options->isSuccess());
        $this->assertEquals($expected, $options->getOptions());
    }

    public function testShort()
    {
        $format = array(
            'options' => array(
                'one' => array(
                ),
                'two' => array(
                    'short' => 't',
                ),
            ),
        );
        $cmd = '--one=value -t';
        $expected = array(
            'one' => 'value',
            'two' => true,
        );
        $options = $this->create($cmd, $format);
        $this->assertEquals($expected, $options->getOptions());
    }

    public function testDefault()
    {
        $format = array(
            'options' => array(
                'one' => array(
                ),
                'two' => array(
                    'default' => 'deftwo',
                    'short' => 't',
                ),
                'three' => array(
                    'default' => 'defthree',
                ),
            ),
        );
        $cmd = '--one=value -t';
        $expected = array(
            'one' => 'value',
            'two' => true,
            'three' => 'defthree',
        );
        $options = $this->create($cmd, $format);
        $this->assertEquals($expected, $options->getOptions());
    }

    public function testRequired()
    {
        $format = array(
            'options' => array(
                'one' => array(
                    'short' => 'o',
                    'required' => true,
                ),
                'two' => array(
                    'required' => true,
                    'short' => 't',
                ),
            ),
        );
        $cmd = '-ot';
        $options = $this->create($cmd, $format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'one' => true,
            'two' => true,
        );
        $this->assertEquals($expected, $options->getOptions());

        $cmd = '-t';
        $options = $this->create($cmd, $format);
        $this->assertFalse($options->isSuccess());
        $this->assertNull($options->getOptions());
        $expectedErrors = array(
            'one' => 'Required option --one is not found',
        );
        $this->assertEquals($expectedErrors, $options->getErrorOptions());
        $expectedLoaded = array(
            'two' => true,
        );
        $this->assertEquals($expectedLoaded, $options->getLoadedOptions());
    }

    public function testAllownUnknown()
    {
        $format = array(
            'options' => array(
                'one' => true,
            ),
        );
        $cmd = '--two=val -ta';
        $options = $this->create($cmd, $format);
        $this->assertFalse($options->isSuccess());
        $expectedErrors = array(
            'two' => 'Option --two is unknown',
            't' => 'Option --t is unknown',
            'a' => 'Option --a is unknown',
        );
        $this->assertEquals($expectedErrors, $options->getErrorOptions());
        $expectedLoaded = array(
            'one' => null,
        );
        $this->assertEquals($expectedLoaded, $options->getLoadedOptions());
        $expectedUnknown = array(
            'two' => 'val',
            't' => true,
            'a' => true,
        );
        $this->assertEquals($expectedUnknown, $options->getUnknownOptions());

        $format['allow_unknown'] = true;
        $options = $this->create($cmd, $format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'one' => null,
            'two' => 'val',
            't' => true,
            'a' => true,
        );
        $this->assertEquals($expected, $options->getOptions());
        $this->assertEquals($expected, $options->getLoadedOptions());
        $this->assertEquals($expectedUnknown, $options->getUnknownOptions());
    }

    public function testFilter()
    {
        $format = array(
            'options' => array(
                'one' => array(
                    'filter' => 'Switch',
                ),
                'two' => array(
                    'filter' => 'Value',
                ),
            ),
        );
        $cmd = '--one=Off --two=value';
        $options = $this->create($cmd, $format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'one' => false,
            'two' => 'value',
        );
        $this->assertEquals($expected, $options->getOptions());

        $cmd = '--one=value --two';
        $options = $this->create($cmd, $format);
        $this->assertFalse($options->isSuccess());
        $expectedErrors = array(
            'one' => 'Option --one is switch (value only on/off)',
            'two' => 'It requires value for --two',
        );
        $this->assertEquals($expectedErrors, $options->getErrorOptions());
    }

    public function testFilters()
    {
        $format = array(
            'options' => array(
                'one' => array(
                    'filters' => array('Switch', 'Flag'),
                ),
            ),
        );

        $cmd = '--one=On';
        $options = $this->create($cmd, $format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'one' => true,
        );
        $this->assertEquals($expected, $options->getOptions());

        $cmd = '--one=Off';
        $options = $this->create($cmd, $format);
        $this->assertFalse($options->isSuccess());
        $expectedErrors = array(
            'one' => 'Option --one is flag (cannot take value)',
        );
        $this->assertEquals($expectedErrors, $options->getErrorOptions());

        $cmd = '--one=qwerty';
        $options = $this->create($cmd, $format);
        $this->assertFalse($options->isSuccess());
        $expectedErrors = array(
            'one' => 'Option --one is switch (value only on/off)',
        );
        $this->assertEquals($expectedErrors, $options->getErrorOptions());
    }

    public function testErrors()
    {
        $format = array(
            'options' => array(
                'one' => array(
                    'filter' => 'Flag',
                ),
                'two' => array(
                    'required' => true,
                ),
            ),
        );
        $cmd = '--one=qwerty';
        $options = $this->create($cmd, $format);
        $errors = array();
        foreach ($options->getErrorObjects() as $e) {
            $errors[$e->getOption()] = array($e->getValue(), $e->getMessage());
        }
        $expected = array(
            'one' => array('qwerty', 'Option --one is flag (cannot take value)'),
            'two' => array(null, 'Required option --two is not found'),
        );
        $this->assertEquals($expected, $errors);
    }

    public function testMagicGet()
    {
        $format = array(
            'options' => array(
                'one' => true,
                'two' => true,
            ),
            'allow_unknown' => true,
        );
        $cmd = '--one=qwe --three';
        $options = $this->create($cmd, $format);
        $this->assertSame('qwe', $options->one);
        $this->assertSame(null, $options->two);
        $this->assertSame(true, $options->three);
        $this->assertSame(null, $options->four);
    }
}
