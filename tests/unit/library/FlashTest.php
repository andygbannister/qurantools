<?php

namespace QT\Flash;

// $this->markTestIncomplete('This test has not been implemented yet.');

require_once "library/functions.php";

class FlashTest extends \Codeception\Test\Unit
{
    // set_flash
    public function testSet_flashThrowsWhenMissingMessage(): void
    {
        $this->expectExceptionMessage('Missing \'message\' option in set_flash()');

        set_flash([]);
    }

    public function testSet_flashSetsMessage(): void
    {
        $message = 'Well done';

        set_flash(['message' => $message]);

        $this->assertEquals($_SESSION['flash']['message'], $message);
    }

    public function testSet_flashSetsDefaultTypeToNotice(): void
    {
        $message = 'Well done';

        set_flash(['message' => $message]);

        $this->assertEquals($_SESSION['flash']['type'], 'notice');
    }

    // clear_flash
    public function testClear_flashClearsFlash(): void
    {
        $_SESSION['Flash'] = 'Something';

        clear_flash();

        $this->assertArrayNotHasKey('flash', $_SESSION);
    }

    public function testClear_flashDoesNotBreakIfAlreadyEmpty(): void
    {
        clear_flash();
    }

    // get_flash
    public function testGet_flashReturnsNullIfNotSet(): void
    {
        $flash = get_flash();

        $this->assertNull($flash);
    }

    public function testGet_flashReturnsFlashIfSet(): void
    {
        $message = 'Well done';
        set_flash(['message' => $message]);

        $flash = get_flash();

        $this->assertNotNull($flash);
        $this->assertEquals($message, $flash['message']);
    }
}
