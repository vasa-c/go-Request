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

}
