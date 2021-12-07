<?php

// regenerate the session

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once 'library/functions.php';

// saves the changes a user makes in the pop up tag editor for an individual verse

$SURA_VERSE = "";

foreach ($_GET as $key => $value)
{
    if ($key == "REF")
    {
        $SURA_VERSE = $value;
    }

    if (substr($key, 0, 5) == "CHECK")
    {
        $changeID = substr($key, 5, strlen($key));

        // delete

        if ($value == 0)
        {
            db_query("DELETE FROM `TAGGED-VERSES` WHERE `TAG ID`=" . db_quote($changeID) . " AND `User ID`=" . db_quote($_SESSION['UID']) . " AND `SURA-VERSE`='" . db_quote($SURA_VERSE) . "'", 0);
        }

        // insert

        if ($value == 1)
        {
            db_query("INSERT IGNORE INTO `TAGGED-VERSES`
			(`TAG ID`, `User ID`, `SURA-VERSE`) 
			VALUES
			(" . db_quote($changeID) . ", " . db_quote($_SESSION['UID']) . ", '" . db_quote($SURA_VERSE) . "')");
        }
    }
}

draw_tags_for_verse($SURA_VERSE);
