<?php

/**
 * Tests for resetting passwords
 */

class ResetPasswordByAdminCest
{
    public $normal_user_email;
    public $normal_user_password = 'smokery123';
    public $normal_user;
    public $super_user_email;
    public $super_user_password = 'smokery123';
    public $super_user;

    public function _before(AcceptanceWebdriverTester $I)
    {
        $this->super_user_email = 'big-cheese' . rand(1, 100000) . '@hotmail.com';
        $this->normal_user      = $I->createUser(
            $I,
            [
                'Password' => $this->normal_user_password,
            ],
            [
                'intro_watched' => true
            ]
        );

        $this->super_user = $I->createSuperUser(
            $I,
            ['Email Address' => $this->super_user_email],
            ['intro_watched' => true]
        );

        // log-in as superuser
        $I->loginToQT($I, ['Email Address' => $this->super_user_email]);

        // navigate to page we want to test
        $I->amOnPage($I->getApplicationPage("user_management"));
    }

    public function _after(AcceptanceWebdriverTester $I)
    {
        // Codeception is clever enough to clear up its own junk, but can't get rid
        // of changes the application makes to the database
        db_query("DELETE FROM `LOGIN-LOGS` WHERE `User ID`=" . $this->normal_user['User ID']);
        db_query("DELETE FROM `LOGIN-LOGS` WHERE `User ID`=" . $this->super_user['User ID']);
    }

    public function superUserChangesPassword(AcceptanceWebdriverTester $I)
    {
        $I->fillField('#manage-users_filter input', $this->normal_user['Email Address']);

        $I->click("#edit-user-password-" . $this->normal_user['User ID']);

        // change password dialog displays
        $I->seeElement('#PASSWORD_PANEL');

        // update password
        $new_password = "florence-and-zebedee";

        // ensure user can log in with new password
        $I->fillField('PASSWORD1', $new_password);
        $I->fillField('CONFIRM_PASSWORD', $new_password);
        $I->click("CHANGE PASSWORD");

        $I->see("Password successfully changed for user '" . $this->normal_user['Email Address'] . "'");

        // and user can login with new password
        $I->logoffQT($I);
        $I->loginToQT($I, ['Email Address' => $this->normal_user['Email Address'], 'Password' => $new_password]);
        $I->seeElement('#ok-button');
    }

    public function superUserResetsPassword(AcceptanceWebdriverTester $I)
    {
        $I->fillField('#manage-users_filter input', $this->normal_user['Email Address']);

        $I->click("#reset-user-password-" . $this->normal_user['User ID']);

        // change password dialog displays
        $I->seeElement('#RESET_PASSWORD_PANEL');

        // reset password
        $I->click("PROCEED");

        $I->see("User " . $this->normal_user['User Name'] . " (" . $this->normal_user['Email Address'] . ") has had their password reset");

        // when user logs in ensure they are required to reset their password
        $I->logoffQT($I);
        $I->amOnPage('/home.php');
        $I->seeElement('#login-button');
        $I->fillField('EMAIL_ADDRESS', $this->normal_user['Email Address']);
        $I->fillField('PASSWORD', 'any old thing');
        $I->click('#login-button');
        $I->see('Your password has been reset by an administrator');
    }
}
