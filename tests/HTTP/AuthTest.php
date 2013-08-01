<?php
/**
 * Test of Auth class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP;

use go\Request\HTTP\Auth;

/**
 * @covers go\Request\HTTP\Auth
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{

    public function testNone()
    {
        $aserver = array(
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        );
        $auth = new Auth($aserver);
        $this->assertEmpty($auth->type);
        $this->assertEmpty($auth->user);
        $this->assertEmpty($auth->password);
        $this->assertEmpty($auth->digest);
        $this->assertEquals('HTTP/1.1 401 Unauthorized', $auth->getAuthHTTPStatus(true));
    }

    public function testBasic()
    {
        $aserver = array(
            'PHP_AUTH_USER' => 'vasya',
            'PHP_AUTH_PW' => 'pupkin',
        );
        $auth = new Auth($aserver);
        $this->assertEquals('Basic', $auth->type);
        $this->assertEquals('vasya', $auth->user);
        $this->assertEquals('pupkin', $auth->password);
        $this->assertEmpty($auth->digest);
        $this->assertEquals('WWW-Authenticate: Basic realm="realm"', $auth->getBasicHeader('realm'));
        $this->assertTrue($auth->checkBasic('vasya', 'pupkin'));
        $this->assertFalse($auth->checkBasic('vasya', 'nepupkin'));
    }

    public function testDigest()
    {
        $digest = 'username="user", realm="area", nonce="1234", '.
            'uri="/auth.php", response="1c99d8cef3ab8c6fcb376a7adfde4e9f"';
        $aserver = array(
            'PHP_AUTH_DIGEST' => $digest,
            'REQUEST_URI' => '/auth.php',
        );
        $auth = new Auth($aserver);
        $this->assertEquals('Digest', $auth->type);
        $this->assertEquals($digest, $auth->digest);
        $this->assertEquals('user', $auth->user);
        $this->assertEmpty($auth->password);
        $params = array(
            'username' => 'user',
            'realm' => 'area',
            'nonce' => '1234',
            'uri' => '/auth.php',
            'response' => '1c99d8cef3ab8c6fcb376a7adfde4e9f',
        );
        $this->assertEquals($params, $auth->digestParams);

        $expected = 'WWW-Authenticate: Digest realm="area", nonce="1234", x="qu\"ot"';
        $this->assertEquals($expected, $auth->getDigestHeader('area', '1234', array('x' => 'qu"ot')));
    }

    public function testCheckBasicForList()
    {
        $users = array(
            'vasya' => 'pupkin',
            'petya' => 'lojkin',
            'misha' => 'krushkin',
        );
        $aserver1 = array();
        $auth1 = new Auth($aserver1);
        $this->assertFalse($auth1->checkBasicForList($users));
        $aserver2 = array(
            'PHP_AUTH_USER' => 'petya',
            'PHP_AUTH_PW' => 'lojkin',
        );
        $auth2 = new Auth($aserver2);
        $this->assertEquals('petya', $auth2->checkBasicForList($users));
        $aserver3 = array(
            'PHP_AUTH_USER' => 'petya',
            'PHP_AUTH_PW' => 'qwerty',
        );
        $auth3 = new Auth($aserver3);
        $this->assertFalse($auth3->checkBasicForList($users));
        $aserver4 = array(
            'PHP_AUTH_USER' => 'masha',
            'PHP_AUTH_PW' => 'kashina',
        );
        $auth4 = new Auth($aserver4);
        $this->assertFalse($auth4->checkBasicForList($users));
    }
}
