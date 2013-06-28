<?php
/**
 * Defining the parameters of arguments
 *
 * @package go\Request
 * @author  Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI\Helpers;

class Params
{
    /**
     * Get list options for block
     *
     * @param string $opt
     *        option (without "-")
     * @param string $f [optional]
     *        format (by default "mixed")
     * @return array
     *         name => value
     */
    public static function getBlockShortOptions($opt, $f = null)
    {
        $result = array();
        switch ($f) {
            case null:
            case 'mixed':
                $result[$opt] = true;
                break;
            case 'equal':
                $opt = \explode('=', $opt, 2);
                $result[$opt[0]] = isset($opt[1]) ? $opt[1] : true;
                break;
            case 'list':
                $len = \strlen($opt);
                for ($i = 0; $i < $len; $i++) {
                    $result[$opt[$i]] = true;
                }
                break;
            case 'value':
                $result[$opt[0]] = \substr($opt, 1) ?: true;
                break;
            default:
                throw new \InvalidArgumentException('Invalid short option format "'.$f.'"');
        }
        return $result;
    }
}
