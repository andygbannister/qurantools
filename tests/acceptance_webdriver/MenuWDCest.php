<?php

/**
 * Tests to ensure that the menu contains the expected elements depending
 * on the user type
 */

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

use QT\TestHelpers as helper;

class MenuWDCest
{
    public $page_of_interest;

    public function _before(AcceptanceWebdriverTester $I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('home');
    }

    public function notLoggedInUserSeesCorrectMenuItems(AcceptanceWebdriverTester $I, $scenario)
    {
        $I->redirectToLoginFor($I, $this->page_of_interest);

        // home page
        $I->seeElement('a', ['id' => 'home-page-link']);
        // help menu
        $I->moveMouseOver(['id' => 'help-menu']);
        // $I->waitForElement('a#this-help-page-link');
        $I->waitForElement('a#getting-started-link', 2);
        $I->waitForElement('a#help-index-link', 2);
        $I->waitForElement('li#about-menu', 2);
        $I->waitForElement('li#legal-menu', 2);
        $I->waitForElement('a#contact-us-link', 2);

        // about menu
        $I->moveMouseOver(['id' => 'about-menu']);
        $I->waitForElement('a#about-link', 2);

        // legal menu
        $I->moveMouseOver(['id' => 'help-menu']);
        $I->moveMouseOver(['id' => 'legal-menu']);
        $I->waitForElement('a#license-link', 2);
        $I->waitForElement('a#privacy-policy-link', 2);

        // stuff a non-logged in should not see
        $I->dontSeeElement('li', ['id' => 'browse-menu']);
        $I->dontSeeElement('li', ['id' => 'admin-tools-menu']);
    }

    public function loggedInConsumerUserSeesCorrectMenuItems(AcceptanceWebdriverTester $I, $scenario)
    {
        $I->loginToQT($I);

        // home page
        $I->waitForElement('a#home-page-link');

        // browse menu
        $I->seeElement('li', ['id' => 'browse-menu']);

        // stuff a consumer should not see
        $I->dontSeeElement('li', ['id' => 'admin-tools-menu']);
    }

    public function loggedInAdminUserSeesCorrectMenuItems(AcceptanceWebdriverTester $I, $scenario)
    {
        $I->loginToQTAsAdmin($I);

        // home page
        $I->waitForElement('a#home-page-link');

        // admin menu
        $I->waitForElement('li#admin-tools-menu');
    }
}
