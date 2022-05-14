<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";

/**
 * Tests for user authorisation (login)
 */
class AuthCest
{
    public $email             = 'smokey@hotmail.com';
    public $locked_user_email = 'locked@example.com';
    public $password          = 'smokery123';
    public $user;
    public $locked_user;
    public $trial_length_days;

    public function _before($I, $scenario)
    {
        $config      = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings(
            'acceptance_phpbrowser',
            $config
        );
        // It would be nice to be able to grab this from qt.ini but I'm not
        // sure how, so we'll have to make do with test_config.yml
        $this->trial_length_days = $apiSettings['modules']['config']['App']['trial_length_days'];

        // Clears all emails
        $I->resetEmails();

        // create a test user who has already watched the intro video
        $this->user = $I->createUser(
            $I,
            [
                'Email Address' => $this->email,
                'Password'      => $this->password,
            ],
            [
                'intro_watched' => true
            ]
        );
    }

    public function _after($I, $scenario)
    {
        // Codeception can't remove changes the application makes to the database
        $user_ids   = [];
        $user_ids[] = $this->user['User ID'] ?? null;
        $user_ids[] = $this->locked_user['User ID'] ?? null;

        $I->clearLoginLogs($I, ['user_ids' => array_filter($user_ids)]);
    }

    public function loginWithGoodPassword(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($I->getApplicationPage('home'));
        $I->fillField('EMAIL_ADDRESS', $this->email);
        // here are other ways to fill in a field - if the page is well formed HTML
        // $I->fillField('form input[name=EMAIL_ADDRESS]', 'tester@example.com');
        // $I->fillField('EMAIL ADDRESS', 'tester@example.com');
        // $I->fillField('#email_address', 'tester@example.com');
        // $I->fillField('input#email_address', 'tester@example.com');
        // $I->fillField(['id' => 'email_address'], 'tester@example.com');
        // $I->fillField(['name' => 'EMAIL_ADDRESS'], 'tester@example.com');
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // UI correct
        $I->seeElement('#ok-button');

        $last_login_date = date("Y-m-d");

        // Users record has been updated
        $I->seeInDatabase('USERS', [
            'email address' => $this->email,
            'Fails Count'   => 0,
            'Login Count'   => 1
        ]);
    }

    public function redirectToLoginPageForPasswordProtectedPage(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage('/formulae/sura_formulae_analyse.php');
        $I->seeInCurrentUrl($I->getApplicationPage("login"));
        $I->see('Welcome to Qur’an Tools');
    }

    public function loginWithGoodPasswordAndGoToIntendedPage(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage('/formulae/formulaic_density_summary_table.php');
        $I->seeInCurrentUrl($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->email);
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // UI correct
        $I->see('Formulaic Density Summaries');

        // Users record has been updated
        $I->seeInDatabase('USERS', [
            'email address' => $this->email,
            'Fails Count'   => 0,
            'Login Count'   => 1
        ]);
    }

    public function loginWithBadEmail(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', "random-bogus-address@email.com");
        $I->fillField('PASSWORD', "wrong password");
        $I->click('#login-button');

        // UI correct
        $I->see('Sorry, your email address and/or password was not recognised.', '.error-message');
    }

    public function loginWithBadPassword(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->email);
        $I->fillField('PASSWORD', "wrong password");
        $I->click('#login-button');

        // UI correct
        $I->see('Sorry, your email address and/or password was not recognised.', '.error-message');

