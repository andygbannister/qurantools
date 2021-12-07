<?php

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

?>
<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title('Preferences & Account Settings');
    ?>

    <script src="library/js/jscolor.js"></script>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>

        <?php

        $message       = '';
        $message_class = 'message-warning';

        // have they reset?

        if (isset($_GET['RESET']))
        {
            if ($_GET['RESET'] == 'Y')
            {
                $message       = 'Preferences reset to the default';
                $message_class = 'message-success';
                db_query(
                    "UPDATE `USERS` SET `Preferred Highlight Colour`='FFFF00', `Preferred Highlight Colour Lightness Value`='127', `Preferred Cursor Colour`='DDDDDD', `Preferred Translation`=1, `Preferred Verse Count`=50, `Preferred Keyboard Direction`='LTR', `Preference Italics Transliteration`=1, `Preference Show Quick Tips`=1, `Preference Floating Page Navigator`=1, `Preference Formulaic Glosses`=1 WHERE `User ID`='" .
                        db_quote($_SESSION['UID']) .
                        "'"
                );
            }
        }

        // have they changed their name?

        if (isset($_POST['NEW_FIRST_NAME']) || isset($_POST['NEW_LAST_NAME']))
        {
            $new_full_name = generate_user_name(
                $_POST['NEW_FIRST_NAME'],
                $_POST['NEW_LAST_NAME']
            );

            if (
                strlen($new_full_name) < $config['minimum_full_name_length']
            ) {
                $message       = 'Your name is too short';
                $message_class = 'message-warning';
            }
            else
            {
                $columns['First Name'] = $_POST['NEW_FIRST_NAME'];
                $columns['Last Name']  = $_POST['NEW_LAST_NAME'];
                $data_to_update        = 'Name';
            }
        }

        // Make database changes

        if (isset($columns))
        {
            $user = update_user_by_id($_SESSION['UID'], $columns, $logged_in_user);
            if (!empty($user))
            {
                $logged_in_user = $user;
                set_user_session_variables($logged_in_user);
                $message       = "$data_to_update updated successfully";
                $message_class = 'message-success';
            }
            else
            {
                $message       = "$data_to_update not updated successfully";
                $message_class = 'message-warning';
            }
        }

        // have they changed the password?
        if (
            isset($_POST['OKbutton']) &&
            isset($_POST['PASSWORD1']) &&
            isset($_POST['CONFIRM_PASSWORD']) &&
            isset($_POST['OLD_PASSWORD'])
        ) {
            if ($_POST['OLD_PASSWORD'] == '')
            {
                $message = 'Your failed to provide your existing password!';
            }

            // check the existing password they have provided matches what we have on file

            $password_hash = db_return_one_record_one_field(
                "SELECT `Password Hash` FROM `USERS` WHERE `User ID`='" .
                    db_quote($_SESSION['UID']) .
                    "'"
            );

            if (
                !hash_equals(
                    $password_hash,
                    crypt($_POST['OLD_PASSWORD'], $password_hash)
                )
            ) {
                $message = 'Sorry, your existing password was entered incorrectly!';
            }

            if ($_POST['PASSWORD1'] == '')
            {
                $message = 'Your new password cannot be blank!';
            }

            if ($_POST['PASSWORD1'] != $_POST['CONFIRM_PASSWORD'] && $message == '')
            {
                $message = 'The two passwords do not match. Please try again!';
            }

            if (strlen($_POST['PASSWORD1']) < 8 && $message == '')
            {
                $message = 'Your password should be at least 8 characters long!';
            }

            if ($message == '')
            {
                $hash = hash_password($_POST['PASSWORD1']);

                db_query(
                    "UPDATE `USERS` SET `Password Hash`='" .
                        db_quote($hash) .
                        "' WHERE `User ID`='" .
                        db_quote($_SESSION['UID']) .
                        "'"
                );

                $message       = 'Your password was successfully changed.';
                $message_class = 'message-success';

                // finally (very important!) refresh the $_SESSION variable

                $_SESSION['password_hash'] = $hash;
                $logged_in_user            = get_logged_in_user();
            }
        }

        // menubar

        include 'library/menu.php';
        include "library/flash.php";

        echo '  <h2 class=page-title-text>Preferences and Account Settings</h2>';

        echo "  <div id='floating-message'>Preference Changes Saved</div>";

        if ($message != '')
        {
            echo "<div class='message $message_class'>$message</div>";
        }

        // save a new random token to the user file for this user, to validate any Ajax transactions for this session

        $AJAXtoken = bin2hex(openssl_random_pseudo_bytes(6));

        db_query(
            "UPDATE `USERS` SET `AJAX Token`= '" .
                db_quote($AJAXtoken) .
                "' WHERE `User ID`='" .
                db_quote($_SESSION['UID']) .
                "'"
        );

        // ==============================================================================

        // =========== NEW NAME PANEL ==================================

        echo "<div id='NAME_PANEL' class='dialog-box' style='display:none;'>";

        echo '<h3>Change Your Name</h3>';

        echo "<form id='new-username-form' class='form' action='preferences.php' method='POST'>";

        echo "<p class='bigger-message'>If you wish to change your name, you may do so by editing it below.</p>";

        echo "<input id='NEW_FIRST_NAME' type=text NAME='NEW_FIRST_NAME' size=42 maxlength=150 autocomplete='off' placeholder='First Name' autofocus VALUE='" .
            htmlspecialchars($logged_in_user['First Name']) .
            "'>";

        echo "<input id='NEW_LAST_NAME' type=text NAME='NEW_LAST_NAME' size=42 maxlength=150 autocomplete='off' placeholder='Last Name' autofocus VALUE='" .
            htmlspecialchars($logged_in_user['Last Name']) .
            "'>";

        echo "<button type='SUBMIT' name='DO_NAME' value='OK'>SAVE CHANGES</button>";

        echo "<button type='button' id='cancel-name-change' class='cancel-button' name='CANCEL' onClick=\"$('#NAME_PANEL').toggle();\">Cancel</button>";

        echo '</form>';

        echo '</div>';

        // ==============================================================================

        // =========== NEW PASSWORD PANEL ==================================

        // The height has to be set here since PASSWORD_PANEL is used elsewhere with
        // slightly different content - and there are a large number of specifically
        // sized sub-components that would all need to be adjusted (across various other
        // pages) to get this sizing right.
        echo "<div id='PASSWORD_PANEL' class='dialog-box' style='display:none'>";

        echo '<h3>Change Password</h3>';

        echo "<form id='new-password-form' class='form' action='preferences.php' method=POST>";

        echo "<p class='bigger-message'>Please confirm your old password<br>and then provide a new one:</p>";

        echo "<input type='password' ID='OLD_PASSWORD' NAME='OLD_PASSWORD' autofocus size=50 maxlength=150 autofocus placeholder='Please Type Your Old Password'>";

        echo "<input onkeyup=\"checkPass(1, 450);\" return false;\" type='password' ID='pass1' NAME='PASSWORD1' size=50 maxlength=150 placeholder='New Password'>";

        echo "<div ID='PasswordWarning1' class='PasswordWarning'>Any messages about password 1</div>";

        echo "<input onkeyup=\"checkPass(2, 450);\" return false;\" type=password ID=pass2 NAME=CONFIRM_PASSWORD size=50 maxlength=150 placeholder='Confirm Password'>";

        echo "<div ID='PasswordWarning2' class='PasswordWarning'>Any messages about password 2</div>";

        echo "<button type='SUBMIT' name='OKbutton' value='OK'>CHANGE PASSWORD</button>";

        echo "<button type='button' id='cancel-password-change' class='cancel-button' name='CANCEL' onClick=\"$('#PASSWORD_PANEL').toggle(); $('#pass1').val(''); $('#pass2').val(''); $('#PasswordWarning1, #PasswordWarning2').hide();\">Cancel</button>";

        echo '</form>';

        echo '</div>';

        // ==============================================================================

        echo "<table id='preferences'>";

        echo "<tr>
          <th colspan='3'>My Account</th>
      </tr>";

        echo "<tr>
        <td>Email</td>";

        echo "  <td colspan='2'>" .
            htmlspecialchars($logged_in_user['Email Address']) .
            '</td>';

        echo '</tr>';

        echo "<tr>
        <td>Name</td>";
        echo '  <td>' .
            htmlspecialchars(
                generate_user_name(
                    $logged_in_user['First Name'],
                    $logged_in_user['Last Name']
                )
            ) .
            '</td>';
        echo "  <td>
                    <input id='change-name' title='Change name' type='button' class='preferences-button' value='Change Name' onClick=\"$('#NAME_PANEL').toggle();\">
                    </td>
                </tr>";

        echo "<tr>
                <td>Password</td>
                <td>**************</td>
                <td>
                <input id='change-password' title='Change password' type='button' class='preferences-button' value='Change Password' onClick=\"$('#PASSWORD_PANEL').toggle();\">";
        echo '  </td>';
        echo '</tr>';

        echo '</table>';

        // ==============================================================================

        echo "<table id='preferences'>";

        echo '<tr><th colspan=2 align=center>';

        echo '<b>Customisation</th></tr>';

        echo '<tr>';
        echo '<td>Search Result Highlight Colour</td>';
        echo '<td>';
        echo '<input class=jscolor value=' .
            $logged_in_user['Preferred Highlight Colour'] .
            " onChange=\"save_preference('ColourSearch', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\">";
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>Arabic/Transliteration Cursor Highlight Colour</td>';
        echo '<td>';
        echo '<input class=jscolor value=' .
            $logged_in_user['Preferred Cursor Colour'] .
            " onChange=\"save_preference('ColourCursor', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\">";
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>';
        echo 'Default Qur’an Translation';
        echo '</td>';

        echo '<td>';

        echo "<select id=TRANSLATION name=TRANSLATION onChange=\"save_preference('Translator', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\">";

        // populate the translation pick list using the TRANSLATION TABLE

        $result_translation = db_query(
            'SELECT * FROM `TRANSLATION-LIST` ORDER BY `TRANSLATION NAME`'
        );

        for ($i = 0; $i < db_rowcount($result_translation); $i++)
        {
            $ROW_TRANSLATION = db_return_row($result_translation);

            echo '<option id=' .
                $ROW_TRANSLATION['TRANSLATION ALL CAPS NAME'] .
                ' value=' .
                $ROW_TRANSLATION['TRANSLATION ID'];

            if (
                $logged_in_user['Preferred Translation'] ==
                $ROW_TRANSLATION['TRANSLATION ID']
            ) {
                echo ' selected';
            }
            echo '>' . $ROW_TRANSLATION['TRANSLATION NAME'] . '</option>';
        }

        echo '</select>';

        echo '</td>';

        echo '</tr>';

        // verses to show per page

        echo '<tr>';

        echo '<td>';
        echo 'Verses to Show per Page';
        echo '</td>';

        echo '<td>';

        echo "<select id='VERSES_TO_SHOW' name='VERSES_TO_SHOW' onChange=\"save_preference('Verses', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\">";

        $verse_options = [15, 25, 40, 50, 75, 100, 150, 200];

        foreach ($verse_options as $verse_value)
        {
            echo "<option id=$verse_value value=$verse_value";
            if ($logged_in_user['Preferred Verse Count'] == $verse_value)
            {
                echo ' selected';
            }
            echo ">$verse_value</option>";
        }

        echo '</select>';

        echo '</td>';

        echo '</tr>';

        // default mode in Verse Browser

        echo '<tr>';

        echo '<td>';
        echo 'Default Mode in Verse Browser';
        echo '</td>';

        echo '<td>';

        echo "<select id=DEFAULT_MODE name=DEFAULT_MODE onChange=\"save_preference('Mode', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\">";

        $mode_options = ['Reader', 'Interlinear', 'Parse'];

        for ($i = 0; $i < sizeof($mode_options); $i++)
        {
            echo "<option id=" . $mode_options[$i] . " value=$i";
            if ($logged_in_user['Preferred Default Mode'] == $i)
            {
                echo ' selected';
            }
            echo ">" . $mode_options[$i] . " Mode</option>";
        }

        echo '</select>';

        echo '</td>';

        echo '</tr>';

        // Italicized Transliterations

        echo '<tr>';

        echo '<td valign=top>';
        echo 'Use Italics for Transliteration';
        echo '</td>';

        echo '<td>';

        echo '<input type=radio name=italics value=1 ';
        if ($logged_in_user['Preference Italics Transliteration'] == '1')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('Italics', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Yes';

        echo '<br><input type=radio name=italics value=0 ';
        if ($logged_in_user['Preference Italics Transliteration'] == '0')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('Italics', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> No';

        echo '</td>';

        echo '</tr>';

        // Hide the Transliteration (requested by Gabriel Said Reynolds)

        echo '<tr>';

        echo '<td valign=top>';
        echo 'Hide Transliteration in Verse Browser<br><span class=smaller_text_for_mini_dialogs>(e.g. only show the Arabic and your chosen translation)</span>';
        echo '</td>';

        echo '<td>';

        echo '<input type=radio name=hidetransliteration value=1 ';
        if ($logged_in_user['Preference Hide Transliteration'] == '1')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('HideTransliteration', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Yes';

        echo '<br><input type=radio name=hidetransliteration value=0 ';
        if ($logged_in_user['Preference Hide Transliteration'] == '0')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('HideTransliteration', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> No';

        echo '</td>';

        echo '</tr>';

        // Show formulaic glosses

        echo '<tr>';

        echo '<td valign=top>';
        echo 'Show Glosses Underneath Formulae';
        echo '</td>';

        echo '<td>';

        echo "<input id='formulaic-glosses' type='radio' name='FormulaicGlosses' value=1 ";
        if ($logged_in_user['Preference Formulaic Glosses'] == '1')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('FormulaicGlosses', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Yes';

        echo "<br><input id='formulaic-glosses' type='radio' name='FormulaicGlosses' value=0 ";
        if ($logged_in_user['Preference Formulaic Glosses'] == '0')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('FormulaicGlosses', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> No';

        echo '</td>';

        echo '</tr>';

        // Show Quick Tips

        echo '<tr>';

        echo '<td valign=top>';
        echo 'Show Quick Tips<br><span class=smaller_text_for_mini_dialogs>(Quick Tips appear on the home page and are a great learning tool)</span>';
        echo '</td>';

        echo '<td>';

        echo "<input id='show-quick-tips' type=radio name=ShowQuickTips value=1 ";
        if ($logged_in_user['Preference Show Quick Tips'] == '1')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('ShowQuickTips', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Yes';

        echo "<br><input id='hide-quick-tips' type=radio name=ShowQuickTips value=0 ";
        if ($logged_in_user['Preference Show Quick Tips'] == '0')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('ShowQuickTips', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> No';

        echo '</td>';

        echo '</tr>';

        // Arabic keyboard Direction

        echo '<tr>';

        echo '<td valign=top>Arabic Keyboard Direction</td>';

        echo '<td>';

        echo '<input type=radio name=keyboard value=LTR ';
        if ($logged_in_user['Preferred Keyboard Direction'] == 'LTR')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('Keyboard', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Left to Right';

        echo '<br><input type=radio name=keyboard value=RTL ';
        if ($logged_in_user['Preferred Keyboard Direction'] == 'RTL')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('Keyboard', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Right to Left';

        echo '</td>';

        // Floating Page Navigator

        echo '<tr>';

        echo '<td valign=top>Page Navigator Position</td>';

        echo '<td>';

        echo '<input type=radio name=FloatingPages value=1 ';
        if ($logged_in_user['Preference Floating Page Navigator'] == '1')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('FloatingPages', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Float at the bottom corner';

        echo '<br><input type=radio name=FloatingPages value=0 ';
        if ($logged_in_user['Preference Floating Page Navigator'] == '0')
        {
            echo 'checked ';
        }
        echo "onChange=\"save_preference('FloatingPages', this.value, " .
            $_SESSION['UID'] .
            ", '$AJAXtoken');\"";
        echo '> Show at the page bottom';

        echo '</td>';

        echo '</table>';

        echo "<div class=small-margin-bottom-message><a href='preferences.php?RESET=Y' class=linky onclick=\"return confirm('About to reset your preferences to the default settings. Click OK to proceed, otherwise click Cancel.');\">Reset Preferences to Default</a></div>";

        echo '</main>';

        // print footer

        include 'library/footer.php';

        echo "<script type='text/javascript' src='preferences.js'>
</script>";
        ?>

</body>

</html>