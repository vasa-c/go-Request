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
        'value' => false,
    );

    protected function process()
    {
        $params = \array_merge($this->defaults, $this->params);
        if ($this->value === null) {
            if ($params['value']) {
                return $this->error('It requires value for --{{ option }}');
            }
            $this->value = 0;
        }
        if (!\preg_match('~^([-+]?)([0-9]+)(\.[0-9]+)?$~', $this->value, $matches)) {
            $this->error();
        }
        if ($params['float']) {
            $this->value = (float)$this->value;
        } else {
            if (!empty($matches[3])) {
                return $this->error('--{{ option }} must be integer');
            }
            $this->value = (int)$this->value;
        }
        if (($this->value < 0) && (!$params['signed'])) {
            return $this->error('--{{ option }} must be positive number');
        }
        if (isset($params['min']) && ($this->value < $params['min'])) {
            return $this->rangeError($params);
        }
        if (isset($params['max']) && ($this->value > $params['max'])) {
            return $this->rangeError($params);
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
