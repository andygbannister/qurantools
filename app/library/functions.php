<?php

// load database functions

require_once 'database.php';

// load auth functions. Not just auth.php use these functions
require_once 'auth/auth_functions.php';

// define the viewing modes we use across the app
define('VIEWING_MODE_READ', 'viewing_mode_read');
define('VIEWING_MODE_INTERLINEAR', 'viewing_mode_interlinear');
define('VIEWING_MODE_PARSE', 'viewing_mode_parse');

/**
 * This function may eventually become obsolete as we roll out DataTables
 * across the app
 */

function print_page_navigator(
    $CURRENT_PAGE,
    $pages_needed,
    $show_buttons,
    $url_link
)
{
    // catch rounding errors
    $pages_needed = intval($pages_needed);

    // load users table and load preferences
    $page_navi_location = db_return_one_record_one_field(
        "SELECT `Preference Floating Page Navigator` 
           FROM `USERS` 
          WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'"
    );

    // how much should the >> and << buttons move by?
    if ($pages_needed > 40)
    {
        $move_big_amount = 10;
    }
    else
    {
        $move_big_amount = 5;
    }

    // where is this page navigator going? (1 = floating; 0 = fixed at page bottom)

    if ($page_navi_location == 1)
    {
        echo '<div id=pageNavigatorFloatingButton>';
    }
    else
    {
        echo '<div id=pageNavigatorFixed>';
    }

    echo 'Pages: ';

    $page_list = build_page_navigator(
        $CURRENT_PAGE,
        $pages_needed,
        $show_buttons,
        $move_big_amount,
        $page_navi_location
    );

    foreach ($page_list as $page)
    {
        $goto    = $page;
        $print   = $page;
        $spacing = ' &nbsp;';

        if ($page == '|<')
        {
            $goto    = 1;
            $print   = "<img src='\images\arrow-left-terminal.png' height=10 width=10>";
            $spacing = ' ';
        }
        if ($page == '>|')
        {
            $goto    = $pages_needed;
            $print   = "<img src='\images\arrow-right-terminal.png' height=10 width=10>";
            $spacing = ' ';
        }
        if ($page == '<')
        {
            $goto  = $CURRENT_PAGE - 1;
            $print = "<img src='\images\arrow-left-single.png' height=10 width=8>";
        }
        if ($page == '>')
        {
            $goto    = $CURRENT_PAGE + 1;
            $print   = "<img src='\images\arrow-right-single.png' height=10 width=8>";
            $spacing = ' ';
        }
        if ($page == '<<')
        {
            $goto    = $CURRENT_PAGE - $move_big_amount;
            $print   = "<img src='\images\arrow-left-double.png' height=10 width=10>";
            $spacing = ' ';
        }
        if ($page == '>>')
        {
            $goto    = $CURRENT_PAGE + $move_big_amount;
            $print   = "<img src='\images\arrow-right-double.png' height=10 width=10>";
            $spacing = ' ';
        }

        if ($page != $CURRENT_PAGE)
        {
            if (substr($url_link, 0, 8) == 'GoToPage')
            {
                echo "<a href='#' onClick='GoToPage($goto);' class=linky>"; // used by the search results list view, which needs (for now) a function called
            }
            else
            {
                echo "<a href='$url_link&PAGE=$goto' class=linky>";
            }

            echo "$print";
            echo "</a>$spacing";
        }
        else
        {
            echo "<b>$page</b>$spacing";
        }
    }

    echo '</div>';
}

