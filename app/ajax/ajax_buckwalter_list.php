<?php

// this script powers the Extra Characters List Pop Up on the home page
// regenerate the session

session_start();
session_regenerate_id();

// check we are being called by a logged in user

if (!isset($_SESSION["UID"]))
{
    exit;
}

// connect to database and load database etc

require_once '../library/config.php';
require_once 'library/functions.php';

$result = db_query("SELECT * FROM `BUCKWALTER-ENCODING`");

// set up page

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

echo "</head>";

echo "<body>";

echo "<div id='extra-characters'>";

echo "  <table class='hoverTable'>";

echo "    <tr>";

echo "      <th width=100>
              <b>Arabic Glyph</b>
            </th>";

echo "			<th width=210>
              <b>Latin<br>(Buckwalter Encoding)</b>
            </th>";

echo "      <th width=80>
							<b>Description</b>
						</th>";

echo "    </tr>";

for ($i = 0; $i < db_rowcount($result); $i++)
{
    // grab next database row
    $ROW = db_return_row($result);

    echo "    <tr>";

    // we use the onClick method below so it makes the whole cell clickable, not just the glyph/letter

    echo "      <td class='arabic-glyph' onClick=\"AddText('" . $ROW["GLYPH"] . "');\"";

    // try to position some of the odder glyphs lower

    if (strpos("!+:o~auNF", $ROW["ASCII"]) !== false)
    {
        echo " style='padding-top: 20px; padding-bottom: -20px;'";
    }

    // try to position some of the odder higher higher

    if (strpos("-];iK", $ROW["ASCII"]) !== false)
    {
        echo " style='padding-top: -15px; padding-bottom: 20px;'";
    }

    echo ">";

    echo "        <a href='#' class='linky'>";

    echo           $ROW["GLYPH"];
    echo "        </a>";
    echo "      </td>";

    echo "      <td class='buckwalter-encoding' onclick=\"AddText('" . addslashes($ROW["ASCII"]) . "');\">";
    echo "        <a href='#' class='linky'>";
    echo            $ROW["ASCII"] . "&nbsp;";
    echo "        </a>";
    echo "      </td>";

    echo "      <td class='description'>";
    echo "        <a href='#'  class='linky' onClick=\"AddText('" . $ROW["GLYPH"] . "');\">";
    echo            $ROW["DESCRIPTION"];
    echo "        </a>";
    echo "      </td>";

    echo "    </tr>";
}

echo "</table>";

echo "</div>";

echo "</body>";
echo "</html>";
