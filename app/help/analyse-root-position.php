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
    window_title("Help: Analyse Root Position in Verse");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Analyse Root Position in Verse</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>The <b>Analyse Root Position</b> tool is accessed by clicking the <img src="images/gridbutton.png" alt="gridbutton" width=19 height=15> button in the List All Roots screen and it looks like this:</p>

<img src="images/root-analyse.png" alt="root-analyse" width="800" height="307" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">

<ol>
<li><b>Only Show Roots With At Least This Many Occurrences</b>. You can choose to list roots that only occur a certain number of times in the Qur’anic text.<br>&nbsp;</li>

<li><b>Root</b>. For each root in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by root, just use the arrows at the top of the column — indeed, you can sort the table by any column you like, simply by using the arrows at the top of it).<br>&nbsp;</li>

<li><b>Total Occurrences</b>. For each root, Qur’an Tools shows you how often that root occurs in the whole Qur’an. Click on any number in this column and Qur’an Tools will show you all the verses where the root occurs.<br>&nbsp;</li>

<li><b>Position: First Word in Verse</b>. These two columns show often each root occurs first in a verse (and what percentage of all the root’s appearances in the Qur’an are <i>first position occurrences</i>.<br>&nbsp;</li>

<li><b>Position: Middle of Verse</b>. These two columns show often each root occurs neither first or last in a verse (and what percentage of all the root’s appearances in the Qur’an are <i>middle position occurrences</i>.<br>&nbsp;</li>

<li><b>Position: Final Word in Verse</b>. These two columns show often each root occurs last in a verse (and what percentage of all the root’s appearances in the Qur’an are <i>final position occurrences</i>.</li>
</ol>

</p>        
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>