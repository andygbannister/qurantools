<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

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

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
        ?>
		<script>
   function reload_with_root(which_field)
   {
   	pass_root = "";
   	if (which_field == 1)
   	{
   		pass_root = document.getElementById("ROOT").value;	
   	}  	
   	else
   	{
   		pass_root = document.getElementById("ROOTARA").value;	
   	}
   	   	
   	window.location = "word_associations.php?ROOT="+pass_root; 	
   	
   }
   
   function display_results_table()
  {
  	// BUTTONS
  	document.getElementById('chartBarButton').style.fontWeight = 'normal';
  	document.getElementById('chartPieButton').style.fontWeight = 'normal';
  	document.getElementById('tableButton').style.fontWeight = 'block';	
  	
  	// DIVS
  	document.getElementById('chartContainer').style.display = 'none';
  	document.getElementById('Messages').style.display = 'none';
  	document.getElementById('TableData').style.display = 'block';
  	document.getElementById('chartContainerPie').style.display = 'none';
  	
  }
  
  function display_results_bar_chart()
  {
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
   
  function display_results_pie_chart()
  {
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

  $ROOT = "";
    if (isset($_GET["ROOT"]))
    {
        $ROOT = $_GET["ROOT"];
    }

    // if no root passed, offer the drop down list
    if ($ROOT == "")
    {
        window_title("Word Associations:Test");

        echo "</head><body class='qt-site'><main class='qt-site-content'>";

        include "library/menu.php";

        echo "<div align=center><h2 class='page-title-text'>Word Associations</h2>";

        echo "This word associations tool enables you to see which Arabic roots are frequently used with each other.";

        echo "<p>Please begin by choosing a root to analyse (from either the Arabic or transliteration menus below)</p>";

        echo "<form action='word_associations.php' method=POST>";

        echo "<div style='border:1px solid black; width:450px; background-color: #f4f4f4; margin-top: 10px;'><table cellpadding=4>";

        echo "<tr>";

        echo "<td>Transliterated Roots:</td><td>";

        echo "<select name=ROOT ID=ROOT onChange='reload_with_root(1);'>";

        $result = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

        echo "<option value=''>Choose Root</option>";

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            $ROW = db_return_row($result);
            echo "<option value='" . urlencode($ROW["ENGLISH"]) . "'>" . transliterate_new($ROW["ENGLISH"]) . "</option>";
        }
        echo "</select>";

        echo "</td></tr>";

        echo "<tr>";

        echo "<td>Arabic Roots:</td><td>";

        echo "<select name=ROOTARA ID=ROOTARA onChange='reload_with_root(2);'>";

        echo "<option value=''>Choose Root</option>";

        $resultRoot = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

        for ($i = 0; $i < db_rowcount($resultRoot); $i++)
        {
            $ROW = db_return_row($resultRoot);
            echo "<option value='" . urlencode($ROW["ENGLISH"]) . "'>" . $ROW["ARABIC"] . "</option>";
        }
        echo "</select>";

        echo "</td></tr>";

        echo "</table></form>";

        include "library/footer.php";

        echo "</body></html>";

        exit;
    }

    $ESCAPED_ROOT = db_quote($ROOT);

    window_title("Word Associations for " . return_arabic_word($ROOT) . " (" . convert_buckwalter($ROOT) . ")");

  // sort order
    $sort       = "C-DESC";
    $SORT_ORDER = "c DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($sort == "C-ASC")
    {
        $SORT_ORDER = "c ASC";
    }
    if ($sort == "D-DESC")
    {
        $SORT_ORDER = "c DESC";
    }

    if ($sort == "ARABIC-ASC")
    {
        $SORT_ORDER = "`ARABIC` ASC";
    }
    if ($sort == "ARABIC-DESC")
    {
        $SORT_ORDER = "`ARABIC` DESC";
    }

    if ($sort == "TRAN-ASC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED` ASC";
    }
    if ($sort == "TRAN-DESC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED` DESC";
    }

// load array of 30 colours for cycling through
include "library/colours.php";

  ?>
  
</title>

<script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
<script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
<script type="text/javascript" src="library/js/jquery-3.2.1.min.js"></script>
   <script type="text/javascript" src="library/js/persistent_table_headers.js"></script>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        "width": "960",
        "height": "65%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "subCaption": "",
            "xAxisName": "Root",
            "yAxisName": "Number of Times Occurs With <?php echo return_arabic_word($ROOT) . " (" . convert_buckwalter($ROOT) . ")"; ?> ",
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php

$sql = "SELECT t1.`SURA-VERSE` v, t1.`QTL-ROOT`, t1.`QTL-ROOT-TRANSLITERATED`, t2.`ARABIC`,
SUM(IF ((SELECT COUNT(*) FROM `QURAN-DATA` WHERE `SURA-VERSE`=t1.`SURA-VERSE` AND BINARY(`QTL-ROOT`)='$ESCAPED_ROOT') > 0, 1, 0)) c
FROM `QURAN-DATA` t1
LEFT JOIN `ROOT-LIST` t2 on t2.`ENGLISH-BINARY`=t1.`QTL-ROOT-BINARY`
WHERE t1.`QTL-ROOT`!='' AND t1.`QTL-ROOT`!='$ESCAPED_ROOT'
GROUP BY t1.`QTL-ROOT-BINARY`
ORDER BY $SORT_ORDER";

 $result = db_query($sql);

       if (db_rowcount($result) > $MAX_ITEMS_FOR_CHART)
       {
           $ITEMS = $MAX_ITEMS_FOR_CHART;
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
                   // grab next database row
                   $ROW = db_return_row($result);

                   if ($ROW["c"] > 0)
                   {
                       if ($i > 0)
                       {
                           echo ",";
                       }
                       echo "{";
                       echo "\"label\": \"" . convert_buckwalter($ROW["QTL-ROOT"]) . "\",";

                       echo "\"value\": \"" . $ROW["c"] . "\",";

                       echo "link:\"verse_browser.php?S=" . urlencode("ROOT:$ROOT AND ROOT:" . $ROW["QTL-ROOT"]) . "\",";

                       echo  "\"color\": \"#6060ff\"";

                       echo "}";
                   }
               }

       ?>
          ]
      }

  });
