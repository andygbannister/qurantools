<?php

require_once 'library/database.php';
require_once 'auth/auth_functions.php';
require_once 'library/mail_functions.php';

/**
 * CONSTANTS
 */

// User Types
DEFINE('USER_TYPE_CONSUMER', 'CONSUMER');
DEFINE('USER_TYPE_SYSTEM', 'SYSTEM');

// Administrator  Types
DEFINE('ADMINISTRATOR_TYPE_ADMIN', 'ADMIN');
DEFINE('ADMINISTRATOR_TYPE_SUPER_USER', 'SUPERUSER');

// Block User modes
define('BLOCK_MODE_BLOCK', 'block');
define('BLOCK_MODE_UNBLOCK', 'unblock');

/**
 * Functions related to database calls for users and the USERS table
 */

/**
 * Retrieve a row from USERS table by User ID
 *
 * Use: $user = get_user_by_id(123)
 *
 * @param int $user_id           - ID of user we are interested in
 *
 * @return object row of the user, or null
 */
function get_user_by_id($user_id = null): ?array
{
    if (empty($user_id))
    {
        throw new \Exception('Missing $user_id for get_user_by_id()');
    }

    // SQL statement retrieving a row by ID
    $query_result = db_query(
        'SELECT *
           FROM `USERS`
          WHERE `User ID`=' . db_quote($user_id)
    );

    if (db_rowcount($query_result) > 0)
    {
        return db_return_row($query_result);
    }
    else
    {
        return null;
    }
}

/**
 * Retrieve a row from USERS by email address
 *
 * Use: $user = get_user_by_email('bob@example.com');
 *
 * @param int $email           - email of the user we are interested in
 *
 * @return object row of the user, or null
 */
function get_user_by_email($email): ?array
{
    if (empty($email))
    {
        throw new \Exception('Missing $email for get_user_by_email()');
    }

    // SQL statement retrieving a row by Email
    $sql = "SELECT * 
           FROM `USERS`
          WHERE UPPER(`Email Address`)='" . db_quote(strtoupper($email)) . "'";

    $result = db_query($sql);

    return db_rowcount($result) > 0 ? db_return_row($result) : null;
}

/**
 * Retrieve a row from USERS by user name
 *
 * Use: $user = get_user_by_user_name('Bob Brown');
 *
 * @param int $user_name           - user_name of the user we are interested in
 *
 * @return object row of the user, or null
 *
 * Now that we are moving towards First and Last Names, this function is less
 * useful and will eventually be retired.
 */
function get_user_by_user_name($user_name = null)
{
    if (empty($user_name))
    {
        throw new \Exception('Missing $user_name for get_user_by_user_name()');
    }

    // SQL statement retrieving a row by user_name
    $sql = "SELECT *
           FROM `USERS`
          WHERE UPPER(`User Name`)='" . db_quote(strtoupper($user_name)) . "'";

    $result = db_query($sql);

    return db_rowcount($result) > 0 ? db_return_row($result) : null;
}

/**
 * Get the system user
 *
 * Use: $system_user = get_system_user();
 *
 * @return array row of the user, or throws
 */
function get_system_user()
{
    $sql = 'SELECT * 
           FROM `USERS`
          WHERE `User Type` = "' . USER_TYPE_SYSTEM . '"
            AND `User Name` = "System User"';
    $result = db_query($sql);

    if (db_rowcount($result) == 0)
    {
        throw new Exception('No system user in the database');
    }

    if (db_rowcount($result) > 1)
    {
        throw new Exception('Multiple system users in the database');
    }

    return db_return_row($result);
}
/**
 * Retrieve a row from USERS table by email address and password hash
 *
 * Use: $user = get_user_by_email_password_hash('bob@example.com','eksdkhfhk$#$^$%^%^%');
 *
 * @param string $email           - email of the user we are interested in
 * @param string $password_hash   - password hash of the user we are interested in
 * @return array row of the user, or null
 *
 * This is used during authentication of every page to ensure that the users
 * email address and password are what we expect they should be and haven't
 * been mysteriously changed.
 */
function get_user_by_email_password_hash($email = null, $password_hash = null)
{
    if (empty($email))
    {
        throw new \Exception(
            'Missing $email for get_user_by_email_password_hash()'
        );
    }

    if (empty($password_hash))
    {
        throw new \Exception(
            'Missing $password_hash for get_user_by_email_password_hash()'
        );
    }

    $sql = "SELECT * 
              FROM `USERS`
             WHERE UPPER(`Email Address`) = '" . db_quote(strtoupper($email)) . "'
               AND `Password Hash`        = '" . db_quote($password_hash) . "'";

    $result = db_query($sql);

    return db_rowcount($result) > 0 ? db_return_row($result) : null;
}

