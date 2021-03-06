<?php
/**
 * Aggregator for request parameters
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request;

/**
 * @property-read \go\Request\CLI\Argv $args
 * @property-read \go\Request\HTTP\HTTPRequest $http
 * @property-read \go\Request\HTTP\Storage $get
 * @property-read \go\Request\HTTP\Storage $post
 * @property-read \go\Request\HTTP\Storage $cookie
 * @property-read string $sapi
 * @property-read string $type
 * @property-read boolean $isCLI
 * @property-read boolean $isHTTP
 * @todo $env
 */
class Request
{
    /**
     * Get instance for system parameters
     *
     * @return \go\Request\Request
     */
    public static function getInstanceForSystem()
    {
        if (!self::$systemInstance) {
            self::$systemInstance = new self();
        }
        return self::$systemInstance;
    }

    /**
     * Constructor
     *
     * @param array $context [optional]
     */
    public function __construct(array $context = null)
    {
        $this->context = array(
            'server' => $_SERVER,
            'headers' => null,
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            'sapi' => \PHP_SAPI,
        );
        if (isset($context['trusted'])) {
            $this->trusted = $context['trusted'];
            $this->finallyTrusted = true;
        }
        if ($context) {
            $this->context = \array_merge($this->context, $context);
            $this->rcontext = true;
        }
        if (isset($this->context['subs'])) {
            foreach ($this->context['subs'] as $k => $v) {
                if (!\array_key_exists($k, $this->subs)) {
                    throw new \LogicException('Invalid subs for Request: "'.$k.'"');
                }
                $this->subs[$k] = $v;
            }
        }
        $this->subs['sapi'] = $this->context['sapi'];
        if ($this->subs['sapi'] == 'cli') {
            $this->subs['type'] = 'cli';
            $this->subs['isHTTP'] = false;
            $this->subs['isCLI'] = true;
        } else {
            $this->subs['type'] = 'http';
            $this->subs['isHTTP'] = true;
            $this->subs['isCLI'] = false;
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
            throw new \LogicException('Request->'.$key.' is not found');
        }
        $value = $this->subs[$key];
        if (\is_null($value)) {
            $value = $this->createSub($key);
            $this->subs[$key] = $value;
        }
        return $value;
    }

    /**
     * Magic set
     *
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    public function __set($key, $value)
    {
        throw new \LogicException('Request instance is read-only');
    }

    /**
     * Set trust for request
     *
     * @param boolean $trusted
     * @throws \LogicException
     */
    public function setTrust($trusted)
    {
        if ($this->finallyTrusted) {
            throw new \LogicException('Request->setTrust() is forbidden');
        }
        $this->trusted = $trusted;
        $this->finallyTrusted = true;
        $subs = array('get', 'post', 'cookie');
        foreach ($subs as $k) {
            if (\is_object($this->subs[$k])) {
                $this->subs[$k]->setTrust($trusted, true);
            }
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function createSub($key)
    {
        $server = $this->context['server'];
        switch ($key) {
            case 'args':
                $args = isset($server['argv']) ? $server['argv'] : array();
                return CLI\Argv::createFromArray($args);
            case 'http':
                if ($this->rcontext) {
                    $context = array(
                        'server' => $server,
                        'getllaheaders' => false,
                        'headers' => $this->context['headers'],
                    );
                } else {
                    $context = null;
                }
                return new HTTP\HTTPRequest($context);
            case 'get':
                $storage = new HTTP\Storage($this->context['get'], false);
                $storage->setTrust($this->trusted, $this->finallyTrusted);
                return $storage;
            case 'cookie':
                $storage = new HTTP\Storage($this->context['cookie'], false);
                $storage->setTrust($this->trusted, $this->finallyTrusted);
                return $storage;
            case 'post':
                $storage = new HTTP\Storage($this->context['post'], true);
                $storage->setTrust($this->trusted, $this->finallyTrusted);
                return $storage;
        }
    }

    /**
     * @var array
     */
    private $context;

    /**
     * @var array
     */
    private $subs = array(
        'args' => null,
        'http' => null,
        'get' => null,
        'post' => null,
        'cookie' => null,
        'sapi' => null,
        'type' => null,
        'isCLI' => null,
        'isHTTP' => null,
    );

    /**
     * @var \go\Request\Request
     */
    private static $systemInstance;

    /**
     * @var boolean
     */
    private $rcontext = false;

    /**
     * Is request trusted?
     *
     * @var boolean
     */
    private $trusted = true;

    /**
     * @var boolean
     */
    private $finallyTrusted = false;
}
