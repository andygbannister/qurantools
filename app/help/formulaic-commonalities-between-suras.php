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
    window_title("Help: Formulaic Commonalities Between Suras");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Commonalities Between Suras</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The 
	<strong>Formulaic Commonalities</strong>&nbsp;report (found under the ‘Formulae’ menu) shows you all the formulae that a sura has in common with other suras. It’s a powerful to trace and export the formulaic diction that underpins the qur’anic text and its composition.
</p>
<p>
	<img src="images/commonalities.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura to Examine</strong>. Pick the sura you want to use as your ‘base’ and look for formulaic commonalities for in other suras.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Sura to Cross-Check</strong>. Pick the other sura you want to look for commonalities/connections to.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
			<strong>Formula Length and Type</strong>. Choose which formula length and type you want to look for (or choose ‘Any’ and ‘All Formula Types’ to look for all formulae).
		<br>
		<div class="callout" style="margin-top:10px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
			<strong>TIP</strong>
			<br>
			<p>
				<a href="formulaic-analysis.php">Read more about Qur’an Tools's formula types and lengths and what they mean</a>.
			</p>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
			<strong>Report Type</strong>. Choose ‘Group by Verses’ and the report will list every verse in the first sura (#2 above) together with any formulae in that also occurs in the other sura (#3 above). Or choose ‘Group by Formulae’ and the report will instead simply list every formula along with its occurrence statistics, like this:
		<p>
			<img src="images/formulaic-commonalities.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Formula List</strong>. Explore the list of formula here. Click on any formula (or occurrence count) to open the verses that show it.&nbsp;Underneath each transliteration is shown a brief gloss of the formula, or the words in it. (You can turn these formulaic glosses off in <a href="preferences.php">Preferences</a> if you would prefer not to see them).
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
			<strong>Show as Chart</strong>. Click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> icon to see a chart showing the formulae in common between this sura and every other sura in the Qur’an.&nbsp;
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