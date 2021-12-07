<?php

session_start();
session_regenerate_id();

require_once 'library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth_functions.php';

// if we are logged in, redirect to home.php
if (is_logged_in_user())
{
    header('Location: /home.php');
    exit();
}

// if we are running locally we show this page; otherwise we do a redirection to marketing

// get the URL path of this page

$pageURL = 'http';
if (isset($_SERVER['HTTPS']))
{
    if ($_SERVER['HTTPS'] == 'on')
    {
        $pageURL .= 's';
    }
}
$pageURL .= '://';
if ($_SERVER['SERVER_PORT'] != '80')
{
    $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
}
else
{
    $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}

if (!strpos($pageURL, 'localhost'))
{
    // redirect home

    header('Location: /home.php');
}
?>

<!DOCTYPE html>
<html>

<head>

    <?php include 'library/standard_header.php'; ?>

</head>

<body class='qt-site' style="background-image: url('images/mss_background.jpg');">

    <?php include 'library/gtm_body.php'; ?>

    <main class='qt-site-content'>

        <div align='center' style='margin-top:20px;'>

            <img src='images/logos/qt_about_page.png' width=280 height=250>

            <p><a href='home.php'><button>If you are already registered, please click here to login</button></a>
            </p>

            <p><a href='/auth/register.php'><button>If wish to sign up click here</button></a></p>

        </div>

</body>

<?php include 'library/footer.php'; ?>

</html>