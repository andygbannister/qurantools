<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

$message       = "";
$message_class = "message-success";

// only superusers can view this page; otherwise redirect
if (strtoupper($_SESSION['administrator']) != "SUPERUSER")
{
    header('Location: /404.php');
}

function rebuild_parsing_data($sura)
{
    $sura = db_quote($sura);

    // wipe this sura
    db_query("DELETE FROM `QURAN-FULL-PARSE` WHERE `SURA`=$sura");

    $sura = db_quote($sura);

    // load the data for this sura
    $results_verses = db_query("SELECT DISTINCT(`SURA-VERSE`), `SURA`, `VERSE` FROM `QURAN-DATA` WHERE `SURA`=$sura ORDER BY `SURA`, `VERSE`");

    echo "<p><b>Processing Sura $sura (" . number_format(db_rowcount($results_verses)) . " verses)</b></p>";

    for ($i = 0; $i < db_rowcount($results_verses); $i++)
    {
        // grab next database row
        $ROW = db_return_row($results_verses);

        // this is where we'll build the parse data
        $parse_data = "";

        echo "<p><b>" . $ROW["SURA-VERSE"] . "</b><br>";

        // load up each element in this verse
        $sql = "SELECT * FROM `QURAN-DATA` 	WHERE `SURA-VERSE`='" . $ROW["SURA-VERSE"] . "' ORDER BY `WORD`, `SEGMENT`";

        $result_elements = db_query($sql);

        $parse_data_for_no_root_or_lemma = ""; // used to create a tag code if a word has no root or lemma

        for ($j = 0; $j < db_rowcount($result_elements); $j++)
        {
            $ROW_ELEMENTS = db_return_row($result_elements);

            // start of a word?
            if ($ROW_ELEMENTS["SEGMENT"] == 1)
            {
                if ($j > 0)
                {
                    $parse_data .= "$parse_data_for_no_root_or_lemma}-";
                }

                $parse_data_for_no_root_or_lemma = "";

                $parse_data .= "{GW:" . $ROW_ELEMENTS["GLOBAL WORD NUMBER"]; // global word number

                // EXACT ARABIC RENDERERING

                $exactArabic_ID = db_return_one_record_one_field("SELECT `EXACT ID` FROM `EXACT-ARABIC-LIST` WHERE `EXACT ARABIC`='" . db_quote($ROW_ELEMENTS["RENDERED ARABIC"]) . "'");

                if ($exactArabic_ID == "")
                {
                    echo "PARSING ERROR 03! RENDERED ARABIC UNKNOWN IN EXACT ARABIC LIST! - <b>" . htmlentities($ROW_ELEMENTS["RENDERED ARABIC"]) . " @ GLOBAL WORD: " . $ROW_ELEMENTS["GLOBAL WORD NUMBER"];

                    echo "<p><a href='parsing_tagger.php?REMAKE_ARABIC=Y'>* Click here to remake the EXACT-ARABIC-LIST table; you can then try to run this rebuilding operation again.</a></p>";

                    echo "<p><a href='parsing_tagger.php'>* Click here to return to the tagging home page.</a></p>";

                    // wipe this sura so it can be tried again later
                    db_query("DELETE FROM `QURAN-FULL-PARSE` WHERE `SURA`=$sura");

                    exit;
                }

                $parse_data .= " EA:" . $exactArabic_ID;

                // EXACT TRANSLITERATION RENDERERING

                $exactTransliteration_ID = db_return_one_record_one_field("SELECT `EXACT ID` FROM `EXACT-TRANSLITERATION-LIST` WHERE `EXACT TRANSLITERATION`='" . db_quote($ROW_ELEMENTS["TRANSLITERATED"]) . "'");

                if ($exactTransliteration_ID == "")
                {
                    echo "PARSING ERROR 04! RENDERED ARABIC UNKNOWN IN EXACT TRANSLITERATION RENDERING LIST! - <b>" . htmlentities($ROW_ELEMENTS["TRANSLITERATED"]) . " @ GLOBAL WORD: " . $ROW_ELEMENTS["GLOBAL WORD NUMBER"];

                    echo "<p><a href='parsing_tagger.php?REMAKE_TRANSLITERATION=Y'>* Click here to remake the EXACT-TRANSLITERATION-LIST table; you can then try to run this rebuilding operation again.</a></p>";

                    echo "<p><a href='parsing_tagger.php'>* Click here to return to the tagging home page.</a></p>";

                    // wipe this sura so it can be tried again later
                    db_query("DELETE FROM `QURAN-FULL-PARSE` WHERE `SURA`=$sura");

                    exit;
                }

                $parse_data .= " ET:" . $exactTransliteration_ID;

                // GLOSS

                if ($ROW_ELEMENTS["GLOSS"] != "")
                {
                    $parse_data .= " G:\"" . strtolower($ROW_ELEMENTS["GLOSS"]) . "\"";
                }
            }

            // additional parsing data
            $additional_parsing = "@";

            // 1. what part of speech
            $part_of_speech_tag = "";

            // adverb
            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "adverb") !== false)
            {
                $part_of_speech_tag = "ADVB";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "verb") !== false && $part_of_speech_tag == "")
            {
                $part_of_speech_tag = "VERB";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "proper noun") !== false)
            {
                $part_of_speech_tag = "PNOU";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "pronoun") !== false)
            {
                $part_of_speech_tag = "PRON";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "noun") !== false && $part_of_speech_tag == "")
            {
                $part_of_speech_tag = "NOUN";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "particle") !== false)
            {
                $part_of_speech_tag = "PART";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "Particle-Preposition") !== false)
            {
                $part_of_speech_tag = "PARP";
            }

            if (stripos($ROW_ELEMENTS["QTL-TAG-EXPLAINED"], "adjective") !== false)
            {
                $part_of_speech_tag = "ADJC";
            }

            if ($part_of_speech_tag == "")
            {
                $part_of_speech_tag = "XXXX";
            }

            $additional_parsing .= $part_of_speech_tag;

            // 2. Mood

            $mood_tag = "MOODXXX";

            if ($ROW_ELEMENTS["QTL-TAG-EXPLAINED"] == "Imperfect Verb")
            {
                if ($ROW_ELEMENTS["QTL-MOOD"] == "Jussive")
                {
                    $mood_tag = "MOODJUS";
                }

                if ($ROW_ELEMENTS["QTL-MOOD"] == "Subjunctive")
                {
                    $mood_tag = "MOODSUB";
                }

                if ($ROW_ELEMENTS["QTL-MOOD"] == "Indicative")
                {
                    $mood_tag = "MOODIND";
                }
            }

            $additional_parsing .= "-$mood_tag";

            // 3. Foreign word?

            $foreign_tag = "XXXXXXX";

            if ($ROW_ELEMENTS["AJC FOREIGN WORD"] == 1)
            {
                $foreign_tag = "FOREIGN";
            }

            $additional_parsing .= "-$foreign_tag";

            // 4. NOM(inative), ACCU(sative), GEN(itive) (or XXX if no tag)

            $case_tag = "XXX";

            if (stripos($ROW_ELEMENTS["QTL-CASE"], "nom") !== false)
            {
                $case_tag = "NOM";
            }

            if (stripos($ROW_ELEMENTS["QTL-CASE"], "acc") !== false)
            {
                $case_tag = "ACC";
            }

            if (stripos($ROW_ELEMENTS["QTL-CASE"], "gen") !== false)
            {
                $case_tag = "GEN";
            }

            $additional_parsing .= "-$case_tag";

            // 5. MASC(uline), FEMI(nine) (or XXXX if no tag)

            $gender_tag = "XXXX";

            if (stripos($ROW_ELEMENTS["QTL-GENDER"], "masc") !== false)
            {
                $gender_tag = "MASC";
            }

            if (stripos($ROW_ELEMENTS["QTL-GENDER"], "femi") !== false)
            {
                $gender_tag = "FEMI";
            }

            $additional_parsing .= "-$gender_tag";

            // 6. S(ingular), D(ual), P(lural) (or X if no tag)

            $number_tag = "X";

            if (stripos($ROW_ELEMENTS["QTL-NUMBER"], "sing") !== false)
            {
                $number_tag = "S";
            }
            if (stripos($ROW_ELEMENTS["QTL-NUMBER"], "dual") !== false)
            {
                $number_tag = "D";
            }
            if (stripos($ROW_ELEMENTS["QTL-NUMBER"], "plur") !== false)
            {
                $number_tag = "P";
            }

            $additional_parsing .= "-$number_tag";

            // 7. Person (1P, 2P, 3P or XX if no tag

            $person_tag = "XX";

            if ($ROW_ELEMENTS["QTL-PERSON"] == "1")
            {
                $person_tag = "1P";
            }
            if ($ROW_ELEMENTS["QTL-PERSON"] == "2")
            {
                $person_tag = "2P";
            }
            if ($ROW_ELEMENTS["QTL-PERSON"] == "3")
            {
                $person_tag = "3P";
            }

            $additional_parsing .= "-$person_tag";

            // 8. Arabic form (e.g F10, or XXX)

            $form_tag = "XXX";

            if ($ROW_ELEMENTS["QTL-ARABIC-FORM"] != "")
            {
                switch ($ROW_ELEMENTS["QTL-ARABIC-FORM"])
                {
                    case "I":
                        $form_tag = "F01";
                        break;

                    case "II":
                        $form_tag = "F02";
                        break;

                    case "III":
                        $form_tag = "F03";
                        break;

                    case "IV":
                        $form_tag = "F04";
                        break;

                    case "V":
                        $form_tag = "F05";
                        break;

                    case "VI":
                        $form_tag = "F06";
                        break;

                    case "VII":
                        $form_tag = "F07";
                        break;

                    case "VIII":
                        $form_tag = "F08";
                        break;

                    case "IX":
                        $form_tag = "F09";
                        break;

                    case "X":
                        $form_tag = "F10";
                        break;

                    case "XI":
                        $form_tag = "F11";
                        break;

                    case "XII":
                        $form_tag = "F12";
                        break;
                }
            }

            $additional_parsing .= "-$form_tag";

            // 9. HAPAX or UNIQUE

            $tag_uniqueness = "XXX";

            $root_uniqueness = db_return_one_record_one_field("SELECT `Hapax or Unique` FROM `ROOT-LIST` WHERE `ENGLISH-BINARY`='" . db_quote($ROW_ELEMENTS["QTL-ROOT-BINARY"]) . "'");

            if ($root_uniqueness == "HAPAX")
            {
                $tag_uniqueness = "HPX";
            }
            if ($root_uniqueness == "UNIQUE")
            {
                $tag_uniqueness = "UNQ";
            }

            $additional_parsing .= "-$tag_uniqueness";

            // 10. DEFINITE OR INDEFINITE

            $definiteness = db_query("SELECT * FROM `QURAN-DATA` WHERE `GLOBAL WORD NUMBER`=" . $ROW_ELEMENTS["GLOBAL WORD NUMBER"] . " AND UPPER(`QTL-TAG-EXPLAINED`)='DEFINITE ARTICLE'");

            if (db_rowcount($definiteness) > 0)
            {
                $additional_parsing .= "-DEF";
            }
            else
            {
                $additional_parsing .= "-IND";
            }

            // 11. Position in sentence [first, last, or mid]

            $position_of_word = "MID";

            if ($ROW_ELEMENTS["WORD"] == 1)
            {
                $position_of_word = "FST";
            }

            if ($ROW_ELEMENTS["WORD"] == db_return_one_record_one_field("SELECT MAX(`WORD`) FROM `QURAN-DATA` WHERE `SURA-VERSE`='" . $ROW["SURA-VERSE"] . "'"))
            {
                // if this is a one word verse, this word might be both first AND last, as it were

                if ($position_of_word == "FST")
                {
                    $position_of_word = "1WD";
                }
                else
                {
                    $position_of_word = "LST";
                }
            }

            $additional_parsing .= "-POS:$position_of_word";

            // 12. actual render of word

            $additional_parsing .= "-RENDER:" . $ROW_ELEMENTS["TRANSLITERATED"];

            // $additional_parsing .= "-RENDER:XXX";

            // 13. CHANGES AFFECTING WORD (this should always come last, as the length is not fixed)

            $additional_parsing .= "£-CHANGES:" . $ROW_ELEMENTS["CHANGES AFFECTING WORD"];

            // ==========

            $additional_parsing .= "@";

            // ROOT
            if ($ROW_ELEMENTS["QTL-ROOT"] != "")
            {
                // we look up the root ID in the roots table and save this
                $root_ID = db_return_one_record_one_field("SELECT `ROOT ID` FROM `ROOT-LIST` WHERE  `ENGLISH-BINARY`='" . db_quote($ROW_ELEMENTS["QTL-ROOT-BINARY"]) . "'");

                if ($root_ID == "")
                {
                    echo "PARSING ERROR 01! ROOT UNKNOWN IN ROOT LIST!";
                    exit;
                }

                $parse_data .= " R:" . $root_ID . $additional_parsing;

                $parse_data_for_no_root_or_lemma = "";
            }

            // LEMMA

            if ($ROW_ELEMENTS["QTL-LEMMA"] != "")
            {
                // we look up the root ID in the roots table and save this
                $lemma_ID = db_return_one_record_one_field("SELECT `LEMMA ID` FROM `LEMMA-LIST` WHERE `ENGLISH-BINARY`='" . db_quote($ROW_ELEMENTS["QTL-LEMMA-BINARY"]) . "'");

                if ($lemma_ID == "")
                {
                    $lemma_ID = db_return_one_record_one_field("SELECT `LEMMA ID` FROM `LEMMA-LIST` WHERE `ENGLISH`='" . db_quote($ROW_ELEMENTS["QTL-LEMMA"]) . "'");

                    if ($lemma_ID == "")
                    {
                        echo "PARSING ERROR 02! LEMMA UNKNOWN IN LEMMA LIST! - <b>" . htmlentities($ROW_ELEMENTS["QTL-LEMMA"]) . " - GW:" . $ROW_ELEMENTS["GLOBAL WORD NUMBER"];
                        exit;
                    }
                }

                $parse_data .= " L:" . $lemma_ID . $additional_parsing;

                $parse_data_for_no_root_or_lemma = "";
            }

            // if there is no LEMMA or ROOT, we still tag with the extra parsing data,
            // appended to a fake root number, just so it is searchable $additional_parsing

            if ($ROW_ELEMENTS["QTL-ROOT"] == "" && $ROW_ELEMENTS["QTL-LEMMA"] == "")
            {
                // $parse_data .= " R:X".$additional_parsing;
                $parse_data_for_no_root_or_lemma = " R:X" . $additional_parsing; // we'll use this only if necessary
            }

            // FORMULAIC STUFF

            if ($ROW_ELEMENTS["SEGMENT"] == 1)
            {
                $formulaic_results = db_return_one_record_one_field("SELECT `FCODES`  FROM `FORMULA-ID-CODES-LINKED-GLOBAL-WORDS` WHERE `GLOBAL WORD NUMBER` = " . $ROW_ELEMENTS["GLOBAL WORD NUMBER"]);

                if ($formulaic_results != "")
                {
                    $parse_data .= "$formulaic_results,";
                }
            }
        }

        // close off final word
        $parse_data .= "$parse_data_for_no_root_or_lemma}";

        // save final word
        $sql = "INSERT INTO `QURAN-FULL-PARSE` (`SURA-VERSE`, `SURA`, `VERSE`, `PARSING`) VALUES (
		'" . $ROW["SURA-VERSE"] . "',
		'" . $ROW["SURA"] . "',
		'" . $ROW["VERSE"] . "',
		'" . db_quote($parse_data) . "'
		)
		";

        db_query($sql);

        echo " <b>$sql</b> ";

        // copy over translator data

        $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION ID`");

        $translators = [];

        for ($j = 0; $j < db_rowcount($result_translation); $j++)
        {
            $ROWT = db_return_row($result_translation);

            $translators[] = $ROWT["TRANSLATION NAME"];
        }

        // ====

        // echo "DEBUG WHEN FIRST RUN; CHECK LIST OF TRANSLATORS BELOW<br>";

        // echo implode(", ", $translators);

        // exit;

        // ====

        foreach ($translators as $translator)
        {
            $translation_text = db_return_one_record_one_field("SELECT `Text` FROM `QURAN-TRANSLATION` WHERE `Sura`=$sura AND `Verse`=" . $ROW["VERSE"] . " AND `Translator`='$translator'");

            // clean out any translation tags

            $translation_text = strtolower(preg_replace('/(\<e\d+\>|\<\/e\>)/m', '', $translation_text));

            // and save

            db_query("UPDATE `QURAN-FULL-PARSE` SET `Translator $translator`='" . db_quote($translation_text) . "' WHERE `SURA-VERSE`='" . $ROW["SURA-VERSE"] . "'");
        }

        // update transliteration

        db_query("UPDATE `QURAN-FULL-PARSE` T1 SET `RENDERED TRANSLITERATION`= (SELECT GROUP_CONCAT(`TRANSLITERATED` ORDER BY `WORD` SEPARATOR ' ') FROM `QURAN-DATA` T2 WHERE T2.`SURA-VERSE`=T1.`SURA-VERSE` AND `SEGMENT`=1) WHERE T1.`SURA`=$sura");

        // update arabic

        db_query("UPDATE `QURAN-FULL-PARSE` T1 SET T1.`RENDERED ARABIC`= (SELECT GROUP_CONCAT(T2.`RENDERED ARABIC` ORDER BY `WORD` SEPARATOR ' ') FROM `QURAN-DATA` T2 WHERE T2.`SURA-VERSE`=T1.`SURA-VERSE` AND `SEGMENT`=1) WHERE T1.`SURA`=$sura");
    }

    // update formulaic counts

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-ROOT` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-ROOT`!='')");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-ROOT-FLAGGED-3-ROOT-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-ROOT`!='' AND `FORMULA-3-ROOT`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-ROOT-FLAGGED-4-ROOT-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-ROOT`!='' AND `FORMULA-4-ROOT`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-ROOT-FLAGGED-5-ROOT-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-ROOT`!='' AND `FORMULA-5-ROOT`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-LEMMA` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-LEMMA`!='')");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
	SET `COUNT-QTL-LEMMA-FLAGGED-3-LEMMA-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-LEMMA`!='' AND `FORMULA-3-LEMMA`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
	SET `COUNT-QTL-LEMMA-FLAGGED-4-LEMMA-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-LEMMA`!='' AND `FORMULA-4-LEMMA`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-LEMMA-FLAGGED-5-LEMMA-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND `QTL-LEMMA`!='' AND `FORMULA-5-LEMMA`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
	SET `COUNT-QTL-ROOT-OR-PARTICLE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND (`QTL-LEMMA`!='' OR `QTL-ROOT`!=''))");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
	SET `COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-3-ROOT-ALL-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND (`QTL-LEMMA`!='' OR `QTL-ROOT`!='') AND `FORMULA-3-ROOT-ALL`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
	SET `COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-4-ROOT-ALL-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND (`QTL-LEMMA`!='' OR `QTL-ROOT`!='') AND `FORMULA-4-ROOT-ALL`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` t1
