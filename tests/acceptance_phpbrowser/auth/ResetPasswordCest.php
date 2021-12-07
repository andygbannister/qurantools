<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";

/**
 * Tests for the reset password functionality
 */

class ResetPasswordCest
{
    public $email;
    public $test_password = '12345678';
    public $user;

    public function _before(AcceptancePhpbrowserTester $I)
    {
        // Clears all emails
        $I->resetEmails();

        // put test user (who has already watched the intro video) in database
        $this->user = $I->createUser(
            $I,
            [
                'Password' => $this->test_password
            ]
        );
        $this->email = $this->user['Email Address'];
    }

    public function linkToResetPasswordOnLoginPage(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage('/home.php');
        $I->click('#reset-password-link');

        $I->see('Email me a password reset link');
    }

    // /**
    //  * @group needs_email_server
    //  */
    // public function requestResetPasswordForUnknownUser(AcceptancePhpbrowserTester $I)
    // {
    //     $unknown_email = 'unknowntester@example.com';
    //     $I->amOnPage('/auth/request_password_reset.php');
    //     $I->fillField('RESET_EMAIL_ADDRESS', $unknown_email);

    //     $I->click('Email me a password reset link');

    //     $I->see("There is no known user with the email address ".$unknown_email);
    // }

    // /**
    //  * @group needs_email_server
    //  */
    // public function requestResetPasswordForKnownUser(AcceptancePhpbrowserTester $I)
    // {
    //     $I->amOnPage('/auth/request_password_reset.php');
    //     $I->fillField('RESET_EMAIL_ADDRESS', $this->email);
    //     $I->click('Email me a password reset link');

    //     // client facing web-stuff
    //     $I->see("An email has been sent to you with a password reset link");
    //     $I->seeElement("#try-to-login-again");

    //     // email stuff
    //     $I->seeInLastEmailTo($this->email, "Dear ".$this->user['User Name']);

    //     // ensure we have a password reset link
    //     $reset_link = $I->grabFromDatabase('USERS', '`Reset Code`', ['email address' => $this->email]);
    //     $I->assertNotNull($reset_link);
    // }

    public function useValidResetPasswordLink(AcceptancePhpbrowserTester $I)
    {
        // generate password link
        $reset_code = $I->createResetPasswordCode($I, $this->email);
        $reset_link = "/auth/password_reset.php?R=$reset_code";

        // codecept_debug($reset_link);

        // now, use link
        $I->amOnPage($reset_link);

        $I->see('Qur’an Tools: Reset Your Password');
        $I->see('Please provide a new password');
    }

    public function useValidResetPasswordLinkTooLate(AcceptancePhpbrowserTester $I)
    {
        // generate password link
        $reset_code = $I->createResetPasswordCode($I, $this->email);
        $reset_link = "/auth/password_reset.php?R=$reset_code";
        $I->updateInDatabase(
            'USERS',
            ['Reset Timecode' => strtotime("-2 day")],
            ['email address'  => $this->email]
        );

        // codecept_debug($reset_link);

        // use password link
        $I->amOnPage($reset_link);

        $I->see('Qur’an Tools: Reset Your Password');
        $I->see('password reset code has elapsed');

        // ensure reset code has been deleted
        $I->seeInDatabase(
            'USERS',
            [
                'email address'  => $this->email,
                'Reset Code'     => '',
                'Reset Timecode' => null
            ]
        );
    }

    public function useValidResetPasswordLinkTwice(AcceptancePhpbrowserTester $I)
    {
        // generate password link
        $reset_code = $I->createResetPasswordCode($I, $this->email);
        $reset_link = "/auth/password_reset.php?R=$reset_code";

        // use the password link
        $I->amOnPage($reset_link);
        $I->fillField('PASSWORD1', $this->test_password);
        $I->fillField('CONFIRM_PASSWORD', $this->test_password);
        $I->click("CHANGE PASSWORD");

        $I->see('Your password was successfully changed.');

        // now, try to use it again
        $I->amOnPage($reset_link);
        $I->see("Bad password reset code!");
    }

    public function changePassword(AcceptancePhpbrowserTester $I)
    {
        // generate password link
        $reset_code = $I->createResetPasswordCode($I, $this->email);
        $reset_link = "/auth/password_reset.php?R=$reset_code";

        // use password link
        $I->amOnPage($reset_link);
        $I->fillField('PASSWORD1', $this->test_password);
        $I->fillField('CONFIRM_PASSWORD', $this->test_password);
        $I->click("CHANGE PASSWORD");

        // do the test
        $I->see('Your password was successfully changed.');

        // ensure reset code has been deleted
        $I->seeInDatabase(
            'USERS',
            [
                'email address'  => $this->email,
                'Reset Code'     => '',
                'Reset Timecode' => null
            ]
        );
    }

    public function useInvalidResetPasswordLink(AcceptancePhpbrowserTester $I)
    {
        $reset_link = "/auth/password_reset.php?R=some-bogus-code";

        // use password link
        $I->amOnPage($reset_link);

        $I->see('Qur’an Tools: Reset Your Password');
        $I->see('Bad password reset code');
    }
}
