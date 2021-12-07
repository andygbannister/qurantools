<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// load users table and load preferences
$result = db_query("SELECT * FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "'");

// set up preferences

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

// CONVERT GET TO POST
if (isset($_GET["L"]))
{
    $_POST["L"] = $_GET["L"];
}
if (isset($_GET["TYPE"]))
{
    $_POST["TYPE"] = $_GET["TYPE"];
}

// LENGTH AND TYPE

$FORMULA_LENGTH = 3;

if (isset($_POST["L"]))
{
    $FORMULA_LENGTH = $_POST["L"];
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

if (isset($_POST["TYPE"]))
{
    $FORMULA_TYPE = $_POST["TYPE"];
    if ($FORMULA_TYPE != "EVERYTHING" && $FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA" && $FORMULA_TYPE != "ANY")
    {
        $FORMULA_TYPE = "ROOT";
    }
}

// avoids a minor script error if we try to use it and it isn't set
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';

             if (isset($_GET["S"]))
             {
                 $windowTitle = "Search Results";
             }
            else
            {
                $windowTitle = "Q. " . $_GET["V"];
            }

            window_title("Cross Referencing Formulae in Selection: $windowTitle");
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
include "../library/search_engine.php";

// sort order
$sort       = "C-DESC";
$SORT_ORDER = "`num` DESC";

if (isset($_GET["SORT"]))
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
    $SORT_ORDER = "`translit`";
}
if ($sort == "E-DESC")
{
    $SORT_ORDER = "`translit` DESC";
}
if ($sort == "C-ASC")
{
    $SORT_ORDER = "num";
}
if ($sort == "C-DESC")
{
    $SORT_ORDER = "num DESC";
}

// menubar

include "../library/menu.php";

// if V is not set, it can trip up the SQL building below

if (!isset($_GET["V"]))
{
    $_GET["V"] = "";
}

// build the SQL we'll use to constrain the master analysis query

if ($_GET["V"] != "")
{
    $RANGE_SQL = "";

    $V = $_GET["V"];

    $what = " Verse Selection";

    // remove whitespace
    $V = preg_replace('/\s+/', '', $V);

    parse_verses($V, true, 0);

    $SQL = "SELECT DISTINCT(`SURA-VERSE`), `SURA`, `VERSE` FROM `QURAN-DATA` WHERE ($RANGE_SQL) ORDER BY `SURA`, `VERSE`";

    $result = db_query($SQL);

    // $countSQL = db_query("SELECT * FROM `QURAN-DATA` WHERE ($RANGE_SQL) AND `QTL-ROOT`!=''");

    // $GrandCount = db_rowcount($countSQL);

    $_GET["S"] = "";
}
else
{
    $result = search($_GET["S"], false);

    // build the SQL we will use to count the total number of verses in this selection
    // $countSQL = "SELECT COUNT(*) FROM `QURAN-FULL-PARSE` t1 LEFT JOIN `QURAN-DATA` t2 ON t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-ROOT`!=''".substr($master_search_sql, 39, strlen($master_search_sql));

    // convert results list to SQL format

    // $GrandCount = db_return_one_record_one_field($countSQL);

    $sVerses = db_rowcount($result) . " verse" . plural(db_rowcount($result));

    $what = " Search Results";

    $_GET["V"] = "";
    $V         = "";
}

// load array of 30 colours for cycling through in charts
include "../library/colours.php";

echo "</head>";
echo "<body class='qt-site'><main class='qt-site-content'>";

include "library/back_to_top_button.php";

echo "<div align=center><h2 class='page-title-text'>Listing & Cross-Referencing Formulae in $what</h2>";

if ($_GET["S"] != "" && $V != "1")
{
    echo "<div style='margin-top:-10px;'>Search Terms: <a href='../verse_browser.php?S=" . urlencode($_GET["S"]) . "' style='text-decoration: none'><b>" . $_GET["S"] . "</a></b> (produced matches in " . number_format($sVerses) . " verse" . plural($sVerses) . ")</div></div><br>";
}
else
{
    echo "<div style='margin-top:-10px;'><a href='../verse_browser.php?V=" . $_GET["V"] . "' style='text-decoration: none'><b>Q. $V</b></a></div><br>";
}

// buttons

echo "<div align=center>";

echo "<form action='selection_formulae_analyse.php?V=" . $_GET["V"] . "&S=" . urlencode($_GET["S"]) . "' method=POST>";

    echo "<div style='border:1px solid black; width:470px; background-color: #f4f4f4; margin-top: -10px;'><table>";

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

    echo "</form></div>";

// reset counts
$hapax_count  = 0;
$unique_count = 0;

// table header
echo "<div align=center ID=TableData>";

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div id=tableContainer class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr table-header-row><th bgcolor=#c0c0c0 align=center rowspan=2 width=60><b>Verse</b></th><th bgcolor=#c0c0c0 width=300 rowspan=2><b>Formulae</b></th><th bgcolor=#c0c0c0 colspan=2 align=center><b>Cross References</b></th>";
    echo "</tr>";

    echo "<tr><th bgcolor=#c0c0c0 align=center width=320><b>In Same Sura</b></th><th bgcolor=#c0c0c0 align=center width=320><b>In Other Suras</b></th></tr>";

    echo "</thead>";

    echo "<tbody>";

    // table data

    // reset the record pointer
    db_goto($result, 0);

    $count_rows_with_formulae       = 0;
    $count_cross_reference_internal = 0;

    $count_cross_reference_external         = 0;
    $count_cross_reference_external_meccan  = 0;
    $count_cross_reference_external_medinan = 0;

    $count_formulae = 0;

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

        $result_formulae = db_query("SELECT * FROM `FORMULA-LIST` WHERE `START-SURA-VERSE`='" . $ROW["SURA-VERSE"] . "' $query_formula_length $query_formula_type");

        // do the formulae

        if (db_rowcount($result_formulae) == 0)
        {
            // echo "<td colspan=2><font color=#909090>None</font></td>";
        }
        else
        {
            $count_rows_with_formulae++;

            echo "<td width=60 align=center";
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
                echo "<td valign=top width=300>";
                echo "<a href='../verse_browser.php?S=FORMULA:" . urlencode($ROW_FORMULA["FORMULA LOWER"]) . "&FORMULA=" . $ROW_FORMULA["LENGTH"] . "&FORMULA_TYPE=" . $ROW_FORMULA["TYPE"] . "' class=linky>";
                echo "<span class=$user_preference_transliteration_style>" . str_ireplace("PORP", "[PART/PRON]", htmlentities($ROW_FORMULA["FORMULA TRANSLITERATED"])) . "</span>";

                echo "</a>";

                if ($show_formulaic_glosses)
                {
                    echo gloss_formula($ROW_FORMULA["FORMULA"], $ROW_FORMULA["FORMULA FULL GLOSS"]);
                }

                echo "</td><td valign=top width=320>";

                $array_verses = explode("; ", $ROW_FORMULA["VERSE LIST"]);

                // do first for internal references

                $count      = 0;
                $all_verses = "";
                foreach ($array_verses as $reference)
                {
                    $elements = explode(":", $reference);

                    if ($elements[0] == $ROW_FORMULA["START SURA"] && $reference != $ROW["SURA-VERSE"])
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

                echo "<td valign=top width=320>";

                // do again for external references

                $count      = 0;
                $all_verses = "";
                foreach ($array_verses as $reference)
                {
                    $elements = explode(":", $reference);

                    if ($elements[0] != $ROW_FORMULA["START SURA"] && $reference != $ROW["SURA-VERSE"])
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
        echo "<tr><td bgcolor=#d0d0d0 colspan=4 align=center><div style='margin-top:20px; margin-bottom:20px;'>No verses have any formulae matching your criteria</div></td></tr>";
    }
    else
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

    echo "</tbody>";

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