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
    window_title("Help: Looking Up a Qur'an Passage");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Looking Up a Qur'an Passage</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	<p>
	 To lookup a portion of the Qur’an, simply type what you are looking for into the search box on the Qur’an Tools home page.
</p>
<p>
	You can lookup verses or passages in a number of ways:
</p>
<ul>
	<li>Type <a href="/home.php?L=2">2</a> to lookup a single sura, in this case sura 2.<br>&nbsp;</li>
	
	<li>Type <a href="/home.php?L=110-114">110-114</a> to lookup a range of suras, in this case suras 110 through 114.<br>&nbsp;</li>
	
	<li>Type <a href="/home.php?L=90f">90f</a> to lookup a sura and the one immediately after it: in this case suras 90 and 91.<br>&nbsp;</li>
	
	<li>Type <a href="/home.php?L=108-">108-</a> or <a href="/home.php?L=108ff">108ff</a> to lookup everything from a sura to the end of the Qur’an: in this case suras 108 to 114.</li>
</ul>
<p>
	 You can also, of course, lookup single verses or a range of them. For example:
</p>
<ul>
	<li>A single verse: <a href="/home.php?L=19:19">19:19</a><br>&nbsp;</li>
	
	<li>A range of verses within a single sura <a href="/home.php?L=19:19-22">19:19-22</a><br>&nbsp;</li>
	
<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">
		<strong>TIP</strong>
		<p>
			 You can also type the sura name (either the Arabic name or the English). So, for example, these are all equivalent:
		</p>
		<ul>
			<li><a href="/home.php?L=2:1-10">2:1-10</a><br>&nbsp;</li>
			<li><a href="/home.php?L=Al%20Baqarah:1-10">Al Baqarah:1-10</a> &lt;-&nbsp;<em>don't forget the colon between the sura name and the verse</em><br>&nbsp;</li>
			<li><a href="/home.php?L=The%20Cow:1-10">The Cow:1-10</a> &lt;-&nbsp;<em>don't forget the colon between the sura name and the verse</em><br>&nbsp;</li>
		</ul>
		 You can see a complete list of sura names, in Arabic and English, using the <a href="browsing-the-sura-list.php">Sura List tool</a>.<br>&nbsp;
	</div><br>
	<li>A range of verses across several suras: <a href="/home.php?L=112:1-114:2">112:1-114:2</a><br>&nbsp;</li>
	
	<li>A verse and the one that follows it: <a href="/home.php?L=40:13f">40:13f</a><br>&nbsp;</li>
	
	<li>A verse and everything after it, up to end of its sura: <a href="/home.php?L=106:1-">106:1-</a> or <a href="/home.php?L=106:1ff">106:1ff</a><br>&nbsp;</li>
	
	<li>A set of discontinuous verses in a sura: <a href="/home.php?L=19:4,11,14,19-22,52">19:4,11,14,19-22,52</a></li>
</ul>
<p>
	 It’s also possible to lookup multiple references in one search, by separating them with a semicolon (;). For example:
</p>
<ul>
	<li><a href="/home.php?L=2:34;7:11-18;15:29-43;17:61-64;18:50;20:116-117;38:71-83">2:34; 7:11-18; 15:29-43; 17:61-64; 18:50; 20:116-117; 38:71-83</a></li>
</ul>
<p>
	 Once you have a entered a reference into the search box and hit enter, Qur’an Tools will display the verses you have asked for in its&nbsp; 
	<a href="the-verse-browser-in-detail.php">browser</a> for you to study.
</p>
</p>
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>