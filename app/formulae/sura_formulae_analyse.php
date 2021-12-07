<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

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

// Formula Shared Sort etc variables
if (!isset($_GET["FSORT"]))
{
    $_GET["FSORT"] = "";
}
if (!isset($_GET["PROV"]))
{
    $_GET["PROV"] = "";
}
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
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
            $FORMULA_LENGTH = "ANY";
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

// which sura?

if (!isset($_GET["SURA"]))
{
    $_GET["SURA"] = 1;
}
$SURA = $_GET["SURA"];

if ($SURA < 1 || $SURA > 114)
{
    $SURA = 1;
}

$SURA = db_quote($SURA);

// are we looking for formulae in common?
$INCOMMON         = 0;
$INCOMMON_GROUPBY = "VERSES";

if (isset($_GET["INCOMMON"]))
{
    $INCOMMON = db_quote($_GET["INCOMMON"]);
    if ($INCOMMON < 1 || $INCOMMON > 114)
    {
        $INCOMMON = 0;
    }

    // check for silly errors
    if ($INCOMMON == $SURA)
    {
        $SURA++;
        if ($SURA > 114)
        {
            $SURA = 1;
        }
    }

    if (isset($_GET["INCOMMON_GROUPBY"]))
    {
        if ($_GET["INCOMMON_GROUPBY"] == "FORMULAE")
        {
            $INCOMMON_GROUPBY = "FORMULAE";
        }
    }
}

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            if ($INCOMMON == 0)
            {
                window_title("Cross Referencing Formulae: Sura $SURA");
            }
            else
            {
                window_title("Formulaic Commonalities: Suras $INCOMMON and $SURA");
            }
        ?>		
		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
  
  <?php

function error_message($m)
{
    if ($m == "")
    {
        $m = "Bad reference!";
    }
    echo "<div align=center><b><font color=red>$m</font></b></div>";
}

include "../library/transliterate.php";
include "../library/arabic.php";
include "../library/verse_parse.php";

// construct query

$SQL = "SELECT DISTINCT(`SURA-VERSE`), `SURA`, `VERSE` FROM `QURAN-DATA` WHERE `SURA`='" . ($INCOMMON > 0 ? $INCOMMON : $SURA) . "' ORDER BY `SURA`, `VERSE`";

$result = db_query($SQL);

echo "</head>";

echo "<body class='qt-site'><main class='qt-site-content'>";

include "library/back_to_top_button.php";

include "../library/menu.php";

echo "<div align=center><h2 class='page-title-text'>";

if ($INCOMMON == 0)
{
    echo "Listing & Cross-Referencing Formulae in Sura $SURA <i>" . sura_name_arabic($SURA) . "</i>";
}
else
{
    echo "Listing Formulaic Commonalities Between Sura $INCOMMON <i>" . sura_name_arabic($INCOMMON) . "</i> and Sura $SURA <i>" . sura_name_arabic($SURA) . "</i>";
    echo " <a href='/charts/chart_formulae_in_common_by_sura.php?SURA=$INCOMMON&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&PROV=" . $_GET["PROV"] . "&SORT=" . $_GET["SORT"] . "&INCOMMON_GROUPBY=$INCOMMON_GROUPBY&FSORT=" . $_GET["FSORT"] . "'><img src='/images/stats.gif'></a>";
}
echo "</h2>";

// buttons

echo "<div align=center>";

