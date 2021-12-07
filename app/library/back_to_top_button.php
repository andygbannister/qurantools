<?php

function move_back_to_top_button()
{
    // called if the floating page navigator is showing and thus we need to slightly elevate the back to top button

    global $pages_needed;

    if ($pages_needed > 1)
    {
        $page_navi_location = db_return_one_record_one_field("SELECT `Preference Floating Page Navigator` FROM `USERS` WHERE `User ID`='" . db_quote($_SESSION["UID"]) . "'");

        if ($page_navi_location == 1)
        {
            ?>
			<script>
			$("#scrollTopFloatingButton").css({bottom:'45px'});	
			</script>
			<?php
        }
    }
}

// code to produce the "back to top button"

echo "<button onclick='topFunction()' id='scrollTopFloatingButton' title='Go to top'>Back To Top</button>";

?>