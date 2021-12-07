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
    window_title("Help: List All Verbs");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Verbs</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 If you select&nbsp;
	<strong>List Verbs</strong> from the&nbsp;<strong>Browse</strong>&nbsp;menu (where it can be found under&nbsp;<strong>Word&nbsp;Lists</strong>), Qur’an Tools will display a list of the 943 different verbs that occur in the qur’anic text. The list looks like this:
</p>
<p>
	<img src="images/list-verbs.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Root</strong>. The root form of the Arabic word. For each word in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by root, just use the arrows at the top of the column &mdash; indeed, you can sort the table by any column you like this way).
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Appearances as a Verb</strong>. This column shows the number of times that this Arabic root appears as a verb in the Qur’an. Click on the number and Qur’an Tools will run a search and open every occurrence in the verse browser.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Person</strong>. The number of times the verb occurs in the Qur’an in the first, second, and third person. (Click on any number in any of these columns to run a search and see the relevant verses in the verse browser).
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Number</strong>. The number of times the verb occurs in the Qur’an in singular, dual or plural form.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Gender</strong>. The number of times the verb occurs in the Qur’an in masculine or feminine form.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Form</strong>. The number of times the verb occurs in the Qur’an in each of the Arabic verb forms (form I through form XII). Again, you may click on any number in any of these columns to run a search and see the relevant verses in the verse browser.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/info.gif" width="15" height="15"> icon to view additional information about this root or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of its occurrences as a verb in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart).
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:0px;">
<p>
	 At the bottom of the root list, you’ll find a page navigator, as displaying the entire list of verbs on one screen slows some web browsers to a crawl.
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