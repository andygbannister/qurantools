<?php

// SIMPLIFY A LIST OF VERSES FOUND BY A SEARCH OR LOOKUP

// the string we'll build with a simplified set of references

$verseSimplifiedText = "";

function next_verse_in_sequence($previousSura, $previousVerse, $currentSura, $currentVerse)
{
    // if verses are in the same sura, it's easy
    if ($currentSura == $previousSura)
    {
        return ($currentVerse - $previousVerse) == 1;
    }

    // otherwise, we check if we have the last verse of one sura and the first verse of the next
    // we have disabled this for now, as it's breaking another part of the parser
    // and probably makes for hard to read reference lists
    /*
        if (($currentSura - $previousSura) == 1)
        {
            if ($currentVerse == 1)
            {
                return $previousVerse == verses_in_sura($previousSura);
            }
        }
    */

    return false;
}

function add_verse_divider($divider)
{
    global $verseSimplifiedText;
    if ($verseSimplifiedText != "")
    {
        return $divider . " ";
    }
    return "";
}

function verse_list_simplify()
{
    global $search_result, $verseSimplifiedText;

    // flush list
    $verseSimplifiedText = "";

    // move to the first record
    $search_result->data_seek(0);

    // track sura numbers as we print them, so we know when to separate things with a common or a semicolon
    $lastSuraNumberPrinted = 0;

    // count where we are in the records

    $countVerses = 0;
    $totalVerses = db_rowcount($search_result);

    // start by grabbing the first verse

    $ROW = db_return_row($search_result);
    $countVerses++;

    // save the sura and verse
    $firstSuraInBlock  = $ROW["SURA"];
    $firstVerseInBlock = $ROW["VERSE"];

    while (true)
    {
        // Is this the last verse? In which case, we print and exit

        if ($countVerses == $totalVerses)
        {
            // if our string is empty, we just print

            if ($verseSimplifiedText == "")
            {
                $verseSimplifiedText .= $ROW["SURA"] . ":" . $ROW["VERSE"];
            }
            else
            {
                // if this a one verse block, we just print

                if ($ROW["SURA"] == $firstSuraInBlock && $ROW["VERSE"] == $firstVerseInBlock)
                {
                    if ($ROW["SURA"] == $lastSuraNumberPrinted)
                    {
                        $verseSimplifiedText .= add_verse_divider(",") . $ROW["VERSE"];
                    }
                    else
                    {
                        $verseSimplifiedText .= add_verse_divider(";") . $ROW["SURA"] . ":" . $ROW["VERSE"];
                    }
                }
                else
                {
                    // otherwise, we add a hyphen first
                    if ($ROW["SURA"] == $lastSuraNumberPrinted)
                    {
                        $verseSimplifiedText .= "-" . $ROW["VERSE"];
                    }
                    else
                    {
                        $verseSimplifiedText .= "-" . $ROW["SURA"] . ":" . $ROW["VERSE"];
                    }
                }
            }
            break;
        }

        // Since this is not the last record, we can start our second loop
        // this pulls verses from the stack one at a time until we decide
        // we have finished the block of verses

        $previousSura  = $firstSuraInBlock;
        $previousVerse = $firstVerseInBlock;
        $currentSura   = 0;
        $currentVerse  = 0;

        while (true)
        {
            // grab next verse
            $ROW = db_return_row($search_result);
            $countVerses++;

            // save the previous verse
            if ($currentSura > 0)
            {
                $previousSura  = $currentSura;
                $previousVerse = $currentVerse;
            }

            // save this verse
            $currentSura  = $ROW["SURA"];
            $currentVerse = $ROW["VERSE"];

            // is it the next verse in sequence?

            if (next_verse_in_sequence($previousSura, $previousVerse, $currentSura, $currentVerse))
            {
                // It's in sequence. So, is it the last verse in the stack? If so, print and finish
                if ($countVerses == $totalVerses)
                {
                    // we have finished, so print (either with the sura number, or without)

                    if ($firstSuraInBlock != $lastSuraNumberPrinted)
                    {
                        // is this verse range in the same sura?
                        if ($firstSuraInBlock == $currentSura)
                        {
                            $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock . "-" . $currentVerse;
                        }
                        else
                        {
                            // or across a sura divide
                            $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock . "-" . $currentSura . ":" . $currentVerse;
                        }
                    }
                    else
                    {
                        // check verse range doesn't span a sura
                        if ($currentSura == $firstSuraInBlock)
                        {
                            $verseSimplifiedText .= add_verse_divider(",") . $firstVerseInBlock . "-" . $currentVerse;
                        }
                        else
                        {
                            $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock . "-" . $currentSura . ":" . $currentVerse;
                        }
                    }

                    // record the fact we have printed this sura number
                    $lastSuraNumberPrinted = $currentSura;
                    break 2;
                }
                else
                {
                    // there is more to do, so we just repeat the loop
                    continue;
                }
            }
            else
            {
                // It's not in sequence. So, we finish off the current batch ...

                if ($firstSuraInBlock != $lastSuraNumberPrinted)
                {
                    // is it a single verse?
                    if ($firstVerseInBlock == $previousVerse)
                    {
                        $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock;
                    }
                    else
                    {
                        // or a range of verses, e.g. ", 100-110"
                        $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock . "-" . $previousVerse;
                    }
                }
                else
                {
                    // is it a single verse?
                    if ($firstVerseInBlock == $previousVerse)
                    {
                        $verseSimplifiedText .= add_verse_divider(",") . $firstVerseInBlock;
                    }
                    else
                    {
                        // or a range of verses, e.g. ", 100-110"
                        // but does it span a sura?
                        if ($firstSuraInBlock != $previousSura)
                        {
                            $verseSimplifiedText .= add_verse_divider(";") . $firstSuraInBlock . ":" . $firstVerseInBlock . "-x-" . $previousSura . ":" . $previousVerse;
                        }
                        else
                        {
                            $verseSimplifiedText .= add_verse_divider(",") . $firstVerseInBlock . "-" . $previousVerse;
                        }
                    }
                }

                // record the fact we have printed this sura number
                $lastSuraNumberPrinted = $previousSura;

                // and create a new block
                $firstSuraInBlock  = $currentSura;
                $firstVerseInBlock = $currentVerse;

                $currentSura  = 0;
                $currentVerse = 0;

                // and break out to the parent loop
                break;
            }
        }
    }

    return $verseSimplifiedText;
}
