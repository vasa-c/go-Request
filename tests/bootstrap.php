<?php
/**
 * Initialization of unit tests for go\Request packages
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request;

use go\Request\Autoloader;

require_once(__DIR__.'/../src/Autoloader.php');

Autoloader::register();
Autoloader::registerForTests(__DIR__);
