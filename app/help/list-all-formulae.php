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
    window_title("Help: List All Formulae");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Formulae</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 If you choose&nbsp; 
	<strong>List All Formulae</strong>&nbsp;report, found under the “Formulae” menu, Qur’an Tools will display a list of all the formulaic phrases found in the Arabic text of the Qur’an. (<a href="formulaic-analysis.php">Learn more about formulaic analysis, the terminology used, and some of its implications for the study of the Qur’an here</a>). The List All Formulae report looks like this:
</p>
<p>
	<img src="images/list-formulae.png" border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Formula Length and Type</strong>. You can choose&nbsp;the type of formulae that Qur’an Tools counts and shows in the report. (The <em>length</em> is the number of Arabic words in a formula; the <em>type</em> allows you to choose from any of the three formula types that Qur’an Tools understands). <a href="formulaic-analysis.php">Learn more about formula lengths and types here</a>.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Limit to Sura</strong>. If you wish, you can choose to limit the list of formulae to those found in just one particular sura; to do so, just pick the sura number you are interested in from this pick list.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Formulae Count</strong>. Helpfully lets you see the row number for each formula in the table.
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Transliterated Form of the Formula</strong>. Shows the formula transliterated into Roman (Latin) letters &mdash; the "+" means followed by, so in the first row above, the formula is the root <em>'mn</em>&nbsp;followed by the root <em>‘ml</em>&nbsp;followed by the root&nbsp;<em>ṣlḥ</em>. Underneath each transliteration is shown a brief gloss of the formula, or the words in it. (You can turn these formulaic glosses off in <a href="preferences.php">Preferences</a> if you would prefer not to see them).
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Arabic Form of the Formula</strong>. Shows the formula in its Arabic form.
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Occurrences</strong>. These three columns show how often each formula occurs in the whole Qur’an (whether in all suras, Meccan suras, or Medinan suras). You can sort the table by any of these columns, should you wish, simply by using the using the&nbsp;<img src="images/arrows.png" class="noBdr" style="display: inline; margin: 0px;">&nbsp;buttons at the top of each of them.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Chart</strong>. See a chart of how a formula is distributed across the Qur’an by clicking its associated&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon. (Or just point your mouse at the <img src="images/st.gif" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon for a few seconds to see a “mini chart” giving you a quick look at the information).
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