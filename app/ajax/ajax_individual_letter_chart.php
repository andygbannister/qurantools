<?php

session_start();
session_regenerate_id();

// basic security check here
if (!isset($_SESSION["UID"]))
{
    exit;
}

?>
<script>
alert("hello");
</script>

<?php

require_once '../library/config.php';
require_once 'library/functions.php';

echo "<p>GETS = " . $_POST["S"] . "</p>";
echo "<p>GETV = " . $_POST["V"] . "</p>";

?>