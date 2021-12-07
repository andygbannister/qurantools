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
    window_title("Help: Interlinear Mode");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Interlinear Mode</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p><p>
	 Interlinear Mode is a useful way of viewing Qur’an passages, with basic linguistic data about each word shown beneath it. To access Interlinear Mode, simply lookup any verse or passage, and then click the Interlinear Mode button:
</p>
<p>
	<img src="images/interlinear.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 In Interlinear Mode, a verse is displayed like this:
</p>
<p>
	<img src="images/interlinear2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Beneath each word, you will see a number of pieces of useful linguistic data:
</p>
<ul>
	<li>A <em>gloss</em>&nbsp;(very brief translation) of the word<br>&nbsp;</li>

	<li>What <em>type</em>&nbsp;of word this is (verb, noun, particle, etc.)<br>&nbsp;</li>

	<li>Some <em>basic linguistic data</em>&nbsp;(gender, person, number, Arabic form). Simply point at this summary to get a little tooltip expanding it if you aren’t sure what some of the abbreviations mean:<br>
	<img src="images/interlinear3.png" class="image-mode"></li>

	<li>The lemma from which this word is derived<br>&nbsp;</li>

	<li>The Arabic root (where applicable) from which this word is derived</li>
</ul>
<p>
	 You can click on any element to search for more examples in the Qur’an (e.g. click on the root to search for any matching roots). You can also point at the main word to access the Instant Details Palette just as you can in 
	<em>Reader</em>&nbsp;<em>Mode</em>:
</p>
<p>
	<img src="images/interlinear4.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
</p>

                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>