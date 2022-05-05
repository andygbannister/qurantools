<?php

namespace Codeception\Module;

require_once 'app/library/functions.php';
require_once 'app/library/database/user_functions.php';

define('ACCESS_LEVEL_OPEN', 'OPEN');
define('ACCESS_LEVEL_NORMAL', 'NORMAL');
define('ACCESS_LEVEL_ADMIN', 'ADMIN');
define('ACCESS_LEVEL_SUPERUSER', 'SUPERUSER');
define('ACCESS_LEVEL_WORD_FIXER', 'WORD_FIXER');

// ini_set('memory_limit', '100M'); // or you could use 1G

class QTTestHelper extends \Codeception\Module
{
    /**
     * The names of the pages in the application that can be used
     * in tests to prevent repeating the name and paths of PHP files
     */
    public $getApplicationPages = [
        '404'                        => '/404.php',
        'maintenance'                => '/maintenance.php',
        'database_error'             => '/database_error.php',
        'duff'                       => '/gloobyWooobyDuffPage.php',
        'login'                      => '/auth/login.php',
        'register'                   => '/auth/register.php',
        'request_password_reset'     => '/auth/request_password_reset.php',
        'login_logs'                 => '/admin/login_logs.php',
        'customer_statistics'        => '/admin/customer_statistics.php',
        'failed_searches'            => '/admin/failed_searches.php',
        'page_usage_statistics'      => '/admin/page_usage_statistics.php',
        'translation_word_tag_stats' => '/admin/translation_word_tag_stats.php',
        'user_management'            => '/admin/user_management.php',
        'user_detail'                => '/admin/user_detail.php',
        'word_correction_logs'       => '/admin/word_correction_logs.php',
        'verse_search_logs'          => '/admin/verse_search_logs.php',
        'cookie_policy'              => '/docs/cookie_policy.php',
        'about'                      => '/about.php',
        'home'                       => '/home.php',
        'preferences'                => '/preferences.php',
        'bookmark_manager'           => '/bookmark_manager.php',
        'browse_root_usage'          => '/browse_root_usage.php',
        'browse_sura'                => '/browse_sura.php',
        'easy_search'                => '/easy_search.php',
        'chart_average_word_length'  => '/charts/chart_average_word_length.php',
        'chart_grammatical'          => '/charts/chart_grammatical.php',
        'terms'                      => '/licenses/terms.php',
    ];

    public function getApplicationPage($page = '')
    {
        return $this->getApplicationPages[$page];
    }

    /**
     * Create a user in Qur'an Tools
     * Use: $I->createUser($this->tester,
     *                    ['Fails Count' => $num_fails, 'Fail Time' => time()];
     *                    ['intro_watched' => true]);
     *
     * @param object  $I            - calling context
     * @param array   $extra_values - column names (cased correctly) and values:
     * @param array   $options      -
     *        boolean intro_watched - Has the new user watched the intro video (the default)
     * @return array  New user on success.
     */
    public function createUser(
        $I,
        $extra_values = [],
        $options = ['intro_watched' => true]
    ) {
        $default_email = 'test' . rand(1, 100000) . '@example.com';

        $updateArray = [
            'Email Address'        => $default_email,
            'First Name'           => 'Some',
            'Last Name'            => 'User',
            'User Type'            => USER_TYPE_CONSUMER,
            'Password Hash'        => hash_password('12345678'),
            'Login Count'          => 0,
            'Fails Count'          => 0,
            'AJAX Token'           => null,
            'Last Login Timestamp' => null
        ];

        // add extra column values to be inserted
        foreach ($extra_values as $key => $value)
        {
            if ($key == 'Password')
            {
                $updateArray['Password Hash'] = hash_password($value);
            }
            else
            {
                $updateArray[$key] = $value;
            }
        }

        // Can be removed when User Name is removed from the USERS table
        $updateArray['User Name'] = \generate_user_name(
            $updateArray['First Name'],
            $updateArray['Last Name']
        );

        $user_id = $I->haveInDatabase('USERS', $updateArray);

        $user = get_user_by_id($user_id);

        return $user;
    }

    /**
     * Create an admin user in Qur'an Tools
     * @see createUser
     * Uses createUser to create an admin user
     */
    public function createAdminUser(
        $I,
        $email = 'admin@example.com',
        $password = '12345678',
        $extra_values = ['Administrator' => 'ADMIN'],
        $options = []
    ) {
        if (!array_key_exists('Administrator', $extra_values))
        {
            $extra_values['Administrator'] = 'ADMIN';
        }
        !array_key_exists('Email Address', $extra_values)
            ? ($extra_values['Email Address'] = $email)
            : null;
        !array_key_exists('Password', $extra_values)
            ? ($extra_values['Password'] = $password)
            : null;

        return $this->createUser($I, $extra_values, $options);
    }

