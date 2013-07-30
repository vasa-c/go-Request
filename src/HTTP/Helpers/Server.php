<?php
/**
 * Helper: parse $_SERVER array
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class Server
{
    /**
     * Get http-headers from $_SERVER
     *
     * @param array $server
     * @return array
     */
    public static function loadHTTPHeaders(array $server)
    {
        $headers = array();
        foreach ($server as $k => $v) {
            if (\strpos($k, 'HTTP_') === 0) {
                $k = \substr($k, 5);
                $k = \strtolower($k);
                $k = \str_replace('_', '-', $k);
                $headers[$k] = $v;
            }
        }
        if (!empty($server['CONTENT_TYPE'])) {
            $headers['content-type'] = $server['CONTENT_TYPE'];
        }
        if (!empty($server['CONTENT_LENGTH'])) {
            $headers['content-length'] = $server['CONTENT_LENGTH'];
        }
        return $headers;
    }
}
