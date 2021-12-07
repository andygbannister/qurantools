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
    window_title("Help: Sura Rhyme Analysis");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Sura Rhyme Analysis</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	<p>
	 The 
	<strong>Sura Rhyme Analysis Tool</strong>&nbsp;(found under the “Rhyme” menu and then “Sura Rhyme Analysis with Verse Details”) allows you to explore the rhymes (sounds that end each verse) in the sura of your choice.
</p>
<p>
	<img src="images/sura-rhyme-analysis.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura to Analyse</strong>. Choose which sura of the Qur’an you wish to examine.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Analysis Mode</strong>. You can examine the verse ending patterns (rhymes) in your chosen sura in one of two ways: you can either study the <a href="#mode1">various ending patterns used in the sura</a>, or see how verses are <a href="#mode2">grouped together by the same pattern used sequentially</a>. That sounds a little complicated&mdash;so we would suggest you look at the two examples below of what these modes look like. Simply click the buttons to change mode.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Charting Button</strong>. Click the chart button and Qur’an Tools will display a <a href="verse-ending-rhyme-frequency-chart.php">chart listing each verse ending</a> (rhyme) pattern and how frequently it appears in this sura. You can also simply point your cursor at the button for a second or two to get a pop-up mini chart; like this:<br>
		<p>
			<img src="images/rhyme-frequency.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:-20px;">
<h4 style="text-align: center;" id="mode1">Verse Ending Pattern Analysis</h4>
<p>
	 The first of the two modes (see above) shows a table with the verses of your chosen sura down the side and the various verse patterns used in the sura across the columns. This lets you see at a glance the verse ending (rhyme) patterns used in the sura. Here’s an example:
</p>
<p>
	<img src="images/patterns.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<ul>
	<li>Click any verse reference to open that verse (with some context) in the verse browser (or point your cursor at it to get a pop-up mini verse browser).</li>
	<li>Click any column heading to show all the verses in your chosen sura that end with that rhyme pattern.</li>
</ul>
<hr>
<h4 style="text-align: center;" id="mode2">Sequential&nbsp;Verse Ending Pattern Analysis</h4>
<p>
	 The second of the two modes shows a table with verses grouped together whose verse ending (rhyme) matches the next verse. It allows you to see at a glance how rhyme patterns string chains of verses together. Here’s an example:
</p>
<p>
	<img src="images/patterns2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<ul>
	<li>Click any verse reference to open that verse (with some context) in the verse browser (or point your cursor at it to get a pop-up mini verse browser).</li>
	<li>Click any rhyme pattern heading to show all the verses in your chosen sura that end with that pattern.</li>
</ul>
</p>
    
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>