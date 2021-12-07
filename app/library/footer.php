<?php

// QUR’AN TOOLS FOOTER

echo "</main>";

echo "<footer class='qt-site-footer'>";

echo "<div class='footer-content-holder'>";

echo "<a href='" . OPEN_SOURCE_REPO_URL . "' target='_blank' class='linky'>";

echo "<span class='footer-icons'>";
echo "<img class='qt-mini-logo' src='/images/qt-mini-logo.png' alt='Small QT Logo'>";
echo "<img class='qt-mini-logo print' src='/images/logos/qt_logo_only.png' alt='Small QT Logo'>";
echo "</span>";

echo "</a>";

echo "Qur&rsquo;an Tools is an open source project, released under the <a class='linky-hover' href='" . QT_LICENSE_URL . "'>GNU General Public License</a> with <a class='linky-hover' href='" . QT_TERMS_URL . "'>terms of use</a>.";

// print the page rendering time if appropriate

if (isset($page_time_start))
{
    $page_time_to_render = microtime(true) - $page_time_start;
    // echo " (Page rendered in ".number_format($page_time_to_render, 3)." seconds)";
}

echo "</div>";

echo "</footer>";

// echo "<BR>  ******************  SESSION ******************** <BR>";
// var_dump($_SESSION);

// echo "<BR>  ******************  POST ******************** <BR>";
// var_dump($_POST);

// echo "<BR>  ******************  SERVER ******************** <BR>";
// var_dump($_SERVER);

// foreach ($_SERVER as $key => $value)
// {
//     echo "$key => $value <br />";
// }
