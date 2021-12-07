<?php

/**
 * PHP Browser tests can extend from this class. Doing so:
 * - allows a more concise way to test the access rules of the page
 * - provides a more consistent tear-down processautomatically
 * - provides a simpler logon and visit page that detects the sort of user
 * - automatically tests some standard items that should be on on all pages
 */
class QTPageCest
{
    public $page_of_interest;
    public $access_level;
    public $email;
    public $extra_values;

    // stuff that needs to be done before all phpbrowser acceptance tests
    public function _before($I, $scenario)
    {
        $I->resetEmails();
        $missing_variables = null;
        if (!isset($this->page_of_interest))
        {
            $missing_variables[] = '$this->page_of_interest';
        }

        if (!isset($this->access_level))
        {
            $missing_variables[] = '$this->access_level';
        }

        if (!empty($missing_variables))
        {
            $I->fail(
                'The following variables have not been set in ' .
                    $scenario->current('name') .
                    ': ' .
                    implode(', ', $missing_variables) .
                    '. This is usually done in the _before method.'
            );
        }

        $this->email = 'user_' . rand(1, 10000) . '@example.com';
    }

    public function _after($I, $scenario)
    {
        // It would be much better to use test database that gets dropped for each test run
        $I->clearLoginLogs($I, ['emails' => [$this->email]]);
    }

    protected function accessRulesWork(
        AcceptancePhpbrowserTester $I,
        $scenario,
        $page_element = null
    ) {
        switch ($this->access_level) {
            // non-password protected pages
            case ACCESS_LEVEL_OPEN:
                $I->allowNotLoggedInUserAccessTo(
                    $I,
                    $scenario,
                    $this->page_of_interest,
                    $page_element
                );

                $I->allowNormalUserAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest,
                    $page_element
                );

                break;

            // normal user access
            case ACCESS_LEVEL_NORMAL:
                $I->redirectToLoginFor($I, $this->page_of_interest);

                $I->allowNormalUserAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest,
                    $page_element
                );

                break;

            // ADMIN level user access
            case ACCESS_LEVEL_ADMIN:
                $I->redirectToLoginFor($I, $this->page_of_interest);

                $I->preventNormalUserAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest
                );

                $I->allowAdminAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest,
                    $page_element
                );

                break;

            // SUPERUSER level user access
            case ACCESS_LEVEL_SUPERUSER:
                $I->redirectToLoginFor($I, $this->page_of_interest);

                $I->preventNormalUserAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest
                );

                $I->preventAdminAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest
                );

                $I->allowSuperUserAccessTo(
                    $I,
                    $this->extra_values,
                    $scenario,
                    $this->page_of_interest,
                    $page_element
                );

                break;

            default:
                // code...
                break;
        }
    }

    public function hasCssAndJavascriptAssets(
        \AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level
        );

        $I->seeInSource(
            '<link rel="stylesheet" type="text/css" href="/assets/qt_styles'
        );
        $I->seeInSource(
            '<script type="text/javascript" src="/assets/qt_javascript'
        );
    }

    public function hasStandardPageItems(
        \AcceptancePhpbrowserTester $I,
        $scenario
    ) {
        $I->loginAndVisitPageOfInterest(
            $I,
            $scenario,
            $this->page_of_interest,
            $this->access_level
        );

        // menu
        $I->seeElement('#qt-menu');
        // footer
        $I->seeElement('footer.qt-site-footer');
    }

    // Helper function
    protected function get_login_log_record_id(
        AcceptancePhpbrowserTester $I,
        array $user
    ): int {
        $sql                 = 'SELECT * FROM `LOGIN-LOGS` WHERE `User ID` = ' . $user['User ID'];
        $result              = $I->selectFromDatabase($sql);
        $login_log_record_id = $result[0]['Record ID'];
        return $login_log_record_id;
    }

    // Helper function
    protected function get_search_log_id(
        AcceptancePhpbrowserTester $I,
        array $user
    ): int {
        $sql = "SELECT * FROM `USAGE-VERSES-SEARCHES` WHERE `User ID` = '" .
            $this->user['User ID'] .
            "' ORDER BY 1 DESC";
        $result              = $I->selectFromDatabase($sql);
        $login_log_record_id = $result[0]['ID'];
        return $login_log_record_id;
    }
}
