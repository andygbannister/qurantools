<?php

function print_tags($sura, $verse)
{
    $result_tags = db_query("SELECT `TAG ID`, `Tag Name`, `Tag Colour`, `Tag Lightness Value` FROM `TAGGED-VERSES` T1
	LEFT JOIN `TAGS` T2 ON T2.`ID`=`TAG ID`  
	WHERE `SURA-VERSE`='" . db_quote("$sura:$verse") . "' AND T1.`User ID`=" . db_quote($_SESSION["UID"]) . " ORDER BY `Tag Name`");

    echo "<div class=tag_group id='tag_$sura" . "v" . "$verse'>";

    if (db_rowcount($result_tags) > 0)
    {
        for ($tags = 0; $tags < db_rowcount($result_tags); $tags++)
        {
            echo ($tags > 0) ? " " : "";
            $tag_data = db_return_row($result_tags);

            draw_tag_lozenge($tag_data["Tag Colour"], $tag_data["Tag Lightness Value"]);

            echo "' data-tipped-options=\"ajax: {url:'/ajax/ajax_tag_hover.php', data:{T:" . $tag_data["TAG ID"] . ", V:'$sura:$verse'}}\"'>";
            echo str_replace(" ", "&nbsp;", htmlentities($tag_data["Tag Name"]));
            echo "</span>";
        }
    }

    echo "</div>";
}

function english_translation($s, $v, $t, $translation_tag_mode, $words_or_phrases_to_replace, $skip_translation_tags)
{
    // lookup English text for a reference
    global $highlight_on_format, $highlight_off_format;

    $translation = db_return_one_record_one_field("SELECT `Text` FROM `QURAN-TRANSLATION` WHERE `Translator`='" . db_quote($t) . "' AND `SURA`=" . db_quote($s) . " AND `VERSE`=" . db_quote($v));

    // highlight any words that need highlighting due to search
    // we need to be aware of translation tag codes lurking in the text

    if (count($words_or_phrases_to_replace) > 0)
    {
        foreach ($words_or_phrases_to_replace as $phrase_to_replace)
        {
            // clean up the replacement text (trapping any translation tags)

            $replacement_text = preg_quote($phrase_to_replace, "/");

            $replacement_text = str_ireplace(" ", "[\s|<e\d+a?b?c?d?>|<\/e>]+", $phrase_to_replace) . "[<\/e>]*";

            $translation = preg_replace("/" . $replacement_text . "/i", "$highlight_on_format$0$highlight_off_format", $translation);
        }
    }

    // switch out any word level tags, unless we are in translation tag mode (or skip_translation_tags is TRUE, usually meaning we are in parse mode)

    if (!$translation_tag_mode && !$skip_translation_tags)
    {
        // replace <e> and </e> with span tags

        $translation = preg_replace("/<(e\d+a?b?c?d?)>/", "<span id=\\1 class='\\1 translationText'>", $translation);
        $translation = preg_replace("/<\/e>/", "</span>", $translation);

        // make sure MARK tags and SPAN tages are correct (basically if we hit a </span> between <mark></mark> tags, it'll break the <mark>, so we wrap the </span> itself in </mark> ... <mark> tags

        if (count($words_or_phrases_to_replace) > 0)
        {
            $translation = preg_replace("/(<MARK>)((?:(?!<\/MARK>).)+?)(<\/span>)/m", "\\1\\2</MARK>\\3<MARK>", $translation);

            // bug fixing for the rare case they look for two strings, one that exists inside another
            $translation = str_replace("</</MARK>MARK>", "</MARK></MARK>", $translation);
            $translation = str_replace("<</MARK>MARK>", "</MARK><MARK>", $translation);
        }
    }

    return $translation;
}

function add_context_button($sura, $verse, $word)
{
    // if we are showing the full sura, we can choose not to show this button
    $hide_context_button_if_full_sura_is_shown = false;

    $FULL_SURA_LIST = [];
    if (!in_array($sura, $FULL_SURA_LIST) || !$hide_context_button_if_full_sura_is_shown)
    {
        $start = $verse - 3;
        $end   = $verse + 3;

        if ($start < 1)
        {
            $surplus = 1 - $start;
            $end += $surplus;
            $start = 1;
        }

        if ($end > verses_in_sura($sura))
        {
            $surplus = $end - verses_in_sura($sura);
            $end     = verses_in_sura($sura);
            $start -= $surplus;
            if ($start < 1)
            {
                $start = 1;
            }
        }

        echo "<div id='CON-$sura-$verse-$word' class='verseTools'>";

        echo "<a href='verse_browser.php?V=$sura:$start-$end&B=$sura:$verse'>";

        echo "<img src='/images/cs.gif' class=loupe-tooltip data-tipped-options=\"showDelay: 900, zIndex: 10, ajax: {url:'/ajax/ajax_loupe_verse_viewer.php', data:{S:$sura, V:$start, EndVerse:$end}}\">";

        echo "</a>";

        echo " <span class='copyVerseButton yellow-tooltip' id='COPY-$sura-$verse' " . build_tooltip("COPYVERSE");
        echo "<img src='/images/copysmall.png'>";
        echo "</span>";

        echo "<br>"; //

        echo "<span class='intertextualityButton yellow-tooltip' id='LINK-$sura-$verse' ";

        if (intertextual_count($sura, $verse) > 0)
        {
            // echo build_tooltip("INTERTEXTUALITY");
            // maybe works better without this, but we'll see
            echo ">";
        }
        else
        {
            echo build_tooltip("INTERTEXTUALITY-NONE");
        }

        echo "<span class='loupe-tooltip' data-tipped-options=\"showOn: 'click', close: true, showDelay:0, hideOn: false, hideOthers: true, zIndex: 50000, ajax: {url:'/ajax/ajax_intertextuality_list.php', data:{V:'$sura:$verse'}}\"'>";
        if (intertextual_count($sura, $verse) > 0)
        {
            echo "<img src='/images/link.png' onClick='close_all_toolbars(); Tipped.hideAll();'>";
        }
        else
        {
            echo "<img src='/images/link.png' style='opacity: 0.2;'>";
        }

        echo "</span>";
        echo "</span> ";

        echo "<span class='loupe-tooltip' data-tipped-options=\"showOn: 'click', close: true, showDelay:0, hideOn: false, hideOthers: true, zIndex: 50000, ajax: {url:'/ajax/ajax_tag_toolbox.php', data:{V:'$sura:$verse'}}\"'>";
        echo "<img src='/images/tags.png' onClick='close_all_toolbars();'>";
        echo "</span>";

        echo "</div>";
    }
}

function render_verse_new($sura, $verse, $translations, $boldVerse, $underlineVersesIfNeeded, $highlightWordStart, $highlightWordEnd, $useItalicsForTransliteration, $highlightFormulaLength, $highlightFormulaType, $popUpMode, $searchArrayWordsToHighlight, $showTranslationTagger, $searchArrayTranslationElementsToHighlight, $globalExactPhrasesToHighlight)
{
    global $formula_cue_number_count, $user_preference_turn_off_transliteration;

    // if we are in popup mode, we need to set $_GET["VIEWING_MODE"] so it doesn't throw errors later

    $pop_up_suffix = "";

    if ($popUpMode)
    {
        $_GET["VIEWING_MODE"] = VIEWING_MODE_READ;
        $pop_up_suffix        = "pop"; // ensures that the highlight cursor only works in the mini verse viewer
    }

    // saving a translation tag edit?

    if ($showTranslationTagger && isset($_POST["translationTextField"]))
    {
        if ($_POST["translationTextField"] != "")
        {
            // find the number of the translation we are working on
            $first_translation_value = reset($translations);

            $sql = "UPDATE `QURAN-TRANSLATION` SET `Text`='" . db_quote($_POST["translationTextField"]) . "' WHERE `Sura`=" . db_quote($_POST["SuraField"]) . " AND `Verse`=" . db_quote($_POST["VerseField"]) . " and `Translator`='" . db_quote($first_translation_value) . "'";

            db_query($sql);
        }
    }

    // configure italics for transliteration option

    $user_preference_transliteration_style = "";

    if ($useItalicsForTransliteration)
    {
        $user_preference_transliteration_style = "transliteration_formatting_preference";
    }

    // these are the two strings that the rendering will build into

    $build_transliteration = "";
    $build_arabic          = "";

    // and these will be used for the simple versions, used for the copy/paste functionality

    $build_simple_arabic          = "";
    $build_simple_transliteration = "";
    $simple_translation           = "";

    $verseData = db_query("SELECT * FROM `QURAN-DATA` WHERE `SURA`=" . db_quote($sura) . " AND `VERSE`=" . db_quote($verse) . " AND `SEGMENT`=1 ORDER BY `WORD`");

    for ($i = 0; $i < db_rowcount($verseData); $i++)
    {
        // grab next database row

        $rowdata = db_return_row($verseData);

        // word highlighting

        $highlight_word_start_code = "";
        $highlight_word_end_code   = "";

        // do we need to insert a highlight start code?

        if ($highlightWordStart > 0)
        {
            // first, because this is the exact start word
            if ($highlightWordStart == $rowdata["GLOBAL WORD NUMBER"])
            {
                $highlight_word_start_code = "<MARK>";
            }
            else
            {
                // otherwise because it's the first word of a verse in the middle of a run
                if ($rowdata["WORD"] == 1 && $rowdata["GLOBAL WORD NUMBER"] > $highlightWordStart && $rowdata["GLOBAL WORD NUMBER"] <= $highlightWordEnd)
                {
                    $highlight_word_start_code = "<MARK>";
                }
            }
        }

        // do we need to insert a highlight end code?

        if ($highlightWordEnd > 0)
        {
            if ($highlightWordEnd == $rowdata["GLOBAL WORD NUMBER"])
            {
                $highlight_word_end_code = "</MARK>";
            }
        }

        // formulaic colouring

        $formula_highlight_colour_code = "";
        $formula_cue_number_code       = "";

        if ($highlightFormulaLength != 0)
        {
            if ($rowdata["FORMULA-$highlightFormulaLength-$highlightFormulaType"] == 1)
            {
                $formula_highlight_colour_code = "formulaic-highlight";
            }
        }

        // formulaic cue numbers

        $formula_cue_number_code = "";

        if ($formula_highlight_colour_code != "")
        {
            $cueResult = db_query("SELECT * FROM `FORMULA-LIST` WHERE `LENGTH`='$highlightFormulaLength' AND `TYPE`='$highlightFormulaType' AND `END GLOBAL WORD NUMBER`=" . $rowdata["GLOBAL WORD NUMBER"]);

            if (db_rowcount($cueResult) > 0)
            {
                $formula_cue_number_count++;
                $formula_cue_number_code = "<span class=simple-tooltip data-tipped-options=\"zIndex: 10, hideOthers: true, ajax: {url:'/ajax/ajax_instant_list_formulae.php', data:{W:" . $rowdata["GLOBAL WORD NUMBER"] . ", L:$highlightFormulaLength, T:'$highlightFormulaType'}}\"'><sup><font size=1 color=blue>$formula_cue_number_count</font></sup></span>";
            }
        }

        // search highlighting

        if (count($searchArrayWordsToHighlight) > 0)
        {
            if (array_key_exists($rowdata["GLOBAL WORD NUMBER"], $searchArrayWordsToHighlight))
            {
                $highlight_word_start_code = "<MARK>";
                $highlight_word_end_code   = "</MARK>";
            }
        }

        // add spaces if this is not the first word

        if ($build_arabic != "")
        {
            $build_arabic .= " &nbsp;";
            $build_transliteration .= " ";
        }

        // add translation tag buttons if that option is switched on
        if ($showTranslationTagger)
        {
            $build_transliteration .= "<button class=transEditButton onClick='tagWordButton(" . $rowdata["GLOBAL WORD NUMBER"] . ", $sura, $verse);' ID='tgwn" . $rowdata["GLOBAL WORD NUMBER"] . "'>" . $rowdata["GLOBAL WORD NUMBER"] . "</button>&nbsp;";
        }

        $build_transliteration .= $highlight_word_start_code;

        $build_transliteration .= "<span ID=t" . $rowdata["GLOBAL WORD NUMBER"] . "$pop_up_suffix class=\"transliteratedWord simple-tooltip $formula_highlight_colour_code\" data-tipped-options=\"zIndex: 50000, hideOthers: true, ajax: {url:'/ajax/ajax_instant_parse.php', data:{W:" . $rowdata["GLOBAL WORD NUMBER"] . ", C:0}}\"'>";

        $build_transliteration .= str_replace("-", "&#8209", htmlentities($rowdata["TRANSLITERATED"])); // replace soft with hard hyphen

        $build_transliteration .= "</span>";

        $build_transliteration .= $highlight_word_end_code;
        $build_transliteration .= $formula_cue_number_code;

        // Are we in "Interlinear Mode"?

        if ($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR)
        {
            $LEMMA_LOOKUP = db_query("SELECT * FROM `QURAN-DATA` 
	        WHERE `GLOBAL WORD NUMBER`=" . $rowdata["GLOBAL WORD NUMBER"] . " AND (`QTL-LEMMA`!='' OR `PART OF WORD`='Stem')");

            $LEMMA_DATA = db_return_row($LEMMA_LOOKUP);

            // INTERLINEAR START

            $build_arabic .= "<div class='interlinear-wordbox";

            if ($rowdata["TAG"] == "INL")
            {
                $build_arabic .= " interlinear-wordbox-qinitials";
            }

            $build_arabic .= "'>";
        }

        $build_arabic .= $highlight_word_start_code;
        $build_arabic .= "<span ID=a" . $rowdata["GLOBAL WORD NUMBER"] . "$pop_up_suffix class=\"arabicWord simple-tooltip $formula_highlight_colour_code\" data-tipped-options=\"zIndex: 50000, hideOthers: true, ajax: {url:'/ajax/ajax_instant_parse.php', data:{W:" . $rowdata["GLOBAL WORD NUMBER"] . ", C:0}}\"'>";
        $build_arabic .= htmlentities($rowdata["RENDERED ARABIC"]);
        $build_arabic .= "</span>";
        $build_arabic .= $highlight_word_end_code;
        $build_arabic .= $formula_cue_number_code;
        $build_arabic .= " ";

        if ($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR)
        {
            // Parsing Data

            $INTERLINEAR_PARSE = "";

            if ($LEMMA_DATA["QTL-TAG-EXPLAINED"] != '')
            {
                $INTERLINEAR_PARSE = $LEMMA_DATA["QTL-TAG-EXPLAINED"];

                if (stripos($INTERLINEAR_PARSE, "pronoun") !== false)
                {
                    $INTERLINEAR_PARSE = "Pronoun";
                }

                if (stripos($INTERLINEAR_PARSE, "particle") !== false)
                {
                    $INTERLINEAR_PARSE = "<a href='verse_browser.php?PARSE=2&S=LEMMA:" . urlencode($LEMMA_DATA["QTL-LEMMA"]) . "@[PARTICLE]' class=linky-light>Particle</a>";
                }

                if (stripos($INTERLINEAR_PARSE, "verb") !== false)
                {
                    $INTERLINEAR_PARSE = "<a href='verse_browser.php?PARSE=2&S=LEMMA:" . urlencode($LEMMA_DATA["QTL-LEMMA"]) . "@[VERB]' class=linky-light>Verb</a>";
                }

                if (stripos($INTERLINEAR_PARSE, "noun") !== false)
                {
                    $INTERLINEAR_PARSE = "<a href='verse_browser.php?PARSE=2&S=LEMMA:" . urlencode($LEMMA_DATA["QTL-LEMMA"]) . "@[NOUN]' class=linky-light>Noun</a>";
                }

                if (stripos($INTERLINEAR_PARSE, "conjunction") !== false)
                {
                    $INTERLINEAR_PARSE = "conjunction";
                }
            }

            // now add in more morphological data

            $INTERLINEAR_PARSE_EXTRA        = "";
            $INTERLINEAR_MORPHOLOGY_TOOLTIP = "";
            $INTERLINEAR_MORPHOLOGY_SEARCH  = "";

            if ($LEMMA_DATA["QTL-GENDER"] != "")
            {
                $INTERLINEAR_PARSE_EXTRA .= substr($LEMMA_DATA["QTL-GENDER"], 0, 1);

                $INTERLINEAR_MORPHOLOGY_TOOLTIP = $LEMMA_DATA["QTL-GENDER"];

                $INTERLINEAR_MORPHOLOGY_SEARCH .= $LEMMA_DATA["QTL-GENDER"];
            }

            if ($LEMMA_DATA["QTL-CASE"] != "")
            {
                $INTERLINEAR_PARSE_EXTRA .= (($INTERLINEAR_PARSE_EXTRA != "") ? "-" : "") . substr($LEMMA_DATA["QTL-CASE"], 0, 3);
                $INTERLINEAR_MORPHOLOGY_TOOLTIP .= (($INTERLINEAR_MORPHOLOGY_TOOLTIP != "") ? "; " : "") . $LEMMA_DATA["QTL-CASE"];
                $INTERLINEAR_MORPHOLOGY_SEARCH .= " " . $LEMMA_DATA["QTL-CASE"];
            }

            if ($rowdata["TAG"] == "INL")
            {
                $INTERLINEAR_PARSE_EXTRA = "Qur'anic Initials";
            }

            if ($LEMMA_DATA["QTL-PERSON"] > 0)
            {
                $numbers = ["", "1st", "2nd", "3rd"];

                $INTERLINEAR_PARSE_EXTRA .= (($INTERLINEAR_PARSE_EXTRA != "") ? "-" : "") . $LEMMA_DATA["QTL-PERSON"] . "P";

                $INTERLINEAR_MORPHOLOGY_TOOLTIP .= (($INTERLINEAR_MORPHOLOGY_TOOLTIP != "") ? "; " : "") . $numbers[$LEMMA_DATA["QTL-PERSON"]] . " Person";

                $INTERLINEAR_MORPHOLOGY_SEARCH .= " " . $LEMMA_DATA["QTL-PERSON"] . "P";
            }

            if ($LEMMA_DATA["QTL-NUMBER"] != "")
            {
                $INTERLINEAR_PARSE_EXTRA .= (($INTERLINEAR_PARSE_EXTRA != "") ? "-" : "") . substr($LEMMA_DATA["QTL-NUMBER"], 0, 2);

                $INTERLINEAR_MORPHOLOGY_TOOLTIP .= (($INTERLINEAR_MORPHOLOGY_TOOLTIP != "") ? "; " : "") . $LEMMA_DATA["QTL-NUMBER"];

                $INTERLINEAR_MORPHOLOGY_SEARCH .= " " . $LEMMA_DATA["QTL-NUMBER"];
            }

            if ($LEMMA_DATA["QTL-ARABIC-FORM"] != "")
            {
                $INTERLINEAR_PARSE_EXTRA .= (($INTERLINEAR_PARSE_EXTRA != "") ? "-" : "") . "F:" . $LEMMA_DATA["QTL-ARABIC-FORM"];

                $INTERLINEAR_MORPHOLOGY_TOOLTIP .= (($INTERLINEAR_MORPHOLOGY_TOOLTIP != "") ? "; " : "") . "FORM " . $LEMMA_DATA["QTL-ARABIC-FORM"];

                $INTERLINEAR_MORPHOLOGY_SEARCH .= " FORM:" . $LEMMA_DATA["QTL-ARABIC-FORM"];
            }

            if ($rowdata["TAG"] != "INL")
            {
                $INTERLINEAR_PARSE .= "<br>";
            }
            $INTERLINEAR_PARSE .= "<span class='morphology yellow-tooltip' title='$INTERLINEAR_MORPHOLOGY_TOOLTIP'>";

            if ($INTERLINEAR_MORPHOLOGY_SEARCH != "")
            {
                $INTERLINEAR_PARSE .= "<a href='verse_browser.php?S=[" . urlencode($INTERLINEAR_MORPHOLOGY_SEARCH) . "]' class=linky-light>";
            }

            $INTERLINEAR_PARSE .= "&nbsp;" . $INTERLINEAR_PARSE_EXTRA;

            if ($INTERLINEAR_MORPHOLOGY_SEARCH != "")
            {
                $INTERLINEAR_PARSE .= "</a>";
            }

            $INTERLINEAR_PARSE_EXTRA .= "</span>";

            $build_arabic .= "<br><span class=interlinear-text>" . $rowdata["GLOSS"] . "</span>";

            $build_arabic .= "<br><span class='interlinear-text interlinear-morphology'>" . $INTERLINEAR_PARSE . "</span>";

            if ($rowdata["TAG"] != "INL")
            {
                if ($LEMMA_DATA["QTL-LEMMA"] != '')
                {
                    $build_arabic .= "<br><a href='verse_browser.php?PARSE=2&S=LEMMA:" . urlencode($LEMMA_DATA["QTL-LEMMA"]) . "' class=linky>" . return_arabic_word($LEMMA_DATA["QTL-LEMMA"]);

                    if (!$user_preference_turn_off_transliteration)
                    {
                        $build_arabic .= "<span class=interlinear-text> (<i>" . transliterate_new($LEMMA_DATA["QTL-LEMMA"]) . "</i>)</span>";
                    }
                    $build_arabic .= "</a>";
                }
                else
                {
                    $build_arabic .= "<br>&nbsp;";
                }
            }

            if ($LEMMA_DATA["QTL-ROOT"] != '')
            {
                $build_arabic .= "<br><a href='verse_browser.php?PARSE=2&S=ROOT:" . urlencode($LEMMA_DATA["QTL-ROOT"]) . "' class=linky>" . return_arabic_word($LEMMA_DATA["QTL-ROOT"]);

                if (!$user_preference_turn_off_transliteration)
                {
                    $build_arabic .= "<span class=interlinear-text> (<i>" . transliterate_new($LEMMA_DATA["QTL-ROOT"]) . "</i>)</span>";
                }
                $build_arabic .= "</a>";
            }
            else
            {
                $build_arabic .= "<br>&nbsp;";
            }

            $build_arabic .= "</div>"; // end of interlinear wordbox
        }

        // simple Arabic and simple Transliteration
        $build_simple_arabic .= htmlentities($rowdata["RENDERED ARABIC"]) . " ";
        $build_simple_transliteration .= htmlentities($rowdata["TRANSLITERATED"]) . " ";
    }

    // if we are in interlinear mode, we now flip hiding transliteration to turn on for the rest of the render

    if ($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR)
    {
        $user_preference_turn_off_transliteration = true;
    }

    // Here we handle highlighting TEXT command results (darned fiddly!)

    foreach ($globalExactPhrasesToHighlight as $phrase)
    {
        $regex_search_pattern = "";

        $regex_replace_pattern = "";

        $capture_group_start = 1;

        $phrases_separated_if_spaces = explode(" ", str_replace("-", "&#8209", $phrase)); // 8209 is the hard hyphen char, used in the rendering routine

        for ($ploop = 0; $ploop < count($phrases_separated_if_spaces); $ploop++)
        {
            // split string into letters, we can't use str_split() as we have a multibyte string
            mb_internal_encoding("UTF-8");
            $letter_array = [];

            for ($split = 0; $split < mb_strlen($phrases_separated_if_spaces[$ploop]); $split++)
            {
                $letter_array[] = mb_substr($phrases_separated_if_spaces[$ploop], $split, 1);
            }

            // open by looking for <span>

            $regex_search_pattern .= "(<span[^>]*>[^<\/span>]?[^<]*)(?:HTML-MARK-START)?(";

            foreach ($letter_array as $letter)
            {
                $regex_search_pattern .= $letter . "(?:HTML-MARK-START|HTML-MARK-END)*";
            }

            // close off the search group and add the closing span

            $regex_search_pattern .= ")([^<]*<\/span>)";

            // add to the match pattern

            $regex_replace_pattern .= "\\" . $capture_group_start . "<mark>\\" . ($capture_group_start + 1) . "</mark>\\" . ($capture_group_start + 2);

            $capture_group_start += 3; // if this is a multi-word phrase, we'll need to keep incrementing the capture groups

            // add a space to the end of the capture group if there is going to be another word

            if ($ploop < (count($phrases_separated_if_spaces) - 1))
            {
                $regex_search_pattern .= "( )";
                $regex_replace_pattern .= "<mark>\\$capture_group_start</mark>";

                $capture_group_start++;
            }
        }

        // top and tail the regex

        $regex_search_pattern = "/" . $regex_search_pattern . "/m";

        // run the regex and hope for the best :-)
        // (we first swap out <MARK> and </MARK>, to avoid any nesting errors if they search for two TEXT items
        // that happen to overlap

        $build_transliteration = str_ireplace("<MARK>", "HTML-MARK-START", $build_transliteration);
        $build_transliteration = str_ireplace("</MARK>", "HTML-MARK-END", $build_transliteration);

        $build_transliteration = preg_replace($regex_search_pattern, $regex_replace_pattern, $build_transliteration);

        $build_transliteration = str_ireplace("HTML-MARK-START", "<MARK>", $build_transliteration);
        $build_transliteration = str_ireplace("HTML-MARK-END", "</MARK>", $build_transliteration);

        $build_arabic = str_ireplace("<MARK>", "HTML-MARK-START", $build_arabic);
        $build_arabic = str_ireplace("</MARK>", "HTML-MARK-END", $build_arabic);

        $build_arabic = preg_replace($regex_search_pattern, $regex_replace_pattern, $build_arabic);

        $build_arabic = str_ireplace("HTML-MARK-START", "<MARK>", $build_arabic);
        $build_arabic = str_ireplace("HTML-MARK-END", "</MARK>", $build_arabic);
    }

    // do we bold this verse
    if ($boldVerse == "$sura:$verse")
    {
        $boldOn   = "<b>";
        $boldOff  = "</b>";
        $cellBold = "bgcolor=#f0f0f0";
    }
    else
    {
        $boldOn   = "";
        $boldOff  = "";
        $cellBold = "";
    }

    // are we using the smaller versions of the styles

    $ayaSmaller              = "";
    $ayaTranscriptionSmaller = "ayaTranscriptionRegular";

    if ($popUpMode)
    {
        $ayaSmaller              = "ayaSmaller";
        $ayaTranscriptionSmaller = "ayaTranscriptionSmaller";
    }

    echo "<tr>";

    if (!$popUpMode)
    {
        echo "<td $cellBold class='$ayaTranscriptionSmaller' valign=top onmouseover=\"con_on('CON-$sura-$verse-0')\" onmouseout=\"con_off('CON-$sura-$verse-0')\"'>$boldOn";
        echo "<a href='/verse_browser.php?V=$sura:$verse' class=linky>";
        echo "($sura:$verse)";
        echo "</a>";
    }
    else
    {
        echo "<td $cellBold class='$ayaTranscriptionSmaller' valign=top>$boldOn";

        if (isset($_GET["SEARCH"]))
        {
            echo "<a href='/verse_browser.php?S=" . $_GET["SEARCH"] . "' class=linky>";
        }
        else
        {
            echo "<a href='/verse_browser.php?V=$sura:$verse' class=linky>";
        }
        echo "($sura:$verse)";
        echo "</a>";
    }
    echo $boldOff;

    // work out whether to show the formulaic percentage of this verse

    if ($highlightFormulaLength > 0)
    {
        if ($highlightFormulaType == "ROOT")
        {
            $roots = db_rowcount(db_query("SELECT `QTL-ROOT` FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));

            $flagged = db_rowcount(db_query("SELECT `QTL-ROOT` FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `FORMULA-$highlightFormulaLength-ROOT` > 0 AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));
        }

        if ($highlightFormulaType == "LEMMA")
        {
            $roots = db_rowcount(db_query("SELECT `QTL-LEMMA` FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));

            $flagged = db_rowcount(db_query("SELECT `QTL-ROOT` FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `FORMULA-$highlightFormulaLength-LEMMA` > 0 AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));
        }

        if ($highlightFormulaType == "ROOT-ALL")
        {
            $roots = db_rowcount(db_query("SELECT `ROOT OR PARTICLE` FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));

            $flagged = db_rowcount(db_query("SELECT `ROOT OR PARTICLE` FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-$highlightFormulaLength-ROOT-ALL` > 0 AND `SURA`='" . db_quote($sura) . "' AND `VERSE`='" . db_quote($verse) . "'"));
        }

        if ($roots > 0)
        {
            echo "<span title='Formulaic density based on a formula of length $highlightFormulaLength'>";
            if ($flagged > 0)
            {
                echo "<a href='formulae/list_formulae.php?SURAVERSE=$sura:$verse&L=$highlightFormulaLength&TYPE=$highlightFormulaType' style='text-decoration:none;'>";
            }
            echo "<font size=-2 color=#8080ff><br>" . number_format(($flagged * 100) / $roots, 2) . "%</font>";
            if ($flagged > 0)
            {
                echo "</a>";
            }
            echo "</span>";
        }
        else
        {
            echo "<span title='Formulaic density based on a formula of length $highlightFormulaLength'><font size=-2 color=#8080ff><br>0.00%</font></span>";
        }
    }

    echo add_context_button($sura, $verse, 0);

    echo "</td>";

    // super wide cell in interlinear mode
    if ($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR)
    {
        echo "<td class='table-cell-interlinear-mode' $cellBold align=left valign=top>";

        echo "<div class=interlinear-chrome-fix>"; // interlinear Chrome bug fix div open
    }
    else
    {
        echo "<td $cellBold align=right valign=top width='" . ($user_preference_turn_off_transliteration ? "50%" : "33%") . "' style='padding-right:20px;'>";
    }

    echo "<span class='aya $ayaSmaller'>";

    if ($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR)
    {
        echo "<div style='font-size: 0;'>";
    }

    echo $boldOn . $build_arabic . $boldOff;
    echo "</span>";

    // show any tags (provided we are a user with a username and therefore customisable settings
    // (show them here if transliteration is turned off, otherwise they'll appear in the other column)

    if ($user_preference_turn_off_transliteration)
    {
        echo "<br><div class=" . (($_GET["VIEWING_MODE"] == VIEWING_MODE_INTERLINEAR) ? "interlinear-tag-area" : "tag-area-no-transliteration") . ">";
        print_tags($sura, $verse);
        echo "</div>";
    }

    echo "</div>"; // interlinear Chrome bug fix div close

    echo "</td>";

    if (!$user_preference_turn_off_transliteration)
    {
        echo "<td $cellBold valign=top width='33%'>";
        echo "<span class='ayaTranscription $ayaTranscriptionSmaller $user_preference_transliteration_style'>" . $boldOn . $build_transliteration . $boldOff . "</span>";

        if (!$popUpMode)
        {
            print_tags($sura, $verse);
        }

        echo "</td>";
    }

    // if they are in interlinear mode, don't show any translations

    if ($_GET["VIEWING_MODE"] != VIEWING_MODE_INTERLINEAR)
    {
        echo "<td $cellBold valign=top width='" . ($user_preference_turn_off_transliteration ? "50%" : "33%") . "'><span class='ayaTranslation $ayaTranscriptionSmaller'>";

        $count_translations = 0;
        foreach ($translations as $translator)
        {
            if ($count_translations > 0)
            {
                echo "<p>";
            }

            echo $boldOn;

            // if showing more than one translation, print its name at the start of the line

            if (count($translations) > 1)
            {
                echo "(" . strtoupper($translator) . ") ";
            }

            echo english_translation($sura, $verse, $translator, false, $searchArrayTranslationElementsToHighlight, false) . $boldOff;

            if ($count_translations > 0)
            {
                echo "</p>";
            }
            else
            {
                // save a translation to use in the clipboard copy hidden div
                $simple_translation = english_translation($sura, $verse, $translator, false, [], true);
            }

            $count_translations++;
        }

        // add translation tags

        if ($showTranslationTagger)
        {
            if (count($translations) == 1)
            {
                $first_translation_value = reset($translations);

                // lookup translator number

                $translatorNumber = db_return_one_record_one_field("SELECT `TRANSLATION ID` FROM `TRANSLATION-LIST` WHERE `TRANSLATION NAME`='" . db_quote($first_translation_value) . "'");

                if ($translatorNumber < 1)
                {
                    $translatorNumber = 1;
                }

                // if we are 'translation tag mode' we do this as a form
                echo "<form action='verse_browser.php?V=" . $_GET["V"] . "&TTM=Y&T=$translatorNumber' method=post>";

                $translationText = english_translation($sura, $verse, $first_translation_value, true, [], false);

                echo "<textarea ID='translationTextField" . "-$sura-$verse' name='translationTextField' rows=" . ((strlen($translationText) / 52) + 10) . " cols=52>" . htmlspecialchars($translationText, ENT_QUOTES) . "</textarea>";

                echo "<input name=SuraField type=hidden value=$sura>";
                echo "<input name=VerseField type=hidden value=$verse>";
                echo "<input name=TranslatorField type=hidden value='" . $first_translation_value . "'>";

                echo "<br><button type=submit>SAVE TAG EDITS</button>";

                echo "&nbsp;<A HREF='verse_browser.php?V=" . $_GET["V"] . "&T=$translatorNumber' class=linky-light>CANCEL EDITING</a>";

                echo "</form>";

                // report back any saves

                if ($showTranslationTagger && isset($_POST["translationTextField"]))
                {
                    if ($_POST["translationTextField"] != "")
                    {
                        if ($_POST["TranslatorField"] == $first_translation_value && $_POST["SuraField"] == $sura && $_POST["VerseField"] == $verse)
                        {
                            echo "<b>Tag changes have been saved.</b>";

                            echo "<p><a href='verse_browser.php?V=" . $_GET["V"] . "' class=linky-light>Exit to Verse Browser</a></p>";

                            echo "<p><a href='admin/translation_word_tag_stats.php' class=linky-light>Exit to Translation Tagging Statistics</a></p>";
                        }
                    }
                }
            }
        }
    }

    echo "<span id=DIV-COPY-$sura-$verse class=hidden><p>Sura " . sura_name_arabic($sura) . " $sura:$verse</p><p>$build_simple_arabic</p><p>$build_simple_transliteration</p><p>" . htmlentities(strip_tags($simple_translation)) . "</p></span>";

    echo "</td>";

    echo "</tr>";
}

function render_verse_parse_mode_new($sura, $verse, $useItalicsForTransliteration, $highlightWordStart, $highlightWordEnd, $searchArrayWordsToHighlight, $globalTranslationPhrasesToHighlight)
{
    global $user_preference_turn_off_transliteration;

    // italics for transliteration

    if ($useItalicsForTransliteration)
    {
        $user_preference_italics_on            = "";
        $user_preference_italics_off           = "";
        $user_preference_transliteration_style = "transliteration_formatting_preference";
    }
    else
    {
        $user_preference_italics_on            = "";
        $user_preference_italics_off           = "";
        $user_preference_transliteration_style = "";
    }

    $result = db_query("SELECT * FROM `QURAN-DATA` WHERE `SURA`=" . db_quote($sura) . " AND `VERSE`=" . db_quote($verse) . " ORDER BY `WORD`, `SEGMENT`");

    // look up the last word number in this verse
    $last_word_number = db_return_one_record_one_field("SELECT `WORD` FROM `QURAN-DATA` WHERE `SURA`=" . db_quote($sura) . " AND `VERSE`=" . db_quote($verse) . " ORDER BY `WORD`, `SEGMENT` LIMIT " . (db_rowcount($result) - 1) . ", 1");

    // build up the various strings
    $build_transliteration = "";
    $build_arabic          = "";
    $word_number           = 0;

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        // only add text once per word (not once per segment)
        if ($word_number != $ROW["WORD"])
        {
            $word_number = $ROW["WORD"];

            $lastRow = 0;
            if ($word_number == $last_word_number)
            {
                $lastRow = 1;
            }

            // build the parsing data
            $parsing_info = "";
            $result_tip   = db_query("SELECT * FROM `QURAN-DATA` WHERE `SURA`=" . db_quote($sura) . " AND `VERSE`=" . db_quote($verse) . " AND WORD='$word_number' ORDER BY `SEGMENT`");

            for ($j = 0; $j < db_rowcount($result_tip); $j++)
            {
                // grab next database row
                $ROWTIP = db_return_row($result_tip);

                if ($j > 0)
                {
                    $parsing_info .= "<br><br>";
                }

                $parsing_info .= "<B>" . $ROWTIP["QTL-TAG-EXPLAINED"] . "</B>";

                if ($ROWTIP["QTL-TAG-EXPLAINED"] == "Verb" || $ROWTIP["QTL-TAG-EXPLAINED"] == "Imperfect Verb" || $ROWTIP["QTL-TAG-EXPLAINED"] == "Imperative Verb" || $ROWTIP["QTL-TAG-EXPLAINED"] == "Perfect Verb")
                {
                    $extra = "";

                    if ($ROWTIP["QTL-NUMBER"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-NUMBER"];
                    }

                    if ($ROWTIP["QTL-PERSON"] > 0)
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-PERSON"] . "P";
                    }

                    if ($ROWTIP["QTL-GENDER"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-GENDER"];
                    }

                    if ($ROWTIP["QTL-ARABIC-FORM"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= "Form " . $ROWTIP["QTL-ARABIC-FORM"];
                    }

                    $parsing_info .= $extra;
                }

                if ($ROWTIP["QTL-TAG-EXPLAINED"] == "Noun" || $ROWTIP["QTL-TAG-EXPLAINED"] == "Adjective" || $ROWTIP["QTL-TAG-EXPLAINED"] == "Proper Noun")
                {
                    $extra = "";

                    if ($ROWTIP["QTL-CASE"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-CASE"];
                    }

                    if ($ROWTIP["QTL-GENDER"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-GENDER"];
                    }

                    if ($ROWTIP["QTL-PERSON"] > 0)
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-PERSON"] . "P";
                    }

                    if ($ROWTIP["QTL-NUMBER"] != "")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-NUMBER"];
                    }

                    if ($ROWTIP["QTL-TAG-EXPLAINED"] == "Proper Noun" && $ROWTIP["FORM"] == "{ll~ahi")
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= " => Allah";
                    }

                    $parsing_info .= $extra;
                }

                if ($ROWTIP["QTL-TAG-EXPLAINED"] == "Personal Pronoun")
                {
                    $extra = "";
                    if ($ROWTIP["QTL-NUMBER"] != "")
                    {
                        $extra .= " - " . $ROWTIP["QTL-NUMBER"];
                    }

                    if ($ROWTIP["QTL-PERSON"] > 0)
                    {
                        if ($extra == "")
                        {
                            $extra = " - ";
                        }
                        else
                        {
                            $extra .= " ";
                        }
                        $extra .= $ROWTIP["QTL-PERSON"] . "P";
                    }
                    $parsing_info .= $extra;
                }

                if ($ROWTIP["QTL-ROOT"] != "" || $ROWTIP["QTL-LEMMA"] != "")
                {
                    $parsing_info .= "<BR><FONT COLOR=GREEN>";

                    if ($ROWTIP["QTL-ROOT"] != "")
                    {
                        $parsing_info .= "Root: <font size=+1><a href='verse_browser.php?S=ROOT:" . $ROWTIP["QTL-ROOT"] . "' class=linky-parsed-word>" . htmlentities(return_arabic_word($ROWTIP["QTL-ROOT"])) . "</a></font> (<a href='verse_browser.php?S=ROOT:" . $ROWTIP["QTL-ROOT"] . "' class=linky-parsed-word>" . htmlentities(transliterate_new($ROWTIP["QTL-ROOT"])) . "</a>)";
                    }

                    if ($ROWTIP["QTL-LEMMA"] != "")
                    {
                        if ($ROWTIP["QTL-ROOT"] != "")
                        {
                            $parsing_info .= "; ";
                        }

                        $arabic_version_of_lemma = return_arabic_word($ROWTIP["QTL-LEMMA"]);

                        $parsing_info .= "Lemma: <font size=+1><a href='verse_browser.php?S=LEMMA:$arabic_version_of_lemma' class=linky-parsed-word>" . htmlentities($arabic_version_of_lemma) . "</a></font> (<a href='verse_browser.php?S=LEMMA:$arabic_version_of_lemma' class=linky-parsed-word>" . htmlentities(transliterate_new($ROWTIP["QTL-LEMMA"])) . "</a>)";
                    }

                    $parsing_info .= "</FONT>";
                }
            }

            if ($lastRow)
            {
                echo "<tr class='border_bottom'>";
            }
            else
            {
                echo "<tr>";
            }

            echo "<td align=center onmouseover=\"con_on('CON-$sura-$verse-$word_number')\" onmouseout=\"con_off('CON-$sura-$verse-$word_number')\"'>";
            echo "($sura:$verse:$word_number)";

            add_context_button($sura, $verse, $word_number);

            echo "</td>";

            echo "<td align=center>";
            echo $ROW["RENDERED ARABIC"];

            echo "</td>";

            // word highlighting

            $highlight_word_start_code = "";
            $highlight_word_end_code   = "";

            // do we need to insert a highlight start code?

            if ($highlightWordStart > 0)
            {
                // first, because this is the exact start word
                if ($highlightWordStart == $ROW["GLOBAL WORD NUMBER"])
                {
                    $highlight_word_start_code = "<MARK>";
                }
                else
                {
                    // otherwise because it's the first word of a verse in the middle of a run
                    if ($ROW["WORD"] == 1 && $ROW["GLOBAL WORD NUMBER"] > $highlightWordStart && $ROW["GLOBAL WORD NUMBER"] <= $highlightWordEnd)
                    {
                        $highlight_word_start_code = "<MARK>";
                    }
                }
            }

            // do we need to insert a highlight end code?

            if ($highlightWordEnd > 0)
            {
                if ($highlightWordEnd == $ROW["GLOBAL WORD NUMBER"])
                {
                    $highlight_word_end_code = "</MARK>";
                }
            }

            // search highlighting

            if (count($searchArrayWordsToHighlight) > 0)
            {
                if (array_key_exists($ROW["GLOBAL WORD NUMBER"], $searchArrayWordsToHighlight))
                {
                    $highlight_word_start_code = "<MARK>";
                    $highlight_word_end_code   = "</MARK>";
                }
            }

            if (!$user_preference_turn_off_transliteration)
            {
                echo "<td align=center>";
                echo $highlight_word_start_code . "<span class='$user_preference_transliteration_style'>" . $ROW["TRANSLITERATED"] . "</span>" . $highlight_word_end_code;

                if ($ROW["GLOSS"] != "" && $ROW["GLOSS"] != "???")
                {
                    if ($user_preference_italics_on == "")
                    {
                        echo "<p><i>" . htmlentities($ROW["GLOSS"]) . "</i></p>";
                    }
                    else
                    {
                        echo "<p>" . htmlentities($ROW["GLOSS"]) . "</p>";
                    }
                }

                echo "</td>";
            }

            echo "<td>";
            echo $parsing_info;

            echo "</td>";

            if ($i == 0)
            {
                echo "<td valign=top style='border-bottom:1pt solid black;' class=ayaTranslation width=40% rowspan=" . db_rowcount(db_query("SELECT * FROM `QURAN-DATA` WHERE `SURA`=" . db_quote($sura) . " AND `VERSE`=" . db_quote($verse) . " AND `SEGMENT`=1")) . ">";

                $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`");

                for ($j = 0; $j < db_rowcount($result_translation); $j++)
                {
                    $ROW_TRANSLATION = db_return_row($result_translation);

                    echo "(" . strtoupper($ROW_TRANSLATION["TRANSLATION NAME"]) . ") " . english_translation($sura, $verse, $ROW_TRANSLATION["TRANSLATION NAME"], false, $globalTranslationPhrasesToHighlight, true);

                    if ($j < (db_rowcount($result_translation) - 1))
                    {
                        echo "<br><br>";
                    }
                }

                // show any tags

                $result_tags = db_query("SELECT `TAG ID`, `Tag Name`, `Tag Colour`, `Tag Lightness Value` FROM `TAGGED-VERSES` T1
				LEFT JOIN `TAGS` T2 ON T2.`ID`=`TAG ID`  
				WHERE `SURA-VERSE`='" . db_quote("$sura:$verse") . "' AND T1.`User ID`=" . db_quote($_SESSION["UID"]) . " ORDER BY `Tag Name`");

                echo "<br><br>";

                echo "<div class=tag_group id='tag_$sura" . "v" . "$verse'>";

                if (db_rowcount($result_tags) > 0)
                {
                    for ($tags = 0; $tags < db_rowcount($result_tags); $tags++)
                    {
                        echo ($tags > 0) ? " " : "";
                        $tag_data = db_return_row($result_tags);

                        draw_tag_lozenge($tag_data["Tag Colour"], $tag_data["Tag Lightness Value"]);

                        echo "' data-tipped-options=\"ajax: {url:'/ajax/ajax_tag_hover.php', data:{T:" . $tag_data["TAG ID"] . ", V:'$sura:$verse'}}\"'>";
                        echo str_replace(" ", "&nbsp;", htmlentities($tag_data["Tag Name"]));
                        echo "</span>";
                    }
                }

                echo "</div>";

                echo "</td>";
            }

            echo "</tr>";
        }
    }
}
