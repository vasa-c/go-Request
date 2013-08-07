<?php
/**
 * PHP and data of request
 *
 * @package go\Request
 * @author  Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @vestion 1.0-beta
 * @link https://github.com/vasa-c/go-Request source
 * @link https://github.com/vasa-c/go-Request/wiki documentation
 * @uses PHP 5.3+
 */

namespace go\Request;

const VERSION = '1.0-beta';

/**
 * Autoloader for go\Request classes
 */
final class Autoloader
{
    /**
     * Register autoloader for this lib
     */
    public static function register()
    {
        if (!self::$autoloader) {
            self::$autoloader = new self(__NAMESPACE__, __DIR__);
            \spl_autoload_register(self::$autoloader);
        }
    }

    /**
     * Register autoloader for unit tests this lib
     *
     * @param string $dir [optional]
     *        root dir of unit tests
     */
    public static function registerForTests($dir = null)
    {
        if (!self::$autoloaderForTests) {
            $dir = $dir ?: \realpath(__DIR__.'/../tests');
            self::$autoloaderForTests = new self('go\Tests\Request', $dir);
            \spl_autoload_register(self::$autoloaderForTests);
        }
    }

    /**
     * Constructor
     *
     * @param string $namespace
     * @param string $dir
     */
    private function __construct($namespace, $dir)
    {
        $this->namespace = $namespace;
        $this->dir = $dir;
    }

    /**
     * Invoke - load class by name
     *
     * @param string $classname
     */
    public function __invoke($classname)
    {
        $prefix = $this->namespace.'\\';
        if (\strpos($classname, $prefix) !== 0) {
            return;
        }
        $short = \substr($classname, \strlen($prefix));
        $filename = \str_replace('\\', \DIRECTORY_SEPARATOR, $short);
        $filename = $this->dir.\DIRECTORY_SEPARATOR.$filename.'.php';
        if (\is_file($filename)) {
            require_once($filename);
        }
    }

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var callable
     */
    private static $autoloader;

    /**
     * @var callable
     */
    private static $autoloaderForTests;
}