function build_page_navigator(
    $current_page,
    $pages_needed,
    $show_buttons,
    $move_big_amount,
    $page_navi_location
)
{
    $pages_needed = intval($pages_needed);

    $page_item = [];

    $MAX_ITEMS = 7;

    // if the page navigator is at the bottom, we just show all pages
    if ($page_navi_location == 0)
    {
        $MAX_ITEMS    = $pages_needed;
        $show_buttons = false;
    }

    // add initial buttons

    if ($show_buttons)
    {
        if ($current_page > 1)
        {
            $page_item[] = '|<';
        }

        if ($current_page > $move_big_amount)
        {
            $page_item[] = '<<';
        }

        if ($current_page > 1)
        {
            $page_item[] = '<';
        }
    }

    if ($pages_needed <= $MAX_ITEMS)
    {
        for ($i = 1; $i <= $pages_needed; $i++)
        {
            $page_item[] = $i;
        }
    }
    else
    {
        // if more pages are needed than max items, then the fun begins

        // We will show 1 2 ... (2 numbers before current page) current page (2 numbers after) ...  (final - 1) (final)

        $numbers_shown_count = $MAX_ITEMS;

        // put in the 1 and 2 if needed (or more, if our number is right at the top of the range)

        for ($i = 1; $i <= 2 + 1 * ($current_page >= $pages_needed - 3); $i++)
        {
            if ($i < $current_page)
            {
                $page_item[] = $i;
                $numbers_shown_count--;
            }
        }

        if ($current_page > 4)
        {
            $page_item[] = '...';
        }

        // next, put in the two numbers before CURRENT and the two after

        for (
            $i = $current_page - 1 - 1 * ($current_page == $pages_needed);
            $i <= $current_page + 1 + 2 * ($current_page < 2);
            $i++
        )
        {
            if (($i > 2 || $i == $current_page) && $i <= $pages_needed)
            {
                $page_item[] = $i;
                $numbers_shown_count--;

                // for some reason 2 gets missed if current page is 1
                if (($current_page == 1) & ($i == 1))
                {
                    $page_item[] = '2';
                    $numbers_shown_count--;
                }
            }
        }

        // finally, put in the two final numbers

        if ($current_page < $pages_needed - 2)
        {
            $page_item[] = '...';
        }

        for (
            $i = $pages_needed - $numbers_shown_count + 1;
            $i <= $pages_needed;
            $i++
        )
        {
            if ($i > $current_page - $numbers_shown_count + 2)
            {
                $page_item[] = $i;
            }
        }
    }

    // add final buttons

    if ($show_buttons)
    {
        if ($current_page < $pages_needed)
        {
            $page_item[] = '>';
        }

        if ($current_page < $pages_needed - $move_big_amount)
        {
            $page_item[] = '>>';
        }

        if ($current_page < $pages_needed)
        {
            $page_item[] = '>|';
        }
    }

    return $page_item;
}
// TODO: this is a bit inefficient on pages with lots of tool tips resulting in
// multiple calls to the DB. Probably not that important as they are not
// expensive calls and MySQL may cache results in memory anyway.
function build_tooltip(string $name)
{
    // we lookup the details from the TOOLTIP-TEXT file and build a tooltip on that basis

    $tooltipResult = db_query(
        "SELECT * FROM `TOOLTIP-TEXT` WHERE `NAME`='" . db_quote($name) . "'"
    );

    if (db_rowcount($tooltipResult) > 0)
    {
        $ROW = db_return_row($tooltipResult);

        if ($ROW['TITLE TEXT'] != '')
        {
            return "class='yellow-tooltip' title=\"<b>" .
                $ROW['TITLE TEXT'] .
                '</b><br>' .
                $ROW['BODY TEXT'] .
                "\">";
        }
        else
        {
            return "class='yellow-tooltip' title=\"" .
                $ROW['BODY TEXT'] .
                "\">";
        }
    }
    else
    {
        return '>';
    }
}

function window_title($title)
{
    $GLOBALS['windowTitle'] = $title;

    if ($title != '')
    {
        $title = " | $title";
    }
    echo "<title>Qur’an Tools$title</title>";
}

// this saves the verse or search details to our logs
function log_verse_or_search_request($verse_or_search)
{
    global $config;

    // referrer is the page they CAME from

    $referrer = '';
    if (isset($_SERVER['HTTP_REFERER']))
    {
        $referrer = $_SERVER['HTTP_REFERER'];
    }

    $user = get_logged_in_user();

    // remove repetitive domain name stuff from the referrer for internal requests
    $referrer = str_ireplace($config['main_app_url'], '', $referrer);

    $sql = "INSERT INTO `USAGE-VERSES-SEARCHES` (
                `VERSES OR SEARCH`, 
                `LOOKED UP`, 
                `USER ID`, 
                `REFERRING PAGE`)
                VALUES (
                '$verse_or_search', 
                '" . db_quote($_GET[$verse_or_search]) . "', 
                " . db_quote($user['User ID']) . ", 
                '" . db_quote($referrer) . "')";

    db_query($sql);
}

