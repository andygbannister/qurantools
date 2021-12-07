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
    window_title("Help: Formulaic Density Summaries");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Density Summaries</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Formulaic Density Summary Table</strong> (found under the ‘Formulae’ menu) shows you the formulaic density of the entire Qur’an, based on a number of different formulae <a href="formulaic-analysis.php#types">types</a> and <a href="formulaic-analysis.php#length">lengths</a> (The formulaic density is a figure showing what percentage of the words in a text or passage, in this case, the <em>entire</em>&nbsp;Qur’an, form part of a formulaic phrase).
</p>
<p align=center>
	<img src="images/formulaic-summaries.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Simply click on any value in this table and Qur’an Tools will “drill down” and display a breakdown of the formulaic density of every individual sura.
</p>
</p>

                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>