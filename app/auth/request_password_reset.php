<?php

session_start();

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'library/mail_functions.php';

echo '<!DOCTYPE html>';

echo '<head>';

include 'library/standard_header.php';
window_title('Reset Password');

// reCAPTCHA code from
// https://github.com/google/ReCAPTCHA
// https://github.com/google/recaptcha/blob/master/examples/recaptcha-v3-verify.php
// https://github.com/google/recaptcha/issues/281

/**
 * The password reset page requires Google reCAPTCHA v3.
 */
?>

<script type="text/javascript">
    qt.google_recaptcha_site_key =
        '<?php echo $config['google_recaptcha_site_key_v3']; ?>';
    qt.google_recaptcha_action =
        '<?php echo GOOGLE_RECAPTCHA_V3_ACTION_RESET_PASSWORD; ?>'
</script>';
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $config['google_recaptcha_site_key_v3']; ?>">
</script>

</head>

<body class="qt-site">
    <main class="qt-site-content">

        <?php
        include 'library/menu.php';

        // have they given us an email address?

        if (isset($_POST['RESET_EMAIL_ADDRESS']))
        {
            // Only try to determine reCAPTCHA if we got a reCAPTCHA value. We don't want this to be dependent on Google reCAPTCHA working.
            if (isset($_POST['g-recaptcha-response']))
            {
                $recaptcha = new \ReCaptcha\ReCaptcha($config['google_recaptcha_secret_key_v3']);
                $resp      = $recaptcha
                    ->setExpectedHostname($_SERVER['SERVER_NAME'])
                    ->setExpectedAction(GOOGLE_RECAPTCHA_V3_ACTION_RESET_PASSWORD)
                    ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                $recaptcha_success = $resp->isSuccess();

                // If reCAPTCHA failed for a real user, then show some kind of human
                // friendly error message
                if (!$recaptcha_success)
                {
                    $error = 'Unable to reset that email address. Please try again.';

                    // TODO: Maybe alert a human with the details of reCAPTCHA fails?
                }
            }

            if (!isset($recaptcha_success) || $recaptcha_success)
            {
                $user = get_user_by_email(
                    db_quote(strtoupper($_POST['RESET_EMAIL_ADDRESS']))
                );

                // TODO: This is a bit of a security hole spotted during a tidy up
                // in Sep 20. This code lets a malicious user know that an email does
                // or doesn't exist. They could keep pumping email addresses in here
                // until they don't get the 'unknown email' message. Better to just
                // send an email to the address they give, but the content of the email
                // would differ depending on whether the account exists or not.
                //
                // For more info see:
                // https://postmarkapp.com/guides/password-reset-email-best-practices
                // https://www.troyhunt.com/everything-you-ever-wanted-to-know/

                if (empty($user))
                {
                    $error = 'There is no known user with the email address <b>' .
                        db_quote($_POST['RESET_EMAIL_ADDRESS']) .
                        '</b>';
                }
                else
                {
                    // generate random password
                    $code = generate_reset_password_code();

                    // save the new password
                    $user = update_user_by_id($user['User ID'], [
                        'Reset Code'     => $code,
                        'Reset Timecode' => time()
                    ]);

                    $reset_link = $config['main_app_url'] . "/auth/password_reset.php?R=$code";

                    // build and process reset email
                    $user_name = generate_user_name(
                        $user['First Name'],
                        $user['Last Name']
                    );
                    $html_message = QT\Mail\generate_password_reset_email_HTML(
                        $user_name,
                        $reset_link
                    );

                    $to        = $user['Email Address'];
                    $mail_sent = QT\Mail\process_password_reset_email(
                        $to,
                        $html_message
                    );

                    echo "<body class='qt-site'>
                <main class='qt-site-content'>
                    <div align=center>
                        <img src='/images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>
                        <h2 class='page-title-text'>Password Reset</h2>";

                    // are we running on a local install(with the word local in the hostname)?
                    if (strpos($_SERVER['HTTP_HOST'], 'local') !== false)
                    {
                        echo "<div class='localhost-password-reset'>
                    <p>It looks like you are running on a local or test install.</p>
                    <p>On the live server, the following message would have been sent:</p>
                    <hr>";
                        echo $html_message;
                        echo "  <hr>
                    <p>If you are running <a href='https://mailcatcher.me/' target='_blank'>mailcatcher</a>, then the email should be at <a href='http://127.0.0.1:1080/' target='_blank'>http://127.0.0.1:1080/</a></p>
                  </div>";
                    }

                    if ($mail_sent)
                    {
                        echo "<p>An email has been sent to you with a password reset link.</p>
                    <p>Simply click the link in that email to reset your password.</p>
                    <p>If the email doesn’t immediately arrive, please check your trash/junk folder.</p>";
                    }
                    else
                    {
                        echo "<p>Sorry, but something went wrong sending you a reset link.</p>
                    <p>You will need to email " .
                            QT_ADMIN_EMAIL .
                            ' and request help manually.</p>';
                    }

                    echo "<a href='/home.php'>
                        <button id='try-to-login-again'>Try to Login Again</button>
                    </a>
                </main>
            </body>";

                    include 'library/footer.php';

                    exit();
                }
            }
        }

        echo "<img src='/images/logos/qt_logo_only.png' style='margin-top:45px;' class='qt-big-logo-header' alt='Large QT Logo'>";

        echo "<h2 class='page-title-text'>Qur’an Tools: Reset Your Password</h2>";
        ?>

        <div class="form" id='RegistrationForm'>

            <?php
            echo "<form id='reset-password' action='/auth/request_password_reset.php' method='POST'>";

            if (isset($error))
            {
                echo "<p class='error-message'>$error</p>";
            }
            else
            {
                echo "<p class='bigger-message'>Please enter your registered email address below and a password reset link will be emailed to you.</p>";
            }

            echo "<input type='text' id='RESET_EMAIL_ADDRESS' NAME='RESET_EMAIL_ADDRESS' size=50 autocomplete='off' maxlength=150 autofocus placeholder='Email Address'>";

            // echo '            <button name ="submit-button" id= "submit-button" type="submit" class="g-recaptcha"
            //                     data-sitekey="'.$config['recaptcha_site_key'].'"
            //                     data-callback="onSubmit"
            //                     data-action="'.GOOGLE_RECAPTCHA_V3_ACTION_RESET_PASSWORD.'">Email me a password reset link</button>';
            echo '            <button name ="submit-button" id= "submit-button" type="submit">Email me a password reset link</button>';

            echo "<p class='message'>Remembered your password after all? <a href='/home.php'>Login here</a>.</p>";

            echo '</form>';

            include 'library/footer.php';
            ?>
            <script type='text/javascript' src='request_password_reset.js'></script>

</body>