<?php

// GNU GPL License Page.

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once '../library/functions.php';
	
?>
<!DOCTYPE html>
<html>

<head>
    <?php
    require '../library/standard_header.php';
    window_title("Help: Advanced Searching");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Advanced Searching</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 Qur’an Tools provides a number of powerful ways to search the qur’anic text. Searches are conducted by typing a search command into the search box on the home page. Each search command and function is described below.
</p>
<table border=1 cellpadding="4">
<tbody>
<tr>
	<td valign="top">
		<strong>ROOT</strong>
	</td>
	<td valign="top">
		<p>
			 Searches the Arabic text for the specific root word that you have specified. For example:
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb">ROOT:ktb</a>
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 You can specify a qur’anic root by giving its English transliteration, 
				<a href="arabic-letters-transliterations-and-encodings.php">Buckwalter encoding</a>, or simply by typing the Arabic. For example, the three searches below are all equivalent.
			</p>
			<p>
				<a href="/home.php?L=ROOT:رحم">ROOT:رحم</a>
			</p>
			<p>
				<a href="/home.php?L=ROOT:rḥm">ROOT:rḥm</a>
			</p>
			<p>
				<a href="/home.php?L=ROOT:rHm">ROOT:rHm</a>
			</p>
			<p>
				 Remember you can use the pop up keyboard on the home page to easily enter Arabic letters or special characters. Just click on the 
				<strong>Keyboard</strong>&nbsp;button on the home page to make it appear; then click on letters or characters to 'type' them.
			</p>
			<p>
				<img src="images/keyb.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
			</p>
		</div>
		<div>
			<h3>Searching for Specific Grammatical Features</h3>
		</div>
		<p>
			 Each qur’anic root in Qur’an Tools has its full grammatical data stored alongside it (including its part of speech, number, case, person, gender, and Arabic form). You can search for a root with particular grammatical attributes by following it with the @ symbol and then the grammatical feature(s) between square brackets. There are several examples below:
		</p>
		<p>
			<strong><em>Part of Speech</em></strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[NOUN]">ROOT:ktb@[NOUN]</a> ... will find any instances of the root <em>ktb</em>&nbsp;that occur as <strong>nouns</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[VERB]">ROOT:كتب@[VERB]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that occur as <strong>verbs</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT%3Ar%E1%B8%A5m%40%5Badjective%5D">ROOT:rḥm@[ADJECTIVE]</a> ... will find any instances of the root&nbsp;<em>rḥm</em>that are used as an adjective.
		</p>
		<p>
			<a href="/home.php?L=ROOT%3Akll%40%5Badverb%5D">ROOT:kll@[ADVERB]</a> ... will find any instances of the root&nbsp;<em>kll&nbsp;</em>that occur as adverbs.
		</p>
		<p>
			<a href="/home.php?L=LEMMA%3A%D8%A5%D9%90%D9%84%D9%8E%D9%89%D9%B0%40%5Bparticle%5D">LEMMA:إِلَىٰ@[PARTICLE]</a> ... will find any instances of the lemma إِلَىٰthat occur as&nbsp;particles.
		</p>
		<p>
			<a href="/home.php?L=ROOT%3A%27lh%40%5BPROPER%5D">ROOT:'lh@[PROPER]</a> ... will find any instances of the root&nbsp;<em>rḥm&nbsp;</em>that occur as proper nouns (e.g. personal names). In this example, that search would find any occurrences of the root&nbsp;<em>'lh</em>&nbsp;that are proper nouns, giving you every occurrence of the name “Allah” in the Qur’an.
		</p>
		<p id="mood">
			<em><strong>Mood</strong></em>
		</p>
		<p>
			 Imperfect verbs in the Qur’an are found in one of three 
			<em>moods</em>&nbsp;(indicative; jussive; or subjunctive). You can easily search for these moods using Qur’an Tools:
		</p>
		<p>
			<a href="/home.php?L=ROOT%3Ayqn%40%5Bindicative%5D">ROOT:yqn@[indicative]</a> ... will find any instances of the root&nbsp;<em>yqn</em>&nbsp;that occurs in the <strong>indicative</strong> mood
		</p>
		<p>
			<a href="/home.php?L=ROOT%3Afsd%40%5Bjussive%5D">ROOT:fsd@[jussive]</a> ... will find any instances of the root&nbsp;<em>yqn</em>&nbsp;that occurs in the <strong>jussive</strong>&nbsp;mood
		</p>
		<p>
			<a href="/home.php?L=ROOT%3Akwn%40%5Bsubjunctive%5D">ROOT:kwn@[subjunctive]</a>... will find any instances of the root&nbsp;<em>yqn</em>&nbsp;that occurs in the&nbsp;<strong>subjunctive</strong> mood
		</p>
		<p>
			<em><strong>Case</strong></em>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[NOMINATIVE]">ROOT:ktb@[NOMINATIVE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;in the <strong>nominative</strong> case
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[ACCUSATIVE]">ROOT:كتب@[ACCUSATIVE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;in the <strong>accusative</strong>&nbsp;case
		</p>
		<p>
			<a href="/home.php?L=ROOT:%D9%83%D8%AA%D8%A8@[GENITIVE]">ROOT:كتب@[GENITIVE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;in the&nbsp;<strong>genitive</strong>&nbsp;case
		</p>
		<p>
			<em><strong>Gender</strong></em>
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[MASCULINE]">ROOT:كتب@[MASCULINE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are <strong>masculine</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[FEMININE]">ROOT:ktb@[FEMININE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are <strong>feminine</strong>
		</p>
		<p>
			<em><strong>Number</strong></em>
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[SINGULAR]">ROOT:كتب@[SINGULAR]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are&nbsp;<strong>singular</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[DUAL]">ROOT:ktb@[DUAL]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are <strong>dual</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[PLURAL]">ROOT:كتب@[PLURAL]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are&nbsp;<strong>plural</strong>
		</p>
		<p>
			<em><strong>Person</strong></em>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[1P]">ROOT:ktb@[1P]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are&nbsp;in the <strong>first person</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[2P]">ROOT:ktb@[2P]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are&nbsp;in the&nbsp;<strong>second</strong> <strong>person</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[3P]">ROOT:ktb@[3P]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are&nbsp;in the&nbsp;<strong>third person</strong>
		</p>
		<p>
			<strong><em>Arabic Verb Form</em></strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:كتب@[FORM:I]">ROOT:كتب@[FORM:I]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that&nbsp;are&nbsp;<strong>form 1</strong>&nbsp;Arabic verbs
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[FORM:II]">ROOT:ktb@[FORM:2]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that are <strong>form 2 </strong>Arabic verbs
		</p>
		<p>
			<strong><em>Definiteness</em><br>
			</strong>
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[DEFINITE]">ROOT:ktb@[DEFINITE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that&nbsp;are <strong>definite</strong> (i.e. have an article, e.g.&nbsp;<em>al-</em>, before them)
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[INDEFINITE]">ROOT:ktb@[INDEFINITE]</a> ... will find any instances of the root&nbsp;<em>ktb</em>&nbsp;that&nbsp;are <strong>indefinite</strong> (i.e. that lack an article, such as&nbsp;<em>al-</em>, before them)
		</p>
				<p>
			<em><strong>Words That Are Unique to a Sura or Occur Just Once (Hapaxes)</strong></em>
		</p>
		<p>
			<a href="/home.php?L=[UNIQUE]">[UNIQUE]</a> ... will find every root that only appears in a single sura
		</p>
		<p>
			<a href="/home.php?L=[HAPAX]">[HAPAX]</a> ... will find every root that only appears once in the Qur’an (the technical linguistic term for such words is a hapax legomenon
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 You can browse the statistics for unique roots and hapax legomena in each sura in the 
				<a href="root-usage-by-sura.php">Root Usage by Sura report</a>.&nbsp;
			</p>
		</div>
		<h4><strong>Combining Search Tags</strong></h4>
		<p>
			 You can also stack these tags up to search for something very specific. For example:
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb@[VERB MASC SINGULAR 1P FORM:I]">ROOT:ktb@[VERB MASC SINGULAR 1P FORM:I]</a>
		</p>
		<h4>Searching for Grammatical Tags on Their Own (e.g. Without a Root or Lemma Specified)</h4>
		<p>
			 It is also possible to simply search for a grammatical tag 
			<em>without any root specified</em>: this will list every word of that type. So, for example:
		</p>
		<p>
			<a href="/home.php?L=[NOUN FEMININE]">[NOUN FEMININE]</a> ... will find every feminine noun in the entire Qur’an
		</p>
		<p>
			<a href="/home.php?L=[DUAL]">[DUAL]</a> ... will find every instance of a dual word
		</p>
		<h4 id="foreign">Searching for Loanwords</h4>
		<p>
			 Every word in the Qur’an listed/catalogued as a loanword in Arthur Jeffery’s classic dictionary, 
			<a href="the-foreign-vocabulary-of-the-qur-an.php"><em>The Foreign Vocabulary of the Qur’an</em></a> has been tagged in Qur’an Tools and can be searched for really easily. Just use the [LOANWORD] tag, like this:
		</p>
		<p>
			<a href="/home.php?L=[LOANWORD]">[LOANWORD]</a>
		</p>
		<h4>Searching for a Word’s Position or Ending</h4>
		<p>
			 You can also easily use the @[...] modifier on a ROOT (or a LEMMA, see below) to look for words that fall in a particular place in their verse. For example:
		</p>
		<p>
			<a href="/home.php?L=root:ktb@[position:first]">root:ktb@[position:first]</a><br>
			<br>
			<a href="/home.php?L=root:ktb@[position:last]">root:ktb@[position:last]</a> or if you prefer <a href="/home.php?L=root:ktb@[position:final]">root:ktb@[position:final]</a><br>
			<br>
			 or, to find a word in neither first or last position: 
			<br>
			<br>
			<a href="/home.php?L=root:ktb@[position:middle]">root:ktb@[position:middle]</a>
		</p>
		<p>
			 You can also search for where a word’s transliteration starts or ends with a particular group of letters. For example:
		</p>
		<p>
			<a href="/home.php?L=root:ktb@[starts:kitā]">root:ktb@[starts:kitā]</a>
		</p>
		<p>
			<a href="/home.php?L=root:ktb@[ends:un]">root:ktb@[ends:un]</a>
		</p>
		<p>
			 And by using a [...] command on its own, you can do things like look for poetic or rhyming features in the Qur’an. For example ...
		</p>
		<p>
			<a href="/home.php?L=[position:final ends:na] range:2">[position:final ends:na] range:2</a>
		</p>
		<p>
			 ... will find all the words in sura 2 that end with 
			<em>na</em>&nbsp;and are found at the end of their verses.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>LEMMA</strong>
	</td>
	<td valign="top">
		<p>
			 Searches the text for the lemma (dictionary head word form) of the word that you specify. For example:
		</p>
		<p>
			<a href="/home.php?L=LEMMA:كِتَٰب">LEMMA:كِتَٰب</a>
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 Searching for lemmata can be tricky, because every part of the inflected word needs to be represented correctly in the Arabic search term, and it’s easy to make mistakes. There are three ways to more easily search for the lemma you are interested in:
			</p>
			<ul>
				<li>Lookup an Arabic root’s full details (the <img src="images/info.gif" class="noBdr" style="display: inline; margin: 0px;" width="15" height="15"> icon in the Instant Details Palette, root list, or dictionary view). You’ll then see a list of every lemmata based on that root &mdash; click on any instance to search for it.</li>
				
				<li><a href="/counts/count_all_lemmata.php">Use the Lemmata Word List</a> to find the lemma you want: click on it to search for it.</li>
				<br>
				<li>Find a lemma like the one you’re interested in the browse view &mdash; point to it to pull up the Instant Details Palette and you’ll find a link to search for more lemmata like it.
				
				</li>
			</ul>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>EXACT</strong>
	</td>
	<td valign="top">
		<p>
			 Searches the text for the exact inflected form of a word, precisely as you have typed it in its transliterated form. For example:
		</p>
		<p>
			<a href="/home.php?L=EXACT:yaktubūna">EXACT:yaktubūna</a>
		</p>
		<p>
			 Just as with the LEMMA command, precision is very important &mdash; thus it can sometimes be easier to find an instance of the word you want, point to it, then choose the exact inflection search at the bottom of the Instant Details Palette.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>TEXT</strong>
	</td>
	<td valign="top">
		<p>
			 Performs a full text search and searches the Arabic (and transliterated) text for any word in which the string of letters you have supplied appears. For example:
		</p>
		<p>
			<a href="/home.php?L=TEXT:kit">TEXT:kit</a>
		</p>
		<p>
			 Please note that sometimes this can produce quite odd results, in particular if you try using Arabic rather than transliterated letters, Qur’an Tools may manage to perform the search but be unable to properly count or highlight hits.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>GLOSS</strong>
	</td>
	<td valign="top">
		<p>
			 Searches the brief glosses (English explanations) attached to each Arabic word in Qur’an Tools' database. You can see the gloss of any word just by pointing your mouse at it until the Instant Details Palette appears. In the example below, you can see that 
			<em>qāla</em>&nbsp;is glossed with “He said”.
		</p>
		<p>
			<img src="images/instant.png" style="width: 228px;" class="shadow-image">
		</p>
		<p>
			 You can search these glosses using the GLOSS command. For example, to find any word whose gloss includes “throne” just do:
		</p>
		<p>
			<a href="/home.php?L=GLOSS:throne">GLOSS:throne</a>
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 By default, Qur’an Tools looks for anywhere the letters you search for with the GLOSS command occur in the glosses. So, for example, if you search for:
			</p>
			<p>
				<a href="/home.php?L=GLOSS%3AARK">GLOSS:ARK</a>
			</p>
			<p>
				 That will not just Arabic roots whose gloss contains the word "Ark" but also that contain words like "h 
				<u>ark</u>", "d<u>ark</u>ness", or "m<u>ark</u>". If you want to find just instances of "Ark", e.g. the exact word, then surround your search word with quote marks, like this:
			</p>
			<p>
				<a href="/home.php?L=GLOSS%3A%22ARK%22">GLOSS:"ARK"</a>
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>ENGLISH</strong>
	</td>
	<td valign="top">
		<p>
			 Searches all of the English translations of the Qur’an for the word you specify. For example:
		</p>
		<p>
			<a href="/home.php?L=ENGLISH:god">ENGLISH:god</a>
		</p>
		<p>
			 Note that Qur’an Tools will find anywhere in the text where the word appears (so searching for 'god' will also find 'gods' and 'goddess'). The search is also case- 
			<em>in</em>sensitive, so searching for 'GOD' and searching for 'god' will produce the same results).
		</p>
		<p>
			 You can also use this command to search for longer phrases; simply surround what you're searching for with quote marks, like this:
		</p>
		<p>
			<a href="/home.php?L=ENGLISH:&quot;People of the Book&quot;">ENGLISH:"People of the Book"</a>
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 By default, Qur’an Tools looks for anywhere the letters you search for with the ENGLISH command (or with the ARBERRY, YUSUFALI, SHAKIR, or PICKTHALL commands) occur in the translation. So, for example, if you search for:
			</p>
			<p>
				<a href="/home.php?L=ENGLISH%3AARK">ENGLISH:ARK</a>
			</p>
			<p>
				 That will not just find the word "Ark" but also words like "h 
				<u>ark</u>", "d<u>ark</u>ness", or "m<u>ark</u>". If you want to find just instances of "Ark", e.g. the exact word, then surround your search word with quote marks, like this:
			</p>
			<p>
				<a href="/home.php?L=ENGLISH%3A%22ARK%22">ENGLISH:"ARK"</a>
			</p>
		</div>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 Please note that Qur’an Tools includes punctuation when it searches for a longer phrase. So, for example, if you search for 
				<a href="/home.php?L=ENGLISH:&quot;Most Gracious Most Merciful&quot;">ENGLISH:"Most Gracious Most Merciful"</a> nothing will be found; however, if you include the comma, and search for <a href="/home.php?L=ENGLISH:&quot;Most Gracious, Most Merciful&quot;">ENGLISH:"Most Gracious, Most Merciful"</a>, you will find the results you were looking for.
			</p>
		</div>
	</td>
</tr>

<tr>
	<td valign="top">
		<strong>ARBERRY</strong>
	</td>
	<td valign="top">
		 Similar to the ENGLISH command above, Qur’an Tools also enables you to search a 
		<em>specific</em> translation. In this case, Arthur John Arberry’s 1955 English translation of the Qur’an (<em>The Koran Interpreted</em>). For example: <a href="/home.php?L=ARBERRY:fear">ARBERRY:fear</a>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>YUSUFALI</strong>
	</td>
	<td valign="top">
		 Searches Abdullah Yusuf Ali's English translation of the Qur’an (1934–8; revised edition 1939–40). For example: 
		<a href="/home.php?L=YUSUFALI:fear">YUSUFALI:fear</a>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>PICKTHALL</strong>
	</td>
	<td valign="top">
		 Searches Marmaduke William Pickthall's English translation of the Qur’an (1930). For example: 
		<a href="/home.php?L=PICKTHALL:inspire">PICKTHALL:inspire</a>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>SHAKIR</strong>
	</td>
	<td valign="top">
		<p>
			 Searches M. H. Shakir's English translation of the Qur’an (1987). For example: 
			<a href="/home.php?L=SHAKIR:blind">SHAKIR:blind</a>
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<a name="PROVENANCE"></a> <strong>PROVENANCE</strong>
	</td>
	<td valign="top">
		<p>
			 Traditionally the Qur’an is divided into a number of periods, corresponding to Muhammad's career in Mecca (610CE-622CE) and Medina (622CE-632CE). If the material in a sura was first preached in Mecca, it is labelled 'Meccan' and if in Medina, it is labelled 'Medinan'. There are a number of different systems by which the Qur’an is divided between this categories (and sometimes sub-categories). Qur’an Tools uses the Nöldeke-Schwally-Robinson system to classify each sura.
		</p>
		<p>
			 (See a list of all 114 suras, with their provenance, 
			<a href="/browse_sura.php">here</a>). Qur’an Tools allows you to easily search by sura provenance; for example:
		</p>
		<p>
			<a href="/home.php?L=PROVENANCE:Meccan">PROVENANCE:Meccan</a>
		</p>
		<p>
			 Which will find material from the 'Meccan' period. Or:
		</p>
		<p>
			<a href="/home.php?L=PROVENANCE:Medinan">PROVENANCE:Medinan</a>
		</p>
		<p>
			 Which will show you material from the 'Medinan' period.
		</p>
		<p>
			 For brevity, you can also use the shorter command 'PROV' instead of 'PROVENANCE' if you wish.
		</p>
		<p>
			<a name="RANGE"></a>
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>RANGE</strong>
	</td>
	<td valign="top">
		<p>
			 The RANGE command allows you to tell Qur’an Tools to limit a search to just a particular portion of the Qur’an, rather than to search the entire text. You would normally use it 
			<em>after</em> another search command. For example, the following would find occurrences of the Arabic root ktb (كتب) just in sura 2:
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb RANGE:2">ROOT:ktb RANGE:2</a>
		</p>
		<p>
			 You can make the range as complex as you like. For example, to find the root qwl (قول) in the first hundred verses of sura, or in the last ten suras of the Qur’an you could use:
		</p>
		<p>
			<a href="/home.php?L=ROOT:qwl RANGE:2:1-100;105-114">ROOT:qwl RANGE:2:1-100;105-114</a>
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 When using complex ranges like this, be careful not to put any spaces in your verse lists, or it will cause an error (i.e. RANGE:2,4,5:10-14 is fine, whereas RANGE:2, 4, 5:10-14 will cause an error).
			</p>
		</div>
		<p>
			 As well as a list of suras or verses, if you have defined some bookmarks, you can use a bookmark name to specify a range. For example, suppose we have defined a bookmark called 'Iblis and Adam Stories' to refer to the seven places in the Qur’an where the legend of Iblis, Adam and the angels is retold (<a href="/home.php?L=2:34;7:11-18;15:29-43;17:61-64;18:50;20:116-117;38:71-83">Q. 2:34; 7:11-18; 15:29-43; 17:61-64; 18:50; 20:116-117; 38:71-83</a>). We can now search for every occurrence of the root <em>mlk</em> (ملك = angel) like so:
		</p>
		<p>
			<a href="/home.php?L=ROOT:mlk RANGE:Iblis and Adam Stories">ROOT:mlk RANGE:Iblis and Adam Stories</a>
		</p>
		<p>
			 Searching within a bookmarked range of verses like this can be very powerful if used correctly.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>VERSELENGTH</strong>
	</td>
	<td valign="top">
		<p>
			 The VERSELENGTH command allows you to tell Qur’an Tools to limit a search to just verses of a particular length. For example:
		</p>
		<p>
			<a href="/verse_browser.php?S=ROOT%3Aqwl+and+VERSELENGTH%3D5">ROOT:qwl and VERSELENGTH=5</a>
		</p>
		<p>
			 Other examples:
		</p>
		<p>
			<a href="/verse_browser.php?S=VERSELENGTH%3E4">VERSELENGTH&gt;4</a> <br>
			 (Finds verses that are&nbsp; 
			<em>more than</em>&nbsp;4 words long)
		</p>
		<p>
			<a href="/verse_browser.php?S=VERSELENGTH%3E%3D4">VERSELENGTH&gt;=4</a><br>
			 (Finds verses that are 
			<em>either</em> 4 words long <em>or longer</em>)
		</p>
		<p>
			<a href="/verse_browser.php?S=VERSELENGTH%3C8">VERSELENGTH&lt;8</a> <br>
			 (Finds verses that are&nbsp; 
			<em>less than</em>&nbsp;8 words long)
		</p>
		<p>
			<a href="/verse_browser.php?S=VERSELENGTH%3C%3D8">VERSELENGTH&lt;=8</a> <br>
			 (Finds verses that are&nbsp; 
			<em>either</em>&nbsp;8 words long&nbsp;<em>or shorter</em>)
		</p>
		<p>
			<a href="/verse_browser.php?S=VERSELENGTH%21%3D10">VERSELENGTH!=10</a> <br>
			 (Finds verses that are 
			<em>not</em>&nbsp;10 words long)
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>TAGS</strong>
	</td>
	<td valign="top">
		<p>
			 The TAGS command searches for verses that you have previously 
			<a href="tags.php">tagged</a>. For example, to search for a verse you have tagged with the tag “Angels”, you would use:
		</p>
		<p>
			<a href="/home.php?L=TAG:ANGELS">TAG:ANGELS</a>
		</p>
		<p>
			 If your tag has a space in it, surround the tag name with quote marks, e.g.:
		</p>
		<p>
			<a href="/home.php?L=TAG:" heaven="" and="" hell""="">TAG:"HEAVEN AND HELL"</a>
		</p>
		<p>
			 You can also search for a verse that has 
			<em>any</em>&nbsp;tag applied to it:
		</p>
		<p>
			<a href="/home.php?L=TAG:ANY">TAG:ANY</a>
		</p>
		<div>
      Or for a verse with no tags applied: 
			<br>
			<br>
			<a href="/home.php?L=TAG:NONE">TAG:NONE</a>
			<br>
		</div>
		<p>
			 Finally, you can&nbsp;search for verses tagged with a tag that 
			<em>begins</em> with certain letters. For example, to find all verses tagged with a tag that starts with “heaven”, you could search for:
		</p>
		<p>
			<a href="/home.php?L=TAG:heaven*">TAG:heaven*</a>
		</p>
		<p>
			 ... which would find verses tagged with “heaven”, “heavens and the earth”, “heaven and hell” and so forth.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>FORMULA</strong>
	</td>
	<td valign="top">
		<p>
			 Underpinning the Arabic text of the Qur’an is an&nbsp; 
			<a href="formulaic-analysis.php">extensive network of formulaic diction</a> &mdash; short, repeated phrases that are used time and time again. Arguably, they are a powerful indicator of the Qur’an being constructed, at least in part, live in oral performance. The FORMULA search command enables you to search the qur’anic text for a particular formula, for example:
		</p>
		<p>
			<a href="/home.php?L=FORMULA:jnn%2Bjry%2Btht%2Bnhr">FORMULA:jnn+jry+tht+nhr</a>
		</p>
		<p>
			 There are two easy ways to search for formulae without typing a search command like the one above manually. First, you can find the formula you an interested in by using Qur’an Tools’s 
			<a href="/formulae/list_formulae.php">formulae list screen</a>. Simply find the formula you are interested in and click on it to search for it.
		</p>
		
		<p>
			 The second method is to view a passage of the Qur’an (either by looking it up, or by searching). Then, in the Qur’an Browser window</a>, turn on formulaic highlighting, using the "Formulae" button at the top left of the screen. After you choose which formulae type you are interested in, Qur’an Tools will highlight all the formulae on the screen in blue. Point to one of the little cue numbers that appears after most formulae in the text to open the Formula Details Palette, like this:
		</p>
		<p>
			<img src="images/form-details-palette.png" alt="" style="display: block; margin: auto; box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px;" border="1">
		</p>
		<p>
			 You can then simply click on the formula in the Formula Details Palette to quickly run a search.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>DENSITY</strong>
	</td>
	<td valign="top">
		 Every sura and verse in Qur’an Tools has its 
		<a href="/formulae/formulaic_density_by_sura.php">formulaic density</a> recorded &mdash; the percentage of roots or lemma in that verse that form part of a formulaic phrase. You can easily search by formulaic density, to find verses with a particular percentage of formulae recorded within them. For example, to find verses that are more than 40% formulaic:<br>
		<br>
		<a href="/home.php?L=DENSITY&gt;40">DENSITY&gt;40</a><br>
		<br>
		<p>
			 Or to find verses that are more than 40% formulaic, but only when one counts formulae that are 4 Arabic roots long:
		</p>
		<p>
			<a href="/home.php?L=DENSITY&gt;40@[LENGTH:4]">DENSITY&gt;40@[LENGTH:4]</a>
		</p>
		<p>
			 Or to find verses that are more than 50% formulaic, but only when one counts lemma-type-formulae that are 5 Arabic roots long:
		</p>
		<p>
			<a href="/home.php?L=DENSITY&gt;50@[LENGTH:5;TYPE:LEMMA]">DENSITY&gt;50@[LENGTH:5;TYPE:LEMMA]</a>
		</p>
		<p>
			 (Qur’an Tools currently knows of three formula lengths &mdash; 3, 4 or 5 &mdash; and three formula types: ROOT, LEMMA and ROOT-ALL).
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>INTERTEXTUALITY</strong>
	</td>
	<td valign="top">
		 Qur’an Tools has a rapidly expanding database of all the 
		<a href="intertextual-connections-between-the-qur-an-and-other-texts-traditions.php">intertextual connections</a> that exist between the Qur’an and many texts/traditions from Late Antiquity (and earlier).<br>
		<br>
		 You can easily find verses with intertextual connections by searching like this: 
		<br>
		<br>
		<a href="/home.php?L=INTERTEXTS%3E0">INTERTEXTS&gt;0</a><br>
		<br>
		<p>
			<a href="/home.php?L=INTERTEXTS%3D3">INTERTEXTS=3</a>
		</p>
		<p>
			 Once your search results are displayed, simply point your cursor at any verse reference it. The verse tools palette will appear:
		</p>
		<p>
			<img src="images/vtools.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<p>
			 Click the 
			<img src="images/links.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> icon to list the intertextual connections for that verse:
		</p>
		<p>
			<img src="images/intertexts.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<p>
			 Click the "View Passage" button to view the relevant passage from an intertextual connection (or click "View Entire Source" to view the entire text/document in a separate browser window or tab). You can also click the 
			<img class="noBdr" style="display: inline; margin: 0px;" src="images/table.png" width="25" height="14"> icon to browse all the intertextually connected sources known to Qur’an Tools for the entire Qur’an, or the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to explore them as a chart.
		</p>
	</td>
</tr>
</tbody>
</table>
<h3 id="combo">Combining and Modifying Searches</h3>
<table border=1 cellpadding="4">
<tbody>
<tr>
	<td valign="top">
		<strong>AND</strong>
	</td>
	<td valign="top">
		<p>
			 The AND command enables you to build up complex searches by searching for multiple search terms; it will find verses where all the search terms are included. For example:
		</p>
		<p>
			<a href="/home.php?L=ROOT:ktb AND ROOT:%27hl">ROOT:ktb AND ROOT:'hl</a>
		</p>
		<p>
			<a href="/home.php?L=ROOT:qwl AND PROVENANCE:Meccan">ROOT:qwl AND PROVENANCE:Meccan</a>
		</p>
		<p>
			<a href="/home.php?L=ROOT:قول AND ROOT: mlk AND PROVENANCE:Meccan">ROOT:قول AND ROOT: mlk AND PROVENANCE:Meccan</a>
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>OR</strong>
	</td>
	<td valign="top">
		<p>
			 The OR command also enables you to search for multiple search terms; it will find verses where any of the search terms are included. Here are some examples:
		</p>
		<p>
			<a href="/home.php?L=ENGLISH:book OR ENGLISH:scripture">ENGLISH:book OR ENGLISH:scripture</a>
		</p>
		<p>
			<a href="/home.php?L=ROOT:jnn OR ROOT:mlk@[plural]">ROOT:jnn OR ROOT:mlk@[plural]</a>
		</p>
		<p>
			<a href="/home.php?L=LEMMA:يَعْقُوب OR LEMMA:مُوسَىٰ OR LEMMA:إِبْرَاهِيم"> LEMMA:يَعْقُوب OR LEMMA:مُوسَىٰ OR LEMMA:إِبْرَاهِيم </a>
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>NOT</strong>
	</td>
	<td valign="top">
		 The NOT command tells Qur’an Tools to 
		<em>exclude</em> verses with a word that matches your search term. For example, to find the 4,357 verses in the Qur’an where ‘Allah’ does not appear, you could perform this search:<a href="/home.php?L=NOT ROOT:%27lh">NOT ROOT:'lh</a> You can also combine this with other search commands; for example, if you wanted to find the verses where the Arabic root <em>ktb</em> occurs but not the translated words ‘people of the’, you could perform this search:<br>
		<br>
		<p>
			<a href="/home.php?L=ROOT:ktb AND NOT ENGLISH:%22people of the%22">ROOT:ktb AND NOT ENGLISH:"people of the"</a>
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>FOLLOWED BY</strong>
	</td>
	<td id="followedby">
		 The FOLLOWED BY command tells Qur’an Tools to look for verses where one word follows another. For example: 
		<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aktb+FOLLOWED+BY+ROOT%3Aqwl" target="_blank">ROOT:ktb FOLLOWED BY ROOT:qwl</a><br>
		<br>
		 This would show you every verse in the Qur’an where 
		<em>qwl</em>&nbsp;comes after <em>ktb</em>&nbsp;somewhere in the verse.<br>
		<br>
		 You can chain these together, so for example if you wished to find everywhere in the Qur’an where 
		<em>ktb</em> is used three times in one verse, you could use:<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aktb+FOLLOWED+BY+ROOT%3Aktb+FOLLOWED+BY+ROOT%3Aktb" target="_blank">ROOT:ktb FOLLOWED BY ROOT:ktb FOLLOWED BY ROOT:ktb</a><br>
		<br>
		 You can use FOLLOWED BY with the ROOT, LEMMA, GLOSS or EXACT commands, or by things like [noun]. For example:: 
		<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aktb+FOLLOWED+BY+LEMMA%3Aqāla+FOLLOWED+BY+%5BNOUN+PLURAL%5D" target="_blank">ROOT:ktb FOLLOWED BY LEMMA:qāla FOLLOWED BY [NOUN PLURAL]</a><br>
		<br>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 By adding the WITHIN&nbsp;keyword to a FOLLOWED BY search, you can tell Qur’an Tools to only show verses where the second search term is found within a certain number of words. For example:
			</p>
			<p>
				<a href="/verse_browser.php?S=ROOT%3Aktb+FOLLOWED+BY+WITHIN+3+WORDS+ROOT%3Aqwl" target="_blank">ROOT:ktb FOLLOWED BY WITHIN 3 WORDS ROOT:qwl</a>
			</p>
		</div>
	</td>
</tr>
<tr>
	<td id="immediately">
		<strong>IMMEDIATELY FOLLOWED BY</strong>
	</td>
	<td valign="top">
		 When using the FOLLOWED BY command (see above), Qur’an Tools doesn’t worry how far after the first word the second comes. If, however, you wish to find places where the second word 
		<em>immediately</em>&nbsp;follows the first, you would use the IMMEDIATELY FOLLOWED BY command. For example:<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aywm+IMMEDIATELY+FOLLOWED+BY+ROOT%3Aqwm">ROOT:ywm IMMEDIATELY FOLLOWED BY ROOT:qwm</a><br>
		<br>
		 &hellip; or &hellip; 
		<br>
		<br>
		<a href="/verse_browser.php?S=LEMMA%3Alā+IMMEDIATELY+FOLLOWED+BY+ROOT%3Aryb" target="_blank">LEMMA:lā IMMEDIATELY FOLLOWED BY ROOT:ryb</a><br>
		<br>
		 And, as with FOLLOWED BY, you can chain these commands together. For example: 
		<br>
		<br>
		<a href="/verse_browser.php?S=LEMMA%3Aٱللَّـهُ+FOLLOWED+IMMEDIATELY+BY+LEMMA%3Alā+FOLLOWED+IMMEDIATELY+BY+ROOT%3Aḥbb" target="_blank">LEMMA:ٱللَّـهُ FOLLOWED IMMEDIATELY BY LEMMA:lā FOLLOWED IMMEDIATELY BY ROOT:ḥbb</a><br>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>PRECEDED BY</strong>
	</td>
	<td id="precededby">
		 The PRECEDED BY command is similar to FOLLOWED BY, only looks to see if the second search term comes 
		<em>before</em>&nbsp;the first. So, for example ...<br>
		<br>
		<p>
			<a href="/verse_browser.php?S=ROOT%3Aqwl+PRECEDED+BY+ROOT%3Aktb" target="_blank">ROOT:qwl PRECEDED BY ROOT:ktb</a>
		</p>
		<p>
			 ... would show you every verse in the Qur’an where 
			<em>ktb</em>&nbsp;comes before&nbsp;<em>qwl</em>&nbsp;somewhere in the verse.
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 By adding the WITHIN&nbsp;keyword to a PRECEDED BY search, you can tell Qur’an Tools to only show verses where the second search term is found within a certain number of words. For example:
			</p>
			<p>
				<a href="/verse_browser.php?S=ROOT%3Aqwl+PRECEDED+BY+WITHIN+2+WORDS+ROOT%3Aktb" target="_blank">ROOT:qwl PRECEDED BY WITHIN 2 WORDS ROOT:ktb</a>
			</p>
		</div>
	</td>
</tr>
<tr>
	<td id="immediately_preceded">
		<strong>IMMEDIATELY PRECEDED BY</strong>
	</td>
	<td valign="top">
		 If you want to find verses where one word comes 
		<em>immediately before</em>&nbsp;another, you can use IMMEDIATELY PROCEEDED BY. For example:<br>
		<br>
		<p>
			<a href="/verse_browser.php?S=ROOT%3Aqwm+IMMEDIATELY+PRECEDED+BY+ROOT%3Aywm" target="_blank">ROOT:qwm IMMEDIATELY PRECEDED BY ROOT:ywm</a>
		</p>
		<p>
			 &hellip; or &hellip; 
			<br>
			<br>
		</p>
		<p>
			<a href="/verse_browser.php?S=ROOT%3Aryb+IMMEDIATELY+PRECEDED+BY+LEMMA%3Alā" target="_blank">ROOT:ryb IMMEDIATELY PRECEDED BY LEMMA:lā</a>
		</p>
	</td>
</tr>
<tr>
	<td id="within">
		<strong>WITHIN</strong>
	</td>
	<td valign="top">
		 The WITHIN command is very powerful and allows you to find verses where two words are within a certain number of words of each other (with Qur’an Tools unconcerned about which comes first). So, for example: 
		<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aqwl+WITHIN+2+WORDS+ROOT%3Aktb" target="_blank">ROOT:qwl WITHIN 2 WORDS ROOT:ktb</a><br>
		<br>
		 Will find (among other results) both: 
		<br>
		<br>
		 Q.2:113 ( 
		<em>...&nbsp;yatlūna <strong>l‑kitāba</strong> ka‑dhālika <strong>qalā</strong> lladhīna ..</em>.)<br>
		<br>
		 ... and ... 
		<br>
		<br>
		 Q. 3:64 ( 
		<em><strong>qul</strong> yāʾahla l<strong>‑kitābi</strong> taʿālaw ...</em>)<br>
	</td>
</tr>
<tr>
	<td id="adjacent">
		<strong>ADJACENT</strong>
	</td>
	<td valign="top">
		 The ADJACENT command finds anywhere in the Qur’an where the two search terms occur next to each other. For example: 
		<br>
		<br>
		<a href="/verse_browser.php?S=ROOT%3Aqwm+ADJACENT+ROOT%3Aywm" target="_blank">ROOT:qwm ADJACENT ROOT:ywm</a><br>
		<br>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>PARENTHESES</strong>
	</td>
	<td valign="top">
		 When combining search terms, sometimes searches can quickly become complex, occasionally making it hard to understand the results that are returned. Consider a search like this one: 
		<br>
		<br>
		<a href="/home.php?=ENGLISH:Isaac AND ENGLISH:Jacob OR ENGLISH:Ishmael">ENGLISH:Isaac AND ENGLISH:Jacob OR ENGLISH:Ishmael</a><br>
		<br>
		 Is that search asking Qur’an Tools to find all the verses where 
		<em>both</em> Isaac and Jacob are included, as well as the verses where Ishmael is mentioned. Or, alternatively, is it asking Qur’an Tools to find all the verses where Isaac is mentioned, as well as those where <em>both</em> Jacob and Ishmael are mentioned? After all, the results might be different. To make this clearer, you can use parentheses to break up a search. For example<br>
		<br>
		<p>
			<a href="/home.php?L=(ENGLISH:Isaac AND ENGLISH:Jacob) OR ENGLISH:Ishmael">(ENGLISH:Isaac AND ENGLISH:Jacob) OR ENGLISH:Ishmael</a> ... (which returns 20 verses)
		</p>
		<p>
			 Or:
		</p>
		<p>
			<a href="/home.php?L=ENGLISH:Isaac AND (ENGLISH:Jacob OR ENGLISH:Ishmael)">ENGLISH:Isaac AND (ENGLISH:Jacob OR ENGLISH:Ishmael)</a> ... (which returns 14 verses)
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		<strong>ANYTHING</strong>
	</td>
	<td valign="top">
		 Tells Qur’an Tools to also show verses that don’t match any of the search criteria. For example: 
		<br>
		<br>
		<a href="/verse_browser.php?S=%28%5BFOREIGN%5D+OR+ANYTHING%29+RANGE%3A109">([FOREIGN] OR ANYTHING) RANGE:109</a><br>
		<br>
		 It can be useful for seeing the "pattern" of how search hits are scattered across, say, a sura, even showing you verses with no search hits. 
		<br>
	</td>
</tr>
</tbody>
</table>
</p>









                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>