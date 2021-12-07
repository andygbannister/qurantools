<?php

// Very simple test to ensure the site and CodeCeption are working at the most basic level
class CanaryCest
{
    public function frontpageWorks(AcceptancePhpbrowserTester $I)
    {
        $I->amOnPage('/home.php');
        $I->see('Welcome to Qur’an Tools');
    }
}
