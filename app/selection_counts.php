<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/colours.php';
require_once 'library/search_engine.php';

// avoids a minor script error if we try to use it and it isn't set
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

if (!isset($_GET["V"]))
{
    $_GET["V"] = "";
}

// if we show both provenances, we'll insert the legend below the chart
$shownMeccan  = false;
$shownMedinan = false;

// are we just showing a single letter's column chart?
$SINGLE_LETTER = 0;
if (isset($_GET["L"]))
{
    $SINGLE_LETTER = $_GET["L"];
    if ($SINGLE_LETTER > 28)
    {
        $SINGLE_LETTER = 0;
    }
}

// what are we counting
$COUNT = "WORDS";

if (isset($_GET["COUNT"]))
{
    if ($_GET["COUNT"] == "LETTERS")
    {
        $COUNT = "LETTERS";
    }
}

// how are we showing it?
$DISPLAY = "TABLE";

if (isset($_GET["DISPLAY"]))
{
    if ($_GET["DISPLAY"] == "CHART")
    {
        $DISPLAY = "CHART";
    }
}

// sort order
$sort       = "VERSES-ASC";
$SORT_ORDER = "t1.`SURA`, t1.`VERSE`";

if (isset($_GET["SORT"]))
{
    $sort = $_GET["SORT"];
}

if ($sort == "VERSES-DESC")
{
    $SORT_ORDER = "t1.`SURA` DESC, t1.`VERSE` DESC";
}

if ($sort == "COUNT-ASC")
{
    $SORT_ORDER = "b ASC";
}

if ($sort == "COUNT-DESC")
{
    $SORT_ORDER = "b DESC";
}

// used by the letters table and chart
$headLetters = "ابتثجحخدذرزسشصضطظعغفقكلمنهوي";

$KeyNames = ["alif", "bāʾ", "tāʾ", "thāʾ", "jīm", "ḥāʾ", "khāʾ", "dāl", "dhāl", "rāʾ", "zāy", "sīn", "shīn", "ṣād", "ḍād", "ṭāʾ", "ẓāʾ", "ʿayn", "ghayn", "fāʾ", "qāf", "kāf", "lām", "mīm", "nūn", "hāʾ", "wāw", "yāʾ"];

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
        ?>

		<script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

		<script>

    var CountWhat = 'WORDS';
    
    <?php

    if ($SINGLE_LETTER == -1)
    {
        echo "CountWhat = 'LETTERS';";
    }

    ?>
    
  	var DisplayMode = 'TABLE';
  	
  	function show_chart_hide_table()
  	{
		document.getElementById('displayChartButton').style.fontWeight = 'bold';
  		document.getElementById('displayTableButton').style.fontWeight = 'normal';
  		$('#analysisTable').hide();
  		$('#mainChartContainer').show();
  	}
  	
  	function show_table_hide_chart()
  	{
		document.getElementById('displayChartButton').style.fontWeight = 'normal';
  		document.getElementById('displayTableButton').style.fontWeight = 'bold';
  		$('#analysisTable').show();
  		$('#mainChartContainer').hide();
  	}
    
    </script>
     
  <?php

  if ($_GET["V"] != "SEARCH" && $_GET["V"] != "")
  {
      $windowTitle = "Counting: Q. " . $_GET["V"];
  }
  else
  {
      $windowTitle = "Word and Letter Count (of Search Results)";
  }

  window_title($windowTitle);

include "library/transliterate.php";
include "library/arabic.php";
include "library/verse_parse.php";

// build the SQL we'll use to constrain the master analysis query

$RANGE_SQL = "";

$V = $_GET["V"];

$savedSearchedTimestamp = "";

// remove whitespace
$V = preg_replace('/\s+/', '', $V);

