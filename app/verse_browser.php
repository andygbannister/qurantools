<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/verse_renderer.php';
require_once 'library/search_engine.php';
require_once 'library/transliterate.php';
require_once 'library/arabic.php';
require_once 'library/verse_parse.php';
require_once 'library/verse_simplifier.php';

$message       = '';
$message_class = 'message-warning';

// pass back info
if (isset($_GET['S']))
{
    $pass_back_info = '&S=' . urlencode($_GET['S']);
    $MODE           = 'SEARCH';
}
else
{
    $pass_back_info = '&V=' . urlencode($_GET['V']);
    $MODE           = 'VERSES';
}

// translation tag mode
$TRANSLATION_TAG_MODE = false;
if (isset($_GET['TTM']))
{
    if ($_GET['TTM'] == 'Y' && $_SESSION['administrator'])
    {
        $TRANSLATION_TAG_MODE = true;
    }
}

// load users table and load preferences
$result = db_query(
    "SELECT * FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'"
);

if (db_rowcount($result) > 0)
{
    $ROW                                              = db_return_row($result);
    $user_preference_highlight_colour                 = '#' . $ROW['Preferred Highlight Colour'];
    $user_preference_highlight_colour_lightness_value = $ROW['Preferred Highlight Colour Lightness Value'];

    $user_preference_cursor_colour = '#' . $ROW['Preferred Cursor Colour'];

    $user_preference_translation = $ROW['Preferred Translation'];

    $user_preference_turn_off_transliteration = $ROW['Preference Hide Transliteration'];

    $VERSES_PER_PAGE = $ROW['Preferred Verse Count'];
}
else
{
    $user_preference_highlight_colour                 = '#FFFF00';
    $user_preference_translation                      = 1;
    $user_preference_cursor_colour                    = '#DDDDDD';
    $user_preference_highlight_colour_lightness_value = 200;
    $VERSES_PER_PAGE                                  = $ROW['Preferred Verse Count'];
    $user_preference_turn_off_transliteration         = false;
}
?>

<!-- SET ?DEBUG=Y in URL to turn on debug mode -->

<html>

<head>

    <?php
    include 'library/standard_header.php';
    if (isset($_GET['V']))
    {
        window_title('Verses: Q.' . $_GET['V']);
    }
    else
    {
        window_title('Search: ' . $_GET['S']);
    }
    ?>
    <!-- define the highlight style for search hits (using the user preferences) -->

    <style>
        mark {
            background-color:
                <?php
                echo $user_preference_highlight_colour . ';';
                echo 'color: ';

                if ($user_preference_highlight_colour_lightness_value < 100)
                {
                    echo 'white';
                }
                else
                {
                    echo 'black';
                }
                ?>
        }
    </style>

    <script type="text/javascript" src="library/js/click_sura_picker_number.js"></script>

    <script>
        // remove a parameter from the URL

        function removeParam(key, sourceURL) {
            var rtn = sourceURL.split("?")[0],
                param,
                params_arr = [],
                queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
            if (queryString !== "") {
                params_arr = queryString.split("&");
                for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                    param = params_arr[i].split("=")[0];
                    if (param === key) {
                        params_arr.splice(i, 1);
                    }
                }
                rtn = rtn + "?" + params_arr.join("&");
            }
            return rtn;
        }

        function tagWordButton(globalWord, suraNumber, verseNumber) {
            fieldName = "translationTextField" + "-" + suraNumber + "-" + verseNumber;

            var cursorPos = $('#' + fieldName).prop('selectionStart');

            var cursorPos = $('#' + fieldName).prop('selectionStart');
            var v = $('#' + fieldName).val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);

            textToInsert = "<e" + globalWord + ">";

            var start = $('#' + fieldName).selectionStart;

            $('#' + fieldName).val(textBefore + textToInsert + textAfter);

            $('#' + fieldName).selectionStart = $('#' + fieldName).selectionEnd = start + textToInsert.length

            $('#' + fieldName).focus();

        }
    </script>


    <script>
        // used by the sura picker
        previouslyHighlightedSura = 0;

        // if they have arrived here using the back button, hide the analytics panel
        window.onpageshow = function(event) {
            if (event.persisted) {
                document.getElementById('ANALYTICS_PANEL').style.display = 'none'; // hide the analysis panel
            }
        };

        function close_all_toolbars() {

            $('#ANALYTICS_PANEL').hide();
            $('#FORMULAIC_PANEL').hide();
            $('#TAGS_PANEL').hide();
        }


        function toggle_formulaic_toolbar() {

            $('#FORMULAIC_PANEL').toggle();

            // hide other panels
            $('#ANALYTICS_PANEL').hide();
            $('#TAGS_PANEL').hide();

            // hide any tooltips that may be lurking over the button
            Tipped.hideAll();
        }

        function toggle_analysis_toolbar() {

            $('#ANALYTICS_PANEL').toggle();

            // hide other panels
            $('#FORMULAIC_PANEL').hide();
            $('#TAGS_PANEL').hide();

            // hide any tooltips that may be lurking over the button
            Tipped.hideAll();
        }

        function toggle_tags_toolbar() {

            $('#TAGS_PANEL').toggle();

            // hide other panels
            $('#FORMULAIC_PANEL').hide();
            $('#ANALYTICS_PANEL').hide();

            // hide any tooltips that may be lurking over the button
            Tipped.hideAll();
        }

        function con_on(text) {
            // hide all others  
            $('.' + 'verseTools').hide();

            // hide any open tips
            Tipped.hideAll();

            $('#' + text).show();
        }

        function con_off(text) {
            // $('#' + text).hide();
            // Tipped.hideAll();
        }

        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
                vars[key] = value;
            });
            return vars;
        }

        function changeTranslationSearch(data, search, page, additional_values) {
            url = "verse_browser.php?T=" + data + "&S=" + encodeURIComponent(search) + "&PAGE=" + page +
                additional_values;
            window.location.assign(url);
        }

        function ChangeTranslation_Verses(data, verse, page) {
            <?php if (isset($_GET['TTM']))
                {
                    echo "url = 'verse_browser.php?T=' + data + '&V=' + verse + '&PAGE=' + page + '&TTM=Y';";
                }
                else
                {
                    echo "url = 'verse_browser.php?T=' + data + '&V=' + verse + '&PAGE=' + page;";
                } ?>

            window.location.assign(url);
        }

        function show_bookmark() {
            var e = document.getElementById('BOOKMARK_FORM');

            if (e.style.display == 'block') {
                e.style.display = 'none';
            } else {
                e.style.display = 'block';
                document.getElementById("BKNAME").focus();
                document.getElementById('MESSAGE').style.display = 'none'; // hide the message bar
            }
        }


        <?php echo "$(document).ready(function() {"; ?>

        Tipped.delegate('.tag-pill-tooltip', {
            close: true,
            position: 'bottommiddle',
            showOn: 'click',
            showDelay: 0,
            hideOn: false,
            hideOthers: true,
            zIndex: 50000,
        });


        $('#qt-menu').mouseover(function(e) {

            // we have to do it this way to avoid a popup breaking the intertextuality popup
            $(".tpd-tooltip").hide();
            Tipped.create('.loupe-tooltip', {
                position: 'left',
                maxWidth: 300,
                skin: 'light'
            });
        });

        $('.copyVerseButton').click(function(e) {
            // copy button has been clicked — find out the ID of the button; that will
            // allow us to work out which hidden div to copy to the clipboard

            var verse_div_to_copy = String($(this).attr('id').split(' '));

            // load the contents of that div and then copy it to the clipboard

            clipboardField = document.getElementById("DIV-" + verse_div_to_copy);

            $('#DIV-' + verse_div_to_copy).show();

            var range = document.createRange();
            range.selectNodeContents(clipboardField);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            document.execCommand('copy');

            $('#DIV-' + verse_div_to_copy).hide();

            // format the verse for the dialog floater

            report_verse = verse_div_to_copy.slice(5);
            report_verse = report_verse.replace("-", ":");

            // and report this to the user

            $('#floating-message').html('Verse Q. ' + report_verse + ' copied to the clipboard');
            $('#floating-message').show();
            setTimeout(function() {
                $("#floating-message").hide();
            }, 1200);

        });

        $('.translationText').mouseover(function(e) {
            // get class name of this translated text element
            var class_names = $(this).attr('class').split(' ');

            // 	get name of transliterated word element and work out name of Arabic word element

            arabicWordID = class_names[0].substring(1);

            <?php
            echo "$('#a' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
            echo "$('#t' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
            echo "$('.e' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
            ?>

        });

        $('.translationText').mouseout(function(e) {
            // get class and ID name of this translated text element
            var class_names = $(this).attr('class').split(' ');

            var id_name = e.target.id;

            // 	get name of transliterated word element and work out name of Arabic word element

            arabicWordID = class_names[0].substring(1);

            <?php
            echo "$('#a' + arabicWordID).css('background-color', 'transparent');";
            echo "$('#t' + arabicWordID).css('background-color', 'transparent');";
            echo "$('.e' + arabicWordID).css('background-color', 'transparent');";
            ?>

        });


        $('.transliteratedWord').mouseover(function(e) {
            // 	get name of transliterated word element and work out name of Arabic word element
            transliteratedWordID = e.target.id;
            arabicWordID = transliteratedWordID.substring(1);

            <?php
            echo "$('#a' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";

            echo "$('#' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');";

            // only do this if the element exists
            echo "if ($('.e' + arabicWordID).length) { $('.e' + arabicWordID).css('background-color', '$user_preference_cursor_colour');}";
            ?>

        });

        $('.transliteratedWord').mouseout(function(e) {
            // 	get name of transliterated word element and work out name of Arabic word element
            transliteratedWordID = e.target.id;
            arabicWordID = transliteratedWordID.substring(1);

            $("#a" + arabicWordID).css('background-color', 'transparent');
            $("#" + transliteratedWordID).css('background-color', 'transparent');

            <?php // only do this if the element exists
            echo "if ($('.e' + arabicWordID).length) { $('.e' + arabicWordID).css('background-color', 'transparent');}"; ?>


        });

        $('.arabicWord').mouseover(function(e) {
            // 	get name of Arabic word element and work out name of Arabic word element
            arabicWordID = e.target.id;
            transliteratedWordID = arabicWordID.substring(1);

            <?php
            echo "$('#' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
            echo "$('#t' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');";

            // only do this if the element exists
            echo "if ($('.e' + transliteratedWordID).length) { $('.e' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');}";
            ?>

        });

        $('.arabicWord').mouseout(function(e) {
            // 	get name of Arabic word element and work out name of Arabic word element
            arabicWordID = e.target.id;
            transliteratedWordID = arabicWordID.substring(1);

            $("#" + arabicWordID).css('background-color', 'transparent');
            $("#t" + transliteratedWordID).css('background-color', 'transparent');

            <?php // only do this if the element exists
            echo "if ($('.e' + transliteratedWordID).length) { $('.e' + transliteratedWordID).css('background-color', 'transparent');}"; ?>

        });

        <?php echo '});'; ?>
    </script>

    <?php // substitute FORMPLUS for + [used to pass escaped values via Javascript from the charting function] (rough & ready error trap)

    if (isset($_GET['S']))
    {
        $_GET['S'] = str_ireplace('FORMPLUS', '+', $_GET['S']);
    } ?>

