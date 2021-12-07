<?php

// global variables needed to make this work

$SEARCH            = "";
$translatorsNeeded = [];
$group_concat_sql  = "";
$master_search_sql = "";

// used to count hits per sura
$hitsPerSura  = [];
$hitsPerVerse = [];

// used to parse verse lists
$RANGE_SQL = "";

// used to turn formulae highlighting on
$FORMULA_SPECIFY_LENGTH = 0;
$FORMULA_SPECIFY_TYPE   = "";

function build_grammatical_tags()
{
    global $SEARCH;

    // check we are looking at @[<tags>]

    if (substr($SEARCH, 0, 2) != "@[" || stripos($SEARCH, "]") === false)
    {
        return "";
    }

    // extract the tags and trim the search string

    $gramm_tags = strtoupper(substr($SEARCH, 2, stripos($SEARCH, "]") - 2));

    $SEARCH = substr($SEARCH, stripos($SEARCH, "]") + 1, strlen($SEARCH));

    // build the clean list of tags

    $parsed_grammatical_tags = "";

    // 1. PART OF SPEECH (NOUN/VERB/ADJC/ADVB/PART)

    $pos_tag = ""; // this will skip past this past of the tagging if they don't specify a part of speech

    if (stripos($gramm_tags, "ADVERB") !== false)
    {
        $pos_tag = "ADVB";
    }
    if (stripos($gramm_tags, "PROPER") !== false || stripos($gramm_tags, "PNOUN") !== false)
    {
        $pos_tag = "PNOU";
    }
    if (stripos($gramm_tags, "PRONOUN") !== false && $pos_tag == "")
    {
        $pos_tag = "PRON";
    }
    if (stripos($gramm_tags, "NOUN") !== false && $pos_tag == "")
    {
        $pos_tag = "NOUN";
    }
    if (stripos($gramm_tags, "ADJ") !== false)
    {
        $pos_tag = "ADJC";
    }
    if (stripos($gramm_tags, "VERB") !== false && $pos_tag == "")
    {
        $pos_tag = "VERB";
    }
    if (stripos($gramm_tags, "PARTICLE") !== false)
    {
        $pos_tag = "PART";
    }
    if (stripos($gramm_tags, "PREPOSITION") !== false)
    {
        $pos_tag = "PARP";
    }

    if ($pos_tag == "")
    {
        $pos_tag = ".{4}";
    } // this will skip past this past of the tagging if they don't specify a part of speech)

    $parsed_grammatical_tags = $pos_tag;

    // 2. MOOD

    $mood_tag = "";

    if (stripos($gramm_tags, "JUSSIVE") !== false)
    {
        $mood_tag = "MOODJUS";
    }
    if (stripos($gramm_tags, "INDICATIVE") !== false)
    {
        $mood_tag = "MOODIND";
    }
    if (stripos($gramm_tags, "SUBJUNCTIVE") !== false)
    {
        $mood_tag = "MOODSUB";
    }

    if ($mood_tag == "")
    {
        $mood_tag = ".{7}";
    } // this will skip past this past of the tagging if they don't specify a mood)

    $parsed_grammatical_tags .= "-" . $mood_tag;

    // 3. FOREIGN WORD (LOAN WORD)

    $foreign_tag = "";

    if (stripos($gramm_tags, "FOREIGN") !== false || stripos($gramm_tags, "LOAN") !== false)
    {
        $foreign_tag = "FOREIGN";
    }

    if ($foreign_tag == "")
    {
        $foreign_tag = ".{7}";
    } // this will skip past this past of the tagging if they don't specify a foreign tag)

    $parsed_grammatical_tags .= "-" . $foreign_tag;

    // 4. CASE

    $case_tag = "";

    if (stripos($gramm_tags, "NOM") !== false)
    {
        $case_tag = "NOM";
    }
    if (stripos($gramm_tags, "ACC") !== false)
    {
        $case_tag = "ACC";
    }
    if (stripos($gramm_tags, "GEN") !== false)
    {
        $case_tag = "GEN";
    }

    if ($case_tag == "")
    {
        $case_tag = ".{3}";
    } // this will skip past this past of the tagging if they don't specify a case)

    $parsed_grammatical_tags .= "-" . $case_tag;

    // 5.  GENDER

    $gender_tag = "";

    if (stripos($gramm_tags, "MASC") !== false)
    {
        $gender_tag = "MASC";
    }
    if (stripos($gramm_tags, "FEM") !== false)
    {
        $gender_tag = "FEMI";
    }

    if ($gender_tag == "")
    {
        $gender_tag = ".{4}";
    } // this will skip past this past of the tagging if they don't specify a gender)

    $parsed_grammatical_tags .= "-" . $gender_tag;

    // 6. NUMBER

    $number_tag = "";

    if (stripos($gramm_tags, "SING") !== false)
    {
        $number_tag = "S";
    }
    if (stripos($gramm_tags, "DUAL") !== false)
    {
        $number_tag = "D";
    }
    if (stripos($gramm_tags, "PLUR") !== false)
    {
        $number_tag = "P";
    }

    if ($number_tag == "")
    {
        $number_tag = ".";
    } // this will skip past this past of the tagging if they don't specify a number)

    $parsed_grammatical_tags .= "-" . $number_tag;

    // 7. PERSON

    $person_tag = "";

    if (stripos($gramm_tags, "1P") !== false)
    {
        $person_tag = "1P";
    }
    if (stripos($gramm_tags, "2P") !== false)
    {
        $person_tag = "2P";
    }
    if (stripos($gramm_tags, "3P") !== false)
    {
        $person_tag = "3P";
    }

    if ($person_tag == "")
    {
        $person_tag = "..";
    } // this will skip past this past of the tagging if they don't specify a person)

    $parsed_grammatical_tags .= "-" . $person_tag;

    // 8. ARABIC FORM

    $form_tag = "";

    if (stripos($gramm_tags, "FORM:12") !== false || stripos($gramm_tags, "FORM:XII") !== false)
    {
        $form_tag = "F12";
    }
    if ((stripos($gramm_tags, "FORM:11") !== false || stripos($gramm_tags, "FORM:XI") !== false) && $form_tag == "")
    {
        $form_tag = "F11";
    }
    if ((stripos($gramm_tags, "FORM:10") !== false || stripos($gramm_tags, "FORM:X") !== false) && $form_tag == "")
    {
        $form_tag = "F10";
    }
    if ((stripos($gramm_tags, "FORM:9") !== false || stripos($gramm_tags, "FORM:IX") !== false) && $form_tag == "")
    {
        $form_tag = "F09";
    }
    if ((stripos($gramm_tags, "FORM:8") !== false || stripos($gramm_tags, "FORM:VIII") !== false) && $form_tag == "")
    {
        $form_tag = "F08";
    }
    if ((stripos($gramm_tags, "FORM:7") !== false || stripos($gramm_tags, "FORM:VII") !== false) && $form_tag == "")
    {
        $form_tag = "F07";
    }
    if ((stripos($gramm_tags, "FORM:6") !== false || stripos($gramm_tags, "FORM:VI") !== false) && $form_tag == "")
    {
        $form_tag = "F06";
    }
    if ((stripos($gramm_tags, "FORM:5") !== false || stripos($gramm_tags, "FORM:V") !== false) && $form_tag == "")
    {
        $form_tag = "F05";
    }
    if ((stripos($gramm_tags, "FORM:4") !== false || stripos($gramm_tags, "FORM:IV") !== false) && $form_tag == "")
    {
        $form_tag = "F04";
    }
    if ((stripos($gramm_tags, "FORM:3") !== false || stripos($gramm_tags, "FORM:III") !== false) && $form_tag == "")
    {
        $form_tag = "F03";
    }
    if ((stripos($gramm_tags, "FORM:2") !== false || stripos($gramm_tags, "FORM:II") !== false) && $form_tag == "")
    {
        $form_tag = "F02";
    }
    if ((stripos($gramm_tags, "FORM:1") !== false || stripos($gramm_tags, "FORM:I") !== false) && $form_tag == "")
    {
        $form_tag = "F01";
    }

    if ($form_tag == "")
    {
        $form_tag = "...";
    } // this will skip past this past of the tagging if they don't specify a form)

    $parsed_grammatical_tags .= "-" . $form_tag;

    // 9. HAPAX OR UNIQUE

    $uniqueness_level_tag = "";

    if (stripos($gramm_tags, "HAPAX") !== false || stripos($gramm_tags, "HPX") !== false)
    {
        $uniqueness_level_tag = "HPX";
    }
    if (stripos($gramm_tags, "UNIQUE") !== false || stripos($gramm_tags, "UNQ") !== false)
    {
        $uniqueness_level_tag = "(UNQ|HPX)";
    }

    if ($uniqueness_level_tag == "")
    {
        $uniqueness_level_tag = "...";
    } // this will skip past this past of the tagging if they don't specify a uniqueness level)

    $parsed_grammatical_tags .= "-" . $uniqueness_level_tag;

    // 10. DEFINITE OR INDEFINITE

    $definite_tag = "";

    if (stripos($gramm_tags, "INDEF") !== false)
    {
        $definite_tag = "IND";
    }
    if (stripos($gramm_tags, "DEF") !== false && $definite_tag == "")
    {
        $definite_tag = "DEF";
    }

    if ($definite_tag == "")
    {
        $definite_tag = "...";
    } // this will skip past this past of the tagging if they don't specify a definite/indefinite status)

    $parsed_grammatical_tags .= "-" . $definite_tag;

    // 11. POSITION

    $position_tag = "";

    if (stripos($gramm_tags, "POS:FIRST") !== false)
    {
        $position_tag = "POS:(FST|1WD)";
    }
    if (stripos($gramm_tags, "POSITION:FIRST") !== false)
    {
        $position_tag = "POS:(FST|1WD)";
    }
    if (stripos($gramm_tags, "POS:START") !== false)
    {
        $position_tag = "POS:(FST|1WD)";
    }
    if (stripos($gramm_tags, "POSITION:START") !== false)
    {
        $position_tag = "POS:(FST|1WD)";
    }

    if (stripos($gramm_tags, "POS:MID") !== false)
    {
        $position_tag = "POS:MID";
    }
    if (stripos($gramm_tags, "POSITION:MID") !== false)
    {
        $position_tag = "POS:MID";
    }
    if (stripos($gramm_tags, "POS:MIDDLE") !== false)
    {
        $position_tag = "POS:MID";
    }
    if (stripos($gramm_tags, "POSITION:MIDDLE") !== false)
    {
        $position_tag = "POS:MID";
    }

    if (stripos($gramm_tags, "POS:LAST") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }
    if (stripos($gramm_tags, "POSITION:LAST") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }
    if (stripos($gramm_tags, "POS:FINAL") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }
    if (stripos($gramm_tags, "POSITION:FINAL") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }
    if (stripos($gramm_tags, "POS:END") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }
    if (stripos($gramm_tags, "POSITION:END") !== false)
    {
        $position_tag = "POS:(LST|1WD)";
    }

    if ($position_tag == "")
    {
        $position_tag = ".{7}";;
    } // this will skip past this past of the tagging if they don't specify a definite/indefinite status)

    $parsed_grammatical_tags .= "-" . $position_tag;

    // 12. RENDERING (word starts with something or ends with something.)

    $render_tag = "";

    // word begins with XXXX

    if (stripos($gramm_tags, "BEGINS:") !== false || stripos($gramm_tags, "STARTS:") !== false)
    {
        // extract the letters we are interested in

        $extract_letters = substr($gramm_tags, stripos($gramm_tags, "ENDS:") + 7);

        if (stripos($extract_letters, " ") !== false)
        {
            $extract_letters = substr($extract_letters, 0, stripos($extract_letters, " "));
        }

        if ($extract_letters != "")
        {
            $render_tag = "RENDER:" . strtolower($extract_letters) . "[^}]*£";
        }
    }

    // word ends with XXXX

    if (stripos($gramm_tags, "ENDS:") !== false)
    {
        // extract the letters we are interested in

        $extract_letters = substr($gramm_tags, stripos($gramm_tags, "ENDS:") + 5);

        if (stripos($extract_letters, " ") !== false)
        {
            $extract_letters = substr($extract_letters, 0, stripos($extract_letters, " "));
        }

        if ($extract_letters != "")
        {
            $render_tag = "RENDER:[^}]*" . strtolower($extract_letters) . "£";
        }
    }

    if ($render_tag == "")
    {
        $render_tag = "RENDER:[^}]*£";
    } // this will skip past this past of the tagging if they don't specify a definite/indefinite status)

    $parsed_grammatical_tags .= "-" . $render_tag;

    // ======

    return $parsed_grammatical_tags;
}

