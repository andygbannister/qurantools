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
    window_title("Help: Bookmarks");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Bookmarks</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>
                <p>
                    It is often the case that you may want to refer to the same verses of the Qur’an or perform the same search on multiple occasions. To save you having to retype references or search commands, Qur’an Tools allows you to create bookmarks, similar to the function built into many web browsers.
                </p>
                <p>
                    To create a bookmark, begin by viewing a series of Qur’an verses, either by
                    <a href="looking-up-a-passage.php">looking them up</a>, or by <a href="performing-a-basic-search.php">performing a search</a>.
                </p>
                <p>
                    Next, click the "Bookmark" button at the top-right of the verse browser window.
                </p>
                <p>
                    <img src="images/vb-bookmark.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>

                    A box will pop open, asking you to give this bookmark a name:
                </p>
                <p>
                    <img src="images/create-bookmark.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>
                    We recommend something meaningful and memorable, as that will make it easier to remember if you’re later doing things like
                    <a href="advanced-searching.php#RANGE">restricting a search to a bookmark</a>.
                </p>
                <p>
                    Once Qur’an Tools confirms the bookmark has been created, return to the home page (click 'HOME') on the menubar at the very top of the page). If you now click 'Bookmarks' below the main search field, the bookmark manager draw will pop open. It looks like this:
                </p>
                <p>
                    <img src="images/home_bookmarks.png" width=50% height=50% alt="" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">

                </p>
                <p>
                    From the bookmarks list, you can do a number of things:
                </p>
                <ul>
                    <li>Click on the bookmark's name to insert the list of verses (or the search command) it have saved directly into Qur’an Tools's search field. (After which you can just press ENTER to look up those verses).</li>
                    <br>
                    <li>Click the <img src="images/use.gif" class="noBdr" style="display: inline; margin: 0px;"> icon to insert the bookmark's name into Qur’an Tools's search field. This can be useful if you wish to type a bookmark name as part of a <a href="advanced-searching.php#RANGE">RANGE restricted search</a>, for instance.</li>
                    <div class="callout" style="margin-top:10px; margin-bottom:10px;  border: 2px solid #969696; padding: 4px 4px 4px 4px;">

                        <strong>TIP</strong>
                        <p>
                            If you wish, you can also simply type a bookmark's name manually into Qur’an Tools's search box and hit enter to look up those verses.
                        </p>
                    </div>
                    <li>Click the <img src="images/edit.gif" class="noBdr" style="display: inline; margin: 0px;"> icon to rename the bookmark.</li>
                    <br>
                    <li>Click the <img src="images/delete.gif" class="noBdr" style="display: inline; margin: 0px;"> icon to delete the bookmark. (If you wish to delete <em>all</em> your bookmarks, simply scroll to the bottom of the list of bookmarks and click 'Delete All Bookmarks').</li>
                    <div class="callout" style="margin-top:10px; margin-bottom:10px;  border: 2px solid #969696; padding: 4px 4px 4px 4px;">
                        <strong>TIP</strong>
                        <p>
                            If you have forgotten what a bookmark refers to, simply hover your pointer over the bookmark name and a tooltip will remind you.
                        </p>
                    </div>
                </ul>
                <hr>
                <p>
                    You can also view your
                    <a href="bookmarks-manager.php">entire list of bookmarks&nbsp;</a>by choosing ‘My Bookmarks’ from Qur’an Tools’s ‘My Profile’ menu.
                </p>
                </p>










            </section>

        </div>

        <?php

        include "library/footer.php";

        ?>

</body>

</html>