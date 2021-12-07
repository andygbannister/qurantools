<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// only superusers can view this page; otherwise redirect
if (strtoupper($_SESSION['administrator']) != "SUPERUSER")
{
    header('Location: /404.php');
}

// SORT OPTIONS
$SORT_LOGIN     = " ORDER BY `DATE AND TIME` DESC";
$SORT_SEARCHES  = " ORDER BY `DATE AND TIME` DESC";
$SORT_HISTORY   = " ORDER BY `Timestamp` DESC";
$SORT_BOOKMARKS = " ORDER BY `Timestamp` DESC";
$SORT_TAGS      = " ORDER BY `Tag Name` ASC";

if (isset($_GET["SORT"]))
{
    switch ($_GET["SORT"])
    {
        case "LOGIN-DATE-ASC":
            $SORT_LOGIN = " ORDER BY `DATE AND TIME` ASC";
            break;

        case "LOGIN-DATE-DESC":
            $SORT_LOGIN = " ORDER BY `DATE AND TIME` DESC";
            break;

        case "LOGIN-IP-ASC":
            $SORT_LOGIN = " ORDER BY `Login IP`";
            break;

        case "LOGIN-IP-DESC":
            $SORT_LOGIN = " ORDER BY `Login IP` DESC";
            break;

        case "IP-COUNT-ASC":
            $SORT_LOGIN = " ORDER BY `COUNT_FROM_IP`";
            break;

        case "IP-COUNT-DESC":
            $SORT_LOGIN = " ORDER BY `COUNT_FROM_IP` DESC";
            break;

        case "SEARCH-DATE-ASC":
            $SORT_SEARCHES = " ORDER BY `DATE AND TIME` ASC";
            break;

        case "SEARCH-DATE-DESC":
            $SORT_SEARCHES = " ORDER BY `DATE AND TIME` DESC";
            break;

        case "SEARCH-CONTENT-ASC":
            $SORT_SEARCHES = " ORDER BY `LOOKED UP` ASC";
            break;

        case "SEARCH-CONTENT-DESC":
            $SORT_SEARCHES = " ORDER BY `LOOKED UP` DESC";
            break;

        case "SEARCH-REFERRER-ASC":
            $SORT_SEARCHES = " ORDER BY `REFERRING PAGE` ASC";
            break;

        case "SEARCH-REFERRER-DESC":
            $SORT_SEARCHES = " ORDER BY `REFERRING PAGE` DESC";
            break;

        case "SEARCH-TYPE-ASC":
            $SORT_SEARCHES = " ORDER BY `VERSES OR SEARCH` ASC";
            break;

        case "SEARCH-TYPE-DESC":
            $SORT_SEARCHES = " ORDER BY `VERSES OR SEARCH` DESC";
            break;

        case "HISTORY-DATE-ASC":
            $SORT_HISTORY = " ORDER BY `Timestamp` ASC";
            break;

        case "HISTORY-DATE-DESC":
            $SORT_HISTORY = " ORDER BY `Timestamp` DESC";
            break;

        case "HISTORY-CONTENT-ASC":
            $SORT_HISTORY = " ORDER BY `History Item` ASC";
            break;

        case "HISTORY-CONTENT-DESC":
            $SORT_HISTORY = " ORDER BY `History Item` DESC";
            break;

        case "BOOKMARK-DATE-ASC":
            $SORT_BOOKMARKS = " ORDER BY `Timestamp` ASC";
            break;

        case "BOOKMARK-DATE-DESC":
            $SORT_BOOKMARKS = " ORDER BY `Timestamp` DESC";
            break;

        case "BOOKMARK-NAME-ASC":
            $SORT_BOOKMARKS = " ORDER BY `Name` ASC";
            break;

        case "BOOKMARK-NAME-DESC":
            $SORT_BOOKMARKS = " ORDER BY `Name` DESC";
            break;

        case "BOOKMARK-CONTENTS-ASC":
            $SORT_BOOKMARKS = " ORDER BY `Contents` ASC";
            break;

        case "BOOKMARK-CONTENTS-DESC":
            $SORT_BOOKMARKS = " ORDER BY `Contents` DESC";
            break;

        case "TAG-NAME-ASC":
            $SORT_TAGS = " ORDER BY `Tag Name` ASC";
            break;

        case "TAG-NAME-DESC":
            $SORT_TAGS = " ORDER BY `Tag Name` DESC";
            break;

        case "TAG-COUNT-ASC":
            $SORT_TAGS = " ORDER BY tag_count ASC";
            break;

        case "TAG-COUNT-DESC":
            $SORT_TAGS = " ORDER BY tag_count DESC";
            break;
    }
}

