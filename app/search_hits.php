<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/search_engine.php';
require_once 'library/colours.php';
require_once 'library/verse_parse.php';

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
        ?>

    <script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script>
        var ShowResultsAs = 'SURAS';
        var DisplayMode = 'TABLE';

        function display_results_table() {
            DisplayMode = 'TABLE';

            // DISPLAY
            $('.chart-control a:contains("Table")').toggleClass('selected')
            $('.chart-control a:contains("Chart")').toggleClass('selected')

            // DATA
            ShowResultsAs == "SURAS" ? show_table_suras() : show_table_verses();
        }

        function display_results_chart() {
            DisplayMode = 'CHARTS';

            // DISPLAY
            $('.chart-control a:contains("Table")').toggleClass('selected')
            $('.chart-control a:contains("Chart")').toggleClass('selected')

            // DATA
            ShowResultsAs == "SURAS" ? show_chart_suras() : show_chart_verses();
        }

        function show_counts_per_verse() {
            // DISPLAY
            $('.chart-control a:contains("Sura")').toggleClass('selected')
            $('.chart-control a:contains("Verse")').toggleClass('selected')

            // hide the legend (meccan/medinan)
            $('.chartLegend').hide();

            ShowResultsAs = 'VERSES';

            // DATA
            DisplayMode == 'TABLE' ? show_table_verses() : show_chart_verses();
        }

        function show_counts_per_sura() {
            // DISPLAY
            $('.chart-control a:contains("Sura")').toggleClass('selected')
            $('.chart-control a:contains("Verse")').toggleClass('selected')

            // hide the legend (meccan/medinan)
            $('.chartLegend').show();

            ShowResultsAs = 'SURAS';

            // DATA
            DisplayMode == 'TABLE' ? show_table_suras() : show_chart_suras();
        }

        function show_table_suras() {
            $('#chartContainerSURAS').hide();
            $('#chartContainerVERSES').hide();
            $('#tableContainerSURAS').show();
            $('#tableContainerVERSES').hide();
            $('#chartLegend').hide();
            $('#100perButtons').hide();
        }

        function show_table_verses() {
            $('#chartContainerSURAS').hide();
            $('#chartContainerVERSES').hide();
            $('#tableContainerSURAS').hide();
            $('#tableContainerVERSES').show();
            $('#chartLegend').hide();
            $('#100perButtons').hide();
        }


        function show_chart_suras() {
            $('#chartContainerSURAS').show();
            $('#chartContainerVERSES').hide();
            $('#tableContainerSURAS').hide();
            $('#tableContainerVERSES').hide();
            $('#chartLegend').show();
        }

        function show_chart_verses() {
            $('#chartContainerSURAS').hide();
            $('#chartContainerVERSES').show();
            $('#tableContainerSURAS').hide();
            $('#tableContainerVERSES').hide();
            $('#chartLegend').show();
            $('#100perButtons').hide();
        }

        <?php

        // Is "DURIE" mode on? (If so, we'll show the provenance coloured by Mark Durie's sura categories)
        $durieMode = false;
        if (isset($_GET["DURIE"]))
        {
            $durieMode = ($_GET["DURIE"] == "Y");
        }

        // have they pre-ordered CHARTS (i.e. come in through the menu)?

        $MODE = "TABLE";

        if (isset($_GET["MODE"]))
        {
            if ($_GET["MODE"] == "CHART")
            {
                $MODE = "CHART"; ?>
        DisplayMode = 'CHARTS';
        <?php
            }
        }

?>
    </script>

    <?php

// substitute FORMPLUS for + [used to pass escaped values via Javascript from the charting function] (rough & ready error trap)

$_GET["S"] = str_ireplace("FORMPLUS", "+", $_GET["S"]);

window_title("Search Hits: " . $_GET["S"]);

// perform the search

