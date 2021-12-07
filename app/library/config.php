<?php

// Define ABSPATH as this file's parent directory (the /app folder)
if (!defined('ABSPATH'))
{
    define('ABSPATH', __DIR__ . '/../');
}

// qt.ini should be in the folder above the document root
$config_file_path = ABSPATH . '../qt.ini';

// load the configuration file
$config = parse_ini_file($config_file_path);

// Set the include path to include the document root of the server, and also
// the folder one above - which contains vendor. This ensures that php include
// and require directives find the right files.
// According to <http://www.geeksengine.com/article/php-include-path.html>
// there is a slight performance hit for doing this for every script load, and it would be better to set it in php.ini
set_include_path(
    get_include_path() . PATH_SEPARATOR . ABSPATH
);

set_include_path(
    get_include_path() . PATH_SEPARATOR . ABSPATH . '..'
); // for the vendor folder which is above the document root

ini_set('display_errors', $config['display_errors_locally']);
error_reporting(E_ALL);

if (!defined('OPEN_SOURCE_REPO_URL'))
{
    define('OPEN_SOURCE_REPO_URL', 'https://github.com/qurantools/qt');
}

if (!defined('QT_LICENSE_URL'))
{
    define('QT_LICENSE_URL', ($config['main_app_url'] ?? '') . ($config['license_path'] ?? ''));
}

if (!defined('QT_TERMS_URL'))
{
    define('QT_TERMS_URL', ($config['main_app_url'] ?? '') . ($config['terms_path'] ?? ''));
}

// Sometimes we want to display the message in an exception to a user, other
// times, it is only for developer or logging purposes. Raising an exception
// with this code indicates that it is safe to show the message to an end user
if (!defined('USER_DISPLAYABLE_EXCEPTION'))
{
    define('USER_DISPLAYABLE_EXCEPTION', 77);
}

// Since global variables are considered poor form in PHP, these are also
// defined as constants which could eventually replace the global variables -
// although a settings table would be the best solution

if (!defined('QT_ADMIN_EMAIL'))
{
    define('QT_ADMIN_EMAIL', $config['qt_admin_email'] ?? 'info@' . $config['hostname']);
}

if (!defined('MAXIMUM_PASSWORD_ATTEMPTS'))
{
    define('MAXIMUM_PASSWORD_ATTEMPTS', $config['maximum_password_attempts'] ?? 5);
}

if (!defined('ACCOUNT_LOCK_TIME_MINUTES'))
{
    define('ACCOUNT_LOCK_TIME_MINUTES', $config['account_lock_time_minutes'] ?? 15);
}

if (!defined('PASSWORD_RESET_TEXT'))
{
    define('PASSWORD_RESET_TEXT', $config['password_reset_text'] ?? 'PASSWORD HAS BEEN RESET');
}

// The format of password reset codes
if (!defined('PASSWORD_RESET_GROUPS'))
{
    define('PASSWORD_RESET_GROUPS', 6);
}

if (!defined('PASSWORD_RESET_GROUP_LENGTH'))
{
    define('PASSWORD_RESET_GROUP_LENGTH', 6);
}

if (!defined('DIGITS_AND_UPPER_CASE_LETTERS'))
{
    define('DIGITS_AND_UPPER_CASE_LETTERS', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
}

if (!defined('DIGITS_AND_ALL_LETTERS'))
{
    define(
        'DIGITS_AND_ALL_LETTERS',
        '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    );
}

// Google reCAPTCHA constants
if (!defined('GOOGLE_RECAPTCHA_MODE_V2_TICK'))
{
    define('GOOGLE_RECAPTCHA_MODE_V2_TICK', 'v2_tick');
}

if (!defined('GOOGLE_RECAPTCHA_MODE_V3'))
{
    define('GOOGLE_RECAPTCHA_MODE_V3', 'v3');
}

if (!defined('GOOGLE_RECAPTCHA_KEY_TYPE_SITE'))
{
    define('GOOGLE_RECAPTCHA_KEY_TYPE_SITE', 'site');
}

if (!defined('GOOGLE_RECAPTCHA_KEY_TYPE_SECRET'))
{
    define('GOOGLE_RECAPTCHA_KEY_TYPE_SECRET', 'secret');
}

if (!defined('GOOGLE_RECAPTCHA_V3_ACTION_REGISTER_INTEREST'))
{
    define('GOOGLE_RECAPTCHA_V3_ACTION_REGISTER_INTEREST', 'register_interest');
}

if (!defined('GOOGLE_RECAPTCHA_V3_ACTION_RESET_PASSWORD'))
{
    define('GOOGLE_RECAPTCHA_V3_ACTION_RESET_PASSWORD', 'password_reset');
}
