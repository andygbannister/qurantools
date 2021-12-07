<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Grammatical Features By Sura");
        ?>
			
<?php

// convert POST to GET
if (isset($_POST["WHAT"]))
{
    $_GET["WHAT"] = $_POST["WHAT"];
}
if (isset($_POST["WHICH"]))
{
    $_GET["WHICH"] = $_POST["WHICH"];
}
if (isset($_POST["AUTO_ADJUST_AXIS"]))
{
    $_GET["AUTO_ADJUST_AXIS"] = $_POST["AUTO_ADJUST_AXIS"];
}

// auto adjust Y axis

$auto_adjust_axis = false;
if (isset($_GET["AUTO_ADJUST_AXIS"]))
{
    $auto_adjust_axis = $_GET["AUTO_ADJUST_AXIS"];
}

// chart what
$CHART_WHAT = "GENDER";

if (isset($_GET["WHAT"]))
{
    $CHART_WHAT = $_GET["WHAT"];
}

// chart which
$CHART_WHICH = "";

if (isset($_GET["WHICH"]))
{
    $CHART_WHICH = $_GET["WHICH"];
}

// error check values

if ($CHART_WHAT == "MOOD")
{
    if ($CHART_WHICH != "Jussive" && $CHART_WHICH != "Subjunctive")
    {
        $CHART_WHICH = "Indicative";
    }
}

if ($CHART_WHAT == "CASE")
{
    if ($CHART_WHICH != "Nominative" && $CHART_WHICH != "Accusative" && $CHART_WHICH != "Genitive")
    {
        $CHART_WHICH = "Nominative";
    }
}

if ($CHART_WHAT == "GENDER")
{
    if ($CHART_WHICH != "Masculine" && $CHART_WHICH != "Feminine")
    {
        $CHART_WHICH = "Masculine";
    }
}

if ($CHART_WHAT == "NUMBER")
{
    if ($CHART_WHICH != "SINGULAR" && $CHART_WHICH != "DUAL" && $CHART_WHICH != "PLURAL")
    {
        $CHART_WHICH = "SINGULAR";
    }
}

if ($CHART_WHAT == "PERSON")
{
    if ($CHART_WHICH != "1P" && $CHART_WHICH != "2P" && $CHART_WHICH != "3P")
    {
        $CHART_WHICH = "1P";
    }
}

if ($CHART_WHAT == "FORM")
{
    if ($CHART_WHICH != "I" && $CHART_WHICH != "II" && $CHART_WHICH != "III" && $CHART_WHICH != "IV" && $CHART_WHICH != "V" && $CHART_WHICH != "VI" && $CHART_WHICH != "VII" && $CHART_WHICH != "VIII" && $CHART_WHICH != "IX" && $CHART_WHICH != "X" && $CHART_WHICH != "XI" && $CHART_WHICH != "XII")
    {
        $CHART_WHICH = "I";
    }
}

// filter by provenance

$filter_by_provenance = "";
$PROV                 = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $filter_by_provenance = "WHERE `PROVENANCE`='Meccan'";
        $PROV                 = "MECCAN";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $filter_by_provenance = "WHERE `PROVENANCE`='Medinan'";
        $PROV                 = "MEDINAN";
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
    $sort_field = "percentage DESC";
}

if ($CHART_WHAT == "GENDER")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-GENDER`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-GENDER`!='') percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

if ($CHART_WHAT == "PERSON")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-PERSON`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-PERSON`!='') percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

if ($CHART_WHAT == "NUMBER")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-NUMBER`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-NUMBER`!='') percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

if ($CHART_WHAT == "FORM")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-ARABIC-FORM`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-ARABIC-FORM`!='') percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

if ($CHART_WHAT == "CASE")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `TAG EXPLAINED`='NOUN' AND `QTL-CASE`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `TAG EXPLAINED`='NOUN') percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

if ($CHART_WHAT == "MOOD")
{
    $sql = "SELECT `Sura Number`, `Provenance`, (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number` AND `QTL-TAG-EXPLAINED`='Imperfect Verb' AND `QTL-MOOD`='$CHART_WHICH') / (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t2.`SURA`=t1.`Sura Number`) percentage FROM `SURA-DATA` t1 $filter_by_provenance ORDER BY $sort_field";
}

