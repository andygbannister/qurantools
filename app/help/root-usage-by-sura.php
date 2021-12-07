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
    window_title("Help: Root Usage by Sura");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Root Usage per Sura</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	<p>
	 The 
	<strong>Root Usage by Sura</strong> report, found under the <strong>browse menu</strong> in Qur’an Tools’s menu bar, offers a number of extremely useful statistics about the different types of Arabic roots used in each of the 114 suras of the Qur’an.
</p>
<p>
	<img src="images/roots-per-sura.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura Name and Provenance</strong>. Each sura has its number and provenance (Meccan or Medinan) listed. Click on the sura number to open it in the viewer. You can also sort the table by the sura number (or any other column) by using the <img src="images/up.gif" class="noBdr" style="display: inline; margin: 0px;" alt="downarrow" width="14" height="14"> and <img src="images/down.gif" class="noBdr" style="display: inline; margin: 0px;" alt="downarrow" width="14" height="14"> buttons at the top of most columns.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Different Roots Used in Sura</strong>. Shows the number of different Arabic roots used in this sura. Click on the number to see a list of roots, or click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" alt="stats" width="25" height="14"> icon at the top of the column to view a chart showing the number of roots in each sura.
	</td>
</tr>
<tr>
	<td valign="top"> 
		 3
	</td>
	<td>
		<strong>Unique Roots in Sura</strong>. Shows how many of the Arabic roots used in this sura are unique to it. Click on the number to run a search for these roots (you can do the same manually by doing a search for <a href="/verse_browser.php?S=[UNIQUE]">[UNIQUE]</a> to find all the unique roots in every sura of Qur’an, or within just one sura with a search like <a href="/verse_browser.php?S=[UNIQUE] RANGE:2">[UNIQUE] RANGE:2</a>. Click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" alt="stats" width="25" height="14"> icon at the top of the column to view a chart showing the number of unique roots in each sura.
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Percentage of Unique Roots</strong>. Shows the percentage of the Arabic roots in this sura that are unique (i.e. occur only in <em>this</em> sura). Click on the number to open the sura in the view with these roots highlighted, or click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" alt="stats" width="25" height="14"> icon at the top of the column to view a chart showing the percentage of roots that are unique in each sura.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Hapax Legomena in Sura</strong>. A <em>hapax legomena</em> is a word that occurs just once in a text. Thus this column shows how many Arabic roots in this particular sura occur just <em>once</em> in the <em>entire Qur’an</em>. Click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" alt="stats" width="25" height="14"> to see a chart analysing this phenomena, or click a value to open the sura in the viewer with the hapax legomena within it highlighted. (You can also run a manual search like <a href="/verse_browser.php?S=[HAPAX]">[HAPAX]</a> or <a href="/verse_browser.php?S=[HAPAX] RANGE:2">[HAPAX] RANGE:2</a> to do the same thing).
	</td>
</tr>
</tbody>
</table>
<p>
	 If you scroll to the bottom of the table, you will see a summary of the root usage for all suras, Meccan suras, and Medinan suras.
</p>
</p>

                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>