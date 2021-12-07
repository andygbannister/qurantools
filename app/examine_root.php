<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
        ?>

		<script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
		
		<script>
			$(document).ready(function(){
				// Assign the Colorbox event to elements
				$(".iframe").colorbox({iframe:true, width:"80%", height:"95%"});
			
			});
		</script>
		
		<script>
		
    function reload_with_root(which_field)
   {
   	pass_root = "";
   	if (which_field == 1)
   	{
   		pass_root = document.getElementById("ROOT").value;	
   	}  	
   	else
   	{
   		pass_root = document.getElementById("ROOTARA").value;	
   	}
   	   	
   	window.location = "examine_root.php?ROOT="+pass_root; 	
   	
   }
   </script> 
      
  <?php

  $ROOT = "";
    if (isset($_GET["ROOT"]))
    {
        $ROOT = $_GET["ROOT"];
    }

    // if no root passed, offer the drop down list
    if ($ROOT == "")
    {
        window_title("Examine Root");

        echo "</head><body class='qt-site'><main class='qt-site-content'>";

        include "library/menu.php";

        echo "<div align=center><h2 class='page-title-text'>Examine Arabic Root</h2>";

        echo "Please begin by choosing a root to analyse (from either the Arabic or transliteration menus below)";

        echo "<form action='examine_root.php' method=POST>";

        echo "<div style='border:1px solid black; width:450px; background-color: #f4f4f4; margin-top: 10px;'><table cellpadding=4>";

        echo "<tr>";

        echo "<td>Transliterated Roots:</td><td>";

        echo "<select name=ROOT ID=ROOT onChange='reload_with_root(1);'>";

        $result = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

        echo "<option value=''>Choose Root</option>";

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            $ROW = db_return_row($result);
            echo "<option value='" . urlencode($ROW["ENGLISH"]) . "'>" . transliterate_new($ROW["ENGLISH"]) . "</option>";
        }
        echo "</select>";

        echo "</td></tr>";

        echo "<tr>";

        echo "<td>Arabic Roots:</td><td>";

        echo "<select name=ROOTARA ID=ROOTARA onChange='reload_with_root(2);'>";

        echo "<option value=''>Choose Root</option>";

        $result = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            $ROW = db_return_row($result);
            echo "<option value='" . urlencode($ROW["ENGLISH"]) . "'>" . $ROW["ARABIC"] . "</option>";
        }
        echo "</select>";

        echo "</td></tr>";

        echo "</table></form></div>";

        include "library/footer.php";

        exit;
    }

    window_title("Examine Root: " . return_arabic_word($ROOT) . " (" . htmlentities(transliterate_new($ROOT)) . ")");

  ?>
  
</title>
</head>
<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "library/back_to_top_button.php";

include "library/menu.php";

// spacer
echo "<div>&nbsp;</div>";

// escape ROOT, so we do it just once
$ROOT_ESCAPED = db_quote($ROOT);

echo "<div align=center><h2 class='page-title-text'>Examining Root: " . return_arabic_word($ROOT) . " (<span $user_preference_transliteration_style>" . htmlentities(transliterate_new($ROOT)) . "</span>)</h2>";

$occurrences = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE BINARY(`QTL-ROOT`)='$ROOT_ESCAPED'");

$meaning = db_return_one_record_one_field("SELECT `MEANING` FROM `ROOT-LIST` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");

$penrice_page = db_return_one_record_one_field("SELECT `PENRICE PAGE` FROM `DICTIONARY-ENTRIES` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");

$lane_page = db_return_one_record_one_field("SELECT `LANE PAGE` FROM `DICTIONARY-ENTRIES` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");

$jeffery_page = db_return_one_record_one_field("SELECT `AJ FOREIGN PAGE` FROM `LEMMA-LIST` WHERE `ROOT`='$ROOT_ESCAPED' AND `AJ FOREIGN PAGE`>0");

