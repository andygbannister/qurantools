<?php
require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/colours.php';
require_once 'library/search_engine.php';
require_once 'library/transliterate.php';
require_once 'library/arabic.php';
require_once 'library/verse_parse.php';

// how many columns / slices for charts?
$MAX_ITEMS_FOR_CHART = 50;
if (isset($_GET["MAX_ITEMS"]))
{
    $MAX_ITEMS_FOR_CHART = $_GET["MAX_ITEMS"];
    if ($MAX_ITEMS_FOR_CHART < 10)
    {
        $MAX_ITEMS_FOR_CHART = 10;
    }
}

// avoids a minor script error if we try to use it and it isn't set
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

if (!isset($_GET["V"]))
{
    $_GET["V"] = "";
}

?>

<html>

<head>
    <?php
            include 'library/standard_header.php';
        ?>

    <script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
    <script type="text/javascript" src="library/js/persistent_table_headers.js"></script>

    <script>
        // button handlers
        function display_results_table() {
            // BUTTONS
            document.getElementById('chartBarButton').style.fontWeight = 'normal';
            document.getElementById('chartPieButton').style.fontWeight = 'normal';
            document.getElementById('tableButton').style.fontWeight = 'bold';

            // DIVS
            document.getElementById('chartContainer').style.display = 'none';
            document.getElementById('Messages').style.display = 'none';
            document.getElementById('TableData').style.display = 'block';
            document.getElementById('chartContainerPie').style.display = 'none';
        }

        function display_results_bar_chart() {
            // BUTTONS
            document.getElementById('chartBarButton').style.fontWeight = 'bold';
            document.getElementById('chartPieButton').style.fontWeight = 'normal';
            document.getElementById('tableButton').style.fontWeight = 'normal';

            // DIVS
            document.getElementById('chartContainer').style.display = 'block';
            document.getElementById('chartContainerPie').style.display = 'none';
            document.getElementById('Messages').style.display = 'block';
            document.getElementById('TableData').style.display = 'none';
        }

        function display_results_pie_chart() {
            // BUTTONS
            document.getElementById('chartPieButton').style.fontWeight = 'bold';
            document.getElementById('chartBarButton').style.fontWeight = 'normal';
            document.getElementById('tableButton').style.fontWeight = 'normal';

            // DIVS
            document.getElementById('chartContainer').style.display = 'none';
            document.getElementById('Messages').style.display = 'block';
            document.getElementById('TableData').style.display = 'none';
            document.getElementById('chartContainerPie').style.display = 'block';
        }
    </script>

    <?php

  if ($_GET["S"] != "")
  {
      $windowTitle = "Analysis of of Search Results";
  }
  else
  {
      $windowTitle = "Analysis of Q. " . $_GET["V"];
  }

 window_title($windowTitle);

function error_message($m)
{
    if ($m == "")
    {
        $m = "Bad reference!";
    }
    echo "<div align=center><b><font color=red>$m</font></b></div>";
}

// sort order
$sort       = "C-DESC";
$SORT_ORDER = "`num` DESC";

if (isset($_GET["SORT"]))
{
    $sort = $_GET["SORT"];
}

if ($sort == "A-ASC")
{
    $SORT_ORDER = "`ARABIC`";
}
if ($sort == "A-DESC")
{
    $SORT_ORDER = "`ARABIC` DESC";
}

if ($sort == "E-ASC")
{
    $SORT_ORDER = "`translit`";
}
if ($sort == "E-DESC")
{
    $SORT_ORDER = "`translit` DESC";
}

if ($sort == "C-ASC")
{
    $SORT_ORDER = "num";
}
if ($sort == "C-DESC")
{
    $SORT_ORDER = "num DESC";
}

if ($sort == "QT-ASC")
{
    $SORT_ORDER = "qcount";
}
if ($sort == "QT-DESC")
{
    $SORT_ORDER = "qcount DESC";
}

if ($sort == "HPX-ASC")
{
    $SORT_ORDER = "`Hapax or Unique`";
}
if ($sort == "HPX-DESC")
{
    $SORT_ORDER = "`Hapax or Unique` DESC";
}

// build the SQL we'll use to constrain the master analysis query

$RANGE_SQL = "";
$V         = "";

