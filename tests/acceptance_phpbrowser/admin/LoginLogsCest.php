<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

/**
 * Tests to ensure login_logs.php works
 */
class LoginLogsCest extends QTPageCest
{
    public $first_name_1;
    public $first_name_2;
    public $email_1;
    public $email_2;
    public $user;
    public $user_1;
    public $user_2;

    public function _before($I, $scenario)
    {
        $this->page_of_interest = $I->getApplicationPage("login_logs");
        $this->access_level     = ACCESS_LEVEL_ADMIN;
        parent::_before($I, $scenario);
    }

    public function _after($I, $scenario)
    {
        $user_ids = [];

        $user_ids[] = $this->user['User ID'] ?? null;
        $user_ids[] = $this->user_1['User ID'] ?? null;
        $user_ids[] = $this->user_2['User ID'] ?? null;

        $I->clearLoginLogs($I, ['user_ids' => array_filter($user_ids)]);
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
            "//h2[contains(text(),'Login Logs')]"
        );
    }

    public function handlesOrphanedUsers(AcceptancePhpbrowserTester $I, $scenario)
    {
        $scenario->skip('This test used to be relevant before there were referential integrity constraints on the LOGIN-LOGS table');

        $this->user = $I->createUser($I);
        $I->loginToQT($I, ['Email Address' => $this->user['Email Address']]);
        $I->logoutFromQT($I);

        // now, orphan that user
        $sql = "DELETE FROM `USERS` 
                 WHERE `User ID` = '" . $this->user['User ID'] . "'";
        $I->executeOnDatabase($sql);

        $login_log_record_id = $this->get_login_log_record_id($I, $this->user);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see($this->user['Email Address'], ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);
        $I->see("Orphaned", ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);
    }

    public function handlesEmptyUserNames(AcceptancePhpbrowserTester $I, $scenario)
    {
        $this->user = $I->createUser($I, ['First Name' => '', 'Last Name' => '']);
        $I->loginToQT($I, ['Email Address' => $this->user['Email Address']]);
        $I->logoutFromQT($I);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see($this->user['Email Address'], ['css' => 'td']);
        $I->see("Name not supplied", ['css' => 'td']);
    }

    public function searchByTextWorksForUserName(AcceptancePhpbrowserTester $I, $scenario)
    {
        // create users
        $this->first_name_1 = 'bob-1-' . rand(1, 10000);
        $this->email_1      = 'bob-one@example.com';
        $this->first_name_2 = 'bob-2-' . rand(1, 10000);
        $this->email_2      = 'bob-two@example.com';
        $this->user_1       = $I->createUser($I, ['First Name' => $this->first_name_1, 'Email Address' => $this->email_1]);
        $this->user_2       = $I->createUser($I, ['First Name' => $this->first_name_2, 'Email Address' => $this->email_2]);

        // create login logs
        $I->loginToQT($I, ['Email Address' => $this->email_1]);
        $I->logoutFromQT($I);
        $I->loginToQT($I, ['Email Address' => $this->email_2]);
        $I->logoutFromQT($I);

        $login_log_record_id_1 = $this->get_login_log_record_id($I, $this->user_1);
        $login_log_record_id_2 = $this->get_login_log_record_id($I, $this->user_2);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see($this->first_name_1, ['css' => 'tr#record-id-' . $login_log_record_id_1 . ' td']);
        $I->see($this->first_name_2, ['css' => 'tr#record-id-' . $login_log_record_id_2 . ' td']);
        $I->fillField('SEARCH', $this->first_name_1);
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name_1, ['css' => 'tr#record-id-' . $login_log_record_id_1 . ' td']);
        $I->dontSee($this->first_name_2, ['css' => 'tr#record-id-' . $login_log_record_id_2 . ' td']);
    }

    public function searchByTextWorksForEmailAddress(AcceptancePhpbrowserTester $I, $scenario)
    {
        // create users
        $this->first_name_1 = 'bob-1-' . rand(1, 10000);
        $this->email_1      = 'bob-one@example.com';
        $this->first_name_2 = 'bob-2-' . rand(1, 10000);
        $this->email_2      = 'bob-two@example.com';
        $this->user_1       = $I->createUser($I, ['First Name' => $this->first_name_1, 'Email Address' => $this->email_1]);
        $this->user_2       = $I->createUser($I, ['First Name' => $this->first_name_2, 'Email Address' => $this->email_2]);

        // create login logs
        $I->loginToQT($I, ['Email Address' => $this->email_1]);
        $I->logoutFromQT($I);
        $I->loginToQT($I, ['Email Address' => $this->email_2]);
        $I->logoutFromQT($I);

        // get the login records
        $login_log_record_id_1 = $this->get_login_log_record_id($I, $this->user_1);
        $login_log_record_id_2 = $this->get_login_log_record_id($I, $this->user_2);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->see($this->first_name_1, ['css' => 'tr#record-id-' . $login_log_record_id_1 . ' td']);
        $I->see($this->first_name_2, ['css' => 'tr#record-id-' . $login_log_record_id_2 . ' td']);
        $I->fillField('SEARCH', $this->first_name_1);
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name_1, ['css' => 'tr#record-id-' . $login_log_record_id_1 . ' td']);
        $I->dontSee($this->first_name_2, ['css' => 'tr#record-id-' . $login_log_record_id_2 . ' td']);
    }

    public function searchByTextIsCaseInsensitive(AcceptancePhpbrowserTester $I, $scenario)
    {
        // create users
        $this->first_name = 'bob-1-' . rand(1, 10000);
        $this->email      = 'bob-one@example.com';
        $this->user       = $I->createUser($I, ['First Name' => $this->first_name, 'Email Address' => $this->email]);

        // create login logs
        $I->loginToQT($I, ['Email Address' => $this->email]);
        $I->logoutFromQT($I);

        $login_log_record_id = $this->get_login_log_record_id($I, $this->user);

        $I->loginAndVisitPageOfInterest($I, $scenario, $this->page_of_interest, $this->access_level);

        $I->fillField('SEARCH', $this->first_name);
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name, ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);

        $I->fillField('SEARCH', strtoupper($this->first_name));
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name, ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);

        $I->fillField('SEARCH', $this->email);
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name, ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);

        $I->fillField('SEARCH', strtoupper($this->email));
        $I->click('SUBMIT_SEARCH');
        $I->see($this->first_name, ['css' => 'tr#record-id-' . $login_log_record_id . ' td']);
    }
}
