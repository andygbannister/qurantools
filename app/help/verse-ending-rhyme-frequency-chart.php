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
    window_title("Help: Verse Ending (Rhyme) Frequency Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Verse Ending (Rhyme) Frequency Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 The 
	<strong>Verse Ending (Rhyme) Frequency Chart</strong>&nbsp;(found under the “Charts” menu and then “Rhymes”) shows, for any sura of the Qur’an you choose, the different verse ending (rhyme) patterns in that sura and how frequently they are used:
</p>
<p>
	<img src="images/rhyme-v-ending.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura to Show</strong>. Choose the sura you wish to chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart alphabetically by <em>rhyme pattern</em>&nbsp;or you can sort your chart by <em>occurrences</em> (meaning patterns that occur more often will appear first in the chart).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” and see all the verses in this sura that end with the rhyme pattern whose column you have clicked on.
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