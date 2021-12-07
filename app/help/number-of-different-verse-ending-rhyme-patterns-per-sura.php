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
    window_title("Help: Number of Different Verse Ending (Rhyme) Patterns per Sura");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Number of Different Verse Ending (Rhyme) Patterns per Sura</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	<p>
	 The 
	<strong>Number of Different Verse Endings (Rhyme) Patterns </strong>tool&nbsp;(found under the “Rhyme” menu) lists each sura in the Qur’an together with some statistics about the number of different verse ending patterns (rhymes) it uses. It looks like this:
</p>
<p>
	<img src="images/chart-rhyme-patterns.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4"cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Sura</strong>. Each sura of the Qur’an is listed. (Click the arrows to sort the table by sura (ascending or descending) &mdash; you can also sort by any of the other columns).
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Provenance</strong>. Each sura’s traditional provenance (Meccan or Medinan) is listed.
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Verses</strong>. The number of verses in each sura.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Different Verse Endings</strong>. The number of different sounds (rhymes) used as verse endings in each sura. Click the <img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to see a <a href="number-of-different-verse-endings-rhymes-per-sura.php">chart</a> of this data (or simply point your cursor at the&nbsp;<img class="noBdr" style="display: inline; margin: 0px;" src="images/st.gif" width="25" height="14"> icon to see a quick pop-up chart).
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