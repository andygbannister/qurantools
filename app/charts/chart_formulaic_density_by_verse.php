<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/verse_parse.php';

// are we running in "mini mode" (e.g. in a loupe view)
$miniMode = false;
if (isset($_GET["VIEW"]))
{
    if ($_GET["VIEW"] = "MINI")
    {
        $miniMode = true;
    }
}

// convert POST to GET
if (isset($_POST["L"]))
{
    $_GET["L"]    = $_POST["L"];
    $_GET["TYPE"] = $_POST["TYPE"];
}

// what formulae type

    $FORMULA_TYPE = "ROOT";

    if (isset($_GET["TYPE"]))
    {
        $FORMULA_TYPE = $_GET["TYPE"];
        if ($FORMULA_TYPE != "ROOT" && $FORMULA_TYPE != "ROOT-ALL" && $FORMULA_TYPE != "LEMMA")
        {
            $FORMULA_TYPE = "ROOT";
        }
    }

    // formula length

    $FORMULA_LENGTH = 3;

    if (isset($_GET["L"]))
    {
        $FORMULA_LENGTH = $_GET["L"];
        if ($FORMULA_LENGTH < 2)
        {
            $FORMULA_LENGTH = 2;
        }
        if ($FORMULA_LENGTH > 5)
        {
            $FORMULA_LENGTH = 5;
        }
    }

    if ($FORMULA_TYPE == "ROOT-ALL" && $FORMULA_LENGTH == 2)
    {
        $FORMULA_LENGTH = 3;
    }

// which sura or verse to view
$SURA = 0;
if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] > 0 && $_GET["SURA"] < 115)
    {
        $SURA             = $_GET["SURA"];
        $extra_title_text = "<a href='../verse_browser.php?V=$SURA&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Sura $SURA)</a>";
    }
}
else
{
    $_GET["SURA"] = "";
}

// or do we want to view a verse or search result?
// limit by selection of verses

$RANGE_SQL = "";

if (isset($_GET["V"]))
{
    $V = $_GET["V"];

    if ($V != "")
    {
        parse_verses($V, true, 0);

        if ($_GET["V"] == "SEARCH")
        {
            if (isset($_GET["S"]))
            {
                $extra_title_text = "<a href='../verse_browser.php?S=" . $_GET["V"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Verses Matching Search: '" . $_GET["S"] . "')</a>";
            }
            else
            {
                $extra_title_text = "(Verses Matching Search Criteria)";
            }
        }
        else
        {
            $extra_title_text = "<a href='../verse_browser.php?V=" . $_GET["V"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE' class=linky>(Q. $V)</a>";
        }
    }
}
else
{
    $_GET["V"] = "";
}

// and to avoid later "unknown index" errors
if (!isset($_GET["S"]))
{
    $_GET["S"] = "";
}

// sort order

    $SORT_ORDER = "`SURA`, `VERSE` ASC";

    if (isset($_GET["SORT"]))
    {
        if ($_GET["SORT"] == "SURA-DESC")
        {
            $SORT_ORDER = "`SURA`, `VERSE` DESC";
        }

        if ($_GET["SORT"] == "WORDS-ASC")
        {
            $SORT_ORDER = "WORDS ASC";
        }

        if ($_GET["SORT"] == "WORDS-DESC")
        {
            $SORT_ORDER = "WORDS DESC";
        }

        if ($_GET["SORT"] == "PART-ASC")
        {
            $SORT_ORDER = "FLAGGED ASC";
        }

        if ($_GET["SORT"] == "PART-DESC")
        {
            $SORT_ORDER = "FLAGGED DESC";
        }

        if ($_GET["SORT"] == "FD-ASC")
        {
            $SORT_ORDER = "FORMULAIC_DENSITY ASC";
        }

        if ($_GET["SORT"] == "FD-DESC")
        {
            $SORT_ORDER = "FORMULAIC_DENSITY DESC";
        }
    }
    else
    {
        $_GET["SORT"] = "";
    }

    // header

?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Chart of Formulaic Density by Verse");
        ?>

    <script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
    <script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

    <?php

$sql = "";

    if ($FORMULA_TYPE == "ROOT")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`QTL-ROOT`!='') as WORDS, SUM(`QTL-ROOT`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`QTL-ROOT`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`QTL-ROOT`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    if ($FORMULA_TYPE == "LEMMA")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`QTL-LEMMA`!='') as WORDS, SUM(`QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`QTL-LEMMA`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`QTL-LEMMA`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    if ($FORMULA_TYPE == "ROOT-ALL")
    {
        $sql = "SELECT DISTINCT(`SURA-VERSE`), SUM(`ROOT OR PARTICLE`!='') as WORDS, SUM(`ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) as FLAGGED, (SUM(`ROOT OR PARTICLE`!='' AND `FORMULA-$FORMULA_LENGTH-$FORMULA_TYPE`>0) / SUM(`ROOT OR PARTICLE`!='')) as FORMULAIC_DENSITY FROM `QURAN-DATA` WHERE ";
        if ($SURA > 0)
        {
            $sql .= "`SURA`=$SURA";
        }
        else
        {
            $sql .= $RANGE_SQL;
        }
        $sql .= " GROUP BY `SURA-VERSE` ORDER BY $SORT_ORDER";
    }

    $result = db_query($sql);