if ($meaning != "")
{
    echo "<table border=1 width=600 cellspacing=0 cellpadding=8><tr><td align=center>";
    echo "<b>Definition:</b><br><i>" . str_ireplace("\n", "<br>", $meaning) . "</i>";

    if ($lane_page > 0 || $penrice_page > 0)
    {
        echo "<div id=lexica class=top-padding-only>";

        if ($lane_page > 0)
        {
            echo "<a class='iframe' href='dictionary/lane.php?PAGE=$lane_page&LIGHTVIEW=YES'>";
            echo "<img src='images/lane_small.png' title='Show in Lane&rsquo;s \"An Arabic-English Lexicon\"'></a>";
        }

        if ($penrice_page > 0)
        {
            if ($lane_page > 0)
            {
                echo "&nbsp;&nbsp;";
            }

            echo "<a class='iframe' href='dictionary/penrice.php?PAGE=$penrice_page&LIGHTVIEW=YES'>";

            echo "<img src='images/penrice_small.png' title='Show in Penrice&rsquo;s \"Dictionary and Glossary of the Qur&rsquo;an\"'></a>";
        }

        if ($jeffery_page > 0)
        {
            if ($lane_page > 0 || $penrice_page > 0)
            {
                echo "&nbsp;&nbsp;";
            }

            echo "<a class='iframe' href='dictionary/jeffery.php?PAGE=" . (14 + $jeffery_page) . "&LIGHTVIEW=YES'>";

            echo "<img src='images/jeffery_small.png' title='Show in Arthur Jeffery&rsquo;s \"The Foreign Vocabulary of the Qur&rsquo;an\"'></a>";
        }

        echo "</div>";
    }

    echo "</td></tr></table><br>";
}

echo "<table border=1 width=600 cellspacing=0 cellpadding=8><tr><td align=center>";
echo "<b>Total Qur’anic Occurrences: <a href='verse_browser.php?S=ROOT:" . urlencode($ROOT) . "' class=linky>" . number_format($occurrences) . "</a></b>";

if (!isMobile())
{
    echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'charts/chart_roots.php?VIEW=MINI&ROOT=" . urlencode(convert_buckwalter($ROOT_ESCAPED)) . "', type: 'post'}\">";
}

echo "<a href='charts/chart_roots.php?ROOT=" . urlencode(convert_buckwalter($ROOT_ESCAPED)) . "'><img src='images/st.gif'></a>";

if (!isMobile())
{
    echo "</span>";
}

echo "<div style='margin-top:4px;'>";
echo "<a href='word_associations.php?ROOT=" . urlencode($ROOT) . "'><button>Find Word Associations</button></a>";
echo "</div>";

echo "</td></tr></table><br>";

// LEMMA

$lemma_result = db_query("SELECT * FROM `LEMMA-LIST` WHERE `ROOT`='$ROOT_ESCAPED'");