// TAB
$TAB_SELECTED = 1;

if (isset($_GET["TAB"]))
{
    $TAB_SELECTED = $_GET["TAB"];

    if ($TAB_SELECTED < 1)
    {
        $TAB_SELECTED = 1;
    }
    if ($TAB_SELECTED > 6)
    {
        $TAB_SELECTED = 6;
    }
}

// TODO: $user should be renamed $user_id

// get user details
$user = 1;

if (isset($_GET["USER"]))
{
    $user = db_quote($_GET["USER"]);
}

// user details
$result_user = db_query("SELECT * FROM `USERS` WHERE `User ID`=" . db_quote($user));

// other data

$result_logins = db_query("SELECT T1.`Login IP`, T1.`DATE AND TIME`, (SELECT COUNT(*) FROM `LOGIN-LOGS` T2 WHERE T1.`Login IP`=T2.`LOGIN IP` AND T2.`User ID`=" . db_quote($user) . ") COUNT_FROM_IP FROM `LOGIN-LOGS` T1 WHERE T1.`User ID`=" . db_quote($user) . " $SORT_LOGIN");

$result_searches = db_query("SELECT * FROM `USAGE-VERSES-SEARCHES` WHERE `USER ID`=$user $SORT_SEARCHES");

$result_history = db_query("SELECT * FROM `HISTORY` WHERE `User ID`=$user $SORT_HISTORY");

$result_bookmarks = db_query("SELECT * FROM `BOOKMARKS` WHERE `USER ID`=$user $SORT_BOOKMARKS");

$result_tags = db_query("SELECT *, (SELECT COUNT(*) FROM `TAGGED-VERSES` T2 WHERE T1.`ID`=T2.`TAG ID`) tag_count FROM `TAGS` T1 WHERE `User ID` = $user $SORT_TAGS");

$result_tagged_verses = db_query("SELECT * FROM `TAGGED-VERSES` WHERE `User ID`=$user");

// get the user details

$ROW = db_return_row($result_user);

?>
<html>

<head>
    <?php
            include 'library/standard_header.php';
            window_title("Examining User");
        ?>
    <script type="text/javascript" src="user_detail.js"></script>

</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php

include "library/back_to_top_button.php";

// menubar

include "library/menu.php";

echo "<h2 class='page-title-text'>Examining User ID #$user: <a href='mailto:" . $ROW["Email Address"] . "' class=linky>" . htmlspecialchars($ROW["Email Address"]) . "</a></h2>";

echo "<h3>";
echo "For: ";
echo show_value_or_missing($ROW["User Name"], "User Name");
echo "</h3>";

echo "<div class='tabWrapper'>";

// TAB LINKS