echo "<form action='sura_formulae_analyse.php' method=GET>";

    echo "<INPUT name=INCOMMON type=HIDDEN value=$INCOMMON>";
    echo "<INPUT name=INCOMMON_GROUPBY type=HIDDEN value=$INCOMMON_GROUPBY>";

    echo "<INPUT name=FSORT type=HIDDEN value=" . $_GET["FSORT"] . ">";;
    echo "<INPUT name=SORT type=HIDDEN value=" . $_GET["SORT"] . ">";
    echo "<INPUT name=PROV type=HIDDEN value=" . $_GET["PROV"] . ">";

    echo "<div class='formulaic-pick-table'><table>";

    // sura picklists

    if ($INCOMMON > 0)
    {
        echo "<tr>";

        echo "<td>";
        echo "Sura to Examine";
        echo "</td>";

        echo "<td>";
        echo "<select name=INCOMMON onChange='this.form.submit();'>";

        for ($i = 1; $i <= 114; $i++)
        {
            echo "<option value=$i";
            if ($i == $INCOMMON)
            {
                echo " selected";
            }
            echo ">$i</option>";
        }

        echo "</select>";

        echo "</td>";

        echo "</tr>";
    }

    echo "<tr>";

    echo "<td>";
    echo "Sura to " . ($INCOMMON > 0 ? "Cross-Check" : "Analyse");
    echo "</td>";

    echo "<td>";
    echo "<select name=SURA onChange='this.form.submit();'>";

    for ($i = 1; $i <= 114; $i++)
    {
        // we don't allow them to look for formulae in common with the same sura as itself
        if ($i != $INCOMMON)
        {
            echo "<option value=$i";
            if ($i == $SURA)
            {
                echo " selected";
            }
            echo ">$i</option>";
        }
    }

    echo "</select>";

    echo "</td>";

    echo "</tr>";

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

    echo "</table></div>";

    echo "</form>";

    echo "</div>";

