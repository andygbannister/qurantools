<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

    ?>
	<html>
		<head>
		<?php
            include 'library/standard_header.php';
            window_title("Rhyme Counts per Sura");
        ?>

		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
      
	</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

        include "library/back_to_top_button.php";

    // menubar

    include "../library/menu.php";

    // sort order

    $SORT_ORDER = "`Sura Number` ASC";

    if (isset($_GET["SORT"]))
    {
        if ($_GET["SORT"] == "SURA-DESC")
        {
            $SORT_ORDER = "`Sura Number` DESC";
        }

        if ($_GET["SORT"] == "PROV-ASC")
        {
            $SORT_ORDER = "`Provenance`, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "PROV-DESC")
        {
            $SORT_ORDER = "`Provenance` DESC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "VERSES-ASC")
        {
            $SORT_ORDER = "`Verses` ASC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "VERSES-DESC")
        {
            $SORT_ORDER = "`Verses` DESC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "PATTERNS-ASC")
        {
            $SORT_ORDER = "`DISTINCT_PATTERNS` ASC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "PATTERNS-DESC")
        {
            $SORT_ORDER = "`DISTINCT_PATTERNS` DESC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "HOMOGENEITY-ASC")
        {
            $SORT_ORDER = "`PERC_FIGURE` ASC, `Sura Number` ASC";
        }

        if ($_GET["SORT"] == "HOMOGENEITY-DESC")
        {
            $SORT_ORDER = "`PERC_FIGURE` DESC, `Sura Number` ASC";
        }
    }
    else
    {
        $_GET["SORT"] = "";
    }

// PAGE HEADER

echo "<div align=center><h2 class='page-title-text'>Number of Different Verse Ending (Rhyme) Patterns per Sura</h2>";

// draw table header

echo "<table class='hoverTable persist-area'>";

echo "<thead class='persist-header table-header-row'>";

echo "<tr class='table-header-row'>";

            echo "<th><b>Sura</b><br><a href='rhyme_counts_per_sura.php?SORT=SURA-ASC'><img src='../images/up.gif'></a> <a href='rhyme_counts_per_sura.php?SORT=SURA-DESC'><img src='../images/down.gif'></a></th>
			
			<th><b>Provenance</b><br><a href='rhyme_counts_per_sura.php?SORT=PROV-ASC'><img src='../images/up.gif'></a> <a href='rhyme_counts_per_sura.php?SORT=PROV-DESC'><img src='../images/down.gif'></a></th>
			
			<th><b>Verses</b><br><a href='rhyme_counts_per_sura.php?SORT=VERSES-ASC'><img src='../images/up.gif'></a> <a href='rhyme_counts_per_sura.php?SORT=VERSES-DESC'><img src='../images/down.gif'></a></th>
			
			<th><b>Different Verse Endings</b><span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_rhyme_number_of_patterns.php?VIEW=MINI', type: 'post'}\"><a href='/charts/chart_rhyme_number_of_patterns.php'><img src='/images/stats.gif'></a></span><br><a href='rhyme_counts_per_sura.php?SORT=PATTERNS-ASC'><img src='../images/up.gif'></a> <a href='rhyme_counts_per_sura.php?SORT=PATTERNS-DESC'><img src='../images/down.gif'></a></th>";

            // <th><b>% Verse Ending Homogeneity</b><span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_rhyme_homogeneity.php?VIEW=MINI', type: 'post'}\"><a href='/charts/chart_rhyme_homogeneity.php'><img src='/images/stats.gif'></a></span><br><a href='rhyme_counts_per_sura.php?SORT=HOMOGENEITY-ASC'><img src='../images/up.gif'></a> <a href='rhyme_counts_per_sura.php?SORT=HOMOGENEITY-DESC'><img src='../images/down.gif'></a></th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    $result = db_query("
    SELECT DISTINCT(`Sura Number`), `Provenance`, `Verses`, 
(SELECT COUNT(DISTINCT(`FINAL 2 LETTERS`)) FROM `QURAN-VERSE-ENDINGS` 
WHERE `Sura Number`=`SURA` AND `FINAL 2 LETTERS`!='**') DISTINCT_PATTERNS,
(((`VERSES`/(SELECT COUNT(DISTINCT(`FINAL 2 LETTERS`)) FROM `QURAN-VERSE-ENDINGS` 
WHERE `Sura Number`=`SURA` AND `FINAL 2 LETTERS`!='**')) * 100)/`VERSES`) PERC_FIGURE
FROM `SURA-DATA` ORDER BY $SORT_ORDER");

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        echo "<tr>";

        $ROW = db_return_row($result);

        $link_ref = "<a href='sura_rhyme_analysis.php?SURA=" . $ROW["Sura Number"] . "' class=linky>";

        echo "<td ALIGN=CENTER>$link_ref" . $ROW["Sura Number"] . "</a></td>";

        echo "<td ALIGN=CENTER>$link_ref" . $ROW["Provenance"] . "</a></td>";

        echo "<td ALIGN=CENTER>$link_ref" . $ROW["Verses"] . "</a></td>";

        echo "<td ALIGN=CENTER>$link_ref" . $ROW["DISTINCT_PATTERNS"] . "</a></td>";

        // echo "<td ALIGN=CENTER>$link_ref".number_format($ROW["PERC_FIGURE"], 2)."%</a></td>";

        echo "</tr>";
    }

    echo "</tbody>";

    echo "</table>";

    echo "</div>";

    // print footer

    include "../library/footer.php";

?>
	</body>
	
	<script type="text/javascript">

$(function() 
{
	Tipped.create('.chart-tip', {position: 'left', showDelay: 750, skin: 'light', close: true});
});

</script>

</html>