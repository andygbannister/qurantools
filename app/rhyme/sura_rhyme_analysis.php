<?php

// CLICKING ON COLUMN HEADS SEARCHES FOR ALL THOSE ENDINGS IN THE SURA
// POINT AT A CELL TO GET A POPUP VERSE BROWSER
// IN VERSE BROWSER, A WAY TO HIGHLIGHT ALL THE LAST 2 LETTERS OF EVERY WORD (OR COLOUR THEM ETC)

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/transliterate.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// filter by root count?

$filter_by_sura = 1;

if (isset($_GET["SURA"]))
{
    $filter_by_sura = db_quote($_GET["SURA"]);
}

    ?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Sura Rhyme Analysis");
        ?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

    <script>
        // button handling code

        function display_panel(panelSelect) {
            otherPanel = 1;

            if (panelSelect == 1) {
                otherPanel = 2;
            }

            // BUTTONS

            document.getElementById('panelButton' + panelSelect).style.fontWeight = 'bold';
            document.getElementById('panelButton' + otherPanel).style.fontWeight = 'normal';

            // DIVS

            $('#panel' + otherPanel).hide();
            $('#panel' + panelSelect).show();



        }
    </script>



</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

include "library/back_to_top_button.php";

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Sura Rhyme Analysis</h2>";

    echo "<div class='button-block-with-spacing'>";

    echo "<form action='sura_rhyme_analysis.php' method=GET>";

    echo "<div class='formulaic-pick-table'><table>";

    echo "<tr>";

    echo "<td>Sura to Analyse</td><td>";
    echo "<select name=SURA onChange='this.form.submit();'>";

    for ($i = 1; $i <= 114; $i++)
    {
        echo "<option value='$i'";
        if ($filter_by_sura == $i)
        {
            echo " selected";
        }
        echo ">$i</option>";
    }

    echo "</select>";

    echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'/charts/chart_rhyme_verse_endings.php?SURA=$filter_by_sura&VIEW=MINI', type: 'post'}\"><a href='/charts/chart_rhyme_verse_endings.php?SURA=$filter_by_sura'><img src='/images/stats.gif' valign=middle></a></span>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    echo "</div>";

    // buttons

echo "<div align=center style='margin-bottom:15px;'>";
echo "<hr style='width:750px;'>";
echo "<button id=panelButton1 onClick='display_panel(1);' style='font-weight:bold;'>Verse Ending Pattern Analysis</button>";
echo "<button id=panelButton2 onClick='display_panel(2);'>Sequential Verse Ending Pattern Analysis</button>";
echo "</div>";

    $result_sura = db_query("SELECT DISTINCT(`FINAL 2 LETTERS`) FROM `QURAN-VERSE-ENDINGS` WHERE `SURA`=$filter_by_sura GROUP BY `FINAL 2 LETTERS`");

    $verse_rhymes = [];

    // fill the rhyme array

    $rhymes_count = db_rowcount($result_sura);

    // $transliteration_class = "smaller_text_for_mini_dialogs";
    $transliteration_class = "";

    for ($i = 0; $i < $rhymes_count; $i++)
    {
        // grab next database row
        $ROW = $result_sura->fetch_assoc();

        $verse_rhymes[] = $ROW["FINAL 2 LETTERS"];
    }

    $result_verses = db_query("SELECT * FROM `QURAN-VERSE-ENDINGS` WHERE `SURA`=$filter_by_sura ORDER BY `VERSE`");

// data for panel 1

