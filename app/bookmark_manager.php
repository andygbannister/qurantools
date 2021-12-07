<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// bookmark wiping
$message       = "";
$message_class = "message-warning";

if (isset($_GET["DB"]))
{
    if ($_GET["DB"] != "")
    {
        if ($_GET["DB"] == "ALL")
        {
            // for now, we leave this functionality switched off for safety
            // db_query("DELETE FROM `BOOKMARKS` WHERE `User ID`='".db_quote($_SESSION['UID'])."'");
            // $message = "All bookmarks deleted";
        }
        else
        {
            db_query("DELETE FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET["DB"]) . "'");
            $message       = "Bookmark deleted";
            $message_class = "message-success";
        }
    }
}

?>	
<!DOCTYPE html>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Bookmarks Manager");
        ?>
		
	<script type="text/javascript" src="library/js/persistent_table_headers.js"></script>	
		
	  <script>
		  
	  function delete_single_bookmark(item, timecode)
		{
			if (confirm ("Delete the bookmark '" + item + "'?"))
			{
 				window.location.assign("bookmark_manager.php?DB="+timecode);
			}	
		}
		  
	</script>  
	  
	</head>
<body class='qt-site'>
<main class='qt-site-content'>
	<?php

    include "library/back_to_top_button.php";

    // menubar

    include "library/menu.php";

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
                    $bookmark_display_setting = "";
                }
                else
                {
                    // check the bookmark isn't numeric
                    if (is_numeric($_POST["BOOKMARK_RENAME"]))
                    {
                        $message                  = "You cannot use a number as a bookmark. Please use letters, or a mix of letters and numbers.";
                        $bookmark_display_setting = "";
                    }
                    else
                    {
                        // check bookmark isn't a sura name
                        $check_sura_name = db_query("SELECT * FROM `SURA-DATA` WHERE UPPER(`English Name`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Arabic Name`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Alternative Name 1`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "' OR UPPER(`Alternative Name 2`)='" . db_quote(strtoupper($_POST["BOOKMARK_RENAME"])) . "'");

                        if (db_rowcount($check_sura_name) > 0)
                        {
                            $message                  = "You cannot create a bookmark with the same name as a sura. Please try again.";
                            $bookmark_display_setting = "";
                        }
                        else
                        {
                            // rename bookmark
                            $message       = "Bookmark renamed to '" . htmlentities($_POST["BOOKMARK_RENAME"] . "'");
                            $message_class = "message-success";

                            $renameCheck = db_query("UPDATE `BOOKMARKS` SET `Name`='" . db_quote($_POST["BOOKMARK_RENAME"]) . "' WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `Timestamp`='" . db_quote($_GET['R']) . "'");

                            $bookmark_display_setting = "";
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

                echo "<form action='bookmark_manager.php?R=" . $_GET["R"] . "' ID=formGETKEY method=POST>";

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

    echo "<div align=center><h2 class='page-title-text'>My Bookmarks</h2>";

    if ($message != "")
    {
        echo "<div class='message $message_class'>$message</div>";
    }

    $result = db_query("SELECT * FROM `BOOKMARKS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY UPPER(`Name`)");

    echo "<table class='hoverTable persist-area fixedTable'>";

    echo "<thead class='persist-header table-header-row'>";

    echo "<tr class='table-header-row'>";

    echo "<th bgcolor=#c0c0c0 align=center width=350><b>Bookmark Name</b></th>";
    echo "<th bgcolor=#c0c0c0 align=center width=350><b>Refers To</b></th>";
    echo "<th bgcolor=#c0c0c0 align=center width=50>&nbsp;</th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td width=350><a href='home.php?SEEK=" . urlencode($ROW["Contents"]) . "' class=linky>" . htmlentities($ROW["Name"]) . "</a></td>";

        echo "<td width=350><a href='home.php?SEEK=" . urlencode($ROW["Contents"]) . "' class=linky>";

        if (strlen($ROW["Contents"]) > 50)
        {
            echo htmlentities(substr($ROW["Contents"], 0, 50) . " ...");
        }
        else
        {
            echo htmlentities($ROW["Contents"]);
        }

        echo "</a></td>";

        echo "<td><a href='bookmark_manager.php?R=" . $ROW["Timestamp"] . "'><img src='images/edit.gif' title='Rename bookmark'></a>&nbsp;<a href='#' onclick=\"delete_single_bookmark('" . htmlspecialchars(addslashes($ROW["Name"]), ENT_QUOTES) . "', '" . htmlentities($ROW["Timestamp"]) . "');\"><img src='images/delete.gif' title='Delete this bookmark'></a></td>";

        echo "</tr>";
    }

    if (db_rowcount($result) > 0)
    {
        echo "<tr><td colspan=3 align=center><b>$i bookmark" . plural($i) . "</b></td></tr>";
    }
    else
    {
        echo "<tr><td colspan=3 width=400px align=center><br><p><b>No bookmarks to show</b></p><p><a href='docs/user_guide_bookmarks.php' class=linky-light>Read about how to create and manage bookmarks</a></p><br></td></tr>";
    }

    echo "</tbody>";

    echo "</table>";

    // print footer

    include "library/footer.php";

?>

</body>

</html>