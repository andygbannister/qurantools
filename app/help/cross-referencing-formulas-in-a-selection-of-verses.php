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
    window_title("Help: Cross Referencing Formulae in Verse Selection");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Cross Referencing Formulae in Verse Selection</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	<p>
	 When you have 
	<a href="looking-up-a-passage.php">looked up a list of verses</a>, or the results of a <a href="advanced-searching.php">search</a>, you can ask Qur’an Tools to list all the <a href="formulaic-analysis.php">formulae</a> found in your selection, showing you where each occurs elsewhere in the Qur’an.
</p>
<p>
	 Begin by clicking on the "Formulae" button at the top left of the verse browser, and then click "List &amp; Cross Reference All Formulae in Selection"):
</p>
<p>
	<img src="images/cross-ref.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Qur’an Tools will then open the Formulae Cross Referencing tool, which looks like this:
</p>
<p>
	<img src="images/cross-ref2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Formula Length and Type</strong>. You can choose the type of formulae that Qur’an Tools counts and shows in the report. (The <em>length</em> is the number of Arabic words in a formula; the <em>type</em> allows you to choose from any of the three formula types that Qur’an Tools understands) <a href="formulaic-analysis.php">Learn more about formula lengths and types here</a>.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Verse</strong>. This column of the report lists each verse in your selection in which one or more formulae are found. (Click on the reference to open that verse in Qur’an Tools' <a href="the-verse-browser-in-detail.php">verse browser</a>.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Formulae</strong>. Lists each formulae (of the type and length you’ve specified in #1 above) found in your selection of verses. (Click on any formulae in the list to browse the text of every verse in the Qur’an where it appears).&nbsp;Underneath each transliteration is shown a brief gloss of the formula, or the words in it. (You can turn these formulaic glosses off in <a href="preferences.php">Preferences</a> if you would prefer not to see them).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Occurrences in Same Sura</strong>. Lists every time the formulae appears in the same sura. (Click on any reference to open the verse browser and show where the formula appears in the verse; or, if there is more than one verse listed, click "View All Verses" to see all of them on one page).
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Occurrences in Other Suras</strong>. Lists every time the formulae appears in different suras.
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:0px;">
<p>
	 If you scroll to the bottom of the window, at the bottom of the list of formulae and verses, you’ll find a summary (like that shown below) showing you how many formulae Qur’an Tools found in total in this selection of verses (“Formulae Analysed”), and how many have matches either in this sura or in other suras:
</p>
<p>
	<img src="images/form-matches.png">
</p>
<hr style="margin-top:0px;">
<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

	<p>
		<strong>TIP</strong>
	</p>
	<p>
		 If you would prefer to list and cross reference the formulae in a 
		<strong>single sura</strong> rather than a selection, you can do that using the <a href="listing-and-cross-referencing-formulae-in-suras.php">Cross Reference Formulae in Sura tool</a>.
	</p>
</div>
</p>                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>