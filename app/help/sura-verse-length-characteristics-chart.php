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
    window_title("Help: Sura Verse Length Characteristics Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Sura Verse Length Characteristics Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	<p>
	 The 
	<strong>Sura Verse Length Characteristics </strong>chart (found under the ‘Charts’ menu and then under ‘Sura, Verse and Word Lengths’) shows you the percentage of verses in a sura that are a particular length. This is one of many indicators of qur’anic style and thus this chart makes it easy to explore the different ‘shape’ or ‘profile’ of the various suras of the Qur’an.
</p>
<p>
	<img src="images/characteristics-chart.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4"cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura to Show</strong>. To show a Verse Length Characteristics chart for a different sura, just pick the sura number from the pick list.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>% Of Verses (Y-Axis)</strong>. The vertical (y-axis) of the chart shows the percentage of verses in this sura that are a particular verse length.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Verse Lengths (X-Axis)</strong>. The horizontal (x-axis) of the chart shows the various verse lengths in the sura.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 4
	</td>
	<td>
		<strong>Chart Data</strong>. Click on any data point (the small, hollow circles) to show the verses of this length in this sura in the verse browser.
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