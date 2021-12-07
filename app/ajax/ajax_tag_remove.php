<?php

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once 'library/functions.php';

$SURA_VERSE = $_POST["VERSE"];
$TAGID      = $_POST["TAGID"];

$sql = "DELETE FROM `TAGGED-VERSES` WHERE `TAG ID`=" . db_quote($TAGID) . " AND `User ID`=" . db_quote($_SESSION['UID']) . " AND `SURA-VERSE`='" . db_quote($SURA_VERSE) . "'";

db_query($sql);

draw_tags_for_verse($SURA_VERSE);

// error_log("Tag ID is $TAGID", 0);
// error_log("Verse is $SURA_VERSE", 0);
// error_log("UID is ".$_SESSION['UID'], 0);
