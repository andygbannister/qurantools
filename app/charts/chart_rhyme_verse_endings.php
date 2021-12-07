<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Verse Ending (Rhyme) Frequency");
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
if (isset($_POST["SURA"]))
{
    $_GET["SURA"] = $_POST["SURA"];
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
    $sort_field = "`FINAL 2 LETTERS`";
}
else
{
    $sort_field = "count DESC";
}

$result = db_query("
SELECT DISTINCT(`FINAL 2 LETTERS`), COUNT(*) count 
FROM `QURAN-VERSE-ENDINGS` WHERE `SURA`=" . db_quote($SURA_TO_SHOW) . " AND `FINAL 2 LETTERS`!='**' 
GROUP BY `FINAL 2 LETTERS`
ORDER BY $sort_field");

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
            "xAxisName": "Final 2 Letters of Verse (Rhyme Pattern)",
            "yAxisName": "Occurrences in Sura <?php echo $SURA_TO_SHOW;?>",
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
                   echo "\"label\": \"" . $ROW["FINAL 2 LETTERS"] . "\",";

                   // data point
                   echo "\"value\": \"" . number_format($ROW["count"]) . "\",";

                   // link
                   if (!$miniMode)
                   {
                       echo "link:\"../verse_browser.php?S=[POSITION:FINAL ENDS:" . $ROW["FINAL 2 LETTERS"] . "] RANGE:$SURA_TO_SHOW\",";
                   }

                   echo  "\"color\": \"#6060ff\"";

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
    $expander_link = "../charts/chart_rhyme_verse_endings.php?SURA=$SURA_TO_SHOW";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Verse Ending (Rhyme) Frequency</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    echo "<form action='chart_rhyme_verse_endings.php?SURA=$SURA_TO_SHOW' method=POST>";

    echo "<div class='formulaic-pick-table'><table>";

    echo "<tr>";

    echo "<td>Sura to Analyse</td><td>";
    echo "<select name=SURA onChange='this.form.submit();'>";

    for ($j = 1; $j <= 114; $j++)
    {
        echo "<option value='$j'";
        if ($SURA_TO_SHOW == $j)
        {
            echo " selected";
        }
        echo ">$j</option>";
    }
    echo "</select>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    // echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "      <a href='chart_rhyme_verse_endings.php?SURA=$SURA_TO_SHOW' class='$default_sort_selected'>";
    echo "        Alphabetically by Pattern";
    echo "      </a>";
    echo "      <a href='chart_rhyme_verse_endings.php?SURA=$SURA_TO_SHOW&SORT=1' class='$first_sort_option_selected'>";
    echo "        Number of Occurrences";
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

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
    
</body>
</html>