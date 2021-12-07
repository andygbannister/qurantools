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
    window_title("Help: The Dictionary Tool");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Dictionary Tool</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


<p>
	
	<p>
	 Built into Qur’an Tools is a powerful 
	<strong>Dictionary Tool&nbsp;</strong>that helps you easily explore the definitions of the Arabic words used throughout the Qur’an. You can find the dictionary under the "Browse" menu on the Qur’an Tools menu bar. The dictionary tool looks like this:
</p>
<p>
	<img src="images/dictionary.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table class="legend_table" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top" align="center">
		 1
	</td>
	<td>
		<strong>Arabic Alphabet Navigator</strong>. To jump to a particular letter in the dictionary, just click its letter.
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 2
	</td>
	<td>
		<strong>Search</strong>. You can search for a word in a definition (e.g. "food"), or an Arabic root (e.g. كتب) or its transliteration (e.g. <em>ktb</em>). Qur’an Tools will list every definition that matches your search criteria (and highlight the match). After searching, you can return to showing all definitions by clicking "Show All Definitions".
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 3
	</td>
	<td>
		<strong>Root/Lemmata List</strong>. Every root (and some lemmata) in the dictionary is shown with its Arabic form and its transliterated form. Click on any root/lemma in either column to perform a search in the Qur’an for that word.
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 4
	</td>
	<td>
		<strong>Definition</strong>. The definition displayed is drawn from several Arabic dictionaries (including&nbsp;<em>al-Mufradāt fī gharīb al-Qurʾān, Lisān al-ʿarab, Tāj al-ʿarūs min jawāhir al-qāmūs</em> and <em>An Arabic-English Lexicon</em> by E.W Lane). For more information, see the <a href="/about.php" class="nodec">About</a> page.<br>
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 5
	</td>
	<td>
		<strong>Other Lexica</strong>. Every Arabic root is linked to its definition in John Penrice’s <em>A Dictionary and Glossary of the Korân</em> and Edward Lane's <em>An Arabic-English Lexicon</em>. Any words that also appear in Arthur Jeffery’s <a href="the-foreign-vocabulary-of-the-qur-an.php" target="_blank"><em>The Foreign Vocabulary of the Qur’an</em></a> can also have their page in that resource viewed.<br>
		<br>
		 To look up a word in one of those dictionaries, just click the appropriate icon. If you are working on a Mac or PC, the definition will open in a pop up window: 
		<img src="images/lane.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		<p>
			 The page displayed should be the one where the definition for your word begins. You can use the arrows or the drop down menu to move between pages, or click the 
			<img class="noBdr" style="display: inline; margin: 0px;" src="images/expand.png" width="12" height="12"> icon to open the PDF in its own page.
		</p>
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 6
	</td>
	<td>
		<strong>Occurrences</strong>. The number of times this root occurs in the Qur’an is displayed. Just click on the number to perform a search for that word and display the results in the <a href="user_guide_intro_browser.php" class="nodec">verse browser</a>.
	</td>
</tr>
<tr>
	<td valign="top" align="center">
		 7
	</td>
	<td>
		<strong>Tools</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/info.gif" width="15" height="15"> icon to <a href="examine-root.php">examine the root in detail</a> and see a wealth of additional information about this root and its usage in the Qur’an; click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of its occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart); or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/network.gif" valign="middle" width="15" height="15"> to open the <a href="word-association-tool.php">Word Association Tool&nbsp;</a>for this root (and see which other qur’anic roots occur with it).
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