</head>

<?php
if ($MODE == 'SEARCH')
    {
        echo "<body class='qt-site'>";
    }
    else
    {
        echo "<body class='qt-site verses'>";
    }
echo "<main class='qt-site-content'>";

include 'library/back_to_top_button.php';

// SAVE MODE => IF SET, WE SAVE THE RESULT OF THIS SEARCH IN A TEMPORARY TABLE, THEN PASS
// TO ANOTHER WEB PAGE (FOR E.G. VERB COUNTING)

$SAVE_SEARCH_AS_VERSE_LIST = '';
if (isset($_GET['SAVE']))
{
    $SAVE_SEARCH_AS_VERSE_LIST = $_GET['SAVE'];
}

// convert their default choice of viewing mode to a constant
$viewing_modes_list = [
    VIEWING_MODE_READ,
    VIEWING_MODE_INTERLINEAR,
    VIEWING_MODE_PARSE
];

$viewing_mode = $viewing_modes_list[$logged_in_user['Preferred Default Mode']];

if (isset($_GET['VIEWING_MODE']))
{
    if ($_GET['VIEWING_MODE'] == 'Y')
    {
        $viewing_mode = VIEWING_MODE_PARSE;
    }
    else
    {
        $viewing_mode = $_GET['VIEWING_MODE'];
    }
}
else
{
    $_GET['VIEWING_MODE'] = $viewing_mode; // so we can later pass it in URLs without an error caused by it being null
}

// max results
$MAX_ARRAY_SIZE = 1000;

// HIGHLIGHT TYPE
$highlight_on_format  = '<MARK>';
$highlight_off_format = '</MARK>';

// BOLD A VERSE?

$BOLD = '';
if (isset($_GET['B']))
{
    $BOLD = $_GET['B'];
}

// these variables are used for the paging

$CURRENT_PAGE = 1;

// GET CURRENT PAGE

if (isset($_GET['PAGE']))
{
    $CURRENT_PAGE = $_GET['PAGE'];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET['PAGE'] = '';
}

// log this request

if ($CURRENT_PAGE == 1)
{
    if (isset($_GET['V']))
    {
        log_verse_or_search_request('V');
    }

    if (isset($_GET['S']) && $CURRENT_PAGE == 1)
    {
        log_verse_or_search_request('S');
    }
}

// GET TRANSLATOR

$TRANSLATOR = $user_preference_translation;

if (isset($_GET['T']))
{
    $TRANSLATOR = $_GET['T'];
}

if (
    $TRANSLATOR < 1 ||
    $TRANSLATOR >
        db_return_one_record_one_field(
            'SELECT COUNT(*) FROM `TRANSLATION-LIST`'
        )
) {
    $TRANSLATOR = 1;
}

function return_formula_type_name()
{
    global $FORMULA_TYPE;
    if ($FORMULA_TYPE == 'ROOT')
    {
        return 'root';
    }
    if ($FORMULA_TYPE == 'ROOT-ALL')
    {
        return 'root-plus-particle/pronoun';
    }
    if ($FORMULA_TYPE == 'LEMMA')
    {
        return 'lemmata';
    }
}

