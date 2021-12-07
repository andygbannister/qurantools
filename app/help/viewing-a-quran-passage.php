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
    window_title("Help: Viewing a Qur'an Passage");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Viewing a Qur'an Passage</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>
                <p>
                    When you ask Qur’an Tools to lookup verses or perform a search, the results of what you have asked to see will appear in a Qur’an browser window, just like this one:
                </p>
                <p>
                    <img src="images/vb-simple.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>

                    The browser window is divided into three sections&mdash;the Arabic text, a transliteration of the Arabic, and an English translation. There is a lot of functionality accessible from here, which is described in full in a <a href="the-verse-browser-in-detail.php">separate help document</a>. For now, here are some things to try.
                </p>
                <ul>
                    <li>Change suras by clicking on the arrows either side of the sura name.</li>
                    <br>
                    <li>Change the translation by picking an alternative one from the pick list that appears when you click the translation name.</li>
                    <br>
                    <li>Get back to the Qur’an Tools home page by clicking the back button on your browser or by clicking the logo in the top left corner of the <a href='menu-bar.php'>menu bar</a> at the top of the page.</li>
                    <br>
                    <li>Qur’an Tools makes it extremely easy to get more details about any word in the Qur’an &mdash; simply point your mouse at any Arabic or transliterated word (or if you’re using a smart-phone or tablet, touch it) and a palette like the one below will pop up with all kinds of useful linguistic information:</li>
                    <p>
                        <img src="images/instant.png" class="image-shadow">
                    </p>
                </ul>
                <p>
                    The Instant Details Palette is packed with useful information:
                </p>
                <ol>
                    <li>The word you have pointed at or touched.<br>&nbsp;</li>

                    <li>All the grammatical details about that word.<br>&nbsp;</li>

                    <li>The Arabic root and lemma (dictionary head word) lying behind this word. Click <img src="images/info.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="14" height="14"> to see the dictionary entry, or either of the chart buttons (<img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="18" height="12">) to see the root or lemma’s usage across the Qur’an.<br>&nbsp;</li>

                    <li>Quick search links, allowing you to easily search for this Arabic root, lemma, or even this exact inflection of the word elsewhere in the Qur’an. One-click searching is a powerful feature built right into Qur’an Tools: if you see a word that interests you whilst browsing the text, it is literally a case of just pointing-and-clicking to see every other qur’anic occurrence.<br>&nbsp;</li>

                </ol>
                <p>
                    To clear the Instant Details Palette, just move your mouse off the word, click the X icon in the top right of the palette or, if you’re using a smart-phone or tablet, touch somewhere else (e.g. a piece of white space) on the screen.
                </p>
                </p>


            </section>

        </div>

        <?php

        include "library/footer.php";

        ?>

</body>

</html>