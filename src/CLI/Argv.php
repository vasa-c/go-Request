<?php
/**
 * Basic parsing arguments
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

use \go\Request\CLI\Helpers\CmdArgs;
use \go\Request\CLI\Helpers\Params;

class Argv
{
    /**
     * Create instance from command line
     *
     * @param string $cmd
     * @param string $fshort [optional]
     * @return \go\Request\CLI\Argv
     */
    public static function createFromString($cmd, $fshort = null)
    {
        return new self($cmd, null, null, $fshort);
    }

    /**
     * Create instance from array of arguments
     *
     * @param array $args
     * @param string $fshort [optional]
     * @return \go\Request\CLI\Argv
     */
    public static function createFromArray(array $args, $fshort = null)
    {
        return new self(null, $args, null, $fshort);
    }

    /**
     * Create instance from arguments parameters
     *
     * @param array $params
     * @param string $fshort [optional]
     * @return \go\Request\CLI\Argv
     */
    public static function createFromParams(array $params, $fshort = null)
    {
        return new self(null, null, $params, $fshort);
    }

    /**
     * Get instance for system argv
     *
     * @param string $fshort [optional]
     * @return \go\Request\CLI\Argv
     */
    public static function getSystemArgv($fshort = null)
    {
        $fshort = $fshort ?: 'mixed';
        if (!isset(self::$systems[$fshort])) {
            $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
            self::$systems[$fshort] = self::createFromArray($args, $fshort);
        }
        return self::$systems[$fshort];
    }

    /**
     * Private constructor
     *
     * @param string $cmd
     * @param array $args
     * @param array $params
     * @param string $fshort
     */
    private function __construct($cmd, $args, $params, $fshort)
    {
        $this->fshort = $fshort ?: 'mixed';
        $this->cmd = $cmd;
        $this->args = $args;
        $this->params[$this->fshort] = $params;
    }

    /**
     * Get args as string
     *
     * @return string
     */
    public function getCommandLine()
    {
        if (\is_null($this->cmd)) {
            $this->cmd = CmdArgs::convertArgsToCmd($this->getArgsArray());
        }
        return $this->cmd;
    }

    /**
     * Get args as array
     *
     * @return array
     */
    public function getArgsArray()
    {
        if (\is_null($this->args)) {
            if (!\is_null($this->cmd)) {
                $this->args = CmdArgs::convertCmdToArgs($this->cmd);
            } elseif (!\is_null($this->params[$this->fshort])) {
                $this->args = Params::convertParamsToArgs($this->params[$this->fshort], $this->fshort);
            } else {
                $this->args = array();
            }
        }
        return $this->args;
    }

    /**
     * Get parameters of arguments
     *
     * @param string $fshort [optional]
     * @return array
     */
    public function getParams($fshort = null)
    {
        $fshort = $fshort ?: $this->fshort;
        if (!isset($this->params[$fshort])) {
            $this->params[$fshort] = Params::getParamsForArgs($this->getArgsArray(), $fshort);
        }
        return $this->params[$fshort];
    }

    /**
     * Get list of arguments (not options)
     *
     * @return array
     */
    public function getListArguments()
    {
        if (\is_null($this->listargs)) {
            $this->listargs = array();
            foreach ($this->getArgsArray() as $arg) {
                if (\strpos($arg, '-') !== 0) {
                    $this->listargs[] = $arg;
                }
            }
        }
        return $this->listargs;
    }

    /**
     * Get options by types (short/long)
     *
     * @param string $fshort [optional]
     * @return array
     */
    public function getOptionsByTypes($fshort = null)
    {
        $fshort = $fshort ?: $this->fshort;
        if (!isset($this->options[$fshort])) {
            $short = array();
            $long = array();
            foreach ($this->getParams($fshort) as $p) {
                if ($p['option']) {
                    if ($p['short']) {
                        $short[$p['name']] = $p['value'];
                    } else {
                        $long[$p['name']] = $p['value'];
                    }
                }
            }
            $this->options[$fshort] = array(
                'short' => $short,
                'long' => $long,
            );
        }
        return $this->options[$fshort];
    }

    /**
     * Get all options
     *
     * @param string $fshort [optional]
     * @return array
     */
    public function getMixedOptions($fshort = null)
    {
        $options = $this->getOptionsByTypes($fshort);
        return \array_merge($options['short'], $options['long']);
    }

    /**
     * @var array
     */
    private static $systems = array();

    /**
     * @var string
     */
    private $cmd;

    /**
     * @var array
     */
    private $args;

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var string
     */
    private $fshort;

    /**
     * @var array
     */
    private $listargs;

    /**
     * @var array
     */
    private $options = array();
}
