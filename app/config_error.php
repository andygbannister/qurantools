<?php

// customised 404 error page.

session_start();
session_regenerate_id();

require_once 'library/config.php';
require_once 'library/functions.php';

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title('Configuration Error');
    ?>

</head>

<body class='qt-site' id='_404'>
    <main class='qt-site-content'>
        <?php include 'library/menu.php'; // menubar?>

        <section class='page-content'>

            <img class='qt-big-logo-header' src='/images/logos/qt_logo_only.png' alt='Large QT Logo'>

            <h2 class='page-title-text'>Oh dear, this is embarrassing. Something has gone wrong ...</h2>


            <p>Please contact one of the Qur&rsquo;an Tools team on <?php QT_ADMIN_EMAIL ?> if the error persists.</p>

            <?php include 'library/footer.php'; // print footer?>

</body>

</html>