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

// filter by sura?

$filter_text        = "";
$filter_by_sura     = "";
$local_count_sql    = "";
$limit_search_range = "";

// SHOW COUNTS OR PERCENTAGES
$SHOW = "COUNT";
if (isset($_GET["SHOW"]))
{
    if ($_GET["SHOW"] == "PERCENT")
    {
        $SHOW = "PERCENT";
    }
}

if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] >= 1 && $_GET["SURA"] <= 114)
    {
        $filter_by_sura = "WHERE (SELECT COUNT(*) FROM `QURAN-DATA` WHERE `Sura`=" . db_quote($_GET["SURA"]) . " AND `QTL-ROOT-BINARY`=`ENGLISH-BINARY`) > 0";

        $local_count_sql = ", (SELECT COUNT(*) FROM `QURAN-DATA` WHERE `Sura`=" . db_quote($_GET["SURA"]) . " AND `QTL-ROOT-BINARY`=`ENGLISH-BINARY`) local_count";

        $filter_text = " in Sura " . $_GET["SURA"];

        $limit_search_range = " RANGE:" . $_GET["SURA"];
    }

    if ($_GET["SURA"] == "MECCAN")
    {
        $filter_by_sura = "WHERE 
(SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT-BINARY`=`ENGLISH-BINARY` AND `Provenance`='Meccan') > 0";
        $filter_text = " in Meccan Suras";

        $local_count_sql = ", (SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT-BINARY`=`ENGLISH-BINARY` AND `Provenance`='Meccan') local_count";

        $limit_search_range = " AND PROVENANCE:MECCAN";
    }

    if ($_GET["SURA"] == "MEDINAN")
    {
        $filter_by_sura = "WHERE 
(SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT-BINARY`=`ENGLISH-BINARY` AND `Provenance`='Medinan') > 0";

        $filter_text = " in Medinan Suras";

        $local_count_sql = ", (SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT-BINARY`=`ENGLISH-BINARY` AND `Provenance`='Medinan') local_count";

        $limit_search_range = " AND PROVENANCE:MEDINAN";
    }
}
else
{
    $_GET["SURA"]   = "";
    $filter_text    = "";
    $filter_by_sura = "";
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 400;
$CURRENT_PAGE   = 1;

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
            window_title("List All Roots$filter_text");
        ?>

		<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
			</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

include "library/back_to_top_button.php";

    // sort order
    $sort = "C-DESC";
    if ($local_count_sql == "")
    {
        $SORT_ORDER = "`COUNT` DESC";
    }
    else
    {
        $SORT_ORDER = "local_count DESC";
    }

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($sort == "FIRST-ASC")
    {
        $SORT_ORDER = "`COUNT FIRST` ASC";
    }
    if ($sort == "FIRST-DESC")
    {
        $SORT_ORDER = "`COUNT FIRST` DESC";
    }

     if ($sort == "MIDDLE-ASC")
     {
         $SORT_ORDER = "`COUNT MIDDLE` ASC";
     }
    if ($sort == "MIDDLE-DESC")
    {
        $SORT_ORDER = "`COUNT MIDDLE` DESC";
    }

    if ($sort == "LAST-ASC")
    {
        $SORT_ORDER = "`COUNT LAST` ASC";
    }
    if ($sort == "LAST-DESC")
    {
        $SORT_ORDER = "`COUNT LAST` DESC";
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
        if ($local_count_sql == "")
        {
            $SORT_ORDER = "`COUNT`";
        }
        else
        {
            $SORT_ORDER = "local_count";
        }
    }

    if ($sort == "C-DESC")
    {
        if ($local_count_sql == "")
        {
            $SORT_ORDER = "`COUNT` DESC";
        }
        else
        {
            $SORT_ORDER = "local_count DESC";
        }
    }

    if ($sort == "ALL-ASC")
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

    if ($sort == "ALL-DESC")
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

    if ($sort == "FORMULA-ASC")
    {
        $SORT_ORDER = "`Appears in Formulae`";
    }
    if ($sort == "FORMULA-DESC")
    {
        $SORT_ORDER = "`Appears in Formulae` DESC";
    }

    if ($sort == "UNIQUE-ASC")
    {
        $SORT_ORDER = "`Hapax or Unique`";
    }
    if ($sort == "UNIQUE-DESC")
    {
        $SORT_ORDER = "`Hapax or Unique` DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Listing All Roots$filter_text</h2>";

    if ($local_count_sql != "")
    {
        echo "<div style='margin-top:-20px; margin-bottom: 25px;'><font size=-1><a href='count_all_roots.php' class=linky-light>(Show All Roots)</a></font></div>";
    }

    echo "<div class='button-block-with-spacing'>";
    echo "<a href='count_all_roots.php?SHOW=COUNT&PAGE=" . $_GET["PAGE"] . "&SORT=" . $_GET["SORT"] . "&SURA=" . $_GET["SURA"] . "'><button";
    if ($SHOW == "COUNT")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Occurrences</button></a>";

    echo "<a href='count_all_roots.php?SHOW=PERCENT&PAGE=" . $_GET["PAGE"] . "&SORT=" . $_GET["SORT"] . "&SURA=" . $_GET["SURA"] . "'><button";
    if ($SHOW == "PERCENT")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Percentage of All Roots</button></a>";

    echo "</div>";

    $sql    = "SELECT *" . $local_count_sql . " FROM `ROOT-LIST` $filter_by_sura ORDER BY $SORT_ORDER";
    $result = db_query($sql);

    $grand_total = db_rowcount($result);

    // $grand_total_meccan = mysq l_result(mysq l_query("SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `PROVENANCE`='Meccan'"), 0, 0);

    // $grand_total_medinan = mysq l_result(mysq l_query("SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `PROVENANCE`='Medinan'"), 0, 0);

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'><th rowspan=2 width=60>&nbsp;</th><th align=center colspan=2><b>Root</b></th>";

    if ($SHOW == "PERCENT" || $local_count_sql != "")
    {
        echo "<th rowspan=2><b>Count</b>&nbsp;<a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=C-ASC'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=C-DESC'><img src='../images/down.gif'></a></th>";
    }

    echo "<th colspan=3 bgcolor=#c0c0c0 align=center><b>";

    if ($SHOW == "PERCENT")
    {
        echo "Percentage of All Roots";
    }
    else
    {
        echo "Occurrences of Root";
    }

    if ($_GET["SURA"] != "")
    {
        echo " in the Qur’an";
    }
    echo "</b></th>";

    echo "<th colspan=3 bgcolor=#c0c0c0 align=center><b>";

    echo "Position Counts <a href='count_root_position.php'><img src='/images/table.png' title='Explore positional information in more detail'></a>";

    echo "</th>";

    echo "<th align=center rowspan=2><b>Appearance<br>in Formulae</b>";
    echo "<br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=FORMULA-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=FORMULA-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th align=center rowspan=2 width=150><b>Unique / Hapax</b>";
    echo "<br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=UNIQUE-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=UNIQUE-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th rowspan=2 width=90>&nbsp;</th>";
    echo "</tr>";
    echo "<tr><th width=50><b>Arabic</b><br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=A-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=A-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th><th width=100><b>Transliteration</b><br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=E-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=E-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>
	<th><b>All Suras</b><br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=ALL-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=ALL-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>
	<th><b>Meccan Suras</b><br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MEC-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MEC-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>
	<th><b>Medinan Suras</b><br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MED-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MED-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>";

    echo "<th>First<br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=FIRST-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=FIRST-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>";
    echo "<th>Middle<br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MIDDLE-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=MIDDLE-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>";
    echo "<th>Last<br><a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=LAST-ASC&SHOW=$SHOW'><img src='../images/up.gif'></a> <a href='count_all_roots.php?SURA=" . $_GET["SURA"] . "&SORT=LAST-DESC&SHOW=$SHOW'><img src='../images/down.gif'></a></th>";

    echo "</tr>";

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

        $AHREF = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "' class=linky>";

        $AHREF_LIMITED = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "$limit_search_range' class=linky>";

        $AHREF_MECCAN = "<a href='../verse_browser.php?S=(ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . " AND PROVENANCE:Meccan) ' class=linky>";

        $AHREF_MEDINAN = "<a href='../verse_browser.php?S=(ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . " AND PROVENANCE:Medinan) ' class=linky>";

        echo "<tr>";
        echo "<td align=center width=60>" . ($i + 1) . "</td>";
        echo "<td align=center width=50>$AHREF" . $ROW["ARABIC"] . "</a></td>";
        echo "<td align=center width=100 $user_preference_transliteration_style>$AHREF" . htmlentities(transliterate_new($ROW["ENGLISH"])) . "</a></td>";

        if ($SHOW == "PERCENT" || $local_count_sql != "")
        {
            echo "<td align=center>";

            if ($local_count_sql == "")
            {
                echo "$AHREF" . number_format($ROW["COUNT"]) . "</a>";
            }
            else
            {
                echo $AHREF_LIMITED . number_format($ROW["local_count"]) . "</a>";
            }
            echo "</td>";
        }

        // meccan and medinan --> uncomment the below if we want to calculate values afresh

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

        // position counts

        echo "<td align=center>";
        if ($ROW["COUNT FIRST"] > 0)
        {
            echo "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:FIRST]' class=linky>";
            echo number_format($ROW["COUNT FIRST"]);
            echo "</a>";
        }
        else
        {
            echo "0";
        }
        echo "</td>";

        echo "<td align=center>";
        if ($ROW["COUNT MIDDLE"] > 0)
        {
            echo "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:MIDDLE]' class=linky>";
            echo number_format($ROW["COUNT MIDDLE"]);
            echo "</a>";
        }
        else
        {
            echo "0";
        }
        echo "</td>";

        echo "<td align=center>";
        if ($ROW["COUNT LAST"] > 0)
        {
            echo "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:LAST]' class=linky>";
            echo number_format($ROW["COUNT LAST"]);
            echo "</a>";
        }
        else
        {
            echo "0";
        }
        echo "</td>";

        echo "<td align=center>";
        echo "<a href='../formulae/list_formulae.php?L=3&TYPE=EVERYTHING&ROOT=" . urlencode($ROW["ENGLISH"]) . "' class=linky>";

        // if we haven't got the number saved, we can calculate it on the fly (by changing >=0 to >0 below) — then we save it
        // run a query "UPDATE `ROOT-LIST` SET `Appears in Formulae`=-1" to reset the flags here if we need to

        echo number_format($ROW["Appears in Formulae"]);

        echo "</a>";
        echo "</td>";

        // hapax or unique info

        echo "<td align=center width=150>";

        if ($ROW["Hapax or Unique"] != "")
        {
            if ($_GET["SURA"] > 0 && $_GET["SURA"] < 115)
            {
                if ($ROW["Hapax or Unique"] == "HAPAX")
                {
                    echo $AHREF . "Hapax</a>";
                }

                if ($ROW["Hapax or Unique"] == "UNIQUE")
                {
                    echo $AHREF . "Unique to Sura</a>";
                }
            }
            else
            {
                if ($ROW["Hapax or Unique"] == "HAPAX")
                {
                    echo $AHREF . "Hapax (Sura " . $ROW["Unique to Sura"] . ")</a>";
                }

                if ($ROW["Hapax or Unique"] == "UNIQUE")
                {
                    echo $AHREF . "Unique (Sura " . $ROW["Unique to Sura"] . ")</a>";
                }
            }
        }
        else
        {
            echo "-";
        }

        echo "</td>";

        echo "<td width=90>";
        echo "<a title='Examine root' href='../examine_root.php?ROOT=" . urlencode($ROW["ENGLISH"]) . "'><img src='../images/info.gif'></a>&nbsp;";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_roots.php?VIEW=MINI&ROOT=" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "', type: 'post'}\">";
        }

        echo "<a href='../charts/chart_roots.php?ROOT=" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "'><img src='../images/stats.gif'></a>";

        if (!isMobile())
        {
            echo "</span>";
        }

        // list exhaustively icon

        echo "&nbsp;";
        echo "<a title='Exhaustively list all occurrences of this root' href='../exhaustive_root_references.php?ROOT=" . urlencode($ROW["ENGLISH"]) . "&BACK=Return to Root List'>";
        echo "<img src='/images/context_v.gif'></a>";

        // word associations icon

        echo "&nbsp;";
        echo "<a title='Root/word associations (explore which roots are often used along with this one)' href='../word_associations.php?ROOT=" . urlencode($ROW["ENGLISH"]) . "'><img src='../images/network.gif'></a>";

        echo "</td>";
        echo "</tr>";
    }

    if ($i >= (db_rowcount($result) - 1))
    {
        echo "<tr><td>&nbsp;</td><td align=center COLSPAN=2><b>" . number_format($grand_total) . " roots</b></td><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>";

        // one more column needed if we're showing percentages
        if ($SHOW == "PERCENT" || $local_count_sql != "")
        {
            echo "<td></td>";
        }

        echo "<tr>";
    }

    echo "</tbody>";

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

    print_page_navigator($CURRENT_PAGE, $pages_needed, false, "count_all_roots.php?SORT=" . $_GET["SORT"] . "&SURA=" . $_GET["SURA"] . "&SHOW=$SHOW");
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