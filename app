#!/usr/bin/env php
<?php

use app\base\application;

/** Error Reporting */
ini_set('display_errors', true);
error_reporting(E_ALL);

/** App Path definition */
defined('APP_PATH') ||
define('APP_PATH', __DIR__ . '/');

/** Register autoloader + set up "app" namespace */
$loader = require __DIR__ . '/vendor/autoload.php';
//$loader->addPsr4('app\\', APP_PATH);

/** Init Application */
application::$loader = $loader;
application::init();
