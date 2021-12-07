<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

// filtering
$filterLogs   = "ALL";
$filterLogSQL = "";

if (isset($_GET["FILTER"]))
{
    if ($_GET["FILTER"] == "ALL")
    {
        $filterLogs = "ALL";
    }

    if ($_GET["FILTER"] == "EXCLUDE_ADMINS")
    {
        $filterLogs   = "EXCLUDE_ADMINS";
        $filterLogSQL = "WHERE `Administrator`=''";
    }

    if ($_GET["FILTER"] == "ONLY_ADMINS")
    {
        $filterLogs   = "ONLY_ADMINS";
        $filterLogSQL = "WHERE `Administrator`!=''";
    }
}

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Page Usage Statistics");
        ?>
  
	  <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
  
  <script>
  
  function table_view()
  {
  	document.getElementById('tableButton').style.fontWeight = 'bold';
  	document.getElementById('chartButton').style.fontWeight = 'normal';
  	
  	document.getElementById('TableView').style.display = 'block';
  	document.getElementById('PieView').style.display = 'none';
  }
  
  function chart_view()
  {
  	document.getElementById('tableButton').style.fontWeight = 'normal';
  	document.getElementById('chartButton').style.fontWeight = 'bold';
  	
  	document.getElementById('TableView').style.display = 'none';
  	document.getElementById('PieView').style.display = 'block';
  }
  
  </script>
  
   <?php

// menubar etc

require_once "library/menu.php";
require_once "library/colours.php";

// the first thing we do is clean up the USAGE table, so we can then do a GROUP BY

db_query("DELETE FROM `USAGE` WHERE `PAGE LOADED` = ''");
db_query("UPDATE `USAGE` SET `ACTUAL PAGE` = SUBSTRING_INDEX(SUBSTRING_INDEX(`PAGE LOADED`, '/', -1), '.php', 1)");

// UPDATE CATEGORIES

// db_query("UPDATE `USAGE` SET `CATEGORY`=''"); // wipe out the categories if we need to reset them

db_query("DELETE FROM `USAGE` WHERE `ACTUAL PAGE` = ''"); // CLEAN UP
db_query("DELETE FROM `USAGE` WHERE `ACTUAL PAGE` LIKE '%jquery%'"); // CLEAN UP again
db_query("DELETE FROM `USAGE` WHERE `ACTUAL PAGE` LIKE '%cs.gif%'"); // CLEAN UP again

// db_query("DELETE FROM `USAGE` WHERE `CATEGORY`=''"); // CLEAN UP again

// home category
db_query("UPDATE `USAGE` SET `CATEGORY`='Home' WHERE `PAGE LOADED` LIKE '%index%' AND `CATEGORY` = ''");

// verse or search category
db_query("UPDATE `USAGE` SET `CATEGORY`='Verse or Search' WHERE `PAGE LOADED` LIKE '%/verse_browser.php%'");
db_query("UPDATE `USAGE` SET `CATEGORY`='Verse or Search' WHERE `PAGE LOADED` LIKE '%/verses.php%'");
db_query("UPDATE `USAGE` SET `CATEGORY`='Verse or Search' WHERE `PAGE LOADED` LIKE '%easy_search%'");
db_query("UPDATE `USAGE` SET `CATEGORY`='Verse or Search' WHERE `PAGE LOADED` LIKE '%/search.php%'");

// system pages
db_query("UPDATE `USAGE` SET `CATEGORY`='System' WHERE `PAGE LOADED` LIKE '%404%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='System' WHERE `PAGE LOADED` LIKE '%maintenance.php%' AND `CATEGORY` = ''");

db_query("UPDATE `USAGE` SET `CATEGORY`='Home' WHERE `PAGE LOADED` LIKE '%home.php%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Dictionary' WHERE `PAGE LOADED` LIKE '%dictionary%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Dictionary' WHERE `PAGE LOADED` LIKE '%examine%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Dictionary' WHERE `PAGE LOADED` LIKE '%associations%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Lists' WHERE `PAGE LOADED` LIKE '%count_%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='User Guide' WHERE `PAGE LOADED` LIKE '%user_guide%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='User Guide' WHERE `PAGE LOADED` LIKE '%about%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Verse Browser' WHERE `PAGE LOADED` LIKE 'verse_browser.php%' AND `CATEGORY` = ''");

