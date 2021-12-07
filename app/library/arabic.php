<?php

// QUR’AN TOOLS ARABIC RENDERER SCRIPTS

// Buckwalter Transliteration Table Encoded As Array

// used to convert just one word (and not print it)

function return_arabic_word($word)
{
    if ($word == "PORP")
    {
        return "[PART/PRON]";
    }

    global $font_encode;

    $text = "";
    for ($i = 0; $i < strlen($word); $i++)
    {
        // skip spaces
        if (substr($word, $i, 1) != " ")
        {
            $text .= $font_encode[substr($word, $i, 1)]['glyph'];
        }
    }
    // fudge the name ALLAH to make the fonts work
    if ($word == "{ll~ahi")
    {
        $text = "اللَّـهِ";
    }

    if ($word == "{ll~ah")
    {
        $text = "ٱللَّـهُ";
    }

    // display waqf marks in different style
    $text = preg_replace('/ ([ۖ-۩])/u', '<span class="sign">&nbsp;$1</span>', $text);

    return $text;
}

function print_arabic($sentence)
{
    global $font_encode, $arabic_tooltips;

    // split into words

    $words = explode(" ", $sentence);

    // loop through each word

    $count = 0;

    foreach ($words as $word)
    {
        $count++;

        // convert each to arabic

        if ($text != "")
        {
            $text .= " &nbsp;";
        }

        $next_up = "";

        for ($i = 0; $i < strlen($word); $i++)
        {
            $next_up .= $font_encode[substr($word, $i, 1)]['glyph'];
        }

        // fudge the name ALLAH to make the font work
        if ($word == "{ll~ahi")
        {
            $next_up = "اللَّـهِ";
        }

        // display waqf marks in different style
        $text = preg_replace('/ ([ۖ-۩])/u', '<span class="sign">&nbsp;$1</span>', $text);

        // =========================================================================================================

        // add the tooltip from the array we built in verse_browser.php
        $text .= "<span class=\"simple-tooltip\" title=\"" . $arabic_tooltips[$count - 1] . "\">" . $next_up . "</span>";

        // or do it without tooltips
        // $text.= $next_up;

        // =========================================================================================================
    }

    //echo "<span class='aya'>";
    echo "<font size='+1'>$text</font>";
    //echo "</span>";
}