echo "<div class=tab>";
echo "<button id='buttonBasicInfo' class='tablinks " . ($TAB_SELECTED == 1 ? "active" : "") . "' onclick=\"openTab(event, 'BasicInfo')\">Basic Info</button>";
echo "<button id='buttonLogins' class='tablinks " . ($TAB_SELECTED == 2 ? "active" : "") . "' onclick=\"openTab(event, 'Logins')\">Logins</button>";
echo "<button id='buttonSearches' class='tablinks " . ($TAB_SELECTED == 3 ? "active" : "") . "' onclick=\"openTab(event, 'Searches')\">Verse Lookups / Searches</button>";
echo "<button id='buttonHistory' class='tablinks " . ($TAB_SELECTED == 4 ? "active" : "") . "' onclick=\"openTab(event, 'History')\">History</button>";
echo "<button id='buttonBookmarks' class='tablinks " . ($TAB_SELECTED == 5 ? "active" : "") . "' onclick=\"openTab(event, 'Bookmarks')\">Bookmarks</button>";
echo "<button id='buttonTags' class='tablinks " . ($TAB_SELECTED == 6 ? "active" : "") . "' onclick=\"openTab(event, 'Tags')\">Tags</button>";

echo "</div>";

// **** BASIC USER INFO TAB ****

    echo "<div id='BasicInfo' class='tabcontent'";
    if ($TAB_SELECTED == 1)
    {
        echo " style='display:block;'";
    }

    echo ">";

    echo "<table class='hoverTable'>";

    echo "<tr>";

        echo "<td>";
        echo "<b>User ID</b>";
        echo "</td>";

        echo "<td>";
        echo $ROW["User ID"];
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Email Address</b>";
        echo "</td>";

        echo "<td>";
        echo show_value_or_missing($ROW["Email Address"]);
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>User Name</b>";
        echo "</td>";

        echo "<td>";
        echo show_value_or_missing(htmlspecialchars($ROW["User Name"]));
        // echo htmlspecialchars($ROW["User Name"]);
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>User Creation Date/Time</b>";
        echo "</td>";

        echo "<td>";
        echo $ROW["Creation Date"];
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>User Type</b>";
        echo "</td>";

        echo "<td>";
        echo $ROW["User Type"];
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total Logins</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Logins');\" class=linky>" . number_format(db_rowcount($result_logins)) . "</a>";

        if (db_rowcount($result_logins) > 0)
        {
            echo "<a href='/charts/chart_user_logins_by_month.php?USER=$user'><img class='float-right' src='/images/stats.gif'></a>";
        }

        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Last Login</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Logins');\" class=linky>" . $ROW["Last Login Timestamp"] . "</a>";
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Distinct IPs Logged In From</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Logins');\" class=linky>" . number_format(db_return_one_record_one_field("SELECT count(distinct(`Login IP`)) FROM `LOGIN-LOGS` WHERE `User ID`=" . db_quote($user))) . "</a>";

        if (db_rowcount($result_logins) > 0)
        {
            echo "<a href='/charts/chart_user_login_ips.php?USER=$user'><img class='float-right src='/images/stats.gif'></a>";
        }

        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total Verse Lookups/Searches</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Searches');\" class=linky>" . number_format(db_rowcount($result_searches)) . "</a>";

        if (db_rowcount($result_logins) > 0)
        {
            echo "<a href='/charts/chart_user_searches_by_month.php?USER=$user'><img class='float-right' src='/images/stats.gif'></a>";
        }

        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total History Records</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'History');\" class=linky>" . number_format(db_rowcount($result_history)) . "</a>";
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total Bookmarks</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Bookmarks');\" class=linky>" . number_format(db_rowcount($result_bookmarks)) . "</a>";
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total Tags</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Tags');\"' class=linky>" . number_format(db_rowcount($result_tags)) . "</a>";
        echo "</td>";

    echo "</tr>";

    echo "<tr>";

        echo "<td>";
        echo "<b>Total Verses Tagged</b>";
        echo "</td>";

        echo "<td>";
        echo "<a href='#' onclick=\"openTab(event, 'Tags');\"' class=linky>" . number_format(db_rowcount($result_tagged_verses)) . "</a>";
        echo "</td>";

    echo "</tr>";

    echo "</table>";

    ?>

        </div>

        <?php

// **** LOGINS TAB ****

