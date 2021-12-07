<?php

class _404WDCest
{
    public function _before(AcceptanceWebdriverTester $I)
    {
        // Clears all emails
        $I->resetEmails();
    }

    public function _after(AcceptanceWebdriverTester $I)
    {
        $I->deleteFromDatabase('LOGIN-LOGS', ['Email Address' => 'user@example.com']);
    }

    public function is_logged_in_userRedirectsTo_404AndThenHome(AcceptanceWebdriverTester $I, $scenario)
    {
        // From https://stackoverflow.com/questions/22259269/need-to-use-codeception-parameters-in-test-code
        $config       = \Codeception\Configuration::config();
        $apiSettings  = \Codeception\Configuration::suiteSettings('acceptance_phpbrowser', $config);
        $main_app_url = $apiSettings['modules']['enabled'][0]['PhpBrowser']['url'];

        $I->loginToQT($I);
        $I->amOnPage($I->getApplicationPage("duff"));
        $I->see('Redirecting to the');
        $I->seeInCurrentUrl($I->getApplicationPage("duff"));
    }

    public function notLoggedInUserRedirectsTo_404ButNotThenHome(AcceptanceWebdriverTester $I, $scenario)
    {
        // $I->amOnPage($I->getApplicationPage("duff"));
        $I->amOnPage('/duff.php');
        $I->dontSee('Redirecting to the');
        // $I->seeInCurrentUrl($I->getApplicationPage("duff"));
        $I->seeInCurrentUrl('/duff.php');
    }
}