function reverse_buckwalter($o)
{
    // convert from translit back to Buckwalter
    // convert things like ḥ to H
    $o = str_ireplace('ḥ', 'H', $o);
    $o = str_ireplace('ṣ', 'S', $o);
    $o = str_ireplace('ḍ', 'D', $o);
    $o = str_ireplace('ẓ', 'Z', $o);
    $o = str_ireplace("'", 'A', $o);
    $o = str_ireplace('‘', 'E', $o);

    return $o;
}

// RASM option
if (!isset($_GET['RASM']))
{
    $_GET['RASM'] = '';
}

// WORD HIGHLIGHTING
$HIGHLIGHT_WORD_START = 0;
$HIGHLIGHT_WORD_END   = 0;

// ABILITY TO HIGHLIGHT JUST ONE WORD
if (isset($_GET['highlight_single_word']))
{
    $HIGHLIGHT_WORD_START = $_GET['highlight_single_word'];
    $HIGHLIGHT_WORD_END   = $_GET['highlight_single_word'];
}

// FORMULAE —> we can get the data from the POST or GET methods.

// GET METHOD

$FORMULA = 0;
if (isset($_GET['FORMULA']))
{
    $FORMULA = $_GET['FORMULA'];
}

$FORMULA_TYPE = 'ROOT';
if (isset($_GET['FORMULA_TYPE']) && $FORMULA > 0)
{
    $FORMULA_TYPE = $_GET['FORMULA_TYPE'];
}

// POST METHOD

if (isset($_POST['DO_FORMULA']))
{
    if ($_POST['DO_FORMULA'] == 'OK')
    {
        $FORMULA      = $_POST['LENGTH'];
        $FORMULA_TYPE = $_POST['TYPE'];
    }
}

// error checking
if ($FORMULA < 3 || $FORMULA > 5)
{
    $FORMULA = 0;
}
if (
    $FORMULA_TYPE != 'ROOT' &&
    $FORMULA_TYPE != 'ROOT-ALL' &&
    $FORMULA_TYPE != 'LEMMA'
) {
    $FORMULA_TYPE = 'ROOT';
}

// BOOKMARKS
$bookmarkSuccess = false;
if (isset($_POST['BOOKMARK_SAVE']))
{
    if ($_POST['BOOKMARK_SAVE'] != '')
    {
        if ($_POST['BOOKMARK_NAME'] != '')
        {
            // check we have enough space
            $count = db_rowcount(
                db_query(
                    "SELECT * FROM `BOOKMARKS` WHERE `User ID`='" .
                        db_quote($_SESSION['UID']) .
                        "'"
                )
            );
            if ($count >= 100)
            {
                $message = "You already have $count bookmarks created, which is the maximum number allowed. Please delete one or more, then try again.";
            }
            else
            {
                // check for duplicate

                $result = db_query(
                    "SELECT * FROM `BOOKMARKS` WHERE `User ID`='" .
                        db_quote($_SESSION['UID']) .
                        "' AND UPPER(`Name`)='" .
                        strtoupper(db_quote($_POST['BOOKMARK_NAME'])) .
                        "'"
                );

                if (db_rowcount($result) > 0)
                {
                    $message = "A bookmark called '" .
                        htmlentities($_POST['BOOKMARK_NAME']) .
                        "' already exists! Each bookmark requires a unique name.";
                }
                else
                {
                    // check the bookmark isn't numeric
                    if (is_numeric($_POST['BOOKMARK_NAME']))
                    {
                        $message = 'You cannot use a number as a bookmark. Please use letters, or a mix of letters and numbers.';
                    }
                    else
                    {
                        // check bookmark isn't a sura name
                        $check_sura_name = db_query(
                            "SELECT * FROM `SURA-DATA` WHERE UPPER(`English Name`)='" .
                                db_quote(strtoupper($_POST['BOOKMARK_NAME'])) .
                                "' OR UPPER(`Arabic Name`)='" .
                                db_quote(strtoupper($_POST['BOOKMARK_NAME'])) .
                                "' OR UPPER(`Alternative Name 1`)='" .
                                db_quote(strtoupper($_POST['BOOKMARK_NAME'])) .
                                "' OR UPPER(`Alternative Name 2`)='" .
                                db_quote(strtoupper($_POST['BOOKMARK_NAME'])) .
                                "'"
                        );

                        if (db_rowcount($check_sura_name) > 0)
                        {
                            $message = 'You cannot create a bookmark with the same name as a sura. Please try again.';
                        }
                        else
                        {
                            if (isset($_GET['V']))
                            {
                                $bookmark_what = $_GET['V'];
                            }
                            else
                            {
                                $bookmark_what = $_GET['S'];
                            }
                            db_query(
                                "INSERT INTO `BOOKMARKS` (`User ID`, `Name`, `Contents`) VALUES ('" .
                                    db_quote($_SESSION['UID']) .
                                    "', '" .
                                    db_quote($_POST['BOOKMARK_NAME']) .
                                    "', '" .
                                    db_quote($bookmark_what) .
                                    "')"
                            );
                            $message = "A bookmark called '" .
                                htmlentities($_POST['BOOKMARK_NAME']) .
                                "' was successfully created.";
                            $message_class   = 'message-success';
                            $bookmarkSuccess = true;
                        }
                    }
                }
            }
        }
    }
}

// menubar

include 'library/menu.php';

// floating message pane
echo "<div id='floating-message' class=floating-message-higher>Floating Message Goes Here</div>";

// freeze the verse info / navigator
echo "<div id='lower-status-bar'>";

// load either the search results or the verses asked for

if (isset($_GET['S']))
{
    $search_result = search($_GET['S'], false);
}
else
{
    $V = $_GET['V'];

    // remove whitespace

    $V = preg_replace('/\s+/', '', $V);

    // are they looking up an exact word (sura:verse:word)

    if (
        preg_match("/^\d+:\d+:\d+$/m", $V, $matches) === 1 &&
        $matches[0] == $V
    ) {
        $parts_of_word_reference = explode(':', $V);

        // find the global word number for the word they want
        $global_word_number = db_return_one_record_one_field(
            'SELECT `GLOBAL WORD NUMBER` FROM `QURAN-DATA` WHERE `SURA`=' .
                db_quote($parts_of_word_reference[0]) .
                ' AND `VERSE`=' .
                db_quote($parts_of_word_reference[1]) .
                ' AND `WORD`=' .
                db_quote($parts_of_word_reference[2])
        );

        $V = $parts_of_word_reference[0] . ':' . $parts_of_word_reference[1];

        $HIGHLIGHT_WORD_START = $global_word_number;
        $HIGHLIGHT_WORD_END   = $global_word_number;
    }

    // now we can parse $V (the verse list passed to us) and turn it into something useable in an SQL query

    $RANGE_SQL = '';
    parse_verses($V, true, 0);

    // trap error ($RANGE_SQL will be null if they have entered a bad selection of verses)
    if ($RANGE_SQL == '')
    {
        $RANGE_SQL = 0;
    }

    $RANGE_SQL_JUST_VERSE_LIST = $RANGE_SQL; // save RANGE_SQL before we mess with it

    $RANGE_SQL = 'SELECT `SURA-VERSE`, `SURA`, `VERSE` FROM `QURAN-FULL-PARSE` WHERE ' .
        str_replace(
            'OR',
            'UNION ALL SELECT `SURA-VERSE`, `SURA`, `VERSE` FROM `QURAN-FULL-PARSE` WHERE ',
            $RANGE_SQL
        );

    $search_result = db_query($RANGE_SQL);
}

// are we removing tags?

