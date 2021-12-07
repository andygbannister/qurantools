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
    window_title("Help: List All Nouns");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Nouns</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 If you select&nbsp;
	<strong>List Nouns</strong>&nbsp;from the&nbsp;<strong>Browse</strong>&nbsp;menu (where it can be found under&nbsp;<strong>Word&nbsp;Lists</strong>), Qur’an Tools will display a list of the 1,352 different verbs that occur in the qur’anic text. The list looks like this:
</p>
<p>
	<img src="images/list-nouns1.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign=top>
		 1
	</td>
	<td>
		<strong>Group Data by Root or by Sura</strong>. By default, every root that appears as a noun is listed in the table. If you prefer, you can choose to see noun data on a sura by sura basis, like this:<br>
		<p>
			<img src="images/list-nouns2.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
		</p>
	</td>
</tr>
<tr>
	<td valign=top>
		 2
	</td>
	<td>
		<strong>Root</strong>. The root form of the Arabic word. For each word in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by root, just use the arrows at the top of the column &mdash; indeed, you can sort the table by any column you like this way).
	</td>
</tr>
<tr>
	<td valign=top>
		 3
	</td>
	<td>
		<strong>Appearances as a Noun</strong>. This column shows the number of times that this Arabic root appears as a noun in the Qur’an. Click on the number and Qur’an Tools will run a search and open every occurrence in the verse browser.
	</td>
</tr>
<tr>
	<td valign=top>
		 4
	</td>
	<td>
		<strong>Case</strong>. The number of times the verb occurs in the Qur’an in the nominative, accusative, and genitive case. (Click on any number in any of these columns to run a search and see the relevant verses in the verse browser).
	</td>
</tr>
<tr>
	<td valign=top>
		 5
	</td>
	<td>
		<strong>Gender</strong>. The number of times the noun occurs in the Qur’an in masculine or feminine form.
	</td>
</tr>
<tr>
	<td valign=top>
		 6
	</td>
	<td>
		<strong>Number</strong>. The number of times the noun occurs in the Qur’an in singular, dual or plural form.
	</td>
</tr>
<tr>
	<td valign=top>
		 7
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/info.gif" width="15" height="15"> icon to view additional information about the root underlying this noun or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of this lemma’s occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart).<br>
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:0px;">
<p>
	 At the bottom of the noun list, you’ll find a page navigator, as displaying the entire list of verbs on one screen slows some web browsers to a crawl.&nbsp;
</p>
<p>
	<img src="images/pagenav.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
</p>
</p>
                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>