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
     * @covers go\Request\CLI\Helpers::convertCmdToArgs
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
}
