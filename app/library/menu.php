<?php

// return the current url
function current_page_url()
{
    $pageURL = 'http';
    if (isset($_SERVER['HTTPS']))
    {
        if ($_SERVER['HTTPS'] == 'on')
        {
            $pageURL .= 's';
        }
    }
    $pageURL .= '://';
    if ($_SERVER['SERVER_PORT'] != '80')
    {
        $pageURL .=
            $_SERVER['SERVER_NAME'] .
            ':' .
            $_SERVER['SERVER_PORT'] .
            $_SERVER['REQUEST_URI'];
    }
    else
    {
        $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }
    return $pageURL;
}

// save the URL to our usage database

if (isset($_SESSION['UID']))
{
    $url_to_save = current_page_url();

    // strip out host name if it appears in the current URL (and it should do)
    $url_to_save = str_ireplace(
        $config['main_app_url'],
        '',
        $url_to_save
    );

    $SQL = "INSERT INTO `USAGE` (`PAGE LOADED`, `USER ID`, `ACTUAL PAGE`, `CATEGORY`) VALUES ('" .
        db_quote($url_to_save) .
        "', '" .
        db_quote($_SESSION['UID']) .
        "', '', '')";

    db_query($SQL);
}

// save the path
$path = $_SERVER['REQUEST_URI'];

// do they have admin rights?
$administrator = $_SESSION['administrator'] ?? false;

