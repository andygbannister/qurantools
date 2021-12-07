<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/verse_parse.php';

// header

?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Formulaic Density by Verse");
        ?>

    <script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

// convert POST to GET
if (isset($_POST["L"]))
{
    $_GET["L"]    = $_POST["L"];
    $_GET["TYPE"] = $_POST["TYPE"];
}

// menubar

include "../library/menu.php";

// what formulae type

    $FORMULA_TYPE = "ROOT";

    if (isset($_GET["TYPE"]))
    {
        $FORMULA_TYPE = $_GET["TYPE"];

        if ($FORMULA_TYPE == "EVERYTHING")
        {
            $FORMULA_TYPE = "ANY";
        }

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
        if ($FORMULA_LENGTH < 2)
        {
            $FORMULA_LENGTH = 2;
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

// which sura or verse to view
$SURA = 0;
if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] > 0 && $_GET["SURA"] < 115)
    {
        $SURA             = $_GET["SURA"];
        $extra_title_text = "<a href='../verse_browser.php?V=$SURA&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Sura $SURA)</a>";
    }
}
else
{
    $_GET["SURA"] = "";
}

// or do we want to view a verse or search result?
// limit by selection of verses

$RANGE_SQL = "";

if (isset($_GET["V"]))
{
    $V = $_GET["V"];

    if ($V != "")
    {
        parse_verses($V, true, 0);

        if ($_GET["V"] == "SEARCH")
        {
            if (isset($_GET["S"]))
            {
                $extra_title_text = "<a href='../verse_browser.php?S=" . $_GET["S"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Verses Matching Search: '" . $_GET["S"] . "')</a>";
            }
            else
            {
                $extra_title_text = "(Verses Matching Search Criteria)";
            }
        }
        else
        {
            $extra_title_text = "<a href='../verse_browser.php?V=" . $_GET["V"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Q. $V)</a>";
        }
    }
}
else
{
    $_GET["V"] = "";
}

// and to avoid later "unknown index" errors
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

