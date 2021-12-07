<?php

$mini_mode_extra_class = "";

if (isset($miniMode))
{
    if ($miniMode)
    {
        $mini_mode_extra_class = "provenance_mini";
    }
}

// we will slowly add the ability to colour sura data chart columns
// by Mark Durie's schema, not just the traditional "Meccan" or "Medinan"

if (!isset($durieMode))
{
    $durieMode = false;
}

if ($durieMode)
{
    echo "<div class='provenance-legend'>";
    echo "<span class='pre-transitional $mini_mode_extra_class'>Pre-Transitional Suras</span>&nbsp;&nbsp;";
    echo "<span class='post-transitional $mini_mode_extra_class'>Post-Transitional Suras</span>&nbsp;&nbsp;";
    echo "<span class='mixed-transitional $mini_mode_extra_class'>Mixed Suras</span>";
    echo "</div>";
}
else
{
    echo "<div class='provenance-legend'>";
    echo "<span class='meccan $mini_mode_extra_class'>Meccan Suras</span>&nbsp;&nbsp;";
    echo "<span class='medinan $mini_mode_extra_class'>Medinan Suras</span>";
    echo "</div>";
}
