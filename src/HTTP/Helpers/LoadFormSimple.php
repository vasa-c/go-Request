<?php

namespace go\Request\HTTP\Helpers;

class LoadFormSimple
{
    /**
     * @param array $form
     * @param array $fields
     * @param array $checks [optional]
     * @param boolean $strict [optional]
     * @return array
     */
    public static function load(array $form, array $fields, array $checks = null, $strict = false)
    {
        $result = array();
        foreach ($fields as $field) {
            if (!isset($form[$field])) {
                return null;
            }
            $result[$field] = $form[$field];
        }
        $count = \count($result);
        if ($checks) {
            foreach ($checks as $field) {
                $e = isset($form[$field]);
                $result[$field] = $e;
                if ($e) {
                    $count++;
                }
            }
        }
        if ($strict && ($count != \count($form))) {
            return null;
        }
        return $result;
    }
}