function increment_hit_counts($sura, $verse, $amount, $increaseGlobalPhraseCount)
{
    global $countWordsOrPhrasesInTranslationHighlighted, $hitsPerSura, $hitsPerVerse;

    if (!isset($hitsPerSura[$sura]))
    {
        $hitsPerSura[$sura] = $amount;
    }
    else
    {
        $hitsPerSura[$sura] += $amount;
    }

    // increase verse hit count

    if (!isset($hitsPerVerse[$sura . ":" . $verse]))
    {
        $hitsPerVerse[$sura . ":" . $verse] = $amount;
    }
    else
    {
        $hitsPerVerse[$sura . ":" . $verse] += $amount;
    }

    // track all highlights
    if ($increaseGlobalPhraseCount)
    {
        $countWordsOrPhrasesInTranslationHighlighted += $amount;
    }
}

function error_handler($message)
{
    global $SEARCH;

    echo "<div align=center><br>";

    if (strtolower($_GET["S"]) == "english:\"spiny norman\"")
    {
        echo "<img src='images/qt_spiny.png' style='margin-top:10px;' class='qt-big-logo-header'>";
    }
    else
    {
        echo "<img src='images/logos/qt_logo_only.png' style='margin-top:10px;' class='qt-big-logo-header'>";
    }

    if ($message == "")
    {
        echo "<h3>Your search returned no results or matches</h3>";
    }
    else
    {
        echo "<h3>$message</h3>";
        echo "<h4>In Search: <font color=red>" . htmlentities($_GET["S"]) . "</font></h4>";
    }

    echo "<hr style='width: 500px'>";

    echo "<p><b>Would you like to:</b></p>";

    echo "<div style='text-align: left; width:500px;'>";

    echo "<ul>";

    echo "<li><p><a href='home.php?L=" . htmlentities($_GET["S"]) . "' class='linky'>Edit your search and try again</a></p></li>";
    echo "<li><p><a href='home.php' class='linky'>Start again with a fresh search</p></a></li>";
    echo "<li><p><a href='/help/advanced-searching.php' target='_blank' class='linky'>Read the Help pages for how to search using Qur&rsquo;an Tools</a></p></li>";

    echo "</ul>";

    echo "</div>";

    echo "</div>";  // lower-status-bar

    include "library/footer.php";

    exit;
}

function returnNextCommandPhraseFromString()
{
    global $SEARCH;

    // special case: if we have been passed RANGE:<bookmark name> we return the whole thing;
    // without this check, a bookmark name with a space in it causes a wobbly

    if (strtoupper(substr($SEARCH, 0, 6)) == "RANGE:")
    {
        $bookmarkSQL = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'");

        for ($i = 0; $i < db_rowcount($bookmarkSQL); $i++)
        {
            $ROW = db_return_row($bookmarkSQL);

            if (strtoupper(substr($SEARCH, 6, strlen($ROW["Name"]) + 1)) == strtoupper($ROW["Name"] . " ") || strtoupper(substr($SEARCH, 6, strlen($SEARCH))) == strtoupper($ROW["Name"]))
            {
                $SEARCH = substr($SEARCH, 0, 6) . "\"" . substr($SEARCH, 6, strlen($ROW["Name"])) . "\"" . substr($SEARCH, 6 + strlen($ROW["Name"]), 30);
            }
        }
    }

    // special case: if we have been passed TAG:<tag name> we return the whole thing;
    // without this check, a tag name with a space in it causes a wobbly

    if (strtoupper(substr($SEARCH, 0, 4)) == "TAG:" || strtoupper(substr($SEARCH, 0, 5)) == "TAGS:" || strtoupper(substr($SEARCH, 0, 7)) == "TAGGED:")
    {
        $colon_pos = stripos($SEARCH, ":");

        $tagsSQL = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'");
        for ($i = 0; $i < db_rowcount($tagsSQL); $i++)
        {
            $ROW = db_return_row($tagsSQL);
            if (strtoupper(substr($SEARCH, $colon_pos + 1, strlen($ROW["Tag Name"]))) == strtoupper($ROW["Tag Name"]))
            {
                $SEARCH = substr($SEARCH, 0, $colon_pos + 1) . "\"" . substr($SEARCH, $colon_pos + 1, strlen($ROW["Tag Name"])) . "\"" . substr($SEARCH, $colon_pos + 1 + strlen($ROW["Tag Name"]), 30);
            }
        }
    }

    $midQuoteMarks = false; // used to track if we are mid quote mark

    $word = "";

    while (true)
    {
        $char   = substr($SEARCH, 0, 1);
        $SEARCH = substr($SEARCH, 1, strlen($SEARCH));

        // parentheses are a special case
        if (($char == "(" || $char == ")") && !$midQuoteMarks)
        {
            if ($word == "")
            {
                return $char;
            } // either return the parenthese
            else
            {
                $SEARCH = $char . $SEARCH; // or return the word we have and handle the parenthese next time

                return $word;
            }
        }

        if (!($char == " " && !$midQuoteMarks))
        {
            // the special case of "@" which marks the start of a grammatical tag
            if ($char == "@" && substr($SEARCH, 0, 1) == "[")
            {
                $SEARCH = "@" . $SEARCH;
                return $word;
            }

            $word .= $char;

            if ($char == "\"")
            {
                $midQuoteMarks = !$midQuoteMarks;
            } // tracks if we are mid quote mark
        }
        else
        {
            break;
        }

        if ($SEARCH == "")
        {
            break;
        }
    }

    return $word;
}

