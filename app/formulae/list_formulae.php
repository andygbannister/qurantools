<?php

// Administrators can set EDIT_GLOSS in the URL to open up full gloss editing
// ... or set HIDE_FULL_GLOSS to only show formulae without full glosses set

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/arabic.php';
require_once 'library/verse_parse.php';
require_once 'library/transliterate.php';

// preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

if ($logged_in_user["Preference Formulaic Glosses"] == 1)
{
    $show_formulaic_glosses = true;
}
else
{
    $show_formulaic_glosses = false;
}

// convert POST to GET
if (isset($_POST["L"]))
{
    $_GET["L"]    = $_POST["L"];
    $_GET["TYPE"] = $_POST["TYPE"];
    $_GET["SURA"] = $_POST["SURA"];

    if (!isset($_POST["UNIQUE"]))
    {
        $_POST["UNIQUE"] = "";
    }

    $_GET["UNIQUE"] = $_POST["UNIQUE"];

    // if (!isset($_POST["PROV"])) {$_POST["PROV"] = "";}
    // $_GET["PROV"] = $_POST["PROV"];

    if ($_GET["SURA"] == 0)
    {
        $_GET["SURA"] = "";
    }
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 750;
$CURRENT_PAGE   = 1;

// UNIQUE TO SURA

$unique_to_sura = false;

if (isset($_GET["UNIQUE"]))
{
    if ($_GET["UNIQUE"] == 1)
    {
        $unique_to_sura = true;
    }
}

// LENGTH AND TYPE

$FORMULA_LENGTH = 3;

if (isset($_GET["L"]))
{
    $FORMULA_LENGTH = $_GET["L"];
    if ($FORMULA_LENGTH != "ANY")
    {
        if ($FORMULA_LENGTH < 3)
        {
            $FORMULA_LENGTH = 3;
        }
        if ($FORMULA_LENGTH > 5)
        {
            $FORMULA_LENGTH = 5;
        }
    }
}

$FORMULA_TYPE = "ROOT";

if (isset($_GET["TYPE"]))
{
    $FORMULA_TYPE = $_GET["TYPE"];
    if ($FORMULA_TYPE != "EVERYTHING" && $FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "ANY")
    {
        $FORMULA_TYPE = "ROOT";
    }
}

// FILTER BY ROOT
$extra_root_SQL = "";
$ROOT           = "";
$ARABIC_ROOT    = "";
if (isset($_GET["ROOT"]))
{
    if ($_GET["ROOT"] != "")
    {
        $ROOT = db_quote($_GET["ROOT"]);

        $ARABIC_ROOT = db_return_one_record_one_field("SELECT `ARABIC` FROM `ROOT-LIST` WHERE BINARY `ENGLISH`='" . db_quote($ROOT) . "'");

        if ($FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "EVERYTHING")
        {
            $extra_root_SQL = "(`FORMULA` LIKE '%+$ROOT+%' OR `FORMULA` LIKE '$ROOT+%' OR `FORMULA` LIKE '%+$ROOT') AND ";
        }
        else
        {
            $extra_root_SQL = "(`Element1`='$ROOT' OR `Element2`='$ROOT' OR `Element3`='$ROOT'";
            if ($FORMULA_LENGTH > 3 || $FORMULA_TYPE == "EVERYTHING" || $FORMULA_LENGTH == "ANY")
            {
                $extra_root_SQL .= " OR `Element4`='$ROOT'";
            }
            if ($FORMULA_LENGTH > 4 || $FORMULA_TYPE == "EVERYTHING" || $FORMULA_LENGTH == "ANY")
            {
                $extra_root_SQL .= " OR `Element5`='$ROOT'";
            }
            $extra_root_SQL .= ") AND ";
        }
    }
}
else
{
    $_GET["ROOT"] = "";
}

// limit by sura?
$SURA_SQL          = "";
$SURA_EFFECTED_SQL = "";
$extra_title_text  = "";

// make sure GET["S"] is not blank (for later passing URLs)
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] > 0 && $_GET["SURA"] < 115)
    {
        $SURA_EFFECTED_SQL = "`SURA`=" . $_GET["SURA"] . " AND";
        $SURA_SQL          = "AND `START SURA`=" . $_GET["SURA"];
        $extra_title_text  = "<a href='../verse_browser.php?V=" . $_GET["SURA"] . "&FORMULA=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky><br><font size=3>(In Sura " . $_GET["SURA"] . ")</font></a>";
    }
}
else
{
    $_GET["SURA"] = "";
}

