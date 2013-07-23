<?php
/**
 * Storage of vars
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

use go\Request\HTTP\Helpers\Validator;

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
     * Is var exists?
     *
     * @param string $name
     *        name of var
     * @param string $type [optional]
     *        type of var
     * @param boolean $ex [optional]
     *        is variable executable?
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function exists($name, $type = null, $ex = null)
    {
        if (!isset($this->vars[$name])) {
            return false;
        }
        $value = $this->vars[$name];
        switch ($type) {
            case null:
            case 'scalar':
                return \is_scalar($value);
            case 'float':
                return Validator::isFloat($value);
            case 'int':
                return Validator::isInt($value);
            case 'uint':
                return Validator::isUInt($value);
            case 'id':
                return Validator::isId($value);
            case 'list':
                return Validator::isListOfScalar($value);
            case 'dict':
                return Validator::isDictOfScalar($value);
            case 'array':
                return \is_array($value);
            case 'mixed':
                return true;
        }
        throw new \InvalidArgumentException(__CLASS__.': type "'.$type.'" is invalid');
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
