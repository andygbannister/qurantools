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
$ITEMS_PER_PAGE = 430;
$CURRENT_PAGE   = 1;

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
            window_title("List All Verbs");
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
    $sort       = "C-DESC";
    $SORT_ORDER = "`c` DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
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

    if ($sort == "1P-ASC")
    {
        $SORT_ORDER = "person_1st ASC";
    }
    if ($sort == "1P-DESC")
    {
        $SORT_ORDER = "person_1st DESC";
    }

    if ($sort == "2P-ASC")
    {
        $SORT_ORDER = "person_2nd ASC";
    }
    if ($sort == "2P-DESC")
    {
        $SORT_ORDER = "person_2nd DESC";
    }

    if ($sort == "3P-ASC")
    {
        $SORT_ORDER = "person_3rd ASC";
    }
    if ($sort == "3P-DESC")
    {
        $SORT_ORDER = "person_3rd DESC";
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

    if ($sort == "FORM-I-ASC")
    {
        $SORT_ORDER = "form_i ASC";
    }
    if ($sort == "FORM-I-DESC")
    {
        $SORT_ORDER = "form_i DESC";
    }

    if ($sort == "FORM-II-ASC")
    {
        $SORT_ORDER = "form_ii ASC";
    }
    if ($sort == "FORM-II-DESC")
    {
        $SORT_ORDER = "form_ii DESC";
    }

    if ($sort == "FORM-III-ASC")
    {
        $SORT_ORDER = "form_iii ASC";
    }
    if ($sort == "FORM-III-DESC")
    {
        $SORT_ORDER = "form_iii DESC";
    }

    if ($sort == "FORM-IV-ASC")
    {
        $SORT_ORDER = "form_iv ASC";
    }
    if ($sort == "FORM-IV-DESC")
    {
        $SORT_ORDER = "form_iv DESC";
    }

    if ($sort == "FORM-V-ASC")
    {
        $SORT_ORDER = "form_v ASC";
    }
    if ($sort == "FORM-V-DESC")
    {
        $SORT_ORDER = "form_v DESC";
    }

    if ($sort == "FORM-VI-ASC")
    {
        $SORT_ORDER = "form_vi ASC";
    }
    if ($sort == "FORM-VI-DESC")
    {
        $SORT_ORDER = "form_vi DESC";
    }

    if ($sort == "FORM-VII-ASC")
    {
        $SORT_ORDER = "form_vii ASC";
    }
    if ($sort == "FORM-VII-DESC")
    {
        $SORT_ORDER = "form_vii DESC";
    }

    if ($sort == "FORM-VIII-ASC")
    {
        $SORT_ORDER = "form_viii ASC";
    }
    if ($sort == "FORM-VIII-DESC")
    {
        $SORT_ORDER = "form_viii DESC";
    }

    if ($sort == "FORM-IX-ASC")
    {
        $SORT_ORDER = "form_ix ASC";
    }
    if ($sort == "FORM-IX-DESC")
    {
        $SORT_ORDER = "form_ix DESC";
    }

    if ($sort == "FORM-X-ASC")
    {
        $SORT_ORDER = "form_x ASC";
    }
    if ($sort == "FORM-X-DESC")
    {
        $SORT_ORDER = "form_x DESC";
    }

    if ($sort == "FORM-XI-ASC")
    {
        $SORT_ORDER = "form_xi ASC";
    }
    if ($sort == "FORM-XI-DESC")
    {
        $SORT_ORDER = "form_xi DESC";
    }

    if ($sort == "FORM-XII-ASC")
    {
        $SORT_ORDER = "form_xii ASC";
    }
    if ($sort == "FORM-XII-DESC")
    {
        $SORT_ORDER = "form_xii DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>List All Verbs</h2>";

if (!$PRE_RENDER)
{
    $result = db_query("SELECT DISTINCT(`QTL-ROOT-BINARY`) qrb, `QTL-ROOT`, `ARABIC`, `QTL-ROOT-TRANSLITERATED`, COUNT(`QTL-ROOT`) c,


(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-PERSON`='1' AND `QTL-ROOT-BINARY`=qrb) person_1st,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-PERSON`='2' AND `QTL-ROOT-BINARY`=qrb) person_2nd,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-PERSON`='3' AND `QTL-ROOT-BINARY`=qrb) person_3rd,

(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='I' AND `QTL-ROOT-BINARY`=qrb) form_i,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='II' AND `QTL-ROOT-BINARY`=qrb) form_ii,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='III' AND `QTL-ROOT-BINARY`=qrb) form_iii,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='IV' AND `QTL-ROOT-BINARY`=qrb) form_iv,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='V' AND `QTL-ROOT-BINARY`=qrb) form_v,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='VI' AND `QTL-ROOT-BINARY`=qrb) form_vi,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='VII' AND `QTL-ROOT-BINARY`=qrb) form_vii,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='VIII' AND `QTL-ROOT-BINARY`=qrb) form_viii,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='IX' AND `QTL-ROOT-BINARY`=qrb) form_ix,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='X' AND `QTL-ROOT-BINARY`=qrb) form_x,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='XI' AND `QTL-ROOT-BINARY`=qrb) form_xi,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-ARABIC-FORM`='XII' AND `QTL-ROOT-BINARY`=qrb) form_xii,

(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-GENDER`='MASCULINE' AND `QTL-ROOT-BINARY`=qrb) gender_masc,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-GENDER`='FEMININE' AND `QTL-ROOT-BINARY`=qrb) gender_fem,

(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-NUMBER`='SINGULAR' AND `QTL-ROOT-BINARY`=qrb) number_singular,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-NUMBER`='DUAL' AND `QTL-ROOT-BINARY`=qrb) number_dual,
(SELECT COUNT(*) FROM `QURAN-DATA` WHERE `TAG EXPLAINED`='VERB' AND `QTL-NUMBER`='PLURAL' AND `QTL-ROOT-BINARY`=qrb) number_plural

FROM `QURAN-DATA` 

LEFT JOIN `ROOT-LIST` ON `QTL-ROOT-BINARY`=`ENGLISH-BINARY`

WHERE `TAG EXPLAINED`='VERB' AND `QTL-ROOT`!=''
GROUP BY `QTL-ROOT-BINARY` ORDER BY $SORT_ORDER");
}
else
{
    $result = db_query("SELECT DISTINCT(`QTL-ROOT-BINARY`) qrb, `QTL-ROOT`, `ARABIC`, `QTL-ROOT-TRANSLITERATED`, `RENDER_COUNT_VERB` c, `RENDER_VERB_PERSON_1ST` person_1st, `RENDER_VERB_PERSON_2ND` person_2nd, `RENDER_VERB_PERSON_3RD` person_3rd, `RENDER_VERB_NUMBER_SINGULAR` number_singular, `RENDER_VERB_NUMBER_DUAL` number_dual, `RENDER_VERB_NUMBER_PLURAL` number_plural, `RENDER_VERB_GENDER_MASC` gender_masc, `RENDER_VERB_GENDER_FEM` gender_fem, `RENDER_VERB_FORM_1` form_i, `RENDER_VERB_FORM_2` form_ii, `RENDER_VERB_FORM_3` form_iii, `RENDER_VERB_FORM_4` form_iv, `RENDER_VERB_FORM_5` form_v, `RENDER_VERB_FORM_6` form_vi, `RENDER_VERB_FORM_7` form_vii, `RENDER_VERB_FORM_8` form_viii, `RENDER_VERB_FORM_9` form_ix, `RENDER_VERB_FORM_10`form_x, `RENDER_VERB_FORM_11` form_xi, `RENDER_VERB_FORM_12` form_xii
	FROM `QURAN-DATA` 

LEFT JOIN `ROOT-LIST` ON `QTL-ROOT-BINARY`=`ENGLISH-BINARY`

WHERE `TAG EXPLAINED`='VERB' AND `QTL-ROOT`!=''
GROUP BY `QTL-ROOT-BINARY` ORDER BY $SORT_ORDER");
}

    $grand_total = db_rowcount($result);

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area tightTable fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'><th align=center colspan=2><b>Root Form</b></th><th align=center rowspan=2 width=85><b>Appears<br>as a Verb</b><br><a href='count_verbs.php?SORT=C-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=C-DESC'><img src='../images/down.gif'></a></th>

	<th colspan=3 align=center><b>Person</b></th>

	<th colspan=3 align=center><b>Number</b></th>

	<th colspan=2 align=center><b>Gender</b></th>
	
	<th colspan=12 align=center><b>Form</b></th>

	<th rowspan=2 bgcolor=#c0c0c0 width=50>&nbsp;</th></tr>";

    echo "<tr><th bgcolor=#c0c0c0><b>Arabic</b><br><a href='count_verbs.php?SORT=A-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=A-DESC'><img src='../images/down.gif'></a></th><th><b>Translit</b><br><a href='count_verbs.php?SORT=E-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=E-DESC'><img src='../images/down.gif'></a></th>
		
	<th align=center width=20><b>1st</b>
	<br><a href='count_verbs.php?SORT=1P-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=1P-DESC'><img src='../images/down.gif'></a>
	</th>
	<th align=center width=20><b>2nd</b>
	<br><a href='count_verbs.php?SORT=2P-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=2P-DESC'><img src='../images/down.gif'></a>
	</th>
	<th align=center width=60><b>&nbsp;3rd&nbsp;</b>
	<br><a href='count_verbs.php?SORT=3P-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=3P-DESC'><img src='../images/down.gif'></a>
	</th>	
	
	<th align=center width=50><b>Sing</b>
	<br><a href='count_verbs.php?SORT=NUMSING-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=NUMSING-DESC'><img src='../images/down.gif'></a>
	</th>
	<th align=center width=50><b>Dual</b>
	<br><a href='count_verbs.php?SORT=NUMDUAL-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=NUMDUAL-DESC'><img src='../images/down.gif'></a>
	</th>
	<th align=center min-width=65><b>Plural</b>
	<br><a href='count_verbs.php?SORT=NUMPLURAL-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=NUMPLURAL-DESC'><img src='../images/down.gif'></a>
	</th>
	
	<th align=center width=45><b>Masc</b>
	<br><a href='count_verbs.php?SORT=GENDERMASC-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=GENDERMASC-DESC'><img src='../images/down.gif'></a>
	</th>
	<th align=center width=50><b>Fem</b>
	<br><a href='count_verbs.php?SORT=GENDERFEM-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=GENDERFEM-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>I</b>
	<br><a href='count_verbs.php?SORT=FORM-I-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-I-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>II</b>
	<br><a href='count_verbs.php?SORT=FORM-II-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-II-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>III</b>
	<br><a href='count_verbs.php?SORT=FORM-III-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-III-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>IV</b>
	<br><a href='count_verbs.php?SORT=FORM-IV-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-IV-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>V</b>
	<br><a href='count_verbs.php?SORT=FORM-V-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-V-DESC'><img src='../images/down.gif'></a>
	</th>";

echo "<th align=center width=30><b>VI</b>
	<br><a href='count_verbs.php?SORT=FORM-VI-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-VI-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>VII</b>
	<br><a href='count_verbs.php?SORT=FORM-VII-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-VII-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>VIII</b>
	<br><a href='count_verbs.php?SORT=FORM-VIII-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-VIII-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>IX</b>
	<br><a href='count_verbs.php?SORT=FORM-IX-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-IX-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>X</b>
	<br><a href='count_verbs.php?SORT=FORM-X-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-X-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>XI</b>
	<br><a href='count_verbs.php?SORT=FORM-XI-ASC'><img src='../images/up.gif'></a>&nbsp;<a href='count_verbs.php?SORT=FORM-XI-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=30><b>XII</b>
	<br><a href='count_verbs.php?SORT=FORM-XII-ASC'><img src='../images/up.gif'></a> <a href='count_verbs.php?SORT=FORM-XII-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "</tr>";

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

        $AHREF = "<a href='../verse_browser.php?S=ROOT:" . $ROW["ARABIC"] . "@[VERB]' class=linky none'>";

        echo "<tr>";
        echo "<td align=center>$AHREF" . $ROW["ARABIC"] . "</a></td>";

        echo "<td align=center $user_preference_transliteration_style>$AHREF" . convert_buckwalter($ROW["QTL-ROOT"]) . "</a></td>";

        echo "<td align=center width=95>$AHREF" . number_format($ROW["c"]) . "</a></td>";

        if (!$PRE_RENDER)
        {
            db_query("UPDATE `ROOT-LIST` SET `RENDER_COUNT_VERB`=" . $ROW["c"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
        }

        // person

        echo "<td align=center width=28>" . str_replace("[VERB", "[VERB 1P", $AHREF) . number_format($ROW["person_1st"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB 2P", $AHREF) . number_format($ROW["person_2nd"]) . "</a></td>";
        echo "<td align=center width=65>" . str_replace("[VERB", "[VERB 3P", $AHREF) . number_format($ROW["person_3rd"]) . "</a></td>";

        if (!$PRE_RENDER)
        {
            db_query("UPDATE `ROOT-LIST` SET `RENDER_VERB_PERSON_1ST`=" . $ROW["person_1st"] . ", `RENDER_VERB_PERSON_2ND`=" . $ROW["person_2nd"] . ", `RENDER_VERB_PERSON_3RD`=" . $ROW["person_3rd"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
        }

        // number

        echo "<td align=center width=50>" . str_replace("[VERB", "[VERB SINGULAR", $AHREF) . number_format($ROW["number_singular"]) . "</a></td>";
        echo "<td align=center width=50>" . str_replace("[VERB", "[VERB DUAL", $AHREF) . $ROW["number_dual"] . "</a></td>";
        echo "<td align=center width=60>" . str_replace("[VERB", "[VERB PLURAL", $AHREF) . $ROW["number_plural"] . "</a></td>";

        if (!$PRE_RENDER)
        {
            db_query("UPDATE `ROOT-LIST` SET `RENDER_VERB_NUMBER_SINGULAR`=" . $ROW["number_singular"] . ", `RENDER_VERB_NUMBER_DUAL`=" . $ROW["number_dual"] . ", `RENDER_VERB_NUMBER_PLURAL`=" . $ROW["number_plural"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
        }

        // gender

        echo "<td align=center width=45>" . str_replace("[VERB", "[VERB MASCULINE", $AHREF) . number_format($ROW["gender_masc"]) . "</a></td>";
        echo "<td align=center width=50>" . str_replace("[VERB", "[VERB FEMININE", $AHREF) . $ROW["gender_fem"] . "</a></td>";

        if (!$PRE_RENDER)
        {
            db_query("UPDATE `ROOT-LIST` SET `RENDER_VERB_GENDER_MASC`=" . $ROW["gender_masc"] . ", `RENDER_VERB_GENDER_FEM`=" . $ROW["gender_fem"] . " WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
        }

        // form

        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:I", $AHREF) . number_format($ROW["form_i"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:II", $AHREF) . number_format($ROW["form_ii"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:III", $AHREF) . number_format($ROW["form_iii"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:IV", $AHREF) . number_format($ROW["form_iv"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:V", $AHREF) . number_format($ROW["form_v"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:VI", $AHREF) . number_format($ROW["form_vi"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:VII", $AHREF) . number_format($ROW["form_vii"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:VIII", $AHREF) . number_format($ROW["form_viii"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:IX", $AHREF) . number_format($ROW["form_ix"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:X", $AHREF) . number_format($ROW["form_x"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:XI", $AHREF) . number_format($ROW["form_xi"]) . "</a></td>";
        echo "<td align=center width=20>" . str_replace("[VERB", "[VERB FORM:XII", $AHREF) . number_format($ROW["form_xii"]) . "</a></td>";

        if (!$PRE_RENDER)
        {
            db_query("UPDATE `ROOT-LIST` SET 
			`RENDER_VERB_FORM_1`=" . $ROW["form_i"] . ", 
			`RENDER_VERB_FORM_2`=" . $ROW["form_ii"] . ", 
			`RENDER_VERB_FORM_3`=" . $ROW["form_iii"] . ", 
			`RENDER_VERB_FORM_4`=" . $ROW["form_iv"] . ", 
			`RENDER_VERB_FORM_5`=" . $ROW["form_v"] . ", 
			`RENDER_VERB_FORM_6`=" . $ROW["form_vi"] . ", 
			`RENDER_VERB_FORM_7`=" . $ROW["form_vii"] . ", 
			`RENDER_VERB_FORM_8`=" . $ROW["form_viii"] . ", 
			`RENDER_VERB_FORM_9`=" . $ROW["form_ix"] . ", 
			`RENDER_VERB_FORM_10`=" . $ROW["form_x"] . ", 
			`RENDER_VERB_FORM_11`=" . $ROW["form_xi"] . ", 
			`RENDER_VERB_FORM_12`=" . $ROW["form_xii"] . " 
			 WHERE `ARABIC`='" . $ROW["ARABIC"] . "'");
        }

        echo "<td width=50 align=center>";
        echo "<a title='Examine root' href='../examine_root.php?ROOT=" . urlencode($ROW["QTL-ROOT"]) . "'><img src='../images/info.gif'></a>&nbsp;";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_verbs.php?VIEW=MINI&ROOT=" . urlencode($ROW["QTL-ROOT-TRANSLITERATED"]) . "', type: 'post'}\">";
        }

        echo "<a href='../charts/chart_verbs.php?ROOT=" . urlencode($ROW["QTL-ROOT-TRANSLITERATED"]) . "'><img src='../images/stats.gif'></a></td>";

        if (!isMobile())
        {
            echo "</span>";
        }

        echo "</td>";

        echo "</tr>";
    }

    if ($i >= (db_rowcount($result) - 1))
    {
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td align=center><b>" . number_format($grand_total) . "</b>";
        echo str_repeat("<td>&nbsp;</td>", 21);
        echo "<tr>";
    }

    echo "</tbody>";

    echo "</table>";

    echo "</div><br>";

// insert the page navigator

$ITEMS_TO_SHOW = db_rowcount($result);
$pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

if ($pages_needed > 1)
{
    if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
    {
        $pages_needed++;
    }

    print_page_navigator($CURRENT_PAGE, $pages_needed, false, "count_verbs.php?SORT=" . $_GET["SORT"] . "$PRE_PASS");
}

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