if ($_GET["S"] != "")
{
    // begin by performing the search

    $search_result = search($_GET["S"], true);

    // modify the master search to become something useful to us
    $master_search_sql = substr($master_search_sql, 48, strlen($master_search_sql));
    $master_search_sql = substr($master_search_sql, 0, stripos($master_search_sql, "ORDER BY") - 1);

    // replace the table identifier for these queries
    $master_search_sql = str_ireplace("qtable.", "t2.", $master_search_sql);

    if ($SINGLE_LETTER < 1)
    {
        // count the words

        $SQL = "SELECT DISTINCT(t1.`SURA-VERSE`), MAX(`WORD`) b, `Provenance`
FROM `QURAN-DATA` t1
LEFT JOIN `QURAN-FULL-PARSE` t2 on t1.`SURA-VERSE`=t2.`SURA-VERSE` 
WHERE ($master_search_sql) GROUP BY t1.`SURA-VERSE` ORDER BY $SORT_ORDER";

        $result = db_query($SQL);
    }
    // count the letters

    $SQL = "SELECT DISTINCT(t1.`SURA-VERSE`), GROUP_CONCAT(`FORM` ORDER BY `RECORD NUMBER` SEPARATOR ' ') b, `Provenance` 
		FROM `QURAN-DATA` t1
		LEFT JOIN `QURAN-FULL-PARSE` t2 on t1.`SURA-VERSE`=t2.`SURA-VERSE` 
WHERE ($master_search_sql)
GROUP BY t1.`SURA-VERSE` ORDER BY t1.`SURA`, t1.`VERSE`";

    $resultLetters = db_query($SQL);
}
else
{
    parse_verses($V, true, 0);

    // if we are only doing a single letter, skip this search
    if ($SINGLE_LETTER < 1)
    {
        $result = db_query("SELECT DISTINCT(`SURA-VERSE`), MAX(`WORD`) b, `Provenance` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE $RANGE_SQL GROUP BY `SURA-VERSE` ORDER BY `SURA`, `VERSE`");
    }

    $resultLetters = db_query("SELECT DISTINCT(`SURA-VERSE`), GROUP_CONCAT(`FORM` SEPARATOR ' ') b, `Provenance` FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE $RANGE_SQL GROUP BY `SURA-VERSE` ORDER BY `SURA`, `VERSE`");
}

// reset the grand total counts

$GrandWordCount   = 0;
$GrandLetterCount = 0;

// build the array for the letters

$lettersArray = [];
$totalsArray  = ["ALIF" => 0, "BA" => 0, "TA" => 0, "THA" => 0, "JIM" => 0, "HA" => 0, "KHA" => 0, "DAL" => 0, "THAL" => 0, "RA" => 0, "ZAIN" => 0, "SEEN" => 0, "SHEEN" => 0, "SAD" => 0, "DAD" => 0, "TTA" => 0, "ZZA" => 0, "AYN" => 0, "GHAYN" => 0, "FA" => 0, "QAF" => 0, "KAF" => 0, "LAM" => 0, "MEEM" => 0, "NOON" => 0, "HAA" => 0, "WAW" => 0, "YA" => 0];

