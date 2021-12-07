<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
require_once "library/admin/customer_statistics.php";

use QT\Admin\CustomerStatistics;

// only administrators can view this page; otherwise redirect
if (!$_SESSION['administrator'])
{
    header('Location: /404.php');
}

?>
<html>

<head>
    <?php
        include 'library/standard_header.php';
        window_title("Customer Statistics");
    ?>

</head>

<body class='qt-site' id='customer-statistics'>
    <main class='qt-site-content'>
        <?php

    include "library/menu.php";

    // create customer stat object based on $_GET values in URL
    $customer_statistics = new CustomerStatistics(
        !empty($_GET["PERIOD_TYPE"]) ? $_GET["PERIOD_TYPE"] : CustomerStatistics::PERIOD_TYPE_SIX_MONTHS,
        !empty($_GET["STATISTIC_TYPE"]) ? $_GET["STATISTIC_TYPE"] : CustomerStatistics::STATISTIC_TYPE_LOGINS
    );

    ?>

        <script type='text/javascript'>
            statisticsTableDataSource =
                <?php echo $customer_statistics->output() ?>
            ;
        </script>

        <?php
    $period_type    = $customer_statistics->get_period_type();
    $statistic_type = $customer_statistics->get_statistic_type();

    echo "<div class='page-content'>";

    echo "<section class='page-header'>";

    echo "  <div><h2 class='page-title-text'>Customer Statistics</h2></div>";

    echo "  <div class='table-controls'>";

    echo "    <div class='table-control show-by-period'>";
    echo "     <span class='label'>Show last</>";
    echo "	    <a href='customer_statistics.php?PERIOD_TYPE=" . CustomerStatistics::PERIOD_TYPE_SIX_MONTHS . "&STATISTIC_TYPE=$statistic_type' class='" . (CustomerStatistics::PERIOD_TYPE_SIX_MONTHS == $period_type ? 'selected' : '') . "'>";
    echo "        Six Calendar Months";
    echo "      </a>";

    echo "      <a href='customer_statistics.php?PERIOD_TYPE=" . CustomerStatistics::PERIOD_TYPE_FOUR_WEEKS . "&STATISTIC_TYPE=$statistic_type' class='" . (CustomerStatistics::PERIOD_TYPE_FOUR_WEEKS == $period_type ? 'selected' : '') . "'>";
    echo "   	  Four Weeks";
    echo "       </a>";

    echo "    </div>";    // .table-control .show-by-period

    echo "    <div class='table-control stat-type'>";
    echo "     <span class='label'>Statistic</>";
    echo "	    <a href='customer_statistics.php?PERIOD_TYPE=$period_type&STATISTIC_TYPE=" . CustomerStatistics::STATISTIC_TYPE_LOGINS . "' class='" . (CustomerStatistics::STATISTIC_TYPE_LOGINS == $statistic_type ? 'selected' : '') . "'>";
    echo "        Logins";
    echo "      </a>";

    echo "	    <a href='customer_statistics.php?PERIOD_TYPE=$period_type&STATISTIC_TYPE=" . CustomerStatistics::STATISTIC_TYPE_ACTIVITY . "' class='" . (CustomerStatistics::STATISTIC_TYPE_ACTIVITY == $statistic_type ? 'selected' : '') . "'>";
    echo "   	  Activity";
    echo "       </a>";

    echo "    </div>";    // .table-control .stat-type

    echo "  </div>";      // .table-controls
    echo "</section>";        // .page-header

    echo "<section class='page-body'>";

    echo $customer_statistics->get_table_html_shell();

    echo "</section>";        // .page-body
    echo "</div>";            // .page-content

    include "library/footer.php";

    echo "<script type='text/javascript' src='statistics.js'>
</script>";

?>
</body>

</html>