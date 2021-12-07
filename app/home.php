<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/quick_tips.php';

function check_for_sura_names_used_as_references($sura_name, $sura_number)
{
    global $SEEKING;

    // the trick is not to do a straight replace (sura name for sura number) in case they're using
    // a sura name as part of a search (e.g. searching for ENGLISH: Joseph)
    // So we look for criteria that give away that they're using sura names as references (e.g. exact match; followed by : or preceded by ;

    if (stripos(strtoupper($SEEKING), $sura_name) !== false)
    {
        $SEEKING = str_ireplace($sura_name . ":", $sura_number . ":", strtoupper($SEEKING));

        $SEEKING = str_ireplace($sura_name . ";", $sura_number . ";", strtoupper($SEEKING));

        $SEEKING = str_ireplace(";" . $sura_name, ";" . $sura_number, strtoupper($SEEKING));

        $SEEKING = str_ireplace("; " . $sura_name, "; " . $sura_number, strtoupper($SEEKING));

        if (strtoupper($SEEKING) == $sura_name)
        {
            ($SEEKING = $sura_number);
        }
    }
}

function create_command_help_link($command)
{
    return " class=\"yellow-tooltip\" data-tipped-options=\"ajax: {url:'/ajax/ajax_home_page_commands_help_tips.php', data:{COMMAND:'$command'}}, hideOn: {element: 'mouseleave'}\"";
}

function clean_excess_history()
{
    // only keep last XX items
    $MAX_HISTORY = 50;

    $result = db_query("SELECT * FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Timestamp`");

    if (db_rowcount($result) > $MAX_HISTORY)
    {
        db_query("DELETE FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Timestamp` LIMIT " . (db_rowcount($result) - $MAX_HISTORY));
    }
}

$message       = "";
$message_class = "message-warning";

