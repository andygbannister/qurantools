<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

// mode
$MODE = "TABLE";
if (isset($_GET["MODE"]))
{
    if ($_GET["MODE"] == "CHART")
    {
        $MODE = "CHART";
    }
}

// filtering

$filterLogs = "ALL";
$filterSQL  = "";
if (isset($_GET["FILTER"]))
{
    $filterLogs = $_GET["FILTER"];
}

if ($filterLogs != "ALL" && $filterLogs != "EXCLUDE_ADMIN" && $filterLogs != "ADMIN_ONLY")
{
    $filterLogs = "ALL";
}

if ($filterLogs == "EXCLUDE_ADMIN")
{
    $filterSQL = " WHERE `Administrator`=''";
}

if ($filterLogs == "ADMIN_ONLY")
{
    $filterSQL = " WHERE `Administrator`!=''";
}

?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Failed Searches");
        ?>
    <script type="text/javascript" src="/library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="/library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript" src="/library/js/persistent_table_headers.js"></script>

    <?php

// remove any records from the database where the referring page was this one (so we don't add logs from here)
db_query("DELETE FROM `USAGE-VERSES-SEARCHES` WHERE `REFERRING PAGE` LIKE '%admin/failed_searches.php%'");

// menubar etc

include "../library/menu.php";
include "../library/colours.php";

// filter by user?
$filter_by_user   = "";
$pass_user_filter = "";
if (isset($_GET["U"]))
{
    $filter_by_user   = " WHERE t1.`User ID`=" . db_quote($_GET["U"]);
    $pass_user_filter = "&U=" . $_GET["U"];
}

// DELETE RECORD?

if (isset($_GET["DELETE"]) && strtoupper($_SESSION['administrator']) == "SUPERUSER")
{
    if ($_GET["DELETE"] > 0)
    {
        $searches_to_remove = db_return_one_record_one_field("SELECT `SEARCH` FROM `FAILED-SEARCHES` WHERE `ID`=" . db_quote($_GET["DELETE"]));

        if ($searches_to_remove != "")
        {
            $count_searches_to_remove = db_return_one_record_one_field("SELECT COUNT(*) FROM `FAILED-SEARCHES` WHERE `SEARCH`='" . db_quote($searches_to_remove) . "'");

            if ($count_searches_to_remove > 0)
            {
                if ($count_searches_to_remove == 1)
                {
                    $message = "Deleted one failed search record that matches '" . $searches_to_remove . "'";
                }
                else
                {
                    $message = "Deleted the " . number_format($count_searches_to_remove) . " failed search records that match '" . $searches_to_remove . "'";
                }

                db_query("DELETE FROM `FAILED-SEARCHES` WHERE `SEARCH`='" . db_quote($searches_to_remove) . "'");
            }
        }
    }
}

// sort order
$SORT_ORDER = " ORDER BY `ID` DESC";

// GET CURRENT PAGE

$ITEMS_PER_PAGE = 500;
$CURRENT_PAGE   = 1;

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

