<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Formulaic Density By Sura");
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

// what formulae type

$FORMULA_LENGTH = 3;

if (isset($_GET["L"]))
{
    $FORMULA_LENGTH = $_GET["L"];
    if ($FORMULA_LENGTH < 3)
    {
        $FORMULA_LENGTH = 3;
    }
    if ($FORMULA_LENGTH > 5)
    {
        $FORMULA_LENGTH = 5;
    }
}

$FORMULA_TYPE = "ROOT";

if (isset($_GET["TYPE"]))
{
    $FORMULA_TYPE = $_GET["TYPE"];
    if ($FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "ANY")
    {
        $FORMULA_TYPE = "ROOT";
    }
}

if ($FORMULA_TYPE == "ROOT-ALL" && $FORMULA_LENGTH == 2)
{
    $FORMULA_LENGTH = 3;
}

$extra = "";
$PROV  = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $extra = "AND `PROVENANCE`='Meccan'";
        $PROV  = "MECCAN";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $extra = "AND `PROVENANCE`='Medinan'";
        $PROV  = "MEDINAN";
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
    $sort_field = "`SURA`";
}
else
{
    $sort_field = "FORMULAIC_DENSITY DESC";
}

if ($FORMULA_TYPE == "ROOT")
{
    $result = db_query("SELECT DISTINCT(`SURA`), SUM(`QTL-ROOT`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`QTL-ROOT`!='') FORMULAIC_DENSITY, `PROVENANCE` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' $extra GROUP BY `SURA` ORDER BY $sort_field");
}

if ($FORMULA_TYPE == "LEMMA")
{
    $result = db_query("SELECT DISTINCT(`SURA`), SUM(`QTL-LEMMA`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`QTL-LEMMA`!='') FORMULAIC_DENSITY, `PROVENANCE` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' $extra GROUP BY `SURA` ORDER BY $sort_field");
}

if ($FORMULA_TYPE == "ROOT-ALL" || $FORMULA_TYPE == "ANY")
{
    $result = db_query("SELECT DISTINCT(`SURA`), SUM(`ROOT OR PARTICLE`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`ROOT OR PARTICLE`!='') FORMULAIC_DENSITY, `PROVENANCE` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' $extra GROUP BY `SURA` ORDER BY $sort_field");
}

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
            "yAxisName": " <?php
            echo str_ireplace("per sura", "", "Formulaic Density");
            ?>",
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
                   echo "\"label\": \"" . $ROW["SURA"] . "\",";

                   // data point
                   echo "\"value\": \"" . number_format(($ROW["FLAGGED"] * 100 / $ROW["ALL_ROOTS"]), 2) . "\",";

                   // link
                   if (!$miniMode)
                   {
                       echo "link:\"../verse_browser.php?V=" . $ROW["SURA"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE\",";
                   }

                   if ($ROW["PROVENANCE"] == "Meccan")
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
    $expander_link = "../charts/chart_formulaic_density.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Formulaic Density by Sura</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // ==== formula length and type selection form =====

    echo "<form action='chart_formulaic_density.php?PROV=$PROV&SORT=" . $_GET["SORT"] . "' method=POST>";

    echo "<div class='formulaic-pick-table'><table>";

    echo "<tr>";

    echo "<td>Formula Length</td><td>";
    echo "<input type=radio name=L value=3 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 3)
    {
        echo "checked=checked";
    }
    echo "> 3</input>";
    echo " &nbsp;&nbsp;<input type=radio name=L value=4 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 4)
    {
        echo "checked=checked";
    }
    echo "> 4</input>";
    echo " &nbsp;&nbsp;<input type=radio name=L value=5 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 5)
    {
        echo "checked=checked";
    }
    echo "> 5</input>";

    echo "</td></tr>";

    echo "<tr>";

    echo "<td>Formula Type</td><td>";
    echo "<select name=TYPE onChange='this.form.submit();'>";
    echo "<option value='ROOT'";
    if ($FORMULA_TYPE == "ROOT")
    {
        echo " selected";
    }
    echo ">Root</option>";

    echo "<option value='ROOT-ALL'";
    if ($FORMULA_TYPE == "ROOT-ALL")
    {
        echo " selected";
    }
    echo ">Root (Plus Particle/Pronouns)</option>";

    echo "<option value='LEMMA'";
    if ($FORMULA_TYPE == "LEMMA")
    {
        echo " selected";
    }
    echo ">Lemmata</option>";

    echo "<option value='ANY'";
    if ($FORMULA_TYPE == "ANY")
    {
        echo " selected";
    }
    echo ">All Formula Types</option>";

    echo "</select>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_formulaic_density.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_formulaic_density.php?PROV=MECCAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_formulaic_density.php?PROV=MEDINAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "      <a href='chart_formulaic_density.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=$PROV' class='$default_sort_selected'>";
    echo "        Sura Number";
    echo "      </a>";
    echo "      <a href='chart_formulaic_density.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=$PROV&SORT=1' class='$first_sort_option_selected'>";
    echo "        Density";
    echo "      </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}
else
{
    echo "<div align=center>";
    echo "Formula Length: $FORMULA_LENGTH | Type: ";
    switch ($FORMULA_TYPE)
    {
        case "LEMMA":
            echo "Lemmata";
            break;

        case "ROOT":
            echo "Root";
            break;

        case "ROOT-ALL":
            echo "Root (Plus Particles/Pronouns)";
            break;

        default:
            echo "All Formulae Types";
        break;
    }

    echo "</div>";
}

echo "</div>";     // page-header

echo "</div>";     // $mini_normal_mode_class

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; height:220px;'";
}
echo "></div>";

    if ($extra == "")
    {
        echo "<div class='provenance-legend'>";
        echo "  <span class='meccan'>Meccan Suras</span>&nbsp;&nbsp;<span class='medinan'>Medinan Suras</span>";
        echo "</div>";
    }

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
    
</body>
</html>