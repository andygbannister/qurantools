<?php


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
    window_title("Help: The Verse Browser (In Detail)");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Verse Browser (In Detail)</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	<p>
	 When you ask Qur’an Tools to lookup verses or perform a search, the results of what you have asked to see will appear in a Qur’an browser window. The key features are numbered and explained below:
</p>
<p>
	<img src="images/verse_browser.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Formulaic Analysis button</strong>. The Qur’an is built upon an extensive system of formulaic diction: short Arabic phrases that are used again and again in multiple contexts. Built into Qur’an Tools are a number of highly sophisticated tools to study the Qur’an’s formulae: these will be introduced in due course. For now, just know that you can click the "Formulae" button and then have Qur’an Tools colour any formulae in your selected verses in blue. For an example, have a look at the <a href="/verse_browser.php?V=2&amp;PAGE=1&amp;T=1&amp;FORMULA=3&amp;FORMULA_TYPE=ROOT&amp;RASM=">formulae three Arabic roots long in Sura 2</a>.
	</td>
</tr>

<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Sura name and title</strong>. Whenever one or more verses are shown from a sura, the sura number, Arabic name, and English name will be displayed above them. If you wish to see the entireity of a sura, just click the context button (<img src="images/context_v.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="16" height="15">) and the browser window will update to show that sura.
	</td>
</tr>
<tr id="pref4mode">
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Mode Selector</strong>. Qur’an Tools can display verses in three “modes” &mdash; Reader Mode, Interlinear Mode, and Parse Mode:<br>
		<ul>
			<li><em>Reader Mode</em>&nbsp;is the standard three column view you’re probably now familiar with. (You can reduce this to two columns if you wish, by <a href="preferences.php#pref9">turning off transliteration in preferences</a>).<br>&nbsp;</li>
			
			<li><em>Interlinear</em>&nbsp;mode shows each Arabic word with <em>some</em>&nbsp;grammatical information beneath it (root, lemma, number, person, gender etc.). <a href="interlinear-mode.php">Read more about <em>Interlinear Mode</em>&nbsp;here</a>.<br>&nbsp;</li>
			
			<li><em>Parse Mode</em> gives you a breakdown of each verse, showing <em>full</em> grammatical (parsing) information alongside.</li>

		</ul>
		<p>
			 To switch between Reader, Interlinear, and Parse modes, simply click on the mode you wish to use at the top of the screen.
		</p>
		<p>
			 You can choose which mode the verse browser chooses by default using 
			<a href="preferences.php#pref8">the Preferences screen</a>.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>What You Looked Up</strong>. At the top of the browser window, Qur’an Tools will show what you searched for (i.e. what is being displayed in the browser), whether that was a range of verses or a search command. If you wish to edit your search, just click here and you’ll be taken back to the home page with your previous search ready and waiting for you to edit.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Copy References</strong>. Click this button to copy the references of all the verses in the current view to the clipboard. (This can be useful if, for instance, you have performed a search and then want to insert the list of verses found into a paper you are writing).
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td id="bookmark">
		<strong>Bookmark button</strong>. If you wish to save this search for posterity, click "Bookmark". Once you’ve given it a name, it’ll appear in the list of bookmarks on the <a href="home-page.php">home page</a>.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td id="bookmark">
		<strong>Tag button</strong>. To apply (or remove a <a href="tags.php">tag</a>) to all the verses in the browser, click this button and use the toolbox that appears. (<a href="tags.php#multiple">Read more tagging multiple verses here</a>).
	</td>
</tr>
<tr>
	<td valign="top">
		 8
	</td>
	<td>
		<strong>Analysis button</strong>. Click this button to analyse, count, or chart the words (or letters) in this set of qur’anic verses. <a href="analysing-verses-and-search-results.php">Read more about these various functions</a>.
	</td>
</tr>
<tr>
	<td valign="top">
		 9
	</td>
	<td>
		<strong>Text</strong>. Qur’an Tools displays your selected verses in three formats: the Arabic text, a transliterated English version, and an English translation. By default, up to 50 verses can be shown at a time. (You can change this by visiting the 'Preferences' page via the menu bar option). If your search has produced more verses than can be displayed on one screen, just scroll down the page and you'll see the page navigator beneath the verses:&nbsp;<br>
		<p>
			<img src="images/vb_pages.png" style="width: 197px;" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<div class="callout" style="margin-top:0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<strong>TIP</strong>
			<p>
				 (If you wish, you can turn off the transliteration and just show the Arabic and your chosen translation. You can do this from the 
				<a href="preferences.php#pref9">Preferences page</a>).
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 10
	</td>
	<td id="TRANSLATION">
		<strong>Translation Chooser</strong>. If you wish to choose the English translation, you can choose a different one here. If you’d like to change the default translation that Qur’an Tools uses, you can do so by clicking on 'Preferences' on the Qur’an Tools menu bar.
	</td>
</tr>
<tr>
	<td valign="top">
		 11
	</td>
	<td>
		<strong>Verse Display</strong>. The left hand column always displays the reference of each verse on display. <id="contextmenu">If you hover your mouse over a verse reference, a pop-up menu shows: 
			<br>
		</p>
		<p>
			<img src="images/pop-verses.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<p>
			 Click on ...&nbsp;
		</p>
		<ul>
			<li>The context button (<img src="images/context_v.gif" class="noBdr" width="13" height="12" style="display: inline; margin-top: 0px; margin-bottom: 0px;">) to display the context of this verse (the three verses before and the three verses after it, with the verse itself emphasised in bold and highlighted). Or just point your mouse at the context button to get a quick popup glimpse at the context; like this:<br>
			<img src="images/verse_menu.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "><br>&nbsp;</li>


			<li>The clipboard button (<img src="images/copysmall.png" class="noBdr" width="13" height="12" style="display: inline; margin-top: 0px; margin-bottom: 0px;">) to copy this verse (its arabic, transliteration, translation and reference) to the clipboard. This is useful if, for example, you’d like to quickly add a verse to a paper or presentation you are working on).<br>&nbsp;</li>

			<li>The <img src="images/links.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> icon (if it’s not grayed out and unclickable) to show any <a href="intertextual-connections-between-the-qur-an-and-other-texts-traditions.php">intertextual connections</a> for this verse (texts or traditions from Late Antiquity or earlier to which this verse has some kind of connection).<br>&nbsp;</li>
			<li>The “Tags” button to add or remove <a href="tags.php">tags</a> to this verse.</li>
		</ul>
		
	</td>
