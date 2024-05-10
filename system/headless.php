<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';
require __DIR__ . '/helpers/common_functions.php';
$db_config = [
    'driver' => 'mysql',
    'host' => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];

$db = new DB();
$db->addConnection($db_config);
$db->setAsGlobal();
$db->bootEloquent();

ORM::configure('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
ORM::configure('driver_options', [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
]);
ORM::configure('return_result_sets', true); // returns result sets

$settings_db = AppConfig::all();
$settings_key_values = [];
foreach ($settings_db as $setting) {
    $settings_key_values[$setting->setting] = $setting->value;
}

$settings = $settings_key_values;
$language_file_path = 'i18n/' . $settings['language'] . '.php';

if (!empty($settings['url_rewrite']) && $settings['url_rewrite'] == '1') {
    define('U', APP_URL . '/');
    define('BASE_URL', APP_URL . '/');
} else {
    define('U', APP_URL . '/?ng=');
    define('BASE_URL', APP_URL . '/?ng=');
}

$_L = file_exists($language_file_path) ? require $language_file_path : (require __DIR__ . '/i18n/en.php');

$overrides_language_strings = [];

if (file_exists('system/overrides/i18n.php')) {
    $overrides_language_strings = require APP_SYSTEM_PATH . '/overrides/i18n.php';
}

$_L = array_merge($_L, $overrides_language_strings);
