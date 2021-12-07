<?php

require_once '../library/config.php';
require_once 'library/functions.php';

session_start();

// deal with a bunch of cookies, sessions and server variables
log_user_out();

// Finally, destroy the session.
session_destroy();

echo "<!DOCTYPE html>";

echo "<head>";

include 'library/standard_header.php';

window_title("Logout");

echo "</head>";

echo "<body class='qt-site'>";
echo "  <main class='qt-site-content'>";

include "library/menu.php";

echo "  <section class='page-content'>
          <img src='/images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>
          <h2 class='page-title-text'>Thank you for using Qur’an Tools</h2>
          <p id='logged-out-message'>You have successfully logged out.</p>
          <div id='next-steps'>
            <div class='step'>
                <a href='" . $config['main_app_url'] . "/home.php'>
                    <button>Login in to Qur&rsquo;an Tools again</button>
                </a>
            </div>
        </div>";   // #next-steps

include "library/footer.php";
exit;
