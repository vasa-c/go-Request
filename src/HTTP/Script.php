<?php
/**
 * HTTP-request: script parameters
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

/**
 * @property-read string $filename
 * @property-read string $relative
 */
class Script extends Helpers\Fields
{
    protected $name = 'Script';

    protected $headers = array(
        'filename' => 'SCRIPT_FILENAME',
        'relative' => 'SCRIPT_NAME',
    );
}
