<?php

    include_once "library/functions.php";
    require_once "library/hash.php";

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 **/

class AcceptanceWebdriverTester extends \Codeception\Actor
{
    use _generated\AcceptanceWebdriverTesterActions;

    /**
     * Define custom actions here
     */

    public function logoffQT()
    {
        $I = $this;
        $I->moveMouseOver(['id' => 'my-profile-menu']);
        $I->click(['id' => 'logout']);
    }
}
