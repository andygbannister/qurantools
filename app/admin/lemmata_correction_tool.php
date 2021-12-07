<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 500;
$CURRENT_PAGE   = 1;

// MESSAGE
$message         = "";
$OUTPUT_DATABASE = false;

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
            window_title("Lemmata Correction Tool");
        ?>
		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
		
		<script type="text/javascript" src="../library/js/lightview/spinners/spinners.min.js"></script>
		
		<script type="text/javascript" src="../library/js/lightview/lightview/lightview.js"></script>
		
		<script>

	function save_preference(lemmaID, prefName, prefValue)
	{			
		// get rid of any spaces from the alternative lemma field
		if (prefName == "ALTERNATIVE TRANSLITERATION")
		{
			prefValue = prefValue.replace(" ", "");
			$('#ALT_LEMMA_FIELD').val(prefValue);
		}
		
		// take the passback data from ajax (message^username) and report it to the user
		
		$("#floating-message").load("../ajax/ajax_lemma_edit_save.php", {I:lemmaID, F:prefName, V:prefValue}, function() 
		{
			var passback_array = $("#floating-message").html().split("^");	
			
			$("#CHANGED_BY" + lemmaID).html(passback_array[1]);
			
			$("#floating-message").html(passback_array[0]);
					
		});
		
		$('#floating-message').show();
		
		setTimeout(function(){$("#floating-message").hide();}, 1200);	
		
	}
	
	</script>
		
		
		
		<link rel="stylesheet" type="text/css" href="../library/js/lightview/css/lightview/lightview.css"/>
		
		
		
      
	</head>
<body class='qt-site'>
<main class='qt-site-content'>
	<?php