</tr>
</tbody>
</table>
<hr>
<h3 id="instant-details">Instant Details Palette</h3>
<p>
	 Qur’an Tools makes it extremely easy to get more details about any word in the Qur’an &mdash; simply point your mouse at any Arabic or transliterated word (or if you’re using a smartphone or tablet, touch it) and a palette like the one below will pop up with all kinds of useful information:
</p>
<p>
	<img src="images/palette.png" style="width: 339px;" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 The Instant Details Palette is packed with useful information:
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Word</strong>. The word you have pointed at (or touched, if using Qur’an Tools on a mobile device).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Grammatical Details</strong>. Information on the grammatical features of this particular occurrence of this word.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Root and Lemma</strong>. The Arabic root and lemma (dictionary head word) lying behind this word. Click <img src="images/info.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="14" height="14"> to examine the root and see it’s definition and wider usage, or either of the chart buttons (<img src="/images/st.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="18" height="12">) to see the root or lemma’s usage across the Qur’an. Finally, you can also click the <img class="noBdr" style="display: inline; margin:0px;" src="images/list.gif" width="15" height="15"> icon to <a href="exhaustive-list-of-references-for-root-or-lemma.php">exhaustively list all the occurrences</a> of this root or lemma in the Qur’an in a brief, easy-to-scan-at-a-glance form.
	</td>
</tr>
<tr id="pref4">
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Loanword</strong>. If a word appears in Arthur Jeffery’s <a href="the-foreign-vocabulary-of-the-qur-an.php"><em>The Foreign Vocabulary of the Qur’an</em></a>, the classic guide to the Qur’an's loanwords, it will be tagged here with a <img src="images/loanword.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> label. Click on the <img src="images/loanword.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> label to open the word’s definition in&nbsp;<em>The Foreign Vocabulary of the Qur’an.</em><br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Quick Search Links</strong>. Click on any link here to&nbsp;easily search for this Arabic root, lemma, gloss, or even this exact inflection of the word elsewhere in the Qur’an. One-click searching is a powerful feature built right into Qur’an Tools: if you see a word that interests you whilst browsing the text, it is literally a case of just pointing-and-clicking to see every other qur’anic occurrence.<br>
	</td>
</tr>
</tbody>
</table>
<p>
	 To clear the Instant Details Palette, simply move your mouse off the word, click the X icon in the top right or the palette or, if you’re using a smart-phone or tablet, touch somewhere else (e.g. a piece of white space) on the screen.
</p>
</p>
               
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>