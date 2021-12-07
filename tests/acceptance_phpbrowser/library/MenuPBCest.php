<?php

// $scenario->skip('Not yet implemented');
// $I->fail('something bad happened');

require_once "library/hash.php";
require_once "auth/auth_functions.php";

use Codeception\Util\Locator;

/**
 * Tests for the menu
 */
class MenuPBCest
{
    public $user_password = 'smokery123';
    public $user;

    public function _before(AcceptancePhpbrowserTester $I)
    {
    }

    public function _after(AcceptancePhpbrowserTester $I)
    {
        // remove login artifacts
        db_query(
            "DELETE FROM `LOGIN-LOGS` WHERE `User ID`=" . $this->user['User ID']
        );
    }

    public function consumerUserCanSeePreferencesMenu(AcceptancePhpbrowserTester $I)
    {
        $this->user = $I->createUser($I, [
            'Password' => $this->user_password
        ]);
        $I->loginToQT($I, [
            'Email Address' => $this->user['Email Address'],
            'Password'      => $this->user_password
        ]);

        $I->seeElement(Locator::contains('a', 'Preferences'));
    }
}
