<?php

/**
 * Tests for user_management.php
 */
use Codeception\Util\Locator;

class UserManagementWDCest extends QTPageCest
{
    public $email      = 'some_email@example.com';
    public $first_name = 'Some Guy';
    public $last_name  = 'Smith';
    public $password   = '12345678';
    public $user;
    public $minimum_full_name_length;

    public function _before($I, $scenario)
    {
        $config      = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings(
            'acceptance_webdriver',
            $config
        );
        // It would be nice to be able to grab this from qt.ini but I'm not
        // sure how, so we'll have to make do with test_config.yml
        $this->minimum_full_name_length = $apiSettings['modules']['config']['App']['minimum_full_name_length'];

        $this->page_of_interest = $I->getApplicationPage("user_management");
        $this->access_level     = ACCESS_LEVEL_ADMIN;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $user_ids = [];
        $emails   = [];

        (!empty($this->user) ? $user_ids[] = $this->user['User ID'] : null);
        (!empty($this->email) ? $emails[] = $this->email : null);

        $I->clearLoginLogs($I, ['user_ids' => $user_ids, 'emails' => $emails]);

        if (!empty($this->email))
        {
            $I->deleteFromDatabase('USERS', ['Email Address' => $this->email]);
        }

        if (!empty($this->user['Email Address']))
        {
            $I->deleteFromDatabase('USERS', ['Email Address' => $this->user['Email Address']]);
        }
    }

    public function createNewUserHappyPath(AcceptanceWebdriverTester $I, $scenario)
    {
        // $I->loginToQTAsSuperUser($I, [], ['snapshot' => 'superuser']);
        $I->loginToQTAsSuperUser($I);
        $I->amOnPage($this->page_of_interest);
        $I->click(['id' => 'create-user']);

        $I->seeElement(Locator::contains('h3', 'Create a New User'));

        $I->fillField('USER_EMAIL', $this->email);
        $I->fillField('FIRST_NAME', $this->first_name);
        $I->fillField('LAST_NAME', $this->last_name);
        $I->fillField('PASSWORD1', $this->password);
        $I->fillField('CONFIRM_PASSWORD', $this->password);
        $I->click(['name' => 'DO_NEW_USER']);

        $I->see("A new user has been created: " . $this->email);

        $new_user = $I->selectFromDatabase('SELECT * FROM `USERS` where `Email Address` = "' . $this->email . '"');

        codecept_debug($new_user);

        // ensure the right values are in the database
        $I->seeInDatabase('USERS', [
            'Email Address' => $this->email,
            'First Name'    => $this->first_name,
            'Last Name'     => $this->last_name,
        ]);
    }

    // This test will change when we start using jQuery validate for the form
    public function createNewUserWithNoUserName(AcceptanceWebdriverTester $I, $scenario)
    {
        $I->loginToQTAsSuperUser($I);
        // $I->loginToQTAsSuperUser($I, [], ['snapshot' => 'superuser']);

        $I->amOnPage($this->page_of_interest);
        $I->click(['id' => 'create-user']);

        $I->seeElement(Locator::contains('h3', 'Create a New User'));

        $I->fillField('USER_EMAIL', $this->email);
        $I->fillField('PASSWORD1', $this->password);
        $I->fillField('CONFIRM_PASSWORD', $this->password);
        $I->click(['name' => 'DO_NEW_USER']);

        $I->dontSee("A new user has been created: " . $this->email);
        $I->see("Combined first and last name must be at least " . $this->minimum_full_name_length . ' characters long');

        // ensure the right values are in the database
        $I->dontSeeInDatabase('USERS', [
            'Email Address' => $this->email,
            'First Name'    => $this->first_name,
            'Last Name'     => $this->last_name,
        ]);
    }

    public function consumerUserShowsActions(AcceptanceWebdriverTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['User Type' => USER_TYPE_CONSUMER]);

        $I->loginToQTAsSuperUser($I, [], ['snapshot' => 'superuser']);
        $I->amOnPage($this->page_of_interest);

        $I->fillField('#manage-users_filter input', $this->user['Email Address']);

        $I->seeElement('//tr[@id="user-id-' . $this->user['User ID'] . '"]/td[contains(@class,"actions")]/a');
    }
}
