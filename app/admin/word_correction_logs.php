<?php

function highlight_search_result($text, $exact)
{
    if (!$exact)
    {
        return str_ireplace(strtolower($_GET["SEARCH"]), "<MARK>" . $_GET["SEARCH"] . "</MARK>", strtolower($text));
    }

    if (strtolower($_GET["SEARCH"]) == $text)
    {
        return "<MARK>$text</MARK>";
    }

    return $text;
}

function apply_change($global_word)
{
    // get the data on the record in question

    $result = db_query("SELECT * FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . db_quote($global_word));

    $ROW = db_return_row($result);

    // is it a gloss fix? (in which case we just apply it)
    if ($ROW["FIXED_GLOSS"] != "")
    {
        db_query("UPDATE `QURAN-DATA` SET `GLOSS`='" . db_quote($ROW["FIXED_GLOSS"]) . "' WHERE `TRANSLITERATED`='" . db_quote($ROW["TRANSLITERATED"]) . "'");
    }

    // is it a transliteration fix? (in which case we apply but save the old version [GLOBALLY])
    if ($ROW["FIXED_TRANSLITERATION"] != "")
    {
        $sql = "UPDATE `QURAN-DATA` SET `PREVIOUS TRANSLITERATION`='" . db_quote($ROW["TRANSLITERATED"]) . "' WHERE `TRANSLITERATED`='" . db_quote($ROW["TRANSLITERATED"]) . "'";

        db_query($sql);

        $sql = "UPDATE `QURAN-DATA` SET `TRANSLITERATED`='" . db_quote($ROW["FIXED_TRANSLITERATION"]) . "' WHERE `TRANSLITERATED`='" . db_quote($ROW["TRANSLITERATED"]) . "'";

        db_query($sql);
    }

    // is it an Arabic fix? (in which case we apply but save the old version [GLOBALLY])
    if ($ROW["FIXED_ARABIC"] != "")
    {
        $sql = "UPDATE `QURAN-DATA` SET `PREVIOUS ARABIC`='" . db_quote($ROW["RENDERED ARABIC"]) . "' WHERE `RENDERED ARABIC`='" . db_quote($ROW["RENDERED ARABIC"]) . "'";

        db_query($sql);

        $sql = "UPDATE `QURAN-DATA` SET `RENDERED ARABIC`='" . db_quote($ROW["FIXED_ARABIC"]) . "' WHERE `RENDERED ARABIC`='" . db_quote($ROW["RENDERED ARABIC"]) . "'";

        db_query($sql);
    }

    db_query("UPDATE `QURAN-DATA` SET `TRANSLITERATION FIX APPLIED`='Y' WHERE `GLOBAL WORD NUMBER`=" . db_quote($global_word));

    return "Change applied. See the updated word in context <a href='../verse_browser.php?V=" . $ROW["SURA"] . ":" . $ROW["VERSE"] . "&highlight_single_word=" . $ROW["GLOBAL WORD NUMBER"] . "' class=linky-light target='blank'>here</a>.";
}

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

// duplicate fix hack
$DUPLICATE_SQL = "";
if (isset($_GET["DUPLICATE"]))
{
    $DUPLICATE_SQL = "AND `FIXED_TRANSLITERATION`=`TRANSLITERATED` AND `FIXED_ARABIC`='' AND `FIXED_GLOSS`=''";
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 500;
$CURRENT_PAGE   = 1;

// MESSAGE
$message = "";

// ARE WE DOING AN SQL EXPORT?

$OUTPUT_DATABASE = false;

if (isset($_GET["OUTPUT_DATABASE"]))
{
    if ($_GET["OUTPUT_DATABASE"] == "Y")
    {
        if (is_admin_user($logged_in_user))
        {
            $OUTPUT_DATABASE = true;
        }
    }
}

// have they applied a change
if (isset($_GET["APPLY"]) && is_admin_user($logged_in_user))
{
    $message = apply_change($_GET["APPLY"]); // we now use a function, so we can call it from elsewhere
}

// have they asked to apply multiple changes?
$apply_all = 0;
if (isset($_GET["APPLY_ALL"]))
{
    $apply_all = $_GET["APPLY_ALL"];
    if ($apply_all < 1)
    {
        $apply_all = 0;
    }
}

$MAX_APPLY_ALL = 50;

if ($apply_all > $MAX_APPLY_ALL)
{
    $apply_all = $MAX_APPLY_ALL;
    $message   = "'Apply all' has been capped at " . number_format($MAX_APPLY_ALL) . " changes: " . number_format($apply_all) . " changes have been applied to the text";
}
else
{
    if ($apply_all > 0)
    {
        $message = "All " . number_format($apply_all) . " changes have been applied to the text";
    }
}

// have they approved a word
if (isset($_GET["APPROVE"]))
{
    if (is_admin_user($logged_in_user) || $_SESSION['administrator'] == "WORD_FIXER")
    {
        $message = "The change for word " . $_GET["APPROVE"] . " has been approved.";

        db_query("UPDATE `QURAN-DATA` SET `FIXED_DB_APPROVED`='Y', `FIX_APPROVED_BY`=" . db_quote($_SESSION['UID']) . ", `TRANSLITERATION FIX APPLIED`='N' WHERE `GLOBAL WORD NUMBER`=" . db_quote($_GET["APPROVE"]));

        // also approve any identical ones
        $fixed_transliteration = db_return_one_record_one_field("SELECT `FIXED_TRANSLITERATION` FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . db_quote($_GET["APPROVE"]));

        $how_many_duplicates = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE (`FIXED_DB_APPROVED`='N' OR `FIXED_DB_APPROVED`='') AND `FIXED_TRANSLITERATION`='" . db_quote($fixed_transliteration) . "' AND `FIXED_GLOSS`='' AND `FIXED_ARABIC`=''");

        if ($how_many_duplicates > 0)
        {
            if ($how_many_duplicates == 1)
            {
                $message .= " (As has one duplicate.)";
            }
            else
            {
                $message .= " (As have $how_many_duplicates duplicates.)";
            }
        }

        db_query("UPDATE `QURAN-DATA` SET `FIXED_DB_APPROVED`='Y', `FIX_APPROVED_BY`=" . db_quote($_SESSION['UID']) . ", `TRANSLITERATION FIX APPLIED`='N' WHERE `FIXED_TRANSLITERATION`='" . db_quote($fixed_transliteration) . "' AND `FIXED_GLOSS`='' AND `FIXED_ARABIC`=''");
    }
}

// have they unapproved a word
if (isset($_GET["UNAPPROVE"]))
{
    if (is_admin_user($logged_in_user) || $_SESSION['administrator'] == "WORD_FIXER")
    {
        $message = "The change for word " . $_GET["UNAPPROVE"] . " has been unapproved";

        db_query("UPDATE `QURAN-DATA` SET `FIXED_DB_APPROVED`='', `FIX_APPROVED_BY`=0 WHERE `GLOBAL WORD NUMBER`=" . db_quote($_GET["UNAPPROVE"]));
    }
}

// GET CURRENT PAGE

if (isset($_GET["PAGE"]))
{
    $CURRENT_PAGE = $_GET["PAGE"];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET["PAGE"] = "";
}

?>
<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title("Word Correction List");
    ?>
    <script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

    <script type="text/javascript" src="../library/js/lightview/spinners/spinners.min.js"></script>

    <script type="text/javascript" src="../library/js/lightview/lightview/lightview.js"></script>

    <script>
        function closeLightView() {
            Lightview.hide();
        }
    </script>



    <link rel="stylesheet" type="text/css" href="../library/js/lightview/css/lightview/lightview.css" />




</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

        include "library/back_to_top_button.php";

        // sort order
        $SORT_ORDER = "ORDER BY `GLOBAL WORD NUMBER`";

        if (!isset($_GET["SORT"]))
        {
            $_GET["SORT"] = "";
        }

        if ($_GET["SORT"] == "GLOBAL-ASC")
        {
            $SORT_ORDER = "ORDER BY `GLOBAL WORD NUMBER` ASC";
        }

        if ($_GET["SORT"] == "GLOBAL-DESC")
        {
            $SORT_ORDER = "ORDER BY `GLOBAL WORD NUMBER` DESC";
        }

        if ($_GET["SORT"] == "REFERENCE-ASC")
        {
            $SORT_ORDER = "ORDER BY `SURA`, `VERSE`,  `WORD`";
        }

        if ($_GET["SORT"] == "REFERENCE-DESC")
        {
            $SORT_ORDER = "ORDER BY `SURA` DESC, `VERSE` DESC,  `WORD` DESC";
        }

        if ($_GET["SORT"] == "DB-ASC")
        {
            $SORT_ORDER = "ORDER BY `FIXED_DB_APPROVED` ASC";
        }

        if ($_GET["SORT"] == "DB-DESC")
        {
            $SORT_ORDER = "ORDER BY `FIXED_DB_APPROVED` DESC";
        }

        if ($_GET["SORT"] == "FIXED-ASC")
        {
            $SORT_ORDER = "ORDER BY `TRANSLITERATION FIX APPLIED` ASC";
        }

        if ($_GET["SORT"] == "FIXED-DESC")
        {
            $SORT_ORDER = "ORDER BY `TRANSLITERATION FIX APPLIED` DESC";
        }

        // menubar

        include "../library/menu.php";

        echo "<div align=center><h2 class='page-title-text'>Word Correction List</h2>";

        // set up bold buttons

        $bold_button_start = ["<b>", "", "", ""];
        $bold_button_end   = ["</b>", "", "", ""];

        $FILTER_SQL = "";

        if (isset($_GET["FILTER"]))
        {
            if ($_GET["FILTER"] == "APPLIED")
            {
                $bold_button_start[0] = "";
                $bold_button_end[0]   = "";

                $bold_button_start[1] = "<b>";
                $bold_button_end[1]   = "</b>";

                $FILTER_SQL = "AND `TRANSLITERATION FIX APPLIED`='Y'";
            }

            if ($_GET["FILTER"] == "APPROVED_NOT_APPLIED")
            {
                $bold_button_start[0] = "";
                $bold_button_end[0]   = "";

                $bold_button_start[2] = "<b>";
                $bold_button_end[2]   = "</b>";

                $FILTER_SQL = "AND `TRANSLITERATION FIX APPLIED`!='Y' AND `FIXED_DB_APPROVED`='Y'";
            }

            if ($_GET["FILTER"] == "NOT_YET_APPROVED")
            {
                $bold_button_start[0] = "";
                $bold_button_end[0]   = "";

                $bold_button_start[3] = "<b>";
                $bold_button_end[3]   = "</b>";

                $FILTER_SQL = "AND `FIXED_DB_APPROVED`!='Y'";
            }
        }
        else
        {
            $_GET["FILTER"] = "";
        }

        // search

        $SEARCH_SQL = "";

        if (isset($_GET["SEARCH"]) && !isset($_GET["WIPE"]))
        {
            if ($_GET["SEARCH"] != "")
            {
                $_GET["SEARCH"] = db_quote($_GET["SEARCH"]);
                $SEARCH_SQL     = " AND (`GLOBAL WORD NUMBER`='" . $_GET["SEARCH"] . "' OR `FIXED_NOTES` LIKE '%" . $_GET["SEARCH"] . "%' OR `FIXED_GLOSS` LIKE '%" . $_GET["SEARCH"] . "%' OR `FIXED_TRANSLITERATION` LIKE '%" . $_GET["SEARCH"] . "%' OR `TRANSLITERATED` LIKE '%" . $_GET["SEARCH"] . "%' OR `RENDERED ARABIC` LIKE '%" . $_GET["SEARCH"] . "%' OR `FIXED_ARABIC` LIKE '%" . $_GET["SEARCH"] . "%')";
            }
        }
        else
        {
            $_GET["SEARCH"] = "";
        }

        // filtering buttons

        if (!$OUTPUT_DATABASE)
        {
            echo "<div class='button-block-with-spacing'>";
            echo "<a href='word_correction_logs.php'><button>" . $bold_button_start[0] . "Show All</button>" . $bold_button_end[0] . "</a>";
            echo "<a href='word_correction_logs.php?FILTER=APPLIED'><button>" . $bold_button_start[1] . "Filter: Corrections Applied" . $bold_button_end[1] . "</button></a>";
            echo "<a href='word_correction_logs.php?FILTER=APPROVED_NOT_APPLIED'><button>" . $bold_button_start[2] . "Filter: Approved but Not Applied" . $bold_button_end[2] . "</button></a>";
            echo "<a href='word_correction_logs.php?FILTER=NOT_YET_APPROVED'><button>" . $bold_button_start[3] . "Filter: Not Yet Approved" . $bold_button_end[3] . "</button></a>";

            echo "<span class=\"yellow-tooltip\" title='Search for particular word corrections'>";
            echo "<img src='/images/mag.png' onClick=\"$('#search_div').toggle(); inputText.focus();\">";
            echo "</span>";

            echo "</div>";

            echo "<div id=search_div ";
            if ($SEARCH_SQL == "")
            {
                echo "style='display: none;'";
            }
            echo ">";

            echo "<form id='pickVerse' action='word_correction_logs.php' method=get name=FormName}>";

            echo "<input NAME='FILTER' type=hidden value='" . $_GET["FILTER"] . "'>";

            echo "<input id='inputText' type=text style='font-size:14px' autofocus NAME=SEARCH size=20 maxlength=40 autocomplete='off' ";
            if ($SEARCH_SQL == "")
            {
                echo "placeholder='Search word correction records'";
            }
            else
            {
                echo "value='" . htmlspecialchars($_GET["SEARCH"], ENT_QUOTES) . "'";
            }

            echo ">";

            echo " <button name=OKbutton style='font-size:14px' type=submit>SEARCH</button>";

            if ($SEARCH_SQL != "")
            {
                echo "<a href='word_correction_logs.php?FILTER=" . $_GET["FILTER"] . "'><button name=WIPE value=wipe type=wipe>(Clear Search Criteria)</button></a>";
            }

            echo "</form></div>";

            if ($message != "")
            {
                echo "<div class='message message-success message-at-top-of-page-after-action'>$message</div>";
            }

            echo "<table class='hoverTable persist-area fixedTable qt-table' width=950>";

            // table header

            echo "<thead>";

            echo "<tr class='persist-header table-header-row fixedTable' width=200>";

            echo "<th width=110><b>Global<br>Word #</b><br><a href='word_correction_logs.php?SORT=GLOBAL-ASC'><img src='../images/up.gif'></a> <a href='word_correction_logs.php?SORT=GLOBAL-DESC'><img src='../images/down.gif'></a></th>";

            echo "<th width=90><b>Reference</b><br><a href='word_correction_logs.php?SORT=REFERENCE-ASC'><img src='../images/up.gif'></a> <a href='word_correction_logs.php?SORT=REFERENCE-DESC'><img src='../images/down.gif'></a></th>";

            echo "<th width=80><b>Original<br>Arabic</b></th>";

            echo "<th width=110><b>Original<br>Transliteration</b></th>";

            echo "<th width=90><b>Corrected<br>Arabic</b></th>";

            echo "<th width=110><b>Corrected<br>Transliteration</b></th>";

            echo "<th width=110><b>Corrected<br>Gloss</b></th>";

            echo "<th width=120><b>Approved by Second Checker?</b><br><a href='word_correction_logs.php?SORT=DB-ASC'><img src='../images/up.gif'></a> <a href='word_correction_logs.php?SORT=DB-DESC'><img src='../images/down.gif'></a></th>";

            echo "<th width=100><b>Fix<br>Applied?</b><br><a href='word_correction_logs.php?SORT=FIXED-ASC'><img src='../images/up.gif'></a> <a href='word_correction_logs.php?SORT=FIXED-DESC'><img src='../images/down.gif'></a></th>";

            echo "<th width=170>&nbsp;</th>";

            echo "</tr>";

            echo "</thead>";

            echo "<tbody>";
        }

        $result = db_query("SELECT DISTINCT(`GLOBAL WORD NUMBER`), `SURA`, `VERSE`, `FIXED_DB_APPROVED`, `TRANSLITERATION FIX APPLIED`, `FIXED_ARABIC`, `FIXED_TRANSLITERATION`, `FIXED_GLOSS`, `RENDERED ARABIC`, `TRANSLITERATED`, `WORD`, `FIXED_NOTES`, `FIX_CREATED_BY`, `FIX_APPROVED_BY`, `FIX_CHANGES_SUGGESTED`, `PREVIOUS ARABIC`, `PREVIOUS TRANSLITERATION` FROM `QURAN-DATA` WHERE (`FIXED_ARABIC`!='' OR `FIXED_TRANSLITERATION`!=''  OR `FIXED_GLOSS`!='') AND `SEGMENT`=1 $FILTER_SQL $DUPLICATE_SQL $SEARCH_SQL $SORT_ORDER");

        $CHANGES_COUNT = db_rowcount($result);

        // table data

        $START = $ITEMS_PER_PAGE * ($CURRENT_PAGE - 1);
        $END   = $START + $ITEMS_PER_PAGE;
        if ($END > db_rowcount($result))
        {
            $END = db_rowcount($result);
        }

        if ($START > 0)
        {
            $result->data_seek($START);
        }

        for ($i = $START; $i < $END; $i++)
        {
            // grab next database row
            $ROW = db_return_row($result);

            // ARE WE DOING A DUPLICATE CLEAN?
            if (isset($_GET["DUPLICATE_CLEAN"]))
            {
                db_query("UPDATE `QURAN-DATA` SET `FIXED_NOTES`='DUPLICATE CLEANED UP:" . $ROW["FIXED_TRANSLITERATION"] . "', `FIXED_TRANSLITERATION`='' WHERE `GLOBAL WORD NUMBER`=" . $ROW["GLOBAL WORD NUMBER"]);
            }

            if (!$OUTPUT_DATABASE)
            {
                echo "<tr>";

                echo "<td align=center width=110>";

                echo "<a href='word_correction_tool.php?W=" . $ROW["GLOBAL WORD NUMBER"] . "&LIGHTVIEW=YES' class='linky lightview' data-lightview-type='iframe' data-lightview-options=\"background: { color: '#ffffff', opacity: 1 }, width: 1000, height: '100%'\">";

                echo highlight_search_result($ROW["GLOBAL WORD NUMBER"], true);

                if ($ROW["FIXED_NOTES"] != "")
                {
                    echo "<br>";
                    echo "<span class=smaller_text_for_mini_dialogs>";
                    echo highlight_search_result($ROW["FIXED_NOTES"], false);
                    echo "</span>";
                }

                echo "</a>";

                // are we applying this change?
                if (is_admin_user($logged_in_user) && $_GET["FILTER"] == "APPROVED_NOT_APPLIED")
                {
                    if ($apply_all > 0)
                    {
                        echo "<br><font color=red>[APPLIED]</font>";
                        apply_change($ROW["GLOBAL WORD NUMBER"]);
                        $apply_all--;
                        $CHANGES_COUNT--;
                    }
                }

                echo "</td>";

                echo "<td align=center width=90><a href='../verse_browser.php?V=" . $ROW["SURA"] . ":" . $ROW["VERSE"] . "&highlight_single_word=" . $ROW["GLOBAL WORD NUMBER"] . "' class=linky>";
                echo $ROW["SURA"] . ":" . $ROW["VERSE"] . ":" . $ROW["WORD"];
                echo "</a>";

                if ($ROW["FIX_CREATED_BY"] > 0)
                {
                    echo "<br>";
                    echo "<span class=smaller_text_for_mini_dialogs>";
                    echo "(Fix created by " . db_return_one_record_one_field("SELECT `First Name` FROM `USERS` WHERE `User ID`=" . db_quote($ROW["FIX_CREATED_BY"])) . ")";
                    echo "</span>";
                }

                echo "</td>";

                echo "<td align=center width=80 class=word-correction-list-arabic>";

                // if there is a previous Arabic rendering recorded, use that, otherwise use the 'current' version

                if ($ROW["PREVIOUS ARABIC"] != "")
                {
                    echo highlight_search_result($ROW["PREVIOUS ARABIC"], false);
                }
                else
                {
                    echo highlight_search_result($ROW["RENDERED ARABIC"], false);
                }

                echo "</td>";

                echo "<td align=center width=110>";

                // if there is a previous transliteration recorded, use that, otherwise use the 'current' version

                if ($ROW["PREVIOUS TRANSLITERATION"] != "")
                {
                    echo highlight_search_result($ROW["PREVIOUS TRANSLITERATION"], false);
                }
                else
                {
                    echo highlight_search_result($ROW["TRANSLITERATED"], false);
                }

                echo "</td>";

                echo "<td align=center width=90 class=word-correction-list-arabic>";
                echo highlight_search_result($ROW["FIXED_ARABIC"], false);
                echo "</td>";

                echo "<td align=center width=110>";

                echo highlight_search_result($ROW["FIXED_TRANSLITERATION"], false);

                echo "</td>";

                echo "<td align=center width=110>";

                echo highlight_search_result($ROW["FIXED_GLOSS"], false);

                echo "</td>";

                echo "<td align=center width=120>";
                if ($ROW["FIXED_DB_APPROVED"] == "Y")
                {
                    echo "Yes";
                    if ($ROW["TRANSLITERATION FIX APPLIED"] != "Y")
                    {
                        echo "<br>";
                        echo "<a href='word_correction_logs.php?UNAPPROVE=" . $ROW["GLOBAL WORD NUMBER"] . "&FILTER=" . $_GET["FILTER"] . "&SORT=" . $_GET["SORT"] . "'>";
                        echo "<button>Unapprove</button>";
                        echo "</a>";
                    }
                    if ($ROW["FIX_APPROVED_BY"] > 0)
                    {
                        echo "<br>";
                        echo "<span class=smaller_text_for_mini_dialogs>";

                        // approved by
                        $approved_by = db_return_one_record_one_field("SELECT `First Name` FROM `USERS` WHERE `User ID`=" . db_quote($ROW["FIX_APPROVED_BY"]));

                        if ($approved_by == "")
                        {
                            $approved_by = db_return_one_record_one_field("SELECT `Email Address` FROM `USERS` WHERE `User ID`=" . db_quote($ROW["FIX_APPROVED_BY"]));
                        }

                        if ($approved_by == "")
                        {
                            $approved_by = "Unknown User";
                        }

                        echo "(Fix approved by $approved_by)";
                        echo "</span>";
                    }
                }
                else
                {
                    echo "No";
                    echo "<br>";

                    // you can't approve your own fixes (unless you are Andy)
                    if ($ROW["FIX_CREATED_BY"] != $_SESSION['UID'] || $_SESSION['UID'] == 1)
                    {
                        echo "<a href='word_correction_logs.php?APPROVE=" . $ROW["GLOBAL WORD NUMBER"] . "&FILTER=" . $_GET["FILTER"] . "&SORT=" . $_GET["SORT"] . "'>";
                        echo "<button>Approve</button>";
                        echo "</a>";
                    }
                    else
                    {
                        echo "<button isabled='disabled' style='opacity:0.5;' title='You cannot approve fixes you yourself created'>Approve</button>";
                    }
                }
                echo "</td>";

                echo "<td align=center width=100>";
                if ($ROW["TRANSLITERATION FIX APPLIED"] == "Y")
                {
                    echo "Yes";
                }
                else
                {
                    echo "No";
                }
                echo "</td>";

                // if user is Andy , we allow the approval of this change from here

                echo "<td width='170' class='actions'>";

                if (is_admin_user($logged_in_user) && $ROW["TRANSLITERATION FIX APPLIED"] != "Y" && $ROW["FIXED_DB_APPROVED"] == "Y")
                {
                    echo "<a href='word_correction_logs.php?APPLY=" . $ROW["GLOBAL WORD NUMBER"] . "&FILTER=" . $_GET["FILTER"] . "&SORT=" . $_GET["SORT"] . "'>";
                    echo "<button><b>Apply Change</b></button>";
                    echo "</a>";
                }
                else
                {
                    echo "<button disabled='disabled' style='opacity:0.5;'>Apply Change</button>";
                }

                // edit correction button (only possible if fix has not yet been applied)

                if ($ROW["FIXED_DB_APPROVED"] != "Y")
                {
                    echo "<a href='word_correction_tool.php?W=" . $ROW["GLOBAL WORD NUMBER"] . "&LIGHTVIEW=YES' class='linky lightview' data-lightview-type='iframe' data-lightview-options=\"background: { color: '#ffffff', opacity: 1 }, width: 1000, height: '100%'\">";
                    echo "<button>Edit Correction <img src='images/edit.gif' class='qt-icon'></button>";
                    echo "</a>";

                    if ($ROW["FIX_CHANGES_SUGGESTED"])
                    {
                        echo "<br><span class=smaller_text_for_mini_dialogs><mark><b>There are some suggested changes to this fix</b></mark></span>";
                    }
                }
                else
                {
                    echo "<button disabled='disabled' style='opacity:0.5;'>Edit Correction <img src='images/edit.gif' class='qt-icon'></button>";
                }

                echo "</td>";

                echo "</tr>";
            }
            else
            {
                // do a database row export instead

                if ($_GET["FILTER"] == "NOT_YET_APPROVED")
                {
                    echo "UPDATE `QURAN-DATA` SET `FIXED_DB_APPROVED`='N', `FIXED_GLOSS`='" . db_quote($ROW["FIXED_GLOSS"]) . "', `FIXED_TRANSLITERATION`='" . db_quote($ROW["FIXED_TRANSLITERATION"]) . "', `FIXED_ARABIC`='" . db_quote($ROW["FIXED_ARABIC"]) . "', `FIX_CREATED_BY`=" . $ROW["FIX_CREATED_BY"] . ", `FIX_APPROVED_BY`=" . $ROW["FIX_APPROVED_BY"] . " WHERE `GLOBAL WORD NUMBER`=" . db_quote($ROW["GLOBAL WORD NUMBER"]) . ";<br>";
                }

                if ($_GET["FILTER"] == "APPROVED_NOT_APPLIED")
                {
                    echo "UPDATE `QURAN-DATA` SET `FIXED_DB_APPROVED`='Y', `FIXED_GLOSS`='" . db_quote($ROW["FIXED_GLOSS"]) . "', `FIXED_TRANSLITERATION`='" . db_quote($ROW["FIXED_TRANSLITERATION"]) . "', `FIXED_ARABIC`='" . db_quote($ROW["FIXED_ARABIC"]) . "', `FIX_CREATED_BY`=" . $ROW["FIX_CREATED_BY"] . ", `FIX_APPROVED_BY`=" . $ROW["FIX_APPROVED_BY"] . " WHERE `GLOBAL WORD NUMBER`=" . db_quote($ROW["GLOBAL WORD NUMBER"]) . ";<br>";
                }
            }
        }

        if (!$OUTPUT_DATABASE)
        {
            echo "<tr><td colspan=10 align=center>";

            if (db_rowcount($result) > 0)
            {
                echo "<div class='message'>Showing Records " . number_format($START + 1) . " to " . number_format($END) . " of " . number_format(db_rowcount($result)) . "</div>";

                if (is_admin_user($logged_in_user) && isset($_GET["FILTER"]))
                {
                    echo "<p>";

                    // only show the APPLY ALL button if we have more than 1 change to apply

                    if ($CHANGES_COUNT > 1)
                    {
                        if ($_GET["FILTER"] == "APPROVED_NOT_APPLIED")
                        {
                            echo "<a href='word_correction_logs.php?APPLY_ALL=$CHANGES_COUNT&FILTER=" . $_GET["FILTER"] . "'><button>Apply All " . number_format($CHANGES_COUNT) . " Changes</button></a>&nbsp;";
                        }
                    }

                    if ($_GET["FILTER"] == "NOT_YET_APPROVED" || $_GET["FILTER"] == "APPROVED_NOT_APPLIED")
                    {
                        echo "<a href='word_correction_logs.php?OUTPUT_DATABASE=Y&FILTER=" . $_GET["FILTER"] . "' target='_blank'><button>Generate SQL to Update Offline Database with Change List</button></a>";
                    }

                    echo "</p>";
                }
            }
            else
            {
                echo "<b><p>&nbsp;No records match your filtering criteria</b></p>";

                // is it because they have applied a search?

                if ($_GET["SEARCH"] != "")
                {
                    echo "<p>";

                    $count_with_just_search = db_return_one_record_one_field("SELECT COUNT(DISTINCT `GLOBAL WORD NUMBER`) FROM `QURAN-DATA` WHERE 1 $SEARCH_SQL");

                    if ($count_with_just_search > 0)
                    {
                        echo "(However, if you ran your search on all records, not just a filtered selection, you would find $count_with_just_search matching record" . plural($count_with_just_search) . ". <a href='word_correction_logs.php?SEARCH=" . $_GET["SEARCH"] . "' class=linky-green>Click here to do this now</a>).";
                    }

                    echo "</p>";
                }
            }
        }

        echo "</b></td></tr>";

        echo "</tbody>";

        echo "</table><br>";

        // insert the page navigator

        $ITEMS_TO_SHOW = db_rowcount($result);
        $pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

        if ($pages_needed > 1)
        {
            if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
            {
                $pages_needed++;
            }

            print_page_navigator($CURRENT_PAGE, $pages_needed, false, "word_correction_logs.php?SORT=" . $_GET["SORT"] . "&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"]);
        }

        include "library/footer.php";

        ?>

        <!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
        <?php
        move_back_to_top_button();

        ?>
</body>

<script>
    Tipped.create('.yellow-tooltip', {
        position: 'bottommiddle',
        maxWidth: 420,
        skin: 'lightyellow',
        showDelay: 1000,
        size: 'large'
    });
</script>

</html>