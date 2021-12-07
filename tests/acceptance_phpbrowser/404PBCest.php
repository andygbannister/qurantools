<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure that 404.php page works properly
 */
class _404PBCest
{
    public function _before(AcceptancePhpbrowserTester $I)
    {
    }

    public function _after(AcceptancePhpbrowserTester $I)
    {
        $I->deleteFromDatabase('LOGIN-LOGS', ['Email Address' => 'user@example.com']);
    }

    public function loggedUserCanVisit_404(AcceptancePhpbrowserTester $I, $scenario)
    {
        $I->loginToQT($I);
        $I->amOnPage($I->getApplicationPage("404"));
        $I->see('Oh dear, something has gone wrong!');
        $I->seeInCurrentUrl($I->getApplicationPage("404"));
    }

    public function is_logged_in_userRedirectsTo_404(AcceptancePhpbrowserTester $I, $scenario)
    {
        $I->loginToQT($I);
        $I->amOnPage($I->getApplicationPage("duff"));
        $I->seeInCurrentUrl($I->getApplicationPage("duff"));
    }

    public function notloggedUserCanVisit_404(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($I->getApplicationPage("404"));
        $I->see('Oh dear, something has gone wrong!');
        $I->seeInCurrentUrl($I->getApplicationPage("404"));
    }

    public function notLoggedInUserRedirectsTo_404(AcceptancePhpbrowserTester $I, $scenario)
    {
        // From https://stackoverflow.com/questions/22259269/need-to-use-codeception-parameters-in-test-code
        $config       = \Codeception\Configuration::config();
        $apiSettings  = \Codeception\Configuration::suiteSettings('acceptance_phpbrowser', $config);
        $main_app_url = $apiSettings['modules']['enabled'][0]['PhpBrowser']['url'];

        $I->amOnPage($I->getApplicationPage("duff"));
        $I->see('Oh dear, something has gone wrong!');
        $I->seeInCurrentUrl($I->getApplicationPage("duff"));
    }

    public function pageTitleCorrect(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($I->getApplicationPage("404"));
        $I->seeInTitle('Page Not Found');
    }
}
