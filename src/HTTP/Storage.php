<?php
/**
 * Storage of vars
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP;

use go\Request\HTTP\Helpers\Validator;

/**
 * @method string scalar(string $name)
 * @method float float(string $name)
 * @method int int(string $name)
 * @method int uint(string $name)
 * @method int id(string $name)
 * @method array list(string $name)
 * @method array dict(string $name)
 * @method array array(string $name)
 * @method mixed mixed(string $name)
 */
class Storage implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Constructor
     *
     * @param array $vars
     *        variables
     * @param boolean $ex
     *        executable by default
     * @param boolean $trusted
     *        is request trusted?
     */
    public function __construct(array $vars, $ex = false, $trusted = true)
    {
        $this->vars = $vars;
        $this->ex = $ex;
        $this->trusted = $trusted;
    }

    /**
     * Get all variables as array
     *
     * @param boolean $onlyscalar [optional]
     *        return only scalar variables
     * @return array
     */
    public function getAllVars($onlyscalar = false)
    {
        return $onlyscalar ? $this->getListOfScalar() : $this->vars;
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
     * Magic get (only scalar)
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key, null);
    }

    /**
     * Magic isset (only scalar)
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->exists($key, null);
    }

    /**
     * Magic set (forbidden)
     *
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    public function __set($key, $value)
    {
        throw new \LogicException('Storage instance is read-only');
    }

    /**
     * Magic unset
     *
     * @param string $key
     * @throws \LogicException
     */
    public function __unset($key)
    {
        throw new \LogicException('Storage instance is read-only');
    }

    /**
     * Magic call
     *
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        if (empty($args)) {
            throw new \LogicException('Format: Storage->'.$method.'($type)');
        }
        \array_splice($args, 1, 0, $method);
        return \call_user_func_array(array($this, 'get'), $args);
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    /**
     * @override \ArrayAccess
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset, null);
    }

    /**
     * @override \ArrayAccess (forbidden)
     *
     * @param string $offset
     * @param mixed $value
     * @throws \LogicException
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Storage instance is read-only');
    }

    /**
     * @override \ArrayAccess (forbidden)
     *
     * @param string $offset
     * @throws \LogicException
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Storage instance is read-only');
    }

    /**
     * @override \Countable
     *
     * @return int
     */
    public function count()
    {
        return \count($this->getListOfScalar());
    }

    /**
     * @override \IteratorAggregate
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getListOfScalar());
    }

    /**
     * Get value from allowed list
     *
     * @param string $name
     * @param array $allowed
     * @param boolean $ex [optional]
     * @return mixed
     */
    public function getEnum($name, array $allowed, $ex = null)
    {
        if (!isset($this->vars[$name])) {
            return null;
        }
        $value = $this->vars[$name];
        if (!\is_scalar($value)) {
            return null;
        }
        if (!\in_array($value, $allowed)) {
            return null;
        }
        return $value;
    }

    /**
     * Is request truested?
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return $this->trusted;
    }

    /**
     * Set request trust
     *
     * @param boolean $trusted
     * @param boolean $finally
     * @throws \LogicException
     */
    public function setTrust($trusted, $finally = false)
    {
        if ($this->finallyTrust) {
            throw new \LogicException('Storage->setTrust() is forbidden');
        }
        $this->trusted = $trusted;
        $this->finallyTrust = $finally;
    }

    /**
     * @return array
     */
    private function getListOfScalar()
    {
        if (!$this->listOfScalar) {
            $this->listOfScalar = array();
            foreach ($this->vars as $k => $v) {
                if (\is_scalar($v)) {
                    $this->listOfScalar[$k] = $v;
                }
            }
        }
        return $this->listOfScalar;
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
    private $trusted;

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

    /**
     * List of scalar variables only
     *
     * @var array
     */
    private $listOfScalar;

    /**
     * $trusted is finally
     *
     * @var boolean
     */
    private $finallyTrust = false;
}
