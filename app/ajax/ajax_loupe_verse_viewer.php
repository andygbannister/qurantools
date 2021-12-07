<?php

// this script powers the Instant Details Formulae Lister

// regenerate the session

session_start();
session_regenerate_id();

// check we are being called by a logged in user

if (!isset($_SESSION["UID"]))
{
    exit;
}

// connect to database and load library functions

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'library/verse_renderer.php';
require_once 'library/search_engine.php';
require_once 'library/verse_parse.php';

// load users table and load preferences
$result = db_query("SELECT * FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "'");

if (db_rowcount($result) > 0)
{
    $ROW                                              = db_return_row($result);
    $user_preference_translation                      = $ROW["Preferred Translation"];
    $user_preference_highlight_colour                 = "#" . $ROW["Preferred Highlight Colour"];
    $user_preference_highlight_colour_lightness_value = $ROW["Preferred Highlight Colour Lightness Value"];

    $user_preference_cursor_colour = "#" . $ROW["Preferred Cursor Colour"];

    $user_preference_turn_off_transliteration = $ROW["Preference Hide Transliteration"];

    if ($ROW["Preference Italics Transliteration"] == 1)
    {
        $useItalicsForTransliteration = true;
    }
    else
    {
        $useItalicsForTransliteration = false;
    }
}
else
{
    $user_preference_translation                      = 1;
    $user_preference_highlight_colour                 = "#FFFF00";
    $user_preference_highlight_colour_lightness_value = 200;
    $user_preference_cursor_colour                    = "#DDDDDD";
    $user_preference_italics_transliteration          = 1;
    $useItalicsForTransliteration                     = false;
    $user_preference_turn_off_transliteration         = false;
}

// load up the translation name to use
$translationName = db_return_one_record_one_field("SELECT `TRANSLATION NAME` FROM `TRANSLATION-LIST` WHERE `TRANSLATION ID`=" . db_quote($user_preference_translation));

echo "<div id='popup-verse-browser'>";

// DO A SEARCH

if (isset($_GET["SEARCH"]))
{
    $search_result = search(urldecode($_GET["SEARCH"]), false);

    $search_result->data_seek(0);

    // decide if we want to highlight rows (if more than one row)

    $noHover = "nohover";

    if (db_rowcount($search_result) > 1)
    {
        $noHover = "";
    }

    echo "<table class='verseBrowserTable $noHover' style='border-color: gray;' border=1 cellpadding=2' width=100%>";

    for ($i = 0; $i < db_rowcount($search_result); $i++)
    {
        $ROW = db_return_row($search_result);

        render_verse_new($ROW["SURA"], $ROW["VERSE"], [$translationName], "", false, 0, 0, $useItalicsForTransliteration, 0, "", true, $globalWordsToHighlight, false, [], []);
    }

    echo "</table>";

    echo "</div>";

    exit;
}

$SURA  = db_quote($_GET["S"]);
$VERSE = db_quote($_GET["V"]);

$FULL_SURA_LIST = [];

$searchArray = [];

// ARE WE LOOKING FOR A ROOT TO HIGHLIGHT
if (isset($_GET["ROOT"]))
{
    $rootsql = db_query("SELECT DISTINCT(`GLOBAL WORD NUMBER`) FROM `QURAN-DATA` WHERE BINARY(`QTL-ROOT`)='" . $_GET["ROOT"] . "' AND `SURA`=$SURA AND `VERSE`=$VERSE");

    if (db_rowcount($rootsql) > 0)
    {
        for ($i = 0; $i < db_rowcount($rootsql); $i++)
        {
            $ROW                                     = db_return_row($rootsql);
            $searchArray[$ROW["GLOBAL WORD NUMBER"]] = $ROW["GLOBAL WORD NUMBER"];
        }
    }
}

$FIRST_WORD_TO_HIGHLIGHT = 0;
$LAST_WORD_TO_HIGHLIGHT  = 0;

// are we looking to highlight a single word?
if (isset($_GET["highlightSingleWord"]))
{
    $FIRST_WORD_TO_HIGHLIGHT = $_GET["highlightSingleWord"];
    $LAST_WORD_TO_HIGHLIGHT  = $_GET["highlightSingleWord"];
}

// is there more than one verse

if (isset($_GET["EndVerse"]))
{
    $END_VERSE = $_GET["EndVerse"];
    $noHover   = "";
}
else
{
    $END_VERSE = $VERSE;
    $noHover   = "nohover";
}

echo "<table class='verseBrowserTable $noHover' style='border-color: gray;' border=1 cellpadding=2' width=100%>";

for ($i = $VERSE; $i <= $END_VERSE; $i++)
{
    render_verse_new($SURA, $i, [$translationName], "", false, $FIRST_WORD_TO_HIGHLIGHT, $LAST_WORD_TO_HIGHLIGHT, $useItalicsForTransliteration, 0, "", true, $searchArray, false, [], []);
}

echo "</table>";

echo "</div>";

?>

<script>
	
	$(document).ready(function() {
	
	$('.translationText').mouseover( function(e) 
	{		
		// 	get name of transliterated word element and work out name of Arabic word element
		translationWordID = e.target.id;
		arabicWordID = translationWordID.substring(1);	
		
		<?php

        echo "$('#a' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#' + translationWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#t' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";

        ?>
	
	});
	
	$('.translationText').mouseout( function(e) 
	{		
		// 	get name of transliterated word element and work out name of Arabic word element
		translationWordID = e.target.id;
		arabicWordID = translationWordID.substring(1);	
		
		<?php

        echo "$('#a' + arabicWordID).css('background-color', 'transparent');";
        echo "$('#' + translationWordID).css('background-color', 'transparent');";
        echo "$('#t' + arabicWordID).css('background-color', 'transparent');";

        ?>
	
	});
		
	$('.transliteratedWord').mouseover( function(e) 
	{		
		// 	get name of transliterated word element and work out name of Arabic word element
		transliteratedWordID = e.target.id;
		arabicWordID = transliteratedWordID.substring(1);
	
		<?php

        echo "$('#a' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#e' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";

        ?>
	
	});
	
	$('.transliteratedWord').mouseout( function(e) 
	{
		// 	get name of transliterated word element and work out name of Arabic word element
		transliteratedWordID = e.target.id;
		arabicWordID = transliteratedWordID.substring(1);
		
		$("#a" + arabicWordID).css('background-color', 'transparent');
		$("#e" + arabicWordID).css('background-color', 'transparent');
		$("#" + transliteratedWordID).css('background-color', 'transparent');
	
	});
	
	$('.arabicWord').mouseover( function(e) 
	{
		// 	get name of Arabic word element and work out name of Arabic word element
		arabicWordID = e.target.id;
		transliteratedWordID = arabicWordID.substring(1);
		
		<?php
        echo "$('#' + arabicWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#t' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');";
        echo "$('#e' + transliteratedWordID).css('background-color', '$user_preference_cursor_colour');";
        ?>
	
	});
	
	$('.arabicWord').mouseout( function(e) 
	{
		// 	get name of Arabic word element and work out name of Arabic word element
		arabicWordID = e.target.id;
		transliteratedWordID = arabicWordID.substring(1);
		
		$("#" + arabicWordID).css('background-color', 'transparent');
		$("#t" + transliteratedWordID).css('background-color', 'transparent');
		$("#e" + transliteratedWordID).css('background-color', 'transparent');
	
	});
	
});

</script>

<style>
		mark {
			background-color: 
			<?php
        echo $user_preference_highlight_colour . ";";
        echo "color: ";

        if ($user_preference_highlight_colour_lightness_value < 100)
        {
            echo "white";
        }
        else
        {
            echo "black";
        }
        ?>
		}
	</style>