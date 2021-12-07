<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for maintenance.php */
class MaintenanceCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('maintenance');
        $this->access_level     = ACCESS_LEVEL_OPEN;
        parent::_before($I, $scenario);
    }

    public function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $access_level = '',
        $page_element = ''
    ) {
        parent::accessRulesWork($I, $scenario, "//h2[contains(text(),'main')]");
    }

    public function pageTitleCorrect(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($this->page_of_interest);
        $I->seeInTitle('Down For Maintenance');
    }
}
