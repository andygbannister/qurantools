<?php

require_once 'library/database.php';
require_once 'library/mail_functions.php';
require_once 'library/hash.php';
require_once 'library/flash_functions.php';

/**
 * CONSTANTS
 */

// attempted login types
define('ATTEMPTED_LOGIN_TYPE_USER_PASSWORD', 'user_password');
define('ATTEMPTED_LOGIN_TYPE_FRESH_FACE', 'fresh_face');

// auth flags for the $_SESSION variable
define('AUTH_CONSUMER_ERROR', 'auth_consumer_error');
define('AUTH_PASSWORD_RESET', 'auth_password_reset');
define('AUTH_ACCOUNT_LOCKED', 'auth_account_locked');
define('AUTH_STOP_PROCESSING', 'auth_stop_processing');

/**
 * Is there a currently logged in user?
 *
 * Use: if (is_logged_in_user()) { ... }
 *
 * @return boolean whether there is a logged in user
 *
 * This function returns true after a user has gone through
 * the entire login process, and all necessary session
 * variables have been set.
 */
function is_logged_in_user()
{
    // \codecept_debug('in real is_logged_in_user()');
    // check the relevant session var
    return isset($_SESSION['UID']);
}

/**
 * Retrieves the currently logged in user
 *
 * Use: $user = get_logged_in_user();
 *
 * @return array - row from USERS table or null if no-one logged in
 *
 * TODO: This could be made much more efficient and potentially save a bunch of
 * calls to the database if we stored the current user in a $_SESSION variable.
 * That may not be the greatest from a security perspective however. Check
 * https://www.php.net/manual/en/features.session.security.management.php
 * A global variable would also work - but not be that elegant
 */
function get_logged_in_user(): ?array
{
    $logged_in_user_id = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;

    if (empty($logged_in_user_id))
    {
        return null;
    }

    return get_user_by_id($logged_in_user_id);
}

/**
 * Sets a session variable for the page the user wants to go after logging in
 *
 * Use: set_redirect_link()
 */
