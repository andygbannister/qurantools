<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// sort order

$sort_order = "`Sura Number`";

if (isset($_GET["SORT"]))
{
    switch ($_GET["SORT"])
    {
        case "SURA-NUMBER-DESC":
            $sort_order = "`Sura Number` DESC";
            break;

        case "SURA-ARABIC-NAME-ASC":
            $sort_order = "LOWER(`Arabic Name`)";
            break;

        case "SURA-ARABIC-NAME-DESC":
            $sort_order = "LOWER(`Arabic Name`) DESC";
            break;

        case "SURA-ENGLISH-NAME-ASC":
            $sort_order = "LOWER(`English Name`)";
            break;

        case "SURA-ENGLISH-NAME-DESC":
            $sort_order = "LOWER(`English Name`) DESC";
            break;

        case "VERSES-ASC":
            $sort_order = "`Verses`";
            break;

        case "VERSES-DESC":
            $sort_order = "`Verses` DESC";
            break;

        case "PROVENANCE-ASC":
            $sort_order = "`Verses`";
            break;

        case "PROVENANCE-DESC":
            $sort_order = "`Verses` DESC";
            break;

        case "WORDS-ASC":
            $sort_order = "words";
            break;

        case "WORDS-DESC":
            $sort_order = "words_discount_initials DESC";
            break;

        case "WORDS-DQI-ASC":
            $sort_order = "words";
            break;

        case "WORDS-DQI-DESC":
            $sort_order = "words_discount_initials DESC";
            break;

        case "WORDS-AFFECTED-ASC":
            $sort_order = "words_affected";
            break;

        case "WORDS-AFFECTED-DESC":
            $sort_order = "words_affected DESC";
            break;

        case "WORDS-AFFECTED-PERCENTAGE-ASC":
            $sort_order = "(words_affected / words)";
            break;

        case "WORDS-AFFECTED-PERCENTAGE-DESC":
            $sort_order = "(words_affected / words) DESC";
            break;

        case "WORDS-PART-FORMULAE-ASC":
            $sort_order = "words_part_of_formulae";
            break;

        case "WORDS-PART-FORMULAE-DESC":
            $sort_order = "words_part_of_formulae DESC";
            break;
    }
}
else
{
    $_GET["SORT"] = "";
}

    // filter
    $SHOW = "";
    if (isset($_GET["SHOW"]))
    {
        $SHOW = $_GET["SHOW"];
    }
    else
    {
        $_GET["SHOW"] = "";
    }

    if ($SHOW != "MECCAN" && $SHOW != "MEDINAN")
    {
        $SHOW = "";
    }

    // work out the title

    $reportTitle = "List of All Suras";
    $windowTitle = "Sura List";

    if ($SHOW != "")
    {
        $windowTitle = "Sura List (" . ucfirst(strtolower($SHOW)) . " Suras)";
        $reportTitle = "List of " . ucfirst(strtolower($SHOW)) . " Suras";
    }

 ?>
