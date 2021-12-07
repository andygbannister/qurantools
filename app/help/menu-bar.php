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
    window_title("Help: Menu Bar");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Menu Bar</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>


                <p>

                <p>
                    Stretching across the top of every Qur’an Tools page is the menu bar, which looks like this:
                </p>
                <p>
                    <img src="images/menubar.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>
                    Each function is briefly described below:
                </p>
                <table cellpadding="4" cellspacing="4">
                    <tbody>
                        <tr>
                            <td width="18%">
                                <p>
                                    <img src="images/qt-icon.png" class="noBdr">
                                </p>
                            </td>
                            <td>
                                <strong>Return to the home page&nbsp;<br>
                                </strong>From anywhere within the program, you can always get back to the <a href="home-page.php">home page</a> by simply clicking this button.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <img src="images/menu-browse.png" class="noBdr">
                            </td>
                            <td>
                                <strong>Browse Menu&nbsp;<br>
                                </strong>Within this menu live a variety of different functions that let you browse lists of suras, words, and also the powerful <a href="the-dictionary-tool.php">dictionary</a> and <a href="word-association-tool.php">word association</a> tools.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <img src="images/menu-charts.png" class="noBdr">
                            </td>
                            <td>
                                <strong>Charts Menu<br>
                                </strong>Built into software are a wide variety of charting tools. Whether it is the length of suras, or the use of formulaic phraseology across the Arabic text there is a chart for you. Also keep a look out for the&nbsp;<img class="noBdr" style="margin: 0px; display: inline;" src="images/st.gif" alt="stats" width="25" height="14">&nbsp;button throughout the site; whenever it appears, you can always click it to quickly access a chart for that particular piece of data.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <img src="images/menu-formulae.png" class="noBdr">
                            </td>
                            <td>
                                <strong>Formulae Menu<br>
                                </strong>The Qur’an contains an extensive network of formulaic phrases &mdash; short repeated phrases used time and time again. Whether or not one believes that these provide <a href="an-oral-formulaic-study-of-the-quran.php">strong evidence for the Qur’an’s composition in oral performance</a>, the Qur’an’s formulaic diction is an important area of study. The software offers a <a href="formulaic-analysis.php">wide variety of tools</a> under the formulae menu that help you explore, analyse, and search this feature of qur’anic Arabic.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <img src="images/menu-rhyme.png" class="noBdr">
                            </td>
                            <td>
                                <strong>Rhymes Menu<br>
                                </strong>Some basic tools to allow you to explore the <a href='sura-rhyme-analysis.php'>rhyme structure</a> of the verses of the Qur'an..
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/menu-profile.png" class="noBdr">
                            </td>
                            <td valign="top">
                                Under the My Profile menu, you can do a number of things, including access the
                                <a href="preferences.php">preferences page</a> (to customise Qur’an Tools or change your user name or password); view and manage your <a href="bookmarks-manager.php">saved bookmarks</a>; or logout of Qur’an Tools if you have finished your session.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/menu-help.png" img="" class="noBdr">
                            </td>
                            <td valign="top">
                                <strong>Help Menu</strong> <br>
                                The help menu provides access to all the various training materials designed to help you get to grips with Qur’an Tools. The resources here will help you get started quickly &mdash; but also to learn how to delve deeper into all the incredibly powerful tools that Qur’an Tools has to offer.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <img src="images/menu-search.png" img="" class="noBdr">
                            </td>
                            <td id="quicksearch">
                                <strong>Quick Search&nbsp;<br>
                                </strong>To save you going via the home page, simply click on this search icon from anywhere within the site and type a search (or a range of verses) right into the menu bar to perform a new <a href="advanced-searching.php">search</a>. (You can also use the keyboard shortcut, CTRL+S). As you type into the search bar, Qur’an Tools will suggest items from your history or <a href="bookmarks.php">bookmark</a> names that match your search. You can click on any of these (or use the up and down cursor keys, followed by the enter key) to pick an item. In the example below, merely typing "i" is enough to bring up one of my frequently used bookmarks &mdash; all that is then needed to open it is a tap of the down cursor key, then press enter.<br><img src="images/search-bar.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 0px;">
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