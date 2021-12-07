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

$TYPE        = 1;
$counting    = "Different Roots Used Per Sura";
$yAxisPrefix = "Different Roots";

if (isset($_GET["TYPE"]))
{
    if ($_GET["TYPE"] == 2)
    {
        $TYPE        = 2;
        $counting    = "Unique Roots Used Per Sura";
        $yAxisPrefix = "Unique Roots";
    }
    if ($_GET["TYPE"] == 3)
    {
        $TYPE     = 3;
        $counting = "Unique Words Used As % Of All Roots";
    }
    if ($_GET["TYPE"] == 4)
    {
        $TYPE        = 4;
        $counting    = "Hapax Legomena Per Sura";
        $yAxisPrefix = "Hapax Legomena";
    }
}

$extra = "";
$PROV  = "";

if (!isset($_GET["PROV"]))
{
    $_GET["PROV"] = "";
}

if (isset($_GET["COUNT"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $extra = "WHERE `Provenance`='Meccan'";
        $PROV  = "MECCAN";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $extra = "WHERE `Provenance`='Medinan'";
        $PROV  = "MEDINAN";
    }
}

$COUNT = "OCC";
if (isset($_GET["COUNT"]) && $TYPE != 3)
{
    if ($_GET["COUNT"] == "PER100")
    {
        $COUNT = "PER100";
    }
}

window_title("Chart of $counting");

?>
	<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
	<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
	<!-- <link rel="stylesheet" type="text/css" href="../library/menubar.css"> -->
<?php

// build sort critiera
$sort_order = "ORDER BY `Sura Number`";
if ($_GET["SORT"] == 1)
{
    if ($COUNT != "PER100")
    {
        if ($TYPE == 1)
        {
            $sort_order = "ORDER BY `Root Count Different` DESC";
        }
        if ($TYPE == 2)
        {
            $sort_order = "ORDER BY `Root Count Unique` DESC";
        }
        if ($TYPE == 3)
        {
            $sort_order = "ORDER BY (`Root Count Unique` / `Root Count Different`) DESC";
        }
        if ($TYPE == 4)
        {
            $sort_order = "ORDER BY `Root Count Hapax` DESC";
        }
    }
    else
    {
        if ($TYPE == 1)
        {
            $sort_order = "ORDER BY (`Root Count Different` / (`Words`/ 100)) DESC";
        }
        if ($TYPE == 2)
        {
            $sort_order = "ORDER BY (`Root Count Unique` / (`Words`/ 100)) DESC";
        }
        if ($TYPE == 4)
        {
            $sort_order = "ORDER BY (`Root Count Hapax` / (`Words`/ 100)) DESC";
        }
    }
}

$result = db_query("SELECT * FROM `SURA-DATA` $extra $sort_order");

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        
        <?php

        if (!$miniMode)
        {
            echo "\"width\": \"960\",";
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
            "subCaption": "",
            "xAxisName": "Sura",
            
             <?php

             if ($TYPE == 3)
             {
                 echo "\"yAxisName\": \"Unique Words Used As % Of All Roots\",";
             }
             else
             {
                 if ($COUNT == "OCC")
                 {
                     echo "\"yAxisName\": \"$yAxisPrefix Used\",";
                 }
                 else
                 {
                     echo "\"yAxisName\": \"$yAxisPrefix Used per 100 Words\",";
                 }
             }
            ?>
            
            
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

                   if ($TYPE == 1)
                   {
                       if ($COUNT == "OCC")
                       {
                           echo "\"value\": \"" . $ROW["Root Count Different"] . "\",";
                       }

                       if ($COUNT == "PER100")
                       {
                           echo "\"value\": \"" . number_format($ROW["Root Count Different"] / ($ROW["Words"] / 100), 2) . "\",";
                       }
                   }

                   if ($TYPE == 2)
                   {
                       if ($COUNT == "OCC")
                       {
                           echo "\"value\": \"" . $ROW["Root Count Unique"] . "\",";
                       }

                       if ($COUNT == "PER100")
                       {
                           echo "\"value\": \"" . number_format($ROW["Root Count Unique"] / ($ROW["Words"] / 100), 2) . "\",";
                       }
                   }

                   if ($TYPE == 3)
                   {
                       echo "\"value\": \"" . number_format(($ROW["Root Count Unique"] * 100) / $ROW["Root Count Different"], 2) . "\",";
                   }

                   if ($TYPE == 4)
                   {
                       if ($COUNT == "OCC")
                       {
                           echo "\"value\": \"" . $ROW["Root Count Hapax"] . "\",";
                       }

                       if ($COUNT == "PER100")
                       {
                           echo "\"value\": \"" . number_format($ROW["Root Count Hapax"] / ($ROW["Words"] / 100), 2) . "\",";
                       }
                   }

                   if ($TYPE == 1 && !$miniMode)
                   {
                       echo "link:\"../counts/count_all_roots.php?SURA=" . $ROW["Sura Number"] . "\",";
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
    $expander_link = "../charts/chart_root_usage.php?TYPE=$TYPE";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>$counting</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_root_usage.php?COUNT=$COUNT&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_root_usage.php?PROV=MECCAN&COUNT=$COUNT&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_root_usage.php?PROV=MEDINAN&COUNT=$COUNT&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    if ($TYPE != 3)
    {
        // Chart occurrences control =====

        echo "    <div class='chart-control occurrences'>";
        echo "      <span class='label'>Count</span>";
        echo "      <a href='chart_root_usage.php?COUNT=OCC&PROV=$PROV&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='$all_occurrences_selected'>";
        echo "        All Occurrences";
        echo "      </a>";
        echo "      <a href='chart_root_usage.php?COUNT=PER100&PROV=$PROV&TYPE=$TYPE&SORT=" . $_GET["SORT"] . "' class='$occurrences_per_100_words_selected'>";
        echo "        Occurrences per 100 Words";
        echo "      </a>";
        echo "    </div>";  // chart-control occurrences
    }

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_root_usage.php?PROV=$PROV&COUNT=$COUNT&TYPE=$TYPE' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_root_usage.php?PROV=$PROV&COUNT=$COUNT&TYPE=$TYPE&SORT=1' class='$first_sort_option_selected'>";
    echo "          Value";
    echo "        </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}

echo "</div>";     // page-header

echo "</div>"; // $mini_normal_mode_class'

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; height:220px;'";
}
echo "></div>";

if ($extra == "")
{
    include "./provenance_footer.php";
}

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
    
</body>
</html>