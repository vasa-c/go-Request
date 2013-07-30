<?php
/**
 * Test of Helper\Server class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\Server;

/**
 * @covers go\Request\HTTP\Helpers\Server
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers go\Request\HTTP\Helpers\Server::loadHTTPHeaders
     */
    public function testLoadHTTPHeaders()
    {
        $server = array(
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.loc',
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
            'REQUEST_URI' => '/',
            'HTTP_ACCEPT' => '*/*',
            'PHP_SELF' => '/index.php',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;',
            'HTTP_X_REAL_IP' => '127.0.0.1',
            'HTTPS' => false,
            'CONTENT_TYPE' => 'Test',
            'CONTENT_LENGTH' => '10',
        );
        $headers = array(
            'host' => 'example.loc',
            'user-agent' => 'Mozilla/5.0',
            'accept' => '*/*',
            'accept-language' => 'en-us,en;',
            'x-real-ip' => '127.0.0.1',
            'content-type' => 'Test',
            'content-length' => '10',
        );
        $this->assertEquals($headers, Server::loadHTTPHeaders($server));
    }
}
