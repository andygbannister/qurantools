<?php

require_once "flash_functions.php";

    $flash = QT\Flash\get_flash();

    if (!empty($flash))
    {
        echo "<div id='flash' class='" . implode(' ', QT\Flash\get_classes($flash)) . "'>";

        //TODO: write method that checks whether the flash is closeable
        if ($flash['is_closeable'])
        {
            echo "<div class='close-button'>×</div>";
        }

        echo "  <p>" . QT\Flash\get_flash()['message'] . "</p>";

        echo "</div>";

        //TODO: write method that check whether the flash is persistent
        if (!$flash['is_persistent'])
        {
            // only show flash once
            QT\Flash\clear_flash();
        }
    }
    echo "<script type='text/javascript' src='/library/js/flash.js'>
</script>";
