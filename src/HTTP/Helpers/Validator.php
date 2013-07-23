<?php
/**
 * Check type of value
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class Validator
{
    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isFloat($value)
    {
        if (!\is_scalar($value)) {
            return false;
        }
        return (boolean)\preg_match('/^-?\d+(\.\d+)?$/s', $value);
    }

    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isInt($value)
    {
        if (!\is_scalar($value)) {
            return false;
        }
        $n = (int)$value;
        if ($value !== (string)$n) {
            return false;
        }
        return true;
    }

    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isUInt($value)
    {
        if (!\is_scalar($value)) {
            return false;
        }
        $n = (int)$value;
        if ($value !== (string)$n) {
            return false;
        }
        return ($n >= 0);
    }

    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isId($value)
    {
        if (!\is_scalar($value)) {
            return false;
        }
        $n = (int)$value;
        if ($value !== (string)$n) {
            return false;
        }
        return ($n > 0);
    }

    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isDictOfScalar($value)
    {
        if (!\is_array($value)) {
            return false;
        }
        foreach ($value as $v) {
            if (!\is_scalar($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string|array $value
     * @return boolean
     */
    public static function isListOfScalar($value)
    {
        if (!self::isDictOfScalar($value)) {
            return false;
        }
        return ($value === \array_values($value));
    }
}
