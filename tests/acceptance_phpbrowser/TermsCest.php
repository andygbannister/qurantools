<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure that about.php page works properly
 */
class TermsCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("terms");
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $I->clearLoginLogs($I);
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
            "//h2[contains(text(),'Terms of Use')]"
        );
    }

    public function showsPrivacyPolicyLinkIfSet(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $scenario->skip('this link is now in the legal menu');

        $privacy_policy_url = 'https://example.com/privacy.html';

        $I->haveHttpHeader('X-Privacy-Policy-URL', $privacy_policy_url);

        $I->amOnPage($this->page_of_interest);
        $I->seeLink('Privacy Policy', $privacy_policy_url);
    }

    public function doesNotShowPrivacyPolicyLinkIfNotSet(
        AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $scenario->skip('this link is now in the legal menu');

        // this is a hack. Sending a space sets the header, but ultimately
        // resets the configs back to nothing by the time we get to login.php
        $privacy_policy_url = ' ';

        $I->haveHttpHeader('X-Privacy-Policy-URL', $privacy_policy_url);

        $I->amOnPage($this->page_of_interest);
        $I->dontSeeLink('Privacy Policy');
    }
}
