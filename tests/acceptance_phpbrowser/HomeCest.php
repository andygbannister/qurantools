<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for home.php
 */
class HomeCest extends QTPageCest
{
    public $user;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage('home');
        $this->access_level     = ACCESS_LEVEL_NORMAL;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $user_ids = [];

        $user_ids[] = $this->user['User ID'] ?? null;

        $I->clearLoginLogs($I, ['user_ids' => array_filter($user_ids)]);
    }

    public function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $access_level = '',
        $page_element = ''
    ) {
        parent::accessRulesWork($I, $scenario, "//div[@id='search-controls']");
    }
}
