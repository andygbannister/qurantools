<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Chart of Formulae Used Per Sura");
        ?>

<?php

function words_in_sura($s)
{
    // db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE `SURA`=$s AND `SEGMENT`=1");
    $result = db_query("SELECT * FROM `QURAN-DATA` WHERE `SURA`=$s AND `SEGMENT`=1");
    return db_rowcount($result);
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

// normalise per 100 words
$per100words = "";
if (isset($_GET["PER100"]))
{
    if ($_GET["PER100"] == 1)
    {
        $per100words = 1;
    }
}
else
{
    $_GET["PER100"] = "";
}

// sort order
$sort_order = "ORDER BY `Sura Number` ASC";
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}
else
{
    if ($_GET["SORT"] == 1)
    {
        if ($per100words != 1)
        {
            $sort_order = "ORDER BY formula_count DESC";
        }
        else
        {
            $sort_order = "ORDER BY (formula_count / (`Words` / 100)) DESC";
        }
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

$extra             = "";
$PROV              = "";
$filter_provenance = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $PROV              = "MECCAN";
        $filter_provenance = "WHERE `Provenance`='Meccan'";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $PROV              = "MEDINAN";
        $filter_provenance = "WHERE `Provenance`='Medinan'";
    }
}
?>

	<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
	<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
	<!-- <link rel="stylesheet" type="text/css" href="../library/menubar.css"> -->

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
							<?php
                if (!$per100words)
                {
                    echo "\"yAxisName\": \"Number of Formulae Used\",";
                }
                else
                {
                    echo "\"yAxisName\": \"Number of Formulae Used per 100 Words\",";
                }
                ?>
							"theme": "fint",
							"showValues": "0"
					},
					
				<?php
            // POPULATE THE DATASET

            if ($FORMULA_TYPE == "ANY")
            {
                $result = db_query("SELECT `Sura Number`, `Provenance`, `Words`,
	(SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`Sura Number` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_count
	FROM `SURA-DATA` $filter_provenance
	$sort_order");
            }
            else
            {
                $result = db_query("SELECT `Sura Number`, `Provenance`, `Words`,
	(SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`Sura Number` AND `TYPE`='" . db_quote($FORMULA_TYPE) . "' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_count
	FROM `SURA-DATA` $filter_provenance
	$sort_order");
            }
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
                        if (!$per100words)
                        {
                            echo "\"value\": \"" . $ROW["formula_count"] . "\",";
                        }
                        else
                        {
                            echo "\"value\": \"" . ($ROW["formula_count"] / ($ROW["Words"] / 100)) . "\",";
                        }

                        if (!$miniMode)
                        {
                            echo "link:\"../formulae/list_formulae.php?SURA=" . $ROW["Sura Number"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE\",";
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
    $expander_link = "../charts/chart_formulae_used_per_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "<h2>Formulae Used per Sura</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // ==== formula length and type selection form =====

    echo "<form action='chart_formulae_used_per_sura.php?PROV=$PROV&PER100=$per100words&SORT=" . $_GET["SORT"] . "' method=POST>";

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
    echo ">All Formulae Types</option>";

    echo "</select>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=$per100words&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_formulae_used_per_sura.php?PROV=MECCAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=$per100words&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_formulae_used_per_sura.php?PROV=MEDINAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=$per100words&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    // Chart occurrences control =====

    echo "    <div class='chart-control occurrences'>";
    echo "      <span class='label'>Count</span>";
    echo "      <a href='chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$all_occurrences_selected'>";
    echo "        All Occurrences";
    echo "      </a>";
    echo "      <a href='chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=1&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$occurrences_per_100_words_selected'>";
    echo "        Occurrences per 100 Words";
    echo "      </a>";
    echo "    </div>";  // chart-control occurrences

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=$per100words&PROV=$PROV' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PER100=$per100words&PROV=$PROV&SORT=1' class='$first_sort_option_selected'>";
    echo "          Number of Formulae";
    echo "        </a>";
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

echo "</div>";       // $mini_normal_mode_class div

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; height:250px;'";
}
echo "></div>";

if ($filter_provenance == "")
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