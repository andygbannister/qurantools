<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only superusers can view this page; otherwise redirect
if (strtoupper($_SESSION['administrator']) != "SUPERUSER")
{
    header('Location: /404.php');
}

$code_message        = "";
$delete_message      = "";
$new_account_message = "";
$message_class       = "message-warning";

// search
$SEARCH_SQL = "";
if (!isset($_GET["SEARCH"]) || isset($_GET["WIPE"]))
{
    $_GET["SEARCH"] = "";
}

if ($_GET["SEARCH"] != "")
{
    $SEARCH_SQL = "WHERE (UPPER(t1.`Access Code`) LIKE '%" . db_quote(strtoupper($_GET["SEARCH"])) . "%' OR UPPER(t1.`Email Address`) LIKE '%" . db_quote(strtoupper($_GET["SEARCH"])) . "%' OR UPPER(t2.`Email Address`) LIKE '%" . db_quote(strtoupper($_GET["SEARCH"])) . "%' OR UPPER(t1.`Comment`) LIKE '%" . db_quote(strtoupper($_GET["SEARCH"])) . "%')";
}

// have they created a new user?
if (isset($_POST["DO_NEW_USER"]))
{
    if ($_POST["USER_EMAIL"] == "")
    {
        $code_message = "No email address supplied for new account!";
    }

    $user_name = generate_user_name($_POST["FIRST_NAME"], $_POST["LAST_NAME"]);
    if (empty($user_name) || strlen($user_name) < $config['minimum_full_name_length'])
    {
        $code_message = "New user's name is too short";
    }

    if ($_POST["PASSWORD1"] != $_POST["CONFIRM_PASSWORD"] && $code_message == "")
    {
        $code_message = "The two passwords do not match. Please try again!";
    }

    if (strlen($_POST["PASSWORD1"]) < 8 && $code_message == "")
    {
        $code_message = "Your password should be at least 8 characters long!";
    }

    if (strlen($_POST["CONFIRM_PASSWORD"]) < 8 && $code_message == "")
    {
        $code_message = "Your password should be at least 8 characters long!";
    }

    // CHECK FOR DUPLICATE ACCOUNT

    if ($code_message == "")
    {
        $duplicateCheck = db_query("SELECT * FROM `USERS` WHERE `Email Address`='" . db_quote($_POST["USER_EMAIL"]) . "'");
        if (db_rowcount($duplicateCheck) > 0)
        {
            $code_message = "A user account for " . $_POST["USER_EMAIL"] . " already exists!";
        }
    }

    // if we have got to here, we can go and create the new user
    if ($code_message == "")
    {
        $insert_data = [
            'EMAIL'         => $_POST['USER_EMAIL'],
            'PASSWORD'      => $_POST['PASSWORD1'],
            'FIRST_NAME'    => $_POST['FIRST_NAME'],
            'LAST_NAME'     => $_POST['LAST_NAME'],
            'ADMINISTRATOR' => $_POST['ADMINISTRATOR'],
            'USER_TYPE'     => USER_TYPE_CONSUMER,
        ];

        $new_user = register_user($insert_data);

        if ($new_user)
        {
            $new_account_message = "A new user has been created: " . $_POST["USER_EMAIL"];
            $message_class       = "message-success";
        }
        else
        {
            $new_account_message = "Internal Error: Unable to insert user (" . $_POST["USER_EMAIL"] . "). Please check logs.";
            $message_class       = "message-warning";
        }

        $_POST["DO_NEW_USER"] = "";
    }
}

// how many rows of the table per page to show
$ITEMS_PER_PAGE = 250;
$CURRENT_PAGE   = 1;

// GET CURRENT PAGE

if (isset($_GET["PAGE"]))
{
    $CURRENT_PAGE = $_GET["PAGE"];
    if ($CURRENT_PAGE < 1)
    {
        $CURRENT_PAGE = 1;
    }
}
else
{
    $_GET["PAGE"] = "";
}

