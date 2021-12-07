<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for standard_header.php
 */
class StandardHeaderCest
{
    public function redirectsIfMaintenceModeEnabled(AcceptancePhpbrowserTester $I, $scenario)
    {
        $scenario->skip('Not yet implemented until we  know how to change $config values in qt.ini at run time for Acceptance Tests.');

        global $config;

        // does not work
        $config['is_maintenance_mode_enabled'] = true;

        $I->amOnPage($I->getApplicationPage('home'));

        $I->seeInCurrentUrl($I->getApplicationPage('maintenance'));
    }
}
