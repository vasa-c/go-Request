<?php
/**
 * HTTP request: connection
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

/**
 * @property-read boolean $https
 * @property-read string $method
 * @property-read string $status
 * @property-read string $redirect
 * @property-read string $protocol
 * @property-read string $httpVersion
 */
class Connection extends Helpers\Fields
{
    protected $name = 'Server';

    protected $headers = array(
        'method' => 'REQUEST_METHOD',
        'status' => 'HTTP_CONNECTION',
        'redirect' => 'REDIRECT_STATUS',
        'protocol' => 'SERVER_PROTOCOL',
    );

    protected function getField($key)
    {
        switch ($key) {
            case 'https':
                return (!empty($this->server['HTTPS']));
            case 'httpVersion':
                $protocol = $this->__get('protocol');
                if (!$protocol) {
                    return null;
                }
                if (!\preg_match('~^HTTP/(.*?)$~s', $protocol, $matches)) {
                    return null;
                }
                return $matches[1];
        }
        $this->notfound($key);
    }
}