if ($_GET["S"] != "")
{
    $what = " of Search Results";

    $search_result = search($_GET["S"], true);

    // modify the master search to become something useful to us
    $master_search_sql = substr($master_search_sql, 48, strlen($master_search_sql));
    $master_search_sql = substr($master_search_sql, 0, stripos($master_search_sql, "ORDER BY") - 1);

    // in case this is a really big search, bump up the memory for this script
    //ini_set('memory_limit', '256M');

    // replace the table identifier for these queries
    $master_search_sql = str_ireplace("qtable.", "t2.", $master_search_sql);

    $GrandCount = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` t1
	LEFT JOIN `QURAN-FULL-PARSE` t2 on t1.`SURA-VERSE`=t2.`SURA-VERSE`
	WHERE `QTL-ROOT`!='' AND ($master_search_sql)");

    $SQL = "SELECT DISTINCT(BINARY(`QTL-ROOT`)) root, COUNT(`QTL-ROOT`) num, `ARABIC` arabic, `ENGLISH TRANSLITERATED` translit, `Hapax or Unique`, `Unique to Sura`, `COUNT` qcount
	FROM `QURAN-DATA` t1 
	LEFT JOIN `QURAN-FULL-PARSE` t2 on t1.`SURA-VERSE`=t2.`SURA-VERSE` 
	JOIN `ROOT-LIST` t3 ON BINARY(t1.`QTL-ROOT`)=t3.`ENGLISH`
	WHERE `QTL-ROOT`!='' AND ($master_search_sql) GROUP BY BINARY(`QTL-ROOT`) ORDER BY $SORT_ORDER";
}
else
{
    $V = $_GET["V"];

    $what = "of Verse Selection";
    
    // catch error caused by "sura:verse:word";
    if (substr_count($V, ":") > 1)
    {
	    // split string at first ":" by exploding into an array and rebuilding
	   $V_exploded = explode(":", $V);
	   $V = $V_exploded[0].":".$V_exploded[1];
    }

    // remove whitespace
    $V = preg_replace('/\s+/', '', $V);

    parse_verses($V, true, 0);

    $SQL = "SELECT DISTINCT(BINARY(`QTL-ROOT`)) root, COUNT(`QTL-ROOT`) num, `ENGLISH TRANSLITERATED` translit, `Hapax or Unique`, `Unique to Sura`, `ARABIC` arabic, `COUNT` qcount FROM `QURAN-DATA` t1 JOIN `ROOT-LIST` t2 ON BINARY(t1.`QTL-ROOT`)=t2.`ENGLISH` WHERE ($RANGE_SQL) AND `QTL-ROOT`!='' GROUP BY BINARY(`QTL-ROOT`) ORDER BY $SORT_ORDER";

    $countSQL   = db_query("SELECT * FROM `QURAN-DATA` WHERE ($RANGE_SQL) AND `QTL-ROOT`!=''");
    $GrandCount = db_rowcount($countSQL);
}

// load the dataset
$result = db_query($SQL);

// charting

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",
                    "width": "960",
                    "height": "420",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "xAxisName": "Root",
                            "yAxisName": "Occurrences ",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php

                        $capped = false;
                        $count  = 0;
                        if (db_rowcount($result) > $MAX_ITEMS_FOR_CHART)
                        {
                            $ITEMS  = $MAX_ITEMS_FOR_CHART;
                            $capped = true;
                        }
                        else
                        {
                            $ITEMS = db_rowcount($result);
                        }

                        // POPULATE THE DATASET

                        // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                                echo "\"data\": [";
                                for ($i = 0; $i < $ITEMS; $i++)
                                {
                                    $count++;
                                    // grab next database row
                                    $ROW = db_return_row($result);

                                    if ($i > 0)
                                    {
                                        echo ",";
                                    }
                                    echo "{";
                                    echo "\"label\": \"" . convert_buckwalter($ROW["translit"]) . "\",";

                                    echo "\"value\": \"" . $ROW["num"] . "\",";

                                    if ($_GET["S"] != "" && $V != "1")
                                    {
                                        echo "link:\"verse_browser.php?S=ROOT:" . $ROW["arabic"] . " AND (" . urlencode($_GET["S"]) . ")\",";
                                    }
                                    else
                                    {
                                        echo "link:\"verse_browser.php?S=ROOT:" . $ROW["arabic"] . " RANGE:" . $_GET["V"] . "\",";
                                    }

                                    echo  "\"color\": \"#6060ff\"";

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
                    "type": "pie2d",
                    "renderAt": "chartContainerPie",
                    "width": "960",
                    "height": "420",
                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "xAxisName": "Root",
                            "yAxisName": "Occurrences",
                            "theme": "fint",
                            "showValues": "1"
                        },

                        <?php

       $capped = false;
       $count  = 0;

       // reset the record pointer
      db_goto($result, 0);

       if (db_rowcount($result) > $MAX_ITEMS_FOR_CHART)
       {
           $ITEMS  = $MAX_ITEMS_FOR_CHART;
           $capped = true;
       }
       else
       {
           $ITEMS = db_rowcount($result);
       }

       // POPULATE THE DATASET

       // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

               echo "\"data\": [";
               for ($i = 0; $i < $ITEMS; $i++)
               {
                   $count++;
                   // grab next database row
                   $ROW = db_return_row($result);

                   if ($i > 0)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"" . convert_buckwalter($ROW["translit"]) . "\",";

                   echo "\"value\": \"" . $ROW["num"] . "\",";

                   if ($_GET["S"] != "" && $V != "1")
                   {
                       echo "link:\"verse_browser.php?S=ROOT:" . $ROW["arabic"] . " AND (" . urlencode($_GET["S"]) . ")\",";
                   }
                   else
                   {
                       echo "link:\"verse_browser.php?S=ROOT:" . $ROW["arabic"] . " RANGE:" . $_GET["V"] . "\",";
                   }

                   echo  "\"color\": \"" . $colourArray[$i % $colour_Choices] . "\"";

                   echo "}";
               }

               /*
            if ($capped)
               {
                   $others_count = 0;
                   for ($i = $ITEMS; $i <= db_rowcount($result); $i++)
                   {
                       $others_count += $ROW["num"];
                   }

                   echo ",{";
                   echo "\"label\": \"All other roots\",";
                   echo "\"value\": \"".$others_count."\",";
                   echo  "\"color\": \"".$colourArray[$ITEMS % $colour_Choices]."\"";
                   echo "}";

               }
            */

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

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>Analysis $what</h2>";

if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

if ($_GET["S"] != "" && $V != "1")
{
    $totalHits   = count($globalWordsToHighlight);
    $totalVerses = db_rowcount($search_result);

    echo "<div style='margin-top:-10px;'><a href='verse_browser.php?S=" . urlencode($_GET["S"]) . "' class=linky><span class='pill-button'>Search Terms: <b>" . $_GET["S"] . "</b>";

    if ($totalHits > 0 && $totalVerses > 0)
    {
        echo " (with " . number_format($totalHits) . " hit" . plural($totalHits) . " in " . number_format($totalVerses) . " verse" . plural($totalVerses) . ")";
    }

    if ($totalHits == 0)
    {
        echo " (which matches " . number_format($totalVerses) . " verse" . plural($totalVerses) . ")";
    }

    echo "</a></span></div></div><br>";
}
else
{
    echo "<a href='verse_browser.php?V=" . $_GET["V"] . "' style='text-decoration: none'><span class='pill-button'><b>Q. $V</b></span></a></div><br>";
}

// buttons

echo "<div align=center style='margin-top:-15px; margin-bottom:15px;'>";
echo "<hr style='width:750px;'>";
echo "<button id=tableButton onClick='display_results_table();' style='font-weight:bold;'>Display the Results as a Table</button>";
echo "<button id=chartBarButton onClick='display_results_bar_chart();'>Display the Results as a Bar Chart</button>";
echo "<button id=chartPieButton onClick='display_results_pie_chart();'>Display the Results as a Pie Chart</button>";
echo "</div>";

// reset counts
$hapax_count  = 0;
$unique_count = 0;

// table header
echo "<div align=center ID=TableData>";

    echo "<table class='hoverTable persist-area'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'><th align=center colspan=2><b>Root</b></th><th rowspan=2><b>Count</b><br><a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=C-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=C-DESC'><img src='images/down.gif'></a></th></th><th rowspan=2><b>% Of Total</b><br><a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=C-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=C-DESC'><img src='images/down.gif'></a></th><th rowspan=2 bgcolor=#c0c0c0><b>Qur’anic Total</b><br><a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=QT-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=QT-DESC'><img src='images/down.gif'></a></th>";
    echo "<th rowspan=2 bgcolor=#c0c0c0 colspan=2 align=center width=170><b>Unique / Hapax</b><BR><a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=HPX-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=HPX-DESC'><img src='images/down.gif'></a></th>";
    echo "<th bgcolor=#c0c0c0 rowspan=2 width=50>&nbsp;</th>";
    echo "</tr>";
    echo "<tr><th bgcolor=#c0c0c0><b>Arabic</b>&nbsp;<a href='selection_analyse.php?V=" . $_GET["V"] . "&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=A-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=A-DESC'><img src='images/down.gif'></a></th><th bgcolor=#c0c0c0><b>English</b>&nbsp;<a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=E-ASC'><img src='images/up.gif'></a> <a href='selection_analyse.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=E-DESC'><img src='images/down.gif'></a></th></tr>";

    echo "</thead>";

    echo "<tbody>";

    // table data

    // reset the record pointer
    db_goto($result, 0);

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        $AHREF = "<a href='verse_browser.php?S=ROOT:" . $ROW["arabic"] . "' class='linky'>";

        echo "<tr>";
        echo "<td align=center>$AHREF" . $ROW["arabic"] . "</a></td>";
        echo "<td align=center>$AHREF" . convert_buckwalter($ROW["translit"]) . "</a></td>";

        if ($_GET["S"] == "")
        {
            echo "<td align=center><a href='verse_browser.php?S=ROOT:" . urlencode($ROW["arabic"] . " RANGE:" . $_GET["V"]) . "' class='linky'>" . number_format($ROW["num"]) . "</a></td>";
        }
        else
        {
            echo "<td align=center>";
            echo "<a href='verse_browser.php?S=ROOT:" . urlencode($ROW["arabic"] . " AND (" . $_GET["S"] . ")") . "' class=linky>" . number_format($ROW["num"]) . "</a>";
            echo "</td>";
        }

        $local_percentage   = ($ROW["num"] / $GrandCount) * 100;
        $quranic_percentage = ($ROW["qcount"] / 49966) * 100;

        // $shade_local = "#ffffff";
        // $shade_master = "#ffffff";
        // if ($local_percentage > $quranic_percentage) {$shade_local =  "#fffff0";}
        // if ($local_percentage < $quranic_percentage) {$shade_master = "#fffff0";}

        echo "<td align=center>" . number_format($local_percentage, 4) . "%</td>";
        echo "<td align=center>" . number_format($quranic_percentage, 4) . "%</td>";

        // hapax or unique
        echo "<td align=center colspan=2 width=170>";

        if ($ROW["Hapax or Unique"] != "")
        {
            if ($ROW["Hapax or Unique"] == "HAPAX")
            {
                echo $AHREF . "Hapax (Sura " . $ROW["Unique to Sura"] . ")</a>";
                $hapax_count++;
                $unique_count++; // because hapaxes are also unique
            }

            if ($ROW["Hapax or Unique"] == "UNIQUE")
            {
                echo $AHREF . "Unique to Sura " . $ROW["Unique to Sura"] . "</a>";
                $unique_count++;
            }
        }
        else
        {
            echo "&nbsp;";
        }

        echo "</td>";

        echo "<td width=50 align=center><a title='Examine root' href='examine_root.php?ROOT=" . urlencode($ROW["root"]) . "'><img src='images/info.gif'></a>&nbsp;";

        // for the chart, we need to find the root in a slightly different format
        $ROOT_FORMAT_TRANSLITERATED = db_return_one_record_one_field("SELECT `ENGLISH TRANSLITERATED` FROM `ROOT-LIST` WHERE `ARABIC`='" . db_quote($ROW["arabic"]) . "'");

        echo "<a title='Chart root occurrences' href='charts/chart_roots.php?ROOT=" . urlencode($ROOT_FORMAT_TRANSLITERATED) . "'><img src='images/stats.gif'></a></td>";

        echo "</tr>";
    }

    echo "<tr><td rowspan=2>&nbsp;</td><td rowspan=2>&nbsp;</td><td align=center rowspan=2><b>" . number_format($GrandCount) . "</b></td><td rowspan=2>&nbsp;</td><td rowspan=2>&nbsp;</td>";

    echo "<td align=right><b>Hapax Total</b></td><td><b>$hapax_count</b></td>";

    echo "<td rowspan=2>&nbsp;</td></tr>";

    echo "<tr><td align=right><b>Unique Total</b></td><td><b>$unique_count</b></td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

// divs for charts

echo "<div align=center id=Messages style='display: none;'>";
if ($capped)
{
    echo "<b>Chart shows only the first $MAX_ITEMS_FOR_CHART items (of " . number_format(db_rowcount($result)) . " in total)</b>";
}
echo "</div>";

echo "<div align=center id=chartContainer style='display: none;'></div>";

echo "<div align=center id=chartContainerPie style='display: none;'></div>";

include "library/footer.php";

?>

    </body>

</html>