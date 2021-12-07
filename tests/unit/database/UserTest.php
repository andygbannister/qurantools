<?php

/**
 * Tests for the database calls related to the USERS table
 */

namespace QT;

include_once 'library/database/user_functions.php';
include_once 'library/hash.php';
require_once 'tests/unit/EmailHelper.php';

use AspectMock\Test as test;

// $this->markTestIncomplete('This test has not been implemented yet.');

class UserTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    public $email;
    public $password = '12345678';
    public $first_name;
    public $last_name = 'Brown';
    public $user;
    public $user_1;
    public $user_2;
    public $free_days = 14;

    // /** @specify */
    // protected $user;

    public function _before()
    {
        global $config;

        $random_number    = rand(1, 100000);
        $this->first_name = "Bob-$random_number";
        $this->email      = "bob-$random_number@example.com";
    }

    public function _after()
    {
        //TODO: I don't think these log tidy ups aren't working and are leaving heaps of crud in the DB

        db_query(
            "DELETE FROM `LOGIN-LOGS` WHERE `Email Address`='" .
                $this->email .
                "'"
        );

        if (isset($this->user))
        {
            db_query(
                'DELETE FROM `LOGIN-LOGS` WHERE `User ID`=' .
                    $this->user['User ID']
            );
        }

        $user_ids = [];
        if (isset($this->user))
        {
            \array_push($user_ids, $this->user['User ID']);
        }
        if (isset($this->user_1))
        {
            \array_push($user_ids, $this->user_1['User ID']);
        }
        if (isset($this->user_2))
        {
            \array_push($user_ids, $this->user_2['User ID']);
        }

        db_query(
            "DELETE FROM `USERS` WHERE `Email Address`='" . $this->email . "'"
        );

        test::clean(); // remove all registered test doubles
    }

    public function testGet_logged_in_userReturnsUserWhenThereIsOne()
    {
        $this->user = $this->tester->createUser($this->tester);

        $_SESSION['UID'] = $this->user['User ID'];

        $retrieved_user = get_logged_in_user();
        $this->assertNotEmpty($retrieved_user);
        $this->assertEquals($this->user['User ID'], $retrieved_user['User ID']);
    }

    public function testGet_logged_in_userReturnsNullWhenThereIsNoMatch()
    {
        // we have no users with negative IDs,so this is bound to fail
        $_SESSION['UID'] = -100;

        $retrieved_user = get_logged_in_user();

        $this->assertEmpty($retrieved_user);
    }

    // get_user_by_email_password_hash
    public function testGet_user_by_email_password_hashReturnsUserIfExists(): void
    {
        $hash_password = hash_password($this->password);

        $user_id = $this->tester->haveInDatabase('USERS', [
            'Email Address' => $this->email,
            'Password Hash' => $hash_password
        ]);

        $retrieved_user = get_user_by_email_password_hash(
            $this->email,
            $hash_password
        );
        $this->assertNotEmpty($retrieved_user);
    }

    public function testGet_user_by_email_password_hashReturnsNullIfUserDoesNotExist(): void
    {
        $retrieved_user = get_user_by_email_password_hash(
            'a duff email',
            'a duff password hash'
        );
        $this->assertEmpty($retrieved_user);
    }

    // get_user_by_email

    public function testGet_user_by_emailThrowsIfNull(): void
    {
        $email = null;
        $this->expectExceptionMessage('Missing $email for get_user_by_email()');

        get_user_by_email($email);
    }

    public function testGet_user_by_emailReturnsUserIfExists(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        $retrieved_user = get_user_by_email($this->user['Email Address']);
        $this->assertNotEmpty($retrieved_user);
    }

    public function testGet_user_by_emailReturnsNullIfUserDoesNotExist(): void
    {
        $retrieved_user = get_user_by_email($this->email);
        $this->assertEmpty($retrieved_user);
    }

    // get_user_by_user_name

    public function testGet_user_by_user_nameThrowsIfNull(): void
    {
        $this->expectExceptionMessage(
            'Missing $user_name for get_user_by_user_name()'
        );

        get_user_by_user_name();
    }

    // get_system_user

    public function testGet_system_userThrowsWhenMultipleSystemUsers(): void
    {
        $second_system_user = $this->tester->haveInDatabase('USERS', [
            'User Type'     => USER_TYPE_SYSTEM,
            'User Name'     => 'System User',
            'Administrator' => 'SUPERUSER'
        ]);
        $this->expectExceptionMessage('Multiple system users in the database');

        $result = get_system_user();
    }

    public function testGet_system_userWorks(): void
    {
        $result = get_system_user();

        $this->assertEquals($result['User Type'], USER_TYPE_SYSTEM);
    }

    // update_user_after_login

    public function testUpdate_user_after_loginThrowsWithoutUser()
    {
        $timestamp = date_timestamp_get(date_create());
        $this->expectExceptionMessage(
            'Argument 1 passed to update_user_after_login() must be of the type array, null given'
        );

        update_user_after_login(null, $timestamp);
    }

    public function testUpdate_user_after_loginThrowsWithoutTimestamp()
    {
        $this->user = $this->tester->createUser($this->tester);
        $this->expectExceptionMessage(
            'Missing $timestamp for update_user_after_login()'
        );

        update_user_after_login($this->user, null);
    }

    public function testUpdate_user_after_loginWorks()
    {
        $this->user = $this->tester->createUser($this->tester);
        $timestamp  = date('Y-m-d H:i:s');

        $result = update_user_after_login($this->user, $timestamp);

        $retrieved_user = get_user_by_id($this->user['User ID']);
        $this->assertEquals($retrieved_user['Login Count'], 1);
        $this->assertEquals(
            $retrieved_user['Last Login Timestamp'],
            $timestamp
        );
    }

    // update_user_by_id

    public function testUpdate_user_by_idThrowsIfCalledWithoutId(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'User Name' => 'Name 1'
        ]);

        $columns = ['User Name' => 'Name 2'];

        $this->expectExceptionMessage(
            'Argument 1 passed to update_user_by_id() must be of the type int'
        );

        $this->user = update_user_by_id(null, $columns);
    }

    public function testUpdate_user_by_idThrowsIfCalledWithoutColumns(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'User Name' => 'Name 1'
        ]);

        $columns = null;

        $this->expectExceptionMessage(
            'Argument 2 passed to update_user_by_id() must be of the type array'
        );

        $this->user = update_user_by_id($this->user['User ID'], $columns);
    }

    public function testUpdate_user_by_idNullIfCalledWithInvalidValues(): void
    {
        $db_connection = db_connect();

        $mysql_version = explode('.', mysqli_get_server_info($db_connection));

        if ($mysql_version[0] >= '5' && $mysql_version[1] >= '7')
        {
            $this->user = $this->tester->createUser($this->tester, [
                'User Name' => 'Name 1'
            ]);

            $this->assertNull(
                update_user_by_id(
                    $this->user['User ID'],
                    [
                        'User Type' => null
                    ]
                ),
                'The USERS table accepted a null value in the `User Type` column.'
            );
        }
        else
        {
            // Since MySQL 5.5 accepts null values in columns that are labelled as NOT NULL this test is of little value for that database version
            $this->markTestIncomplete(
                'MySQL version doesn\'t support this test'
            );
        }
    }

    public function testUpdate_user_by_idUpdatesUserNameIfFirstAndLastNameChanged(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'First Name' => 'First Name 1',
            'Last Name'  => 'Last Name 1'
        ]);

        $this->assertNotNull(
            update_user_by_id($this->user['User ID'], [
                'First Name' => 'First Name 2',
                'Last Name'  => 'Last Name 2'
            ])
        );

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals($updated_user['First Name'], 'First Name 2');
        $this->assertEquals($updated_user['Last Name'], 'Last Name 2');
        $this->assertEquals(
            $updated_user['User Name'],
            'First Name 2 Last Name 2'
        );
    }

    public function testUpdate_user_by_idUpdatesUserNameIfFirstOrLastNameChanged(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'First Name' => 'First Name 1',
            'Last Name'  => 'Last Name 1'
        ]);

        $this->assertNotNull(
            update_user_by_id($this->user['User ID'], [
                'First Name' => 'First Name 2'
            ])
        );

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals($updated_user['First Name'], 'First Name 2');
        $this->assertEquals($updated_user['Last Name'], 'Last Name 1');
        $this->assertEquals(
            $updated_user['User Name'],
            'First Name 2 Last Name 1'
        );
    }

    // insert_login_log
    public function testInsert_login_logForConsumer()
    {
        $this->user  = $this->tester->createUser($this->tester);
        $user_id     = $this->user['User ID'];
        $login_count = $this->tester->grabNumRecords('LOGIN-LOGS', [
            'User ID' => $user_id
        ]);
        $test_ip                = '229.74.0.0';
        $_SERVER['REMOTE_ADDR'] = $test_ip;

        $result = insert_login_log($this->user);

        $this->assertEquals(
            $login_count + 1,
            $this->tester->grabNumRecords('LOGIN-LOGS', ['User ID' => $user_id])
        );
        $new_log = $this->tester->selectFromDatabase(
            "SELECT * FROM `LOGIN-LOGS` WHERE `User ID` =$user_id AND `Login Date` = '" .
                date('Y-m-d') .
                "'"
        )[0];

        $this->assertEquals(
            $new_log['Email Address'],
            $this->user['Email Address']
        );
        $this->assertEquals($new_log['Login Date'], date('Y-m-d'));
        $this->assertEquals($new_log['Login IP'], $test_ip);
    }

    // register_consumer_user
    public function testRegister_consumer_userThrowsIfNoDataProvided()
    {
        $this->expectExceptionMessage(
            'Too few arguments to function register_consumer_user(), 0 passed'
        );

        register_consumer_user();
    }

    public function testRegister_consumer_userThrowsIfFieldsAreMissing()
    {
        $insert_data = [];
        $this->expectExceptionMessage(
            '$insert_data for register_consumer_user() must contain: EMAIL, PASSWORD'
        );

        register_consumer_user($insert_data);
    }

    public function testRegister_consumer_userThrowsIfEmailNotUnique()
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Email Address' => $this->email
        ]);

        $insert_data = [
            'EMAIL'      => $this->email,
            'PASSWORD'   => $this->password,
            'FIRST_NAME' => $this->first_name,
            'LAST_NAME'  => $this->last_name,
        ];

        $this->expectExceptionMessage(
            // $this->email.' has already been used.'
            'An account with that email address already exists.'
        );

        register_consumer_user($insert_data);
    }

    public function testRegister_consumer_userThrowsIfNamesTooShort()
    {
        global $config;

        $insert_data = [
            'EMAIL'      => $this->email,
            'PASSWORD'   => $this->password,
            'FIRST_NAME' => 'Joe',
            'LAST_NAME'  => '',
        ];

        $this->expectExceptionMessage(
            'Your combined first and last name must be at least ' .
                $config['minimum_full_name_length'] .
                ' characters long'
        );

        register_consumer_user($insert_data);
    }

    public function testRegister_consumer_userHappyPath()
    {
        global $config;

        $insert_data = [
            'EMAIL'      => $this->email,
            'PASSWORD'   => $this->password,
            'FIRST_NAME' => $this->first_name,
            'LAST_NAME'  => $this->last_name,
        ];

        $this->user = register_consumer_user($insert_data);

        // Ensure the user has been created
        $this->assertNotEmpty($this->user);

        $this->assertEquals(USER_TYPE_CONSUMER, $this->user['User Type']);

        // No need to test names or login count/timestamp stuff, since that
        // is tested in insert_user
    }

    /******************************************************************
     *                  register_user
     *
     * This is the last step on the trail of registering a user. Other
     * tests for the other register_*_user functions ensure that the
     * relevant pieces of $insert_data are set further up the line
     ******************************************************************/

    public function testRegister_userThrowsIfNoDataProvided()
    {
        $insert_data = null;

        $this->expectExceptionMessage(
            'Argument 1 passed to register_user() must be of the type array, null given'
        );

        register_user($insert_data);
    }

    public function testRegister_userThrowsIfNoEmailOrShibDataProvided()
    {
        $insert_data = [
            'PASSWORD'   => $this->password,
            'FIRST_NAME' => $this->first_name,
            'LAST_NAME'  => $this->last_name,
        ];

        $this->expectExceptionMessage(
            'Arguments for register_user() must contain an email address'
        );
        register_user($insert_data);
    }

    // insert_user
    public function testInsert_userThrowsIfNoDataProvided()
    {
        $this->expectExceptionMessage('Missing $insert_data for insert_user()');

        insert_user([]);
    }

    public function testInsert_userForConsumerUser()
    {
        // Most of this will actually come from $_POST variables
        $insert_data = [
            'EMAIL'      => $this->email,
            'PASSWORD'   => $this->password,
            'FIRST_NAME' => $this->first_name,
            'LAST_NAME'  => $this->last_name,
            'USER_TYPE'  => USER_TYPE_CONSUMER,
        ];

        $new_user = insert_user($insert_data);

        // ensure the user record has been created properly
        $this->assertNotEmpty($new_user);
        $this->assertNotEmpty(
            $new_user['Creation Date'],
            'Creation Date is empty'
        );
        // When User Name gets retired, we can remove this assertion
        $this->assertEquals(
            \generate_user_name(
                $this->first_name,
                \generate_user_name($this->last_name)
            ),
            $new_user['User Name']
        );
        $this->assertEquals(USER_TYPE_CONSUMER, $new_user['User Type']);
        $this->assertEmpty($new_user['Last Login Timestamp']);
    }

    // unlock_user
    public function testUnlock_userResetsFailCountAndFailTime()
    {
        $last_fail_timestamp = 10000; // sometime in the 1970s I suspect...
        $this->user          = $this->tester->createUser($this->tester, [
            'Fails Count' => MAXIMUM_PASSWORD_ATTEMPTS,
            'Fail Time'   => $last_fail_timestamp
        ]);

        unlock_user($this->user);

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals(0, $updated_user['Fails Count']);
        $this->assertEquals(0, $updated_user['Fail Time']);
    }

    // reset_password
    public function testReset_passwordResetsPassword(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        reset_password($this->user);

        $updated_user = get_user_by_id($this->user['User ID']);

        $this->assertEquals(
            PASSWORD_RESET_TEXT,
            $updated_user['Password Hash']
        );
    }

    // is_user_locked
    public function testIs_user_lockedReturnsTrueForLockedUser()
    {
        $this->user = $this->tester->createUser($this->tester, [
            'Fails Count' => MAXIMUM_PASSWORD_ATTEMPTS
        ]);

        $this->assertTrue(is_user_locked($this->user));
    }

    public function testIs_user_lockedReturnsFalseForUnlockedUser()
    {
        $this->user = $this->tester->createUser($this->tester);

        $this->assertFalse(is_user_locked($this->user));
    }

    // increment_fail_count_and_time
    public function testIncrement_fail_count_and_time(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        $this->assertEquals(0, $this->user['Fails Count']);
        $this->assertEmpty($this->user['Fail Time']);

        $updated_user = increment_fail_count_and_time($this->user);

        $this->assertEquals(1, $updated_user['Fails Count']);
        $this->assertNotEmpty($updated_user['Fail Time']);
    }

    // is_lock_time_passed
    public function testIs_lock_time_passedReturnsTrueAfterLockTime(): void
    {
        $last_fail_timestamp = 10000; // sometime in the 1970s I suspect...
        $this->user          = $this->tester->createUser($this->tester, [
            'Fails Count' => MAXIMUM_PASSWORD_ATTEMPTS,
            'Fail Time'   => $last_fail_timestamp
        ]);

        $this->assertTrue(is_lock_time_passed($this->user));
    }

    public function testIs_lock_time_passedReturnsFalseBeforeLockTime(): void
    {
        $last_fail_timestamp = time() - 10; // 10 seconds ag0
        $this->user          = $this->tester->createUser($this->tester, [
            'Fails Count' => MAXIMUM_PASSWORD_ATTEMPTS,
            'Fail Time'   => $last_fail_timestamp
        ]);

        $this->assertFalse(is_lock_time_passed($this->user));
    }

    // is_consumer_user

    public function testIs_consumer_userThrowsIfNoUser(): void
    {
        $this->user = null;

        $this->expectExceptionMessage(
            'Argument 1 passed to is_consumer_user() must be of the type array, null given'
        );

        $result = is_consumer_user($this->user);
    }

    public function testIs_consumer_userTrueForConsumerUser(): void
    {
        $this->user = $this->tester->createUser($this->tester, [
            'User Type' => USER_TYPE_CONSUMER
        ]);
        $this->assertTrue(is_consumer_user($this->user));
    }

    // is_admin_user

    public function testIs_admin_userFalseIfNoUser(): void
    {
        $this->user = null;

        $this->assertFalse(is_admin_user($this->user));
    }

    public function testIs_admin_userFalseIfNormalUser(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        $this->assertFalse(is_admin_user($this->user));
    }

    public function testIs_admin_userTrueIfAdmin(): void
    {
        $this->user = $this->tester->createAdminUser($this->tester);

        $this->assertTrue(is_admin_user($this->user));
    }

    public function testIs_admin_userTrueIfSuperAdmin(): void
    {
        $this->user = $this->tester->createSuperUser($this->tester);

        $this->assertTrue(is_admin_user($this->user));
    }
}
