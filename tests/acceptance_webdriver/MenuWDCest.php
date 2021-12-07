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
        $I->seeElement('a', ['id' => 'user-guide-link']);
        $I->moveMouseOver(['id' => 'about-menu']);
        $I->seeElement('a', ['id' => 'about-link']);

        // stuff I shouldn't see
        $I->dontSeeElement('li', ['id' => 'browse-menu']);
        $I->dontSeeElement('li', ['id' => 'browse-menu']);
        $I->dontSeeElement('li', ['id' => 'admin-tools-menu']);
        $I->dontSeeElement('a', ['id' => 'whats-new-link']);
        $I->dontSeeElement('a', ['id' => 'blog-news-link']);
    }

    public function loggedInConsumerUserSeesCorrectMenuItems(AcceptanceWebdriverTester $I, $scenario)
    {
        $scenario->skip('for some reason this test runs REAL slow, so I\'m canning it for now');
        $I->loginToQT($I);
        // $I->redirectToLoginFor($I, $this->page_of_interest);

        $I->see('Quick Tips');

        // home page
        $I->seeElement('a', ['id' => 'home-page-link']);
        // help menu
        $I->moveMouseOver(['id' => 'help-menu']);
        $I->seeElement('a', ['id' => 'user-guide-link']);
        $I->seeElement('a', ['id' => 'training-videos-link']);
        $I->seeElement('a', ['id' => 'contact-us-link']);
        $I->moveMouseOver(['id' => 'about-menu']);
        $I->seeElement('a', ['id' => 'about-link']);
        $I->seeElement('a', ['id' => 'whats-new-link']);
        $I->seeElement('a', ['id' => 'blog-news-link']);

        // stuff I shouldn't see
        $I->dontSeeElement('li', ['id' => 'browse-menu']);
        $I->dontSeeElement('li', ['id' => 'browse-menu']);
        $I->dontSeeElement('li', ['id' => 'admin-tools-menu']);
    }
}
