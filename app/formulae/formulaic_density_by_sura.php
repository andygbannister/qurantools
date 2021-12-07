<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

    ?>
	<html>
		<head>
		<?php
            include 'library/standard_header.php';
            window_title("Formulaic Density & Usage by Sura");
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

    $SORT_ORDER = "`SURA` ASC";

    if (isset($_GET["SORT"]))
    {
        if ($_GET["SORT"] == "SURA-DESC")
        {
            $SORT_ORDER = "`SURA` DESC";
        }

        if ($_GET["SORT"] == "ROOTS-ASC")
        {
            $SORT_ORDER = "ALL_ROOTS ASC";
        }

        if ($_GET["SORT"] == "ROOTS-DESC")
        {
            $SORT_ORDER = "ALL_ROOTS DESC";
        }

        if ($_GET["SORT"] == "PART-ASC")
        {
            $SORT_ORDER = "FLAGGED ASC";
        }

        if ($_GET["SORT"] == "PART-DESC")
        {
            $SORT_ORDER = "FLAGGED DESC";
        }

        if ($_GET["SORT"] == "FD-ASC")
        {
            $SORT_ORDER = "FORMULAIC_DENSITY ASC";
        }

        if ($_GET["SORT"] == "FD-DESC")
        {
            $SORT_ORDER = "FORMULAIC_DENSITY DESC";
        }

        if ($_GET["SORT"] == "NUMBER-ASC")
        {
            $SORT_ORDER = "formula_used_count ASC";
        }

        if ($_GET["SORT"] == "NUMBER-DESC")
        {
            $SORT_ORDER = "formula_used_count DESC";
        }

        if ($_GET["SORT"] == "DIVERSITY-ASC")
        {
            $SORT_ORDER = "formula_diversity_count ASC";
        }

        if ($_GET["SORT"] == "DIVERSITY-DESC")
        {
            $SORT_ORDER = "formula_diversity_count DESC";
        }

        if ($_GET["SORT"] == "PROV-ASC")
        {
            $SORT_ORDER = "`Provenance`, `SURA` ASC";
        }

        if ($_GET["SORT"] == "PROV-DESC")
        {
            $SORT_ORDER = "`Provenance` DESC, `SURA` ASC";
        }

        if ($_GET["SORT"] == "UNIQUE-ASC")
        {
            $SORT_ORDER = "`unique_to_sura` ASC";
        }

        if ($_GET["SORT"] == "UNIQUE-DESC")
        {
            $SORT_ORDER = "`unique_to_sura` DESC";
        }
    }
    else
    {
        $_GET["SORT"] = "";
    }

    // what formulae type

    $FORMULA_TYPE = "ROOT";

    if (isset($_GET["TYPE"]))
    {
        $FORMULA_TYPE = $_GET["TYPE"];
        if ($FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "ANY")
        {
            $FORMULA_TYPE = "ROOT";
        }
    }

    // formula length

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

    if ($FORMULA_TYPE == "ROOT-ALL" && $FORMULA_LENGTH == 2)
    {
        $FORMULA_LENGTH = 3;
    }

// PAGE HEADER

echo "<div align=center><h2 class='page-title-text'>Formulaic Density and Usage Statistics per Sura</h2>";

// ==== formula length and type selection form =====

echo "<form action='formulaic_density_by_sura.php' method=GET>";

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

// echo "<button type=SUBMIT>Refresh</button>";

echo "</td></tr>";

echo "</table></div>";

echo "</form>";

// set up column titles

if ($FORMULA_TYPE == "ROOT")
{
    $columnB = "Roots";
}

if ($FORMULA_TYPE == "ROOT-ALL" || $FORMULA_TYPE == "ANY")
{
    $columnB = "Words";
}

if ($FORMULA_TYPE == "LEMMA")
{
    $columnB = "Lemmata";
}

// draw table header

echo "<table class='hoverTable persist-area'>";

echo "<thead class='persist-header table-header-row'>";

