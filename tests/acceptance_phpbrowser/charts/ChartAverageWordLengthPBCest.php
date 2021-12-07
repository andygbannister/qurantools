<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure chart_average_word_length.php works
 */
require_once 'tests/_support/QTChartPBCest.php';

class ChartAverageWordLengthPBCest extends QTPageCest
{
    use QTChartPBCest;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("chart_average_word_length");
        $this->access_level     = ACCESS_LEVEL_NORMAL;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $I->clearLoginLogs($I);
    }

    public function accessRulesWork(AcceptancePhpbrowserTester $I, $scenario, $access_level = '', $page_element = '')
    {
        parent::accessRulesWork($I, $scenario, "//h2[contains(text(),'Average Word Length per Sura')]");
    }
}