if (db_rowcount($lemma_result) > 0)
{
    echo "<table border=1 width=600 cellspacing=0 cellpadding=4>";
    echo "<tr><td align=center align=center bgcolor=#b0b0b0><b><a href='counts/count_all_lemmata.php?ROOT=" . urlencode($ROOT) . "' class=linky>";

    if (db_rowcount($lemma_result) > 1)
    {
        echo "Lemmata";
    }
    else
    {
        echo "Lemma";
    }
    echo " Based on This Root: " . db_rowcount($lemma_result) . "</a></b></td></tr>";
    echo "<tr><td><ul style='margin-bottom: 0px;'>";

    for ($i = 0; $i < db_rowcount($lemma_result); $i++)
    {
        $ROW = db_return_row($lemma_result);

        // LOOK UP LEMMA STATISTICS FROM MAIN QURAN TABLE
        $lemma_info = db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-LEMMA`='" . db_quote($ROW["ENGLISH"]) . "'");

        echo "<li><a href='verse_browser.php?S=LEMMA:" . urlencode($ROW["ARABIC"]) . "' class=linky>" . $ROW["ARABIC"] . " &nbsp;<span $user_preference_transliteration_style>" . $ROW["ENGLISH TRANSLITERATED"] . "</span>";

        echo "&nbsp;&nbsp;(" . number_format($lemma_info) . " occurrence" . plural($lemma_info) . ")";

        echo "</a>";

        echo "</li>";
    }

    echo "</ul></td></tr>";

    echo "</table><br>";
}

// COUNT TYPES

$type_verb = db_return_one_record_one_field("SELECT `RENDER_COUNT_VERB` FROM `ROOT-LIST` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");

$type_noun = db_return_one_record_one_field("SELECT `RENDER_COUNT_NOUN` FROM `ROOT-LIST` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");

// if we have verbs or noun types to show, print the header for this section
if ($type_verb > -0 || $type_noun > 0)
{
    echo "<table border=1 width=600 cellspacing=0 cellpadding=4>";

    $cols = 4;
    if ($type_verb == 0)
    {
        $cols = 3;
    }

    echo "<tr><td colspan=$cols align=center bgcolor=#b0b0b0><b>Breakdown of Appearances</b></td></tr>";

    if ($type_verb > 0)
    {
        $verb_result = db_query("SELECT * FROM `ROOT-LIST` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");
        $ROW_VERB    = db_return_row($verb_result);

        echo "<tr><td colspan=4 bgcolor=#e0e0e0 align=center><b>Verb: <a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB]' class=linky>" . number_format($type_verb) . "</a></b></td></tr>";
        echo "<tr>";
        echo "<td width=100>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Form</b></td></tr>";
        echo "<tr><td width=30>I:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:1]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_1"]) . "</a></td></tr>";
        echo "<tr><td width=30>II:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:2]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_2"]) . "</a></td></tr>";
        echo "<tr><td width=30>III:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:3]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_3"]) . "</a></td></tr>";
        echo "<tr><td width=30>IV:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:4]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_4"]) . "</a></td></tr>";
        echo "<tr><td width=30>V:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:5]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_5"]) . "</a></td></tr>";
        echo "<tr><td width=30>VI:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:6]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_6"]) . "</a></td></tr>";
        echo "<tr><td width=30>VII:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:7]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_7"]) . "</a></td></tr>";
        echo "<tr><td width=30>VIII:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:8]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_8"]) . "</a></td></tr>";
        echo "<tr><td width=30>VIX:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:9]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_9"]) . "</a></td></tr>";
        echo "<tr><td width=30>IX:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:10]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_10"]) . "</a></td></tr>";
        echo "<tr><td width=30>XI:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:11]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_11"]) . "</a></td></tr>";
        echo "<tr><td width=30>XII:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FORM:12]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_FORM_12"]) . "</a></td></tr>";

        echo "</table>";
        echo "</td>";

        echo "<td width=100 valign=top>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Gender</b></td></tr>";
        echo "<tr><td width=90>Masculine:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB MASCULINE]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_GENDER_MASC"]) . "</a></td></tr>";
        echo "<tr><td>Feminine:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB FEMININE]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_GENDER_FEM"]) . "</a></td></tr>";
        echo "<tr><td>Unassigned:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB NEUTER]' class=linky>" . number_format($ROW_VERB["RENDER_COUNT_VERB"] - $ROW_VERB["RENDER_VERB_GENDER_MASC"] - $ROW_VERB["RENDER_VERB_GENDER_FEM"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        echo "<td width=100 valign=top>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Person</b></td></tr>";
        echo "<tr><td width=40>1st:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB 1P]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_PERSON_1ST"]) . "</a></td></tr>";
        echo "<tr><td>2nd:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB 2P]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_PERSON_2ND"]) . "</a></td></tr>";
        echo "<tr><td>3rd:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB 3P]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_PERSON_3RD"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        echo "<td width=100 valign=top>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Number</b></td></tr>";
        echo "<tr><td width=90>Singular:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB SINGULAR]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_NUMBER_SINGULAR"]) . "</a></td></tr>";
        echo "<tr><td>Dual:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB DUAL]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_NUMBER_DUAL"]) . "</a></td></tr>";
        echo "<tr><td>Plural:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[VERB PLURAL]' class=linky>" . number_format($ROW_VERB["RENDER_VERB_NUMBER_PLURAL"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        echo "</tr>";
    }

    if ($type_noun > 0)
    {
        $noun_result = db_query("SELECT * FROM `ROOT-LIST` WHERE BINARY(`ENGLISH`)='$ROOT_ESCAPED'");
        $ROW_NOUN    = db_return_row($noun_result);

        echo "<tr><td colspan=" . (3 + ($type_verb > 0)) . " align=center bgcolor=#e0e0e0><b>Noun: <a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN]' class=linky>" . number_format($type_noun) . "</a></b></td></tr>";
        echo "<tr>";

        echo "<td width=100>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Case</b></td></tr>";
        echo "<tr><td width=90>Nominative:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN NOMINATIVE]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_CASE_NOM"]) . "</a></td></tr>";
        echo "<tr><td>Accusative:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN ACCUSATIVE]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_CASE_ACC"]) . "</a></td></tr>";
        echo "<tr><td>Genitive:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN NOMINATIVE]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_CASE_GEN"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        echo "<td width=100 valign=top>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Gender</b></td></tr>";
        echo "<tr><td width=90>Masculine:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN MASCULINE]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_GENDER_MASC"]) . "</a></td></tr>";
        echo "<tr><td>Feminine:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN FEMININE]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_GENDER_FEM"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        echo "<td width=100 valign=top>";
        echo "<table width=100%>";
        echo "<tr><td align=center colspan=2><b>Number</b></td></tr>";
        echo "<tr><td width=90>Singular:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN SINGULAR]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_NUMBER_SINGULAR"]) . "</a></td></tr>";
        echo "<tr><td>Dual:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN DUAL]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_NUMBER_DUAL"]) . "</a></td></tr>";
        echo "<tr><td>Plural:</td><td align=left><a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT) . "@[NOUN PLURAL]' class=linky>" . number_format($ROW_NOUN["RENDER_NOUN_NUMBER_PLURAL"]) . "</a></td></tr>";
        echo "</table>";
        echo "</td>";

        // pad out the table if needed
        if ($cols == 4)
        {
            echo "<td>&nbsp;</td>";
        }
    }

    echo "</table><BR>";
}

// FORMULA LIST

$count_root_formula = db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`Element1` = '$ROOT_ESCAPED' OR `Element2` = '$ROOT_ESCAPED' OR `Element3` = '$ROOT_ESCAPED' OR `Element4` = '$ROOT_ESCAPED' OR `Element5` = '$ROOT_ESCAPED') AND `TYPE` LIKE '%ROOT%' AND `OCCURRENCES`>1 AND `LENGTH`>2");

$count_lemma_formula = db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`Element1` = '$ROOT_ESCAPED' OR `Element2` = '$ROOT_ESCAPED' OR `Element3` = '$ROOT_ESCAPED' OR `Element4` = '$ROOT_ESCAPED' OR `Element5` = '$ROOT_ESCAPED') AND `TYPE`='LEMMA' AND `OCCURRENCES`>1 AND `LENGTH`>2");

if ($count_root_formula > 0)
{
    echo "<table border=1 width=600 cellspacing=0 cellpadding=8><tr><td colspan=3 align=center bgcolor=#b0b0b0>";
    echo "<b>Formula</b>";
    echo "</td></tr>";
    echo "<tr><td colspan=4 bgcolor=#e0e0e0 align=center>Appears in " . number_format($count_root_formula) . " Root-Based Formulae";
    if ($count_lemma_formula > 0)
    {
        echo "<br>and lies behind " . number_format($count_lemma_formula) . " Lemma-Based Formulae";
    }

    echo "</td></tr>";
    echo "<tr><td align=center><b>Root Formulae</b></td><td align=center><b>Root-Plus-Particle-Pronoun Formulae</b></td><td align=center><b>Lemma Formulae</b></td></tr>";
    echo "<tr>";

    // ROOT-PLUS-PARTICLE/PRONOUN FORMULAE COUNT

    echo "<td valign=top>";

    echo "Length 3: <a href='formulae/list_formulae.php?L=3&TYPE=ROOT&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT' AND `LENGTH`=3")) . "</a>";

    echo "<br>Length 4: <a href='formulae/list_formulae.php?L=4&TYPE=ROOT&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT' AND `LENGTH`=4")) . "</a>";

    echo "<br>Length 5: <a href='formulae/list_formulae.php?L=5&TYPE=ROOT&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT' AND `LENGTH`=5")) . "</a>";
    echo "</td>";

    // ROOT-PLUS-PARTICLE/PRONOUN FORMULAE COUNT

    echo "<td valign=top>";

    echo "Length 3: <a href='formulae/list_formulae.php?L=3&TYPE=ROOT-ALL&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT-ALL' AND LENGTH=3")) . "</a>";

    echo "<BR>Length 4: <a href='formulae/list_formulae.php?L=4&TYPE=ROOT-ALL&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT-ALL' AND LENGTH=4")) . "</a>";

    echo "<BR>Length 5: <a href='formulae/list_formulae.php?L=5&TYPE=ROOT-ALL&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`FORMULA` LIKE '$ROOT_ESCAPED+%' OR `FORMULA` LIKE '%+$ROOT_ESCAPED' OR `FORMULA` LIKE '%+$ROOT_ESCAPED+%') AND `OCCURRENCES`>1 AND `TYPE`='ROOT-ALL' AND LENGTH=5")) . "</a>";

    echo "</td>";

    // LEMMA FORMULAE COUNT

    echo "<td valign=top>";

    echo "Length 3: <a href='formulae/list_formulae.php?L=3&TYPE=LEMMA&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`Element1` = '$ROOT_ESCAPED' OR `Element2` = '$ROOT_ESCAPED' OR `Element3` = '$ROOT_ESCAPED') AND `LENGTH`=3 AND `TYPE`='LEMMA' AND `OCCURRENCES`>1")) . "</a>";

    echo "<br>Length 4: <a href='formulae/list_formulae.php?L=4&TYPE=LEMMA&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`Element1` = '$ROOT_ESCAPED' OR `Element2` = '$ROOT_ESCAPED' OR `Element3` = '$ROOT_ESCAPED' OR `Element4` = '$ROOT_ESCAPED') AND `LENGTH`=4 AND `TYPE`='LEMMA' AND `OCCURRENCES`>1")) . "</a>";

    echo "<br>Length 5: <a href='formulae/list_formulae.php?L=5&TYPE=LEMMA&ROOT=" . htmlentities($ROOT) . "' class=linky>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE (`Element1` = '$ROOT_ESCAPED' OR `Element2` = '$ROOT_ESCAPED' OR `Element3` = '$ROOT_ESCAPED' OR `Element4` = '$ROOT_ESCAPED' OR `Element5` = '$ROOT_ESCAPED') AND `LENGTH`=5 AND `TYPE`='LEMMA' AND `OCCURRENCES`>1")) . "</a>";
    echo "</td>";

    echo "</tr>";
    echo "</table><br>";
}

// GLOSS LIST

echo "<table border=1 width=600 cellspacing=0 cellpadding=8><tr><td colspan=2 align=center bgcolor=#b0b0b0>";
echo "<b>Glosses for this Root</b>";
echo "</td></tr>";

$result = db_query("SELECT DISTINCT(`GLOSS`), COUNT(*) NUM FROM `QURAN-DATA` WHERE BINARY(`QTL-ROOT`)='$ROOT_ESCAPED' GROUP BY `GLOSS` ORDER BY NUM DESC");

echo "<tr>";

echo "<td valign=top>";

$second_column_starts_at = intval((db_rowcount($result) / 2) + 1);

for ($i = 0; $i < db_rowcount($result); $i++)
{
    $newline = "<br>";

    if ($i == 0)
    {
        $newline = "";
    }

    // break out the second column at the right point

    if ($i == $second_column_starts_at)
    {
        echo "</td><td valign=top>";
        $newline = "";
    }

    $ROW = db_return_row($result);

    echo "$newline<a href='verse_browser.php?S=ROOT:" . urlencode($ROOT) . " AND GLOSS:\"" . urlencode($ROW["GLOSS"]) . "\"' class=linky>" . htmlentities($ROW["GLOSS"]) . "<span class=smaller_text_for_mini_dialogs> (x" . number_format($ROW["NUM"]) . ")</span></a>";
}

echo "</td>";

echo "</tr>";

echo "</table><br>";

// VERSE LIST

echo "<table border=1 width=600 cellspacing=0 cellpadding=8><tr><td align=center bgcolor=#b0b0b0>";
echo "<b>Verse List</b>";
echo "</td></tr>";

echo "<tr><td>";

$result = db_query("SELECT DISTINCT(`SURA-VERSE`), `SURA`, `VERSE` FROM `QURAN-DATA` WHERE BINARY(`QTL-ROOT`)='$ROOT_ESCAPED' GROUP BY `SURA-VERSE` ORDER BY `SURA`, `VERSE`");

for ($i = 0; $i < db_rowcount($result); $i++)
{
    // grab next database row
    $ROW = db_return_row($result);
    if ($i > 0)
    {
        echo "; ";
    }
    echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:" . $ROW["SURA"] . ", V:" . $ROW["VERSE"] . ",ROOT:'$ROOT_ESCAPED'}}\">";
    echo "<a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT . " RANGE:" . $ROW["SURA-VERSE"]) . "' class=linky>";
    echo $ROW["SURA-VERSE"];
    echo "</a>";
    echo "</span>";
}

echo "<p align=center><a href='exhaustive_root_references.php?ROOT=" . $_GET["ROOT"] . "' class=linky-parsed-word>(Examine All Occurrences)</a></p>";

echo "</td></tr></table><br>";

// generate a 'back' button
if (isset($_GET["BACK"]))
{
    echo "<a href='javascript:history.back();'><button>" . $_GET["BACK"] . "</button></a>";
}

echo "<a href='examine_root.php'><button>Examine Another Root</button></a>";

include "library/footer.php";

?>

</body>

	<script type="text/javascript">
  $(function() {
    Tipped.create('.chart-tip', {position: 'right', showDelay: 800, skin: 'light', close: true});
    Tipped.create('.loupe-tooltip', {position: 'left', maxWidth: 300, skin: 'light'});
  });
</script>

</html>