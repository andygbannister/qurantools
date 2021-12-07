<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

function build_form_row($row_number)
{
    global $sura_list_contents, $select_list_contents;

    echo "<table cellpadding=1 style='padding: 10px 10px 10px 10px;'>";
    echo "<tr>";

    echo "<td valign=top rowspan=4><b>Find</b></td>";

    echo "<td>";
    echo "<input type=radio name=FindWhatRadioGroup" . $row_number . " id=FindWhatRadio" . $row_number . "a value=1 checked onClick=\"$('#OPTION_LIST_" . $row_number . "').show(); $('#TEXT_FIELD_" . $row_number . "').hide(); $('#RootList" . $row_number . "').focus(); process_search(false);\"> An Arabic Root<br>";

    echo "</td>";

    echo "<td rowspan=4 width=20>&nbsp;</td>";

    echo "<td valign=top rowspan=4><b>Matching</b></td>";

    echo "<td valign=top rowspan=4>";

    echo "<div id=OPTION_LIST_" . $row_number . ">";

    echo "<select ID=RootList" . $row_number . " style='width:150px;' onChange='process_search(false);'>$select_list_contents</select>";

    echo "</div>";

    echo "<div id=TEXT_FIELD_" . $row_number . " style='display: none;'><INPUT ID=EnglishInput" . $row_number . " type=TEXT width=50 maxlength=50 placeholder='Text to find' onKeyUp='process_search(false);'></div>";

    echo "</td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td rowspan=3 valign=top>";

    echo "<input type=radio name=FindWhatRadioGroup" . $row_number . " id=FindWhatRadio" . $row_number . "b value=2 onClick=\"process_search(false); $('#OPTION_LIST_" . $row_number . "').hide(); $('#TEXT_FIELD_" . $row_number . "').show(); $('#EnglishInput" . $row_number . "').focus(); process_search(false);\"> English (Translation) Text</td>";

    echo "</table>";
}