$search_result = search($_GET["S"], true);

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainerSURAS",
                    "width": "1000",
                    "height": "420",
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

             echo "\"yAxisName\": \"Hits\",";

            ?>


                            "theme": "fint",
                            "showValues": "0",
                            "unescapeLinks": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                        // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                                echo "\"data\": [";
                                for ($i = 1; $i <= 114; $i++)
                                {
                                    if ($i > 1)
                                    {
                                        echo ",";
                                    }
                                    echo "{";
                                    echo "\"label\": \"" . $i . "\",";

                                    if (isset($hitsPerSura[$i]))
                                    {
                                        $hits = $hitsPerSura[$i];
                                    }
                                    else
                                    {
                                        $hits = 0;
                                    }

                                    echo "\"value\": \"" . $hits . "\",";

                                    // unless their search already specified a range, we'll limit this link to one sura

                                    if (stripos($_GET["S"], "range") !== false)
                                    {
                                        // strip out any range commands and replace with the sura in question

                                        $strip_out_range = $_GET["S"];

                                        $strip_out_range = preg_replace("|(?m-Usi)[Rr][Aa][Nn][Gg][Ee]:[0123456789;,-:]*|", "RANGE:$i", $strip_out_range);

                                        echo "link:\"verse_browser.php?S=" . urlencode($strip_out_range) . "\",";
                                    }
                                    else
                                    {
                                        echo "link:\"verse_browser.php?S=(" . urlencode($_GET["S"] . ") RANGE:$i") . "\",";
                                    }

                                    // if we are showing hits per verse, then we colour code the bar based on the sura

                                    if ($durieMode)
                                    {
                                        $durie_classification = sura_durie_classification($i);

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
                                        if (sura_provenance($i) == "Meccan")
                                        {
                                            echo  "\"color\": \"#6060ff\"";
                                        }
                                        else
                                        {
                                            echo  "\"color\": \"#ff9090\"";
                                        }
                                    }

                                    // $suraNumber = stristr($DATASET_LABELS_VERSES[$i],":",true) - 1;
                                    // echo  "\"color\": \"".$colourArray[$suraNumber % $colour_Choices]."\"";

                                    echo "}";
                                }

                        ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainerVERSES",
                    "width": "1000",
                    "height": "420",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "outCnvBaseFontSize": "11",
                            "yAxisNameFontSize": "11",
                            "xAxisNameFontSize": "11",
                            "subCaption": "",

                            <?php

           echo "\"xAxisName\": \"Verse\",";

             echo "\"yAxisName\": \"Hits\",";

            ?>


                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                        // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                                 $i = 0;

                                echo "\"data\": [";
                                foreach ($hitsPerVerse as $ref => $count)
                                {
                                    if ($i > 0)
                                    {
                                        echo ",";
                                    }

                                    $i++;

                                    echo "{";
                                    echo "\"label\": \"" . $ref . "\",";

                                    echo "\"value\": \"" . $count . "\",";

                                    // unless their search already specified a range, we'll limit this link to one sura

                                    if (stripos($_GET["S"], "range") !== false)
                                    {
                                        // strip out any range commands and replace with the verse in question

                                        $strip_out_range = $_GET["S"];

                                        $strip_out_range = str_ireplace("+", "FORMPLUS", $strip_out_range);

                                        $strip_out_range = preg_replace("|(?m-Usi)[Rr][Aa][Nn][Gg][Ee]:[0123456789;,-:]*|", "RANGE:$ref", $strip_out_range);

                                        echo "link:\"verse_browser.php?S=" . urlencode($strip_out_range) . "\",";
                                    }
                                    else
                                    {
                                        echo "link:\"verse_browser.php?S=(" . urlencode(str_ireplace("+", "FORMPLUS", $_GET["S"])) . ")+range%3A" . $ref . "\",";
                                    }

                                    // if we are showing hits per verse, then we colour code the bar based on the sura

                                    $suraNumber = stristr($ref, ":", true) - 1;
                                    echo  "\"color\": \"" . $colourArray[$suraNumber % $colour_Choices] . "\"";

                                    echo "}";
                                }

                        ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>

    <?php

echo "</head>";

echo "<body class='qt-site'>";
echo "<main class='qt-site-content'>";

include "library/back_to_top_button.php";

// menubar

include "library/menu.php";

echo "<div align=center>";

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='page-header'>";

echo "  <h2>";
echo "    Search Hits for: <a href='verse_browser.php?S=" . urlencode($_GET["S"]) . "' style='text-decoration: none'>" . htmlentities($_GET["S"]) . "</a>";
echo "  </h2>";

