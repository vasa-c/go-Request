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
 * @property-read string $referer
 */
class Client extends Helpers\Fields
{
    protected $name = 'Client';

    protected $headers = array(
        'ip' => 'REMOTE_ADDR',
        'port' => 'REMOTE_PORT',
        'userAgent' => 'HTTP_USER_AGENT',
        'acceptLanguage' => 'HTTP_ACCEPT_LANGUAGE',
        'acceptEncoding' => 'HTTP_ACCEPT_ENCODING',
        'referer' => 'HTTP_REFERER'
    );
}
