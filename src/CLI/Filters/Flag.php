<?php
/**
 * Flag: only true
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

class Flag extends Base
{
    protected $errorMessage = 'Option --{{ option }} is flag (cannot take value)';

    protected function process()
    {
        if ($this->value === null) {
            $this->value = false;
        } elseif ($this->value !== true) {
            return $this->error();
        }
        return true;
    }
}