revenueChart.render();
})
</script>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "pie2d",
        "renderAt": "chartContainerPie",
        "width": "960",
        "height": "65%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "subCaption": "",
            "xAxisName": "Root",
            "yAxisName": "Number of Times Occurs With <?php echo return_arabic_word($ROOT) . " (" . convert_buckwalter($ROOT) . ")"; ?> ",
            "theme": "fint",
            "showValues": "1"
         },
         
       <?php

       // reset record pointer
       db_goto($result, 0);

       // POPULATE THE DATASET

       // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

               echo "\"data\": [";
               for ($i = 0; $i < $ITEMS; $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result);

                   if ($ROW["c"] > 0)
                   {
                       if ($i > 0)
                       {
                           echo ",";
                       }
                       echo "{";
                       echo "\"label\": \"" . convert_buckwalter($ROW["QTL-ROOT"]) . "\",";

                       echo "\"value\": \"" . $ROW["c"] . "\",";

                       echo "link:\"verse_browser.php?S=" . urlencode("ROOT:$ROOT AND ROOT:" . $ROW["QTL-ROOT"]) . "\",";

                       echo  "\"color\": \"" . $colourArray[$i % $colour_Choices] . "\"";

                       echo "}";
                   }
               }

               /*
            if ($capped)
               {
                   $others_count = 0;
                   for ($i = $ITEMS; $i <= db_rowcount($result); $i++)
                   {
                       $others_count += $ROW["c"];
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

  });
revenueChart.render();
})
</script>

</head>
<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "library/back_to_top_button.php";

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>Word Associations for " . return_arabic_word($ROOT) . " (" . convert_buckwalter($ROOT) . ")</h2>";

echo "<div style='margin-top:-15px; margin-bottom:15px;'>";
echo "<button id=tableButton onClick='display_results_table();' style='font-weight:bold;'>Display the Results as a Table</button>";
echo "<button id=chartBarButton onClick='display_results_bar_chart();'>Display the Results as a Bar Chart</button>";
echo "<button id=chartPieButton onClick='display_results_pie_chart();'>Display the Results as a Pie Chart</button>";
echo "<a href='word_associations.php'><button>Choose Another Root</button></a>";
echo "</div>";

// reset record pointer
db_goto($result, 0);

echo "<div id=TableData><table class='hoverTable persist-area'>";

echo "<thead class='persist-header table-header-row'>";

echo "<tr class='table-header-row'><th align=center bgcolor=#c0c0c0 colspan=2><b>Root</b></th><th bgcolor=#c0c0c0 rowspan=2 align=center><b>Number of Times Word<br>Occurs in a Verse with " . return_arabic_word($ROOT) . " (" . convert_buckwalter($ROOT) . ")&nbsp;<a href='word_associations.php?ROOT=$ROOT&SORT=C-ASC'><img src='images/up.gif'></a><a href='word_associations.php?ROOT=$ROOT&SORT=C-DESC'><img src='images/down.gif'></a></th><th bgcolor=#c0c0c0 rowspan=2 width=20>&nbsp;</th></tr>";

echo "<tr><th bgcolor=#c0c0c0><b>Arabic</b>&nbsp;<a href='word_associations.php?ROOT=$ROOT&SORT=ARABIC-ASC'><img src='images/up.gif'></a><a href='word_associations.php?ROOT=$ROOT&SORT=ARABIC-DESC'><img src='images/down.gif'></a></th><th bgcolor=#c0c0c0><b>Transliterated</b>&nbsp;<a href='word_associations.php?ROOT=$ROOT&SORT=TRAN-ASC'><img src='images/up.gif'></a><a href='word_associations.php?ROOT=$ROOT&SORT=TRAN-DESC'><img src='images/down.gif'></a></th></tr>";

echo "</thead>";

echo "<tbody>";

$count = 0;

for ($i = 0; $i < db_rowcount($result); $i++)
{
    // grab next database row
    $ROW = db_return_row($result);

    if ($ROW["c"] > 0)
    {
        $count++;

        // build HTML link
        $link1 = "<a href='verse_browser.php?S=" . urlencode("ROOT:$ROOT AND ROOT:" . $ROW["QTL-ROOT-TRANSLITERATED"]) . "' class=linky>";
        $link2 = "<a href='word_associations.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "' class=linky>";

        echo "<tr>";

        echo "<td align=center>$link2";
        echo $ROW["ARABIC"];
        echo "</td>";

        echo "<td align=center $user_preference_transliteration_style>$link2";
        echo convert_buckwalter($ROW["QTL-ROOT"]);
        echo "</td>";

        echo "<td align=center>$link1";
        echo number_format($ROW["c"]);
        echo "</a></td>";

        echo "<td width=20 align=center>";
        echo "<a href='examine_root.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "'><img src='images/info.gif'></a>";
        echo "</td>";

        echo "</tr>";
    }
}

echo "</tbody>";

echo "</table></div>";

echo "<div align=center id=Messages style='display: none;'>";
if ($count > $MAX_ITEMS_FOR_CHART)
{
    echo "<p><b>Chart shows only the top $MAX_ITEMS_FOR_CHART items (of " . number_format(db_rowcount($result)) . " in total)</b></p>";
}
echo "</div>";

echo "<div align=center id=chartContainer style='display: none;'></div>";

echo "<div align=center id=chartContainerPie style='display: none;'></div>";

include "library/footer.php";

?>

</body>
</html>