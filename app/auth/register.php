<?php

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'library/hash.php';

define('PAGE_MODE_CONSUMER_SUCCESS', 'consumer success');
define('PAGE_MODE_REGISTER', 'register');

define(
    'INTERNAL_UNKNOWN_ERROR_MESSAGE',
    'Something went wrong trying to register you. Please try again, and if the problem persists, please get in touch.'
);

// default mode shows the register form
$page_mode = PAGE_MODE_REGISTER;

// prevent access is user sign-ups not allowed
if (!is_user_registration_allowed())
{
    header('Location: /auth/login.php');
}

// only users that are not logged in can access this page
if (is_logged_in_user())
{
    header('Location: /home.php');
}

echo '<!DOCTYPE html>';
echo '<html>';
echo '  <head>';

include 'library/standard_header.php';

window_title('New Account');

echo '  </head>';

echo "  <body class='qt-site'>";
echo "    <main class='qt-site-content'>";

include 'library/menu.php';

// TODO: It would probably be a good idea to have some kind of verify function
// that ensures users have given a real email address

if (isset($_POST['REGISTER_BUTTON']))
{
    // Register a consumer user
    try
    {
        $insert_data = [
            'EMAIL'      => trim($_POST['EMAIL']),
            'PASSWORD'   => trim($_POST['PASSWORD']),
            'FIRST_NAME' => trim($_POST['FIRST_NAME']),
            'LAST_NAME'  => trim($_POST['LAST_NAME']),
        ];

        $new_user = register_consumer_user($insert_data);

        if (!$new_user)
        {
            throw new \Exception('Something mild just happened that prevented a user from being created. Check the logs above here.');
        }
        $page_mode = PAGE_MODE_CONSUMER_SUCCESS;
    }
    catch (\Throwable $thrown)
    {
        error_log($thrown->getMessage());
        error_log('$insert_data: ' . json_encode($insert_data));

        if ($thrown->getCode() == USER_DISPLAYABLE_EXCEPTION)
        {
            $message = $thrown->getMessage();
        }
        else
        {
            $message = INTERNAL_UNKNOWN_ERROR_MESSAGE;
        }
    }
}

switch ($page_mode)
{
    case PAGE_MODE_CONSUMER_SUCCESS:
        //TODO: it would probably be a better user experience to just log them
        // in now rather than show them this.

        echo '<div align="center">';

        echo "<img src='../images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>";

        echo "<h2 class='page-title-text'>Qur’an Tools: New Account Created</h2>";

        echo '<p>Success! Your new account has been created.</p>';

        echo '<p>We hope that you enjoy using Qur’an Tools!</p>';

        echo "<p><a href='../home.php'><button id='login-to-new-account'>Login to Your New Account</button></a></p>";

        break;

    case PAGE_MODE_REGISTER:

        echo "  <img src='/images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>";

        echo "  <h2 class='page-title-text'>Register New Account</h2>";

        echo "  <form id='register' class='form' action='register.php' method='POST'>";

        if (!empty($message))
        {
            echo "    <p class='error-message'>$message</p>";
        }

        echo '    <p class="bigger-message">Please confirm your name and choose a password to register with Qur`an Tools</p>';

        echo "    <input type='text' id='EMAIL' name='EMAIL' size=50 autocomplete=off maxlength='150' placeholder='Email Address' ";
        if (!empty($_GET['EMAIL']))
        {
            echo " value='" .
                htmlspecialchars($_GET['EMAIL'], ENT_QUOTES) .
                "' ";
        }
        if (!empty($_POST['EMAIL']))
        {
            echo " value='" .
                htmlspecialchars($_POST['EMAIL'], ENT_QUOTES) .
                "' ";
        }
        echo '>';

        echo "<div class='names'>";

        echo "    <input type='text' autocomplete=off id='FIRST_NAME' class='name-component' name='FIRST_NAME' placeholder='First name' ";
        if (!empty($_GET['FIRST_NAME']))
        {
            echo " value='" . htmlspecialchars($_GET['FIRST_NAME'], ENT_QUOTES) . "' ";
        }
        if (!empty($_POST['FIRST_NAME']))
        {
            echo " value='" . htmlspecialchars($_POST['FIRST_NAME'], ENT_QUOTES) . "' ";
        }
        echo '>';

        echo "    <input type='text' autocomplete=off id='LAST_NAME' class='name-component' name='LAST_NAME' placeholder='Last name' ";
        if (!empty($_GET['LAST_NAME']))
        {
            echo " value='" . htmlspecialchars($_GET['LAST_NAME'], ENT_QUOTES) . "' ";
        }
        if (!empty($_POST['LAST_NAME']))
        {
            echo " value='" . htmlspecialchars($_POST['LAST_NAME'], ENT_QUOTES) . "' ";
        }
        echo '>';

        echo '</div>'; // .names

        echo "    <input type='password' id='PASSWORD' name='PASSWORD' placeholder='New Password' required autocomplete='new-password'>";

        echo "    <input type='password' id='PASSWORD_AGAIN' name='PASSWORD_AGAIN' placeholder='Confirm Password' required autocomplete='new-password'>";

        echo '    <p class="message in-between">' . get_gdpr_registration_inner_html(
            $config['show_gdpr'],
            $config['gdpr_base_text'],
            get_privacy_policy_url(),
            $config['cookie_policy_url']
        ) . '</p>';

        echo "    <input name='REGISTER_BUTTON' type='submit' value='register' class='general-button'>";

        echo "    <p class='message'>Already registered? <a href='/home.php'>Login here</a>.</p>";

        echo '  </form>';
?>

<?php break;

    default:
        echo INTERNAL_UNKNOWN_ERROR_MESSAGE;
        break;
}

include 'library/footer.php';
?>
<script>
    $(document).ready(function() {
        if (typeof qt == 'undefined') qt = {};

        qt.minimum_name_length = <?php echo $config['minimum_full_name_length']; ?>

    })
</script>
<script type='text/javascript' src='register.js'></script>

</body>

</html>