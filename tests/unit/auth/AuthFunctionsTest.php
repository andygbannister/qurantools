<?php

// $this->markTestIncomplete('This test has not been implemented yet.');

/**
 * Tests for the authorisation functions used in auth.php
 */

require_once 'tests/unit/ReCaptchaHelper.php';
require_once 'auth/auth_functions.php';
require_once 'tests/unit/EmailHelper.php';

use AspectMock\Test as test;

class AuthFunctionsTest extends \Codeception\Test\Unit
{
    use \ReCaptchaHelper;

    public $email;

    // public $user_name = 'Bobbity Bobster';
    public $user_first_name  = 'Bobbity';
    public $user_last_name   = 'Bobster';
    public $password         = 'secret';
    public $institution_name = 'Some Institution';
    public $main_app_url;
    public $entity_id = '1234.shib.org';
    public $entitlement;
    public $config;
    public $user; // row from USERS
    public $set_flash_mock;

    public function _before()
    {
        global $config;

        $_SERVER['REMOTE_ADDR'] = '229.74.0.0';
        $_SESSION               = [];
        session_start();

        $this->email = 'bob' . rand(1, 100000) . '@example.com';

        // get the main_app_url since some tests may use it
        $this->config = \Codeception\Configuration::config();
        $apiSettings  = \Codeception\Configuration::suiteSettings(
            'unit',
            $this->config
        );
        $this->main_app_url     = $apiSettings['modules']['config']['main_app_url'];
        $config['main_app_url'] = $this->main_app_url;

        // this needs to be done in the _before otherwise it doesn't seem to work
        // for multiple tests later on. This command would work for a single test,
        // but not when running several in a row. It could be a bug in AspectMock.
        $this->set_flash_mock = test::func('QT\Flash', 'set_flash', null);
    }

    public function _after()
    {
        // remove login artifacts
        // TODO: use the clean_logs helper.
        isset($this->user)
            ? db_query(
                "DELETE FROM `LOGIN-LOGS` WHERE `User ID`='" .
                    $this->user['User ID'] .
                    "'"
            )
            : null;

        session_destroy();

        test::clean(); // remove all registered test doubles
    }

    // set_redirect_link
    public function testSet_redirect_linkForHTTPS(): void
    {
        $_SERVER['HTTPS']       = true;
        $_SERVER['HTTP_HOST']   = 'qurantools.org';
        $_SERVER['REQUEST_URI'] = '/some_page.php';

        set_redirect_link();
        $this->assertEquals(
            $_SESSION['auth_redirect_link'],
            'https://qurantools.org/some_page.php'
        );
    }

    public function testSet_redirect_linkForHTTP(): void
    {
        $_SERVER['HTTP']        = true;
        $_SERVER['HTTP_HOST']   = 'qurantools.org';
        $_SERVER['REQUEST_URI'] = '/some_page.php';

        set_redirect_link();
        $this->assertEquals(
            $_SESSION['auth_redirect_link'],
            'http://qurantools.org/some_page.php'
        );
    }

    public function testGet_attempted_login_typeReturnsConsumerWhenConsumer(): void
    {
        $_POST['EMAIL_ADDRESS'] = $this->email;
        $_POST['PASSWORD']      = $this->password;

        $this->assertEquals(
            ATTEMPTED_LOGIN_TYPE_USER_PASSWORD,
            get_attempted_login_type()
        );
    }

    public function testGet_attempted_login_typeDoesNotReturnConsumerWhenNotConsumer(): void
    {
        $_POST['EMAIL_ADDRESS'] = null;
        $_POST['PASSWORD']      = null;
        $this->assertNotEquals(
            ATTEMPTED_LOGIN_TYPE_USER_PASSWORD,
            get_attempted_login_type()
        );
    }

    public function testGet_attempted_login_typeReturnsFreshFaceWhenUnknown(): void
    {
        $this->assertEquals(
            ATTEMPTED_LOGIN_TYPE_FRESH_FACE,
            get_attempted_login_type()
        );
    }

    // set_auth_flag
    // TODO: get rid of this test when we get rid of the now superfluous set_auth_flag function

    public function testSet_auth_flagSetsFlag(): void
    {
        $value = 'bad things happened at sea';
        $flag  = 'consumer';

        set_auth_flag($flag, $value);

        $this->assertTrue(isset($_SESSION[$flag]));
        $this->assertEquals($value, $_SESSION[$flag]);
    }