// load users table and load preferences
$result = db_query("SELECT * FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "'");

if (db_rowcount($result) > 0)
{
    $ROW                      = db_return_row($result);
    $user_preference_keyboard = $ROW["Preferred Keyboard Direction"];
}
else
{
    $user_preference_keyboard = "LTR";
}

// clean excess history

clean_excess_history();

// history wiping

$history_display_setting = "none";

if (isset($_GET["DH"]) && $_GET["DH"] != "")
{
    if ($_GET["DH"] == "ALL")
    {
        db_query("DELETE FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'");
    }
    else
    {
        db_query("DELETE FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET["DH"]) . "'");
        $history_display_setting = "";
    }
}

// bookmark wiping
$bookmark_display_setting = "none";

if (isset($_GET["DB"]))
{
    if ($_GET["DB"] != "")
    {
        if ($_GET["DB"] == "ALL")
        {
            db_query("DELETE FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'");
        }
        else
        {
            db_query("DELETE FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET["DB"]) . "'");

            $bookmark_display_setting = "";
        }
    }
}

if (isset($_GET["SEEK"]) && $_GET["SEEK"] != "")
{
    // ================== PROCESS INPUT ====================

    $SEEKING = $_GET["SEEK"];

    // strip excess white space

    $SEEKING = preg_replace('/\s+/', ' ', $SEEKING);

    // have they typed a bookmark
    $result = db_query("SELECT * FROM `BOOKMARKS` WHERE LOWER(`NAME`)='" . db_quote(strtolower($SEEKING)) . "' AND `User ID`='" . db_quote($_SESSION['UID']) . "'");

    if (db_rowcount($result) > 0)
    {
        // substitute the bookmark code for the name
        $ROW     = db_return_row($result);
        $SEEKING = $ROW["Contents"];
    }
    else
    {
        // update history

        clean_excess_history();

        db_query("DELETE FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `History Item`='" . db_quote($_GET["SEEK"]) . "'");

        db_query("INSERT INTO `HISTORY` (`History Item`, `User ID`) VALUES ('" . db_quote($_GET["SEEK"]) . "', '" . db_quote($_SESSION['UID']) . "')");
    }

    // STRIP OFF ANY "Q"s or "cfs" in REFERENCES
    $pattern = "/(cf\\.?\\s?)?([qQsS]|[cC][fF])\\.?\\s?(\\d+)/";
    $subst   = "$3";
    $SEEKING = preg_replace($pattern, $subst, $SEEKING);

    // replace sura<space><reference> or surah<space><reference with just <reference> (to allow e.g. "surah 2" as a look up
    $pattern = "/,\s?(\\d+:)/";
    $subst   = ";$1";
    $SEEKING = preg_replace($pattern, $subst, $SEEKING);

    // if they use commas instead of semi colons, fix this
    $pattern = "/([s|S][u|U][r|R][a|A][h|H]?) (\d)/";
    $subst   = "\\2";
    $SEEKING = preg_replace($pattern, $subst, $SEEKING);

    // surah name cleverness

    $sura_result = db_query("SELECT * FROM `SURA-DATA`");

    for ($i = 0; $i < db_rowcount($sura_result); $i++)
    {
        $ROW = db_return_row($sura_result);

        // we use a function to do the substitution, as we can then pass it various versions of the sura name to match against

        // try the English name
        check_for_sura_names_used_as_references(strtoupper($ROW["English Name"]), $ROW["Sura Number"]);

        // try the Arabic sura name
        check_for_sura_names_used_as_references(strtoupper($ROW["Arabic Name"]), $ROW["Sura Number"]);

        // try the Arabic sura name with spaces rather than hyphens
        check_for_sura_names_used_as_references(strtoupper(str_ireplace("-", " ", $ROW["Arabic Name"])), $ROW["Sura Number"]);

        // try the Arabic sura name with hyphens rather than spaces
        check_for_sura_names_used_as_references(strtoupper(str_ireplace(" ", "-", $ROW["Arabic Name"])), $ROW["Sura Number"]);

        if ($ROW["Alternative Name 1"] != "")
        {
            // try the alternative name (1)
            check_for_sura_names_used_as_references(strtoupper($ROW["Alternative Name 1"]), $ROW["Sura Number"]);

            // try the alternative name (1) with spaces rather than hyphens
            check_for_sura_names_used_as_references(strtoupper(str_ireplace("-", " ", $ROW["Alternative Name 1"])), $ROW["Sura Number"]);

            // try the alternative name (1) with hyphens rather than spaces
            check_for_sura_names_used_as_references(strtoupper(str_ireplace(" ", "-", $ROW["Alternative Name 1"])), $ROW["Sura Number"]);
        }

        if ($ROW["Alternative Name 2"] != "")
        {
            // try the alternative name (2)
            check_for_sura_names_used_as_references(strtoupper($ROW["Alternative Name 2"]), $ROW["Sura Number"]);

            // try the alternative name (2) with spaces rather than hyphens
            check_for_sura_names_used_as_references(strtoupper(str_ireplace("-", " ", $ROW["Alternative Name 2"])), $ROW["Sura Number"]);

            // try the alternative name (2) with hyphens rather than spaces
            check_for_sura_names_used_as_references(strtoupper(str_ireplace(" ", "-", $ROW["Alternative Name 2"])), $ROW["Sura Number"]);
        }
    }

    // work out which mode of verse browser to load

    if (is_numeric(substr($SEEKING, 0, 1)))
    {
        header("Location: /verse_browser.php?V=$SEEKING");
        exit;
    }
    else
    {
        header("Location: /verse_browser.php?S=" . urlencode($SEEKING));
    }
    exit;
}
?>
<!DOCTYPE html>
<html id='home'>

<head>

    <?php
            include 'library/standard_header.php';
            window_title("Home");
        ?>
    <script type="text/javascript" src="home.js"></script>

    <!-- Load the files needed to run Lightview pop up windows -->
    <script type="text/javascript" src="library/js/lightview/spinners/spinners.min.js"></script>
    <script type="text/javascript" src="library/js/lightview/lightview/lightview.js"></script>
    <link rel="stylesheet" type="text/css" href="library/js/lightview/css/lightview/lightview.css" />

    <!-- Load files needed to run 'tipped' tooltips and pop ups -->
    <?php
            // tipped.js is loaded with commonjs in standard_header.php -->
        ?>
    <script type="text/javascript" src="library/js/quick_tips.js"></script>
    <script type="text/javascript" src="library/js/click_sura_picker_number.js"></script>

    <!-- use an AJAX call to populate the root list/picker (for speed + less flicker) -->
    <script>
        $(document).ready(function() {

            // 		deal with clicking a number in the sura picker	
            previouslyHighlightedSura = 0;
            $('.suraPickerNumber').click(function(e) {
                $('#aya-picker-container').show();
                e.preventDefault(); // prevent page scrolling
            });

            $('#roots-container').load("ajax/ajax_populate_home_page_root_list.php");
            <?php
            if (user_wants_quick_tips($logged_in_user))
            {
                // the 'null' sends a null event into show_quick_tip
                echo "show_quick_tip(null, " . get_current_quick_tip_id($logged_in_user) . ")";
            } ?>
        });
    </script>

    <?php

     ?>

    <!-- if they have arrived here using the back button, -->
    <!-- SubPageVisited will be set, telling us to reload the page -->
    <!-- we have to trap this, as otherwise the history list won't get refreshed properly -->
    <script type="text/javascript">
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload()
            }
        };

        // set some javascript variables we will need later on from PHP 
        var bookmarkDisplaySetting = '<?php echo $bookmark_display_setting ?>';
        var historyDisplaySetting = '<?php echo $history_display_setting ?>';
        var keyboard_direction = '<?php echo $user_preference_keyboard ?>';
    </script>
</head>

<body class='qt-site'>
    <main class='qt-site-content' id='qt-site-main'>
        <?php

    function substitute_translit($letter)
    {
        if ($letter == "H")
        {
            return "ḥ";
        }
        if ($letter == "D")
        {
            return "ḍ";
        }
        if ($letter == "Z")
        {
            return "ẓ";
        }
        if ($letter == "$")
        {
            return "sh";
        }
        if ($letter == "S")
        {
            return "ṣ";
        }
        if ($letter == "T")
        {
            return "ṭ";
        }
        return "";
    }

    // menubar

    include "library/menu.php";
    include "library/arabic.php";
    include "library/transliterate.php";

    // LOAD HISTORY
    $resultHistory = db_query("SELECT * FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Timestamp`");

    // LOAD TAGS
    $resultTags = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Tag Name`");

    // BOOKMARK RENAME/RENAMING
    if (!isset($_POST["BOOKMARK_RENAME_CANCEL"]))
    {
        $_POST["BOOKMARK_RENAME_CANCEL"] = "";
    } // trap if POST var not set
    if (!isset($_POST["BOOKMARK_RENAME"]))
    {
        $_POST["BOOKMARK_RENAME"] = "";
    } // trap if POST var not set

    if (isset($_GET["R"]) && $_POST["BOOKMARK_RENAME_CANCEL"] == "")
    {
        // check bookmark exists
        $result = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET['R']) . "'");

        if (db_rowcount($result) > 0)
        {
            // has the rename form already been completed?
            if ($_POST["BOOKMARK_RENAME"] != "")
            {
                // check the new name doesn't already exist

                $duplicateCheck = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Name`='" . db_quote($_POST["BOOKMARK_RENAME"]) . "'");

                if (db_rowcount($duplicateCheck) > 0)
                {
                    $message                  = "There is already a bookmark called '" . htmlentities($_POST["BOOKMARK_RENAME"]) . "'.";
                    $bookmark_display_setting = "none";
                }
                else
                {
                    // check the bookmark isn't numeric
                    if (is_numeric($_POST["BOOKMARK_RENAME"]))
                    {
                        $message                  = "You cannot use a number as a bookmark. Please use letters, or a mix of letters and numbers.";
                        $bookmark_display_setting = "none";
                    }
                    else
                    {
                        // check bookmark isn't a sura name
                        $check_sura_name = db_query("SELECT * FROM `SURA-DATA` WHERE UPPER(`English Name`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Arabic Name`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Alternative Name 1`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Alternative Name 2`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "'");

                        if (db_rowcount($check_sura_name) > 0)
                        {
                            $message                  = "You cannot create a bookmark with the same name as a sura. Please try again.";
                            $bookmark_display_setting = "none";
                        }
                        else
                        {
                            // rename bookmark
                            $message       = "Bookmark renamed to '" . htmlentities($_POST["BOOKMARK_RENAME"] . "'");
                            $message_class = "message-success";

                            $renameCheck = db_query("UPDATE `BOOKMARKS` SET `Name`='" . db_quote($_POST["BOOKMARK_RENAME"]) . "' WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET['R']) . "'");

                            $bookmark_display_setting = "none";
                        }
                    }
                }
            }
            else
            {
                // grab database row
                $ROW = db_return_row($result);

                echo "<h2 class='page-title-text'>Rename Bookmark</h2>";

                echo "<div class='form' id='RegistrationForm'>";

                echo "<form action='home.php?L=" . $_GET["L"] . "&R=" . $_GET["R"] . "' ID=formGETKEY method=POST>";

                echo "<p class='bigger-message'>To rename the bookmark currently named <b>'" . htmlentities($ROW["Name"]) . "'</b>, simply enter a new name below.</p>";

                echo "<input type='text' NAME=BOOKMARK_RENAME ID=BKRENAME size=50 autocomplete=off autofocus maxlength=50 placeholder='New Bookmark Name'>";

                echo "<button name=BOOKMARK_SAVE type=submit value=1>RENAME BOOKMARK</button>";

                echo "<button name=BOOKMARK_RENAME_CANCEL ID=cancelButton type=submit value='Cancel'>CANCEL</button>";

                echo "</form>";

                echo "</div>";

                include "library/footer.php";

                exit;
            }
        }
    }

    echo "<section id='home-content' class='page-content'>";

    // TODO: The flash has been designed for rolling out more widely across the site but
    // for now it is just hard-coded in the home page,
    include "library/flash.php";

    echo "  <div class='mainLogo'>";
    echo "    <img src='/images/logos/qt_home_page_400.png'>";
    echo "  </div>";

    // LOOKUP VERSE OR SEARCH INPUT FIELD

    // TODO: this form should probably not be called pick-verse; perhaps that was what it used to do, but now the search functionality is much smarter than just picking verses.
    echo "  <form id='pick-verse' action='home.php' method='get' name='FormName'>";
  echo "    <div id='search-controls'>";
  echo "      <div id='left-side-controls'>";
    echo "        <input id='inputText' type=text autofocus NAME='SEEK' ";

    if (isset($_GET["L"]))
    {
        echo "VALUE='" . htmlspecialchars($_GET["L"], ENT_QUOTES) . "' ";
    }

    echo "maxlength='250' autocomplete='off' placeholder='Verse, range, or search command'>";

    echo "        <div id='drop-down-controls'>";

    // LOAD BOOKMARKS
    $resultBookmark = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY UPPER(`Name`)");

    if (db_rowcount($resultBookmark) > 0)
    {
        echo "          <a href='#' id='bookmarks-anchor' class='pop-up-anchor linky'>Bookmarks</a>";
    }

    if (db_rowcount($resultHistory) > 0)
    {
        echo "          <a href='#' id='history-anchor' class='pop-up-anchor linky'>History</a>";
    }

    if (db_rowcount($resultTags) > 0)
    {
        echo "          <a href='#' id='tags-anchor' class='pop-up-anchor linky'>Tags</a>";
    }

    echo "          <a href='#' id='search-commands-anchor' class='pop-up-anchor linky'>Commands</a>";

    echo "          <a href='#' id='roots-anchor' class='pop-up-anchor linky'>Roots</a>";

    echo "          <a href='#' id='verse-picker-anchor' class='pop-up-anchor linky'>Verse Picker</a>";

    echo "          <a href='#' id='keyboard-anchor' class='pop-up-anchor linky'>Keyboard</a>";

    echo "        </div>";    // drop-down-controls
    echo "      </div>";      // left-side-controls

    echo "      <div id='right-side-controls'>";

    echo "				<a name='OKbutton' id='ok-button' href='#' onclick='$(\"#pick-verse\").submit();'>Search</a>
								<a href='easy_search.php' id='easy-search' class='linky'>Easy Search</a>";

    echo "      </div>";      // right-side-controls
    echo "    </div>";        // search-controls
    echo "  </form>";

    // ===================================

    // BOOKMARK LIST DIV

    if ($message != "")
    {
        echo "<div ID='message' class='message $message_class'>$message</div><br>";
    }
    else
    {
        echo "<div ID='message'></div>";
    }

    if (db_rowcount($resultBookmark) > 0)
    {
        echo "<div id='bookmarks-container' class='tipped-container' style='display: $bookmark_display_setting;'>";

        $plural = "";
        if (db_rowcount($resultBookmark) != 1)
        {
            $plural = "s";
        }

        echo "  <p>Click any item from your bookmark list.</p>";

        // scrolling div

        echo "  <div id='bookmarks-scroller' class='tipped-scroller'>";
        echo "    <table class='hoverTable'>";

        for ($i = 0; $i < db_rowcount($resultBookmark); $i++)
        {
            // grab next database row
            $ROW = db_return_row($resultBookmark);

            echo "    <tr>";
            echo "      <td>";
            echo "        <a href='#' class=\"yellow-tooltip linky\" title=\"<font size='2'><b>" . htmlentities($ROW["Name"]) . "</b><br>This bookmark denotes the following ";

            // if this bookmark denotes a search, the bookmark table has saved the results, allowing us to determine that this bookmark is a search
            if ($ROW["Search Dump"] == "")
            {
                echo "set of verses";
            }
            else
            {
                echo "search";
            }

            echo ":<br><font color='#707070'>" . htmlentities($ROW["Contents"]) . "</font></font>\" style='text-decoration: none' onclick=\"LoadBookmark('" . htmlentities($ROW["Contents"]) . "');\">" . ($i + 1) . ". " . htmlentities($ROW["Name"]) . "</a>";

            echo "        <span style='float:right;'>";
            echo "          <a href='#' onclick=\"AddText('" . htmlspecialchars(addslashes($ROW["Name"]), ENT_QUOTES) . "');\" title='Copy the bookmark name to the input/search field above'>";
            echo "            <img src='images/use.gif' >";
            echo "          </a>";
            echo "          <a href='home.php?L=";
            if (isset($_GET["L"]))
            {
                echo htmlentities($_GET["L"]);
            }
            echo "&R=" . htmlentities($ROW["Timestamp"]) . "'><img src='images/edit.gif'></a> <a href='#' onclick=\"delete_single_bookmark('" . htmlspecialchars(addslashes($ROW["Name"]), ENT_QUOTES) . "', '" . htmlentities($ROW["Timestamp"]) . "');\"><img src='images/delete.gif'></a></span>";
            echo "      </td>";
            echo "    </tr>";
        }
        echo "    </table>";

        echo "    <a href='#' class='delete linky' onclick='delete_all_bookmarks()'>
								Delete all bookmarks
							</a>";

        echo "  </div>";          // #bookmarks-scroller
        echo "</div>";          // #bookmarks-container
    }

    // ===================================

    // HISTORY LIST DIV

    $resultHistory = db_query("SELECT * FROM `HISTORY` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY `Timestamp` DESC");

    if (db_rowcount($resultHistory) > 0)
    {
        echo "<div id='history-container' class='tipped-container' style='display: $history_display_setting;'>";

        $plural = "";
        if (db_rowcount($resultHistory) != 1)
        {
            $plural = "es";
        }

        echo "  <p>Click any item from your last " . db_rowcount($resultHistory) . " search$plural.</p>";

        // scrolling div

        echo "  <div id='history-scroller' class='tipped-scroller'>";
        echo "    <table class='hoverTable'>";

        for ($i = 0; $i < db_rowcount($resultHistory); $i++)
        {
            // grab next database row
            $ROW = db_return_row($resultHistory);

            // truncate the history item if necessary
            if (strlen($ROW["History Item"]) > 68)
            {
                $history_item_display = substr($ROW["History Item"], 0, 68) . " ...";
            }
            else
            {
                $history_item_display = $ROW["History Item"];
            }

            echo "    <tr>";
            echo "      <td>";

            echo "    	  <a href='#' class='linky' onclick=\"LoadHistory('" . htmlspecialchars(addslashes($ROW["History Item"]), ENT_QUOTES) . "');\">" . ($i + 1) . ". " . htmlentities($history_item_display) . "</a>";

            echo "        <span style='float:right';>";
            echo "          <a href='#' onclick=\"delete_single_history_item('" . htmlspecialchars(addslashes($ROW["History Item"]), ENT_QUOTES) . "', '" . htmlentities($ROW["Timestamp"]) . "');\">";
            echo "            <img src='images/delete.gif'>";
            echo "          </a>";
            echo "        </span>";

            echo "      </td>";
            echo "    </tr>";
        }
        echo "    </table>";
        echo "    <a href='#' class='delete linky' onclick='delete_all_history()'>
							  Delete all history
						  </a>";

        echo "  </div>";          // #history-scroller
        echo "</div>";          // #history-container
    }

