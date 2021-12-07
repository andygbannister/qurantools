<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Root Usage by Sura");
        ?>
		<script type="text/javascript" src="library/js/lightview/spinners/spinners.min.js"></script>
		<script type="text/javascript" src="library/js/lightview/lightview/lightview.js"></script>
		<link rel="stylesheet" type="text/css" href="library/js/lightview/css/lightview/lightview.css"/>
       
		<script type="text/javascript" src="library/js/persistent_table_headers.js"></script>
	  
		<!-- use jQuery to make the two tables match in size -->
        <script type="text/javascript">
	  	$(document).ready(function() {
            if ($('#mainTable').width() > $('#summaryTable').width())
            {
                $('#summaryTable').attr('width', $('#mainTable').width());
            }
            else
            {
                $('#mainTable').attr('width', $('#summaryTable').width());
            }
        });
	  </script>
	  
	</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

    include "library/back_to_top_button.php";

    // menubar

    include "library/menu.php";

    // sort order

    $SORT_ORDER = "`Sura Number` ASC";

    if (isset($_GET["SORT"]))
    {
        if ($_GET["SORT"] == "SURA-DESC")
        {
            $SORT_ORDER = "`Sura Number` DESC";
        }

        if ($_GET["SORT"] == "DIFF-ASC")
        {
            $SORT_ORDER = "`Root Count Different`";
        }

        if ($_GET["SORT"] == "DIFF-DESC")
        {
            $SORT_ORDER = "`Root Count Different` DESC";
        }

        if ($_GET["SORT"] == "UNIQUE-ASC")
        {
            $SORT_ORDER = "`Root Count Unique`";
        }

        if ($_GET["SORT"] == "UNIQUE-DESC")
        {
            $SORT_ORDER = "`Root Count Unique` DESC";
        }

        if ($_GET["SORT"] == "HAPAX-ASC")
        {
            $SORT_ORDER = "`Root Count Hapax`";
        }

        if ($_GET["SORT"] == "HAPAX-DESC")
        {
            $SORT_ORDER = "`Root Count Hapax` DESC";
        }

        if ($_GET["SORT"] == "HAPAXPER100-ASC")
        {
            $SORT_ORDER = "`Root Count Hapax`";
        }

        if ($_GET["SORT"] == "HAPAXPER100-DESC")
        {
            $SORT_ORDER = "`Hapax per 100 Words` DESC";
        }
    }
    else
    {
        $_GET["SORT"] = "";
    }

    echo "<div align=center><h2 class='page-title-text'>Root Usage by Sura</h2>";

    $result = db_query("SELECT * FROM `SURA-DATA` ORDER BY $SORT_ORDER");

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable' id='mainTable'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "
	<th bgcolor=#c0c0c0 align=center><b>Sura</b> <a href='browse_root_usage.php?SORT=SURA-ASC'><img src='images/up.gif'></a> <a href='browse_root_usage.php?SORT=SURA-DESC'><img src='images/down.gif'></a></th>
	<th bgcolor=#c0c0c0 align=center><b>Provenance</b></th>
	<th bgcolor=#c0c0c0 align=center>";

    if (!isMobile())
    {
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'charts/chart_root_usage.php?TYPE=1&VIEW=MINI', type: 'post'}\">";
    }

    echo "<a href='charts/chart_root_usage.php?TYPE=1'><img src='images/stats.gif'></a>";

    if (!isMobile())
    {
        echo "</span>";
    }

    echo "<b>Different Roots Used</b> <a href='browse_root_usage.php?SORT=DIFF-ASC'><img src='images/up.gif'></a> <a href='browse_root_usage.php?SORT=DIFF-DESC'><img src='images/down.gif'></a></th>
	<th bgcolor=#c0c0c0 align=center>";

    if (!isMobile())
    {
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'charts/chart_root_usage.php?TYPE=2&VIEW=MINI', type: 'post'}\">";
    }

    echo "<a href='charts/chart_root_usage.php?TYPE=2'><img src='images/stats.gif'></a>";

    if (!isMobile())
    {
        echo "</span>";
    }

    echo "<b>Unique Roots Used</b> <a href='browse_root_usage.php?SORT=UNIQUE-ASC'><img src='images/up.gif'></a> <a href='browse_root_usage.php?SORT=UNIQUE-DESC'><img src='images/down.gif'></a></th>

	<th bgcolor=#c0c0c0 align=center>";

    if (!isMobile())
    {
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'charts/chart_root_usage.php?TYPE=3&VIEW=MINI', type: 'post'}\">";
    }

    echo "<a href='charts/chart_root_usage.php?TYPE=3'><img src='images/stats.gif'></a>";

    if (!isMobile())
    {
        echo "</span>";
    }

    echo " <b>% Unique</b></th>

	<th bgcolor=#c0c0c0 align=center>";

    if (!isMobile())
    {
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'charts/chart_root_usage.php?TYPE=4&VIEW=MINI', type: 'post'}\">";
    }

    echo "<a href='charts/chart_root_usage.php?TYPE=4'><img src='images/stats.gif'></a>";

    if (!isMobile())
    {
        echo "</span>";
    }

    echo " <b>Hapax Legomena</b> <a href='browse_root_usage.php?SORT=HAPAX-ASC'><img src='images/up.gif'></a> <a href='browse_root_usage.php?SORT=HAPAX-DESC'><img src='images/down.gif'></a></th>";

    // <td align=center bgcolor=#c0c0c0><b>Hapax Legomena<br>per 100 Words</b> <a href='browse_root_usage.php?SORT=HAPAXPER100-ASC'><img src='images/up.gif'></a> <a href='browse_root_usage.php?SORT=HAPAXPER100-DESC'><img src='images/down.gif'></a></td>

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    $total_count  = 0;
    $total_unique = 0;

    $total_hapax   = 0;
    $meccan_hapax  = 0;
    $medinan_hapax = 0;

    $medinan_hapax_per_100 = 0;
    $all_hapax_per_100     = 0;
    $meccan_hapax_per_100  = 0;

    $avg_meccan_percent  = 0;
    $avg_medinan_percent = 0;
    $avg_all_percent     = 0;

    $highest_meccan_percent  = 0;
    $highest_medinan_percent = 0;
    $highest_total_percent   = 0;

    $meccan_suras  = 0;
    $medinan_suras = 0;

    $TOTAL_UNIQUE_MECCAN  = 0;
    $TOTAL_UNIQUE_MEDINAN = 0;
    $TOTAL_UNIQUE         = 0;

    $TOTAL_COUNT         = 0;
    $TOTAL_COUNT_MECCAN  = 0;
    $TOTAL_COUNT_MEDINAN = 0;

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td ALIGN=CENTER><a href='counts/count_all_roots.php?SURA=" . $ROW["Sura Number"] . "' class=linky>" . $ROW["Sura Number"] . "</a></td>";

        echo "<td align=center>" . $ROW["Provenance"] . "</td>";

        echo "<td align=center><a href='counts/count_all_roots.php?SURA=" . $ROW["Sura Number"] . "' class=linky>" . $ROW["Root Count Different"] . "</a></td>";
        $TOTAL_COUNT += $ROW["Root Count Different"];

        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_COUNT_MECCAN += $ROW["Root Count Different"];
        }
        else
        {
            $TOTAL_COUNT_MEDINAN += $ROW["Root Count Different"];
        }

        echo "<td align=center>";

        $unique_link = "";
        if ($ROW["Root Count Unique"] > 0)
        {
            $unique_link = "<a href='verse_browser.php?S=[UNIQUE] RANGE:" . $ROW["Sura Number"] . "' class=linky>";
            echo $unique_link;
        }

        echo $ROW["Root Count Unique"];

        if ($ROW["Root Count Unique"] > 0)
        {
            echo "</a>";
        }

        echo "</td>";

        $TOTAL_UNIQUE += $ROW["Root Count Unique"];

        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_UNIQUE_MECCAN += $ROW["Root Count Unique"];
        }
        else
        {
            $TOTAL_UNIQUE_MEDINAN += $ROW["Root Count Unique"];
        }

        $percent = ($ROW["Root Count Unique"] * 100) / $ROW["Root Count Different"];
        echo "<td align=center>$unique_link" . number_format($percent, 2) . "%</a></td>";

        if ($ROW["Provenance"] == "Meccan")
        {
            $avg_meccan_percent += $percent;
            $meccan_suras++;
            if ($percent > $highest_meccan_percent)
            {
                $highest_meccan_percent = $percent;
            }
        }
        else
        {
            $avg_medinan_percent += $percent;
            $medinan_suras++;
            if ($percent > $highest_medinan_percent)
            {
                $highest_medinan_percent = $percent;
            }
        }

        $avg_all_percent += $percent;

        if ($percent > $highest_total_percent)
        {
            $highest_total_percent = $percent;
        }

        // HAPAX

        $total_hapax += $ROW["Root Count Hapax"];
        $all_hapax_per_100 += $ROW["Hapax per 100 Words"];

        if ($ROW["Provenance"] == "Meccan")
        {
            $meccan_hapax += $ROW["Root Count Hapax"];
            $meccan_hapax_per_100 += $ROW["Hapax per 100 Words"];
        }
        else
        {
            $medinan_hapax += $ROW["Root Count Hapax"];
            $medinan_hapax_per_100 += $ROW["Hapax per 100 Words"];
        }

        echo "<td align=center>";

        if ($ROW["Root Count Hapax"] > 0)
        {
            echo "<a href='verse_browser.php?S=[HAPAX] RANGE:" . $ROW["Sura Number"] . "' class=linky>";
        }

        echo $ROW["Root Count Hapax"];

        if ($ROW["Root Count Hapax"] > 0)
        {
            echo "</a>";
        }

        echo "</td>";

        // get word count
        // $words = db_return_one_record_one_field("SELECT count(*) FROM `QURAN-DATA`WHERE `SURA`=".$ROW["Sura Number"]." AND `SEGMENT`=1");
        //$per100 = $ROW["Root Count Hapax"] / ($words / 100);
        // db_query("UPDATE `SURA-DATA` SET `Hapax per 100 Words`=$per100 WHERE `Sura Number`=".$ROW["Sura Number"]);

        // echo "<td align=center>".number_format($ROW["Hapax per 100 Words"], 2)."</td>";

        echo "</tr>";
    }

    echo "</tbody>";

    echo "</table><br>";

    $result_meccan = db_query("SELECT AVG(`Root Count Different`) ac, AVG(`Root Count Unique`) au FROM `SURA-DATA` WHERE `Provenance`='Meccan'");

    $ROW = db_return_row($result_meccan);

    $MECCAN_AVERAGE_DIFFERENT_ROOTS = number_format($ROW["ac"], 2);
    $MECCAN_AVERAGE_UNIQUE_ROOTS    = number_format($ROW["au"], 2);
    $MECCAN_AVERAGE_PERCENT         = number_format($avg_meccan_percent / $meccan_suras, 2);

    $result_medinan = db_query("SELECT AVG(`Root Count Different`) ac, AVG(`Root Count Unique`) au FROM `SURA-DATA` WHERE `Provenance`='Medinan'");

    $ROW = db_return_row($result_medinan);

    $MEDINAN_AVERAGE_DIFFERENT_ROOTS = number_format($ROW["ac"], 2);
    $MEDINAN_AVERAGE_UNIQUE_ROOTS    = number_format($ROW["au"], 2);
    $MEDINAN_AVERAGE_PERCENT         = number_format($avg_medinan_percent / $medinan_suras, 2);

    $result_all = db_query("SELECT AVG(`Root Count Different`) ac, AVG(`Root Count Unique`) au FROM `SURA-DATA`");

    $ROW = db_return_row($result_all);

    $ALL_AVERAGE_DIFFERENT_ROOTS = number_format($ROW["ac"], 2);
    $ALL_AVERAGE_UNIQUE_ROOTS    = number_format($ROW["au"], 2);
    $ALL_AVERAGE_PERCENT         = number_format($avg_all_percent / 114, 2);

    echo "<table class='hoverTable' id='summaryTable'>";
    echo "<tr>";

    echo "<th rowspan=2 bgcolor=#c0c0c0>&nbsp;</th><th colspan=2 align=center bgcolor=#c0c0c0><b>Different Roots Used</b></th><th colspan=2 align=center bgcolor=#c0c0c0 style='border-left: 1px solid #000;'><b>Unique Roots Used</b></th><th colspan=3 align=center bgcolor=#c0c0c0 style='border-left: 1px solid #000;'><b>% Unique</b></th><th colspan=2 align=center bgcolor=#c0c0c0 style='border-left: 1px solid #000;'><b>Hapax Legomena</b></th>";

    echo "</tr>";

    echo "<tr>";

    echo "<th align=center><b>Total</b></th><th align=center><b>Average</b></th><th align=center style='border-left: 1px solid #000;'><b>Total</b></th><th align=center bgcolor=#c0c0c0><b>Average</b></th><th align=center style='border-left: 1px solid #000;'><b>Total</b></th><th align=center bgcolor=#c0c0c0><b>Average</b></th><th align=center><b>Highest</b></th><th align=center style='border-left: 1px solid #000;'><b>Total</b></th><th align=center><b>Average per Sura</b></th>";
    // <td align=center bgcolor=#c0c0c0><b>Average per<br>100 Words</b></td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td>All Suras</td>";
    echo "<td ALIGN=CENTER bgcolor=#f0f0ff><a href='counts/count_all_roots.php' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(DISTINCT(`QTL-ROOT-BINARY`)) FROM `QURAN-DATA` WHERE `QTL-ROOT`!=''")) . "</a></td>";
    echo "<td align=center>$ALL_AVERAGE_DIFFERENT_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff><a href='verse_browser.php?S=[UNIQUE] OR [HAPAX]' style='text-decoration:none;'><font color=black>" . $TOTAL_UNIQUE . "<font></a></td>";
    echo "<td align=center>$ALL_AVERAGE_UNIQUE_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff>" . number_format(($TOTAL_UNIQUE * 100) / $TOTAL_COUNT, 2) . "%</td>";
    echo "<td align=center >$ALL_AVERAGE_PERCENT%</td>";
    echo "<td align=center>" . number_format($highest_total_percent, 2) . "%</td>";
    echo "<td align=center style='border-left: 1px solid #000;'><a href='verse_browser.php?S=[HAPAX]' class=linky>$total_hapax</a></td>";
    echo "<td align=center>" . number_format($total_hapax / 114, 2) . "</td>";
    // 	echo "<td align=center>".number_format($all_hapax_per_100 / 114, 2)."</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Meccan Suras</td>";
    echo "<td ALIGN=CENTER bgcolor=#f0f0ff><a href='counts/count_all_roots.php?SURA=MECCAN' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(DISTINCT(`QTL-ROOT-BINARY`)) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `Sura Number`=`SURA` WHERE `QTL-ROOT`!='' AND `Provenance`='Meccan'")) . "</a></td>";
    echo "<td align=center>$MECCAN_AVERAGE_DIFFERENT_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff><a href='verse_browser.php?S=[UNIQUE] AND PROVENANCE:MECCAN' class=linky>" . number_format($TOTAL_UNIQUE_MECCAN) . "</a></td>";
    echo "<td align=center>$MECCAN_AVERAGE_UNIQUE_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff>" . number_format(($TOTAL_UNIQUE_MECCAN * 100 / $TOTAL_COUNT_MECCAN), 2) . "%</td>";
    echo "<td align=center>$MECCAN_AVERAGE_PERCENT%</td>";
    echo "<td align=center>" . number_format($highest_meccan_percent, 2) . "%</td>";
    echo "<td align=center style='border-left: 1px solid #000;'><a href='verse_browser.php?S=[HAPAX] AND PROVENANCE:MECCAN' class=linky>$meccan_hapax</a></td>";
    echo "<td align=center>" . number_format($meccan_hapax / $meccan_suras, 2) . "</td>";
