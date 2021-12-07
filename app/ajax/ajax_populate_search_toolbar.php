<?php

// Fill Up the Searchbar Dropdown with Suggestions Pulled From History / Bookmarks

// truncate and bold the start of the text for the menu

function truncate_and_bold($text, $length)
{
    global $SEARCH;

    // Fill Up the Searchbar Dropdown with Suggestions Pulled From History / Bookmarks

    if (strlen($text) > $length)
    {
        $text = substr($text, 0, $length) . " ...";
    }

    // 	Bold the part of the string matched by the letters typed

    $text = "<span class='SearchSuggestionsEmphasised'>" . htmlentities(substr($text, 0, strlen($SEARCH))) . "</span><span class='SearchSuggestionsStandard'>" . htmlentities(substr($text, strlen($SEARCH), strlen($text))) . "</span>";

    return $text;
}

session_start();
session_regenerate_id();

// ARE THEY LEGITIMATELY LOGGED IN?
if (!isset($_SESSION["UID"]))
{
    exit;
}

require_once '../library/config.php';
require_once 'library/functions.php';

// we will have been passed the contents of what they are searching for

$SEARCH = "";

if (isset($_POST["S"]))
{
    $SEARCH = $_POST["S"];
}

if ($SEARCH == "")
{
    echo "NONE";
    exit;
}

// generate contents for the smart search toolbar

// first, find any history items

$resultHistory = db_query("SELECT * FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "' AND UPPER(`History Item`) LIKE '" . strtoupper(db_quote($SEARCH)) . "%' ORDER BY `Timestamp` DESC LIMIT 0, 10");

// second, find any bookmarks

$resultBookmark = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "' AND UPPER(`Name`) LIKE '" . strtoupper(db_quote($SEARCH)) . "%' LIMIT 0, 10");

// third, find any tags

$resultTags = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "' AND UPPER(`Tag Name`) LIKE '" . strtoupper(db_quote($SEARCH)) . "%' LIMIT 0, 10");

$some_text = false;

$elementIDcounter = 0;

// any history to include

if (db_rowcount($resultHistory) > 0)
{
    echo "<table>";
    echo "<tr><th colspan=2>Suggestions From Your Recent History</th></tr>";

    for ($i = 0; $i < db_rowcount($resultHistory); $i++)
    {
        // increment the element ID counter
        $elementIDcounter++;

        // grab next database row
        $ROW = db_return_row($resultHistory);

        echo "<tr class='suggested-row' id='suggestion$elementIDcounter' onMouseOver=\"SuggestionHover('$elementIDcounter');\">";
        echo "<td width=5>";

        if (is_numeric(substr($ROW["History Item"], 0, 1)))
        {
            // is this a verse reference or was it looking up a change id?
            if (is_numeric($ROW["History Item"]) && intval($ROW["History Item"]) > 1000)
            {
                echo "<img src='/images/qt-pen-small-gray.png' width=12 height=14>";
            }
            else
            {
                echo "<img src='images/qt-bookmark-small.png' width=10 height=10>";
            }
        }
        else
        {
            echo "<img src='images/qt-mag-small.png' width=10 height=10>";
        }
        echo "</td>";
        echo "<td><a href='/home.php?SEEK=" . urlencode($ROW["History Item"]) . "' onMouseOver=\"SuggestionHover(0);\">" . truncate_and_bold($ROW["History Item"], 125) . "</a>
		<input type=hidden ID=SuggestedURL$elementIDcounter value='" . htmlspecialchars($ROW["History Item"], ENT_QUOTES) . "'></td></tr>";
    }

    echo "</table>";

    $some_text = true;
}

// any bookmarks to include

if (db_rowcount($resultBookmark) > 0)
{
    echo "<table>";
    echo "<tr><th colspan=2>Suggestions From Your Bookmarks</th></tr>";

    for ($i = 0; $i < db_rowcount($resultBookmark); $i++)
    {
        // increment the element ID counter
        $elementIDcounter++;

        // grab next database row
        $ROW = db_return_row($resultBookmark);

        echo "<tr class='suggested-row' id='suggestion$elementIDcounter' onMouseOver=\"SuggestionHover('$elementIDcounter');\">
		<td width=5><img src='images/qt-bookmark-small.png' width=10 height=10></td>
		<td><a href='/home.php?SEEK=" . urlencode($ROW["Name"]) . "' onMouseOver=\"SuggestionHover(0);\">" . truncate_and_bold($ROW["Name"], 125) . "</a>
		<input type=hidden ID=SuggestedURL$elementIDcounter value='" . htmlspecialchars($ROW["Name"], ENT_QUOTES) . "'></td></tr>";
    }

    echo "</table>";

    $some_text = true;
}

// any tags to include

if (db_rowcount($resultTags) > 0)
{
    echo "<table>";
    echo "<tr><th colspan=2>Suggestions From Your Tags</th></tr>";

    for ($i = 0; $i < db_rowcount($resultTags); $i++)
    {
        // increment the element ID counter
        $elementIDcounter++;

        // grab next database row
        $ROW = db_return_row($resultTags);

        echo "<tr class='suggested-row' id='suggestion$elementIDcounter' onMouseOver=\"SuggestionHover('$elementIDcounter');\">
		<td width=5><img src='images/qt-bookmark-small.png' width=10 height=10></td>
		<td><a href='/home.php?SEEK=TAG:\"" . htmlspecialchars($ROW["Tag Name"], ENT_QUOTES) . "\"' onMouseOver=\"SuggestionHover(0);\">" . truncate_and_bold($ROW["Tag Name"], 125) . "</a>
		<input type=hidden ID=SuggestedURL$elementIDcounter value='TAG:\"" . htmlspecialchars($ROW["Tag Name"], ENT_QUOTES) . "\"'></td></tr>";
    }

    echo "</table>";

    $some_text = true;
}

// if nothing to show, pass that fact back to the searchbar handler
if (!$some_text)
{
    if ($SEARCH != "")
    {
        echo "<span id='PressEnter'>Press enter to search</span>";
    }
    else
    {
        echo "NONE";
    }
}
