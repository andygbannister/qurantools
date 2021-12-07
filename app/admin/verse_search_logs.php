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
            window_title("Viewing and Searching Activity");
        ?>
    <script type="text/javascript" src="/library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="/library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript" src="/library/js/persistent_table_headers.js"></script>

    <?php

// remove any records from the database where the referring page was this one (so we don't add logs from here)
db_query("DELETE FROM `USAGE-VERSES-SEARCHES` WHERE `REFERRING PAGE` LIKE '%admin/verse_search_logs.php%'");

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
        $SORT_ORDER = " ORDER BY `LOOKED UP` ASC";
    }
    if ($_GET["SORT"] == "DATA-DESC")
    {
        $SORT_ORDER = " ORDER BY `LOOKED UP` DESC";
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
                            "yAxisName": "Number of Verse Lookups and Searches",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
       // POPULATE THE DATASET

       $result = db_query("
	   SELECT DATE(`DATE AND TIME`) d, COUNT(*) c FROM `USAGE-VERSES-SEARCHES` t1
	   LEFT JOIN `USERS` t2 ON t1.`USER ID`=t2.`User ID`
	   $filterSQL
	   GROUP BY DATE(`DATE AND TIME`)
ORDER BY DATE(`DATE AND TIME`)  ASC
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

echo"<h2 class='page-title-text'>Viewing and Searching Activity</h2>";

// if filtering by name, show that info

if ($filter_by_user)
{
    $user_name = db_return_one_record_one_field("SELECT `User Name` FROM `USERS` WHERE `User ID`='" . db_quote($_GET["U"]) . "'");

    echo "<h3>";
    echo "For: ";

    $user_name = db_return_one_record_one_field("SELECT `User Name` FROM `USERS` WHERE `User ID`='" . db_quote($_GET["U"]) . "'");

    echo show_value_or_missing($user_name, "User Name");

    // more info icon

    if (strtoupper($_SESSION['administrator']) == "SUPERUSER")
    {
        echo "&nbsp;<a href='user_detail.php?USER=" . $_GET["U"] . "&TAB=2'>";
        echo "<img src='../images/info.png' title='Get more info on this user' class='qt-icon'>";
        echo "</a>";
    }

    echo "</h3>";
}

// filtering buttons

echo "<div class='button-block-with-spacing'>";

echo "<a href='verse_search_logs.php?FILTER=ALL&MODE=$MODE'><button";
if ($filterLogs == "ALL" && !$filter_by_user)
{
    echo " style='font-weight:bold;'";
}
echo ">Show All Logs</button></a>";

if ($filter_by_user == "")
{
    echo "<a href='verse_search_logs.php?FILTER=EXCLUDE_ADMIN&MODE=$MODE'><button";
    if ($filterLogs == "EXCLUDE_ADMIN")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Exclude Admins</button></a>";

    echo "<a href='verse_search_logs.php?FILTER=ADMIN_ONLY&MODE=$MODE'><button";
    if ($filterLogs == "ADMIN_ONLY")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Only Show Admins</button></a>";

    echo "<a href='verse_search_logs.php?FILTER=$filterLogs&MODE=TABLE'>";
    echo "<button";
    if ($MODE == "TABLE")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show as Table</button>";
    echo "</a>";

    echo "<a href='verse_search_logs.php?FILTER=$filterLogs&MODE=CHART'>";
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

$sql = "SELECT t1.`ID`, 
               t1.`VERSES OR SEARCH`, 
               t1.`LOOKED UP`, 
               t1.`REFERRING PAGE`, 
               t2.`User ID`,
               t1.`DATE AND TIME`,
               t2.`User Name`,
               t2.`Administrator`
         FROM `USAGE-VERSES-SEARCHES` t1
    LEFT JOIN `USERS` t2 ON t1.`User ID`=t2.`User ID` 
               $filter_by_user $filterSQL $SORT_ORDER";

$result = db_query($sql);

// print the table header

if ($MODE != "CHART")
{
    echo "<table class='hoverTable persist-area fixedTable qt-table' width='1010'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th bgcolor=#c0c0c0 align=center width=60><b>Type</b><br>";
    echo "<a href='verse_search_logs.php?SORT=TYPE-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='verse_search_logs.php?SORT=TYPE-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 width=300><b>Verses Viewed or Search Performed</b><br>";
    echo "<a href='verse_search_logs.php?SORT=DATA-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='verse_search_logs.php?SORT=DATA-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 align=center width=300><b>Referring Page</b><br>";
    echo "<a href='verse_search_logs.php?SORT=REFERRER-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='verse_search_logs.php?SORT=REFERRER-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 align=center width=200><b>User</b><br>";
    echo "<a href='verse_search_logs.php?SORT=USER-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='verse_search_logs.php?SORT=USER-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 align=center width=150><b>Date and Time</b><br>";
    echo "<a href='verse_search_logs.php?SORT=TIMESTAMP-ASC$pass_user_filter'><img src='../images/up.gif'></a> <a href='verse_search_logs.php?SORT=TIMESTAMP-DESC$pass_user_filter'><img src='../images/down.gif'></a>";
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

        echo "<tr id='record-id-" . $ROW["ID"] . "'>";

        // echo "<td align=center width=60>";
        // echo number_format($i + 1);
        // echo "</td>";

        echo "<td width=60>";

        if ($ROW["VERSES OR SEARCH"] == "V")
        {
            echo "Verses";
        }
        else
        {
            echo "Search";
        }

        echo "</td>";

        echo "<td width='300'>";

        if ($ROW["VERSES OR SEARCH"] == "V")
        {
            echo "<a href='../verse_browser.php?V=" . urlencode($ROW["LOOKED UP"]) . "' class=linky>";
        }
        else
        {
            echo "<a href='../verse_browser.php?S=" . urlencode($ROW["LOOKED UP"]) . "' class=linky>";
        }

        if (strlen($ROW["LOOKED UP"]) > 40)
        {
            echo "<span title='" . htmlentities($ROW["LOOKED UP"]) . "'>" . substr(htmlentities(($ROW["LOOKED UP"])), 0, 40) . "</span>";
        }
        else
        {
            echo htmlentities(($ROW["LOOKED UP"]));
        }

        echo "</a>";

        echo "</td>";

        echo "<td width='300' title='" . htmlentities($ROW["REFERRING PAGE"]) . "' class='left-align'>";

        echo $ROW["REFERRING PAGE"];

        echo "</td>";

        echo "<td width='200'>";

        if (!empty($ROW["User ID"]))
        {
            if (!$filter_by_user)
            {
                echo "<a href='verse_search_logs.php?U=" . $ROW["User ID"] . "' class='linky'>";
            }

            echo show_value_or_missing($ROW["User Name"], "User Name");

            if (!$filter_by_user)
            {
                echo "</a>";
            }
        }
        else
        {
            echo 'Orphaned User';
        }

        if ($ROW["Administrator"] == "ADMIN")
        {
            echo " <img src='/images/manager.png' alt='Admin' title='Admin' class='qt-icon'>";
        }
        if ($ROW["Administrator"] == "SUPERUSER")
        {
            echo " <img src='/images/admin-superuser-icon.png' alt='Super Admin' title='Super Admin' class='qt-icon'>";
        }

        echo "</td>";

        echo "<td align=center width=150>";
        echo $ROW["DATE AND TIME"];
        echo "</td>";

        echo "</tr>";
    }

    if (db_rowcount($result) > 0)
    {
        echo "<tr><td colspan=6 align=center><b>Showing Records " . number_format($START + 1) . " to " . number_format($END) . " of " . number_format(db_rowcount($result)) . "</b></td></tr>";
    }
    else
    {
        echo "<tr><td colspan=6 align=center><b><br>No log entries match your criteria<br>&nbsp;</b></td></tr>";
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

        print_page_navigator($CURRENT_PAGE, $pages_needed, true, "verse_search_logs.php?SORT=" . $_GET["SORT"] . "$pass_user_filter&FILTER=$filterLogs");

        echo "</div>";
    }
}

echo "<div id='chartContainer' class='chart-container' align=center";

if ($MODE != "CHART")
{
    echo " style='display: none;'";
}

echo "></div>";

include "library/footer.php";

?>

    <!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
    <?php
move_back_to_top_button();

?>

    </body>

</html>