/**
 * Update a user row after login
 *
 * Use: update_user_after_login($user, $timestamp);
 *
 * @param array  $user      - row from USERS table to update
 * @param string $timestamp - timestamp of when the user logged in (formatted 'Y-m-d H:i:s')
 *
 * @return void
 *
 * - Increment login count
 * - Reset fail count to 0
 * - Set the last login timestamp
 */
function update_user_after_login(array $user, $timestamp = null)
{
    if (empty($timestamp))
    {
        throw new \Exception(
            'Missing $timestamp for update_user_after_login()'
        );
    }

    $sql = "UPDATE `USERS` 
               SET `Fails Count`          = 0,
                   `Login Count`          = `Login Count` + 1,
                   `Last Login Timestamp` = '" . $timestamp . "'
             WHERE `User ID`              = '" . $user['User ID'] . "'";

    $result = db_query($sql);
}

/**
 * Register a new consumer user
 *
 * Use: $new_user = register_consumer_user($insert_data);
 *
 * @param array  $insert_data - mostly user supplied $_POST data
 *
 * @return array $user        - newly inserted USER record
 *
 * - Ensures a new consumer user is inserted with consumer user fields set correctly
 */
function register_consumer_user(array $insert_data): ?array
{
    global $config;

    // Ensure no important fields are missing
    $missing_or_empty_keys = get_missing_or_empty_keys(
        ['EMAIL', 'PASSWORD'],
        $insert_data
    );
    if (!empty($missing_or_empty_keys))
    {
        throw new Exception(
            '$insert_data for ' .
                __FUNCTION__ .
                '() must contain: ' .
                join(', ', $missing_or_empty_keys)
        );
    }

    // ensure not a bogus email address
    $sanitized_email = filter_var($insert_data['EMAIL'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($insert_data['EMAIL'], FILTER_VALIDATE_EMAIL) || $sanitized_email != $insert_data['EMAIL'])
    {
        throw new Exception(
            "Invalid email address.",
            USER_DISPLAYABLE_EXCEPTION
        );
    }

    // Ensure Email address is unique
    if (get_user_by_email($insert_data['EMAIL']))
    {
        throw new Exception(
            "An account with that email address already exists. If that's your account, simply <a href='../home.php'>login</a>.",
            USER_DISPLAYABLE_EXCEPTION
        );
    }

    // Ensure name information is sufficient
    $user_name = generate_user_name(
        $insert_data['FIRST_NAME'] ?? '',
        $insert_data['LAST_NAME'] ?? ''
    );

    if (strlen($user_name) < $config['minimum_full_name_length'])
    {
        throw new Exception(
            'Your combined first and last name must be at least ' .
                $config['minimum_full_name_length'] .
                ' characters long',
            USER_DISPLAYABLE_EXCEPTION
        );
    }

    $insert_data['USER_TYPE'] = USER_TYPE_CONSUMER;

    $new_user = register_user($insert_data);

    return $new_user;
}

/**
 * Register a new user
 *
 * Use: $new_user = register_user($insert_data);
 *
 * @param array  $insert_data - mostly user supplied $_POST data
 *
 * @return array $user        - newly inserted USER record
 *
 * This function should never be called directly but only from within another
 * register_*_user function, as that function will be checking other
 * $insert_data arguments and performing other processing relating to
 * registering a new user.
 */
function register_user(array $insert_data): ?array
{
    if (
        empty($insert_data['EMAIL'])
    ) {
        throw new \Exception(
            'Arguments for register_user() must contain an email address'
        );
    }

    $insert_data = db_quote_array($insert_data);

    // Add new user to our database
    $new_user = insert_user($insert_data);

    if (empty($new_user))
    {
        return null;
    }

    return $new_user;
}

/**
 * Insert a new user
 *
 * Use: $user = insert_user($insert_data);
 *
 * @param array  $insert_data - mostly user supplied $_POST data
 *
 * @return array $user        - newly inserted USER record
 */
function insert_user(array $insert_data): ?array
{
    if (empty($insert_data))
    {
        throw new \Exception('Missing $insert_data for insert_user()');
    }

    // This code can be ditched when User Names are ditched
    $first_name = isset($insert_data['FIRST_NAME'])
        ? $insert_data['FIRST_NAME']
        : '';
    $last_name = isset($insert_data['LAST_NAME'])
        ? $insert_data['LAST_NAME']
        : '';
    $user_name = generate_user_name($first_name, $last_name);

    $sql = "INSERT INTO `USERS`
                 (`Email Address`,
                  `Password Hash`,
                  `User Name`,
                  `First Name`,
                  `Last Name`,
                  `User Type`,
                  `Administrator`,
                  `Reset Code`,
                  `Reset Timecode`,
                  `Last Login Timestamp`,
                  `Fails Count`,
                  `Fail Time`,
                  `Login Count`,
                  `AJAX Token`,
                  `Creation Date`)
            VALUES
                 (" . (!empty($insert_data['EMAIL']) ? "'" . $insert_data['EMAIL'] . "'" : ' NULL ') . ",
                  " . (!empty($insert_data['PASSWORD']) ? "'" . hash_password($insert_data['PASSWORD']) . "'" : ' NULL ') . ",
                  " . (!empty($user_name) ? "'" . $user_name . "'" : ' NULL ') . ",
                  " . (!empty($insert_data['FIRST_NAME']) ? "'" . $insert_data['FIRST_NAME'] . "'" : ' NULL ') . ",
                  " . (!empty($insert_data['LAST_NAME']) ? "'" . $insert_data['LAST_NAME'] . "'" : ' NULL ') . ",
                  '" . $insert_data["USER_TYPE"] . "',
                  " . (!empty($insert_data['ADMINISTRATOR']) ? "'" . $insert_data['ADMINISTRATOR'] . "'" : ' NULL ') . ",
                  NULL,
                  NULL,
                  NULL,
                  0,
                  NULL,
                  0,
                  ' ',
                  CURRENT_TIMESTAMP)";

    $result = db_query($sql);

    $new_user = get_user_by_email($insert_data['EMAIL']);

    return $new_user;
}

/**
 * Log the login
 *
 * Use: insert_login_log($user);
 *
 * @param array $user        - row from USERS table
 *
 * @return boolean           - whether INSERT succeeded or not
 *
 * Add a new row to the LOGIN-LOGS table with
 * - User ID
 * - Email Address
 * - Login Date
 * - Login Time
 * - Login IP
 *
 * TODO: This should be moved into a login_logs_functions.php file
 */
function insert_login_log($user)
{
    $user_id = db_quote($user['User ID']);
    $email   = db_quote($user['Email Address']);
    $ip      = client_ip_address();

    $sql = "INSERT INTO `LOGIN-LOGS` (
                `User ID`,
                `Email Address`,
                `Login Date`,
                `Login Time`,
                `Login IP`) 
            VALUES (
                '$user_id', 
                '$email', 
                '" .
        date('Y/m/d') .
        "', 
                '" .
        date('H:i:s') .
        "',
                '$ip')";

    $result = db_query($sql);
}

