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
    window_title("Help: Formulaic Density and Usage Statistics per Sura");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Density and Usage Statistics per Sura</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Formulaic Density and Usage Statistics per Sura</strong>&nbsp;report, found under the “Formulae” menu lists each of the 114 suras in the Qur’an along with information related to the <a href="formulaic-analysis.php">formulaic phrases</a> found in that sura. It looks like this:
</p>
<p>
	<img src="images/formulae-per-sura.png" border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
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
		<strong>Sura Number and Provenance</strong>.&nbsp;Each sura has its number and provenance (Meccan or Medinan, according to the commonly used Nöldeke-Schwally-Robinson system) listed. Click on the sura number to see <a href="formulaic-density-by-verse.php">verse by verse detail of the formulae in the sura</a>.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Roots</strong>. The number of Arabic roots in each sura. (You can sort by this, and indeed <em>any</em>&nbsp;column, by using the&nbsp;<img src="images/arrows.png" class="noBdr" style="display: inline; margin: 0px;">&nbsp;buttons&nbsp;at the top of each column).
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Roots Part of a Formulae</strong>. Shows the number of Arabic roots in this sura that are part of a formulaic phrase. (This is used, along with the total number of roots (see #3 above) to calculate the formulaic density of this sura, which is defined as [Number of Roots Part of a Formulae] / [Total Number of Roots].<br>
		<br>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<br>
			 Click on any number in this column to see a list of all the formulae in the sura.
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Number of Formulae Used</strong>. Reports the total number of formulae used in this sura, including duplicates (so, for example, if a sura used just one formulae 10 times, this column would show "10". You can see a chart of this column by clicking the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> icon at the top of the column.
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Diversity of Formulae Used</strong>. Reports the total number of different formulae used in this sura, including duplicates (so, for example, if a sura used just one formulae 10 times, this column would show "1". You can see a <a href="formulaic-diversity-per-sura-chart.php">chart</a> of this column by clicking the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon at the top of the column.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Formulae Unique to Sura</strong>. Counts how many formulae only occur in this sura. You can see a chart of this column by clicking the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon at the top of the column.
	</td>
</tr>
<tr>
	<td valign="top">
		 8
	</td>
	<td>
		<strong>Formulaic Density</strong>. The formulaic density of this sura (the percentage of words in it that are part of a formulae). You can see a <a href="formulaic-density-by-sura-chart.php">chart</a> of this information by clicking the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> icon at the top of the column (or the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> icon on an individual row, to see a chart of the formulaic densities of each verse in that sura).
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