<?php
/**
 * Test of Server class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Server;

/**
 * @covers go\Request\HTTP\Server
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Server::__get
     */
    public function testGet()
    {
        $aserver = array(
            'SERVER_SOFTWARE' => 'nginx',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'SERVER_NAME' => 'example.loc',
            'DOCUMENT_ROOT' => '/var/www',
        );
        $server = new Server($aserver);
        $this->assertEquals('127.0.0.1', $server->ip);
        $this->assertEquals('80', $server->port);
        $this->assertEquals('nginx', $server->soft);
        $this->assertEquals('CGI/1.1', $server->interface);
        $this->assertEquals('/var/www', $server->root);

        $this->setExpectedException('LogicException');
        return $server->unknown;
    }
}
