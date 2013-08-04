<?php
/**
 * Helper for Storage::loadForm
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\HTTP\Helpers;

class LoadForm
{
    /**
     * @param string $name
     * @param array $settings
     * @param array $vars
     * @return array
     */
    public static function getDataForm($name, array $settings, array $vars)
    {
        if (!$name) {
            if (!isset($settings['name'])) {
                return $vars;
            }
            $name = $settings['name'];
        }
        if (!isset($vars[$name])) {
            return null;
        }
        $result = $vars[$name];
        if (!\is_array($result)) {
            return null;
        }
        return $result;
    }

    /**
     * @param string $name
     * @param array $settings
     * @param array $vars
     * @throws \Exception
     */
    public static function load($name, array $settings, array $vars)
    {
        $vars = self::getDataForm($name, $settings, $vars);
        $return = empty($settings['return']) ? 'object' : $settings['return'];
        if ($vars) {
            if (isset($settings['format'])) {
                $result = self::formatLoad($vars, $settings, ($return == 'object'));
            } elseif (isset($settings['fields'])) {
                $result = self::simpleLoad($vars, $settings);
            } else {
                throw new \LogicException('Storage::loadForm(): loading type is not defined');
            }
        } else {
            $result = null;
        }
        if (\is_null($result)) {
            if (!empty($settings['throws'])) {
                throw new \RuntimeException('Storage::loadForm(): form is not loaded');
            }
            return null;
        }
        switch ($return) {
            case 'object':
                $result = (object)$result;
                break;
            case 'array':
                break;
            case 'Storage':
                $result = new \go\Request\HTTP\Storage($result);
                break;
            default:
                throw new \LogicException('Storage::loadForm(): invalid return type "'.$return.'"');
        }
        return $result;
    }

    private static function simpleLoad(array $vars, array $settings)
    {
        $fields = $settings['fields'];
        $checks = empty($settings['checks']) ? null : $settings['checks'];
        $strict = !empty($settings['strict']);
        return LoadFormSimple::load($vars, $fields, $checks, $strict);
    }

    private static function formatLoad(array $vars, array $settings, $asobject)
    {
        $strict = !empty($settings['strict']);
        return LoadFormFormat::load($vars, $settings['format'], $asobject, $strict);
    }
}
