<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// how many tags can they create?

$MAX_NUMBER_OF_TAGS = 50;

// tag deletion

$message       = "";
$message_class = "message-warning";

if (isset($_GET["DT"]))
{
    if ($_GET["DT"] != "")
    {
        db_query("DELETE FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `ID`='" . db_quote($_GET["DT"]) . "'");
        db_query("DELETE FROM `TAGGED-VERSES` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `TAG ID`='" . db_quote($_GET["DT"]) . "'");
        $message       = "Tag deleted";
        $message_class = "message-success";
    }
}

?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Tags Manager");
        ?>

    <script src="library/js/jscolor.js"></script>
    <script type="text/javascript" src="library/js/persistent_table_headers.js"></script>

    <script>
        function delete_single_tag(tagID, tagName, count, plural) {
            message = "Delete the tag '" + tagName + "'?";

            if (count > 0) {
                message += " (This will remove the tag from the " + count + " verse" + plural +
                    " it is currently applied to).";
            }

            if (confirm(message)) {
                window.location.assign("tag_manager.php?DT=" + tagID);
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

    // creating a new tag

    // renaming and/or recolouring a tag

    if (isset($_GET["NT"]) && db_return_one_record_one_field("SELECT COUNT(*) FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "'") < $MAX_NUMBER_OF_TAGS)
    {
        echo "<h2 class='page-title-text'>Create a New Tag</h2>";

        echo "<div class='form' id='RegistrationForm'>";

        echo "<form action='tag_manager.php?CT=Y' ID=formGETKEY method=POST>";

        echo "<p class='bigger-message'>To create a new tag, give it a name below. You can also change its colour if you wish.</p>";

        echo "<input type='text' NAME=TAG_NAME ID=TAG_NAME size=50 autocomplete=off autofocus maxlength=30 placeholder='Type the name of your new tag'>";

        echo "<input class=jscolor NAME=TAG_COLOUR ID=TAG_COLOUR value=0000FF>";

        echo "<button name=TAG_SAVE type=submit value=1>CREATE TAG</button>";

        echo "<button name=TAG_CREAE_CANCEL ID=cancelButton type=submit value='Cancel'>CANCEL</button>";

        echo "</form>";

        echo "</div>";

        // echo "</div>";

        // echo "</div>";

        include "library/footer.php";

        exit;
    }

    // creating a new

    if (isset($_GET["CT"]))
    {
        if ($_GET["CT"] != "" && !isset($_POST["TAG_CREATE_CANCEL"]))
        {
            if ($_POST["TAG_NAME"] != "" && ctype_xdigit($_POST["TAG_COLOUR"]) && strlen($_POST["TAG_COLOUR"]) == 6)
            {
                // check it doesn't already exist

                $result = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND UPPER(`Tag Name`)='" . db_quote(strtoupper($_POST['TAG_NAME'])) . "'");

                if (db_rowcount($result) > 0)
                {
                    $message = "A tag called '" . htmlentities($_POST["TAG_NAME"]) . "' already exists!";
                }
                else
                {
                    if (strtoupper($_POST["TAG_NAME"]) == "ANY" || strtoupper($_POST["TAG_NAME"]) == "NONE")
                    {
                        $message = "You can't name a tag '" . strtoupper($_POST["TAG_NAME"]) . "', as that's a command used when searching for TAG:" . strtoupper($_POST["TAG_NAME"]);
                    }
                    else
                    {
                        if (stripos($_POST["TAG_NAME"], "%") !== false || stripos($_POST["TAG_NAME"], "*") !== false)
                        {
                            $message = "Tag names can't include either the '*' or '%' symbols.";
                        }
                        else
                        {
                            // calculate colour values

                            $rgb = HTMLToRGB($_POST["TAG_COLOUR"]);
                            $hsl = RGBToHSL($rgb);

                            // save the new tag

                            db_query("INSERT INTO `TAGS` 
							
							(`User ID`, `Tag Name`, `Tag Colour`, `Tag Lightness Value`)
							
							VALUES ( 
								'" . db_quote($_SESSION['UID']) . "',
						        '" . db_quote($_POST["TAG_NAME"]) . "',
						        '" . db_quote($_POST["TAG_COLOUR"]) . "',
						        '" . db_quote($hsl->lightness) . "'
						        )
						        ");

                            $message       = "New tag '" . htmlentities($_POST["TAG_NAME"]) . "' has been created.";
                            $message_class = "message-success";
                        }
                    }
                }
            }
            else
            {
                $message = "There was a problem with the tag name or colour.";
            }
        }
    }

    // applying changes to a tag

    if (isset($_GET["UT"]))
    {
        if (strtoupper($_POST["TAG_RENAME"]) == "ANY")
        {
            $message    = "You cannot name a tag 'ANY', as that's a command used when searching for TAG:ANY";
            $_GET["UT"] = "";
        }

        if (strtoupper($_POST["TAG_RENAME"]) == "NONE")
        {
            $message    = "You cannot name a tag 'NONE', as that's a command used when searching for TAG:NONE";
            $_GET["UT"] = "";
        }

        if ($_GET["UT"] != "" && !isset($_POST["TAG_RENAME_CANCEL"]))
        {
            // check tag exists

            $result = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `ID`='" . db_quote($_GET['UT']) . "'");

            if (db_rowcount($result) > 0)
            {
                if ($_POST["TAG_RENAME"] != "" && ctype_xdigit($_POST["TAG_COLOUR"]) && strlen($_POST["TAG_COLOUR"]) == 6)
                {
                    // check the tag name doesn't already exist

                    $result = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND UPPER(`Tag Name`)='" . db_quote(strtoupper($_POST['TAG_RENAME'])) . "' AND `ID`!='" . db_quote($_GET['UT']) . "'");

                    if (db_rowcount($result) > 0)
                    {
                        $message = "A tag called '" . htmlentities($_POST["TAG_RENAME"]) . "' already exists!";
                    }
                    else
                    {
                        // calculate colour values

                        $rgb = HTMLToRGB($_POST["TAG_COLOUR"]);
                        $hsl = RGBToHSL($rgb);

                        // save the changes

                        db_query("UPDATE `TAGS` SET 
				        `Tag Name`='" . db_quote($_POST["TAG_RENAME"]) . "',
				        `Tag Colour`='" . db_quote($_POST["TAG_COLOUR"]) . "',
				        `Tag Lightness Value`='" . db_quote($hsl->lightness) . "'
				        
				        WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `ID`='" . db_quote($_GET['UT']) . "'");

                        $message       = "Changes to tag have been saved.";
                        $message_class = "message-success";
                    }
                }
                else
                {
                    $message = "There was a problem with the tag name or colour.";
                }
            }
        }
    }

    // renaming and/or recolouring a tag

    if (isset($_GET["RT"]))
    {
        if ($_GET["RT"] != "")
        {
            // check tag exists

            $result = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' AND `ID`='" . db_quote($_GET['RT']) . "'");

            if (db_rowcount($result) > 0)
            {
                $ROW = db_return_row($result);

                echo "<h2 class='page-title-text'>Rename Tag</h2>";

                echo "<div class='form' id='RegistrationForm'>";

                echo "<form action='tag_manager.php?UT=" . $_GET["RT"] . "' ID=formGETKEY method=POST>";

                echo "<p class='bigger-message'>To rename the tag currently named <b>'" . htmlentities($ROW["Tag Name"]) . "'</b>, simply edit its name below. You can also change its colour if you wish.</p>";

                echo "<input type='text' NAME=TAG_RENAME ID=TAG_RENAME size=50 autocomplete=off autofocus maxlength=30 value='" . htmlspecialchars($ROW["Tag Name"], ENT_QUOTES) . "'>";

                echo "<input class=jscolor NAME=TAG_COLOUR ID=TAG_COLOUR value=" . $ROW["Tag Colour"] . ">";

                echo "<button name=TAG_SAVE type=submit value=1>UPDATE TAG</button>";

                echo "<button name=TAG_RENAME_CANCEL ID=cancelButton type=submit value='Cancel'>CANCEL</button>";

                echo "</form>";

                echo "</div>";

                include "library/footer.php";

                exit;
            }
        }
    }

    // LIST TAGS

    $result = db_query("SELECT * FROM `TAGS` WHERE `User ID`='" . db_quote($_SESSION['UID']) . "' ORDER BY UPPER(`Tag Name`)");

    echo "<div align=center><h2 class='page-title-text'>My Tags";

    if (db_rowcount($result) > 0)
    {
        echo "<span class='chart-tip' data-tipped-options=\"zIndex: 19000, hideOthers: true, ajax: {url:'../charts/chart_tags.php?VIEW=MINI', type: 'post'}\">";

        echo "<a href='/charts/chart_tags.php'><img src='/images/stats.gif'></a></span>";
    }

    echo "</h2>";

    if ($message != "")
    {
        echo "<div class='message $message_class'>$message</div>";
    }

    echo "<table class='hoverTable persist-area fixedTable'>";

    if (db_rowcount($result) > 0)
    {
        echo "<thead class='persist-header table-header-row'>";

        echo "<tr class='table-header-row'>";

        echo "<th bgcolor=#c0c0c0 align=center width=250><b>Tag Name</b></th>";
        echo "<th bgcolor=#c0c0c0 align=center width=130><b>Verses Tagged</b></th>";
        echo "<th bgcolor=#c0c0c0 align=center width=50>&nbsp;</th>";

        echo "</tr>";

        echo "</thead>";

        echo "<tbody>";
    }

    for ($i = 0; $i < db_rowcount($result); $i++)
    {
        // grab next database row
        $ROW = db_return_row($result);

        echo "<tr>";

        echo "<td>";

        $count_verses_tagged = db_return_one_record_one_field("SELECT COUNT(*) FROM `TAGGED-VERSES` WHERE `TAG ID`=" . $ROW["ID"] . " AND `User ID`=" . db_quote($_SESSION['UID']));

        if ($count_verses_tagged > 0)
        {
            echo "<a href='verse_browser.php?S=";

            // wrap the tag name in quote marks if it has a space in it
            echo  (stripos($ROW["Tag Name"], " ") > 0) ? "TAG:\"" . urlencode($ROW["Tag Name"]) . "\"'" : "TAG:" . urlencode($ROW["Tag Name"]) . "'";

            echo " class=nodec_basic>";
        }

        draw_tag_lozenge($ROW["Tag Colour"], $ROW["Tag Lightness Value"]);

        echo "'>";

        echo htmlentities($ROW["Tag Name"]);

        echo "</span>";

        if ($count_verses_tagged > 0)
        {
            echo "</a>";
        }

        echo "</td>";

        echo "<td align=center>";

        if ($count_verses_tagged > 0)
        {
            echo "<a href='verse_browser.php?S=";

            // wrap the tag name in quote marks if it has a space in it
            echo  (stripos($ROW["Tag Name"], " ") > 0) ? "TAG:\"" . urlencode($ROW["Tag Name"]) . "\"'" : "TAG:" . urlencode($ROW["Tag Name"]) . "'";

            echo " class=linky>";
        }

        echo number_format($count_verses_tagged);

        if ($count_verses_tagged > 0)
        {
            echo "</a>";
        }

        echo "</td>";

        echo "<td><a href='tag_manager.php?RT=" . $ROW["ID"] . "'><img src='images/edit.gif' title='Edit Tag Name or Colour'></a>&nbsp;<a href='#' onclick=\"delete_single_tag(" . $ROW["ID"] . ", '" . htmlspecialchars(addslashes($ROW["Tag Name"]), ENT_QUOTES) . "', $count_verses_tagged, '" . plural($count_verses_tagged) . "');\"><img src='images/delete.gif' title='Delete this tag'></a></td>";

        echo "</tr>";
    }

    if (db_rowcount($result) > 0)
    {
        echo "<tr><td colspan=3 align=center><a href='verse_browser.php?S=TAG:ANY' class=linky><b>$i tag" . plural($i) . "</a></b></td></tr>";
    }
    else
    {
        echo "<tr><td colspan=3 width=400px align=center><br><p><b>No Tags To Show</b></p><br></td></tr>";
    }

    if (db_rowcount($result) < $MAX_NUMBER_OF_TAGS)
    {
        echo "<tr><td colspan=3 align=center><a href='tag_manager.php?NT=Y' class='linky-light'>Create a New Tag</a></td></tr>";
    }

    echo "</tbody>";

    echo "</table>";

    // print footer

    include "library/footer.php";

?>

</body>

<script type="text/javascript">
    $(function() {
        Tipped.create('.chart-tip', {
            position: 'left',
            showDelay: 750,
            skin: 'light',
            close: true
        });
    });
</script>

</html>