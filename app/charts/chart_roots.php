<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
        ?>

<?php

include "../library/arabic.php";
include "../library/transliterate.php";

// are we running in "mini mode" (e.g. in a loupe view)
$miniMode = false;
if (isset($_GET["VIEW"]))
{
    if ($_GET["VIEW"] = "MINI")
    {
        $miniMode = true;
    }
}

// are we only looking for proper nouns?
$properNounSQL = "";
$properNounTag = "";
if (isset($_GET["PROPER"]))
{
    if ($_GET["PROPER"] == "YES")
    {
        $properNounSQL = "AND `QTL-TAG-EXPLAINED` = 'proper noun'";
        $properNounTag = "@[propernoun]";
    }
}

$ROOT = "'lh";
$SURA = 1;
if (isset($_GET["ROOT"]))
{
    $ROOT = $_GET["ROOT"];
}

// look up the arabic and neat transliteration of the root

$ARABIC         = db_return_one_record_one_field("SELECT `ARABIC` FROM `ROOT-LIST` WHERE `ENGLISH TRANSLITERATED`='" . db_quote($ROOT) . "' OR `ARABIC`='" . db_quote($ROOT) . "'");
$TRANSLITERATED = db_return_one_record_one_field("SELECT `ENGLISH TRANSLITERATED` FROM `ROOT-LIST` WHERE `ENGLISH TRANSLITERATED`='" . db_quote($ROOT) . "' OR `ARABIC`='" . db_quote($ROOT) . "'");

$COUNT = "OCC";
if (isset($_GET["COUNT"]))
{
    if ($_GET["COUNT"] == "PER100")
    {
        $COUNT = "PER100";
    }
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
        if ($COUNT != "PER100")
        {
            $sort_order = "ORDER BY count_root DESC";
        }
        else
        {
            $sort_order = "ORDER BY (count_root / (`Words` / 100)) DESC";
        }
    }
}

$PROV        = "ANY";
$filter_prov = "";
if (isset($_GET["PROV"]))
{
    if ($_GET["PROV"] == "MECCAN")
    {
        $PROV        = "MECCAN";
        $filter_prov = "WHERE `Provenance`='Meccan'";
    }
    if ($_GET["PROV"] == "MEDINAN")
    {
        $PROV        = "MEDINAN";
        $filter_prov = "WHERE `Provenance`='Medinan'";
    }
}

window_title("Chart of Root Occurrences: $ARABIC (" . htmlentities(transliterate_new($ROOT)) . ")");

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
                if ($COUNT == "OCC")
                {
                    echo "\"yAxisName\": \"Occurrences\",";
                }
                else
                {
                    echo "\"yAxisName\": \"Occurrences per 100 Words\",";
                }
                ?>
							"theme": "fint",
							"showValues": "0"
					},
					
				<?php
            // POPULATE THE DATASET

            $result = db_query("SELECT `Sura Number`, `Provenance`, `Words`, (SELECT COUNT(*) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND BINARY(`QTL-ROOT-TRANSLITERATED`)='" . db_quote($ROOT) . "' $properNounSQL) count_root FROM `SURA-DATA` $filter_prov $sort_order");

            // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                    $count = 0;
                    echo "\"data\": [";

                    for ($i = 0; $i < db_rowcount($result); $i++)
                    {
                        // for ($i = 1; $i <= 114; $i++)
                        // grab next database row
                        $ROW = db_return_row($result);

                        if ($i > 0)
                        {
                            echo ",";
                        }
                        echo "{";
                        echo "\"label\": \"" . $ROW["Sura Number"] . "\",";

                        if ($COUNT == "OCC")
                        {
                            echo "\"value\": \"" . $ROW["count_root"] . "\",";
                        }
                        else
                        {
                            echo "\"value\": \"" . $ROW["count_root"] / ($ROW["Words"] / 100) . "\",";
                        }

                        if (!$miniMode)
                        {
                            echo "link:\"../verse_browser.php?S=ROOT:" . urlencode($ROOT) . $properNounTag . "%20RANGE:" . $ROW["Sura Number"] . "\",";
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
    echo "  <a href='/charts/chart_roots.php?ROOT=" . urlencode($_GET["ROOT"]) . "'>";
    echo "    <img src='images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>";
echo "    <a href='../counts/count_all_roots.php' class=linky>Root Occurrences</a>: $ARABIC (<span class='override-cabin-font-for-subtitles-with-transcription $user_preference_transliteration_style'>" . htmlentities(transliterate_new($TRANSLITERATED)) . "</span>)";

if ($properNounSQL != "")
{
    echo " (Occurrences as a Proper Noun)";
}

echo "  </h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=$COUNT&PROV=ANY&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=$COUNT&PROV=MECCAN&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=$COUNT&PROV=MEDINAN&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    // Chart occurrences control =====

    echo "    <div class='chart-control occurrences'>";
    echo "      <span class='label'>Show</span>";
    echo "      <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=OCC&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$all_occurrences_selected'>";
    echo "        All Occurrences";
    echo "      </a>";
    echo "      <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=PER100&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$occurrences_per_100_words_selected'>";
    echo "        Occurrences per 100 Words";
    echo "      </a>";
    echo "    </div>";  // chart-control occurrences

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=$COUNT&PROV=$PROV' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_roots.php?ROOT=" . urlencode($ROOT) . "&COUNT=$COUNT&SORT=1&PROV=$PROV' class='$first_sort_option_selected'>";
    echo "          Occurrences";
    echo "        </a>";
    echo "    </div>"; // chart-control sort-by

    echo "  </div>";   // chart-controls
}

echo "</div>";     // page-header

echo "</div>"; // mini_normal_mode_class

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    if ($properNounSQL != "")
    {
        echo " style='width:500px; height:220px;'";
    }
    else
    {
        echo " style='width:420px; height:220px;'";
    }
}
echo "></div>";

if (!$miniMode)
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