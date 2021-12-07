<?php

use phpDocumentor\Reflection\Types\Integer;

function user_wants_quick_tips($user = null)
{
    if (empty($user))
    {
        throw new \Exception('Missing $user for user_wants_quick_tips()');
    }

    $sql = "
    SELECT COUNT(*) FROM `USERS`
     WHERE `USER ID`                    = " . db_quote($user['User ID']) . "
       AND `Preference Show Quick Tips` = 1";

    $result = db_query($sql);

    return db_rowcount($result) > 0;
}

/**
 * get ID of the current quick tip for the given user
 *
 * Use: echo "show_quick_tip(null, ".get_current_quick_tip_id($user).")"
 *
 * @param array $user      - row from USERS table
 *
 * @return integer ID from the QUICK-TIPS table of current users current quick tip
 */
function get_current_quick_tip_id($user = null): ?int
{
    if (empty($user))
    {
        throw new \Exception('Missing $user for get_current_quick_tip_id()');
    }

    $sql = "
        SELECT `USERS`.`Current Quick Tip ID`
          FROM `USERS`
         WHERE `Preference Show Quick Tips` IS TRUE
           AND `USERS`.`User ID` = " . db_quote($user['User ID']);

    $result = db_query($sql);

    if (db_rowcount($result) == 1)
    {
        $ROW = db_return_row($result);
        return $ROW['Current Quick Tip ID'];
    }
    else
    {
        return null;
    }
}

function get_quick_tip($quick_tip_id)
{
    $sql = "
    SELECT quick_tips.*
      FROM `QUICK-TIPS` AS quick_tips 
     WHERE quick_tips.`ID` = " . db_quote($quick_tip_id);

    $result = db_query($sql);

    if (db_rowcount($result) == 1)
    {
        $ROW = db_return_row($result);
        return $ROW;
    }
    else
    {
        return null;
    }
}

/**
 * update current quick tip for the given user
 *
 * Use: update_current_quick_tip(10, $user);
 *
 * @param integer $new_quick_tip_id - id of new quick tip
 * @param array $user               - row from USERS table
 *
 * @return void
 */
function update_current_quick_tip(int $new_quick_tip_id, array $user)
{
    update_user_by_id(
        $user['User ID'],
        ['Current Quick Tip ID' => $new_quick_tip_id],
        $user
    );

    return;
}

/**
 * update preference for seeing quick tip for the given user
 *
 * Use: update_tip_preference(false, $user);
 *
 * @param boolean $show_quick_tips - whether the user wants to see quick tips or not
 * @param array $user              - row from USERS table
 *
 * @return void
 */

function update_tip_preference(bool $show_quick_tips, array $user)
{
    // Update USERS table for all other users

    $show_quick_tips = (int) (bool) $show_quick_tips; // cast boolean value to 0 if false, 1 if anything else

    // Sep 2020
    // update_user_by_id() does not work due to the confusing way that MySQL
    // and PHP handle null and false values. There is a constraint on the USERS
    // table that ensures 'Preference Show Quick Tips' is NOT NULL and the
    // default value on the column is 1. So, unless this value is explicitly
    // set to 0, the DB will set it to 1. However, in the process of using
    // mysqli_real_escape_string in db_quote(), values are calculated
    // differently. It is just simpler to issue a direct UPDATE statement here
    // rather than risk unpredictable behaviour in other parts of the app:
    //
    // update_user_by_id($user['User ID'], ['Preference Show Quick Tips' => $show_quick_tips]);

    $sql = "
    UPDATE `USERS`
       SET `Preference Show Quick Tips`=" . db_quote($show_quick_tips) . "
     WHERE `USER ID`=" . db_quote($user['User ID']);

    $result = db_query($sql);
}
