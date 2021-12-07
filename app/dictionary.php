<?php

// Qur’an Tools Dictionary Page

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/transliterate.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// how many rows per page to show (too many, and the loupe viewer and other functions slow down)
$ITEMS_PER_PAGE = 250;
$CURRENT_PAGE   = 1;

// GET CURRENT PAGE

if (isset($_GET["PAGE"]))
{
    $CURRENT_PAGE = $_GET["PAGE"];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET["PAGE"] = "";
}

// have we saved or are we editing Lane lexicon links?

$show_edit_lane_fields = false;
// $extra_lane_sql_bit = " AND `MEANING`='LEMMA'";
$extra_lane_sql_bit = "";

$lane_autofocus_done = false;

if ($_SESSION["UID"] == 1 && $show_edit_lane_fields)
{
    if (isset($_GET["LANE_PAGE"]) && isset($_GET["LANE_ROOT"]))
    {
        if ($_GET["LANE_PAGE"] > 0)
        {
            db_query("UPDATE `DICTIONARY-ENTRIES` SET `LANE PAGE`=" . db_quote($_GET["LANE_PAGE"]) . " WHERE `DICTIONARY ID`=" . db_quote($_GET["LANE_ROOT"]));
        }
    }
}

// load users table and load preferences
$result = db_query("SELECT * FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "'");

if (db_rowcount($result) > 0)
{
    $ROW                                              = db_return_row($result);
    $user_preference_highlight_colour                 = "#" . $ROW["Preferred Highlight Colour"];
    $user_preference_highlight_colour_lightness_value = $ROW["Preferred Highlight Colour Lightness Value"];
}
else
{
    $user_preference_highlight_colour                 = "#FFFF00";
    $user_preference_highlight_colour_lightness_value = 200;
}

// SEARCH

$SEARCH = "";
if (isset($_GET["S"]))
{
    $SEARCH = $_GET["S"];
}

$STARTS = "";
if (isset($_GET["STARTS"]))
{
    $STARTS = $_GET["STARTS"];
}

// ================== SOLICIT INPUT ====================

?>

<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title("Dictionary");
    ?>

    <script type="text/javascript" src="library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>
    <script type="text/javascript" src="library/js/persistent_table_headers.js"></script>

    <script>
        $(document).ready(function() {
            // Assign the Colorbox event to elements
            $(".iframe").colorbox({
                iframe: true,
                width: "80%",
                height: "90%"
            });

        });
    </script>

    <style>
        mark {
            background-color:
                <?php
                echo $user_preference_highlight_colour . ";";
                echo "color: ";

                if ($user_preference_highlight_colour_lightness_value < 100)
                {
                    echo "white";
                }
                else
                {
                    echo "black";
                }
                ?>
        }
    </style>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>

        <?php

        include "library/back_to_top_button.php";

        // menubar

        include "library/menu.php";

        // QUR’AN TOOLS HEADER TEXT

        echo "<div align=center>";

        echo "<h2 class='page-title-text'>Dictionary</h2>";

        if ($show_edit_lane_fields)
        {
            $done    = db_return_one_record_one_field("SELECT COUNT(*)  FROM `DICTIONARY-ENTRIES` WHERE `LANE PAGE` > 0 AND `TYPE`='ROOT'");
            $done_of = db_return_one_record_one_field("SELECT COUNT(*) FROM `DICTIONARY-ENTRIES` WHERE `TYPE`='ROOT'");
            echo "[Done " . number_format($done) . " of " . number_format($done_of) . " Lane entries = " . number_format($done * 100 / $done_of, 2) . "%] (Highest Lane File Number Used Is: " . db_return_one_record_one_field("SELECT MAX(`LANE PAGE`) FROM `DICTIONARY-ENTRIES`") . ")";
        }

        $sql = "SELECT * FROM `DICTIONARY-ENTRIES` WHERE `MEANING`!=''";

        if ($STARTS != "")
        {
            // do oddities, such as odd types of aleph
            if ($STARTS == "ا")
            {
                $sql .= " AND (`ARABIC` LIKE '" . db_quote($STARTS) . "%' OR `ENGLISH` LIKE '{%' OR `ENGLISH` LIKE '<%')";
            }
            else
            {
                $sql .= " AND `ARABIC` LIKE '" . db_quote($STARTS) . "%'";
            }
        }

        if ($show_edit_lane_fields)
        {
            $sql .= $extra_lane_sql_bit;
        }

        if ($SEARCH != "")
        {
            $wildcard_search = "'%" . db_quote(strtolower($SEARCH)) . "%'";

            $sql .= " AND (`ARABIC` LIKE $wildcard_search OR LOWER(`ENGLISH`) LIKE $wildcard_search OR LOWER(`ENGLISH TRANSLITERATED`) LIKE $wildcard_search OR LOWER(`ENGLISH ALT 1`) LIKE $wildcard_search OR LOWER(`ENGLISH ALT 2`) LIKE $wildcard_search OR LOWER(`MEANING`) LIKE $wildcard_search)";
        }

        if (isset($_GET["TYPE"]))
        {
            if ($_GET["TYPE"] == "LEMMA")
            {
                $sql .= " AND `TYPE`='LEMMA'";
            }
        }

        $sql .= " ORDER BY `ARABIC FOR SORTING`";

        $result = db_query($sql);

        if (db_rowcount($result) == 0)
        {
            echo "No matching dictionary definitions found for <b>" . htmlentities($SEARCH) . "</b>.";
            echo "<p><a href='dictionary.php'>Please try again</a>.</p><br><br><br><br><br><br>";
            include "library/footer.php";
            exit;
        }

        if ($SEARCH != "")
        {
            $plural  = "s";
            $plural2 = "";
            if (db_rowcount($result) == 1)
            {
                $plural  = "";
                $plural2 = "es";
            }
            echo "<p>Showing the " . number_format(db_rowcount($result)) . " dictionary definition$plural which match$plural2";

            if (isset($_GET["DISPLAY"]))
            {
                echo " <b>" . $_GET["DISPLAY"] . "</b>";
            }
            else
            {
                echo " <b>" . htmlentities($SEARCH) . "</b>";
            }
            echo " <a href='dictionary.php'>(Show All Definitions)</a></p>";
        }
        else
        {
            echo "<p><font size=+1>";

            if ($STARTS == "ا")
            {
                echo "<span class=selected-dictionary-letter>ا</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ا'>ا</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ب")
            {
                echo "<span class=selected-dictionary-letter>ب</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ب'>ب</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ت")
            {
                echo "<span class=selected-dictionary-letter>ت</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ت'>ت</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ث")
            {
                echo "<span class=selected-dictionary-letter>ث</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ث'>ث</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ج")
            {
                echo "<span class=selected-dictionary-letter>ج</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ج'>ج</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ح")
            {
                echo "<span class=selected-dictionary-letter>ح</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ح'>ح</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "خ")
            {
                echo "<span class=selected-dictionary-letter>خ</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=خ'>خ</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "د")
            {
                echo "<span class=selected-dictionary-letter>د</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=د'>د</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ذ")
            {
                echo "<span class=selected-dictionary-letter>ذ</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ذ'>ذ</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ر")
            {
                echo "<span class=selected-dictionary-letter>ر</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ر'>ر</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ز")
            {
                echo "<span class=selected-dictionary-letter>ز</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ز'>ز</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "س")
            {
                echo "<span class=selected-dictionary-letter>س</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=س'>س</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ش")
            {
                echo "<span class=selected-dictionary-letter>ش</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ش'>ش</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ص")
            {
                echo "<span class=selected-dictionary-letter>ص</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ص'>ص</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ض")
            {
                echo "<span class=selected-dictionary-letter>ض</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ض'>ض</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ط")
            {
                echo "<span class=selected-dictionary-letter>ط</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ط'>ط</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ظ")
            {
                echo "<span class=selected-dictionary-letter>ظ</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ظ'>ظ</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ع")
            {
                echo "<span class=selected-dictionary-letter>ع</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ع'>ع</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "غ")
            {
                echo "<span class=selected-dictionary-letter>غ</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=غ'>غ</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ف")
            {
                echo "<span class=selected-dictionary-letter>ف</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ف'>ف</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ق")
            {
                echo "<span class=selected-dictionary-letter>ق</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ق'>ق</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ك")
            {
                echo "<span class=selected-dictionary-letter>ك</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ك'>ك</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ل")
            {
                echo "<span class=selected-dictionary-letter>ل</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ل'>ل</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "م")
            {
                echo "<span class=selected-dictionary-letter>م</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=م'>م</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ن")
            {
                echo "<span class=selected-dictionary-letter>ن</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ن'>ن</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ه")
            {
                echo "<span class=selected-dictionary-letter>ه</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ه'>ه</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "و")
            {
                echo "<span class=selected-dictionary-letter>و</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=و'>و</a>&nbsp;&nbsp;";
            }

            if ($STARTS == "ي")
            {
                echo "<span class=selected-dictionary-letter>ي</span>&nbsp;&nbsp;";
            }
            else
            {
                echo "<a class=linky href='dictionary.php?STARTS=ي'>ي</a>&nbsp;&nbsp;";
            }

            // echo "<a href='dictionary.php?STARTS=X'>X</a>&nbsp;&nbsp;";

            echo "</font></p>";
        }

        if ($SEARCH == "" && $STARTS == "")
        {
            echo "<form id=pickVerse action='dictionary.php' method=get name=FormName}>";
            echo "<input id='inputText' type=text style='font-size:14px' autofocus NAME=S size=30 maxlength=40 autocomplete='off' placeholder='Search dictionary definitions'>";

            echo " <button name=OKbutton style='font-size:14px' type=submit>SEARCH</button><p>";
        }

        if ($STARTS != "")
        {
            $plural  = "s";
            $plural2 = "";
            if (db_rowcount($result) == 1)
            {
                $plural  = "";
                $plural2 = "es";
            }
            echo "<p>Showing the " . db_rowcount($result) . " dictionary definition$plural for roots beginning with $STARTS. <a href='dictionary.php'>(Show All Definitions)</a></p>";
        }

        // table container div and fixed cols solves wide table persistent header issues
        echo "<div id=tableContainer class='tableContainer'>";

        echo "<table class='hoverTable persist-area fixedTable'>";

        // table header

        echo "<thead class='persist-header table-header-row'>";

        echo "<tr class='table-header-row'>";

        echo "<th bgcolor=#c0c0c0 align=center colspan=2><b>Root (or Lemma)</b></th><th bgcolor=#c0c0c0 rowspan=2 align=center width=600><b>Dictionary Definition</b></th><th rowspan=2 bgcolor=#c0c0c0 align=center width=120><b>Other Lexica</b></th><th rowspan=2 bgcolor=#c0c0c0 align=center width=100><b>Occurrences</b></th><th bgcolor=#c0c0c0 align=center rowspan=2 width=90>&nbsp;</th></tr>";

        echo "<tr><th bgcolor=#c0c0c0 align=center width=60><b>Arabic</b></th><th bgcolor=#c0c0c0 align=center width=60><b>English</b></th>";

        echo "</tr>";

        echo "</thead>";

        echo "<tbody>";

        // set up the format we use for highlighting (we could probably do this using better css at some point)

        $highlight_on_format  = "<MARK>";
        $highlight_off_format = "</MARK>";

        // figure out which rows to show

        $START = $ITEMS_PER_PAGE * ($CURRENT_PAGE - 1);
        $END   = $START + $ITEMS_PER_PAGE;
        if ($END > db_rowcount($result))
        {
            $END = db_rowcount($result);
        }

        if ($START > 0)
        {
            $result->data_seek($START);
        }

        // table printing loop

        for ($i = $START; $i < $END; $i++)
        {
            // grab next database row
            $ROW = db_return_row($result);

            if ($ROW["TYPE"] == "ROOT")
            {
                $AHREF = "<a href='verse_browser.php?S=ROOT:" . $ROW["ARABIC"] . "' class=linky>";
            }
            else
            {
                $AHREF = "<a href='../verse_browser.php?S=(LEMMA:" . urlencode($ROW["ARABIC"]) . " OR LEMMA:" . urlencode($ROW["ENGLISH"]) . ")' class=linky>";
            }

            echo "<tr>";

            $arabic  = $ROW["ARABIC"];
            $english = transliterate_new($ROW["ENGLISH"]);

            $meaning = $ROW["MEANING"];

            if ($SEARCH != "")
            {
                // try to cover a few oddities with transliteation
                // when looking up a lemma from the instant details palette, we want to highlight the arabic only
                if (!isset($_GET["DISPLAY"]))
                {
                    if ((strtolower($SEARCH) == strtolower($ROW["ENGLISH TRANSLITERATED"]) || strtolower($SEARCH) == strtolower($ROW["ENGLISH"]) || strtolower($SEARCH) == strtolower($ROW["ENGLISH ALT 1"]) || strtolower($SEARCH) == strtolower($ROW["ENGLISH ALT 2"])) && $SEARCH != $english)
                    {
                        $english = $highlight_on_format . $english . $highlight_off_format;
                    }
                }

                if ($SEARCH == $ROW["ARABIC FOR SORTING"] || isset($_GET["DISPLAY"]))
                {
                    $arabic = $highlight_on_format . $arabic . $highlight_off_format;
                }

                if (!isset($_GET["DISPLAY"]))
                {
                    $arabic  = preg_replace("/" . preg_quote($SEARCH, "/") . "/i", "$highlight_on_format$0$highlight_off_format", $arabic);
                    $english = preg_replace("/" . preg_quote($SEARCH, "/") . "/i", "$highlight_on_format$0$highlight_off_format", $english);
                    $meaning = preg_replace("/" . preg_quote($SEARCH, "/") . "/i", "$highlight_on_format$0$highlight_off_format", $meaning);
                }
            }

            echo "<td align=center>$AHREF" . $arabic . "</a></td>";
            echo "<td align=center $user_preference_transliteration_style>$AHREF" . $english . "</a></td>";

            echo "<td width=600>" . str_ireplace("\n", "<br>", $meaning) . "</td>";

            echo "<td align=center width=120>";

            $AJC_PAGE = db_return_one_record_one_field("SELECT `AJ FOREIGN PAGE` FROM `LEMMA-LIST` WHERE `ROOT`='" . db_quote($ROW["ENGLISH"]) . "'");

            // link to Lane

            if ($ROW["LANE PAGE"] > 0)
            {
                echo "<a href='#.'><img src='images/lane_small.png' title='Show in Lane&rsquo;s \"An Arabic-English Lexicon\"' onclick='$.colorbox({href:\"dictionary/lane.php?PAGE=" . $ROW["LANE PAGE"] . "&LIGHTVIEW=YES\",width:\"90%\", height:\"90%\"});'></a>";
            }
            else
            {
                echo "<img src='images/lane_small.png' style='opacity:0.10;'>";
            }

            echo "&nbsp;&nbsp;";

            // link to Penrice?

            if ($ROW["PENRICE PAGE"] > 0)
            {
                echo "<a href='#.'><img src='images/penrice_small.png' title='Show in Penrice&rsquo;s \"Dictionary and Glossary of the Qur&rsquo;an\"' onclick='$.colorbox({href:\"dictionary/penrice.php?PAGE=" . $ROW["PENRICE PAGE"] . "&LIGHTVIEW=YES\",width:\"90%\", height:\"90%\"});'></a>";
            }
            else
            {
                echo "<img src='images/penrice_small.png' style='opacity:0.10;>";
            }

            echo "&nbsp;&nbsp;";

            if ($AJC_PAGE > 0)
            {
                echo "<a href='#.'><img src='images/jeffery_small.png' title='Show in Arthur Jeffery&rsquo;s \"The Foreign Vocabulary of the Qur&rsquo;an\"' onclick='$.colorbox({href:\"dictionary/jeffery.php?PAGE=" . (14 + $AJC_PAGE) . "&LIGHTVIEW=YES\",width:\"90%\", height:\"95%\"});'></a>";
            }
            else
            {
                echo "<img src='images/jeffery_small.png' style='opacity:0.10'>";
            }

            // if this is a superuser, show a field we can enter Penrice numbers into
            if (is_admin_user($logged_in_user) && $show_edit_lane_fields)
            {
                echo "<span style='float:right;'><form action='dictionary.php' method=get>Page <input NAME=LANE_PAGE autocomplete=off";

                if (!$lane_autofocus_done)
                {
                    echo " autofocus";
                    $lane_autofocus_done = true;
                }

                if ($ROW["LANE PAGE"] == 0)
                {
                    echo " style='background-color: yellow; width:40px;'";
                }
                else
                {
                    echo " style='width:40px;'";
                }
                echo " value='" . $ROW["LANE PAGE"] . "'></input>";

                echo "<input type=hidden NAME=LANE_ROOT VALUE=" . $ROW["DICTIONARY ID"] . "></input>";
                echo "<input type=hidden NAME=STARTS VALUE=$STARTS></input>";

                echo "<input type=submit value=OK>";

                echo "</form></span>";
            }

            echo "</td>";

            echo "<td align=center>$AHREF";

            echo number_format($ROW["COUNT"]);

            echo "</a></td>";

            echo "<td width=90>";
            if ($ROW["TYPE"] == "ROOT")
            {
                echo "<a title='Examine root' href='examine_root.php?ROOT=" . htmlentities($ROW["ENGLISH"]) . "'><img src='images/info.gif'></a>&nbsp;";
            }
            else
            {
                echo "<img src='images/info.gif' style='opacity:0.25;'>&nbsp;";
            }

            // chart is handled slightly differently if it's a root or a lemma
            if ($ROW["TYPE"] == "ROOT")
            {
                if (!isMobile())
                {
                    echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'charts/chart_roots.php?VIEW=MINI&ROOT=" . urlencode($ROW["ENGLISH TRANSLITERATED"]) . "', type: 'post'}\">";
                }

                echo "<a title='Chart root occurrences' href='charts/chart_roots.php?ROOT=" . urlencode(convert_buckwalter($ROW["ENGLISH"])) . "'><img src='images/stats.gif'></a>";

                if (!isMobile())
                {
                    echo "</span>";
                }
            }
            else
            {
                if (!isMobile())
                {
                    echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'charts/chart_lemmata.php?VIEW=MINI&LEMMA=" . urlencode($ROW["ENGLISH"]) . "', type: 'post'}\">";
                }

                echo "<a title='Chart root occurrences' href='charts/chart_lemmata.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "'><img src='images/stats.gif'></a>";

                if (!isMobile())
                {
                    echo "</span>";
                }
            }

            // exhaustive root list

            echo "&nbsp;";

            if ($ROW["TYPE"] == "ROOT")
            {
                echo "<a title='Exhaustively list all occurrences of this root' href='../exhaustive_root_references.php?ROOT=" . urlencode($ROW["ENGLISH"]) . "&BACK=Return to Dictionary'>";
                echo "<img src='images/context_v.gif'></a>";
            }
            else
            {
                echo "<a title='Exhaustively list all occurrences of this lemma' href='../exhaustive_root_references.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "&BACK=Return to Dictionary'>";
                echo "<img src='images/context_v.gif'></a>";
            }

            // network diagram

            echo "&nbsp;";

            if ($ROW["TYPE"] == "ROOT")
            {
                echo "<a title='Root/word associations (explore which roots are often used along with this one)' href='word_associations.php?ROOT=" . urlencode($ROW["ENGLISH"]) . "'><img src='images/network.gif'></a>";
            }
            else
            {
                echo "<img src='images/network.gif' style='opacity: 0.25;'>";
            }
            echo "</td>";
            echo "</tr>";
        }

        echo "<tbody>";

        echo "</table>";

        echo "</div><br>";

        // insert the page navigator

        $ITEMS_TO_SHOW = db_rowcount($result);
        $pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

        if ($pages_needed > 1)
        {
            if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
            {
                $pages_needed++;
            }

            print_page_navigator($CURRENT_PAGE, $pages_needed, false, "dictionary.php?S=$SEARCH&STARTS=$STARTS");
        }

        // print footer

        include "library/footer.php";

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
    });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php

move_back_to_top_button();

?>

</html>