<!DOCTYPE html>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title($windowTitle)
        ?>

    <script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript" src="library/js/persistent_table_headers.js"></script>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>

        <?php

    include "library/back_to_top_button.php";

    // menubar

    include "library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>$reportTitle</h2>";

    echo "<div class='button-block-with-spacing'>";
    echo "<a href='browse_sura.php?SORT=" . $_GET["SORT"] . "'><button";
    if ($SHOW == "")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show All Suras</button></a>";

    echo "<a href='browse_sura.php?SHOW=MECCAN&SORT=" . $_GET["SORT"] . "'><button";
    if ($SHOW == "MECCAN")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Only Meccan Suras</button></a>";

    echo "<a href='browse_sura.php?SHOW=MEDINAN&SORT=" . $_GET["SORT"] . "'><button";
    if ($SHOW == "MEDINAN")
    {
        echo " style='font-weight:bold;'";
    }
    echo ">Show Only Medinan Suras</button></a>";

    echo "</div>";

    if ($SHOW == "")
    {
        $result = db_query("SELECT *, (SELECT count(*) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND `SEGMENT`=1) words, (SELECT count(*) FROM `QURAN-DATA`WHERE `SURA`=`Sura Number` AND `SEGMENT`=1 AND `TAG EXPLAINED` !='Qur\'anic Initials') words_discount_initials, (SELECT COUNT(DISTINCT `GLOBAL WORD NUMBER`) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND (`FORMULA-3-ANY`>0 OR `FORMULA-4-ANY`>0 OR `FORMULA-5-ANY`>0)) words_part_of_formulae FROM `SURA-DATA` ORDER BY $sort_order");
    }
    else
    {
        $result = db_query("SELECT *, (SELECT count(*) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND `SEGMENT`=1) words, (SELECT count(*) FROM `QURAN-DATA`WHERE `SURA`=`Sura Number` AND `SEGMENT`=1 AND `TAG EXPLAINED` !='Qur\'anic Initials') words_discount_initials, (SELECT COUNT(DISTINCT `GLOBAL WORD NUMBER`) FROM `QURAN-DATA` WHERE `SURA`=`Sura Number` AND (`FORMULA-3-ANY`>0 OR `FORMULA-4-ANY`>0 OR `FORMULA-5-ANY`>0)) words_part_of_formulae FROM `SURA-DATA` WHERE `Provenance`='" . db_quote(ucfirst(strtolower($SHOW))) . "' ORDER BY $sort_order");
    }

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table id=BrowseSuraTable class='hoverTable persist-area fixedTable'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th bgcolor=#c0c0c0 rowspan=2 align=center width=30><br><b>#</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-NUMBER-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-NUMBER-DESC'><img src='/images/down.gif'></a></th><th bgcolor=#c0c0c0 align=center rowspan=2 width=90><b>Arabic Name</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-ARABIC-NAME-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-ARABIC-NAME-DESC'><img src='/images/down.gif'></a></th><th align=center bgcolor=#c0c0c0 rowspan=2 width=100><br><b>English Name</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-ENGLISH-NAME-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=SURA-ENGLISH-NAME-DESC'><img src='/images/down.gif'></a></th>";

    echo "<th bgcolor=#c0c0c0 rowspan=2 width=90><br><b>Provenance</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=PROVENANCE-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=PROVENANCE-DESC'><img src='/images/down.gif'></a></th><th bgcolor=#c0c0c0 rowspan=2 width=50><br><b>Verses</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=VERSES-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=VERSES-DESC'><img src='/images/down.gif'></a></th><th align=center bgcolor=#c0c0c0 rowspan=2 width=80><br><b>Words</b><span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'charts/chart_sura_length.php?VIEW=MINI', type: 'post'}\"><a href='charts/chart_sura_length.php?TYPE=2&SHOW=$SHOW'><img src='images/stats.gif'></a></span><br><a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-DESC'><img src='/images/down.gif'></a></th><th bgcolor=#c0c0c0 align=center rowspan=2 width=150><br><b>Words Discounting Qur’anic Initials</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-DQI-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-DQI-DESC'><img src='/images/down.gif'></a></th>";

    echo "<th bgcolor=#c0c0c0 align=center rowspan=2 width=110><br><b>Words Part of a Formula</b><br><a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-PART-FORMULAE-ASC'><img class=top-padding-only src='/images/up.gif'></a> <a href='browse_sura.php?SHOW=$SHOW&SORT=WORDS-PART-FORMULAE-DESC'><img src='/images/down.gif'></a></th>";

    echo "</tr>";

    echo "<tr>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    $TOTAL_VERSES         = 0;
    $TOTAL_VERSES_MECCAN  = 0;
    $TOTAL_VERSES_MEDINAN = 0;

    $TOTAL_WORDS         = 0;
    $TOTAL_WORDS_MECCAN  = 0;
    $TOTAL_WORDS_MEDINAN = 0;

    $TOTAL_WORDS_LESS_INITIALS         = 0;
    $TOTAL_WORDS_LESS_INITIALS_MECCAN  = 0;
    $TOTAL_WORDS_LESS_INITIALS_MEDINAN = 0;

    $TOTAL_FORMULAE         = 0;
    $TOTAL_FORMULAE_MECCAN  = 0;
    $TOTAL_FORMULAE_MEDINAN = 0;

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        $link = "<a href='verse_browser.php?V=" . $ROW["Sura Number"] . "' class=linky>";

        echo "<td align=center width=30>$link" . $ROW["Sura Number"] . "</a></td>";

        echo "<td width=90>$link<i>" . $ROW["Arabic Name"] . "</a></i></td>";

        echo "<td width=100>$link" . $ROW["English Name"] . "</a></td>";

        // calculate the number of words (it's only printed if we're not focussing on CHANGES)

        $TOTAL_WORDS += $ROW["words"];
        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_WORDS_MECCAN += $ROW["words"];
        }
        else
        {
            $TOTAL_WORDS_MEDINAN += $ROW["words"];
        }

        echo "<td align=center width=90>$link" . $ROW["Provenance"] . "</a></td>";
        echo "<td align=center width=50>$link" . $ROW["Verses"] . "</a></td>";

        $TOTAL_VERSES += $ROW["Verses"];
        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_VERSES_MECCAN += $ROW["Verses"];
        }
        else
        {
            $TOTAL_VERSES_MEDINAN += $ROW["Verses"];
        }

        // print the number of words
        echo "<td align=center width=80>$link" . number_format($ROW["words"]) . "</a>";

        echo "&nbsp;<span style='float:right;'>";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'charts/chart_verse_lengths.php?TYPE=1&VIEW=MINI&S=" . $ROW["Sura Number"] . "', type: 'post'}\">";
        }

        echo "<a href='charts/chart_verse_lengths.php?S=" . $ROW["Sura Number"] . "'><img src='images/stats.gif'></a>";

        if (!isMobile())
        {
            echo "</span>";
        }

        echo "</span></td>";

        echo "<td align=center width=150>$link" . number_format($ROW["words_discount_initials"]) . "</a>";

        echo "&nbsp;<span style='float:right;'>";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'charts/chart_length_characteristics.php?VIEW=MINI&SURA=" . $ROW["Sura Number"] . "', type: 'post'}\">";
        }

        echo "<a href='charts/chart_length_characteristics.php?SURA=" . $ROW["Sura Number"] . "'><img src='images/stats-line.png'></a>";
        if (!isMobile())
        {
            echo "</span>";
        }

        echo "</span>";

        echo "</td>";

        $TOTAL_WORDS_LESS_INITIALS += $ROW["words_discount_initials"];
        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_WORDS_LESS_INITIALS_MECCAN += $ROW["words_discount_initials"];
        }
        else
        {
            $TOTAL_WORDS_LESS_INITIALS_MEDINAN += $ROW["words_discount_initials"];
        }

        // formulaic count
        echo "<td align=center width=110>";

        echo "<a href='formulae/list_formulae.php?SURA=" . $ROW["Sura Number"] . "&TYPE=EVERYTHING' class=linky>";

        echo number_format($ROW["words_part_of_formulae"]);

        $TOTAL_FORMULAE += $ROW["words_part_of_formulae"];
        if ($ROW["Provenance"] == "Meccan")
        {
            $TOTAL_FORMULAE_MECCAN += $ROW["words_part_of_formulae"];
        }
        else
        {
            $TOTAL_FORMULAE_MEDINAN += $ROW["words_part_of_formulae"];
        }

        echo "</a></td>";

        echo "</tr>";
    }

    echo "<tr>";

    echo "<td colspan=3 align=center>";
    echo "<b>" . db_rowcount($result) . " Suras</b>";
    echo "</td>";

        echo "<td></td>";

        echo "<td align=center";
        if ($SHOW == "")
        {
            echo " title='Meccan: " . number_format($TOTAL_VERSES_MECCAN) . " verses (" . number_format(($TOTAL_VERSES_MECCAN / $TOTAL_VERSES) * 100, 2) . "%); Medinan: " . number_format($TOTAL_VERSES_MEDINAN) . " verses (" . number_format(($TOTAL_VERSES_MEDINAN / $TOTAL_VERSES) * 100, 2) . "%)'";
        }
        echo "><b>" . number_format($TOTAL_VERSES) . "<b></td>";

        echo "<td align=center";
        if ($SHOW == "")
        {
            echo " title='Meccan: " . number_format($TOTAL_WORDS_MECCAN) . " words (" . number_format(($TOTAL_WORDS_MECCAN / $TOTAL_WORDS) * 100, 2) . "%); Medinan: " . number_format($TOTAL_WORDS_MEDINAN) . " words (" . number_format(($TOTAL_WORDS_MEDINAN / $TOTAL_WORDS) * 100, 2) . "%)'";
        }
        echo "><b>" . number_format($TOTAL_WORDS) . "</b></td>";

        echo "<td align=center";
        if ($SHOW == "")
        {
            echo " title='Meccan: " . number_format($TOTAL_WORDS_LESS_INITIALS_MECCAN) . " words (" . number_format(($TOTAL_WORDS_LESS_INITIALS_MECCAN / $TOTAL_WORDS_LESS_INITIALS) * 100, 2) . "%); Medinan: " . number_format($TOTAL_FORMULAE_MEDINAN) . " words (" . number_format(($TOTAL_WORDS_LESS_INITIALS_MEDINAN / $TOTAL_WORDS_LESS_INITIALS) * 100, 2) . "%)'";
        }
        echo "><b>" . number_format($TOTAL_WORDS_LESS_INITIALS) . "</b></td>";

        echo "<td align=center";
        if ($SHOW == "")
        {
            echo " title='Meccan: " . number_format($TOTAL_FORMULAE_MECCAN) . " formulae (" . number_format(($TOTAL_FORMULAE_MECCAN / $TOTAL_FORMULAE) * 100, 2) . "%); Medinan: " . number_format($TOTAL_FORMULAE_MEDINAN) . " formulae (" . number_format(($TOTAL_FORMULAE_MEDINAN / $TOTAL_FORMULAE) * 100, 2) . "%)'";
        }
        echo "><b>" . number_format($TOTAL_FORMULAE) . "</b></td>";

    echo "</tr>";

    echo "</tbody>";

    echo "</table>";

    echo "</div>";

    // print footer

    include "library/footer.php";

?>

</body>

<script type="text/javascript">
    // alert(document.getElementById('BrowseSuraTable').offsetWidth);		

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