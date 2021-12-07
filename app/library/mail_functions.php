<?php

namespace QT\Mail;

require_once 'vendor/autoload.php';

/**
 * CONSTANTS
 */

// attempted login types
define('SUBJECT_MESSAGE_FROM_QT_SYSTEM', 'Message from QT System');
define('SUBJECT_QT_APPLICATION_ERROR', 'QT Application Error');
define('SUBJECT_RECAPTCHA_NOT_ATTEMPTED', 'reCAPTCHA attempt not made');
define('SUBJECT_RECAPTCHA_ERROR', 'Error attempting reCAPTCHA');
define('SUBJECT_RECAPTCHA_FAIL_REGISTER_INTEREST', 'Failed reCAPTCHA while registering an interest');

define('PASSWORD_RESET_SUBJECT', 'Password Reset for Qur’an Tools');

/**
 * Create text of a password reset email. May end up in a 3rd party mail
 * provider, like mailchimp
 *
 * Use: $message = generate_password_reset_email("Bob Smith", "https://qt.org/fjldsjsl");
 * @param string $user_name  - salutation for user receiving email
 * @param string $reset_link - linmk the user needs to click to reset their password
 *
 * @return string the HTML formatted message
 */
function generate_password_reset_email_HTML($user_name, $reset_link)
{
    $message = "<p>Dear $user_name,</p>";
    $message .=
        "<p>You requested a password reset for your account at Qur&rsquo;an Tools.</p>";
    $message .=
        "<p>To reset your password, please click the link below (or copy and paste it into your web browser):</p>";
    $message .= "<p><a href='$reset_link'>$reset_link</a></p>";
    $message .=
        "<p>Please note that this link will expire in 24 hours. If you have not used it by then, you will need to request a fresh password reset.</p>";
    $message .= "<p>Best wishes,</p>";
    $message .= "<p><i>The Qur&rsquo;an Tools Team</i></p>";

    return $message;
}

/**
 * Construct and send password reset email. May end up in a 3rd party mail
 * provider, like Mailchimp or similar
 *
 * Use: $message = generate_password_reset_email("bob@example.com", "<p>Dear Bob, check this out.</p>");
 * @param string $to           - email address of user receiving email
 * @param string $html_message - linmk the user needs to click to reset their password
 *
 * @return boolean whether or not the email was accepted for delivery by the server
 */
function process_password_reset_email($to, $html_message)
{
    $error_messages = [];

    // TODO: it would be good to check that this looks like an
    //       email address, but since this will ultimately be
    //       farmed out to a third party, it's not worth spending
    //       time on it now.
    if (!isset($to) || $to === '')
    {
        array_push($error_messages, "No email address provided");
    }

    if (!isset($html_message) || $html_message === '')
    {
        array_push($error_messages, "No email body provided");
    }
    if (count($error_messages) > 0)
    {
        error_log(implode(", ", $error_messages));
        return false;
    }

    $subject = PASSWORD_RESET_SUBJECT;

    $headers = "From: " . QT_ADMIN_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $env_headers = "-f " . QT_ADMIN_EMAIL;

    return mail($to, $subject, $html_message, $headers, $env_headers);
}

/**
 * Debugging function that emails errors requiring dev input to Qur'an Tools
 *
 * Use: email_error_to_qt($thrown, $extra_message, 'Some Bad Error');
 *
 * @param Exception $exception  - an exception that has been raised
 * @param string $extra_message - an extra message we may wish to send to Qur'an Tools
 * @param string $subject       - a custom subject for the error
 *
 * @return void
 *
 * Sometimes errors happen behind the scenes that require a special developer
 * alert. It uses the mailer specified in php.ini 
 */

function email_error_to_qt(
    \Throwable $exception = null,
    string $extra_message = null,
    $subject = SUBJECT_QT_APPLICATION_ERROR
): void {
    global $config;

    if (empty($exception) && empty($extra_message))
    {
        throw new \Exception(
            'email_error_to_qt() requires either an Exception or a text error message.'
        );
    }

    $to = \join(',', $config['qt_developers']);

    $message = '<p>An error was thrown that you probably need to look into fairly promptly.</p>';

    if (!empty($extra_message))
    {
        $message .=
            PHP_EOL . '<p><b>Extra Message</b>: ' . $extra_message . '</p>';
    }

    if (!empty($exception))
    {
        $message .=
            '<p><b>Error Message</b>: ' .
            \htmlspecialchars($exception->getMessage()) .
            '</p>';
        $message .=
            '<p><b>Stack Trace</b>:</p><pre>' .
            $exception->getTraceAsString() .
            '<code></code></pre>';
    }

    $headers = 'From: ' . QT_ADMIN_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $env_headers = '-f ' . QT_ADMIN_EMAIL;

    // mail($to, $subject, $message);
    mail($to, $subject, $message, $headers, $env_headers);
}
/**
 * Email system generated message to Qur'an Tools admin
 *
 * Use: email_qt($message, $subject);
 *
 * @param string $message - the message for Qur'an Tools
 * @param string $subject - a custom subject for the email
 *
 * @return void
 */
function email_qt(
    string $message,
    string $subject = SUBJECT_MESSAGE_FROM_QT_SYSTEM
): void {
    global $config;

    // $to = \join(',', QT_ADMIN_EMAIL);
    $to = QT_ADMIN_EMAIL;

    $message = $message .
        '<br><p>This message was generated by the Qur\'an Tools website for Qur\'an Tools admin staff.</p>';

    $headers = 'From: ' . QT_ADMIN_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $env_headers = '-f ' . QT_ADMIN_EMAIL;

    mail($to, $subject, $message, $headers, $env_headers);
}
