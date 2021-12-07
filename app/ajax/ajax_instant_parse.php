<?php

// this script powers the Instant Details Parser in verse_browser.php and verse_browser.php

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
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

// load the little bit of css we need

// echo " <link rel='stylesheet' type='text/css' href='library/menubar.css'>";

// grab the word
$WORD = 1;
if (isset($_GET["W"]))
{
    $WORD = $_GET["W"];
}

// lookup the word to parse

$result = db_query("SELECT * FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));

// build the tip

$extra        = "";
$parsing_info = "";
$SEARCH_ROOT  = "";
$SEARCH_LEMMA = "";
$SEARCH_EXACT = "";

// loop through each segment of this word, building up the parsing info

for ($j = 0; $j < db_rowcount($result); $j++)
{
    // grab next database row
    $ROW = db_return_row($result);

    if ($j > 0)
    {
        $parsing_info .= "<br> + ";
    }

    $parsing_info .= "<b>" . $ROW["QTL-TAG-EXPLAINED"] . "</b>";

    if ($ROW["QTL-GENDER"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-GENDER"];
    }

    if ($ROW["QTL-CASE"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-CASE"];
    }

    if ($ROW["QTL-NUMBER"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-NUMBER"];
    }

    if ($ROW["QTL-PERSON"] > 0)
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-PERSON"] . "P";
    }

    if ($ROW["QTL-VOICE"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-VOICE"];
    }

    if ($ROW["QTL-DERIVED-NOUN-TYPE"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-DERIVED-NOUN-TYPE"];
    }

    if ($ROW["QTL-ARABIC-FORM"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= "Form " . $ROW["QTL-ARABIC-FORM"];
    }

    if ($ROW["QTL-NOUN-STATE"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-NOUN-STATE"];
    }

    if ($ROW["QTL-MOOD"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= $ROW["QTL-MOOD"];
    }

    if ($ROW["QTL-ROOT"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= "Root: <font size=+1>" . htmlentities(return_arabic_word($ROW["QTL-ROOT"])) . "</font> (" . htmlentities(transliterate_new($ROW["QTL-ROOT"])) . ") <a href='examine_root.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "&BACK=Return to Verse Browser' onClick='Tipped.hideAll();'><img src='/images/it.gif'></a>&nbsp;<a href='exhaustive_root_references.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "&BACK=Return to Verse Browser' onClick='Tipped.hideAll();'><img src='/images/lines_small.gif'></a>&nbsp;<a href='charts/chart_roots.php?ROOT=" . urlencode($ROW["QTL-ROOT-TRANSLITERATED"]) . "'><img src='/images/st.gif'></a>";
        $SEARCH_ROOT = $ROW["QTL-ROOT-TRANSLITERATED"];
    }

    if ($ROW["QTL-LEMMA"] != "")
    {
        $arabic_version_of_lemma = return_arabic_word($ROW["QTL-LEMMA"]);

        // if this lemma exists in the dictionary, we show a link
        $lemmaDictionaryLink = "";
        if (db_return_one_record_one_field("SELECT COUNT(*) FROM `DICTIONARY-ENTRIES` WHERE `ENGLISH`='" . db_quote($ROW["QTL-LEMMA"]) . "' AND `TYPE`='LEMMA' AND `MEANING`!=''") > 0)
        {
            $lemmaDictionaryLink = " <a href='dictionary.php?S=" . $ROW["QTL-LEMMA"] . "&TYPE=LEMMA&DISPLAY=" . $ROW["RENDERED ARABIC"] . "'><img src='/images/it.gif'></a>";
        }

        // show the "foreign" (loanword) icon/word

        $foreign_text = "";

        $for_result = db_return_one_record_one_field("SELECT `AJ FOREIGN PAGE` FROM `LEMMA-LIST` WHERE (BINARY `ENGLISH` ='" . db_quote(convert_buckwalter($ROW["QTL-LEMMA"])) . "' OR BINARY `ENGLISH TRANSLITERATED` ='" . db_quote(convert_buckwalter($ROW["QTL-LEMMA"])) . "' OR `ARABIC`='" . db_quote($arabic_version_of_lemma) . "' OR `ARABIC ALTERNATE RENDERING`='" . db_quote($arabic_version_of_lemma) . "') AND `AJ FOREIGN PAGE`>0");

        if ($for_result > 0)
        {
            $foreign_text .= "&nbsp;<a href='#' onclick='$.colorbox({href:\"dictionary/jeffery.php?PAGE=" . (14 + $for_result) . "&LIGHTVIEW=YES\",width:\"90%\", height:\"95%\"});'><img src='/images/loan.png' valign=middle></a>";
        }

        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= "Lemma: <font size=+1>" . htmlentities($arabic_version_of_lemma) . "</font> (" . htmlentities(transliterate_new($ROW["QTL-LEMMA"])) . ")$lemmaDictionaryLink" . "&nbsp;<a href='exhaustive_root_references.php?LEMMA=" . urlencode($ROW["QTL-LEMMA"]) . "&BACK=Return to Verse Browser' onClick='Tipped.hideAll();'><img src='/images/lines_small.gif'></a>&nbsp;<a href='charts/chart_lemmata.php?LEMMA=" . urlencode($ROW["QTL-LEMMA"]) . "'><img src='/images/st.gif'></a>$foreign_text";
        $SEARCH_LEMMA = urlencode(convert_buckwalter($ROW["QTL-LEMMA"]));
    }

    if ($ROW["QTL-SPECIAL-WORD-GROUP"] != "")
    {
        $extra .= "<br>&nbsp;&nbsp;- ";
        $extra .= "Special Group: <font size=+1>" . htmlentities(return_arabic_word($ROW["QTL-SPECIAL-WORD-GROUP"])) . "</font> (" . htmlentities(transliterate_new($ROW["QTL-SPECIAL-WORD-GROUP"])) . ")";
    }

    $SEARCH_EXACT = $ROW["TRANSLITERATED"];

    if ($extra != "")
    {
        $parsing_info .= $extra;
        $extra = "";
    }
}

// reposition back to the first record ready to draw the tooltip

db_goto($result, 0);

$ROW = db_return_row($result);

// first, print the header (word number; Arabic; transliteration)

echo "<div style='margin-top: 3px;'>";

// if user has admin rights, they can click on this word to get to the word correction entry screen

if (isset($_SESSION['administrator']))
{
    if ($_SESSION['administrator'] != "" && $_SESSION['administrator'] != "WORD_FIXER")
    {
        echo "<a href='/admin/word_correction_tool.php?W=$WORD' style='color: white' class=linky title='Click to edit the Arabic rendering or transliteration of this word (global word #$WORD)'>";
    }
}

echo "#" . $ROW["WORD"];

if (isset($_SESSION['administrator']))
{
    if ($_SESSION['administrator'] != "")
    {
        echo "</a>";
    }
}

echo "&nbsp;&nbsp;<font size=+2>" . $ROW["RENDERED ARABIC"] . "</font> <font size=+1>(" . $ROW["TRANSLITERATED"] . ")</font>";

// does this word have an associated audio file? (for now, only works for administrators)
if ($_SESSION['administrator'])
{
    $morphemeNumber = db_return_one_record_one_field("SELECT `EXACT ID` FROM `EXACT-ARABIC-LIST` WHERE `EXACT ARABIC`='" . db_quote($ROW["RENDERED ARABIC"]) . "'");

    $filename = "../audio/words/$morphemeNumber.mp3";

    if (file_exists($filename))
    {
        echo "<audio controls id=wordAudio class=hidden>";
        echo "<source src='$filename' type='audio/mpeg'>";
        echo "</audio>";
        echo " <a href='#' onClick=\"document.getElementById('wordAudio').play(); return false;\">";
        echo "<img src='/images/speaker.png'></a>";
    }
}

echo "</div>";

// second, print the gloss

if ($ROW["GLOSS"] != "" && $ROW["GLOSS"] != "???")
{
    $indent = 20;
    if ($ROW["WORD"] > 9)
    {
        $indent += 7;
    }
    echo "<div style='margin-top:1px; margin-left:" . $indent . "px;'>" . htmlentities($ROW["GLOSS"]) . "</div>";
}

// third, print the parsing info

echo "<div style='margin-top: 1px;'>$parsing_info</div>";

// fourth, we print the search links

echo "<hr style='margin-top: 8px;'>";
echo "<div align=left>Search: ";

if ($SEARCH_ROOT != "")
{
    echo "<a href='verse_browser.php?S=ROOT:" . urlencode($SEARCH_ROOT) . "'>Root</a> | ";
}

if ($SEARCH_LEMMA != "")
{
    if ($ROW["QTL-LEMMA"] != "")
    {
        echo "<a href='verse_browser.php?S=LEMMA:" . urlencode($ROW["QTL-LEMMA"]) . " OR LEMMA:$arabic_version_of_lemma'>Lemma</a> | ";
    }
    else
    {
        echo "<a href='verse_browser.php?S=LEMMA:" . $SEARCH_LEMMA . " OR LEMMA:$arabic_version_of_lemma'>Lemma</a> | ";
    }
}

echo "<a href='verse_browser.php?S=GLOSS:\"" . urlencode($ROW["GLOSS"]) . "\"'>Gloss</a> | ";

echo "<a href='verse_browser.php?S=EXACT:$SEARCH_EXACT'>Exact Inflection</a></div>";