include 'gtm_body.php';
?>
<div id="qt-menu" class="align-center">
    <ul>
        <li>
            <a id='home-page-link' href='/home.php'>
                <img class='qt-mini-logo' src='/images/qt-mini-logo.png' alt='Small QT Logo'>
            </a>
        </li>
        <?php
        // only show the browse menu if user is logged in
        if (is_logged_in_user())
        {
            echo "<li id='browse-menu'>";

            echo "  <a href=# class='top-menu'>Browse</a>";

            echo '  <ul>';
            echo "    <li><a class='MenuLink' href='/browse_sura.php'>Sura List</a></li>";
            echo "    <li><a class='MenuLink' href='/dictionary.php'>Dictionary</a></li>";

            echo "    <li><a class='MenuLink' href='/browse_root_usage.php'>Root Usage by Sura</a></li>";
            echo "    <li><a class='MenuLink' href='/word_associations.php'>Word Associations</a></li>";

            echo "    <li><a href='#'>Word Lists</a>";
            echo '  <ul>';
            echo "<li><a class='MenuLink' href='/counts/count_all_roots.php'>List Roots</a></li>";
            echo "<li><a class='MenuLink' href='/counts/count_all_lemmata.php'>List Lemmata</a></li>";
            echo "<li><a class='MenuLink' href='/counts/count_verbs.php'>List Verbs</a></li>";
            echo "<li><a class='MenuLink' href='/counts/count_nouns.php'>List Nouns</a></li>";
            echo "<li><a class='MenuLink' href='/counts/count_proper_nouns.php'>List Proper Nouns</a></li>";
            echo "<li><a class='MenuLink' href='/counts/count_loanwords.php'>List Loanwords</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo "<li><a href='#'>Intertextuality</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/intertextuality/intertextual_browser.php'>Intertextual Connections</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo '</ul>';

            echo '</li>';
        }

        // only show the menu if user is logged in

        if (is_logged_in_user())
        {
            echo "<li><a href='#' class='top-menu'>Charts</a>";

            echo '<ul>';
            echo "<li><a href='#'>Sura, Verse & Word Lengths</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/charts/chart_sura_length.php?TYPE=1'>Sura Length by Verses</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_sura_length.php?TYPE=2'>Sura Length by Words</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_sura_length.php?TYPE=3'>Mean Verse Length by Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_average_word_length.php'>Average Word Length by Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_length_characteristics.php?SURA=1'>Sura Verse Length Characteristics</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo "<li><a href='#'>Formulae</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/charts/chart_formulaic_density.php'>Formulaic Density per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_formulae_used_per_sura.php'>Number of Formulae Used per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_formulaic_diversity_per_sura.php'>Diversity of Formulae Used per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_formulae_in_common_by_sura.php'>Formulae in Common Between Suras</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo "<li><a href='#'>Rhymes</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/charts/chart_rhyme_verse_endings.php'>Verse Ending (Rhyme) Patterns per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_rhyme_number_of_patterns.php'>Number of Different Ending (Rhyme) Patterns per Sura</a></li>";
            // echo "<li><a class='MenuLink' href='/charts/chart_rhyme_homogeneity.php'>Verse Ending (Rhyme) Pattern Homogeneity per Sura</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo "<li><a href='#'>Intertextuality</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/charts/chart_intertextual_connections.php'>Verses with Intertextual Connections per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_intertextual_links_per_source.php'>Intertextual Links per Source</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            echo "<li><a href='#'>Words & Grammar</a>";
            echo '<ul>';
            echo "<li><a class='MenuLink' href='/charts/chart_loanwords_per_sura.php'>Loanwords per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/charts/chart_grammatical.php'>Grammatical Features by Sura</a></li>";
            echo '</ul>';
            echo "<span id='floatarrow'>▶︎</span>";
            echo '</li>';

            if (stripos($path, 'verse_browser.php?S'))
            {
                echo "<li><a class='MenuLink' href='/search_hits.php?S=" .
                    urlencode($_GET['S']) .
                    "&MODE=CHART'>Chart Search Hits</a></li>";
            }

            if (stripos($path, 'search_new.php?S'))
            {
                echo "<li><a class='MenuLink' href='/search_hits_new.php?S=" .
                    urlencode($_GET['S']) .
                    "&MODE=CHART'>Chart Search Hits</a></li>";
            }

            echo '</ul>';

            echo '</li>';
        }

        // only show the formulae menu if user is logged in

        if (is_logged_in_user())
        {
            echo "<li><a href='#' class='top-menu'>Formulae</a>";

            echo '<ul>';
            echo "<li><a class='MenuLink' href='/formulae/formulaic_density_summary_table.php'>Formulaic Density Summary Table</a></li>";
            echo "<li><a class='MenuLink' href='/formulae/formulaic_density_by_sura.php'>Formulaic Density and Usage per Sura</a></li>";
            echo "<li><a class='MenuLink' href='/formulae/list_formulae.php'>List All Formulae</a></li>";
            echo "<li><a class='MenuLink' href='/formulae/sura_formulae_analyse.php'>Cross Reference Formulae in Suras</a></li>";
            echo "<li><a class='MenuLink' href='/formulae/sura_formulae_analyse.php?INCOMMON=1&SURA=2&L=0&TYPE=ANY'>List Formulaic Continuities Between Suras</a></li>";
            echo '</ul>';

            echo '</li>';
        }

        // only show the rhyme menu if user is logged in

        if (is_logged_in_user())
        {
            echo "<li><a href='#' class='top-menu'>Rhymes</a>";

            echo '<ul>';
            echo "<li><a class='MenuLink' href='/rhyme/sura_rhyme_analysis.php'>Sura Rhyme Analysis with Verse Detail</a></li>";
            echo "<li><a class='MenuLink' href='/rhyme/rhyme_counts_per_sura.php'>Sura Rhyme & Verse Ending Statistics</a></li>";
            echo '</ul>';

            echo '</li>';
        }

        // only show the admin menu if user has admin status

        if ($administrator)
        {
            echo "<li id='admin-tools-menu'><a href='#' class='top-menu'>Admin Tools</a>";
            echo '<ul>';

            if ($administrator != 'WORD_FIXER')
            {
                echo "<li><a href='#'>Logs</a>";
                echo '<ul>';

                echo "<li><a class='MenuLink' href='/admin/page_usage_statistics.php'>Page Usage Statistics</a></li>";
                echo "<li><a class='MenuLink' href='/admin/login_logs.php'>Browse Login Logs</a></li>";
                echo "<li><a class='MenuLink' href='/admin/customer_statistics.php'>Customer Statistics</a></li>";
                echo "<li><a class='MenuLink' href='/admin/verse_search_logs.php'>Browse Verse and Search Activity Logs</a></li>";
                echo "<li><a class='MenuLink' href='/admin/failed_searches.php'>Failed Search Logs</a></li>";

                echo '</ul>';
                echo "<span id='floatarrow'>▶︎</span>";
                echo '</li>';

                if ($administrator == 'SUPERUSER')
                {
                    echo "<li><a class='MenuLink' href='/admin/user_management.php'>Users</a></li>";
                }

                if ($administrator == 'SUPERUSER')
                {
                    echo "<li><a class='MenuLink' href='/admin/parsing_tagger.php' class='top-menu'>Parsing Tagger</a></li>";
                }

                echo "<li><a href='#'>Correction Tools</a>";
                echo '<ul>';

                echo "<li><a class='MenuLink' href='/admin/word_correction_logs.php'>Word Correction List</a></li>";
                echo "<li><a class='MenuLink' href='/admin/lemmata_correction_tool.php'>Lemmata Correction Tool</a></li>";

                echo '</ul>';
                echo "<span id='floatarrow'>▶︎</span>";
                echo '</li>';
            }
            else
            {
                echo "<li><a href='#'>Correction Tools</a>";
                echo '<ul>';

                echo "<li><a class='MenuLink' href='/admin/word_correction_logs.php'>Word Correction List</a></li>";
                echo "<li><a class='MenuLink' href='/admin/lemmata_correction_tool.php'>Lemmata Correction Tool</a></li>";

                echo '</ul>';
                echo "<span id='floatarrow'>▶︎</span>";
                echo '</li>';
            }

            if ($administrator != 'WORD_FIXER')
            {
                echo "<li><a href='#' class='top-menu'>Translation Tagging</a>";

                echo '<ul>';

                echo "<li><a class='MenuLink' href='/admin/translation_word_tag_stats.php'>Translation Word Tagging Statistics</a></li>";

                if (stripos($path, 'verse_browser.php?'))
                {
                    if (isset($_GET['T']))
                    {
                        $TRANSLATOR_FOR_TAG_MODE = $_GET['T'];
                    }
                    else
                    {
                        if (isset($TRANSLATOR))
                        {
                            $TRANSLATOR_FOR_TAG_MODE = $TRANSLATOR;
                        }
                        else
                        {
                            $TRANSLATOR_FOR_TAG_MODE = '';
                        }
                    }

                    // depending on if translation tag mode is on or off, we'll either show a menu item to enter it or exit it

                    if (!stripos($path, 'TTM=Y'))
                    {
                        if (isset($_GET['V']))
                        {
                            echo "<li><a class='MenuLink' href='/verse_browser.php?V=" .
                                urlencode($_GET['V']) .
                                "&TTM=Y&T=$TRANSLATOR_FOR_TAG_MODE'>Edit Translation Word Tags</a></li>";
                        }
                    }
                    else
                    {
                        if (isset($_GET['V']))
                        {
                            echo "<li><a class='MenuLink' href='/verse_browser.php?V=" .
                                urlencode($_GET['V']) .
                                "&T=$TRANSLATOR_FOR_TAG_MODE'>Exit Translation Word Tagging Mode</a></li>";
                        }
                    }
                }

                echo '</ul>';
                echo "<span id='floatarrow'>▶︎</span>";
                echo '</li>';
            }

            echo '</ul>';
            echo '</li>';
        }

        // profile menu

        if (is_logged_in_user())
        {
            echo "<li id='my-profile-menu'><a href='#' class='top-menu'>My Profile</a>";

            echo '<ul>';

            echo "<li><a class='MenuLink user-name' ID='LOGGED_IN_NAME' href='#'>" .
                profile_menu_user_text($logged_in_user);

            if ($administrator == 'ADMIN')
            {
                echo "  <img src='/images/manager.png' alt='You have admin status' title='You have admin status' class='menu-icon'>";
            }
            if ($administrator == 'SUPERUSER')
            {
                echo "  <img src='/images/admin-superuser-icon.png' alt='You have superuser status' title='You have superuser status' class='menu-icon'>";
            }

            echo '</a></li>';

            echo "<li><a href='/preferences.php'>Preferences & Account</a></li>";

            // if they have bookmarks, offer the "My Bookmarks" link
            if (
                db_rowcount(
                    db_query(
                        "SELECT * FROM `BOOKMARKS` WHERE `User ID`='" .
                            db_quote($_SESSION['UID']) .
                            "' ORDER BY UPPER(`Name`)"
                    )
                ) > 0
            )
            {
                echo "<li><a class='MenuLink' href='/bookmark_manager.php'>My Bookmarks</a></li>";
            }

            echo "<li><a class='MenuLink' href='/tag_manager.php'>My Tags</a></li>";

            echo "<li><a class='MenuLink' href='/auth/logout.php' id='logout'>Log Out</a></li>";
            echo '</ul> </li>';
        }

        // help menu

        echo "<li id='help-menu'><a href='#' class='top-menu'>Help</a>";

        echo '<ul>';

        // work out which help page to send them to

        // first we look in HELP-PAGE-LINKS to see if we have the direct URL for an article on this page

        $sql = "SELECT `ARTICLE URL` 
                  FROM `HELP-PAGE-LINKS`
                 WHERE UPPER(`QT PAGE TITLE`) LIKE '" .
            db_quote(strtoupper(strtok($GLOBALS['windowTitle'], ':'))) .
            "'";

        $helpPageLink = db_return_one_record_one_field($sql);

        if ($helpPageLink != '')
        {
            echo "<li><a id='user-guide-link' href='$helpPageLink' target='_blank'>Get Help on This Page <img src='/images/help-green.png' class='menu-icon'></a></li>";
        }

        echo "<li><a id='user-guide-link' href='/help/welcome-to-quran-tools.php' target='_blank'>Getting Started</a></li>";
        
        echo "<li><a id='user-guide-link' href='/help/help-index.php' target='_blank'>Help Index</a></li>";

        echo "<li id='about-menu'><a href='#'>About</a>";

        echo '  <ul>';
        echo "    <li><a id='about-link' href='/about.php'>About Qur&rsquo;an Tools</a></li>";
        echo '  </ul>';
        echo "  <span id='floatarrow'>▶︎</span>";

        echo '</li>'; // #about-menu

        echo "<li id='legal-menu'><a href='#'>License</a>";

        echo '  <ul>';
        echo "    <li id='privacy-policy'><a href='" .
            QT_LICENSE_URL .
            "'>GNU Public License</a></li>";
        echo "    <li id='cookie-policy'><a href='" .
            QT_TERMS_URL .
            "'>Terms of Use</a></li>";
        echo '  </ul>';
        echo "  <span id='floatarrow'>▶︎</span>";

        echo '</li>'; // #legal-menu

        echo '</ul>';

        echo '</li>'; // #help-menu

        // search icon, if logged in

        if (is_logged_in_user())
        {
            echo '<li>';
            echo "<a href='#' id='menu-search'><img src='/images/qt-mini-search-icon.png' alt='Search logo' width='20' height='20'></a>";
            echo '</li>';
        }

        echo '  </ul>';
        echo "	<div class='mobile-warning'>
					Qur&rsquo;an Tools is not optimised for narrow screens
				</div>";
        echo '</div>'; // #qt-menu

        // draw the smart search bar over the top of the other toolbar

        echo "<div id='search-bar'>";

        echo "<div id='search-tool'>";

        echo "<span class='search-bar-span search-icon'>";
        echo "<img src='/images/qt-mini-search-icon.png' alt='Search logo'>";
        echo '</span>';

        echo "<form id=pickVerse action='/home.php' method=get name=FormName style='display:inline;'>";

        echo "<input id='miniSearchBox' type=text onKeyUp='processInput();' NAME=SEEK maxlength=250 autocomplete='off' placeholder='Verse, range, or search command'>";
        echo '</form>';

        echo "<span class='search-bar-span close-search-icon'>";
        echo "<img src='/images/qt-close.png' id='SEARCH_CLOSE_ICON' alt='Search close icon'>";
        echo '</span>';
        ?>

</div>
</div>

<div id='search-suggestions'>
</div>
<?php
//  It would be nice to get rid of this spacer and let the content on each page position itself, but since the menu is postion:fixed this can only be done with either flickering javascript or imprecise media queries
?>

<div class='main-menu-spacer'></div>