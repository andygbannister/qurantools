<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for customer_statistics.php
 */
class CustomerStatisticsCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("customer_statistics");
        $this->access_level     = ACCESS_LEVEL_ADMIN;
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
            "//h2[contains(text(),'Customer Statistics')]"
        );
    }

    public function hasTableControlsWithDefaultsSelected($I, $scenario)
    {
        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        // period types
        $I->see("Six Calendar Months", ['css' => 'a.selected']);
        $I->see("Four Weeks", ['css' => 'a']);

        // statistics types
        $I->see("Logins", ['css' => 'a.selected']);
        $I->see("Activity", ['css' => 'a']);
    }
}