// TAG LIST DIV

if (db_rowcount($resultTags) > 0)
{
    echo "<div id='tags-container' class='tipped-container'>";

    // scrolling div

    echo "  <div id='tags-scroller' class='tipped-scroller'>";
    echo "    <table class='hoverTable'>";

    for ($i = 0; $i < db_rowcount($resultTags); $i++)
    {
        // grab next database row
        $ROW = db_return_row($resultTags);

        echo "    <tr>";
        echo "      <td>";

        echo "<a href=# onclick=\"LoadHistory('" . htmlspecialchars(addslashes((stripos($ROW["Tag Name"], " ") > 0) ? "TAG:\"" . ($ROW["Tag Name"]) . "\"" : "TAG:" . $ROW["Tag Name"]), ENT_QUOTES) . "');\">";

        echo "<span class='pill-tag' style='background-color:#" . $ROW["Tag Colour"] . ";";

        // if the tag has a super light colour, we draw a black border (with bigger padding to clarify it)

        if ($ROW["Tag Lightness Value"] > 220)
        {
            echo "border: 1px solid #000000; padding-top: 2px; padding-bottom: 2px;";
        }
        else
        {
            echo "border-color:#" . $ROW["Tag Colour"] . ";";
        }

        if ($ROW["Tag Lightness Value"] > 130)
        {
            echo "color: black";
        }

        echo "'>";

        echo htmlentities($ROW["Tag Name"]);

        echo "</span>";

        echo "</a>";

        echo "      </td>";
        echo "    </tr>";
    }
    echo "    </table>";
    echo "    <a href='tag_manager.php' class='delete linky'>
							  Edit Tags
						  </a>";

    echo "  </div>";          // #history-scroller

    echo "</div>";
}

