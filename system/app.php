<?php

/*
|--------------------------------------------------------------------------
| Disable direct access
|--------------------------------------------------------------------------
|
*/

if (!defined('APP_RUN')) {
    exit('No direct access allowed');
}

/*
|--------------------------------------------------------------------------
| Application Build Number to handle cache & updates
|--------------------------------------------------------------------------
|
*/
$spEntry = 'config';

define('APP_MODE', 'default');

/*
|--------------------------------------------------------------------------
| Load the config file to connect with database and handle url
|--------------------------------------------------------------------------
|
*/

if (file_exists('system/' . $spEntry . '.php')) {
    require 'system/' . $spEntry . '.php';
} else {
    header('location: install');

    exit();
}

/*
|--------------------------------------------------------------------------
| To load composer autoload
|--------------------------------------------------------------------------
|
*/

require APP_BASE_PATH.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Start the Application
|--------------------------------------------------------------------------
|
*/

require APP_SYSTEM_PATH.'/helpers/bootstrap.php';
