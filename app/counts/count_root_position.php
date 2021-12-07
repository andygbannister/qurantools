<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/transliterate.php';

// preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// filter by root count?

$filter_root_count = 1;

if (isset($_GET["ROOT_COUNT"]))
{
    $filter_root_count = $_GET["ROOT_COUNT"];
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
            window_title("Analyse Root Position in Verse");
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
   $SORT_ORDER = "`COUNT` DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
        $sort         = "";
    }

    if ($sort == "FIRST-COUNT-ASC")
    {
        $SORT_ORDER = "`COUNT FIRST` ASC";
    }

    if ($sort == "FIRST-COUNT-DESC")
    {
        $SORT_ORDER = "`COUNT FIRST` DESC";
    }

    if ($sort == "FIRST-PERCENT-ASC")
    {
        $SORT_ORDER = "`PERCENTAGE_FIRST` ASC";
    }

    if ($sort == "FIRST-PERCENT-DESC")
    {
        $SORT_ORDER = "`PERCENTAGE_FIRST` DESC";
    }

    if ($sort == "MIDDLE-COUNT-ASC")
    {
        $SORT_ORDER = "`COUNT MIDDLE` ASC";
    }

    if ($sort == "MIDDLE-COUNT-DESC")
    {
        $SORT_ORDER = "`COUNT MIDDLE` DESC";
    }

    if ($sort == "MIDDLE-PERCENT-ASC")
    {
        $SORT_ORDER = "`PERCENTAGE_MIDDLE` ASC";
    }

    if ($sort == "MIDDLE-PERCENT-DESC")
    {
        $SORT_ORDER = "`PERCENTAGE_MIDDLE` DESC";
    }

    if ($sort == "FINAL-COUNT-ASC")
    {
        $SORT_ORDER = "`COUNT LAST` ASC";
    }

    if ($sort == "FINAL-COUNT-DESC")
    {
        $SORT_ORDER = "`COUNT LAST` DESC";
    }

    if ($sort == "FINAL-PERCENT-ASC")
    {
        $SORT_ORDER = "`PERCENTAGE_LAST` ASC";
    }

    if ($sort == "FINAL-PERCENT-DESC")
    {
        $SORT_ORDER = "`PERCENTAGE_LAST` DESC";
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
        $SORT_ORDER = "`COUNT`";
    }

    if ($sort == "ALL-DESC")
    {
        $SORT_ORDER = "`COUNT` DESC";
    }

    if ($sort == "FORMULA-ASC")
    {
        $SORT_ORDER = "`Appears in Formulae`";
    }
    if ($sort == "FORMULA-DESC")
    {
        $SORT_ORDER = "`Appears in Formulae` DESC";
    }

    if ($sort == "CHANGES-ASC")
    {
        $SORT_ORDER = "`AFFECTED BY CHANGES`";
    }
    if ($sort == "CHANGES-DESC")
    {
        $SORT_ORDER = "`AFFECTED BY CHANGES` DESC";
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

    echo "<div align=center><h2 class='page-title-text'>Analyse Root Position in Verse</h2>";

    echo "<div class='button-block-with-spacing'>";

    echo "<form action='count_root_position.php' method=GET>";

    echo "<input name=SORT type=hidden value='" . $_GET["SORT"] . "'>";

    echo "<div class='formulaic-pick-table'><table>";

    echo "<tr>";

    echo "<td>Only show roots with at least this many occurences</td><td>";
    echo "<select name=ROOT_COUNT onChange='this.form.submit();'>";

    for ($i = 1; $i <= 10; $i++)
    {
        echo "<option value='$i'";
        if ($filter_root_count == $i)
        {
            echo " selected";
        }
        echo ">$i</option>";
    }

    for ($i = 15; $i <= 100; $i += 5)
    {
        echo "<option value='$i'";
        if ($filter_root_count == $i)
        {
            echo " selected";
        }
        echo ">$i</option>";
    }

    for ($i = 200; $i <= 2000; $i += 100)
    {
        echo "<option value='$i'";
        if ($filter_root_count == $i)
        {
            echo " selected";
        }
        echo ">" . number_format($i) . "</option>";
    }

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    echo "</div>";

    $result = db_query("SELECT `ROOT ID`, `ENGLISH`, `ARABIC`, `ENGLISH TRANSLITERATED`, `COUNT`, 
    `COUNT FIRST`, ((`COUNT FIRST` / `COUNT`) * 100) PERCENTAGE_FIRST,
    `COUNT MIDDLE`, ((`COUNT MIDDLE` / `COUNT`) * 100) PERCENTAGE_MIDDLE,
    `COUNT LAST`, ((`COUNT LAST` / `COUNT`) * 100) PERCENTAGE_LAST
     FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count) . " ORDER BY $SORT_ORDER");

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'><th rowspan=3 width=60>&nbsp;</th><th align=center colspan=2><b>Root</b></th>";

    echo "<th rowspan=3>Total Occurrences<br>of Root<br><a href='count_root_position.php?SORT=ALL-ASC&&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=ALL-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

    echo "<th colspan=6 bgcolor=#c0c0c0 align=center><b>Position Counts</b></th>";

    echo "</tr>";
    echo "<tr><th width=50 rowspan=2><b>Arabic</b><br><a href='count_root_position.php?SORT=A-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=A-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th><th width=100 rowspan=2><b>Transliteration</b><br><a href='count_root_position.php?SORT=E-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=E-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>
	<th colspan=2><b>First Word<br>in Verse</b></th>
	<th colspan=2><b>Middle of Verse<br>(e.g. not first or last)</b></th>
	<th colspan=2><b>Final Word<br>in Verse</b></th>";

        echo "</tr>";

        echo "<tr>";

        echo "<th>Count<br><a href='count_root_position.php?SORT=FIRST-COUNT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=FIRST-COUNT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

        echo "<th>Percentage<br><a href='count_root_position.php?SORT=FIRST-PERCENT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=FIRST-PERCENT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

        echo "<th>Count<br><a href='count_root_position.php?SORT=MIDDLE-COUNT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=MIDDLE-COUNT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

        echo "<th>Percentage<br><a href='count_root_position.php?SORT=MIDDLE-PERCENT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=MIDDLE-PERCENT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

        echo "<th>Count<br><a href='count_root_position.php?SORT=FINAL-COUNT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=FINAL-COUNT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

        echo "<th>Percentage<br><a href='count_root_position.php?SORT=FINAL-PERCENT-ASC&ROOT_COUNT=$filter_root_count'><img src='../images/up.gif'></a> <a href='count_root_position.php?SORT=FINAL-PERCENT-DESC&ROOT_COUNT=$filter_root_count'><img src='../images/down.gif'></a></th>";

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

        $AHREF_LIMITED = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "' class=linky>";

        $AHREF_MECCAN = "<a href='../verse_browser.php?S=(ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . " AND PROVENANCE:Meccan) ' class=linky>";

        $AHREF_MEDINAN = "<a href='../verse_browser.php?S=(ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . " AND PROVENANCE:Medinan) ' class=linky>";

        echo "<tr>";
        echo "<td align=center width=60>" . ($i + 1) . "</td>";
        echo "<td align=center width=50>$AHREF" . $ROW["ARABIC"] . "</a></td>";
        echo "<td align=center width=100 class='$user_preference_transliteration_style'>$AHREF" . htmlentities($ROW["ENGLISH TRANSLITERATED"]) . "</a></td>";

        // total count

        echo "<td align=center>" . number_format($ROW["COUNT"]) . "</td>";

        // postitions

        if ($ROW["COUNT FIRST"] > 0)
        {
            $link_pos_open  = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:FIRST]' class=linky>";
            $link_pos_close = "</a>";
        }
        else
        {
            $link_pos_open  = "";
            $link_pos_close = "";
        }

        echo "<td align=center>$link_pos_open" . number_format($ROW["COUNT FIRST"]) . "$link_pos_close</td>";
        echo "<td align=center>$link_pos_open" . number_format($ROW["PERCENTAGE_FIRST"], 2) . "%$link_pos_close</td>";

        if ($ROW["COUNT MIDDLE"] > 0)
        {
            $link_pos_open  = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:MIDDLE]' class=linky>";
            $link_pos_close = "</a>";
        }
        else
        {
            $link_pos_open  = "";
            $link_pos_close = "";
        }

        echo "<td align=center>$link_pos_open" . number_format($ROW["COUNT MIDDLE"]) . "$link_pos_close</td>";
        echo "<td align=center>$link_pos_open" . number_format($ROW["PERCENTAGE_MIDDLE"], 2) . "%$link_pos_close</td>";

        if ($ROW["COUNT LAST"] > 0)
        {
            $link_pos_open  = "<a href='../verse_browser.php?S=ROOT:" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "@[POSITION:FINAL]' class=linky>";
            $link_pos_close = "</a>";
        }
        else
        {
            $link_pos_open  = "";
            $link_pos_close = "";
        }

        echo "<td align=center>$link_pos_open" . number_format($ROW["COUNT LAST"]) . "$link_pos_close</td>";
        echo "<td align=center>$link_pos_open" . number_format($ROW["PERCENTAGE_LAST"], 2) . "%$link_pos_close</td>";

        echo "<tr>";
    }

    // do we show a totals rot

    if ($i == db_rowcount($result))
    {
        echo "<tr>";

        echo "<td colspan=3>&nbsp;</b>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT`) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count))) . "</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT FIRST`) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count))) . "</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT (SUM(`COUNT FIRST`) * 100/ SUM(`COUNT`)) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count)), 2) . "%</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT MIDDLE`) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count))) . "</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT (SUM(`COUNT MIDDLE`) * 100/ SUM(`COUNT`)) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count)), 2) . "%</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT LAST`) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count))) . "</b></td>";

        echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT (SUM(`COUNT LAST`) * 100/ SUM(`COUNT`)) FROM `ROOT-LIST` WHERE `COUNT`>=" . db_quote($filter_root_count)), 2) . "%</b></td>";

        echo "</tr>";
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

    print_page_navigator($CURRENT_PAGE, $pages_needed, false, "count_root_position.php?SORT=" . $_GET["SORT"] . "&ROOT_COUNT=$filter_root_count");
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