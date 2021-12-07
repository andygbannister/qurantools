<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

include_once "library/database.php";

/**
 * Tests related to the registration of a new consumer user
 *
 * Most of this is tested in the PHPBrowser Cests, but these tests ensure that
 * the JavaScript works - currently forms validation.
 */
class RegisterConsumerUserWDCest extends QTPageCest
{
    public $registration_email;
    public $registration_first_name = 'Bob';
    public $registration_last_name  = 'Brown';
    public $password                = '12345678';

    public function _before($I, $scenario)
    {
        global $config;

        $this->page_of_interest = $I->getApplicationPage("register");
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);

        $this->registration_email = 'test' . rand(1, 10000) . '@example.com';
    }

    public function _after($I, $scenario)
    {
        $I->deleteFromDatabase('USERS', ['Email Address' => $this->registration_email]);
    }

    // This test will fail if is_user_registration_allowed is not true
    // in qt.ini
    public function registerUserRequiresCertainFields(AcceptanceWebdriverTester $I)
    {
        $I->amOnPage($this->page_of_interest);

        $I->fillField('#FIRST_NAME', 'bo');
        $I->fillField('#LAST_NAME', 'jo');
        $I->fillField('#PASSWORD', '');

        $I->click("REGISTER_BUTTON");

        $I->see('Your combined first and last name must be at least 7 characters long');
        $I->see('This field is required.');
    }
}
