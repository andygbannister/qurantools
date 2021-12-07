<?php

/**
 * Tests to ensure that home.php page works properly
 */

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

class HomeWDCest
{
    public $user;
    public $email;
    public $password = '12345678';

    public function _before(AcceptanceWebdriverTester $I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('home');
        $this->email            = 'test' . rand(1, 100000) . '@example.com';
    }

    public function _after()
    {
        // remove login artifacts
        db_query("DELETE FROM `LOGIN-LOGS` WHERE `Email Address` IN ('superuser@example.com','user@example.com','admin@example.com','" . $this->email . "')");
    }

    public function showsQuickTipIfRequested(AcceptanceWebdriverTester $I, $scenario)
    {
        $this->user = $I->createUser(
            $I,
            [
                'Email Address'              => $this->email,
                'Preference Show Quick Tips' => true,
                'Current Quick Tip ID'       => 1
            ]
        );
        $I->loginToQT($I, ['Email Address' => $this->email]);

        $I->amOnPage($this->page_of_interest);
        $I->seeInCurrentUrl($this->page_of_interest);

        $I->waitForText('Quick Tips');  // since this comes via AJAX, we need to wait for it
        $I->see('Next Tip');
        $I->dontSee('Previous Tip');
    }

    public function doesNotShowQuickTipIfNotRequested(AcceptanceWebdriverTester $I, $scenario)
    {
        $this->user = $I->createUser(
            $I,
            [
                'Email Address'              => $this->email,
                'Preference Show Quick Tips' => false,
                'Current Quick Tip ID'       => 1
            ]
        );
        $I->loginToQT($I, ['Email Address' => $this->email]);

        $I->amOnPage($this->page_of_interest);
        $I->seeInCurrentUrl($this->page_of_interest);

        $I->dontSee('Quick Tips');
        $I->dontSee('Next Tip');
        $I->dontSee('Previous Tip');
    }
}
