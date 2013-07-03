<?php
/**
 * Interface of option filter
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

interface IFilter
{
    /**
     * Filtering value
     *
     * @throws \go\Request\CLI\Filters\Error
     *         invalid value
     * @param mixed $value
     *        original value
     * @return mixed
     *         result value
     */
    public function filter($value);
}
