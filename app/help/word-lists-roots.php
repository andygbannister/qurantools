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
    window_title("Help: List All Roots");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Roots</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 If you select 
	<strong>List Roots</strong> from the <strong>Browse</strong> menu (where it can be found under <strong>Word Lists</strong>), Qur’an Tools will display a list of the 1,642 different Arabic roots that occur in the qur’anic text.
</p>
<p>
	<img src="images/list-all-roots.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Show&nbsp;Occurrences&nbsp;or Show Percentages</strong>. You can choose whether the root list shows the <em>occurrences</em>&nbsp;of each root in the Qur'an (for example, <em>'lh</em> occurs 2,851 times) or whether you’d like to see the <em>percentage</em> of roots that each root makes up &mdash; for example, click "Show Percentage of All Roots" and you’ll discover that <em>'lh</em> makes up 5.706% of all Arabic roots in the Qur’an.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Root</strong>. For each root in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by root, just use the arrows at the top of the column &mdash; indeed, you can sort the table by any column you like, simply by using the arrows at the top of it).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Occurrences</strong>. For each root, Qur’an Tools shows you how often that root occurs in the whole Qur’an, in Meccan suras, and in Medinan suras. Click on any number in these columns and Qur’an Tools will show you the verses where the root occurs.<br>
	</td>
</tr>
<tr id="position">
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Position Counts</strong>. Qur’an Tools shows how many times each root appears as the first word in a verse, as the last verse, or elsewhere in the verse (“middle”). Click on any number in these columns to search for those particular occurrences. Click the <img src="/images/table.png" class="noBdr" style="display: inline; margin: 0px;" width="13" height="12"> button to see a more detailed display of the verse position information and statistics for each root.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Appearance in Formula</strong>. A count of how many times this particular Arabic root appears in a formulaic phrase somewhere in the Qur’an. Simply click on any number in this column and Qur’an Tools will display the appropriate list of formulae for you.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Unique/Hapax</strong>. A unique root is a root that is unique to one sura. For example, if you sort by this column, you can quickly see that <em>'zz</em> is unique to sura 19.<br>
		<p>
			<img src="images/unique1.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		 A hapax, on the other hand, is a root that occurs just once in the 
		<em>entire</em> Qur’an. <br>
		<p>
			<img src="images/unique2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		 For every unique or hapax root, the root list also tells you which sura it occurs in. 
		<br>
		<br>
<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<strong>TIP</strong>
			<p>
				<br>
				 You can easily find all the unique or hapax roots in the Qur’an by just typing [UNIQUE] or [HAPAX] into the search box on the Qur’an Tools home page. (Don’t forget the square brackets!)
			</p>
			<p>
				 And remember that 
				<em>every</em>&nbsp;hapax is also a <em>unique</em>&nbsp;root (because by definition, a root that only occurs once can only occur in one sura). By contrast, a unique root may occur more than once&mdash;but all those occurrences will be in the <em>same</em>&nbsp;sura.
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img class="noBdr" style="display: inline; margin:0px;" src="images/info.gif" width="15" height="15"> icon to view additional information about this root; click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of its occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart); click the <img class="noBdr" style="display: inline; margin:0px;" src="images/list.gif" width="15" height="15"> icon to <a href="exhaustive-list-of-references-for-root-or-lemma.php">exhaustively list all the occurrences</a> in the Qur’an of this root;&nbsp;or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/network.gif" valign="middle" width="15" height="15"> to open the <a href="word-association-tool.php">Word Association Tool</a> for this root (which other qur’anic roots occur with it).
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:0px;">
 At the bottom of the root list, you’ll find a page navigator, as displaying the entire list of roots on one screen slows some web browsers to a crawl.
<p>
	<img src="images/pagenav.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Just click on a number to move between pages.
</p>
</p>
                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>