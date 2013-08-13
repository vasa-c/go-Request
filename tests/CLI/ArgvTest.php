<?php
/**
 * Test of Argv class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI;

use go\Request\CLI\Argv;

/**
 * @covers go\Request\CLI\Argv
 */
class ArgvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\CLI\Argv
     */
    public function testArgv()
    {
        $cmd = 'cmd -ab --opt="value" arg1 arg2';
        $cmdWithoutQuot = 'cmd -ab --opt=value arg1 arg2';
        $cmdSep = 'cmd -a -b --opt=value arg1 arg2';
        $cmdEqual = 'cmd -a=b --opt=value arg1 arg2';
        $args = array(
            'cmd',
            '-ab',
            '--opt=value',
            'arg1',
            'arg2',
        );
        $argsSep = array(
            'cmd',
            '-a',
            '-b',
            '--opt=value',
            'arg1',
            'arg2',
        );
        $listArgs = array(
            'cmd',
            'arg1',
            'arg2',
        );
        $paramsByList = array(
            array(
                'option' => false,
                'value' => 'cmd',
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
        $paramsByValue = array(
            array(
                'option' => false,
                'value' => 'cmd',
            ),
            array(
                'option' => true,
                'short' => true,
                'name' => 'a',
                'value' => 'b',
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
        $optionsByTypes = array(
            'short' => array(
                'a' => true,
                'b' => true,
            ),
            'long' => array(
                'opt' => 'value',
            ),
        );
        $optionsByList = array(
            'a' => true,
            'b' => true,
            'opt' => 'value',
        );
        $optionsByValue = array(
            'a' => 'b',
            'opt' => 'value',
        );

        $instance1 = Argv::createFromString($cmd, 'list');
        $this->assertEquals($cmd, $instance1->getCommandLine());
        $this->assertEquals($args, $instance1->getArgsArray());
        $this->assertEquals($paramsByList, $instance1->getParams());
        $this->assertEquals($paramsByValue, $instance1->getParams('value'));
        $this->assertEquals($listArgs, $instance1->getListArguments());
        $this->assertEquals($optionsByTypes, $instance1->getOptionsByTypes());
        $this->assertEquals($optionsByList, $instance1->getMixedOptions());
        $this->assertEquals($optionsByValue, $instance1->getMixedOptions('value'));

        $instance2 = Argv::createFromArray($args, 'list');
        $this->assertEquals($cmdWithoutQuot, $instance2->getCommandLine());
        $this->assertEquals($args, $instance2->getArgsArray());
        $this->assertEquals($paramsByList, $instance2->getParams());

        $instance3 = Argv::createFromParams($paramsByList, 'list');
        $this->assertEquals($cmdSep, $instance3->getCommandLine());
        $this->assertEquals($argsSep, $instance3->getArgsArray());
        $this->assertEquals($paramsByList, $instance3->getParams());

        $instance4 = Argv::createFromString($cmd, 'value');
        $this->assertEquals($cmd, $instance4->getCommandLine());
        $this->assertEquals($args, $instance4->getArgsArray());
        $this->assertEquals($paramsByValue, $instance4->getParams());

        $instance5 = Argv::createFromParams($paramsByValue, 'equal');
        $this->assertEquals($cmdEqual, $instance5->getCommandLine());
    }

    /**
     * @covers go\Request\CLI\Argv::getStack
     */
    public function testGetStack()
    {
        $cmd = 'cmd --opt1=1 --opt2=2 arg1 arg2';
        $argv = Argv::createFromString($cmd);
        $stack1 = $argv->getStack();
        $expected = array('cmd', 'arg1', 'arg2');
        $this->assertEquals($expected, $stack1->getAllArguments());
        $this->assertEmpty($stack1->getAllArguments());
        $stack2 = $argv->getStack();
        $this->assertNotSame($stack1, $stack2);
        $this->assertEquals($expected, $stack2->getAllArguments());
    }
}
