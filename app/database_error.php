<!-- // database error page. -->

<?php require_once 'library/config.php'; ?>

<!DOCTYPE html>
<html>

<head>

    <title>Database Connection Error</title>

    <?php include 'library/standard_header.php'; ?>

</head>

<body class='qt-site' id='_404'>
    <main class='qt-site-content'>

        <?php include 'library/menu_minimum.php'; // menubar (minimal version, no d/b connection needed)?>

        <section class='page-content'>

            <img class='qt-big-logo-header' src='/images/logos/qt_logo_only.png' alt='Large QT Logo'>

            <h2 class='page-title-text'>Oh dear, this is embarrassing. Something has gone wrong :-(</h2>

            <p>Qur&rsquo;an Tools was unable to connect to its databases on the server.</p>

            <p>Please contact one of the Qur&rsquo;an Tools team on <?php QT_ADMIN_EMAIL ?> if the error persists.</p>

            <?php include 'library/footer.php'; // print footer?>

</body>

</html>