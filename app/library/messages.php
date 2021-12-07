<?php

// this little module allows us to insert messages for individual users (or groups thereof)
// onto the home page. Right now it's basic, but it's better to have this factored out
// than embedded directly into home.php.

// messages to users

// messages block (used especially for new features)

$result = db_query("SELECT * FROM `MESSAGES` WHERE `EXPIRY DATE` >= CURRENT_DATE AND `RETIRED`!='Y' AND `MESSAGE`!='' ORDER BY `EXPIRY DATE` DESC");

if (db_rowcount($result) > 0)
{
    echo "<div class=message_block_home_page>";

    for ($i = 0; $i <= db_rowcount($result); $i++)
    {
        $ROW = db_return_row($result);
        echo "<p>" . $ROW["MESSAGE"] . "</p>";
    }

    echo "</div>";
}
