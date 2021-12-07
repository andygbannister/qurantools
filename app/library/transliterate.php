<?php

function output($string)
{
    /*
        global $debug_text;

        $debug_text.=$string;
    */
}

function transliterate_new($text)
{
    global $previous_letter, $previous_but_one, $quranic_initials;

    $render = "";

    // PRE FLIGHT

    // deal with a ALIF MAQSURA followed by sukun and hamza
    $text = str_ireplace("Yo'", "y", $text);

    for ($i = 0; $i < strlen($text); $i++)
    {
        output(mb_substr($text, $i, 1));

        switch (mb_substr($text, $i, 1))
        {
            default:
                output("-> **** NO MATCH ***<br><b>$render</b>");
                break;

            case "A":
                output(" -> ALIF");
                switch ($previous_letter)
                {
                    default:
                        if ($quranic_initials > 0)
                        {
                            $render .= "a";
                        }
                        else
                        {
                            if ($previous_letter == "F")
                            {
                                // no alif needed to be printed in these cases
                            }
                            else
                            {
                                $render .= "'";
                            }
                        }
                        break;

                    case "a":
                        $render = substr($render, 0, strlen($render) - 1) . "ā";
                        break;
                }

                break;

            case " ":
                output(" -> SPACE");
                $render .= " ";
                break;

            case "F":
                output(" -> FATHATAN");
                $render .= "an";
                break;

            case "Y":
                output(" -> ALIF MAQSURA");
                if ($previous_letter == "i")
                {
                    $render = substr($render, 0, strlen($render) - 1) . "ī";
                }

                break;

            case "9":
                output(" -> HYPHEN");
                $render .= "-";
                break;

            case "S":
                output(" -> SAD");
                $render .= "ṣ";
                break;

            case "b":
                output(" -> BA");
                $render .= "b";
                break;

            case "q":
                output(" -> QAD");
                $render .= "q";
                break;

            case "t":
                output(" -> TA");
                $render .= "t";
                break;

            case "D":
            case "ḍ":
                output(" -> DAD");
                $render .= "ḍ";
                break;

            case "T":
            case "ṭ":
                output(" -> TAA");
                $render .= "ṭ";
                break;

            case "i":
                output(" -> KASRA");
                $render .= "i";
                break;

            case "K":
                output(" -> KASRATAN");
                $render .= "in";
                break;

            case "]":
                output(" -> SMALL LOW MEEM");
                $render .= "";
                break;

            case "s":
                output(" -> SEEN");
                $render .= "s";
                break;

            case "$":
                output(" -> SHEEN");
                $render .= "sh";
                break;

            case "o":
                output(" -> SUKUN");
                if ($previous_letter == "$")
                {
                    $render .= "'";
                }
                break;

            case "m":
                output(" -> meem");
                $render .= "m";
                break;

            case "Z":
                output(" -> DTHA");
                $render .= "ẓ";
                break;

            case "@":
                output(" -> SMALL HIGH ROUNDED ZERO");
                $render .= "";
                break;

            case "{":
                output(" -> Alif + HamzatWasl");
                if ($previous_letter != "i" && $previous_letter != "a" && $previous_letter != "u" && $previous_letter != "A")
                {
                    $render .= "a";
                }
                break;

            case "l":
                output(" -> LAM");
                $render .= "l";
                break;

            case "~":
                output(" -> SHADDA (Double: $previous_letter");
                if ($previous_but_one != "9")
                { // if hyphen was before the last letter, it's probably an article: don't double it
                    // don't double certain first letters
                    if ($i == 1 && $previous_letter == "m")
                    {
                        break;
                    }

                    // in fact, don't double the very, very first letter of the word
                    if ($i == 1)
                    {
                        break;
                    }

                    // need to tweak certain doubled letters

                    switch ($previous_letter)
                    {
                        case "S":
                            $previous_letter = "ṣ";
                            break;

                        case "$":
                            $previous_letter = "sh";
                            break;

                        case "Y":
                            $previous_letter = "y";
                            break;

                        case "T":
                            $previous_letter = "ṭ";
                            break;

                        case "v":
                            $previous_letter = "th";
                            break;

                        case "x":
                            $previous_letter = "kh";
                            break;
                    }

                    $render .= $previous_letter;
                }
                break;

            case "a":
                output(" -> FATHA");
                $render .= "a";
                break;

            case "g":
                output(" -> GHAYN");
                $render .= "gh";
                break;

            case "z":
                output(" -> ZAIN");
                $render .= "z";
                break;

            case "w":
                output(" -> WAW");
                // makes an u long (check if previous letter was an u but that previous letter wasn't on the end of the last word)
                if ($previous_letter == "u" && $i > 0)
                {
                    $render = substr($render, 0, strlen($render) - 1) . "ū";
                    break;
                }
                // makes an a long (check if previous letter was an a but that previous letter wasn't on the end of the last word)
                // if ($previous_letter == "a" && $i > 0)
                // {
                    // $render = substr($render, 0, strlen($render) - 1)."ā";
                    // break;
                // }

                $render .= "w";
                break;

            case "k":
                output(" -> KAF");
                $render .= "k";
                break;

            case "E":
                output(" -> AYN");
                $render .= "ʿ";
                break;

            case "<":
                output(" -> Alif + HamzaBelow");
                $render .= "'";
                break;

            case "_":
                output(" -> TATWEEL");
                $render .= "";
                break;

            case "#":
                output(" -> HAMZA ABOVE");
                if ($previous_letter == "_")
                {
                    $render .= "'";
                }
                break;

            case "d":
                output(" -> DAL");
                $render .= "d";
                break;

            case "u":
                output(" -> DAMMA");
                $render .= "u";
                $previous_vowel = 1;
                break;

            case "N":
                output(" -> DAMMATAN");
                $render .= "un";
                break;

            case "h":
                output(" -> HA");
                $render .= "h";
                break;

            case "H":
            case "ḥ":
                output(" -> HAA");
                $render .= "ḥ";
                break;

            case "r":
                output(" -> RA");
                $render .= "r";
                break;

            case "n":
                output(" -> NOON");
                $render .= "n";
                break;

            case "*":
                output(" -> DHAL");
                $render .= "dh";
                break;

            case "&":
                output(" -> WAW + HAMZA ABOVE");
                $render .= "'";
                break;

            case "x":
                output(" -> KHA");
                $render .= "kh";
                break;

            case ".":
                output(" -> SMALL YA");
                $render .= "";
                break;

            case "v":
                output(" -> THAL");
                $render .= "th";
                break;

            case "f":
                output(" -> FA");
                $render .= "f";
                break;

            case "j":
                output(" -> JEEM");
                $render .= "j";
                break;

            case "'":
                output(" -> HAMZA");
                $render .= "'";
                break;

            case ":":
                output(" -> SMALL HIGH SEN");
                $render .= "'";
                break;

            case "[":
                output(" -> SmallHighMeemIsolatedForm");
                $render .= "";
                break;

            case ">":
                output(" -> ALIF + HAMZA ABOVE");
                $render .= "";
                break;

            case "+":
                output(" -> EmptyCentreHighStop");
                $render .= "";
                break;

            case "!":
                output(" -> SmallHighNoon");
                $render .= "n";
                break;

            case ";":
                output(" -> SmallLowSeen");
                $render .= "";
                break;

            case "%":
                output(" -> RoundedHighStopWithFilledCentre");
                $render .= "";
                break;

            case "}":
                output(" -> Ya + HamzaAbove");
                $render .= "";
                break;

            case "-":
                output(" -> EmptyCentreLowStop");
                $render .= "";
                break;

            case "p":
                output(" -> Ta Marbuta");
                $render .= "t";
                break;

            case "^":
                output(" -> MADDAH");
                $render .= "";
                break;

            case "\"":
                output(" -> SmallHighUprightRectangularZero");
                $render .= "";
                break;

            case "y":
                output(" -> YA");
                // makes an i long (check if previous letter was an i but that previous letter wasn't on the end of the last word)
                if ($previous_letter == "i" && $i > 0)
                {
                    $render = substr($render, 0, strlen($render) - 1) . "ī";
                }
                else
                {
                    $render .= "y";
                }
                break;

            case "`":
                output(" -> Alif Khanjareeya");
                // makes the previous letter long
                if (substr($text, $i - 1, 1) == "a")
                {
                    $render = substr($render, 0, strlen($render) - 1) . "ā";
                }
                break;
        }

        if (substr($text, $i, 1) != " ")
        {
            $previous_but_one = $previous_letter;
            $previous_letter  = substr($text, $i, 1);
        }

        output("<br>");
    }

    output("<br>");

    // final cleanups (e.g. fix niggles)

    // replace lll with ll
    $render = str_ireplace("lll", "l-l", $render);

    // replace -' with '
    $render = str_ireplace("-'", "'", $render);

    // replace llil with lil
    $render = str_ireplace("llil", "lil", $render);

    // replace hūa with huwa
    $render = str_ireplace("hūa", "huwa", $render);

    // does a transliteration exception apply (i.e. has the renderer made a mistake we've coded for?)

    $result = db_query("SELECT * FROM `TRANSLITERATION-EXCEPTIONS` WHERE `RENDERED`='" . db_quote($render) . "'");

    if (db_rowcount($result) > 0)
    {
        $render = db_return_one_record_one_field("SELECT `SUBSTITUTE` FROM `TRANSLITERATION-EXCEPTIONS` WHERE `RENDERED`='" . db_quote($render) . "'");
    }

    return $render;
}
