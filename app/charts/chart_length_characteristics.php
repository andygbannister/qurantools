<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// are we running in "mini mode" (e.g. in a loupe view)
$miniMode = false;
if (isset($_GET["VIEW"]))
{
    if ($_GET["VIEW"] = "MINI")
    {
        $miniMode = true;
    }
}

// which sura?

$SURA = 1;
if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] >= 1 && $_GET["SURA"] <= 114)
    {
        $SURA = $_GET["SURA"];
    }
}

$sura_length = verses_in_sura($SURA);

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Sura Verse Lengths Characteristics Chart: Sura $SURA");
?>


    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <?php

$sql = "select distinct(`VERSE LENGTH (EXCLUDING QURANIC INITIALS)`) LENGTH, COUNT(*) COUNT FROM `QURAN-FULL-PARSE` 
where `SURA`=" . db_quote($SURA) . " AND `VERSE LENGTH (EXCLUDING QURANIC INITIALS)`>0 GROUP BY `VERSE LENGTH (EXCLUDING QURANIC INITIALS)`";

$result = db_query($sql);

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "Line",
                    "renderAt": "chartContainer",

                    <?php

                     if (!$miniMode)
                     {
                         echo "\"width\": \"1000\",";
                         echo "\"height\": \"65%\",";
                     }
                     else
                     {
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
                            "xAxisName": "Verse Length (Words)",
                            "yAxisName": "% of Verses in Sura of This Length",

                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                        $last_number = -1; // used to track and fill gaps in

                        // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                                echo "\"data\": [";
                                for ($i = 0; $i < db_rowcount($result); $i++)
                                {
                                    // grab next database row
                                    $ROW = db_return_row($result);

                                    if ($i > 0)
                                    {
                                        echo ",";
                                    }

                                    // gap filling

                                    $last_number++;

                                    if ($ROW["LENGTH"] > $last_number)
                                    {
                                        for ($j = $last_number; $j < $ROW["LENGTH"]; $j++)
                                        {
                                            echo "{";
                                            echo "\"label\": \"" . $j . "\",";
                                            echo "\"value\": \"" . "0" . "\",";
                                            echo "}, ";
                                        }
                                    }

                                    $last_number = $ROW["LENGTH"];

                                    echo "{";
                                    echo "\"label\": \"" . $ROW["LENGTH"] . "\",";

                                    // data point
                                    echo "\"value\": \"" . number_format((($ROW["COUNT"] / $sura_length) * 100), 2) . "\",";

                                    if (!$miniMode)
                                    {
                                        echo "link:\"../verse_browser.php?S=VERSELENGTH:" . $ROW["LENGTH"] . " AND RANGE:$SURA" . "\",";
                                    }

                                    echo "}";
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

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='page-header'>";

if ($miniMode)
{
    echo "<span style='float:right; margin-right:10px;'>";
    echo "  <a href='charts/chart_length_characteristics.php?SURA=$SURA'>";
    echo "    <img src='images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Sura Verse Lengths Characteristics Chart</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    echo "    <div class='chart-control grammar-choosers'>"; // contains grammar choosers (for now)

    echo "<form action='chart_length_characteristics.php' method=GET style='margin-top:-10px;margin-bottom:0px;'>";

    echo "Showing Length Characteristic Curve for Sura ";

    echo "<select name=SURA onChange='this.form.submit();'>";

    for ($i = 1; $i <= 114; $i++)
    {
        echo "<option value='$i'";
        if ($SURA == $i)
        {
            echo " selected";
        }
        echo ">$i</option>";
    }

    echo "</select>";
    echo "    </div>";   // chart-control grammar-chooser
}
else
{
    echo "  <div class='chart-controls'>";

    echo "Showing Length Characteristic Curve for Sura $SURA";

    echo "    </div>";   // chart-control grammar-chooser
}

// Autoscale-axis control
echo "    <div class='chart-control axis-scaler' style='margin-top:2px;'>"; // contains grammar choosers (for now)

echo        "</form>";
echo "    </div>"; // chart-control autoscale-axis

echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

// Sort control  =====

echo "  </div>";   // chart-controls
echo "</div>";     // page-header

  echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:500px; height:250px;'";
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