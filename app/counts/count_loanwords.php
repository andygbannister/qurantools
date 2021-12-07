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

// group by
$GROUP_BY = "ROOT";
if (isset($_GET["GROUPBY"]))
{
    if ($_GET["GROUPBY"] == "SURA")
    {
        $GROUP_BY = "SURA";
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
            window_title("List Loanwords");
        ?>

		<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
		<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

		<script type="text/javascript" src="../library/js/persistent_table_headers.js"></script>

	</head>
	<body class='qt-site'>
<main class='qt-site-content'>
	<?php

        include "library/back_to_top_button.php";

    // sort order
    $sort = "TRANSLITERATION-ASC";

    if (isset($_GET["SORT"]))
    {
        $sort = $_GET["SORT"];
    }

    $SORT_ORDER = "`ARABIC` DESC";

    if ($sort == "ARABIC-ASC")
    {
        $SORT_ORDER = "`ARABIC`";
    }

    if ($sort == "ARABIC-DESC")
    {
        $SORT_ORDER = "`ARABIC` DESC";
    }

    if ($sort == "ROOT-ASC")
    {
        $SORT_ORDER = "`ROOT`";
    }

    if ($sort == "ROOT-DESC")
    {
        $SORT_ORDER = "`ROOT` DESC";
    }

    if ($sort == "TRANSLITERATION-ASC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED`";
    }

    if ($sort == "TRANSLITERATION-DESC")
    {
        $SORT_ORDER = "`ENGLISH TRANSLITERATED` DESC";
    }

    if ($sort == "GLOSS-ASC")
    {
        $SORT_ORDER = "LOWER(`GLOSS`)";
    }

    if ($sort == "GLOSS-DESC")
    {
        $SORT_ORDER = "LOWER(`GLOSS`) DESC";
    }

    if ($sort == "COUNT-DESC")
    {
        $SORT_ORDER = "`COUNT` DESC";
    }

    if ($sort == "COUNT-ASC")
    {
        $SORT_ORDER = "`COUNT`";
    }

    // menubar

    include "../library/menu.php";

    echo "<div align=center><h2 class='page-title-text'>List All Loanwords <a href='/charts/chart_loanwords_per_sura.php'><img src='/images/stats.gif'></a></h2><h3 class='button-block-with-spacing'>(Based on Arthur Jeffery's <i>The Foreign Vocabulary of the Qur&rsquo;an</i>)</h3><br>";

        // table container div and fixed cols solves wide table persistent header issues
        echo "<div id=tableContainer class='tableContainer'>";

        echo "<table class='hoverTable persist-area fixedTable'>";

        // table header

        echo "<thead class='persist-header table-header-row'>";

        echo "<tr class='table-header-row'>";

        echo "<th align=center width=150 colspan=2><b>Lemma</b></th>";

        echo "<th align=center rowspan=2 width=70><b>Root</b><br><a href='count_loanwords.php?SORT=ROOT-ASC'><img src='../images/up.gif'></a> <a href='count_loanwords.php?SORT=ROOT-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center rowspan=2 width=240><b>Gloss</b><br><a href='count_loanwords.php?SORT=GLOSS-ASC'><img src='../images/up.gif'></a> <a href='count_loanwords.php?SORT=GLOSS-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center rowspan=2 width=100><b>Occurrences</b><br><a href='count_loanwords.php?SORT=COUNT-ASC'><img src='../images/up.gif'></a> <a href='count_loanwords.php?SORT=COUNT-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center rowspan=2 width=70>&nbsp;</th>";

        echo "</tr>";

        echo "<tr class='table-header-row'>";

        echo "<th align=center width=150><b>Arabic</b><br><a href='count_loanwords.php?SORT=ARABIC-ASC'><img src='../images/up.gif'></a> <a href='count_loanwords.php?SORT=ARABIC-DESC'><img src='../images/down.gif'></a></th>";

        echo "<th align=center width=150><b>Transliteration</b><br><a href='count_loanwords.php?SORT=TRANSLITERATION-ASC'><img src='../images/up.gif'></a> <a href='count_loanwords.php?SORT=TRANSLITERATION-DESC'><img src='../images/down.gif'></a></th>";

        echo "</tr>";

        echo "</thead>";

        echo "<tbody>";

        // table data

        $result = db_query("SELECT * FROM `LEMMA-LIST` WHERE `AJ FOREIGN PAGE`>0 ORDER BY $SORT_ORDER");

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

            $AHREF = "<a href='../verse_browser.php?S=(LEMMA:" . urlencode($ROW["ARABIC"]) . " OR LEMMA:" . urlencode($ROW["ENGLISH"]) . ")' class=linky>";

            $jeffery_page = db_return_one_record_one_field("SELECT `AJ FOREIGN PAGE` FROM `LEMMA-LIST` WHERE `ENGLISH`='" . db_quote($ROW["ENGLISH"]) . "'");

            echo "<tr>";

            echo "<td align=center>$AHREF" . $ROW["ARABIC"] . "</a></td>";

            echo "<td align=center $user_preference_transliteration_style>$AHREF" . $ROW["ENGLISH TRANSLITERATED"] . "</a></td>";
            echo "<td align=center WIDTH=70 $user_preference_transliteration_style>";

            if ($ROW["ROOT"] == "")
            {
                echo "-";
            }
            else
            {
                echo "$AHREF" . convert_buckwalter($ROW["ROOT"]) . "</a>";
            }
            echo "</td>";

            echo "<td align=center width=240'>$AHREF" . htmlentities($ROW["GLOSS"]) . "</a></td>";

            echo "<td align=center>$AHREF" . number_format($ROW["COUNT"]) . "</a>";

            echo "</td>";

            echo "<td width=70 align=center>";

            echo "<a href='#.'>";  // this prevents scrolling when clicking
            echo "<img valign=middle src='/images/jeffery_small.png' border=0 width=15 height=20 valign=middle onclick='$.colorbox({href:\"/dictionary/jeffery.php?PAGE=" . (14 + $jeffery_page) . "&LIGHTVIEW=YES\",width:\"90%\", height:\"95%\"});'>";
            echo "</a>";

            echo "&nbsp;<a title='Exhaustively list all occurrences of this lemma' href='../exhaustive_root_references.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "&BACK=Return to Lemma List'>";
            echo "<img src='/images/context_v.gif' valign=middle></a>&nbsp;";

            if (!isMobile())
            {
                echo "<span class='chart-tip' data-tipped-options=\"zIndex: 1000, hideOthers: true, ajax: {url:'../charts/chart_lemmata.php?VIEW=MINI&LEMMA=" . urlencode($ROW["ENGLISH"]) . "', type: 'post'}\">";
            }

            echo "<a href='../charts/chart_lemmata.php?LEMMA=" . urlencode($ROW["ENGLISH"]) . "'><img valign=middle src='../images/stats.gif'></a></td>";

            if (!isMobile())
            {
                echo "</span>";
            }

            echo "</tr>";
        }

        echo "<tr><td align=center colspan=6><b>" . db_rowcount($result) . " loanwords in total</b></td></tr>";

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

        print_page_navigator($CURRENT_PAGE, $pages_needed, false, "count_loanwords.php?SORT=" . $_GET["SORT"] . "$PRE_PASS");
    }

echo "</div>";

    // print footer

    include "../library/footer.php";

?>
</body>

<script type="text/javascript">
  $(function() {
    Tipped.create('.chart-tip', {position: 'left', showDelay: 750, skin: 'light', close: true});
  });
</script>

<!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
<?php
move_back_to_top_button();

?>

</html>