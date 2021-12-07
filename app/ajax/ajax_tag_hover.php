<?php

require_once '../library/config.php';
require_once 'library/functions.php';

$tag_name = db_return_one_record_one_field("SELECT `Tag Name` FROM `TAGS` WHERE `ID`=" . db_quote($_GET["T"]));

echo "<a href='verse_browser.php?S=TAG:" . urlencode("\"" . $tag_name . "\"") . "'><button class=smaller_text_for_mini_dialogs>Find All Verses With This Tag</button></a>";

echo "<button class=smaller_text_for_mini_dialogs onClick=\"$('#tag_" . str_ireplace(":", "v", $_GET["V"]) . "').load('ajax/ajax_tag_remove.php', {TAGID:'" . $_GET["T"] . "',VERSE:'" . $_GET["V"] . "'}); Tipped.hideAll();\">Remove Tag From Verse</button>";
