<?php

// echo "Update: ".$_POST["P"]." with value: ".$_POST["V"]." for user: ".$_POST["U"]." with token: ".$_POST["T"];

require_once '../library/config.php';
require_once 'library/functions.php';

$change_success_message = "Preference Change Saved";

// UPDATE THE APPROPRIATE PREFERENCE

if ($_POST["P"] == "Italics")
{
    if ($_POST["V"] != "1" && $_POST["V"] != "0")
    {
        $_POST["V"] = "1";
    }

    db_query("UPDATE `USERS` SET `Preference Italics Transliteration`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

    echo $change_success_message;
}

if ($_POST["P"] == "HideTransliteration")
{
    if ($_POST["V"] != "1" && $_POST["V"] != "0")
    {
        $_POST["V"] = "1";
    }

    db_query("UPDATE `USERS` SET `Preference Hide Transliteration`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

    echo $change_success_message;
}

if ($_POST["P"] == "FormulaicGlosses")
{
    if ($_POST["V"] != "1" && $_POST["V"] != "0")
    {
        $_POST["V"] = "1";
    }

    db_query("UPDATE `USERS` SET `Preference Formulaic Glosses`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

    echo $change_success_message;
}

if ($_POST["P"] == "ShowQuickTips")
{
    if ($_POST["V"] != "1" && $_POST["V"] != "0")
    {
        $_POST["V"] = "1";
    }

    db_query("UPDATE `USERS` SET `Preference Show Quick Tips`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

    echo $change_success_message;
}

if ($_POST["P"] == "Keyboard")
{
    if ($_POST["V"] != "LTR" && $_POST["V"] != "RTL")
    {
        $_POST["V"] = "LTR";
    }

    db_query("UPDATE `USERS` SET `Preferred Keyboard Direction`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");
    echo "Preference changes saved";
}

if ($_POST["P"] == "FloatingPages")
{
    if ($_POST["V"] != "1" && $_POST["V"] != "0")
    {
        $_POST["V"] = "1";
    }

    db_query("UPDATE `USERS` SET `Preference Floating Page Navigator`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");
    echo "Preference changes saved";
}

if ($_POST["P"] == "Verses")
{
    if ($_POST["V"] > 0 && $_POST["V"] < 500)
    {
        db_query("UPDATE `USERS` SET `Preferred Verse Count`=" . db_quote($_POST["V"]) . " WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");
        // echo " * DONE-T *";
        echo $change_success_message;
    }
}

if ($_POST["P"] == "Mode")
{
    if ($_POST["V"] >= 0 && $_POST["V"] < 3)
    {
        db_query("UPDATE `USERS` SET `Preferred Default Mode`=" . db_quote($_POST["V"]) . " WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");
        // echo " * DONE-T *";
        echo $change_success_message;
    }
}

if ($_POST["P"] == "Translator")
{
    if ($_POST["V"] < 1 || $_POST["V"] > db_return_one_record_one_field("SELECT COUNT(*) FROM `TRANSLATION-LIST`"))
    {
        $_POST["V"] = 1;
    }

    db_query("UPDATE `USERS` SET `Preferred Translation`=" . db_quote($_POST["V"]) . " WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

    echo $change_success_message;
}

// save the search highlight colour preference

if ($_POST["P"] == "ColourSearch")
{
    if (ctype_xdigit($_POST["V"]))
    {
        // we want to get the lightness value, so we can later on work out whether to use black or white text in front of it when highlighting

        $rgb = HTMLToRGB($_POST["V"]);
        $hsl = RGBToHSL($rgb);

        db_query("UPDATE `USERS` SET `Preferred Highlight Colour Lightness Value`='" . ($hsl->lightness) . "', `Preferred Highlight Colour`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

        // echo " * DONE-C * ".($hsl->lightness);
        echo $change_success_message;
    }
}

// save the "cursor" (that highlights Arabic or transliterated words when you point to the other type) highlight colour preference

if ($_POST["P"] == "ColourCursor")
{
    if (ctype_xdigit($_POST["V"]))
    {
        db_query("UPDATE `USERS` SET `Preferred Cursor Colour`='" . db_quote($_POST["V"]) . "' WHERE `User ID`=" . db_quote($_POST["U"]) . " AND `Ajax Token`='" . db_quote($_POST["T"]) . "'");

        // echo " * DONE-C * ".($hsl->lightness);
        echo $change_success_message;
    }
}
