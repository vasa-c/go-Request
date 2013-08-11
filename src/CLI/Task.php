<?php
/**
 * Run task and subtasks from console
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

abstract class Task
{
    /**
     * Constructor
     *
     * @param \go\Request\CLI\Stack $stack
     * @param array $params [optional]
     * @param boolean $first [optional]
     */
    final public function __construct(Stack $stack = null, array $params = null, $first = true)
    {
        $this->stack = $stack ?: Stack::createFromSystem();
        if ($first) {
            $this->stack->getNextArgument();
        }
        $params = $params ?: array();
        $this->params = $params;
        if (\array_key_exists('_out', $params)) {
            $this->oout = $params['_out'];
        }
        if (\array_key_exists('_error', $params)) {
            $this->oerror = $params['_error'];
        }
        if (\array_key_exists('_quiet', $params)) {
            $this->quiet = $params['_quiet'];
        }
        $this->createFormat();
    }

    /**
     * Run task
     *
     * @return boolean
     */
    final public function run()
    {
        $r = $this->loadOptions();
        if ($r !== null) {
            return $r;
        }
        return $this->process();
    }

    /**
     * Process task
     *
     * @return boolean
     */
    abstract protected function process();

    /**
     * Run subtask
     *
     * @param string $name [optional]
     *        subtack name (null - get from stack)
     * @param array $params [optional]
     * @return boolean
     */
    final protected function runSubtask($name = null, array $params = null)
    {
        if (!$name) {
            $name = $this->stack->getNextArgument();
            if (!$name) {
                $this->error('Task name not specified');
                return false;
            }
        }
        if (!isset($this->subtasks[$name])) {
            $this->error('Task "'.$name.'" is unknown');
            return false;
        }
        $task = $this->subtasks[$name];
        if (\strpos($task, '::') === 0) {
            $method = \substr($task, 2);
            return $this->$method($params);
        } else {
            $params = $params ?: array();
            if (!\array_key_exists('_out', $params)) {
                $params['_out'] = $this->oout;
            }
            if (!\array_key_exists('_error', $params)) {
                $params['_error'] = $this->oerror;
            }
            if (!\array_key_exists('_quiet', $params)) {
                $params['_quiet'] = $this->quiet;
            }
            $classname = $task;
            $task = new $classname($this->stack, $params, false);
            return $task->run();
        }
    }

    /**
     * Write message
     *
     * @param string $message
     */
    final protected function out($message)
    {
        if ($this->quiet) {
            return;
        }
        if ($this->oout === true) {
            echo $message.\PHP_EOL;
        } elseif ($this->oout !== null) {
            \call_user_func($this->oout, $message);
        }
    }

    /**
     * Write error message
     *
     * @param string $message
     */
    final protected function error($message)
    {
        if ($this->oerror === true) {
            $this->out('Error: '.$message);
        } elseif ($this->oerror !== null) {
            \call_user_func($this->oerror, $message);
        }
    }

    /**
     * Out errors list
     *
     * @param array $errors
     */
    protected function outErrors(array $errors)
    {
        foreach ($errors as $message) {
            $this->error($message);
        }
    }

    private function createFormat()
    {
        $format = $this->format;
        $options = &$format['options'];
        if ($this->oHelp && (!isset($options[$this->oHelp]))) {
            $options[$this->oHelp] = array(
                'title' => 'show help',
                'filter' => 'Flag',
            );
        }
        if ($this->oVersion && (!isset($options[$this->oVersion]))) {
            $options[$this->oVersion] = array(
                'title' => 'show version',
                'filter' => 'Flag',
            );
        }
        if ($this->oQuiet && (!isset($options[$this->oQuiet]))) {
            $options[$this->oQuiet] = array(
                'title' => 'disable output',
                'filter' => 'Flag',
            );
        }
        $this->format = new Format($format);
    }

    /**
     * @return boolean|null
     */
    private function loadOptions()
    {
        $this->options = $this->stack->getNextOptions($this->format);
        if (!$this->options->isSuccess()) {
            $this->outErrors($this->options->getErrorOptions());
            return false;
        }
        if ($this->oQuiet && $this->options->__get($this->oQuiet)) {
            $this->quiet = true;
        }
        if ($this->oHelp && $this->options->__get($this->oHelp)) {
            foreach ($this->format->getHelp(null) as $message) {
                $this->out($message);
            }
            $this->helpSubtasks();
            return true;
        }
        if ($this->oVersion && ($this->options->__get($this->oVersion))) {
            $f = $this->format->getConfig();
            if ($f['title']) {
                $this->out($f['title']);
            }
            $this->out('Version: '.($f['version'] ? $f['version'] : 'not specified'));
            return true;
        }
        return null;
    }

    /**
     * Display help for subtasks
     */
    protected function helpSubtasks()
    {
        if (empty($this->subtasks)) {
            return;
        }
        $this->out('');
        $this->out('Tasks:');
        $help = array();
        $max = 0;
        foreach ($this->subtasks as $k => $v) {
            $title = '';
            if (\strpos($v, '::') !== 0) {
                if (\class_exists($v, true)) {
                    $ref = new \ReflectionClass($v);
                    $props = $ref->getDefaultProperties();
                    if (isset($props['format']['title'])) {
                        $title = $props['format']['title'];
                    }
                }
            }
            $help[] = array($k, $title);
            $max = \max($max, \strlen($k));
        }
        foreach ($help as $v) {
            $h = $v[0].\str_repeat(' ', $max - \strlen($v[0])).' - '.$v[1];
            $this->out('   '.$h);
        }
    }

    /**
     * Format options (for override)
     *
     * @var array
     */
    protected $format = array();

    /**
     * List of subtasks (for override)
     *
     * @var array
     */
    protected $subtasks = array();

    /**
     * @var string
     */
    protected $oHelp = 'help';

    /**
     * @var string
     */
    protected $oVersion = 'version';

    /**
     * @var string
     */
    protected $oQuiet = 'quiet';

    /**
     * @var \go\Request\CLI\Stack
     */
    protected $stack;

    /**
     * @var \go\Request\CLI\Options
     */
    protected $options;

    /**
     * Is --quiet enable?
     *
     * @var boolean
     */
    private $quiet = false;

    /**
     * Out callback
     *
     * @var callable|true|null
     */
    private $oout = true;

    /**
     * Error out callback
     *
     * @var callable|true|null
     */
    private $oerror = true;
}