/**
 * Generate random groups of codes separated by a hyphen for user entered passwords
 *
 * Use: $code = generate_random_code(3, 4);
 * @param integer $groups - number of code groups
 * @param string $group_length - length of each code group
 * @param string $characters - characters to choose password from
 *
 * @return string the random code
 */
function generate_random_code(
    $groups = 4,
    $group_length = 4,
    $characters = DIGITS_AND_UPPER_CASE_LETTERS
)
{
    $code = '';

    for ($i = 1; $i <= $groups; $i++)
    {
        if ($i > 1)
        {
            $code .= '-';
        }
        for ($j = 1; $j <= $group_length; $j++)
        {
            $r = rand(0, strlen($characters));
            if ($r == strlen($characters))
            {
                $r = 0;
            }
            $char = substr($characters, $r, 1);
            $code .= $char;
        }
    }
    return $code;
}

/**
 *  Generate reset password for new users
 *  @see generate_random_code
 */
function generate_reset_password_code(
    $groups = PASSWORD_RESET_GROUPS,
    $group_length = PASSWORD_RESET_GROUP_LENGTH,
    $characters = DIGITS_AND_ALL_LETTERS
)
{
    return generate_random_code($groups, $group_length, $characters);
}

function verses_in_sura($s)
{
    return db_return_one_record_one_field(
        "SELECT `Verses` 
           FROM `SURA-DATA` 
          WHERE `Sura Number` = " . db_quote($s)
    );
}

function intertextual_count($s, $v)
{
    return db_return_one_record_one_field(
        "SELECT `Intertextual Link Count` 
        	FROM `QURAN-FULL-PARSE` 
        	WHERE `SURA`= " . db_quote($s) . " and `VERSE` = " . db_quote($v)
    );
}

// todo: This is a lousy way to check for mobile phones - it would be much 
// better done with responsive media queries that are used (sparingly) in
// other parts of the site. But isMobile() is used in quite a few places
// in the app, so removing it would be a bit of work
function isMobile()
{
    $USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    if (stripos($USER_AGENT, 'iphone') > 0)
    {
        return true;
    }
    if (stripos($USER_AGENT, 'ipad') > 0)
    {
        return true;
    }
    return false;
}

function draw_tag_lozenge($tag_colour, $tag_lightness_value)
{
    echo "<span class='pill-tag tag-pill-tooltip' style='float:left; background-color:#$tag_colour;";

    if ($tag_lightness_value > 130)
    {
        echo 'color: black';
    }
}

function sura_name_arabic($s)
{
    $arabic = db_return_one_record_one_field(
        "SELECT `Arabic Name` 
           FROM `SURA-DATA` 
          WHERE `Sura Number` = " . db_quote($s)
    );
    return $arabic;
}

function sura_name_english($s)
{
    return db_return_one_record_one_field(
        "SELECT `English Name` 
           FROM `SURA-DATA`
          WHERE `Sura Number` = " . db_quote($s)
    );
}

function sura_provenance($s)
{
    return db_return_one_record_one_field(
        "SELECT `Provenance` 
           FROM `SURA-DATA`
          WHERE `Sura Number` = " . db_quote($s)
    );
}

function sura_durie_classification($s)
{
    return db_return_one_record_one_field(
        'SELECT `Durie Classification` FROM `SURA-DATA` WHERE `Sura Number`=' . db_quote($s)
    );
}

function sura_length_words($s)
{
    return db_return_one_record_one_field(
        "SELECT count(*) FROM `QURAN-DATA` 
          WHERE `SURA`    = " . db_quote($s) . ' AND `SEGMENT` = 1'
    );
}