echo "<div id=panel1>";

    // table container div and fixed cols solves wide table persistent header issues
    echo "<div class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row' cellpadding=0>";

    echo "<tr class='table-header-row'>";

    echo "<th rowspan=2 width=40>Reference</th>";

    echo "<th colspan=$rhymes_count>Word Endings (Last Two Letters)</th>";

    echo "</tr><tr>";

    // put the endings in

    foreach ($verse_rhymes as $ending)
    {
        if ($ending == "**")
        {
            $ending = "-";
        }

        echo "<th rowspan=3 width=50 class=$user_preference_transliteration_style>";

        if ($ending == "-")
        {
            echo $ending;
        }
        else
        {
            echo "<a href='/verse_browser.php?S=[ENDS:$ending POSITION:FINAL] RANGE:$filter_by_sura' class=linky>";
            echo $ending;
            echo "</a>";
        }

        echo "</th>";
    }

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    // table data

    for ($i = 0; $i < db_rowcount($result_verses); $i++)
    {
        // grab next database row
        $ROW_VERSE = $result_verses->fetch_assoc();

        echo "<tr>";

        echo "<td align=center>";

        $start_verses = $ROW_VERSE["VERSE"];

        if ($start_verses < 1)
        {
            $start_verses = 1;
        }

        $end_verses = $ROW_VERSE["VERSE"] + 10;

        if ($end_verses > verses_in_sura($ROW_VERSE["SURA"]))
        {
            $end_verses = verses_in_sura($ROW_VERSE["SURA"]);
        }

        echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:" . $ROW_VERSE["SURA"] . ", V:" . $ROW_VERSE["VERSE"] . ", highlightSingleWord:'" . $ROW_VERSE["FINAL GLOBAL WORD NUMBER"] . "'}}\">";

        echo "<a href='/verse_browser.php?V=" . $ROW_VERSE["SURA"] . ":$start_verses-$end_verses&highlight_single_word=" . $ROW_VERSE["FINAL GLOBAL WORD NUMBER"] . "' class=linky>" . $ROW_VERSE["SURA-VERSE"] . "</a>";

        echo "</span>";

        echo "</td>";

        // figure out where in the array it comes

        $key = array_search($ROW_VERSE["FINAL 2 LETTERS"], $verse_rhymes);

        echo str_repeat("<td>&nbsp;</td>", $key);

        echo "<td align=center width=50 class='$transliteration_class $user_preference_transliteration_style'>";

        // print the word, with the last 2 letters coloured differently

        // unless it's quranic initials

        if ($ROW_VERSE["FINAL 2 LETTERS"] != "**")
        {
            echo htmlentities($ROW_VERSE["FINAL 2 LETTERS"]);
        }
        else
        {
            // echo htmlentities($ROW_VERSE["FINAL WORD"]);
            echo "-";
        }

        echo "</td>";

        echo str_repeat("<td>&nbsp;</td>", $rhymes_count - $key - 1);

        echo "</tr>";
    }

    echo "</tbody>";

    echo "</table>";

    echo "</div><br>";

echo "</div>";

// move back to the first record

db_goto($result_verses, 0);

echo "<div id=panel2>";

// table container div and fixed cols solves wide table persistent header issues
    echo "<div class='tableContainer'>";

    echo "<table class='hoverTable persist-area fixedTable'>";

    // table header

    echo "<thead class='persist-header table-header-row' cellpadding=0>";

    echo "<tr class='table-header-row'>";

    echo "<th><b>Reference</b></th>";
    echo "<th><b>Verse Ending (Rhyme Pattern)</b></th>";

    echo "</tr>";

    echo "<tbody>";

    // if the verse ending is the same in this verse as the next, we use
    // a line break and keep in the same table row

    $line_break_instead_of_table_row = false;

    for ($i = 0; $i < db_rowcount($result_verses); $i++)
    {
        // grab next database row
        $ROW_VERSE = $result_verses->fetch_assoc();

        if (!$line_break_instead_of_table_row)
        {
            echo "<tr>";
            echo "<td align=center>";
        }
        else
        {
            echo "<br>";
        }

        $start_verses = $ROW_VERSE["VERSE"];

        if ($start_verses < 1)
        {
            $start_verses = 1;
        }

        $end_verses = $ROW_VERSE["VERSE"] + 10;

        if ($end_verses > verses_in_sura($ROW_VERSE["SURA"]))
        {
            $end_verses = verses_in_sura($ROW_VERSE["SURA"]);
        }

        echo "<span class=loupe-tooltip data-tipped-options=\"zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:" . $ROW_VERSE["SURA"] . ", V:" . $ROW_VERSE["VERSE"] . ",highlightSingleWord:'" . $ROW_VERSE["FINAL GLOBAL WORD NUMBER"] . "'}}\">";

        echo "<a href='/verse_browser.php?V=" . $ROW_VERSE["SURA"] . ":$start_verses-$end_verses&highlight_single_word=" . $ROW_VERSE["FINAL GLOBAL WORD NUMBER"] . "' class=linky>" . $ROW_VERSE["SURA-VERSE"] . "</a>";

        echo "</span>";

        if ($ROW_VERSE["MATCHES NEXT VERSE ENDING"] > 0)
        {
            $line_break_instead_of_table_row = true;
        }
        else
        {
            $line_break_instead_of_table_row = false;
            echo "</td>";

            echo "<td align=center valign=top>";

            if ($ROW_VERSE["FINAL 2 LETTERS"] == "**")
            {
                echo "-";
            }
            else
            {
                echo "<a href='/verse_browser.php?S=[ENDS:$ending POSITION:FINAL] RANGE:$filter_by_sura' class=linky>";
                echo "--" . $ROW_VERSE["FINAL 2 LETTERS"];
                echo "</a>";
            }

            echo "</td>";

            echo "</tr>";
        }
    }

    if ($line_break_instead_of_table_row)
    {
        echo "</td></tr>";
    }

    echo "</tbody>";

    echo "</table>";

    echo "</div>";

echo "</div>";

// print footer

include "../library/footer.php";

?>
</body>

<script type="text/javascript">
    $(function() {
        Tipped.create('.chart-tip', {
            position: 'left',
            showDelay: 750,
            skin: 'light',
            close: true
        });
        Tipped.create('.loupe-tooltip', {
            position: 'left',
            maxWidth: 300,
            skin: 'light'
        });
        $('#panel2').hide();
    });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>

</html>