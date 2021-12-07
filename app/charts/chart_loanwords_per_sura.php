<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Chart of Number of Loanwords per Sura");
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

if (!isset($_GET["EXCLUDE_COVERING"]))
{
    if (isset($_POST["EXCLUDE_COVERING"]))
    {
        $_GET["EXCLUDE_COVERING"] = $_POST["EXCLUDE_COVERING"];
    }
    else
    {
        $_GET["EXCLUDE_COVERING"] = "";
    }
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
                // if ($MODE == "PERCENT")
                // {
                // 	echo "\"height\": \"320\",";
                // }
                // else
                // {
                // 	echo "\"height\": \"250\",";
                // }
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

                if ($MODE != "PERCENT")
                {
                    echo "\"yAxisName\": \"Number of Loanwords\",";
                }

                if ($MODE == "PERCENT")
                {
                    echo "\"yAxisName\": \"Loanwords (As % of All Words in Sura)\",";
                }

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

            if ($MODE != "PERCENT")
            {
                $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(DISTINCT `GLOBAL WORD NUMBER`) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND `AJC FOREIGN WORD` > 0) WC FROM `SURA-DATA` $provenance_filter ORDER BY $sort_field";
            }
            else
            {
                $sql = "SELECT `Sura Number`, `Provenance`, (SELECT (COUNT(*)/`Words`)*100 FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND `AJC FOREIGN WORD` > 0) WC
	FROM `SURA-DATA` $provenance_filter ORDER BY $sort_field";
            }

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
                                echo "link:\"../verse_browser.php?S=[LOANWORD]%20RANGE:" . $ROW["Sura Number"] . "\",";
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
    echo "    <a href='charts/chart_loanwords_per_sura.php?MODE=$MODE'>";
    echo "      <img src='images/expand.png' width=12 height=12>";
    echo "    </a>";
    echo "  </span>";
}

echo "    <h2>Chart of Loanwords per Sura <a href='/counts/count_loanwords.php'><img src='/images/table.png'></a></h2>";
echo "<h3 class='button-block-with-spacing'>(Based on Arthur Jeffery’s <i>The Foreign Vocabulary of the Qur&rsquo;an</i>)</h3>";

if (!$miniMode)
{
    echo "<div class='chart-controls'>";

    // Provenance Filter  =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "    	<a href='chart_loanwords_per_sura.php?SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$all_suras_selected'>";
    echo "	    	All Suras";
    echo "    	</a>";

    echo "	    <a href='chart_loanwords_per_sura.php?PROV=MECCAN&SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$meccan_suras_selected'>";
    echo "	     	Meccan";
    echo "    	</a>";

    echo "	    <a href='chart_loanwords_per_sura.php?PROV=MEDINAN&SORT=" . $_GET["SORT"] . "&MODE=$MODE' class='$medinan_suras_selected'>";
    echo "	    	Medinan";
    echo "    	</a>";
    echo "    </div>"; // chart-control provenance

    // Sort control  =====

    switch ($MODE) {
        case "PERCENT":
            $sort_by_other_text = "% of Loanwords in Sura";
            break;
        default:
            $sort_by_other_text = "Number of Loanwords in Sura";
    }

    echo "<div class='chart-control count-mode'>";
    echo "  <span class='label'>Sort By</span>";
    echo "  <a href='chart_loanwords_per_sura.php?PROV=$PROV&MODE=$MODE' class='$default_sort_selected'>";
    echo "    Sura Number";
    echo "  </a>";

    echo "  <a href='chart_loanwords_per_sura.php?PROV=$PROV&SORT=1&MODE=$MODE' class='$first_sort_option_selected'>";
    echo "    $sort_by_other_text";
    echo "  </a>";

    echo "</div>"; // chart-control count-mode

    // Count mode controls  =====

    $count_mode_all_selected     = '';
    $count_mode_percent_selected = '';
    switch ($MODE){
        case "PERCENT":
            $count_mode_percent_selected = 'selected';
            break;
        default:
            $count_mode_all_selected = 'selected';
            break;
  }

    echo "    <div class='chart-control count-mode'>";
    echo "      <span class='label'>Count</span>";

    echo "      <a href='chart_loanwords_per_sura.php?PROV=$PROV&SORT=" . $_GET["SORT"] . "&MODE=ALL' class='$count_mode_all_selected'>";
    echo "        All Loanwords";
    echo "      </a>";

    echo "      <a href='chart_loanwords_per_sura.php?PROV=$PROV&SORT=" . $_GET["SORT"] . "&MODE=PERCENT' class='$count_mode_percent_selected'>";
    echo "        Loanwords As % of All Words in Sura";
    echo "      </a>";
    echo "    </div>"; // chart-control count-mode
}
echo "  </div>"; // chart-controls
echo "</div>";   // page-header

echo "<div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; ";
    if ($MODE == "PERCENT")
    {
        echo "height:320px";
    }
    else
    {
        echo "height:250px";
    }
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