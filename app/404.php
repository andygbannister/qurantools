<?php

session_start();

require_once 'library/config.php';
require_once 'library/functions.php';

// only load these functions if a user is logged in

if (isset($_SESSION['UID']))
{
    include "auth/auth.php";
}

?>

<!-- // customised 404 error page. -->

<!DOCTYPE html>
<html>

<head>

    <?php
    include 'library/standard_header.php';
    window_title("Page Not Found");
?>

    <script type="text/javascript">
        var count = 10;

        function countDown() {
            var timer = document.getElementById("timer");
            if (count > 0) {
                count--;
                timer.innerHTML = "Redirecting to the Qur&rsquo;an Tools home page in " + count + " second" + (count ==
                    1 ? "" : "s") + ".";
                setTimeout("countDown()", 1000);
            } else {
                window.location.href = '\home.php';
            }
        }
    </script>

</head>

<body class='qt-site' id='_404'>
    <main class='qt-site-content'>
        <?php

// menubar

if (isset($_SESSION['UID']))
{
    include "library/menu.php";
}
else
{
    include "library/menu_minimum.php";
}

echo "<section class='page-content'>";

echo "  <img class='qt-big-logo-header' src='/images/logos/qt_logo_only.png' alt='Large QT Logo'>";

echo "  <h2 class='page-title-text'>Oh dear, something has gone wrong!</h2>";

?>

        <p>Well now, this is very embarrassing. The page you asked for can&rsquo;t seem to be found!</p>

        <?php

if (isset($_SESSION['UID']))
{
    ?>

        <span id="timer">
            <script type="text/javascript">
                countDown();
            </script>
        </span>

        <p>Or <a href='/home.php' class='linky-light'>click here</a> to head there yourself now.</p>

        <?php
}
else
{
    echo "<p><a href='/auth/login.php' class='linky-light'>Click here to login to Qur&rsquo;an Tools.</a></p>";
}

// print footer

include "library/footer.php";

?>

</body>

</html>