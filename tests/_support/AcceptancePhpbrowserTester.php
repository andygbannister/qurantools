<?php

/**
 * Inherited Methods
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
*/
class AcceptancePhpbrowserTester extends \Codeception\Actor
{
    use _generated\AcceptancePhpbrowserTesterActions;

    /**
     * Used for testing whether a not logged-in QT user can access a given page
     * Use:  $I-> allowNotLoggedInUserAccessTo($I, $scenario, '/about.php')
     *
     * @param object $I            - $I from the calling test
     * @param object $scenario     - $scenario from the calling test
     * @param string $page_path    - relative path of the page under test
     * @param string $page_element - (optional) an element that should appear on the page
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a not logged-in Qur'an Tools user is able to access the given page and the given element appears on tha page
     */
    public function allowNotLoggedInUserAccessTo(
        $I,
        $scenario,
        $page_path,
        $page_element = null
    ) {
        $I->amOnPage($page_path);
        if ($page_element)
        {
            $I->seeElement($page_element);
        }
        $I->seeInCurrentUrl($page_path);
    }

    /**
     * Used for testing whether a normal (logged-in) Qur'an Tools user cannot access a given page
     *
     * Use:  $I-> preventNormalUserAccessTo($I, $scenario, '/admin/some_page.php')
     *
     * @param object $I             - $I from the calling test
     * @param array $extra_values   - Extra values for logging in - usually just an email and /or password
     * @param object $scenario      - $scenario from the calling test
     * @param string $page_path     - relative path of the page under test
     * @param string $redirect_path - path of the page user is redirected to
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a normal Qur'an Tools user is unable to access the given page and redirected
     * to $redirect_path
     */
    public function preventNormalUserAccessTo(
        $I,
        $extra_values = [],
        $scenario,
        $page_path,
        $redirect_path = null
    ) {
        // If no redirect path specified, then default it to 404.
        $redirect_path = (is_null($redirect_path) ? $I->getApplicationPage('404') : $redirect_path);

        if (empty($extra_values))
        {
            $extra_values = [];
        }
        $I->loginToQT($I, $extra_values);
        $I->amOnPage($page_path);
        $I->seeInCurrentUrl($redirect_path);
        $I->logoutFromQT($I);
    }

    /**
     * Used for testing whether a normal (logged in) Qur'an Tools user can access a given page
     * Use:  $I-> allowNormalUserAccessTo($I, $scenario, '/home.php')
     *
     * @param object $I            - $I from the calling test
     * @param array $extra_values  - Extra values for logging in - usually just an email and /or password
     * @param object $scenario     - $scenario from the calling test
     * @param string $page_path    - relative path of the page under test
     * @param string $page_element - (optional) an element that should appear on the page
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a normal Qur'an Tools user is able to access the given page and the given element appears on tha page
     */
    public function allowNormalUserAccessTo(
        $I,
        $extra_values = [],
        $scenario,
        $page_path,
        $page_element = null
    ) {
        if (empty($extra_values))
        {
            $extra_values = [];
        }

        $I->loginToQT($I, $extra_values);
        $I->amOnPage($page_path);
        if ($page_element)
        {
            $I->seeElement($page_element);
        }
        $I->seeInCurrentUrl($page_path);
    }

    /**
     * Used for testing whether an admin user of Qur'an Tools cannot access a given page
     * Use:  $I-> preventAdminAccessTo($I, $scenario, '/admin/some_very_special_page.php')
     *
     * @param object $I             - $I from the calling test
     * @param array $extra_values   - Extra values for logging in - usually just an email and /or password
     * @param object $scenario      - $scenario from the calling test
     * @param string $page_path     - relative path of the page under test
     * @param string $redirect_path - path of the page user is redirected to
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a Qur'an Tools admin user is unable to access the given page - and redirected
     * to the 404 page instead
     */
    public function preventAdminAccessTo(
        $I,
        $extra_values = [],
        $scenario,
        $page_path,
        $redirect_path = null
    ) {
        // If no redirect path specified, then default it to 404.
        $redirect_path = (is_null($redirect_path) ? $I->getApplicationPage('404') : $redirect_path);

        if (empty($extra_values))
        {
            $extra_values = [];
        }

        $I->loginToQTAsAdmin($I, $extra_values);
        $I->amOnPage($page_path);
        $I->seeInCurrentUrl($redirect_path);
        $I->logoutFromQT($I);
    }

    /**
     * Used for testing whether a Qur'an Tools admin user can access a given page
     * Use:  $I-> allowAdminAccessTo($I, $scenario, '/admin/some_log_page.php')
     *
     * @param object $I            - $I from the calling test
     * @param array $extra_values  - Extra values for logging in - usually just an email and /or password
     * @param object $scenario     - $scenario from the calling test
     * @param string $page_path    - relative path of the page under test
     * @param string $page_element - (optional) an element that should appear on the page
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a Qur'an Tools admin user is able to access the given page and the given element appears on tha page
     */
    public function allowAdminAccessTo(
        $I,
        $extra_values = [],
        $scenario,
        $page_path,
        $page_element = null
    ) {
        if (empty($extra_values))
        {
            $extra_values = [];
        }

        $I->loginToQTAsAdmin($I, $extra_values);
        $I->amOnPage($page_path);
        $I->seeElement($page_element);
        $I->seeInCurrentUrl($page_path);
    }

    /**
     * Used for testing whether a Qur'an Tools super user can access a given page
     * Use:  $I-> allowSuperUserAccessTo($I, $scenario, '/some_very_special_page.php')
     *
     * @param object $I            - $I from the calling test
     * @param array  $user         - user we are testing access for
     * @param object $scenario     - $scenario from the calling test
     * @param string $page_path    - relative path of the page under test
     * @param string $page_element - (optional) an element that should appear on the page
     *
     * Function doesn't return anything, but will pass a codeception test if
     * a Qur'an Tools admin user is able to access the given page and the given element appears on tha page
     */
    public function allowSuperUserAccessTo(
        $I,
        $extra_values = [],
        $scenario,
        $page_path,
        $page_element
    ) {
        if (empty($extra_values))
        {
            $extra_values = [];
        }

        if (!array_key_exists('Email Address', $extra_values))
        {
            $extra_values['Email Address'] = 'superuser@example.com';
        }

        if (!array_key_exists('Password', $extra_values))
        {
            $extra_values['Password'] = '12345678';
        }

        $I->loginToQTAsSuperUser($I, $extra_values);
        $I->amOnPage($page_path);
        $I->seeElement($page_element);
        $I->seeInCurrentUrl($page_path);
    }
}
