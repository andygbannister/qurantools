<?php

echo "<div id='sura-picker-container'>";
echo "  <table>";

$rowCount = 0;
for ($i = 1; $i <= 114; $i++)
{
    $rowCount++;
    echo "      <td id='q" . $i . "' class='sura-picker-number' onClick=\"click_sura_number($i, " . verses_in_sura($i) . ");\">";
    echo "        <span class='suraPickerNumber'>$i</span>";
    echo "      </td>";

    if ($rowCount > 22)
    {
        echo "    </tr>";
        echo "    <tr>";
        $rowCount = 0;
    }
}

echo "    </tr>";
echo "  </table>";
echo "</div>";

echo "<div id='aya-picker-container'>";
echo "</div>";
