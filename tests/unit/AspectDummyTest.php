<?php

namespace QTTest;

use AspectMock\Test as test;

require_once "auth/auth_functions.php";

/**
 * A canary test to ensure that AspectMock is correctly configued and we can
 * reprogram the functionality of predefined PHP functions and custom QT
 * functions.
 */

// class AspectDummyTest extends \PHPUnit\Framework\TestCase
class AspectDummyTest extends \Codeception\Test\Unit
{
    protected function _after()
    {
        test::clean(); // remove all registered test doubles
    }

    public function testPhpFunctionCanBeStubbed(): void
    {
        $proxy = test::func('QTTest', 'time', 'now');
        $this->assertEquals('now', time());
        // $proxy = test::func(null, 'time', 'now');
        // $this->assertEquals('now', time());
        $proxy->verifyInvoked();
    }

    public function testCustomFunctionCanBeStubbed(): void
    {
        // $proxy = test::func('QT', 'is_logged_in_user', '2050-05-01');
        $proxy = test::func('\QT', 'is_logged_in_user', function () {
            // \codecept_debug('in mock is_logged_in_user');
            return '2050-05-01';
        });
        $this->assertEquals('2050-05-01', \QT\is_logged_in_user(1));
        $proxy->verifyInvoked();
    }
}
