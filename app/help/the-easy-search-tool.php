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
    window_title("Help: The Easy Search Tool");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Easy Search Tool</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


<p>
	
	<p>
	 Built into Qur’an Tools is a powerful (and relatively easy to learn) 
	<a href="advanced-searching.php">search language</a> that allows you to search the qur’anic text for almost anything you can think of (Arabic roots, lemmata, grammatical features, or <a href="formulaic-analysis.php">formulaic features</a>.
</p>
<p>
	 However, we recognise that constructing searches can be a bit daunting at first, hence we created the 
	<strong>Easy Search</strong> tool. Access it by clicking on the 'EASY SEARCH' button on Qur’an Tools’ home page <em>before you type anything into the input box.</em>
</p>
<p>
	<img src="images/easy1.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 The 
	<strong>Easy Search Tool</strong> will then open. It is very easy to use; each feature is explained below:
</p>
<p style="text-align: center;">
	<img src="images/easy2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Root or Translation Text Picker</strong>.&nbsp;Choose whether you want to search for an Arabic root or for a word/phrase in one of the translations.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>And/Or</strong>. If you are searching for more than one thing, you can choose whether you want to look for both things, or one or the other. There is a good discussion of the difference between "and" and "or" searches on the <a href="advanced-searching.php#combo">Advanced Qur’an Searching</a> help page.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
			<strong>Matching Root or Text</strong>. If you are searching a translation, type the word (or phrase) you want to search for. If searching for an Arabic root, pick a root from the drop down list. (If you wish to search for a phrase, make sure you surround it with quote marks, as in the example below).
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Add/Remove Search Items</strong>. If you want to search for more than one item (up to a maximum of four), click the <img src="images/button_green.png" class="noBdr" style="background-color: red; display: inline; margin: 0px;" width="22" height="22"> button. To delete the last row, click <img src="images/button_red.png" class="noBdr" style="background-color: green; display: inline; margin: 0px;" width="22" height="22">.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong> Search In</strong>. You can choose to search in all suras, Meccan suras, Medinan suras or one specific sura of your choice (choose from the drop down list).
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong> Search or Cancel</strong>. Once you're ready, click the SEARCH button and Qur’an Tools will perform your search (or click CANCEL to return to the home page). After searching (or if you accidentally hit CANCEL), just click the back button in your browser to return to the easy search you constructed.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Search Language Preview</strong>. To help you learn Qur’an Tools' search language, Easy Search will show you what your search would look like if you typed it into the input box on the home page (or the search tool on the menu bar) in the normal way. (You can click on the search preview text to 'type' it into the input box on Qur’an Tools’ home page if you wish). &nbsp;
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