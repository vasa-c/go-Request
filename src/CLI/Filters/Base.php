<?php
/**
 * Basic class of filters
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

abstract class Base implements IFilter
{
    /**
     * Pattern for errorMessage
     *
     * @var string
     */
    protected $errorMessage = 'Invalid value for option "{{ option }}"';

    /**
     * Constructor
     *
     * @param string $option
     *        option name
     * @param array $params [optional]
     *        parameters of filter
     */
    final public function __construct($option, array $params = null)
    {
        $this->option = $option;
        $this->params = $params ?: array();
    }

    /**
     * @override \go\Request\CLI\Filters\IFilter
     *
     * @throws \go\Request\CLI\Filters\Error
     * @param mixed $value
     * @return mixed
     */
    final public function filter($value)
    {
        $this->value = $value;
        return $this->value;
    }

    /**
     * Process filtering
     *
     * @throws \go\Request\CLI\Filters\Error
     */
    abstract public function process();

    /**
     * Filtering error
     *
     * @thorws \go\Request\CLI\Filters\Error
     * @param string $message [optional]
     */
    final public function error($message = null)
    {
        $message = $message ?: $this->errorMessage;
        $message = \str_replace('{{ option }}', $this->option, $message);
        throw new Error($message, $this->option, $this->value);
    }

    /**
     * @var string
     */
    protected $option;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var mixed
     */
    protected $value;
}