// ===================================

    // SEARCH COMMANDS DIV

    echo "<div id='search-commands-container' class='tipped-container'>";

    echo "  <p>Click on a command to add it to your current search above.</p>";

    echo "  <div id='search-commands'>";

    echo "    <div class='left'>";

    echo "      <h3>Words</h3>
				    	<a href='#' onclick=\"AddText('ROOT:');\"" . create_command_help_link("ROOT") . ">ROOT</a>
					    <a href='#' onclick=\"AddText('LEMMA:');\"" . create_command_help_link("LEMMA") . ">LEMMA</a>
    					<a href='#' onclick=\"AddText('TEXT:');\"" . create_command_help_link("TEXT") . ">TEXT</a>
		    			<a href='#' onclick=\"AddText('EXACT:');\"" . create_command_help_link("EXACT") . ">EXACT</a>";

    echo "      <h3>Grammatical Features</h3>
    					<a href='#' onclick=\"AddText('VERB');\"" . create_command_help_link("VERB") . ">VERB</a>
		    			<a href='#' onclick=\"AddText('NOUN');\"" . create_command_help_link("NOUN") . ">NOUN</a>
				    	<br>
    					<a href='#' onclick=\"AddText('NOMINATIVE');\"" . create_command_help_link("NOMINATIVE") . ">NOMINATIVE</a>
		    			<a href='#' onclick=\"AddText('ACCUSATIVE');\"" . create_command_help_link("ACCUSATIVE") . ">ACCUSATIVE</a>
				    	<a href='#' onclick=\"AddText('GENITIVE');\"" . create_command_help_link("GENITIVE") . ">GENITIVE</a>
    					<br>
		    			<a href='#' onclick=\"AddText('MASCULINE');\"" . create_command_help_link("MASCULINE") . ">MASCULINE</a>
				    	<a href='#' onclick=\"AddText('FEMININE');\"" . create_command_help_link("FEMININE") . ">FEMININE</a>
    					<br>
		    			<a href='#' onclick=\"AddText('SINGULAR');\"" . create_command_help_link("SINGULAR") . ">SINGULAR</a>
				    	<a href='#' onclick=\"AddText('DUAL');\"" . create_command_help_link("DUAL") . ">DUAL</a>
		    			<a href='#' onclick=\"AddText('PLURAL');\"" . create_command_help_link("PLURAL") . ">PLURAL</a>
				    	<br>
    					<a href='#' onclick=\"AddText('1P');\"" . create_command_help_link("1P") . ">1P</a>
		    			<a href='#' onclick=\"AddText('2P');\"" . create_command_help_link("2P") . ">2P</a>
				    	<a href='#' onclick=\"AddText('3P');\"" . create_command_help_link("3P") . ">3P</a>
    					<br>
    					<a href='#' onclick=\"AddText('INDICATIVE');\"" . create_command_help_link("INDICATIVE") . ">INDICATIVE</a>
		    			<a href='#' onclick=\"AddText('JUSSIVE');\"" . create_command_help_link("JUSSIVE") . ">JUSSIVE</a>
		    			<a href='#' onclick=\"AddText('SUBJUNCTIVE');\"" . create_command_help_link("SUBJUNCTIVE") . ">SUBJUNCTIVE</a>
				    	<br>
		    			<a href='#' onclick=\"AddText('FORM:');\"" . create_command_help_link("FORM") . ">FORM</a>
				    	<br>
    					<a href='#' onclick=\"AddText('HAPAX');\"" . create_command_help_link("HAPAX") . ">HAPAX</a>
		    			<a href='#' onclick=\"AddText('UNIQUE');\"" . create_command_help_link("UNIQUE") . ">UNIQUE</a>
		    			<a href='#' onclick=\"AddText('LOANWORD');\"" . create_command_help_link("LOANWORD") . ">LOANWORD</a>";

    echo "      <h3>Formulaic Language</h3>
    					<a href='#' onclick=\"AddText('FORMULA');\"" . create_command_help_link("FORMULA") . ">FORMULA</a>
		    			<a href='#' onclick=\"AddText('DENSITY');\"" . create_command_help_link("DENSITY") . ">DENSITY</a>
				    	<a href='#' onclick=\"AddText('@[');\">@[</a>
    					<a href='#' onclick=\"AddText('LENGTH');\"" . create_command_help_link("LENGTH") . ">LENGTH</a>
		    			<a href='#' onclick=\"AddText('TYPE');\"" . create_command_help_link("TYPE") . ">TYPE</a>
				    	<a href='#' onclick=\"AddText('ROOTF');\"" . create_command_help_link("ROOTF") . ">ROOT</a>
    					<a href='#' onclick=\"AddText('ROOT-ALL');\"" . create_command_help_link("ROOT-ALL") . ">ROOT-ALL</a>
	    				<a href='#' onclick=\"AddText('LEMMA');\"" . create_command_help_link("LEMMAF") . ">LEMMA</a>
		    			<a href='#' onclick=\"AddText(']');\">]</a>";

    echo "  	</div>";        // .left

    echo "  <div class='right'>";

    echo "  		<h3>Text Features</h3>
							<a href='#' onclick=\"AddText('PROVENANCE:');\"" . create_command_help_link("PROVENANCE") . ">PROVENANCE</a>
							<i>
							<a href='#' onclick=\"AddText('Meccan');\"" . create_command_help_link("MECCAN") . ">(Meccan)</a>
							<a href='#' onclick=\"AddText('Medinan');\"" . create_command_help_link("MEDINAN") . ">(Medinan)</a>
							</i>";

    echo "  		<h3>English Translations</h3>";

    echo "<a href='#' onclick=\"AddText('ENGLISH:');\"" . create_command_help_link("ENGLISH") . ">ENGLISH</a> ";

    $result_translation = db_query("SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`");

        for ($j = 0; $j < db_rowcount($result_translation); $j++)
        {
            $ROW = db_return_row($result_translation);

            echo "<a href='#' onclick=\"AddText('" . $ROW["TRANSLATION ALL CAPS NAME"] . ":');\"" . create_command_help_link($ROW["TRANSLATION ALL CAPS NAME"]) . ">" . $ROW["TRANSLATION ALL CAPS NAME"] . "</a>";

            echo(($j % 3) == 0 ? "<br>" : " ");
        }

    echo "  		<h3>Other Search Commands</h3>
							<a href='#' onclick=\"AddText('AND');\"" . create_command_help_link("AND") . ">AND</a> 
							<a href='#' onclick=\"AddText('OR');\"" . create_command_help_link("OR") . ">OR</a>
							<a href='#' onclick=\"AddText('NOT');\"" . create_command_help_link("NOT") . ">NOT</a><BR>
							<a href='#' onclick=\"AddText('RANGE');\"" . create_command_help_link("RANGE") . ">RANGE</a><BR>";
