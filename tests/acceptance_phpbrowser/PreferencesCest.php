<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for preferences.php
 */
class PreferencesCest extends QTPageCest
{
    public $user;
    public $email;
    public $password = 'secret-codeword-123';

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('preferences');
        $this->access_level     = ACCESS_LEVEL_NORMAL;
        parent::_before($I, $scenario);
    }

    // this is a hack function because the tests use the dev database rather
    // than a test database that would automatically be cleared after each test
    public function _after($I, $scenario)
    {
        $user_ids = [];
        $emails   = [];

        // harvest possible IDs and emails from tests in order to delete them
        !empty($this->user) ? ($user_ids[] = $this->user['User ID']) : null;
        !empty($this->email) ? ($emails[]  = $this->email) : null;
        !empty($this->user) ? ($emails[]   = $this->user['Email Address']) : null;

        $I->clearLoginLogs($I, ['user_ids' => $user_ids, 'emails' => $emails]);

        if (!empty($this->email))
        {
            $I->deleteFromDatabase('USERS', ['Email Address' => $this->email]);
        }

        if (!empty($this->user['Email Address']))
        {
            $I->deleteFromDatabase('USERS', [
                'Email Address' => $this->user['Email Address']
            ]);
        }
    }

    public function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $access_level = '',
        $page_element = ''
    ) {
        parent::accessRulesWork(
            $I,
            $scenario,
            "//h2[contains(text(),'Preferences and Account Settings')]",
            ['Email Address' => $this->email]
        );
    }

    public function userCanUpdateEmailWithValidEmailAddress(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $old_email  = 'bob@example.com';
        $new_email  = 'mary@example.com';
        $this->user = $I->createUser($I, ['Email Address' => $old_email, 'Password' => $this->password]);
        $I->loginToQT($I, ['Email Address' => $old_email, 'Password' => $this->password]);

        $I->amOnPage($this->page_of_interest);
        $I->seeElement('#change-email');
        $I->click('#change-email');
        $I->fillField('NEW_EMAIL', $new_email);
        $I->click('DO_EMAIL');

        // do the test
        $I->see('Email Address updated successfully');
    }

    public function userCannotUpdateEmailWithBogusAddress(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $old_email  = 'bob@example.com';
        $new_email  = 'goofball';
        $this->user = $I->createUser($I, ['Email Address' => $old_email, 'Password' => $this->password]);
        $I->loginToQT($I, ['Email Address' => $old_email, 'Password' => $this->password]);

        $I->amOnPage($this->page_of_interest);
        $I->seeElement('#change-email');
        $I->click('#change-email');
        $I->fillField('NEW_EMAIL', $new_email);
        $I->click('DO_EMAIL');

        $I->see('You cannot change you email address to ' . $new_email);
        // Ensure the database has not been changed
        $I->dontSeeInDatabase('USERS', ['Email Address' => $new_email]);
    }

    public function userCannotUpdateEmailWithDuplicateAddress(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $existing_email = 'mary@example.com';

        $old_email = 'bob@example.com';
        $new_email = $existing_email;

        $existing_user = $I->createUser($I, ['Email Address' => $existing_email]);

        $this->user = $I->createUser($I, ['Email Address' => $old_email, 'Password' => $this->password]);

        $I->loginToQT($I, ['Email Address' => $old_email, 'Password' => $this->password]);
        $I->amOnPage($this->page_of_interest);

        $I->seeElement('#change-email');

        $I->click('#change-email');

        $I->fillField('NEW_EMAIL', $new_email);

        $I->click('DO_EMAIL');

        // do the tests
        $I->see('You cannot change you email address to ' . $new_email);

        // Ensure the database has not been changed
        $I->seeInDatabase('USERS', ['User ID' => $this->user['User ID'], 'Email Address' => $old_email]);
        $I->seeInDatabase('USERS', ['User ID' => $existing_user['User ID'], 'Email Address' => $existing_email]);

        $this->email = $existing_email; // ensure mary is deleted from DB after test
    }

    public function consumerUserCannotUpdateFirstAndLastNamesToBeTooShort(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $new_first_name = 'Jo';
        $new_last_name  = 'Mo';
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level,
            ['Email Address' => $this->email]
        );

        $I->seeElement('#change-name');

        $I->click('#change-name');
        $I->fillField('NEW_FIRST_NAME', $new_first_name);
        $I->fillField('NEW_LAST_NAME', $new_last_name);
        $I->click('DO_NAME');

        $I->see('Your name is too short');
    }

    public function consumerUserCannotUpdateFirstAndLastNamesToBeEmpty(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $new_first_name = '';
        $new_last_name  = '';
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level,
            ['Email Address' => $this->email]
        );

        $I->seeElement('#change-name');

        $I->click('#change-name');
        $I->fillField('NEW_FIRST_NAME', $new_first_name);
        $I->fillField('NEW_LAST_NAME', $new_last_name);
        $I->click('DO_NAME');

        $I->see('Your name is too short');
    }

    public function consumerUserCanUpdatePassword(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level,
            ['Email Address' => $this->email]
        );

        $I->seeElement('#change-password');
    }

    public function changePassword(AcceptancePhpbrowserTester $I, $scenario)
    {
        $old_password = 'super-secret!';
        $new_password = 'scooby-snacks!';
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level,
            ['Email Address' => $this->email, 'Password' => $old_password]
        );

        $I->seeElement('#change-password');

        $I->click("#change-password");

        $I->fillField('OLD_PASSWORD', $old_password);
        $I->fillField('PASSWORD1', $new_password);
        $I->fillField('CONFIRM_PASSWORD', $new_password);
        $I->click("CHANGE PASSWORD");

        // do the test
        $I->see('Your password was successfully changed.');
    }
}