SET `COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-5-ROOT-ALL-FORMULAE` = (SELECT COUNT(*) FROM `QURAN-DATA` t2 WHERE t1.`SURA-VERSE`=t2.`SURA-VERSE` AND (`QTL-LEMMA`!='' OR `QTL-ROOT`!='') AND `FORMULA-5-ROOT-ALL`>0)");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-3-ROOT`=(`COUNT-QTL-ROOT-FLAGGED-3-ROOT-FORMULAE`/`COUNT-QTL-ROOT`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-4-ROOT`=(`COUNT-QTL-ROOT-FLAGGED-4-ROOT-FORMULAE`/`COUNT-QTL-ROOT`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-5-ROOT`=(`COUNT-QTL-ROOT-FLAGGED-5-ROOT-FORMULAE`/`COUNT-QTL-ROOT`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-3-LEMMA`=(`COUNT-QTL-LEMMA-FLAGGED-3-LEMMA-FORMULAE`/`COUNT-QTL-LEMMA`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-4-LEMMA`=(`COUNT-QTL-LEMMA-FLAGGED-4-LEMMA-FORMULAE`/`COUNT-QTL-LEMMA`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-5-LEMMA`=(`COUNT-QTL-LEMMA-FLAGGED-5-LEMMA-FORMULAE`/`COUNT-QTL-LEMMA`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-3-ROOT-ALL`=(`COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-3-ROOT-ALL-FORMULAE`/ `COUNT-QTL-ROOT-OR-PARTICLE`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-4-ROOT-ALL`=(`COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-4-ROOT-ALL-FORMULAE`/ `COUNT-QTL-ROOT-OR-PARTICLE`) * 100");

    db_query("UPDATE `QURAN-FULL-PARSE` SET `FORMULAIC-DENSITY-5-ROOT-ALL`=(`COUNT-QTL-ROOT-OR-PARTICLE-FLAGGED-5-ROOT-ALL-FORMULAE`/ `COUNT-QTL-ROOT-OR-PARTICLE`) * 100");

    // update provenances
    db_query("UPDATE `QURAN-FULL-PARSE` t1 
	LEFT JOIN `SURA-DATA` t2 ON t1.`SURA`=t2.`Sura Number`
	SET t1.`Provenance`=t2.`Provenance`");

    // update verse lengths

    db_query("UPDATE `QURAN-FULL-PARSE` T1 SET `VERSE LENGTH (EXCLUDING QURANIC INITIALS)`=(SELECT MAX(`WORD`) FROM `QURAN-DATA` T2 WHERE T1.`SURA-VERSE`=T2.`SURA-VERSE` AND `Tag Explained`!='Qur\'anic Initials')");
}

