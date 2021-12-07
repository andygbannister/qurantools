<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

/**
 * In March 2020, we tried to use DataTables to show this data and lose all the
 * fiddly column sorting. But this didn't work too well since the LOGIN-LOGS
 * table has enough rows in it now that searches should be done server-side on
 * the database, and not client-side. But server-side searches that need joins
 * (to show the user name - which is not stored in LOGIN-LOGS) are difficult
 * to do in the free version of DataTables - and I'm not sure whether it is
 * supported in the paid Editor version either. Work was partially done, and
 * git stashed in "On feature-login-log-search".
 *
 * Apr 2020: This could be solved by using an SQL view meaning DataTables only
 * has to select from one entity on the DB, even if the entity is made up of
 * multiple joined tables.
 *
 * Note: This whole page could do with a big reworking to make it testable
 * and more maintainable
 */

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 500;
$CURRENT_PAGE   = 1;

// mode
$MODE = "TABLE";
if (isset($_GET["MODE"]))
{
    if ($_GET["MODE"] == "CHART")
    {
        $MODE = "CHART";
    }
}

// full or summary
$FULL_OR_SUMMARY = "FULL";
if (isset($_GET["FULL_OR_SUMMARY"]))
{
    if ($_GET["FULL_OR_SUMMARY"] == "SUMMARY")
    {
        $FULL_OR_SUMMARY = "SUMMARY";
    }
}

// filtering

$filterLogs     = "ALL";
$filterSQL      = "";
$filterSQLshort = "";

if (isset($_GET["FILTER"]))
{
    $filterLogs = $_GET["FILTER"];
}

if ($filterLogs != "ALL" && $filterLogs != "EXCLUDE_ADMIN" && $filterLogs != "ADMIN_ONLY")
{
    $filterLogs = "ALL";
}

/**
 * TODO: These NULL/'' checks could just be NULL checks if the '' values in the
 * Administrator column in `Users` were all updated to NULL. In the absence of
 * automated tests, it is risky to do that update as other parts of the code
 * base may be expecting '' rather than NULL.
 */
if ($filterLogs == "EXCLUDE_ADMIN")
{
    $filterSQL      = " WHERE (`Administrator`='' OR `Administrator` IS NULL) ";
    $filterSQLshort = " AND (`Administrator`='' OR `Administrator` IS NULL) ";
}

if ($filterLogs == "ADMIN_ONLY")
{
    $filterSQL      = " WHERE (`Administrator` IS NOT NULL AND `Administrator` != '') ";
    $filterSQLshort = " AND (`Administrator` IS NOT NULL AND `Administrator` != '') ";
}

// Search

$searchSQL  = '';
$searchTerm = '';

if (isset($_GET["SEARCH"]) && !empty($_GET["SEARCH"]))
{
    $searchTerm      = $_GET["SEARCH"];
    $upperSearchTerm = strtoupper($searchTerm);
    $searchSQL       = "(    UPPER(t1.`Email Address`) LIKE '%" . db_quote($upperSearchTerm) . "%' 
                    OR UPPER(`User Name`) LIKE '%" . db_quote($upperSearchTerm) . "%')";

    if (!empty($filterSQL))
    {
        $searchSQL = " AND " . $searchSQL;
    }
    else
    {
        $searchSQL = " WHERE " . $searchSQL;
    }
}

