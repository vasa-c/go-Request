<?php
/**
 * PHP and data of request
 *
 * @package go/request
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/vasa-c/go-Request/master/LICENSE MIT
 * @link https://github.com/vasa-c/go-Request repository
 * @link https://github.com/vasa-c/go-Request/blob/master/README.md documentation
 * @link https://packagist.org/packages/go/request composer
 * @uses PHP5.4+
 */

namespace go\Request;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
