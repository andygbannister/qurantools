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

$LEMMA = "min";
$SURA  = 1;
if (isset($_GET["LEMMA"]))
{
    $LEMMA = $_GET["LEMMA"];
}

// this is needed to make the link from the chart work properly if the lemma
// has a character such as ' or & in it
$LEMMA_URL_FRIENDLY = urlencode($LEMMA);

// look up Arabic version as we may need that for some searches
$LEMMA_ARABIC = db_return_one_record_one_field("SELECT `ARABIC` FROM `LEMMA-LIST` WHERE BINARY(`ENGLISH`)='" . db_quote($LEMMA) . "'");

$PER100 = "";
if (isset($_GET["PER100"]))
{
    if ($_GET["PER100"] == "1")
    {
        $PER100 = "1";
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
        if ($PER100 != "1")
        {
            $sort_order = "ORDER BY count_lemma DESC";
        }
        else
        {
            $sort_order = "ORDER BY (count_lemma / (`Words` / 100)) DESC";
        }
    }
}

// $PROV = "ANY";
$PROV        = "";
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

window_title("Chart of Lemma Occurrences (" . transliterate_new($LEMMA) . ")");

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
                if ($PER100 == "")
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

            $result = db_query("SELECT `Sura Number`, `Provenance`, `Words`, (SELECT COUNT(*) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND BINARY(`QTL-LEMMA`)='" . db_quote($LEMMA) . "') count_lemma FROM `SURA-DATA` $filter_prov $sort_order");

            // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                    $count = 0;
                    echo "\"data\": [";

                    for ($i = 0; $i < db_rowcount($result); $i++)
                    {
                        // grab next database row
                        $ROW = db_return_row($result);

                        $count++;
                        if ($i > 0)
                        {
                            echo ",";
                        }
                        echo "{";
                        echo "\"label\": \"" . $ROW["Sura Number"] . "\",";

                        if ($PER100 == "")
                        {
                            echo "\"value\": \"" . $ROW["count_lemma"] . "\",";
                        }
                        else
                        {
                            echo "\"value\": \"" . number_format($ROW["count_lemma"] / ($ROW["Words"] / 100), 2) . "\",";
                        }

                        if (!$miniMode)
                        {
                            echo "link:\"../verse_browser.php?S=(LEMMA:" . urlencode($LEMMA_URL_FRIENDLY) . " OR LEMMA:$LEMMA_ARABIC) RANGE:" . $ROW["Sura Number"] . "\",";
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
    echo "  <a href='/charts/chart_lemmata.php?LEMMA=" . urlencode($_GET["LEMMA"]) . "'>";
    echo "    <img src='images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>";
echo "    <a href='../counts/count_all_lemmata.php' class=linky>Lemma</a> Occurrences: " . return_arabic_word($LEMMA) . " (<span class='override-cabin-font-for-subtitles-with-transcription $user_preference_transliteration_style'>" . transliterate_new($LEMMA) . "</span>)";
echo "  </h2>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // Provenance control =====

    echo "    <div class='chart-control provenance'>";
    echo "	    <span class='label'>Show</span>";
    echo "  	  <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=$PER100&SORT=" . $_GET["SORT"] . "' class='" . $all_suras_selected . "'>";
    echo "	  	  All Suras";
    echo "	    </a>";

    echo "	    <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=$PER100&PROV=MECCAN&SORT=" . $_GET["SORT"] . "' class='" . $meccan_suras_selected . "'>";
    echo "		    Meccan";
    echo "	    </a>";

    echo "	    <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=$PER100&PROV=MEDINAN&SORT=" . $_GET["SORT"] . "' class='" . $medinan_suras_selected . "'>";
    echo "		    Medinan";
    echo "	    </a>";
    echo "    </div>"; // chart-control provenance

    // Count Type Form =====

    echo "    <div class='chart-control occurrences'>";
    echo "      <span class='label'>Count</span>";
    echo "      <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$all_occurrences_selected'>";
    echo "        All Occurrences";
    echo "      </a>";
    echo "      <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=1&PROV=$PROV&SORT=" . $_GET["SORT"] . "' class='$occurrences_per_100_words_selected'>";
    echo "        Occurrences per 100 Words";
    echo "      </a>";
    echo "    </div>";  // chart-control occurrences

    // Sort control  =====

    echo "    <div class='chart-control sort-by'>";
    echo "      <span class='label'>Sort By</span>";
    echo "        <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=$PER100&PROV=$PROV' class='$default_sort_selected'>";
    echo "          Sura Number";
    echo "        </a>";
    echo "        <a href='chart_lemmata.php?LEMMA=" . urlencode($LEMMA) . "&PER100=$PER100&SORT=1&PROV=$PROV' class='$first_sort_option_selected'>";
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
    echo " style='width:420px; height:220px;'";
}
echo "></div>";

if (!$miniMode)
{
    if ($PROV == "ANY" || $PROV == "")
    {
        include "./provenance_footer.php";
    }

    include "library/print_control.php";
    include "../library/footer.php";
}

?>  
 
</body>
</html>