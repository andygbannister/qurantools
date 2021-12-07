<?php
require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// redirect if changes are turned off

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
            window_title("Chart of Login IPs for User");
        ?>


<?php

// sort order
if (!isset($_GET["SORT"]))
{
    $_GET["SORT"] = "";
}

// which user to lookup?

$user = 1;

if (isset($_GET["USER"]))
{
    $user = db_quote($_GET["USER"]);
}

?>

<script type="text/javascript" src="../library/fusioncharts/fusioncharts.js"></script>
<script type="text/javascript" src="../library/fusioncharts/themes/fusioncharts.theme.fint.js"></script>

<script type="text/javascript">
  FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "pie2d",
        "renderAt": "chartContainer",
        "width": "960",
        "height": "70%",
        "dataFormat": "json",
        "dataSource":  {
          "chart": {
            "caption": "",
               "outCnvBaseFontSize": "11",
            "yAxisNameFontSize": "11",
            "xAxisNameFontSize": "11",
            "subCaption": "",
            <?php

            echo "\"xAxisName\": \"Login IP\",";

            ?>
            
            
            "yAxisName": "Logins From This IP Address",
            "theme": "fint",
            "showValues": "1",
            "use3DLighting": "1",
			"showPercentInTooltip":"1"
         },
         
       <?php
       // POPULATE THE DATASET

               $sort_field = "`Login IP`";

               if ($_GET["SORT"] == 1)
               {
                   $sort_field = "COUNT_IP DESC";
               }

               $result = db_query("SELECT DISTINCT (`Login IP`), count(*) COUNT_IP FROM `LOGIN-LOGS` WHERE `User ID`=$user GROUP BY `Login IP` ORDER BY $sort_field");

               $test  = "";
               $count = 0;
               echo "\"data\": [";
               for ($i = 0; $i < db_rowcount($result); $i++)
               {
                   // grab next database row
                   $ROW = db_return_row($result);

                   // count how many columns we have actually rendered
                   $count++;

                   if ($count > 1)
                   {
                       echo ",";
                   }
                   echo "{";
                   echo "\"label\": \"";

                   if ($ROW["Login IP"] == "")
                   {
                       echo "(No IP Recorded)";
                   }
                   else
                   {
                       echo $ROW["Login IP"];
                   }
                   echo "\",";
                   echo "\"value\": \"" . $ROW["COUNT_IP"] . "\",";

                   echo "link:\"http://whatismyipaddress.com/ip/" . $ROW["Login IP"] . "\",";

                   echo "}";
               }

       ?>
          ]
      }

  });
revenueChart.render();
})
</script>
</head>
<body class='qt-site'>
<main class='qt-site-content'>

<?php

include "../library/menu.php";

// title and navigation stuff
set_chart_control_selectors();

echo "<div class='page-header'>";

echo "  <h2><a href='/admin/user_detail.php?USER=$user' class=nodec>Login IPs Used by User ID #$user " . " (" . htmlspecialchars(db_return_one_record_one_field("SELECT `Email Address` FROM `USERS` WHERE `User ID`=" . $user)) . ")</a></h2>";

echo "</div>";     // page-header

?>

  <div align=center id="chartContainer"></div>
<?php
echo "<div align=center><br><a href='/admin/user_detail.php?USER=$user' class=linky>Back to User Details</a></div>";

include "../library/footer.php";

?>  
   
</body>
</html>