// MESSAGE

if (isset($_GET["MESSAGE"]))
{
    $message = $_GET["MESSAGE"];
}

// REMAKE THE TRANSLITERATION LIST
if (isset($_GET["REMAKE_TRANSLITERATION"]))
{
    $message = "The EXACT-TRANSLITERATION-LIST table has been rebuilt; this should solve parsing errors.";

    db_query("TRUNCATE TABLE `EXACT-TRANSLITERATION-LIST`");

    db_query("INSERT INTO `EXACT-TRANSLITERATION-LIST` (`EXACT TRANSLITERATION`) SELECT DISTINCT(`TRANSLITERATED`) FROM `QURAN-DATA` ORDER BY `TRANSLITERATED`");
}

// REMAKE THE ARABIC LIST
if (isset($_GET["REMAKE_ARABIC"]))
{
    $message = "The EXACT-ARABIC-LIST table has been rebuilt; this should solve parsing errors.";

    db_query("TRUNCATE TABLE `EXACT-ARABIC-LIST`");

    db_query("INSERT INTO `EXACT-ARABIC-LIST` (`EXACT ARABIC`) SELECT DISTINCT(`RENDERED ARABIC`) FROM `QURAN-DATA`");
}

// WIPE

if (isset($_GET["WIPE"]))
{
    if ($_GET["WIPE"] == "Y")
    {
        db_query("TRUNCATE TABLE `QURAN-FULL-PARSE`");
        $message = "All parsing data wiped.";
    }
}

