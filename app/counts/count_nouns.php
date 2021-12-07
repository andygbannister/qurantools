<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 420;
$CURRENT_PAGE   = 1;

// ensure "SORT" is set, just to save lots of checking later
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

// pre-render data or calculate it live?

$PRE_RENDER = true;
$PRE_PASS   = "";

if (isset($_GET["PRERENDER"]))
{
    if ($_GET["PRERENDER"] == "OFF")
    {
        $PRE_RENDER = false;
        $PRE_PASS   = "&PRERENDER=OFF";
    }
}

// group by
$GROUP_BY = "ROOT";
if (isset($_GET["GROUPBY"]))
{
    if ($_GET["GROUPBY"] == "SURA")
    {
        $GROUP_BY = "SURA";
    }
}

// GET CURRENT PAGE

if (isset($_GET["PAGE"]))
{
    $CURRENT_PAGE = $_GET["PAGE"];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET["PAGE"] = "";
}

    ?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("List All Nouns");
        ?>

		<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

	</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

        include "library/back_to_top_button.php";

    // sort order
    $sort = "C-DESC";

    $SORT_ORDER = "`c` DESC";

    if ($_GET["SORT"] != "")
    {
        $sort = $_GET["SORT"];
    }

    if ($sort == "A-ASC")
    {
        $SORT_ORDER = "`ARABIC`";
    }
    if ($sort == "A-DESC")
    {
        $SORT_ORDER = "`ARABIC` DESC";
    }

    if ($sort == "E-ASC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED`";
    }
    if ($sort == "E-DESC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED` DESC";
    }

    if ($sort == "C-ASC")
    {
        $SORT_ORDER = "c";
    }
    if ($sort == "C-DESC")
    {
        $SORT_ORDER = "c DESC";
    }

    if ($sort == "ALL-ASC")
    {
        $SORT_ORDER = "`100-COUNT-ALL`";
    }
    if ($sort == "ALL-DESC")
    {
        $SORT_ORDER = "`100-COUNT-ALL` DESC";
    }

    if ($sort == "MEC-ASC")
    {
        $SORT_ORDER = "`100-COUNT-MECCAN`";
    }

    if ($sort == "MEC-DESC")
    {
        $SORT_ORDER = "`100-COUNT-MECCAN` DESC";
    }

    if ($sort == "MED-ASC")
    {
        $SORT_ORDER = "`100-COUNT-MEDINAN`";
    }

    if ($sort == "MED-DESC")
    {
        $SORT_ORDER = "`100-COUNT-MEDINAN` DESC";
    }

    if ($sort == "CASENOM-ASC")
    {
        $SORT_ORDER = "case_nom ASC";
    }

    if ($sort == "CASENOM-DESC")
    {
        $SORT_ORDER = "case_nom DESC";
    }

    if ($sort == "CASEACC-ASC")
    {
        $SORT_ORDER = "case_acc ASC";
    }

    if ($sort == "CASEACC-DESC")
    {
        $SORT_ORDER = "case_acc DESC";
    }

    if ($sort == "CASEGEN-ASC")
    {
        $SORT_ORDER = "case_gen ASC";
    }
    if ($sort == "CASEGEN-DESC")
    {
        $SORT_ORDER = "case_gen DESC";
    }

    if ($sort == "GENDERMASC-ASC")
    {
        $SORT_ORDER = "gender_masc ASC";
    }

    if ($sort == "GENDERMASC-DESC")
    {
        $SORT_ORDER = "gender_masc DESC";
    }

    if ($sort == "GENDERFEM-ASC")
    {
        $SORT_ORDER = "gender_fem ASC";
    }

    if ($sort == "GENDERFEM-DESC")
    {
        $SORT_ORDER = "gender_fem DESC";
    }

    if ($sort == "NUMSING-ASC")
    {
        $SORT_ORDER = "number_singular ASC";
    }

    if ($sort == "NUMSING-DESC")
    {
        $SORT_ORDER = "number_singular DESC";
    }

    if ($sort == "NUMDUAL-ASC")
    {
        $SORT_ORDER = "number_dual ASC";
    }

    if ($sort == "NUMDUAL-DESC")
    {
        $SORT_ORDER = "number_dual DESC";
    }

    if ($sort == "NUMPLURAL-ASC")
    {
        $SORT_ORDER = "number_plural ASC";
    }

    if ($sort == "NUMPLURAL-DESC")
    {
        $SORT_ORDER = "number_plural DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>List All Nouns</h2>";

    echo "<div class='button-block-with-spacing'><a href='count_nouns.php?GROUPBY=ROOTS'><button>";

    if ($GROUP_BY == "ROOT")
    {
        echo "<b>";
    }
    echo "Group Data by Root";
    if ($GROUP_BY == "ROOT")
    {
        echo "</b>";
    }

    echo "</button></a><a href='count_nouns.php?GROUPBY=SURA'><button>";

    if ($GROUP_BY == "SURA")
    {
        echo "<b>";
    }
    echo "Group Data by Sura";
    if ($GROUP_BY == "SURA")
    {
        echo "</b>";
    }

    echo "</button></a></div>";

    if ($GROUP_BY == "ROOT")
    {
        if (!$PRE_RENDER)
        {
            $result = db_query("SELECT DISTINCT(`QTL-ROOT-BINARY`) qrb, `QTL-ROOT`, `ARABIC`, `QTL-ROOT-TRANSLITERATED`, COUNT(`QTL-ROOT`) c,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-CASE`='ACCUSATIVE' AND `QTL-ROOT-BINARY`=qrb) case_acc,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-CASE`='GENITIVE' AND `QTL-ROOT-BINARY`=qrb) case_gen,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-CASE`='NOMINATIVE' AND `QTL-ROOT-BINARY`=qrb) case_nom,
	
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-GENDER`='MASCULINE' AND `QTL-ROOT-BINARY`=qrb) gender_masc,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-GENDER`='FEMININE' AND `QTL-ROOT-BINARY`=qrb) gender_fem,
	
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-NUMBER`='SINGULAR' AND `QTL-ROOT-BINARY`=qrb) number_singular,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-NUMBER`='DUAL' AND `QTL-ROOT-BINARY`=qrb) number_dual,
	(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='NOUN' AND `QTL-NUMBER`='PLURAL' AND `QTL-ROOT-BINARY`=qrb) number_plural
	
	
	FROM `QURAN-DATA` 
	
	LEFT JOIN `ROOT-LIST` ON `QTL-ROOT-BINARY`=`ENGLISH-BINARY`
	
	
	WHERE `TAG EXPLAINED`='NOUN' AND `QTL-ROOT`!=''
	GROUP BY `QTL-ROOT-BINARY` ORDER BY $SORT_ORDER");
        }
        else
        {
            $result = db_query("SELECT DISTINCT(`QTL-ROOT-BINARY`) qrb, `QTL-ROOT`, `ARABIC`, `QTL-ROOT-TRANSLITERATED`, `RENDER_COUNT_NOUN` c, `RENDER_NOUN_CASE_NOM` case_nom, `RENDER_NOUN_CASE_ACC` case_acc, `RENDER_NOUN_CASE_GEN` case_gen, `RENDER_NOUN_GENDER_MASC` gender_masc, `RENDER_NOUN_GENDER_FEM` gender_fem, `RENDER_NOUN_NUMBER_SINGULAR` number_singular, `RENDER_NOUN_NUMBER_DUAL` number_dual, `RENDER_NOUN_NUMBER_PLURAL` number_plural
	
	FROM `QURAN-DATA` 
	
	LEFT JOIN `ROOT-LIST` ON `QTL-ROOT-BINARY`=`ENGLISH-BINARY`
	
	
	WHERE `TAG EXPLAINED`='NOUN' AND `QTL-ROOT`!=''
	GROUP BY `QTL-ROOT-BINARY` ORDER BY $SORT_ORDER");
        }

        $grand_total = db_rowcount($result);

        // table container div and fixed cols solves wide table persistent header issues
        echo "<div id=tableContainer class='tableContainer'>";

        echo "<table class='hoverTable persist-area fixedTable'>";

        // table header

        echo "<thead class='persist-header table-header-row'>";

        echo "<tr class='table-header-row'><th align=center colspan=2><b>Root Form</b></th><th align=center rowspan=2><b>Appearances as a Noun</b><br><a href='count_nouns.php?SORT=C-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=C-DESC'><img src='../images/down.gif'></a></th></th>
		<th colspan=3 align=center><b>Case</b></th>
		<th colspan=2 align=center><b>Gender</b></th>
		<th colspan=3 align=center><b>Number</b></th>
		<th rowspan=2 width=50>&nbsp;</th></tr>";

        echo "<tr><th><b>Arabic</b><br><a href='count_nouns.php?SORT=A-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=A-DESC'><img src='../images/down.gif'></a></th><th bgcolor=#c0c0c0 align=center><b>Transliterated</b><br><a href='count_nouns.php?SORT=E-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=E-DESC'><img src='../images/down.gif'></a></th>
		
		<th align=center><b>Nominative</b>
		<br><a href='count_nouns.php?SORT=CASENOM-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=CASENOM-DESC'><img src='../images/down.gif'></a>
		</th>
		<th align=center><b>Accusative</b>
		<br><a href='count_nouns.php?SORT=CASEACC-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=CASEACC-DESC'><img src='../images/down.gif'></a>
		</th>
		<th align=center><b>Genitive</b>
		<br><a href='count_nouns.php?SORT=CASEGEN-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=CASEGEN-DESC'><img src='../images/down.gif'></a>
		</th>
		
		<th align=center><b>Masculine</b>
		<br><a href='count_nouns.php?SORT=GENDERMASC-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=GENDERMASC-DESC'><img src='../images/down.gif'></a>
		</th>
		
		<th align=center><b>Feminine</b>
		<br><a href='count_nouns.php?SORT=GENDERFEM-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=GENDERFEM-DESC'><img src='../images/down.gif'></a>
		</th>
		
		<th align=center><b>Singular</b>
		<br><a href='count_nouns.php?SORT=NUMSING-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=NUMSING-DESC'><img src='../images/down.gif'></a>
		</th>
		
		<th align=center><b>Dual</b>
		<br><a href='count_nouns.php?SORT=NUMDUAL-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=NUMDUAL-DESC'><img src='../images/down.gif'></a>
		</td>
		
		<th align=center><b>Plural</b>
		<br><a href='count_nouns.php?SORT=NUMPLURAL-ASC'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=NUMPLURAL-DESC'><img src='../images/down.gif'></a>
		</th>
	
		</tr>";

        echo "</thead>";

        echo "<tbody>";

        // table data

        $START = $ITEMS_PER_PAGE * ($CURRENT_PAGE - 1);
        $END   = $START + $ITEMS_PER_PAGE;
        if ($END > db_rowcount($result))
        {
            $END = db_rowcount($result);
        }

        if ($START > 0)
        {
            $result->data_seek($START);
        }

        for ($i = $START; $i < $END; $i++)
        {
            // grab next database row
            $ROW = $result->fetch_assoc();

            $AHREF = "<a href='../verse_browser.php?S=ROOT:" . $ROW["ARABIC"] . "@[NOUN]' class=linky none'>";

            echo "<tr>";
            echo "<td align=center>$AHREF" . $ROW["ARABIC"] . "</a></td>";

            echo "<td align=center $user_preference_transliteration_style>$AHREF" . convert_buckwalter($ROW["QTL-ROOT"]) . "</a></td>";

            // count occurrences as a noun

            echo "<td align=center>$AHREF" . number_format($ROW["c"]) . "</a></td>";

            if (!$PRE_RENDER)
            {
                db_query("UPDATE `ROOT-LIST` SET `RENDER_COUNT_NOUN`=" . $ROW["c"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
            }

            // case

            echo "<td align=center>" . str_replace("[NOUN", "[NOUN NOMINATIVE", $AHREF) . $ROW["case_nom"] . "</a></td>";
            echo "<td align=center>" . str_replace("[NOUN", "[NOUN ACCUSTIVE", $AHREF) . $ROW["case_acc"] . "</a></td>";
            echo "<td align=center>" . str_replace("[NOUN", "[NOUN GENITIVE", $AHREF) . $ROW["case_gen"] . "</a></td>";

            if (!$PRE_RENDER)
            {
                db_query("UPDATE `ROOT-LIST` SET `RENDER_NOUN_CASE_NOM`=" . $ROW["case_nom"] . ", `RENDER_NOUN_CASE_ACC`=" . $ROW["case_acc"] . ", `RENDER_NOUN_CASE_GEN`=" . $ROW["case_gen"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
            }

            // gender

            echo "<td align=center>" . str_replace("[NOUN", "[NOUN MASCULINE", $AHREF) . $ROW["gender_masc"] . "</a></td>";
            echo "<td align=center>" . str_replace("[NOUN", "[NOUN FEMININE", $AHREF) . $ROW["gender_fem"] . "</a></td>";

            if (!$PRE_RENDER)
            {
                db_query("UPDATE `ROOT-LIST` SET `RENDER_NOUN_GENDER_MASC`=" . $ROW["gender_masc"] . ", `RENDER_NOUN_GENDER_FEM`=" . $ROW["gender_fem"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
            }

            // case

            echo "<td align=center>" . str_replace("[NOUN", "[NOUN SINGULAR", $AHREF) . $ROW["number_singular"] . "</a></td>";
            echo "<td align=center>" . str_replace("[NOUN", "[NOUN DUAL", $AHREF) . $ROW["number_dual"] . "</a></td>";
            echo "<td align=center>" . str_replace("[NOUN", "[NOUN PLURAL", $AHREF) . $ROW["number_plural"] . "</a></td>";

            if (!$PRE_RENDER)
            {
                db_query("UPDATE `ROOT-LIST` SET `RENDER_NOUN_NUMBER_SINGULAR`=" . $ROW["number_singular"] . ", `RENDER_NOUN_NUMBER_DUAL`=" . $ROW["number_dual"] . ", `RENDER_NOUN_NUMBER_PLURAL`=" . $ROW["number_plural"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
            }

            echo "<td width=50 align=center>";
            echo "<a title='Examine root' href='../examine_root.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "'><img src='../images/info.gif'></a>&nbsp;";

            if (!isMobile())
            {
                echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_nouns.php?VIEW=MINI&ROOT=" . urlencode($ROW["QTL-ROOT-TRANSLITERATED"]) . "', type: 'post'}\">";
            }

            echo "<a href='../charts/chart_nouns.php?ROOT=" . urlencode($ROW["QTL-ROOT-TRANSLITERATED"]) . "'><img src='../images/stats.gif'></a></td>";

            if (!isMobile())
            {
                echo "</span>";
            }

            echo "</tr>";
        }

        if ($i >= (db_rowcount($result) - 1))
        {
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td align=center><b>" . number_format($grand_total) . "</b>";
            echo str_repeat("<td>&nbsp;</td>", 9);
            echo "<tr>";
        }

        echo "</table><br>";

        // insert the page navigator

        $ITEMS_TO_SHOW = db_rowcount($result);
        $pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

        if ($pages_needed > 1)
        {
            if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
            {
                $pages_needed++;
            }

            print_page_navigator($CURRENT_PAGE, $pages_needed, false, "count_nouns.php?SORT=" . $_GET["SORT"] . "$PRE_PASS");
        }
    }
else
{
    // group by sura instead

    // sort order

    $SORT_ORDER = "`Sura Number` ASC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($sort == "SURA-ASC")
    {
        $SORT_ORDER = "`Sura Number`";
    }

    if ($sort == "SURA-DESC")
    {
        $SORT_ORDER = "`Sura Number` DESC";
    }

    if ($sort == "SURA-NOUN-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN`";
    }
    if ($sort == "SURA-NOUN-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN` DESC";
    }

    if ($sort == "SURA-NOUN-NOM-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_NOM`";
    }
    if ($sort == "SURA-NOUN-NOM-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_NOM` DESC";
    }

    if ($sort == "SURA-NOUN-ACC-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_ACC`";
    }
    if ($sort == "SURA-NOUN-ACC-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_ACC` DESC";
    }

    if ($sort == "SURA-NOUN-GEN-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_GEN`";
    }
    if ($sort == "SURA-NOUN-GEN-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_GEN` DESC";
    }

    if ($sort == "SURA-NOUN-SINGULAR-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_SINGULAR`";
    }
    if ($sort == "SURA-NOUN-SINGULAR-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_SINGULAR` DESC";
    }

    if ($sort == "SURA-NOUN-DUAL-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_DUAL`";
    }
    if ($sort == "SURA-NOUN-DUAL-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_DUAL` DESC";
    }

    if ($sort == "SURA-NOUN-PLURAL-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_PLURAL`";
    }
    if ($sort == "SURA-NOUN-PLURAL-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_PLURAL` DESC";
    }

    if ($sort == "SURA-NOUN-MASC-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_MASC`";
    }
    if ($sort == "SURA-NOUN-MASC-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_MASC` DESC";
    }

    if ($sort == "SURA-NOUN-FEM-ASC")
    {
        $SORT_ORDER = "`COUNT_NOUN_FEM`";
    }
    if ($sort == "SURA-NOUN-FEM-DESC")
    {
        $SORT_ORDER = "`COUNT_NOUN_FEM` DESC";
    }

    $result = db_query("SELECT * FROM `SURA-DATA` ORDER BY $SORT_ORDER");

    echo "<table class='hoverTable'>";

    echo "<tr>";

    echo "<th rowspan=2>Sura<br><a href='count_nouns.php?SORT=SURA-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th rowspan=2>Total Nouns<br><a href='count_nouns.php?SORT=SURA-NOUN-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th colspan=3>Case</th>";

    echo "<th colspan=2>Gender</th>";

    echo "<th colspan=3>Number</th>";

    echo "</tr>";

    echo "<tr>";

    echo "<th>Nominative<br><a href='count_nouns.php?SORT=SURA-NOUN-NOM-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-NOM-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Accusative<br><a href='count_nouns.php?SORT=SURA-NOUN-ACC-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-ACC-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Genitive<br><a href='count_nouns.php?SORT=SURA-NOUN-GEN-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-GEN-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Masculine<br><a href='count_nouns.php?SORT=SURA-NOUN-MASC-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-MASC-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Feminine<br><a href='count_nouns.php?SORT=SURA-NOUN-FEM-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-FEM-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Singular<br><a href='count_nouns.php?SORT=SURA-NOUN-SINGULAR-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-SINGULAR-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Dual<br><a href='count_nouns.php?SORT=SURA-NOUN-DUAL-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-DUAL-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "<th>Plural<br><a href='count_nouns.php?SORT=SURA-NOUN-PLURAL-ASC&GROUPBY=$GROUP_BY'><img src='../images/up.gif'></a> <a href='count_nouns.php?SORT=SURA-NOUN-PLURAL-DESC&GROUPBY=$GROUP_BY'><img src='../images/down.gif'></a></th>";

    echo "</tr>";

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = $result->fetch_assoc();

        echo "<tr>";

        echo "<td align=center>" . $ROW["Sura Number"] . "</td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN NOMINATIVE] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_NOM"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN ACCUSATIVE] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_ACC"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN GENITIVE] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_GEN"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN MASCULINE] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_MASC"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN FEMININE] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_FEM"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN SINGULAR] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_SINGULAR"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN DUAL] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_DUAL"]) . "</a></td>";

        echo "<td align=center><a href='../verse_browser.php?S=[NOUN PLURAL] RANGE:" . $ROW["Sura Number"] . "' class=linky>" . number_format($ROW["COUNT_NOUN_PLURAL"]) . "</a></td>";

        echo "</tr>";
    }

    echo "<tr>";

    echo "<td>&nbsp;</td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun nominative]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_NOM`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun accusative]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_ACC`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun genitive]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_GEN`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun masculine]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_MASC`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun feminine]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_FEM`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun singular]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_SINGULAR`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun dual]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_DUAL`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "<td align=center><b><a href='../verse_browser.php?S=[noun plural]' class=linky>" . number_format(db_return_one_record_one_field("SELECT SUM(`COUNT_NOUN_PLURAL`) FROM `SURA-DATA`")) . "</b></a></td>";

    echo "</tr>";

    echo "</tbody>";

    echo "</table>";

    echo "</div>";
}

echo "</div>";

    // print footer

    include "../library/footer.php";

?>
</body>

<script type="text/javascript">
  $(function() {
    Tipped.create('.chart-tip', {position: 'left', showDelay: 750, skin: 'light', close: true});
  });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>

</html>