if ($INCOMMON > 0)
{
    echo "<div class='button-block-with-spacing'>";

    if ($INCOMMON_GROUPBY == "VERSES")
    {
        echo "<button><b>Group by Verses</b></button>";
        echo "<a href='sura_formulae_analyse.php?SURA=$SURA&INCOMMON=$INCOMMON&INCOMMON_GROUPBY=FORMULAE&PROV=" . $_GET["PROV"] . "&SORT=" . $_GET["SORT"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&FSORT=" . $_GET["FSORT"] . "'>";
        echo "<button>Group by Formulae</button>";
        echo "</a>";
    }
    else
    {
        echo "<a href='sura_formulae_analyse.php?SURA=$SURA&INCOMMON=$INCOMMON&INCOMMON_GROUPBY=VERSES&PROV=" . $_GET["PROV"] . "&SORT=" . $_GET["SORT"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE&FSORT=" . $_GET["FSORT"] . "'>";
        echo "<button>Group by Verses</button>";
        echo "</a>";
        echo "<button><b>Group by Formulae</b></button>";
    }
    echo "</div>";
}

// reset counts
$hapax_count  = 0;
$unique_count = 0;

// table header

echo "<div align=center ID=TableData>";
echo "<table class='hoverTable persist-area fixedTable'>";
echo "<thead class='persist-header table-header-row'>";

if ($INCOMMON_GROUPBY == "FORMULAE")
{
    // do a formulae table instead

    $COUNT_MASTER = 0;
    $COUNT_MINOR  = 0;

    $link = "sura_formulae_analyse.php?SURA=$SURA&INCOMMON=$INCOMMON&INCOMMON_GROUPBY=FORMULAE&PROV=" . $_GET["PROV"] . "&SORT=" . $_GET["SORT"] . "&L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE";

    echo "<tr table-header-row>
	<th bgcolor=#c0c0c0 align=center width=30><b>#</b></th>
	<th bgcolor=#c0c0c0 width=250><b>Transliterated</b><br><a href='$link&FSORT=TRANSLIT-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=TRANSLIT-DESC'><img src='../images/down.gif'></a></th>
	<th bgcolor=#c0c0c0 align=center width=200><b>Arabic</b><br><a href='$link&FSORT=ARABIC-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=ARABIC-DESC'><img src='../images/down.gif'></a></th>
	<th bgcolor=#c0c0c0 align=center width=150><b>Type</b><br>&nbsp;";

    if ($FORMULA_TYPE == "ANY")
    {
        echo "<a href='$link&FSORT=TYPE-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=TYPE-DESC'><img src='../images/down.gif'></a>&nbsp;";
    }

    echo "</th><th bgcolor=#c0c0c0 align=center width=70><b>Length</b><br>&nbsp;";

    if ($FORMULA_LENGTH == "ANY")
    {
        echo "<a href='$link&FSORT=LENGTH-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=LENGTH-DESC'><img src='../images/down.gif'></a>&nbsp;";
    }

    echo "</th><th bgcolor=#c0c0c0 align=center><b>Occurrences<br>(Sura $INCOMMON)</b><br><a href='$link&FSORT=OCC-MAJ-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=OCC-MAJ-DESC'><img src='../images/down.gif'></a></th>
	<th bgcolor=#c0c0c0 align=center><b>Occurrences<br>(Sura $SURA)</b><br><a href='$link&FSORT=OCC-MIN-ASC'><img src='../images/up.gif'></a> <a href='$link&FSORT=OCC-MIN-DESC'><img src='../images/down.gif'></a></th>";
    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    // get the formula table sort order if there is one
    $formula_sort_sql = "";

    if (isset($_GET["FSORT"]))
    {
        switch ($_GET["FSORT"])
        {
            case "OCC-MAJ-ASC":
                $formula_sort_sql = " ORDER BY count_major ASC";
                break;

            case "OCC-MAJ-DESC":
                $formula_sort_sql = " ORDER BY count_major DESC";
                break;

            case "OCC-MIN-ASC":
                $formula_sort_sql = " ORDER BY count_minor ASC";
                break;

            case "OCC-MIN-DESC":
                $formula_sort_sql = " ORDER BY count_minor DESC";
                break;

            case "TRANSLIT-ASC":
                $formula_sort_sql = " ORDER BY `FORMULA TRANSLITERATED` ASC";
                break;

            case "TRANSLIT-DESC":
                $formula_sort_sql = " ORDER BY `FORMULA TRANSLITERATED` DESC";
                break;

            case "ARABIC-ASC":
                $formula_sort_sql = " ORDER BY `FORMULA ARABIC` ASC";
                break;

            case "ARABIC-DESC":
                $formula_sort_sql = " ORDER BY `FORMULA ARABIC` DESC";
                break;

            case "TYPE-ASC":
                $formula_sort_sql = " ORDER BY `TYPE` ASC";
                break;

            case "TYPE-DESC":
                $formula_sort_sql = " ORDER BY `TYPE` DESC";
                break;

            case "LENGTH-ASC":
                $formula_sort_sql = " ORDER BY `LENGTH` ASC";
                break;

            case "LENGTH-DESC":
                $formula_sort_sql = " ORDER BY `LENGTH` DESC";
                break;
        }
    }

    // build SQL (wow, a bit complex but it's pretty logical really)

    $sql_incommon = "SELECT DISTINCT(CONCAT(T2.`FORMULA`,'-',T2.`TYPE`)), T2.`FORMULA`, T2.`FORMULA LOWER`, T2.`FORMULA TRANSLITERATED`, T2.`LENGTH`, T2.`FORMULA ARABIC`, T2.`TYPE`, T2.`FORMULA FULL GLOSS`, 
	(SELECT COUNT(*) FROM `FORMULA-LIST` T3 WHERE T3.`FORMULA`=T1.`FORMULA` AND T3.`TYPE`=T1.`TYPE` AND T3.`START SURA`=$INCOMMON";

    if ($FORMULA_TYPE != "ANY")
    {
        $sql_incommon .= " AND T3.`TYPE`='$FORMULA_TYPE'";
    }
    if ($FORMULA_LENGTH != "ANY")
    {
        $sql_incommon .= " AND T3.`LENGTH`=$FORMULA_LENGTH";
    }

    $sql_incommon .= ") count_major,
	(SELECT COUNT(*) FROM `FORMULA-LIST` T4 WHERE T4.`FORMULA`=T1.`FORMULA` AND T4.`TYPE`=T1.`TYPE` AND T4.`START SURA`=$SURA";

    if ($FORMULA_TYPE != "ANY")
    {
        $sql_incommon .= " AND T4.`TYPE`='$FORMULA_TYPE'";
    }
    if ($FORMULA_LENGTH != "ANY")
    {
        $sql_incommon .= " AND T4.`LENGTH`=$FORMULA_LENGTH";
    }

    $sql_incommon .= ") count_minor
	
	FROM `FORMULA-LIST` T1 
LEFT JOIN `FORMULA-LIST` T2 ON T1.`FORMULA`=T2.`FORMULA` AND T1.`TYPE`=T2.`TYPE` AND T2.`START SURA`=$SURA";

    if ($FORMULA_TYPE != "ANY")
    {
        $sql_incommon .= " AND T2.`TYPE`='$FORMULA_TYPE'";
    }
    if ($FORMULA_LENGTH != "ANY")
    {
        $sql_incommon .= " AND T2.`LENGTH`=$FORMULA_LENGTH";
    }

    $sql_incommon .= " WHERE T1.`START SURA`=$INCOMMON AND T2.`FORMULA`!=''
 $formula_sort_sql";

    $result = db_query($sql_incommon);

    if (db_rowcount($result) == 0)
    {
        echo "<tr><td colspan=7 align=center><div style='margin-top:20px; margin-bottom:20px;'><p>There are no formulae matching your criteria that occur in both sura $INCOMMON and sura $SURA</p><p><a href='/charts/chart_formulae_in_common_by_sura.php?SURA=$INCOMMON' class=linky-light>Click here to show a chart of any formulae that sura $INCOMMON has in common with other suras</a></p></div></td></tr>";
    }

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td width=30 align=center>" . number_format($i + 1) . "</td>";

        $link_code = "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . urlencode(" RANGE:$INCOMMON;$SURA") . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>";

        echo "<td width=250>$link_code" . "<span class=$user_preference_transliteration_style>" . str_ireplace("PORP", "[PART/PRON]", $ROW["FORMULA TRANSLITERATED"]) . "</a>";

        if ($show_formulaic_glosses)
        {
            echo gloss_formula($ROW["FORMULA"], $ROW["FORMULA FULL GLOSS"]);
        }

        echo "</td>";

        echo "<td width=200>$link_code" . $ROW["FORMULA ARABIC"] . "</a></td>";

        echo "<td width=150 align=center>";

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

        echo "</td>";

        echo "<td width=70 align=center>" . $ROW["LENGTH"] . "</td>";

        echo "<td align=center>";
        echo "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . urlencode(" RANGE:$INCOMMON") . "&FORMULA=" . $ROW["LENGTH"] . "&FORMULA_TYPE=" . $ROW["TYPE"] . "' class=linky>";
        echo number_format($ROW["count_major"]);
        $COUNT_MASTER += $ROW["count_major"];
        echo "</a>";
        echo "</td>";

        echo "<td align=center>";
        echo "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW["FORMULA LOWER"]) . urlencode(" RANGE:$SURA") . "&FORMULA=" . $ROW["LENGTH"] . "&FORMULA_TYPE=" . $ROW["TYPE"] . "' class=linky>";
        echo number_format($ROW["count_minor"]) . "</td>";
        $COUNT_MINOR += $ROW["count_minor"];
        echo "</a>";
    }

    echo "</tr>";

    if (db_rowcount($result) > 0)
    {
        echo "<td>&nbsp;</td>";
        echo "<td>&nbsp;</td>";
        echo "<td>&nbsp;</td>";
        echo "<td>&nbsp;</td>";
        echo "<td>&nbsp;</td>";

        echo "<td ALIGN=CENTER><b>" . number_format($COUNT_MASTER) . "</b></td>";
        echo "<td ALIGN=CENTER><b>" . number_format($COUNT_MINOR) . "</b></td>";

        echo "</tr>";
    }

    echo "</tbody>";
}
else
{
    if ($INCOMMON > 0)
    {
        echo "<tr table-header-row><th bgcolor=#c0c0c0 align=center rowspan=2 width=60><b>Verse</b></th><th bgcolor=#c0c0c0 rowspan=2 width=400><b>Formulae</b></th><th bgcolor=#c0c0c0 width=320 colspan=1 align=center><b>Occurrences in Sura $SURA</b></th>";
    }
    else
    {
        echo "<tr table-header-row><th bgcolor=#c0c0c0 align=center rowspan=2 width=60><b>Verse</b></th><th bgcolor=#c0c0c0 rowspan=2 width=400><b>Formulae</b></th><th bgcolor=#c0c0c0 colspan=2 align=center><b>Cross References</b></th>";
    }

    echo "</tr>";

    if ($INCOMMON == 0)
    {
        echo "<tr><th bgcolor=#c0c0c0 align=center width=320><b>In Sura $SURA</b></td><th bgcolor=#c0c0c0 align=center width=320><b>In Other Suras</b></th></tr>";
    }

    echo "</thead>";

    echo "<tbody>";

    // table data

    // reset the record pointer
    db_goto($result, 0);

    $count_rows_with_formulae       = 0;
    $count_formulae                 = 0;
    $count_cross_reference_internal = 0;

    $count_cross_reference_external         = 0;
    $count_cross_reference_external_meccan  = 0;
    $count_cross_reference_external_medinan = 0;

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        // lookup the formulae we will be showing

        $query_formula_type = "";
        if ($FORMULA_TYPE != "ANY")
        {
            $query_formula_type = " AND `TYPE`='" . db_quote($FORMULA_TYPE) . "'";
        }

        $query_formula_length = "";
        if ($FORMULA_LENGTH != "ANY")
        {
            $query_formula_length = " AND `LENGTH`=" . db_quote($FORMULA_LENGTH);
        }
        else
        {
            $query_formula_length = " AND `LENGTH`>2";
        }

        if ($INCOMMON == 0)
        {
            $result_formulae = db_query("SELECT * FROM `FORMULA-LIST` WHERE `START-SURA-VERSE`='" . $ROW["SURA-VERSE"] . "' $query_formula_length $query_formula_type");
        }
        else
        {
            $sql_incommon = "SELECT * FROM `FORMULA-LIST` T1 WHERE `START-SURA-VERSE`='" . $ROW["SURA-VERSE"] . "' $query_formula_length $query_formula_type AND (SELECT COUNT(DISTINCT `START GLOBAL WORD NUMBER`)>0 FROM `FORMULA-LIST` T2 WHERE T1.`FORMULA`=T2.`FORMULA`";

            // if ($FORMULA_LENGTH > 0) {$sql_incommon .=" AND T2.`LENGTH`=".db_quote($FORMULA_LENGTH);}

            // if ($FORMULA_TYPE !="ANY") $sql_incommon .=" AND T2.`TYPE`='".db_quote($FORMULA_TYPE)."'";

            $sql_incommon .= " AND T2.`TYPE`=T1.`TYPE` AND T2.`LENGTH`=T1.`LENGTH`";

            $sql_incommon .= " AND `START SURA`=$SURA) > 0";

            $result_formulae = db_query($sql_incommon);
        }

        // do the formulae

        if (db_rowcount($result_formulae) == 0)
        {
            // echo "<td><font color=#909090>None</font></td><td>&nbsp;</td><td>&nbsp;</td>";
        }
        else
        {
            $count_rows_with_formulae++;
            echo "<td align=center width=60";
            if (db_rowcount($result_formulae) > 1)
            {
                echo " valign=top rowspan=" . db_rowcount($result_formulae);
            }
            echo "><a href='../verse_browser.php?V=" . urlencode($ROW["SURA-VERSE"]) . "' class=linky>" . $ROW["SURA-VERSE"] . "</a></td>";

            for ($j = 0; $j < db_rowcount($result_formulae); $j++)
            {
                $count_formulae++;

                // grab next database row
                $ROW_FORMULA = db_return_row($result_formulae);

                if ($j > 0)
                {
                    echo "<tr>";
                }
                echo "<td valign=top width=400>";
                echo "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW_FORMULA["FORMULA LOWER"]) . urlencode(" RANGE:" . $ROW["SURA-VERSE"]) . "&FORMULA=" . $ROW_FORMULA["LENGTH"] . "&FORMULA_TYPE=" . $ROW_FORMULA["TYPE"] . "' class=linky>";
                echo "<span class=$user_preference_transliteration_style>" . str_ireplace("PORP", "[PART/PRON]", htmlentities($ROW_FORMULA["FORMULA TRANSLITERATED"])) . "</span>";

                echo "</a>";
                if ($show_formulaic_glosses)
                {
                    echo gloss_formula($ROW_FORMULA["FORMULA"], $ROW_FORMULA["FORMULA FULL GLOSS"]);
                }

                echo "</td><td valign=top width=320>";

                $array_verses = explode("; ", $ROW_FORMULA["VERSE LIST"]);

                // first for internal references

                $count      = 0;
                $all_verses = "";
                foreach ($array_verses as $reference)
                {
                    $elements = explode(":", $reference);

                    if ($elements[0] == $SURA && $reference != $ROW["SURA-VERSE"])
                    {
                        $count_cross_reference_internal++;

                        if ($count > 0)
                        {
                            echo "; ";
                        }

                        // does formulae span multiple verses
                        $reference_for_link = $reference;
                        if ($ROW_FORMULA["END VERSE"] != $ROW_FORMULA["START VERSE"])
                        {
                            $reference_for_link .= "-" . $ROW_FORMULA["END VERSE"];
                        }

                        if ($INCOMMON == 0)
                        {
                            $search_criterion_to_find_this_formula = urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$reference_for_link");
                        }
                        else
                        {
                            $search_criterion_to_find_this_formula = urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$reference_for_link;" . $ROW["SURA-VERSE"]);
                        }

                        echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{SEARCH:'$search_criterion_to_find_this_formula'}}\">";

                        echo "<a href='../verse_browser.php?S=$search_criterion_to_find_this_formula' class=linky>";
                        echo "$reference</a>";

                        echo "</span>";

                        $count++;

                        // save this reference to the the all verses list
                        if ($all_verses != "")
                        {
                            $all_verses .= ";";
                        }
                        $all_verses .= $reference_for_link;
                    }
                }

                // build the "view all" link

                if ($count > 1)
                {
                    echo " <font size=-2>";

                    if ($INCOMMON == 0)
                    {
                        echo "<a href='../verse_browser.php?S=" . urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$all_verses") . "' class=linky>";
                        echo "(View&nbsp;All&nbsp;$count&nbsp;Verses)"; // we use &nbsp; so the link appears all on one line
                    }
                    else
                    {
                        echo "<a href='../verse_browser.php?S=" . urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$all_verses;" . $ROW["SURA-VERSE"]) . "' class=linky>";
                        echo "(View&nbsp;All)"; // we use &nbsp; so the link appears all on one line
                    }
                    echo "</a></font>";
                }

                echo "</td>";

                // second for external references -- but not needed if we are doing an "incommon" search

                if ($INCOMMON == 0)
                {
                    echo "<td valign=top width=320>";

                    $count      = 0;
                    $all_verses = "";
                    foreach ($array_verses as $reference)
                    {
                        $elements = explode(":", $reference);

                        if ($elements[0] != $SURA)
                        {
                            $count_cross_reference_external++;

                            if (sura_provenance($elements[0]) == "Meccan")
                            {
                                $count_cross_reference_external_meccan++;
                            }
                            else
                            {
                                $count_cross_reference_external_medinan++;
                            }

                            if ($count > 0)
                            {
                                echo "; ";
                            }

                            // does formulae span multiple verses
                            $reference_for_link = $reference;
                            if ($ROW_FORMULA["END VERSE"] != $ROW_FORMULA["START VERSE"])
                            {
                                $reference_for_link .= "-" . $ROW_FORMULA["END VERSE"];
                            }

                            $search_criterion_to_find_this_formula = urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$reference_for_link");

                            echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{SEARCH:'$search_criterion_to_find_this_formula'}}\">";

                            echo "<a href='../verse_browser.php?S=$search_criterion_to_find_this_formula' class=linky>";
                            echo "$reference</a>";

                            echo "</span>";

                            $count++;

                            // save this reference to the the all verses list
                            if ($all_verses != "")
                            {
                                $all_verses .= ";";
                            }
                            $all_verses .= $reference_for_link;
                        }
                    }

                    // build the "view all" link

                    if ($count > 1)
                    {
                        echo " <font size=-2>";
                        echo "<a href='../verse_browser.php?S=" . urlencode("FORMULA:" . $ROW_FORMULA["FORMULA LOWER"] . " RANGE:$all_verses") . "' class=linky>";
                        echo "(View&nbsp;All&nbsp;$count&nbsp;Verses)"; // we use &nbsp; so the link appears all on one line
                        echo "</a></font>";
                    }

                    echo "</td>";
                }

                // finish the row

                if ($j > 0)
                {
                    echo "</tr>";
                }
            }
        }

        echo "</tr>";
    }

    if ($count_rows_with_formulae == 0)
    {
        if ($INCOMMON == 0)
        {
            echo "<tr><td colspan=4 align=center><div style='margin-top:20px; margin-bottom:20px;'>No verses in this sura have any formulae matching your criteria</div></td></tr>";
        }
        else
        {
            echo "<tr><td colspan=3 align=center><div style='margin-top:20px; margin-bottom:20px;'><p>There are no formulae matching your criteria that occur in both sura $INCOMMON and sura $SURA</p><p><a href='/charts/chart_formulae_in_common_by_sura.php?SURA=$INCOMMON' class=linky-light>Click here to show a chart of any formulae that sura $INCOMMON has in common with other suras</a></p></div></td></tr>";
        }
    }
    else
    {
        if ($INCOMMON == 0)
        {
            echo "<tr><td bgcolor=#d0d0d0 colspan=4 align=center><div style='margin-top:20px; margin-bottom:20px;'><b>Formulae Analysed: " . number_format($count_formulae) . "</b><br>From these, there ";

            if ($count_cross_reference_internal == 0)
            {
                echo "were no";
            }
            if ($count_cross_reference_internal == 1)
            {
                echo "was 1";
            }
            if ($count_cross_reference_internal > 1)
            {
                echo "were " . number_format($count_cross_reference_internal);
            }

            echo " internal cross reference" . plural($count_cross_reference_internal) . " and ";

            if ($count_cross_reference_external == 0)
            {
                echo "no";
            }
            if ($count_cross_reference_external > 0)
            {
                echo number_format($count_cross_reference_external);
            }

            echo " external cross reference" . plural($count_cross_reference_external);

            // do meccan and medinan count

            echo " (" . number_format($count_cross_reference_external_meccan) . " in Meccan suras; " . number_format($count_cross_reference_external_medinan) . " in Medinan suras)";

            echo "</div></td></tr>";
        }
    }

    echo "<tbody>";
}

    echo "</table>";
    echo "</div>";
    echo "</div>";

include "../library/footer.php";

?>

</body>

<script type="text/javascript">
  $(function() {
    Tipped.create('.loupe-tooltip', {position: 'left', maxWidth: 300, showDelay: 800, skin: 'light'});
  });
</script>

</html>