$font_encode = [
    "'" => [
        "glyph" => "ء", "unicode" => "0621", "orthography" => "Hamza"
    ],

    ">" => [
        "glyph" => "أ", "unicode" => "0623", "orthography" => "Alif + HamzaAbove"
    ],

    "&" => [
        "glyph" => "ؤ", "unicode" => "0624", "orthography" => "Waw + HamzaAbove"
    ],

    "<" => [
        "glyph" => "إ", "unicode" => "0625", "orthography" => "Alif + HamzaBelow"
    ],

    "}" => [
        "glyph" => "ئ", "unicode" => "0626", "orthography" => "Ya + HamzaAbove"
    ],

    "A" => [
        "glyph" => "ا", "unicode" => "0627", "orthography" => "Alif"
    ],

    "b" => [
        "glyph" => "ب", "unicode" => "0628", "orthography" => "Ba"
    ],

    "p" => [
        "glyph" => "ة", "unicode" => "0629", "orthography" => "TaMarbuta"
    ],

    "t" => [
        "glyph" => "ت", "unicode" => "062A", "orthography" => "Ta"
    ],

    "v" => [
        "glyph" => "ث", "unicode" => "062B", "orthography" => "Tha"
    ],

    "j" => [
        "glyph" => "ج", "unicode" => "062C", "orthography" => "Jeem"
    ],

    "H" => [
        "glyph" => "ح", "unicode" => "062D", "orthography" => "HHa"
    ],

    "x" => [
        "glyph" => "خ", "unicode" => "062E", "orthography" => "Kha"
    ],

    "d" => [
        "glyph" => "د", "unicode" => "062F", "orthography" => "Dal"
    ],

    "*" => [
        "glyph" => "ذ", "unicode" => "0630", "orthography" => "Thal"
    ],

    "r" => [
        "glyph" => "ر", "unicode" => "0631", "orthography" => "Ra"
    ],

    "z" => [
        "glyph" => "ز", "unicode" => "0632", "orthography" => "Zain"
    ],

    "s" => [
        "glyph" => "س", "unicode" => "0633", "orthography" => "Seen"
    ],

    "$" => [
        "glyph" => "ش", "unicode" => "0634", "orthography" => "Sheen"
    ],

    "S" => [
        "glyph" => "ص", "unicode" => "0635", "orthography" => "Sad"
    ],

    "D" => [
        "glyph" => "ض", "unicode" => "0636", "orthography" => "DDad"
    ],

    "T" => [
        "glyph" => "ط", "unicode" => "0637", "orthography" => "TTa"
    ],

    "Z" => [
        "glyph" => "ظ", "unicode" => "0638", "orthography" => "DTha"
    ],

    "E" => [
        "glyph" => "ع", "unicode" => "0639", "orthography" => "Ain"
    ],

    "g" => [
        "glyph" => "غ", "unicode" => "063A", "orthography" => "Ghain"
    ],

    "_" => [
        "glyph" => "ـ", "unicode" => "0640", "orthography" => "Tatweel"
    ],

    "f" => [
        "glyph" => "ف", "unicode" => "0641", "orthography" => "Fa"
    ],

    "q" => [
        "glyph" => "ق", "unicode" => "0642", "orthography" => "Qaf"
    ],

    "k" => [
        "glyph" => "ك", "unicode" => "0643", "orthography" => "Kaf"
    ],

    "l" => [
        "glyph" => "ل", "unicode" => "0644", "orthography" => "Lam"
    ],

    "m" => [
        "glyph" => "م", "unicode" => "0645", "orthography" => "Meem"
    ],

    "n" => [
        "glyph" => "ن", "unicode" => "0646", "orthography" => "Noon"
    ],

    "h" => [
        "glyph" => "ه", "unicode" => "0647", "orthography" => "Ha"
    ],

    "w" => [
        "glyph" => "و", "unicode" => "0648", "orthography" => "Waw"
    ],

    "Y" => [
        "glyph" => "ى", "unicode" => "0649", "orthography" => "AlifMaksura"
    ],

    "y" => [
        "glyph" => "ي", "unicode" => "064A", "orthography" => "Ya"
    ],

    "F" => [
        "glyph" => "ً", "unicode" => "064B", "orthography" => "Fathatan"
    ],

    "N" => [
        "glyph" => "ٌ", "unicode" => "064C", "orthography" => "Dammatan"
    ],

    "K" => [
        "glyph" => "ٍ", "unicode" => "064D", "orthography" => "Kasratan"
    ],

    "a" => [
        "glyph" => "َ", "unicode" => "064E", "orthography" => "Fatha"
    ],

    "u" => [
        "glyph" => "ُ", "unicode" => "064F", "orthography" => "Damma"
    ],

    "i" => [
        "glyph" => "ِ", "unicode" => "0650", "orthography" => "Kasra"
    ],

    "~" => [
        "glyph" => "ّ", "unicode" => "0651", "orthography" => "Shadda"
    ],

    "o" => [
        "glyph" => "ْ", "unicode" => "0652", "orthography" => "Sukun"
    ],

    "^" => [
        "glyph" => "ٓ", "unicode" => "0653", "orthography" => "Maddah"
    ],

    "#" => [
        "glyph" => "ٔ", "unicode" => "0654", "orthography" => "HamzaAbove"
    ],

    "`" => [
        "glyph" => "ٰ", "unicode" => "0670", "orthography" => "AlifKhanjareeya"
    ],

    "{" => [
        "glyph" => "ٱ", "unicode" => "0671", "orthography" => "Alif + HamzatWasl"
    ],

    ":" => [
        "glyph" => "ۜ", "unicode" => "06DC", "orthography" => "Small High Seen"
    ],

    "@" => [
        "glyph" => "۟", "unicode" => "06DF", "orthography" => "Small High Rounded Zero"
    ],

    "\"" => [
        "glyph" => "۠", "unicode" => "06E0", "orthography" => "SmallHighUprightRectangularZero"
    ],

    "[" => [
        "glyph" => "ۢ", "unicode" => "06E2", "orthography" => "SmallHighMeemIsolatedForm"
    ],

    ";" => [
        "glyph" => "ۣ", "unicode" => "06E3", "orthography" => "Small Low Seen"
    ],

    "," => [
        "glyph" => "ۥ", "unicode" => "06E5", "orthography" => "Small Waw"
    ],

    "." => [
        "glyph" => "ۦ", "unicode" => "06E6", "orthography" => "Small Ya"
    ],

    "!" => [
        "glyph" => "ۨ", "unicode" => "06E8", "orthography" => "Small High Noon"
    ],

    "-" => [
        "glyph" => "۪", "unicode" => "06EA", "orthography" => "EmptyCentreLowStop"
    ],

    "+" => [
        "glyph" => "۫", "unicode" => "06EB", "orthography" => "EmptyCentreHighStop"
    ],

    "%" => [
        "glyph" => "۬", "unicode" => "06EC", "orthography" => "RoundedHighStopWithFilledCentre"
    ],

    "]" => [
        "glyph" => "ۭ", "unicode" => "06ED", "orthography" => "Small Low Meem"
    ]
];