    /**
     * Create a superuser in Qur'an Tools
     * @see createUser
     * Uses createUser to create a super user
     */
    public function createSuperUser(
        $I,
        $extra_values = ['Administrator' => 'SUPERUSER'],
        $options = []
    ) {
        if (!array_key_exists('Administrator', $extra_values))
        {
            $extra_values['Administrator'] = 'SUPERUSER';
        }

        !array_key_exists('Email Address', $extra_values)
            ? ($extra_values['Email Address'] = 'admin@example.com')
            : null;
        !array_key_exists('Password', $extra_values)
            ? ($extra_values['Password'] = '12345678')
            : null;

        return $this->createUser($I, $extra_values, $options);
    }

    /**
     * Create a customer user in Qur'an Tools
     *
     * @see createUser
     *
     * Uses createUser to create a customer user
     */
    public function createCustomerUser($I, $extra_values = [], $options = [])
    {
        $extra_values['Email Address'] = $extra_values['Email Address'] ??
            'customer' . rand(1, 10000) . '@example.com';

        return $this->createUser($I, $extra_values, $options);
    }

    /**
     * Log in as a Qur'an Tools user
     *
     * Use: $I->loginToQT($I, ['Administrator' => 'ADMIN'],['use_snapshot' => true]);
     *
     * @param object  $I            - calling context
     * @param array   $extra_values - that may be used for logging in or creating a new user
     * @param array   $options      - extra settings
     *        string  snapshot      - Name of snapshot to use for fast (recycled) logins (not working)
     */
    public function loginToQT($I, $extra_values = [], $options = [])
    {
        // // Snapshots don't seem to work - since I think they only work with
        // // browser-side and not server side cookies.
        // $snapshot = array_key_exists('snapshot', $options)
        //     ? $options['snapshot']
        //     : null;

        // // Use a snapshot if we want to and one exists - and skip a slow login
        // if (
        //     $snapshot &&
        //     $I->loadSessionSnapshot($snapshot)
        // ) {
        //     session_start();
        //     return;
        // }

        if (!array_key_exists('intro_watched', $options))
        {
            $options['intro_watched'] = true;
        }

        if (!array_key_exists('Email Address', $extra_values))
        {
            $extra_values['Email Address'] = 'user@example.com';
        }

        if (!array_key_exists('Password', $extra_values))
        {
            $extra_values['Password'] = '12345678';
        }

        // create the user if needs be
        if (
            $I->grabNumRecords('USERS', [
                'email address' => $extra_values['Email Address']
            ]) == 0
        ) {
            $user = $this->createUser($I, $extra_values, $options);
        }

        $I->amOnPage($I->getApplicationPage('home'));
        $I->seeElement('#login-button');
        $I->fillField('EMAIL_ADDRESS', $extra_values['Email Address']);
        $I->fillField('PASSWORD', $extra_values['Password']);
        $I->click('#login-button');
        $I->seeElement('#browse-menu'); // only logged on users can see this menu item

        // // save snapshot for fast future login
        // if ($snapshot)
        // {
        //     $I->saveSessionSnapshot($snapshot);
        // }
    }

    public function loginToQTAsAdmin(
        $I,
        $extra_values = ['Administrator' => 'ADMIN'],
        $options = []
    ) {
        if (!array_key_exists('Administrator', $extra_values))
        {
            $extra_values['Administrator'] = 'ADMIN';
        }
        if (!array_key_exists('Email Address', $extra_values))
        {
            $extra_values['Email Address'] = 'admin@example.com';
        }
        $this->loginToQT($I, $extra_values, $options);
    }

    public function loginToQTAsSuperUser($I, $extra_values = [], $options = [])
    {
        if (!array_key_exists('Administrator', $extra_values))
        {
            $extra_values['Administrator'] = 'SUPERUSER';
        }

        if (!array_key_exists('Email Address', $extra_values))
        {
            $extra_values['Email Address'] = 'superuser@example.com';
        }

        $this->loginToQT($I, $extra_values, $options);
    }

    /**
     * Log out
     *
     * Use: $I->logoutFromQT($I);
     *
     * @param object  $I            - calling context
     */
    public function logoutFromQT($I)
    {
        $I->click('#logout');
    }

    public function createResetPasswordCode($I, $email): string
    {
        $reset_code = \generate_reset_password_code();

        $user = \get_user_by_email($email);

        // save the new password
        $user = update_user_by_id($user['User ID'], [
            'Reset Code'     => $reset_code,
            'Reset Timecode' => time()
        ]);

        return $reset_code;
    }

    /**
     * Used for testing whether a page is accessible by a non-logged in user.
     * Use:  $I->redirectToLoginFor($I, '/home.php');
     *
     * @param object $I          context of the test
     * @param string $page_path  relative path of the page under test
     *
     * Function doesn't return anything, but will pass a codeception test
     * if the given page redirects a non-logged in user to the login page
     */
    public function redirectToLoginFor($I, $page_path): void
    {
        $I->amOnPage($page_path);
        $I->seeInCurrentUrl($I->getApplicationPage('login'));
    }

