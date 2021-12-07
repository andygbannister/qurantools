<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Average Word Length per Sura");
        ?>
	

<?php

// provenance filtering

$PROV  = "";
$extra = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $PROV = "MECCAN";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $PROV = "MEDINAN";
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
            // "exportEnabled": "1",
            "caption": "",
               "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            <?php

            echo "\"xAxisName\": \"Sura\",";

            ?>
            
            <?php

            if (!$miniMode)
            {
                echo "\"yAxisName\": \"Average Word Length\",";
            }
            else
            {
                echo "\"yAxisName\": \"Average Word Length\",";
            }

            ?>
            
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php
       // POPULATE THE DATASET

               $sort_field = "`SURA`";

               if ($_GET["SORT"] == 1)
               {
                   $sort_field = "C DESC";
               }

               $result = db_query("SELECT DISTINCT(`SURA`), AVG(CHAR_LENGTH(`RENDERED ARABIC`)) C, `Provenance` FROM `QURAN-DATA`
LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `SEGMENT`=1 GROUP BY `SURA` ORDER BY $sort_field");

               $test  = "";
               $count = 0;
               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result);

                   if ($PROV != "")
                   {
                       if (strtoupper($ROW["Provenance"]) != $PROV)
                       {
                           continue;
                       }
                   }

                   // count how many columns we have actually rendered
                   $count++;

                   if ($count > 1)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"" . $ROW["SURA"] . "\",";
                   echo "\"value\": \"" . $ROW["C"] . "\",";

                   if (!$miniMode)
                   {
                       echo "link:\"../verse_browser.php?V=" . $ROW["SURA"] . "\",";
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
    echo "<span style='float:right; margin-right:10px;'>";
    echo "  <a href='/charts/chart_average_word_length.php'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Average Word Length per Sura</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_average_word_length.php?" . "&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	    	All Suras";
    echo "	    </a>";

    echo "  	  <a href='chart_average_word_length.php?PROV=MECCAN&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "	    	Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_average_word_length.php?PROV=MEDINAN&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "  	  </a>";
    echo "    </div>"; // chart-control provenance

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "      <a href='chart_average_word_length.php?PROV=$PROV' class='$default_sort_selected'>";
    echo "        Sura";
    echo "      </a>";
    echo "      <a href='chart_average_word_length.php?PROV=$PROV&SORT=1' class='$first_sort_option_selected'>";
    echo "        Average Word Length";
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

    if ($PROV == "" && !$miniMode)
    {
        include "./provenance_footer.php";
    }

if (!$miniMode)
{
    echo "<div class='provenance-legend' style='font-size:10pt;'>";
    echo "</div>";

    include "library/print_control.php";
    include "library/footer.php";
}

?>  
   
</body>
</html>