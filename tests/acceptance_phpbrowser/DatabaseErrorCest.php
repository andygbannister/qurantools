<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for database_error.php */
class DatabaseErrorCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('database_error');
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);
    }

    public function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $access_level = '',
        $page_element = ''
    ) {
        parent::accessRulesWork($I, $scenario, "//p[contains(text(),'unable to connect to its databases')]");
    }

    public function pageTitleCorrect(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($this->page_of_interest);
        $I->seeInTitle('Database Connection Error');
    }
}
