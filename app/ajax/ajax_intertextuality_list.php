<?php

// regenerate the session

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once 'library/functions.php';

// pull apart the reference

$reference = explode(":", $_GET["V"]);

// look up sources

$result_sources = db_query(
    "SELECT * FROM `INTERTEXTUAL LINKS` 
		LEFT JOIN `INTERTEXTUAL SOURCES` ON `SOURCE ID`=`SOURCE` 
			WHERE `SURA`=" . db_quote($reference[0]) . " AND (`START VERSE`<=" . db_quote($reference[1]) . " AND `END VERSE`>=" . db_quote($reference[1]) . ")
"
);

echo "<h2>Intertextual Connections: Q. " . $_GET["V"] . " <a href='/charts/chart_intertextual_connections.php'><img src='/images/stats.gif'></a> <a href='/intertextuality/intertextual_browser.php'><img src='/images/table.png'></a></h2>";

echo "<div id='intertext-container' class='tipped-container'>";

echo "  <div id='intertext-scroller' class='tipped-scroller'>";
echo "    <table id='intertext-table' class='hoverTable'>";

echo "<thead>";

echo "<tr>";

    echo "<th><b>Source Name</b></th>";

    echo "<th><b>Reference</b></th>";

    echo "<th><b>Date Range</b></th>";

    echo "<th>&nbsp;</th>";

echo "</tr>";

echo "</thead>";

echo "<tbody>";

for ($i = 0; $i < db_rowcount($result_sources); $i++)
{
    // grab next database row
    $ROW = db_return_row($result_sources);

    echo "<tr>";

    echo "<td align=middle>";

    echo htmlentities($ROW["SOURCE NAME"]);

    echo "</td>";

    echo "<td align=middle>";

    echo htmlentities($ROW["SOURCE REF"]);

    echo "</td>";

    echo "<td align=middle>";

    echo htmlentities($ROW["SOURCE DATE"]);

    echo "</td>";

    echo "<td width=120>";

    echo "<a href='#' onclick='$.colorbox({href:\"/intertextuality/intertextual_viewer.php?ID=" . $ROW["INTERTEXT ID"] . "&LIGHTVIEW=YES\",width:\"90%\", height:\"90%\"});'>";

    echo "<button class=smaller_text_for_mini_dialogs>View Passage</button>";
    echo "</a>";

    if ($ROW["SOURCE URL"] != "")
    {
        echo "<a href='" . $ROW["SOURCE URL"] . "' target='_blank'>";
        echo "<button class=smaller_text_for_mini_dialogs>View Entire Source</button>";
        echo "</a>";
    }

    echo "</td>";

    echo "</tr>";
}

echo "</tbody>";

echo "</table>";

echo "</div>";

echo "</div>";
