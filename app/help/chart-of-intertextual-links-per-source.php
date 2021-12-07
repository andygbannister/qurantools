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
    window_title("Help: Chart of Intertextual Links per Source");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>
	                Chart of Intertextual Links per Source</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Chart of Intertextual Links per Source</strong>&nbsp;(found under the ‘Charts’ menu and then under ‘Intertextuality’) shows you, for each earlier tradition/text/document/source logged in Qur’an Tools, how many <a href="intertextual-connections-between-the-qur-an-and-other-texts-traditions.php">intertextual connections</a> it has to the Qur’an.
</p>
<p>
	<img src="images/chart-per-source.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		1
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart alphabetically by the <em>source name</em>, or you can sort by the <em>number</em> of intertextual links (so sources with the greatest number of intertextual links will appear first).
	</td>
</tr>
<tr>
	<td valign="top">
		2
	</td>
	<td>
		<strong>Browse All Connections Button</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/table.png"> icon to <a href="browse-intertextual-connections.php">browse all the intertextual connections in the Qur’an</a>, grouping them by the source/text/tradition they are linked to.
	</td>
</tr>
<tr>
	<td valign="top">
		3
	</td>
	<td>
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click any column to open all the verses in the Qur’an that appear to exhibit intertextual connections to the source whose column you have clicked on.
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