?>

    <script type="text/javascript">
        FusionCharts.ready(function() {
            var revenueChart = new FusionCharts({
                    "type": "column2d",
                    "renderAt": "chartContainer",
                    // "width": "960",

                    <?php
                    if (!$miniMode)
                    {
                        echo "\"width\": \"1000\",";
                        echo "\"height\": \"50%\",";
                    }
                    else
                    {
                        echo "\"width\": \"100%\",";
                        echo "\"height\": \"100%\",";
                    }
                    ?>

                    "dataFormat": "json",
                    "dataSource": {
                        "chart": {
                            "caption": "",
                            "subCaption": "",
                            "xAxisName": "Verse",
                            "yAxisName": "Formulaic Density",
                            "yAxisMaxValue": "100",
                            "theme": "fint",
                            "showValues": "0"
                        },

                        <?php
                        // POPULATE THE DATASET

                        // if we are showing both Meccan and Medinan suras, we want two datasets and a legend

                                echo "\"data\": [";
                                for ($i = 0; $i < db_rowcount($result); $i++)
                                {
                                    // grab next database row
                                    $ROW = db_return_row($result);

                                    if ($i > 0)
                                    {
                                        echo ",";
                                    }
                                    echo "{";
                                    echo "\"label\": \"Q." . $ROW["SURA-VERSE"] . "\",";

                                    // data point

                                    $value = 0;
                                    if ($ROW["WORDS"] > 0)
                                    {
                                        $value = number_format(($ROW["FLAGGED"] * 100 / $ROW["WORDS"]), 2);
                                    }

                                    echo "\"value\": \"" . $value . "\",";

                                    if (!$miniMode)
                                    {
                                        echo "link:\"../verse_browser.php?V=" . $ROW["SURA-VERSE"] . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE\",";
                                    }

                                    echo  "\"color\": \"#6060ff\"";

                                    echo "}";
                                }

                        ?>
                    ]
                }

            }); revenueChart.render();
        })
    </script>


</head>

<body class='qt-site'>
    <main class='qt-site-content'>

        <?php

if (!$miniMode)
{
    include "../library/menu.php";
    $mini_normal_mode_class = 'normal-mode';
}
else
{
    $mini_normal_mode_class = 'mini-mode';
}

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='$mini_normal_mode_class'>";

echo "<div class='page-header'>";

if ($miniMode)
{
    $expander_link = "../charts/chart_formulaic_density_by_verse.php?TYPE=$FORMULA_TYPE&L=$FORMULA_LENGTH&SURA=$SURA";

    echo "<span class='expander'>";
    echo "  <a href='$expander_link'>";
    echo "    <img src='/images/expand.png' width=12 height=12>";
    echo "  </a>";
    echo "</span>";
}

echo "  <h2>Chart: Formulaic Density by Verse</h2>";
echo "  <h3>$extra_title_text</h3>";

if (!$miniMode)
{
    echo "  <div class='chart-controls'>";

    // ==== formula length and type selection form =====

    echo "<form action='chart_formulaic_density_by_verse.php?S=" . $_GET["S"] . "&SURA=" . $_GET["SURA"] . "&V=" . $_GET["V"] . "' method=POST>";

    echo "<div class='formulaic-pick-table'>";
    echo "<table>";

    echo "<tr>";

    echo "<td>Formula Length</td><td>";
    echo "<input type=radio name=L value=3 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 3)
    {
        echo "checked=checked";
    }
    echo "> 3</input>";
    echo " &nbsp;&nbsp;<input type=radio name=L value=4 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 4)
    {
        echo "checked=checked";
    }
    echo "> 4</input>";
    echo " &nbsp;&nbsp;<input type=radio name=L value=5 onChange='this.form.submit();' ";
    if ($FORMULA_LENGTH == 5)
    {
        echo "checked=checked";
    }
    echo "> 5</input>";

    echo "</td></tr>";

    echo "<tr>";

    echo "<td>Formula Type</td><td>";
    echo "<select name=TYPE onChange='this.form.submit();'>";
    echo "<option value='ROOT'";
    if ($FORMULA_TYPE == "ROOT")
    {
        echo " selected";
    }
    echo ">Root</option>";

    echo "<option value='ROOT-ALL'";
    if ($FORMULA_TYPE == "ROOT-ALL")
    {
        echo " selected";
    }
    echo ">Root (Plus Particle/Pronouns)</option>";

    echo "<option value='LEMMA'";
    if ($FORMULA_TYPE == "LEMMA")
    {
        echo " selected";
    }
    echo ">Lemmata</option>";
    echo "</select>";

    echo "</td></tr>";

    echo "</table></div>";

    echo "</form>";

    // View as table  =====

    echo "  <div class='chart-control view-as-table'>";
    echo "    <a href='../formulae/formulaic_density_by_verse.php?S=" . $_GET["S"] . "&L=$FORMULA_LENGTH&SORT=" . $_GET["SORT"] . "&TYPE=$FORMULA_TYPE&SURA=$SURA&V=" . $_GET["V"] . "'>";
    echo "    View as a Table";
    echo "    </a>";
    echo "  </div>";   // chart-control view-as-table

    echo "  </div>";   // chart-controls
}

echo "</div>";       // page-header

echo "</div>";        // mini_normal_mode_class div

// chart container

echo "  <div id='chartContainer' class='chart-container'";
if ($miniMode)
{
    echo " style='width:520px; height:220px;'";
}
echo "></div>";

// print footer

if (!$miniMode)
{
    include "library/print_control.php";
    include "../library/footer.php";
}

?>

</body>

</html>