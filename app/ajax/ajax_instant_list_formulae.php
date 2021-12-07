<?php

// this script powers the Instant Details Formulae Lister

// regenerate the session

session_start();
session_regenerate_id();

// check we are being called by a logged in user

if (!isset($_SESSION["UID"]))
{
    exit;
}

// connect to database and load library functions

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'library/arabic.php';
require_once 'library/transliterate.php';

$FORMULA_LENGTH = 3;
if (isset($_GET["L"]))
{
    $FORMULA_LENGTH = db_quote($_GET["L"]);
}

$FORMULA_TYPE = "ROOT";
if (isset($_GET["T"]))
{
    $FORMULA_TYPE = db_quote($_GET["T"]);
}

$GLOBAL_WORD_NUMBER = "1";
if (isset($_GET["W"]))
{
    $GLOBAL_WORD_NUMBER = db_quote($_GET["W"]);
}

$cueResult = db_query("SELECT * FROM `FORMULA-LIST` WHERE `LENGTH`='$FORMULA_LENGTH' AND `TYPE`='$FORMULA_TYPE' AND `END GLOBAL WORD NUMBER`=$GLOBAL_WORD_NUMBER");

for ($i = 0; $i < db_rowcount($cueResult); $i++)
{
    $ROWFORM = db_return_row($cueResult);

    // dividing line if we have more than one formula

    if ($i > 0)
    {
        echo "<hr color=#404040>";
    }

    echo "<p>";

    echo "<table class='formula-popup-list-table'>";

    $link_to_search = "<a href=\"verse_browser.php?S=FORMULA:" . urlencode($ROWFORM["FORMULA"]) . "&FORMULA=$FORMULA_LENGTH&FORMULA_TYPE=$FORMULA_TYPE\">";

    echo "<tr>";
    echo "<td align=right><b>Formula</b></td>";
    echo "<td>";
    echo $link_to_search;
    echo htmlentities($ROWFORM["FORMULA TRANSLITERATED"]);
    echo "</a>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=right><b>Formula Length</b></td>";
    echo "<td>";
    echo "<a href=\"formulae/list_formulae.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE\">";
    echo $FORMULA_LENGTH;
    echo "</a>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=right><b>Formula Type</b></td>";
    echo "<td>";
    echo "<a href=\"formulae/list_formulae.php?L=$FORMULA_LENGTH&TYPE=$FORMULA_TYPE\">";
    echo ucfirst($FORMULA_TYPE);
    echo "</a>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=right><b>Qur’anic Occurrences</b></td>";
    echo "<td>";
    echo $link_to_search;
    echo $ROWFORM["OCCURRENCES"];
    echo "</a>&nbsp;";
    echo "<a href=\"charts/chart_formula_distribution.php?FID=" . $ROWFORM["FORMID"] . "\">";
    echo "<img src='images/st.gif'>";
    echo "</a>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";

    echo "</p>";
}
