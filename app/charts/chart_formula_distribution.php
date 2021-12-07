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

// Is "DURIE" mode on? (If so, we'll show the provenance coloured by Mark Durie's sura categories)
$durieMode = false;
if (isset($_GET["DURIE"]))
{
    $durieMode = ($_GET["DURIE"] == "Y");
}

// are we in MINI mode
$MINI_MODE = false;

if (isset($_GET["MINI"]))
{
    if ($_GET["MINI"] == "Y")
    {
        $MINI_MODE = true;
    }
}

// get formula, length, type

$FORMULA        = "";
$FORMULA_LENGTH = "3";
$FORMULA_TYPE   = "ROOT";
$ROOT           = "";

if (isset($_GET["F"]))
{
    $FORMULA = $_GET["F"];
}
if (isset($_GET["L"]))
{
    $FORMULA_LENGTH = $_GET["L"];
}
if (isset($_GET["T"]))
{
    $FORMULA_TYPE = $_GET["T"];
}
if (isset($_GET["ROOT"]))
{
    $ROOT = db_quote($_GET["ROOT"]);
}

// do we need to look up a formula by ID?

if (isset($_GET["FID"]))
{
    $FORMULA        = db_return_one_record_one_field("SELECT `FORMULA LOWER` FROM `FORMULA-LIST` WHERE `FORMID`=" . db_quote($_GET["FID"]));
    $FORMULA_LENGTH = db_return_one_record_one_field("SELECT `LENGTH` FROM `FORMULA-LIST` WHERE `FORMID`=" . db_quote($_GET["FID"]));
    $FORMULA_TYPE   = db_return_one_record_one_field("SELECT `TYPE` FROM `FORMULA-LIST` WHERE `FORMID`=" . db_quote($_GET["FID"]));
}

$PROV = "ANY";
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

if ($FORMULA_TYPE == "EVERYTHING")
{
    window_title("Chart of Distribution of Formulae Involving the Root " . htmlentities($ROOT));;
}
else
{
    window_title("Formulae Distribution Chart");
}

// we don't need to load js and css in mini mode as the 'parent' window will have it

if (!$MINI_MODE)
{
    ?>
	<!-- <link rel="stylesheet" type="text/css" href="../library/menubar.css"> -->
	<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
	<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
	<?php
}

include "../library/transliterate.php";

