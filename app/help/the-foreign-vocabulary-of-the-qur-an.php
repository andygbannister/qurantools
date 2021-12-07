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
    window_title("Help: The Foreign Vocabulary of the Qur’an");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Foreign Vocabulary of the Qur’an</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	<img src="images/arthur.png" style="width: 198px; float: right; margin: 0px 0px 10px 10px;" alt="" class="shadow-image">
</p>
<p>
	 One of the resources built into Qur’an Tools is Arthur Jeffery’s classic book, 
	<em>The Foreign Vocabulary</em>&nbsp;<em>of the Qur’an</em>.
</p>
<div><p>
  Arthur Jeffery (1892-1959) was Chair of the Department of Near and Middle East Languages at Columbia University. Educated in both his native Australia and Scotland, he held earlier teaching appointments in India and Egypt before moving to the United States. His 
	<em>The Foreign Vocabulary of the Qur’an</em>&nbsp;has had a tremendous influence in qur’anic studies and has been cited by tens of thousands of papers, articles, and books.
</p></div>
<div>
  As the introduction to the 
	<a href="https://brill.com/view/title/13028?lang=en" target="_blank">recently republished edition of the work</a> by Brill puts it:
</div>
<blockquote>
  Arranged in Arabic alphabetical order, Jeffery’s compendium of philological scholarship remains an indispensable tool for any serious study of Qur’ānic semantics. Drawing upon etymological examination of languages such as Greek, Persian, Syriac, Ethiopic, Coptic and Nabataean, Jeffery’s work illuminates the rich linguistic texture of Islam’s holy book. His lengthy introductory essay explores the exegetical analysis offered by medieval Muslim commentators as well as the insights provided by more recent research. 
	<br>
</blockquote>
<div>
  In Qur’an Tools, we have tagged every word in the Qur’an which is listed by Jeffery as a loanword (what he called a “foreign” word), along with a link to the relevant page in the 1938 edition of Jeffery’s book.
</div>
<div>
  There are several ways you can access this resource:
</div>
<ol>
	<li>From the Qur’an Tools home page, search for <a href="/home.php?L=%5Bloanword%5D" target="_blank">[LOANWORD]</a>. That will run a search for every word in the Qur’an tagged as being a loanword.<br>&nbsp;</li>

	<li>When pointing to a word in the text and viewing its details using the Instant Details Pane, loanwords will be indicated as such. Click on the <img src="images/loanword.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> icon to view the relevant page of&nbsp;<em>The Foreign Vocabulary</em>&nbsp;<em>of the Qur’an</em>.<br>
	<br>
	<img src="images/instant2.png" style="width: 260px;" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "><br>&nbsp;</li>

	<li>Within the Dictionary Tool (and in several other screens, you can click on the little page picture icon next to a word to see its page in&nbsp;<em>The Foreign Vocabulary</em>&nbsp;<em>of the Qur’an</em>.<p><img src="images/dc-foreign.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "><br>&nbsp;</li>

	<li>To see a list of all the loanwords in the Qur’an, click on the “Browse” menu, then choose “Word Lists” and then choose <a href="list-all-foreign-words.php">“List Loanwords”</a>.<img src="images/dc-list-loan.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "><br>&nbsp;</li>
	
	<li>To see a chart of the usage of loanwords in the Qur’an,&nbsp;click on the “Charts” menu, then choose “Words &amp; Grammar” and then choose <a href="foreign-words-vocabulary-per-sura-chart.php">“Loanwords per Sura”</a>.<br>
	<img src="images/dc-loan-chart.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "></li>
</ol>
	
</p>

               
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>