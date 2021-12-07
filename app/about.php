<?php

// general about page.

session_start();
session_regenerate_id();

require_once 'library/config.php';
require_once 'library/functions.php';

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    require 'library/standard_header.php';
    window_title("About");
    ?>
</head>

<body class='qt-site about' id='about'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo">
                <h2 class='page-title-text'>About Qur&rsquo;an Tools</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>Formulaic analysis is based upon the tools and techniques in<br><a
                        href='help/an-oral-formulaic-study-of-the-quran.php'
                        class=linky-light><i>An Oral-Formulaic Study of the Qur’an</i> (New York: Lexington, 2017
                        [2014])</a> by
                    Dr. Andrew G. Bannister.</p>

                <p>Qur’an transliteration is based upon the Tanzil Qur’an Text (Uthmani, version 1.0.2); Copyright
                    ©2008-2009
                    <a href='http://tanzil.info' target='_blank' class=linky-light>Tanzil.info</a>;<br>License: Creative
                    Commons BY-ND 3.0.
                </p>

                <p>
                    Qur’an Tools makes some use of the <a href='http://corpus.quran.com' target='_blank'
                        class=linky-light>Quranic Arabic Corpus</a> developed at the University of Leeds by Kais Dukes.
                    It is released under the <a href='http://corpus.quran.com/license.jsp' class=linky-light>GNU
                        License</a>.
                </p>

                <p>
                    Dictionary data derived from <a href='http://www.studyquran.co.uk/PRLonline.htm'
                        class=linky-light>Project
                        Root List</a>, which has digitised several classical Arabic dictionaries (<i>al-Mufradāt fī
                        gharīb
                        al-Qurʾān</i>, <i>Lisān al-ʿarab</i>, <i>Tāj al-ʿarūs min jawāhir al-qāmūs</i>, and <i>An
                        Arabic-English
                        Lexicon</i> by E.W Lane) and made the data publicly available.
                </p>

                <p>
                    Sura provenance information (Meccan or Medinan) is assigned via the commonly used Nöldeke-Schwally
                    system (see Neal Robinson, <i>Discovering the Qur’an: A Contemporary Approach to a Veiled Text</i>
                    (London: SCM, 2003) pp60-96 esp. p77).</p>

            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>