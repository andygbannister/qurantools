<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Verse Ending (Rhymes) Homogeneity per Sura");
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

// convert POST to GET
if (isset($_POST["L"]))
{
    $_GET["L"]    = $_POST["L"];
    $_GET["TYPE"] = $_POST["TYPE"];
}

// what sura

$SURA_TO_SHOW = 1;

if (isset($_GET["SURA"]))
{
    $SURA_TO_SHOW = $_GET["SURA"];

    if ($SURA_TO_SHOW < 1)
    {
        $SURA_TO_SHOW = 3;
    }
    if ($SURA_TO_SHOW > 114)
    {
        $SURA_TO_SHOW = 114;
    }
}

?>

<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

<?php

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

if ($_GET["SORT"] == "")
{
    $sort_field = "`Sura Number`";
}
else
{
    $sort_field = "`PERC_FIGURE` DESC";
}

$result = db_query("
    SELECT DISTINCT(`Sura Number`), `Provenance`, `Verses`, 
(SELECT COUNT(DISTINCT(`FINAL 2 LETTERS`)) FROM `QURAN-VERSE-ENDINGS` 
WHERE `Sura Number`=`SURA` AND `FINAL 2 LETTERS`!='**') DISTINCT_PATTERNS,
(((`VERSES`/(SELECT COUNT(DISTINCT(`FINAL 2 LETTERS`)) FROM `QURAN-VERSE-ENDINGS` 
WHERE `Sura Number`=`SURA` AND `FINAL 2 LETTERS`!='**')) * 100)/`VERSES`) PERC_FIGURE
FROM `SURA-DATA` ORDER BY $sort_field");

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        
        <?php
        if (!$miniMode)
        {
            echo "\"width\": \"1000\",";
            echo "\"height\": \"55%\",";
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
                "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            "xAxisName": "Sura",
            "yAxisName": "Rhyme/Verse Ending Homogeneity (%)",
            "yAxisMaxValue": "100.0",
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
                   echo "\"label\": \"" . $ROW["Sura Number"] . "\",";

                   // data point
                   echo "\"value\": \"" . number_format($ROW["PERC_FIGURE"], 2) . "\",";

                   // link
                   if (!$miniMode)
                   {
                       echo "link:\"../rhyme/sura_rhyme_analysis.php?SURA=" . $ROW["Sura Number"] . "\",";
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
    $expander_link = "../charts/chart_rhyme_homogeneity.php";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Verse Ending Pattern (Rhyme) Homogeneity per Sura</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "      <a href='chart_rhyme_homogeneity.php' class='$default_sort_selected'>";
    echo "        By Sura";
    echo "      </a>";
    echo "      <a href='chart_rhyme_homogeneity.php?SORT=1' class='$first_sort_option_selected'>";
    echo "        Verse Ending Pattern Homogeneity %";
    echo "      </a>";
    echo "    </div>"; // chart-control sort-by
}

echo "</div>";     // page-header

echo "</div>";     // $mini_normal_mode_class

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; height:220px;'";
}
echo "></div>";

include "./provenance_footer.php";

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
    
</body>
</html>