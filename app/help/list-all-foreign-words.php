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
    window_title("Help: List All Loanwords");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Loanwords</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 If you select 
	<strong>List Loanwords</strong>&nbsp;from the <strong>Browse</strong> menu (where it can be found under <strong>Word Lists</strong>), Qur’an Tools will display a list of all the loanwords that occur in the qur’anic text, as classified by one of the standard reference guides (Arthur Jeffery’s <a href="the-foreign-vocabulary-of-the-qur-an.php"><em>The Foreign Vocabulary of the Qur'an</em></a>).
</p>
<p>
	<img src="images/list-loanwords.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Chart Icon</strong>.&nbsp;Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to see a <a href="foreign-words-vocabulary-per-sura-chart.php.php">chart</a> showing how frequently loanwords are used in each of the Qur’an’s 114 suras.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Lemma</strong>.&nbsp;For each loanword in the list, Qur’an Tools shows you both the Arabic and transliterated version of its lemma. (If you’d like to sort the table by lemma, just use the arrows at the top of the columns &mdash; indeed, you can sort the table by any column you like this way).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Root</strong>. For those words with an Arabic root as well as a lemma (most of them), the table lists that root.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Gloss</strong>. A brief English translation of the word.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Occurrences</strong>. Shows how often each word occurs in the Qur’an.
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img src="images/jeffery.png" class="noBdr" style="display: inline; margin-top: -4px; margin-bottom: 0px;" width="14" height="16"> icon to see the definition/discussion of this word in <em>The Foreign Vocabulary of the Qur’an</em>. Click the <img class="noBdr" style="display: inline; margin:0px;" src="images/list.gif" width="15" height="15"> icon to <a href="exhaustive-list-of-references-for-root-or-lemma.php">exhaustively list all the occurrences</a> in the Qur’an of this lemma. Or click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="Click" the="" <img="" height="14">&nbsp;icon to display a chart of this proper noun’s occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart).
	</td>
</tr>
</tbody>
</table>
<div class="callout" style="margin-top:0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
	<strong>TIP</strong>
	<p>
		<br>
		 Simply click on any item in a row in the table (either the Arabic, transliteration, gloss or occurrence count) and Qur’an Tools will open up the verse browser and show you every verse where that word appears.
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