echo "<div id='Logins' class='tabcontent'";

if ($TAB_SELECTED == 2)
{
    echo " style='display:block;'";
}

    echo ">";

      if (db_rowcount($result_logins) == 0)
      {
          echo "<div align=center>";

          echo "<br><p>There are no logins recorded for this user yet.</p></br>";

          echo "</div>";
      }
      else
      {
          echo "<div class=user-detail-pane-table-header>";

          echo "<table class='hoverTable'>";

          echo "<thead>";

          echo "<tr>";

          echo "<th width=217><b>Login Date & Time (GMT)</b>&nbsp;<a href='user_detail.php?TAB=2&SORT=LOGIN-DATE-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=2&SORT=LOGIN-DATE-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=197><b>Login IP Address</b>&nbsp;<a href='user_detail.php?TAB=2&SORT=LOGIN-IP-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=2&SORT=LOGIN-IP-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=200><b>Logins From This IP</b>&nbsp;<a href='user_detail.php?TAB=2&SORT=IP-COUNT-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=2&SORT=IP-COUNT-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "</tr>";

          echo "</thead>";
          echo "</table>";

          echo "</div>";

          echo "<div id=logins_table class=user-detail-pane-table-content>";

          echo "<table class='hoverTable'>";

          echo "<tbody>";

          for ($i = 0; $i < db_rowcount($result_logins); $i++)
          {
              $ROW = db_return_row($result_logins);

              echo "<tr>";

              echo "<td align=center width=220>" . $ROW["DATE AND TIME"] . "</td>";

              echo "<td align=center width=200>";

              if ($ROW["Login IP"] != "")
              {
                  echo "<a href='http://whatismyipaddress.com/ip/" . $ROW["Login IP"] . "' class=linky target='_blank'>" . $ROW["Login IP"] . "</a>";
              }
              else
              {
                  echo "(None Recorded)";
              }
              echo "</td>";

              echo "<td align=center width=200>" . number_format($ROW["COUNT_FROM_IP"]) . "</td>";

              echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";

          echo "</div>";

          echo "<span class=smaller_text_for_mini_dialogs>Logins: " . number_format(db_rowcount($result_logins));

          if (db_rowcount($result_logins) > 0)
          {
              echo "&nbsp;<a href='/charts/chart_user_logins_by_month.php?USER=$user'><img src='/images/st.gif'></a>";
          }

          echo " &nbsp;|&nbsp; Distinct IPs: " . number_format(db_return_one_record_one_field("SELECT count(distinct(`Login IP`)) FROM `LOGIN-LOGS` WHERE `User ID`=" . db_quote($user)));

          if (db_rowcount($result_logins) > 0)
          {
              echo "&nbsp;<a href='/charts/chart_user_login_ips.php?USER=$user'><img src='/images/st.gif'></a>";
          }

          echo "</span>";

          if (db_rowcount($result_logins) > 25)
          {
              echo "<span class='float-right'><a href='#' class='smaller_text_for_mini_dialogs linky' onClick=\"$('#logins_table').animate({ scrollTop: 0 }, 'fast');\">Back to Top</a></span>";
          }
      }

echo "</div>";

// **** VERSES AND SEARCH LOOKUPS TAB ****

echo "<div id='Searches' class='tabcontent'";

if ($TAB_SELECTED == 3)
{
    echo " style='display:block;'";
}

    echo ">";

if (db_rowcount($result_searches) == 0)
{
    echo "<div align=center>";

    echo "<br><p>There are no searches or verse lookups recorded for this user yet.</p></br>";

    echo "</div>";
}
      else
      {
          echo "<div class=user-detail-pane-table-header>";

          echo "<table class='hoverTable'>";

          echo "<thead>";

          echo "<tr>";

          echo "<th width=167><b>Timestamp (GMT)</b>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-DATE-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-DATE-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=67><b>Type</b>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-TYPE-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-TYPE-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=297 ALIGN=LEFT><b>Verses Viewed or Search Performed</b>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-CONTENT-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-CONTENT-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=297 ALIGN=LEFT><b>Referring Page</b>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-REFERRER-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=3&SORT=SEARCH-REFERRER-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "</tr>";

          echo "</thead>";
          echo "</table>";

          echo "</div>";

          echo "<div id=searches_table class=user-detail-pane-table-content>";

          echo "<table class='hoverTable'>";

          echo "<tbody>";

          for ($i = 0; $i < db_rowcount($result_searches); $i++)
          {
              $ROW = db_return_row($result_searches);

              echo "<tr>";

              echo "<td align=center width=200>";

              echo $ROW["DATE AND TIME"];

              echo "</td>";

              echo "<td align=center width=70>";
              if ($ROW["VERSES OR SEARCH"] == "V")
              {
                  echo "Verses";
              }
              else
              {
                  echo "Search";
              }
              echo "</td>";

              echo "<td width=300>";

              if ($ROW["VERSES OR SEARCH"] == "V")
              {
                  echo "<a href='../verse_browser.php?V=" . urlencode($ROW["LOOKED UP"]) . "' class=linky>";
              }
              else
              {
                  echo "<a href='../verse_browser.php?S=" . urlencode($ROW["LOOKED UP"]) . "' class=linky>";
              }

              if (strlen($ROW["LOOKED UP"]) > 30)
              {
                  echo "<span title='" . htmlentities($ROW["LOOKED UP"]) . "'>" . mb_substr(htmlentities($ROW["LOOKED UP"]), 0, 30) . " ...</span>";
              }
              else
              {
                  echo htmlentities($ROW["LOOKED UP"]);
              }

              echo "</a>";

              echo "</td>";

              echo "</td>";

              echo "<td width=300>";

              if (strlen($ROW["REFERRING PAGE"]) > 30)
              {
                  echo "<span title='" . htmlentities($ROW["REFERRING PAGE"]) . "'>" . mb_substr(htmlentities($ROW["REFERRING PAGE"]), 0, 30) . " ...</span>";
              }
              else
              {
                  echo htmlentities($ROW["REFERRING PAGE"]);
              }

              echo "</a>";

              echo "</td>";

              echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";

          echo "</div>";

          if (db_rowcount($result_searches) > 0)
          {
              echo "<span class=smaller_text_for_mini_dialogs>Number of Records: " . number_format(db_rowcount($result_searches));

              echo "&nbsp;<a href='/charts/chart_user_searches_by_month.php?USER=$user'><img src='/images/st.gif'></a>";

              echo "</span>";
          }

          if (db_rowcount($result_searches) > 25)
          {
              echo "<span class='float-right'><a href='#' class='smaller_text_for_mini_dialogs linky' onClick=\"$('#searches_table').animate({ scrollTop: 0 }, 'fast');\">Back to Top</a></span>";
          }
      }

echo "</div>";

// **** HISTORY TAB ****

echo "<div id='History' class='tabcontent'";

if ($TAB_SELECTED == 4)
{
    echo " style='display:block;'";
}

    echo ">";

if (db_rowcount($result_history) == 0)
{
    echo "<div align=center>";

    echo "<br><p>There is no history recorded for this user yet.</p></br>";

    echo "</div>";
}
      else
      {
          echo "<div class=user-detail-pane-table-header>";

          echo "<table class='hoverTable'>";

          echo "<thead>";

          echo "<tr>";

          echo "<th width=197><b>Timestamp (GMT)</b>&nbsp;<a href='user_detail.php?TAB=4&SORT=HISTORY-DATE-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=4&SORT=HISTORY-DATE-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=650 ALIGN=left><b>History Item</b>&nbsp;<a href='user_detail.php?TAB=4&SORT=HISTORY-CONTENT-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=4&SORT=HISTORY-CONTENT-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "</tr>";

          echo "</thead>";
          echo "</table>";

          echo "</div>";

          echo "<div id=history_table class=user-detail-pane-table-content>";

          echo "<table class='hoverTable'>";

          echo "<tbody>";

          for ($i = 0; $i < db_rowcount($result_history); $i++)
          {
              $ROW = db_return_row($result_history);

              echo "<tr>";

              echo "<td align=center width=200>";

              echo $ROW["Timestamp"];

              echo "</td>";

              echo "</td>";

              echo "<td width=640>";

              echo "<a href='../home.php?L=" . urlencode($ROW["History Item"]) . "' class=linky>";

              if (strlen($ROW["History Item"]) > 90)
              {
                  echo "<span title='" . htmlentities($ROW["History Item"]) . "'>" . mb_substr(htmlentities($ROW["History Item"]), 0, 90) . " ...</span>";
              }
              else
              {
                  echo htmlentities($ROW["History Item"]);
              }

              echo "</a>";

              echo "</a></td>";

              echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";

          echo "</div>";

          if (db_rowcount($result_history) > 0)
          {
              echo "<span class=smaller_text_for_mini_dialogs>Number of History Items: " . number_format(db_rowcount($result_history)) . "</span>";
          }

          if (db_rowcount($result_history) > 25)
          {
              echo "<span class='float-right'><a href='#' class='smaller_text_for_mini_dialogs linky' onClick=\"$('#history_table').animate({ scrollTop: 0 }, 'fast');\">Back to Top</a></span>";
          }
      }

echo "</div>";

// **** BOOMARKS TAB ****

echo "<div id='Bookmarks' class='tabcontent'";

if ($TAB_SELECTED == 5)
{
    echo " style='display:block;'";
}

    echo ">";

if (db_rowcount($result_bookmarks) == 0)
{
    echo "<div align=center>";

    echo "<br><p>There are no bookmarks saved by this user yet.</p></br>";

    echo "</div>";
}
      else
      {
          echo "<div class=user-detail-pane-table-header>";

          echo "<table class='hoverTable'>";

          echo "<thead>";

          echo "<tr>";

          echo "<th width=197 align=left><b>Timestamp (GMT)</b>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-DATE-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-DATE-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=337 align=left><b>Bookmark Name</b>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-NAME-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-NAME-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=297><b>Bookmark Contents</b>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-CONTENTS-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=5&SORT=BOOKMARK-CONTENTS-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "</tr>";

          echo "</thead>";
          echo "</table>";

          echo "</div>";

          echo "<div id=bookmarks_table class=user-detail-pane-table-content>";

          echo "<table class='hoverTable'>";

          echo "<tbody>";

          for ($i = 0; $i < db_rowcount($result_bookmarks); $i++)
          {
              $ROW = db_return_row($result_bookmarks);

              echo "<tr>";

              echo "<td align=center width=200>";

              echo $ROW["Timestamp"];

              echo "</td>";

              echo "</td>";

              echo "<td width=340>";

              if (strlen($ROW["Name"]) > 50)
              {
                  echo "<span title='" . htmlentities($ROW["Name"]) . "'>" . mb_substr(htmlentities($ROW["Name"]), 0, 50) . " ...</span>";
              }
              else
              {
                  echo htmlentities($ROW["Name"]);
              }

              echo "</td>";

              echo "<td width=300>";

              echo "<a href='../home.php?L=" . urlencode($ROW["Contents"]) . "' class=linky>";

              if (strlen($ROW["Contents"]) > 40)
              {
                  echo "<span title='" . htmlentities($ROW["Contents"]) . "'>" . mb_substr(htmlentities($ROW["Contents"]), 0, 40) . " ...</span>";
              }
              else
              {
                  echo htmlentities($ROW["Contents"]);
              }

              echo "</a>";

              echo "</a></td>";

              echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";

          echo "</div>";

          if (db_rowcount($result_bookmarks) > 0)
          {
              echo "<span class=smaller_text_for_mini_dialogs>Number of Bookmarks: " . number_format(db_rowcount($result_bookmarks)) . "</span>";
          }

          if (db_rowcount($result_bookmarks) > 25)
          {
              echo "<div class='float-right'><a href='#' class='smaller_text_for_mini_dialogs linky' onClick=\"$('#bookmarks_table').animate({ scrollTop: 0 }, 'fast');\">Back to Top</a></span>";
          }
      }

echo "</div>";

// **** BOOMARKS TAB ****

echo "<div id='Tags' class='tabcontent'";

if ($TAB_SELECTED == 6)
{
    echo " style='display:block;'";
}

    echo ">";

if (db_rowcount($result_tags) == 0)
{
    echo "<div align=center>";

    echo "<br><p>No tags have been created by this user yet.</p></br>";

    echo "</div>";
}
      else
      {
          echo "<div class=user-detail-pane-table-header>";

          echo "<table class='hoverTable'>";

          echo "<thead>";

          echo "<tr>";

          echo "<th width=397 align=left><b>Tag Name</b>&nbsp;<a href='user_detail.php?TAB=6&SORT=TAG-NAME-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=6&SORT=TAG-NAME-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "<th width=197 align=left><b>Verses Tagged</b>&nbsp;<a href='user_detail.php?TAB=6&SORT=TAG-COUNT-ASC&USER=$user'><img src='../images/up.gif'></a>&nbsp;<a href='user_detail.php?TAB=6&SORT=TAG-COUNT-DESC&USER=$user'><img src='../images/down.gif'></a></th>";

          echo "</tr>";

          echo "</thead>";
          echo "</table>";

          echo "</div>";

          echo "<div id='tabs_table' class='user-detail-pane-table-content'>";

          echo "<table class='hoverTable'>";

          echo "<tbody>";

          for ($i = 0; $i < db_rowcount($result_tags); $i++)
          {
              $ROW = db_return_row($result_tags);

              echo "<tr>";

              echo "<td width=400>";

              echo "<span class='pill-tag' style='background-color:#" . $ROW["Tag Colour"] . ";";

              // if the tag has a super light colour, we draw a black border (with bigger padding to clarify it)

              if ($ROW["Tag Lightness Value"] > 220)
              {
                  echo "border: 1px solid #000000; padding-top: 2px; padding-bottom: 2px;";
              }
              else
              {
                  echo "border-color:#" . $ROW["Tag Colour"] . ";";
              }

              if ($ROW["Tag Lightness Value"] > 130)
              {
                  echo "color: black";
              }

              echo "'>";

              echo htmlentities($ROW["Tag Name"]);

              echo "</span>";

              echo "</td>";

              echo "<td width=200 align=center>";

              echo number_format($ROW["tag_count"]);

              echo "</td>";

              echo "</tr>";
          }

          echo "<tr><td>&nbsp;</td><td align=center><b>" . number_format(db_rowcount($result_tagged_verses)) . "</b></td></tr>";

          echo "</tbody>";
          echo "</table>";

          echo "</div>";

          if (db_rowcount($result_tags) > 0)
          {
              echo "<span class=smaller_text_for_mini_dialogs>Number of Tags: " . number_format(db_rowcount($result_tags)) . "</span>";
          }

          if (db_rowcount($result_tags) > 25)
          {
              echo "<div class='float-right'><a href='#' class='smaller_text_for_mini_dialogs linky' onClick=\"$('#tabs_table').animate({ scrollTop: 0 }, 'fast');\">Back to Top</a></span>";
          }
      }

echo "<div><a href='user_management.php' class=linky><br>Find Another User to Examine</a></div>";

include "library/footer.php";

?>

        <!-- if the page navigator is showing, we bump the 'Back to Top' button up slightly -->
        <?php
move_back_to_top_button();

?>

</body>

</html>