for ($i = 0; $i < db_rowcount($resultLetters); $i++)
{
    $ROW = db_return_row($resultLetters);

    // different forms of alif, including AlifMaksura
    $count_aleph = mb_substr_count($ROW["b"], "{") + mb_substr_count($ROW["b"], "A") + mb_substr_count($ROW["b"], "<") + mb_substr_count($ROW["b"], ">") + mb_substr_count($ROW["b"], "Y");

    $totalsArray["ALIF"] += $count_aleph;

    $count_ba = mb_substr_count($ROW["b"], "b");
    $totalsArray["BA"] += $count_ba;

    $count_ta = mb_substr_count($ROW["b"], "p") + mb_substr_count($ROW["b"], "t");
    $totalsArray["TA"] += $count_ta;

    $count_tha = mb_substr_count($ROW["b"], "v");
    $totalsArray["THA"] += $count_tha;

    $count_jim = mb_substr_count($ROW["b"], "j");
    $totalsArray["JIM"] += $count_jim;

    $count_ha = mb_substr_count($ROW["b"], "H");
    $totalsArray["HA"] += $count_ha;

    $count_kha = mb_substr_count($ROW["b"], "x");
    $totalsArray["KHA"] += $count_kha;

    $count_dal = mb_substr_count($ROW["b"], "d");
    $totalsArray["DAL"] += $count_dal;

    $count_thal = mb_substr_count($ROW["b"], "*");
    $totalsArray["THAL"] += $count_thal;

    $count_ra = mb_substr_count($ROW["b"], "r");
    $totalsArray["RA"] += $count_ra;

    $count_zain = mb_substr_count($ROW["b"], "z");
    $totalsArray["ZAIN"] += $count_zain;

    $count_seen = mb_substr_count($ROW["b"], "s");
    $totalsArray["SEEN"] += $count_seen;

    $count_sheen = mb_substr_count($ROW["b"], "$");
    $totalsArray["SHEEN"] += $count_sheen;

    $count_sad = mb_substr_count($ROW["b"], "S");
    $totalsArray["SAD"] += $count_sad;

    $count_dad = mb_substr_count($ROW["b"], "D");
    $totalsArray["DAD"] += $count_dad;

    $count_tta = mb_substr_count($ROW["b"], "T");
    $totalsArray["TTA"] += $count_tta;

    $count_zza = mb_substr_count($ROW["b"], "Z");
    $totalsArray["ZZA"] += $count_zza;

    $count_ayn = mb_substr_count($ROW["b"], "E");
    $totalsArray["AYN"] += $count_ayn;

    $count_ghayn = mb_substr_count($ROW["b"], "g");
    $totalsArray["GHAYN"] += $count_ghayn;

    $count_fa = mb_substr_count($ROW["b"], "f");
    $totalsArray["FA"] += $count_fa;

    $count_qaf = mb_substr_count($ROW["b"], "q");
    $totalsArray["QAF"] += $count_qaf;

    $count_kaf = mb_substr_count($ROW["b"], "k");
    $totalsArray["KAF"] += $count_kaf;

    $count_lam = mb_substr_count($ROW["b"], "l");
    $totalsArray["LAM"] += $count_lam;

    $count_meem = mb_substr_count($ROW["b"], "m");
    $totalsArray["MEEM"] += $count_meem;

    $count_noon = mb_substr_count($ROW["b"], "n");
    $totalsArray["NOON"] += $count_noon;

    $count_haa = mb_substr_count($ROW["b"], "h");
    $totalsArray["HAA"] += $count_haa;

    $count_waw = mb_substr_count($ROW["b"], "w");
    $totalsArray["WAW"] += $count_waw;

    $count_ya = mb_substr_count($ROW["b"], "y");
    $totalsArray["YA"] += $count_ya;

    $lettersArray[] = ["VERSE" => $ROW["SURA-VERSE"], "LETTERS" => $ROW["b"],
        "ALIF"                 => $count_aleph,
        "BA"                   => $count_ba,
        "TA"                   => $count_ta,
        "THA"                  => $count_tha,
        "JIM"                  => $count_jim,
        "HA"                   => $count_ha,
        "KHA"                  => $count_kha,
        "DAL"                  => $count_dal,
        "THAL"                 => $count_thal,
        "RA"                   => $count_ra,
        "ZAIN"                 => $count_zain,
        "SEEN"                 => $count_seen,
        "SHEEN"                => $count_sheen,
        "SAD"                  => $count_sad,
        "DAD"                  => $count_dad,
        "TTA"                  => $count_tta,
        "ZZA"                  => $count_zza,
        "AYN"                  => $count_ayn,
        "GHAYN"                => $count_ghayn,
        "FA"                   => $count_fa,
        "QAF"                  => $count_qaf,
        "KAF"                  => $count_kaf,
        "LAM"                  => $count_lam,
        "MEEM"                 => $count_meem,
        "NOON"                 => $count_noon,
        "HAA"                  => $count_haa,
        "WAW"                  => $count_waw,
        "YA"                   => $count_ya
    ];
}

// build charts JavasScript

