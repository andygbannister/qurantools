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
   function display_results_table()
  {
  	// BUTTONS
  	document.getElementById('chartButton').style.fontWeight = 'normal';
  	document.getElementById('tableButton').style.fontWeight = 'bold';	
  	
  	// DIVS
  	$("#chartContainer").hide();
  	$("#chartLegend").hide();
  	$("#TableData").show();
  }
  
  function display_results_chart()
  {
  	// BUTTONS
  	document.getElementById('chartButton').style.fontWeight = 'bold';
  	document.getElementById('tableButton').style.fontWeight = 'normal';	
  	
  	// DIVS
  	$("#chartContainer").show();
  	$("#chartLegend").fadeIn(300);
  	$("#TableData").hide();
  }
   
  </script>  
    
  <?php

  if ($_GET["S"] != "")
  {
      $windowTitle = "Sura Length Analysis: Search Results";
  }
  else
  {
      $windowTitle = "Sura Length Analysis: Q. " . $_GET["V"];
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
$sort       = "SURA-ASC";
$SORT_ORDER = "`SURA` ASC";

if (isset($_GET["SORT"]))
{
    $sort = $_GET["SORT"];
}

if ($sort == "SURA-ASC")
{
    $SORT_ORDER = "`SURA`";
}
if ($sort == "SURA-DESC")
{
    $SORT_ORDER = "`SURA` DESC";
}

if ($sort == "VERSES-ASC")
{
    $SORT_ORDER = "`VERSES`";
}
if ($sort == "VERSES-DESC")
{
    $SORT_ORDER = "`VERSES` DESC";
}

if ($sort == "WORDS-ASC")
{
    $SORT_ORDER = "`WORDS`";
}
if ($sort == "WORDS-DESC")
{
    $SORT_ORDER = "`WORDS` DESC";
}

if ($sort == "MEAN-ASC")
{
    $SORT_ORDER = "mean";
}
if ($sort == "MEAN-DESC")
{
    $SORT_ORDER = "mean DESC";
}

// build the SQL we'll use to constrain the master analysis query

$RANGE_SQL = "";

$V = $_GET["V"];

$what = " Verse Selection";

// remove whitespace
$V = preg_replace('/\s+/', '', $V);

if ($_GET["S"] != "")
{
    $what = " Search Results";

    $search_result = search($_GET["S"], true);

    // modify the master search to become something useful to us
    $master_search_sql = substr($master_search_sql, 48, strlen($master_search_sql));
    $master_search_sql = substr($master_search_sql, 0, stripos($master_search_sql, "ORDER BY") - 1);

    // in case this is a really big search, bump up the memory for this script
    //ini_set('memory_limit', '256M');

    // replace the table identifier for these queries
    $master_search_sql = str_ireplace("qtable.", "t2.", $master_search_sql);

    $SQL = "SELECT DISTINCT(`SURA`), `Verses`, `Words`, (`Words` / `Verses`) mean FROM `QURAN-FULL-PARSE` t2
	LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` 
	WHERE ($master_search_sql) GROUP BY `SURA` ORDER BY $SORT_ORDER";
}
else
{
    parse_verses($V, true, 0);

    $SQL = "SELECT DISTINCT(`SURA`), `Verses`, `Words`, (`Words` / `Verses`) mean FROM `QURAN-DATA` t1 
	LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` 
	WHERE ($RANGE_SQL) GROUP BY `SURA` ORDER BY $SORT_ORDER";
}

// zero the grand totals

$grand_totals_verses = 0;
$grand_totals_words  = 0;

// load the dataset

$result = db_query($SQL);

// charting

$meccanShown  = false; // both this and the below must be tripped for the legend to show
$medinanShown = false;

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        "width": "960",
        "height": "420",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "subCaption": "",
            "xAxisName": "Sura",
            "yAxisName": "Mean Verse Length",
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
                   echo "\"label\": \"" . $ROW["SURA"] . "\",";

                   echo "\"value\": \"" . $ROW["mean"] . "\",";

                   echo "link:\"verse_browser.php?V=" . $ROW["SURA"] . "\",";

                   if (sura_provenance($ROW["SURA"]) == "Meccan")
                   {
                       echo  "\"color\": \"#6060ff\"";
                       $meccanShown = true;
                   }
                   else
                   {
                       echo  "\"color\": \"#ff9090\"";
                       $medinanShown = true;
                   }

                   echo "}";
               }

       ?>
          ]
      }

  });
