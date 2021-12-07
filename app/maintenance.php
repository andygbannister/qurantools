<?php

// MAINTENANCE PAGE: displayed when we are doing exciting things behind the scenes

require_once 'library/config.php'; ?>
<html>

<head>
    <title>Down For Maintenance</title>
    <?php include 'library/standard_header.php'; ?>

</head>

<body class='qt-site' id='_404'>
    <main class='qt-site-content'>
        <?php
        // menubar (minimal version, no d/b connection needed)

        include 'library/menu_minimum.php';

        echo "<section class='page-content'>";

        echo "  <img class='qt-big-logo-header' src='/images/logos/qt_logo_only.png' alt='Large QT Logo'>";

        echo "  <h2 class='page-title-text'>Sorry, Qur&rsquo;an Tools is down for maintenance</h2>";
        ?>

        <p>We are busy doing exciting things behind the scenes.</p>

        <p>Please check back again soon.</p>

        <?php include 'library/footer.php'; ?>

</body>

</html>