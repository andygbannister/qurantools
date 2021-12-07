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
    window_title("Help: Formulaic Diversity per Sura Chart");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Diversity per Sura Chart</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	<p>
	 The 
	<strong>Formulaic Diversity per Sura</strong> chart (found under the ‘Charts’ menu and then under ‘Formulae’) shows you how many <em>different</em>&nbsp;formulae are used in each sura. (Find out more about formulaic phraseology, how to study it in Qur’an Tools, and some of the implications of the Qur’an’s heavy use of formulae <a href="formulaic-analysis.php">here</a>).
</p>
<p>
	<img src="images/formulaic_diversity_chart.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Formula Type and Length to Show</strong>. This allows you to customise the type of formulae that Qur’an Tools counts and shows in the chart below. (The <a href="formulaic-analysis.php#length"><em>length</em></a> is the number of Arabic words in a formula; the <a href="formulaic-analysis.php#types"><em>type</em></a> allows you to choose from any of the three formula types that Qur’an Tools understands, or to choose ‘All Formula Types’ which means the chart will count what percentage of the words in each sura are part of <em>any</em>&nbsp;type of formula.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan suras, or just Medinan suras on the chart. <br>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Count</strong>. By clicking the buttons, you can choose whether to count every formulae in each sura, or instead count how many formulae occur per 100 words. (The latter is useful as it helps adjust for the fact that <a href="browsing-the-sura-list.php">some suras</a> are much longer than others, so you can see the relative formulae use per sura).&nbsp; &nbsp;<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by&nbsp;<em>sura</em>&nbsp;(so sura 1 is drawn first and sura 114 last), or you can sort your chart by&nbsp;<em>the number of different formula</em>&nbsp;(meaning suras with a greater number of different formulae in them will appear first).&nbsp;
	</td>
</tr>
<tr>
	<td valign="top"> 
		 5
	</td>
	<td>
			<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to “drill down” &mdash; Qur’an Tools will show you a list of every formulae in the sura whose column you clicked. &nbsp;&nbsp;
	</td>
</tr>
</tbody>
</table>
<div class="callout" style="margin-top:10px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
	<p>
		<strong>TIP</strong>
	</p>
	<p>
		 This chart counts the number of 
		<em>different</em>&nbsp;formulae in a sura (e.g. if a sura used one formulae ten times, it would show a diversity of "1" on this chart). If you’d prefer to count how many formulae <em>in total</em>&nbsp;occur in each sura, you’d want to use the <a href="number-of-formulae-used-per-sura-chart.php">‘Number of Formulae Used per Sura’ chart</a> instead.
	</p>
</div>
</p>









                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>