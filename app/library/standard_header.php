<?php

/**
 * Various pieces of HTML that should go in the <head> item of most pages
 *
 * PHP Version 7.3
 *
 * This could ultimately end up in a function that takes various parameters
 * such as title, description etc.
 *
 * @category PHP
 **/

// These will probably have already been included by the sequence of pages that called
// standard_header.php, but just in case, it's included here too since this page
// uses functions defined in them e.g. get_logged_in_user(), get_asset_paths()
require_once 'auth/auth_functions.php';
require_once 'functions.php';

if (!isset($logged_in_user))
{
    // $logged_in_user is set in auth.php but not all pages run it. However,
    // this variable is required on every page (eg by menu.php)
    $logged_in_user = get_logged_in_user();
}

require_once 'library/gtm_head.php';

$asset_paths = get_asset_paths(dirname(__FILE__) . '/../assets/assets.json');

// Note that this includes jQuery on every page - which might give us a wee bit of
// performance hit on those (few?) pages that don't need it.
?>
<meta charset="utf-8" />
<meta name="robots" content="NOINDEX, NOFOLLOW">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css"
    href="/assets/<?php echo $asset_paths['qt_styles_path']; ?>" />

<link rel="shortcut icon" href="/images/logos/logo-favicon.png" />

<script type="text/javascript"
    src="/assets/<?php echo $asset_paths['qt_javascript_path']; ?>">
</script>
<?php

// if they are an admin, we will record the page rendering time
if (!empty($_SESSION['administrator']))
{
    $page_time_start = microtime(true);
}

// redirect for maintenance mode
if ($config['is_maintenance_mode_enabled'] ?? false)
{
    // doesn't apply to admin users who are already logged in, or if IGNORE_MAINTENANCE_MODE=Y is set as a GET
    $ignore_maintenance_mode = isset($_SESSION['administrator']) ||
        'Y' == ($_GET['IGNORE_MAINTENANCE_MODE'] ?? '');

    if (!$ignore_maintenance_mode)
    {
        // check we are not already at maintenance.php, or we will cause a loop and a crash
        if (strpos($_SERVER['REQUEST_URI'], 'maintenance.php') === false)
        {
            header('Location: /maintenance.php');
        }
    }
}
