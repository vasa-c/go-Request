<?php
/**
 * Test of Str helper (CLI)
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Helpers;

use go\Request\CLI\Helpers\CmdArgs;

/**
 * @covers go\Request\CLI\Helpers\CmdArgs
 */
class CmdArgsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see \go\Tests\Request\CLI\Helpers\CmdParserTest
     * @covers go\Request\CLI\Helpers\CmdArgs::convertCmdToArgs
     */
    public function testConvertCmdToArgs()
    {
        $cmd = '-o=\'qwe ="= rty\' --opt arg --last';
        $args = array(
            '-o=qwe ="= rty',
            '--opt',
            'arg',
            '--last',
        );
        $this->assertEquals($args, CmdArgs::convertCmdToArgs($cmd));
    }

    /**
     * @covers go\Request\CLI\Helpers\CmdArgs::convertSingleArgToCmdPart
     * @param string $arg
     * @param string $pcmd
     * @dataProvider providerConvertSingleArgToCmdPart
     */
    public function testConvertSingleArgToCmdPart($arg, $pcmd)
    {
        $this->assertEquals($pcmd, CmdArgs::convertSingleArgToCmdPart($arg));
    }

    /**
     * @return array
     */
    public function providerConvertSingleArgToCmdPart()
    {
        return array(
            array('arg', 'arg'),
            array('--opt', '--opt'),
            array('ar"g', 'ar\"g'),
            array('arg with space', '"arg with space"'),
            array('--opt=String with space', '--opt="String with space"'),
            array('--o pt', '"--o pt"'),
            array('--o "pt=String with space', '"--o \"pt=String with space"'),
            array('--opt=String with "quot"', '--opt="String with \"quot\""'),
        );
    }
}