// REBUILD ONE SURA

if (isset($_GET["SURA"]))
{
    if ($_GET["SURA"] >= 1 && $_GET["SURA"] <= 114)
    {
        rebuild_parsing_data($_GET["SURA"]);

        // echo "DEBUG.STOP.";
        // exit;

        // tell the javascript at the bottom to reload the window
        $redirect = "parsing_tagger.php?MESSAGE=" . urlencode("Parsing data for sura " . $_GET["SURA"] . " rebuilt.");
    }
}

// REBUILD SEVERAL SURAS

if (isset($_GET["SURA_FROM"]) && isset($_GET["SURA_TO"]))
{
    if ($_GET["SURA_FROM"] < 1 || $_GET["SURA_FROM"] > 114 || $_GET["SURA_TO"] < 1 || $_GET["SURA_TO"] > 114 || $_GET["SURA_FROM"] > $_GET["SURA_TO"])
    {
        $message       = "Something wrong with your sura range, please try again.";
        $message_class = "message-warning";
    }
    else
    {
        for ($i = $_GET["SURA_FROM"]; $i <= $_GET["SURA_TO"]; $i++)
        {
            rebuild_parsing_data($i);
        }

        // tell the javascript at the bottom to reload the window
        $redirect = "parsing_tagger.php?MESSAGE=" . urlencode("Parsing data for suras " . $_GET["SURA_FROM"] . " to " . $_GET["SURA_TO"] . " rebuilt.");
    }
}

