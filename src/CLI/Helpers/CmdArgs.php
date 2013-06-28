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
}
