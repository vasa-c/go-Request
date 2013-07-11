<?php
/**
 * Value: requires value
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

class Value extends Base
{
    protected $errorMessage = 'It requires value for --{{ option }}';

    protected function process()
    {
        if ($this->value === null) {
            $this->value = '';
        } elseif (\is_bool($this->value)) {
            $this->error();
        }
        return true;
    }
}