// REBULD INTERTEXTUALITY DATA

if (isset($_GET["INTERTEXTUALITY"]))
{
    if ($_GET["INTERTEXTUALITY"] == "Y")
    {
        // wipe the changes counts
        db_query("UPDATE `QURAN-FULL-PARSE` SET `Intertextual Link Count` = 0");

        $result = db_query("SELECT * FROM `INTERTEXTUAL LINKS`");

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            // grab next database row
            $ROW = db_return_row($result);

            echo "<br>";

            echo "ID: " . $ROW["INTERTEXT ID"] . " Q." . $ROW["SURA"] . ":" . $ROW["START VERSE"];

            if ($ROW["START VERSE"] != $ROW["END VERSE"])
            {
                echo "-" . $ROW["END VERSE"];
            }

            $sql = "UPDATE `QURAN-FULL-PARSE` 
            	SET `Intertextual Link Count` = (`Intertextual Link Count` + 1)
            	WHERE `SURA`=" . db_quote($ROW["SURA"]) . "
            	AND `VERSE` >= " . db_quote($ROW["START VERSE"]) . " AND `VERSE` <= " . db_quote($ROW["END VERSE"]) . "";

            db_query($sql);

            echo "<br>";
        }

        echo "<hr>";

        echo "Building verse lists for main sources ...";

        $result = db_query("SELECT * FROM `INTERTEXTUAL SOURCES`");

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            // grab next database row
            $ROW = db_return_row($result);

            $result_refs = db_query("SELECT `SURA`, `START VERSE`, `END VERSE` FROM `INTERTEXTUAL LINKS` WHERE `SOURCE`='" . $ROW["SOURCE ID"] . "' ORDER BY `SURA`, `START VERSE`");

            $full_reference_list = "";

            for ($k = 0; $k < db_rowcount($result_refs); $k++)
            {
                if ($k > 0)
                {
                    $full_reference_list .= ";";
                }

                // grab next database row
                $ROW_QURAN = $result_refs->fetch_assoc();

                $ref = $ROW_QURAN["SURA"] . ":" . $ROW_QURAN["START VERSE"];

                if ($ROW_QURAN["START VERSE"] != $ROW_QURAN["END VERSE"])
                {
                    $ref .= "-" . $ROW_QURAN["END VERSE"];
                }

                $full_reference_list .= "$ref";
            }

            db_query("UPDATE `INTERTEXTUAL SOURCES` SET `VERSE REFERENCES`='$full_reference_list' WHERE `SOURCE ID`='" . $ROW["SOURCE ID"] . "'");
        }

        // tell the javascript at the bottom to reload the window
        $redirect = "parsing_tagger.php?MESSAGE=" . urlencode("Intertextuality links data added to the parsing data");
    }
}

