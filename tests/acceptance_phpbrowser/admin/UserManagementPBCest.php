<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure that user_management.php page works properly
 */
class UserManagementPBCest extends QTPageCest
{
    public $user;
    public $email_address;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("user_management");
        $this->access_level     = ACCESS_LEVEL_SUPERUSER;
        parent::_before($I, $scenario);

        $this->email_address = "test" . rand(0, 10000) . '@example.com';
    }

    public function _after($I, $scenario)
    {
        $I->clearLoginLogs($I);
        $I->deleteUser($I, ['Email Address' => $this->email_address]);
    }

    public function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $access_level = '',
        $page_element = ''
    )
    {
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

    public function canEditAdminType(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => 'Getting An', 'Last Name' => 'Upgrade']);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->click('#edit-user-' . $this->user['User ID']);

        $I->selectOption('#ADMINISTRATOR', 'ADMIN');
        $I->click('Update User');

        $I->see('Getting An Upgrade was successfully updated', 'b');;
        $I->see('ADMIN', "tr#user-id-" . $this->user['User ID'] . " td.administrator",);
    }

    public function canChooseAdminTypeForNewUser(AcceptancePhpbrowserTester $I, $scenario)
    {
        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->click('#create-user');

        $I->fillField('USER_EMAIL', $this->email_address);
        $I->fillField('FIRST_NAME', 'Clark');
        $I->fillField('LAST_NAME', 'Kent');
        $I->fillField('PASSWORD1', 'secret-squirrel');
        $I->fillField('CONFIRM_PASSWORD', 'secret-squirrel');

        $I->selectOption('#ADMINISTRATOR', 'ADMIN');
        $I->click('CREATE NEW USER');

        $this->user   = \get_user_by_email($this->email_address);

        $I->see('A new user has been created: ' . $this->email_address, 'b');;
        $I->see('ADMIN', "tr#user-id-" . $this->user['User ID'] . " td.administrator",);
    }
}
