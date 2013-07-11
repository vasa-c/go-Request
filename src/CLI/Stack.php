<?php
/**
 * Stack subtasks
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

class Stack
{
    /**
     * Create instance from command line
     *
     * @param string $cmd
     * @return \go\Request\CLI\Stack
     */
    public static function createFromString($cmd)
    {
        return new self(Helpers\CmdArgs::convertCmdToArgs($cmd));
    }

    /**
     * Create instance from args array
     *
     * @param array $args
     * @return \go\Request\CLI\Stack
     */
    public static function createFromArray(array $args)
    {
        return new self($args);
    }

    /**
     * Create instance from Argv object
     *
     * @param \go\Request\CLI\Argv $argv
     * @return \go\Request\CLI\Stack
     */
    public static function createFromArgv(Argv $argv)
    {
        return new self($argv->getArgsArray());
    }

    /**
     * Create from system argv
     *
     * @return \go\Request\CLI\Stack
     */
    public static function createFromSystem()
    {
        return new self(isset($_SERVER['argv']) ? $_SERVER['argv'] : array());
    }

    /**
     * Constructor
     *
     * @param array $args
     *        args as array
     */
    private function __construct(array $args)
    {
        $this->args = $args;
    }

    /**
     * Get single next argument
     *
     * @return string
     *         argument value or NULL if ended
     */
    public function getNextArgument()
    {
        do {
            if (empty($this->args)) {
                return null;
            }
            $arg = \array_shift($this->args);
        } while (\substr($arg, 0, 1) == '-');
        return $arg;
    }

    /**
     * Get list next arguments (until options)
     *
     * @return array
     */
    public function getListNextArguments()
    {
        $this->loadNextComponents(true);
        return $this->loadNextComponents(false);
    }

    /**
     * Get all remaining arguments
     *
     * @return array
     */
    public function getAllArguments()
    {
        $args = array();
        foreach ($this->args as $arg) {
            if (\strpos($arg, '-') !== 0) {
                $args[] = $arg;
            }
        }
        $this->args = array();
        return $args;
    }

    /**
     * Load options by format
     *
     * @param mixed $format
     *        format config, format object, short-parsing value or NULL
     * @return \go\Request\CLI\Options
     */
    public function getNextOptions($format = null)
    {
        if (\is_array($format)) {
            $format = Format::normalizeConfig($format);
            $short = $format['short_parsing'];
        } elseif ($format instanceof Format) {
            $format = $format->getConfig();
            $short = $format['short_parsing'];
        } elseif ($format === null) {
            $short = 'list';
        } else {
            $short = $format;
            $format = null;
        }
        $opts = $this->loadNextComponents(true);
        if (!empty($opts)) {
            $argv = Argv::createFromArray($opts);
            $opts = $argv->getOptionsByTypes($short);
        } else {
            $opts = array(
                'short' => array(),
                'long' => array(),
            );
        }
        return new Options($opts, $format);
    }

    /**
     * @param boolean $options
     * @return array
     */
    private function loadNextComponents($options)
    {
        $args = array();
        while (!empty($this->args)) {
            $arg = $this->args[0];
            $isopt = (\strpos($arg, '-') === 0);
            if ($options === $isopt) {
                $args[] = $arg;
                \array_shift($this->args);
            } else {
                break;
            }
        }
        return $args;
    }

    /**
     * @var array
     */
    private $args;
}
