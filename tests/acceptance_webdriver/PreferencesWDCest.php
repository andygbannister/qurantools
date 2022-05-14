<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for preferences.php
 */
class PreferencesWDCest
{
    public $user;
    public $password = 'secret-codeword-123';

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('preferences');
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

        // This text comes from the jQuery validator plugin
        $I->see('Please enter a valid email address');
        // Ensure the database has not been changed
        $I->dontSeeInDatabase('USERS', ['Email Address' => $new_email]);
    }
}
