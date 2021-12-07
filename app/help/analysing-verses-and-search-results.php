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
    window_title("Help: Analysing Verses and Search Results");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Analysing Verses and Search Results</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	<p>
	 Once the Qur’an browser window is showing the results of a 
	<a href="advanced-searching.php">search</a> or a series of <a href="the-verse-browser-in-detail.php">verses</a> you have looked up, you can analyse the verses that are displayed in a number of different ways. Click on the "Analyse" button at the top right of the browser window to open the Analysis palette. This will offer you four options (for search results) or three options (for verses looked up):
</p>
<p>
	<img src="images/anapalette.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">

</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
			<strong>Count or Chart Search Hits</strong>. Click this to display a count of all the "hits" from your search (places where your search terms appear in the text). You can choose whether to display the number of hits per sura, or per verse (just click the button to change the option). You can also show your results as a chart, if you prefer a more visual analysis.		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<p>
				<strong>TIP</strong>
			</p>
			<p>
				 Click on any of the columns in the chart to "drill down" and see just the verses in that sura which match your search. (You can then use your browser’s back button to get back to the chart should you wish).
			</p>
		</div>
		<br>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<p>
				<strong>TIP</strong>
			</p>
			<p>
				 You can also easily see a chart of your search results by choosing "Chart Search Hits" from the charts menu when viewing the results of a search.
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
			<strong>Analyse Words in These Verses</strong>. Choose this to display a count of all the various Arabic roots that appear in the verses you have looked up (or your search has returned).
		</p>
		
		<p>
			 For any root in the list, you can click on 
			<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" alt="stats" width="25" height="14"> to see a chart of its frequency across the whole Qur’an, or click <img src="images/info.gif" class="noBdr" style="display: inline; margin: 0px;" alt="info" width="15" height="15"> to see its definition and linguistic features. You can also easily display these information graphically, too, for example as a pie chart, just by clicking on the appropriate button
		</p>
		
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<p>
				<strong>TIP</strong>
			</p>
			<p>
				 Click on any slice in the pie chart (or any column, if viewing a bar chart) to "drill down" and see more detail. (You can then use your browser’s back button to get back to the chart should you wish).
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
			<strong>Count Words and Letters in These Verses</strong>. Choose this to display a count of the words (or the letters) in the verses you have looked up (or your search has returned).
		
			 You can also choose to count the letters rather than the words&mdash;and to display your results as a chart if you prefer. So, if for example, you 
			<em>really</em> can't sleep because you're lying awake wondering how many <em>alifs</em> occur in Q. 2:1-10, then Qur’an Tools has you covered:
		</p>
		
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
			<strong>Analyse Sura Lengths in This Selection</strong>. Choose this to show (either as a table, or as a chart) a list of each sura from which verses in your selection or search results comes. Each sura is shown along with its length.
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