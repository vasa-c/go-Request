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
     *         TRUE - var is exists
     * @throws \InvalidArgumentException
     *         type is invalid
     */
    public function exists($name, $type = null, $ex = null)
    {
        if (!isset($this->vars[$name])) {
            return false;
        }
        $value = $this->vars[$name];
        $this->last = $value;
        switch ($type) {
            case null:
            case 'scalar':
                return \is_scalar($value);
            case 'float':
                $this->last = (float)$value;
                return Validator::isFloat($value);
            case 'int':
                $this->last = (int)$value;
                return Validator::isInt($value);
            case 'uint':
                $this->last = (int)$value;
                return Validator::isUInt($value);
            case 'id':
                $this->last = (int)$value;
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
     * Get value of variable
     *
     * @param string $name
     *        name of var
     * @param string $type [optional]
     *        type of var
     * @param mixed $default [optional]
     *        default value
     * @param boolean $ex [optional]
     *        executable var
     * @return mixed
     *         value or default
     * @throws \InvalidArgumentException
     *         type is invalid
     */
    public function get($name, $type = null, $default = null, $ex = null)
    {
        if ($type === 'check') {
            return isset($this->vars[$name]);
        }
        if (!$this->exists($name, $type, $ex)) {
            return $default;
        }
        return $this->last;
    }

    /**
     * Get child storage
     *
     * @param string $name
     *        name of variable
     * @param boolean $throw [optional]
     *        throw exception if not exists
     * @return \go\Request\HTTP\Storage
     *         child object or NULL if not exists
     * @throws \InvalidArgumentException
     *         var is not exists
     */
    public function child($name, $throw = false)
    {
        if (isset($this->childs[$name])) {
            return $this->childs[$name];
        }
        if (!isset($this->vars[$name])) {
            if ($throw) {
                throw new \InvalidArgumentException('Storage->'.$name.' is not exist');
            } else {
                return null;
            }
        }
        $vars = $this->vars[$name];
        if (!\is_array($vars)) {
            if ($throw) {
                throw new \InvalidArgumentException('Storage->'.$name.' is not array');
            } else {
                return null;
            }
        }
        $child = new self($vars, $this->ex, $this->trust);
        $this->childs[$name] = $child;
        return $child;
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

    /**
     * Last value
     *
     * @var mixed
     */
    private $last;

    /**
     * Cache of childs
     *
     * @var array
     */
    private $childs = array();
}
