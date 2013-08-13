<?php
/**
 * Test of CmdParser helper (CLI)
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Helpers;

use go\Request\CLI\Helpers\CmdParser;

/**
 * @covers go\Request\CLI\Helpers\CmdParser
 */
class CmdParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $cmd
     * @param array $args
     * @covers go\Request\CLI\Helpers\CmdParser::__construct
     * @covers go\Request\CLI\Helpers\CmdParser::getArgs
     * @dataProvider providerParser
     */
    public function testParser($cmd, $args)
    {
        $parser = new CmdParser($cmd);
        $this->assertEquals($args, $parser->getArgs());
    }

    /**
     * @return array
     */
    public function providerParser()
    {
        return array(
            array(
                './script.php one two',
                array(
                    './script.php',
                    'one',
                    'two',
                ),
            ),
            array(
                './script -o --opt=qwe one two',
                array(
                    './script',
                    '-o',
                    '--opt=qwe',
                    'one',
                    'two',
                ),
            ),
            array(
                '-o=1 --opt="qwe =\"= rty" arg',
                array(
                    '-o=1',
                    '--opt=qwe ="= rty',
                    'arg',
                ),
            ),
            array(
                '-o=\'qwe ="= rty\' --opt arg --last',
                array(
                    '-o=qwe ="= rty',
                    '--opt',
                    'arg',
                    '--last',
                ),
            ),
            array(
                '-o "qwe rty"',
                array(
                    '-o',
                    'qwe rty',
                ),
            ),
            array(
                '-o qwe\\ rty',
                array(
                    '-o',
                    'qwe rty',
                ),
            ),
        );
    }
}
