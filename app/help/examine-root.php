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
    window_title("Help: Examine Root");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Examine Root</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


<p>
	
	<p>
	 The 
	<strong>Examine Root</strong>&nbsp;tool allows you to see lots of information about any of the Arabic roots used in the Qur’an &mdash; everything from definitions, to usage, to how it is used in <a href="formulaic-analysis.php">formulaic language</a>.
</p>
<p>
	 To examine a root, simply click the&nbsp; 
	<img src="images/info.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="14" height="14">&nbsp;icon that can be found next to each root in the <a href="the-dictionary-tool.php">dictionary tool</a>, or in the <a href="the-verse-browser-in-detail.php#instant-details">Instant Details palette</a> that appears when you point your mouse at a word in the verse browser.
</p>
<p>
	 The 
	<strong>Examine Root</strong>&nbsp;screen looks like this:
</p>
<p>
	<img src="images/examine-root.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Root Being Examined</strong>. The root you are currently examining, shown in both Arabic and transliterated form. <br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Definition</strong>. The definition displayed is drawn from several Arabic dictionaries (<em>al-Mufradāt fī gharīb al-Qurʾān, Lisān al-ʿarab, Tāj al-ʿarūs min jawāhir al-qāmūs</em> and <em>An Arabic-English Lexicon</em> by E.W Lane). For more information, see the <a href="/about.php" class="nodec">About</a> page.
	</td>
</tr>
<tr>
	<td valign="top" align="center" valign="top">
		 3
	</td>
	<td>
		<strong>Other Lexica</strong>. Every Arabic root is linked to its definition in John Penrice’s <em>A Dictionary and Glossary of the Korân</em> and we are busy completing the links for Edward Lane's <em>An Arabic-English Lexicon</em>.&nbsp;Any words that also appear in Arthur Jeffery’s <a href="the-foreign-vocabulary-of-the-qur-an.php"><em>The Foreign Vocabulary of the Qur’an</em></a> can also have their page in that resource viewed.<br>
		<br>
		To look up a word in one of those dictionaries, just click the appropriate icon. If you are working on a Mac or PC, the definition will open in a pop up window: 
		<br>
		<p>
			<img src="images/lane.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<p>
			 The page displayed should be the one where the definition for your word begins. You can use the arrows or the drop down menu to move between pages, or click the 
			<img class="noBdr" style="display: inline; margin: 0px;" src="images/expand.png" width="12" height="12"> icon to open the PDF in its own page.<br>
		</p>
		<div class="callout" style="margin-top:-10px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<p>
				<strong>TIP<br>
				</strong>If you are using Qur’an Tools on a tablet or phone, some of these devices have problems displaying PDFs in pop up windows, so the resource will open in a new browser tab instead.
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Total Qur’anic Occurrences</strong>. The number of times this root appears in the Qur’an. Click the number to open the verse browser and search for every instance, or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of this root’s occurrences.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Find Word Associations</strong>. Click this button to open the <a href="the-word-association-tool.php">Word Associations tool</a> and see at a glance which other Arabic words occur most frequently along with this root.
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Lemmata Based on Root. </strong>Lists all the lemmata (dictionary “head words”) based on this root in the Qur’an. Click on the number to see a detailed list of them, or click on any of the lemmata to search for that lemma in the Qur’an.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong> Breakdown of Appearances</strong>. Details any time the root you are examining appears as a noun or a verb, along with details of each appearance (number, person, gender, case, Arabic form etc.) Click on any number to search for qur’anic occurrences of that particular category (e.g. in the example above, clicking on the “48” after “Nominative” would show you the 48 times that the root&nbsp;ابو appears in the Qur’an as a noun in the nominative case).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 8
	</td>
	<td>
		<strong> Formulaic Appearances</strong>. Details how many times the root appears as part of a formula &mdash; formulae are short, repeated phrases that form a building block of qur’anic diction. Click on any number in this section to see a list of that formula type and where the root you are examining appears in it. (For more information on formulaic analysis of the Qur’an and the terminology used by Qur’an Tools, <a href="formulaic-analysis.php">read this article</a>).
	</td>
</tr>
<tr>
	<td valign="top">
		 9
	</td>
	<td>
		<strong> Verse List.</strong>&nbsp;Lists every verse where the root you are examining appears. Click any verse to open it in the browser.
	</td>
</tr>
<tr>
	<td valign="top">
		 10
	</td>
	<td>
		<strong> Examine Another Root</strong>. If you would like to examine another root, you can easily do so simply by clicking this button and then picking a new root from either of the drop down menus of Arabic or transliterated roots that Qur’an Tools offers to you.<br>
		<p>
			<img src="images/another-root.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
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