// pre-load a string to use for the root option list
$select_list_contents = "";
$result               = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ARABIC`");
for ($i = 0; $i < db_rowcount($result); $i++)
{
    $ROW = db_return_row($result);
    // $select_list_contents.="<option value='".urlencode($ROW["ARABIC"])."'>".$ROW["ARABIC"]." (".transliterate_new($ROW["ENGLISH"]).")</option>";
    $select_list_contents .= "<option value='" . urlencode($ROW["ARABIC"]) . "'>" . transliterate_new($ROW["ENGLISH"]) . " (" . $ROW["ARABIC"] . ")</option>";
}

// pre-load a string to use for the sura option list
$sura_list_contents = "";
for ($i = 1; $i <= 114; $i++)
{
    $sura_list_contents .= "<option value=$i>$i</option>";
}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Easy Search");
        ?>

<script type="text/javascript">
  $(document).ready(function() {
	  
	// set up the row counter
	search_row_counter = 1;
    
  });
  
</script>
  
<script>

function process_search(load_new_page)
{	
	search_string = "";
	for (i = 1; i <= search_row_counter; i++)
	{
		if (i > 1)
		{
			search_string += " " + document.getElementById("logical_operator" + i).value + " ";	
		}
		
		// are we doing a root or an english word?
		
		if (document.getElementById("FindWhatRadio" + i + "a").checked)
		{				
			search_string += "ROOT:"+document.getElementById("RootList" + i).value;
		}
		else
		{
			if (document.getElementById("EnglishInput" + i).value !="")
			{
				search_string += "ENGLISH:"+document.getElementById("EnglishInput" + i).value;
			}
			else
			{
				search_string += "ENGLISH:TEXT";
			}
		}
	}
	
	// 	if we have more than one search term, best use parentheses for clarity
	
	if (search_row_counter > 1)
	{
		search_string = "(" + search_string + ")";
	}
	
	if (document.getElementById("WHERE2").checked)
	{
	search_string += " AND PROVENANCE:MECCAN";
	}
	
	if (document.getElementById("WHERE3").checked)
	{
	search_string += " AND PROVENANCE:MEDINAN";
	}
	
	if (document.getElementById("WHERE4").checked)
	{
	search_string += " RANGE:" + document.getElementById("SURA_LIST").value;
	}
	
	document.getElementById("build_search").innerHTML = "<a href='home.php?L=" + search_string + "' class=linky><font color=gray>" + decodeURI(search_string) + "</font></a>";
	
	if (load_new_page)
	{	
		window.location = "home.php?SEEK=" + search_string;	
	}
}
	
</script>

</head>
<body class='qt-site'>
<main class='qt-site-content'>
	
<?php

// menubar

include "library/menu.php";

echo "<div align=center><h2 class='page-title-text'>Easy Search</h2>";

$MAX_ROWS = 4;

for ($i = 1; $i <= $MAX_ROWS; $i++)
{
    echo "<div id='wrapper" . $i . "' style='margin-bottom:10px;";

    if ($i > 1)
    {
        echo "display: none;";
    }

    echo "'>";

    echo "<div id=operator_div" . $i . " style='display:inline-block; width:60px; vertical-align: top; margin-right:10px; margin-top:20px;'>";

    if ($i > 1)
    {
        echo "<select ID=logical_operator" . $i . " onChange='process_search(false);'>";
        echo "<option value=AND>AND</option>";
        echo "<option value=OR>OR</option>";
        echo "</select>";
    }

    echo "</div>";

    echo "<div id=search_construction" . $i . " style='display:inline-block; background-color: #f0f0f0; width:550px;'>";
    build_form_row($i);
    echo "</div>";

    echo "<div id=search_add_remove" . $i . " style='width:50px; display:inline-block; vertical-align: top; margin-left:10px; margin-top:20px;'>";

    // print the "delete row" button

    if ($i > 1)
    {
        echo "<span id=DELETE_BUTTON" . $i . " class=\"yellow-tooltip\" title=\"<font size=2>Delete this row from your search query</font>\"><a href='#' onClick=\"$('#wrapper" . $i . "').hide(); $('#ADD_BUTTON" . ($i - 1) . "').show(); $('#DELETE_BUTTON" . ($i - 1) . "').show(); search_row_counter--; process_search(false);\"><img src='images/minus_icon.png' style='background-color:red; margin-right:10px;'></a></span>";
    }

    // print the "add row" button

    if ($i < $MAX_ROWS)
    {
        echo "<span id=ADD_BUTTON" . $i . " class=\"yellow-tooltip\" title=\"<font size=2>Add another row to your search query (so you can search for multiple things at once).</font>\"><a href='#' onClick=\"$('#wrapper" . ($i + 1) . "').show(); $('#ADD_BUTTON" . $i . "').hide(); $('#DELETE_BUTTON" . $i . "').hide(); search_row_counter++; process_search(false);\"><img src='images/plus_icon.png' style='background-color:green;'></a></span>";
    }

    // print a "spacer" button if needed

    if ($i == 1)
    {
        echo "<a href='#'><img src='images/minus_icon.png' style='background-color:red; margin-right:10px; opacity: 0;'></a>";
    }

    if ($i == $MAX_ROWS)
    {
        echo "<a href='#'><img src='images/minus_icon.png' style='background-color:green; opacity: 0;'></a>";
    }

    echo "</div>";

    echo "</div>";
}

echo "<div id=search_where style='display:inline-block; background-color: #e0e0e0; width: 600px; padding: 10px 10px 10px 10px;'>";
echo "<b>Search in:</b> ";
echo "<input type=radio name=SEARCH_WHERE ID=WHERE1 VALUE=ALL checked> All Suras</input>";
echo "&nbsp;&nbsp;<input type=radio name=SEARCH_WHERE ID=WHERE2 VALUE=MECCAN onClick='process_search(false);'> Meccan Suras</input>";
echo "&nbsp;&nbsp;<input type=radio name=SEARCH_WHERE ID=WHERE3 VALUE=MEDINAN onClick='process_search(false);'> Medinan Suras</input>";
echo "&nbsp;&nbsp;<input type=radio name=SEARCH_WHERE ID=WHERE4 VALUE=ALL onClick='process_search(false);'> Just Sura</input>";
echo "&nbsp;&nbsp;<select ID=SURA_LIST onChange=\"WHERE4.checked=true; process_search(false);\">$sura_list_contents</select>";
echo "</div>";

echo "<div id=BUTTONS style='margin-top:15px;'>";

echo "<button ID=GO_BUTTON class='general-button' value=OK onClick='process_search(true);'>SEARCH</button>";

echo "<a href='home.php'><button ID=cancelButton style='margin-left:2px;' class='general-button' name=CANCEL>Cancel</button></a>";

echo "</div>";

echo "<div ID=build_search style='margin-top:20px; padding: 10px 10px 10px 10px; width: 600px; color: gray; border: 1px dashed black;' class=\"yellow-tooltip\" title=\"<font size=2>As you choose options above, your search will build here. This helps you see what Qur’an Tools search commands look like. You can perform more powerful queries by typing search commands; read about Qur’an Tools' search language <a href='docs/user_guide_advanced_verse_browser.php'>here</a>.</font>\">Your search will build here as you add search terms above.";
echo "</div>";

// print footer

include "library/footer.php";

?>

<!-- Convert tooltips to "Tipped" format -->

<script type="text/javascript">
  $(document).ready(function() {
    Tipped.create('.yellow-tooltip', {position: 'bottommiddle', maxWidth: 420, skin: 'lightyellow', showDelay: 1000});
  });
</script>

</body>

</html>