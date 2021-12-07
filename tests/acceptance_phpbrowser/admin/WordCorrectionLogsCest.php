<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for word_correction_logs.php
 */
class WordCorrectionLogsCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("word_correction_logs");
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
            "//h2[contains(text(),'Word Correction List')]"
        );
    }
}
