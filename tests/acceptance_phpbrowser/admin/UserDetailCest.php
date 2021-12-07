<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure user_detail.php works
 */
class UserDetailCest extends QTPageCest
{
    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("user_detail");
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
            "//h2[contains(text(),'Examining User ID')]"
        );
    }

    public function showsConsumerUser(AcceptancePhpbrowserTester $I, $scenario): void
    {
        $first_name = 'Batman';
        $user       = $I->createUser($I, ['First Name' => $first_name]);

        $I->loginToQTAsSuperUser($I);

        $this->page_of_interest = $this->page_of_interest . '?USER=' . $user['User ID'];

        $I->amOnPage($this->page_of_interest);

        $I->see(USER_TYPE_CONSUMER);
        $I->see($first_name);
    }

    public function handlesEmptyUserNames(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => '', 'Last Name' => '']);

        $this->page_of_interest .= '?USER=' . $this->user['User ID'];

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see("Not supplied", ['css' => 'td']);
    }
}