/*

                             <a href='#' onclick=\"AddText('FOLLOWED BY');\"".create_command_help_link("FOLLOWED BY").">FOLLOWED BY</a>
                            <a href='#' onclick=\"AddText('PRECEDED BY');\"".create_command_help_link("PRECEDED BY").">PRECEDED BY</a>
                            <a href='#' onclick=\"AddText('WITHIN');\"".create_command_help_link("WITHIN").">WITHIN</a>";
 */

    echo "  	</div>";        // .right

    echo "  </div>";          // #search-commands

    echo "</div>";            // #search-commands-container

    // ===================================

    // ROOT LIST DIV

    echo "<div id='roots-container' class='tipped-container'>";
    echo "</div>";

    // ===================================

    // VERSE PICKER

    echo "<div id='verse-picker-container' class='tipped-container' style='display:none'>";
    include "library/sura_picker.php";
    echo "</div>";            // #verse-picker-container

    // ===================================

    // KEYBOARD

    echo "<div id='keyboard-container' class='tipped-container'>";

    echo "  <div id='flipper' title='Flip keyboard'>
						<a href='#' onClick='flip_keyboard();' class='linky'>
							Flip Keyboard Direction ⇔
						</a>
					</div>";

    $buckwalter_letters = "A'btvjHxd*rzs\$SDTZEgfqklmnhwy aiuFNK~o";

    echo "  <div id='keyboard'>";

    for ($i = 0; $i < strlen($buckwalter_letters); $i++)
    {
        echo "    <div class='column'>";

        echo "      <div class='arabic-letter' onclick=\"AddText('" . return_arabic_word(substr($buckwalter_letters, $i, 1)) . "');\">";

        $letter = mb_substr($buckwalter_letters, $i, 1);

        echo return_arabic_word($letter);

        // spacing around vowels/diacritics
        if (stripos("aiuFNK~o ", $letter) !== false)
        {
            echo "&nbsp;";
        }

        echo "      </div>";    // .arabic-letter

        // substitute-translit row

        if (substitute_translit(substr($buckwalter_letters, $i, 1)) != "")
        {
            echo "      <div class='substitute-translit' onclick=\"AddText('" . substitute_translit(substr($buckwalter_letters, $i, 1)) . "');\">";
            echo substitute_translit(substr($buckwalter_letters, $i, 1));
        }
        else
        {
            echo "      <div>&nbsp;";   // need the space to ensure div doesn't collapse
        }

        echo "      </div>";    // .substitute-translit

        echo "      <div class='buckwalter-letter' onclick=\"AddText('" . htmlspecialchars(addslashes(substr($buckwalter_letters, $i, 1)), ENT_QUOTES) . "');\">";

        echo substr($buckwalter_letters, $i, 1) != " " ? substr($buckwalter_letters, $i, 1) : "&nbsp;";

        echo "      </div>";    // .buckwalter-letter
        echo "    </div>";      // .column
    }

    echo "  </div>";          // #keyboard

    // full character drop down

    echo "  <a href='#' id='extra-chars-anchor' class='pop-up-anchor linky'>
						Extra Characters
					</a>";

    echo "  <span style='float:right;'>
	          <a href='#' onclick='backspace();' class='linky'>
							Backspace
						</a>
					</span>";

    echo "  </div>";            // #keyboard-container

    echo "  </form>";

    echo "<div id='info-area'>";

    // ===================================

    // QUICK TIPS

    echo "  <section id='quick-tips'>";
    echo "  </section>";      // #quick-tips

    echo "</section>";        // #homeContent

    // report any messages
    include "library/messages.php";

    // A hack component that expands vertically if we need to push the bottom footer
    // down when a tipped.js component is too big
    echo "  <div id='tipped-expander'></div>";

    echo "</div>";            // #info-area

    include "library/footer.php";
?>

</body>

</html>