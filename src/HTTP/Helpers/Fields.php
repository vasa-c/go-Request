<?php
/**
 * Basic class for client, server and etc
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class Fields
{
    /**
     * Constructor
     *
     * @param array $server [optional]
     *        repl for $_SERVER (for test)
     */
    public function __construct(array $server = null)
    {
        $this->server = $server ?: $_SERVER;
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
        if (!\array_key_exists($key, $this->fields)) {
            if (isset($this->headers[$key])) {
                $skey = $this->headers[$key];
                if (isset($this->server[$skey])) {
                    $this->fields[$key] = $this->server[$skey];
                } else {
                    $this->fields[$key] = null;
                }
            } else {
                $this->fields[$key] = $this->getField($key);
            }
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
        throw new \LogicException($this->name.' instance is read-only');
    }

    /**
     * For override
     *
     * @param string $key
     * @return mixed
     * @throws \LogicException
     */
    protected function getField($key)
    {
        $this->notfound($key);
    }

    /**
     * @param string $key
     * @throws \LogicException
     */
    protected function notfound($key)
    {
        throw new \LogicException($this->name.'->'.$key.' is not found');
    }

    /**
     * For override
     *
     * @var string
     */
    protected $name;

    /**
     * For override
     *
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $server;
}
