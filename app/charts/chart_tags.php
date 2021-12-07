<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Tagged Verses");
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

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

?>

<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

<script type="text/javascript">
  FusionCharts.ready(function(){
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
        "dataSource":  {
          "chart": {
            "caption": "",
               "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            "labelDisplay": "ROTATE",
            "slantLabels": "1",
            <?php

            echo "\"xAxisName\": \"Tag Name\",";

            ?>
            
            <?php

            if (!$miniMode)
            {
                echo "\"yAxisName\": \"Verses Tagged\",";
            }
            else
            {
                echo "\"yAxisName\": \"Verses Tagged With This Tag\",";
            }

            ?>
            
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php
       // POPULATE THE DATASET

               $sort_field = "UPPER(`Tag Name`)";

               if ($_GET["SORT"] == 1)
               {
                   $sort_field = "C DESC";
               }

               $result = db_query("SELECT DISTINCT(`Tag Name`), `Tag Colour`, `Tag Lightness Value`, count(`SURA-VERSE`) C FROM `TAGS` T1 
LEFT JOIN `TAGGED-VERSES` T2 ON T1.`ID`=T2.`TAG ID`
WHERE T1.`User ID`='" . db_quote($_SESSION['UID']) . "'
GROUP BY `Tag Name` ORDER BY $sort_field");

                $count = 0;

               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   // count how many columns we have actually rendered
                   $count++;

                   if ($count > 1)
                   {
                       echo ",";
                   }

                   // grab next database row
                   $ROW = db_return_row($result);

                   echo "{";
                   echo "\"label\": \"" . $ROW["Tag Name"] . "\",";
                   echo "\"value\": \"" . $ROW["C"] . "\",";

                   if ($ROW["Tag Lightness Value"] < 230)
                   {
                       echo "\"color\": \"" . $ROW["Tag Colour"] . "\",";
                   }

                   if (!$miniMode && $ROW["C"] > 0)
                   {
                       echo "link:\"../verse_browser.php?S=TAG:" . urlencode("\"" . $ROW["Tag Name"] . "\"") . "\",";
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
    echo "<span style='float:right; margin-right:10px;'>";
    echo "  <a href='/charts/chart_tags.php'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Number of Verses Tagged With Each Tag</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "      <a href='chart_tags.php' class='$default_sort_selected'>";
    echo "        Tag Name";
    echo "      </a>";
    echo "      <a href='chart_tags.php?SORT=1' class='$first_sort_option_selected'>";
    echo "        Number of Verses Tagged";
    echo "      </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}

echo "</div>";     // page-header

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:450px; height:250px;'";
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