<?php

// load user credentials
session_start();
session_regenerate_id();

// get config info
require_once '../library/config.php';

// load misc functions library
require_once 'library/functions.php';

// if the page is only for users who are authenticated, include the below (otherwise comment it out)
require_once 'auth/auth.php';
?>

<html>

<head>
    <?php

    include 'library/standard_header.php';

    window_title('');

    ?>

</head>

<body class='qt-site' id='page-name'>
    <main class='qt-site-content'>

        <?php include 'library/menu.php'; ?>

        <div class='page-content'>
            <section class='page-header'>

                <img src='/images/logos/qt_logo_only.png' class='qt-big-logo-header' alt='Large QT Logo'>
                <h2 class='page-title-text'>Hello, I am a title</h2>

            </section> <!-- .page-header -->

            <section class='page-body'>

                <!-- main body of page would follow here -->

                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip ex
                    ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
                    eu fugiat
                    nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in ulpa qui officia deserunt
                    mollit
                    anim id est laborum.</p>


            </section> <!-- .page-body -->
        </div> <!-- .page-content -->

    </main>

    <?php include 'library/footer.php'; ?>

</body>

</html>