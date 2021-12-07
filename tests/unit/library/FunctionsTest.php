<?php

// $this->markTestIncomplete('This test has not been implemented yet.');

require_once "library/functions.php";
require_once "tests/unit/EmailHelper.php";

/**
 * The following line used to be required when there were lots
 * of global vars in the app - which Codeception doesn't like
 * and the unit tests had to be run with something like:
 *
 * ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests/unit/
 *
 */
// class FunctionsTest extends \PHPUnit\Framework\TestCase
class FunctionsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public $user;
    public $user_id;
    public $email     = 'some_email@example.com';
    public $user_name = "Bob the Builder";
    public $password  = '12334566';

    protected function _before()
    {
    }

    protected function _after()
    {
        // remove any search artifacts
        isset($this->user_id)
            ? db_query(
                "DELETE FROM `USAGE-VERSES-SEARCHES` WHERE `USER ID`=" .
                    $this->user_id
            )
            : null;
    }

    // sura_provenance
    public function testSuraProvenanceMedinan(): void
    {
        $this->assertEquals(sura_provenance(2), "Medinan");
    }

    public function testSuraProvenanceMeccan(): void
    {
        $this->assertEquals(sura_provenance(1), "Meccan");
    }

    public function testSuraProvenanceBogusSura(): void
    {
        $this->assertEquals(sura_provenance(500), "");
    }

    // plural
    public function testPluralZero(): void
    {
        $this->assertEquals(plural(0), "s");
    }

    public function testPluralOne(): void
    {
        $this->assertEquals(plural(1), "");
    }

    public function testPluralTwo(): void
    {
        $this->assertEquals(plural(2), "s");
    }

    // random code generation
    public function testGenerateRandomCodeDefault(): void
    {
        // 4 groups of 4 characters with "-" between each group
        $this->assertEquals(strLen(generate_random_code()), 19);
    }

    public function testGenerateRandomCodeSixByThree(): void
    {
        // 6 groups of 3 characters with "-" between each group
        $this->assertEquals(strLen(generate_random_code(6, 3)), 23);
    }

    public function testGenerateRandomCodeTwoByTwoA(): void
    {
        // 2 groups of 2 characters chosen from "A"
        $this->assertEquals(generate_random_code(2, 2, "A"), "AA-AA");
    }

    // password reset code generation
    public function testGenerateResetPasswordDefault(): void
    {
        // 6 groups of 6 characters with "-" between each group
        $this->assertEquals(strLen(generate_reset_password_code()), 41);
    }

    // format_ip_ranges

    public function testFormat_ip_rangesThrowsWhenMissingIpRange(): void
    {
        $this->expectExceptionMessage(
            'Missing $ip_ranges for format_ip_ranges()'
        );

        format_ip_ranges();
    }

    // log_verse_or_search_request
    public function testLog_verse_or_search_requestForConsumer(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        // We set $this->user_id so that the _after test function deletes the row
        // about to be added into USAGE-VERSES-SEARCHES
        $this->user_id           = $this->user['User ID'];
        $verse_or_search         = 'V';
        $_GET[$verse_or_search]  = '2';
        $_SERVER["HTTP_REFERER"] = "/some_test_page.php";
        $_SESSION["UID"]         = $this->user_id;

        log_verse_or_search_request($verse_or_search);

        $usage_log = $this->tester->selectFromDatabase(
            "SELECT * FROM `USAGE-VERSES-SEARCHES` WHERE `User ID` =" .
                $this->user_id
        )[0];

        $this->assertEquals($usage_log['VERSES OR SEARCH'], $verse_or_search);
        $this->assertEquals($usage_log['LOOKED UP'], $_GET[$verse_or_search]);
        $this->assertEquals($usage_log['USER ID'], $this->user_id);
        $this->assertEquals(
            $usage_log['REFERRING PAGE'],
            $_SERVER["HTTP_REFERER"]
        );
    }

    // profile_menu_user_text

    public function testProfile_menu_user_textThrowsWhenMissingUser(): void
    {
        $this->expectExceptionMessage(
            'Missing $user for profile_menu_user_text()'
        );

        profile_menu_user_text();
    }

    public function testProfile_menu_user_textForConsumer(): void
    {
        $this->user = $this->tester->createUser($this->tester);

        $text = profile_menu_user_text($this->user);

        $this->assertEquals(
            "Logged in as <b>" . $this->user['User Name'] . "</b>",
            $text
        );

        $this->assertEquals(
            "Logged in as <b>" . $this->user['User Name'] . "</b>",
            $text
        );
    }

    // asset_paths
    public function testAsset_pathsReturnsDefaultsOnError(): void
    {
        $paths = get_asset_paths('missing_file.json');
        $this->assertEquals('qt_javascript.js', $paths['qt_javascript_path']);
        $this->assertEquals('qt_styles.css', $paths['qt_styles_path']);
    }

    public function testAsset_pathsReturnsPaths(): void
    {
        $paths = get_asset_paths("./tests/unit/library/test_assets.json");
        $this->assertEquals('qt_javascript-111.js', $paths['qt_javascript_path']);
        $this->assertEquals('qt_styles-111.css', $paths['qt_styles_path']);
    }

    // show_value_or_missing
    public function testShow_value_or_missingHandlesMissingValueMissingLabel(): void
    {
        $values = ['', null];
        $label  = null;

        foreach ($values as $value)
        {
            $result = show_value_or_missing($value, $label);
            $this->assertEquals('Not supplied', $result);
        }
    }

    public function testShow_value_or_missingHandlesMissingValuePresentLabel(): void
    {
        $value  = '';
        $label  = 'Name';
        $result = show_value_or_missing($value, $label);
        $this->assertEquals('Name not supplied', $result);
    }

    public function testShow_value_or_missingHandlesPresentValue(): void
    {
        $value  = 'Bob the Builder';
        $label  = 'Anything';
        $result = show_value_or_missing($value, $label);
        $this->assertEquals($value, $result);
    }

    // generate_user_name
    public function testGenerate_user_nameHandlesMissingNames(): void
    {
        foreach ([['', ''], [null, null]] as $name_pair)
        {
            $full_name = generate_user_name($name_pair[0], $name_pair[1]);
            $this->assertEquals("", $full_name);
        }
    }

    public function testGenerate_user_nameTrimsWhiteSpace(): void
    {
        $full_name = generate_user_name('    bobo   ', '  the clown   ');
        $this->assertEquals('bobo the clown', $full_name);
    }

    public function testGenerate_user_nameHandlesOneNameOnly(): void
    {
        $full_name = generate_user_name('Mary');
        $this->assertEquals('Mary', $full_name);
    }

    // set_user_session_variables
    public function testSet_user_session_variablesThrowsWhenMissingUser(): void
    {
        $this->expectExceptionMessage(
            'Argument 1 passed to set_user_session_variables() must be of the type array'
        );

        foreach (['', null] as $user)
        {
            set_user_session_variables($user);
        }
    }

    public function testSet_user_session_variablesSetsSessionVariables(): void
    {
        $user = $this->tester->createUser($this->tester);
        set_user_session_variables($user);

        $this->assertEquals($_SESSION['UID'], $user["User ID"]);
        $this->assertEquals($_SESSION['Email Address'], $user["Email Address"]);
        $this->assertEquals($_SESSION['User Name'], $user["User Name"]);
        $this->assertEquals($_SESSION['First Name'], $user["First Name"]);
        $this->assertEquals($_SESSION['Last Name'], $user["Last Name"]);
        $this->assertEquals($_SESSION['administrator'], $user["Administrator"]);
    }

    // get_missing_or_empty_keys
    public function testGet_missing_or_empty_keysThrowsWhenMissingNeedle(): void
    {
        $needles  = [[], '', null, [[], []]];
        $haystack = ['a', 'b'];

        $this->expectExceptionMessage(
            'Argument 1 passed to get_missing_or_empty_keys() must be of the type array'
        );

        foreach ($needles as $needle)
        {
            get_missing_or_empty_keys($needle, $haystack);
        }
    }

    public function testget_missing_or_empty_keysThrowsWhenMissingHaystack(): void
    {
        $needles   = ['a', 'b'];
        $haystacks = [[], '', null];

        $this->expectExceptionMessage(
            'Argument 2 passed to get_missing_or_empty_keys() must be of the type array'
        );

        foreach ($haystacks as $haystack)
        {
            get_missing_or_empty_keys($needles, $haystack);
        }
    }

    public function testget_missing_or_empty_keysWorks(): void
    {
        $needles  = ['key1', 'key2', 'key3'];
        $haystack = ['key1' => 1, 'key2' => null, 'key3' => ''];

        $result = get_missing_or_empty_keys($needles, $haystack);

        $this->assertEquals(['key2', 'key3'], $result);
    }

    // get_privacy_policy_url
    public function testGet_privacy_policy_urlWorks(): void
    {
        global $config;

        $result = get_privacy_policy_url();
        $this->assertEquals($config['privacy_policy_url'], $result);
    }

    public function testGet_privacy_policy_urlHandlesEmptyConfig(): void
    {
        global $config;

        unset($config['privacy_policy_url']);

        $result = get_privacy_policy_url();

        $this->assertEquals(null, $result);
    }

    // get_cookie_policy_url
    public function testGet_cookie_policy_urlWorks(): void
    {
        global $config;

        $result = get_cookie_policy_url();
        $this->assertEquals($config['cookie_policy_url'], $result);
    }

    public function testGet_cookie_policy_urlHandlesEmptyConfig(): void
    {
        global $config;

        unset($config['cookie_policy_url']);

        $result = get_cookie_policy_url();
        $this->assertEquals(null, $result);
    }

    // get_gdpr_registration_inner_html
    public function testGet_gdpr_registration_inner_htmlIfPrivacyPolicyUrlSet(): void
    {
        global $config;
        $config['privacy_policy_url'] = 'https://zoobie.com/privacy';

        $result = get_gdpr_registration_inner_html(true, $config['gdpr_base_text'], $config['privacy_policy_url']);

        $this->assertStringContainsString($config['privacy_policy_url'], $result);
    }

    public function testGet_gdpr_registration_inner_htmlIfPrivacyPolicyUrlNotSet(): void
    {
        global $config;

        $result = get_gdpr_registration_inner_html(true, $config['gdpr_base_text'], null, null);

        $this->assertStringNotContainsString($config['privacy_policy_url'], $result);
    }

    // get_recaptcha_key
    public function testGet_recaptcha_keyThrowsIfMissingV3KeysInConfig(): void
    {
        global $config;

        $config['google_recaptcha_mode']          = 'v3';
        $config['google_recaptcha_site_key_v3']   = null;
        $config['google_recaptcha_secret_key_v3'] = null;

        $this->expectExceptionMessage(
            "Missing config value for 'recaptcha_site_key_" . $config['google_recaptcha_mode'] . "' in qt.ini."
        );

        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SITE);

        $this->expectExceptionMessage(
            "Missing config value for 'recaptcha_secret_key_" . $config['google_recaptcha_mode'] . "' in qt.ini."
        );
        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SECRET);
    }

    public function testGet_recaptcha_keyThrowsIfMissingV2_tickKeysInConfig(): void
    {
        global $config;

        $config['google_recaptcha_mode']               = 'v2_tick';
        $config['google_recaptcha_site_key_v2_tick']   = null;
        $config['google_recaptcha_secret_key_v2_tick'] = null;

        $this->expectExceptionMessage(
            "Missing config value for 'recaptcha_site_key_" . $config['google_recaptcha_mode'] . "' in qt.ini."
        );

        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SITE);

        $this->expectExceptionMessage(
            "Missing config value for 'recaptcha_secret_key_" . $config['google_recaptcha_mode'] . "' in qt.ini."
        );
        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SECRET);
    }

    public function testGet_recaptcha_keyWorks(): void
    {
        global $config;

        $config['google_recaptcha_mode']               = 'v2_tick';
        $config['google_recaptcha_site_key_v2_tick']   = '123';
        $config['google_recaptcha_secret_key_v2_tick'] = 'abc';

        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SITE);
        $this->assertEquals('123', $result);

        $config['google_recaptcha_mode']          = 'v3';
        $config['google_recaptcha_site_key_v3']   = '123';
        $config['google_recaptcha_secret_key_v3'] = 'abc';

        $result = get_google_recaptcha_key(GOOGLE_RECAPTCHA_KEY_TYPE_SITE);
        $this->assertEquals('123', $result);
    }

    // get_google_recaptcha_mode

    public function testGet_google_recaptcha_modeDefaultsToV3MissingRecaptcha_modeInConfig(): void
    {
        global $config;

        $config['google_recaptcha_mode'] = null;

        $result = get_google_recaptcha_mode();

        $result = get_google_recaptcha_mode();
        $this->assertEquals(GOOGLE_RECAPTCHA_MODE_V3, $result);
    }

    public function testGet_google_recaptcha_modeWorks(): void
    {
        global $config;

        $config['google_recaptcha_mode'] = GOOGLE_RECAPTCHA_MODE_V2_TICK;

        $result = get_google_recaptcha_mode();
        $this->assertEquals(GOOGLE_RECAPTCHA_MODE_V2_TICK, $result);

        $config['google_recaptcha_mode'] = GOOGLE_RECAPTCHA_MODE_V3;

        $result = get_google_recaptcha_mode();
        $this->assertEquals(GOOGLE_RECAPTCHA_MODE_V3, $result);
    }

    // is_running_locally
    public function testIs_running_locallyIsTrueWhenLocal(): void
    {
        $hosts = ['127.0.0.1', 'host.local', 'localhost'];

        foreach ($hosts as $host)
        {
            $_SERVER["HTTP_HOST"] = $host;
            $this->assertTrue(is_running_locally(), 'host "' . $host . '" should be a local host, but is not');;
        }
    }

    public function testIs_running_locallyIsFalseWhenNotLocal(): void
    {
        $hosts = ['www.example.org', '', null];

        foreach ($hosts as $host)
        {
            $_SERVER["HTTP_HOST"] = $host;
            $this->assertFalse(is_running_locally(), 'host "' . $host . '" should not be a local host, but is');;
        }
    }

    public function testIs_running_locallyThrowsWhenEmptyHTTP_HOST(): void
    {
        $this->expectException(PHPUnit\Framework\Exception::class);
        $this->expectExceptionMessage('Undefined index: HTTP_HOST');

        $result = is_running_locally();
    }

    // is_show_google_tag_manager
    public function testIs_show_google_tag_managerIsTrueWhenNotLocalNotAdminCodeIsSet(): void
    {
        global $config;
        $config['google_tag_manager_code'] = 'GOOGLE_CODE';
        $_SERVER["HTTP_HOST"]              = 'www.qurantools.org';
        $this->user                        = $this->tester->createUser($this->tester);

        $this->assertTrue(is_show_google_tag_manager($this->user));
    }

    public function testIs_show_google_tag_managerIsTrueWhenNotLocalNoUserCodeIsSet(): void
    {
        global $config;
        $config['google_tag_manager_code'] = 'GOOGLE_CODE';
        $_SERVER["HTTP_HOST"]              = 'www.qurantools.org';
        $this->user                        = null;

        $this->assertTrue(is_show_google_tag_manager($this->user));
    }

    public function testIs_show_google_tag_managerIsFalseWhenLocalNotAdminCodeIsSet(): void
    {
        global $config;
        $config['google_tag_manager_code'] = 'GOOGLE_CODE';
        $_SERVER["HTTP_HOST"]              = 'localhost';
        $this->user                        = $this->tester->createUser($this->tester);

        $this->assertFalse(is_show_google_tag_manager($this->user));
    }

    public function testIs_show_google_tag_managerIsFalseWhenNotLocalIsAdminCodeIsSet(): void
    {
        global $config;
        $config['google_tag_manager_code'] = 'GOOGLE_CODE';
        $_SERVER["HTTP_HOST"]              = 'www.qurantools.org';
        $this->user                        = $this->tester->createAdminUser($this->tester);

        $this->assertFalse(is_show_google_tag_manager($this->user));
    }

    public function testIs_show_google_tag_managerIsFalseWhenNotLocalNotAdminCodeNotSet(): void
    {
        global $config;
        unset($config['google_tag_manager_code']);
        $_SERVER["HTTP_HOST"] = 'www.qurantools.org';
        $this->user           = $this->tester->createUser($this->tester);

        $this->assertFalse(is_show_google_tag_manager($this->user));
    }

    // is_user_registration_allowed
    public function testIs_user_registration_allowedFalseWhenConfigNotSet(): void
    {
        global $config;
        unset($config['is_user_registration_allowed']);

        $this->assertFalse(is_user_registration_allowed($this->user));
    }

    public function testIs_user_registration_allowedFalseWhenConfigSetToFalse(): void
    {
        global $config;
        $values = [false, 'false'];
        foreach ($values as $value)
        {
            $config['is_user_registration_allowed'] = $value;
            $this->assertFalse(is_user_registration_allowed());
        }
    }

    public function testIs_user_registration_allowedFalseWhenConfigSetToBogusValue(): void
    {
        global $config;
        $config['is_user_registration_allowed'] = 'zoop';

        $this->assertFalse(is_user_registration_allowed());
    }
    public function testIs_user_registration_allowedTrueWhenConfigSetToTrue(): void
    {
        global $config;

        $values = [true, 'true'];
        foreach ($values as $value)
        {
            $config['is_user_registration_allowed'] = $value;
            $this->assertTrue(is_user_registration_allowed());
        }
    }
}