function colour_or_hide_rasm_transliterated($text)
{
    // option to show only the rasm

    if ($_GET['RASM'] == 'Y')
    {
        // hide letters not part of the rasm
        $text = str_replace('a', '', $text);
        $text = str_replace('u', '', $text);
        $text = str_replace('ū', '', $text);
        $text = str_replace('i', '', $text);
        $text = str_replace('ī', '', $text);
        $text = str_replace('ā', '', $text);
    }

    // colour code the RASM
    // $regex = "/(a|i|u|ā|ì|ū)/";
    // $colour_code_replacement = '<font color=green>\\1</font>';
    // $text = preg_replace($regex, $colour_code_replacement, $text);

    return $text;
}

function colour_or_hide_rasm_arabic($text)
{
    // for future expansion
    return $text;

    if ($_GET['RASM'] == 'Y')
    {
        // hide letters not part of the rasm
        $text = str_replace('ْ', '', $text); // sukun
        $text = str_replace('َ', '', $text); // fatah
        $text = str_replace('ِ', '', $text); // kasra
        $text = str_replace('ُ', '', $text); // damma

        $text = str_replace('ّ', '', $text); // shaddah
        $text = str_replace('ٱ', 'ا', $text); // remove hamaza from alif
        $text = str_replace('إ', 'ا', $text); // remove hamaza below
        $text = str_replace('أ', 'ا', $text); // remove hamaza above
        $text = str_replace('ٰ', '', $text); // remove AlifKhanjareeya
        $text = str_replace('ٌ', '', $text); // remove Dammatan
        $text = str_replace('ۢ', '', $text); // remove SmallHighMeemIsolatedForm
        $text = str_replace('ً', '', $text); // remove kasratan
        $text = str_replace('ٓ', '', $text); // remove maddah
        $text = str_replace('ۦ', '', $text); // remove small ya
    }

    // colour code the RASM
    $regex                   = '/(َ|i|u|ā|ì|ū)/';
    $colour_code_replacement = "<span style='color:green; position:absolute;'>\\1</span>";
    $text                    = preg_replace($regex, $colour_code_replacement, $text);

    return $text;
}

function plural($p, $singular = '', $plural = 's')
{
    if ($p != 1)
    {
        return $plural;
    }
    return $singular;
}

function mysqli_return_one_record_one_field($query)
{
    $local = db_query($query);

    if (!$local || db_rowcount($local) == 0)
    {
        return '';
    }

    $ROW = db_return_row($local);
    return reset($ROW); // return the first item of the array
}

function convert_buckwalter($o)
{
    // convert things like ḥ to H

    $o = str_replace('H', 'ḥ', $o);
    $o = str_replace('S', 'ṣ', $o);
    $o = str_replace('D', 'ḍ', $o);
    $o = str_replace('Z', 'ẓ', $o);
    $o = str_replace('A', "'", $o);
    $o = str_replace('E', '‘', $o);
    $o = str_replace("$", 'sh', $o);
    $o = str_replace('T', 'ṭ', $o);
    $o = str_replace('*', 'dh', $o);
    return $o;
}

