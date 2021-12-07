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
    window_title("Help: List All Proper Nouns");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Proper Nouns</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<hr id="horizontalrule">
<p>
	If you select <strong>List Proper Nouns</strong> from the <strong>Browse</strong> menu (where it can be found under <strong>Word Lists</strong>), Qur’an Tools will display a list of all the proper nouns that occur in the qur’anic text.
</p>
<p>
	<img src="images/list-proper-nouns.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Lemma</strong>.&nbsp;For each lemma in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by lemma, just use the arrows at the top of the column &mdash; indeed, you can sort the table by any column you like this way).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Translation</strong>. An English translation of the proper noun (where the name is known in multiple forms, such as Yusuf/Joseph, both are listed.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Occurrences</strong>. A count of how many times this proper noun appears in the Qur’an.&nbsp;<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14">&nbsp;icon to display a chart of this proper noun’s occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart).
	</td>
</tr>
</tbody>
</table>
<br>

<div class="callout" style="margin-top:-10px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
	<strong>TIP</strong>
		<br>
		 Simply click on any item in a row in the table (either the Arabic, transliteration, translation or occurrence count) and Qur’an Tools will open up the verse browser and show you every verse where that proper noun appears.
</div>
</p>









                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>