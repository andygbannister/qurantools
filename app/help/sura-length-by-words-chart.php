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
    window_title("Help: Sura Length by Words Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Sura Length by Words Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 The 
	<strong>Sura Length by Words</strong> chart (found under the ‘Charts’ menu and then under ‘Sura &amp; Verse Lengths’) shows you the length of each sura in terms of the number of Arabic words it contains:
</p>
<p>
	<img src="images/sura-length-words-chart.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em> (so sura 1 is drawn first and sura 114 last), or you can sort by <em>length</em> (meaning longer suras will appear first).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td valign="top">
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” and see a chart of the individual verse lengths (by words) within this particular sura.
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