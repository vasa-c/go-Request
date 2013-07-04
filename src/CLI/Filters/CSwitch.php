<?php
/**
 * Switch: true/false
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

class CSwitch extends Base
{
    private $switches = array(
        'on' => true,
        '1' => true,
        'true' => true,
        'yes' => true,
        'y' => true,
        'enable' => true,
        'off' => false,
        '0' => false,
        'false' => false,
        'no' => false,
        'n' => false,
        'disable' => false,
    );

    protected $errorMessage = 'Option --{{ option }} is switch (value only on/off)';

    protected function process()
    {
        $value = \strtolower($this->value);
        if (!isset($this->switches[$value])) {
            return $this->error();
        }
        $this->value = $this->switches[$value];
    }
}
