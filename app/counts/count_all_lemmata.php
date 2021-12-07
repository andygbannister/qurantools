<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/transliterate.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 500;
$CURRENT_PAGE   = 1;

// SHOW COUNTS OR PERCENTAGES
$SHOW = "COUNT";
if (isset($_GET["SHOW"]))
{
    if ($_GET["SHOW"] == "PERCENT")
    {
        $SHOW = "PERCENT";
    }
}

// FILTER BY ROOT
$ROOT    = "";
$ROOTSQL = "";
if (isset($_GET["ROOT"]))
{
    if ($_GET["ROOT"] != "")
    {
        $ROOT    = $_GET["ROOT"];
        $ROOTSQL = "WHERE `ROOT`='" . db_quote($_GET["ROOT"]) . "'";
    }
}
else
{
    $_GET["ROOT"] = "";
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
            window_title("List All Lemmata");
        ?>
		
		<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
		
		<script>
			$(document).ready(function(){
				// Assign the Colorbox event to elements
				$(".iframe").colorbox({iframe:true, width:"80%", height:"90%"});
			
			});
		</script>

	</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

        include "library/back_to_top_button.php";

    // sort order
    $sort       = "C-DESC";
    $SORT_ORDER = "`COUNT` DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($sort == "A-ASC")
    {
        $SORT_ORDER = "`ARABIC`";
    }
    if ($sort == "A-DESC")
    {
        $SORT_ORDER = "`ARABIC` DESC";
    }

    if ($sort == "E-ASC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED`";
    }
    if ($sort == "E-DESC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED` DESC";
    }

    if ($sort == "C-ASC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-ALL`";
        }
        else
        {
            $SORT_ORDER = "`COUNT`";
        }
    }

    if ($sort == "C-DESC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-ALL` DESC";
        }
        else
        {
            $SORT_ORDER = "`COUNT` DESC";
        }
    }

    if ($sort == "ROOT-ASC")
    {
        $SORT_ORDER = "`ROOT`";
    }
    if ($sort == "ROOT-DESC")
    {
        $SORT_ORDER = "`ROOT` DESC";
    }

    if ($sort == "LOAN-ASC")
    {
        $SORT_ORDER = "`AJ FOREIGN PAGE`";
    }

    if ($sort == "LOAN-DESC")
    {
        $SORT_ORDER = "`AJ FOREIGN PAGE` DESC";
    }

    if ($sort == "MEC-ASC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-MECCAN`";
        }
        else
        {
            $SORT_ORDER = "`COUNT MECCAN`";
        }
    }

    if ($sort == "MEC-DESC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-MECCAN` DESC";
        }
        else
        {
            $SORT_ORDER = "`COUNT MECCAN` DESC";
        }
    }

    if ($sort == "MED-ASC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-MEDINAN`";
        }
        else
        {
            $SORT_ORDER = "`COUNT MEDINAN`";
        }
    }

    if ($sort == "MED-DESC")
    {
        if ($SHOW == "PERCENT")
        {
            $SORT_ORDER = "`100-COUNT-MEDINAN` DESC";
        }
        else
        {
            $SORT_ORDER = "`COUNT MEDINAN` DESC";
        }
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>List All Lemmata";

    if ($ROOT != "")
    {
        echo "<font size=3><br>(Based on the root <i>" . transliterate_new($ROOT) . "</i>)</font>";
    }

    echo "</h2>";

    echo "<div class='button-block-with-spacing'>";
    echo "<a href='count_all_lemmata.php?SHOW=COUNT&PAGE=" . $_GET["PAGE"] . "&ROOT=$ROOT&SORT=" . $_GET["SORT"] . "'><button";
    if ($SHOW == "COUNT")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Occurrences</button></a>";

    echo "<a href='count_all_lemmata.php?SHOW=PERCENT&PAGE=" . $_GET["PAGE"] . "&ROOT=$ROOT&SORT=" . $_GET["SORT"] . "'><button";
    if ($SHOW == "PERCENT")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Percentage of All Lemmata</button></a>";

    echo "</div>";

    $sql = "SELECT * FROM `LEMMA-LIST` $ROOTSQL	ORDER BY $SORT_ORDER";

    $result = db_query($sql);

    $grand_total = db_rowcount($result);

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'><th rowspan=2 width=60>&nbsp;</th><th align=center colspan=2><b>Lemma</b></th>
	<th rowspan=2 align=center><b>Root</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=ROOT-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=ROOT-DESC'><img src='../images/down.gif'></a></th>";

    if ($SHOW == "PERCENT")
    {
        echo "<th rowspan=2><b>Count</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=C-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&?SORT=C-DESC'><img src='../images/down.gif'></a></th>";
    }

    echo "<th colspan=3 align=center><b>";

    if ($SHOW == "PERCENT")
    {
        echo "Percentage of All Lemmata";
    }
    else
    {
        echo "Occurrences of Lemmata";
    }

    echo "</b></th><th rowspan=2 width=60>Loanword?<br><a href='count_all_lemmata.php?SHOW=$SHOW&SORT=LOAN-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=LOAN-DESC'><img src='../images/down.gif'></a></th>";

    echo "<th rowspan=2 width=70>&nbsp;</th>";

    echo "</tr>";

    echo "<tr><th><b>Arabic</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=A-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=A-DESC'><img src='../images/down.gif'></a></th><th><b>Transliteration</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=E-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=E-DESC'><img src='../images/down.gif'></a></th>
	
	<th><b>Whole Qur’an</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=C-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=C-DESC'><img src='../images/down.gif'></a></th>
	
	<th><b>Meccan Suras</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=MEC-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=MEC-DESC'><img src='../images/down.gif'></a></th>
	<th><b>Medinan Suras</b>&nbsp;<a href='count_all_lemmata.php?SHOW=$SHOW&SORT=MED-ASC'><img src='../images/up.gif'></a> <a href='count_all_lemmata.php?SHOW=$SHOW&SORT=MED-DESC'><img src='../images/down.gif'></a></th></tr>";

    echo "</thead>";

    echo "<tbody>";

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
        $ROW = $result->fetch_assoc();

        $AHREF = "<a href='../verse_browser.php?S=(LEMMA:" . urlencode($ROW["ARABIC"]) . " OR LEMMA:" . urlencode($ROW["ENGLISH"]) . ")' class=linky>";

        $AHREF_MECCAN = "<a href='../verse_browser.php?S=(LEMMA:" . urlencode($ROW["ARABIC"]) . " OR LEMMA:" . urlencode($ROW["ENGLISH"]) . ") AND PROVENANCE:Meccan' class=linky>";

        $AHREF_MEDINAN = "<a href='../verse_browser.php?S=(LEMMA:" . urlencode($ROW["ARABIC"]) . " OR LEMMA:" . urlencode($ROW["ENGLISH"]) . ") AND PROVENANCE:Medinan' class=linky>";

        echo "<tr>";

        echo "<td align=center width=40>$AHREF" . number_format($i + 1) . "</a></td>";

        echo "<td align=center>$AHREF" . $ROW["ARABIC"] . "</a></td>";

        echo "<td align=center $user_preference_transliteration_style>$AHREF" . $ROW["ENGLISH TRANSLITERATED"] . "</a></td>";

        echo "<td align=center $user_preference_transliteration_style>";

        if ($ROW["ROOT"] == "")
        {
            echo "-";
        }
        else
        {
            echo "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ROOT"]) . "' class=linky>" . convert_buckwalter($ROW["ROOT"]) . "</a>";
        }

        echo "</td>";

        if ($SHOW == "PERCENT")
        {
            echo "<td align=center>$AHREF" . number_format($ROW["COUNT"]) . "</a></td>";
        }

        // per 100 word stuff

        if ($SHOW == "PERCENT")
        {
            echo "<td align=center>$AHREF" . number_format($ROW["100-COUNT-ALL"], 3) . "%</a></td>";

            if ($ROW["100-COUNT-MECCAN"] > 0)
            {
                echo "<td align=center>$AHREF_MECCAN" . number_format($ROW["100-COUNT-MECCAN"], 3) . "%</a></td>";
            }
            else
            {
                echo "<td align=center>" . number_format($ROW["100-COUNT-MECCAN"], 3) . "%</td>";
            }

            if ($ROW["100-COUNT-MEDINAN"] > 0)
            {
                echo "<td align=center>$AHREF_MEDINAN" . number_format($ROW["100-COUNT-MEDINAN"], 3) . "%</a></td>";
            }
            else
            {
                echo "<td align=center>" . number_format($ROW["100-COUNT-MEDINAN"], 3) . "%</td>";
            }
        }
        else
        {
            echo "<td align=center>$AHREF" . number_format($ROW["COUNT"]) . "</a></td>";

            if ($ROW["COUNT MECCAN"] > 0)
            {
                echo "<td align=center>$AHREF_MECCAN" . number_format($ROW["COUNT MECCAN"]) . "</a></td>";
            }
            else
            {
                echo "<td align=center>" . number_format($ROW["COUNT MECCAN"]) . "</td>";
            }

            if ($ROW["COUNT MEDINAN"] > 0)
            {
                echo "<td align=center>$AHREF_MEDINAN" . number_format($ROW["COUNT MEDINAN"]) . "</a></td>";
            }
            else
            {
                echo "<td align=center>" . number_format($ROW["COUNT MEDINAN"]) . "</td>";
            }
        }

        // foreign word (from Arthur Jeffery)

        echo "<td width=60 align=center>";

        if ($ROW["AJ FOREIGN PAGE"] > 0)
        {
            echo "Yes ";
            echo "<a href='#.'>";  // this prevents scrolling when clicking
            echo "<img src='/images/jeffery_small.png' border=0 width=15 height=20 valign=middle onclick='$.colorbox({href:\"/dictionary/jeffery.php?PAGE=" . (14 + $ROW["AJ FOREIGN PAGE"]) . "&LIGHTVIEW=YES\",width:\"90%\", height:\"95%\"});'>";
            echo "</a>";
        }
        else
        {
            echo "&nbsp;";
        }
        echo "</td>";

        // action buttons

        echo "<td width=70>";

        if ($ROW["ROOT"] != "")
        {
            echo "<a title='Examine root' href='../examine_root.php?ROOT=" . urlencode($ROW["ROOT"]) . "'><img src='../images/info.gif'></a>&nbsp;";
        }
        else
        {
            // if this lemma exists in the dictionary, we show a link
            if (db_return_one_record_one_field("SELECT COUNT(*) FROM `DICTIONARY-ENTRIES` WHERE `ARABIC`='" . db_quote($ROW["ARABIC"]) . "' AND `TYPE`='LEMMA' AND `MEANING`!=''") > 0)
            {
                echo " <a href='../dictionary.php?S=" . $ROW["ARABIC"] . "'><img src='/images/info.gif'></a>&nbsp;";
            }
            else
            {
                echo "<img src='../images/info_white.gif'>&nbsp;";
            }
        }

        // list exhaustively icon

        echo "&nbsp;";
        echo "<a title='Exhaustively list all occurrences of this lemma' href='../exhaustive_root_references.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "&BACK=Return to Lemma List'>";
        echo "<img src='/images/context_v.gif'></a>";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_lemmata.php?VIEW=MINI&LEMMA=" . urlencode($ROW["ENGLISH"]) . "', type: 'post'}\">";
        }

        echo "<a href='../charts/chart_lemmata.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "'><img src='../images/stats.gif'></a></td>";

        if (!isMobile())
        {
            echo "</span>";
        }

        echo "</tr>";
    }

    if ($i >= (db_rowcount($result) - 1))
    {
        echo "<tr><td>&nbsp;</td><td align=center colspan=2><b>" . number_format($grand_total) . " lemmata</b></td><td></td><td></td><td></td><td></td><td></td><td></td>";

        // if we are showing percentages, we need one more column
        if ($SHOW == "PERCENT")
        {
            echo "<td></td>";
        }

        echo "<tr>";
    }

    echo "<tbody>";

    echo "</table>";

    echo "</div><br>";

// insert the page navigator

$ITEMS_TO_SHOW = db_rowcount($result);
$pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

if ($pages_needed > 1)
{
    if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
    {
        $pages_needed++;
    }

    print_page_navigator($CURRENT_PAGE, $pages_needed, true, "count_all_lemmata.php?SHOW=$SHOW&ROOT=$ROOT&SORT=" . $_GET["SORT"]);
}

// print footer

include "../library/footer.php";

?>
	</body>
	
	<script type="text/javascript">
  $(function() {
    Tipped.create('.chart-tip', {position: 'left', showDelay: 750, skin: 'light', close: true});
  });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>
	
</html>