function search($searchString, $countHitsPerSura)
{
    global $SEARCH, $bookmarkSuccess; // we will need to rework how we do bookmarks (or at least test it) before this goes live
    global $translatorsNeeded;
    global $globalWordsToHighlight;
    global $globalTranslationPhrasesToHighlight;
    global $globalExactPhrasesToHighlight;
    global $hitsPerSura, $hitsPerVerse;
    global $master_search_sql, $group_concat_sql;
    global $RANGE_SQL;
    global $countWordsOrPhrasesInTranslationHighlighted;

    // logs if we may want to turn formulae highlighting on
    global $FORMULA_SPECIFY_LENGTH, $FORMULA_SPECIFY_TYPE;

    // save to global string
    $SEARCH = $searchString;

    $searchNodes                   = []; // we will fill this up as we process the search
    $regexesToHighlightGlobalWords = [];
    $globalWordsToHighlight        = [];

    $globalTranslationPhrasesToHighlight = [];
    $globalExactPhrasesToHighlight       = [];
    $phrasesToCountInResults             = [];

    // hit counting at the sura or verse level
    $hitsPerSura  = [];
    $hitsPerVerse = [];

    // first, clean up the search string by removing any multiple spaces

    $SEARCH = preg_replace("/ {2,}/m", " ", $SEARCH);

    // in case they have mistyped the IMMEDIATELY FOLLOWED BY command or the IMMEDIATELY PRECEDED BY command

    $SEARCH = str_ireplace("FOLLOWED IMMEDIATELY BY", "IMMEDIATELY FOLLOWED BY", $SEARCH);
    $SEARCH = str_ireplace("FOLLOWED BY IMMEDIATELY", "IMMEDIATELY FOLLOWED BY", $SEARCH);

    $SEARCH = str_ireplace("PRECEDED IMMEDIATELY BY", "IMMEDIATELY PRECEDED BY", $SEARCH);
    $SEARCH = str_ireplace("PRECEDED BY IMMEDIATELY", "IMMEDIATELY PRECEDED BY", $SEARCH);

    // and tidy up any WITHIN tags in the wrong place ...

    $pattern = '/(?i)(?:(?:FOLLOWED WITHIN (\d*) (?:WORDS|WORD) BY)|(?:WITHIN (\d*) (?:WORDS|WORD) FOLLOWED BY))/m';
    $match   = 'FOLLOWED BY WITHIN \1\2';
    $SEARCH  = preg_replace($pattern, $match, $SEARCH);

    $pattern = '/(?i)(?:(?:PRECEDED WITHIN (\d*) (?:WORDS|WORD) BY)|(?:WITHIN (\d*) (?:WORDS|WORD) PRECEDED BY))/m';
    $match   = 'PRECEDED BY WITHIN \1\2';
    $SEARCH  = preg_replace($pattern, $match, $SEARCH);

    // or within plus a colon
    $SEARCH = preg_replace("/(?i)WITHIN:/m", "WITHIN ", $SEARCH);

    // clear error flags
    $errorOccurred = false;
    $errorMessage  = "";

    // concatenation field
    $group_concat_sql = "";

    // debug mode (will show the SQL that is formed if on)
    $debugMode = false;

    // set up translation array to log which translations we must be sure to show
    $translatorsNeeded = [];

    // clean up the RANGE command, to trap dumb entries with spaces between separators, thus turnining
    // e.g. RANGE:2:1-10; 6:4-5 into RANGE 2:1-10;6:4-5

    if (stripos($SEARCH, "RANGE"))
    {
        $regex = "/[R|r][A|a][N|n][G|g][E|e]:([\d|;|,|\-|:|f|F]+)\s+(\d)/m";

        while (preg_match_all($regex, $SEARCH))
        {
            $SEARCH = preg_replace($regex, "RANGE:\\1\\2", $SEARCH);
        }
    }

    // zeitgeist mode (stats on suras that have been browsed)

    if (strtoupper($SEARCH) == "ZEITGEIST")
    {
        echo "<script>";
        echo "window.location.replace('zeitgeist.php');";
        echo "</script>";
        exit;
    }

    $previous_command = ""; // stores the previous commmand; used by e.g. FOLLOWED BY to check it is applicable

    while ($SEARCH != "" && !$errorOccurred)
    {
        // trim any whitespace
        $SEARCH = trim($SEARCH);

        // trap any spaces after colons in command words (e.g. ROOT: ktb)

        $SEARCH = preg_replace("/^(\S*:)(\s)*(\S*)/m", "\\1\\3", $SEARCH);

        // switch out NEXT TO, ADJACENT, or ADJACENT TO, BESIDE for WITHIN 1 WORD

        $adjacent_synonyms = ["ADJACENT TO", "ADJACENT", "BESIDES", "BESIDE", "NEXT TO", "NEXT"];

        foreach ($adjacent_synonyms as $synonym)
        {
            if (strtoupper(substr($SEARCH, 0, strlen($synonym))) == $synonym)
            {
                $SEARCH = "WITHIN 1 " . trim(substr($SEARCH, strlen($synonym)));
            }
        }

        // strip off the next command word from the string

        $command = returnNextCommandPhraseFromString();

        // debug mode
        if (strtoupper($command) == "DEBUG")
        {
            if ($_SESSION['administrator'])
            {
                $debugMode = true;
            }
            continue;
        }

        // AND, OR, NOT COMMANDs, also the ANYTHING command, and also parentheses just get pushed to our growing array
        if (strtoupper($command) == "ANYTHING" || strtoupper($command) == "AND" || strtoupper($command) == "OR" || strtoupper($command) == "NOT" || strtoupper($command) == "(" || strtoupper($command) == ")")
        {
            $searchNodes[]    = strtoupper($command);
            $previous_command = strtoupper($command);
            continue;
        }

        // IMMEDIATELY FOLLOWED BY

        if (strtoupper($command) == "IMMEDIATELY")
        {
            if ($SEARCH == "" || strtoupper($SEARCH) == "FOLLOWED BY" || strtoupper($SEARCH) == "PRECEDED BY" || strtoupper($SEARCH) == "FOLLOWED" || strtoupper($SEARCH) == "PRECEDED")
            {
                $errorOccurred = true;
                error_handler("Error! The IMMEDIATELY FOLLOWED BY or IMMEDIATELY PROCEEDED BY commands<br>need a second search term after them. For example:<p>ROOT:ywm IMMEDIATELY FOLLOWED BY ROOT:qwm</p><p>or</p><p>ROOT:qwm IMMEDIATELY PRECEDED BY ROOT:ywm</p>");
                break;
            }

            if (strtoupper(substr($SEARCH, 0, 12)) == "FOLLOWED BY ")
            {
                if ($previous_command != "[]" && $previous_command != "ROOT" && $previous_command != "LEMMA" && $previous_command != "GLOSS" && $previous_command != "EXACT")
                {
                    $errorOccurred = true;
                    error_handler("Error! The 'IMMEDIATELY FOLLOWED BY' command can only be used between ROOT, LEMMA, EXACT, or GLOSS searches.");
                    break;
                }

                $SEARCH = substr($SEARCH, 12, strlen($SEARCH));

                $searchNodes[]    = "IMMEDIATELY FOLLOWED BY";
                $previous_command = "IMMEDIATELY FOLLOWED BY";
                continue;
            }

            if (strtoupper(substr($SEARCH, 0, 12)) == "PRECEDED BY ")
            {
                if ($previous_command != "[]" && $previous_command != "ROOT" && $previous_command != "LEMMA" && $previous_command != "GLOSS" && $previous_command != "EXACT")
                {
                    $errorOccurred = true;
                    error_handler("Error! The 'IMMEDIATELY PRECEDED BY' command can only be used between ROOT, LEMMA, EXACT, or GLOSS searches.");
                    break;
                }

                $SEARCH = substr($SEARCH, 12, strlen($SEARCH));

                $searchNodes[]    = "IMMEDIATELY PRECEDED BY";
                $previous_command = "IMMEDIATELY PRECEDED BY";
                continue;
            }

            // if we have reached here, something has gone wrong

            $errorOccurred = true;
            error_handler("Error! Use the command 'IMMEDIATELY FOLLOWED BY or 'IMMEDIATELY PRECEDED BY', not simply 'IMMEDIATELY': '$command'");
            break;
        }

        // FOLLOWED BY

        if (strtoupper($command) == "FOLLOWED")
        {
            if (strtoupper(substr($SEARCH, 0, 3)) == "BY ")
            {
                if ($previous_command != "[]" && $previous_command != "ROOT" && $previous_command != "LEMMA" && $previous_command != "GLOSS" && $previous_command != "EXACT")
                {
                    $errorOccurred = true;
                    error_handler("Error! The 'FOLLOWED BY' command can only be used between ROOT, LEMMA, EXACT, or GLOSS searches.");
                    break;
                }

                $SEARCH = substr($SEARCH, 3, strlen($SEARCH));

                // within => if the below is set to a value, we'll execute a within search

                $within_words = "";

                if (strtoupper(substr($SEARCH, 0, 6)) == "WITHIN")
                {
                    $SEARCH = ltrim(substr($SEARCH, 6, strlen($SEARCH)));

                    $within_words = substr($SEARCH, 0, stripos($SEARCH, " "));

                    $SEARCH = substr($SEARCH, stripos($SEARCH, " ") + 1);

                    if ($within_words < 1)
                    {
                        $within_words = "";
                    }

                    if (strtoupper(substr($SEARCH, 0, 5)) == "WORDS")
                    {
                        $SEARCH = ltrim(substr($SEARCH, 5, strlen($SEARCH)));
                    }

                    if (strtoupper(substr($SEARCH, 0, 4)) == "WORD")
                    {
                        $SEARCH = ltrim(substr($SEARCH, 4, strlen($SEARCH)));
                    }
                }

                $searchNodes[]    = "FOLLOWED BY$within_words";
                $previous_command = "FOLLOWED BY";
                continue;
            }
            else
            {
                $errorOccurred = true;
                error_handler("Error! Use the command 'FOLLOWED BY', not simply 'FOLLOWED': '$command'");
                break;
            }
        }

        // within

        if (strtoupper($command) == "WITHIN")
        {
            $within_words = substr($SEARCH, 0, stripos($SEARCH, " "));

            $SEARCH = substr($SEARCH, stripos($SEARCH, " ") + 1);

            if (strtoupper(substr($SEARCH, 0, 5)) == "WORDS")
            {
                $SEARCH = ltrim(substr($SEARCH, 5, strlen($SEARCH)));
            }

            if (strtoupper(substr($SEARCH, 0, 4)) == "WORD")
            {
                $SEARCH = ltrim(substr($SEARCH, 4, strlen($SEARCH)));
            }

            if ($within_words < 1)
            {
                $errorOccurred = true;
                error_handler("Error! The WITHIN command needs a number following it; e.g. WITHIN 4 WORDS");
                break;
            }

            if ($SEARCH == "")
            {
                $errorOccurred = true;
                error_handler("Error! The WITHIN command needs a second search command after it;<br>e.g. ROOT:ktb WITHIN 4 WORDS ROOT:qwl'");
                break;
            }

            $searchNodes[]    = "WITHIN$within_words";
            $previous_command = "WITHIN";
            continue;
        }

        // PRECEDED BY

        if (strtoupper($command) == "PRECEDED")
        {
            if ($SEARCH == "" || strtoupper($SEARCH) == "BY")
            {
                $errorOccurred = true;
                error_handler("Error! The FOLLOWED BY command needs a second search command after it;<br>e.g. ROOT:ktb PRECEDED BY ROOT:qwl'");
                break;
            }

            if (strtoupper(substr($SEARCH, 0, 3)) == "BY ")
            {
                $SEARCH = substr($SEARCH, 3, strlen($SEARCH));

                // within => if the below is set to a value, we'll execute a within search

                $within_words = "";

                if (strtoupper(substr($SEARCH, 0, 6)) == "WITHIN")
                {
                    $SEARCH = ltrim(substr($SEARCH, 6, strlen($SEARCH)));

                    $within_words = substr($SEARCH, 0, stripos($SEARCH, " "));

                    $SEARCH = substr($SEARCH, stripos($SEARCH, " ") + 1);

                    if ($within_words < 1)
                    {
                        $within_words = "";
                    }

                    if (strtoupper(substr($SEARCH, 0, 5)) == "WORDS")
                    {
                        $SEARCH = ltrim(substr($SEARCH, 5, strlen($SEARCH)));
                    }

                    if (strtoupper(substr($SEARCH, 0, 4)) == "WORD")
                    {
                        $SEARCH = ltrim(substr($SEARCH, 4, strlen($SEARCH)));
                    }
                }

                $searchNodes[]    = "PRECEDED BY$within_words";
                $previous_command = "PRECEDED BY";
                continue;
            }
        }

        // JUST A [....] command with no ROOT or LEMMA in front of it?
        if (substr(strtoupper($command), 0, 1) == "[")
        {
            // out $SEARCH back together so the build_grammatical_tags() function is happy, as that's normally there if this is appended to ROOT or LEMMA
            $SEARCH = "@" . $command . " " . $SEARCH;

            $regex_grammatical_tagging = build_grammatical_tags();

            // trim search, as sometimes here it can end up with just a space in it, which is not good!
            $SEARCH = trim($SEARCH);

            $searchNodes[] = "GRAM_TAGS";
            $searchNodes[] = $regex_grammatical_tagging;

            $previous_command = "[]";

            continue;
        }

        // TAG or TAGGED command

        if (substr(strtoupper($command), 0, 4) == "TAG:" || substr(strtoupper($command), 0, 5) == "TAGS:" || substr(strtoupper($command), 0, 7) == "TAGGED:")
        {
            $command = substr($command, stripos($command, ":") + 1, strlen($command));

            // strip off speech marks
            $command = trim($command, '"');

            $searchNodes[]    = "TAG";
            $searchNodes[]    = strtoupper($command);
            $previous_command = "TAG";
            continue;
        }

        // GLOBAL WORD COMMAND (GWORD, OR G, OR GLOBALWORD)
        if (substr(strtoupper($command), 0, 2) == "G:" || substr(strtoupper($command), 0, 6) == "GWORD:" || substr(strtoupper($command), 0, 7) == "GLOBAL:" || substr(strtoupper($command), 0, 11) == "GLOBALWORD:" || substr(strtoupper($command), 0, 3) == "GW:")
        {
            $command = substr($command, stripos($command, ":") + 1, strlen($command));

            $searchNodes[] = "GLOBAL WORD";
            $searchNodes[] = $command;

            $previous_command = "GLOBAL WORD";

            continue;
        }

        // ROOT command

        if (substr(strtoupper($command), 0, 5) == "ROOT:")
        {
            $searchNodes[]    = "ROOT";
            $previous_command = "ROOT";
            $command          = substr($command, 5, strlen($command));

            // build any additional grammatical tagging needed
            $additional_grammatical_tagging = build_grammatical_tags();

            // we now lookup the root
            $escaped    = db_quote($command);
            $rootNumber = db_return_one_record_one_field("SELECT `ROOT ID`  FROM `ROOT-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR  `ENGLISH ALT 1` ='$escaped' OR  `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped'");

            // if the lookup fails, we also try one last attempt, this time with the root forced to lower case
            if ($rootNumber == 0)
            {
                $escaped    = db_quote(strtolower($command));
                $rootNumber = db_return_one_record_one_field("SELECT `ROOT ID` FROM `ROOT-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR BINARY `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped'");
            }

            if ($rootNumber > 0)
            {
                $searchNodes[] = $rootNumber;

                if ($additional_grammatical_tagging != "")
                {
                    $searchNodes[] = "@";
                    $searchNodes[] = $additional_grammatical_tagging;
                }

                continue;
            }
            else
            {
                $errorOccurred = true;
                error_handler("Error! Unknown Root: '$command'");
                break;
            }
        }

        // LEMMA command

        if (substr(strtoupper($command), 0, 6) == "LEMMA:")
        {
            $searchNodes[]    = "LEMMA";
            $previous_command = "LEMMA";
            $command          = substr($command, 6, strlen($command));

            // build any additional grammatical tagging needed
            $additional_grammatical_tagging = build_grammatical_tags();

            // we now lookup the lemma
            // $escaped = db_quote($command);

            $escaped = db_quote(strtolower($command));

            $lemmaNumber = db_return_one_record_one_field("SELECT `LEMMA ID`  FROM `LEMMA-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR BINARY `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped' OR `ARABIC ALTERNATE RENDERING`='$escaped' OR CONCAT(',', `ALTERNATIVE TRANSLITERATION`, ',') LIKE '%,$escaped,%'");

            // if the lookup fails, we try again, this time with the root forced to lower case
            if ($lemmaNumber == 0)
            {
                $escaped     = db_quote(strtolower($command));
                $lemmaNumber = db_return_one_record_one_field("SELECT `LEMMA ID` FROM `LEMMA-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR BINARY `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped' OR CONCAT(',', `ALTERNATIVE TRANSLITERATION`, ',') LIKE '%,$escaped,%'");
            }

            // if the lookup again fails, we a third time, this time with a looser query
            if ($lemmaNumber == 0)
            {
                $escaped     = db_quote($command);
                $lemmaNumber = db_return_one_record_one_field("SELECT `LEMMA ID` FROM `LEMMA-LIST` WHERE `ENGLISH` LIKE '$escaped' OR `ENGLISH TRANSLITERATED` LIKE '$escaped' OR `ARABIC` LIKE '$escaped' OR CONCAT(',', `ALTERNATIVE TRANSLITERATION`, ',') LIKE '%,$escaped,%'");
            }

            if ($lemmaNumber >= 0)
            {
                $searchNodes[] = $lemmaNumber;

                if ($additional_grammatical_tagging != "")
                {
                    $searchNodes[] = "@";
                    $searchNodes[] = $additional_grammatical_tagging;
                }

                continue;
            }
            else
            {
                // $errorOccurred = true;
                // error_handler("Error! Unknown Lemma: '$command'");
                // break;
            }
        }

        // EXACT command

        if (substr(strtoupper($command), 0, 6) == "EXACT:")
        {
            $command = substr($command, 6, strlen($command));

            $previous_command = "EXACT";

            $exactArabicID = db_return_one_record_one_field("SELECT `EXACT ID`  FROM `EXACT-ARABIC-LIST` WHERE `EXACT ARABIC` ='" . db_quote($command) . "'");

            if ($exactArabicID > 0)
            {
                $searchNodes[] = "EXACT-ARABIC";
                $searchNodes[] = $exactArabicID;
                continue;
            }
            else
            {
                // see if it matches an exact transliteration

                $exactTransliteratedID = db_return_one_record_one_field("SELECT `EXACT ID`  FROM `EXACT-TRANSLITERATION-LIST` WHERE `EXACT TRANSLITERATION` ='" . db_quote($command) . "'");

                if ($exactTransliteratedID > 0)
                {
                    $searchNodes[] = "EXACT-TRANSLITERATION";
                    $searchNodes[] = $exactTransliteratedID;
                    continue;
                }
                else
                {
                    $errorOccurred = true;
                    error_handler("Error! Unknown Exact Arabic or Transliterated Form: '$command'");
                    break;
                }
            }
        }

        // GLOSS COMMAND

        if (substr(strtoupper($command), 0, 6) == "GLOSS:")
        {
            if ($command != "")
            {
                $searchNodes[] = "GLOSS";

                $command = substr($command, 6, strlen($command));

                $searchNodes[]    = strtolower($command);
                $previous_command = "GLOSS";
                continue;
            }
            else
            {
                error_handler("Error! Gloss command needs something after it, e.g. GLOSS: \"the record\"");
            }
        }

        // TEXT COMMAND

        if (substr(strtoupper($command), 0, 5) == "TEXT:")
        {
            $command = trim(substr($command, 5, strlen($command)), '"');

            $previous_command = "TEXT";

            // switch hard to soft hyphens, in case they have copied/pasted from the verse browser
            $command = mb_ereg_replace("‑", "-", $command);

            if ($command != "")
            {
                $searchNodes[] = "TEXT";
                $searchNodes[] = strtolower($command);
                continue;
            }
            else
            {
                error_handler("Error! ENGLISH command needs something after it, e.g. ENGLISH: \"people of the book\"");
            }
        }

        // ENGLISH COMMAND

        if (substr(strtoupper($command), 0, 8) == "ENGLISH:")
        {
            $previous_command = "ENGLISH";

            $command = substr($command, 8, strlen($command));

            if ($command != "")
            {
                // trap single letter searches, which can cause an issue with translation tags
                if (strlen($command) == 1)
                {
                    error_handler("Error! The ENGLISH command can't search for a single letter. Try something longer, e.g. ENGLISH:\"throne\"");
                }

                $searchNodes[] = "ENGLISH";
                $searchNodes[] = strtolower($command);
                continue;
            }
            else
            {
                error_handler("Error! ENGLISH command needs something after it, e.g. ENGLISH:\"people of the book\"");
            }
        }

        // ONE OF THE OTHER TRANSLATIONS

        $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION ID`");

        for ($j = 0; $j < db_rowcount($result_translation); $j++)
        {
            $ROW = db_return_row($result_translation);

            if (substr(strtoupper($command), 0, strlen($ROW["TRANSLATION ALL CAPS NAME"]) + 1) == ($ROW["TRANSLATION ALL CAPS NAME"] . ":"))
            {
                // we have matched a translator name, so do the good stuff

                $command = substr($command, strlen($ROW["TRANSLATION ALL CAPS NAME"]) + 1, strlen($command));

                $previous_command = $ROW["TRANSLATION ALL CAPS NAME"];

                if ($command != "")
                {
                    // trap single letter searches, which can cause an issue with translation tags
                    if (strlen($command) == 1)
                    {
                        error_handler("Error! The $previous_command command can't search for a single letter. Try something longer, e.g. $previous_command:\"throne\"");
                    }

                    $searchNodes[] = "TRANSLATION";
                    $searchNodes[] = $ROW["TRANSLATION NAME"];
                    $searchNodes[] = strtolower($command);
                    continue;
                }
                else
                {
                    error_handler("Error! $previous_command command needs something after it, e.g. $previous_command:\"people of the book\"");
                }
            }
        }

        // VERSE LENGTH COMMAND

        // check they haven't tried LENGTH
        if (substr(strtoupper($command), 0, 6) == "LENGTH")
        {
            $command = "VERSE" . $command;
        }

        if (substr(strtoupper($command), 0, 11) == "VERSELENGTH")
        {
            $command = substr($command, 11, strlen($command));

            $previous_command = "VERSELENGTH";

            if (substr($command, 0, 2) == "<>")
            {
                $command = "!=" . substr($command, 2, strlen($command));
            }

            if (substr($command, 0, 1) == ":")
            {
                $command = "=" . substr($command, 1, strlen($command));
            }

            if (substr($command, 0, 2) == ">=" || substr($command, 0, 2) == "<=" || substr($command, 0, 2) == "!=")
            {
                $searchNodes[] = "VERSELENGTH";
                $searchNodes[] = substr($command, 0, 2);
                $searchNodes[] = substr($command, 2, strlen($command));
                continue;
            }

            if (substr($command, 0, 1) == ">" || substr($command, 0, 1) == "=" || substr($command, 0, 1) == "<")
            {
                $searchNodes[] = "VERSELENGTH";
                $searchNodes[] = substr($command, 0, 1);
                $searchNodes[] = substr($command, 1, strlen($command));
                continue;
            }
        }

        // PROVENANCE COMMAND

        if (substr(strtoupper($command), 0, 11) == "PROVENANCE:" || substr(strtoupper($command), 0, 5) == "PROV:")
        {
            $previous_command = "PROVENANCE";

            if (substr(strtoupper($command), 0, 11) == "PROVENANCE:")
            {
                $command = strtoupper(substr($command, 11, strlen($command)));
            }
            else
            {
                $command = strtoupper(substr($command, 5, strlen($command)));
            }

            // expand short forms

            if ($command == "PRE" || $command == "PRE-TRANS")
            {
                $command = "PRE-TRANSITIONAL";
            }
            if ($command == "POST" || $command == "POST-TRANS")
            {
                $command = "POST-TRANSITIONAL";
            }

            if ($command != "MECCAN" && $command != "MEDINAN" && $command != "PRE-TRANSITIONAL" && $command != "POST-TRANSITIONAL" && $command != "MIXED")
            {
                error_handler("Error! Unknown Provenance: '$command'");
            }

            // regular provenance

            if ($command == "MECCAN" || $command == "MEDINAN")
            {
                $searchNodes[] = "PROVENANCE";
                $searchNodes[] = ucfirst(strtolower($command));
            }
            else
            {
                // Durie's classification/provenance
                $searchNodes[] = "DURIE";
                $searchNodes[] = $command;
            }

            continue;
        }

        // FORMULA COMMAND

        if (substr(strtoupper($command), 0, 8) == "FORMULA:")
        {
            $command = trim(substr($command, 8, strlen($command)));

            $previous_command = "FORMULA";

            // does a formula like this exist in the formulaic archetypes table

            $form_find = db_query("SELECT * FROM `FORMULA-ARCHETYPE-LIST-LOWER` WHERE `FORMULA LOWER`='" . db_quote($command) . "'");

            if (db_rowcount($form_find) == 0)
            {
                // LOOK FOR WHAT THEY WANT IN THE ARABIC OR TRANSLITERATED VERSIONS OF THE FORMULA
                // First, though, add spaces, as that's how they are stored in this table
                $command_with_spaces = str_ireplace("+", " + ", $command);

                $form_find = db_query("SELECT * FROM `FORMULA-LIST` WHERE `FORMULA`='" . db_quote($command) . "' OR `FORMULA ARABIC`='" . db_quote($command_with_spaces) . "' OR `FORMULA TRANSLITERATED`='" . db_quote($command_with_spaces) . "'");

                if (db_rowcount($form_find) == 0)
                {
                    $errorOccurred = true;
                    error_handler("Error! Unknown Formula: '$command'");
                    break;
                }
                else
                {
                    $searchNodes[] = "FORMULA";

                    // retrieve the formula ID and save it to the node list

                    $ROWF = db_return_row($form_find);

                    $searchNodes[] = $ROWF["FORMULA ARCHETYPE ID"];

                    // turn on formulaic highlighting
                    if (!isset($_GET["FORMULA"]))
                    {
                        $FORMULA_SPECIFY_LENGTH = $ROWF["LENGTH"];
                        $FORMULA_SPECIFY_TYPE   = $ROWF["TYPE"];
                    }
                }
            }
            else
            {
                $searchNodes[] = "FORMULA";

                // retrieve the formula ID and save it to the node list

                $ROWF = db_return_row($form_find);

                $searchNodes[] = $ROWF["FORMULA ID"];

                // turn on formulaic highlighting
                if (!isset($_GET["FORMULA"]))
                {
                    $FORMULA_SPECIFY_LENGTH = $ROWF["LENGTH"];
                    $FORMULA_SPECIFY_TYPE   = $ROWF["TYPE"];
                }
            }

            continue;
        }

        // DENSITY COMMAND

        if (substr(strtoupper($command), 0, 7) == "DENSITY")
        {
            $command = trim(substr($command, 7, strlen($command)));

            $previous_command = "DENSITY";

            // do we have additional parameters (e.g. @[LENGTH:X TYPE:Y]

            $form_tags = "";

            if (substr($SEARCH, 0, 2) == "@[" && stripos($SEARCH, "]") !== false)
            {
                $form_tags = strtoupper(substr($SEARCH, 2, stripos($SEARCH, "]") - 2));

                $SEARCH = substr($SEARCH, stripos($SEARCH, "]") + 1, strlen($SEARCH));
            }

            if (substr($command, 0, 2) == "<>")
            {
                $command = "!=" . substr($command, 2, strlen($command));
            }

            if (substr($command, 0, 1) == ":")
            {
                $command = "=" . substr($command, 1, strlen($command));
            }

            if (substr($command, 0, 2) == ">=" || substr($command, 0, 2) == "<=" || substr($command, 0, 2) == "!=")
            {
                $searchNodes[] = "DENSITY";
                $searchNodes[] = substr($command, 0, 2);
                $searchNodes[] = substr($command, 2, strlen($command));

                if ($form_tags != "")
                {
                    $searchNodes[] = "@";
                    $searchNodes[] = $form_tags;
                }

                continue;
            }

            if (substr($command, 0, 1) == ">" || substr($command, 0, 1) == "=" || substr($command, 0, 1) == "<")
            {
                $searchNodes[] = "DENSITY";
                $searchNodes[] = substr($command, 0, 1);
                $searchNodes[] = substr($command, 1, strlen($command));

                if ($form_tags != "")
                {
                    $searchNodes[] = "@";
                    $searchNodes[] = $form_tags;
                }

                continue;
            }
        }

        // INTERTEXT COMMAND

        // deal with alternative versions of the command
        if (substr(strtoupper($command), 0, 10) == "INTERTEXTS")
        {
            $command = "INTERTEXT" . substr($command, 10, strlen($command));
        }

        if (substr(strtoupper($command), 0, 9) == "INTERTEXT")
        {
            $command = trim(substr($command, 9, strlen($command)));

            $previous_command = "INTERTEXT";

            if (substr($command, 0, 2) == "<>")
            {
                $command = "!=" . substr($command, 2, strlen($command));
            }

            if (substr($command, 0, 2) == ">=" || substr($command, 0, 2) == "<=" || substr($command, 0, 2) == "!=")
            {
                $searchNodes[] = "INTERTEXT";
                $searchNodes[] = substr($command, 0, 2);
                $searchNodes[] = substr($command, 2, strlen($command));
                continue;
            }

            if (substr($command, 0, 1) == ">" || substr($command, 0, 1) == "=" || substr($command, 0, 1) == "<")
            {
                $searchNodes[] = "INTERTEXT";
                $searchNodes[] = substr($command, 0, 1);
                $searchNodes[] = substr($command, 1, strlen($command));
                continue;
            }
        }

        // RANGE COMMAND

        if (substr(strtoupper($command), 0, 6) == "RANGE:")
        {
            $command = trim(substr($command, 6, strlen($command)));

            $previous_command = "RANGE";

            // does the command match a bookmark?

            // first, if quote marks are involved
            if (substr($command, -1, 1) == "\"" && substr($command, -1, 1) == "\"")
            {
                $bookmarkSQL = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND UPPER(`Name`) = '" . db_quote(strtoupper(substr($command, 1, strlen($command) - 2))) . "'");
            }
            else
            {
                // otherwise we try the whole thing
                $bookmarkSQL = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND UPPER(`Name`) = '" . db_quote(strtoupper($command)) . "'");
            }

            // any matches

            if (db_rowcount($bookmarkSQL) > 0)
            {
                $ROW = db_return_row($bookmarkSQL);

                if ($ROW["Search Dump"] != "")
                {
                    $command = $ROW["Search Dump"];
                }
                else
                {
                    $command = $ROW["Contents"];
                }
            }

            // parse the verse list they have provided

            $RANGE_SQL = "";
            parse_verses($command, true, 0);

            // add table specifier
            $RANGE_SQL = str_ireplace("`SURA`", "qtable.`SURA`", $RANGE_SQL);
            $RANGE_SQL = str_ireplace("`VERSE`", "qtable.`VERSE`", $RANGE_SQL);

            $searchNodes[] = "RANGE";
            $searchNodes[] = $RANGE_SQL;
            continue;
        }

        // LAZY SEARCH - try to make something out of their search

        // lazy step #1 - have they given a root?

        $escaped = db_quote($command);

        $rootNumber = db_return_one_record_one_field("SELECT `ROOT ID`  FROM `ROOT-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR  `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped'");

        if ($rootNumber > 0)
        {
            // label the command as a ROOT search
            $SEARCH = "ROOT:$command " . $SEARCH;
            continue;
        }

        // lazy step #2 - have they given a root but the case is odd?

        $rootTakeTwo = db_return_one_record_one_field("SELECT `ENGLISH` FROM `ROOT-LIST` WHERE UPPER(`ENGLISH`) = UPPER('$escaped')");

        if ($rootTakeTwo != "")
        {
            // label the command as a ROOT search
            $SEARCH = "ROOT:$rootTakeTwo " . $SEARCH;
            continue;
        }

        // lazy step #3 - have they supplied a lemma?

        $lemmaNumber = db_return_one_record_one_field("SELECT `LEMMA ID`  FROM `LEMMA-LIST` WHERE BINARY `ENGLISH` ='$escaped' OR BINARY `ENGLISH TRANSLITERATED` ='$escaped' OR `ARABIC`='$escaped' OR `ARABIC ALTERNATE RENDERING`='$escaped'");

        if ($lemmaNumber > 0)
        {
            // label the command as a LEMMA search
            $SEARCH = "LEMMA:$command " . $SEARCH;
            continue;
        }

        // lazy step #4 - have they supplied some exact Arabic?

        $exactArabicID = db_return_one_record_one_field("SELECT `EXACT ID`  FROM `EXACT-ARABIC-LIST` WHERE `EXACT ARABIC` ='" . db_quote($escaped) . "'");

        $exactTransliteratedID = db_return_one_record_one_field("SELECT `EXACT ID`  FROM `EXACT-TRANSLITERATION-LIST` WHERE `EXACT TRANSLITERATION` ='" . db_quote($escaped) . "'");

        if ($exactArabicID > 0 || $exactTransliteratedID > 0)
        {
            // label the command as an EXACT search
            $SEARCH = "EXACT:$command " . $SEARCH;
            continue;
        }

        // lazy step #5 - have they just written 'Meccan' or 'Medinan' without the PROVENANCE command?

        if (strtoupper($command) == "MECCAN")
        {
            $SEARCH .= "PROVENANCE:MECCAN " . $SEARCH;;
            continue;
        }

        if (strtoupper($command) == "MEDINAN")
        {
            $SEARCH .= "PROVENANCE:MEDINAN " . $SEARCH;;
            continue;
        }

        // lazy step #6 -- turn it into an ENGLISH command

        $SEARCH = "ENGLISH:$command " . $SEARCH;
        continue;
    }

    $searchNodes[] = "END";

    // report any error
    if ($errorOccurred)
    {
        // we need to do something better with the error later
        echo "<p><font color=red>An error occurred: $errorMessage</font></p>";
        exit;
    }

    // DO A SCAN AND REBUILD OF THE NODES ARRAY TO SWAP or BUILD ELEMENTS IF A "PRECEDED BY" IS USED (XX PRECEDED BY YY IS JUST YY FOLLOWED BY XX) ... or if they've used WITHIN

    $counter     = 0;
    $swap_needed = true;

    while ($swap_needed)
    {
        $swap_needed = false;

        foreach ($searchNodes as $node_value)
        {
            // echo "<p>$counter  =  $node_value";

            if (substr($node_value, 0, 11) == "PRECEDED BY" || $node_value == "IMMEDIATELY PRECEDED BY" || substr($node_value, 0, 6) == "WITHIN")
            {
                $start_swap_first = $counter - 2;
                $end_swap_first   = $counter - 1;

                // if they're using clever modifiers, we must go further back

                if ($searchNodes[$start_swap_first] == "@")
                {
                    $start_swap_first -= 2;
                }

                $start_swap_second = $counter + 1;
                $end_swap_second   = $counter + 2;

                // if they're using clever modifiers, we must go further back

                if (isset($searchNodes[$end_swap_second + 1]))
                {
                    if ($searchNodes[$end_swap_second + 1] == "@")
                    {
                        $end_swap_second += 2;
                    }
                }

                $preceded_position = $counter;

                if (substr($node_value, 0, 11) == "PRECEDED BY")
                {
                    $searchNodes[$preceded_position] = "FOLLOWED BY" . substr($node_value, 11);
                }
                elseif ($node_value == "IMMEDIATELY PRECEDED BY")
                {
                    $searchNodes[$preceded_position] = "IMMEDIATELY FOLLOWED BY";
                }

                $swap_needed = true;
            }

            $counter++;
        }

        if ($swap_needed)
        {
            if (substr($searchNodes[$preceded_position], 0, 6) == "WITHIN")
            {
                // echo "<P>Duplicate chunk [$start_swap_first to $end_swap_second], wrapping in parentheses, as we have a WITHIN at $preceded_position";

                if ($start_swap_first > 0)
                {
                    $searchNodes = array_merge(
                        array_slice($searchNodes, 0, $start_swap_first),
                        ["("],
                        array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1),
                        ["FOLLOWED BY" . substr($searchNodes[$preceded_position], 6)],
                        array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1),
                        ["OR"],
                        array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1),
                        ["FOLLOWED BY" . substr($searchNodes[$preceded_position], 6)],
                        array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1),
                        [")"],
                        array_slice($searchNodes, $end_swap_second + 1)
                    );
                }
                else
                {
                    $searchNodes = array_merge(
                        ["("],
                        array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1),
                        ["FOLLOWED BY" . substr($searchNodes[$preceded_position], 6)],
                        array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1),
                        ["OR"],
                        array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1),
                        ["FOLLOWED BY" . substr($searchNodes[$preceded_position], 6)],
                        array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1),
                        [")"],
                        array_slice($searchNodes, $end_swap_second + 1)
                    );
                }

                // foreach( $searchNodes as $index=>$test) {echo "<br>$index = ".htmlentities($test);}

                // exit;
            }
            else
            {
                // echo "<P>Swap chunk [$start_swap_first to $end_swap_first] with [$start_swap_second to $end_swap_second] as we have a PRECEDED at $preceded_position";

                // echo "<p>";

                if ($start_swap_first > 0)
                {
                    $searchNodes = array_merge(array_slice($searchNodes, 0, $start_swap_first), array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1), [$searchNodes[$preceded_position]], array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1), array_slice($searchNodes, $end_swap_second + 1));
                }
                else
                {
                    $searchNodes = array_merge(array_slice($searchNodes, $start_swap_second, $end_swap_second - $start_swap_second + 1), [$searchNodes[$preceded_position]], array_slice($searchNodes, $start_swap_first, $end_swap_first - $start_swap_first + 1), array_slice($searchNodes, $end_swap_second + 1));
                }
            }

            // foreach( $searchNodes as $test) {echo "<p><b>$test</b></p>";}
        }
    }

    // **********************************************************************************
    // WORK THROUGH OUR NODES ARRAY AND BUILD A QUERY OUT OF IT!
    // **********************************************************************************

    $sql = "SELECT * FROM `QURAN-FULL-PARSE` qtable 
	WHERE"; // the search string that we will build

    $followedby = false; // triggered if we deploy a FOLLOWED BY command

    for ($i = 0; $i < count($searchNodes); $i++)
    {
        // AND, OR, NOT, parentheses just get pushed to the $sql

        if ($searchNodes[$i] == "(" || $searchNodes[$i] == ")" || $searchNodes[$i] == "AND" || $searchNodes[$i] == "OR" || $searchNodes[$i] == "NOT")
        {
            $sql .= " " . db_quote($searchNodes[$i]);
        }

        // ANYTHING command

        if ($searchNodes[$i] == "ANYTHING")
        {
            $sql .= " 1=1";
        }

        // FOLLOWED BY

        if (substr($searchNodes[$i], 0, 11) == "FOLLOWED BY")
        {
            // are they doing a within type search?
            $within = "";
            if (strlen($searchNodes[$i]) > 11)
            {
                $within = "WITHIN" . substr($searchNodes[$i], 11);
            }

            $sql .= " FOLLOWEDBY$within ";
            $followedby                      = true;
            $regexesToHighlightGlobalWords[] = "FOLLOWEDBY$within"; // we will use this to later merge this bits of the array together
        }

        // IMMEDIATELY FOLLOWED BY

        if ($searchNodes[$i] == "IMMEDIATELY FOLLOWED BY")
        {
            $sql .= " IMMEDIATELYFOLLOWEDBY ";
            $followedby                      = true;
            $regexesToHighlightGlobalWords[] = "IMMEDIATELY FOLLOWED BY"; // we will use this to later merge this bits of the array together
        }

        // GRAM_TAGS (e.g. just a [<attributes>] search
        if ($searchNodes[$i] == "GRAM_TAGS")
        {
            $GRAMMATICAL_TAGS = db_quote($searchNodes[$i + 1]);
            $i++;

            $sql .= " (`PARSING` REGEXP '@$GRAMMATICAL_TAGS')";

            // add a non-capturing flag to searches for UNIQUE tags and also for first/last words
            $GRAMMATICAL_TAGS = str_ireplace("(UNQ|HPX)", "(?:UNQ|HPX)", $GRAMMATICAL_TAGS);

            // and also for first or last word position
            $GRAMMATICAL_TAGS = str_ireplace("(FST|1WD)", "(?:FST|1WD)", $GRAMMATICAL_TAGS);
            $GRAMMATICAL_TAGS = str_ireplace("(LST|1WD)", "(?:LST|1WD)", $GRAMMATICAL_TAGS);

            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*@$GRAMMATICAL_TAGS/m";
        }

        // GLOBAL WORD COMMAND
        if ($searchNodes[$i] == "GLOBAL WORD")
        {
            $sql .= " `PARSING` REGEXP 'GW:" . db_quote($searchNodes[$i + 1]) . " '";
            $regexesToHighlightGlobalWords[] = "/{GW:(" . $searchNodes[$i + 1] . ") /m";
            $i++;
        }

        // ROOT COMMAND

        if ($searchNodes[$i] == "ROOT")
        {
            $ROOT_NUMBER = db_quote($searchNodes[$i + 1]);
            $i++;

            // check if this is modified with grammatical tags

            $GRAMMATICAL_TAGS = "";

            if ($searchNodes[$i + 1] == "@")
            {
                $GRAMMATICAL_TAGS = db_quote($searchNodes[$i + 2]);
                $i += 2;
            }

            $sql .= " (`PARSING` REGEXP 'R:" . $ROOT_NUMBER . "@$GRAMMATICAL_TAGS')";

            // add a non-capturing flag to searches for UNIQUE tags
            $GRAMMATICAL_TAGS = str_ireplace("(UNQ|HPX)", "(?:UNQ|HPX)", $GRAMMATICAL_TAGS);

            // and also for first or last word position
            $GRAMMATICAL_TAGS = str_ireplace("(FST|1WD)", "(?:FST|1WD)", $GRAMMATICAL_TAGS);
            $GRAMMATICAL_TAGS = str_ireplace("(LST|1WD)", "(?:LST|1WD)", $GRAMMATICAL_TAGS);

            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*R:" . $ROOT_NUMBER . "@$GRAMMATICAL_TAGS/m";
        }

        // LEMMA COMMAND

        if ($searchNodes[$i] == "LEMMA")
        {
            $LEMMA_NUMBER = db_quote($searchNodes[$i + 1]);
            $i++;

            // check if this is modified with grammatical tags

            $GRAMMATICAL_TAGS = "";

            if ($searchNodes[$i + 1] == "@")
            {
                $GRAMMATICAL_TAGS = db_quote($searchNodes[$i + 2]);
                $i += 2;
            }

            $sql .= " (`PARSING` REGEXP 'L:" . $LEMMA_NUMBER . "@$GRAMMATICAL_TAGS')";

            // add a non-capturing flag to searches for UNIQUE tags
            $GRAMMATICAL_TAGS = str_ireplace("(UNQ|HPX)", "(?:UNQ|HPX)", $GRAMMATICAL_TAGS);

            // and also for first or last word position
            $GRAMMATICAL_TAGS = str_ireplace("(FST|1WD)", "(?:FST|1WD)", $GRAMMATICAL_TAGS);
            $GRAMMATICAL_TAGS = str_ireplace("(LST|1WD)", "(?:LST|1WD)", $GRAMMATICAL_TAGS);

            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*L:" . $LEMMA_NUMBER . "@$GRAMMATICAL_TAGS/m";
        }

        // EXACT ARABIC COMMAND

        if ($searchNodes[$i] == "EXACT-ARABIC")
        {
            // $sql.=" `PARSING` LIKE '%EA:".db_quote($searchNodes[$i + 1])."%'";

            $sql .= " (`PARSING` REGEXP 'EA:" . db_quote($searchNodes[$i + 1]) . " ')";

            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*EA:" . $searchNodes[$i + 1] . " /m";
            $i++;
        }

        // EXACT TRANSLITERATION COMMAND

        if ($searchNodes[$i] == "EXACT-TRANSLITERATION")
        {
            // $sql.=" `PARSING` LIKE '%ET:".db_quote($searchNodes[$i + 1])." %'"; // the space before the final % is important, so ET:1554 does't match e.g. ET:15541

            $sql .= " (`PARSING` REGEXP 'ET:" . db_quote($searchNodes[$i + 1]) . " ')";

            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*ET:" . $searchNodes[$i + 1] . " /m";
            $i++;
        }

        // VERSE LENGTH COMMAND
        if ($searchNodes[$i] == "VERSELENGTH")
        {
            $sql .= " `VERSE LENGTH (EXCLUDING QURANIC INITIALS)`" . $searchNodes[$i + 1] . db_quote($searchNodes[$i + 2]);
            $i += 2;
        }

        // PROVENANCE COMMAND

        if ($searchNodes[$i] == "PROVENANCE")
        {
            $sql .= " `Provenance`='" . db_quote($searchNodes[$i + 1]) . "'";
        }

        // TAG COMMAND

        if ($searchNodes[$i] == "TAG")
        {
            if ($searchNodes[$i + 1] == "ANY")
            {
                $sql .= " ((SELECT COUNT(*) FROM `TAGGED-VERSES` tagged_verses 
       LEFT JOIN `TAGS` tag_list ON `TAG ID`=tag_list.`ID`
       WHERE tagged_verses.`SURA-VERSE`=qtable.`SURA-VERSE` AND tagged_verses.`User ID`=" . db_quote($_SESSION['UID']) . " AND `Tag Name`!='') > 0)";
            }
            else
            {
                if ($searchNodes[$i + 1] == "NONE")
                {
                    $sql .= " ((SELECT COUNT(*) FROM `TAGGED-VERSES` tagged_verses WHERE tagged_verses.`SURA-VERSE`=qtable.`SURA-VERSE` AND tagged_verses.`User ID`=" . db_quote($_SESSION['UID']) . ") = 0)";
                }
                else
                {
                    $sql .= " ((SELECT COUNT(*) FROM `TAGGED-VERSES` tagged_verses 
       LEFT JOIN `TAGS` tag_list ON `TAG ID`=tag_list.`ID`
       WHERE tagged_verses.`SURA-VERSE`=qtable.`SURA-VERSE` AND tagged_verses.`User ID`=" . db_quote($_SESSION['UID']) . " AND UPPER(`Tag Name`)  LIKE '" . db_quote(str_ireplace("*", "%", $searchNodes[$i + 1])) . "') > 0)";
                }
            }
            $i += 1;
        }

        // DURIE 'PROVENANCE'/CLASSIFICATION COMMAND

        if ($searchNodes[$i] == "DURIE")
        {
            // $sql.=" `Durie Classification`='".db_quote($searchNodes[$i + 1])."'";

            $sql .= " (SELECT COUNT(*) FROM `SURA-DATA` WHERE `SURA`=`Sura Number` AND `Durie Classification`='" . db_quote($searchNodes[$i + 1]) . "')>0";
        }

        // GLOSS COMMAND

        if ($searchNodes[$i] == "GLOSS")
        {
            $searchPhrase = $searchNodes[$i + 1];

            $searchPhrase = "[[:<:]]" . trim($searchPhrase, '"') . "[[:>:]]";
            $sql .= " (`PARSING` REGEXP 'G:\"[^\"]*" . db_quote($searchPhrase) . "')";
            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*G:\"[^\"]*" . $searchPhrase . "/m";
        }

        // DENSITY COMMAND
        if ($searchNodes[$i] == "DENSITY")
        {
            // do we have additional parameters (e.g. @[LENGTH:X TYPE:Y]

            $form_tags               = "";
            $specific_formula_length = 0;
            $specific_formula_type   = "";

            if ($searchNodes[$i + 3] == "@")
            {
                $form_tags = $searchNodes[$i + 4];
            }

            // have they specified a length?

            if (stripos($form_tags, "LENGTH:3") !== false || stripos($form_tags, "LENGTH=3") !== false || stripos($form_tags, "L:3") !== false || stripos($form_tags, "L=3") !== false)
            {
                $specific_formula_length = 3;
                $FORMULA_SPECIFY_LENGTH  = 3;
            }

            if (stripos($form_tags, "LENGTH:4") !== false || stripos($form_tags, "LENGTH=4") !== false || stripos($form_tags, "L:4") !== false || stripos($form_tags, "L=4") !== false)
            {
                $specific_formula_length = 4;
                $FORMULA_SPECIFY_LENGTH  = 4;
            }

            if (stripos($form_tags, "LENGTH:5") !== false || stripos($form_tags, "LENGTH=5") !== false || stripos($form_tags, "L:5") !== false || stripos($form_tags, "L=5") !== false)
            {
                $specific_formula_length = 5;
                $FORMULA_SPECIFY_LENGTH  = 5;
            }

            // have they specified a type?

            if (stripos($form_tags, "TYPE:ROOT") !== false || stripos($form_tags, "TYPE=ROOT") !== false || stripos($form_tags, "T:ROOT") !== false || stripos($form_tags, "T=ROOT") !== false)
            {
                $specific_formula_type = "ROOT";
                $FORMULA_SPECIFY_TYPE  = "ROOT";
            }

            if (stripos($form_tags, "TYPE:LEMMA") !== false || stripos($form_tags, "TYPE=LEMMA") !== false || stripos($form_tags, "T:LEMMA") !== false || stripos($form_tags, "T=LEMMA") !== false)
            {
                $specific_formula_type = "LEMMA";
                $FORMULA_SPECIFY_TYPE  = "LEMMA";
            }

            if (stripos($form_tags, "TYPE:ROOT-ALL") !== false || stripos($form_tags, "TYPE=ROOT-ALL") !== false || stripos($form_tags, "T:ROOT-ALL") !== false || stripos($form_tags, "T=ROOT-ALL") !== false)
            {
                $specific_formula_type = "LEMMA";
                $FORMULA_SPECIFY_TYPE  = "ROOT-ALL";
            }

            // complete the flags that tell verse_browser.php what to highlight

            if ($FORMULA_SPECIFY_TYPE != "" && $FORMULA_SPECIFY_LENGTH == 0)
            {
                $FORMULA_SPECIFY_LENGTH = 3;
            }
            if ($FORMULA_SPECIFY_TYPE == "" && $FORMULA_SPECIFY_LENGTH != 0)
            {
                $FORMULA_SPECIFY_TYPE = "ROOT";
            }

            // we now iterate across the possible numbers and types and build the query

            $TYPES_OF_FORMULA = ["ROOT", "LEMMA", "ROOT-ALL"];

            $sql .= "(";

            foreach ($TYPES_OF_FORMULA as $TYPE)
            {
                for ($j = 3; $j <= 5; $j++)
                {
                    if (($specific_formula_length == 0 || $specific_formula_length == $j) && ($specific_formula_type == "" || $specific_formula_type == $TYPE))
                    {
                        $sql .= "`FORMULAIC-DENSITY-$j-$TYPE` " . db_quote($searchNodes[$i + 1]) . " " . db_quote($searchNodes[$i + 2]) . " OR ";
                    }
                }
            }

            // strip off last " OR " and add the closing ")"

            $sql = substr($sql, 0, -4) . ")";

            // advance the search pointer (further if formula modifier tags were used)

            if ($form_tags != "")
            {
                $i += 4;
            }
            else
            {
                $i += 2;
            }
        }

        // formula COMMAND
        if ($searchNodes[$i] == "FORMULA")
        {
            $sql .= " `PARSING` REGEXP 'FC:" . db_quote($searchNodes[$i + 1]) . ",'";
            $regexesToHighlightGlobalWords[] = "/{GW:(\d*) [^}]*FC:" . $searchNodes[$i + 1] . ",/m";
            $i++;
        }

        // intertext COMMAND

        if ($searchNodes[$i] == "INTERTEXT")
        {
            $sql .= " `Intertextual Link Count` " . db_quote($searchNodes[$i + 1]) . " " . db_quote($searchNodes[$i + 2]);
            $i += 2;
        }

        // range command
        if ($searchNodes[$i] == "RANGE")
        {
            $sql .= " AND (" . db_quote($searchNodes[$i + 1]) . ")";
            $i++;
        }

        // TEXT COMMAND
        if ($searchNodes[$i] == "TEXT")
        {
            $sql .= " (`RENDERED ARABIC` LIKE '%" . db_quote($searchNodes[$i + 1]) . "%' OR `RENDERED TRANSLITERATION` LIKE '%" . db_quote($searchNodes[$i + 1]) . "%')";

            // save the word/phrase to the global list; verse renderer can then know what to highlight
            $globalExactPhrasesToHighlight[] = $searchNodes[$i + 1];

            // add to the list of words/phrases we will count in the search results
            $phrasesToCountInResults[] = ["TEXT", $searchNodes[$i + 1]];

            $i++;
        }

        // ENGLISH COMMAND

        if ($searchNodes[$i] == "ENGLISH")
        {
            $searchPhrase = $searchNodes[$i + 1];

            // As we are using the ENGLISH command, we populate the $translatorsNeeded array
            // with the name of every translator (in time, we may want to be more selective)

            $translatorsNeeded = [];

            $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`");

            for ($j = 0; $j < db_rowcount($result_translation); $j++)
            {
                $ROW_TRANSLATION     = db_return_row($result_translation);
                $translatorsNeeded[] = $ROW_TRANSLATION["TRANSLATION NAME"];
            }

            if (substr($searchPhrase, 0, 1) == "\"" && stripos($searchPhrase, " ") === false)
            {
                $searchPhrase = "[[:<:]]" . trim($searchPhrase, '"') . "[[:>:]]"; // tells the regexp to look for a whole word

                $searchPhraseEscaped = db_quote($searchPhrase);

                db_goto($result_translation, 0);

                $translator_list_sql = "";

                for ($j = 0; $j < db_rowcount($result_translation); $j++)
                {
                    $ROW_TRANSLATION = db_return_row($result_translation);

                    $translator_list_sql .= ($translator_list_sql == "" ? "" : " OR");

                    $translator_list_sql .= "`Translator " . $ROW_TRANSLATION["TRANSLATION NAME"] . "` REGEXP '$searchPhraseEscaped'";
                }

                $sql .= " ($translator_list_sql)";
            }
            else
            {
                $searchPhrase = trim($searchPhrase, '"'); // trim any quote marks

                $searchPhraseEscaped = db_quote($searchPhrase);

                db_goto($result_translation, 0);

                $translator_list_sql = "";

                for ($j = 0; $j < db_rowcount($result_translation); $j++)
                {
                    $ROW_TRANSLATION = db_return_row($result_translation);

                    $translator_list_sql .= ($translator_list_sql == "" ? "" : " OR");

                    $translator_list_sql .= "`Translator " . $ROW_TRANSLATION["TRANSLATION NAME"] . "` REGEXP '$searchPhraseEscaped'";
                }

                $sql .= " ($translator_list_sql)";
            }

            // save the word/phrase to the global list; verse renderer can then know what to highlight
            $globalTranslationPhrasesToHighlight[] = $searchPhrase;

            // add to the list of words/phrases we will count in the search results
            $phrasesToCountInResults[] = ["All", $searchPhrase];

            $i++;
        }

        // TRANSLATION COMMAND
        if ($searchNodes[$i] == "TRANSLATION")
        {
            $searchTranslation = $searchNodes[$i + 1];
            $searchPhrase      = $searchNodes[$i + 2];

            if (substr($searchPhrase, 0, 1) == "\"" && stripos($searchPhrase, " ") === false)
            {
                $searchPhrase = "[[:<:]]" . trim($searchPhrase, '"') . "[[:>:]]"; // tells the regexp to look for a whole word

                $sql .= " `Translator $searchTranslation` REGEXP '" . db_quote($searchPhrase) . "'";
            }
            else
            {
                $searchPhrase = trim($searchPhrase, '"'); // trim any quote marks

                $sql .= " `Translator $searchTranslation` LIKE '%" . db_quote($searchPhrase) . "%'";
            }

            // note which translator we will need to show
            $translatorsNeeded[$searchTranslation] = $searchTranslation;

            // add to the list of words/phrases we will count in the search results
            $phrasesToCountInResults[] = [$searchTranslation, $searchPhrase];

            // save the word/phrase to the global list; verse renderer can then know what to highlight
            $globalTranslationPhrasesToHighlight[] = $searchPhrase;

            $i += 3;
        }
    }

    // final clean up for neatness

    $sql = trim($sql);
    $sql = preg_replace("/ \)/m", ")", $sql); // replace space+parenthese with bracket
    $sql = preg_replace("/\( /m", "(", $sql); // replace parenthese+parentheses with bracket

    $sql = preg_replace("/ +/m", " ", $sql); // replace two spaces with one

    // replace double ands
    while (stripos($sql, " AND AND "))
    {
        $sql = str_ireplace(" AND AND ", " AND ", $sql);
    }

    // add ordering

    $sql .= " ORDER BY qtable.`SURA`, qtable.`VERSE` ASC";

    // finally, if we have done FOLLOWED BY, some tweakage is needed

    if ($followedby)
    {
        // regular FOLLOWED BY searches

        // $sql = str_ireplace("') FOLLOWEDBY (`PARSING` REGEXP '", ".*", $sql);

        $sql = str_ireplace("') FOLLOWEDBY (`PARSING` REGEXP '", "[^}]*}.*", $sql);

        // followed by within

        while (stripos($sql, "FOLLOWEDBYWITHIN") !== false)
        {
            // okay, now it gets tricky, we need to extract the number part up to the space after it

            $within_pos = stripos($sql, "FOLLOWEDBYWITHIN");

            $space_after_within = stripos(substr($sql, $within_pos), " ");

            $within_value = substr($sql, $within_pos + 16, $space_after_within - 16);

            $sql = substr($sql, 0, $within_pos) . "FOLLOWEDBY" . substr($sql, $within_pos + $space_after_within);

            $sql = str_ireplace("') FOLLOWEDBY (`PARSING` REGEXP '", "([^}]*}-){1,$within_value}[^}]*", $sql);
        }

        // IMMEDIATELY FOLLOWED BY searches (basically scans up to the first {, which is the next word in the parse text

        $sql = str_ireplace("') IMMEDIATELYFOLLOWEDBY (`PARSING` REGEXP '", "[^{]+{GW:[0-9]+ [^}]*", $sql); // ROOT or LEMMA commands
    }

    // debug mode -- should we print the SQL?

    if ($debugMode)
    {
        echo "<div align=center class=message-at-top-of-page-after-action>$sql</div>";
    }

    if ($sql != "")
    {
        $search_result = db_query($sql);

        // save a concatenated version

        // increase space to make sure we can do this

        db_query("SET SESSION group_concat_max_len = 1000000");

        $group_concat_sql = "SELECT GROUP_CONCAT(DISTINCT `SURA-VERSE` ORDER BY `SURA`, `VERSE` SEPARATOR ';')" . substr($sql, 8, strlen($sql));

        // if a bookmark has just been saved, we also need to save the verse dump from this query
        if ($bookmarkSuccess)
        {
            db_query("UPDATE `BOOKMARKS` SET `Search Dump`='" . db_quote(db_return_one_record_one_field($group_concat_sql)) . "' WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Name`='" . db_quote($_POST["BOOKMARK_NAME"]) . "'");
        }
    }

    // do we need to modify the regex highlight array due to FOLLOWED BY commands?
    for ($i = 0; $i < count($regexesToHighlightGlobalWords); $i++)
    {
        if ($regexesToHighlightGlobalWords[$i] == "FOLLOWEDBY")
        {
            // if we find a followed by, we merge the two commands
            $regexesToHighlightGlobalWords[$i - 1] .= $regexesToHighlightGlobalWords[$i + 1];

            // clean it up

            $regexesToHighlightGlobalWords[$i - 1] = str_ireplace("/m/", ".*?", $regexesToHighlightGlobalWords[$i - 1]);

            //  WITHIN TEST [basically allows between 0 and 20 occurrences of }-, the divider between tags
            // the regex looks for any number of characters except } — the tag terminator, then a }-, and allows this up to N times

            // $regexesToHighlightGlobalWords[$i - 1] = str_ireplace("/m/", "(?:[^}]*}-){0,20}?", $regexesToHighlightGlobalWords[$i - 1]);

            // delete this entry ("FOLLOWED BY" and the now redundant command that follows it, then loop through again)

            array_splice($regexesToHighlightGlobalWords, $i, 2);

            $i = -1;

            continue;
        }

        if (substr($regexesToHighlightGlobalWords[$i], 0, 16) == "FOLLOWEDBYWITHIN")
        {
            $within_words = substr($regexesToHighlightGlobalWords[$i], 16);

            // if we find a followed by, we merge the two commands
            $regexesToHighlightGlobalWords[$i - 1] .= $regexesToHighlightGlobalWords[$i + 1];

            // clean it up

            // the regex looks for any number of characters except } — the tag terminator, then a }-, and allows this up to N times

            $regexesToHighlightGlobalWords[$i - 1] = str_ireplace("/m/", "(?:[^}]*}-){0,$within_words}?", $regexesToHighlightGlobalWords[$i - 1]);

            // delete this entry ("FOLLOWED BY" and the now redundant command that follows it, then loop through again)

            array_splice($regexesToHighlightGlobalWords, $i, 2);

            $i = -1;

            continue;
        }

        if ($regexesToHighlightGlobalWords[$i] == "IMMEDIATELY FOLLOWED BY")
        {
            // if we find a followed by, we merge the two commands
            $regexesToHighlightGlobalWords[$i - 1] .= $regexesToHighlightGlobalWords[$i + 1];

            // clean it up

            $regexesToHighlightGlobalWords[$i - 1] = str_ireplace("/m/", "[^{]+", $regexesToHighlightGlobalWords[$i - 1]);

            // delete this entry ("FOLLOWED BY" and the now redundant command that follows it, then loop through again)

            array_splice($regexesToHighlightGlobalWords, $i, 2);

            $i = -1;

            continue;;
        }
    }

    // uncomment the line below to do a test output of our various regexes to highlight array (useful for debugging)

    // foreach ($regexesToHighlightGlobalWords as $test) {echo "<p>$test</p>";}

    // work through the results and build the list of words to highlight

    $global_word_matches = [];

    for ($i = 0; $i < db_rowcount($search_result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($search_result);

        // work through the results and count how many words/phrases in the translations have been scooped up by this search
        foreach ($phrasesToCountInResults as $phraseArray)
        {
            $count = 0;

            if ($phraseArray[0] != "All" && $phraseArray[0] != "TEXT")
            { // in which case it is a translator name
                // unless the search text is tagged as a whole word regexp, we escape it

                if (stripos($phraseArray[1], "[[:<:]]") === false)
                {
                    $replacement_text = preg_quote($phraseArray[1], "/");
                }
                else
                {
                    $replacement_text = $phraseArray[1];
                }

                $count = preg_match_all("/" . $replacement_text . "/i", $ROW["Translator " . $phraseArray[0]]);
            }

            if ($phraseArray[0] == "All")
            {
                // unless the search text is tagged as a whole word regexp, we escape it

                if (stripos($phraseArray[1], "[[:<:]]") === false)
                {
                    $replacement_text = preg_quote($phraseArray[1], "/");
                }
                else
                {
                    $replacement_text = $phraseArray[1];
                }

                $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`");

                $count = 0;

                for ($j = 0; $j < db_rowcount($result_translation); $j++)
                {
                    $ROW_TRANSLATION = db_return_row($result_translation);

                    $count += preg_match_all("/" . $replacement_text . "/i", $ROW["Translator " . $ROW_TRANSLATION["TRANSLATION NAME"]]);
                }
            }

            if ($phraseArray[0] == "TEXT")
            {
                $replacement_text = preg_quote($phraseArray[1], "/");

                $count = preg_match_all("/" . $replacement_text . "/i", $ROW["RENDERED TRANSLITERATION"]);

                $count += preg_match_all("/" . $replacement_text . "/i", $ROW["RENDERED ARABIC"]);
            }

            if ($count > 0)
            {
                increment_hit_counts($ROW["SURA"], $ROW["VERSE"], $count, true);
            }
        }

        // apply each regex to extract things to highlight
        foreach ($regexesToHighlightGlobalWords as $regexItem)
        {
            if ($regexItem != "")
            {
                preg_match_all($regexItem, $ROW["PARSING"], $matches, PREG_SET_ORDER);

                // save each word matched to the master array (checking that it is unique)
                foreach ($matches as $item)
                {
                    // we iterate over the array — items 1 and upwards will be capture group results (e.g. global word nums)

                    for ($process_hits = 1; $process_hits < count($item); $process_hits++)
                    {
                        // be careful we don't duplicate; we only want to tag each global word number once

                        if (!array_key_exists($item[$process_hits], $globalWordsToHighlight))
                        {
                            // save the global word number (as a key/value pair)
                            $globalWordsToHighlight[$item[$process_hits]] = $item[$process_hits];

                            // count hits?
                            if ($countHitsPerSura)
                            {
                                increment_hit_counts($ROW["SURA"], $ROW["VERSE"], 1, false);
                            }
                        }
                    }
                }
            }
        }
    }

    if (array_key_exists(33, $globalWordsToHighlight))
    {
        echo "Array Key exists...";
        exit;
    }

    $master_search_sql = $sql;

    return $search_result;
}