// check for errors [search results = 0]
if (db_rowcount($search_result) == 0)
{
    echo "<div align=center><b><font color='red'>Your search returned no results. <a href='home.php?L=" . urlencode($_GET["S"]) . "'>Please try again.</a></font></b></span>";
    include "library/footer.php";
    exit;
}

// preferences form (hits per verse; hits per sura; show as table; show as graph etc.)
echo "  <div class='chart-controls'>";

// Show counts control =====

echo "    <div class='chart-control show-counts'>";
echo "      <span class='label'>Show Counts per</span>";
echo "      <a href=# onClick='show_counts_per_sura();' class='selected'>";
echo "        Sura";
echo "      </a>";
echo "      <a href=# onClick='show_counts_per_verse();'>";
echo "        Verse";
echo "      </a>";
echo "    </div>";  // chart-control show-counts

// Display table or chart control =====

echo "    <div class='chart-control display-table-chart'>";
echo "      <span class='label'>Display Results as a</span>";

// decide which button (Table or Chart) should initially be highlighted

if ($MODE == "CHART")
{
    echo "      <a href=# onClick='display_results_table();'>";
    echo "        Table";
    echo "      </a>";
    echo "      <a href=# onClick='display_results_chart();' class='selected'>";
}
else
{
    echo "      <a href=# onClick='display_results_table();' class='selected'>";
    echo "        Table";
    echo "      </a>";
    echo "      <a href=# onClick='display_results_chart();'>";
}

echo "        Chart";
echo "      </a>";
echo "    </div>";  // chart-control display-table-chart

echo "<span ID=100perButtons style='display:none;'><button>" . "Chart All Hits</button>";
echo "<button>" . "Chart Hits per 100 Words</button>";
echo "</form></span></p>";

echo "  </div>";   // chart-controls
echo "</div>";       // page-header

// div for table 1 (sura values)
echo "<div id='tableContainerSURAS' align=center";
if ($MODE == "CHART")
{
    echo " style='display: none;'";
}
echo ">";

$totalHits         = 0;
$totalHits_Meccan  = 0;
$totalHits_Medinan = 0;

$totalHits_Pre_Transitional   = 0;
$totalHits_Post_Transitional  = 0;
$totalHits_Mixed_Transitional = 0;

echo "<table class='hoverTable' width=400'>";
echo "<thead>";
echo "<tr>";
echo "<th><b>Sura</b></th><th align=center><b>Matches</b></th><th align=center><b>Matches per 100 Words</b></th>";
echo "</tr>";
echo "</thead>";
for ($i = 1; $i <= 114; $i++)
{
    echo "<tr>";

    echo "<td align=center>";
    echo "<a href='verse_browser.php?V=" . $i . "' class=linky>";
    echo $i;
    echo "</a></td>";

    if (isset($hitsPerSura[$i]))
    {
        $hits = $hitsPerSura[$i];
    }
    else
    {
        $hits = 0;
    }

    echo "<td align=center>";
    if ($hits == 0)
    {
        echo "<font color=#b0b0b0>";
    }
    else
    {
        if (stripos($_GET["S"], "range") !== false)
        {
            echo "<a href=\"verse_browser.php?S=" . urlencode($_GET["S"]) . "\" class=linky>";
        }
        else
        {
            echo "<a href=\"verse_browser.php?S=(" . urlencode($_GET["S"] . ") RANGE:$i") . "\" class=linky>";
        }
    }

    echo number_format($hits);

    if ($hits == 0)
    {
        echo "</font>";
    }
    else
    {
        echo "</a>";
    }

    echo "</td>";

    echo "<td align=center>";

    if ($hits == 0)
    {
        echo "<font color=#b0b0b0>";
    }
    else
    {
        if (stripos($_GET["S"], "range") !== false)
        {
            echo "<a href=\"verse_browser.php?S=" . urlencode($_GET["S"]) . "\" class=linky>";
        }
        else
        {
            echo "<a href=\"verse_browser.php?S=(" . urlencode($_GET["S"] . ") RANGE:$i") . "\" class=linky>";
        }
    }

    echo number_format($hits / (sura_length_words($i) / 100), 2);

    if ($hits == 0)
    {
        echo "</font>";
    }
    else
    {
        echo "</a>";
    }
    echo "</td>";

    $totalHits += $hits;

    if ($durieMode)
    {
        $durie_classification = sura_durie_classification($i);

        switch ($durie_classification)
        {
            case "PRE-TRANSITIONAL":
                $totalHits_Pre_Transitional += $hits;
                break;

            case "POST-TRANSITIONAL":
                $totalHits_Post_Transitional += $hits;
                break;

            case "MIXED":
                $totalHits_Mixed_Transitional += $hits;
                break;
           }
    }
    else
    {
        if (sura_provenance($i) == "Meccan")
        {
            $totalHits_Meccan += $hits;
        }
        else
        {
            $totalHits_Medinan += $hits;
        }
    }

    echo "</tr>";
}
echo "<tr><td>&nbsp;</td><td align=center><b>" . number_format($totalHits) . "</b></td><td>&nbsp;</td></tr>";
echo "</table>";

