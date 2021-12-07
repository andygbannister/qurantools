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
    window_title("Help: Number of Different Verse Endings (Rhymes) per Sura Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Number of Different Verse Endings (Rhymes) per Sura Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


<p>
	
	<p>
	 The 
	<strong>Number of Different Verse Endings (Rhymes) per Sura</strong><strong>&nbsp;Chart</strong>&nbsp;(found under the “Charts” menu and then “Rhymes”) shows how many different verse endings (rhyme) patterns each sura uses:
</p>
<p>
	<img src="images/rhyme-chart.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em>&nbsp;or by the&nbsp;<em>number of rhyme patterns</em> (meaning that suras which use a greater number of different rhyme patterns will appear first in the chart).
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” and see all the verses and their rhyme patterns laid out.
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