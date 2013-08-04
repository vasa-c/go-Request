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

    /**
     * @param array $vars
     * @param string $name
     * @param string $type
     * @param mixed $lvalue
     */
    public static function exists(array $vars, $name, $type, &$lvalue = null)
    {
        if (!isset($vars[$name])) {
            return false;
        }
        $value = $vars[$name];
        $lvalue = $value;
        switch ($type) {
            case null:
            case 'scalar':
                return \is_scalar($value);
            case 'float':
                $lvalue = (float)$value;
                return self::isFloat($value);
            case 'int':
                $lvalue = (int)$value;
                return self::isInt($value);
            case 'uint':
                $lvalue = (int)$value;
                return self::isUInt($value);
            case 'id':
                $lvalue = (int)$value;
                return self::isId($value);
            case 'list':
                return self::isListOfScalar($value);
            case 'dict':
                return self::isDictOfScalar($value);
            case 'array':
                return \is_array($value);
            case 'mixed':
                return true;
        }
        throw new \InvalidArgumentException('Storage: type "'.$type.'" is invalid');
    }
}
