<?php
/**
 * Static functions for filters
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c
 */

namespace go\Request\CLI\Filters;

class Filters
{
    /**
     * Create filter for option by parameters
     *
     * @param mixed $filter
     * @param string $option
     * @return \go\Request\CLI\Filters\IFilter
     */
    public static function createFilter($filter, $option)
    {
        if (!\is_object($filter)) {
            if (\is_array($filter)) {
                $classname = $filter[0];
                $params = isset($filter[1]) ? $filter[1] : array();
            } else {
                $classname = $filter;
                $params = array();
            }
            if (\strpos($classname, '\\') !== 0) {
                if ($classname == 'Switch') {
                    $classname = 'CSwitch'; // hack: switch is keyword
                }
                $classname = __NAMESPACE__.'\\'.$classname;
            }
            if (!\class_exists($classname, true)) {
                throw new \InvalidArgumentException('Class '.$classname.' is not found');
            }
            $filter = new $classname($option, $params);
        }
        return $filter;
    }

    /**
     * Run chain filters for option value
     *
     * @throws \go\Request\CLI\Filters\Error
     *         invalid option value
     * @param array $params
     *        list filters params
     * @param string $option
     *        option name
     * @param mixed $value
     *        initial option value
     * @return mixed
     *         result option value
     */
    public static function runChainFilters(array $params, $option, $value)
    {
        foreach ($params as $p) {
            $value = self::createFilter($p, $option)->filter($value);
        }
        return $value;
    }
}
