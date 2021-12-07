<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once 'library/quick_tips.php';

// function ajax_failure($message) {
//   // maybe set some headers or something in here for future error trapping
// }

if (!isset($_POST['action']))
{
    // ajax_failure( 'No action specified!');
    exit;
}
else
{
    switch ($_POST['action']) {
    case 'get_quick_tip':
        if (isset($_POST["quick_tip_id"]))
        {
            $new_quick_tip_id = $_POST["quick_tip_id"];
            update_current_quick_tip($new_quick_tip_id, $logged_in_user);
            echo_tip_html($new_quick_tip_id);
        }
        break;

    case 'hide_quick_tips':
        update_tip_preference(false, $logged_in_user);
        break;

    default:
        break;
  }
}

function echo_tip_html($quick_tip_id)
{
    $quick_tip = get_quick_tip($quick_tip_id);

    echo "			<div class='header'>";
    echo "				Quick Tips";
    echo " 				<a href='#' onclick='hide_quick_tips(event)' id='hide-quick-tips' class='linky hide-quick-tips'>&times;</a>";
    echo "			</div>";                   // .header

    echo "			<div class='content'>";
    echo "        <div class='quick-tip'>";
    echo "          " . $quick_tip['Quick Tip'];
    echo "        </div>";                 // .quick-tip

    echo "        <div class='example'>";

    echo "<a href='#' id='use-quick-tip' onClick=\"overwriteInputText('" . htmlspecialchars(addslashes($quick_tip['Example'])) . "');\">";
    echo "          " . $quick_tip['Example'];
    echo "</a>";

    echo "        </div>";                 // .example

    echo "      </div>";                   // .content

    echo "			<div class='footer'>";

    // only show the more help link if it has content

    if (!empty($quick_tip['More Help Link']))
    {
        echo "        <a href='" . $quick_tip['More Help Link'] . "' target='_blank' id='more-help-link' class='linky'>Learn More About This</a>";
    }
    else
    {
        echo "&nbsp;";
    }

    echo "        <div class='quick-tip-navigation'>";

    if (!empty($quick_tip['Previous Quick Tip ID']))
    {
        echo "        <a href='#' onclick='show_quick_tip(event, " . $quick_tip['Previous Quick Tip ID'] . ");' id='previous-quick-tip' class='button linky'>&lt; Previous Tip</a>";
    }

    if (!empty($quick_tip['Next Quick Tip ID']))
    {
        echo "        <a href='#' onclick='show_quick_tip(event, " . $quick_tip['Next Quick Tip ID'] . ");' id='next-quick-tip' class='button linky'>Next Tip &gt;</a>";
    }

    echo "        </div>";                 // quick-tip-navigation>

    echo "      </div>";                   // .footer
}
