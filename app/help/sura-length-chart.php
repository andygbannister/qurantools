<?php

// GNU GPL License Page.

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
    window_title("Help: Sura Length Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo">
                <h2 class='page-title-text'>Sura Length Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>At the top of the 'Charts' menu, you will find the 'Sura & Verse Lengths' sub menu:</p>

<img src="images/sura-lengths-menu.png" alt="sura-lengths-menu" width="490" height="191" border=1>

<p>This offers you three different ways to chart the lengths of each sura of the Qur’an</p>

<ul>
<li>By verses (the chart will show the number of verses in each sura)<br>&nbsp;</li>
<li>By words (the chart will show the number of Arabic words in each sura)<br>&nbsp;</li>
<li>By mean verse length (the chart will show the average (arithmetic mean) of each sura)</li>
</ul>

<p>The chart drawn will look something like this:</p>

<img src="images/mean-sura-length-chart.png" alt="mean-sura-length-chart" width="700" height="414" border=1>

<ol>
<li><b>Suras to Show</b>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart.<br>&nbsp;</li>
<li><b>Sort By</b>. You can either sort your chart by sura (so sura 1 is drawn first and sura 114 last), or you can sort your chart by length (meaning longer suras will appear first).<br>&nbsp;</li>
<li><b>Chart Columns</b>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” and see a chart of the individual verse lengths (by words) within this particular sura.</li>
</ol>

</p>
     
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>