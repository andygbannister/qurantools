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
    window_title("Help: Exhaustive List of References for Root (or Lemma)");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Exhaustive List of References for Root (or Lemma)</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 The 
	<strong>Exhaustive List of References </strong>report lists every verse in which a particular root (or lemma) appears, along with a brief context (the few words immediately before and after it). It can be helpful at getting a quick overview of where a lemma or root appears, or quickly finding a verse you are looking for without wading through multiple pages of search results&nbsp;in the verse browser. It looks like this:
</p>
<p>
	<img src="images/exhaustive.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Root or Lemma to Find</strong>. Tells you which root or lemma is currently being studied. To pick a different root or lemma, scroll to the bottom of the report and click “Exhaustively List Another Root/Lemma”, or use the <a href="word-lists-roots.php">List All Roots</a> or <a href="word-lists-lemmata.php">List All Lemmata</a> reports and click&nbsp;the <img class="noBdr" style="display: inline; margin:0px;" src="images/list.gif" width="15" height="15"> icon next to the root or lemma you are interested in.&nbsp;
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Reference</strong>. The sura, verse, and word number for each place in the Qur’an where this particular root or lemma appears. Click on the reference to open the full verse in the verse browser (or just point your mouse at it for a few seconds to see a pop-up verse browser).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Arabic Text</strong>. The occurrence itself, along with the few words before or after it. An ellipsis (“...”) shows you there have been words truncated (so the report isn’t too long). Click any Arabic text to open the full verse in the verse browser.
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Transliterated Text</strong>. As #3 above, but the transliterated Arabic text. Click any text to open the full verse.<br>
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