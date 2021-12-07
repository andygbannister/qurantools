<?php

session_start();
session_regenerate_id();

include "../library/config.php";
include "library/functions.php";
include_once "library/hash.php";

echo "<!DOCTYPE html>";

?>

<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Password Reset");
        ?>
</head>

<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "library/menu.php";

// echo "<div align=center>";

echo "<img class='qt-big-logo-header' src='../images/logos/qt_logo_only.png' alt='Large QT Logo'>";

echo "<h2>Qur’an Tools: Reset Your Password</h2>";

$RESET_CODE = "";
$error      = "";

if (isset($_GET["R"]))
{
    $RESET_CODE = $_GET["R"];
}

// can we find an account with this password reset code?

$result = db_query("SELECT * FROM `USERS` WHERE `Reset Code`='" . db_quote($RESET_CODE) . "'");

if (db_rowcount($result) != 1)
{
    echo "<p><b>Bad password reset code!</b></p>";
    echo "<a href='/auth/request_password_reset.php'><button>Click here to request a fresh password reset code</button></a>";
    echo "</div>";
    include "../library/footer.php";
    exit;
}

// check elapsed time

$ROW = db_return_row($result);

$elapsed = time() - $ROW["Reset Timecode"];

if ($elapsed > 86400)
{
    // wipe old reset code
    db_query("UPDATE `USERS` SET `Reset Code`='', `Reset Timecode`=NULL WHERE `Reset Code`='" . db_quote($RESET_CODE) . "'");

    echo "<p><b>Sorry, but that password reset code has elapsed. (Reset codes are only good for 24 hours).</b></p>";
    echo "<a href='/auth/request_password_reset.php'><button>Click here to request a fresh password reset code</button></a>";
    echo "</div>";
    include "../library/footer.php";
    exit;
}

// have they supplied a new password

if (isset($_POST["PASSWORD1"]) && isset($_POST["CONFIRM_PASSWORD"]))
{
    if ($_POST["PASSWORD1"] == "")
    {
        $error = "<p><b><font color=red>Your new password cannot be blank!</font></b></p>";
    }

    if ($_POST["PASSWORD1"] != $_POST["CONFIRM_PASSWORD"] && $error == "")
    {
        $error = "<p><b><font color=red>The two passwords do not match. Please try again!</font></b></p>";
    }

    if (strlen($_POST["PASSWORD1"]) < 8)
    {
        $error = "<p><b><font color=red>Your password should be at least 8 characters long!</font></b></p>";
    }

    // got this, password is good

    if ($error == "")
    {
        db_query("UPDATE `USERS` SET `Password Hash`='" . db_quote(hash_password($_POST["PASSWORD1"])) . "', `Reset Timecode`=NULL, `Reset Code`='' WHERE `Reset Code`='" . db_quote($RESET_CODE) . "'");
        echo "<p>Your password was successfully changed.</p>";
        echo "<p><a href='../home.php'><button>Login With Your New Password</button></a></p>";
        include "../library/footer.php";
        exit;
    }
}

// ask for the new password

echo $error;

echo "<div class='form' id='RegistrationForm'>";

echo "<form id='new-password-form' action='password_reset.php?R=$RESET_CODE' method=POST>";

echo "<p class='bigger-message'>Please provide a new password:</p>";

echo "<input onkeyup=\"checkPass(1, 0);\" return false;\" type=password ID='pass1' NAME='PASSWORD1' autofocus size=50 maxlength=150 placeholder='New Password'>";

echo "<div ID='PasswordWarning1' class='PasswordWarning'>Any messages about password 1</div>";

echo "<input onkeyup=\"checkPass(2, 0);\" return false;\" type=password ID='pass2' NAME='CONFIRM_PASSWORD' size=50 maxlength=150 placeholder='Confirm Password'>";

echo "<div ID='PasswordWarning2' class='PasswordWarning'>Any messages about password 2</div>";

echo "<button type='SUBMIT' id='change-password' name='OKbutton' value='OK'>CHANGE PASSWORD</button>";

echo "<a href='../home.php'><button type='button' ID='cancelButton' name='CANCEL'>Cancel</button></a>";

echo "</form>";

include "../library/footer.php";

?>