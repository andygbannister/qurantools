<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

// LIGHTVIEW (POPUP) MODE?
$lightviewMode = false;

if (isset($_GET["LIGHTVIEW"]))
{
    if ($_GET["LIGHTVIEW"] == "YES")
    {
        $lightviewMode = true;
    }
}
else
{
    $_GET["LIGHTVIEW"] = "";
}

?>
<html>
    <head>
    <?php
        require 'library/standard_header.php';
        window_title("Word Correction Tool");
    ?>
    
     <script>
    // add a listener to detect for the escape key
	
	document.onkeydown = checkKey;
	
	window.focus();

    function checkKey(e) 
	{
	    e = e || window.event;
	    
	    if (e.keyCode == '27') 
	    {
		    // escape key
		    parent.closeLightView();
		}
	}
	
    </script>
    
    
    <?php

    // menubar etc

    if (!$lightviewMode)
    {
        require "../library/menu.php";
    }

// grab the word number

$WORD = 1;
if (isset($_GET["W"]))
{
    $WORD = $_GET["W"];
}

// deal with any changes

$message = "";

// have they saved a change to a word?

if (isset($_GET["OK"]))
{
    // if this is the first time that a fix has been created for this record, we need to record who created the record

    $count_already_done = db_query("SELECT * FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD) . " AND (`FIXED_TRANSLITERATION`!='' OR `FIXED_GLOSS`!='' OR `FIXED_ARABIC`!='')");

    if (db_rowcount($count_already_done) > 0)
    {
        db_query("UPDATE `QURAN-DATA` SET `FIX_CREATED_BY`='" . db_quote($_SESSION['UID']) . "' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }

    // now save any changes/edits

    if (isset($_GET["NOTES"]))
    {
        $message = "Changes saved.";
        db_query("UPDATE `QURAN-DATA` SET `FIXED_NOTES`='" . db_quote($_GET["NOTES"]) . "' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }

    if (isset($_GET["FIX_CHANGES_SUGGESTED"]))
    {
        $message = "Changes saved.";
        db_query("UPDATE `QURAN-DATA` SET `FIX_CHANGES_SUGGESTED`='" . db_quote($_GET["FIX_CHANGES_SUGGESTED"]) . "' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }

    if (isset($_GET["NEW_GLOSS"]))
    {
        $message = "Changes saved. They will not be applied to the actual text until a moderator/admin signs off on them.";
        db_query("UPDATE `QURAN-DATA` SET `FIXED_GLOSS`='" . db_quote($_GET["NEW_GLOSS"]) . "', `FIXED_DB_APPROVED`='' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }

    if (isset($_GET["NEW_TRANSLITERATION"]))
    {
        $message = "Changes saved. They will not be applied to the actual text until a moderator/admin signs off on them.";
        db_query("UPDATE `QURAN-DATA` SET `FIXED_TRANSLITERATION`='" . db_quote($_GET["NEW_TRANSLITERATION"]) . "', `FIXED_DB_APPROVED`='' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }

    if (isset($_GET["NEW_ARABIC"]))
    {
        $message = "Changes saved. They will not be applied to the actual text until moderator/admin signs off on them.";
        db_query("UPDATE `QURAN-DATA` SET `FIXED_ARABIC`='" . db_quote($_GET["NEW_ARABIC"]) . "', `FIXED_DB_APPROVED`='' WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
    }
}

// in lightview mode, we just reload the parent to page to clear this window and update the parent

if ($message != "")
{
    if ($lightviewMode)
    {
        echo "<script>";
        echo "parent.closeLightView();";
        echo "parent.location.reload()";
        echo "</script>";
        exit;
    }
    else
    {
        if (!empty($_GET["REFERRING_PAGE"]))
        {
            echo "<script>";
            echo "window.location = \"" . urldecode($_GET["REFERRING_PAGE"]) . "\";";
            echo "</script>";
            exit;
        }
    }
}

// finish setting up

 echo "</head><body class='qt-site'><main class='qt-site-content'>";

echo "<div align=center>";

echo "<h2 class='page-title-text'>Word Correction Tool</h2>";

// tell user changes have been saved
if ($message)
{
    echo "<div style='margin-top:10px; margin-bottom:20px;' class='message message-success'>$message</div>";
}

// load the details of this word from the database

$result = db_query("SELECT * FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . db_quote($WORD));
$ROW    = db_return_row($result);

// create the form

echo "<form action='word_correction_tool.php' METHOD=GET>";

echo "<input NAME=LIGHTVIEW type=hidden value=" . $_GET["LIGHTVIEW"] . ">";

echo "<input NAME=REFERRING_PAGE size=150 type=hidden value=" . urlencode($_SERVER['HTTP_REFERER']) . ">";

echo "<table border=1 cellpadding=8 cellspacing=0 width=900>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
    echo "Editing Global Word Number";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium><a href='../verse_browser.php?V=" . $ROW["SURA"] . ":" . $ROW["VERSE"] . "&highlight_single_word=$WORD' class=linky>";
    echo $WORD;
    echo "</a>";
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
    echo "Location (Sura:Verse:Word)";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium><a href='../verse_browser.php?V=" . $ROW["SURA"] . ":" . $ROW["VERSE"] . "&highlight_single_word=$WORD' class=linky>";
    echo $ROW["SURA"] . ":" . $ROW["VERSE"] . ":" . $ROW["WORD"];
    echo "</a></td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
    echo "Current Arabic Rendering";
    echo "</td>";

    echo "<td class=wordCorrectionTableLarge>";
    echo $ROW["RENDERED ARABIC"];
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
    echo "Current Transliteration";
    echo "</td>";

    echo "<td class=wordCorrectionTableLarge>";
    echo $ROW["TRANSLITERATED"];
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
    echo "Current Gloss";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium>";
    echo $ROW["GLOSS"];
    echo "</td>";

echo "</tr>";

echo "<tr>";
echo "<td class=wordCorrectionTableMedium colspan=2 align=center style='background-color:#909090;'><b>To Correct This Word, Make Changes Below</td>";
echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
        echo "New Arabic Rendering";
    echo "</td>";

    echo "<td class=wordCorrectionTableLarge>";

        echo "<input style='font-size: 20pt; width:400px;' name=NEW_ARABIC width=50 max=40";

        if ($ROW["FIXED_ARABIC"] != "")
        {
            echo " value='" . $ROW["FIXED_ARABIC"] . "'";
        }
        else
        {
            // echo " placeholder='".$ROW["RENDERED ARABIC"]."'";
            echo " placeholder='Amended Arabic'";
        }
        echo ">";

    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
        echo "New Transliteration";
    echo "</td>";

    echo "<td class=wordCorrectionTableLarge>";

        echo "<input style='font-size: 20pt; width:400px;' name=NEW_TRANSLITERATION width=50 max=40";

        if ($ROW["FIXED_TRANSLITERATION"] != "")
        {
            echo " value='" . htmlspecialchars($ROW["FIXED_TRANSLITERATION"], ENT_QUOTES) . "'";
        }
        else
        {
            // echo " placeholder='".htmlspecialchars($ROW["TRANSLITERATED"], ENT_QUOTES)."'";
            echo " placeholder='Amended Transliteration'";
        }

        echo ">";

    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
        echo "New Gloss";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium>";

        echo "<input style='width:400px;' name=NEW_GLOSS width=50 max=40";

        if ($ROW["FIXED_GLOSS"] != "")
        {
            echo " value='" . htmlspecialchars($ROW["FIXED_GLOSS"], ENT_QUOTES) . "'";
        }
        else
        {
            // echo " placeholder='".htmlspecialchars($ROW["TRANSLITERATED"], ENT_QUOTES)."'";
            echo " placeholder='Amended Gloss'";
        }

        echo ">";

    echo "</td>";

echo "</tr>";

// any suggested changes

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
        echo "Suggested Changes To Fix";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium>";

        echo "<textarea name=FIX_CHANGES_SUGGESTED rows=2 cols=70 maxlength=100";

        if ($ROW["FIX_CHANGES_SUGGESTED"] == "")
        {
            echo " placeholder='Any comments, suggestions etc'";
        }

        echo ">";

        if ($ROW["FIX_CHANGES_SUGGESTED"] != "")
        {
            echo htmlspecialchars($ROW["FIX_CHANGES_SUGGESTED"], ENT_QUOTES);
        }

        echo "</textarea>";

    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td class=wordCorrectionTableMedium>";
        echo "Notes";
    echo "</td>";

    echo "<td class=wordCorrectionTableMedium>";

        echo "<textarea name=NOTES rows=3 cols=70 maxlength=200";

        if ($ROW["FIXED_NOTES"] == "")
        {
            echo " placeholder='Notes/Comments/Tags etc'";
        }

        echo ">";

        if ($ROW["FIXED_NOTES"] != "")
        {
            echo htmlspecialchars($ROW["FIXED_NOTES"], ENT_QUOTES);
        }

        echo "</textarea>";

    echo "</td>";

echo "</tr>";

echo "<tr><td colspan=2 align=center>";

// hidden field to pass back the word number
echo "<input type=HIDDEN name=W value=$WORD>";

// hidden field to test we have actually hit submit
echo "<input type=HIDDEN name=OK value=Y>";

if (!$lightviewMode)
{
    echo "<a href='" . $_SERVER['HTTP_REFERER'] . "&highlight_single_word=" . $_GET["W"] . "'>";
}
else
{
    echo "<a href='word_correction_tool.php?W=" . $_GET["W"] . "'>";
}

echo "<button type='button' onClick='parent.closeLightView();'>Cancel</button>";
echo "</a>";

echo "<button type='submit'>Save Any Changes</button>";
echo "</td></tr>";

echo "</table>";
echo "</form>";

if (!$lightviewMode)
{
    echo "<a href='word_correction_logs.php' class=linky-light>(See All Word Corrections Entered So Far)</a>";
}

echo "</div>";

// print footer

if (!$lightviewMode)
{
    require "../library/footer.php";
}

?>

</body>
</html>