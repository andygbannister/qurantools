<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// what sura?

$SURA = 1;

if (isset($_GET["SURA"]))
{
    $SURA = $_GET["SURA"];

    if ($SURA < 1)
    {
        $SURA = 1;
    }
    if ($SURA > 114)
    {
        $SURA = 114;
    }
}

$SURA = db_quote($SURA);

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Formulae in Common: Sura $SURA");
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
$sort_order = "ORDER BY common_sura ASC";
if (isset($_GET["SORT"]))
{
    if ($_GET["SORT"] == "1")
    {
        $sort_order = "ORDER BY common_count DESC";
    }
    else
    {
        $_GET["SORT"] = "";
    }
}
else
{
    $_GET["SORT"] = "";
}

// what formulae type

$FORMULA_LENGTH = 0;

if (isset($_GET["L"]))
{
    $FORMULA_LENGTH = $_GET["L"];
    if ($FORMULA_LENGTH < 3 && $FORMULA_LENGTH != 0)
    {
        $FORMULA_LENGTH = 3;
    }
    if ($FORMULA_LENGTH > 5)
    {
        $FORMULA_LENGTH = 5;
    }
}

$filter_length = "";
if ($FORMULA_LENGTH > 0)
{
    $filter_length = "AND T1.`LENGTH`=" . db_quote($FORMULA_LENGTH);
}

$FORMULA_TYPE = "ANY";

if (isset($_GET["TYPE"]))
{
    $FORMULA_TYPE = $_GET["TYPE"];
    if ($FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "ANY")
    {
        $FORMULA_TYPE = "ROOT";
    }
}

$filter_type = "";
if ($FORMULA_TYPE != "ANY")
{
    $filter_type = "AND T1.`TYPE`='" . db_quote($FORMULA_TYPE) . "'";
}

$extra             = "";
$PROV              = "";
$filter_provenance = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $PROV              = "MECCAN";
        $filter_provenance = "AND `Provenance`='Meccan'";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $PROV              = "MEDINAN";
        $filter_provenance = "AND `Provenance`='Medinan'";
    }
}

// incommon group by --> used when hopping between this chart and the list of formulae in common
$INCOMMON_GROUPBY = "";
if (isset($_GET["INCOMMON_GROUPBY"]))
{
    $INCOMMON_GROUPBY = $_GET["INCOMMON_GROUPBY"];
}

