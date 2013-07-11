<?php
/**
 * Value: number
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

class Number extends Base
{
    protected $errorMessage = '--{{ option }} must be number';

    protected $defaults = array(
        'signed' => false,
        'float' => false,
        'min' => null,
        'max' => null,
    );

    protected function process()
    {
        if ($this->value === null) {
            $this->value = 0;
        }
        $params = \array_merge($this->defaults, $this->params);
        if (!\preg_match('~^([-+]?)([0-9]+)(\.[0-9]+)?$~', $this->value, $matches)) {
            $this->error();
        }
        if ($params['float']) {
            $this->value = (float)$this->value;
        } else {
            if (!empty($matches[3])) {
                $this->error('--{{ option }} must be integer');
            }
            $this->value = (int)$this->value;
        }
        if (($this->value < 0) && (!$params['signed'])) {
            $this->error('--{{ option }} must be positive number');
        }
        if (isset($params['min']) && ($this->value < $params['min'])) {
            $this->rangeError($params);
        }
        if (isset($params['max']) && ($this->value > $params['max'])) {
            $this->rangeError($params);
        }
        return true;
    }

    protected function rangeError($p)
    {
        if (isset($p['min'])) {
            if (isset($p['max'])) {
                $message = '--{{ option }} must be between '.$p['min'].' and '.$p['max'];
            } else {
                $message = '--{{ option }} must be less than '.$p['min'];
            }
        } elseif (isset($p['max'])) {
            $message = '--{{ option }} must be greater than '.$p['max'];
        } else {
            $message = 'error range for --{{ option }}';
        }
        $this->error($message);
    }
}
