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
    window_title("Help: Bookmarks Manager");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Bookmarks Manager</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

<p>
	 The 
	<strong>Bookmarks Manager</strong> easily enables you to manage your lists of bookmarks (verse lookups or searches that you want to return to and work with again). Bookmarks are easily created simply by hitting the "Bookmarks" button at the top right of the verse browser window when looking at verses or search results.
</p>
<p>
	 To easily manage your bookmarks, simply choose ‘My Profile’ from the Qur’an Tools menu bar and then ‘My Bookmarks’. The Bookmarks Manager looks like this:
</p>
<p>
	<img src="images/bookmarks-manager.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table style="background-color: rgb(255, 255, 255);">
<tbody>
<tr>
	<td>
		 1
	</td>
	<td>
		<strong>Bookmark Name</strong>. The name you gave this bookmark. Click it to open the verses or search (see #2 below) in the verse browser.
	</td>
</tr>
<tr>
	<td>
		 2
	</td>
	<td>
		<strong>Refers To</strong>. The search command(s) or verse(s) the bookmark refers to. Click it to open the verses or search in the verse browser.<br>
	</td>
</tr>
<tr>
	<td>
		 3
	</td>
	<td>
		<strong>Edit or Delete Bookmark</strong>. Click <img src="images/edit.gif" class="noBdr" style="display: inline; margin: 0px;" width="16" height="16"> to edit the bookmark and change its name, or&nbsp;<img src="images/delete.gif" class="noBdr" style="display: inline; margin: 0px;" width="16" height="16"> to delete it.
	</td>
</tr>
</tbody>
</table>
<p>
<div class="callout">
	<strong>TIP</strong>
	<p>
		 Remember that you can use a bookmark in a number of ways:
	</p>
	<ol>
		<li>Click 'Bookmarks' and then pick a bookmark on Qur’an Tools’ home page, to quickly access it.</li>
		<li>Type the bookmark’s name directly into the search box.</li>
		<li>Use it to limit a search; for example, this search will find every instance of the Arabic root <em>qwl</em>&nbsp;in the verses defined by my bookmark “Iblis and Adam Stories":<br>
		<img src="images/bookmarks-search.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "></li>
	</ol>
</div>
</p>
                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>