// Sets commonly used class selectors used in the charts based on the $_GET variable.
// For controls that are only used in one or two charts, the selectors are set in
// the actual chart_*.php file
function set_chart_control_selectors()
{
    if (isset($_GET['DEBUG']))
    {
        if ($_GET['DEBUG'] == 'Y')
        {
            echo '<br><br>';
            var_dump($_GET);
        }
    }

    // Provenance (sometimes the GET variable is 'SHOW', sometimes 'PROV')
    $prov_selector                     = '';
    $GLOBALS['meccan_suras_selected']  = '';
    $GLOBALS['medinan_suras_selected'] = '';
    $GLOBALS['all_suras_selected']     = '';
    if (isset($_GET['SHOW']))
    {
        $prov_selector = $_GET['SHOW'];
    }

    if (isset($_GET['PROV']))
    {
        $prov_selector = $_GET['PROV'];
    }

    switch ($prov_selector)
    {
        case 'MECCAN':
            $GLOBALS['meccan_suras_selected'] = 'selected';
            break;

        case 'MEDINAN':
            $GLOBALS['medinan_suras_selected'] = 'selected';
            break;

        default:
            $GLOBALS['all_suras_selected'] = 'selected';
            break;
    }

    // Sorting
    $GLOBALS['first_sort_option_selected'] = '';
    $GLOBALS['default_sort_selected']      = '';
    if (isset($_GET['SORT']))
    {
        switch ($_GET['SORT'])
        {
            case '1':
                $GLOBALS['first_sort_option_selected'] = 'selected';
                break;

            default:
                $GLOBALS['default_sort_selected'] = 'selected';
                break;
        }
    }
    else
    {
        $GLOBALS['default_sort_selected'] = 'selected';
    }

    // Count Occurrences
    // TODO: sometimes this is done with a PER100 variable (with values of "1" or ""), sometimes with a MODE variable (with 3 different possible values I think) and sometimes a COUNT variable (with values of "PER100" or "OCC"). This should be rationalised to just the one, and I suspect something like MODE_COUNT would be the best name.
    $mode_count_selector                           = '';
    $GLOBALS['occurrences_per_100_words_selected'] = '';
    $GLOBALS['all_occurrences_selected']           = '';
    if (isset($_GET['PER100']))
    {
        $mode_count_selector = $_GET['PER100'];
    }

    if (isset($_GET['COUNT']))
    {
        $mode_count_selector = $_GET['COUNT'];
    }

    switch ($mode_count_selector)
    {
        case '1':
        case 'PER100':
            $GLOBALS['occurrences_per_100_words_selected'] = 'selected';
            break;

        default:
            $GLOBALS['all_occurrences_selected'] = 'selected';
            break;
    }
}

function gloss_formula($formula, $formula_full_gloss)
{
    if ($formula_full_gloss == '')
    {
        $formula_bits = explode('+', $formula);

        $build_gloss = '';

        foreach ($formula_bits as $little_bit)
        {
            $build_gloss .= $build_gloss != '' ? ' + ' : '';

            $gloss = db_return_one_record_one_field(
                "SELECT `GLOSS` FROM `FORMULAIC-GLOSSES` WHERE `LEXEME`='" .
                    db_quote($little_bit) .
                    "'"
            );

            $build_gloss .= $gloss != '' ? $gloss : '???';
        }
    }
    else
    {
        $build_gloss = $formula_full_gloss;
    }

    return '<br><span class=smaller_text_for_mini_dialogs><font color=gray>' .
        htmlentities($build_gloss) .
        '</font></span><br>';
}

function return_translator_name($t_id)
{
    return db_return_one_record_one_field(
        'SELECT `TRANSLATION NAME`  FROM `TRANSLATION-LIST` WHERE `TRANSLATION ID` = ' . db_quote($t_id)
    );
}

function HTMLToRGB($htmlCode)
{
    if ($htmlCode[0] == '#')
    {
        $htmlCode = substr($htmlCode, 1);
    }

    if (strlen($htmlCode) == 3)
    {
        $htmlCode = $htmlCode[0] .
            $htmlCode[0] .
            $htmlCode[1] .
            $htmlCode[1] .
            $htmlCode[2] .
            $htmlCode[2];
    }

    $r = hexdec($htmlCode[0] . $htmlCode[1]);
    $g = hexdec($htmlCode[2] . $htmlCode[3]);
    $b = hexdec($htmlCode[4] . $htmlCode[5]);

    return $b + ($g << 0x8) + ($r << 0x10);
}

