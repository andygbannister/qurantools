<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests related to the registration of a new consumer user
 *
 * /auth/register.php
 *
 */

class RegisterConsumerUserCest extends QTPageCest
{
    public $registration_email;
    public $registration_first_name = 'Bob';
    public $registration_last_name  = 'Brown';
    public $password                = '12345678';
    public $main_app_url;
    public $extra_values;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('register');
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);

        $this->registration_email = 'test' . rand(1, 10000) . '@example.com';
    }

    public function _after($I, $scenario)
    {
        $user_id = $I->grabFromDatabase('USERS', '`User ID`', [
            'email address' => $this->registration_email
        ]);

        // The Db module of Codeception is clever enough to clear up  some of
        // its own junk, but can't get rid of changes the application makes to
        // the database
        $I->clearLoginLogs($I, [
            'user_ids' => [$user_id],
            'emails'   => [$this->registration_email]
        ]);

        $I->deleteFromDatabase('USERS', ['User ID' => $user_id]);
    }

    // This test will fail if is_user_registration_allowed is not true
    // in qt.ini
    public function notLoggedInUserCanAccess(
        AcceptancePhpbrowserTester $I,
        $scenario
    )
    {
        global $config;

        $I->allowNotLoggedInUserAccessTo(
            $I,
            $scenario,
            $this->page_of_interest
        );
    }

    public function normalUserRedirectedToHome(
        AcceptancePhpbrowserTester $I,
        $scenario
    )
    {
        global $config;

        $I->preventNormalUserAccessTo(
            $I,
            $this->extra_values,
            $scenario,
            $this->page_of_interest,
            $I->getApplicationPage('home')
        );
    }

    // TODO: it's a bit hard to test no privacy link since it's tough
    // with the current set-up to inject different config values into
    // the config file
    // This test will fail if is_user_registration_allowed is not true
    // in qt.ini
    public function showsPrivacyPolicyLinkIfSet(AcceptancePhpbrowserTester $I)
    {

        $config_file_path = __DIR__ . '/../../../qt.ini';

        $config = parse_ini_file($config_file_path);

        $privacy_policy_url = $config['privacy_policy_url'];

        $I->amOnPage($this->page_of_interest);

        $I->seeLink('Privacy policy', $privacy_policy_url);
    }

    // This test will fail if is_user_registration_allowed is not true
    // in qt.ini
    public function registerNewUser(
        AcceptancePhpbrowserTester $I,
        $scenario
    )
    {
        global $config;
        $config['main_app_url'] = $this->main_app_url;

        // register with the code and a password
        $I->amOnPage($I->getApplicationPage('register'));

        $I->fillField('#EMAIL', $this->registration_email);
        $I->fillField('#FIRST_NAME', $this->registration_first_name);
        $I->fillField('#LAST_NAME', $this->registration_last_name);
        $I->fillField('PASSWORD', $this->password);
        $I->fillField('PASSWORD_AGAIN', $this->password);

        $I->click('REGISTER_BUTTON');

        // ensure the user sees success messages
        $I->see('Success! Your new account has been created');

        $new_user = $I->selectFromDatabase(
            "SELECT * FROM `USERS` WHERE `email address` = '$this->registration_email'"
        )[0];

        // ensure the right values are in the database
        $I->seeInDatabase('USERS', [
            'Email Address'        => $this->registration_email,
            'First Name'           => $this->registration_first_name,
            'Last Name'            => $this->registration_last_name,
            'User Type'            => USER_TYPE_CONSUMER,
            'Administrator'        => null, // and NOT the empty string
            'Last Login Timestamp' => null,
            'Login Count'          => 0,
            'Fails Count'          => 0,
        ]);

        // ensure the user can actually log-in
        $I->loginToQT($I, [
            'Email Address' => $this->registration_email,
            'Password'      => $this->password
        ]);
    }

    // This test will fail if is_user_registration_allowed is not true
    // in qt.ini
    public function dontRegisterDuplicateEmailAddress(
        AcceptancePhpbrowserTester $I,
        $scenario
    )
    {
        $existing_user = $I->createUser($I, [
            'Email Address' => $this->registration_email
        ]);

        $I->amOnPage($this->page_of_interest);

        $I->fillField('EMAIL', $this->registration_email);
        $I->fillField('FIRST_NAME', $this->registration_first_name);
        $I->fillField('LAST_NAME', $this->registration_last_name);
        $I->fillField('PASSWORD', $this->password);
        $I->fillField('PASSWORD_AGAIN', $this->password);

        $I->click('REGISTER_BUTTON');

        // ensure the user sees success messages
        $I->see('An account with that email address already exists');
    }
}
