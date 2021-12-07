<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// fetch the source we want

$source_ID = 1;

if (isset($_GET["ID"]))
{
    $source_ID = $_GET["ID"];
}

$result_sources = db_query(
    "SELECT * FROM `INTERTEXTUAL LINKS` 
		LEFT JOIN `INTERTEXTUAL SOURCES` ON `SOURCE ID`=`SOURCE` 
			WHERE `INTERTEXT ID`=" . db_quote($source_ID)
);

$ROW_SOURCE_DATA = db_return_row($result_sources);

// WORK OUT IF WE ARE RUNNING IN LIGHTVIEW MODE (I.E. A POP OVER WINDOW)
$LIGHTVIEW = false;
if (isset($_GET["LIGHTVIEW"]))
{
    $LIGHTVIEW = ($_GET["LIGHTVIEW"] == "YES");
}

    ?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title($ROW_SOURCE_DATA["SOURCE NAME"] . " " . $ROW_SOURCE_DATA["SOURCE REF"]);
        ?>

	</head>
		
	<body class='qt-site' style='background-color: white;'>
<main class='qt-site-content'>
	<?php

    // menubar

    // include "../library/menu.php";

    // When running in Lightview (pop up) mode, provide an icon to pop the window into its own discrete tab

    if ($LIGHTVIEW)
    {
        echo "<div align=center>";
        echo "<span style='float:right; margin-top:-10px;' title='Open this text in a new window'><a href='/intertextuality/intertextual_viewer.php?ID=$source_ID' target='_blank'><img src='../images/expand.png'></a></span>";
    }
    else
    {
        include "../library/menu.php";
    }

echo "<div align=center>";

echo "<h1 class=page-title-text>" . htmlentities($ROW_SOURCE_DATA["SOURCE NAME"]) . " <a href='/charts/chart_intertextual_links_per_source.php'><img src='/images/stats.gif'></a></h1>";

if ($ROW_SOURCE_DATA["SOURCE ALTERNATIVE NAME"] != "")
{
    echo "<h4 class='button-block-with-spacing'>(Also known as <i>" . $ROW_SOURCE_DATA["SOURCE ALTERNATIVE NAME"] . ")</I></h4><br>";
}

echo "<h4 class='button-block-with-spacing'>" . $ROW_SOURCE_DATA["SOURCE DATE"] . " &nbsp;&nbsp;~&nbsp;&nbsp; " . $ROW_SOURCE_DATA["SOURCE LANGUAGE"] . "</h4>";

echo "<hr>";

echo "<h3>" . htmlentities($ROW_SOURCE_DATA["SOURCE REF"]) . "</h2>";

echo "</div>";

echo "<div class='source-document-text'>";

echo "<p>";

// create verse numbers properly

$text = preg_replace("/V(\d+) /m", "<sup>\\1</sup>&nbsp;", $ROW_SOURCE_DATA["TEXT"]);

// form proper paragraphs

$text = preg_replace("/\r\n|\r|\n/", '</p><p>', $text);

echo $text;

echo "</p>";

echo "<hr>";

// figure out how many verses/passages use this source

echo "<span class=green-text>This passage has intertextual connections to: ";

$result_verse = db_query(
    "SELECT * FROM `INTERTEXTUAL LINKS` 
			WHERE `SOURCE`='" . db_quote($ROW_SOURCE_DATA["SOURCE"]) . "' AND `SOURCE REF`='" . db_quote($ROW_SOURCE_DATA["SOURCE REF"]) . "' ORDER BY `SURA`"
);

for ($i = 0; $i < db_rowcount($result_verse); $i++)
{
    // grab next database row
    $ROW_VERSE = db_return_row($result_verse);
    if ($i > 0)
    {
        echo "; ";
    }
    // echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:".$ROW["SURA"].", V:".$ROW["$ROW_VERSE"]."'}}\">";

    if ($ROW_VERSE["START VERSE"] == $ROW_VERSE["END VERSE"])
    {
        echo "<a href='/verse_browser.php?V=" . $ROW_VERSE["SURA"] . ":" . $ROW_VERSE["START VERSE"] . "' class=linky-green>";
        echo $ROW_VERSE["SURA"] . ":" . $ROW_VERSE["START VERSE"];
    }
    else
    {
        echo "<a href='/verse_browser.php?V=" . $ROW_VERSE["SURA"] . ":" . $ROW_VERSE["START VERSE"] . "-" . $ROW_VERSE["END VERSE"] . "' class=linky-green>";
        echo $ROW_VERSE["SURA"] . ":" . $ROW_VERSE["START VERSE"] . "-" . $ROW_VERSE["END VERSE"];
    }
    echo "</a>";
    // echo "</span>";
}

echo "</span><hr>";

if ($ROW_SOURCE_DATA["BIBLIOGRAPHY"] != "")
{
    echo "<span class=smaller_text_for_mini_dialogs>";
    echo "<p><b>Further Reading</b></p>";
    echo $ROW_SOURCE_DATA["BIBLIOGRAPHY"];
    echo "</span>";
    echo "<hr>";
}

if ($ROW_SOURCE_DATA["SOURCE URL"] != "")
{
    echo "<div align=center><a href='" . $ROW_SOURCE_DATA["SOURCE URL"] . "' target='_blank'>";
    echo "<button class=smaller_text_for_mini_dialogs>View Entire Text</button>";
    echo "</a></div>";
}

echo "</div>";

// if running in our own window (i.e. not a Lightview pop up) then show the normal footer

if (!$LIGHTVIEW)
{
    include "../library/footer.php";
}
else
{
    echo "</div>";
}

?>

</body>

</html>