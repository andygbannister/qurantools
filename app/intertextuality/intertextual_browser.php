<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// set up preferences

if ($logged_in_user["Preference Italics Transliteration"] == 1)
{
    $user_preference_transliteration_style = "class=transliteration_formatting_preference";
}
else
{
    $user_preference_transliteration_style = "";
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 420;
$CURRENT_PAGE   = 1;

// ensure "SORT" is set, just to save lots of checking later
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

// pre-render data or calculate it live?

$PRE_RENDER = true;
$PRE_PASS   = "";

if (isset($_GET["PRERENDER"]))
{
    if ($_GET["PRERENDER"] == "OFF")
    {
        $PRE_RENDER = false;
        $PRE_PASS   = "&PRERENDER=OFF";
    }
}

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

    ?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Browse Intertextual Connections");
        ?>

    <script type="text/javascript" src="/library/js/persistent_table_headers.js"></script>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

        include "library/back_to_top_button.php";

    // sort order

    $SORT_ORDER = "REPLACE(`SOURCE NAME`, 'The ', '') ASC";

    if ($_GET["SORT"] == "NAME-DESC")
    {
        $SORT_ORDER = "REPLACE(`SOURCE NAME`, 'The ', '') DESC";
    }

    if ($_GET["SORT"] == "COUNT-ASC")
    {
        $SORT_ORDER = "`TC` ASC";
    }

    if ($_GET["SORT"] == "COUNT-DESC")
    {
        $SORT_ORDER = "`TC` DESC";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>Browse Intertextual Connections <a href='/charts/chart_intertextual_connections.php'><img src='/images/stats.gif'></a></h2>";

        // table container div and fixed cols solves wide table persistent header issues
        echo "<div id=tableContainer class='tableContainer'>";

        echo "<table class='hoverTable complexIntertextTable persist-area fixedTable'>";

        // table header

        echo "<thead class='persist-header table-header-row'>";

        echo "<tr class='table-header-row'>";

        echo "<th colspan=2 width=420>Source <a href='/charts/chart_intertextual_links_per_source.php'><img src='/images/stats.gif'></a></th>";

        echo "<th colspan=2 width=420>Specific References</th>";

        echo "<th rowspan=2 width=300>Qur’an Passages</th>";

        echo "</tr>";

        echo "<th align=center width=340><b>Name/Description</b><br><a href='intertextual_browser.php?SORT=NAME-ASC'><img src='../images/up.gif'></a> <a href='intertextual_browser.php?SORT=NAME-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center width=80><b>Total Links</b><br><a href='intertextual_browser.php?SORT=COUNT-ASC'><img src='../images/up.gif'></a> <a href='intertextual_browser.php?SORT=COUNT-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center width=340><b>References</b><br><a href='intertextual_browser.php?SORT=COUNT-ASC'><img src='../images/up.gif'></a> <a href='intertextual_browser.php?SORT=COUNT-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center width=80><b>Total Links</b></th>";

        echo "</tr>";

        echo "</thead>";

        echo "<tbody>";

        // table data

        $result = db_query("SELECT *, (SELECT COUNT(*) FROM `INTERTEXTUAL LINKS` WHERE `SOURCE ID`=`SOURCE`) TC FROM `INTERTEXTUAL SOURCES` ORDER BY $SORT_ORDER");

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

        for ($i = $START; $i < $END; $i++)
        {
            // grab next database row
            $ROW = $result->fetch_assoc();

            // look up the details of references that use this source

            $result_links = db_query("SELECT DISTINCT(`SOURCE REF`), `INTERTEXT ID`, `SURA`, `START VERSE`, `END VERSE`, COUNT(*) SC FROM `INTERTEXTUAL LINKS` WHERE `SOURCE`='" . $ROW["SOURCE ID"] . "' GROUP BY `SOURCE REF`");

            for ($j = 0; $j < db_rowcount($result_links); $j++)
            {
                // grab next database row
                $ROW_LINK_INFO = $result_links->fetch_assoc();

                // and we'll use this query to build the list of qur'an references

                $result_quran = db_query("SELECT * FROM `INTERTEXTUAL LINKS` WHERE `SOURCE`='" . db_quote($ROW["SOURCE ID"]) . "' AND `SOURCE REF`='" . addslashes($ROW_LINK_INFO["SOURCE REF"]) . "' ORDER BY `SURA`, `START VERSE`");

                echo "<tr>";

                if ($j == 0)
                {
                    echo "<td width=340 class='thickBorder ";

                    if (($i % 2))
                    {
                        echo " complexIntertextTable_Light'";
                    }
                    else
                    {
                        echo " complexInterTextTable_Dark'";
                    }

                    echo " rowspan=" . db_rowcount($result_links) . "><a href='/verse_browser.php?V=" . $ROW["VERSE REFERENCES"] . "' class=linky>" . $ROW["SOURCE NAME"] . "<br><span class=smaller_text_for_mini_dialogs>" . $ROW["SOURCE DATE"] . "</span></a>";

                    echo "&nbsp;<a href='" . $ROW["SOURCE URL"] . "' target='_blank'>";

                    echo "<img class='float-right' src='/images/openweb.png' valign=middle>";

                    echo "</a>";

                    echo "</td>";

                    // now we need to pull up the qur'an verses for this source

                    echo "<td width=80 align=center class='thickBorder ";

                    if (($i % 2))
                    {
                        echo " complexIntertextTable_Light'";
                    }
                    else
                    {
                        echo " complexInterTextTable_Dark'";
                    }

                    echo "rowspan=" . db_rowcount($result_links) . ">";

                    echo "<a href='/verse_browser.php?V=" . $ROW["VERSE REFERENCES"] . "' class=linky>";

                    echo number_format($ROW["TC"]);

                    echo "</a>";

                    echo "</td>";
                }

                echo "<td width=340";

                if ($j == (db_rowcount($result_links) - 1))
                {
                    echo " class='thickBorder ";
                }
                else
                {
                    echo " class='";
                }

                if (($i % 2))
                {
                    echo "complexIntertextTable_Light'";
                }
                else
                {
                    echo "complexInterTextTable_Dark'";
                }

                echo ">";

                echo $ROW_LINK_INFO["SOURCE REF"] . "&nbsp;";

                echo "<a href='#' onclick='$.colorbox({href:\"intertextual_viewer.php?ID=" . $ROW_LINK_INFO["INTERTEXT ID"] . "&LIGHTVIEW=YES\",width:\"90%\", height:\"90%\"});'>";

                echo "<img class='float-right' src='/images/scroll.png' valign=middle>";

                echo "</a>";

                echo "</td>";

                // build verse list(s)

                $full_reference_list = "";

                $itemized_list = "";

                for ($k = 0; $k < db_rowcount($result_quran); $k++)
                {
                    if ($k > 0)
                    {
                        $itemized_list .= "; ";
                        $full_reference_list .= ";";
                    }

                    // grab next database row
                    $ROW_QURAN = $result_quran->fetch_assoc();

                    $ref = $ROW_QURAN["SURA"] . ":" . $ROW_QURAN["START VERSE"];

                    if ($ROW_QURAN["START VERSE"] != $ROW_QURAN["END VERSE"])
                    {
                        $ref .= "-" . $ROW_QURAN["END VERSE"];
                    }

                    $full_reference_list .= "$ref";

                    $itemized_list .= "<a href='/verse_browser.php?V=$ref' class=linky>";
                    $itemized_list .= $ref;
                    $itemized_list .= "</a>";
                }

                echo "<td width=80 align=center";

                if ($j == (db_rowcount($result_links) - 1))
                {
                    echo " class='thickBorder ";
                }
                else
                {
                    echo " class='";
                }

                if (($i % 2))
                {
                    echo "complexIntertextTable_Light'";
                }
                else
                {
                    echo "complexInterTextTable_Dark'";
                }

                echo ">";

                echo "<a href='/verse_browser.php?V=$full_reference_list' class=linky>";

                echo number_format($ROW_LINK_INFO["SC"]);

                echo "</a>";

                echo "</td>";

                // qur'an verses

                echo "<td width=300";

                if ($j == (db_rowcount($result_links) - 1))
                {
                    echo " class='thickBorder ";
                }
                else
                {
                    echo " class='";
                }

                if (($i % 2))
                {
                    echo " complexIntertextTable_Light'";
                }
                else
                {
                    echo " complexInterTextTable_Dark'";
                }

                echo ">";

                echo $itemized_list;

                if ($k > 1)
                {
                    echo " <span class=smaller_text_for_mini_dialogs>";
                    echo "<a href='/verse_browser.php?V=$full_reference_list' class=linky>";
                    echo "(View All)";
                    echo "</a>";
                    echo "</span>";
                }

                echo "</td>";

                echo "</tr>";
            }
        }

        // do the totals row if this is the last screen

        if ($i == db_rowcount($result))
        {
            echo "<tr>";

            echo "<td align=center><b>" . number_format(db_rowcount($result)) . " sources</b></td>";

            echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `INTERTEXTUAL LINKS`")) . "<b></td>";

            echo "<td>&nbsp;</td>";

            echo "<td align=center><b>" . number_format(db_return_one_record_one_field("SELECT COUNT(*) FROM `INTERTEXTUAL LINKS`")) . "<b></td>";

            echo "<td>&nbsp;</td>";

            echo "</tr>";
        }

        echo "</table><br>";

    // insert the page navigator

    $ITEMS_TO_SHOW = db_rowcount($result);
    $pages_needed  = $ITEMS_TO_SHOW / $ITEMS_PER_PAGE;

    if ($pages_needed > 1)
    {
        if (($ITEMS_TO_SHOW % $ITEMS_PER_PAGE) > 0)
        {
            $pages_needed++;
        }

        print_page_navigator($CURRENT_PAGE, $pages_needed, false, "intertextual_browser.php?SORT=" . $_GET["SORT"] . "$PRE_PASS");
    }

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
    });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>

</html>