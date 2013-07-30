<?php
/**
 * Access to headers of HTTP-request
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c
 */

namespace go\Request\HTTP;

/**
 * @property-read string $accept
 * @property-read string $accept_encoding
 * @property-read string $accept_language
 * @property-read string $cache_control
 * @property-read string $connection
 * @property-read string $content-type
 * @property-read string $content-length
 * @property-read string $cookie
 * @property-read string $host
 * @property-read string $user_agent
 * @property-read string $x_forvarded_for
 * @property-read string $x_real_ip
 */
class Headers implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Constructor
     *
     * @param array $headers [optional]
     *        headers as list "header: value"
     */
    public function __construct(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $k => $v) {
            $k = \strtolower($k);
            $this->headers[$k] = $v;
        }
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public function getAllHeaders()
    {
        return $this->headers;
    }

    /**
     * Get header by name
     *
     * @param string $name
     * @param string $default [optional]
     * @return string|null
     */
    public function getHeader($name, $default = null)
    {
        $name = \strtolower($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : $default;
    }

    /**
     * Magic get
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $key = \str_replace('_', '-', $key);
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * Magic set
     *
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     *         forbidden
     */
    public function __set($key, $value)
    {
        throw new \LogicException('Headers instance is read-only');
    }

    /**
     * Magic isset
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        $key = \str_replace('_', '-', $key);
        return isset($this->headers[$key]);
    }

    /**
     * Magic unset
     *
     * @param string $key
     * @throws \LogicException
     *         forbidden
     */
    public function __unset($key)
    {
        throw new \LogicException('Headers instance is read-only');
    }

    /**
     * @override \Countable
     *
     * @return int
     */
    public function count()
    {
        return \count($this->headers);
    }

    /**
     * @override \IteratorAggregate
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->headers);
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->headers[$offset]);
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @return string|null
     */
    public function offsetGet($offset)
    {
        return isset($this->headers[$offset]) ? $this->headers[$offset] : null;
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @param mixed $value
     * @throws \LogicException
     *         forbidden
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Headers instance is read-only');
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @return \LogicException
     *         forbidden
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Headers instance is read-only');
    }

    /**
     * @var array
     */
    private $headers;
}