// calculate time period login stats
$TOTAL_NUMBER_OF_USERS = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` $filterSQL");

/**
 * TODO:
 * Pull these SQL queries out and put into some functions - ideally that run in
 * parallel - since they take a while to run. Maybe not worth the effort at this
 * stage since only admin staff use this page:
 *
 * https://www.mullie.eu/parallel-processing-multi-tasking-php/
 * https://www.php.net/manual/en/intro.parallel.php
 */

$LOGGED_IN_LAST_DAY = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL 24 HOUR)" . $filterSQLshort);

$LOGGED_IN_LAST_WEEK = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . (24 * 7) . " HOUR)" . $filterSQLshort);

$LOGGED_IN_LAST_MONTH = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . (30 * 24) . " HOUR)" . $filterSQLshort);

$LOGGED_IN_LAST_QUARTER = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . (90 * 24) . " HOUR)" . $filterSQLshort);

$LOGGED_IN_LAST_SIX_MONTHS = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . (182 * 24) . " HOUR)" . $filterSQLshort);

$LOGGED_IN_LAST_YEAR = db_return_one_record_one_field("SELECT COUNT(*) FROM `USERS` WHERE `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . (365 * 24) . " HOUR)" . $filterSQLshort);

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
<html id='login-logs-page'>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Login Logs");
        ?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>


    <?php
          if ($FULL_OR_SUMMARY == "SUMMARY")
          {
              ?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",
                    "width": "950",
                    "height": "500",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "xAxisName": "Login Period",
                            "yAxisName": "Number of Users Who Logged In",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                                echo "\"data\": [";

              echo "{";
              echo "\"label\": \"" . "Last Day" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_DAY . "\",";
              echo  "\"color\": \"#6060ff\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 1) . "&LABEL=Last Day&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Week" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_WEEK . "\",";
              echo  "\"color\": \"#ff8080\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 7) . "&LABEL=Last Week&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Month" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_MONTH . "\",";
              echo  "\"color\": \"#904040\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 30) . "&LABEL=Last Month&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Quarter" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_QUARTER . "\",";
              echo  "\"color\": \"#20dd30\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 90) . "&LABEL=Last Quarter&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Six Months" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_SIX_MONTHS . "\",";
              echo  "\"color\": \"#aa00aa\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 182) . "&LABEL=Last Six Months&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Year" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_YEAR . "\",";
              echo  "\"color\": \"#ff2050\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 365) . "&LABEL=Last Year&MODE=CHART&FILTER=$filterLogs\"";
              echo "}"; ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "pie2d",
                    "renderAt": "chartContainer2",
                    "width": "950",
                    "height": "500",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "xAxisName": "Login Period",
                            "yAxisName": "Number of Users Who Logged In",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                                echo "\"data\": [";

              echo "{";
              echo "\"label\": \"" . "Last Day" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_DAY . "\",";
              echo  "\"color\": \"#6060ff\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60) . "&LABEL=Last Day&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Week" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_WEEK . "\",";
              echo  "\"color\": \"#ff8080\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60 * 7) . "&LABEL=Last Week&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Month" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_MONTH . "\",";
              echo  "\"color\": \"#904040\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60 * 30) . "&LABEL=Last Month&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Quarter" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_QUARTER . "\",";
              echo  "\"color\": \"#20dd30\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60 * 90) . "&LABEL=Last Quarter&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Six Months" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_SIX_MONTHS . "\",";
              echo  "\"color\": \"#aa00aa\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60 * 182) . "&LABEL=Last Six Months&MODE=CHART&FILTER=$filterLogs\"";
              echo "},";

              echo "{";
              echo "\"label\": \"" . "Last Year" . "\",";
              echo "\"value\": \"" . $LOGGED_IN_LAST_YEAR . "\",";
              echo  "\"color\": \"#ff2050\"" . ",";
              echo  "\"link\": \"user_management.php?TIME=" . (24 * 60 * 60 * 365) . "&LABEL=Last Year&MODE=CHART&FILTER=$filterLogs\"";

              echo "}"; ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <?php
          }
    else
    {
        ?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",
                    "width": "950",
                    "height": "500",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "xAxisName": "Date",
                            "yAxisName": "Logins",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
       // POPULATE THE DATASET

       $result = db_query("
	   SELECT DATE(`DATE AND TIME`) d, COUNT(*) c FROM `LOGIN-LOGS` t1
	   LEFT JOIN `USERS` t2 ON t1.`User ID`=t2.`User ID`
	   $filterSQL
	   GROUP BY DATE(`DATE AND TIME`) ORDER BY DATE(`DATE AND TIME`) ASC
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
        } ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <?php
    }
    ?>


