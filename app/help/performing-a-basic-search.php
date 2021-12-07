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
    window_title("Help: Performing a Basic Search");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo"><br>Performing a Basic Search</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>
                <p>
                    Qur’an Tools has been designed to make it incredibly easy to search the qur’anic text, including the Arabic text as well as the English translations.
                </p>
                <p>
                    Let’s start by searching the English text of the Qur’an. Try typing the following into the search box on the home page:
                </p>
                <p>
                    <a href="/home.php?L=ROOT:ktb">ROOT:ktb</a>
                </p>
                <p>
                    The "ROOT" command tells Qur’an Tools that you want it to search for an Arabic root; and
                    <em>ktb</em> is a well-known qur'anic root that bears a number of <a href="/examine_root.php?ROOT=ktb">meanings</a>, including write, dictate, and book.
                </p>
                <div class="callout">
                    <strong>TIP</strong> You can specify a qur’anic root by specifying its English transliteration, Buckwalter encoding, or simply by typing the Arabic. For example, the three searches below are all equivalent.
                    <p>
                        <a href="/home.php?L=ROOT:رحم">ROOT:رحم</a>
                    </p>
                    <p>
                        <a href="/home.php?L=ROOT:rḥm">ROOT:rḥm</a>
                    </p>
                    <p>
                        <a href="/home.php?L=ROOT:rHm">ROOT:rHm</a>
                    </p>
                    <p>
                        If this all sounds a bit baffling at first, remember you can use the pop up keyboard on the home page to easily enter letters, or browse Qur’an Tools’s
                        <a href="/counts/count_all_roots.php">list of every root in the Qur’an</a> and search for a root simply by clicking on it there. You might also find <a href="arabic-letters-transliterations-and-encodings.php">this table</a> of Arabic letters, transliterations, and Buckwalter encodings helpful.
                    </p>
                </div>
                <p>
                    The search above for ROOT:ktb should have opened a browser window listing the 279 verses in which that root appears in the Qur’an. At the top of the browser window, you can see the search statistics:
                </p>
                <p>
                    <img src="images/hit-counter.png" alt="" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>
                    The
                    <strong>hits</strong> tells you how many words in the Qur’an match your search (and each one is highlighted in yellow); whilst the <strong>verses</strong> count tells you how many verses contain this root.
                </p>
                <p>
                    Once you have some search results displayed like this, you can analyse your results in a number of ways by clicking the "Analyse" button. Click it, and a dialogue box like this one will appear:
                </p>
                <p>
                    <img src="images/analysis.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>
                    Click
                    <strong>Count or Chart Search Hits</strong> and Qur’an Tools will count the number of times the search term appears in each sura of the Qur’an (and also the occurrences per 100 words). You can also, if you wish, then choose to see these same results plotted as a chart:
                </p>
                <p>
                    <img src="images/hits_chart.png" alt="chart_search_hits" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <p>
                    (Click on the blue 'ROOT:ktb' text at the top of the chart to be returned to your verse list).
                </p>
                <div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

                    <strong>TIP</strong> In most charts in Qur’an Tools, you can "drill down" by clicking on a column or pie slice. For example, in the chart above, if one were to click on the column representing search hits in sura 2, Qur’an Tools would open the verse browser and show you sura 2 with your search term highlighted. Press your web browser’s back button to return to the chart.
                </div>
                <p>
                    You can also analyse the words in the verses your search has produced. After clicking "Analyse" choose <strong>Analyse Words in These Verses</strong> and Qur’an Tools will count every word (not just your search term) that appears in these verses. Again, you have the option to view your results in chart form, if that is more helpful to you.
                </p>
                <div class="callout" style="margin-top:-0px; border: 2px solid #969696; padding: 4px 4px 4px 4px;">

                    <strong>TIP</strong> You will often find table views like this throughout Qur’an Tools. Most items in a table are clickable: in this case, click on any root word in the list to perform a new search for that word. (You can also click on <img src="/images/info.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="15" height="15"> to view the dictionary entry for that word, or the <img src="/images/st.gif" class="noBdr" style="display: inline; margin: 0px;" valign="middle" width="20" height="12"> button to chart its frequency across the whole Qur’an.
                </div>
                <hr>
                <h3>Searching the English Translations</h3>
                <p>
                    In the example search above, we searched the Qur’an for an Arabic root. But it’s also possible to search one or all of the English translations built into Qur’an Tools. For example, try the following search:
                </p>
                <p>
                    <a href="/home.php?L=ENGLISH:throne">ENGLISH:throne</a>
                </p>
                <p>
                    And Qur’an Tools will show you the 85 hits in 40 verses where this word appears. Notice how Qur’an Tools displays all four English translations in the browser window, simply because it has searched all&nbsp;&nbsp;of them. So what if would like to search just one specific translation? Well, that's easy. Try one of the following commands:
                </p>
                <p>
                    <a href="/home.php?L=ARBERRY:throne">ARBERRY:throne</a>
                </p>
                <p>
                    <a href="/home.php?L=YUSUFALI:throne">YUSUFALI:throne</a>
                </p>
                <p>
                    <a href="/home.php?L=PICKTHALL:throne">PICKTHALL:throne</a>
                </p>
                <p>
                    <a href="/home.php?L=SHAKIR:throne">SHAKIR:throne</a>
                </p>
                <p>
                    And Qur’an Tools will search just the translation you have specified.
                </p>
                <div>
                    <strong>TIP</strong> To search for an English
                    <em>phrase</em> in the translations (i.e. more than one word), surround your search term with quote marks. For example, try this search:
                    <p>
                        <a href="/home.php?L=ENGLISH:&quot;the world&quot;">ENGLISH:"the world"</a>
                    </p>
                </div>
                <hr>
                <h3>Searching for More Than One Thing</h3>
                <p>
                    So far, we have searched for just a single thing &mdash; an Arabic root, or an English word. But Qur’an Tools offers you far more power than that, by searching for multiple items. For example, suppose we want to find everywhere the Arabic roots
                    <em>ktb</em> and <em>ryb</em> appear in the same verse. We can combine two search terms by using the AND command:
                </p>
                <p>
                    <a href="/home.php?L=ROOT:ktb AND ROOT:ryb">ROOT:ktb AND ROOT:ryb</a>
                </p>
                <p>
                    You can even mix and match Arabic and English, like this:
                </p>
                <p>
                    <a href="/home.php?L=ROOT:ktb AND ENGLISH:book">ROOT:ktb AND ENGLISH:book</a>
                </p>
                <p>
                    This is just the beginning of what Qur’an Tools’s search tools can do &mdash; when you’re ready to dig deeper, read the
                    <a href="advanced-searching.php">Advanced Searching Guide</a> that describes every search command and function built into the software.
                </p>
                </p>










            </section>

        </div>

        <?php

        include "library/footer.php";

        ?>

</body>

</html>