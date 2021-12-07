<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Chart of Verses with Intertextual Connections per Sura");
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

// mode (all, or % of all words)
$MODE = "ALL";
if (isset($_GET["MODE"]))
{
    $MODE = $_GET["MODE"];
}

// Provenance filtering

$PROV              = "";
$provenance_filter = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $provenance_filter = "WHERE `Provenance`='Meccan'";
        $PROV              = "MECCAN";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $provenance_filter = "WHERE `Provenance`='Medinan'";
        $PROV              = "MEDINAN";
    }
}

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",

                    <?php

            if (!$miniMode)
            {
                // echo "\"width\": \"960\",";
                echo "\"width\": \"100%\",";
                echo "\"height\": \"60%\",";
            }
            else
            {
                // echo "\"width\": \"520\",";
                echo "\"width\": \"100%\",";
                echo "\"height\": \"100%\",";
            }
            ?>

                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "subCaption": "",
                            <?php
                echo "\"xAxisName\": \"Sura\",";

                echo "\"yAxisName\": \"Number of Verses with Intertextual Connections\",";

                ?>

                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
            // POPULATE THE DATASET

            $sql        = "";
            $sort_field = "`Sura Number`";
            if ($_GET["SORT"] == "1")
            {
                $sort_field = "WC DESC, `Sura Number` ASC";
            }

            // show all

            $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-FULL-PARSE` WHERE `SURA`=`Sura Number` AND `Intertextual Link Count`>0) WC FROM `SURA-DATA` $provenance_filter ORDER BY $sort_field";

                $result = db_query($sql);

            // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                    $test  = "";
                    $count = 0;
                    echo "\"data\": [";
                    for ($i = 0; $i < db_rowcount($result); $i++)
                    {
                        // load next database row
                        $ROW          = db_return_row($result);
                        $countChanges = $ROW["WC"];

                        if (($PROV == "MECCAN" && $ROW["Provenance"] != "Meccan") || ($PROV == "MEDINAN" && $ROW["Provenance"] != "Medinan"))
                        {
                            // do nothing
                        }
                        else
                        {
                            if ($count > 0)
                            {
                                echo ",";
                            }
                            echo "{";

                            echo "\"label\": \"" . $ROW["Sura Number"] . "\",";
                            echo "\"value\": \"" . $countChanges . "\",";
                            $count++;

                            if (!$miniMode)
                            {
                                echo "link:\"../verse_browser.php?S=" . urlencode("INTERTEXT>0") . "%20RANGE:" . $ROW["Sura Number"] . "\",";
                            }

                            if ($ROW["Provenance"] == "Meccan")
                            {
                                echo  "\"color\": \"#6060ff\"";
                            }
                            else
                            {
                                echo  "\"color\": \"#ff9090\"";
                            }

                            echo "}";
                        }
                    }

            ?>
                    ]
                }

            }); revenueChart.render();
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
echo "<div class='$mini_normal_mode_class'>";

// title and navigation stuff
set_chart_control_selectors();

echo "  <div class='page-header'>";

if ($miniMode)
{
    echo "  <span style='float:right; margin-right:10px;'>";
    echo "    <a href='charts/chart_intertextual_connections.php?MODE=$MODE'>";
    echo "      <img src='images/expand.png' width=12 height=12>";
    echo "    </a>";
    echo "  </span>";
}

echo "<h2>Chart of Verses with Intertextual Connections per Sura <a href='/intertextuality/intertextual_browser.php'><img src='/images/table.png'></a></h2>";

if (!$miniMode)
{
    echo "<div class='chart-controls'>";

    // Provenance Filter  =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "    	<a href='chart_intertextual_connections.php?SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$all_suras_selected'>";
    echo "	    	All Suras";
    echo "    	</a>";

    echo "	    <a href='chart_intertextual_connections.php?PROV=MECCAN&SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$meccan_suras_selected'>";
    echo "	     	Meccan";
    echo "    	</a>";

    echo "	    <a href='chart_intertextual_connections.php?PROV=MEDINAN&SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$medinan_suras_selected'>";
    echo "	    	Medinan";
    echo "    	</a>";
    echo "    </div>"; // chart-control provenance

    // Sort control  =====

    echo "<div class='chart-control count-mode'>";
    echo "  <span class='label'>Sort By</span>";
    echo "  <a href='chart_intertextual_connections.php?PROV=$PROV&MODE=$MODE' class='$default_sort_selected'>";
    echo "    Sura Number";
    echo "  </a>";

    echo "  <a href='chart_intertextual_connections.php?PROV=$PROV&SORT=1&MODE=$MODE' class='$first_sort_option_selected'>";
    echo "    Number of Verses with Intertextual Connections in Sura";
    echo "  </a>";

    echo "</div>"; // chart-control count-mode
}
echo "  </div>"; // chart-controls
echo "</div>";   // page-header

echo "<div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; ";
    echo "height:250px";
    echo ";'";
}
echo "></div>";

?>

        <?php
    if ($provenance_filter == "")
    {
        include "./provenance_footer.php";
    }
echo "</div>";     // mini-mode or normal-mode

    if (!$miniMode)
    {
        include "library/print_control.php";
        include "../library/footer.php";
    }

?>

</body>

</html>