<?php

if (!isset($_GET["COMMAND"]))
{
    return;
}

require_once '../library/config.php';
require_once 'library/functions.php';

$command = $_GET["COMMAND"];

echo "<div class=commandHelpTextHomePage>";

if ($command == "ROOT")
{
    echo "Searches the Arabic text for the specific root word that you have specified. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb' class='linky helpPageLink'>ROOT:ktb</a></b></p>";
}

if ($command == "LEMMA")
{
    echo "Searches the text for the lemma (dictionary head word form) of the word that you specify. For example:";
    echo "<p><b><a href='../home.php?L=LEMMA:كِتَٰب' class='linky'>LEMMA:كِتَٰب</a></b></p>";
}

if ($command == "TEXT")
{
    echo "Performs a full text search and searches the Arabic (and transliterated) text for any word in which the string of letters you have supplied appears. For example:";
    echo "<p><b><a href='../home.php?L=TEXT:kit' class='linky'>TEXT:kit</a></b></p>";
}

if ($command == "EXACT")
{
    echo "Searches the text for the exact inflected form of a word, precisely as you have typed it. For example:";
    echo "<p><b><a href='../home.php?L=EXACT:yaktubūna' class='linky'>EXACT:yaktubūna</a></b></p>";
}

if ($command == "PROVENANCE")
{
    echo "Allows you to search within suras of a particular provenance, Meccan or Medinan. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:KTB AND PROVENANCE:Meccan' class='linky'>ROOT:KTB AND PROVENANCE:Meccan</a></b></p>";
    echo "<p>Will search for the root 'ktb' but only where it occurs in <i>Meccan</i> suras.</p>";
}

if ($command == "MECCAN")
{
    echo "Used with the PROVENANCE command, this allows you to search within suras of <i>Meccan</i> provenance. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:KTB AND PROVENANCE:Meccan' class='linky'>ROOT:KTB AND PROVENANCE:Meccan</a></b></p>";
    echo "<p>Will search for the root 'ktb' but only where it occurs in Meccan suras.</p>";
}

if ($command == "MEDINAN")
{
    echo "Used with the PROVENANCE command, this allows you to search within suras of <i>Medinan</i> provenance. For example:";
    echo "<p><b><a href='../home.php?L=ENGLISH:throne AND PROVENANCE:Medinan' class='linky'>ENGLISH:throne AND PROVENANCE:Medinan</a></b></p>";
    echo "<p>Will search the English translations for the word 'throne', but only where it occurs in Medinan suras.</p>";
}

if ($command == "ENGLISH")
{
    echo "Searches all of the English translations of the Qur’an for the word you specify. For example:";
    echo "<p><b><a href='../home.php?L=ENGLISH:throne' class=linky>ENGLISH:throne</a></b></p>";
}

