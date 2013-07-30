<?php
/**
 * Test of Client class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Client;

/**
 * @covers go\Request\HTTP\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Client::__get
     */
    public function testGet()
    {
        $aserver = array(
            'REMOTE_ADDR' => '123.45.67.89',
            'REMOTE_PORT' => '12345',
            'HTTP_USER_AGENT' => 'Mozilla',
            'HTTP_ACCEPT_LANGUAGE' => 'ru-ru',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
            'REQUEST_URI' => '/',
            'HTTP_REFERER' => '/login',
        );
        $client = new Client($aserver);
        $this->assertEquals('123.45.67.89', $client->ip);
        $this->assertEquals('12345', $client->port);
        $this->assertEquals('Mozilla', $client->userAgent);
        $this->assertEquals('ru-ru', $client->acceptLanguage);
        $this->assertEquals('gzip, deflate', $client->acceptEncoding);
        $this->assertEquals('/login', $client->referer);

        $this->setExpectedException('LogicException');
        return $client->unknown;
    }

    /**
     * @covers go\Request\HTTP\Client::__get
     */
    public function testEmptyGet()
    {
        $server = array(
            'REQUEST_URI' => '/',
        );
        $client = new Client($server);
        $this->assertNull($client->ip);
        $this->assertNull($client->port);
        $this->assertNull($client->userAgent);
        $this->assertNull($client->acceptLanguage);
        $this->assertNull($client->acceptEncoding);
    }

    /**
     * @covers go\Request\HTTP\Client::__set
     * @expectedException LogicException
     */
    public function testSet()
    {
        $client = new Client(array());
        $client->ip = 'new ip';
    }
}