db_query("UPDATE `USAGE` SET `CATEGORY`='Search' WHERE `PAGE LOADED` LIKE 'verse_browser.php%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Search' WHERE `PAGE LOADED` LIKE '%co.uk/verse_browser.php%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%user%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%logs%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%password%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%database%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%statistics%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%zeitgeist%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Preferences' WHERE `PAGE LOADED` LIKE '%preferences%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Formulaic Analysis' WHERE `PAGE LOADED` LIKE '%formula%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Charts' WHERE `PAGE LOADED` LIKE '%chart%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Analysis' WHERE `PAGE LOADED` LIKE '%selection%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Analysis' WHERE `PAGE LOADED` LIKE '%search_hits%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Lists' WHERE `PAGE LOADED` LIKE '%browse_%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%word_correction%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Admin' WHERE `PAGE LOADED` LIKE '%interest_shown%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Registration' WHERE `PAGE LOADED` LIKE '%register%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='User Guide' WHERE `PAGE LOADED` LIKE '%cookie%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='User Guide' WHERE `PAGE LOADED` LIKE '%privacy	%' AND `CATEGORY` = ''");
db_query("UPDATE `USAGE` SET `CATEGORY`='Preferences' WHERE `PAGE LOADED` LIKE '%bookmark%' AND `CATEGORY` = ''");

db_query("DELETE FROM `USAGE` WHERE `PAGE LOADED` LIKE '%png%' OR `PAGE LOADED` LIKE '%jpg%'");

// we can uncomment the below if we want to do a clean up
// db_query("DELETE FROM `USAGE` WHERE `CATEGORY` = ''");

// sort order
$SORT_ORDER = " ORDER BY `c` DESC";

if (isset($_GET["SORT"]))
{
    if ($_GET["SORT"] == "PAGE-ASC")
    {
        $SORT_ORDER = " ORDER BY `ACTUAL PAGE` ASC";
    }
    if ($_GET["SORT"] == "PAGE-DESC")
    {
        $SORT_ORDER = " ORDER BY `ACTUAL PAGE` DESC";
    }

    if ($_GET["SORT"] == "COUNT-ASC")
    {
        $SORT_ORDER = " ORDER BY `c` ASC";
    }
    if ($_GET["SORT"] == "COUNT-DESC")
    {
        $SORT_ORDER = " ORDER BY `c` DESC";
    }

    if ($_GET["SORT"] == "CATEGORY-ASC")
    {
        $SORT_ORDER = " ORDER BY `CATEGORY` ASC";
    }
    if ($_GET["SORT"] == "CATEGORY-DESC")
    {
        $SORT_ORDER = " ORDER BY `CATEGORY` DESC";
    }
}

// load datasets

// TODO: This query is not SQL-92 standards compliant since not all the non-
// aggregated elements in the SELECT clause appear in the GROUP BY.
$result = db_query("SELECT DISTINCT(`ACTUAL PAGE`), `CATEGORY`, T1.`USER ID`, `Administrator`, COUNT(*) c FROM `USAGE` T1
LEFT JOIN `USERS` T2 ON T2.`User ID`=T1.`USER ID`
$filterLogSQL
GROUP BY `ACTUAL PAGE` $SORT_ORDER");

$result_chart = db_query("SELECT DISTINCT(`CATEGORY`), COUNT(*) c FROM `USAGE` T1 
LEFT JOIN `USERS` T2 ON T2.`User ID`=T1.`USER ID`
$filterLogSQL
GROUP BY `CATEGORY`");

// pie chart view

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "pie2d",
        "renderAt": "PieView",
        "width": "1100",
        "height": "70%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
                "labelFontSize": "12",
            "subCaption": "",
            <?php
           echo "\"xAxisName\": \"Type\",";
            ?>
            
            
            "yAxisName": "Count",
            "theme": "fint",
            "showValues": "1",

             "use3DLighting": "1",
             "showPercentInTooltip":"1"
         },
         
       <?php
       // POPULATE THE DATASET

       // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

               $test = "";
               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result_chart); $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result_chart);

                   if ($i > 0)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"" . ucfirst($ROW["CATEGORY"]) . "\",";
                   echo "\"value\": \"" . $ROW["c"] . "\",";

                   echo  "\"color\": \"" . $colourArray[$i % 30] . "\"";

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

