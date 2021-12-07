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
    window_title("Help: Browse Intertextual Connections");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                
                <h2 class='page-title-text'>
	                
	                <img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>
	                Browse Intertextual Connections</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	 The 
	<strong>Browse Intertextual Connections</strong> screen, accessible from the "Browse" menu and then under "Intertextuality" lists all the texts/tradition/document to which the Qur’an has some kind of <a href="intertextual-connections-between-the-qur-an-and-other-texts-traditions.php">intertextual connection</a>.
</p>
<p>
	<img src="images/browse_intertexts.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding=4 cellspacing=3>
<tbody>
<tr>
	<td valign=1>
		 1
	</td>
	<td valign=top>
		<strong>Chart All Connections Icon.</strong>&nbsp;Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to display a <a href="chart-of-verses-with-intertextual-connections.php">chart showing the&nbsp;total number of intertextual connections for each sura</a> in the Qur’an.
	</td>
</tr>
<tr>
	<td valign=top>
		 2
	</td>
	<td valign=1>
		<strong>Source</strong>. Each text/tradition/document to which the Qur’an has some kind of intertextual connection is listed here.&nbsp;Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to see a <a href="chart-of-intertextual-links-per-source.php">chart showing how many links to each source</a> can be found in the Qur’an.<br>
	</td>
</tr>
<tr>
	<td valign=top>
		 3
	</td>
	<td valign=1>
		<strong>Source Name and Date</strong>. Each text/tradition/document to which the Qur’an has some kind of intertextual connection is named, along with its date or date range. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/openweb.png"">&nbsp;icon to open the text/tradition in a new tab or browser window for easy reading/study.&nbsp;<br>
	</td>
</tr>
<tr>
	<td valign=1>
		 4
	</td>
	<td valign=1>
		<strong>Total Links</strong>. A count of how many intertextual connections to this particular tradition/text appear in the Qur’an.&nbsp;<br>
	</td>
</tr>
<tr>
	<td valign=1>
		 5
	</td>
	<td valign=1>
		<strong>Reference</strong>. The specific passage (verse, chapter, folio, etc.) within the source to which the intertextual connection links. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/scroll.png">&nbsp;icon to open the passage in a pop up window for easy consultation.<br>
	</td>
</tr>
<tr>
	<td valign=1>
		 6
	</td>
	<td>
		<strong>Links to this Passage</strong>. A count of how many times this particular passage within the source has intertextual connections to the Qur’an.<br>
	</td>
</tr>
<tr>
	<td valign=1>
		 7
	</td>
	<td valign=1>
		<strong>Qur’an References</strong>. For each passage within a source, the Qur’an verses which have intertextual links to it are listed. Click any verse reference to open it in the verse browser (or if there is more than one verse listed, click "View All" to view all of them).<br>
	</td>
</tr>
</tbody>
</table>








                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>