if (isset($_GET["SORT"]))
{
    if ($_GET["SORT"] == "USER-ASC")
    {
        $SORT_ORDER = " ORDER BY `User Name` ASC";
    }
    if ($_GET["SORT"] == "USER-DESC")
    {
        $SORT_ORDER = " ORDER BY `User Name` DESC";
    }

    if ($_GET["SORT"] == "TIMESTAMP-ASC")
    {
        $SORT_ORDER = " ORDER BY `ID` ASC";
    }
    if ($_GET["SORT"] == "TIMESTAMP-DESC")
    {
        $SORT_ORDER = " ORDER BY `ID` DESC";
    }

    if ($_GET["SORT"] == "TYPE-ASC")
    {
        $SORT_ORDER = " ORDER BY `VERSES OR SEARCH` ASC";
    }
    if ($_GET["SORT"] == "TYPE-DESC")
    {
        $SORT_ORDER = " ORDER BY `VERSES OR SEARCH` DESC";
    }

    if ($_GET["SORT"] == "DATA-ASC")
    {
        $SORT_ORDER = " ORDER BY `SEARCH` ASC";
    }
    if ($_GET["SORT"] == "DATA-DESC")
    {
        $SORT_ORDER = " ORDER BY `SEARCH` DESC";
    }

    if ($_GET["SORT"] == "TIMESTAMP-ASC")
    {
        $SORT_ORDER = " ORDER BY `ID` ASC";
    }
    if ($_GET["SORT"] == "TIMESTAMP-DESC")
    {
        $SORT_ORDER = " ORDER BY `ID` DESC";
    }

    if ($_GET["SORT"] == "REFERRER-ASC")
    {
        $SORT_ORDER = " ORDER BY `REFERRING PAGE` ASC";
    }
    if ($_GET["SORT"] == "REFERRER-DESC")
    {
        $SORT_ORDER = " ORDER BY `REFERRING PAGE` DESC";
    }
}
else
{
    $_GET["SORT"] = "";
}

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",
                    "width": "950",
                    "height": "550",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "xAxisName": "Date",
                            "yAxisName": "Number of Failed Searches",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
       // POPULATE THE DATASET

       $result = db_query("
	   SELECT DATE(`TIMESTAMP`) d, COUNT(*) c FROM `FAILED-SEARCHES` t1
	   LEFT JOIN `USERS` t2 ON t1.`USER ID`=t2.`User ID`
	   $filterSQL
	   GROUP BY DATE(`TIMESTAMP`)
ORDER BY DATE(`TIMESTAMP`)  ASC
	   ");
               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result);

                   if ($i > 0)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"" . $ROW["d"] . "\",";

                   echo "\"value\": \"" . number_format($ROW["c"]) . "\",";

                   echo  "\"color\": \"#6060ff\"";

                   echo "}";
               }

       ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <?php

// header

echo "</head><body class='qt-site'><main class='qt-site-content'>";

include "library/back_to_top_button.php";

echo "<div align=center>";

echo"<h2 class='page-title-text'>Failed Searches (i.e. Searches Returning No Hits)</h2>";

if (isset($message))
{
    echo "<p class='bigger-message'><font color=red>" . htmlentities($message) . "</font></p>";
}

// if filtering by name, show that info

if ($filter_by_user)
{
    echo "<div style='margin-top:-15px; margin-bottom: 20px;'>";
    echo "<b>(For User: ";
    echo db_return_one_record_one_field("SELECT `User Name` FROM `USERS` WHERE `User ID`='" . db_quote($_GET["U"]) . "'");
    echo ")</b>";

    // more info icon

    if (strtoupper($_SESSION['administrator']) == "SUPERUSER")
    {
        echo "&nbsp;<a href='user_detail.php?USER=" . $_GET["U"] . "&TAB=2'>";
        echo "<img src='../images/info.gif' title='Get more info on this user'>";
        echo "</a>";
    }

    echo "</div>";
}

// filtering buttons

echo "<div class='button-block-with-spacing'>";

echo "<a href='failed_searches.php?FILTER=ALL&MODE=$MODE'><button";
if ($filterLogs == "ALL" && !$filter_by_user)
{
    echo " style='font-weight:bold;'";
}
echo ">Show All Logs</button></a>";

if ($filter_by_user == "")
{
    echo "<a href='failed_searches.php?FILTER=EXCLUDE_ADMIN&MODE=$MODE'><button";
    if ($filterLogs == "EXCLUDE_ADMIN")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Exclude Admins</button></a>";

    echo "<a href='failed_searches.php?FILTER=ADMIN_ONLY&MODE=$MODE'><button";
    if ($filterLogs == "ADMIN_ONLY")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Only Show Admins</button></a>";

    echo "<a href='failed_searches.php?FILTER=$filterLogs&MODE=TABLE'>";
    echo "<button";
    if ($MODE == "TABLE")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show as Table</button>";
    echo "</a>";

    echo "<a href='failed_searches.php?FILTER=$filterLogs&MODE=CHART'>";
    echo "<button";
    if ($MODE == "CHART")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show as Chart</button>";
    echo "</a>";
}

