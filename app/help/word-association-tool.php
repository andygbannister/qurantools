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
    window_title("Help: The Word Association Tool");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Word Association Tool</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	<p>
	 The Word Association Tool helps you to explore qur’anic diction by seeing which words frequently occur with another one. There are three ways to access the tool.
</p>
<ol>
	<li>Open the <a href="the-dictionary-tool.php">Dictionary Tool</a> (Browse Menu &gt; Dictionary), find the word you want to see associations for, then click the <img src="images/network.gif" height="22" style="display: inline; margin: 0px; border:none; vertical-align: -20%;"> icon.<img src="images/dictionary_example.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px; margin-bottom: 0px;"><br>
	<em>... or ...</em></li>
	<li>Open the Root List (Browse Menu &gt; Word Lists &gt; List Roots), find the root you want see associations for, then click the <img src="images/network.gif" height="22" style="display: inline; margin: 0px; border:none; vertical-align: -20%;"> icon.<br>
	<br>
	<em>... or ...</em></li>
	<li>Directly open the Word Association Tool (Browse Menu &gt; Word Associations), then use either the list of Arabic roots or transliterated roots to find the root you would like to see associations for. As soon as you choose a root, its word associations will be displayed.</li>
</ol>
<h3 style="text-align: center;">Using the Results of the Word Association Tool</h3>
<p>
	 Once you have chosen a root, the Word Association Tool will list every other root that occurs in a verse along with your chosen root. For example, in the screen shot below, Qur’an Tools is showing every Arabic root that occurs in the same verse as the root&nbsp; 
	<em>'bd.&nbsp;</em>
</p>
<p>
	<img src="images/word-assoc.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top"> 
		 1
	</td>
	<td>
		<strong>Display the Results as a Table</strong>. By default, the Word Association Tool displays a list of other words that associate with your chosen one as a table.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 2
	</td>
	<td>
		<strong>Display the Results as a Bar Chart</strong>. If you prefer to see things graphically, click this button to see the results of this Word Association analysis as a bar chart. (If there are more than 50 results, Qur’an Tools will show just the first 50, so the chart doesn't get too cramped).
	</td>
</tr>
<tr>
	<td valign="top"> 
		 3
	</td>
	<td>
		<strong>Display the Results as a Pie Chart</strong>. Exactly as it says on the tin. You can display (up to 50) results as a pie chart.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 4
	</td>
	<td>
		<strong>Choose Another Root</strong>. Click this to reset the Word Association Tool and choose another root (from one of the pick lists) to examine.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 5
	</td>
	<td>
		<strong>Arabic</strong>. Qur’an Tools shows you the Arabic as well as a transliteration of each root. You can use the arrows at the top of this column to sort by (Arabic) alphabetic order.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 6
	</td>
	<td>
		<strong>Transliteration</strong>. The <a href="arabic-letters-transliterations-and-encodings.php">transliterated</a> form of each root. Use the arrows to sort by (transliterated) alphabetic order.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 7
	</td>
	<td valign="top"> 
		<strong>Appearance Count</strong>. This third column shows you the number of times each root listed appears in a verse with the root you are analysing. So, in the example above, <em>'lh</em> occurs 30 times in the same verse as <em>'bd</em>.<br>
	</td>
</tr>
<tr>
	<td valign="top"> 
		 8
	</td>
	<td>
		<strong>Count</strong>. Click on any number in this column and Qur’an Tools will show you every verse where this root appears with the root you are analysing.&nbsp;So, in the example above, if you clicked on "30", Qur’an Tools would show you all the verses in which <em>'lh</em>&nbsp;occurs with <em>'bd</em>.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 9
	</td>
	<td>
		<strong>Info Icon</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px; vertical-align:-10%;" src="images/info.gif" width="15" height="15"> icon to view additional information about this particular root.<br>
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