<?php
/**
 * Helper for auth methods
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class Auth
{
    /**
     * @param array $params
     * @return string
     */
    public static function makeHeaderParams(array $params)
    {
        $result = array();
        foreach ($params as $k => $v) {
            $result[] = $k.'="'.\addslashes($v).'"';
        }
        return \implode(', ', $result);
    }

    /**
     * @param string $header
     * @return array
     */
    public static function parseHeaderParams($header)
    {
        $params = array();
        while (!empty($header)) {
            $name = self::loadVarName($header);
            $value = self::loadVarValue($header);
            $params[$name] = $value;
        }
        return $params;
    }

    private static function loadVarName(&$header)
    {
        $h = \explode('=', $header, 2);
        $header = isset($h[1]) ? $h[1] : '';
        return $h[0];
    }

    private static function loadVarValue(&$header)
    {
        if (\substr($header, 0, 1) !== '"') {
            $h = \explode(',', $header, 2);
            $header = isset($h[1]) ? $h[1] : '';
            return $h[0];
        }
        $header = \substr($header, 1);
        $value = array();
        while (true) {
            $h = \explode('"', $header, 2);
            $header = isset($h[1]) ? $h[1] : '';
            $h = $h[0];
            if (preg_match('~\\\\+$~', $h, $matches)) {
                if (\strlen($matches[0]) % 2 == 1) {
                    $value[] = \stripslashes(\substr($h, 0, -1));
                } else {
                    $value[] = \stripslashes($h);
                    break;
                }
            } else {
                $value[] = \stripslashes($h);
                break;
            }
        }
        $header = \preg_replace('~^\s*,\s*~s', '', $header);
        return \implode('"', $value);
    }
}