echo "</div>";

// load dataset

$result = db_query(
    "SELECT t1.*, 
            t2.`User Name`,
            t2.`Administrator` 
       FROM `FAILED-SEARCHES` t1
       LEFT JOIN `USERS` t2 ON t1.`User ID`=t2.`User ID` 
       $filter_by_user $filterSQL $SORT_ORDER"
);

// print the table header

if ($MODE != "CHART")
{
    echo "<div id=TableView>";

    echo "<table class='hoverTable persist-area fixedTable' width=1010>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th bgcolor=#c0c0c0 width=60><b>#</b>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 width=450><b>Search Performed</b><br>";
    echo "<a href='failed_searches.php?SORT=DATA-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='failed_searches.php?SORT=DATA-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 align=center width=250><b>User</b><br>";
    echo "<a href='failed_searches.php?SORT=USER-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='failed_searches.php?SORT=USER-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 align=center width=190><b>Date and Time</b><br>";
    echo "<a href='failed_searches.php?SORT=TIMESTAMP-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='failed_searches.php?SORT=TIMESTAMP-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 width=30>&nbsp;";
    echo "</th>";

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
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td align=center width=60>";
        echo number_format($i + 1);
        echo "</td>";

        echo "<td align=center width=450>";

        echo "<a href='../verse_browser.php?NO_LOG_FAILED&S=" . urlencode($ROW["SEARCH"]) . "' class=linky>";

        if (strlen($ROW["SEARCH"]) > 40)
        {
            echo "<span title='" . htmlentities($ROW["SEARCH"]) . "'>" . mb_substr(htmlentities(($ROW["SEARCH"])), 0, 40) . "</span>";
        }
        else
        {
            echo htmlentities(($ROW["SEARCH"]));
        }

        echo "</a>";

        echo "</td>";

        echo "<td align=center width=250>";
        if (!$filter_by_user)
        {
            echo "<a href='failed_searches.php?U=" . $ROW["USER ID"] . "' class=linky>";
        }

        echo $ROW["User Name"];

        if ($ROW["Administrator"] == "ADMIN")
        {
            echo " <img src='../images/manager.png' alt='Admin' title='Admin' valign=middle>";
        }
        if ($ROW["Administrator"] == "SUPERUSER")
        {
            echo " <img src='../images/admin-superuser-icon.png' alt='Super Admin' title='Super Admin' valign=middle>";
        }

        if (!$filter_by_user)
        {
            echo "</a>";
        }

        echo "</td>";

        echo "<td align=center width=190>";
        echo $ROW["TIMESTAMP"];
        echo "</td>";

        echo "<td align=center width=30>";

        if (strtoupper($_SESSION['administrator']) == "SUPERUSER")
        {
            echo "<a href='failed_searches.php?DELETE=" . $ROW["ID"] . "&SORT=" . $_GET["SORT"] . "'><img src='../images/delete.gif' title='Delete this particular failed search record' onClick=\"return confirm('Are you sure you wish to delete this record? (Click OK to proceed).')\"></a>";
        }
        else
        {
            echo "&nbsp;";
        }
        echo "</td>";

        echo "</tr>";
    }

    if (db_rowcount($result) > 0)
    {
        echo "<tr><td colspan=5 align=center><b>Showing Records " . number_format($START + 1) . " to " . number_format($END) . " of " . number_format(db_rowcount($result)) . "</b></td></tr>";
    }
    else
    {
        echo "<tr><td colspan=5 align=center><b><br>No search records match your criteria<br>&nbsp;</b></td></tr>";
    }

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

        print_page_navigator($CURRENT_PAGE, $pages_needed, true, "failed_searches.php?SORT=" . $_GET["SORT"] . "$pass_user_filter&FILTER=$filterLogs");

        echo "</div>";
    }
}

echo "<div id='chartContainer' class='chart-container' align=center";

if ($MODE != "CHART")
{
    echo " style='display: none;'";
}

echo "></div>";

// print footer

include "library/footer.php";

?>

    <!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
    <?php
move_back_to_top_button();

?>

    </body>

</html>