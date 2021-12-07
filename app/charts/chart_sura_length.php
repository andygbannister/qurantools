<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
        ?>

    <?php
$TYPE     = 1;
$counting = "Sura Length by Verses";
$axis     = "Verses";
if (isset($_GET["TYPE"]))
{
    if ($_GET["TYPE"] == 2)
    {
        $TYPE     = 2;
        $counting = "Sura Length by Words";
        $axis     = "Words";
    }
    if ($_GET["TYPE"] == 3)
    {
        $TYPE     = 3;
        $counting = "Mean Verse Length by Sura";
        $axis     = "Mean Verse Length";
    }
}

// are we running in "mini mode" (e.g. in a loupe view)
$miniMode = false;
if (isset($_GET["VIEW"]))
{
    if ($_GET["VIEW"] = "MINI")
    {
        $miniMode = true;
    }
}

// Is "DURIE" mode on? (If so, we'll show the provenance coloured by Mark Durie's sura categories)
$durieMode = false;
$passDurie = "";
if (isset($_GET["DURIE"]))
{
    $durieMode = ($_GET["DURIE"] == "Y");
    $passDurie = "&DURIE=" . $_GET["DURIE"];
}
else
{
    $_GET["DURIE"] = "";
}

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

window_title($counting);

?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
    <!-- <link rel="stylesheet" type="text/css" href="../library/menubar.css"> -->

    <?php

// Provenance filtering

    $extra = "";
    $PROV  = "";
    if (isset($_GET["PROV"]))
    {
        if ($_GET["PROV"] == "MECCAN")
        {
            $extra = "AND `Provenance`='Meccan'";
            $PROV  = "MECCAN";
        }
        if ($_GET["PROV"] == "MEDINAN")
        {
            $extra = "AND `Provenance`='Medinan'";
            $PROV  = "MEDINAN";
        }
    }

    if ($TYPE == 1)
    {
        $sort_field = "`Sura Number`";
        if ($_GET["SORT"] != "")
        {
            $sort_field = "yaxis DESC";
        }

        $result = db_query("SELECT `Sura Number` sura, `Verses` yaxis, `Provenance` FROM `SURA-DATA` WHERE 1 $extra ORDER BY $sort_field");
    }

    if ($TYPE == 2)
    {
        $sort_field = "`SURA`";
        if ($_GET["SORT"] != "")
        {
            $sort_field = "yaxis DESC";
        }

        $result = db_query("SELECT DISTINCT(a.`SURA`) sura, COUNT(a.`WORD`) yaxis, b.`Provenance` FROM `QURAN-DATA` a, `SURA-DATA` b WHERE a.`SURA`=b.`Sura Number` $extra AND `SEGMENT`=1 GROUP BY `SURA` ORDER BY $sort_field");
    }

    if ($TYPE == 3)
    {
        $sort_field = "`SURA`";
        if ($_GET["SORT"] != "")
        {
            $sort_field = "mean DESC";
        }

        $result = db_query("SELECT DISTINCT(`SURA`) sura, (COUNT(`WORD`) / MAX(`VERSE`)) mean, `Provenance` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `SEGMENT`=1 $extra GROUP BY `SURA` ORDER BY $sort_field");
    }

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",

                    <?php

                    if (!$miniMode)
                    {
                        echo "\"width\": \"100%\",";
                        echo "\"height\": \"60%\",";
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
                            "subCaption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "xAxisName": "Sura",
                            "yAxisName": "<?php
            echo $axis;
            ?>",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

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
                                    echo "{";
                                    echo "\"label\": \"" . $ROW["sura"] . "\",";

                                    if ($TYPE == 3)
                                    {
                                        echo "\"value\": \"" . number_format($ROW["mean"], 2) . "\",";
                                    }
                                    else
                                    {
                                        echo "\"value\": \"" . $ROW["yaxis"] . "\",";
                                    }

                                    if (!$miniMode)
                                    {
                                        echo "link:\"chart_verse_lengths.php?S=" . $ROW["sura"] . "&TYPE=$TYPE&PROV=$PROV\",";
                                    }

                                    if ($durieMode)
                                    {
                                        $durie_classification = sura_durie_classification($ROW["sura"]);

                                        switch ($durie_classification)
                                     {
                                         case "PRE-TRANSITIONAL":
                                             echo  "\"color\": \"#008e00\"";
                                             break;

                                         case "POST-TRANSITIONAL":
                                             echo  "\"color\": \"#ff0000\"";
                                             break;

                                         case "MIXED":
                                             echo  "\"color\": \"#0000ff\"";
                                             break;
                                     }
                                    }
                                    else
                                    {
                                        if ($ROW["Provenance"] == "Meccan")
                                        {
                                            echo  "\"color\": \"#6060ff\"";
                                        }
                                        else
                                        {
                                            echo  "\"color\": \"#ff9090\"";
                                        }
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

echo "<div class='$mini_normal_mode_class'>";

echo "  <div class='page-header'>";

if ($miniMode)
{
    echo "<span style='float:right; margin-right:10px;'>";
    echo "  <a href='charts/chart_sura_length.php'>";
    echo "    <img src='images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "<h2>$counting</h2>";

if (!$miniMode)
{
    echo "<div class='chart-controls'>";

    // Provenance control =====

    echo "  <div class='chart-control provenance'>";
    echo "	  <span class='label'>Show</span>";
    echo "	  <a href='chart_sura_length.php?TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "		  All Suras";
    echo "	  </a>";

    echo "	  <a href='chart_sura_length.php?PROV=MECCAN&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		  Meccan";
    echo "	  </a>";

    echo "	  <a href='chart_sura_length.php?PROV=MEDINAN&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		  Medinan";
    echo "	  </a>";
    echo "  </div>"; // chart-control provenance

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_sura_length.php?PROV=$PROV&TYPE=$TYPE" . $passDurie . "' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_sura_length.php?PROV=$PROV&TYPE=$TYPE&SORT=1" . $_GET["SORT"] . "$passDurie' class='$first_sort_option_selected'>";
    echo "          Length";
    echo "        </a>";
    echo "    </div>"; // chart-control sort-by
}

echo "    </div>";   // chart-controls
echo "  </div>";       // page-header

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:420px; height:220px;'";
}
echo "></div>";

?>

        <?php
    if ($extra == "")
    {
        include "provenance_footer.php";
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