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
    window_title("Help: Chart of Verses with Intertextual Connections");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'>
	                <img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo">
	                <br>
	                Chart of Verses with Intertextual Connections</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 The 
	<strong>Verses with Intertextual Connections</strong>&nbsp;chart (found under the ‘Charts’ menu and then under ‘Intertextuality’) shows you, for each sura of the Qur’an, how many <a href="intertextual-connections-between-the-qur-an-and-other-texts-traditions.php">intertextual connections it has to earlier texts, traditions, documents, or sources</a>.
</p>
<p>
	<img src="images/links_per_sura.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
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
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em> (so sura 1 is drawn first and sura 114 last), or you can sort by the <em>number</em> of intertextual connections (so suras with the greatest number of intertextual connections will appear first).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Browse All Connections Button</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/table.png"> icon to <a href="browse-intertextual-connections.php">browse all the intertextual connections in the Qur’an</a>, grouping them by the source/text/tradition they are linked to.
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click any column to open the verses from the sura whose column you have clicked that have intertextual connections in the verse browser.
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