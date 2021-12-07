<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";

/**
 * Tests for normal user access. These tests are all designed to ensure that
 * normal users cannot access admin/superuser/word fixer areas of the site
 */
class AccessCest
{
    public $default_page = '/home.php';
    public $_404_page    = '/404.php';

    // $I is of AcceptanceWebdriverTester so that we can use the createUser
    // function defined in AcceptanceWebdriverTester
    public function _before(AcceptancePhpbrowserTester $I)
    {
        $I->loginToQT($I);
    }

    public function _after(AcceptancePhpbrowserTester $I)
    {
        // remove login artifacts
        db_query("DELETE FROM `LOGIN-LOGS` WHERE `Email Address` IN ('superuser@example.com','user@example.com','admin@example.com')");
    }

    public function amOnHomePageAfterLoggingIn(AcceptancePhpbrowserTester $I)
    {
        $I->seeInCurrentUrl($this->default_page);
    }

    public function cannotVisitAdminPages(AcceptancePhpbrowserTester $I)
    {
        // TODO: this is not an exhaustive list of admin pages
        // More should be added in QTTestHelper.php and referred to here.
        $admin_pages = [
            'login_logs',
            'customer_statistics',
            'failed_searches',
            'page_usage_statistics',
            'translation_word_tag_stats',
            'user_management',
            'user_detail',
            'word_correction_logs',
            'verse_search_logs'
        ];

        foreach ($admin_pages as $page)
        {
            // codecept_debug('----> Trying to visit: ' . $I->getApplicationPage($page));
            $I->amOnPage($I->getApplicationPage($page));
            $I->seeInCurrentUrl($this->_404_page);
        }
    }
}