//	echo "<td align=center>".number_format($meccan_hapax_per_100 / $meccan_suras, 2)."</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Medinan Suras</td>";
    echo "<td ALIGN=CENTER bgcolor=#f0f0ff><a href='counts/count_all_roots.php?SURA=MEDINAN' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(DISTINCT(`QTL-ROOT-BINARY`)) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `Sura Number`=`SURA` WHERE `QTL-ROOT`!='' AND `Provenance`='Medinan'")) . "</a></td>";
    echo "<td align=center>$MEDINAN_AVERAGE_DIFFERENT_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff><a href='verse_browser.php?S=[UNIQUE] AND PROVENANCE:MEDINAN' class=linky>" . number_format($TOTAL_UNIQUE_MEDINAN) . "</a></td>";
    echo "<td align=center>$MEDINAN_AVERAGE_UNIQUE_ROOTS</td>";
    echo "<td ALIGN=CENTER style='border-left: 1px solid #000;' bgcolor=#f0f0ff>" . number_format(($TOTAL_UNIQUE_MEDINAN * 100 / $TOTAL_COUNT_MEDINAN), 2) . "%</td>";
    echo "<td align=center>$MEDINAN_AVERAGE_PERCENT%</td>";
    echo "<td align=center>" . number_format($highest_medinan_percent, 2) . "%</td>";
    echo "<td align=center style='border-left: 1px solid #000;'><a href='verse_browser.php?S=[HAPAX] AND PROVENANCE:MEDINAN' class=linky>$medinan_hapax</a></td>";
    echo "<td align=center>" . number_format($medinan_hapax / $medinan_suras, 2) . "</td>";
//	echo "<td align=center>".number_format($medinan_hapax_per_100 / $medinan_suras, 2)."</td>";
    echo "</tr>";

    echo "</table>";

    echo "</div>";

    // print footer

    include "library/footer.php";

?>

</body>

<script type="text/javascript">

$(function() 
{
	Tipped.create('.chart-tip', {position: 'left', showDelay: 750, skin: 'light', close: true});
});

</script>

</html>