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
     * @param string $fshort [optional]
     *        format (by default "mixed")
     * @return array
     *         name => value
     */
    public static function getBlockShortOptions($opt, $fshort = null)
    {
        $result = array();
        switch ($fshort) {
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

    /**
     * Get params of arguments
     *
     * @param array $args
     *        array of arguments
     * @param string $fshort [optional]
     *        format short options
     * @return array
     *         array of params
     */
    public static function getParamsForArgs(array $args, $fshort = null)
    {
        $result = array();
        foreach ($args as $arg) {
            if (\preg_match('~^(-{1,2})(.+)$~s', $arg, $matches)) {
                if ($matches[1] == '-') {
                    foreach (self::getBlockShortOptions($matches[2], $fshort) as $k => $v) {
                        $result[] = array(
                            'option' => true,
                            'short' => true,
                            'name' => $k,
                            'value' => $v,
                        );
                    }
                } else {
                    $arg = \explode('=', $matches[2], 2);
                    $result[] = array(
                        'option' => true,
                        'short' => false,
                        'name' => $arg[0],
                        'value' => isset($arg[1]) ? $arg[1] : true,
                    );
                }
            } else {
                $result[] = array(
                    'option' => false,
                    'value' => $arg,
                );
            }
        }
        return $result;
    }

    /**
     * Convert parameters to arguments array
     *
     * @param array $params
     * @param string $fshort [optional]
     * @return array
     */
    public static function convertParamsToArgs(array $params, $fshort = null)
    {
        $args = array();
        foreach ($params as $p) {
            if ($p['option']) {
                $name = $p['name'];
                $value = $p['value'];
                if ($p['short']) {
                    switch ($fshort) {
                        case null:
                        case 'mixed':
                        case 'list':
                            $value = '';
                            break;
                        case 'equal':
                            $value = '='.$value;
                            break;
                    }
                    $args[] = '-'.$name.$value;
                } else {
                    $args[] = '--'.$name.(($value === true) ? '' : '='.$value);
                }
            } else {
                $args[] = $p['value'];
            }
        }
        return $args;
    }
}
