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
    window_title("Help: Tags");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Tags</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<h4> <strong>Creating Tags</strong></h4>
<p>
	 Qur’an Tools allows you to create your own list of tags, labels that you can apply to as many verses as you like, in order to help you easily find them again.
</p>
<p>
	 For example, here is a verse with two tags applied:
</p>
<p>
	<img src="images/two-tags.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 To create a tag, choose “My Tags” from the “My Profile” menu in Qur’an Tools’ menubar and the 
	<strong>Tag Manager</strong> will appear. Then click “Create a New Tag” and you’ll be asked to type a name for your tag. You can also click the colour box and drag the + sign and/or the slider to choose the colour for your new tag, like this:
</p>
<p>
	<img src="images/tag-col.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Once you are happy with the tag name and colour, click “Create Tag” or press ENTER.
</p>
<h4>Editing or Deleting a Tag</h4>

<p>
	 To delete a tag, simply click on the 
	<img src="images/delete.gif" class="noBdr" style="display: inline; margin: 0px;" width="16" height="16"> icon next to its name in the Tag Manager. To rename it, or change its colour, click a tag’s <img src="images/edit.gif" class="noBdr" style="display: inline; margin: 0px;" width="16" height="16"> icon.
</p>
</h4>
<h4>Applying a Tag to a Verse</h4>
<p>
	 Once you have created your tag, it is easy to apply it to any verse. Simple open the verse browser and point your mouse at the verse reference, until the verse tools appear, like this:
</p>
<p>
	<img src="images/vtools.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
	</p>
	Click the “Tags” button and Qur’an Tools will show you a list of all your tags. Click the tag (or tags) you wish to apply to this verse and click the “Apply Changes” button.
</p>
<h4>Removing a Tag from a Verse</h4>
<p>
	 There are two ways to remove a tag from a verse. The first way is to use the same steps as above to create a tag, only untick the tag (or tags) you wish to remove, before clicking “Apply Changes”.
</p>
<p>
	 The second method is to point your mouse at any tag beneath a verse and click it; you'll see a toolbox like this appear:
</p>
<p>
	<img src="images/tag-tools.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Click “Remove Tag from Verse” to remove the tag.
</p>
<h4 id="multiple">Adding or Removing Tags From Multiple Verses</h4>
<p>
	 It is also easy to add (or remove) a tag from multiple verses at once. Simply bring up a group of verses in the verse browser (either by looking up a range of verses, or by performing a search). Then click the TAGS button at the top right of the verse browser and you’ll see a toolbox like this:
</p>
<p>
	<img src="images/tag-multiple.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Tick the tag of your choice and then click either “Apply Tag To All These Verses” to add the tag to all the verses shown in the browser, or “Remove Tag From All These Verses” to remove it.
</p>
<h4>Searching for Tags</h4>
<p>
	 It is incredibly easy to search for a verse once it has been tagged. Here are a few ways to do it:
</p>
<p>
	 The first way is to open the Tag Manager (Profile menu -&gt; My Tags) where you'll see a list of all your tags together with how many verses each is applied to:
</p>
<p>
	<img src="images/my-tags.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Simply click on tag name (or its count of occurrences) to open those verses in the verse browser.
</p>
<div class="callout" style="margin-top:0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

	<strong>TIP</strong>
	<p>
		 Click on the 
		<img src="images/st.gif" class="noBdr" style="display: inline; margin-top: -4px; margin-bottom: 0px;"> icon to see a chart of all your tags and how many times they are used. From there, you can click on any chart column to drill down to see the verses in question. You can also just point your mouse at the <img src="images/st.gif" class="noBdr" style="display: inline; margin-top: -4px; margin-bottom: 0px;"> icon to see a pop-up mini chart.
	</p>
</div>
<p>
	 The second way to search is to click a tag below any verse and from the menu that appears, choose “Find All Verses With This Tag”:
</p>
<p>
	<img src="images/tag-findall.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 Thirdly, you can use the TAG command when typing a search on the main Qur’an Tools home page, like so:
</p>
<p>
	<img src="images/tag-home-page.png" height=70% width=70% border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<p>
	 (Note, if your tag name has spaces in it, surround it with quote marks, like this: 
	<strong>TAG:"HEAVEN AND HELL"</strong>).
</p>
<p>
	 When searching for tags like this, you can also use a couple of tricks:
</p>
<ul>
	<li>To search for all verses with any tag, search for <strong>TAG:ANY</strong>.</li>
	<li>To search for verses tagged with a tag that <em>begins with</em>&nbsp;certain letters, such as all verses tagged with a tag that starts with “heaven”, you could search for <strong>TAG:heaven*</strong>, which would find verses tagged with “heaven”, “heavens and the earth”, “heaven and hell” and so forth.</li>
</ul>
<p>
	 And finally, you can also click the word&nbsp; 
	<strong>Tags</strong>&nbsp;under the search box on the Qur’an Tools home page to see a list of all your tags &mdash; click on any of them and press ENTER to search for it.
</p>
</p>
                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>