if ($SINGLE_LETTER < 1 && $COUNT == "WORDS")
{
    ?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "column2d",
        "renderAt": "mainChartContainer",
        "width": "960",
        "height": "420",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            "xAxisName": "Verse",
            "yAxisName": "Words",
            "theme": "fint",
            "showValues": "0"
         },
         
       <?php

       $ITEMS = db_rowcount($result);

    // POPULATE THE DATASET

    // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

    echo "\"data\": [";
    for ($i = 0; $i < $ITEMS; $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        if ($i > 0)
        {
            echo ",";
        }
        echo "{";
        echo "\"label\": \"" . $ROW["SURA-VERSE"] . "\",";

        echo "\"value\": \"" . $ROW["b"] . "\",";

        echo "link:\"verse_browser.php?V=" . $ROW["SURA-VERSE"] . "\",";

        if ($ROW["Provenance"] == "Meccan")
        {
            echo  "\"color\": \"#6060ff\"";
            $shownMeccan = true;
        }
        else
        {
            echo  "\"color\": \"#ff9090\"";
            $shownMedinan = true;
        }

        echo "}";
    } ?>
          ]
      }

  });
revenueChart.render();
})
</script>

<?php
}

if ($SINGLE_LETTER < 1 && $COUNT == "LETTERS")
{
    ?>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "pie2d",
        "renderAt": "mainChartContainer",
        "width": "960",
        "height": "420",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
            "labelFontSize": "11",
            "subCaption": "",
            "xAxisName": "Letter",
            "yAxisName": "Count",
            "theme": "fint",
            "showValues": "1"
         },
         
       <?php

       $ITEMS = db_rowcount($result);

    // if we show both provenances, we'll insert the legend below the chart
    $count = 0;

    // POPULATE THE DATASET

    // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

    echo "\"data\": [";
    foreach ($totalsArray as $value)
    {
        if ($i > 0)
        {
            echo ",";
        }
        $count++;

        echo "{";

        // echo "\"label\": \"".mb_substr($headLetters, $count - 1, 1)."\",";

        echo "\"label\": \"" . $KeyNames[$count - 1] . "\",";

        echo "\"value\": \"" . $value . "\",";

        echo  "\"color\": \"" . $colourArray[$count] . "\"";

        echo "}";
    } ?>
          ]
      }

  });
revenueChart.render();
})

</script>

<?php
}

if ($SINGLE_LETTER > 0)
{
    ?>
	
	<script type="text/javascript">
	  FusionCharts.ready(function(){
	    var revenueChart = new FusionCharts({
	        "type": "column2d",
	        
	        <?php

            echo "\"renderAt\": \"chartContainerSingleLetter\","; ?>
	        
	        "width": "960",
	        "height": "420",
	        "dataFormat": "json",
	        "dataSource":  {
	          "chart": {
	            "caption": "",
	              "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
	            "subCaption": "",
	            "xAxisName": "Verse",
	            
	            <?php

                echo "\"yAxisName\": \"Occurrences of " . mb_substr($headLetters, $SINGLE_LETTER - 1, 1) . " (" . ucfirst(strtolower($KeyNames[$SINGLE_LETTER - 1])) . ")\","

                ?>
	            
	            
	            "theme": "fint",
	            "showValues": "0"
	         },
	         
	       <?php

           $ITEMS = db_rowcount($result);

    // if we show both provenances, we'll insert the legend below the chart
    $count = 0;

    // POPULATE THE DATASET

    // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

    echo "\"data\": [";
    foreach ($lettersArray as $ROW)
    {
        if ($count > 0)
        {
            echo ",";
        }
        $count++;

        echo "{";

        echo "\"label\": \"" . $ROW["VERSE"] . "\",";

        echo "\"value\": \"" . $ROW[$KeyNames[$SINGLE_LETTER - 1]] . "\",";

        // colour code by sura number

        $reference = explode(":", $ROW["VERSE"]);

        echo  "\"color\": \"" . $colourArray[$reference[0] % $colour_Choices] . "\"";

        echo "}";
    } ?>
	          ]
	      }
	
	  });
	revenueChart.render();
	})

	</script>
	
<?php
}

// now we've run the SQL query and built our Javascript, close off the header

if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

