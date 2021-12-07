<?php

// This script is called by most QT pages to check user is logged in.
// if not, it runs them through the login procedure

if (session_status() == PHP_SESSION_NONE)
{
    session_start();
}

require_once 'auth/auth_functions.php';
require_once "library/database.php";

$logged_in_user = get_logged_in_user();

if ($logged_in_user)
{
    handle_logged_in_user($logged_in_user);
}
else
{
    // work out the page to redirect to after successful login
    set_redirect_link();

    // how is this surfer trying to log-in?
    $attempted_login_type = get_attempted_login_type();
    // error_log('$attempted_login_type: ' . $attempted_login_type);
    switch ($attempted_login_type) {
        case ATTEMPTED_LOGIN_TYPE_USER_PASSWORD:
            attempt_consumer_login();
            break;
        case ATTEMPTED_LOGIN_TYPE_FRESH_FACE:
            redirect_to_login_page();
            break;
        default:
            error_log('\$attempted_login_type is null');
            set_auth_flag(AUTH_CONSUMER_ERROR, 'Internal Error. Please try again');
            redirect_to_login_page();
            break;
    }
    // these will be set to null if no-one is logged in
    $logged_in_user = get_logged_in_user();
}

if (is_auth_stop_processing())
{
    $a = [1, 2, 3];
    /**
     * we are redirecting to another page on the website, so don't keep
     * processing this request - which would show the user what comes after
     * the 'include auth.php' on the calling page
     */
    exit;
}
