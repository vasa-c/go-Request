<?php
/**
 * HTTP request: http-document
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c
 */

namespace go\Request\HTTP;

/**
 * @property-read string $uri
 * @property-read string $absolute
 * @property-read string $path
 * @property-read string $host
 * @property-read int $port
 * @property-read string $query
 * @property-read array $components
 * @property-read string $info
 * @property-read boolean $https
 */
class Document
{
    /**
     * @const int
     */
    const HTTP_PORT = 80;

    /**
     * @const int
     */
    const HTTPS_PORT = 443;

    /**
     * Constructor
     *
     * @param array $server [optional]
     */
    public function __construct(array $server = null)
    {
        $server = $server ?: $_SERVER;
        $this->subs = array();
        if (isset($server['HTTP_HOST'])) {
            $this->subs['host'] = $server['HTTP_HOST'];
        } elseif (isset($server['SERVER_NAME'])) {
            $this->subs['host'] = $server['SERVER_NAME'];
        } else {
            $this->subs['host'] = '';
        }
        $uri = isset($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '';
        $this->subs['uri'] = $uri;
        $uri = \explode('?', $uri, 2);
        $this->subs['path'] = $uri[0];
        $this->subs['query'] = isset($uri[1]) ? $uri[1] : '';
        $this->subs['info'] = isset($server['PATH_INFO']) ? $server['PATH_INFO'] : '';
        $this->subs['https'] = !empty($server['HTTPS']);
        if (isset($server['SERVER_PORT'])) {
            $this->subs['port'] = $server['SERVER_PORT'];
        } else {
            $this->subs['port'] = $this->subs['https'] ? self::HTTPS_PORT : self::HTTP_PORT;
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
            switch ($key) {
                case 'absolute':
                    $this->subs['absolute'] = $this->createAbsolute();
                    break;
                case 'components':
                    $this->subs['components'] = $this->createComponents();
                    break;
                default:
                    throw new \LogicException('Document->'.$key.' is not found');
            }
        }
        return $this->subs[$key];
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
        throw new \LogicException('Document instance is read-only');
    }

    /**
     * @return string
     */
    private function createAbsolute()
    {
        $result = array();
        $https = $this->subs['https'];
        $result[] = $https ? 'https://' : 'http://';
        $result[] = $this->subs['host'];
        $dport = $https ? self::HTTPS_PORT : self::HTTP_PORT;
        if ($this->subs['port'] != $dport) {
            $result[] = ':'.$this->subs['port'];
        }
        $result[] = $this->subs['uri'];
        return \implode('', $result);
    }

    /**
     * @return array
     */
    private function createComponents()
    {
        $result = \explode('/', $this->subs['path']);
        if (!empty($result)) {
            \array_shift($result);
        }
        if ((!empty($result)) && empty($result[\count($result) - 1])) {
            \array_pop($result);
        }
        return $result;
    }

    /**
     * @var array
     */
    private $subs;
}
