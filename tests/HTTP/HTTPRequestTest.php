<?php
/**
 * Test of HTTPRequest class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\HTTPRequest;

/**
 * @covers go\Request\HTTP\HTTPRequest
 */
class HTTPRequestTest extends \PHPUnit_Framework_TestCase
{
    private $aserver = array(
        'REQUEST_METHOD' => 'GET',
        'SCRIPT_FILENAME' => '/var/www/index.php',
        'SCRIPT_NAME' => '/index.php',
        'REQUEST_URI' => '/?x=1',
        'DOCUMENT_ROOT' => '/var/www',
        'SERVER_PROTOCOL' => 'HTTP/1.1',
        'SERVER_SOFTWARE' => 'test-server',
        'REMOTE_ADDR' => '127.0.0.1',
        'REMOTE_PORT' => '1234',
        'SERVER_ADDR' => '127.0.0.1',
        'SERVER_PORT' => '80',
        'SERVER_NAME' => 'example.loc',
        'HTTP_HOST' => 'www.example.loc',
        'HTTP_USER_AGENT' => 'Mozilla/5.0',
        'PHP_AUTH_USER' => 'auser',
        'PHP_AUTH_PW' => 'apwd',
    );

    /**
     * @covers go\Request\HTTP\HTTPRequest::__get
     */
    public function testGet()
    {
        $context = array(
            'getallheaders' => false,
            'server' => $this->aserver,
        );
        $http = new HTTPRequest($context);
        $this->assertEquals('Mozilla/5.0', $http->headers['user-agent']);
        $this->assertEquals('1234', $http->client->port);
        $this->assertEquals('/var/www', $http->server->root);
        $this->assertEquals('1.1', $http->connection->httpVersion);
        $this->assertEquals('http://www.example.loc/?x=1', $http->document->absolute);
        $this->assertTrue($http->auth->checkBasic('auser', 'apwd'));
        $this->assertEquals('/index.php', $http->script->relative);

        $this->setExpectedException('LogicException');
        return $http->unknown;
    }

    /**
     * @covers go\Request\HTTP\HTTPRequest::__set
     * @expectedException \LogicException
     */
    public function testSet()
    {
        $context = array(
            'getallheaders' => false,
            'server' => $this->aserver,
        );
        $http = new HTTPRequest($context);
        $http->client = 1;
    }

    /**
     * @covers go\Request\HTTP\HTTPRequest::__construct
     * @covers go\Request\HTTP\HTTPRequest::__get
     */
    public function testLoadHeaders()
    {
        $context1 = array(
            'headers' => array(
                'host' => 'new.host.loc',
            ),
            'server' => $this->aserver,
        );
        $http1 = new HTTPRequest($context1);
        $this->assertEquals('new.host.loc', $http1->headers->host);

        $context2 = array(
            'server' => $this->aserver,
            'getallheaders' => (function () {
                return array(
                    'user-agent' => 'IE',
                );
            }),
        );
        $http2 = new HTTPRequest($context2);
        $this->assertEquals('IE', $http2->headers->user_agent);
    }

    /**
     * @covers go\Request\HTTP\HTTPRequest::__construct
     * @covers go\Request\HTTP\HTTPRequest::__get
     */
    public function testLoadSubs()
    {
        $aserver = array(
            'REMOTE_ADDR' => '123.45.67.89',
        );
        $context = array(
            'getallheaders' => false,
            'server' => $this->aserver,
            'subs' => array(
                'client' => new \go\Request\HTTP\Client($aserver),
            ),
        );
        $http = new HTTPRequest($context);
        $this->assertEquals('123.45.67.89', $http->client->ip);
        $this->assertEquals('127.0.0.1', $http->server->ip);
    }
}