        // Users record has been updated
        $I->seeInDatabase('USERS', [
            'email address' => $this->email,
            'Fails Count'   => 1
        ]);
    }

    public function userReachesMaximumBadAttempts(AcceptancePhpbrowserTester $I, $scenario)
    {
        // put locked test user in database
        $num_fails         = MAXIMUM_PASSWORD_ATTEMPTS - 1;
        $this->locked_user = $I->createUser(
            $I,
            [
                'Email Address' => $this->locked_user_email,
                'Password'      => $this->password,
                'Fails Count'   => $num_fails,
                'Fail Time'     => time()
            ],
            [
                'intro_watched' => true
            ]
        );

        // ensure the createUser worked properly
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->locked_user_email,
                'Fails Count'   => $num_fails
            ]
        );

        // do the test
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->locked_user_email);
        $I->fillField('PASSWORD', "bad password");
        $I->click('#login-button');

        // $answer = $I->selectFromDatabase("SELECT * FROM `USERS` WHERE `email address` = '$to_be_locked_user_email'");
        // codecept_debug($answer);

        // UI is correct
        $I->see("your account has been locked for the next " . ACCOUNT_LOCK_TIME_MINUTES . " minutes");

        // the fails have been not been updated
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->locked_user_email,
                'Fails Count'   => $num_fails + 1
            ]
        );
    }

    public function lockedOutUserTryingTooSoon(AcceptancePhpbrowserTester $I, $scenario)
    {
        // put locked test user in database
        $num_fails         = 6;
        $this->locked_user = $I->createUser(
            $I,
            [
                'Email Address' => $this->locked_user_email,
                'Password'      => $this->password,
                'Fails Count'   => $num_fails,
                'Fail Time'     => time()
            ],
            [
                'intro_watched' => true
            ]
        );

        // ensure the createUser worked properly
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->locked_user_email,
                'Fails Count'   => 6
            ]
        );

        // do the test
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->locked_user_email);
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // $answer = $I->selectFromDatabase("SELECT * FROM `USERS` WHERE `email address` = '$locked_user_email'");
        // codecept_debug($answer);

        // UI is correct
        $I->see('your account is currently locked', '.error-message');
        // $I->see('locked for');

        // the fails count has been updated
        $I->seeInDatabase('USERS', [
            'email address' => $this->locked_user_email,
            'Fails Count'   => $num_fails + 1
        ]);
    }

    public function lockedOutUserTryingAfterWaiting(AcceptancePhpbrowserTester $I, $scenario)
    {
        // put locked test user in database who has waited for a while
        $minutes_waited    = 20;
        $this->locked_user = $I->createUser(
            $I,
            [
                'Email Address' => $this->locked_user_email,
                'Password'      => $this->password,
                'Fails Count'   => 6, 'Fail Time' => time() - $minutes_waited * 60
            ],
            [
                'intro_watched' => true
            ]
        );

        // sanity check to ensure the createUser worked properly
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->locked_user_email,
                'Fails Count'   => 6
            ]
        );

        // do the test
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->locked_user_email);
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // UI is correct
        $I->seeElement('#ok-button');

        // fail count has been reset
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->locked_user_email,
                'Fails Count'   => 0
            ]
        );
    }

    public function passwordResetByAdmin(AcceptancePhpbrowserTester $I, $scenario)
    {
        // put reset test user in database
        $I->updateInDatabase('USERS', ['Password Hash' => PASSWORD_RESET_TEXT], ['email address' => $this->email]);

        // sanity check to ensure the update worked properly
        $I->seeInDatabase('USERS', ['email address' => $this->email, 'Password Hash' => PASSWORD_RESET_TEXT]);

        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->email);
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // UI correct
        $I->see("Your password has been reset by an administrator and you must choose a new one", '.error-message');
    }

    public function blockedUserCannotLogin(AcceptancePhpbrowserTester $I, $scenario)
    {
        // put reset test user in database
        $I->updateInDatabase('USERS', ['Is Blocked' => true], ['email address' => $this->email]);

        // sanity check to ensure the createUser worked properly
        $I->seeInDatabase(
            'USERS',
            [
                'email address' => $this->email,
                'Is Blocked'    => true
            ]
        );

        // do the test
        $I->amOnPage($I->getApplicationPage("login"));
        $I->fillField('EMAIL_ADDRESS', $this->email);
        $I->fillField('PASSWORD', $this->password);
        $I->click('#login-button');

        // UI is correct
        $I->see("You have been blocked from using Qur`an Tools.");
    }
}
