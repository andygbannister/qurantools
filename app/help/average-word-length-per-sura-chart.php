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
    window_title("Help: Average Word Length per Sura Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Help: Average Word Length per Sura Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>The <b>Average Word Length per Sura</b> chart (found under the ‘Charts’ menu and then under ‘Sura, Verse and Word Lengths’) shows you the average length of the words in each sura in the Qur’an. This is one of many indicators of qur’anic style and thus this chart makes it easy to see the fluctuations in average word length across the Qur’an:</p>

<img src="images/average-word-length-chart.png" alt="average-word-length-chart" width="75%" height="75%" border=1>

<ol>
<li><b>Suras to Show</b>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart.<br>&nbsp;</li>

<li><b>Sort By</b>. You can either sort your chart by <i>sura</i> (so sura 1 is drawn first and sura 114 last), or you can sort your chart by the <i>number of formula</i> (meaning suras with more formulae in them will appear first).<br>&nbsp;</li>

<li><b>Chart Columns</b>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” and open that particular sura in the verse browser.</li>
</ol>

</p>

               
                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>