revenueChart.render();
})
</script>

<?php

echo "</head>";

echo "<body class='qt-site'>";

echo "<main class='qt-site-content'>";

include "library/back_to_top_button.php";

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>Sura Length Analysis in $what</h2>";

if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

if ($_GET["S"] != "" && $V != "1")
{
    $totalHits   = count($globalWordsToHighlight);
    $totalVerses = db_rowcount($search_result);

    echo "<div style='margin-top:-10px;'><a href='verse_browser.php?S=" . urlencode($_GET["S"]) . "' style='text-decoration: none'><span class='pill-button'>Search Terms: <b>" . $_GET["S"] . "</b>";

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
    echo "<a href='verse_browser.php?V=" . $_GET["V"] . "' class=linky><span class='pill-button'><b>Q. $V</b></span></a></div><br>";
}

// buttons

echo "<div align=center style='margin-top:-15px; margin-bottom:15px;'>";
echo "<hr style='width:750px;'>";

echo "<button id=tableButton onClick='display_results_table();' style='font-weight:bold;'>Display the Results as a Table</button>";
echo "<button id=chartButton onClick='display_results_chart();'>Display the Results as a Chart</button>";

echo "</div>";

// reset counts
$hapax_count  = 0;
$unique_count = 0;

// table header
echo "<div align=center ID=TableData>";

    echo "<table class='hoverTable persist-area'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th rowspan=2 bgcolor=#c0c0c0><b>Sura</b><br><a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=SURA-ASC'><img src='images/up.gif'></a> <a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=SURA-DESC'><img src='images/down.gif'></a></th>";

    echo "<th rowspan=2 bgcolor=#c0c0c0><b>Length (Verses)</b><br><a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=VERSES-ASC'><img src='images/up.gif'></a> <a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=VERSES-DESC'><img src='images/down.gif'></a></th>";

    echo "<th rowspan=2 bgcolor=#c0c0c0><b>Length (Words)</b><br><a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=WORDS-ASC'><img src='images/up.gif'></a> <a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=WORDS-DESC'><img src='images/down.gif'></a></th>";

    echo "<th rowspan=2 bgcolor=#c0c0c0><b>Mean Verse Length</b><br><a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=MEAN-ASC'><img src='images/up.gif'></a> <a href='selection_lengths.php?V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&SORT=MEAN-DESC'><img src='images/down.gif'></a></th>";

    echo "</thead>";

    echo "<tbody>";

    // table data

    // reset the record pointer
    db_goto($result, 0);

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td align=center>" . $ROW["SURA"] . "</td>";

        echo "<td align=center>" . $ROW["Verses"] . "</td>";

        echo "<td align=center>" . number_format($ROW["Words"]) . "</td>";

        echo "<td align=center>" . number_format($ROW["mean"], 2) . "</td>";

        // increment counts

        $grand_totals_verses = $grand_totals_verses + $ROW["Verses"];

        $grand_totals_words = $grand_totals_words + $ROW["Words"];

        echo "</tr>";
    }

    // grand total row

    echo "<tr>";

    echo "<td>&nbsp;</td>";

    echo "<td align=center><b>" . number_format($grand_totals_verses) . "<b></td>";

    echo "<td align=center><b>" . number_format($grand_totals_words) . "</b></td>";

    echo "<td align=center><b>" . number_format($grand_totals_words / $grand_totals_verses, 2) . "</b></td>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

// divs for charts

echo "<div align=center id=chartContainer style='display: none;'></div>";

echo "<div align=center id=chartLegend style='display: none;'>";

if ($meccanShown && $medinanShown)
{
    include "charts/provenance_footer.php";
}
echo "</div>";

include "library/footer.php";

?>

</body>
</html>