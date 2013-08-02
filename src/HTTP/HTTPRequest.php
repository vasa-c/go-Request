<?php
/**
 * Container for http-request parameters
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

/**
 * @property-read \go\Request\HTTP\Auth $auth
 * @property-read \go\Request\HTTP\Client $client
 * @property-read \go\Request\HTTP\Connection $connection
 * @property-read \go\Request\HTTP\Document $document
 * @property-read \go\Request\HTTP\Headers $headers
 * @property-read \go\Request\HTTP\Server $server
 * @property-read \go\Request\HTTP\Script $script
 * @property-read string $postData
 */
class HTTPRequest
{
    /**
     * Constructor
     *
     * @param array $context [optional]
     *        (for tests [getallheaders, headers, server, subs])
     * @throws \LogicException
     */
    public function __construct(array $context = null)
    {
        $this->context = $context ?: array();
        $this->server = isset($this->context['server']) ? $this->context['server'] : $_SERVER;
        if (isset($this->context['subs'])) {
            foreach ($this->context['subs'] as $k => $v) {
                if (!isset($this->subs[$k])) {
                    throw new \LogicException('Invalid subservice "'.$k.'" for HTTPRequest');
                }
                $this->subs[$k] = $v;
            }
        }
    }

    /**
     * Magic get
     *
     * @param string $key
     * @return mixed
     * @thorws \LogicException
     */
    public function __get($key)
    {
        if (!isset($this->subs[$key])) {
            throw new \LogicException('HTTPRequest->'.$key.' is not found');
        }
        if (!$this->subs[$key]) {
            $this->subs[$key] = $this->createSub($key);
        }
        return $this->subs[$key];
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
        throw new \LogicException('HTTPReques is read-only');
    }

    /**
     * Get plain request
     *
     * @param boolean $body [optional]
     * @param string $sep [optional]
     * @return string
     */
    public function getPlain($body = true, $sep = "\r\n")
    {
        $top = array('REQUEST_METHOD', 'REQUEST_URI', 'SERVER_PROTOCOL');
        foreach ($top as &$v) {
            if (isset($_SERVER[$v])) {
                $v = $_SERVER[$v];
            } else {
                $v = '';
            }
        }
        unset($v);
        $result = \implode(' ', $top).$sep;
        $headers = $this->__get('headers')->getListPlainHeaders();
        if (!empty($headers)) {
            $result .= \implode($sep, $headers).$sep;
        }
        if ($body) {
            $data = $this->__get('postData');
            if (!empty($data)) {
                $result .= $sep.$data;
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @return object
     */
    private function createSub($key)
    {
        if ($key == 'headers') {
            return new \go\Request\HTTP\Headers($this->loadHeaders());
        }
        if ($key == 'postData') {
            return @\file_get_contents('php://input');
        }
        $classname = 'go\Request\HTTP\\'.\ucfirst($key);
        return new $classname($this->server);
    }

    /**
     * @return array
     */
    private function loadHeaders()
    {
        if (isset($this->context['headers'])) {
            return $this->context['headers'];
        }
        $func = isset($this->context['getallheaders']) ? $this->context['getallheaders'] : 'getallheaders';
        if ($func && (\is_callable($func))) {
            return \call_user_func($func);
        }
        return Helpers\Server::loadHTTPHeaders($this->server);
    }

    /**
     * @var array
     */
    private $subs = array(
        'auth' => false,
        'client' => false,
        'connection' => false,
        'document' => false,
        'headers' => false,
        'server' => false,
        'script' => false,
        'postData' => false,
    );

    /**
     * @var array
     */
    private $server;

    /**
     * @var array
     */
    private $context;
}