include "library/back_to_top_button.php";

    // sort order
    $SORT_ORDER = "ORDER BY `LEMMA ID` ASC";

    if (!isset($_GET["SORT"]))
    {
        $_GET["SORT"] = "";
    }

    if ($_GET["SORT"] == "ID-ASC")
    {
        $SORT_ORDER = "ORDER BY `LEMMA ID` ASC";
    }

    if ($_GET["SORT"] == "ID-DESC")
    {
        $SORT_ORDER = "ORDER BY `LEMMA ID` DESC";
    }

    if ($_GET["SORT"] == "ARABIC-ASC")
    {
        $SORT_ORDER = "ORDER BY `ARABIC`";
    }

    if ($_GET["SORT"] == "ARABIC-DESC")
    {
        $SORT_ORDER = "ORDER BY `ARABIC` DESC";
    }

    if ($_GET["SORT"] == "ORIGINAL-TRANSLITERATION-ASC")
    {
        $SORT_ORDER = "ORDER BY `ENGLISH TRANSLITERATED` ASC";
    }

    if ($_GET["SORT"] == "ORIGINAL-TRANSLITERATION-DESC")
    {
        $SORT_ORDER = "ORDER BY `ENGLISH TRANSLITERATED` DESC";
    }

    if ($_GET["SORT"] == "CORRECTED-TRANSLITERATION-ASC")
    {
        $SORT_ORDER = "ORDER BY `CORRECTED TRANSLITERATION` ASC";
    }

    if ($_GET["SORT"] == "CORRECTED-TRANSLITERATION-DESC")
    {
        $SORT_ORDER = "ORDER BY `CORRECTED TRANSLITERATION` DESC";
    }

    if ($_GET["SORT"] == "ALTERNATIVE-TRANSLITERATION-ASC")
    {
        $SORT_ORDER = "ORDER BY `ALTERNATIVE TRANSLITERATION` ASC";
    }

    if ($_GET["SORT"] == "ALTERNATIVE-TRANSLITERATION-DESC")
    {
        $SORT_ORDER = "ORDER BY `ALTERNATIVE TRANSLITERATION` DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Lemmata Correction Tool</h2>";

    echo "<div id='floating-message'>Changes Saved</div>";

    // set up bold buttons

    $bold_button_start = ["<b>", "", "", "", "", ""];
    $bold_button_end   = ["</b>", "", "", "", "", ""];

    $FILTER_SQL = "";

    if (isset($_GET["FILTER"]))
    {
        if ($_GET["FILTER"] == "WORK_NEEDED")
        {
            $bold_button_start[0] = "";
            $bold_button_end[0]   = "";

            $bold_button_start[1] = "<b>";
            $bold_button_end[1]   = "</b>";

            $FILTER_SQL = "AND (`CORRECTED TRANSLITERATION`='' OR `CORRECTED TRANSLITERATION` IS NULL) AND (`LEMMA FIX NOT NEEDED`=0 OR `LEMMA FIX NOT NEEDED` IS NULL)";
        }

        if ($_GET["FILTER"] == "TRANSLITERATION_FIXED")
        {
            $bold_button_start[0] = "";
            $bold_button_end[0]   = "";

            $bold_button_start[2] = "<b>";
            $bold_button_end[2]   = "</b>";

            $FILTER_SQL = "AND `CORRECTED TRANSLITERATION`!=''";
        }

        if ($_GET["FILTER"] == "NO_CHANGE_NEEDED")
        {
            $bold_button_start[0] = "";
            $bold_button_end[0]   = "";

            $bold_button_start[3] = "<b>";
            $bold_button_end[3]   = "</b>";

            $FILTER_SQL = "AND `LEMMA FIX NOT NEEDED`=1";
        }

        if ($_GET["FILTER"] == "ALTERNATIVE_TRANSLITERATION")
        {
            $bold_button_start[0] = "";
            $bold_button_end[0]   = "";

            $bold_button_start[4] = "<b>";
            $bold_button_end[4]   = "</b>";

            $FILTER_SQL = "AND `ALTERNATIVE TRANSLITERATION`!=''";
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
            if (strtoupper($_GET["SEARCH"]) == "HAMZA")
            {
                $SEARCH_SQL = "AND (`ENGLISH` LIKE '%\'%' OR `ENGLISH` LIKE '%>%' OR `ENGLISH` LIKE '%<%' OR `ENGLISH` LIKE '%&%' OR `ENGLISH` LIKE '%}%')";
            }
            else
            {
                $_GET["SEARCH"] = db_quote($_GET["SEARCH"]);
                $SEARCH_SQL     = " AND (`LEMMA ID`='" . $_GET["SEARCH"] . "' OR `ENGLISH TRANSLITERATED` LIKE '%" . $_GET["SEARCH"] . "%' OR `ARABIC` LIKE '%" . $_GET["SEARCH"] . "%' OR `CORRECTED TRANSLITERATION` LIKE '%" . $_GET["SEARCH"] . "%' OR `ALTERNATIVE TRANSLITERATION` LIKE '%" . $_GET["SEARCH"] . "%')";
            }
        }
    }
    else
    {
        $_GET["SEARCH"] = "";
    }

    // filtering buttons

        echo "<div align=center class='button-block-with-spacing'>";
        echo "<a href='lemmata_correction_tool.php?SEARCH=" . $_GET["SEARCH"] . "'><button>" . $bold_button_start[0] . "Show<br>All</button>" . $bold_button_end[0] . "</a>";
        echo "<a href='lemmata_correction_tool.php?FILTER=WORK_NEEDED&SEARCH=" . $_GET["SEARCH"] . "'><button>" . $bold_button_start[1] . "Filter: Neither Corrected<br>Nor Tagged No Change" . $bold_button_end[1] . "</button></a>";
        echo "<a href='lemmata_correction_tool.php?FILTER=TRANSLITERATION_FIXED&SEARCH=" . $_GET["SEARCH"] . "'><button>" . $bold_button_start[2] . "Filter: Corrected<br>Transliteration Supplied" . $bold_button_end[2] . "</button></a>";
        echo "<a href='lemmata_correction_tool.php?FILTER=NO_CHANGE_NEEDED&SEARCH=" . $_GET["SEARCH"] . "'><button>" . $bold_button_start[3] . "Filter: Tagged<br>No Change Needed" . $bold_button_end[3] . "</button></a>";
        echo "<a href='lemmata_correction_tool.php?FILTER=ALTERNATIVE_TRANSLITERATION&SEARCH=" . $_GET["SEARCH"] . "'><button>" . $bold_button_start[4] . "Filter: Alternative<br>Transliteration Supplied" . $bold_button_end[4] . "</button></a>";

                echo " <span class=\"yellow-tooltip\" title='Search for particular word corrections'>";
        echo "<img src='/images/mag.png' onClick=\"$('#search_div').toggle(); inputText.focus();\">";
        echo "</span>";

        echo "</div>";

        echo "<div id=search_div ";
        if ($SEARCH_SQL == "")
        {
            echo "style='display: none;'";
        }
        echo ">";

        echo "<form id=pickVerse action='lemmata_correction_tool.php' method=get name=FormName}>";

        echo "<input NAME=FILTER type=hidden value='" . $_GET["FILTER"] . "'>";

        echo "<input id='inputText' type=text style='font-size:14px' autofocus NAME=SEARCH size=70 maxlength=40 autocomplete='off' ";
        if ($SEARCH_SQL == "")
        {
            echo "placeholder='Search for a lemma (Arabic, original, or corrected transliteration)'";
        }
        else
        {
            echo "value='" . htmlspecialchars(stripslashes($_GET["SEARCH"]), ENT_QUOTES) . "'";
        }

        echo ">";

        echo " <button name=OKbutton style='font-size:14px' type=submit>SEARCH</button>";

        if ($SEARCH_SQL != "")
        {
            echo "<a href='lemmata_correction_tool.php?FILTER=" . $_GET["FILTER"] . "'><button name=WIPE value=wipe type=wipe>(Clear Search Criteria)</button></a>";
        }

        echo "</form></div>";

        if ($message != "")
        {
            echo "<div class='message message-success message-at-top-of-page-after-action'>$message</div>";
        }

        echo "<table class='hoverTable persist-area fixedTable' width=950>";

        // table header

        echo "<thead>";

        echo "<tr class='persist-header table-header-row fixedTable' width=200>";

        echo "<th width=110><b>Lemma Number</b><br><a href='lemmata_correction_tool.php?SORT=ID-ASC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/up.gif'></a> <a href='lemmata_correction_tool.php?SORT=ID-DESC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/down.gif'></a></th>";

        echo "<th width=80><b>Original<br>Arabic</b><br><a href='lemmata_correction_tool.php?SORT=ARABIC-ASC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/up.gif'></a> <a href='lemmata_correction_tool.php?SORT=ARABIC-DESC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/down.gif'></a></th>";

        echo "<th width=110><b>Original<br>Transliteration</b><br><a href='lemmata_correction_tool.php?SORT=ORIGINAL-TRANSLITERATION-ASC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/up.gif'></a> <a href='lemmata_correction_tool.php?SORT=ORIGINAL-TRANSLITERATION-DESC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/down.gif'></a></th>";

        echo "<th width=156><b>Corrected<br>Transliteration</b><br><span class=smaller_text_for_mini_dialogs>Remember, lemmata transliterations appear in things like the Instant Details Palette. They don't affect how a Qur'an verse is displayed in the browser window.</span><br><a href='lemmata_correction_tool.php?SORT=CORRECTED-TRANSLITERATION-ASC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/up.gif'></a> <a href='lemmata_correction_tool.php?SORT=CORRECTED-TRANSLITERATION-DESC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/down.gif'></a></th>";

        echo "<th width=156><b>Alternative<br>Transliteration</b><br><span class=smaller_text_for_mini_dialogs>Other possible transliterations a user might try and search for (separate multiple entries with a comma)</span><br><a href='lemmata_correction_tool.php?SORT=ALTERNATIVE-TRANSLITERATION-ASC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/up.gif'></a> <a href='lemmata_correction_tool.php?SORT=ALTERNATIVE-TRANSLITERATION-DESC&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"] . "'><img src='../images/down.gif'></a></th>";

        echo "<th width=100><b>No Change Needed</b><br><span class=smaller_text_for_mini_dialogs>Please check this box for a lemma if it's correct and needs no fixing.</span></th>";

        echo "<th width=110><b>Last Change Made By</b></th>";

        echo "</tr>";

        echo "</thead>";

        echo "<tbody>";

    $result = db_query("SELECT * FROM `LEMMA-LIST` WHERE 1 $FILTER_SQL $SEARCH_SQL $SORT_ORDER");

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

        echo "<tr>";

        echo "<td align=center width=110>";

        echo $ROW["LEMMA ID"];

        echo "</td>";

        echo "<td align=center width=80 class=word-correction-list-arabic>";

        echo $ROW["ARABIC"];

        echo "</td>";

        echo "<td align=center width=110>";

        echo $ROW["ENGLISH TRANSLITERATED"];

        echo "</td>";

        echo "<td align=center width=156>";

        echo "<input value='" . htmlspecialchars($ROW["CORRECTED TRANSLITERATION"], ENT_QUOTES) . "' width=30 max=30 onChange=\"save_preference('" . $ROW["LEMMA ID"] . "','CORRECTED TRANSLITERATION', this.value);\">";

        echo "</td>";

        echo "<td align=center width=156>";

        echo "<input id=ALT_LEMMA_FIELD value='" . htmlspecialchars($ROW["ALTERNATIVE TRANSLITERATION"], ENT_QUOTES) . "' width=30 max=30 onChange=\"save_preference('" . $ROW["LEMMA ID"] . "','ALTERNATIVE TRANSLITERATION', this.value);\">";

        echo "</td>";

        echo "<td align=center width=100>";

        echo "<input type=checkbox ";

        if ($ROW["LEMMA FIX NOT NEEDED"] == 1)
        {
            echo "checked ";
        }

        echo "onChange=\"save_preference('" . $ROW["LEMMA ID"] . "','LEMMA FIX NOT NEEDED', this.checked);\">";

        echo "</td>";

        echo "<td align=center width=120>";

        echo "<span id=CHANGED_BY" . $ROW["LEMMA ID"] . ">";

        if ($ROW["LEMMA FIXED BY USER"] > 0)
        {
            echo mysqli_return_one_record_one_field("SELECT `User Name` FROM `USERS` WHERE `User ID`=" . db_quote($ROW["LEMMA FIXED BY USER"]));
        }

        echo "</span>";

        echo "</td>";

        echo "</tr>";
    }

    echo "<tr><td colspan=10 align=center>";

    if (db_rowcount($result) > 0)
    {
        echo "<div class='message'>Showing Records " . number_format($START + 1) . " to " . number_format($END) . " of " . number_format(db_rowcount($result)) . "</div>";

        if ( is_admin_user($logged_in_user) && isset($_GET["FILTER"]))
        {
            echo "<p>";

            // only show the APPLY ALL button if we have more than 1 change to apply

            if ($CHANGES_COUNT > 1)
            {
                if ($_GET["FILTER"] == "APPROVED_NOT_APPLIED")
                {
                    echo "<a href='lemmata_correction_tool.php?APPLY_ALL=$CHANGES_COUNT&FILTER=" . $_GET["FILTER"] . "'><button>Apply All " . number_format($CHANGES_COUNT) . " Changes</button></a>&nbsp;";
                }
            }

            if ($_GET["FILTER"] == "NOT_YET_APPROVED" || $_GET["FILTER"] == "APPROVED_NOT_APPLIED")
            {
                echo "<a href='lemmata_correction_tool.php?OUTPUT_DATABASE=Y&FILTER=" . $_GET["FILTER"] . "' target='_blank'><button>Generate SQL to Update Offline Database with Change List</button></a>";
            }

            echo "</p>";
        }
    }
    else
    {
        echo "<b><p>&nbsp;No records match your filtering criteria</b></p>";
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

    print_page_navigator($CURRENT_PAGE, $pages_needed, false, "lemmata_correction_tool.php?SORT=" . $_GET["SORT"] . "&FILTER=" . $_GET["FILTER"] . "&SEARCH=" . $_GET["SEARCH"]);
}

    // print footer

    include "library/footer.php";

?>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>
	</body>
	
	<script>
		 Tipped.create('.yellow-tooltip', {position: 'bottommiddle', maxWidth: 420, skin: 'lightyellow', showDelay: 1000, size: 'large'});
    </script>
</html>