<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

$SURA = 1;
if (isset($_GET["S"]))
{
    $SURA = $_GET["S"];
    if ($SURA < 1 || $SURA > 114)
    {
        $SURA = 1;
    }
}

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Verse Lengths (Sura $SURA)");
        ?>
		

<?php

// are we running in "mini mode" (e.g. in a loupe view)
$miniMode = false;
if (isset($_GET["VIEW"]))
{
    if ($_GET["VIEW"] = "MINI")
    {
        $miniMode = true;
    }
}

$SORT_ORDER = "";
if (isset($_GET["SORT"]))
{
    if ($_GET["SORT"] == "1")
    {
        $SORT_ORDER = " ORDER BY count DESC";
    }
}

// type (used to call back up a level to the chart of all verse lengths)
$TYPE = 1;
if (isset($_GET["TYPE"]))
{
    $TYPE = $_GET["TYPE"];
}

?>

	<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
	<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
	<!-- <link rel="stylesheet" type="text/css" href="../library/menubar.css"> -->

	<script type="text/javascript">
		FusionCharts.ready(function(){
			var revenueChart = new FusionCharts({
					"type": "column2d",
					"renderAt": "chartContainer",
					
					<?php

                    if (!$miniMode)
                    {
                        echo "\"width\": \"1000\",";
                        echo "\"height\": \"70%\",";
                    }
                    else
                    {
                        echo "\"width\": \"100%\",";
                        echo "\"height\": \"100%\",";
                    }
                    ?>
					
					
					"dataFormat": "json",
					"dataSource":  {
						"chart": {
							"caption": "",
							"subCaption": "",
							"xAxisName": "Verse",
							"yAxisName": "Length (Words)",
							"theme": "fint",
							"showValues": "0"
					},
					
				<?php
            // POPULATE THE DATASET
            $result = db_query("SELECT DISTINCT(`VERSE`), (SELECT COUNT(*) FROM `QURAN-DATA` t1 WHERE `SURA`=" . db_quote($SURA) . " AND t1.`VERSE`=t2.`VERSE` AND `SEGMENT`=1) count FROM `QURAN-DATA` t2 WHERE `SURA`=" . db_quote($SURA) . $SORT_ORDER);

            // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                    $test = "";
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
                        echo "\"label\": \"" . $ROW["VERSE"] . "\",";
                        echo "\"value\": \"" . $ROW["count"] . "\",";

                        if (!$miniMode)
                        {
                            echo "link:\"../verse_browser.php?V=$SURA:" . $ROW["VERSE"] . "\",";
                        }

                        echo  "\"color\": \"#6060ff\"";

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

if (!$miniMode)
{
    include "../library/menu.php";
    $mini_normal_mode_class = 'normal-mode';
}
else
{
    $mini_normal_mode_class = 'mini-mode';
}

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='$mini_normal_mode_class'>";

echo "<div class='page-header'>";

if ($miniMode)
{
    echo "<span style='float:right; margin-right:10px;'>";
    echo "  <a href='/charts/chart_verse_lengths.php?S=" . urlencode($_GET["S"]) . "'>";
    echo "    <img src='images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>";
echo "    <a href='chart_sura_length.php?TYPE=$TYPE' class=linky>";
echo "      Verse Lengths";
echo "    </a> by Words (Sura $SURA)";
echo "  </h2>";

echo "<p>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_verse_lengths.php?S=$SURA' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_verse_lengths.php?S=$SURA&SORT=1' class='$first_sort_option_selected'>";
    echo "          Number of Words";
    echo "        </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}

echo "</div>";     // page-header

echo "</div>"; // mini_normal_mode_class

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:420px; height:220px;'";
}
echo "></div>";

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
   
</body>
</html>