// meccan and medinan
echo "<br>";

// 48922 words (Meccan) and 28507 (Medinan) of 77429 (total)
$meccan_should_be  = number_format((48922 / 77429) * $totalHits, 0);
$medinan_should_be = number_format((28507 / 77429) * $totalHits, 0);

if ($durieMode)
{
    echo "<table class='hoverTable' width=400'>";
    echo "<tr><th colspan=2 align=center><b>Total Occurrences</b></th></tr>";
    echo "<tr><td><b>Pre-Transitional Suras</b></td><td align=center>" . number_format($totalHits_Pre_Transitional) . "</td></tr>";
    echo "<tr><td><b>Post-Transitional Suras</b></td><td align=center>" . number_format($totalHits_Post_Transitional) . "</td></tr>";
    echo "<tr><td><b>Miaxed-Transitional Suras</b></td><td align=center>" . number_format($totalHits_Mixed_Transitional) . "</td></tr>";
    echo "</table>";
}
else
{
    echo "<table class='hoverTable' width=400'>";
    echo "<tr><th colspan=2 align=center><b>Total Occurrences</b></th></tr>";
    echo "<tr><td><b>Meccan Suras</b></td><td align=center title='Based on the ratio of Meccan to Medinan material, we would expect this figure to be $meccan_should_be'>" . number_format($totalHits_Meccan) . "</td></tr>";
    echo "<tr><td><b>Medinan Suras</b></td><td align=center title='Based on the ratio of Meccan to Medinan material, we would expect this figure to be $medinan_should_be'>" . number_format($totalHits_Medinan) . "</td></tr>";
    echo "</table>";
}

echo "</div>";

// div for table 2 (verse values)
echo "<div id='tableContainerVERSES' align=center style='display:none;'>";
$totalHits = 0;
echo "<table class='hoverTable' border=1 cellpadding=4 cellspacing=0'>";
echo "<tr><td><b>Verse</b></td><td><b>Matches</b></td></tr>";

foreach ($hitsPerVerse as $ref => $count)
{
    echo "<tr>";

    echo "<td align=center>";
    echo "<a href='verse_browser.php?V=$ref' class=linky>";
    echo $ref;
    echo "</a></td>";

    echo "<td align=center>";
    if ($count == 0)
    {
        echo "<font color=#b0b0b0>";
    }
    echo number_format($count);
    if ($count == 0)
    {
        echo "</font>";
    }
    echo "</td>";
    $totalHits += $count;
    echo "</tr>";
}
echo "<tr><td>&nbsp;</td><td align=center><b>" . number_format($totalHits) . "</b></td></tr>";
echo "</table>";
echo "</div>";

// div that will hold the charts

echo "<div align=center id='chartContainerSURAS'";
if ($MODE != "CHART")
{
    echo " style='display: none;'";
}
echo "></div>";

echo "<div align=center id='chartContainerVERSES' style='display: none;'></div>";

echo "<div align=center id='chartLegend' style='margin-left:40px;";
if ($MODE != "CHART")
{
    echo "display: none;";
}

echo "'>";

include "charts/provenance_footer.php";

echo "</div>";

include "library/footer.php";

?>

    </body>

</html>