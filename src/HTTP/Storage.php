<?php
/**
 * Storage of vars
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

class Storage
{
    /**
     * Constructor
     *
     * @param array $vars
     *        variables
     * @param boolean $ex
     *        executable by default
     * @param boolean $trust
     *        is request trusted?
     */
    public function __construct(array $vars, $ex = false, $trust = true)
    {
        $this->vars = $vars;
        $this->ex = $ex;
        $this->trust = $trust;
    }

    /**
     * Get all variables as array
     *
     * @return array
     */
    public function getAllVars()
    {
        return $this->vars;
    }

    /**
     * Variables
     *
     * @var array
     */
    private $vars;

    /**
     * Executable by default
     *
     * @var boolean
     */
    private $ex;

    /**
     * Is request trusted?
     *
     * @var boolean
     */
    private $trust;
}