$result = db_query($sql);

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        "width": "1000",
        "height": "55%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
                "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            "xAxisName": "Sura",
            
            <?php

            if (!$auto_adjust_axis)
            {
                echo "\"yAxisMaxValue\": \"100\",";
            }
            ?>
            
             <?php
            if ($CHART_WHAT == "FORM")
            {
                echo "\"yAxisName\": \"Percentage of Words with this Morphology\",";
            }
            else
            {
                echo "\"yAxisName\": \"Percentage of Words with this Inflection\",";
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

                   // data point
                   echo "\"value\": \"" . ($ROW["percentage"] * 100) . "%\",";

                   if ($CHART_WHAT == "CASE")
                   {
                       echo "link:\"../verse_browser.php?S=" . urlencode("[NOUN " . strtoupper($CHART_WHICH) . "] RANGE:" . $ROW["Sura Number"]) . "\",";
                   }

                   if ($CHART_WHAT == "GENDER" || $CHART_WHAT == "PERSON" || $CHART_WHAT == "NUMBER")
                   {
                       echo "link:\"../verse_browser.php?S=" . urlencode("[" . strtoupper($CHART_WHICH) . "] RANGE:" . $ROW["Sura Number"]) . "\",";
                   }

                   if ($CHART_WHAT == "FORM")
                   {
                       echo "link:\"../verse_browser.php?S=" . urlencode("[FORM:" . $CHART_WHICH . "] RANGE:" . $ROW["Sura Number"]) . "\",";
                   }

                   if ($CHART_WHAT == "MOOD")
                   {
                       echo "link:\"../verse_browser.php?S=" . urlencode("[$CHART_WHICH] RANGE:" . $ROW["Sura Number"]) . "\",";
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

include "../library/menu.php";

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='page-header'>";

echo "  <h2>Grammatical Features by Sura</h2>";

echo "  <div class='chart-controls'>";

echo "    <div class='chart-control grammar-choosers'>"; // contains grammar choosers (for now)

echo "<form action='chart_grammatical.php' method=POST style='margin-top:-10px;margin-bottom:0px;'>";

echo "Showing the percentage of words whose ";

echo "<select name=WHAT onChange='this.form.submit();'>";
echo "<option value='GENDER'";
if ($CHART_WHAT == "GENDER")
{
    echo " selected";
}
echo ">gender</option>";

echo "<option value='PERSON'";
if ($CHART_WHAT == "PERSON")
{
    echo " selected";
}
echo ">person</option>";

echo "<option value='NUMBER'";
if ($CHART_WHAT == "NUMBER")
{
    echo " selected";
}
echo ">number</option>";

echo "<option value='FORM'";
if ($CHART_WHAT == "FORM")
{
    echo " selected";
}
echo ">form</option>";

echo "<option value='CASE'";
if ($CHART_WHAT == "CASE")
{
    echo " selected";
}
echo ">case</option>";

echo "<option value='MOOD'";
if ($CHART_WHAT == "MOOD")
{
    echo " selected";
}
echo ">mood</option>";

echo "</select>";

echo " is ";

echo "<select name=WHICH onChange='this.form.submit();'>";

if ($CHART_WHAT == "MOOD")
{
    echo "<option value='Indicative'";
    if ($CHART_WHICH == "Indicative")
    {
        echo " selected";
    }
    echo ">indicative</option>";

    echo "<option value='Jussive'";
    if ($CHART_WHICH == "Jussive")
    {
        echo " selected";
    }
    echo ">jussive</option>";

    echo "<option value='Subjunctive'";
    if ($CHART_WHICH == "Subjunctive")
    {
        echo " selected";
    }
    echo ">subjunctive</option>";
}

if ($CHART_WHAT == "GENDER")
{
    echo "<option value='Masculine'";
    if ($CHART_WHICH == "Masculine")
    {
        echo " selected";
    }
    echo ">masculine</option>";

    echo "<option value='Feminine'";
    if ($CHART_WHICH == "Feminine")
    {
        echo " selected";
    }
    echo ">feminine</option>";
}

if ($CHART_WHAT == "NUMBER")
{
    echo "<option value='SINGULAR'";
    if ($CHART_WHICH == "SINGULAR")
    {
        echo " selected";
    }
    echo ">singular</option>";

    echo "<option value='DUAL'";
    if ($CHART_WHICH == "DUAL")
    {
        echo " selected";
    }
    echo ">dual</option>";

    echo "<option value='PLURAL'";
    if ($CHART_WHICH == "PLURAL")
    {
        echo " selected";
    }
    echo ">plural</option>";
}

if ($CHART_WHAT == "PERSON")
{
    echo "<option value='1P'";
    if ($CHART_WHICH == "1P")
    {
        echo " selected";
    }
    echo ">1st person</option>";

    echo "<option value='2P'";
    if ($CHART_WHICH == "2P")
    {
        echo " selected";
    }
    echo ">2nd person</option>";

    echo "<option value='3P'";
    if ($CHART_WHICH == "3P")
    {
        echo " selected";
    }
    echo ">3rd person</option>";
}

if ($CHART_WHAT == "FORM")
{
    echo "<option value='I'";
    if ($CHART_WHICH == "I")
    {
        echo " selected";
    }
    echo ">I</option>";

    echo "<option value='II'";
    if ($CHART_WHICH == "II")
    {
        echo " selected";
    }
    echo ">II</option>";

    echo "<option value='III'";
    if ($CHART_WHICH == "III")
    {
        echo " selected";
    }
    echo ">III</option>";

    echo "<option value='IV'";
    if ($CHART_WHICH == "IV")
    {
        echo " selected";
    }
    echo ">IV</option>";

    echo "<option value='V'";
    if ($CHART_WHICH == "V")
    {
        echo " selected";
    }
    echo ">V</option>";

    echo "<option value='VI'";
    if ($CHART_WHICH == "VI")
    {
        echo " selected";
    }
    echo ">VI</option>";

    echo "<option value='VII'";
    if ($CHART_WHICH == "VII")
    {
        echo " selected";
    }
    echo ">VII</option>";

    echo "<option value='VIII'";
    if ($CHART_WHICH == "VIII")
    {
        echo " selected";
    }
    echo ">VIII</option>";

    echo "<option value='IX'";
    if ($CHART_WHICH == "IX")
    {
        echo " selected";
    }
    echo ">IX</option>";

    echo "<option value='X'";
    if ($CHART_WHICH == "X")
    {
        echo " selected";
    }
    echo ">X</option>";

    echo "<option value='XI'";
    if ($CHART_WHICH == "XI")
    {
        echo " selected";
    }
    echo ">XI</option>";

    echo "<option value='XII'";
    if ($CHART_WHICH == "XII")
    {
        echo " selected";
    }
    echo ">XII</option>";
}

if ($CHART_WHAT == "CASE")
{
    echo "<option value='Nominative'";
    if ($CHART_WHICH == "Nominative")
    {
        echo " selected";
    }
    echo ">Nominative</option>";

    echo "<option value='Accusative'";
    if ($CHART_WHICH == "Accusative")
    {
        echo " selected";
    }
    echo ">Accusative</option>";

    echo "<option value='Genitive'";
    if ($CHART_WHICH == "Genitive")
    {
        echo " selected";
    }
    echo ">Genitive</option>";
}

echo "</select>";
echo "    </div>";   // chart-control grammar-chooser

// Autoscale-axis control
echo "    <div class='chart-control axis-scaler' style='margin-top:2px;'>"; // contains grammar choosers (for now)

echo "      <label><input type=checkbox name=AUTO_ADJUST_AXIS value=1";
if ($auto_adjust_axis)
{
    echo " checked";
}
echo " onChange='this.form.submit();'>";
echo "        Auto Scale Y-Axis</label>";

echo        "</form>";
echo "    </div>"; // chart-control autoscale-axis

echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

// Provenance control =====

echo "    <div class='chart-control provenance'>";
echo "	    <span class='label'>Show</span>";
echo "  	  <a href='chart_grammatical.php?WHAT=$CHART_WHAT&WHICH=$CHART_WHICH&SORT=" . $_GET["SORT"] . "	&AUTO_ADJUST_AXIS=$auto_adjust_axis' class='" . $all_suras_selected . "'>";
echo "	  	  All Suras";
echo "	    </a>";

echo "	    <a href='chart_grammatical.php?PROV=MECCAN&WHAT=$CHART_WHAT&WHICH=$CHART_WHICH&SORT=" . $_GET["SORT"] . "&AUTO_ADJUST_AXIS=$auto_adjust_axis' class='" . $meccan_suras_selected . "'>";
echo "		    Meccan";
echo "	    </a>";

echo "	    <a href='chart_grammatical.php?PROV=MEDINAN&WHAT=$CHART_WHAT&WHICH=$CHART_WHICH&SORT=" . $_GET["SORT"] . "&AUTO_ADJUST_AXIS=$auto_adjust_axis' class='" . $medinan_suras_selected . "'>";
echo "		    Medinan";
echo "	    </a>";
echo "    </div>"; // chart-control provenance

// Sort control  =====

echo "    <div class='chart-control sort-by'>";
echo "      <span class='label'>Sort By</span>";
echo "        <a href='chart_grammatical.php?WHAT=$CHART_WHAT&WHICH=$CHART_WHICH&PROV=$PROV&AUTO_ADJUST_AXIS=$auto_adjust_axis' class='$default_sort_selected'>";
echo "          Sura Number";
echo "        </a>";
echo "        <a href='chart_grammatical.php?WHAT=$CHART_WHAT&WHICH=$CHART_WHICH&PROV=$PROV&SORT=1&AUTO_ADJUST_AXIS=$auto_adjust_axis' class='$first_sort_option_selected'>";
echo "          Percentage";
echo "        </a>";
echo "    </div>"; // chart-control sort-by

echo "  </div>";   // chart-controls
echo "</div>";     // page-header

?>

  <div id="chartContainer" class="chart-container"></div>
  
<?php
    if ($filter_by_provenance == "")
    {
        include "provenance_footer.php";
    }

    include "library/print_control.php";
    include "library/footer.php";

?>  
    
</body>
</html>