// sort order

    $SORT_ORDER = "`SURA`, `VERSE` ASC";

    if (isset($_GET["SORT"]))
    {
        if ($_GET["SORT"] == "SURA-DESC")
        {
            $SORT_ORDER = "`SURA`, `VERSE` DESC";
        }

        if ($_GET["SORT"] == "WORDS-ASC")
        {
            $SORT_ORDER = "WORDS ASC";
        }

        if ($_GET["SORT"] == "WORDS-DESC")
        {
            $SORT_ORDER = "WORDS DESC";
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
    }
    else
    {
        $_GET["SORT"] = "";
    }

// title and navigation stuff

echo "<div align=center><h2 class='page-title-text'>Formulaic Density by Verse<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'../charts/chart_formulaic_density_by_verse.php?SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
<a href='../charts/chart_formulaic_density_by_verse.php?SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'>
<img src='../images/stats.gif'></a></span>
<br><font size=3>$extra_title_text</font></h2>";

// ==== formula length and type selection form =====

echo "<form action='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&SURA=" . $_GET["SURA"] . "&V=" . $_GET["V"] . "' method=POST>";

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

// echo "<button type=SUBMIT>Refresh</button>";

echo "</td></tr>";

echo "</table></div>";

echo "</form>";

echo "<div style='margin-bottom:10px;'>";

echo "<a href='../charts/chart_formulaic_density_by_verse.php?SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'>";
echo "<button style='margin-top:-4px;'>View as Chart</button>";
echo "</a>";

echo "<a href='list_formulae.php?S=" . $_GET["S"] . "&L=$FORMULA_LENGTH&SORT=" . $_GET["SORT"] . "&TYPE=$FORMULA_TYPE&SURA=$SURA&V=" . $_GET["V"] . "'>";
echo "<button style='margin-top:-4px;'>List All Formulae in This Selection</button>";
echo "</a>";

echo "</div>";

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

    $GRAND_TOTAL      = 0;
    $GRAND_IN_FORMULA = 0;

    $sql = "";

    if ($FORMULA_TYPE == "ROOT")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`QTL-ROOT`!='') as WORDS, SUM(`QTL-ROOT`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`QTL-ROOT`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`QTL-ROOT`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    if ($FORMULA_TYPE == "LEMMA" || $FORMULA_TYPE == "ANY")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`QTL-LEMMA`!='') as WORDS, SUM(`QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`QTL-LEMMA`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    if ($FORMULA_TYPE == "ROOT-ALL")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`ROOT OR PARTICLE`!='') as WORDS, SUM(`ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`ROOT OR PARTICLE`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    // draw the start of the table
    echo "<table class='hoverTable persist-area'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr>";

            echo "<th width=110 bgcolor=#c0c0c0><b>Reference</b><br><a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=SURA-ASC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/up.gif'></a> <a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=SURA-DESC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/down.gif'></a></th>
			<th width=80 bgcolor=#c0c0c0><b>$columnB</b><br><a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=WORDS-ASC&SURA=$SURA'><img src='../images/up.gif'></a> <a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=WORDS-DESC&SURA=$SURA'><img src='../images/down.gif'></a></th>
			<th width=200 bgcolor=#c0c0c0><b>$columnB Part of a Formula</b><br><a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PART-ASC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/up.gif'></a> <a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=PART-DESC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/down.gif'></a></th>
			
			<th width=190 bgcolor=#c0c0c0>
			<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'../charts/chart_formulaic_density_by_verse.php?SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">
			<a href='../charts/chart_formulaic_density_by_verse.php?MODE=2&S=" . $_GET["S"] . "&L=$FORMULA_LENGTH&SORT=" . $_GET["SORT"] . "&TYPE=ROOT-ALL&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/stats.gif'></a></span> <b>Formulaic Density</b><br><a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=FD-ASC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/up.gif'></a> <a href='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SORT=FD-DESC&SURA=$SURA&V=" . $_GET["V"] . "'><img src='../images/down.gif'></a></th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    // perform the query
    $result = db_query($sql);

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        echo "<tr>";

        $ROW = db_return_row($result);

        $VERSE_LINK = "<a href='../verse_browser.php?V=" . $ROW["SURA-VERSE"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        echo "<td width=110 ALIGN=CENTER>$VERSE_LINK" . $ROW["SURA-VERSE"] . "</a></td>";

        echo "<td width=80 ALIGN=CENTER>$VERSE_LINK" . $ROW["WORDS"] . "</a></td>";

        echo "<td width=200 ALIGN=CENTER>$VERSE_LINK" . $ROW["FLAGGED"] . "</a></td>";

        echo "<td width=190 ALIGN=CENTER>$VERSE_LINK";
        if ($ROW["WORDS"] > 0)
        {
            echo number_format(($ROW["FLAGGED"] * 100) / $ROW["WORDS"], 2);
        }
        else
        {
            echo "0.00";
        }
        echo "%</a></td>";

        // UPDATE COUNTERS

        $GRAND_TOTAL += $ROW["WORDS"];
        $GRAND_IN_FORMULA += $ROW["FLAGGED"];

        echo "</tr>";
    }

echo "<tr>";
echo "<td bgcolor=#d0d0d0 align=center><b>" . number_format(db_rowcount($result)) . " verse" . plural(db_rowcount($result)) . "</b></td>";
echo "<td bgcolor=#d0d0d0 align=center><b>" . number_format($GRAND_TOTAL) . "</b></td>";
echo "<td bgcolor=#d0d0d0 align=center><b>" . number_format($GRAND_IN_FORMULA) . "</b></td>";
echo "<td bgcolor=#d0d0d0 align=center><b>" . number_format(($GRAND_IN_FORMULA * 100) / $GRAND_TOTAL, 2) . "%</b></td>";
echo "</tr>";

echo "</tbody>";

echo "</table>";

// print footer

include "../library/footer.php";

?>

</body>

<script type="text/javascript">
    $(function() {
        Tipped.create('.chart-tip', {
            position: 'left',
            showDelay: 750,
            skin: 'light',
            close: true
        });
    });
</script>

</html>