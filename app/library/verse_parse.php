<?php

// PROCESS LISTS/RANGES OF VERSES

function add_to_master_array($sura, $aya)
{
    global $MASTER_VERSE_LIST, $MAX_ARRAY_SIZE;

    // CAP THE ARRAY SIZE
    if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
    {
        return;
    }

    if ($sura >= 1 && $sura <= 114)
    {
        $MASTER_VERSE_LIST[] = "$sura:$aya";
    }
}

function parse_verses($V, $buildSQL, $ADD_CONTEXT)
{
    $ADD_CONTEXT = 0;

    if ($ADD_CONTEXT < 0 || $ADD_CONTEXT > 5)
    {
        $ADD_CONTEXT = 0;
    }

    global $MASTER_VERSE_LIST, $MAX_ARRAY_SIZE, $RANGE_SQL, $FULL_SURA_LIST, $error_messages;

    $RANGE_SQL = "";
    $SUBVERSE  = "";

    // step one -> strip out spaces
    $V = str_ireplace(" ", "", $V);

    $VERSES = explode(";", $V);

    $MASTER_VERSE_LIST = [];

    $FULL_SURA_LIST = [];

    foreach ($VERSES as $REFERENCE)
    {
        $error = true; // will be cleared if this command parses

        // build SQL
        if ($buildSQL)
        {
            if ($RANGE_SQL != "")
            {
                $RANGE_SQL .= " OR ";
            } // OR1
        }

        // single chapter
        if (is_numeric($REFERENCE))
        {
            if ($REFERENCE >= 1 && $REFERENCE <= 114)
            {
                $error            = false;
                $FULL_SURA_LIST[] = $REFERENCE;
                // build SQL
                if ($buildSQL)
                {
                    $RANGE_SQL .= "`SURA`=$REFERENCE";
                }
                else
                {
                    // add to array
                    for ($i = 1; $i <= verses_in_sura($REFERENCE);$i++)
                    {
                        add_to_master_array($REFERENCE, $i);
                    }
                }
                continue;
            }
        }

        // range of chapters
        if (substr_count($REFERENCE, "-") == 1)
        {
            $SUBREF = explode("-", $REFERENCE);
            if (is_numeric($SUBREF[0]) && is_numeric($SUBREF[1]))
            {
                if ($SUBREF[0] >= 1 && $SUBREF[0] <= 114 && $SUBREF[1] >= 1 && $SUBREF[1] <= 114 && $SUBREF[1] >= $SUBREF[0])
                {
                    $error = false;
                    for ($s = $SUBREF[0]; $s <= $SUBREF[1]; $s++)
                    {
                        $FULL_SURA_LIST[] = $s;
                    }
                    // build SQL
                    if ($buildSQL)
                    {
                        $RANGE_SQL .= "(`SURA`>=$SUBREF[0] AND `SURA`<=$SUBREF[1])";
                    }
                    else
                    {
                        // add to array
                        for ($s = $SUBREF[0]; $s <= $SUBREF[1]; $s++)
                        {
                            for ($v = 1; $v <= verses_in_sura($s);$v++)
                            {
                                add_to_master_array($s, $v);
                                if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                {
                                    break;
                                }
                            }
                        }
                    }

                    continue;
                }
            }
        }

        // chapter and one more
        if (strtolower(substr($REFERENCE, -1)) == "f")
        {
            $temp = substr($REFERENCE, 0, strlen($REFERENCE) - 1);
            if (is_numeric($temp))
            {
                if ($temp >= 1 && $temp <= 113)
                {
                    $FULL_SURA_LIST[] = $temp;
                    $FULL_SURA_LIST[] = $temp + 1;
                    $error            = false;
                    // build SQL
                    if ($buildSQL)
                    {
                        $RANGE_SQL .= "`SURA`=$temp OR `SURA`=" . ($temp + 1);
                    }
                    else
                    {
                        // add to array
                        for ($s = $temp; $s <= ($temp + 1); $s++)
                        {
                            for ($v = 1; $v <= verses_in_sura($s);$v++)
                            {
                                add_to_master_array($s, $v);
                                if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                {
                                    break;
                                }
                            }
                        }
                    }

                    continue;
                }
            }
        }

        // chapter and every chapter following (using ff)
        if (strtolower(substr($REFERENCE, -2)) == "ff")
        {
            $temp = substr($REFERENCE, 0, strlen($REFERENCE) - 2);
            if (is_numeric($temp))
            {
                if ($temp >= 1 && $temp <= 113)
                {
                    $error = false;
                    for ($s = $temp; $s <= 114; $s++)
                    {
                        $FULL_SURA_LIST[] = $s;
                    }

                    // build SQL
                    if ($buildSQL)
                    {
                        $RANGE_SQL .= "(`SURA`>=$temp AND `SURA`<=114)";
                    }
                    else
                    {
                        // add to array
                        for ($s = $temp; $s <= 114; $s++)
                        {
                            for ($v = 1; $v <= verses_in_sura($s);$v++)
                            {
                                add_to_master_array($s, $v);
                                if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                {
                                    break;
                                }
                            }
                        }
                    }

                    continue;
                }
            }
        }

        // chapter and every chapter following (using -)
        if (strtolower(substr($REFERENCE, -1)) == "-")
        {
            $temp = substr($REFERENCE, 0, strlen($REFERENCE) - 1);
            if (is_numeric($temp))
            {
                if ($temp >= 1 && $temp <= 113)
                {
                    $error = false;
                    // build SQL
                    if ($buildSQL)
                    {
                        $RANGE_SQL .= "(`SURA`>=$temp AND `SURA`<=114)";
                    }
                    else
                    {
                        // add to array
                        for ($s = $temp; $s <= 114; $s++)
                        {
                            for ($v = 1; $v <= verses_in_sura($s);$v++)
                            {
                                add_to_master_array($s, $v);
                                if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                {
                                    break;
                                }
                            }
                        }
                    }

                    continue;
                }
            }
        }

        // single verse, or single verse followed by "f" or "ff" or "-"
        if (substr_count($REFERENCE, ":") == 1 && substr_count($REFERENCE, ",") < 1)
        {
            $SUBREF = explode(":", $REFERENCE);

            if (is_numeric($SUBREF[0]))
            {
                if ($SUBREF[0] >= 1 && $SUBREF[0] <= 114)
                {
                    // does this verse have a double "ff" on the end
                    if (strtolower(substr($SUBREF[1], -2)) == "ff")
                    {
                        $temp = substr($SUBREF[1], 0, strlen($SUBREF[1]) - 2);

                        if ($temp == 1)
                        {
                            $FULL_SURA_LIST[] = $SUBREF[0];
                        }

                        if (is_numeric($temp))
                        {
                            if ($temp > 0 && $temp < verses_in_sura($SUBREF[0]))
                            {
                                $error = false;
                                // build SQL
                                if ($buildSQL)
                                {
                                    $RANGE_SQL .= "(`SURA`=$SUBREF[0] AND `VERSE`>=$temp AND `VERSE`<=" . verses_in_sura($SUBREF[0]) . ")";
                                }
                                else
                                {
                                    for ($v = $temp; $v <= verses_in_sura($SUBREF[0]); $v++)
                                    {
                                        add_to_master_array($SUBREF[0], $v);
                                    }
                                }
                                continue;
                            }
                        }
                    }

                    // does this verse have a "-" on the end
                    if (strtolower(substr($SUBREF[1], -1)) == "-")
                    {
                        $temp = substr($SUBREF[1], 0, strlen($SUBREF[1]) - 1);
                        if (is_numeric($temp))
                        {
                            if ($temp > 0 && $temp < verses_in_sura($SUBREF[0]))
                            {
                                $error = false;
                                if ($temp == 1)
                                {
                                    $FULL_SURA_LIST[] = $SUBREF[0];
                                }
                                // build SQL
                                if ($buildSQL)
                                {
                                    $RANGE_SQL .= "(`SURA`=$SUBREF[0] AND `VERSE`>=$temp AND `VERSE`<=" . verses_in_sura($SUBREF[0]) . ")";
                                }
                                else
                                {
                                    for ($v = $temp; $v <= verses_in_sura($SUBREF[0]); $v++)
                                    {
                                        add_to_master_array($SUBREF[0], $v);
                                    }
                                }

                                continue;
                            }
                        }
                    }

                    // does this verse have a single "f" on the end
                    if (strtolower(substr($SUBREF[1], -1)) == "f")
                    {
                        $temp = substr($SUBREF[1], 0, strlen($SUBREF[1]) - 1);
                        if (is_numeric($temp))
                        {
                            if ($temp < verses_in_sura($SUBREF[0]))
                            {
                                $error = false;
                                // build SQL
                                if ($buildSQL)
                                {
                                    $RANGE_SQL .= "(`SURA`=$SUBREF[0] AND `VERSE`>=$temp AND `VERSE`<=" . ($temp + 1 + $ADD_CONTEXT) . ")";
                                }
                                else
                                {
                                    add_to_master_array($SUBREF[0], $temp);
                                    add_to_master_array($SUBREF[0], $temp + 1);
                                }
                            }
                        }
                        continue;
                    }
                }
            }

            if (is_numeric($SUBREF[1]))
            {
                if ($SUBREF[1] > 0)
                {
                    if ($SUBREF[1] <= verses_in_sura($SUBREF[0]))
                    {
                        $error = false;
                        // build SQL
                        if ($buildSQL)
                        {
                            if ($ADD_CONTEXT == 0)
                            {
                                $RANGE_SQL .= "(`SURA`=$SUBREF[0] AND `VERSE`=$SUBREF[1])";
                            }
                            else
                            {
                                $RANGE_SQL .= "(`SURA`=$SUBREF[0] AND `VERSE`>=$SUBREF[1] AND `VERSE`<=" . ($SUBREF[1] + $ADD_CONTEXT) . ")";
                            }
                        }
                        else
                        {
                            if ($ADD_CONTEXT == 0)
                            {
                                add_to_master_array($SUBREF[0], $SUBREF[1]);
                            }
                            else
                            {
                                for ($a = 0; $a < $ADD_CONTEXT; $a++)
                                {
                                    add_to_master_array($SUBREF[0], $SUBREF[1] + $a);
                                }
                            }
                        }
                    }
                }

                continue;
            }
        }

        // range of verses within one chapter
        if (substr_count($REFERENCE, ":") == 1 && substr_count($REFERENCE, "-") == 1 && substr_count($REFERENCE, ",") == 0)
        {
            $CHAPTER = substr($REFERENCE, 0, strpos($REFERENCE, ":"));
            if (is_numeric($CHAPTER))
            {
                if ($CHAPTER >= 1 && $CHAPTER <= 114)
                {
                    $SUBREF = explode("-", substr($REFERENCE, strpos($REFERENCE, ":") + 1, strlen($REFERENCE)));

                    if (is_numeric($SUBREF[0]) && is_numeric($SUBREF[1]))
                    {
                        if ($SUBREF[1] >= $SUBREF[0] && $SUBREF[0] > 0 && $SUBREF[1] > 0)
                        {
                            if ($SUBREF[0] <= verses_in_sura($CHAPTER) && $SUBREF[1] <= verses_in_sura($CHAPTER))
                            {
                                $error = false;

                                $SUBREF[1] += $ADD_CONTEXT;

                                if ($SUBREF[0] == 1 && $SUBREF[1] == verses_in_sura($CHAPTER))
                                {
                                    $FULL_SURA_LIST[] = $CHAPTER;
                                }

                                // build SQL
                                if ($buildSQL)
                                {
                                    $RANGE_SQL .= "(`SURA`=$CHAPTER AND `VERSE`>=$SUBREF[0] AND `VERSE`<=$SUBREF[1])";
                                }
                                else
                                {
                                    for ($v = $SUBREF[0]; $v <= $SUBREF[1]; $v++)
                                    {
                                        add_to_master_array($CHAPTER, $v);
                                    }
                                }
                                continue;
                            }
                        }
                    }
                }
            }
        }

        // range of verses across chapters
        if (substr_count($REFERENCE, ":") == 2 && substr_count($REFERENCE, "-") == 1 && substr_count($REFERENCE, ",") == 0)
        {
            $FROM_REF = explode(":", substr($REFERENCE, 0, strpos($REFERENCE, "-")));
            $TO_REF   = explode(":", substr($REFERENCE, strpos($REFERENCE, "-") + 1, strlen($REFERENCE)));

            if (is_numeric($FROM_REF[0]) && is_numeric($FROM_REF[1]) && is_numeric($TO_REF[0]) && is_numeric($TO_REF[1]))
            {
                if ($FROM_REF[0] >= 1 && $FROM_REF[0] <= 114 & $TO_REF[0] >= 1 && $TO_REF[0] <= 114 && $FROM_REF[0] < $TO_REF[0])
                {
                    if ($FROM_REF[1] > 0 && $TO_REF[1] > 0)
                    {
                        if ($FROM_REF[1] <= verses_in_sura($FROM_REF[0]) && $TO_REF[1] <= verses_in_sura($TO_REF[0]))
                        {
                            $FROM_REF[1] -= $ADD_CONTEXT;
                            if ($FROM_REF[1] < 1)
                            {
                                $FROM_REF[1] = 1;
                            }
                            $TO_REF[1] += $ADD_CONTEXT;
                            if ($TO_REF[1] > verses_in_sura($TO_REF[0]))
                            {
                                $TO_REF[1] = verses_in_sura($TO_REF[0]);
                            }

                            for ($s = $FROM_REF[0]; $s <= $TO_REF[0]; $s++)
                            {
                                $start = 1;
                                if ($s == $FROM_REF[0])
                                {
                                    $start = $FROM_REF[1];
                                }

                                $end = verses_in_sura($s);
                                if ($s == $TO_REF[0])
                                {
                                    $end = $TO_REF[1];
                                }

                                if ($start == 1 && $end == verses_in_sura($s))
                                {
                                    $FULL_SURA_LIST[] = $s;
                                }

                                $error = false;
                                // build SQL
                                if ($buildSQL)
                                {
                                    if ($RANGE_SQL != "")
                                    {
                                        $RANGE_SQL .= " OR ";
                                    } //OR2
                                    $RANGE_SQL .= "(`SURA`=$s AND `VERSE`>=$start AND `VERSE`<=$end)";
                                }
                                else
                                {
                                    for ($v = $start; $v <= $end; $v++)
                                    {
                                        add_to_master_array($s, $v);
                                    }
                                }
                            }

                            continue;
                        }
                    }
                }
            }
        }

        // mixed range of verses in the same chapter
        // e.g 2:3, 4f, 5ff, 6-, 6-10
        if (substr_count($REFERENCE, ":") == 1 && substr_count($REFERENCE, ",") > 0)
        {
            $CHAPTER = substr($REFERENCE, 0, strpos($REFERENCE, ":"));
            if (is_numeric($CHAPTER))
            {
                if ($CHAPTER >= 1 && $CHAPTER <= 114)
                {
                    $PARTS = explode(",", substr($REFERENCE, strpos($REFERENCE, ":") + 1, strlen($REFERENCE)));
                    foreach ($PARTS as $SUBVERSE)
                    {
                        $error2 = true; // will be set to false if we clear parsing below

                        if ($RANGE_SQL != "" and substr($RANGE_SQL, -3) != "OR ")
                        {
                            $RANGE_SQL .= " OR ";
                        }	//OR3

                        // process each subverse

                        // single verse
                        if (is_numeric($SUBVERSE))
                        {
                            if ($SUBVERSE > 0)
                            {
                                if ($SUBVERSE <= verses_in_sura($CHAPTER))
                                {
                                    $error2 = false;

                                    $temp = $SUBVERSE;
                                    if ($ADD_CONTEXT > 0)
                                    {
                                        $temp = $SUBVERSE + $ADD_CONTEXT;
                                        if ($temp > verses_in_sura($CHAPTER))
                                        {
                                            $temp = verses_in_sura($CHAPTER);
                                        }
                                    }

                                    // build SQL
                                    if ($buildSQL)
                                    {
                                        $RANGE_SQL .= "(`SURA`=$CHAPTER AND `VERSE`>=$SUBVERSE AND `VERSE`<=$temp)";
                                    }
                                    else
                                    {
                                        add_to_master_array($CHAPTER, $SUBVERSE);
                                    }
                                    continue;
                                }
                            }
                        }

                        // single verse plus "ff"
                        if (strtolower(substr($SUBVERSE, -2)) == "ff")
                        {
                            $temp = substr($SUBVERSE, 0, strlen($SUBVERSE) - 2);
                            if (is_numeric($temp))
                            {
                                if ($temp > 0 && $temp < verses_in_sura($CHAPTER))
                                {
                                    $error2 = false;
                                    if ($temp == 1)
                                    {
                                        $FULL_SURA_LIST[] = $CHAPTER;
                                    }
                                    // build SQL
                                    if ($buildSQL)
                                    {
                                        $RANGE_SQL .= "(`SURA`=$CHAPTER AND `VERSE`>=$temp AND `VERSE`<=" . verses_in_sura($CHAPTER) . ")";
                                    }
                                    else
                                    {
                                        for ($v = $temp; $v <= verses_in_sura($CHAPTER); $v++)
                                        {
                                            add_to_master_array($CHAPTER, $v);
                                            if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                            {
                                                break;
                                            }
                                        }
                                    }

                                    continue;
                                }
                            }
                        }

                        // single verse plus "-"
                        if (strtolower(substr($SUBVERSE, -1)) == "-")
                        {
                            $temp = substr($SUBVERSE, 0, strlen($SUBVERSE) - 1);
                            if (is_numeric($temp))
                            {
                                if ($temp > 0 && $temp < verses_in_sura($CHAPTER))
                                {
                                    $error2 = false;
                                    if ($temp == 1)
                                    {
                                        $FULL_SURA_LIST[] = $CHAPTER;
                                    }
                                    // build SQL
                                    if ($buildSQL)
                                    {
                                        $RANGE_SQL .= "(`SURA`=$CHAPTER AND `VERSE`>=$temp AND `VERSE`<=" . verses_in_sura($CHAPTER) . ")";
                                    }
                                    else
                                    {
                                        for ($v = $temp; $v <= verses_in_sura($CHAPTER); $v++)
                                        {
                                            add_to_master_array($CHAPTER, $v);
                                            if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                            {
                                                break;
                                            }
                                        }
                                    }

                                    continue;
                                }
                            }
                        }

                        // single verse plus an "f"
                        if (strtolower(substr($SUBVERSE, -1)) == "f")
                        {
                            $temp = substr($SUBVERSE, 0, strlen($SUBVERSE) - 1);
                            if (is_numeric($temp))
                            {
                                if ($temp > 0)
                                {
                                    if ($temp < verses_in_sura($CHAPTER))
                                    {
                                        $error2 = false;

                                        $temp2 = $temp + 1 + $ADD_CONTEXT;
                                        if ($temp2 > verses_in_sura($CHAPTER))
                                        {
                                            $temp2 = verses_in_sura($CHAPTER);
                                        }

                                        // build SQL
                                        if ($buildSQL)
                                        {
                                            $RANGE_SQL .= "(`SURA`=$CHAPTER AND (`VERSE`>=$temp AND `VERSE`<=" . ($temp2) . "))";
                                        }
                                        else
                                        {
                                            for ($i = $temp; $i <= $temp2; $i++)
                                            {
                                                add_to_master_array($CHAPTER, $i);
                                            }
                                        }
                                        continue;
                                    }
                                }
                            }
                        }

                        // range of verses
                        if (substr_count($SUBVERSE, "-") == 1)
                        {
                            $FROM_REF = substr($SUBVERSE, 0, strpos($SUBVERSE, "-"));
                            $TO_REF   = substr($SUBVERSE, strpos($SUBVERSE, "-") + 1, strlen($SUBVERSE));

                            if (is_numeric($FROM_REF) && is_numeric($TO_REF))
                            {
                                if ($FROM_REF > 0 && $TO_REF > 0 && $FROM_REF < $TO_REF)
                                {
                                    if ($FROM_REF < verses_in_sura($CHAPTER) && $TO_REF <= verses_in_sura($CHAPTER))
                                    {
                                        $TO_REF += $ADD_CONTEXT;
                                        if ($TO_REF > verses_in_sura($CHAPTER))
                                        {
                                            $TO_REF = verses_in_sura($CHAPTER);
                                        }

                                        $error2 = false;
                                        if ($FROM_REF == 1 && $TO_REF == verses_in_sura($CHAPTER))
                                        {
                                            $FULL_SURA_LIST[] = $CHAPTER;
                                        }
                                        if ($buildSQL)
                                        {
                                            $RANGE_SQL .= "(`SURA`=$CHAPTER AND `VERSE`>=$FROM_REF AND `VERSE`<=$TO_REF)";
                                        }
                                        else
                                        {
                                            for ($v = $FROM_REF; $v <= $TO_REF; $v++)
                                            {
                                                add_to_master_array($CHAPTER, $v);
                                                if (count($MASTER_VERSE_LIST) >= $MAX_ARRAY_SIZE)
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!$error2)
            {
                $error = false;
            }
        }

        if ($error)
        {
            $e = "You have specified a bad range: $REFERENCE";

            if ($SUBVERSE != "")
            {
                $e .= " / $SUBVERSE";
            }

            $error_messages[] .= $e;

            return;
        }
    }

    // clean up double spaces
    $RANGE_SQL = preg_replace('/\s+/', ' ', $RANGE_SQL);

    // clean up double OR's (which sometimes creep in)
    while (stripos($RANGE_SQL, " OR OR ") > 0)
    {
        $RANGE_SQL = str_ireplace("OR OR", " OR ", $RANGE_SQL);
    }
}
