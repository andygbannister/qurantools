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
    window_title("Help: The Qur’an Tools Home Page (In Detail)");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>The Qur’an Tools Home Page (In Detail)</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	
	<p>
	 The home page of Qur’an Tools offers a number of tools to help you quickly start browsing or searching the qur’anic text.
</p>
<p>
	<img src="images/home-page.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>Menu Bar</strong>. This appears on the top of every Qur’an Tools page. Remember that you can always click the <img src="images/menu_logo.png" class="noBdr" alt="home_menu_button" width="16" height="15" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon to return to Qur’an Tools' home page (or click the <img src="images/menu_search.png" class="noBdr" alt="menu_search" width="15" height="15" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon to type a search or verse lookup right into the menu bar). <a href="menu-bar.php">Read more about the menu bar here</a>.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Verse/Search Input Box</strong>. Type the verses you wish to look up (or the search you wish to perform) here and press enter (or click the large ‘Search’ button).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Bookmarks</strong>. Click to open the bookmarks list, which looks like this:<br>
		<p>
			<img src="images/home_bookmarks.png" width=50% height=50% style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		 To copy the contents of a bookmark to the input box, simply click it (if you’ve forgotten what the bookmark refers to, point your mouse at it for a reminder.) Or click 
		<img src="images/use.gif" class="noBdr" style="display: inline; margin: 0px;" width="12" height="12"> to copy the <em>name</em> of the bookmark to the search box, <img src="images/edit.gif" class="noBdr" style="display: inline; margin: 0px;" width="16" height="16"> to rename it, or <img src="images/delete.gif" class="noBdr" style="display: inline; margin: 0px;" width="12" height="12"> to delete it. To delete all your bookmarks, click 'Delete All Bookmarks' at the bottom of the bookmarks list. Once you have finished working with bookmarks, you can shut its drawer just by clicking on "Bookmarks" again.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>History</strong>. Click to open the history drawer, which shows a list of up to 50 previous searches and verse lookups. Click any history item to copy it to the input box, or click&nbsp;<img src="images/delete.gif" class="noBdr" style="display: inline; margin: 0px;" width="12" height="12"> to delete it it. (You can also delete your entire history by scrolling to the bottom of the history list and clicking ‘Delete all history’.
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Commands</strong>. Click to open the commands drawer, which shows a list of some of the most common Qur’an Tools commands. You can 'type' a command into the input box by simply clicking on it. To close the commands drawer, simply click "Commands" a second time.
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td>
		<strong>Roots</strong>. Click to open the roots drawer, which shows a list of all the 1,642 Arabic roots in the Qur’an Tools. Simply scroll through the list, find the root you want, then click on it (or its transliteration) to 'type' that root (or its transliteration) into the input box. To close the roots drawer, simply click "Roots" a second time. <p><img src="images/home-roots.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "></p>
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
			<strong>Verse Picker</strong>. Click to open the verse picker tool. Next click a sura number and the verse picker will list every verse number in that sura; pick a verse number to open that verse in the browser.
		<p>
			<img src="images/home-verse-picker.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
	</td>
</tr>
<tr>
	<td valign="top">
		 8
	</td>
	<td>
		<strong>Arabic and Transliteration Keyboard</strong>. Opens a 'keyboard' listing every Arabic letter (and it's transliterated equivalent). By clicking on letters, you can enter them into the search box. This can be a great timer saver if you can’t remember the right key press on your computer keyboard for a particular letter. (Click the ⇔ icon to flip the keyboard between left-to-right and right-to-left). <br>
		<p>
			<img src="images/arabic-keyboard.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		<div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

			<strong>TIP</strong>
			<p>
				 Click on "Extra Characters" at the bottom of the keyboard to open a list of every possible character/glyph that Qur’an Tools is aware of. Scroll through until you find the character you want and either click on the Arabic or the transliterated character to type it.
			</p>
			<img src="images/keyboard-extra.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</div>
	</td>
</tr>
<tr>
	<td valign="top">
		 9
	</td>
	<td>
		<strong>Easy Search</strong>. Click this to open Qur’an Tools' <a href="the-easy-search-tool.php">'Easy Search' tool</a>, which lets you very quickly build searches just by clicking (rather than typing commands).
	</td>
</tr>
<tr>
	<td valign="top">
		 10
	</td>
	<td>
		<strong>Search Button</strong>. Once you have entered a range of verses, or a search command, in the input box (#2 above), click this button to lookup the verses, or perform the search. (Or you can just press enter when the cursor is in the input box).
	</td>
</tr>
<tr>
	<td valign="top">
		 11
	</td>
	<td>
			<strong> Quick Tips</strong>. To help you learn how to use Qur’an Tools' many functions, ‘quick tips’ are displayed on the home page. You can navigate through these using the “Next Tip” or “Previous Tip” buttons, click on the bold text to ‘type’ it into the input box, or click ‘Learn More About This’ to read a help page explaining this function in more detail. <strong>We strongly suggest you take the time to read all the quick tips, as they’re carefully designed to teach you to use Qur’an Tools in rapid time.</strong>
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