// limit by sura AND verse?

if (isset($_GET["SURAVERSE"]))
{
    $parts = explode(":", $_GET["SURAVERSE"]);

    if ($parts[0] > 0 && $parts[0] < 115 && $parts[1] > 0 && $parts[1] < 290)
    {
        $SURA_SQL         = "AND `START SURA`=" . $parts[0] . " AND (`START VERSE`=" . $parts[1] . " OR `END VERSE`=" . $parts[1] . ")";
        $extra_title_text = "<a href='../verse_browser.php?V=" . $_GET["SURAVERSE"] . "&FORMULA=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE' class=linky>(In Q. " . $parts[0] . ":" . $parts[1] . ")</a>";
    }
}
else
{
    $_GET["SURAVERSE"] = "";
}

// limit by selection of verses
if (isset($_GET["V"]))
{
    $V = $_GET["V"];


    if ($V != "")
    {
        parse_verses($V, true, 0);

        // convert our usual SQL to deal with the field set up in the formula list table
        $fiddle_array = explode(" OR ", $RANGE_SQL);

        $SURA_SQL = "";
        foreach ($fiddle_array as $SQL)
        {
            if ($SURA_SQL != "")
            {
                $SURA_SQL .= " OR ";
            }
            $SQL2 = $SQL;

            $SQL = str_ireplace("`VERSE`<=", "`START VERSE`<=", $SQL);
            $SQL = str_ireplace("`VERSE`", "`START VERSE`", $SQL);

            $SQL2 = str_ireplace("`VERSE`<=", "`END VERSE`<=", $SQL2);
            $SQL2 = str_ireplace("`VERSE`", "`END VERSE`", $SQL2);

            $SURA_SQL .= str_ireplace("SURA", "START SURA", "$SQL OR $SQL2");
        }

        $SURA_SQL = "AND ($SURA_SQL)";

        if ($_GET["V"] == "SEARCH")
        {
            $extra_title_text = "<a href='../verse_browser.php?FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE&S=" . $_GET["S"] . "' class=linky>(in Verses Matching Search: '" . $_GET["S"] . "')</a>";
        }
        else
        {
            $extra_title_text = "<a href='../verse_browser.php?V=" . $_GET["V"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(in Q. $V)</a>";
        }
    }
}
else
{
    $_GET["V"] = "";
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
        ?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
    <script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

    <?php
      if ($_GET["ROOT"] == "")
      {
          window_title("Formulae List");
      }
      else
      {
          if ($FORMULA_TYPE != "LEMMA")
          {
              window_title("Listing Formulae Which Include the Root " . convert_buckwalter($_GET["ROOT"]) . " $extra_title_text");
          }
          else
          {
              window_title("Listing Formulae Based on the Root " . convert_buckwalter($_GET["ROOT"]) . " $extra_title_text");
          }
      }
      ?>
</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

    include "library/back_to_top_button.php";

    // sort order
    $sort       = "C-DESC";
    $SORT_ORDER = "`OCCURRENCES` DESC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }
    else
    {
        $_GET["SORT"] = "";
    }

    if ($sort == "F-ASC")
    {
        $SORT_ORDER = "`FORMULA TRANSLITERATED`";
    }
    if ($sort == "F-DESC")
    {
        $SORT_ORDER = "`FORMULA TRANSLITERATED` DESC";
    }

    if ($sort == "A-ASC")
    {
        $SORT_ORDER = "`FORMULA ARABIC`";
    }
    if ($sort == "A-DESC")
    {
        $SORT_ORDER = "`FORMULA ARABIC` DESC";
    }

    if ($sort == "C-ASC")
    {
        $SORT_ORDER = "`OCCURRENCES`";
    }
    if ($sort == "C-DESC")
    {
        $SORT_ORDER = "`OCCURRENCES` DESC";
    }

    if ($sort == "CMEC-ASC")
    {
        $SORT_ORDER = "`OCCURRENCES MECCAN`";
    }
    if ($sort == "CMEC-DESC")
    {
        $SORT_ORDER = "`OCCURRENCES MECCAN` DESC";
    }

    if ($sort == "CMED-ASC")
    {
        $SORT_ORDER = "`OCCURRENCES MEDINAN`";
    }
    if ($sort == "CMED-DESC")
    {
        $SORT_ORDER = "`OCCURRENCES MEDINAN` DESC";
    }

    if ($sort == "OCCSURA-ASC")
    {
        $SORT_ORDER = "localOccurrences";
    }
    if ($sort == "OCCSURA-DESC")
    {
        $SORT_ORDER = "localOccurrences DESC";
    }

    if ($sort == "LENGTH-ASC")
    {
        $SORT_ORDER = "`LENGTH`";
    }
    if ($sort == "LENGTH-DESC")
    {
        $SORT_ORDER = "`LENGTH` DESC";
    }

    if ($sort == "TYPE-ASC")
    {
        $SORT_ORDER = "`TYPE`";
    }
    if ($sort == "TYPE-DESC")
    {
        $SORT_ORDER = "`TYPE` DESC";
    }

    // menubar

    include "library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>";
    if ($_GET["ROOT"] != "")
    {
        if ($FORMULA_TYPE != "LEMMA")
        {
            if ($FORMULA_TYPE == "EVERYTHING")
            {
                echo "Listing All Formulae Which Include (or are Based on) Root <i>" . convert_buckwalter($_GET["ROOT"]) . "</i>";

                echo "<a href='../charts/chart_formula_distribution.php?T=EVERYTHING&ROOT=" . urlencode($_GET["ROOT"]) . "'>";
                echo "<img src='../images/stats.gif'>";
                echo "</a>";

                echo $extra_title_text;
            }
            else
            {
                echo "Listing Formulae Which Include the Root '" . convert_buckwalter($_GET["ROOT"]) . "' $extra_title_text";
            }
        }
        else
        {
            echo "Listing Formulae Based on the Root '" . convert_buckwalter($_GET["ROOT"]) . "' $extra_title_text";
        }
    }
    else
    {
        echo "Listing All Formulae";
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'../charts/chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&VIEW=MINI', type: 'post'}\">";
        echo "<a href='../charts/chart_formulae_used_per_sura.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE'><img src='../images/stats.gif'></a></span>$extra_title_text";
    }
    echo "</h2>";

if ($FORMULA_TYPE != "EVERYTHING")
{
    // ==== formula length and type selection form =====

    echo "<form action='list_formulae.php?S=" . $_GET["S"] . "&SURA=" . $_GET["SURA"] . "&V=" . $_GET["V"] . "&ROOT=" . $_GET["ROOT"] . "' method=POST>";

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

    echo " &nbsp;&nbsp;<input type=radio name=L value=ANY onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == "ANY")
    {
        echo "checked=checked";
    }
    echo "> Any</input>";

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

    echo "<tr>";
    echo "<td>Limit to Sura</td>";
    echo "<td>";
    echo "<select name=SURA onChange='this.form.submit();'>";

    echo "<option value=0>Show All Suras</option>";

    for ($i = 1; $i <= 114; $i++)
    {
        echo "<option value=$i";
        if ($i == $_GET["SURA"])
        {
            echo " selected";
        }
        echo ">$i</option>";
    }

    echo "</select>";

    echo "</td>";

    echo "</tr>";

    if ($_GET["SURA"] > 0)
    {
        echo "<tr><td>&nbsp;</td>";
        echo "<td>";
        echo "<input type=checkbox name=UNIQUE value=1";
        if ($unique_to_sura)
        {
            echo " checked";
        }
        echo " onChange='this.form.submit();'>";
        echo " Only Show Formulae Unique to This Sura";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table></div>";

    echo "</form>";
}

// if viewing a set of verses, allow them to see densities per verse
if ($_GET["S"] != "" || $_GET["SURA"] != "" || $_GET["V"] != "")
{
    if (!$unique_to_sura && $FORMULA_LENGTH != "ANY")
    {
        echo "<form action='formulaic_density_by_verse.php?S=" . $_GET["S"] . "&L=$FORMULA_LENGTH&SORT=" . $_GET["SORT"] . "&TYPE=$FORMULA_TYPE&SURA=" . $_GET["SURA"] . "&V=" . $_GET["V"] . "' method=post>";
        echo "<button style='margin-top:-4px;margin-bottom:-4px;'>View Formulaic Densities by Verse</button>";
        echo "</form>";
    }
}

// build the SQL query

$query_type             = "";
$query_length           = "`LENGTH`>2 ";
$sql_to_find_provenance = "";
$query_unique           = "";

if ($FORMULA_TYPE != "EVERYTHING" && $FORMULA_TYPE != "ANY")
{
    $query_type = "AND `TYPE`='$FORMULA_TYPE'";
}

if ($FORMULA_LENGTH != "ANY")
{
    $query_length = "`LENGTH`=$FORMULA_LENGTH";
}

if ($FORMULA_TYPE == "ANY" && $FORMULA_LENGTH != "ANY")
{
    $query_length = "`LENGTH`=$FORMULA_LENGTH";
}

if ($unique_to_sura)
{
    $query_unique = "AND `APPEARS IN HOW MANY SURAS`=1";
}

// allow filtering out of records without full glosses (helpful as we add them)
$filter_out_full_glosses = "";

if (isset($_GET["HIDE_FULL_GLOSS"]))
{
    $filter_out_full_glosses = " AND (`FORMULA FULL GLOSS`='' OR `FORMULA FULL GLOSS` IS NULL)";
}

$sql = "SELECT DISTINCT(`CONCAT OF FORMULA AND TYPE`), `FORMULA`, `FORMULA LOWER`, `FORMULA ARABIC`, `LENGTH`, `TYPE`, `FORMULA TRANSLITERATED`, `FORMULA FULL GLOSS`, `FORMULA ARCHETYPE ID`, `Element1`, `Element2`, `Element3`, `Element4`, `Element5`, COUNT(*) localOccurrences,`OCCURRENCES`, `OCCURRENCES MECCAN`, `OCCURRENCES MEDINAN` FROM `FORMULA-LIST`  WHERE $extra_root_SQL $query_length $query_type $SURA_SQL $query_unique $filter_out_full_glosses GROUP BY `CONCAT OF FORMULA AND TYPE` ORDER BY $SORT_ORDER";

$result = db_query($sql);

$count_everything = db_return_one_record_one_field("SELECT COUNT(`OCCURRENCES`) FROM `FORMULA-LIST` WHERE $extra_root_SQL $query_length $query_type AND `OCCURRENCES` > 1 $query_unique $SURA_SQL");

$count_meccan_total = db_return_one_record_one_field("SELECT COUNT(`OCCURRENCES`) FROM `FORMULA-LIST` WHERE $extra_root_SQL $query_length $query_type AND `OCCURRENCES` > 1 AND `FORMULA PROVENANCE`='Meccan' $SURA_SQL");

$count_medinan_total = db_return_one_record_one_field("SELECT COUNT(`OCCURRENCES`) FROM `FORMULA-LIST` WHERE $extra_root_SQL $query_length $query_type AND `OCCURRENCES` > 1 AND `FORMULA PROVENANCE`='Medinan' $SURA_SQL");

// if we are limiting our report to a sura, then $count_everything is actually the sura count, so swap it and do a fresh "everything" count
if ($_GET["SURA"] != "")
{
    $CountQuranicOccurrences = $count_everything;
    $count_everything        = 0;
    $count_meccan_total      = 0;
    $count_medinan_total     = 0;

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);
        $count_everything += $ROW["OCCURRENCES"];

        // provenance => meccan

        $provsql = "SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `TYPE`='" . $ROW["TYPE"] . "' AND `FORMULA`='" . db_quote($ROW["FORMULA"]) . "' AND `FORMULA PROVENANCE`='Meccan'";

        if ($query_length != "")
        {
            $provsql .= " AND $query_length";
        }

        $provsql .= " " . $query_type;

        $count_meccan_total += db_return_one_record_one_field($provsql);

        // provenance => meccan

        $provsql = "SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `FORMULA`='" . db_quote($ROW["FORMULA"]) . "' AND `FORMULA PROVENANCE`='Medinan'";

        if ($query_length != "")
        {
            $provsql .= " AND $query_length";
        }

        $provsql .= " " . $query_type;

        $count_medinan_total += db_return_one_record_one_field($provsql);
    }
    // reset record pointer
    db_goto($result, 0);
}

    // how many words "effected" by this/these formula(s) listed if only one sura is specified

    $count_words_effected = 0;

    if ($FORMULA_LENGTH != "ANY")
    {
        if ($SURA_EFFECTED_SQL != "" || $SURA_SQL)
        {
            // modify SURA_SQL so its field names match the QURAN-DATA ones
            $modified_SURA_SQL = str_ireplace("START SURA", "SURA", $SURA_SQL);
            $modified_SURA_SQL = str_ireplace("START VERSE", "VERSE", $modified_SURA_SQL);
            $modified_SURA_SQL = str_ireplace("END VERSE", "VERSE", $modified_SURA_SQL);

            if ($FORMULA_TYPE == "ANY")
            {
                $count_words_effected = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE $SURA_EFFECTED_SQL `QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-ANY`>0 $modified_SURA_SQL");
            }

            if ($FORMULA_TYPE == "LEMMA")
            {
                $count_words_effected = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE $SURA_EFFECTED_SQL `QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-LEMMA`>0 $modified_SURA_SQL");
            }

            if ($FORMULA_TYPE == "ROOT")
            {
                $count_words_effected = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE $SURA_EFFECTED_SQL `QTL-ROOT`!='' AND `FORMULA-$FORMULA_LENGTH-ROOT`>0 $modified_SURA_SQL");
            }

            if ($FORMULA_TYPE == "ROOT-ALL")
            {
                $count_words_effected = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE $SURA_EFFECTED_SQL `ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA_LENGTH-ROOT-ALL`>0 $modified_SURA_SQL");
            }
        }
    }

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    $extra_rows = 0;

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr table-header-row>";

    echo "<th rowspan=2 width=50>&nbsp;</th>";

    echo "<th rowspan=2 width=250><b>Transliterated</b>&nbsp;<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=F-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=F-DESC'><img src='../images/down.gif'></a></th>";

    echo "<th rowspan=2 align=center width=250><b>Arabic</b>&nbsp;<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=A-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=A-DESC'><img src='../images/down.gif'></a></th>";

