<?php
/**
 * Test of Connection class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Connection;

/**
 * @covers go\Request\HTTP\Connection
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Connection::__get
     */
    public function testGet()
    {
        $aserver = array(
            'HTTPS' => '',
            'REQUEST_METHOD' => 'GET',
            'REDIRECT_STATUS' => '200',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_CONNECTION' => 'keep-alive',
        );
        $connection = new Connection($aserver);

        $this->assertSame(false, $connection->https);
        $this->assertSame('GET', $connection->method);
        $this->assertSame('keep-alive', $connection->status);
        $this->assertSame('200', $connection->redirect);
        $this->assertSame('HTTP/1.1', $connection->protocol);
        $this->assertSame('1.1', $connection->httpVersion);

        $this->setExpectedException('LogicException');
        return $connection->unknown;
    }
}
