<?php

/**
 * -----------------------------------------------------------------------------
 *
 * General file to set any configuration values and load all the required files
 *
 * -----------------------------------------------------------------------------
 */
// -----------------------------------------------------------------------------
//  Load Core
// -----------------------------------------------------------------------------
// load important files in order
require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';
foreach (glob(__DIR__ . '/core/*.php') as $core) {
    require_once $core;
}

// -----------------------------------------------------------------------------
//  Load Middlewares
// -----------------------------------------------------------------------------
foreach (glob(__DIR__ . '/middlewares/*.php') as $middleware) {
    require_once $middleware;
}

// -----------------------------------------------------------------------------
//  Load Modules
// -----------------------------------------------------------------------------
foreach (glob(__DIR__ . '/modules/*.php') as $module) {
    require_once $module;
}

// -----------------------------------------------------------------------------
//  Load Models
// -----------------------------------------------------------------------------
require_once __DIR__ . '/models/BaseModel.php';
foreach (glob(__DIR__ . '/models/*.php') as $model) {
    require_once $model;
}

// -----------------------------------------------------------------------------
//  Load Controllers
// -----------------------------------------------------------------------------
require_once __DIR__ . '/controllers/BaseController.php';
foreach (glob(__DIR__ . '/controllers/*.php') as $controller) {
    require_once $controller;
}

// -----------------------------------------------------------------------------
//  Include the Composer autoloader for external dependencies
// -----------------------------------------------------------------------------
require_once __DIR__ . '/vendor/autoload.php';


// Set all dates to the UTC (default)
// Set this to your own needs or comment it out.
// You can get the list of all supported timezones here:
//  - http://php.net/manual/en/timezones.php
// -----------------------------------------------------------------------------
date_default_timezone_set('UTC');


//  Determine error reporting
// -----------------------------------------------------------------------------
if (Core\Config::getInstance() -> API() -> env == 'dev' || Core\Config::getInstance() -> API() -> env == 'stage') {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set("log_errors", 0);

    error_reporting(E_ALL);
} else {

    ini_set("log_errors", 1);
    ini_set("display_errors", 0);
    ini_set("error_log", __DIR__ . '/logs/error-' . date('Y-m-d') . '.log');

    error_reporting(E_ALL);
}


// Connect with the DB
// This could also be within some sort of App class.
// See the DB class to get the list of available functions
// ------------------------------------------------------------------------------
try {
    Core\Database::init();
} catch (\Exception $ex) {
    echo "Failed to connect with the database. Reason: " . $ex -> getMessage();
    // No DB, no API.
    exit;
}