echo "</head><body class='qt-site'><main class='qt-site-content'>";

include "library/back_to_top_button.php";

echo "<h2 class='page-title-text'>Page Usage Statistics</h2>";

echo "<div class='button-block-with-spacing'>";

echo "<a href='page_usage_statistics.php?FILTER=ALL'><button";
if ($filterLogs == "ALL")
{
    echo " style='font-weight:bold;'";
}
echo ">Count All Activity</button></a>";

echo "<a href='page_usage_statistics.php?FILTER=EXCLUDE_ADMINS'><button";
if ($filterLogs == "EXCLUDE_ADMINS")
{
    echo " style='font-weight:bold;'";
}
echo ">Exclude Activity by Admins</button></a>";

echo "<a href='page_usage_statistics.php?FILTER=ONLY_ADMINS'><button";
if ($filterLogs == "ONLY_ADMINS")
{
    echo " style='font-weight:bold;'";
}
echo ">Only Count Activity by Admins</button></a>";

echo "<button id=tableButton style='font-weight:bold;' onClick='table_view();'>";
echo "Display as Table";
echo "</button>";

echo "<button id=chartButton onClick='chart_view()';>";
echo "Display as Chart";
echo "</button>";

echo "</div>";

// print the table

// go to first database row
$result->data_seek(0);

echo "<div id='TableView'>";

echo "<table class='hoverTable persist-area qt-table'>";

echo "<thead>";

echo "<tr class='persist-header table-header-row'>";

echo "<th bgcolor=#c0c0c0 width='300'>Page";
echo "<span class='pull-right'><a href='page_usage_statistics.php?SORT=PAGE-ASC'><img src='../images/up.gif'></a> <a href='page_usage_statistics.php?SORT=PAGE-DESC'><img src='../images/down.gif'></a></span>";
echo "</th>";

echo "<th bgcolor=#c0c0c0 width='200'><b>Category</b>&nbsp;&nbsp;";
echo "<span class='pull-right'><a href='page_usage_statistics.php?SORT=CATEGORY-ASC'><img src='../images/up.gif'></a> <a href='page_usage_statistics.php?SORT=CATEGORY-DESC'><img src='../images/down.gif'></a></span>";

echo "</th>";

echo "<th bgcolor=#c0c0c0 width='150'><b>Load Count</b>&nbsp;&nbsp;";
echo "<span class='pull-right'><a href='page_usage_statistics.php?SORT=COUNT-ASC'><img src='../images/up.gif'></a> <a href='page_usage_statistics.php?SORT=COUNT-DESC'><img src='../images/down.gif'></a></span>";

echo "</th>";

echo "</tr>";

echo "</thead>";

echo "<tbody>";

for ($i = 0; $i < db_rowcount($result); $i++)
{
    // grab next database row
    $ROW = db_return_row($result);

    echo "<tr>";

    echo "<td width='300' class='left-align'>";
    echo $ROW["ACTUAL PAGE"] . ".php";
    echo "</td>";

    echo "<td width='200'>";
    echo $ROW["CATEGORY"];
    echo "</td>";

    echo "<td width='150'>";
    echo number_format($ROW["c"]);
    echo "</td>";

    echo "</tr>";
}

echo "</tbody>";

echo "</table>";

echo "</div>";

// graph

echo "<div id=PieView style='display: none;'>";
echo "</div>";

// print footer

include "library/footer.php";

?>

</body>
</html>