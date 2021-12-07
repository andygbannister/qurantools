<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure that user_management.php page works properly
 */
class UserManagementPBCest extends QTPageCest
{
    public $user;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("user_management");
        $this->access_level     = ACCESS_LEVEL_SUPERUSER;
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
            "//h2[contains(text(),'User Management')]"
        );
    }

    public function handlesEmptyUserNames(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => '', 'Last Name' => '']);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see($this->user['Email Address'], ['css' => 'td']);
        $I->see("Name not supplied", ['css' => 'td']);
    }
}
