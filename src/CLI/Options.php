<?php
/**
 * Result of parsing options
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

class Options
{
    /**
     * Constructor
     *
     * @param array $options
     *        options in format array('short'=>array(), 'long'=>array())
     * @param array $format
     *        normalized format config
     */
    public function __construct(array $options, array $format)
    {

    }

    /**
     * Success parsing
     *
     * @return boolean
     */
    public function isSuccess()
    {

    }

    /**
     * Get list of parsing errors
     *
     * @return array
     *         list of go\Request\CLI\Filters\Error
     */
    public function getErrorObjects()
    {

    }

    /**
     * Get errors
     *
     * @return array
     *         option name => error message
     */
    public function getErrorOptions()
    {

    }

    /**
     * Get all options
     *
     * @return array
     *         option => value or NULL if error
     */
    public function getOptions()
    {

    }

    /**
     * Get loaded options (even when error)
     *
     * @return array
     */
    public function getLoadedOptions()
    {

    }

    /**
     * Get unknown options (allow_unkonwn is enabled)
     *
     * @return array
     */
    public function getUnknownOptions()
    {

    }

    /**
     * Get option value (as object property)
     *
     * @param string $key
     *        option name
     * @return mixed
     *         option value or NULL if not exists
     */
    public function __get($key)
    {

    }
}