function RGBToHSL($RGB)
{
    $r = 0xff & ($RGB >> 0x10);
    $g = 0xff & ($RGB >> 0x8);
    $b = 0xff & $RGB;

    $r = ((float) $r) / 255.0;
    $g = ((float) $g) / 255.0;
    $b = ((float) $b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if ($maxC == $minC)
    {
        $s = 0;
        $h = 0;
    }
    else
    {
        if ($l < 0.5)
        {
            $s = ($maxC - $minC) / ($maxC + $minC);
        }
        else
        {
            $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
        }
        if ($r == $maxC)
        {
            $h = ($g - $b) / ($maxC - $minC);
        }
        if ($g == $maxC)
        {
            $h = 2.0 + ($b - $r) / ($maxC - $minC);
        }
        if ($b == $maxC)
        {
            $h = 4.0 + ($r - $g) / ($maxC - $minC);
        }

        $h = $h / 6.0;
    }

    $h = (int) round(255.0 * $h);
    $s = (int) round(255.0 * $s);
    $l = (int) round(255.0 * $l);

    return (object) ['hue' => $h, 'saturation' => $s, 'lightness' => $l];
}

function draw_tags_for_verse($reference)
{
    $result_tags = db_query(
        "SELECT `TAG ID`, `Tag Name`, `Tag Colour`, `Tag Lightness Value` FROM `TAGGED-VERSES` T1
LEFT JOIN `TAGS` T2 ON T2.`ID`=`TAG ID`  
WHERE `SURA-VERSE`='" .
            db_quote($reference) . "' AND T1.`User ID`=" . db_quote($_SESSION['UID']) . ' ORDER BY `Tag Name`'
    );

    if (db_rowcount($result_tags) > 0)
    {
        for ($tags = 0; $tags < db_rowcount($result_tags); $tags++)
        {
            echo $tags > 0 ? ' ' : '';
            $tag_data = db_return_row($result_tags);
            echo "<span class='pill-tag tag-pill-tooltip' style='background-color:#" .
                $tag_data['Tag Colour'] .
                ';';

            echo 'border: 1px solid #000000; padding-top: 2px; padding-bottom: 2px;';

            if ($tag_data['Tag Lightness Value'] > 130)
            {
                echo 'color: black';
            }

            echo "' data-tipped-options=\"ajax: {url:'/ajax/ajax_tag_hover.php', data:{T:" .
                $tag_data['TAG ID'] .
                ", V:'$reference'}}\"'>";
            echo str_replace(
                ' ',
                '&nbsp;',
                htmlentities($tag_data['Tag Name'])
            );
            echo '</span>';
        }
    }
}

/**
 * Format an IP range for HTML display.
 *
 * Use: echo format_ip_ranges('1.1.1.1-2.2.2.2');
 *
 * @param string $ip_ranges - the IP range to format
 * @param array $option     - display options
 *              max_lines   - maximum number of lines to display (-1 = show all)
 *
 * @return string formatted IP Ranges
 */
function format_ip_ranges(string $ip_ranges_string = null, $options = ['max_lines' => 2])
{
    if (empty($ip_ranges_string))
    {
        throw new \Exception('Missing $ip_ranges for format_ip_ranges()');
    }

    $result    = '';
    $ip_ranges = explode(',', $ip_ranges_string);

    foreach ($ip_ranges as $index => $ip_range)
    {
        if (!is_valid_ip_range($ip_range))
        {
            return "'$ip_ranges_string' is an invalid list of IP Ranges";
        }

        if (-1 == $options['max_lines'])
        {
            // show every IP address
            $result .= str_replace('-', ' - ', $ip_range);
            if ($index != sizeof($ip_ranges) - 1)
            {
                // Put a </br> at the end of every line except the last one
                $result .= '</br>';
            }
        }
        elseif ($index < $options['max_lines'])
        {
            $result .= str_replace('-', ' - ', $ip_range);

            if (
                $index < sizeof($ip_ranges) - 1 &&
                $index != $options['max_lines'] - 1
            )
            {
                $result .= '</br>';
            }
        }
        else
        {
            // add the icon
            $result .=
                "…<img src='/images/info.png' class='qt-icon long-ip-range' title='" .
                format_ip_ranges($ip_ranges_string, ['max_lines' => -1]) .
                "'>";
            break;
        }
    }
    return $result;
}

/**
 * Text for who is currently logged in
 *
 * Use: $text = profile_menu_user_text($user);
 *
 * @param array  $user        - row from USERS table
 *
 * @return string HTML markup for top My Profile menu item
 */
function profile_menu_user_text($user = null): string
{
    if (empty($user))
    {
        throw new \Exception('Missing $user for profile_menu_user_text()');
    }

    $login_name = !is_null($user['User Name'])
        ? $user['User Name']
        : $user['Email Address'];

    if (!empty($login_name))
    {
        $text = 'Logged in as <b>' . htmlspecialchars($login_name) . '</b>';
        return $text;
    }
}

/**
 * Get the paths of the static assets
 *
 * Use: <link rel="stylesheet" type="text/css" href="<?php echo get_asset_paths("assets.json")[qt_styles_path']; ?>" />
 *
 * @param string $manifest_file_path - Path of the (brunch generated) manifest
 *                                     file that contains asset path names
 * @return array                     - contains these keys: qt_styles_path, qt_javascript_path
 *
 * If the manifest file cannot be found or has not been generated, it returns
 * the un-fingerprinted file names - although this will not solve the problem
 * if fingerprint-brunch has already deleted these files.
 */
function get_asset_paths(string $manifest_file_path): array
{
    try
    {
        $asset_manifest      = file_get_contents($manifest_file_path);
        $asset_manifest_json = json_decode($asset_manifest);
        $qt_javascript_path  = $asset_manifest_json->{'qt_javascript.js'};
        $qt_styles_path      = $asset_manifest_json->{'qt_styles.css'};
    }
    catch (\Throwable $th)
    {
        // This will not work if fingerprint-brunch has deleted these files
        $qt_javascript_path = 'qt_javascript.js';
        $qt_styles_path     = 'qt_styles.css';
    }

    $result = [
        'qt_javascript_path' => $qt_javascript_path,
        'qt_styles_path'     => $qt_styles_path
    ];

    return $result;
}

/**
 * Shows the user name or that it wasn't set
 *
 * Use: echo show_value_or_missing('Bob the Builder', 'User Name');
 *
 * @param string $value - The value we are checking
 * @param string $label - The name of the value
 *
 * @return string           - Either the user name or
 *
 * If the manifest file cannot be found or has not been generated, it returns
 * the un-fingerprinted file names - although this will not solve problem if
 * fingerprint-brunch has already deleted these files.
 */
function show_value_or_missing(?string $value, ?string $label = ''): string
{
    return !empty($value) ? $value : ucfirst(trim("$label not supplied"));
}

/**
 * Concatenates first and last names
 *
 * Use: $full_name = generate_user_name('bob  ','builder  ')
 *
 * @param string $first_name - a first name
 * @param string $last_name - a last name
 *
 * @return string
 *
 * If the generated user name is an empty string, then return null
 */
function generate_user_name(?string $first_name = '', ?string $last_name = ''): ?string
{
    $user_name = trim(join(' ', [trim($first_name), trim($last_name)]));

    return empty($user_name) ? null : $user_name;
}

/**
 * Set $_SESSION variables related to a user
 *
 * Use: set_user_session_variables($user);
 *
 * @param array $user - row from USERS table
 *
 * @return void
 *
 * These session variables are here for legacy purposes. In most cases, it will
 * be better to refer to the $logged_in_user variable that is available in
 * every page that requires a log-in, however since lots of the code-base
 * refers (especially) to $_SESSION['UID'] it has not been removed.
 */
function set_user_session_variables(array $user): void
{
    $_SESSION['UID']           = $user['User ID'];
    $_SESSION['Email Address'] = $user['Email Address'];
    // TODO: Remove when User Name removed from USERS table
    $_SESSION['User Name']  = $user['User Name'];
    $_SESSION['First Name'] = $user['First Name'];
    $_SESSION['Last Name']  = $user['Last Name'];
    // This strtoupper is probably not required but legacy code had it, so to
    // prevent accidental breakage, it is included
    $_SESSION['administrator'] = strtoupper($user['Administrator']);
}

/**
 * Returns a list of keys that are missing or have empty values in an array
 *
 * Use:     if (!empty(get_missing_or_empty_keys($keys, $insert_data))) { ... }
 *
 *
 * @param array $needles  - array of keys we are looking to find
 * @param array $haystack - arrays we are looking to find the keys in
 *
 * @return array          - array of keys that were missing or had empty values in the $haystack
 *
 */
function get_missing_or_empty_keys(array $needles, array $haystack): array
{
    $missing_or_empty_keys = [];

    foreach ($needles as $key)
    {
        if (empty($haystack[$key]))
        {
            $missing_or_empty_keys[] = $key;
        }
    }

    return $missing_or_empty_keys;
}

/**
 * Builds the URL of where the Qur'an Tools privacy policy is
 */
function get_privacy_policy_url(): ?string
{
    global $config;

    $result = $config['privacy_policy_url'] ?? null;
    return $result;
}

/**
 * Builds the URL of where the Qur'an Tools cookie policy is
 */
function get_cookie_policy_url(): ?string
{
    global $config;

    $result = $config['cookie_policy_url'] ?? null;
    return $result;
}

/**
 * Builds the text for showing GDPR stuff
 */
function get_gdpr_registration_inner_html(
    bool $show_gdpr = false,
    string $gdpr_base_text = null,
    string $privacy_policy_url = null,
    string $cookie_policy_url = null

): ?string
{
    if ($show_gdpr && $gdpr_base_text)
    {
        $text = $gdpr_base_text;

        if ($privacy_policy_url)
        {
            $text .= '&nbsp;<a href="' . $privacy_policy_url . '" target="_blank">Privacy policy</a>.';
        }

        if ($cookie_policy_url)
        {
            $text .= '&nbsp;<a href="' . $cookie_policy_url . '" target="_blank">Cookie policy</a>.';
        }
    }

    return $text ?? null;
}

/**
 * Get the Google reCATPCHA site or secret key, depending on which reCAPTCHA mode we are using
 * TODO: this function is not currently being used
 */
function get_google_recaptcha_key(string $key_type): string
{
    global $config;

    $google_recaptcha_mode = get_google_recaptcha_mode();

    if (empty($config['google_recaptcha_' . $key_type . '_key_' . $google_recaptcha_mode]))
    {
        throw new Exception("Missing config value for 'recaptcha_" . $key_type . "_key_" . $config['google_recaptcha_mode'] . "' in qt.ini.");
    }

    return $config['google_recaptcha_' . $key_type . '_key_' . $google_recaptcha_mode];
}

/**
 * Get the Google reCATPCHA mode, default it to V3 if not set
 * TODO: this function is not currently being used
 */
function get_google_recaptcha_mode(): string
{
    global $config;

    return $config['google_recaptcha_mode'] ?? GOOGLE_RECAPTCHA_MODE_V3;
}

/**
 * is the current host machine a local, development machine?
 *
 * Use: if (is_running_locally()) { ... do something }
 *
 * Used for deciding whether to put google tag manager on a page
 */
function is_running_locally(): bool
{
    // var_dump($_SERVER);
    // codecept_debug($_SERVER);

    if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)
    {
        return true;
    }

    return (strpos($_SERVER['HTTP_HOST'], 'local') !== false);
}

/**
 * Should we show google tag manager snippet?
 *
 * Use: if (is_show_google_tag_manager($logged_in_user)) { ... do something }
 *
 * Only show tag manager stuff if
 *   a) not an admin user - since that skews usage results
 *   b) on a production server - since dev usage is not (usually) interesting
 *   c) there is actually a google tag manager code configured
 *
 * This code is run for every page view, but it's pretty low cost
 */
function is_show_google_tag_manager(?array $user): bool
{
    global $config;

    if (!is_admin_user($user) && !is_running_locally() && !empty($config['google_tag_manager_code']))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Determine whether user initiated sign-ups are allowed
 * 
 * Used on register.php and login.php
 */
function is_user_registration_allowed(): bool
{
    global $config;

    if (empty($config['is_user_registration_allowed']))
    {
        return false;
    }

    else
    {
        return filter_var($config['is_user_registration_allowed'], FILTER_VALIDATE_BOOL);
    }
}
