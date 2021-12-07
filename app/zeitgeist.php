<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

    // sort order
    $SORT_ORDER = "ORDER BY `Sura Number` ASC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($_GET["SORT"] == "SURA-ASC")
    {
        $SORT_ORDER = "ORDER BY `Sura Number` ASC";
    }

    if ($_GET["SORT"] == "SURA-DESC")
    {
        $SORT_ORDER = "ORDER BY `Sura Number` DESC";
    }

    if ($_GET["SORT"] == "TIMES-ASC")
    {
        $SORT_ORDER = "ORDER BY `Count`, `Sura Number` ASC";
    }

    if ($_GET["SORT"] == "TIMES-DESC")
    {
        $SORT_ORDER = "ORDER BY `Count` DESC, `Sura Number` ASC";
    }

// load the records

$result = db_query("SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `STATS-SURAS` WHERE `Sura Number`=`SURA`) Count FROM `SURA-DATA` $SORT_ORDER");

// GET CURRENT PAGE

if (isset($_GET["PAGE"]))
{
    $CURRENT_PAGE = $_GET["PAGE"];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET["PAGE"] = "";
}
?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Sura Browsing Stats");
        ?>
        
		<script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

		<script>

function show_as_table()
{
	document.getElementById('tableButton').style.fontWeight = 'bold';
	document.getElementById('chartButton').style.fontWeight = 'normal';
	document.getElementById('chartDiv').style.display = 'none';
	document.getElementById('tableDiv').style.display = 'block';
}

function show_as_chart()
{
	document.getElementById('chartButton').style.fontWeight = 'bold';
	document.getElementById('tableButton').style.fontWeight = 'normal';
	document.getElementById('chartDiv').style.display = 'block';
	document.getElementById('tableDiv').style.display = 'none';
}

  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartDiv",
        "width": "1000",
        "height": "600",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
                        
             <?php

           echo "\"xAxisName\": \"Sura\",";

             echo "\"yAxisName\": \"Times Accessed/Browsed\",";

            ?>
            
            
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php
       // POPULATE THE DATASET

               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   $ROW = db_return_row($result);

                   if ($i > 0)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"" . $ROW["Sura Number"] . "\",";

                   echo "\"value\": \"" . $ROW["Count"] . "\",";

                   // if we are showing hits per verse, then we colour code the bar based on the sura

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

    // menubar

    include "library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Sura Browsing Statistics</h2>";

    echo "<p>";
echo "<button id=tableButton style='margin-top:-10px; font-weight: bold;' onClick='show_as_table();'>Show Stats as Table</button>";

echo "<button id=chartButton style='margin-top:-10px;' onClick='show_as_chart();'>Show Stats as Chart</button>";
echo "</form>";

echo "</p>";

echo "<div id=tableDiv>";

echo "<table border=1 cellspacing=0 cellpadding=4 class='hoverTable'>";

    // table header

    echo "<tr><td bgcolor=#c0c0c0><b>Sura</b>&nbsp;<a href='zeitgeist.php?SORT=SURA-ASC'><img src='images/up.gif'></a> <a href='zeitgeist.php?SORT=SURA-DESC'><img src='images/down.gif'></a></td>
	
	<td bgcolor=#c0c0c0><b>Times Browsed/Accessed</b>&nbsp;<a href='zeitgeist.php?SORT=TIMES-ASC'><img src='images/up.gif'></a> <a href='zeitgeist.php?SORT=TIMES-DESC'><img src='images/down.gif'></a></td>
		
	</tr>";

    // go to first record

    db_goto($result, "FIRST");

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td align=center>" . $ROW["Sura Number"] . "</td>";

        echo "<td align=center>" . number_format($ROW["Count"]) . "</td>";

        echo "</tr>";
    }

echo "</table>";

echo "</div>";

echo "<div id=chartDiv style='display:none;'>";
echo "</div>";

// print footer

include "library/footer.php";

?>
	</body>
</html>