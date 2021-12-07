<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";

/**
 * Tests for the reset password functionality.
 * WebDriver tests since they require a call to Google Recaptcha
 * Unless you have Google Recaptcha set-up, these tests will fail
 * https://www.google.com/recaptcha/admin
 */

class ResetPasswordWDCest
{
    public $email;
    public $test_password = '12345678';
    public $user;

    public function _before($I)
    {
        // Clears all emails
        $I->resetEmails();

        $this->page_of_interest = $I->getApplicationPage("request_password_reset");

        // put test user (who has already watched the intro video) in database
        $this->user = $I->createUser(
            $I,
            [
                'Password' => $this->test_password
            ]
        );
        $this->email = $this->user['Email Address'];
    }

    /**
     * @group needs_email_server
     */
    public function requestResetPasswordForUnknownUser($I)
    {
        $unknown_email = 'unknowntester@example.com';
        $I->amOnPage($this->page_of_interest);
        $I->fillField('RESET_EMAIL_ADDRESS', $unknown_email);

        $I->click('Email me a password reset link');

        $I->waitForText("There is no known user with the email address " . $unknown_email);
    }

    /**
     * @group needs_email_server
     */
    public function requestResetPasswordForKnownUser($I)
    {
        $I->amOnPage($this->page_of_interest);
        $I->fillField('RESET_EMAIL_ADDRESS', $this->email);
        $I->click('Email me a password reset link');

        // client facing web-stuff

        $I->wait(1);
        $I->dontSee("Sorry, but something went wrong sending you a reset link.");
        $I->waitForText("An email has been sent to you with a password reset link");
        $I->waitForElement("#try-to-login-again");

        // email stuff
        $I->seeInLastEmailTo($this->email, "Dear " . $this->user['User Name']);

        // ensure we have a password reset link
        $reset_link = $I->grabFromDatabase('USERS', '`Reset Code`', ['email address' => $this->email]);
        $I->assertNotNull($reset_link);
    }
}
