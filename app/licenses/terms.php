<?php

// GNU GPL License Page.

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once '../library/functions.php';

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    require '../library/standard_header.php';
    window_title("Terms of Use");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large QT Logo">
                <h2 class='page-title-text'>Terms of Use</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>
                    By downloading this data, you agree to the terms and conditions of the <a class='linky-light'
                        href='<?php echo  QT_LICENSE_URL ?>'>GNU
                        License</a>.</p>
                <pre>
#  Qur'an Tools (Version 2.0)
#  Copyright (C) 2021 Andy Bannister
#  License: GNU General Public License
#
#  TERMS OF USE:
#
#  - This copyright notice shall be included in all copies of
#    the source code, and shall be reproduced appropriately in
#    all works derived from or containing substantial portion 
#    of these files.
</pre>

                <?php
                if (!empty(get_privacy_policy_url()))
                  { ?>
                <p>
                    <a href='<?php echo get_privacy_policy_url(); ?>'
                        class='linky-light'>Privacy
                        Policy</a>
                </p>
                <?php }
                ?>

            </section>

        </div>

        <?php include "library/footer.php"; ?>

</body>

</html>