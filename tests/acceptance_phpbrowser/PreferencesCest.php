<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for preferences.php
 */
class PreferencesCest extends QTPageCest
{
    public $user;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('preferences');
        $this->access_level     = ACCESS_LEVEL_NORMAL;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $user_ids = [];
        $emails   = [];

        !empty($this->user) ? ($user_ids[] = $this->user['User ID']) : null;
        !empty($this->email) ? ($emails[] = $this->email) : null;

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

    public function consumerUserCannotUpdateEmail(
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

        $I->dontSeeElement('#change-email');
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
