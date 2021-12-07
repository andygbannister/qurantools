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
    window_title("Help: Formulaic Density by Sura Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Density by Sura Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 The 
	<strong>Formulaic Density by Sura</strong> chart (found under the ‘Charts’ menu and then under ‘Formulae’) shows you what percentage of the words in each sura form part of a <a href="formulaic-analysis.php">formulaic phrase</a> &mdash; or what in Oral Traditional studies is termed the ‘formulaic density’ of the sura.
</p>
<p>
	<img src="images/f-per-sura.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Formula Type and Length to Show</strong>. This allows you to customise the type of formulae that Qur’an Tools counts and shows in the chart below. (The <a href="formulaic-analysis.php#length"><em>length</em></a> is the number of Arabic words in a formula; the <a href="formulaic-analysis.php#types"><em>type</em></a> allows you to choose from any of the three formula types that Qur’an Tools understands, or to choose ‘All Formula Types’ which means the chart will count what percentage of the words in each sura are part of <em>any</em> type of formula.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em> (so sura 1 is drawn first and sura 114 last), or you can sort your chart by <em>formulaic density</em> (meaning suras with a higher formulaic density will appear first).
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” &mdash; Qur’an Tools will open the <a href="the-verse-browser-in-detail.php">verse browser</a> and show the sura you have clicked on, with every formula of the type you are charting helpfully coloured blue in the text.
	</td>
</tr>
</tbody>
</table>
<div class="callout" style="margin-top:0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
	<p>
		<strong>TIP</strong>
	</p>
	<p>
		 If you would like to see the formulaic density of the 
		<em>entire</em> Qur’an (rather than of individual suras, as the chart above shows), simply click on ‘Formulae’ on the Qur’an Tools main menu bar, then choose ‘Formulaic Density Summary Table’.
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