if ($FORMULA_TYPE == "EVERYTHING" || $FORMULA_TYPE == "ANY")
{
    echo "<th rowspan=2 align=center width=150><b>Formula Type</b>&nbsp;<a href='list_formulae.php?V=" . $_GET["V"] . "&?SURA=" . $_GET["SURA"] . "&TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&L=$FORMULA_LENGTH&SORT=TYPE-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=TYPE-DESC'><img src='../images/down.gif'></a></th>";
    $extra_rows++;
}

if ($FORMULA_TYPE == "EVERYTHING" || $FORMULA_LENGTH == "ANY")
{
    echo "<th rowspan=2 width=100 align=center><b>Length</b>&nbsp;<a href='list_formulae.php?V=" . $_GET["V"] . "&?SURA=" . $_GET["SURA"] . "&TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&L=$FORMULA_LENGTH&SORT=LENGTH-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=LENGTH-DESC'><img src='../images/down.gif'></a></th>";
    $extra_rows++;
}

if ($_GET["SURA"] > 0)
{
    echo "<th rowspan=2 align=center width=110><b>Occurrences in Sura " . $_GET["SURA"] . "</b>&nbsp;<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=OCCSURA-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=OCCSURA-DESC'><img src='../images/down.gif'></a></th>";
    $extra_rows++;
}

    echo "<th";
    echo " align=center width=130 colspan=3><b>Occurrences in Whole Qur’an</b></th>";
    echo "<th rowspan=2 width=20>&nbsp;</th></tr>";

    echo "<th align=center width=60><b>All Suras</b>";
    echo "<br>";
    echo "<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=C-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=C-DESC'><img src='../images/down.gif'></a>
	</th>";

    echo "<th align=center width=60><b>Meccan Suras</b>";
    echo "<br>";
    echo "<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=CMEC-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=CMEC-DESC'><img src='../images/down.gif'></a>
	</th>
	
	<th align=center width=70><b>Medinan Suras</b>";
    echo "<br>";
    echo "<a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=CMED-ASC'><img src='../images/up.gif'></a> <a href='list_formulae.php?TYPE=$FORMULA_TYPE&ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=CMED-DESC'><img src='../images/down.gif'></a>
	</th></tr>";

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

    if (db_rowcount($result) == 0)
    {
        echo "<tr><td align=center colspan=" . (7 + $extra_rows) . "><br>No formulae match your search criteria<br>&nbsp;</td></tr>";
    }

    for ($i = $START; $i < $END; $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td align=center width=50>" . number_format($i + 1) . "</td>";

        $search_link = "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        $search_link_meccan = "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . " AND PROVENANCE:MECCAN&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        $search_link_medinan = "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . " AND PROVENANCE:MEDINAN&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        $search_link_with_sura = "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . urlencode(" RANGE:" . $_GET["SURA"]) . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        $bolded_element = -1;

        if ($FORMULA_TYPE == "LEMMA" || $FORMULA_TYPE == "EVERYTHING")
        {
            // we can't just do a search and replace for the root to bold (if searching/filtering by root)
            // so we need to explode then put back together

            echo "<td width=250>";

            if ($_GET["SURA"] > 0)
            {
                echo $search_link_with_sura;
            }
            else
            {
                echo $search_link;
            }
            $temp           = explode("+", $ROW["FORMULA"]);
            $transliterated = explode("+", $ROW["FORMULA TRANSLITERATED"]);

            for ($j = 0; $j < count($temp); $j++)
            {
                if ($j > 0)
                {
                    echo " + ";
                }

                // turn italics on if required
                echo "<span class=$user_preference_transliteration_style>";

                // do we bold it because of a filter by root?
                if ($ROOT != "")
                {
                    if ($ROW["Element" . ($j + 1)] == $_GET["ROOT"])
                    {
                        echo "<b>" . $transliterated[$j] . "</b>";
                        $bolded_element = $j;
                    }
                    else
                    {
                        echo $transliterated[$j];
                    }
                }
                else
                {
                    if ($temp[$j] == "PORP")
                    {
                        echo "[PART/PRON]";
                    }
                    else
                    {
                        echo transliterate_new($temp[$j]);
                    }
                }
            }

            // turn italics off if necessary
            echo "</span>";

            echo "</a>";
            if ($show_formulaic_glosses)
            {
                // allow an an admin to trigger edit mode, and edit the full gloss
                // for this formula
                if ($_SESSION['administrator'] && isset($_GET["EDIT_GLOSS"]))
                {
                    echo "<a href='edit_formula_full_gloss.php?ARCHETYPE=" . $ROW["FORMULA ARCHETYPE ID"] . "'>";
                    echo "<img src='/images/edit.gif' class='float-right'>";
                    echo "</a>";
                }

                echo gloss_formula($ROW["FORMULA"], $ROW["FORMULA FULL GLOSS"]);
            }
            echo "</td>";
        }
        else
        {
            if ($_GET["ROOT"] != "")
            {
                $match = convert_buckwalter($_GET["ROOT"]);
                echo "<td width=250>$search_link<span class=$user_preference_transliteration_style>" . str_ireplace($match, "<b>$match</b>", str_ireplace("PORP", "[PART/PRON]", $ROW["FORMULA TRANSLITERATED"])) . "</span></a>";
                if ($show_formulaic_glosses)
                {
                    // allow an an admin to trigger edit mode, and edit the full gloss
                    // for this formula
                    if ($_SESSION['administrator'] && isset($_GET["EDIT_GLOSS"]))
                    {
                        echo "<a href='edit_formula_full_gloss.php?ARCHETYPE=" . $ROW["FORMULA ARCHETYPE ID"] . "'>";
                        echo "<img src='/images/edit.gif' class='float-right'>";
                        echo "</a>";
                    }

                    echo gloss_formula($ROW["FORMULA"], $ROW["FORMULA FULL GLOSS"]);
                }
                echo "</td>";
            }
            else
            {
                echo "<td width=250>$search_link<span class=$user_preference_transliteration_style>" . str_ireplace("PORP", "[PART/PRON]", $ROW["FORMULA TRANSLITERATED"]) . "</span></a>";
                if ($show_formulaic_glosses)
                {
                    // allow an an admin to trigger edit mode, and edit the full gloss
                    // for this formula
                    if ($_SESSION['administrator'] && isset($_GET["EDIT_GLOSS"]))
                    {
                        echo "<a href='edit_formula_full_gloss.php?ARCHETYPE=" . $ROW["FORMULA ARCHETYPE ID"] . "'>";
                        echo "<img src='/images/edit.gif' class='float-right'>";
                        echo "</a>";
                    }

                    echo gloss_formula($ROW["FORMULA"], $ROW["FORMULA FULL GLOSS"]);
                }
                echo "</td>";
            }
        }

        $bits = explode("+", $ROW["FORMULA"]);

        $build_arabic = "";

        $count = 0;

        foreach ($bits as $SEGMENT)
        {
            if ($build_arabic != "")
            {
                $build_arabic .= " + ";
            }

            if ($bolded_element == $count)
            {
                $build_arabic .= "<b>" . return_arabic_word($SEGMENT) . "</b>";
            }
            else
            {
                $build_arabic .= return_arabic_word($SEGMENT);
            }
            $count++;
        }

        if ($bolded_element == -1 && $ARABIC_ROOT != "")
        {
            $build_arabic = str_ireplace($ARABIC_ROOT, "<b>$ARABIC_ROOT</b>", $build_arabic);
        }

        echo "<td width=250>$search_link" . $build_arabic . "</font></a></td>";

        if ($ROW["FORMULA ARABIC"] == "")
        {
            db_query("UPDATE `FORMULA-LIST` SET `FORMULA ARABIC`='" . db_quote($build_arabic) . "' WHERE `FORMULA`='" . db_quote($ROW["FORMULA"]) . "'");
        }

        if ($FORMULA_TYPE == "EVERYTHING" || $FORMULA_TYPE == "ANY")
        {
            $link_ahref = "<a href='list_formulae.php?TYPE=" . $ROW["TYPE"] . "&L=" . $ROW["LENGTH"] . "' class=linky>";

            echo "<td align=center width=150>$link_ahref";

            if ($ROW["TYPE"] == "LEMMA")
            {
                echo "Lemmata";
            }
            if ($ROW["TYPE"] == "ROOT")
            {
                echo "Root";
            }
            if ($ROW["TYPE"] == "ROOT-ALL")
            {
                echo "Root (Plus Particles/Pronouns)";
            }

            echo "</a>";
        }

        if ($FORMULA_TYPE == "EVERYTHING" || $FORMULA_LENGTH == "ANY")
        {
            $link_ahref = "<a href='list_formulae.php?TYPE=" . $ROW["TYPE"] . "&L=" . $ROW["LENGTH"] . "' class=linky>";

            echo "</td>";
            echo "<td align=center>$link_ahref";
            echo $ROW["LENGTH"];
            echo "</a></td>";
        }

        // occurrences
        if ($_GET["SURA"] > 0)
        {
            echo "<td align=center>$search_link_with_sura" . number_format($ROW["localOccurrences"]) . "</a></td>";
        }

        echo "<td align=center width=60>$search_link" . number_format($ROW["OCCURRENCES"]) . "</a></td>";
        echo "<td align=center width=60>$search_link_meccan" . number_format($ROW["OCCURRENCES MECCAN"]) . "</a></td>";
        echo "<td align=center width=70>$search_link_medinan" . number_format($ROW["OCCURRENCES MEDINAN"]) . "</a></td>";

        // chart link

        echo "<td width=20 align=center>";

        if (!isMobile())
        {
            echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_formula_distribution.php?F=" . urlencode($ROW["FORMULA LOWER"]) . "&L=" . $ROW["LENGTH"] . "&T=" . $ROW["TYPE"] . "&MINI=Y', type: 'post'}\">";
        }

        echo "<a href='../charts/chart_formula_distribution.php?F=" . urlencode($ROW["FORMULA LOWER"]) . "&L=" . $ROW["LENGTH"] . "&T=" . $ROW["TYPE"] . "'><img src='../images/stats.gif'></a>";

        if (!isMobile())
        {
            echo "</span>";
        }

        echo "</td>";

        echo "</tr>";
    }

