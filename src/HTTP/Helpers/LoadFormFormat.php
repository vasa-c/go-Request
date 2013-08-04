<?php
/**
 * Helper: load form by format
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class LoadFormFormat
{
    /**
     * @param mixed $params
     * @return array
     */
    public static function normalizeParams($params)
    {
        if (\is_scalar($params)) {
            return array(
                'type' => ($params === true) ? 'scalar' : $params,
            );
        }
        if (!isset($params['type'])) {
            $params['type'] = isset($params['format']) ? 'array' : 'scalar';
        }
        return $params;
    }

    /**
     * @param mixed $value
     * @param array $params
     * @return mixed
     */
    public static function filter($value, array $params)
    {
        if (isset($params['trim'])) {
            $trim = $params['trim'];
            if ($trim === 'left') {
                $value = \ltrim($value);
            } elseif ($trim === 'right') {
                $value = \rtrim($value);
            } else {
                $value = \trim($value);
            }
        }
        if (isset($params['filter'])) {
            $value = \call_user_func($params['filter'], $value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @param array $params
     * @return boolean
     */
    public static function validate($value, array $params)
    {
        if (!empty($params['notempty'])) {
            if (\is_string($value) && (\strlen($value) === 0)) {
                return false;
            }
        }
        if (!empty($params['enum'])) {
            if (!\in_array($value, $params['enum'])) {
                return false;
            }
        }
        if (!empty($params['match'])) {
            if (!\preg_match($params['match'], $value)) {
                return false;
            }
        }
        if (!empty($params['maxlength'])) {
            if (\function_exists('mb_strlen')) {
                $len = \mb_strlen($value, 'UTF-8');
            } else {
                $len = \strlen($value);
            }
            if ($len > $params['maxlength']) {
                return false;
            }
        }
        if (!empty($params['range'])) {
            $range = $params['range'];
            if (isset($range[0]) && ($value < $range[0])) {
                return false;
            }
            if (isset($range[1]) && ($value > $range[1])) {
                return false;
            }
        }
        if (!empty($params['validator'])) {
            if (!\call_user_func($params['validator'], $value)) {
                return false;
            }
        }
        return true;
    }
}
