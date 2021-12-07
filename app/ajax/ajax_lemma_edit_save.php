<?php

session_start();
session_regenerate_id();

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    exit;
}

require_once '../library/config.php';
require_once 'library/functions.php';

$return_message = "Problem saving your changes.";

// CORRECTED TRANSLITERATION?

if ($_POST["F"] == "CORRECTED TRANSLITERATION")
{
    db_query("UPDATE `LEMMA-LIST` SET `CORRECTED TRANSLITERATION`='" . db_quote($_POST["V"]) . "', `LEMMA FIXED BY USER`=" . db_quote($_SESSION['UID']) . " WHERE `LEMMA ID`=" . db_quote($_POST["I"]));

    $return_message = "Updated the corrected transliteration for Lemma ID #" . $_POST["I"] . ".";
}

if ($_POST["F"] == "ALTERNATIVE TRANSLITERATION")
{
    db_query("UPDATE `LEMMA-LIST` SET `ALTERNATIVE TRANSLITERATION`='" . db_quote($_POST["V"]) . "', `LEMMA FIXED BY USER`=" . db_quote($_SESSION['UID']) . " WHERE `LEMMA ID`=" . db_quote($_POST["I"]));

    $return_message = "Updated the alternative transliteration for Lemma ID #" . $_POST["I"] . ".";
}

if ($_POST["F"] == "LEMMA FIX NOT NEEDED")
{
    if ($_POST["V"] == "true")
    {
        db_query("UPDATE `LEMMA-LIST` SET `LEMMA FIX NOT NEEDED`=1, `LEMMA FIXED BY USER`=" . db_quote($_SESSION['UID']) . " WHERE `LEMMA ID`=" . db_quote($_POST["I"]));
    }
    else
    {
        db_query("UPDATE `LEMMA-LIST` SET `LEMMA FIX NOT NEEDED`=NULL, `LEMMA FIXED BY USER`=" . db_quote($_SESSION['UID']) . " WHERE `LEMMA ID`=" . db_quote($_POST["I"]));
    }

    $return_message = "Updated Lemma ID #" . $_POST["I"];
}

echo $return_message . "^" . str_ireplace("^", " ", $_SESSION['User Name']); // can't include ^ in username during passback