if (isset($_GET['REMOVE_TAG']))
{
    if ($_GET['REMOVE_TAG'] > 0)
    {
        // first, check this one of our tags

        if (
            db_return_one_record_one_field(
                'SELECT COUNT(*) FROM `TAGS` WHERE `ID`=' .
                    db_quote($_GET['REMOVE_TAG']) .
                    " AND `User ID`='" .
                    db_quote($_SESSION['UID']) .
                    "'"
            ) > 0
        ) {
            db_goto($search_result, 0);

            // loop through each selected record

            for ($i = 0; $i < db_rowcount($search_result); $i++)
            {
                $verse_data = db_return_row($search_result);

                db_query(
                    'DELETE FROM `TAGGED-VERSES` WHERE `TAG ID`=' .
                        db_quote($_GET['REMOVE_TAG']) .
                        " AND `SURA-VERSE`='" .
                        db_quote($verse_data['SURA-VERSE']) .
                        "' AND  `User ID`='" .
                        db_quote($_SESSION['UID']) .
                        "'"
                );
            }

            db_goto($search_result, 0);
        }
    }
}

// are we adding tags?

if (isset($_GET['ADD_TAG']))
{
    if ($_GET['ADD_TAG'] > 0)
    {
        // first, check this one of our tags

        if (
            db_return_one_record_one_field(
                'SELECT COUNT(*) FROM `TAGS` WHERE `ID`=' .
                    db_quote($_GET['ADD_TAG']) .
                    ' AND `User ID`=' .
                    db_quote($_SESSION['UID'])
            ) > 0
        ) {
            db_goto($search_result, 0);

            // loop through each selected record

            for ($i = 0; $i < db_rowcount($search_result); $i++)
            {
                $verse_data = db_return_row($search_result);

                db_query(
                    "INSERT IGNORE INTO `TAGGED-VERSES`
				(`TAG ID`, `SURA-VERSE`, `User ID`)
				VALUES
				(" .
                        db_quote($_GET['ADD_TAG']) .
                        ", '" .
                        db_quote($verse_data['SURA-VERSE']) .
                        "', " .
                        db_quote($_SESSION['UID']) .
                        ')'
                );
            }

            db_goto($search_result, 0);
        }
    }
}

// do we need to turn on formulaic highlighting? (Maybe they have searched for e.g. DENSITY>40@[LENGTH:4 TYPE:ROOT]

if ($FORMULA_SPECIFY_LENGTH > 0)
{
    $FORMULA      = $FORMULA_SPECIFY_LENGTH;
    $FORMULA_TYPE = $FORMULA_SPECIFY_TYPE;
}

// move to first row

echo '<table width=100% cellpadding=2 cellspacing=0>';

echo "  <tr class='show-search-params-and-hits'>";

echo '    <td>';

if (isset($_GET['S']))
{
    echo "      <a href='home.php?L=" . urlencode($_GET['S']) . "'class=linky>";
}
else
{
    echo "      <a href='home.php?L=" . urlencode($_GET['V']) . "'class=linky>";
}

// setup the pill shape and the tooltip
echo "        <span class='pill-button'> <span " . build_tooltip('EDIT VERSES');

if (isset($_GET['S']))
{
    if (strlen($_GET['S']) > 130)
    {
        echo htmlentities(substr($_GET['S'], 0, 130)) . ' ...';
    }
    else
    {
        echo htmlentities($_GET['S']);
    }
}
else
{
    if (strlen($_GET['V']) > 130)
    {
        echo htmlentities(substr($_GET['V'], 0, 130)) . ' ...';
    }
    else
    {
        echo htmlentities($_GET['V']);
    }
}
echo '      </span></span>';
echo '    </a>';

if ($MODE == 'SEARCH')
{
    echo "<div id='hit_counter_wrapper'>";

    echo '<span id=AjaxHitCount>';

    $totalHits = count($globalWordsToHighlight) +
        $countWordsOrPhrasesInTranslationHighlighted;

    $totalVerses = db_rowcount($search_result);

    if ($totalHits > 0)
    {
        echo "<a href='search_hits.php?S=" .
            urlencode($_GET['S']) .
            "' class=linky-olive>";
        echo '(' .
            number_format($totalHits) .
            ' hit' .
            plural($totalHits) .
            ' in ' .
            number_format($totalVerses) .
            ' verse' .
            plural($totalVerses) .
            ')';
        echo '</a>';
    }
    else
    {
        echo '(' .
            number_format($totalVerses) .
            ' verse' .
            plural($totalVerses) .
            ')';
    }

    echo '</span>';

    echo '</div>';
}

echo '    </td>';
echo '  </tr>'; // show-search-params-and-hits

echo "  <tr class='extra-controls'>";
echo '    <td>';
echo "      <div class='extra-controls-container'>";

echo "        <div class='button-group'>";
if ($viewing_mode == VIEWING_MODE_READ)
{
    // new drop down formulaic menu
    echo '<span ' . build_tooltip('FORMULAE');

    if ($viewing_mode != VIEWING_MODE_INTERLINEAR)
    {
        // don't show FORMULA button in Interlinear Mode
        echo "            <button id='formulae_button' onClick='toggle_formulaic_toolbar();'>";

        if ($FORMULA > 0)
        {
            echo '            <b>Formulae</b>';
        }
        else
        {
            echo '              Formulae';
        }
        echo '            </button>';
        echo '          </span>';
    }
}
echo '      </div>'; // button-group

// interlinear mode
if ($viewing_mode == VIEWING_MODE_INTERLINEAR)
{
    echo "        <div class='button-group'>";
    echo '<span ' . build_tooltip('READER MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&T=$TRANSLATOR&VIEWING_MODE=" .
        VIEWING_MODE_READ .
        "'>";
    echo '            <button>Reader Mode</button>';
    echo '          </a></span>';

    echo '            <button><b>Interlinear Mode</b></button>';

    echo '<span ' . build_tooltip('PARSE MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&VIEWING_MODE=" .
        VIEWING_MODE_PARSE .
        "&T=$TRANSLATOR'>";
    echo '				 	<button>Parse Mode</button>';
    echo '          </a></span>';

    if ($MODE == 'SEARCH')
    {
        echo "          <a href='search_hits.php?S=" .
            urlencode($_GET['S']) .
            "&MODE=CHART' class=linky>";
        echo "            <button><img src='images/st.gif'> Chart Hits</button>";
        echo '          </a>';
    }

    echo '	 		<span ' . build_tooltip('COPYREF');
    echo "           <button onClick='click_copy_verses_button();'><img src='images/copyrefs3.png'></button>";
    echo '  		</span>';
    echo '        </div>'; // button-group
}

// parse mode
if ($viewing_mode == VIEWING_MODE_PARSE)
{
    echo "        <div class='button-group'>";
    echo '<span ' . build_tooltip('READER MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&T=$TRANSLATOR&VIEWING_MODE=" .
        VIEWING_MODE_READ .
        "'>";
    echo '            <button>Reader Mode</button>';
    echo '          </a></span>';
    echo '<span ' . build_tooltip('INTERLINEAR MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&T=$TRANSLATOR&VIEWING_MODE=" .
        VIEWING_MODE_INTERLINEAR .
        "'>";
    echo '            <button>Interlinear Mode</button>';
    echo '          </a></span>';

    echo '          <button><b>Parse Mode</b></button>';

    if ($MODE == 'SEARCH')
    {
        echo "          <a href='search_hits.php?S=" .
            urlencode($_GET['S']) .
            "&MODE=CHART' class=linky>";
        echo "            <button><img src='images/st.gif'> Chart Hits</button>";
        echo '          </a>';
    }

    echo '	 		<span ' . build_tooltip('COPYREF');
    echo "           <button onClick='click_copy_verses_button();'><img src='images/copyrefs3.png'></button>";
    echo '  		</span>';
    echo '        </div>'; // button-group
}

// viewer mode
if ($viewing_mode == VIEWING_MODE_READ)
{
    echo "        <div class='button-group'>";
    echo '            <button><b>Reader Mode</b></button>';

    echo '<span ' . build_tooltip('INTERLINEAR MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&T=$TRANSLATOR&VIEWING_MODE=" .
        VIEWING_MODE_INTERLINEAR .
        "'>";
    echo '            <button>Interlinear Mode</button>';
    echo '          </a></span>';

    echo '<span ' . build_tooltip('PARSE MODE');
    echo "          <a href='verse_browser.php?" .
        $pass_back_info .
        "&PAGE=$CURRENT_PAGE&VIEWING_MODE=" .
        VIEWING_MODE_PARSE .
        "&T=$TRANSLATOR'>";
    echo '				 	<button>Parse Mode</button>';
    echo '          </a></span>';

    if ($MODE == 'SEARCH')
    {
        echo "          <a href='search_hits.php?S=" .
            urlencode($_GET['S']) .
            "&MODE=CHART' class=linky>";
        echo "            <button><img src='images/st.gif'> Chart Hits</button>";
        echo '          </a>';
    }

    echo '	 		<span ' . build_tooltip('COPYREF');
    echo "           <button onClick='click_copy_verses_button();'><img src='images/copyrefs3.png'></button>";
    echo '  		</span>';
    echo '        </div>'; // button-group
}

echo "        <div class='button-group'>";

// bookmarks button
echo '          <span ' . build_tooltip('BOOKMARK');
echo "            <button id='bookmark_button' onClick='show_bookmark();'>Bookmark</button>";
echo '          </span>';

// tags button
if (
    db_return_one_record_one_field(
        "SELECT COUNT(*) FROM `TAGS` WHERE `User ID`='" .
            db_quote($_SESSION['UID']) .
            "'"
    ) > 0
) {
    echo '          <span ' . build_tooltip('TAGS');
    echo "            <button id='tags_button' onClick='toggle_tags_toolbar();'>Tags</button>";
    echo '          </span>';
}

// analysis button
echo '          <span ' . build_tooltip('ANALYSE');
echo "            <button id='analyse_button' onClick='toggle_analysis_toolbar();'>Analyse</button>";
echo '          </span>';
echo '        </div>'; // button-group

echo '      </div>'; // extra-controls-container
echo '    </td>';
echo '  </tr>'; // extra-controls
echo '</table>';

// =========== FORMULAIC ANALYSIS PANEL =======================================

echo "<div id='FORMULAIC_PANEL' class='dialog-box'>";

echo '<h3>Formulaic Analysis</h3>';
echo '<hr>';

echo "<form id='formulaic_form' action='verse_browser.php?PAGE=$CURRENT_PAGE" .
    $pass_back_info .
    "&T=$TRANSLATOR&RASM=" .
    $_GET['RASM'] .
    "' style='display: inline;' method=post>";
echo "<table><tr><td style='text-align: right;'>";
echo "Show Formulae of Length:</td><td style='text-align: left;'>";
echo '<input type=radio name=LENGTH value=3 ';
if ($FORMULA == 3 || $FORMULA == 0)
{
    echo 'checked=checked';
}
echo '> 3</input>';
echo ' &nbsp;&nbsp;<input type=radio name=LENGTH value=4 ';
if ($FORMULA == 4)
{
    echo 'checked=checked';
}
echo '> 4</input>';
echo ' &nbsp;&nbsp;<input type=radio name=LENGTH value=5 ';
if ($FORMULA == 5)
{
    echo 'checked=checked';
}
echo '> 5</input>';

echo "</td><tr><td style='text-align: right;'>Formula Type:</td>";
echo '<td><select name=TYPE>';
echo '<option value=ROOT ';
if ($FORMULA_TYPE == 'ROOT')
{
    echo ' selected';
}
echo '>Roots</option>';

echo '<option value=ROOT-ALL';
if ($FORMULA_TYPE == 'ROOT-ALL')
{
    echo ' selected';
}
echo '>Roots Plus Particles/Pronouns</option>';

echo '<option value=LEMMA';
if ($FORMULA_TYPE == 'LEMMA')
{
    echo ' selected';
}
echo '>Lemmata</option>';
echo '</td></tr>';

echo '<tr><td colspan=2></td></tr>';

echo '<tr>';
echo '<td>';
if ($FORMULA > 0)
{
    echo '<button type=SUBMIT name=CLEAR value=Clear>Clear All Formulaic Highlighting</button>';
}
else
{
    echo '&nbsp;';
}
echo '</td>';

echo "<td style='text-align: right;'>";

echo "<button type=button name=CANCEL onClick='toggle_formulaic_toolbar();'>Cancel</button>";
echo '<button type=SUBMIT name=DO_FORMULA value=OK>OK</button>';

echo '</td>';

echo '</tr>';

echo '</table></form><hr>';

echo "<a href='formulae/selection_formulae_analyse.php?" .
    $pass_back_info .
    "&L=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE'><button style='width:380px;'>List & Cross Reference All Formulae in Selection</button></a>";

echo '</div>';

// ========== TAGS PANEL====================================

echo "<div id='TAGS_PANEL' class='dialog-box'>";

echo '<h3>Tags</h3>';
echo '<hr>';

$tag_list = db_query(
    "SELECT * FROM `TAGS` WHERE `User ID`='" .
        db_quote($_SESSION['UID']) .
        "' ORDER BY `Tag Name`"
);

if (db_rowcount($tag_list) < 0)
{
    echo "<p>You haven't yet created any tags</p>";

    echo "<p><a href='tag_manager.php' class=linky-light>Click here</a> to create some</p>";
}
else
{
    // inner div listing the tags (and will have scroll bars if the list is too long

    echo "<div class='tag-popup-panel'>";

    echo '<form id=TagDialogForm>';

    echo '<table>';

    for ($i = 0; $i < db_rowcount($tag_list); $i++)
    {
        $ROW = db_return_row($tag_list);

        echo '<tr>';

        echo '<td>';

        echo '<input type=radio NAME=tag_radio_group value=' . $ROW['ID'];

        if ($i == 0)
        {
            echo ' checked';
        }

        echo " onClick=\" $('#SelectedTag').val(" . $ROW['ID'] . ");\">";

        echo '</td>';

        echo '<td align=left>';

        draw_tag_lozenge($ROW['Tag Colour'], $ROW['Tag Lightness Value']);

        echo "' onClick=\"$('input:radio[name=tag_radio_group]').val(['" .
            $ROW['ID'] .
            "']);\">";

        echo htmlentities($ROW['Tag Name']) . '</span></td>';

        echo '</tr>';
    }

    echo '</table>';

    echo '</form>';

    echo '</div>';

    // to apply or remove all, we reload the page but append a get variable to the end of the request

    echo "<button onClick=\"window.location.href = removeParam('REMOVE_TAG', removeParam('ADD_TAG', window.location.href)) + '&ADD_TAG=' + $('input[name=tag_radio_group]:checked', '#TagDialogForm').val();\">Apply Tag To All These Verses</button>";

    echo "<button onClick=\"window.location.href = removeParam('REMOVE_TAG', removeParam('ADD_TAG', window.location.href)) + '&REMOVE_TAG=' + $('input[name=tag_radio_group]:checked', '#TagDialogForm').val();\">Remove Tag From All These Verses</button>";

    echo "<button onClick=\"close_all_toolbars();\">Cancel</button>";
}

echo '</div>';

// ==========ANALYTICS PANEL====================================

echo "<div id='ANALYTICS_PANEL' class='dialog-box'>";

echo '<h3>Analysis Tools</h3>';
echo '<hr>';

if ($MODE == 'SEARCH')
{
    echo "<form id=formulaic_form action='search_hits.php?" .
        $pass_back_info .
        "' style='display: inline;' method=post>";
    echo "<button type=SUBMIT name=ANALYSE_HITS value=OK class='analytics-button'>Count or Chart Search Hits</button>";
    echo '</form><br>';
}

echo "<form id=formulaic_form action='selection_analyse.php?" .
    $pass_back_info .
    "' style='display: inline;' method=post>";
echo "<button type=SUBMIT name=ANALYSE_ROOTS value=OK class='analytics-button'>Analyse Words in These Verses</button>";
echo '</form><br>';

echo "<form id=formulaic_form action='selection_counts.php?=" .
    $pass_back_info .
    "' style='display: inline;' method=post>";
echo "<button type=SUBMIT name=ANALYSE_ROOTS value=OK class='analytics-button'>Count Words and Letters in These Verses</button>";
echo '</form>';

echo "<form id=formulaic_form action='selection_lengths.php?=" .
    $pass_back_info .
    "' style='display: inline;' method=post>";
echo "<button type=SUBMIT name=ANALYSE_LENGTHS value=OK class='analytics-button'>Analyse Sura Lengths in This Selection</button>";
echo '</form>';

echo '<hr>';
echo "<button onClick='toggle_analysis_toolbar();'>Cancel</button>";

echo '</div>';

// =============================================================

// formulaic density (entire selection)
if ($FORMULA > 0)
{
    // strip off the bit of the master search we need
    $sql_for_formulae = substr(
        $master_search_sql,
        40,
        strlen($master_search_sql)
    );
    $sql_for_formulae = substr(
        $sql_for_formulae,
        0,
        stripos($sql_for_formulae, 'ORDER BY') - 1
    );

    if ($MODE == 'VERSES')
    {
        $sql_for_formulae = " WHERE $RANGE_SQL_JUST_VERSE_LIST";
    }

    if ($FORMULA_TYPE == 'ROOT')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-ROOT`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    if ($FORMULA_TYPE == 'ROOT-ALL')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-ROOT-OR-PARTICLE`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    if ($FORMULA_TYPE == 'LEMMA')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-LEMMA`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    $roots = db_return_one_record_one_field($formulaicSQL);

    if ($FORMULA_TYPE == 'ROOT')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-ROOT-FLAGGED-$FORMULA-ROOT-FORMULAE`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    if ($FORMULA_TYPE == 'ROOT-ALL')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-$FORMULA-ROOT-ALL-FORMULAE`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    if ($FORMULA_TYPE == 'LEMMA')
    {
        $formulaicSQL = "SELECT SUM(`COUNT-QTL-LEMMA-FLAGGED-$FORMULA-LEMMA-FORMULAE`) FROM `QURAN-FULL-PARSE` qtable $sql_for_formulae";
    }

    $flagged = db_return_one_record_one_field($formulaicSQL);

    echo "<table width=100% border=0 cellpadding='5' cellspacing=0 bgcolor='#f0f0ff'><tr><td align=center>";
    echo "<span id=OVERALL_FORMUALAIC_DENSITY'><a style='text-decoration: none;' href='verse_browser.php?" .
        $pass_back_info .
        "&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE&SAVE=LISTFORMULAE'><font size=-1 color=black>Highlighting <b>" .
        return_formula_type_name() .
        "</b> formulae of length <b>$FORMULA</b>. Formulaic density of all your selected verses: ";
    if ($roots > 0)
    {
        echo '<b>' .
            number_format(($flagged * 100) / $roots, 2) .
            '%</b></font></span>';
    }
    else
    {
        echo '0.00%</font></span>';
    }
    echo '</a></td></tr></table>';
}

echo '</div>'; // closes the "header freeze" div

// BOOKMARK SAVER FORM

// for some reason this won't work in the css file
echo "<div ID='BOOKMARK_FORM' class='dialog-box'>";

echo "<h2 class='page-title-text'>Create Bookmark</h2>";

echo "<div class='form' id='formGETKEY'>";

echo "<form ID=formGETKEY method=POST action='verse_browser.php?PAGE=$CURRENT_PAGE&VIEWING_MODE=" .
    $viewing_mode .
    $pass_back_info .
    "&T=$TRANSLATOR&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE'>";

echo "<p class='bigger-message'>You may save this range of verses as a bookmark, simply by giving it a name below.</p>";

echo "<input type='text' ID='BKNAME' name='BOOKMARK_NAME' size=50 autocomplete=off autofocus maxlength=50 placeholder='New Bookmark Name'>";

echo '<button name=BOOKMARK_SAVE type=submit value=OK>CREATE BOOKMARK</button>';

echo "<button name=BOOKMARK_RENAME_CANCEL ID=cancelButton type=submit value='Cancel'>CANCEL</button>";

echo '</form>';

echo '</div>';

echo '</div>';

// spacing needed because the top status bar is now fixed

if ($FORMULA > 0)
{
    echo "<div class='verses-and-search-buttons-search-spacer formula-summary-spacer'></div>";
}
else
{
    echo "<div class='verses-and-search-buttons-search-spacer'></div>";
}

// message
if ($message != '')
{
    echo "<div class='message $message_class' id='MESSAGE'>$message</div>";
}

// check for errors
if (db_rowcount($search_result) == 0)
{
    echo '<div align=center>';

    if (isset($_GET['S']))
    {
        if (strtolower($_GET['S']) == "english:\"spiny norman\"")
        {
            echo "<img src='images/qt_spiny.png' style='margin-top:10px;' class='qt-big-logo-header'>";
        }
        else
        {
            if (
                strtolower($_GET['S']) == "english:\"dr pickles\"" ||
                strtolower($_GET['S']) == "\"dr pickles\""
            ) {
                echo "<img src='images/drpickles.jpg' style='margin-top:10px;'>";
            }
            else
            {
                echo "<img src='images/logos/qt_logo_only.png' style='margin-top:-10px;' class='qt-big-logo-header'>";
            }
        }

        // fill out a failed searches record (unless we've flagged not to)

        if (!isset($_GET['NO_LOG_FAILED']))
        {
            db_query(
                "INSERT INTO `FAILED-SEARCHES` (`USER ID`, `SEARCH`) VALUES ('" .
                    db_quote($_SESSION['UID']) .
                    "', '" .
                    db_quote($_GET['S']) .
                    "')"
            );
        }

        echo '<h3>Your search returned no results or matches.</h3>';

        echo "<hr style='width: 500px'>";

        echo '<p><b>Would you like to:</b></p>';

        echo "<div style='text-align: left; width:500px;'>";

        echo '<ul>';

        echo "<li><p><a href='home.php?L=" .
            htmlentities($_GET['S']) .
            "' class='linky'>Edit your search and try again</a></p></li>";
        echo "<li><p><a href='home.php' class='linky'>Start again with a fresh search</p></a></li>";
        echo "<li><p><a href='/help/advanced-searching.php' target='_blank' class='linky'>Read the Help pages for how to search using Qur&rsquo;an Tools</a></p></li>";

        echo '</ul>';

        echo '</div>';

        echo '</div>'; // lower-status-bar
    }
    else
    {
        echo "<img src='images/logos/qt_logo_only.png' style='margin-top:-40px' class='qt-big-logo-header'>";

        echo "<h3 style='font-family: helvetica'>Whoops! There an appears to be an error with your verse selection.</h3>";

        echo "<hr style='width: 640px'>";

        echo '<p><b>Would you like to:</b></p>';

        echo "<div style='text-align: left; width:500px;'>";

        echo '<ul>';

        echo "<li><p><a href='home.php?L=" .
            htmlentities($_GET['V']) .
            "' class=linky-light>Edit what you typed and try again</a></p></li>";
        echo "<li><p><a href='home.php' class=linky-light>Start again and lookup a fresh selection of verses</p></a></li>";
        echo "<li><p><a href='docs/user_guide_intro_lookup_verse.php' class=linky-light>Read the Help pages on how to lookup verses</a></p></li>";
        echo '<li><p>Or simply click a sura and then a verse below to look it up</p></li>';

        echo '</ul>';
        echo '</div>';

        echo "<div id='verse-picker-container'>";

        include 'library/sura_picker.php';

        echo '</div>';

        // attempt to remove this lookup from your history, as it's pointless

        $sql = "DELETE FROM `HISTORY` WHERE `User ID`='" .
            db_quote($_SESSION['UID']) .
            "' AND `History Item`='" .
            db_quote($_GET['V']) .
            "'";

        db_query($sql);
    }

    include 'library/footer.php'; ?>

<!-- Hide elements of the interface that don't apply given at this point -->

<script type="text/javascript">
    $("#formulae_button").hide();
    $("#bookmark_button").hide();
    $("#analyse_button").hide();
    $(".extra-controls").hide();
    $("#hit_counter_wrapper").hide();

    Tipped.create('.simple-tooltip', {
        position: 'bottommiddle',
        close: true,
        showDelay: 500
    });
    Tipped.create('.yellow-tooltip', {
        position: 'bottommiddle',
        maxWidth: 420,
        skin: 'lightyellow',
        showDelay: 1000,
        size: 'large'
    });
</script>

<?php exit();
}

// run through verses

$previous_sura = 0;

// formulaic cue number reset
$formula_cue_number_count = 0;

$start = $VERSES_PER_PAGE * ($CURRENT_PAGE - 1);
$end   = $start + $VERSES_PER_PAGE - 1;
if ($end >= db_rowcount($search_result))
{
    $end = db_rowcount($search_result) - 1;
}

// move record pointer
$search_result->data_seek($start);

// create the appropriate div

if (isset($_GET['S']))
{
    echo "<div id='search-results'>";
}
else
{
    echo "<div id='verse-results'>";
}

for ($i = $start; $i <= $end; $i++)
{
    // grab next database row
    $ROW = db_return_row($search_result);

    $SURA  = $ROW['SURA'];
    $VERSE = $ROW['VERSE'];

    if ($SURA != $previous_sura)
    {
        if ($previous_sura != 0)
        {
            echo '</table><hr>';
        }
        $previous_sura = $SURA;

        echo "<div class='text-reference'>";

        // previous sura button
        if ($SURA > 1)
        {
            echo '<span ' . build_tooltip('PREVIOUS SURA');
            echo "<a href='verse_browser.php?V=" .
                ($SURA - 1) .
                "&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE'><img src='images/arrow-left-single.gif' height=8 width=8></a></span> ";
        }

        echo "<span class=sura-name>Surah $SURA <i>" .
            sura_name_arabic($SURA) .
            '</i> (' .
            sura_name_english($SURA) .
            ')</span>';

        // update the sura access statistics

        db_query(
            "INSERT INTO `STATS-SURAS` (`SURA`, `USER ID`, `ACCESS DATE`) VALUES ('" .
                db_quote($SURA) .
                "', '" .
                db_quote($_SESSION['UID']) .
                "', '" .
                db_quote(date('Y/m/d')) .
                "')"
        );

        // next sura button
        if ($SURA < 114)
        {
            echo '<span ' . build_tooltip('NEXT SURA');
            echo " <a href='verse_browser.php?V=" .
                ($SURA + 1) .
                "&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE'><img src='images/arrow-right-single.gif' height=8 width=8></a></span>";
        }

        // print the sura context button
        echo '<span ' . build_tooltip('WHOLE SURA');
        echo " <a href='verse_browser.php?V=$SURA&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE'><img style='vertical-align: -10%;' src='images/context_v.gif'></a>";
        echo '</span>';

        // PRINT FORMULAIC DENSITY FOR THIS SURA
        if ($FORMULA > 0)
        {
            if ($FORMULA_TYPE == 'ROOT')
            {
                $roots = db_rowcount(
                    db_query(
                        "SELECT `QTL-ROOT` FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );

                $flagged = db_rowcount(
                    db_query(
                        "SELECT `QTL-ROOT` FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `FORMULA-$FORMULA-ROOT` > 0 AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );
            }

            if ($FORMULA_TYPE == 'LEMMA')
            {
                $roots = db_rowcount(
                    db_query(
                        "SELECT `QTL-LEMMA` FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );

                $flagged = db_rowcount(
                    db_query(
                        "SELECT `QTL-LEMMA` FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `FORMULA-$FORMULA-LEMMA` > 0 AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );
            }

            if ($FORMULA_TYPE == 'ROOT-ALL')
            {
                $roots = db_rowcount(
                    db_query(
                        "SELECT `ROOT OR PARTICLE` FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );

                $flagged = db_rowcount(
                    db_query(
                        "SELECT `ROOT OR PARTICLE` FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA-ROOT-ALL` > 0 AND `SURA`='" .
                            db_quote($SURA) .
                            "'"
                    )
                );
            }

            echo "<span title='Formulaic density for the entire of sura $SURA (based on a formula of length $FORMULA)'><a href='formulae/formulaic_density_by_verse.php?SURA=$SURA&L=$FORMULA&TYPE=$FORMULA_TYPE' style='text-decoration:none;'><font size=-2 color=#8080ff><br>Sura $SURA Formulaic Density: ";

            if ($roots > 0)
            {
                echo number_format(($flagged * 100) / $roots, 2) .
                    '%</font></span>';
            }
            else
            {
                echo '0.00%</font></span>';
            }
        }
        echo '  </a>';
        echo '</div>';

        $border = 0;
        if ($viewing_mode == VIEWING_MODE_PARSE)
        {
            $border = 1;
        }

        echo "<table class='verseBrowserTable " .
            ($viewing_mode == VIEWING_MODE_INTERLINEAR ? 'nohover' : '') .
            " verseBrowserMinWidth' border=$border cellpadding=2>";

        if ($i == $start && $viewing_mode != VIEWING_MODE_INTERLINEAR)
        {
            echo '<tr><td align=left><font color=gray>Ref</font></td><td align=right><font color=gray>Arabic&nbsp;&nbsp;&nbsp;&nbsp;</gray></td>';

            if (!$user_preference_turn_off_transliteration)
            {
                echo '<td align=';
                if ($_GET['VIEWING_MODE'] == VIEWING_MODE_PARSE)
                {
                    echo 'center';
                }
                else
                {
                    echo 'left';
                }
                echo '>';

                echo '<font color=gray>Transliteration';

                if ($_GET['VIEWING_MODE'] == VIEWING_MODE_PARSE)
                {
                    echo ' / Gloss';
                }

                echo '</gray></td>';
            }

            if ($viewing_mode != VIEWING_MODE_INTERLINEAR)
            {
                echo '<td>';

                if ($viewing_mode == VIEWING_MODE_PARSE)
                {
                    echo '</td><td align=center>';
                }

                echo '<span class=gray-text>Translation';

                if ($viewing_mode == VIEWING_MODE_READ)
                {
                    // if we are only showing one translation because they specifically searched it (e.g. "YUSUFALI:book"), just show its name
                    // rather than the translation pick list
                    if (count($translatorsNeeded) == 1)
                    {
                        reset($translatorsNeeded); // make sure we are looking at the start of the array
                        echo ' (' . current($translatorsNeeded) . ')';
                    }

                    if (count($translatorsNeeded) == 0)
                    {
                        // show the translation picker if they haven't specified the translation(s) in the search

                        if ($MODE == 'SEARCH')
                        {
                            echo " <select ID=translation onChange='changeTranslationSearch(this.value, \"" .
                                htmlspecialchars(
                                    addslashes($_GET['S']),
                                    ENT_QUOTES
                                ) .
                                "\", \"" .
                                $_GET['PAGE'] .
                                "\", \"" .
                                "&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE" .
                                "\");'>";
                        }
                        else
                        {
                            echo " <select ID=translation onChange='ChangeTranslation_Verses(this.value, \"" .
                                $_GET['V'] .
                                "&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE" .
                                "\", \"" .
                                $_GET['PAGE'] .
                                "\");'>";
                        }

                        // populate the translation pick list using the TRANSLATION TABLE

                        $result_translation = db_query(
                            'SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`'
                        );

                        for (
                            $j = 0;
                            $j < db_rowcount($result_translation);
                            $j++
                        ) {
                            $ROW_TRANSLATION = db_return_row(
                                $result_translation
                            );

                            echo '<option value=' .
                                $ROW_TRANSLATION['TRANSLATION ID'];

                            if (
                                $TRANSLATOR ==
                                $ROW_TRANSLATION['TRANSLATION ID']
                            ) {
                                echo ' selected';
                            }
                            echo '>' .
                                $ROW_TRANSLATION['TRANSLATION NAME'] .
                                '</option>';
                        }

                        echo '</select></font>';
                    }

                    echo '</span>';
                }
                echo '</td>';
            }
            echo '</tr>';
        }
    }

    if ($viewing_mode == VIEWING_MODE_PARSE)
    {
        if ($MODE == 'SEARCH')
        {
            render_verse_parse_mode_new(
                $SURA,
                $VERSE,
                $logged_in_user['Preference Italics Transliteration'] == 1,
                0,
                0,
                $globalWordsToHighlight,
                $globalTranslationPhrasesToHighlight
            );
        }
        else
        {
            render_verse_parse_mode_new(
                $SURA,
                $VERSE,
                $logged_in_user['Preference Italics Transliteration'] == 1,
                $HIGHLIGHT_WORD_START,
                $HIGHLIGHT_WORD_END,
                [],
                []
            );
        }
    }
    else
    {
        // if the search engine hasn't returned a translator choice, use the default
        if (count($translatorsNeeded) == 0)
        {
            $translator_name = db_return_one_record_one_field(
                'SELECT `TRANSLATION NAME` FROM `TRANSLATION-LIST` WHERE `TRANSLATION ID`=' .
                    db_quote($TRANSLATOR)
            );

            if ($translator_name == '')
            {
                $translator_name = 'Arberry';
            }

            $translatorsNeeded[$translator_name] = $translator_name;
        }

        if ($MODE == 'SEARCH')
        {
            render_verse_new(
                $SURA,
                $VERSE,
                $translatorsNeeded,
                '',
                false,
                0,
                0,
                $logged_in_user['Preference Italics Transliteration'] == 1,
                $FORMULA,
                $FORMULA_TYPE,
                false,
                $globalWordsToHighlight,
                false,
                $globalTranslationPhrasesToHighlight,
                $globalExactPhrasesToHighlight
            );
        }
        else
        {
            render_verse_new(
                $SURA,
                $VERSE,
                $translatorsNeeded,
                $BOLD,
                false,
                $HIGHLIGHT_WORD_START,
                $HIGHLIGHT_WORD_END,
                $logged_in_user['Preference Italics Transliteration'] == 1,
                $FORMULA,
                $FORMULA_TYPE,
                false,
                [],
                $TRANSLATION_TAG_MODE,
                [],
                []
            );
        }
    }
}
echo '</div>';

echo '</table>';

echo '<hr>';

// insert the page navigator

$pages_needed = db_rowcount($search_result) / $VERSES_PER_PAGE;

if ($pages_needed > 1)
{
    if (db_rowcount($search_result) % $VERSES_PER_PAGE > 0)
    {
        $pages_needed++;
    }

    $pages_needed = intval($pages_needed);

    print_page_navigator(
        $CURRENT_PAGE,
        $pages_needed,
        true,
        'verse_browser.php?' .
            $pass_back_info .
            "&T=$TRANSLATOR&FORMULA=$FORMULA&FORMULA_TYPE=$FORMULA_TYPE"
    );
}

// hidden field used for clipboard data
echo "<input id=clipboardTemp value='" . verse_list_simplify() . "'>";

include 'library/footer.php';
?>

<script>
    $('#clipboardTemp').hide();

    // hide the Instant Details Palette on window scroll (stops it going behind things at the top, like bits of the interface)
    $(window).scroll(function() {
        Tipped.hideAll();
    });

    function click_copy_verses_button() {
        clipboardField = document.getElementById("clipboardTemp");
        $('#clipboardTemp').show();
        clipboardField.select();
        document.execCommand("copy");
        <?php if (db_rowcount($search_result) > 1)
{
    echo "$('#floating-message').html('Verse references copied to the clipboard');";
}
else
{
    echo "$('#floating-message').html('Verse reference copied to the clipboard');";
} ?>
        $('#floating-message').show();
        $('#clipboardTemp').hide();
        setTimeout(function() {
            $("#floating-message").hide();
        }, 1200);
    }
</script>

<!-- Convert tooltips to "Tipped" format -->

<script type="text/javascript">
    $(document).ready(function() {
        Tipped.create('.simple-tooltip', {
            position: 'bottommiddle',
            close: true,
            showDelay: 500
        });
        Tipped.create('.yellow-tooltip', {
            position: 'bottommiddle',
            maxWidth: 420,
            skin: 'lightyellow',
            showDelay: 1000,
            size: 'large'
        });
        Tipped.create('.loupe-tooltip', {
            position: 'left',
            maxWidth: 300,
            skin: 'light'
        });
    });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php move_back_to_top_button(); ?>
</body>

</html>