$result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` WHERE `TRANSLATION ALL CAPS NAME`='" . db_quote($command) . "'");

if (db_rowcount($result_translation) > 0)
{
    $ROWT = db_return_row($result_translation);

    echo "Searches " . $ROWT["DESCRIPTION"] . " for the word you specify. For example:";
    echo "<p><b><a href='../home.php?L=" . $ROWT["TRANSLATION ALL CAPS NAME"] . ":fear' class=linky>" . $ROWT["TRANSLATION ALL CAPS NAME"] . ":fear</a></b></p>";
}

if ($command == "AND")
{
    echo "Enables you to build up complex searches by searching for multiple search terms; it will find verses where all the search terms are included. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:qwl AND PROVENANCE:Meccan' class=linky>ROOT:qwl AND PROVENANCE:Meccan</a></b></p>";
    echo "<p><b><a href='../home.php?L=ROOT:قول AND ROOT: mlk' class=linky>ROOT:قول AND ROOT: mlk</a></b></p>";
}

if ($command == "OR")
{
    echo "enables you to search for multiple search terms; it will find verses where any of the search terms are included. Here are some examples:";
    echo "<p><b><a href='../home.php?L=ENGLISH:book OR ENGLISH:scripture' class=linky>ENGLISH:book OR ENGLISH:scripture</a></b></p>";
    echo "<p><b><a href='../home.php?L=LEMMA:يَعْقُوب OR LEMMA:مُوسَىٰ OR LEMMA:إِبْرَاهِيم' class=linky>LEMMA:يَعْقُوب OR LEMMA:مُوسَىٰ OR LEMMA:إِبْرَاهِيم</a></b></p>";
}

if ($command == "NOT")
{
    echo "The NOT command tells Qur’an Tools to exclude verses with a word that matches your search term. For example, to find the verses where the Arabic root ktb occurs but not the translated words ‘people of the’, you could perform this search:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb AND NOT ENGLISH:\"people of the\"' class=linky>ROOT:ktb AND NOT ENGLISH:\"people of the\"</a></b></p>";
}

if ($command == "RANGE")
{
    echo "Limits a search to just a particular portion of the Qur’an, rather than to search the entire text. You would normally use it after another search command. For example, the following would find occurrences of the Arabic root <i>ktb</i> (كتب) just in sura 2:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb RANGE:2' class=linky>ROOT:ktb RANGE:2</a></b></p>";
}

if ($command == "FOLLOWED BY")
{
    echo "Instructs Qur’an Tools search for a root that is then followed by another specific one. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:jhd FOLLOWED BY:sbl' class=linky>ROOT:jhd FOLLOWED BY:sbl</a></b></p>";
}

if ($command == "PRECEDED BY")
{
    echo "Instructs Qur’an Tools search for a root that is preceded by another specific one. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:sbl PRECEDED BY:jhd' class=linky>ROOT:sbl PRECEDED BY:jhd</a></b></p>";
}

if ($command == "WITHIN")
{
    echo "Used in conjunction with the FOLLOWED BY or PRECEDED BY commands, to tell Qur’an Tools to look for the other root within a certain number of words. For example:";
    echo "<p><b><a href='../home.php?L=ROOT:jhd FOLLOWED BY:sbl WITHIN 2 WORDS' class=linky>ROOT:jhd FOLLOWED BY:sbl WITHIN 2 WORDS</a></b></p>";
}

if ($command == "VERB")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root that are verbs. Don't forget to follow the root with @ and enclose 'VERB' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[VERB]' class=linky>ROOT:ktb@[VERB]</a></b></p>";
}

if ($command == "NOUN")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root that are nouns. Don't forget to follow the root with @ and enclose 'NOUN' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[NOUN]' class=linky>ROOT:ktb@[NOUN]</a></b></p>";
}

if ($command == "NOMINATIVE")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root that are in the nominative case. Don't forget to follow the root with @ and enclose 'NOUN' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[NOMINATIVE]' class=linky>ROOT:ktb@[NOMINATIVE]</a></b></p>";
}

if ($command == "ACCUSATIVE")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root that are in the accusative case. Don't forget to follow the root with @ and enclose 'NOUN' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[ACCUSATIVE]' class=linky>ROOT:ktb@[ACCUSATIVE]</a></b></p>";
}

if ($command == "GENITIVE")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root that are in the genitive case. Don't forget to follow the root with @ and enclose 'GENITIVE' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[GENITIVE]' class=linky>ROOT:ktb@[GENITIVE]</a></b></p>";
}

if ($command == "MASCULINE")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root whose grammatical gender is masculine. Don't forget to follow the root with @ and enclose 'MASCULINE' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[MASCULINE]' class=linky>ROOT:ktb@[MASCULINE]</a></b></p>";
}

if ($command == "FEMININE")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root whose grammatical gender is feminine. Don't forget to follow the root with @ and enclose 'FEMININE' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:jry@[FEMININE]' class=linky>ROOT:jry@[FEMININE]</a></b></p>";
}

if ($command == "SINGULAR")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root whose grammatical number is singular. Don't forget to follow the root with @ and enclose 'SINGULAR' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:jry@[SINGULAR]' class=linky>ROOT:jry@[SINGULAR]</a></b></p>";
}

if ($command == "DUAL")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root whose grammatical number is dual. Don't forget to follow the root with @ and enclose 'DUAL' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:qrb@[DUAL]' class=linky>ROOT:qrb@[DUAL]</a></b></p>";
}

if ($command == "PLURAL")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root whose grammatical number is plural. Don't forget to follow the root with @ and enclose 'PLURAL' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[PLURAL]' class=linky>ROOT:ktb@[PLURAL]</a></b></p>";
}

if ($command == "1P")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root in the first person. Don't forget to follow the root with @ and enclose '1P' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[1P]' class=linky>ROOT:ktb@[1P]</a></b></p>";
}

if ($command == "2P")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root in the second person. Don't forget to follow the root with @ and enclose '1P' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[2P]' class=linky>ROOT:ktb@[2P]</a></b></p>";
}

if ($command == "3P")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root in the third person. Don't forget to follow the root with @ and enclose '1P' in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[3P]' class=linky>ROOT:ktb@[3P]</a></b></p>";
}

if ($command == "FORM")
{
    echo "Used with the ROOT command to tell Qur&rsquo;an Tools to find instances of the root in a particular Arabic form (forms are numbered with Roman numerals, from I to XII). Don't forget to follow the root with @ and enclose 'FORM' and the form number in square brackets, as in this example:";
    echo "<p><b><a href='../home.php?L=ROOT:ktb@[FORM:II]' class=linky>ROOT:ktb@[FORM:II]</a></b></p>";
}

if ($command == "HAPAX")
{
    echo "Finds every root that only appears once in the Qur’an (the technical linguistic term for such words is a hapax legomenon. You can either use it with the root command, like this:";
    echo "<p><b><a href='../home.php?L=ROOT:bql@[HAPAX]' class=linky>ROOT:bql@[HAPAX]</a></b></p>";
    echo "<p>Or more commonly, on its own, like this:</p>";
    echo "<p><b><a href='../home.php?L=[HAPAX]' class=linky>[HAPAX]</a></b></p>";
}

if ($command == "UNIQUE")
{
    echo "Finds every root that appears only in a single sura. You can either use it with the root command, like this:";
    echo "<p><b><a href='../home.php?L=ROOT:khf@[UNIQUE]' class=linky>ROOT:khf@[UNIQUE]</a></b></p>";
    echo "<p>Or on its own, like this:</p>";
    echo "<p><b><a href='../home.php?L=[UNIQUE]' class=linky>[UNIQUE]</a></b></p>";
}

if ($command == "FORMULA")
{
    echo "Underpinning the Arabic text of the Qur’an is an extensive network of formulaic diction -- short, repeated phrases that are used time and time again. Arguably, they are a powerful indicator of the Qur’an being constructed, at least in part, live in oral performance. The FORMULA search command enables you to search the qur’anic text for a particular formula, for example:";
    echo "<p><b><a href='../home.php?L=FORMULA:jnn%2Bjry%2Btht%2Bnhr' class=linky>FORMULA:jnn+jry+tht+nhr</a></b></p>";
    echo "<p>A much easier way to search for formulae without typing a search command manually is simply by finding the formula you are interested in by using Qur’an Tools' <a href='/formulae/list_formulae.php' class=linky-parsed-word>formulae list screen</a> and clicking on it.</p>";
}

if ($command == "DENSITY")
{
    echo "Every sura and verse in Qur’an Tools has its formulaic density recorded — the percentage of roots or lemma in that verse that form part of a formulaic phrase. You can easily search by formulaic density, to find verses with a particular percentage of formulae recorded with in them. For example, to find verses that are more than 40% formulaic:";
    echo "<p><b><a href='../home.php?L=DENSITY>40' class=linky>DENSITY>40</a></b></p>";
}

if ($command == "LENGTH")
{
    echo "Used to modify the DENSITY search command. For example, to find verses that are more than 40% formulaic, but only when one counts formulae that are 4 Arabic roots long:";
    echo "<p><b><a href='../home.php?L=DENSITY>40@[LENGTH:4]' class=linky>DENSITY>40@[LENGTH:4]</a></b></p>";
}

if ($command == "TYPE")
{
    echo "Used to modify the DENSITY search command. For example, to find verses that are more than 40% formulaic, but only when one counts <i>root</i> type formulae:";
    echo "<p><b><a href='../home.php?L=DENSITY>40@[TYPE:ROOT]' class=linky>DENSITY>40@[TYPE:ROOT]</a></b></p>";
}

if ($command == "ROOT-ALL")
{
    echo "Used with the TYPE modifier to adjust a DENSITY search. For example, to find verses that are more than 40% formulaic, but only when one counts <i>root-all</i> type formulae (roots plus particles/pronouns):";
    echo "<p><b><a href='../home.php?L=DENSITY>40@[TYPE:ROOT-ALL]' class=linky>DENSITY>40@[TYPE:ROOT-ALL]</a></b></p>";
}

if ($command == "LEMMAF")
{
    echo "Used with the TYPE modifier to adjust a DENSITY search. For example, to find verses that are more than 40% formulaic, but only when one counts <i>lemma</i> type formulae:";
    echo "<p><b><a href='../home.php?L=DENSITY>40@[TYPE:LEMMA]' class=linky>DENSITY>40@[TYPE:LEMMA]</a></b></p>";
}

if ($command == "ROOTF")
{
    echo "Used with the TYPE modifier to adjust a DENSITY search. For example, to find verses that are more than 40% formulaic, but only when one counts <i>root</i> type formulae:";
    echo "<p><b><a href='../home.php?L=DENSITY>40@[TYPE:LEMMA]' class=linky>ROOT>40@[TYPE:ROOT]</a></b></p>";
}

// strip off the trailing "F" from ROOTF or LEMMAF (passed to disambiguate them)
if ($command == "ROOTF")
{
    $command = "ROOT";
}
if ($command == "LEMMAF")
{
    $command = "LEMMA";
}

echo "<hr>Simply click the <b>$command</b> command word in the list above to insert it into Qur&rsquo;an Tools' search box.";

echo "<p><a href='help/advanced-searching.php' class=nodec><font color=blue>Learn more about Qur&rsquo;an Tools&rsquo;s search commands</font></a></p>";

echo "</div>";
