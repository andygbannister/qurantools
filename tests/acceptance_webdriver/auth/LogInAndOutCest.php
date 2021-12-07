<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

class LogInAndOutCest
{
    public $user;
    public $password = '12345678';

    public function _before(AcceptanceWebdriverTester $I)
    {
        // Clears all emails
        $I->resetEmails();

        $this->user = $I->createUser($I, ['Password' => $this->password]);
    }

    public function _after(AcceptanceWebdriverTester $I)
    {
        // Codeception is clever enough to clear up its own junk, but can't get rid
        // of changes the application makes to the database
        $I->deleteFromDatabase('LOGIN-LOGS', ['User ID' => $this->user['User ID']]);

        // remove login artifacts
        db_query("DELETE FROM `LOGIN-LOGS` WHERE `Email Address` IN ('superuser@example.com','user@example.com','admin@example.com','" . $this->user['Email Address'] . "')");
    }

    public function loginSuccessfully(AcceptanceWebdriverTester $I)
    {
        $I->amOnPage('/home.php');
        $I->seeElement('#login-button');
        $I->fillField('EMAIL_ADDRESS', $this->user['Email Address']);
        // here are other ways to fill in a field - assuming the page is well formed HTML
        // $I->fillField('form input[name=EMAIL_ADDRESS]', 'tester@example.com');
        // $I->fillField('EMAIL ADDRESS', 'tester@example.com');
        // $I->fillField('#email_address', 'tester@example.com');
        // $I->fillField('input#email_address', 'tester@example.com');
        // $I->fillField(['id' => 'email_address'], 'tester@example.com');
        // $I->fillField(['name' => 'EMAIL_ADDRESS'], 'tester@example.com');
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');
        $I->seeElement('#ok-button');
    }

    public function logoutSuccessfully(AcceptanceWebdriverTester $I)
    {
        // log-in first
        $I->loginToQT(
            $I,
            ['Email Address' => $this->user['Email Address']]
        );

        // do logout
        $I->moveMouseOver(['id' => 'my-profile-menu']);
        $I->click(['id' => 'logout']);
        // $I->waitForElementVisible(['id' => 'logged-out-message']);
        $I->seeElement(['id' => 'logged-out-message']);
    }
}
