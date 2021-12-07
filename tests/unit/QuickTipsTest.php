<?php

/**
 * Tests for the authorisation functions used in auth.php
 */

// $this->markTestIncomplete('This test has not been implemented yet.');

require_once 'library/quick_tips.php';

class QuickTipsTest extends \Codeception\Test\Unit
{
    public $user;
    public $quick_tip_id;

    public function _before()
    {
        $_SESSION = [];
    }

    public function _after()
    {
    }

    // get_current_quick_tip_id

    public function testGet_current_quick_tip_idThrowsWhenMissingUser(): void
    {
        $this->expectExceptionMessage(
            'Missing $user for get_current_quick_tip_id()'
        );

        get_current_quick_tip_id();
    }

    public function testGet_current_quick_tip_idReturnsQuickTipId(): void
    {
        $current_quick_tip_id = 3;
        $this->user           = $this->tester->createUser($this->tester, [
            'Current Quick Tip ID' => $current_quick_tip_id
        ]);

        $result = get_current_quick_tip_id($this->user);

        $this->assertEquals($current_quick_tip_id, $result);
    }

    // update_current_quick_tip

    public function testUpdate_current_quick_tipThrowsWhenMissingUser(): void
    {
        $this->expectExceptionMessage(
            'Argument 1 passed to update_current_quick_tip() must be of the type int, null given'
        );

        update_current_quick_tip($this->quick_tip_id, $this->user);
    }

    public function testUpdate_current_quick_tipThrowsWhenMissingQuickTipId(): void
    {
        $this->quick_tip_id = 123;

        $this->expectExceptionMessage(
            'Argument 2 passed to update_current_quick_tip() must be of the type array, null given'
        );

        update_current_quick_tip($this->quick_tip_id, $this->user);
    }

    public function testUpdate_current_quick_tipUpdatesDatabase(): void
    {
        $this->user       = $this->tester->createUser($this->tester);
        $new_quick_tip_id = 7;

        update_current_quick_tip($new_quick_tip_id, $this->user);

        $this->user = get_user_by_id($this->user['User ID']);
        $this->assertEquals(
            $new_quick_tip_id,
            $this->user['Current Quick Tip ID']
        );
    }

    // update_tip_preference

    public function testUpdate_tip_preferenceThrowsWhenMissingPreference(): void
    {
        $this->expectExceptionMessage(
            'Argument 1 passed to update_tip_preference() must be of the type bool, null given'
        );

        update_tip_preference(null);
    }

    public function testUpdate_tip_preferenceThrowsWhenMissingUser(): void
    {
        $this->expectExceptionMessage(
            'Argument 2 passed to update_tip_preference() must be of the type array, null given'
        );

        update_tip_preference(false, null);
    }

    public function testUpdate_tip_preferenceUpdatesDatabase(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        update_tip_preference(false, $this->user);

        $this->user = get_user_by_id($this->user['User ID']);

        $this->assertEquals(0, $this->user['Preference Show Quick Tips']);

        update_tip_preference(true, $this->user);

        $this->user = get_user_by_id($this->user['User ID']);
        $this->assertEquals(1, $this->user['Preference Show Quick Tips']);
    }
}
