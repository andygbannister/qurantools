<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Intertextual Links per Source");
        ?>


<?php

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
        "width": "960",
        "height": "70%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
               "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
			"labeldisplay": "rotate",
			"slantLabel": "1",
            "subCaption": "",
            <?php

            echo "\"xAxisName\": \"Source\",";

            ?>
            
            "yAxisName": "Number of Intertextual Links to Source",
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php
       // POPULATE THE DATASET

               $sort_field = "`SOURCE NAME`";

               if ($_GET["SORT"] == 1)
               {
                   $sort_field = "`TC` DESC";
               }

               $result = db_query("SELECT *, (SELECT COUNT(*) FROM `INTERTEXTUAL LINKS` WHERE `SOURCE ID`=`SOURCE`) TC FROM `INTERTEXTUAL SOURCES` ORDER BY $sort_field");

               $test  = "";
               $count = 0;
               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result);

                   // count how many columns we have actually rendered
                   $count++;

                   if ($count > 1)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"";

                   echo $ROW["SOURCE NAME"];

                   echo "\",";
                   echo "\"value\": \"" . $ROW["TC"] . "\",";

                   echo "link:\"../verse_browser.php?V=" . urlencode($ROW["VERSE REFERENCES"]) . "\",";
                   echo  "\"color\": \"#404090\"";

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

include "../library/menu.php";

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='normal-mode'>";

echo "<div class='page-header'>";

echo "  <h2>Intertextual Links per Source <a href='/intertextuality/intertextual_browser.php'><img src='/images/table.png'></a></h2>";

echo "  <div class='chart-controls'>";

// Sort control  =====

echo "    <div class='chart-control sort-by'>";

echo "      <span class='label'>Sort By</span>";
echo "      <a href='chart_intertextual_links_per_source.php' class='$default_sort_selected'>";
echo "        Source Name";
echo "      </a>";
echo "      <a href='chart_intertextual_links_per_source.php?SORT=1' class='$first_sort_option_selected'>";
echo "        Number of Links";
echo "      </a>";
echo "    </div>"; // chart-control sort-by

echo "  </div>";   // chart-controls
echo "</div>";     // page-header

echo "</div>";     // normal-mode

?>

  <div align=center id="chartContainer"></div>
<?php
    include "library/print_control.php";
    include "../library/footer.php";

?>  
   
</body>
</html>