?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "chartContainer",
        
        <?php

         // if this is a "mini mode" chart (running in a pop up window, we scale it differently)

         if ($MINI_MODE)
         {
             echo "
		    \"width\": \"420\",
		    \"height\": \"200\",
		    ";
         }
         else
         {
             echo "
		    \"width\": \"960\",
		    \"height\": \"450\",
		    ";
         }

        ?>
        
        
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "subCaption": "",
            "xAxisName": "Sura",
            "yAxisName": "Occurrences of Formula",
            "theme": "fint",
            "showValues": "0"
            
         },
         
       <?php

       // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

               $test       = "";
               $first_item = true;
               echo "\"data\": [";
               for ($i = 1; $i <= 114; $i++)
               {
                   // if we are filtering by provenance, we may need to skip
                   if ($PROV == "MECCAN")
                   {
                       if (sura_provenance($i) != "Meccan")
                       {
                           continue;
                       }
                   }
                   if ($PROV == "MEDINAN")
                   {
                       if (sura_provenance($i) != "Medinan")
                       {
                           continue;
                       }
                   }

                   // count occurrences of formula
                   if ($FORMULA_TYPE == "EVERYTHING")
                   {
                       $count = db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=$i AND (`Element1`='$ROOT' || `Element2`='$ROOT' || `Element3`='$ROOT' || `Element4`='$ROOT' || `Element5`='$ROOT') AND `OCCURRENCES`>1");
                   }
                   else
                   {
                       $count = db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=$i AND `FORMULA LOWER`='" . db_quote($FORMULA) . "' AND `LENGTH`='" . db_quote($FORMULA_LENGTH) . "' AND `TYPE`='" . db_quote($FORMULA_TYPE) . "'");
                   }

                   // comma only goes after the first item
                   if (!$first_item)
                   {
                       echo ",";
                   }
                   $first_item = false;

                   echo "{";
                   echo "\"label\": \"" . $i . "\",";

                   echo "\"value\": \"" . $count . "\",";

                   if ($FORMULA_TYPE != "EVERYTHING")
                   {
                       echo "link:\"../verse_browser.php?S=FORMULA:" . str_ireplace("%2B", "FORMPLUS", urlencode($FORMULA)) . " RANGE:$i&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE\",";
                   }
                   else
                   {
                       echo "link:\"../formulae/list_formulae.php?TYPE=EVERYTHING&SURA=$i&ROOT=" . urlencode($ROOT) . "\",";
                   }

                   if ($durieMode)
                   {
                       $durie_classification = sura_durie_classification($i);

                       switch ($durie_classification)
                    {
                        case "PRE-TRANSITIONAL":
                            echo  "\"color\": \"#008e00\"";
                            break;

                        case "POST-TRANSITIONAL":
                            echo  "\"color\": \"#ff0000\"";
                            break;

                        case "MIXED":
                            echo  "\"color\": \"#0000ff\"";
                            break;
                    }
                   }
                   else
                   {
                       if (sura_provenance($i) == "Meccan")
                       {
                           echo  "\"color\": \"#6060ff\"";
                       }
                       else
                       {
                           echo  "\"color\": \"#ff9090\"";
                       }
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

// if we are running in full mode, we need the header; in mini mode (pop up window), we don't

if (!$MINI_MODE)
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

echo "  <div class='page-header'>";

// if running in a pop up window, we display a "resize" icon (linked to the main chart)

if ($MINI_MODE)
{
    echo "    <span class='expander'>";
    echo "      <a href='../charts/chart_formula_distribution.php?F=" . urlencode($FORMULA) . "&L=$FORMULA_LENGTH&T=$FORMULA_TYPE&ROOT=" . urlencode($ROOT) . "'>";
    echo "        <img src='/images/expand.png' width=12 height=12>";
    echo "      </a>";
    echo "    </span>";
}

echo "  <h2>";

if ($FORMULA_TYPE == "EVERYTHING")
{
    echo "Distribution of All Formulae Involving the <a href='../formulae/list_formulae.php?TYPE=EVERYTHING&ROOT=" . urlencode($ROOT) . "' class=linky>Root <i>" . htmlentities($ROOT) . "</i></a>";
}
else
{
    echo "Formula Distribution Chart";
}

echo "  </h2>";

if ($FORMULA_TYPE != "EVERYTHING")
{
    echo "  <h3>";

    if (!$MINI_MODE)
    {
        echo "    <a href='../formulae/list_formulae.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=override-cabin-font-for-subtitles-with-transcription>Formula: ";
    }

    // transliterate the formula
    echo db_return_one_record_one_field("SELECT `FORMULA TRANSLITERATED` FROM `FORMULA-LIST` WHERE `FORMULA LOWER`='" . db_quote($FORMULA) . "' AND `LENGTH`='" . db_quote($FORMULA_LENGTH) . "' AND `TYPE`='" . db_quote($FORMULA_TYPE) . "'");

    echo "; Length: $FORMULA_LENGTH; Type: $FORMULA_TYPE</a></h3>";
}

// if we are running in mini mode, we don't need the filtering buttons

if (!$MINI_MODE)
{
    echo "    <div class='chart-controls'>";

    // Provenance control =====

    echo "      <div class='chart-control provenance'>";
    echo "	      <span class='label'>Show</span>";
    echo "	      <a href='chart_formula_distribution.php?F=" . urlencode($FORMULA) . "&L=$FORMULA_LENGTH&T=$FORMULA_TYPE&PROV=ANY&ROOT=$ROOT' class='" . $all_suras_selected . "'>";
    echo "	        All Suras";
    echo "	      </a>";

    echo "        <a href='chart_formula_distribution.php?F=" . urlencode($FORMULA) . "&L=$FORMULA_LENGTH&T=$FORMULA_TYPE&PROV=MECCAN&ROOT=$ROOT' class='" . $meccan_suras_selected . "'>";
    echo "     	    Meccan";
    echo "        </a>";

    echo "   	    <a href='chart_formula_distribution.php?F=" . urlencode($FORMULA) . "&L=$FORMULA_LENGTH&T=$FORMULA_TYPE&PROV=MEDINAN&ROOT=$ROOT' class='" . $medinan_suras_selected . "'>";
    echo "          Medinan";
    echo "        </a>";
    echo "      </div>"; // chart-control provenance

    echo "    </div>";   // chart-controls
    echo "  </div>";     // page-header
    echo "</div>";       // mini or normal mode
}

// chart container
echo "<div align=center id='chartContainer'";
if ($MINI_MODE)
{
    echo " style='width:420px; height:200px;'";
}
echo "></div>";

// footer (if required)

if (!$MINI_MODE)
{
    if ($PROV == "ANY")
    {
        include "./provenance_footer.php";
    }

    include "library/print_control.php";
    include "../library/footer.php";
}

?>   
   
</body>
</html>