// REBUILD SEVERAL SURAS

?><html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title("Parsing Tagger Tool");
    ?>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>

        <?php

        // menubar

        include "../library/menu.php";

        echo "<div align='center'><h2 style='font-family: Helvetica;'>Parsing Tagger Tool</h2>";

        if (isset($message))
        {
            echo "<p class='$message_class message'>$message</p>";
        }

        $surasTaggedSoFar = db_return_one_record_one_field("SELECT COUNT(DISTINCT `SURA`) FROM `QURAN-FULL-PARSE`");

        echo "<table width='1200'>";

        echo "<tr>";
        echo "<td colspan=12 align='center'>";

        if ($surasTaggedSoFar != 1)
        {
            echo "$surasTaggedSoFar suras have";
        }
        else
        {
            echo "1 sura has";
        }

        echo " parsing data recorded in QURAN-FULL-PARSE. This tool lets you reset parsing data or add it for missing suras. Click any sura button below to rebuild the parsing data for that sura, or enter a range below.";

        if ($surasTaggedSoFar > 0)
        {
            echo " (Suras in <b>bold</b> have parsing data recorded right now).";
        }

        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td colspan=12 align=center><HR>";

        echo "<a href='parsing_tagger.php?WIPE=Y' onClick=\"return confirm('About to wipe any existing parsing data. Click OK to proceed or cancel to abort.');\"><button>Wipe All Parsing Data</button></a>";

        echo " <a href='parsing_tagger.php?INTERTEXTUALITY=Y'><button>Add Intertextuality Counts to Parsing Table</button></a>";

        echo " <a href='parsing_tagger.php'><button>Reload This Page</button></a>";

        echo "<hr>";

        echo "</td>";
        echo "</tr>";

        $colCount = 0;
        $openRow  = false;

        for ($i = 1; $i <= 114; $i++)
        {
            if ($colCount == 0)
            {
                if ($openRow)
                {
                    echo "</tr>";
                }
                echo "<tr>";
                $colCount = 12;
            }

            echo "<td>";

            echo "<a href='parsing_tagger.php?SURA=$i' onClick=\"return confirm('About to rebuild the parsing data for sura $i. Click OK to proceed or cancel to abort.');\">";

            echo "<button ";

            if (db_return_one_record_one_field("SELECT COUNT(*) FROM `QURAN-FULL-PARSE` WHERE `SURA`=$i") > 0)
            {
                echo "style='font-weight:bold;'";
            }

            echo ">";
            echo "Sura $i";
            echo "</button>";
            echo "</a>";
            echo "</td>";

            $colCount--;
        }

        echo "</tr>";

        echo "<tr>";

        echo "<td colspan=12 align=center><hr>";

        echo "<form action='parsing_tagger.php'>";

        echo "Tag Suras From <input ID=SURA_FROM NAME=SURA_FROM SIZE=4";

        if (isset($_GET["SURA_FROM"]))
        {
            echo " VALUE=" . $_GET["SURA_FROM"];
        }

        echo "> to <input ID=SURA_TO NAME=SURA_TO SIZE=4";

        if (isset($_GET["SURA_TO"]))
        {
            echo " VALUE=" . $_GET["SURA_TO"];
        }

        echo "> <input TYPE=SUBMIT VALUE=Go onClick=\"return confirm('About to rebuild the parsing data for suras ' + document.getElementById('SURA_FROM').value + ' to ' + document.getElementById('SURA_TO').value + '. Click OK to proceed or cancel to abort.');\">";

        echo "</form>";

        echo "</td>";

        echo "</tr>";

        echo "</table>";

        include "library/footer.php";

        ?>

</body>

<?php

if (isset($redirect))
{
    echo "<script>";

    echo "window.location.replace('$redirect');";

    echo "</script>";
}