/*
    if ($i >= (db_rowcount($result) - 1) && db_rowcount($result) > 0)
    {
        echo "<tr>";

        if ($_GET["SURA"] > 0)
        {
            echo "<td bgcolor=#d0d0d0 colspan=".(2 + $extra_rows).">&nbsp;</td>";
            echo "<td bgcolor=#d0d0d0 align=center><b>".number_format($CountQuranicOccurrences)."</b></td>";
        }
        else
        {
            echo "<td bgcolor=#d0d0d0 colspan=".(3 + $extra_rows).">&nbsp;</td>";
        }

        echo "<td bgcolor=#d0d0d0 align=center><b>".number_format($count_everything)."</b></td>";
        echo "<td bgcolor=#d0d0d0 align=center><b>".number_format($count_meccan_total)."</b></td>";
        echo "<td bgcolor=#d0d0d0 align=center><b>".number_format($count_medinan_total)."</b></td>";
        echo "<td bgcolor=#d0d0d0>&nbsp;</td>";
        echo "</tr>";

        echo "<tr>";

        if ($count_words_effected > 0 && db_rowcount($result) > 0 && !$unique_to_sura)
        {
            echo "<td bgcolor=#d0d0d0 align=center COLSPAN=".(7 + $extra_rows).">";
            echo "<i><b>".number_format($count_words_effected)." words in the selected text are tagged with these formulae</b></i>";
            echo "</td>";
        }

        echo "</tr>";
    }
*/

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

    print_page_navigator($CURRENT_PAGE, $pages_needed, ($pages_needed > 7), "list_formulae.php?ROOT=" . $_GET["ROOT"] . "&S=" . $_GET["S"] . "&V=" . $_GET["V"] . "&SURA=" . $_GET["SURA"] . "&L=$FORMULA_LENGTH&SORT=" . $_GET["SORT"] . "&TYPE=$FORMULA_TYPE");
}

echo "</div>";

    // print footer

    include "library/footer.php";

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

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php

move_back_to_top_button();

?>

</html>