<?php
/**
 * HTTP request: client
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

/**
 * @property-read string $ip
 * @property-read string $port
 * @property-read string $userAgent
 * @property-read string $acceptLanguage
 * @property-read string $acceptEncoding
 */
class Client
{
    /**
     * Constructor
     *
     * @param array $server [optional]
     *        repl for $_SERVER (for test)
     */
    public function __construct(array $server = null)
    {
        if (\is_null($server)) {
            $server = $_SERVER;
        }
        $this->fields = array();
        foreach (self::$headers as $k => $v) {
            $this->fields[$k] = isset($server[$v]) ? $server[$v] : null;
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
        if (!isset(self::$headers[$key])) {
            throw new \LogicException('Client->'.$key.' is not found');
        }
        return $this->fields[$key];
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
        throw new \LogicException('Client instance is read-only');
    }

    /**
     * @var array
     */
    private static $headers = array(
        'ip' => 'REMOTE_ADDR',
        'port' => 'REMOTE_PORT',
        'userAgent' => 'HTTP_USER_AGENT',
        'acceptLanguage' => 'HTTP_ACCEPT_LANGUAGE',
        'acceptEncoding' => 'HTTP_ACCEPT_ENCODING',
    );

    /**
     * @var array
     */
    private $fields;
}
