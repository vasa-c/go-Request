<?php
/**
 * Test of Script class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Script;

/**
 * @covers go\Request\HTTP\Script
 */
class ScriptTest extends \PHPUnit_Framework_TestCase
{

    public function testScript()
    {
        $aserver = array(
            'SCRIPT_FILENAME' => '/var/www/folder/script.php',
            'SCRIPT_NAME' => '/folder/script.php',
        );
        $script = new Script($aserver);
        $this->assertEquals('/var/www/folder/script.php', $script->filename);
        $this->assertEquals('/folder/script.php', $script->relative);
    }
}