$FSORT = "";
if (isset($_GET["FSORT"]))
{
    $FSORT = $_GET["FSORT"];
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
							"yAxisName": "Number of Formulae in Common with Sura <?php echo $SURA; ?>",
							"theme": "fint",
							"showValues": "0"					},
					
				<?php
            // POPULATE THE DATASET

            $last_number = 0; // used to track and fill gaps in

            $result = db_query("SELECT DISTINCT(T2.`START SURA`) common_sura, COUNT(DISTINCT CONCAT(T2.`FORMULA`,'-',T2.`TYPE`)) common_count, `Provenance` FROM `FORMULA-LIST` T1 
LEFT JOIN `FORMULA-LIST` T2 ON T1.`FORMULA`=T2.`FORMULA` AND T1.`TYPE`=T2.`TYPE` AND T2.`START SURA`!=$SURA
LEFT JOIN `SURA-DATA` ON `Sura Number`=T2.`START SURA`
WHERE T1.`START SURA`=$SURA $filter_provenance $filter_length $filter_type
GROUP BY T2.`START SURA` $sort_order");
                            echo "\"data\": [";

                    if (db_rowcount($result) == 0)
                    {
                        ?>
						
						{}
						
						<?php
                    }

                    for ($i = 0; $i < db_rowcount($result); $i++)
                    {
                        // grab next database row
                        $ROW = db_return_row($result);

                        if ($i > 0)
                        {
                            echo ",";
                        }

                        // gap filling

                        if ($_GET["SORT"] == "")
                        {
                            $last_number++;

                            if ($ROW["common_sura"] > $last_number)
                            {
                                for ($j = $last_number; $j < $ROW["common_sura"]; $j++)
                                {
                                    if ($j != $SURA)
                                    {
                                        if ($PROV != "" && strtoupper(sura_provenance($j)) != $PROV)
                                        {
                                            continue;
                                        }
                                        echo "{";
                                        echo "\"label\": \"" . $j . "\",";
                                        echo "\"value\": \"" . "0" . "\",";
                                        echo "}, ";
                                    }
                                }
                            }

                            $last_number = $ROW["common_sura"];
                        }

                        echo "{";
                        echo "\"label\": \"" . $ROW["common_sura"] . "\",";

                        // data point
                        echo "\"value\": \"" . $ROW["common_count"] . "\",";

                        if (!$miniMode)
                        {
                            echo "link:\"../formulae/sura_formulae_analyse.php?INCOMMON=$SURA&INCOMMON_GROUPBY=$INCOMMON_GROUPBY&FSORT=$FSORT&SORT=" . $_GET["SORT"] . "&PROV=$PROV&SURA=" . $ROW["common_sura"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE\",";
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

                    // closing gap fill

                    if ($_GET["SORT"] == "")
                    {
                        if ($last_number < 114)
                        {
                            for ($j = $last_number; $j <= 114; $j++)
                            {
                                if ($j != $SURA)
                                {
                                    if ($PROV != "" && strtoupper(sura_provenance($j)) != $PROV)
                                    {
                                        continue;
                                    }
                                    echo ",{";
                                    echo "\"label\": \"" . $j . "\",";
                                    echo "\"value\": \"" . "0" . "\",";
                                    echo "}";
                                }
                            }
                        }
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

echo "<h2>Formulae in Common With Sura $SURA</h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // ==== formula length and type selection form =====

    echo "<form action='chart_formulae_in_common_by_sura.php' method=GET>";

    echo "<INPUT NAME=INCOMMON_GROUPBY VALUE='$INCOMMON_GROUPBY' TYPE=HIDDEN>";
    echo "<INPUT NAME=PROV VALUE='$PROV' TYPE=HIDDEN>";
    echo "<INPUT NAME=SORT VALUE='" . $_GET["SORT"] . "' TYPE=HIDDEN>";
    echo "<INPUT NAME=FSORT VALUE='$FSORT' TYPE=HIDDEN>";

    echo "<div class='formulaic-pick-table'><table cellpadding=2>";

    echo "<tr>";

    echo "<td align=right>Sura</td><td>";
    echo "<select name=SURA onChange='this.form.submit();'>";

    for ($i = 1; $i <= 114; $i++)
    {
        echo "<option value='$i'";

        if ($SURA == $i)
        {
            echo " selected";
        }

        echo ">$i</option>";
    }

    echo "</select>";

    echo "</td></tr>";

    echo "<tr>";

    echo "<td align=right>Formula Length</td><td>";
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

    echo " &nbsp;&nbsp;<input type=radio name=L value=0 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 0)
    {
        echo "checked=checked";
    }
    echo "> Any</input>";

    echo "</td></tr>";

    echo "<tr>";

    echo "<td align=right>Formula Type</td><td>";
    echo "<select name=TYPE onChange='this.form.submit();'>";

    echo "<option value='ANY'";
    if ($FORMULA_TYPE == "ANY")
    {
        echo " selected";
    }
    echo ">All Formulae Types</option>";

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

    echo "</select>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    echo "    <div class='flex-breaker'></div>"; // Hack to make the next set of controls flow to the next line.

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_formulae_in_common_by_sura.php?SURA=$SURA&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_formulae_in_common_by_sura.php?SURA=$SURA&PROV=MECCAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "&INCOMMON_GROUPBY=$INCOMMON_GROUPBY' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_formulae_in_common_by_sura.php?SURA=$SURA&PROV=MEDINAN&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&SORT=" . $_GET["SORT"] . "&INCOMMON_GROUPBY=$INCOMMON_GROUPBY' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_formulae_in_common_by_sura.php?SURA=$SURA&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=$PROV&INCOMMON_GROUPBY=$INCOMMON_GROUPBY' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_formulae_in_common_by_sura.php?SURA=$SURA&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=$PROV&SORT=1&INCOMMON_GROUPBY=$INCOMMON_GROUPBY' class='$first_sort_option_selected'>";
    echo "          Number of Formulae in Common";
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