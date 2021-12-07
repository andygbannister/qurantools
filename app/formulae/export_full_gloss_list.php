<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

?>
<html>

<head>
    <?php
        require '../library/standard_header.php';
        window_title("Edit Formula Full Gloss");
    ?>

    <?php

    // menubar etc

    require "../library/menu.php";

// finish setting up

 echo "</head><body class='qt-site'><main class='qt-site-content'>";

echo "<div align=center>";

echo "<h2 class='page-title-text'>Formula Full Gloss Export List</h2>";

$result = db_query(
    "
SELECT DISTINCT(`FORMULA LOWER`), `FORMULA FULL GLOSS` FROM `FORMULA-LIST`
WHERE `FORMULA FULL GLOSS`!=''
GROUP BY `FORMULA LOWER`"
);

for ($i = 0; $i < db_rowcount($result); $i++)
{
    $ROW = db_return_row($result);

    echo "<p>";
    echo "UPDATE `FORMULA-LIST` SET `FORMULA FULL GLOSS`='" . db_quote($ROW["FORMULA FULL GLOSS"]) . "' WHERE `FORMULA LOWER`='" . db_quote($ROW["FORMULA LOWER"]) . "';";
    echo "</p>";
}

echo "<p><b>Done</b></p>";

echo "</div>";

// print footer

require_once "library/footer.php";

?>

    </body>

</html>