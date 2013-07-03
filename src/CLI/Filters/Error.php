<?php
/**
 * Error filtering: invalid value
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Filters;

class Error extends \Exception
{
    /**
     * Constructor
     *
     * @param string $message
     * @param string $option
     * @param mixed $value
     */
    public function __construct($message, $option, $value)
    {
        parent::__construct($message, 0);
        $this->option = $option;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @var string
     */
    private $option;

    /**
     * @var mixed
     */
    private $value;
}
