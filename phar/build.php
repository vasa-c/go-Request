#!/usr/bin/env php
<?php
/**
 * Utility: build phar archive
 *
 * @author Grigoriev Oleg aka vasa_c
 */

require_once(__DIR__.'/../src/Autoloader.php');
\go\Request\Autoloader::register();

$task = new \go\Request\Build\PharBuild();
$task->run();
