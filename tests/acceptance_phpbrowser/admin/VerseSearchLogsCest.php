<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests for verse_search_logs.php
 */
class VerseSearchLogsCest extends QTPageCest
{
    public $user;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("verse_search_logs");
        $this->access_level     = ACCESS_LEVEL_ADMIN;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $user_ids                          = [];
        (!empty($this->user) ? $user_ids[] = $this->user['User ID'] : null);

        $I->clearLoginLogs($I, ['user_ids' => $user_ids]);
        $I->clearSearchLogs($I, ['user_ids' => $user_ids]);
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
            "//h2[contains(text(),'Viewing and Searching Activity')]"
        );
    }

    public function tableHandlesOrphanedUsers(AcceptancePhpbrowserTester $I, $scenario)
    {
        $scenario->skip('This test used to be relevant before there were referential integrity constraints on the LOGIN-LOGS table');

        $this->user = $I->createUser($I);
        $I->loginToQT($I, ['Email Address' => $this->user['Email Address']]);
        $I->doSearch($I, $scenario);
        $I->logoutFromQT($I);

        // now, orphan that user
        $sql = "DELETE FROM `USERS` 
                 WHERE `User ID` = '" . $this->user['User ID'] . "'";
        $I->executeOnDatabase($sql);

        $search_log_id = $this->get_search_log_id($I, $this->user);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see("Orphaned", ['css' => 'tr#record-id-' . $search_log_id . ' td']);
    }

    public function tableHandlesEmptyUserNames(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => '', 'Last Name' => '']);
        $I->loginToQT($I, ['Email Address' => $this->user['Email Address']]);
        $I->doSearch($I, $scenario);
        $I->logoutFromQT($I);

        $search_log_id = $this->get_search_log_id($I, $this->user);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see("Name not supplied", ['css' => 'tr#record-id-' . $search_log_id . ' td']);

        codecept_debug($this->user);
    }

    public function headingHandlesEmptyUserNames(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => '', 'Last Name' => '']);
        $I->loginToQT($I, ['Email Address' => $this->user['Email Address']]);
        $I->doSearch($I, $scenario);
        $I->logoutFromQT($I);

        $search_log_id = $this->get_search_log_id($I, $this->user);

        $this->page_of_interest .= '?U=' . $this->user['User ID'];

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see("User Name not supplied", ['css' => 'tr#record-id-' . $search_log_id . ' td']);
    }
}
