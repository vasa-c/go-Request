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
}
