<?php
/**
 * Test of Document class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Document;

/**
 * @covers go\Request\HTTP\Document
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    private $aserver = array(
        'REQUEST_URI' => '/one/two/three.php?x=1&y=2',
        'HTTP_HOST' => 'example.loc',
        'HTTPS' => '',
    );

    public function testBase()
    {
        $document = new Document($this->aserver);
        $this->assertEquals('/one/two/three.php?x=1&y=2', $document->uri);
        $this->assertEquals('http://example.loc/one/two/three.php?x=1&y=2', $document->absolute);
        $this->assertEquals('x=1&y=2', $document->query);
        $this->assertEquals('/one/two/three.php', $document->path);
        $this->assertEquals(array('one', 'two', 'three.php'), $document->components);
        $this->assertEquals('example.loc', $document->host);
        $this->assertEmpty($document->info);
        $this->assertFalse($document->https);
        $this->assertEquals(80, $document->port);

        $this->setExpectedException('LogicException');
        return $document->unknown;
    }

    public function testHttps()
    {
        $aserver = $this->aserver;
        $aserver['HTTPS'] = '1';
        $document = new Document($aserver);
        $this->assertTrue($document->https);
        $this->assertEquals('https://example.loc/one/two/three.php?x=1&y=2', $document->absolute);
        $this->assertEquals(443, $document->port);
    }

    public function testPort()
    {
        $aserver1 = $this->aserver;
        $aserver1['SERVER_PORT'] = '80';
        $document1 = new Document($aserver1);
        $this->assertEquals('http://example.loc/one/two/three.php?x=1&y=2', $document1->absolute);

        $aserver2 = $this->aserver;
        $aserver2['SERVER_PORT'] = '8080';
        $document2 = new Document($aserver2);
        $this->assertEquals('http://example.loc:8080/one/two/three.php?x=1&y=2', $document2->absolute);
        $this->assertEquals(8080, $document2->port);

        $aserver3 = $this->aserver;
        $aserver3['SERVER_PORT'] = '443';
        $aserver3['HTTPS'] = '1';
        $document3 = new Document($aserver3);
        $this->assertEquals('https://example.loc/one/two/three.php?x=1&y=2', $document3->absolute);

        $aserver4 = $this->aserver;
        $aserver4['SERVER_PORT'] = '80';
        $aserver4['HTTPS'] = '1';
        $document4 = new Document($aserver4);
        $this->assertEquals('https://example.loc:80/one/two/three.php?x=1&y=2', $document4->absolute);
    }

    public function testComponents()
    {
        $aserver1 = $this->aserver;
        $aserver1['REQUEST_URI'] = '/one/two/three/?x=1&y=2';
        $document1 = new Document($aserver1);
        $this->assertEquals(array('one', 'two', 'three'), $document1->components);

        $aserver2 = $this->aserver;
        $aserver2['REQUEST_URI'] = '/?x=1';
        $document2 = new Document($aserver2);
        $this->assertEmpty($document2->components);
    }

    public function testInfo()
    {
        $aserver = $this->aserver;
        $aserver['PATH_INFO'] = 'info';
        $document = new Document($aserver);
        $this->assertEquals('info', $document->info);
    }

    /**
     * @expectedException \LogicException
     */
    public function testReadOnly()
    {
        $document = new Document($this->aserver);
        $document->uri = '/new/uri';
    }
}