    /**
     * Used for ensuring there is a system user in the database before running
     * a test.
     * Use:  $I->ensureSystemUser($I);
     *
     * @param object $I          context of the test
     * @return array           - system user (from USERS table)
     *
     * Function doesn't return anything, but will pass a codeception test
     * if the given page redirects a non-logged in user to the login page
     */
    public function getSystemUser($I): array
    {
        $system_user = $I->selectFromDatabase(
            'SELECT * 
               FROM `USERS` 
              WHERE `User Type` = "' . USER_TYPE_SYSTEM .
                '" AND `User Name` = "System User"'
        );

        if (empty($system_user))
        {
            $I->fail(
                'No system user in the database. Run \'2019-10-30 Create SYSTEM user.sql\' and try test again'
            );
        }
        else
        {
            return $system_user[0];
        }
    }

    /**
     * Helper for Webdriver tests that need to reset sessions/cookies/local storage etc
     */
    public function restartBrowser($I)
    {
        $this->getModule('WebDriver')->_restart();
    }

    /**
     * Clears out the LOGIN-LOGS table for the given emails and user IDs
     * @param object $I
     * @param array  $to_delete Array of things to delete
     *        array  emails     Array of email addresses to delete
     *        array  user_ids   Array of user IDs to delete
     */
    public function clearLoginLogs(
        $I,
        $to_delete = ['emails' => [], 'user_ids' => []]
    ) {
        // add default testing emails to delete from logs
        $to_delete['emails'][] = 'superuser@example.com';
        $to_delete['emails'][] = 'user@example.com';
        $to_delete['emails'][] = 'admin@example.com';
        $to_delete['emails'][] = 'ip_range@example.com';

        $sql = "DELETE FROM `LOGIN-LOGS` 
              WHERE `Email Address` IN ('" . implode("', '", $to_delete['emails']) . "')";

        db_query($sql);

        if (!empty($to_delete['user_ids']))
        {
            $sql = "DELETE FROM `LOGIN-LOGS` 
                  WHERE `User ID` IN (" . implode(', ', array_map('intval', $to_delete['user_ids'])) . ')';
            db_query($sql);
        }
    }

    /**
     * Clears out the USAGE-VERSES-SEARCHES table for the given user IDs
     * @param object $I
     * @param array  $to_delete Array of things to delete
     *        array  user_ids   Array of user IDs to delete
     */
    public function clearSearchLogs(
        $I,
        $to_delete = ['emails' => [], 'user_ids' => []]
    ) {
        if (!empty($to_delete['user_ids']))
        {
            $sql = "DELETE FROM `USAGE-VERSES-SEARCHES` 
                  WHERE `USER ID` IN (" . implode(', ', array_map('intval', $to_delete['user_ids'])) . ')';
            db_query($sql);
        }
    }

    /**
     * Helper that logs a user on and visits the given page of interest
     * @param string $page_of_interest Page to visit after logon
     * @param string $access_level     One of a some access level constants
     * @param array  $extra_values     Extra values (eg email, password) that
     *                                 override the defaults to use for the logon
     * @param array  $options          Extra options for the logon. See LoginToQT
     *                                 for details
     */
    public function loginAndVisitPageOfInterest(
        $I,
        $scenario = null,
        string $page_of_interest = null,
        string $access_level = ACCESS_LEVEL_NORMAL,
        array $extra_values = [],
        array $options = []
    ) {
        switch ($access_level)
        {
            case ACCESS_LEVEL_NORMAL:
                $I->loginToQT($I, $extra_values, $options);

                break;

            case ACCESS_LEVEL_ADMIN:
                $I->loginToQTAsAdmin($I, $extra_values, $options);

                break;
            case ACCESS_LEVEL_SUPERUSER:
                $I->loginToQTAsSuperUser($I, $extra_values, $options);

                break;

            default:
                // code...
                break;
        }
        // Watch out - this may visit the page you are currently on again.
        $I->amOnPage($page_of_interest);
    }

    /**
     * Helper that performs a search from the home page
     *
     * @param string $term Search term
     *
     * The default search is for sura 2
     */
    public function doSearch(
        \AcceptancePhpbrowserTester $I,
        $scenario = null,
        string $term = 'test search'
    ) {
        if (!$I->seeInCurrentUrl($I->getApplicationPage('home')))
        {
            $I->amOnPage($I->getApplicationPage('home'));
        }

        $I->submitForm('#pick-verse', ['SEEK' => $term]);
    }

    /**
     * Nasty helper that deletes a user by email address or UserID
     *
     * @param array $args Array with keys of either 'Email Address' or "User ID'
     *
     */
    public function deleteUser(
        \AcceptancePhpbrowserTester $I,
        array $args = null
    ): void {
        $where = null;

        if (\array_key_exists('Email Address', $args))
        {
            $where = " WHERE `Email Address` = '" . $args['Email Address'] . "'";
        }
        elseif (\array_key_exists('User ID', $args) && is_int($args['User ID']))
        {
            $where = " WHERE `User ID` = " . $args['User ID'];
        }

        if (empty($where))
        {
            return;
        }

        $sql = "DELETE FROM `USERS`" . $where;

        codecept_debug($sql);

        db_query($sql);
    }
}
