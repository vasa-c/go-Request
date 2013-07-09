<?php
/**
 * Result of parsing options
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

use go\Request\CLI\Filters\Filters;
use go\Request\CLI\Filters\Error;

class Options
{
    /**
     * Constructor
     *
     * @param array $input
     *        options in format array('short'=>array(), 'long'=>array())
     * @param array $format
     *        normalized format config
     */
    public function __construct(array $input, array $format)
    {
        $this->input = $input;
        $this->format = $format;
        $this->success = $this->process();
    }

    /**
     * Success parsing
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Get list of parsing errors
     *
     * @return array
     *         list of go\Request\CLI\Filters\Error
     */
    public function getErrorObjects()
    {
        return $this->errors;
    }

    /**
     * Get errors
     *
     * @return array
     *         option name => error message
     */
    public function getErrorOptions()
    {
        if (\is_null($this->oerrors)) {
            $this->oerrors = array();
            foreach ($this->errors as $e) {
                $this->oerrors[$e->getOption()] = $e->getMessage();
            }
        }
        return $this->oerrors;
    }

    /**
     * Get all options
     *
     * @return array
     *         option => value or NULL if error
     */
    public function getOptions()
    {
        return $this->success ? $this->options : null;
    }

    /**
     * Get loaded options (even when error)
     *
     * @return array
     */
    public function getLoadedOptions()
    {
        return $this->loaded;
    }

    /**
     * Get unknown options (allow_unkonwn is enabled)
     *
     * @return array
     */
    public function getUnknownOptions()
    {
        return $this->unknown;
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
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * @return boolean
     */
    private function process()
    {
        foreach ($this->format['options'] as $name => $params) {
            $this->processOptions($name, $params);
        }
        $this->processUnknown();
        $this->success = empty($this->errors);
        if (!$this->success) {
            $this->options = array();
        }
        return $this->success;
    }

    /**
     * @param string $name
     * @param array $params
     */
    private function processOptions($name, $params)
    {
        $value = null;
        $svalue = null;
        if ($params['short']) {
            $short = $params['short'];
            if (isset($this->input['short'][$short])) {
                $svalue = $this->input['short'][$short];
            }
            unset($this->input['short'][$short]);
        }
        if (isset($this->input['long'][$name])) {
            $value = $this->input['long'][$name];
            unset($this->input['long'][$name]);
        } elseif (!\is_null($svalue)) {
            $value = $svalue;
        }
        $load = true;
        if (\is_null($value)) {
            if ($params['required']) {
                $this->errors[] = new Error('Required option --'.$name.' is not found', $name, null);
                $load = false;
            } else {
                $value = $params['default'];
            }
        }
        if ($load) {
            if ($params['filter']) {
                $filters = array($params['filter']);
            } elseif ($params['filters']) {
                $filters = $params['filters'];
            } else {
                $filters = null;
            }
            if ($filters) {
                try {
                    $value = Filters::runChainFilters($filters, $name, $value);
                } catch (Error $e) {
                    $load = false;
                    $this->errors[] = $e;
                }
            }
            if ($load) {
                $this->options[$name] = $value;
                $this->loaded[$name] = $value;
            }
        }
    }

    private function processUnknown()
    {
        $unknown = \array_merge($this->input['short'], $this->input['long']);
        if (empty($unknown)) {
            return;
        }
        $this->unknown = $unknown;
        if ($this->format['allow_unknown']) {
            $this->options = \array_merge($this->options, $unknown);
            $this->loaded = \array_merge($this->loaded, $unknown);
        } else {
            foreach ($unknown as $name => $value) {
                $this->errors[] = new Error('Option --'.$name.' is unknown', $name, $value);
            }
        }
    }

    /**
     * @var array
     */
    private $input;

    /**
     * @var array
     */
    private $format;

    /**
     * @var boolean
     */
    private $success = false;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $loaded = array();

    /**
     * @var array
     */
    private $unknown = array();

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $oerrors = null;
}
