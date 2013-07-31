<?php
/**
 * Test of Request class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request;

use go\Request\Request;

/**
 * @covers go\Request\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testCLI()
    {
        $context = array(
            'sapi' => 'cli',
            'server' => array(
                'argv' => array(
                    './script.php',
                    '--opt=value',
                    'arg'
                ),
            ),
        );
        $request = new Request($context);
        $this->assertEquals('cli', $request->sapi);
        $this->assertEquals('cli', $request->type);
        $this->assertTrue($request->isCLI);
        $this->assertFalse($request->isHTTP);
        $this->assertEquals('./script.php --opt=value arg', $request->args->getCommandLine());
        $this->assertNull($request->http->client->ip);
        $this->assertNull($request->post->get('var'));

        $this->setExpectedException('LogicException');
        return $request->unknown;
    }

    public function testHTTP()
    {
        $context = array(
            'sapi' => 'apache',
            'server' => array(
                'HTTP_USER_AGENT' => 'Mozilla/5.5',
            ),
            'get' => array(
                'x' => '1',
            ),
        );
        $request = new Request($context);
        $this->assertEquals('apache', $request->sapi);
        $this->assertEquals('http', $request->type);
        $this->assertFalse($request->isCLI);
        $this->assertTrue($request->isHTTP);
        $this->assertEquals('', $request->args->getCommandLine());
        $this->assertEquals('Mozilla/5.5', $request->http->client->userAgent);
        $this->assertEquals('1', $request->get->get('x'));
    }

    public function testSubs()
    {
        $context = array(
            'sapi' => 'apache',
            'get' => array(
                'x' => '1',
            ),
            'post' => array(
                'y' => '2',
            ),
            'subs' => array(
                'get' => new \go\Request\HTTP\Storage(array('x' => '3')),
            ),
        );
        $request = new Request($context);
        $this->assertEquals('3', $request->get->get('x'));
        $this->assertEquals('2', $request->post->get('y'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testReadOnly()
    {
        $request = new Request();
        $request->get = 'get';
    }
}
