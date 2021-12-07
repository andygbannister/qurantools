<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

    ?><html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Translation Tagging Statistics");
        ?>

		<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
       
		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>
      		  
	</head>
<body class='qt-site'>
<main class='qt-site-content'>
	<?php

        include "library/back_to_top_button.php";

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Translation Tagging Statistics</h2>";

echo "<br><table class='hoverTable'>";

        echo "<thead>";

        echo "<th><b>Translation</b></th>";
        echo "<th><b>Verses Tagged</b></th>";
        echo "<th><b>Verses Remaining</b></th>";
        echo "<th><b>Next Verse To Tag</b></th>";
        echo "<th><b>% Complete</b></th>";

        echo "</thead>";

        $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`");

        for ($j = 0; $j < db_rowcount($result_translation); $j++)
        {
            $ROW = db_return_row($result_translation);

            echo "<tr>";

            echo "<td>" . $ROW["TRANSLATION NAME"] . "</td>";

            $complete = db_return_one_record_one_field("SELECT COUNT(*)  FROM `QURAN-TRANSLATION` WHERE `Translator` LIKE '" . db_quote($ROW["TRANSLATION NAME"]) . "' AND `Text` LIKE '%</e>%'");

            $incomplete = db_return_one_record_one_field("SELECT COUNT(*)  FROM `QURAN-TRANSLATION` WHERE `Translator` LIKE '" . db_quote($ROW["TRANSLATION NAME"]) . "' AND `Text` NOT LIKE '%</e>%'");

            echo "<td align=center>" . number_format($complete) . "</td>";
            echo "<td align=center>" . number_format($incomplete) . "</td>";

            // next verse to tag
            echo "<td align=center>";

            if ($incomplete > 0)
            {
                $next_verse = db_return_one_record_one_field("SELECT CONCAT(`Sura`, ':', `Verse`) FROM `QURAN-TRANSLATION` WHERE `Translator` LIKE '" . db_quote($ROW["TRANSLATION NAME"]) . "' AND `Text` NOT LIKE '%</e>%' ORDER BY `Sura`, `Verse` LIMIT 0,1");

                echo "<a href='/verse_browser.php?V=$next_verse&T=" . $ROW["TRANSLATION ID"] . "&TTM=Y' class=linky>$next_verse</a>";
            }
            else
            {
                echo "N/A";
            }

            echo "</td>";

            echo "<td align=center>" . number_format(($complete * 100) / ($complete + $incomplete), 2) . "%</td>";

            echo "</tr>";
        }

        echo "</table>";

    include "library/footer.php";

?>
	</body>
</html>