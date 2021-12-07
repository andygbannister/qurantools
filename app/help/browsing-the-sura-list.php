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
    window_title("Help: Browsing the Sura List");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Browsing the Sura List</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 Under the&nbsp; 
	<strong>browse menu</strong>&nbsp;in Qur’an Tools’ menu bar, you will find the Sura List, which gives you an at-a-glance list of all 114 suras of the Qur’an, along with a number of useful details about each of them. The key features are numbered and then described below.
</p>
<p>
	<img src="images/list-all-suras.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura Name and Provenance</strong>. Each sura has its Arabic and English name listed. (Some suras have more than one name associated with them; in such cases, Qur’an Tools shows the most commonly used name). The sura’s provenance (Meccan or Medinan, according to the commonly used&nbsp;Nöldeke-Schwally system, is also listed (see Neal Robinson, <em>Discovering the Qur’an: A Contemporary Approach to a Veiled Text</em>&nbsp;(London: SCM, 2003) 60-96 esp. 77 for a thorough discussion on sura provenance). Click on the sura number or either of its names to open it in the viewer.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Sura Filtering</strong>. Click to filter the sura list to show all suras, just Meccan, or just Medinan suras.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Verse and Word Counts</strong>. Shows the numbers of verses and words in each sura. (And also the number of words if one discounts the "qur’anic initials" that start <a href="/verse_browser.php?V=2:1;3:1;7:1;10:1;11:1;12:1;13:1;14:1;15:1;19:1;20:1;26:1;27:1;28:1;29:1;30:1;31:1;32:1;36:1;38:1;40:1;41:1;42:1;43:1;44:1;45:1;46:1;50:1;68:1">29 suras</a> of the Qur’an. If you click on the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;">&nbsp;icon you can see a chart of the lengths of the qur’anic suras, or of the length of each verse within a sura; or click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> icon to see a <a href="sura-verse-length-characteristics-chart.php">Sura Verse Lengths Characteristics chart</a> for this sura).<br>
		<br>
		<div class="callout" style="margin-top:-10px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<strong>TIP&nbsp;</strong>If you simply point at one of the&nbsp; 
			<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;">&nbsp; or 
			<img src="images/st2.png" class="noBdr" style="display: inline; margin: 0px;"> icons for an individual sura and wait for a moment, Qur’an Tools will pop open a mini version of that chart for you to see. If you’d like to see it in greater detail, just click the&nbsp; 
			<img src="images/expand.png" class="noBdr" style="display: inline; margin: 0px;" width="14" height="14">&nbsp;icon. 
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Words Part of a Formulae</strong>. Shows you how many words in this sura form part of qur’anic formulae (short, repeated phrases, used time and time again as a building block of qur’anic diction. Click on the number to see a list of all the formulae that occur in this particular sura.
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