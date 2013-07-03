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
     * @param string $option
     * @param mixed $filter
     * @return \go\Request\CLI\Filters\IFilter
     */
    public static function createFilter($option, $filter)
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
                $classname = __NAMESPACE__.'\\'.$classname;
            }
            $filter = new $classname($option, $params);
        }
        return $filter;
    }
}
