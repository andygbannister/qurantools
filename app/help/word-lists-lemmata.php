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
    window_title("Help: List All Lemmata");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>List All Lemmata</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


<p>
	
	<p>
	 If you select 
	<strong>List Lemmata</strong>&nbsp;from the <strong>Browse</strong> menu (where it can be found under <strong>Word</strong> <strong>Lists</strong>), Qur’an Tools will display a list of the 4,817 different lemmata that occur in the qur’anic text.
</p>
<p>
	 If you are unfamiliar with the term 
	<em>lemmata</em>, it is the plural of the term <em>lemma</em>. The lemma is the word that stands at the head of an entry in a dictionary; for example, here is the lemma <em>kātib</em>&nbsp;defined in Lane’s Arabic-English Lexicon:
</p>
<p>
	<img src="images/lane2.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
</p>
<p>
	 Each Arabic root in the Qur’an will usually be expressed in multiple lemmata. For example, here are the&nbsp; 
	<a href="/examine_root.php?ROOT=ktb" target="_blank">seven lemmata</a> based on the root <em>ktb </em>that can be found in the qur’anic text:
</p>
<p>
	<img src="images/lemmlist.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
</p>
<hr>
<p>
	 The 
	<strong>List Lemmata</strong>&nbsp;tool allows you to easily explore all the lemmata in the Qur’an; it looks like this:
</p>
<p>
	<img src="images/list_lemmata.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Show&nbsp;Occurrences&nbsp;or Show Percentages</strong>. You can choose whether the lemmata list counts the <em>occurrences</em>&nbsp;of each lemma in the Qur'an (for example, <em>min</em>&nbsp;occurs 3,225 times) or whether you’d prefer to see the percentage of lemmata that each lemma makes up &mdash; for example, click "Show Percentage of All Lemmata" and you’ll discover that <em>min</em>&nbsp;makes up 4.323% of all lemmata in the Qur’an.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Lemma</strong>. For each lemma in the list, Qur’an Tools shows you both its Arabic and its transliterated version. (If you’d like to sort the table by lemma, just use the arrows at the top of the column &mdash; indeed, you can sort the table by any column you like this way).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Root</strong>. This column shows the root that each lemma is based on (if the lemma is based on a root &mdash; for example, <em>min</em>&nbsp;is a particle-preposition, so it has no root. If there is a root listed here, you may click it on it to run a search for where it occurs in the Qur’an.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Occurrences</strong>. For each lemma, Qur’an Tools shows you how often that lemma occurs in the whole Qur’an, in Meccan suras, and in Medinan suras. Click on any number in these columns and Qur’an Tools will show you the verses where the root occurs.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Action Icons</strong>. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/info.gif" width="15" height="15"> icon to view additional information about the root underlying this lemma (if the lemma has a root); click the <img class="noBdr" style="display: inline; margin:0px;" src="images/list.gif" width="15" height="15"> icon to <a href="exhaustive-list-of-references-for-root-or-lemma.php">exhaustively list all the occurrences</a> in the Qur’an of this lemma; or click <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> to display a chart of this lemma’s occurrences in the Qur’an (or simply point at the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon and wait a few seconds to see a mini-chart, like the example below).<br>
		<p>
			<img src="images/lemma-pop.png" style="box-shadow: rgba(0, 0, 0, 0.498039) 0px 8px 8px 3px;">
		</p>
	</td>
</tr>
</tbody>
</table>
<hr style="margin-top:0px;">
<p>
	 At the bottom of the root list, you’ll find a page navigator, as displaying the entire list of lemmata on one screen slows some web browsers to a crawl.
</p>
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