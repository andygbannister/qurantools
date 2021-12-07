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
    window_title("Help: Intertextual Connections");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo">
	                <br>Intertextual Connections Between the Qur’an and Other Texts/Traditions</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	 One of the striking features of the Qur’an is the extensive connections it has to other religious traditions from Late Antiquity and earlier, especially Jewish and Christian traditions (both scriptural and apocryphal):
</p>
<blockquote>
  According to Faruq Sherif, approximately one-fourth of the Qur’ān’s verses are concerned with narratives of prophets or other figures from Jewish and Christian tradition. 
	<a href="#footnotes"><sup>1</sup></a>
</blockquote>
<div>
  Given that neither the Bible nor other Jewish and Christian writings were available in Arabic at the time of the Qur’an’s composition, and given the absence of direct quotations and the often allusive way the Qur’an refers to these traditions, the connection between the Qur’an and these earlier traditions is most likely oral.
</div>
<p>
  Qur’an Tools contains a growing database of what we have termed <strong>intertextual connections</strong> found in the Qur’an, along with a number of tools to make it easy to study them. Whilst there are a number of scholarly theories concerning how this material found its way into the Qur’an, Qur’an Tools is simply concerned with recording the connections, making them easy to discover, and equally easy to explore.
</p>
<div>
	<p>
		 There are several ways you can begin to explore the Qur’an’s intertextual connections ...
	</p>
	<h4> <strong>First, you can study intertextual connections right in the verse browser ...</strong> </h4>
	<p>
		 Open a verse in the verse browser and point your cursor at it. The verse tools palette will appear:
	</p>
	<p>
		<img src="images/vtools.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
	<p>
		 If there are intertextual connections for this verse, click the 
		<img src="images/links.png" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 1px;"> icon to list them:
	</p>
	<p>
		<img src="images/connections.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
	<p>
		 Click the "View Passage" button to view the relevant passage from an intertextual connection (or click "View Entire Source" to view the entire text/document in a separate browser window or tab). You can also click the 
		<img class="noBdr" style="display: inline; margin: 0px;" src="images/table.png"> icon to browse all the intertextually connected sources known to Qur’an Tools for the entire Qur’an, or the <img src="images/st.gif" width="25" height="14"> icon to explore them as a chart.
	</p>
	<h4> <strong>Second, you can easily search for verses with intertextual connections ...</strong> </h4>
	<p>
		 Simply type INTERTEXTS&gt;0 into the search box on the home page:
	</p>
	<p>
		<img src="images/s_intertexts.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
</div>
<div>
  ... and Qur’an Tools will list every verse with (in this this case) at least one intertextual connection.
</div>
<h4> <strong>Third, you can quickly browse all the sources linked intertextually to the Qur’an ...</strong> </h4>
<div>
  Simply choose "Intertextuality" from the Browse menu and then "Intertextual Connections":
</div>
<div>
	<p>
		 Qur’an Tools will then show a 
		<a href="browse-intertextual-connections.php">list of all the sources/texts/documents it knows of</a>, along with where there are connections to them in the Qur’an.
	</p>
	<p>
		<img src="images/browse_inter.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
	<h4> <strong>Fourth, you can explore intertextuality as a chart ...</strong> </h4>
	<p>
		 Just choose "Intertextuality" from the "Charts" menu and then decide whether you want to see a chart of every source/document and the number of times it has links, or the 
		<a href="chart-of-verses-with-intertextual-connections.php">number of intertextual connections per sura</a>. The former chart type looks like this:
	</p>
	<p>
		<img src="images/links-per-source.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
	<hr id="footnotes">
	<sup>1</sup>&nbsp;&nbsp;&nbsp;&nbsp;Gabriel Said Reynolds, 
	<em>The Qur’ān and the Bible: Text and Commentary</em>&nbsp;(New Haven: Yale University Press, 2018) p2
</div>





                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>