</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

        include "library/back_to_top_button.php";

    // sort order
    $SORT_ORDER = "ORDER BY `DATE AND TIME` DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($_GET["SORT"] == "TIMESTAMP-ASC")
    {
        $SORT_ORDER = "ORDER BY `DATE AND TIME` ASC";
    }

    if ($_GET["SORT"] == "TIMESTAMP-DESC")
    {
        $SORT_ORDER = "ORDER BY `DATE AND TIME` DESC";
    }

    if ($_GET["SORT"] == "EMAIL-ASC")
    {
        $SORT_ORDER = "ORDER BY `Email Address` ASC";
    }

    if ($_GET["SORT"] == "EMAIL-DESC")
    {
        $SORT_ORDER = "ORDER BY `Email Address` DESC";
    }

    if ($_GET["SORT"] == "UNAME-ASC")
    {
        $SORT_ORDER = "ORDER BY `User Name` ASC";
    }

    if ($_GET["SORT"] == "UNAME-DESC")
    {
        $SORT_ORDER = "ORDER BY `User Name` DESC";
    }

    if ($_GET["SORT"] == "IP-ASC")
    {
        $SORT_ORDER = "ORDER BY `Login IP` ASC";
    }

    if ($_GET["SORT"] == "IP-DESC")
    {
        $SORT_ORDER = "ORDER BY `Login IP` DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Login Logs</h2>";

    echo "<div class='button-block-with-spacing'>";

    echo "<a href='login_logs.php?FILTER=ALL&MODE=$MODE&FULL_OR_SUMMARY=$FULL_OR_SUMMARY&SEARCH=$searchTerm'><button";
    if ($filterLogs == "ALL")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show All Logins</button></a>";

    echo "<a href='login_logs.php?FILTER=EXCLUDE_ADMIN&MODE=$MODE&FULL_OR_SUMMARY=$FULL_OR_SUMMARY&SEARCH=$searchTerm'><button";
    if ($filterLogs == "EXCLUDE_ADMIN")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Exclude Admins</button></a>";

    echo "<a href='login_logs.php?FILTER=ADMIN_ONLY&MODE=$MODE&FULL_OR_SUMMARY=$FULL_OR_SUMMARY&SEARCH=$searchTerm'><button";
    if ($filterLogs == "ADMIN_ONLY")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Only Show Admins</button></a>";

    echo "<a href='login_logs.php?FILTER=$filterLogs&MODE=$MODE&FULL_OR_SUMMARY=FULL&SEARCH=$searchTerm'><button";
    if ($FULL_OR_SUMMARY == "FULL")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Full List</button></a>";

    echo "<a href='login_logs.php?FILTER=$filterLogs&MODE=$MODE&FULL_OR_SUMMARY=SUMMARY&SEARCH=$searchTerm'><button";

    if ($FULL_OR_SUMMARY == "SUMMARY")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Summary Stats</button></a>";

    echo "<a href='login_logs.php?FILTER=$filterLogs&MODE=TABLE&FULL_OR_SUMMARY=$FULL_OR_SUMMARY&SEARCH=$searchTerm'>";
    echo "<button";
    if ($MODE == "TABLE")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show as Table</button>";
    echo "</a>";

    echo "<a href='login_logs.php?FILTER=$filterLogs&MODE=CHART&FULL_OR_SUMMARY=$FULL_OR_SUMMARY&SEARCH=$searchTerm'>";
    echo "<button";
    if ($MODE == "CHART")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show as Chart</button>";
    echo "</a>";

    echo "</div>";

    $sql = "SELECT t1.`Record ID`,
                   t1.`Email Address`,
                   t2.`User ID`, 
                   `Login IP`,
                   `DATE AND TIME`, 
                   `Administrator`, 
                   `User Name`
              FROM `LOGIN-LOGS` t1 
              LEFT JOIN `USERS` t2 ON t1.`User ID`=t2.`User ID`   
              $filterSQL $searchSQL $SORT_ORDER";

    $result = db_query($sql);

if ($MODE != "CHART")
{
    if ($FULL_OR_SUMMARY == "SUMMARY")
    {
        echo "<br><table class='hoverTable'>";

        echo "<thead>";

        echo "<th><b>Logged In During Period</b></th>";
        echo "<th><b>Number of Logins</b></th>";

        if ($filterLogs == "ADMIN_ONLY")
        {
            echo "<th><b>% of Administrators</b></th>";
        }
        else
        {
            if ($filterLogs == "EXCLUDE_ADMIN")
            {
                echo "<th><b>% of User Base (Excluding Administrators)</b></th>";
            }
            else
            {
                echo "<th><b>% of User Base</b></th>";
            }
        }

        echo "</thead>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 1) . "&LABEL=Last Day&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Day</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_DAY) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_DAY * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 7) . "&LABEL=Last Week&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Week</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_WEEK) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_WEEK * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 30) . "&LABEL=Last Month&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Month</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_MONTH) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_MONTH * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 90) . "&LABEL=Last Quarter&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Quarter</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_QUARTER) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_QUARTER * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 182) . "&LABEL=Six Months&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Six Months</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_SIX_MONTHS) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_SIX_MONTHS * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "<tr>";

        $link = "<a href='user_management.php?TIME=" . (24 * 365) . "&LABEL=Last Year&FILTER=$filterLogs' class=linky>";

        echo "<td>" . $link . "Last Year</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_YEAR) . "</a></td>";
        echo "<td align=center>$link" . number_format($LOGGED_IN_LAST_YEAR * 100 / $TOTAL_NUMBER_OF_USERS, 2) . "%</a></td>";
        echo "</tr>";

        echo "</table>";
    }
    else
    {
        echo "
            <form id='search' method='get' action='login_logs.php'>

                <input type='hidden' name='SORT' value='" . $_GET["SORT"] . "'>
                <input type='hidden' name='FILTER' value='$filterLogs'>
                <input type='hidden' name='FULL_OR_SUMMARY' value='$FULL_OR_SUMMARY'>
                <input type='hidden' 'hidden='FULL_OR_SUMMARY' value='$MODE'>
                <input id='search-term' type='search' name='SEARCH' placeholder='Search by email or user name' value='$searchTerm'>
                <input type='submit' name='SUBMIT_SEARCH' value='Search'>
        
            </form>";

        echo "
            <table id='login-logs' class='qt-table hoverTable persist-area fixedTable' width=890>

                <thead>

                    <tr class='persist-header table-header-row'>

                        <th width='220'>
                            Login Date & Time
                            <a href='login_logs.php?SORT=TIMESTAMP-ASC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/up.gif'></a>
                            <a href='login_logs.php?SORT=TIMESTAMP-DESC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/down.gif'></a>
                        </th>
		
                        <th width='250'>
                            Email Address
                            <a href='login_logs.php?SORT=EMAIL-ASC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/up.gif'></a>
                            <a href='login_logs.php?SORT=EMAIL-DESC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/down.gif'></a>
                        </th>
		
                        <th width='240'>
                            Name
                            <a href='login_logs.php?SORT=UNAME-ASC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/up.gif'></a>
                            <a href='login_logs.php?SORT=UNAME-DESC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/down.gif'></a></th>
		
                        <th width='135'>
                            Login IP
                            <a href='login_logs.php?SORT=IP-ASC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/up.gif'></a>
                            <a href='login_logs.php?SORT=IP-DESC&FILTER=$filterLogs&SEARCH=$searchTerm' class='table-sorter'><img src='../images/down.gif'></a>
                        </th>
		
        		</tr>

            </thead>

            <tbody>";

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

            echo "<tr id='record-id-" . $ROW["Record ID"] . "'>";

            // Login Date and Time

            echo "<td width='220'>" . $ROW["DATE AND TIME"] . "</td>";

            // Email Address and admin icon

            echo "<td width='250'>" . $ROW["Email Address"];

            switch ($ROW["Administrator"]) {
                case 'ADMIN':
                    echo "<span class='pull-right'><img src='../images/manager.png' alt='Admin' title='Admin' class='qt-icon'></span>";
                    break;

                case 'SUPERUSER':
                    echo "<span class='pull-right'><img src='../images/admin-superuser-icon.png' alt='Super Admin' title='Super Admin' class='qt-icon'></span>";
                    break;
            }

            echo "</td>";

            // User Name, user detail

            echo "<td width='240'>";

            if (!empty($ROW["User ID"]))
            {
                echo !empty($ROW["User Name"]) ? $ROW["User Name"] : "Name not supplied";
                echo "<span class='pull-right'><a href='user_detail.php?USER=" . $ROW["User ID"] . "'>
                    <img src='../images/info.png' title='Get more info on this user' class='qt-icon'>
                </a></span>";
            }
            else
            {
                echo 'Orphaned User';
            }

            echo "</td>";

            // IP Address

            echo "<td>";
            if (is_valid_ip($ROW["Login IP"]))
            {
                echo "<a href='http://whatismyipaddress.com/ip/" . $ROW["Login IP"] . "' class=linky target='_blank'>" . $ROW["Login IP"] . "</a>";
            }
            else
            {
                echo $ROW["Login IP"];
            }

            echo "</td>";

            echo "</tr>";
        }
        echo "</tbody>";

        echo "<tfoot>";
        echo "<tr><td colspan=4 align=center>Showing Records " . number_format($START + 1) . " to " . number_format($END) . " of " . number_format(db_rowcount($result)) . "</td></tr>";

        echo "</tfoot>";

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

            print_page_navigator($CURRENT_PAGE, $pages_needed, false, "login_logs.php?SORT=" . $_GET["SORT"] . "&FILTER=$filterLogs");
        }
    }
}

echo "<div id='chartContainer' class='chart-container' align='center'";
if ($MODE != "CHART")
{
    echo " style='display: none;'";
}
echo "></div>";

if ($MODE == "CHART" && $FULL_OR_SUMMARY == "SUMMARY")
{
    echo "<hr width='1000'>";
}

echo "<div id='chartContainer2' class='chart-container' align=center";
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