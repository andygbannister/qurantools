<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

// set up preferences

$user_preference_highlight_colour                 = $logged_in_user["Preferred Highlight Colour"];
$user_preference_highlight_colour_lightness_value = $logged_in_user["Preferred Highlight Colour Lightness Value"];

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
        ?>

		<script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
		
		<script type="text/javascript" src="library/js/lightview/spinners/spinners.min.js"></script>
		<script type="text/javascript" src="library/js/lightview/lightview/lightview.js"></script>
		<link rel="stylesheet" type="text/css" href="library/js/lightview/css/lightview/lightview.css"/>
		
		<script type="text/javascript" src="/library/js/persistent_table_headers.js"></script>

		<script>
		
		function closeLightView()
		{
			Lightview.hide();
		}
			
    function reload_with_root(which_field)
   {
   	pass_root = "";
   	if (which_field == 1)
   	{
   		pass_root = document.getElementById("ROOT_TRANSLITERATED_LIST").value;	
   	}  	
   	else
   	{
   		pass_root = document.getElementById("ROOT_ARABIC_LIST").value;	
   	}
   	   	
   	window.location = "exhaustive_root_references.php?ROOT="+pass_root; 	
   	
   }
   
   function reload_with_lemma(which_field)
   {
   	pass_lemma = "";
   	if (which_field == 1)
   	{
   		pass_lemma = document.getElementById("LEMMA_TRANSLITERATED_LIST").value;	
   	}  	
   	else
   	{
   		pass_lemma = document.getElementById("LEMMA_ARABIC_LIST").value;	
   	}
   	   	
   	window.location = "exhaustive_root_references.php?LEMMA="+pass_lemma; 	
   	
   }
   
   
   </script> 
   
   <style>
		mark {
			background-color: 
			<?php
        echo $user_preference_highlight_colour . ";";
        echo "color: ";

        if ($user_preference_highlight_colour_lightness_value < 100)
        {
            echo "white";
        }
        else
        {
            echo "black";
        }
        ?>
		}
	</style>
      
  <?php

  $ROOT = "";
    if (isset($_GET["ROOT"]))
    {
        $ROOT = $_GET["ROOT"];
    }

    $LEMMA = "";
    if (isset($_GET["LEMMA"]))
    {
        $LEMMA = $_GET["LEMMA"];
    }

    // if no root passed, offer the drop down list
    if ($ROOT == "" && $LEMMA == "")
    {
        window_title("Exhaustive List of References for Root or Lemma");

        echo "</head><body class='qt-site'><main class='qt-site-content'>";

        include "library/menu.php";

        // ROOT LIST

        echo "<div align=center><h2 class='page-title-text'>Exhaustive List of References For Arabic Root</h2>";

        echo "Please begin by choosing a <b>root</b> to analyse (from either the Arabic or transliteration menus below):";

        echo "<form action='exhaustive_root_references.php' method=POST>";

        echo "<div style='border:1px solid black; width:450px; background-color: #f4f4f4; margin-top: 10px;'><table cellpadding=4>";

        echo "<tr>";

        echo "<td>Transliterated Roots:</td><td>";

        echo "<select name=ROOT_TRANSLITERATED_LIST ID=ROOT_TRANSLITERATED_LIST onChange='reload_with_root(1);'>";

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

        echo "<select name=ROOT_ARABIC_LIST ID=ROOT_ARABIC_LIST onChange='reload_with_root(2);'>";

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

        // LEMMA LIST

        echo "Or, instead, choose a <b>lemma</b> to analyse:";

        echo "<form action='exhaustive_root_references.php' method=POST>";

        echo "<div style='border:1px solid black; width:450px; background-color: #f4f4f4; margin-top: 10px;'><table cellpadding=4>";

        echo "<tr>";

        echo "<td>Transliterated Lemmata:</td><td>";

        echo "<select name=LEMMA_TRANSLITERATED_LIST ID=LEMMA_TRANSLITERATED_LIST onChange='reload_with_lemma(1);'>";

        $result = db_query("SELECT * FROM `LEMMA-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

        echo "<option value=''>Choose Lemma</option>";

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            $ROW = db_return_row($result);
            echo "<option value='" . urlencode($ROW["ENGLISH"]) . "'>" . transliterate_new($ROW["ENGLISH"]) . "</option>";
        }
        echo "</select>";

        echo "</td></tr>";

        echo "<tr>";

        echo "<td>Arabic Lemmata:</td><td>";

        echo "<select name=LEMMA_ARABIC_LIST ID=LEMMA_ARABIC_LIST onChange='reload_with_lemma(2);'>";

        echo "<option value=''>Choose Lemma</option>";

        $result = db_query("SELECT * FROM `LEMMA-LIST` ORDER BY `ENGLISH TRANSLITERATED`");

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

    if ($ROOT != "")
    {
        window_title("Exhaustive List of References for Root: " . return_arabic_word($ROOT) . " (" . htmlentities(transliterate_new($ROOT)) . ")");
    }
    else
    {
        window_title("Exhaustive List of References for Lemma: " . return_arabic_word($LEMMA) . " (" . htmlentities(transliterate_new($LEMMA)) . ")");
    }

  ?>
  
</title>
</head>
<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "library/back_to_top_button.php";

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>";

if ($ROOT != "")
{
    echo "Exhaustive List of References for Root: " . return_arabic_word($ROOT) . " <span class=$user_preference_transliteration_style>(" . htmlentities(transliterate_new($ROOT)) . "</span>)";
}
else
{
    echo "Exhaustive List of References for Lemma: " . return_arabic_word($LEMMA) . " (<span class=$user_preference_transliteration_style>" . htmlentities(transliterate_new($LEMMA)) . "</span>)";
}

echo "</h2>";

// VERSE LIST

if ($ROOT != "")
{
    $sql = "SELECT DISTINCT(CONCAT(`SURA-VERSE`, ':',`WORD`)) ref, `SURA`, `VERSE`, `SURA-VERSE`, `WORD`, `GLOBAL WORD NUMBER`, (SELECT MAX(`WORD`) FROM `QURAN-DATA` T2 WHERE T1.`SURA-VERSE`=T2.`SURA-VERSE`) maxword FROM `QURAN-DATA` T1 WHERE BINARY(`QTL-ROOT`)='" . db_quote($ROOT) . "' GROUP BY ref ORDER BY `SURA`, `VERSE`";
}
else
{
    $sql = "SELECT DISTINCT(CONCAT(`SURA-VERSE`, ':',`WORD`)) ref, `SURA`, `VERSE`, `SURA-VERSE`, `WORD`, `GLOBAL WORD NUMBER`, (SELECT MAX(`WORD`) FROM `QURAN-DATA` T2 WHERE T1.`SURA-VERSE`=T2.`SURA-VERSE`) maxword FROM `QURAN-DATA` T1 WHERE BINARY(`QTL-LEMMA`)='" . db_quote($LEMMA) . "' GROUP BY ref ORDER BY `SURA`, `VERSE`";
}

$result = db_query($sql);

echo "<div id=TableView>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th bgcolor=#c0c0c0 width=70><b>Ref</b><br>";
    echo "</th>";

     echo "<th bgcolor=#c0c0c0 width=450 align=right><b>Arabic</b><br>";
    echo "</th>";

    echo "<th bgcolor=#c0c0c0 width=450 align=left><b>Transliteration</b><br>";
    echo "</th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    // table data

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        // link to expand the verse
        $link = "<a href='verse_browser.php?V=" . $ROW["ref"] . "' class=linky>";

        echo "<tr>";

        echo "<td align=center width=70>";

        /*
                echo $link;
                echo $ROW["ref"];
                echo "</a>";
        */

        echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:" . $ROW["SURA"] . ", V:" . $ROW["VERSE"] . ", highlightSingleWord:" . $ROW["GLOBAL WORD NUMBER"] . "}}\">";

        if ($LEMMA == "")
        {
            echo "<a href='verse_browser.php?S=ROOT:" . htmlentities($ROOT . " RANGE:" . $ROW["SURA-VERSE"]) . "' class=linky>";
        }
        else
        {
            echo "<a href='verse_browser.php?S=LEMMA:" . htmlentities($LEMMA . " RANGE:" . $ROW["SURA-VERSE"]) . "' class=linky>";
        }
        echo $ROW["ref"];
        echo "</a>";
        echo "</span>";

        echo "</td>";

        // work out which words to show

        $START_WORD = $ROW["WORD"] - 3;

        $END_WORD = $ROW["WORD"] + 3;

        // adjust if we have undershot word 1

        if ($START_WORD < 1)
        {
            $END_WORD += (1 - $START_WORD);
            $START_WORD = 1;
        }

        // adjust if we have overshot the last word

        if ($END_WORD > $ROW["maxword"])
        {
            $START_WORD -= ($END_WORD - $ROW["maxword"]);
            $END_WORD = $ROW["maxword"];

            if ($START_WORD < 1)
            {
                $START_WORD = 1;
            }
        }

        // arabic

        echo "<td width=450 align=right class='ayaMedium right-to-left'>";

        echo $link;

        // opening ellipsis
        echo $START_WORD > 1 ? "... " : "";

        $text_result = db_query("SELECT DISTINCT(`GLOBAL WORD NUMBER`), `TRANSLITERATED`, `RENDERED ARABIC`, `WORD` FROM `QURAN-DATA` WHERE `SURA-VERSE`='" . db_quote($ROW["SURA-VERSE"]) . "' AND `WORD` >= $START_WORD AND `WORD`<=$END_WORD");

        for ($j = 0; $j < db_rowcount($text_result); $j++)
        {
            $ROWT = db_return_row($text_result);

            echo $j > 0 ? " &nbsp;" : "";

            if ($ROW["WORD"] == $ROWT["WORD"])
            {
                echo "<mark>";
            }

            echo $ROWT["RENDERED ARABIC"];

            if ($ROW["WORD"] == $ROWT["WORD"])
            {
                echo "</mark>";
            }
        }

        // closing ellipsis
        echo $END_WORD < $ROW["maxword"] ? " ..." : "";

        echo "</a></td>";

        // transliterated

        echo "<td width=450 class=transcriptionMedium>";

        db_goto($text_result, "FIRST");

        echo $link;

        // opening ellipsis
        echo $START_WORD > 1 ? "... " : "";

        echo "<span class=$user_preference_transliteration_style>";

        for ($j = 0; $j < db_rowcount($text_result); $j++)
        {
            $ROWT = db_return_row($text_result);

            echo $j > 0 ? " " : "";

            if ($ROW["WORD"] == $ROWT["WORD"])
            {
                echo "<mark>";
            }

            echo $ROWT["TRANSLITERATED"];

            if ($ROW["WORD"] == $ROWT["WORD"])
            {
                echo "</mark>";
            }
        }

        echo "</span>";

        // closing ellipsis
        echo $END_WORD < $ROW["maxword"] ? " ..." : "";

        echo "</a></td>";

        echo "</tr>";
    }

    echo "</tbody>";

    echo "<tr>";

    echo "<td colspan=3 align=center>";

    echo "<b>" . number_format(db_rowcount($result)) . " occurrence" . plural(db_rowcount($result)) . " of this " . ($ROOT != "" ? "root" : "lemma") . "</b>";

    echo "</td>";

    echo "</tr>";

    echo "</table><br>";

if ($ROOT != "")
{
    echo "<a href='examine_root.php?ROOT=" . $_GET["ROOT"] . "'><button>Examine This Root</button></a>";
}

// generate a 'back' button
if (isset($_GET["BACK"]))
{
    echo "<a href='javascript:history.back();'><button>" . $_GET["BACK"] . "</button></a>";
}

echo "<a href='exhaustive_root_references.php'><button>Exhaustively List Another Root or Lemma</button></a>";

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