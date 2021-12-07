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
    window_title("Help: Formulae in Common Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulae in Common Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Formulae in Common</strong>&nbsp;chart (found under the ‘Charts’ menu and then under ‘Formulae’) shows how many different types of formulae a sura has in common with the other suras in the Qur’an. It's a powerful way to see at a glimpse the system of formulaic diction that underpins the Qur’an. Tracing the formulaic connections between suras is also a way to see which suras have more in common (such as theological ideas and terminology) with others.
</p>
<p>
	<img src="images/incommon.png" border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura to Analyse</strong>. Pick the sura you would like to study using the list of suras in the pick list.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Formula Length</strong>. Choose whether to look for formula in common with other suras that are either 3, 4, or 5 words long. (Or choose ‘Any’ to show all formulaic commonalities, of any length).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
			<strong>Formula Type</strong>. Choose from the pick list which type of formula to look at (or choose ‘All Formulae Types’).

		<div class="callout" style="margin-top:0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<strong>TIP</strong>
			<br>
			<p>
				<a href="formulaic-analysis.php">Read more about Qur’an Tools' formula types and lengths and what they mean</a>.
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Sort By</strong>. Either sort the columns in the chart by sura number, or in descending order (allowing you to quickly identify the suras with the most formulae in common with the one you are studying).
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>

			<strong>Chart Columns</strong>. Each column shows the <em>number of different formulae</em>&nbsp;each sura has in common with the one you are examining. Each different formula is counted once (e.g. if a sura shares the formulae <em>āmana + mā + anzala</em> and <em>ʿala + kull + shay</em> in common, the first of which occurs 4 times and the second 3 times, the column would show a value of 2, not 7). Click on any column in the chart to drill down and see <a href="formulaic-commonalities-between-suras.php">verse level detail and a list of formulae</a> in common for this sura.

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