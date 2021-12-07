<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: 404.php');
}

?>
<html>
    <head>
    <?php
        require '../library/standard_header.php';
        window_title("Edit Formula Full Gloss");
    ?>
     
    
    <?php

    // menubar etc

    require "../library/menu.php";

// grab the formula archetype number

$FORMULA_ARCHETYPE = 0;
if (isset($_GET["ARCHETYPE"]))
{
    $FORMULA_ARCHETYPE = db_quote($_GET["ARCHETYPE"]);
}

if ($FORMULA_ARCHETYPE == 0)
{
    echo "Bad Formula Archetype number.";
    exit;
}

// deal with any changes

$message = "";

if (isset($_POST["FULL_GLOSS"]))
{
    if ($_POST["FULL_GLOSS"] == "")
    {
        $message = "Wiped the full gloss for this formula.";
    }
    else
    {
        $message = "Saved full gloss as: " . htmlentities($_POST["FULL_GLOSS"]);
    }

    db_query("UPDATE `FORMULA-LIST` SET `FORMULA FULL GLOSS`='" . db_quote($_POST["FULL_GLOSS"]) . "' WHERE `FORMULA ARCHETYPE ID`=" . db_quote($FORMULA_ARCHETYPE));
}

// finish setting up

 echo "</head><body class='qt-site'><main class='qt-site-content'>";

echo "<div align=center>";

echo "<h2 class='page-title-text'>Formula Full Gloss Editing Tool</h2>";

echo "<b>Formula Archetype Number: $FORMULA_ARCHETYPE</b>";

// tell user changes have been saved
if ($message)
{
    echo "<div class='message message-success message-at-top-of-interest-page-after-action'>$message</div>";
}
else
{
    echo "<BR><BR>";
}

// load the details of this word from the database

$result = db_query("SELECT * FROM `FORMULA-LIST` WHERE `FORMULA ARCHETYPE ID`=" . db_quote($FORMULA_ARCHETYPE));

$ROW = db_return_row($result);

if (db_rowcount($result) == 0)
{
    echo "Bad Formula Archetype number.";
    exit;
}

// print details of the formula

echo "<table BORDER=1 CELLPADDING=4 CELLSPACING=0>";

echo "<tr>";

    echo "<td>";
        echo "<b>Formula (Transliterated)</b>";
    echo "</td>";

    echo "<td>";
        echo htmlentities($ROW["FORMULA TRANSLITERATED"]);
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td>";
        echo "<b>Formula (Arabic)</b>";
    echo "</td>";

    echo "<td>";
        echo htmlentities($ROW["FORMULA ARABIC"]);
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td>";
        echo "<b>Length</b>";
    echo "</td>";

    echo "<td>";
        echo htmlentities($ROW["LENGTH"]);
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td>";
        echo "<b>Type</b>";
    echo "</td>";

    echo "<td>";
        echo htmlentities($ROW["TYPE"]);
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td>";
        echo "<b>Occurrences</b>";
    echo "</td>";

    echo "<td>";
        echo db_return_one_record_one_field("SELECT COUNT(*) FROM `FORMULA-LIST` WHERE `FORMULA ARCHETYPE ID`=" . db_quote($FORMULA_ARCHETYPE));
    echo "</td>";

echo "</tr>";

echo "<tr>";

    echo "<td valign=top>";
        echo "<b>Full Gloss</b>";
    echo "</td>";

    echo "<td>";

        echo "<form id=pickVerse action='edit_formula_full_gloss.php?ARCHETYPE=$FORMULA_ARCHETYPE' method=POST name=FormName}>";
        echo "<input id='inputText' type=text style='font-size:14px' autofocus NAME=FULL_GLOSS size=50 maxlength=100 autocomplete='off' placeholder='Provide a full formulaic gloss' value='" . htmlspecialchars($ROW["FORMULA FULL GLOSS"], ENT_QUOTES) . "'>";

        echo " <button name=OKbutton type=submit>SAVE</button></FORM>";

    echo "</td>";

echo "</tr>";

echo "</table>";

echo "<p><a href='export_full_gloss_list.php'>Generate SQL list of all formulaic glosses (for exporting etc.)</a></p>";

echo "</div>";

// print footer

require "../library/footer.php";

?>

</body>
</html>