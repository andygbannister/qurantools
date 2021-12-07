<?php

// regenerate the session

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once 'library/functions.php';

$tag_list = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Tag Name`");

if (db_rowcount($tag_list) < 1)
{
    echo "<div align=center>";

    echo "<h3>Apply Tags To Verse</h3>";

    echo "<p>You haven't created any tags yet</p>";

    echo "<p><a href='tag_manager.php' class=linky-light>Click here</a> to create some</p>";

    echo "</div>";

    exit;
}
else
{
    echo "<b>Choose which tags are applied to Q. " . $_GET["V"] . " ...</b>";

    // inner div listing the tags (and will have scroll bars if the list is too long

    echo "<div class='tag-popup-panel'>";

    echo "<form id=TagToolBoxForm>";

    echo "<input type=hidden NAME=REF value=" . $_GET["V"] . ">";

    echo "<table>";

    for ($i = 0; $i < db_rowcount($tag_list); $i++)
    {
        $ROW = db_return_row($tag_list);

        echo "<tr>";

        echo "<td>";

        // hidden field with same name as checkbox, so a 0 value will be returned if it's not checked
        echo "<input type=hidden NAME=CHECK" . $ROW["ID"] . " value=0>";

        echo "<input type=checkbox NAME=CHECK" . $ROW["ID"] . " value=1";

        // is the tag applied to this verse

        $checked_count = db_return_one_record_one_field("SELECT COUNT(*) FROM `TAGGED-VERSES` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `SURA-VERSE`='" . db_quote($_GET["V"]) . "' AND `TAG ID`=" . db_quote($ROW["ID"]));

        if ($checked_count > 0)
        {
            echo " checked";
        }

        echo " >";

        echo "</td>";

        echo "<td>";

        draw_tag_lozenge($ROW["Tag Colour"], $ROW["Tag Lightness Value"]);

        echo "' onClick=\"$('input[name=CHECK" . $ROW["ID"] . "]').attr('checked', !($('input[name=CHECK" . $ROW["ID"] . "]').attr('checked')));\">";

        echo htmlentities($ROW["Tag Name"]) . "</span></td>";

        echo "</tr>";
    }

    echo "</table>";

    echo "</form>";

    echo "</div>";

    echo "<button class=smaller_text_for_mini_dialogs onClick='Tipped.hideAll();'>Cancel</button>";

    echo "<button class=smaller_text_for_mini_dialogs onClick=\"$('#tag_" . str_ireplace(":", "v", $_GET["V"]) . "').load('ajax/ajax_tag_save_changes.php',
    $('#TagToolBoxForm').serialize()); Tipped.hideAll();\">Apply Changes</button>";

    echo "<span style='float:right;'><a href='tag_manager.php'><button class=smaller_text_for_mini_dialogs>Create or Edit Tags</button></a></span>";
}
