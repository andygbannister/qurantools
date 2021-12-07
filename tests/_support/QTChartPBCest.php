<?php

trait QTChartPBCest
{
    public function hasStandardChartFeatures(\AcceptancePhpbrowserTester $I, $scenario)
    {
        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->seeElement('.print-control');
    }
}
