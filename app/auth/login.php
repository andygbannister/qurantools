<?php
session_start();

require_once '../library/config.php';
require_once 'library/functions.php';

// access session variables that should have been set in /auth/auth.php
$redirect_link  = isset($_SESSION[AUTH_REDIRECT_LINK]) ? $_SESSION[AUTH_REDIRECT_LINK] : "/home.php";
$password_reset = isset($_SESSION[AUTH_PASSWORD_RESET]) ? $_SESSION[AUTH_PASSWORD_RESET] : false;
$account_locked = isset($_SESSION[AUTH_ACCOUNT_LOCKED]) ? $_SESSION[AUTH_ACCOUNT_LOCKED] : false;
$consumer_error = isset($_SESSION[AUTH_CONSUMER_ERROR]) ? $_SESSION[AUTH_CONSUMER_ERROR] : "";

// wipe session variables
$_SESSION = [];

echo '<!DOCTYPE html>';
echo '<head>';

include 'library/standard_header.php';

window_title('Login');

echo '</head>';
echo '  <body class="qt-site">';
echo '    <main class="qt-site-content">';

include 'library/menu.php';

echo "  <img src='/images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>";

echo "  <h2 class='page-title-text'>Welcome to Qur’an Tools</h2>";

echo "  <div id='login-form'>";
echo "    <div id='consumer-login' class='login-type'>";

if ($password_reset)
{
    // if $consumer_error is set, print it. It may be reporting things like a
    // password having been reset by an administrator

    if ($consumer_error)
    {
        echo "      <p class='error-message'>$consumer_error</p>";
    }

    echo "      Click here to <a href='/auth/request_password_reset.php'>send</a> a password reset code to your registered email.";
}

if (!$account_locked && !$password_reset)
{
    // consumer login form

    echo "    <form id='login' action='$redirect_link' method='POST'>";

    if ($consumer_error)
    {
        echo "      <p class='error-message'>$consumer_error</p>";
    }

    echo "      <input type='text' NAME='EMAIL_ADDRESS' autofocus autocomplete='username' placeholder='Email Address' required/>
                <input type='password' NAME='PASSWORD' placeholder='Password' required autocomplete='current-password'/>
                <a id='reset-password-link' class='message' href='/auth/request_password_reset.php'>Forgot password?</a>
                <input type='submit' id='login-button' class='general-button' value='login'>";

    if (is_user_registration_allowed())
    {
        echo "      <p class='message'>Not got an account yet? <a href='register.php' id='register'>Sign up</a></p>";
    }

    if (is_branded())
    {
        echo "<p class='message branding'>";
        echo branding_text('This installation of Qur&rsquo;an Tools is hosted by ');
        echo "</p>";
    }

    echo '    </form>';
}

if (!$password_reset)
{
    if ($account_locked)
    {
        echo "    <div align='center' style='margin-top:30px;'>";

        echo "      <p class='error-message'>$consumer_error</p>";

        echo "      <p><a href='/home.php'><button>Go Back</button></a></p>";
    }
}

echo '    </div>'; // #consumer-login

echo '  </div>'; // #login-form

include 'library/footer.php';
?>

<script type='text/javascript' src='login.js'></script>
</body>

</html>