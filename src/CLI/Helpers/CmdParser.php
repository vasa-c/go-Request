<?php
/**
 * Helper: parse command line
 *
 * @package go\Request
 * @author  Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Helpers;

class CmdParser
{
    /**
     * Constructor
     *
     * @param string $cmd
     *        command line
     */
    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * Get all components from cmd
     *
     * @return array
     */
    public function getArgs()
    {
        $args = array();
        do {
            $c = $this->getNextArg();
            $eol = \is_null($c);
            if (!$eol) {
                $args[] = $c;
            }
        } while (!$eol);
        return $args;
    }

    /**
     * Get next component from cmd
     *
     * @return string
     *         next component or NULL if EOL
     */
    private function getNextArg()
    {
        $this->cmd = \ltrim($this->cmd);
        if (empty($this->cmd)) {
            return null;
        }
        if (!\preg_match('~^(.*?)([\s"\'\\\\])(.*?)$~s', $this->cmd, $matches)) {
            $result = $this->cmd;
            $this->cmd = '';
            return $result;
        }
        $value = $matches[1];
        $sep = $matches[2];
        $this->cmd = $matches[3];
        if ($sep == '\\') {
            $char = \substr($this->cmd, 0, 1);
            $this->cmd = \substr($this->cmd, 1);
            $value .= $char.$this->getNextArg();
        } elseif (($sep == '"') || ($sep == "'")) {
            $value .= $this->getInlineValue($sep);
            $len = \strlen($this->cmd);
            $this->cmd = \ltrim($this->cmd);
            if ($len == \strlen($this->cmd)) {
                $value .= $this->getNextArg();
            }
        }
        return $value;
    }

    /**
     * @param string $quot
     * @return string
     */
    private function getInlineValue($quot)
    {
        $result = null;
        $pos = 0;
        while (true) {
            $pos = \strpos($this->cmd, $quot, $pos);
            if ($pos === false) {
                $result = $this->cmd;
                $this->cmd = '';
                return $result;
            } elseif (($pos == 0) || ($this->cmd[$pos - 1] != '\\')) {
                $result = \substr($this->cmd, 0, $pos);
                $this->cmd = \substr($this->cmd, $pos + 1);
                return $result;
            }
            $this->cmd = \substr($this->cmd, 0, $pos - 1).\substr($this->cmd, $pos);
        }
        return $result;
    }

    /**
     * @var string
     */
    private $cmd;
}
