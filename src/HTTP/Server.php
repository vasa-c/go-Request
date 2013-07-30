<?php
/**
 * HTTP request: server
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

/**
 * @property-read string $ip
 * @property-read string $port
 * @property-read string $name
 * @property-read string $soft
 * @property-read string $interface
 * @property-read string $root
 */
class Server extends Helpers\Fields
{
    protected $name = 'Server';

    protected $headers = array(
        'ip' => 'SERVER_ADDR',
        'port' => 'SERVER_PORT',
        'name' => 'SERVER_NAME',
        'soft' => 'SERVER_SOFTWARE',
        'interface' => 'GATEWAY_INTERFACE',
        'root' => 'DOCUMENT_ROOT',
    );
}
