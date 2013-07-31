<?php
/**
 * HTTP-request: basic and digest authorization
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 *
 * @todo checkDigest
 */

namespace go\Request\HTTP;

/**
 * @property-read string $type
 * @property-read string $user
 * @property-read string $password
 * @property-read string $digest
 * @property-read string $digestParams
 */
class Auth
{
    /**
     * Constructor
     *
     * @param array $server [optional]
     */
    public function __construct(array $server = null)
    {
        $server = $server ?: $_SERVER;
        $this->server = $server;
        $this->subs = array(
            'type' => null,
            'user' => null,
            'password' => null,
            'digest' => null,
            'digestParams' => false,
        );
        if (!empty($server['PHP_AUTH_DIGEST'])) {
            $this->subs['type'] = 'Digest';
            $this->subs['digest'] = $server['PHP_AUTH_DIGEST'];
            $this->subs['user'] = false;
        } elseif (!empty($server['PHP_AUTH_USER'])) {
            $this->subs['type'] = 'Basic';
            $this->subs['user'] = $server['PHP_AUTH_USER'];
            $this->subs['password'] = isset($server['PHP_AUTH_PW']) ? $server['PHP_AUTH_PW'] : '';
        }
    }

    /**
     * Magic get
     *
     * @param string $key
     * @return mixed
     * @throws \LogicException
     */
    public function __get($key)
    {
        if (!\array_key_exists($key, $this->subs)) {
            throw new \LogicException('Auth->'.$key.' is not found');
        }
        $value = $this->subs[$key];
        if ($value === false) {
            switch ($key) {
                case 'user':
                    $params = $this->__get('digestParams');
                    $this->subs['user'] = isset($params['username']) ? $params['username'] : null;
                    return $this->subs['user'];
                case 'digestParams':
                    $this->subs['digestParams'] = $this->loadDigestParams();
                    return $this->subs['digestParams'];
            }
        }
        return $value;
    }

    /**
     * Magic set (forbidden)
     *
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    public function __set($key, $value)
    {
        throw new \LogicException('Auth instance is read-only');
    }

    /**
     * Get status header for authorization
     *
     * @param boolean $protocol [optional]
     * @return string
     */
    public function getAuthHTTPStatus($protocol = false)
    {
        if ($protocol) {
            if ($protocol === true) {
                if (isset($this->server['SERVER_PROTOCOL'])) {
                    $prefix = $this->server['SERVER_PROTOCOL'];
                } else {
                    $prefix = 'HTTP/1.1';
                }
            } else {
                $prefix = $protocol;
            }
            $prefix .= ' ';
        } else {
            $prefix = '';
        }
        return $prefix.'401 Unauthorized';
    }

    /**
     * Get http-header for basic authorization
     *
     * @param string $realm
     * @return string
     */
    public function getBasicHeader($realm)
    {
        return 'WWW-Authenticate: Basic realm="'.\addslashes($realm).'"';
    }

    /**
     * Get http-header for digest authorization
     *
     * @param string $area
     * @param string $nonce
     * @param array $params [optional]
     * @return string
     */
    public function getDigestHeader($area, $nonce = null, array $params = null)
    {
        $p = array(
            'realm' => $area,
            'nonce' => $nonce ?: \uniqid(),
        );
        if ($params) {
            $p = \array_merge($p, $params);
        }
        return 'WWW-Authenticate: Digest '.Helpers\Auth::makeHeaderParams($p);
    }

    /**
     * Check basic authorization
     *
     * @param string $user
     * @param string $password
     * @return boolean
     */
    public function checkBasic($user, $password)
    {
        if ($this->subs['type'] != 'Basic') {
            return false;
        }
        return (($this->subs['user'] == $user) && ($this->subs['password'] == $password));
    }

    /**
     * @return array
     */
    private function loadDigestParams()
    {
        $digest = $this->subs['digest'];
        if (empty($digest)) {
            return array();
        }
        return Helpers\Auth::parseHeaderParams($digest);
    }

    /**
     * @var array
     */
    private $subs = array();

    /**
     * @var array
     */
    private $server;
}
