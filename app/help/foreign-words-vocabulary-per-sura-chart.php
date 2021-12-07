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
    window_title("Help: Chart of Number of Loanwords per Sura");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Chart of Number of Loanwords per Sura</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Number of Loanwords per Sura Chart</strong> (found in the 'Charts' menu under the ‘Words &amp; Grammar’ submenu) allows you to quickly see how the different suras of the Qur’an use loanwords (words classified as such in the classic study <a href="the-foreign-vocabulary-of-the-qur-an.php"><em>The Foreign Vocabulary of the Qur’an</em></a> by Arthur Jeffery).&nbsp;
</p>
<p>
	<img src="images/foreign-per-sura.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Table Button</strong>. Click the button to see a <a href="list-all-foreign-words.php">complete list of every loanword</a> in the Qur’an in detail.&nbsp;
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Suras to Show</strong>. By clicking the buttons, you can choose whether to plot every sura, just Meccan, or just Medinan suras on the chart.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Sort By</strong>. You can either sort your chart by <em>sura</em> (so sura 1 is drawn first and sura 114 is drawn last), or you can sort your chart by <em>percentage value</em> (meaning suras that have a higher concentration of loanwords will appear first).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Count</strong>. Choose whether to count the total number of loanwords in each sura, or what percentage of all words in the Qur’an are loanwords. (Counting by percentage has the advantage of normalising the dataset, enabling you to more easily compare long and short suras).<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Chart Columns</strong>. Point your mouse at a column and Qur’an Tools will show you a tooltip with the exact value in it. You can also click a column to run a search for loanwords in the given sura.<br>
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