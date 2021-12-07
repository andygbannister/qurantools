<?php

// The stuff in this bootstrap only applies to unit tests and should live
// in tests/unit rather than at the top level. But it wouldn't work in that
// folder. It's probably just a matter of changing all the paths slightly.

include __DIR__ . '/../vendor/autoload.php'; // composer autoload

$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'appDir'       => __DIR__ . '/..',
    'debug'        => true,
    'includePaths' => [
        __DIR__ . '/../vendor/google/recaptcha',
        __DIR__ . '/../app/library' // not sure if this is actually used...
    ],
    'excludePaths' => [__DIR__, '/../vendor/codeception', '/../vendor/phpunit'],
    'cacheDir'     => '/tmp/qt'
]);

// Load any non-composer classes that need to be transmogrified by AspectMock.
// This is typically done with an autoload.php
// $kernel->loadFile(__DIR__.'/../library/Dumb.php');
// echo __DIR__.'/../library/functions.php';
// $kernel->loadFile(__DIR__.'/../auth/auth_functions.php');
// $kernel->loadFile(__DIR__.'/../library/functions.php');
// $kernel->loadFile(__DIR__.'/../library/flash_functions.php');
// $kernel->loadPhpFiles(__DIR__.'/../auth');
// $kernel->loadPhpFiles(__DIR__.'/../library');

set_include_path(
    get_include_path() . PATH_SEPARATOR . getcwd() . DIRECTORY_SEPARATOR . 'app'
);

// config.php needs to know $_SERVER['DOCUMENT_ROOT']
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . 'app/';

global $config;

// load all the config here. We can over-ride it later on in unit tests
// require_once __DIR__.'/../app/library/config.php';
require_once 'app/library/config.php';

// $config_file_path = '../qt.ini';

// $tries = 0;

// while (!file_exists($config_file_path))
// {
//     $tries++;
//     if ($tries > 10)
//     {
//         header('Location: /config_error.php');
//     }
//     $config_file_path = '../'.$config_file_path;
// }

// // load the configuration file
// $config = parse_ini_file($config_file_path);

// // email addresses
// QT_ADMIN_EMAIL        = 'foo@example.com'; // QT admin email
// $config['qt_developers'][0]      = 'foo@example.com'; // email of developer to receive alert emails

// // other
// $config['main_app_url']                = 'https://localhost:4010';
// $config['privacy_policy_path']         = '/privacy-policy';
// $config['cookie_policy_path']          = '/cookie-policy';
// $config['minimum_full_name_length']    = 7; // combined character length of first and last name for consumer users
// $config['is_maintenance_mode_enabled'] = false;
