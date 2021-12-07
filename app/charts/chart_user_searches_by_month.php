<?php
require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// redirect if changes are turned off

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Monthly Searches/Verse Lookups for User");

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

// which user to lookup?

$user = 1;

if (isset($_GET["USER"]))
{
    $user = db_quote($_GET["USER"]);
}

// mode

$MODE = "MONTH";

if (isset($_GET["MODE"]))
{
    $MODE = db_quote($_GET["MODE"]);

    if ($MODE != "MONTH" && $MODE != "DAY")
    {
        $MODE = "MONTH";
    }
}

// filter by month

$YEAR  = 2019;
$MONTH = 1;

if (isset($_GET["MONTH"]))
{
    $MONTH = db_quote($_GET["MONTH"]);
}

if (isset($_GET["YEAR"]))
{
    $YEAR = db_quote($_GET["YEAR"]);
}

?>

	<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
	<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

	<script type="text/javascript">
		FusionCharts.ready(function(){
			var revenueChart = new FusionCharts({
					"type": "column2d",
					"renderAt": "chartContainer",
					"width": "960",
					"height": "70%",
					"dataFormat": "json",
					"dataSource":  {
						"chart": {
							"caption": "",
								"outCnvBaseFontSize": "11",
							"yAxisNameFontSize": "11",
							"xAxisNameFontSize": "11",
							"subCaption": "",
							<?php

                if ($MODE == "DAY")
                {
                    echo "\"xAxisName\": \"Day of the Month\",";
                }
                else
                {
                    echo "\"xAxisName\": \"Month and Year\",";
                }

                ?>
							
							
							"yAxisName": "Number of Verse Lookups/Searches",
							"theme": "fint",
							"showValues": "0"
					},
					
				<?php
            // POPULATE THE DATASET

                    if ($MODE == "MONTH")
                    {
                        $sort_field = "YEAR(`DATE AND TIME`) ASC, MONTH(`DATE AND TIME`) ASC";
                    }
                    else
                    {
                        $sort_field = "DAY(`DATE AND TIME`) ASC";
                    }

                    if ($_GET["SORT"] == 1)
                    {
                        $sort_field = "DATE_COUNT DESC";
                    }

                    if ($MODE != "DAY")
                    {
                        $result = db_query("SELECT CONCAT(MONTH(`DATE AND TIME`), '-', YEAR(`DATE AND TIME`)) DATE_LABEL, MONTH(`DATE AND TIME`) MONTH, YEAR(`DATE AND TIME`) YEAR, COUNT(*) DATE_COUNT FROM `USAGE-VERSES-SEARCHES` WHERE `USER ID`=$user GROUP BY CONCAT(MONTH(`DATE AND TIME`), '-', YEAR(`DATE AND TIME`)) ORDER BY $sort_field");
                    }
                    else
                    {
                        $result = db_query("SELECT DAY(`DATE AND TIME`) DAY, COUNT(*) DATE_COUNT FROM `USAGE-VERSES-SEARCHES` WHERE `USER ID`=$user AND MONTH(`DATE AND TIME`)=$MONTH AND YEAR(`DATE AND TIME`)=$YEAR GROUP BY DAY(`DATE AND TIME`) ORDER BY DAY(`DATE AND TIME`) ASC");
                    }

                    $test  = "";
                    $count = 0;
                    echo "\"data\": [";
                    for ($i = 0; $i < db_rowcount($result); $i++)
                    {
                        // grab next database row
                        $ROW = db_return_row($result);

                        // count how many columns we have actually rendered
                        $count++;

                        if ($count > 1)
                        {
                            echo ",";
                        }
                        echo "{";
                        echo "\"label\": \"";

                        if ($MODE != "DAY")
                        {
                            echo date('M', mktime(0, 0, 0, $ROW["MONTH"], 10)) . " " . $ROW["YEAR"];
                        }
                        else
                        {
                            echo $ROW["DAY"];
                        }

                        echo "\",";
                        echo "\"value\": \"" . $ROW["DATE_COUNT"] . "\",";

                        if ($MODE == "MONTH")
                        {
                            echo  "\"link\": \"chart_user_searches_by_month.php?USER=$user&MODE=DAY&MONTH=" . $ROW["MONTH"] . "&YEAR=" . $ROW["YEAR"] . "\",";
                        }

                        echo  "\"color\": \"#6898FF\"";

                        echo "}";
                    }

            ?>
						]
				}

		});
	revenueChart.render();
})
	</script>
</head>
<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "../library/menu.php";

// Title and navigation stuff
set_chart_control_selectors();

echo "<div class='page-header'>";

if ($MODE == "DAY")
{
    echo "  <h2><a href='/admin/user_detail.php?USER=$user' class=nodec>Verse Lookups/Searches in " . date('F', mktime(0, 0, 0, $MONTH, 10)) . " $YEAR by User ID #$user " . " (" . htmlspecialchars(db_return_one_record_one_field("SELECT `Email Address` FROM `USERS` WHERE `User ID`=" . $user)) . ")</a></h2>";
}
else
{
    echo "  <h2><a href='/admin/user_detail.php?USER=$user' class=nodec>Verse Lookups/Searches per Month by User ID #$user " . " (" . htmlspecialchars(db_return_one_record_one_field("SELECT `Email Address` FROM `USERS` WHERE `User ID`=" . $user)) . ")</a></h2>";
}
echo "  <div class='chart-controls'>";

// Sort control  =====

if ($MODE != "DAY")
{
    echo "    <div class='chart-control sort-by'>";
    echo "			<span class='label'>Sort By</span>";
    echo "      <a href='chart_user_searches_by_month.php?USER=$user' class='$default_sort_selected'>";
    echo "        Login Month/Year";
    echo "      </a>";
    echo "      <a href='chart_user_searches_by_month.php?USER=$user&SORT=1' class='$first_sort_option_selected'>";
    echo "        Number of Verse Lookups/Searches";
    echo "      </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}
else
{
    echo "    <div class='chart-control sort-by'>";
    echo "      <a href='chart_user_searches_by_month.php?USER=$user' class='$default_sort_selected'>";
    echo "        Show All Verse/Search Data for User";
    echo "      </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}

echo "</div>";     // page-header

?>

  <div align=center id="chartContainer"></div>
<?php

    echo "<div align=center><br><a href='/admin/user_detail.php?USER=$user' class=linky>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back to User Details</a></div>";

    include "library/print_control.php";
    include "../library/footer.php";

?>  
   
</body>
</html>