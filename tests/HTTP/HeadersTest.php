<?php
/**
 * Test of Headers class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Headers;

/**
 * @covers go\Request\HTTP\Headers
 */
class HeadersTest extends \PHPUnit_Framework_TestCase
{
    private $list = array(
        'host' => 'example.loc',
        'accept' => '*/*',
        'accept-encoding' => 'gzip, deflate',
        'cookie' => 'x=1, y=2',
        'connection' => 'close',
    );

    /**
     * @covers go\Request\HTTP\Headers::__construct
     * @covers go\Request\HTTP\Headers::getAllHeaders
     */
    public function testGetAllHeaders()
    {
        $headers = new Headers($this->list);
        $this->assertEquals($this->list, $headers->getAllHeaders());
    }

    /**
     * @covers go\Request\HTTP\Headers::__construct
     * @covers go\Request\HTTP\Headers::getAllHeaders
     */
    public function testLowerCase()
    {
        $list = $this->list;
        $expected = $this->list;
        $list['X-My-Header'] = 'Value';
        $expected['x-my-header'] = 'Value';
        $headers = new Headers($list);
        $this->assertEquals($expected, $headers->getAllHeaders());
    }

    /**
     * @covers go\Request\HTTP\Headers::__construct
     * @covers go\Request\HTTP\Headers::getList
     */
    public function testGetHeader()
    {
        $headers = new Headers($this->list);
        $this->assertSame('example.loc', $headers->getHeader('host'));
        $this->assertSame('gzip, deflate', $headers->getHeader('accept-encoding'));
        $this->assertSame('gzip, deflate', $headers->getHeader('Accept-Encoding'));
        $this->assertSame(null, $headers->getHeader('x-unknown'));
        $this->assertSame('default', $headers->getHeader('x-unknown', 'default'));
        $this->assertSame('example.loc', $headers->getHeader('host', 'default'));
    }

    /**
     * @covers go\Request\HTTP\Headers::__get
     * @covers go\Request\HTTP\Headers::__isset
     */
    public function testMagic()
    {
        $headers = new Headers($this->list);
        $this->assertSame('example.loc', $headers->host);
        $this->assertSame('gzip, deflate', $headers->accept_encoding);
        $this->assertSame(null, $headers->Accept_Encoding);
        $this->assertSame('gzip, deflate', $headers->__get('accept-encoding'));
        $key = 'accept-encoding';
        $this->assertSame('gzip, deflate', $headers->$key);
        $this->assertSame(null, $headers->unknown);
        $this->assertTrue(isset($headers->host));
        $this->assertTrue(isset($headers->accept_encoding));
        $this->assertFalse(isset($headers->Accept_Encoding));
        $this->assertFalse(isset($headers->unknown));
    }

    /**
     * @covers go\Request\HTTP\Headers::offsetGet
     * @covers go\Request\HTTP\Headers::offsetExists
     */
    public function testArrayAccess()
    {
        $headers = new Headers($this->list);
        $this->assertSame('example.loc', $headers['host']);
        $this->assertSame('gzip, deflate', $headers['accept-encoding']);
        $this->assertSame(null, $headers['Accept-Encoding']);
        $this->assertSame(null, $headers['accept_encoding']);
        $this->assertSame(null, $headers['unknown']);
        $this->assertTrue(isset($headers['host']));
        $this->assertTrue(isset($headers['accept-encoding']));
        $this->assertFalse(isset($headers['Accept-Encoding']));
        $this->assertFalse(isset($headers['unknown']));
    }

    /**
     * @covers go\Request\HTTP\Headers::count
     */
    public function testCountable()
    {
        $headers = new Headers($this->list);
        $this->assertCount(\count($this->list), $headers);
    }

    /**
     * @covers go\Request\HTTP\Headers::getIterator
     */
    public function testIterator()
    {
        $headers = new Headers($this->list);
        $result = array();
        foreach ($headers as $k => $v) {
            $result[$k] = $v;
        }
        $this->assertEquals($this->list, $result);
    }

    /**
     * @covers go\Request\HTTP\Headers::__set
     * @covers go\Request\HTTP\Headers::__unset
     * @covers go\Request\HTTP\Headers::offsetSet
     * @covers go\Request\HTTP\Headers::offsetUnset
     * @dataProvider providerReadonly
     * @expectedException \LogicException
     * @param callee $func
     */
    public function testReadonly($func)
    {
        $headers = new Headers($this->list);
        \call_user_func($func, $headers);
    }

    /**
     * @return array
     */
    public function providerReadonly()
    {
        return array(
            array((function ($headers) {
                $headers->unknown = 'value';
            })),
            array((function ($headers) {
                $headers->host = 'value';
            })),
            array((function ($headers) {
                unset($headers->unkown);
            })),
            array((function ($headers) {
                unset($headers->host);
            })),
            array((function ($headers) {
                $headers['unknown'] = 'value';
            })),
            array((function ($headers) {
                $headers['host'] = 'value';
            })),
            array((function ($headers) {
                unset($headers['unknown']);
            })),
            array((function ($headers) {
                unset($headers['host']);
            })),
        );
    }
}