    // redirect_to_login_page
    public function testRedirect_to_login_pageSetsStopProcessingFlag(): void
    {
        redirect_to_login_page();

        $this->assertTrue($_SESSION[AUTH_STOP_PROCESSING]);
    }

    /******************************************************************
     *                  attempt_consumer_login
     *
     * The following tests somewhat duplicate the tests in
     * validate_email_password() but are a little broader.
     ******************************************************************/
    public function testAttempt_consumer_loginLogsConsumerInWithRightCredentials(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Password'  => $this->password,
            'User Type' => USER_TYPE_CONSUMER
        ]);
        $_POST['EMAIL_ADDRESS'] = $this->user['Email Address'];
        $_POST['PASSWORD']      = $this->password;

        attempt_consumer_login();

        $this->assertTrue(is_logged_in_user());
        $this->assertFalse(isset($_SESSION['auth_stop_processing']));
    }

    public function testAttempt_consumer_loginDoesNotLogUserInWithMissingEmail(): void
    {
        $_POST['EMAIL_ADDRESS'] = '';
        $_POST['PASSWORD']      = $this->password;

        attempt_consumer_login();

        $this->assertFalse(is_logged_in_user());
        $this->assertTrue(isset($_SESSION['auth_stop_processing']));

        $session_key = AUTH_CONSUMER_ERROR;
        $this->assertTrue(isset($_SESSION[$session_key]));
        $this->assertStringContainsString(
            'Internal Error',
            $_SESSION[$session_key]
        );
    }

    public function testAttempt_consumer_loginDoesNotLogUserInWithMissingPassword(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Password'  => $this->password,
            'User Type' => USER_TYPE_CONSUMER
        ]);

        $_POST['EMAIL_ADDRESS'] = $this->user['Email Address'];
        $_POST['PASSWORD']      = null;

        attempt_consumer_login();

        $this->assertFalse(is_logged_in_user());
        $this->assertTrue(isset($_SESSION['auth_stop_processing']));

        $session_key = AUTH_CONSUMER_ERROR;
        $this->assertTrue(isset($_SESSION[$session_key]));
        $this->assertStringContainsString(
            'Internal Error',
            $_SESSION[$session_key]
        );
    }

    public function testAttempt_consumer_loginDoesNotLogUserInWithUnknownEmail(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Password' => $this->password,
        ]);
        $_POST['EMAIL_ADDRESS'] = 'some_unknown_email_address';
        $_POST['PASSWORD']      = $this->password;

        attempt_consumer_login();

        $this->assertFalse(is_logged_in_user());
        $this->assertTrue(isset($_SESSION['auth_stop_processing']));

        $session_key = AUTH_CONSUMER_ERROR;
        $this->assertTrue(isset($_SESSION[$session_key]));
        $this->assertEquals(
            'Sorry, your email address and/or password was not recognised.',
            $_SESSION[$session_key]
        );
    }

    public function testAttempt_consumer_loginDoesNotLogUserInWithBadCredentials(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Password' => $this->password,
        ]);
        $_POST['EMAIL_ADDRESS'] = $this->user['Email Address'];
        $_POST['PASSWORD']      = 'wrong password';

        attempt_consumer_login();

        $this->assertFalse(is_logged_in_user());
        $this->assertTrue(isset($_SESSION['auth_stop_processing']));

        $session_key = AUTH_CONSUMER_ERROR;
        $this->assertTrue(isset($_SESSION[$session_key]));
        $this->assertEquals(
            'Sorry, your email address and/or password was not recognised.',
            $_SESSION[$session_key]
        );
    }

    // is_valid_ip_range

    public function testIs_valid_ip_rangeThrowsWhenMissingIpRange(): void
    {
        $this->expectExceptionMessage(
            'Argument 1 passed to is_valid_ip_range() must be of the type string, null given'
        );

        $result = is_valid_ip_range(null);
    }

    public function testIs_valid_ip_rangeTrueWhenValid(): void
    {
        $valid_ip_ranges = ['1.1.1.1-1.1.1.1', '1.1.1.1-1.1.1.2'];
        foreach ($valid_ip_ranges as $ip_range)
        {
            $this->assertTrue(
                is_valid_ip_range($ip_range),
                "$ip_range was not validated, when in fact it is valid"
            );
        }
    }

    public function testIs_valid_ip_rangeFalseWhenInvalid(): void
    {
        $invalid_ip_ranges = [
            '1.1.1-1.1.1',
            ' 1.1.1.1-1.1.1.2',
            'scooby_snacks',
            '1.1.1.1-2.2.2.2-3.3.3.3'
        ];
        foreach ($invalid_ip_ranges as $ip_range)
        {
            $this->assertFalse(
                is_valid_ip_range($ip_range),
                "$ip_range was validated, when in fact it is invalid"
            );
        }
    }

    // is_logged_in_user
    public function testIs_logged_in_userReturnsTrueForLoggedInUser(): void
    {
        // here we are mocking the $_SESSION variable since the CLI doesn't
        // set it when running unit tests
        $_SESSION['UID'] = '1234';

        $this->assertTrue(is_logged_in_user());
    }

    public function testIs_logged_in_userReturnsFalseWhenNoLoggedInUser(): void
    {
        $_SESSION = [];

        $this->assertFalse(is_logged_in_user());
    }

    // validate_email_password
    public function testValidate_email_passwordHandlesNullUser(): void
    {
        $session_key = AUTH_CONSUMER_ERROR;

        foreach (['', null] as $user)
        {
            $this->assertFalse(
                validate_email_password($user, $this->email, $this->password)
            );
            $this->assertEquals(
                'Sorry, your email address and/or password was not recognised.',
                $_SESSION[$session_key]
            );
        }
    }

    public function testValidate_email_passwordReturnsFalseForPasswordReset(): void
    {
        $this->user = $this->tester->createUser($this->tester);
        $result     = reset_password($this->user); // set the password hash to PASSWORD_RESET_TEXT

        $this->user = get_user_by_id($this->user['User ID']);

        $result = validate_email_password(
            $this->user,
            $this->email,
            $this->password
        );

        $this->assertEquals($result, false);
        $this->assertEquals(
            'Your password has been reset by an administrator and you must choose a new one.',
            $_SESSION[AUTH_CONSUMER_ERROR]
        );
        $this->assertEquals(true, $_SESSION[AUTH_PASSWORD_RESET]);
    }

    public function testValidate_email_passwordResetsFailCountAfterElapsedTime(): void
    {
        $last_fail_timestamp = 10000; // sometime in the 1970s I suspect...
        $this->user          = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password,
            'Fails Count'   => MAXIMUM_PASSWORD_ATTEMPTS,
            'Fail Time'     => $last_fail_timestamp
        ]);

        $result = validate_email_password(
            $this->user,
            $this->email,
            $this->password
        );

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals(0, $updated_user['Fails Count']);
        $this->assertEquals(0, $updated_user['Fail Time']);
    }

    public function testValidate_email_passwordWarnsLockedLockedUser(): void
    {
        $session_flag = AUTH_CONSUMER_ERROR;

        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password,
            'Fails Count'   => MAXIMUM_PASSWORD_ATTEMPTS + 1,
            'Fail Time'     => time() - 10 // 10 seconds ago
        ]);
        $result = validate_email_password(
            $this->user,
            $this->email,
            'duff password'
        );

        $updated_user = get_user_by_id($this->user['User ID']);
        $this->assertEquals(false, $result);
        $this->assertEquals(
            $updated_user['Fails Count'],
            MAXIMUM_PASSWORD_ATTEMPTS + 2
        );
        $this->assertStringContainsString(
            'Due to multiple incorrect password attempts, your account is currently locked for ' .
                ACCOUNT_LOCK_TIME_MINUTES .
                ' minutes.',
            $_SESSION[$session_flag]
        );
    }

    public function testValidate_email_passwordReturnsFalseForBadEmailPasswordCombo(): void
    {
        $session_key = AUTH_CONSUMER_ERROR;

        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);
        $result = validate_email_password(
            $this->user,
            $this->email,
            'duff password'
        );

        $updated_user = get_user_by_id($this->user['User ID']);
        $this->assertEquals(false, $result);
        $this->assertEquals($updated_user['Fails Count'], 1);
        $this->assertEquals(
            'Sorry, your email address and/or password was not recognised.',
            $_SESSION[$session_key]
        );
    }

    public function testValidate_email_passwordLocksUserAfterTooManyLoginAttempts(): void
    {
        $session_flag = AUTH_CONSUMER_ERROR;

        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password,
            'Fails Count'   => MAXIMUM_PASSWORD_ATTEMPTS - 1,
            'Fail Time'     => time() - 10 // 10 seconds ago
        ]);

        $result = validate_email_password(
            $this->user,
            $this->email,
            'wrong password'
        );

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals(
            $updated_user['Fails Count'],
            MAXIMUM_PASSWORD_ATTEMPTS
        );
        $this->assertStringContainsString(
            'Due to multiple incorrect password attempts, your account has been locked for the next ' .
                ACCOUNT_LOCK_TIME_MINUTES .
                ' minutes.',
            $_SESSION[$session_flag]
        );
    }

    public function testValidate_email_passwordReturnsFalseForBlockedUser(): void
    {
        $session_flag = AUTH_CONSUMER_ERROR;

        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password,
            'Is Blocked'    => true
        ]);

        $result = validate_email_password(
            $this->user,
            $this->email,
            $this->password
        );

        $this->assertFalse($result, 'Blocked user should not have validated their email/password combo.');

        $this->assertStringContainsString(
            'You have been blocked from using Qur`an Tools.',
            $_SESSION[$session_flag]
        );
    }

    public function testValidate_email_passwordReturnsTrueForGoodEmailPasswordCombo(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);

        $this->assertTrue(
            validate_email_password($this->user, $this->email, $this->password)
        );
    }

    // log_user_in
    public function testLog_user_inSetsSessionVariablesForConsumerUsers(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);
        $_POST['PASSWORD'] = $this->password;

        log_user_in($this->user);

        $hashed_password = crypt($this->password, $this->user['Password Hash']);

        $this->assertEquals($_SESSION['UID'], $this->user['User ID']);
        $this->assertEquals(
            $_SESSION['Email Address'],
            $this->user['Email Address']
        );
        $this->assertEquals($_SESSION['User Name'], $this->user['User Name']);
        $this->assertEquals(
            $_SESSION['administrator'],
            $this->user['Administrator']
        );
        $this->assertFalse(isset($_SESSION['password_hash']));
        $this->assertFalse(isset($_SESSION['institution_name']));
    }

    public function testLog_user_inUnsetsAuthSessionVariables(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);
        $_POST['PASSWORD'] = $this->password;

        log_user_in($this->user);

        $this->assertFalse(isset($_SESSION['auth_redirect_link']));
        $this->assertFalse(isset($_SESSION['auth_password_reset']));
        $this->assertFalse(isset($_SESSION['auth_account_locked']));
        $this->assertFalse(isset($_SESSION[AUTH_CONSUMER_ERROR]));
    }

    // log_user_out
    public function testLog_user_outHandlesConsumerUser(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);
        $_POST['PASSWORD'] = $this->password;
        log_user_in($this->user);

        log_user_out();

        $this->assertEquals($_SESSION, []);
    }

    // is_auth_stop_processing
    public function testIs_auth_stop_processingReturnsTrueWhenSessionVariableSet(): void
    {
        $_SESSION['auth_stop_processing'] = true;
        $this->assertTrue(is_auth_stop_processing());
    }

    public function testIs_auth_stop_processingReturnsFalseWhenSessionVariableNotSet(): void
    {
        $_SESSION['auth_stop_processing'] = null;
        $this->assertFalse(is_auth_stop_processing());
    }

    // handle_email_password_hash_mismatch
    public function testHandle_email_password_hash_mismatchLogsUserOutAndRedirectsToLogin(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);
        $_POST['PASSWORD'] = $this->password;
        log_user_in($this->user);

        handle_email_password_hash_mismatch($this->user);

        $this->assertFalse(
            is_logged_in_user(),
            'There should not be a logged in user, but there is.'
        );
        $this->assertTrue(isset($_SESSION['auth_stop_processing']));
    }

    // get_logged_in_user
    public function testGet_logged_in_userIfCurrentUser(): void
    {
        $_POST['PASSWORD'] = $this->password;

        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email,
            'Password'      => $this->password
        ]);

        log_user_in($this->user);

        $this->assertEquals(
            $this->user['User ID'],
            get_logged_in_user()['User ID']
        );
    }

    public function testGet_logged_in_userIfNoCurrentUser(): void
    {
        $this->assertEquals(null, get_logged_in_user());
    }
}
