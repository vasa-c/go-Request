<?php
/**
 * Test of Stack class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI;

use go\Request\CLI\Stack;

/**
 * @covers go\Request\CLI\Stack
 */
class StackTest extends \PHPUnit_Framework_TestCase
{

    public function testStack()
    {
        $cmd = './script.php --quiet install --log=filename db -ton one.sql two.sql';
        $stack = Stack::createFromString($cmd);
        $this->assertEquals('./script.php', $stack->getNextArgument());

        $format = array(
            'options' => array(
                'quiet' => true,
                'opt' => true,
            ),
        );
        $options = $stack->getNextOptions($format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'quiet' => true,
            'opt' => null,
        );
        $this->assertEquals($expected, $options->getOptions());

        $this->assertEquals('install', $stack->getNextArgument());

        $format = array(
            'options' => array(
                'log' => true,
            ),
        );
        $options = $stack->getNextOptions($format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'log' => 'filename',
        );
        $this->assertEquals($expected, $options->getOptions());

        $this->assertEquals('db', $stack->getNextArgument());
        $format = array(
            'options' => array(
                'drop' => true,
                'trans' => array(
                    'short' => 't',
                    'filter' => 'Switch',
                ),
            ),
            'short_parsing' => 'value',
        );
        $options = $stack->getNextOptions($format);
        $this->assertTrue($options->isSuccess());
        $expected = array(
            'trans' => true,
            'drop' => null,
        );
        $this->assertEquals($expected, $options->getOptions());

        $expectedArgs = array('one.sql', 'two.sql');
        $this->assertEquals($expectedArgs, $stack->getListNextArguments());
    }

    public function testGetAllArguments()
    {
        $cmd = 'one two -abc three --qwe=qwe four five -qw';
        $stack = Stack::createFromString($cmd);
        $this->assertEquals('one', $stack->getNextArgument());

        $expected = array('two', 'three', 'four', 'five');
        $this->assertEquals($expected, $stack->getAllArguments());

        $options = $stack->getNextOptions();
        $this->assertEquals(array(), $options->getOptions());

        $this->assertNull($stack->getNextArgument());
        $this->assertEmpty($stack->getListNextArguments());
        $this->assertEmpty($stack->getAllArguments());
    }

    public function testGetNextOptions()
    {
        $cmd = '--one=1 --two=2 -ab arg --three=3';

        $format = array(
            'options' => array(
                'one' => true,
                'two' => true,
                'aa' => array(
                    'short' => 'a',
                ),
                'bb' => array(
                    'short' => 'b',
                ),
                'four' => array(
                    'default' => '4',
                ),
            ),
        );
        $stack = Stack::createFromString($cmd);
        $options = $stack->getNextOptions($format);
        $expected = array(
            'one' => '1',
            'two' => '2',
            'aa' => true,
            'bb' => true,
            'four' => '4',
        );
        $this->assertEquals($expected, $options->getOptions());

        $format = new \go\Request\CLI\Format($format);
        $stack = Stack::createFromString($cmd);
        $options = $stack->getNextOptions($format);
        $this->assertEquals($expected, $options->getOptions());

        $stack = Stack::createFromString($cmd);
        $options = $stack->getNextOptions();
        $expected = array(
            'one' => '1',
            'two' => '2',
            'a' => true,
            'b' => true,
        );
        $this->assertEquals($expected, $options->getOptions());

        $stack = Stack::createFromString($cmd);
        $options = $stack->getNextOptions('value');
        $expected = array(
            'one' => '1',
            'two' => '2',
            'a' => 'b',
        );
        $this->assertEquals($expected, $options->getOptions());
    }
}