function set_redirect_link()
{
    $_SESSION['auth_redirect_link'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') .
        "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

/**
 * Returns how the user trying to login
 *
 * Use: $attempted_login_type = get_attempted_login_type();
 * @return string - one of ATTEMPTED_LOGIN_TYPE_* (see defines above)
 */
function get_attempted_login_type()
{
    if (isset($_POST['EMAIL_ADDRESS']) && isset($_POST['PASSWORD']))
    {
        return ATTEMPTED_LOGIN_TYPE_USER_PASSWORD;
    }

    // The user must be someone we don't know about yet
    return ATTEMPTED_LOGIN_TYPE_FRESH_FACE;
}

/**
 * Sets session variable with various auth flags
 *
 * use: set_auth_flag(AUTH_CONSUMER_ERROR, 'Internal Error. Please try again');
 * @param string $flag - one of AUTH_CONSUMER_ERROR,
 * @param string $message    - actual error message
 * @return void
 *
 * Commonly used for error messages displayed in the relevant places on
 * login.php and (potentially) other places during authentication, as well as
 * other flags.
 */

//TODO: now that auth_ is prefixed into each constant, we don't need this
// function and can lose it. Just set $_SESSION directly.
function set_auth_flag($flag, $value)
{
    // $session_key = 'auth_'.$flag;
    $_SESSION[$flag] = $value;
}

/**
 * Redirect browser to the log-in page to collect username/password
 *
 * Use: redirect_to_login_page()
 *
 * @return void
 */
function redirect_to_login_page()
{
    // redirect to auth/login.php
    header('Location: /auth/login.php');

    // set flag so that we know to call exit in auth.php
    set_auth_flag(AUTH_STOP_PROCESSING, true);
}

/**
 * Get current client IP address
 *
 * Use: $ip = client_ip_address();
 * @return string IP address of current client
 */
function client_ip_address()
{
    // better code examples are at: http://itman.in/en/how-to-get-client-ip-address-in-php/
    $ip = $_SERVER['REMOTE_ADDR'] ?:
        ($_SERVER['HTTP_X_FORWARDED_FOR'] ?:
        $_SERVER['HTTP_CLIENT_IP']);
    // error_log('in client_ip_address(): '.$ip);
    return $ip;
}

/**
 * Determine whether a given IP address is valid
 *
 * Use: if (is_valid_ip('129.74.0.0') { ... }
 *
 * @param string $ip    - IP address we are validating
 *
 * @return boolean      - is the given IP valid
 */
function is_valid_ip(string $ip)
{
    // use native PHP function to check validity of IPV4 ip address
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

/**
 * Determine whether a given IP range is valid
 *
 * Use: if (is_valid_ip_range('129.74.0.0-129.74.0.10') { ... }
 *
 * @param string $ip_range  - IP range we are validating
 *
 * @return bool             - is the given IP range valid
 */
function is_valid_ip_range(string $ip_range): bool
{
    // ensure it contains a hyphen
    if (strpos($ip_range, '-') === false)
    {
        return false;
    }

    // ensure there are (at least) two components
    list($lower, $upper) = explode('-', $ip_range, 2);
    if (empty($lower) || empty($upper))
    {
        return false;
    }

    // ensure each component is a valid IP
    if (!is_valid_ip($lower) || !is_valid_ip($upper))
    {
        return false;
    }

    return true;
}

/**
 * Attempt to log a consumer user in by username & password
 *
 * Use: attempt_consumer_login();
 *
 * @return void
 *
 * Checks for valid email/password combo for consumer user. If so, it continues
 * the login, otherwise redirects to other pages.
 */
function attempt_consumer_login(): void
{
    $email    = $_POST['EMAIL_ADDRESS'] ?? null;
    $password = $_POST['PASSWORD'] ?? null;

    // Do we have an email and password?
    if (empty($email) || empty($password))
    {
        set_auth_flag(
            AUTH_CONSUMER_ERROR,
            'Internal Error: Please try again or alert Qur`an Tools'
        );

        redirect_to_login_page();
        return;
    }

    $user = get_user_by_email($email);

    if (!validate_email_password($user, $email, $password))
    {
        redirect_to_login_page();
        return;
    }

    log_user_in($user);
}

/**
 * Validate email and password of a consumer or IP range surfer
 *
 * Use:     if (!validate_email_password($user, $email, $password)) { ... }
 * @param array $user      - row from USERS table
 * @param string $email    - surfer provided email address
 * @param string $password - surfer provided email address
 * @return boolean         - could we validate this user by username/password?
 *
 * In addition to checking the username/password match, this function also keeps
 * track of failed attempts to log-in and password resets.
 */
function validate_email_password($user, $email, $password)
{
    if (empty($user))
    {
        // since no $user was provided to the function, most likely the email
        // that the surfer provided does not exist in the USERS table. But we
        // don't need to tell hackers that...
        set_auth_flag(
            AUTH_CONSUMER_ERROR,
            'Sorry, your email address and/or password was not recognised.'
        );
        return false;
    }

    if ($user['Password Hash'] == PASSWORD_RESET_TEXT)
    {
        set_auth_flag(
            AUTH_CONSUMER_ERROR,
            'Your password has been reset by an administrator and you must choose a new one.'
        );
        set_auth_flag(AUTH_PASSWORD_RESET, true);
        return false;
    }

    if (is_user_locked($user))
    {
        if (is_lock_time_passed($user))
        {
            $user = unlock_user($user);
        }
        else
        {
            // update fail count (to protect against brute force attacks)
            $user = increment_fail_count_and_time($user);
            set_auth_flag(
                AUTH_CONSUMER_ERROR,
                'Due to multiple incorrect password attempts, your account is currently locked for ' .
                    ACCOUNT_LOCK_TIME_MINUTES .
                    " minutes.</p><p>If you have forgotten your password, you can request a <a href='/auth/request_password_reset.php'>password reset code</a> via email."
            );
            set_auth_flag(AUTH_ACCOUNT_LOCKED, true);
            return false;
        }
    }

    // we are dealing with an unlocked user now.

    if (
        !hash_equals(
            $user['Password Hash'],
            crypt($password, $user['Password Hash'])
        )
    ) {
        $error_message = 'Sorry, your email address and/or password was not recognised.';
        $user          = increment_fail_count_and_time($user);

        if (is_user_locked($user))
        {
            $error_message .=
                'Due to multiple incorrect password attempts, your account has been locked for the next ' .
                ACCOUNT_LOCK_TIME_MINUTES .
                " minutes.</p><p>If you have forgotten your password, you can request a <a href='/auth/request_password_reset.php'>password reset code</a> via email.";
        }
        set_auth_flag(AUTH_CONSUMER_ERROR, $error_message);
        return false;
    }

    // has the user had their account locked with a specific message to give them?

    $locked_message = mysqli_return_one_record_one_field(
        "SELECT `LOCKED WITH MESSAGE` FROM `USERS` WHERE `Email Address`='" .
            db_quote($email) .
            "'"
    );

    if ($locked_message)
    {
        set_auth_flag(AUTH_CONSUMER_ERROR, $locked_message);
        return false;
    }

    // If we got to here, the surfer must have submitted a good email/password
    // combo within the allowed number of tries within the allowed time period.
    // They are therefore validated.
    return true;
}

/**
 * Housekeeping needed on a freshly authenticated surfer
 *
 * Use: log_user_in($user)
 *
 * @param array $user        - row from USERS table
 * @return void
 *
 * Populates session vars and updates the database with some stats for logins
 */
function log_user_in(array $user): void
{
    global $config;

    set_user_session_variables($user);

    session_regenerate_id();

    // unset any session variables that may have been used during login
    unset(
        $_SESSION['auth_redirect_link'],
        $_SESSION['auth_password_reset'],
        $_SESSION['auth_account_locked'],
        $_SESSION['auth_consumer_error'],
        $_SESSION['auth_stop_processing']
    );

    // TODO: refactor these into a new function
    $timestamp = date('Y-m-d H:i:s');
    update_user_after_login($user, $timestamp);
    insert_login_log($user);
}

/**
 * Do stuff for logging a user out
 *
 * Use: log_user_out()
 *
 * @return void
 *
 * we don't need to know who we are logging out since this function
 * just resets a bunch of cookies, server and session vars to make sure
 * that no-one is logged in.
 */
function log_user_out(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies'))
    {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}

/**
 * Has auth_stop_processing been set?
 *
 * Use: if (is_auth_stop_processing()) { ... }
 *
 * @return bool - whether the flag has been set or not
 *
 * Instead of calling 'exit' in a function in order to stop processing, we set
 * a flag instead. If this flag is set, then exit is called outside of a
 * function - which means that testing will continue
 */
function is_auth_stop_processing(): bool
{
    $result = isset($_SESSION['auth_stop_processing']) &&
        $_SESSION['auth_stop_processing'];

    return $result;
}

/**
 * Checks that happen when a logged in user is accessing a page
 *
 * Use: handle_logged_in_user($user);
 *
 * @param array $user      - row from USERS table
 *
 * @return void
 *
 * - Check their user name and password still match
 */
function handle_logged_in_user($user): void
{
    // Not sure why we are doing this...
    if (
        isset($_SESSION['Email Address']) &&
        isset($_SESSION['password_hash']) &&
        !get_user_by_email_password_hash(
            $_SESSION['Email Address'],
            $_SESSION['password_hash']
        )
    ) {
        handle_email_password_hash_mismatch($user);
    }
}

/**
 * Processing for when the logged in user's email and password suddenly don't match
 *
 * Use:handle_email_password_hash_mismatch($user);
 *
 * @param array $user      - row from USERS table
 * @return void
 *
 * Not sure why we need to do this... Maybe it is a check
 * against a malicious user spoofing a session variable?
 */
function handle_email_password_hash_mismatch($user = [])
{
    log_user_out($user);
    redirect_to_login_page();
}
