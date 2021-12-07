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
    window_title("Help: Welcome to Qur’an Tools");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Welcome to Qur’an Tools</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
                <p>

                <p>
                    Welcome to <strong>Qur’an Tools</strong>, a powerful piece of software that enables more efficient critical study of the Qur’an. It combines a host of ground-breaking features such as a <a href="viewing-a-quran-passage.php">powerful Qur’an browser</a>, <a href="performing-a-basic-search.php">fast and flexible searching</a>, <a href="preferences.php">customization options</a>, and one-click access to beautiful charting and <a href="analysing-verses-and-search-results.php">analysis functions</a>. Qur’an Tools also contains a wealth of tools to help you explore <a href="11-the-dictionary-tool">qur’anic vocabulary</a>, plus resources for <a href="formulaic-analysis.php">easy formulaic analysis</a> and other scholarly analysis.
                </p>
                <p>
                    This user guide has been written to introduce you to the various features of Qur’an Tools and to help you quickly understand how to get the most from the software. We recommend you take the time to read through it and try the various examples it gives, as this will quickly help you understand what Qur’an Tools can do and begin using its powerful tools in your own critical study of the Qur’an.
                </p>
                <h2>What Would You Like to Do?</h2>
                <h3>I’m new to Qur’an Tools. Help me get started!</h3>
                <ul>
                    <li><a href="home-page.php">The Qur’an Tools home page</a></li>
                    <li><a href="menu-bar.php">The menu bar</a></li>
                    <li><a href="looking-up-a-passage.php">Looking up a Qur’an passage</a></li>
                    <li><a href="viewing-a-quran-passage.php">Viewing a Qur’an passage</a></li>
                    <li><a href="performing-a-basic-search.php">Performing a basic search</a></li>
                    <li><a href="browsing-the-sura-list.php">Browsing the sura list sura</a></li>
                    <li><a href="the-dictionary-tool.php">Using the dictionary to look up a word</a></li>
                </ul>
                <h3>I want to do something specific. Talk me through each function and help me understand it.</h3>
                <ul>
                    <li><strong>Core Functions</strong></li>
                    <ul>
                        <li><a href="home-page.php">The Qur’an Tools home page</a></li>
                        <ul>
                            <li><a href="the-easy-search-tool.php">Using the 'Easy Search' tool</a></li>
                            <li><a href="bookmarks.php">Saving verses or search results as a bookmark</a></li>
                        </ul>
                        <li><a href="the-verse-browser-in-detail.php">The verse browser in detail</a></li>
                        <li><a href="advanced-searching.php">Advanced Qur’an Searching</a></li>
                        <li><a href="analysing-verses-and-search-results.php">Analysing and working with search results</a></li>
                    </ul>
                </ul>
                <ul>
                    <li><strong>Browse Menu</strong></li>
                    <ul>
                        <li><a href="browsing-the-sura-list.php">Sura List</a></li>
                        <li><a href="root-usage-by-sura.php">Root Usage by Sura</a></li>
                        <li><a href="the-dictionary-tool.php">Dictionary</a></li>
                        <li><a href="word-association-tool.php">Word Association Tool</a></li>
                        <li>Word Lists</li>
                        <ul>
                            <li><a href="word-lists-roots.php-roots">List Roots</a></li>
                            <li><a href="word-lists-lemmata.php">List Lemmata</a></li>
                            <li><a href="list-all-verbs.php">List Verbs</a></li>
                            <li><a href="word-lists-nouns.php">List Nouns</a></li>
                        </ul>
                    </ul>
                </ul>
                <ul>
                    <li><strong>Charts Menu<br>
                        </strong></li>
                    <ul>
                        <li><a href="sura-length-chart.php">Sura and Verse Length</a></li>
                        <li>Formulae
                            <ul>
                                <li><a href="formulaic-density-by-sura-chart.php">Formulaic Density per Sura</a></li>
                                <li><a href="number-of-formulae-used-per-sura-chart.php">Number of Formulae Used per Sura</a></li>
                                <li><a href="formulaic-diversity-per-sura-chart.php">Diversity of Formulae Used per Sura</a></li>
                            </ul>
                        </li>
                        <li><a href="grammatical-features-by-sura-chart.php">Grammatical Features by Sura</a></li>
                    </ul>
                </ul>
                <ul>
                    <li><strong>Formulae Menu</strong></li>
                    <ul>
                        <li><a href="formulaic-density-summaries.php">Formulaic Density Summary Table</a></li>
                        <li><a href="formulaic-density-and-usage-statistics-per-sura.php">Formulaic Density and Usage per Sura</a></li>
                        <li><a href="list-all-formulae.php">List All Formulae</a></li>
                        <li><a href="cross-referencing-formulas-in-a-selection-of-verses.php">Cross Reference Formulae in Sura</a></li>
                    </ul>
                </ul>
                <ul>
                    <li><strong>My Profile Menu</strong></li>
                    <ul>
                        <li><a href="preferences.php">Preferences</a></li>
                        <li><a href="bookmarks-manager.php">My Bookmarks</a></li>
                    </ul>
                </ul>

                </p>

            </section>

        </div>

        <?php

        include "library/footer.php";

        ?>

</body>

</html>