?>
<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title("User Admin Tool");
    ?>

    <script LANGUAGE='JavaScript' SRC='../library/js/CalendarPopup.js'></script>

    <script type="text/javascript">
        var cal = new CalendarPopup();

        function toggle_newuser_form() {
            var e = document.getElementById('NEWUSER_PANEL');
            if (e.style.display == 'block') {
                e.style.display = 'none';
                document.getElementById('USER_EMAIL').value = '';
                document.getElementById('FIRST_NAME').value = '';
                document.getElementById('LAST_NAME').value = '';
                document.getElementById('pass1').value = '';
                document.getElementById('pass2').value = '';
                document.getElementById('lengthMessage').innerHTML = '';
                document.getElementById('confirmMessage').innerHTML = '';
                document.getElementById('pass2').style.backgroundColor = '#ffffff';
            } else {
                e.style.display = 'block';
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

        echo "<h2 class='page-title-text'>User Management</h2>";

        // filter based on admin or not

        $filterByAdminStatus     = "ALL";
        $filterByAdminStatus_SQL = "";

        if (isset($_GET["FILTER"]))
        {
            $filterByAdminStatus = $_GET["FILTER"];
        }

        if ($filterByAdminStatus != "ALL" && $filterByAdminStatus != "EXCLUDE_ADMIN" && $filterByAdminStatus != "ADMIN_ONLY")
        {
            $filterByAdminStatus = "ALL";
        }

        if ($filterByAdminStatus == "EXCLUDE_ADMIN")
        {
            $filterByAdminStatus_SQL = "AND `Administrator`=''";
        }

        if ($filterByAdminStatus == "ADMIN_ONLY")
        {
            $filterByAdminStatus_SQL = "AND `Administrator`!=''";
        }

        // filter based on login time

        $timeGETURLpass = "";
        $filterTime     = "";

        if (isset($_GET["TIME"]) && isset($_GET["LABEL"]))
        {
            $timeGETURLpass = "TIME=" . $_GET["TIME"] . "&LABEL=" . $_GET["LABEL"];

            if (isset($_GET["MODE"]))
            {
                $timeGETURLpass .= "&MODE=" . $_GET["MODE"];
            }

            echo "<b>Listing Users Who Have Logged On In The " . $_GET["LABEL"] . "</b>";

            $filterTime = "AND `Last Login Timestamp` >= DATE_SUB(NOW(), INTERVAL " . $_GET["TIME"] . " HOUR)";

            echo "<p>";
            echo "<a href='login_logs.php?FILTER=ALL&MODE=TABLE&FULL_OR_SUMMARY=SUMMARY&$timeGETURLpass&FILTER=$filterByAdminStatus' class=linky><button class='button-block-with-spacing'><< Back To Login Logs</button></a>";
            echo "<a href='user_management.php' class=linky><button class='button-block-with-spacing'>Show All Users</button></a>";
            echo "</p>";
        }

        // edit user request
        // TODO: This should be refactored into external and testable functions

        if (isset($_POST["DO_NEW_USER_INFO_FOR_USER"]))
        {
            if ($_GET["EDITCONFIRM"] > 0)
            {
                $user = get_user_by_id($_GET["EDITCONFIRM"]);

                if ($user)
                {
                    $columns = [
                        'First Name'    => $_POST['FIRST_NAME'],
                        'Last Name'     => $_POST['LAST_NAME'],
                        'Administrator' => $_POST['ADMINISTRATOR']
                    ];

                    $success = update_user_by_id($_GET["EDITCONFIRM"], $columns, $logged_in_user);

                    if ($success)
                    {
                        $code_message  = generate_user_name($_POST['FIRST_NAME'], $_POST['LAST_NAME']) . " was successfully updated";
                        $message_class = "message-success";
                    }
                    else
                    {
                        $code_message = "Internal Error: Something went wrong, user not updated";
                    }
                }
            }
        }

        // edit user form
        // TODO: like almost all of this page, this part also needs tidying up and refactoring

        if (isset($_GET["EDIT_USER"]) && $_GET["EDIT_USER"] > 0)
        {
            $user = get_user_by_id($_GET["EDIT_USER"]);
            if ($user)
            {
                echo "<div id='update-user-dialog' name='update-user-dialog' class='dialog-box'>";

                echo "<h3>Update User Information</h3>";

                echo "<form id='edit-user' class='form' action='user_management.php?PAGE=$CURRENT_PAGE&EDITCONFIRM=" . $_GET["EDIT_USER"] . "&$timeGETURLpass' method=POST>";

                // First Name
                echo "<input ID='FIRST_NAME' NAME='FIRST_NAME' autofocus size=50 maxlength=100 autocomplete='off' placeholder='First Name' class='name-component' value='" . $user['First Name'] . "'>";

                // Last Name
                echo "<input ID='LAST_NAME' NAME='LAST_NAME' autofocus size=50 maxlength=100 autocomplete='off' placeholder='Last Name' class='name-component names' value='" . $user['Last Name'] . "'>";

                // Administrator
                echo "<select name='ADMINISTRATOR' id='ADMINISTRATOR'>
                        <option value=''>Normal User</option>
                        <option value=''" . ('' == $user['Administrator'] ? 'selected' : '') . ">Normal User</option>
                        <option value='ADMIN'" . ('ADMIN' == $user['Administrator'] ? 'selected' : '') . ">ADMIN</option>
                        <option value='SUPERUSER' " . ('SUPERUSER' == $user['Administrator'] ? 'selected' : '') . "  " . ">SUPERADMIN</option>
                      </select>";

                echo "<button type='SUBMIT' name='DO_NEW_USER_INFO_FOR_USER'>Update User</button>";

                echo "<button type='button' class='cancel-button' onClick='\$(\"#update-user-dialog\").toggle();'>Cancel</button>";

                echo "</form>";

                echo "<p></p></div>";
            }
            else
            {
                $code_message = "User can't be found!";
            }
        }

        // edit user password request

        if (isset($_POST["DO_NEW_PASSWORD_FOR_USER"]))
        {
            if ($_GET["EDITCONFIRM"] > 0)
            {
                // check user exists
                $edit_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=" . db_quote($_GET["EDITCONFIRM"]));

                if (db_rowcount($edit_result) > 0)
                {
                    // WE NOW CHECK THE PASSWORDS ARE OKAY
                    if ($_POST["PASSWORD1"] != $_POST["CONFIRM_PASSWORD"] && $code_message == "")
                    {
                        $code_message = "The two passwords do not match. Please try again!";
                    }

                    if (strlen($_POST["PASSWORD1"]) < 8 && $code_message == "")
                    {
                        $code_message = "A password should be at least 8 characters long!";
                    }

                    if (strlen($_POST["CONFIRM_PASSWORD"]) < 8 && $code_message == "")
                    {
                        $code_message = "A password should be at least 8 characters long!";
                    }

                    if ($code_message == "")
                    {
                        // load the row
                        $ROW = db_return_row($edit_result);

                        $code_message = "Password successfully changed for user '" . $ROW["Email Address"] . "'";

                        $message_class = "message-success";

                        update_user_by_id(
                            $_GET['EDITCONFIRM'],
                            ['Password Hash' => hash_password($_POST["PASSWORD1"])],
                            $logged_in_user
                        );
                    }
                }
            }
        }

        // edit user password form

        if (isset($_GET["EDIT_USER_PASSWORD"]))
        {
            if ($_GET["EDIT_USER_PASSWORD"] > 0)
            {
                // look up user
                $edit_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=" . db_quote($_GET["EDIT_USER_PASSWORD"]));

                if (db_rowcount($edit_result) > 0)
                {
                    // load the row
                    $ROW = db_return_row($edit_result);

                    // create the edit user password
                    echo "<div id='PASSWORD_PANEL' class='dialog-box'>";

                    echo "<h3>Change Password</h3>";

                    echo "<form id='new-password-form' class='form' action='user_management.php?PAGE=$CURRENT_PAGE&EDITCONFIRM=" . $_GET["EDIT_USER_PASSWORD"] . "&$timeGETURLpass&SORT=" . $_GET["SORT"] . "' method=POST>";

                    if ($code_message != "")
                    {
                        echo "<p class='bigger-message'>$code_message</p>";
                    }
                    else
                    {
                        echo "<p class='bigger-message'>Please provide a new password for user:<br><b>" . $ROW["Email Address"] . "</b></p>";
                    }

                    echo "<input onkeyup=\"checkPass(1, 390);\" return false;\" type=password ID='pass1' NAME='PASSWORD1' autofocus size=50 maxlength=150 autocomplete='off' placeholder='New Password'>";

                    echo "<div ID='PasswordWarning1' class='PasswordWarning'>Any messages about password 1</div>";

                    echo "<input onkeyup=\"checkPass(2, 390);\" return false;\" type=password ID=pass2 NAME='CONFIRM_PASSWORD' size=50 maxlength=150 autocomplete='off' placeholder='Confirm Password'>";

                    echo "<div ID='PasswordWarning2' class='PasswordWarning'>Any messages about password 2</div>";

                    echo "<button type=SUBMIT name='DO_NEW_PASSWORD_FOR_USER' value=OK>CHANGE PASSWORD</button>";

                    echo "<button type=SUBMIT class='cancel-button' name='CANCEL'>Cancel</button>";

                    echo "</form>";

                    echo "</div>";
                }
                else
                {
                    $code_message = "User ID can't be found to change their password!";
                }
            }
        }

        // TODO: replace with an archive feature

        // // is this a delete request?
        // if (isset($_GET["DELUSER"]))
        // {
        //     if ($_GET["DELUSER"] > 0)
        //     {
        //         // look up user
        //         $del_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=".db_quote($_GET["DELUSER"]));

        //         if (db_rowcount($del_result) > 0)
        //         {
        //             // load the row
        //             $DROW = db_return_row($del_result);

        //             // create the delete user panel
        //             echo "<div id='DELETE_USER_PANEL' class='dialog-box'>";

        //             echo "<h3>Delete User</h3>";

        //             echo "<div class='form'>";

        //             echo "<p class='bigger-message'>About to delete the user:<br><b>".$DROW["Email Address"]."</b></p>";

        //             echo "<a href='user_management.php?DELCONFIRM=".$_GET["DELUSER"]."&$timeGETURLpass&SORT=".$_GET["SORT"]."'>";
        //             echo "<button name='DELETE_USER' type='submit' value='1'>PROCEED AND DELETE</button>";
        //             echo "</a>";

        //             echo "<a href='user_management.php?PAGE=$CURRENT_PAGE&$timeGETURLpass&SORT=".$_GET["SORT"]."'>";
        //             echo "<button name=BOOKMARK_RENAME_CANCEL class='cancel-button' type=submit value='Cancel'>CANCEL</button>";
        //             echo "</a>";

        //             echo "</div>";

        //             echo "</div>";
        //         }
        //         else
        //         {
        //             $code_message = "User ID can't be found to delete!";
        //         }
        //     }
        // }

        // TODO: replace with an archive feature
        // delete confirmed
        // if (isset($_GET["DELCONFIRM"]))
        // {
        //     if ($_GET["DELCONFIRM"] > 0)
        //     {
        //         // TODO: Create a delete user function that deletes a user and its
        //         // dependent records

        //         // look up user
        //         $del_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=".db_quote($_GET["DELCONFIRM"]));

        //         if (db_rowcount($del_result) > 0)
        //         {
        //             // load the row
        //             $DROW = db_return_row($del_result);

        //             $delete_message = "User '".htmlentities($DROW["User Name"])."' (".$DROW["Email Address"].") has been deleted";

        //             // delete user
        //             db_query("DELETE FROM `USERS` WHERE `User ID`=".db_quote($_GET["DELCONFIRM"]));

        //             // delete history
        //             db_query("DELETE FROM `HISTORY` WHERE `User ID`=".db_quote($_GET["DELCONFIRM"]));

        //             // delete bookmarks
        //             db_query("DELETE FROM `BOOKMARKS` WHERE `User ID`=".db_quote($_GET["DELCONFIRM"]));
        //         }
        //     }
        // }

        // is this a reset password request?
        if (isset($_GET["RESETUSER"]))
        {
            if ($_GET["RESETUSER"] > 0)
            {
                // look up user
                $del_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=" . db_quote($_GET["RESETUSER"]));

                if (db_rowcount($del_result) > 0)
                {
                    // load the row
                    $DROW = db_return_row($del_result);

                    // create the reset user password panel
                    echo "<div id='RESET_PASSWORD_PANEL' class='dialog-box'>";

                    echo "<h3>Reset User Password</h3>";

                    echo "<div class='form'>";

                    echo "<p class='bigger-message'>Are you sure you wish to reset the password for <b>" . $DROW["Email Address"] . "</b>? They will be asked to reset their password when they next login.</p>";

                    echo "<a href='user_management.php?RESETCONFIRM=" . $_GET["RESETUSER"] . "&$timeGETURLpass&SORT=" . $_GET["SORT"] . "'>";
                    echo "<button name=BOOKMARK_SAVE type=submit value=1>PROCEED</button>";
                    echo "</a>";

                    echo "<a href='user_management.php?PAGE=$CURRENT_PAGE&$timeGETURLpass&SORT=" . $_GET["SORT"] . "'>";
                    echo "<button name=BOOKMARK_RENAME_CANCEL class='cancel-button' type=submit value='Cancel'>CANCEL</button>";
                    echo "</a>";

                    echo "</div>";

                    echo "</div>";
                }
                else
                {
                    $code_message = "User ID can't be found to reset!";
                }
            }
        }

        // reset confirmed
        if (isset($_GET["RESETCONFIRM"]))
        {
            if ($_GET["RESETCONFIRM"] > 0)
            {
                // look up user
                $del_result = db_query("SELECT * FROM `USERS` WHERE `User ID`=" . db_quote($_GET["RESETCONFIRM"]));

                if (db_rowcount($del_result) > 0)
                {
                    // load the row
                    $DROW = db_return_row($del_result);

                    // wipe their password hash
                    if (db_query("UPDATE `USERS` SET `Password Hash`='" . PASSWORD_RESET_TEXT . "' WHERE `User ID`=" . db_quote($_GET["RESETCONFIRM"])))
                    {
                        $code_message = "User " . htmlentities($DROW["User Name"]) . " (" . $DROW["Email Address"] . ") has had their password reset.";

                        $message_class = "message-success";
                    }
                    else
                    {
                        $code_message = "User " . htmlentities($DROW["User Name"]) . " (" . $DROW["Email Address"] . ") has not had their password reset.";

                        $message_class = "message-warning";
                    };
                }
            }
        }

        // ====================== CREATE NEW USER PANEL ==================================

        echo "<div id='NEWUSER_PANEL' name='NEWUSER_PANEL' class='dialog-box' ";
        if (isset($_POST["DO_NEW_USER"]))
        {
            if ($_POST["DO_NEW_USER"] != "")
            {
                echo "display: block;";
            }
        }
        echo "'>";

        echo "<h3>Create a New User</h3>";

        echo "<form id='new-user-form' class='form' action='user_management.php?PAGE=$CURRENT_PAGE' method=POST>";

        if ($code_message != "")
        {
            echo "<p class='bigger-message'>$code_message</p>";
        }

        echo "<input id='USER_EMAIL' NAME='USER_EMAIL' type='email' size=50 maxlength=150 autocomplete='off' required ";
        if (isset($_POST["DO_NEW_USER"]) && $code_message != "")
        {
            echo "value='" . $_POST["USER_EMAIL"] . "' ";
        }
        echo "autofocus placeholder='Email Address'>";

        echo "<input id='FIRST_NAME' type=text NAME='FIRST_NAME' size=50 maxlength=100 ";
        if (isset($_POST["DO_NEW_USER"]) && $code_message != "")
        {
            echo "value='" . $_POST["FIRST_NAME"] . "' ";
        }
        echo "autocomplete='off' placeholder='First Name'>";

        echo "<input id='LAST_NAME' type=text NAME='LAST_NAME' size=50 maxlength=100 ";
        if (isset($_POST["DO_NEW_USER"]) && $code_message != "")
        {
            echo "value='" . $_POST["LAST_NAME"] . "' ";
        }
        echo "autocomplete='off' placeholder='Last Name' class='names'>";

        // Administrator
        echo "<select name='ADMINISTRATOR' id='ADMINISTRATOR'>
                <option value=''>Are they an admin?</option>
                <option value=''>Normal User</option>
                <option value='ADMIN'>ADMIN</option>
                <option value='SUPERUSER'>SUPERADMIN</option>
              </select>";

        echo "<input type='password' ID='pass1' NAME='PASSWORD1' size='50' maxlength='150' autocomplete='off' placeholder='New Password' required>";

        echo "<input type='password' ID='pass2' NAME='CONFIRM_PASSWORD' size='50' maxlength='150' autocomplete='off' placeholder='Confirm Password' required>";

        echo "<button type='SUBMIT' name='DO_NEW_USER' value='OK'>CREATE NEW USER</button>";

        echo "<button class='cancel-button' name='CANCEL' onclick='$(\"#NEWUSER_PANEL\").hide();'>Cancel</button>";;

        echo "</form>";

        echo "</table></form>";

        echo "</div>";

        // ==============================================================================

        // sort order

        if (!isset($_GET["SORT"]))
        {
            $_GET["SORT"] = "";
        }
        $SORT_ORDER = "";

        if (!isset($_GET["TIME"]))
        {
            echo "<button id='create-user' type=button onClick='toggle_newuser_form();'>Create a New User</button>";

            echo "<a href='user_management.php?PAGE=$CURRENT_PAGE&SORT=" . $_GET["SORT"] . "'>";

            echo "</a>";

            echo "</form>";

            echo "<br>&nbsp;";
        }

        if ($new_account_message != "" || $code_message != "" || $delete_message != "")
        {
            if (!isset($_GET["TIME"]))
            {
                echo "<div align=center class='message $message_class'><b>$new_account_message$code_message$delete_message</b></div>";
            }
            else
            {
                echo "<div align=center class='message $message_class'><b>$new_account_message$code_message$delete_message</b></div>";
            }
        }

        // LIST ALL USERS

        $sql = "SELECT *,
               (SELECT COUNT(*) 
                  FROM `USAGE-VERSES-SEARCHES` U2 
                 WHERE U1.`User ID`=U2.`User ID`) activityCount 
           FROM `USERS` U1
          WHERE 1 $filterTime $filterByAdminStatus_SQL";

        $result = db_query($sql);

        echo "<table id='manage-users' class='hoverTable fixedTable qt-table'>
            <thead class='table-header-row'>
              <tr class='table-header-row'>
                <th width='140'>Name</th>
                <th width='170'>Email Address</th>
                <th width='115'>Last Login</th>
                <th width='50'><b>Logins</b></th>
                <th width='55'><b>Activity</b></th>
                <th width='50'><b>Admin</b></th>
                <th width='90'>Actions</th>
              </tr>
            </thead>
            <tbody>";

        for ($i = 0; $i < db_rowcount($result); $i++)
        {
            // grab next database row

            $ROW = db_return_row($result);
            echo "<tr id='user-id-" . $ROW['User ID'] . "'>";

            // User Name and locked password

            $user_name = (empty($ROW["User Name"]) ? 'Name not supplied' : $ROW["User Name"]);

            echo "<td class='user-name left-align'><a href='verse_search_logs.php?U=" . $ROW["User ID"] . "' class=linky>" . htmlspecialchars($user_name) . "</a>";

            // Is password locked?

            if ($ROW["Password Hash"] == PASSWORD_RESET_TEXT)
            {
                echo " <img src='..\images\padlock-locked-red.png' class='qt-icon pull-right' title='User&lsquo;s password is locked'>";
            }

            echo "</td>";

            echo "<td class='email-address left-align'><a href='mailto:" . $ROW["Email Address"] . "' class=linky>" . htmlspecialchars($ROW["Email Address"]) . "</a></td>";

            echo "<td class='last-login'>";

            // last login + bookmarks etc

            echo $ROW["Last Login Timestamp"];

            // TODO: add these look-ups to the main query to speed this page up
            // and lose the 3 n+1 queries!
            $count_history = db_return_one_record_one_field("SELECT COUNT(*) FROM `HISTORY` WHERE `User ID`=" . db_quote($ROW["User ID"]));

            if ($count_history > 0)
            {
                echo "<br><span class='smaller_text_for_mini_dialogs'><a href='user_detail.php?USER=" . $ROW["User ID"] . "&TAB=4' class=linky>History Items: " . number_format($count_history) . "</a></span>";
            }

            $count_bookmarks = db_return_one_record_one_field("SELECT COUNT(*) FROM `BOOKMARKS` WHERE `User ID`=" . db_quote($ROW["User ID"]));

            if ($count_bookmarks > 0)
            {
                echo "<br><span class=smaller_text_for_mini_dialogs><a href='user_detail.php?USER=" . $ROW["User ID"] . "&TAB=5' class=linky>Bookmarks: " . number_format($count_bookmarks) . "</a></span>";
            }

            $count_tags = db_return_one_record_one_field("SELECT COUNT(*) FROM `TAGS` WHERE `User ID`=" . db_quote($ROW["User ID"]));

            if ($count_tags > 0)
            {
                echo "<br><span class=smaller_text_for_mini_dialogs><a href='user_detail.php?USER=" . $ROW["User ID"] . "&TAB=6' class=linky>Tags: " . number_format($count_tags) . "</a></span>";
            }

            echo "</td>";

            echo "<td class='login-count'>" . number_format($ROW["Login Count"]) . "</td>";

            // count how many searches or verse look ups we have recorded for this user

            echo "<td class='activity-count'><a href='verse_search_logs.php?U=" . $ROW["User ID"] . "' class=linky>" . number_format($ROW["activityCount"]) . "</a></td>";

            echo "<td class='administrator'>";

            echo $ROW["Administrator"];

            echo "</td>";

            // Actions

            echo "<td class='actions left-align'>";

            if (USER_TYPE_SYSTEM != $ROW['User Type'])
            {
                // more info icon
                echo "<a href='user_detail.php?USER=" . $ROW["User ID"] . "'>";
                echo "<img src='/images/info.png' class='qt-icon' title='Get more info on this user'>";
                echo "</a>";

                // edit user icon
                echo "<a id='edit-user-" . $ROW["User ID"] . "' href='user_management.php?EDIT_USER=" . $ROW["User ID"] . "&$timeGETURLpass'>";
                echo "<img src='/images/edit.png' class='qt-icon' title='Edit the user'>";
                echo "</a>";

                // these actions not available to the currently logged in user
                if ($ROW["User ID"] != $_SESSION['UID'])
                {
                    // delete icon
                    // TODO: create an archive function instead
                    // echo "<a id='delete-user-".$ROW["User ID"]."' href='user_management.php?DELUSER=".$ROW["User ID"]."&$timeGETURLpass&SORT=".$_GET["SORT"]."'>";
                    // echo "<img src='/images/delete.png' class='qt-icon' title='Delete this user'>";
                    // echo "</a>";

                    echo "<a href='user_management.php?EDIT_USER_PASSWORD=" . $ROW["User ID"] . "&$timeGETURLpass&SORT=" . $_GET["SORT"] . "' id='edit-user-password-" . $ROW["User ID"] . "'>";
                    echo "<img src='/images/edit_password.png' class='qt-icon' title='Change password for this user'>";
                    echo "</a>";

                    if ($ROW["Password Hash"] != PASSWORD_RESET_TEXT)
                    {
                        echo "<a href='user_management.php?RESETUSER=" . $ROW["User ID"] . "&$timeGETURLpass&SORT=" . $_GET["SORT"] . "' id='reset-user-password-" . $ROW["User ID"] . "' class='linky-light'>";
                        echo "<img src='/images/padlock.png' class='qt-icon' title='Reset the password for this user'>";
                        echo "</a>";
                    }
                    else
                    {
                        echo "<img src='/images/padlock_faint.png' class='qt-icon' title='User&lsquo;s password is locked'>";
                    }
                }
                else
                {
                    echo "&nbsp;";
                }
            }

            echo "</td>";

            echo "</tr>";
        }

        echo "</tbody>";

        echo "</table><br>";

        // code list/manager

        include "library/footer.php";
        ?>
        <script type="text/javascript">
            if (typeof qt == 'undefined') qt = {};
            qt
                .minimumFullNameLength = <?php echo $config['minimum_full_name_length']; ?> ;
        </script>

        <script type='text/javascript' src='user_management.js'></script>

</body>

</html>