echo "<tr class='table-header-row'>";

            echo "<th><b>Sura</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=SURA-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=SURA-DESC'><img src='../images/down.gif'></a></th>
			<th><b>Provenance</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PROV-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PROV-DESC'><img src='../images/down.gif'></a></th>
			
			<th><b>$columnB</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=ROOTS-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=ROOTS-DESC'><img src='../images/down.gif'></a></th>
			<th><b>$columnB Part of<br>a Formula</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PART-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PART-DESC'><img src='../images/down.gif'></a></th>
			
			<th>
			<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
			<a href='/charts/chart_formulae_used_per_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='../images/stats.gif'></a> <b>Number of<br>Formulae Used</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=NUMBER-ASC'><img src='../images/up.gif'></a></span> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=NUMBER-DESC'><img src='../images/down.gif'></a></th>
			
			<th>
			<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_formulaic_diversity_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
			<a href='../charts/chart_formulaic_diversity_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='../images/stats.gif'></a></span> <b>Diversity of<br>Formulae Used</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=DIVERSITY-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=DIVERSITY-DESC'><img src='../images/down.gif'></a></th>
			
			<th>
			<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'../charts/chart_formulae_unique_to_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
			<a href='/charts/chart_formulae_unique_to_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='/images/stats.gif'></a></span> <b>Formulae<br>Unique to Sura</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=UNIQUE-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=UNIQUE-DESC'><img src='../images/down.gif'></a></th>
			
			<th>
			<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_formulaic_density.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
			<a href='/charts/chart_formulaic_density.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='/images/stats.gif'></a></span> <b>Formulaic Density</b><br><a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=FD-ASC'><img src='../images/up.gif'></a> <a href='formulaic_density_by_sura.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=FD-DESC'><img src='../images/down.gif'></a></span></th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    $GRAND_TOTAL         = 0;
    $GRAND_TOTAL_MECCAN  = 0;
    $GRAND_TOTAL_MEDINAN = 0;

    $GRAND_IN_FORMULA         = 0;
    $GRAND_IN_FORMULA_MECCAN  = 0;
    $GRAND_IN_FORMULA_MEDINAN = 0;

    if ($FORMULA_TYPE == "ROOT")
    {
        $result = db_query("SELECT DISTINCT(`SURA`), SUM(`QTL-ROOT`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`QTL-ROOT`!='') FORMULAIC_DENSITY, `PROVENANCE`, (SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='ROOT' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_used_count, (SELECT COUNT(DISTINCT `FORMULA`) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='ROOT' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_diversity_count, 
		(SELECT COUNT(DISTINCT `CONCAT OF FORMULA AND TYPE`) FROM `FORMULA-LIST` WHERE `APPEARS IN HOW MANY SURAS`=1 AND `TYPE`='ROOT' AND `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") unique_to_sura 
		FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' GROUP BY `SURA` ORDER BY $SORT_ORDER");
    }

    if ($FORMULA_TYPE == "ROOT-ALL")
    {
        $result = db_query("SELECT DISTINCT(`SURA`), SUM(`ROOT OR PARTICLE`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`ROOT OR PARTICLE`!='') FORMULAIC_DENSITY, `PROVENANCE`, (SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='ROOT-ALL' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_used_count, (SELECT COUNT(DISTINCT `FORMULA`) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='ROOT-ALL' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_diversity_count,
		(SELECT COUNT(DISTINCT `CONCAT OF FORMULA AND TYPE`) FROM `FORMULA-LIST` WHERE `APPEARS IN HOW MANY SURAS`=1 AND `TYPE`='ROOT-ALL' AND `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") unique_to_sura 
		FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' GROUP BY `SURA` ORDER BY $SORT_ORDER");
    }

    if ($FORMULA_TYPE == "LEMMA")
    {
        $result = db_query("SELECT DISTINCT(`SURA`), SUM(`QTL-LEMMA`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`QTL-LEMMA`!='') FORMULAIC_DENSITY, `PROVENANCE`, (SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='LEMMA' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_used_count, (SELECT COUNT(DISTINCT `FORMULA`) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `TYPE`='LEMMA' AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_diversity_count,
		(SELECT COUNT(DISTINCT `CONCAT OF FORMULA AND TYPE`) FROM `FORMULA-LIST` WHERE `APPEARS IN HOW MANY SURAS`=1 AND `TYPE`='LEMMA' AND `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") unique_to_sura 
		 FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' GROUP BY `SURA` ORDER BY $SORT_ORDER");
    }

    if ($FORMULA_TYPE == "ANY")
    {
        $result = db_query("SELECT DISTINCT(`SURA`), SUM(`ROOT OR PARTICLE`!='') ALL_ROOTS, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) FLAGGED, SUM(`FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE` > 0) / SUM(`ROOT OR PARTICLE`!='') FORMULAIC_DENSITY, `PROVENANCE`, (SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_used_count, (SELECT COUNT(DISTINCT CONCAT(`FORMULA`,'-',`TYPE`)) FROM `FORMULA-LIST` WHERE `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") formula_diversity_count,
		(SELECT COUNT(DISTINCT `CONCAT OF FORMULA AND TYPE`) FROM `FORMULA-LIST` WHERE `APPEARS IN HOW MANY SURAS`=1 AND `START SURA`=`SURA` AND `LENGTH`=" . db_quote($FORMULA_LENGTH) . ") unique_to_sura 
		
		 FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' GROUP BY `SURA` ORDER BY $SORT_ORDER");
    }

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        echo "<tr>";

        $ROW = db_return_row($result);

        echo "<td ALIGN=CENTER><a href='formulaic_density_by_verse.php?SURA=" . $ROW["SURA"] . "&FORMULA=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky>" . $ROW["SURA"] . "</a></td>";

        echo "<td ALIGN=CENTER>" . $ROW["PROVENANCE"] . "</td>";

        $GRAND_TOTAL += $ROW["ALL_ROOTS"];
        $GRAND_IN_FORMULA += $ROW["FLAGGED"];

        if ($ROW["PROVENANCE"] == "Meccan")
        {
            $GRAND_TOTAL_MECCAN += $ROW["ALL_ROOTS"];
            $GRAND_IN_FORMULA_MECCAN += $ROW["FLAGGED"];
        }
        else
        {
            $GRAND_TOTAL_MEDINAN += $ROW["ALL_ROOTS"];
            $GRAND_IN_FORMULA_MEDINAN += $ROW["FLAGGED"];
        }

        echo "<td ALIGN=CENTER>" . number_format($ROW["ALL_ROOTS"]) . "</td>";

        echo "<td ALIGN=CENTER><a href='list_formulae.php?SURA=" . $ROW["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky>" . number_format($ROW["FLAGGED"]) . "</a></td>";

        echo "<td align=center><a href='list_formulae.php?SURA=" . $ROW["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky>" . number_format($ROW["formula_used_count"]) . "</a></td>";

        echo "<td align=center><a href='list_formulae.php?SURA=" . $ROW["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky>" . number_format($ROW["formula_diversity_count"]) . "</a></td>";

        echo "<td align=center><a href='list_formulae.php?SURA=" . $ROW["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&UNIQUE=1' class=linky>" . number_format($ROW["unique_to_sura"]) . "</a></td>";

        echo "<td ALIGN=CENTER>" . number_format(($ROW["FLAGGED"] * 100 / $ROW["ALL_ROOTS"]), 2) . "%";

        echo "<span style='float:right;' class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_formulaic_density_by_verse.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI&SURA=" . $ROW["SURA"] . "', type: 'post'}\"><a href='../charts/chart_formulaic_density_by_verse.php?SURA=" . $ROW["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='../images/stats.gif'></a></span></td>";

        echo "</tr>";
    }

    echo "</tbody>";

    echo "</table>";
    echo "<br>";

    echo "<table border=1 cellspacing=0 cellpadding=4 class='hoverTable' width=720>";
    echo "<tr>";

    echo "<td>&nbsp;</td><td align=center><b>Total $columnB</b></td><td align=center><b>Total $columnB Part<br>of Formulae</b></td><td align=center><b>Overall<br>Formulaic Density</b></td><td align=center><b>Average	<br>Formulaic Density</b></td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td>All Suras</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_TOTAL) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_IN_FORMULA) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format(($GRAND_IN_FORMULA * 100 / $GRAND_TOTAL), 2) . "%</td>";
    echo "<td align=CENTER>" . number_format(db_return_one_record_one_field("SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE`"), 2) . "%</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Meccan Suras</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_TOTAL_MECCAN) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_IN_FORMULA_MECCAN) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format(($GRAND_IN_FORMULA_MECCAN * 100 / $GRAND_TOTAL_MECCAN), 2) . "%</td>";
    echo "<td align=CENTER>" . number_format(db_return_one_record_one_field("SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"), 2) . "%</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Medinan Suras</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_TOTAL_MEDINAN) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format($GRAND_IN_FORMULA_MEDINAN) . "</td>";
    echo "<td ALIGN=CENTER>" . number_format(($GRAND_IN_FORMULA_MEDINAN * 100 / $GRAND_TOTAL_MEDINAN), 2) . "%</td>";
    echo "<td align=CENTER>" . number_format(db_return_one_record_one_field("SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"), 2) . "%</td>";
    echo "</tr>";

    echo "</table>";

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