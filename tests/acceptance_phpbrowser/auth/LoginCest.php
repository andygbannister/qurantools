<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";

/**
 * Tests for the look of the login page.
 * Tests for authentication and logging in are done in AccessCest.php and
 * AuthCest.php
 */
class LoginCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("login");
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $I->clearLoginLogs($I, ['user_ids' => []]);
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
            "//div[@id='login-form']"
        );
    }

    public function showsNoHostingOrganisationIfNotSet(AcceptancePhpbrowserTester $I)
    {
        global $config;

        // this is a hack. Sending a space sets the header, but ultimately
        // resets the configs back to nothing by the time we get to login.php
        $hosting_organisation     = ' ';
        $hosting_organisation_url = ' ';

        $I->haveHttpHeader('X-Hosting-Organisation', $hosting_organisation);
        $I->haveHttpHeader('X-Hosting-Organisation-URL', $hosting_organisation_url);

        $I->amOnPage($this->page_of_interest);

        $config['hosting_organisation'] = $hosting_organisation;

        $I->dontSee('This installation of Qur’an Tools is hosted by', 'form#login .branding.message');
        $I->dontSeeElement('form#login .branding.message a');
    }

    public function showsHostingOrganisationAndUrlIfSet(AcceptancePhpbrowserTester $I)
    {
        global $config;

        $hosting_organisation     = 'University X';
        $hosting_organisation_url = 'https://universityx.edu/';

        $I->haveHttpHeader('X-Hosting-Organisation', $hosting_organisation);
        $I->haveHttpHeader('X-Hosting-Organisation-URL', $hosting_organisation_url);

        $I->amOnPage($this->page_of_interest);

        $config['hosting_organisation'] = $hosting_organisation;

        $I->see('This installation of Qur’an Tools is hosted by', 'form#login .branding.message');
        $I->see($hosting_organisation, 'form#login .branding.message');
        $I->seeLink($hosting_organisation, $hosting_organisation_url);
        $I->seeElement('form#login .branding.message a');
    }

    public function showsOnlyHostingOrganisationIfUrlNotSet(AcceptancePhpbrowserTester $I)
    {
        global $config;

        $hosting_organisation = 'University Y';
        // this is a hack. Sending a space sets the header, but ultimately
        // resets the URL back to nothing by the time we get to login.php
        $hosting_organisation_url = ' ';

        $I->haveHttpHeader('X-Hosting-Organisation', $hosting_organisation);
        $I->haveHttpHeader('X-Hosting-Organisation-URL', $hosting_organisation_url);

        $I->amOnPage($this->page_of_interest);

        $config['hosting_organisation'] = $hosting_organisation;

        $I->see('This installation of Qur’an Tools is hosted by', 'form#login .branding.message');
        $I->see($hosting_organisation, 'form#login .branding.message');
        $I->dontSeeLink($hosting_organisation, 'form#login .branding.message');
        $I->dontSeeElement('form#login .branding.message a');
    }
}
