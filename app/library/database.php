<?php
/**
 * Commonly used functions library
 */

function db_connect()
{
    global $config;
    // Define the database connection as a static, so it holds its status
    // between calls to the function

    static $db_connection;

    // If no connection exists, we try to connect

    if (!isset($db_connection))
    {
        // parse the variable names from the config file (as we may in time move away from preloading its contents into global variables)

        // $config = parse_ini_file(config_file_path());

        // open a database connection with those settings (the '@' will ensure an error doesn't crash us at this point)

        $db_connection = @mysqli_connect($config["hostname"], $config["mysqli_login"], $config["mysqli_password"], $config["database"]);
    }

    // If we have failed at connecting, we log or report the error, then push them to the error page

    // codecept_debug($db_connection);

    if ($db_connection === false)
    {
        // If the connection fails, throw an error and redirect to the database error page
        db_error(mysqli_connect_error(), true);
    }

    // Required, or else odd things happen to certain glyphs (try changing to utf16 and running the Browse Sura page and see what happens to sura names)
    mysqli_set_charset($db_connection, 'utf8');

    return $db_connection;
}

function db_error($error, $redirect_to_database_error_page)
{
    global $config;

    // we first look up what to do from our config file
    // $config = parse_ini_file(config_file_path());

    if (isset($config["mysql_error_reporting"]))
    {
        switch (strtoupper($config["mysql_error_reporting"]))
        {
            case "LOG":
                error_log($error);
                // We never want to show database errors on the console in production
                if (isset($config["display_errors_locally"]) && $config["display_errors_locally"])
                {
                    debug_to_console("SQL ERROR: " . addslashes($error), ENT_QUOTES);
                }
                break;

            case "DIE":
                die($error);
                break;
        }
    }

    // we can also redirect the error page (if DIE has been used above, that can't happen)

    if ($redirect_to_database_error_page)
    {
        header('Location: /database_error.php');
    }
}

function db_query($query)
{
    // connect to the database

    $db_connection = db_connect();

    // query the database

    $result = mysqli_query($db_connection, $query);

    // if there is an error, do something

    // if ($result == false)
    // for some reason malformed SQL does not set $result to false

    if (mysqli_error($db_connection))
    {
        // error_log($query);
        db_error(mysqli_error($db_connection), false);
    }

    return $result;
}

function db_quote($value)
{
    // escape the value passed, for safe inclusion in a MySQL statement

    global $config;

    // codecept_debug('in db_qupte');
    // codecept_debug($config);

    $db_connection = db_connect();

    // As per https://forge.typo3.org/issues/68562, passing false into
    // mysqli_real_escape_string returns '', rather than '0' which breaks
    // boolean table values
    if ($value === false)
    {
        return '0';
    }
    else
    {
        return mysqli_real_escape_string($db_connection, $value);
    }
}

/**
 * Runs the db_quote function over every item in the provided array
 *
 * @param array  - $array_to_quote, typically the $_POST variable
 * @return array - everything that was in $array_to_quote but db_quote'd
 *
 * This is a convenience function that saves having to put db_quote in what
 * are often already complicated looking SQL statements
 *
 * TODO: replace the foreach loop with an array_map one-liner
 */
function db_quote_array(array $array_to_db_quote)
{
    foreach ($array_to_db_quote as $key => $value)
    {
        $array_to_db_quote[$key] = db_quote($value);
    }
    // $result = array_map('db_quote', $array_to_db_quote);  // this should work, but test it
    $result = $array_to_db_quote;
    return $result;
}

function db_rowcount($result)
{
    // return the number of rows (or return 0 if SQL is malformed)

    if ($result == false)
    {
        return 0;
    }

    return mysqli_num_rows($result);
}

function db_return_row($result)
{
    // return the next database row

    return $result->fetch_assoc();
}

function db_goto($result, $goto)
{
    // go to a record (either FIRST, LAST, or a NUMBER

    // remember that 0 is the first record and (RECORD_COUNT - 1) is the last record

    if ($goto == "FIRST")
    {
        $goto = 0;
    }

    if (is_integer($goto))
    {
        if ($goto < 0)
        {
            $goto = 0;
        }
    }

    if ($goto === "LAST" || $goto >= db_rowcount($result))
    {
        $goto = db_rowcount($result) - 1;
    }

    mysqli_data_seek($result, $goto);
}

function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
    {
        $output = implode(',', $output);
    }
    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

function db_return_one_record_one_field($query)
{
    $result = db_query($query);

    if (!$result || db_rowcount($result) == 0)
    {
        return "";
    }

    $ROW = db_return_row($result);
    return reset($ROW);  // return the first item of the array
}

/**
 * Load all database access functions
 *
 * These are mostly SQL for select/update/insert/delete statements for MySQL
 */

require_once "library/database/user_functions.php";
