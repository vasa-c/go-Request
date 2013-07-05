<?php
/**
 * Format of command call
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Request\CLI;

class Format
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = self::normalizeConfig($config);
    }

    /**
     * Get normalized config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Normalize config (default values)
     *
     * @param array $config
     * @return array
     */
    public static function normalizeConfig(array $config)
    {
        $config = \array_merge(self::$defaultConfig, $config);
        foreach ($config['options'] as &$v) {
            if ($v === true) {
                $v = self::$defaultOption;
            } else {
                $v = \array_merge(self::$defaultOption, $v);
            }
        }
        return $config;
    }

    /**
     * @var array
     */
    private static $defaultConfig = array(
        'title' => null,
        'version' => null,
        'copyright' => null,
        'usage' => null,
        'allow_unknown' => false,
        'short_parsing' => 'list',
        'options' => array(),
    );

    /**
     * @var array
     */
    private static $defaultOption = array(
        'title' => null,
        'short' => null,
        'default' => null,
        'required' => false,
        'filter' => null,
        'filters' => null,
    );

    /**
     * @var array
     */
    private $config;
}
