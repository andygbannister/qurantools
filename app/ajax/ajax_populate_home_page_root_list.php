<?php

// This populates the root list/picker on the home page
// We do it via an AJAX call as otherwise it can slow the page slightly
// causing the footer to flicker annoyingly

require_once '../library/config.php';
require_once 'library/functions.php';

echo "<p>Click on a root to add it to your current search above.</p>";

$result = db_query("SELECT * FROM `ROOT-LIST` ORDER BY `ROOT-LIST`.`ARABIC` ASC");

// scroller div
echo "<div id='roots-scroller' class='tipped-scroller'>";

echo "  <table>";

$previous_letter = "";

$columns   = 9;
$col_count = 0;

for ($i = 0; $i < db_rowcount($result); $i++)
{
    // grab next database row
    $ROW = db_return_row($result);

    // new row
    if (mb_substr($ROW["ARABIC"], 0, 1) != $previous_letter)
    {
        $previous_letter = mb_substr($ROW["ARABIC"], 0, 1);

        if ($col_count > 0)
        {
            echo "</tr>";
        }
        echo "<tr class='header'>";
        $arabic_letter = mb_substr($ROW["ARABIC"], 0, 1);
        echo "<td colspan=" . ($columns + 1) . "><div class='title'>$arabic_letter <font size='-1'>(";

        switch ($arabic_letter)
        {
            case "ش":
                echo "sh";
                break;

            case "ث":
                echo "v, th";
                break;

            case "خ":
                echo "x, kh";
                break;

            default:
                echo mb_substr($ROW["ENGLISH TRANSLITERATED"], 0, 1);
        }

        echo ")</font>" . "</div></td>";
        echo "</tr>";
        $col_count = 0;
    }

    if ($col_count == 0)
    {
        echo "<tr>";
    }

    echo "<td>";

    echo "<span title='" . htmlentities($ROW["MEANING"]) . "'>";

    echo "<a href='#' class='linky' onclick=\"AddText('" . $ROW["ARABIC"] . "');\">";
    echo $ROW["ARABIC"];
    echo "</a>";

    echo "<a href='#' class='linky' onclick=\"AddText('" . addslashes($ROW["ENGLISH TRANSLITERATED"]) . "');\">";

    echo " (" . htmlentities($ROW["ENGLISH TRANSLITERATED"]) . ")";
    echo "</a>";

    echo "</span>";

    echo "</td>";

    $col_count++;
    if ($col_count > $columns)
    {
        $col_count = 0;
        echo "</tr>";
    }
}

if ($col_count > 0)
{
    echo "</tr>";
}
echo "</table>";

echo "</div>";