/**
 * Reset the Fails Count and Fails Time columns for given user
 *
 * Use: unlock_user($user);
 *
 * @param array $user      - row from USERS table to update
 *
 * @return array $user     - updated $user record
 *
 * After a user has waited the requisite time, and tries to login again, we
 * need to reset the fails count and time columns
 */
function unlock_user($user)
{
    $sql = "UPDATE `USERS` 
            SET `Fails Count` = 0, `Fail Time` = 0 
          WHERE `User ID`     = " . db_quote($user['User ID']);

    $result = db_query($sql);

    $user = get_user_by_id($user['User ID']);

    return $user;
}

/**
 * Blocks/unblocks a user
 *
 * Use: update_user_block($user, $block_mode);
 *
 * @param array $user       - row from USERS table to update
 * @param array $block_mode - Are we blocking or unblocking ('block' or 'unblock')
 *
 * @return array $user      - updated $user record, or null if it didn't work
 */
function update_user_block(array $user, string $block_mode): ?array
{
    if (!(BLOCK_MODE_BLOCK == $block_mode || BLOCK_MODE_UNBLOCK == $block_mode))
    {
        throw new \Exception('Unknown $block_mode (' . $block_mode . ') for update_user_block()', 1);
    }

    $user = update_user_by_id($user['User ID'], ['Is Blocked' => (BLOCK_MODE_BLOCK == $block_mode ? '1' : '0')]);

    return $user;
}

/**
 * Is the given user locked out of Qur'an Tools due to too many poor password attempts?
 *
 * Use: if (is_user_locked($user)) { ... }
 * @param array $user      - row from USERS table
 *
 * @return boolean         - is the user locked?
 */
function is_user_locked($user)
{
    return $user['Fails Count'] >= MAXIMUM_PASSWORD_ATTEMPTS;
}

/**
 * Is the given user blocked from Qur'an Tools by an admin?
 *
 * Use: if (is_user_blocked($user)) { ... }
 * @param array $user      - row from USERS table
 *
 * @return boolean         - is the user blocked?
 */
function is_user_blocked($user)
{
    return 1 == $user['Is Blocked'];
}