echo "</head><body class='qt-site'><main class='qt-site-content'>";

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>Word and Letter Counting</h2>";
if ($_GET["S"] != "" && $V != "1")
{
    $totalHits   = count($globalWordsToHighlight);
    $totalVerses = db_rowcount($search_result);

    echo "<a href='verse_browser.php?S=" . urlencode($_GET["S"]) . "' style='text-decoration: none'><span class='pill-button'>Search Terms: <b>" . $_GET["S"] . "</b> ";

    if ($totalHits > 0 && $totalVerses > 0)
    {
        echo " (with " . number_format($totalHits) . " hit" . plural($totalHits) . " in " . number_format($totalVerses) . " verse" . plural($totalVerses) . ")";
    }

    if ($totalHits == 0)
    {
        echo " (which matches " . number_format($totalVerses) . " verse" . plural($totalVerses) . ")";
    }

    echo "</a></span></div><br>";
}
else
{
    echo "<a href='verse_browser.php?V=" . $_GET["V"] . "' style='text-decoration: none'><span class='pill-button'><b>Q. $V</b></a></a></div><br>";
}
if ($SINGLE_LETTER < 1)
{
    echo "<div align=center style='margin-top:-15px; margin-bottom:15px;'>";
    echo "<hr style='width:850px;'>";

    echo "<a href='selection_counts.php?V=" . $_GET["V"] . "&S=" . urlencode($_GET["S"]) . "&COUNT=WORDS&DISPLAY=$DISPLAY'><button id=countWordsButton";
    if ($COUNT == "WORDS")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Count Words in Selection</button></a>";

    echo "<a href='selection_counts.php?V=" . $_GET["V"] . "&S=" . urlencode($_GET["S"]) . "&COUNT=LETTERS&DISPLAY=$DISPLAY'><button id=countLettersButton";
    if ($COUNT == "LETTERS")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Count Letters in Selection</button></a>";

    // style='font-weight:bold;'
    echo "<a href='selection_counts.php?V=" . $_GET["V"] . "&S=" . urlencode($_GET["S"]) . "&COUNT=$COUNT&DISPLAY=TABLE'><button id=displayTableButton";
    if ($DISPLAY == "TABLE")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Display Results as a Table</button></a>";

    echo "<a href='selection_counts.php?V=" . $_GET["V"] . "&S=" . urlencode($_GET["S"]) . "&COUNT=$COUNT&DISPLAY=CHART'><button id=displayChartButton";
    if ($DISPLAY == "CHART")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Display Results as a Chart</button></a>";

    echo "</div>";

    if ($DISPLAY == "TABLE")
    {
        // reset the record pointer
        db_goto($result, 0);

        echo "<div ID=analysisTable align=center>";

        // the first table — word counts

        if ($COUNT == "WORDS")
        {
            echo "<table class='hoverTable persist-area'>";

            echo "<thead class='persist-header table-header-row'>";

            echo "<tr class='table-header-row'>";
            echo "<th width=60><b>Verse</b><br><a href='selection_counts.php?L=-1&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&DISPLAY=TABLE&COUNT=$COUNT&SORT=VERSES-ASC'><img src='images/up.gif'></a> <a href='selection_counts.php?L=-1&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&DISPLAY=TABLE&COUNT=$COUNT&SORT=VERSES-DESC'><img src='images/down.gif'></a></th>";
            echo "<th width=120><b>Word Count</b><br><a href='selection_counts.php?L=-1&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&DISPLAY=TABLE&COUNT=$COUNT&SORT=COUNT-ASC'><img src='images/up.gif'></a> <a href='selection_counts.php?L=-1&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&DISPLAY=TABLE&COUNT=$COUNT&SORT=COUNT-DESC'><img src='images/down.gif'></a></th>";

            echo "</thead>";

            echo "<tbody>";

            for ($i = 0; $i < db_rowcount($result); $i++)
            {
                // grab next database row
                $ROW = db_return_row($result);

                echo "<tr>";

                echo "<td width=60><a href='verse_browser.php?V=" . $ROW["SURA-VERSE"] . "' class=linky>" . $ROW["SURA-VERSE"] . "</a></td>";
                echo "<td width=120 align=center>" . $ROW["b"] . "</td>";

                $GrandWordCount += $ROW["b"];

                echo "</tr>";
            }

            echo "<tr><td>&nbsp;</td><td align=center><b>" . number_format($GrandWordCount) . "</b></td><tr>";

            echo "</tbody>";

            echo "</table>";
        }

        // the second table — letter counts

        if ($COUNT == "LETTERS")
        {
            $letter_column_width = 40;

            echo "<table class='hoverTable persist-area' align=center>";

            echo "<thead class='persist-header table-header-row'>";

            echo "<tr class='table-header-row'>";
            echo "<th width=60 rowspan=3><b>Verse</b></th>";
            echo "<th align=center colspan=28 bgcolor=#c0c0c0><b>Letter Counts</b></th>";

            echo "</tr>";

            echo "<tr>";

            for ($i = mb_strlen($headLetters) - 1; $i >= 0; $i--)
            {
                echo "<th bgcolor=#c0c0c0 align=center><b>" . mb_substr($headLetters, $i, 1) . "</b></th>";
            }

            echo "</tr>";

            echo "<tr>";

            for ($i = 28; $i > 0; $i--)
            {
                echo "<th width=$letter_column_width bgcolor=#c0c0c0><a href='selection_counts.php?L=$i&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "'><img src='images/stats.gif' height=10 width=18></a></th>";
            }

            echo "</tr>";

            echo "</thead>";

            echo "<tbody>";

            foreach ($lettersArray as $ROW)
            {
                echo "<tr>";

                echo "<td width=60><a href='verse_browser.php?V=" . $ROW["VERSE"] . "' class=linky>" . $ROW["VERSE"] . "</a></td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["YA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["WAW"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["HAA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["NOON"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["MEEM"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["LAM"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["KAF"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["QAF"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["FA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["GHAYN"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["AYN"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["ZZA"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["TTA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["DAD"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["SAD"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["SHEEN"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["SEEN"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["ZAIN"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["RA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["THAL"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["DAL"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["KHA"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["HA"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["JIM"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["THA"]) . "</td>";

                echo "<td width=$letter_column_width align=center>" . number_format($ROW["TA"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["BA"]) . "</td>";
                echo "<td width=$letter_column_width align=center>" . number_format($ROW["ALIF"]) . "</td>";

                echo "</tr>";
            }

            // print the letter totals row

            echo "<tr class=smaller_text_for_mini_dialogs>";
            echo "<td width=60>&nbsp;</td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["YA"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["WAW"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["HAA"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["NOON"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["MEEM"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["LAM"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["KAF"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["QAF"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["FA"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["GHAYN"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["AYN"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["ZZA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["TTA"]) . "</b></td>";

            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["DAD"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["SAD"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["SHEEN"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["SEEN"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["ZAIN"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["RA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["THAL"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["DAL"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["KHA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["HA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["JIM"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["THA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["TA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["BA"]) . "</b></td>";
            echo "<td width=$letter_column_width align=center><b>" . number_format($totalsArray["ALIF"]) . "</b></td>";

            echo "</tr>";

            // print the grand total final row

            $grand_total = 0;
            foreach ($totalsArray as $value)
            {
                $grand_total += $value;
            }
            echo "<tr><td align=center colspan=29><b>" . number_format($grand_total) . "</b></td></tr>";

            echo "</tbody>";

            echo "</table>";
        }

        echo "</div>";    // close the analysisTable div
    }

    // chart div

    if ($DISPLAY == "CHART")
    {
        echo "<div align=center id=mainChartContainer></div>";

        // legends
        if ($shownMeccan && $shownMedinan)
        {
            echo "<div align=center ID=legendWordsChart style='margin-left:40px;'>";
            echo "(<font size=-1 color='#6060ff'>Meccan Suras</font> | <font size=-1 color='#ff9090'>Medinan Suras</font>)";
            echo "</div>";
        }
    }
}
else
{
    echo "<b><div align=center>Charting Occurrences of <font size=4>" . mb_substr($headLetters, $SINGLE_LETTER - 1, 1) . "</font> (" . ucfirst(strtolower($KeyNames[$SINGLE_LETTER - 1])) . ")</b><br>";
    echo "<a href='selection_counts.php?L=-1&V=" . $_GET["V"] . "&S=" . $_GET["S"] . "&DISPLAY=TABLE&COUNT=LETTERS'><button style='margin-top:10px;'>Return to Letter Counts</button></a>";
    echo "</div>";

    echo "<div align=center id=chartContainerSingleLetter style='display: block;'></div>";
}

include "library/footer.php";

?>

</body>
</html>