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
    window_title("Help: Grammatical Features by Sura Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Grammatical Features by Sura Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 The 
	<strong>Grammatical Features by Sura Chart</strong> (found in the 'Charts' menu under the ‘Words &amp; Grammar’ submenu) allows you to quickly see how the different suras of the Qur’an use various aspects of Arabic grammar. The chart looks like the example below and the features of this screen are described underneath:
</p>
<p>
	<img src="images/gramm-features.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Grammatical Feature to Chart</strong>. You can plot the Qur’an’s use of the following grammatical features: <br>
		<br>
		<ul>
			<li>Gender (masculine or feminine)</li>
			<li>Person (first, second, or third)</li>
			<li>Number (singular, dual, or plural)</li>
			<li>Form (form I through to form XII)</li>
			<li>Case (nominative, accusative, or genitive)</li>
			<li>Mood (indicative, jussive, or subjunctive)</li>
		</ul>
		 To change the feature you are looking at, simply pick a new grammatical feature and then a new value you want to chart from the two pick lists.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan, or just Medinan suras on the chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em> (so sura 1 is drawn first and sura 114 is drawn last), or you can sort your chart by <em>percentage value</em> (meaning suras that have a higher concentration of the feature you are charting will appear first).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Auto-Scale Y Axis</strong>. By default, Qur’an Tools always draws the Y axis on this chart (the percentage axis) from 0% to 100%. However, depending what grammatical feature you are looking at, sometimes all the values are very small. By ticking this option, Qur’an Tools will intelligently adjust the Y axis so your data can be seen more clearly.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to run a search for this particular grammatical feature in the given sura. For example, in the chart picture above, if one clicked on the column for sura 2, Qur’an Tools would show you the 3,026 Arabic roots in sura 2 that are masculine.<br>
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