/**
 * Is the given user a consumer user?
 *
 * Use: if (is_consumer_user($user)) { ...}
 *
 * @param array $user - row from USERS table
 *
 * @return bool       - is given user a consumer?
 *
 */
function is_consumer_user(array $user): bool
{
    return USER_TYPE_CONSUMER === $user['User Type'];
}

/**
 * Is the given user an admin user of some sort?
 *
 * Use: if (is_admin_user($user)) { ...}
 *
 * @param array $user - row from USERS table
 *
 * @return bool       - is given user a consumer?
 *
 */
function is_admin_user(?array $user): bool
{
    if (empty($user))
    {
        return false;
    }

    return (ADMINISTRATOR_TYPE_ADMIN === $user['Administrator'] || ADMINISTRATOR_TYPE_SUPER_USER === $user['Administrator']);
}

/**
 * Resets users password to the a reset value
 *
 * Use: reset_password($user);
 *
 * @param array $user      - row from USERS table to update
 *
 * @return void
 */
function reset_password($user)
{
    $sql = "UPDATE `USERS` 
            SET `Password Hash`='" . PASSWORD_RESET_TEXT . "'
          WHERE `User ID`=" . db_quote($user['User ID']);

    $result = db_query($sql);
}

/**
 * Increments fail count and time after an unsuccesful login attempt
 *
 * Use: $user = increment_fail_count_and_time($user);
 *
 * @param array $user      - row from USERS table to update
 *
 * @return array $user     - updated row from USERS table to update
 */
function increment_fail_count_and_time($user)
{
    $sql = "UPDATE `USERS`
            SET `Fail Time`='" . db_quote(time()) . "',
                   `Fails Count` = `Fails Count` + 1
          WHERE `User ID`=" . db_quote($user['User ID']);

    $result = db_query($sql);

    $updated_user = get_user_by_id(db_quote($user['User ID']));

    return $updated_user;
}

/**
 * Has sufficient time passed since the user was locked?
 *
 * Use: if (is_lock_time_passed($user)) { ... }
 * @param array $user      - row from USERS table to update
 *
 * @return boolean         - has sufficient time passed?
 */
function is_lock_time_passed($user)
{
    $elapsedTime = time() - $user['Fail Time'];
    return $elapsedTime >= ACCOUNT_LOCK_TIME_MINUTES * 60;
}

/**
 * Update user
 *
 * Use: $user = update_user_by_id(123, ['User Name' => 'New name'])
 *
 * @param int user_id      - ID of user to update
 * @param array columns    - array of column names to update
 * @param array changed_by - row from USERS table of user attempting this update
 *                            Defaults to SYSTEM USER if not specified
 *
 * @return array           - updated $user or null if update failed
 */
function update_user_by_id(
    int $user_id,
    array $columns,
    array $changed_by = null
): ?array {
    $user_id = db_quote($user_id);

    // Update User Name if First or Last Name are being updated.
    // Can be removed after User Name is removed from USERS table
    if (isset($columns['First Name']) || isset($columns['Last Name']))
    {
        $user = get_user_by_id($user_id);

        $first_name = isset($columns['First Name'])
            ? trim($columns['First Name'])
            : $user['First Name'];
        $last_name = isset($columns['Last Name'])
            ? trim($columns['Last Name'])
            : $user['Last Name'];

        // Build a user name from first and last names
        $columns['User Name'] = generate_user_name($first_name, $last_name);
    }

    $set_array = [];

    foreach ($columns as $key => $value)
    {
        // clean and quote any cruft from user input
        $value = db_quote(trim($value));
        $key   = db_quote(trim($key));

        if (empty($value))
        {
            array_push($set_array, '`' . $key . '` = NULL');
        }
        else
        {
            array_push($set_array, '`' . $key . "` = '" . $value . "'");
        }
    }

    $set = implode(', ', $set_array);

    if (empty($set))
    {
        return false;
    }

    try
    {
        mysqli_begin_transaction(db_connect());  // TODO: add extra flags for later versions of MySQL

        $sql = "UPDATE `USERS`
                   SET " . $set . "
                 WHERE `User ID` = " . $user_id;

        $result = db_query($sql);

        if ($result)
        {
            $user = get_user_by_id($user_id);

            mysqli_commit(db_connect());
            return $user;
        }
        else
        {
            mysqli_commit(db_connect());
            return null;
        }
    }
    catch (\Throwable $th)
    {
        mysqli_rollback(db_connect());

        throw new \Exception("Unable to update user (User ID: " . $user_id . ", Email: " . (isset($user) ? $user['Email Address'] : 'unknown') . ") because: " . $th->getMessage(), 1);
    }
}
