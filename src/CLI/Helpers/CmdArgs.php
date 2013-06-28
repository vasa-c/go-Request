<?php
/**
 * Convert between string and array forms
 *
 * @package go\Request
 * @author  Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Helpers;

class CmdArgs
{
    /**
     * Convert command line to array of args
     *
     * @param string $cmd
     * @return array
     */
    public static function convertCmdToArgs($cmd)
    {
        $parser = new CmdParser($cmd);
        return $parser->getArgs();
    }

    /**
     * Convert array of args to command line
     *
     * @param array $args
     * @return string
     */
    public static function convertArgsToCmd(array $args)
    {
        $cmd = array();
        foreach ($args as $arg) {
            $cmd[] = self::convertSingleArgToCmdPart($arg);
        }
        return \implode(' ', $cmd);
    }

    /**
     * Convert component from args to part of command line
     *
     * @param string $arg
     * @return string
     */
    public static function convertSingleArgToCmdPart($arg)
    {
        $arg = \addcslashes($arg, '"');
        if (\strpos($arg, ' ') !== false) {
            if ($arg[0] == '-') {
                $arg = \explode('=', $arg, 2);
                if (\strpos($arg[0], ' ')) {
                    $arg = '"'.$arg[0].(isset($arg[1]) ? ('='.$arg[1]) : '').'"';
                } else {
                    $arg = $arg[0].(isset($arg[1]) ? ('="'.$arg[1].'"') : '');
                }
            } else {
                $arg = '"'.$arg.'"';
            }
        }
        return $arg;
    }
}
