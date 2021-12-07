<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';
?>
<html>

<head>
    <?php
    include 'library/standard_header.php';
    window_title('Formulaic Density Summaries');
    ?>
</head>

<body class='qt-site'>
    <main class='qt-site-content'>
        <?php
        // used to slightly customise the table css
        $thick_right_line = "style='border-right: 2px solid gray;'";

        // menubar

        include 'library/menu.php';

        // show comparative figures from "An Oral-Formulaic Study of the Qur’an

        $show_figures_from_book = false;

        if (isset($_GET['BOOK']))
        {
            if ($_GET['BOOK'] == 'Y')
            {
                $show_figures_from_book = true;
            }
        }

        echo "<div align=center><h2 class='page-title-text'>Formulaic Density Summaries</h2>";

        echo "<table class='hoverTable'>";

        echo '<tr>';
        echo '<th colspan=10 align=center bgcolor=#c0c0c0><b>Total Formulaic Densities</b></th>';

        echo '</tr>';

        // table header rows

        echo '<tr>';
        echo "<th rowspan=2 $thick_right_line><b>Provenance</b></th>";
        echo "<th colspan=3 $thick_right_line align=center><b>Root Formulae</b></th>";
        echo "<th colspan=3 $thick_right_line align=center><b>Root-Plus-Particle/Pronoun Formulae</b></th>";
        echo '<th colspan=3 align=center><b>Lemma Formulae</b></th>';
        echo '</tr>';

        echo '<tr>';
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo "<th align=center $thick_right_line><b>Length: 5</b></th>";
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo "<th align=center $thick_right_line><b>Length: 5</b></th>";
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo '<th align=center><b>Length: 5</b></th>';
        echo '</tr>';

        // set up fields

        $PRE_RENDER = false;

        $check_prerender = db_rowcount(
            db_query('SELECT * FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES`')
        );

        if ($check_prerender > 0)
        {
            $PRE_RENDER = true; // if this is set, we use the "pre rendered" data, we don't calculate it afresh
        }

        if (isset($_GET['PRERENDER']))
        {
            if ($_GET['PRERENDER'] == 'OFF')
            {
                $PRE_RENDER = false;
            }
        }

        if (!$PRE_RENDER)
        {
            // roots

            $ROOTS_ALL = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-ROOT`!=''"
            );

            $ROOTS_MECCAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `PROVENANCE`='Meccan'"
            );

            $ROOTS_MEDINAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `PROVENANCE`='Medinan'"
            );

            // 3 root formulae

            $FLAGGED_ROOTS_ALL_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `FORMULA-3-ROOT` > 0"
            );

            $FLAGGED_ROOTS_MECCAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-3-ROOT` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_ROOTS_MEDINAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-3-ROOT` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 4 root formulae

            $FLAGGED_ROOTS_ALL_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `FORMULA-4-ROOT` > 0"
            );

            $FLAGGED_ROOTS_MECCAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-4-ROOT` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_ROOTS_MEDINAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-4-ROOT` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 5 root formulae

            $FLAGGED_ROOTS_ALL_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-ROOT`!='' AND `FORMULA-5-ROOT` > 0"
            );

            $FLAGGED_ROOTS_MECCAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-5-ROOT` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_ROOTS_MEDINAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-ROOT`!='' AND `FORMULA-5-ROOT` > 0 AND `PROVENANCE`='Medinan'"
            );

            // roots-with-particles-and-pronouns

            $RPROP_ALL = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!=''"
            );
            $RPROP_MECCAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `PROVENANCE`='Meccan'"
            );
            $RPROP_MEDINAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `PROVENANCE`='Medinan'"
            );

            // 4 root+particle_pronoun formulae

            $FLAGGED_RPROP_ALL_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-3-ROOT-ALL` > 0"
            );

            $FLAGGED_RPROP_MECCAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-3-ROOT-ALL` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_RPROP_MEDINAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-3-ROOT-ALL` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 4 root+particle_pronoun formulae

            $FLAGGED_RPROP_ALL_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-4-ROOT-ALL` > 0"
            );

            $FLAGGED_RPROP_MECCAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-4-ROOT-ALL` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_RPROP_MEDINAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-4-ROOT-ALL` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 5 root+particle_pronoun formulae

            $FLAGGED_RPROP_ALL_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-5-ROOT-ALL` > 0"
            );

            $FLAGGED_RPROP_MECCAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-5-ROOT-ALL` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_RPROP_MEDINAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `ROOT OR PARTICLE`!='' AND `FORMULA-5-ROOT-ALL` > 0 AND `PROVENANCE`='Medinan'"
            );

            // LEMMA

            $LEMMA_ALL = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-LEMMA`!=''"
            );

            $LEMMA_MECCAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `PROVENANCE`='Meccan'"
            );

            $LEMMA_MEDINAN = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `PROVENANCE`='Medinan'"
            );

            // 3 lemma formulae

            $FLAGGED_LEMMA_ALL_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `FORMULA-3-LEMMA` > 0"
            );

            $FLAGGED_LEMMA_MECCAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-3-LEMMA` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_LEMMA_MEDINAN_3 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-3-LEMMA` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 4 lemma formulae

            $FLAGGED_LEMMA_ALL_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `FORMULA-4-LEMMA` > 0"
            );

            $FLAGGED_LEMMA_MECCAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-4-LEMMA` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_LEMMA_MEDINAN_4 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-4-LEMMA` > 0 AND `PROVENANCE`='Medinan'"
            );

            // 5 lemma formulae

            $FLAGGED_LEMMA_ALL_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` WHERE `QTL-LEMMA`!='' AND `FORMULA-5-LEMMA` > 0"
            );

            $FLAGGED_LEMMA_MECCAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-5-LEMMA` > 0 AND `PROVENANCE`='Meccan'"
            );

            $FLAGGED_LEMMA_MEDINAN_5 = db_return_one_record_one_field(
                "SELECT COUNT(*) FROM `QURAN-DATA` LEFT JOIN `SURA-DATA` ON `SURA`=`Sura Number` WHERE `QTL-LEMMA`!='' AND `FORMULA-5-LEMMA` > 0 AND `PROVENANCE`='Medinan'"
            );

            // flush then save data to the pre-render table

            db_query('TRUNCATE TABLE `RENDER-FORMULAIC-DENSITY-SUMMARIES`');

            db_query(
                "INSERT INTO `RENDER-FORMULAIC-DENSITY-SUMMARIES` (`ID`, `ROOT_3`, `ROOT_4`,`ROOT_5`, `ROOTPLUS_3`, `ROOTPLUS_4`, `ROOTPLUS_5`, `LEMMA_3`, `LEMMA_4`,`LEMMA_5`)
VALUES
('TOTAL-ALL', " .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_3 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_4 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_5 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_3 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_4 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_5 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_3 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_4 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_5 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ')'
            );

            db_query(
                "INSERT INTO `RENDER-FORMULAIC-DENSITY-SUMMARIES` (`ID`, `ROOT_3`, `ROOT_4`,`ROOT_5`, `ROOTPLUS_3`, `ROOTPLUS_4`, `ROOTPLUS_5`, `LEMMA_3`, `LEMMA_4`,`LEMMA_5`)
VALUES
('TOTAL-MECCAN', " .
                    number_format(
                        ($FLAGGED_ROOTS_MECCAN_3 / $ROOTS_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_MECCAN_4 / $ROOTS_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_MECCAN_5 / $ROOTS_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MECCAN_3 / $RPROP_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MECCAN_4 / $RPROP_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MECCAN_5 / $RPROP_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MECCAN_3 / $LEMMA_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MECCAN_4 / $LEMMA_MECCAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MECCAN_5 / $LEMMA_MECCAN) * 100,
                        2
                    ) .
                    ')'
            );

            db_query(
                "INSERT INTO `RENDER-FORMULAIC-DENSITY-SUMMARIES` (`ID`, `ROOT_3`, `ROOT_4`,`ROOT_5`, `ROOTPLUS_3`, `ROOTPLUS_4`, `ROOTPLUS_5`, `LEMMA_3`, `LEMMA_4`,`LEMMA_5`)
                VALUES
                ('TOTAL-MEDINAN', " .
                    number_format(
                        ($FLAGGED_ROOTS_MEDINAN_3 / $ROOTS_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_MEDINAN_4 / $ROOTS_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_MEDINAN_5 / $ROOTS_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MEDINAN_3 / $RPROP_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MEDINAN_4 / $RPROP_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_MEDINAN_5 / $RPROP_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MEDINAN_3 / $LEMMA_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MEDINAN_4 / $LEMMA_MEDINAN) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_MEDINAN_5 / $LEMMA_MEDINAN) * 100,
                        2
                    ) .
                    ')'
            );

            db_query(
                "INSERT INTO `RENDER-FORMULAIC-DENSITY-SUMMARIES` (`ID`, `ROOT_3`, `ROOT_4`,`ROOT_5`, `ROOTPLUS_3`, `ROOTPLUS_4`, `ROOTPLUS_5`, `LEMMA_3`, `LEMMA_4`,`LEMMA_5`)
VALUES
('AVERAGE-ALL', " .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_3 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_4 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_ROOTS_ALL_5 / $ROOTS_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_3 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_4 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_RPROP_ALL_5 / $RPROP_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_3 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_4 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ', ' .
                    number_format(
                        ($FLAGGED_LEMMA_ALL_5 / $LEMMA_ALL) * 100,
                        2
                    ) .
                    ')'
            );
        }

        // ALL SURAS

        echo '<tr>';

        echo "<td align=center $thick_right_line>All Suras</td>";

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_ROOTS_ALL_3 / $ROOTS_ALL) * 100, 2) .
                '%</a>';
        }
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_ROOTS_ALL_4 / $ROOTS_ALL) * 100, 2) .
                '%</a></td>';
        }

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_ROOTS_ALL_5 / $ROOTS_ALL) * 100, 2) .
                '%</a></td>';
        }

        // root-plus-particles/pronouns

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_RPROP_ALL_3 / $RPROP_ALL) * 100, 2) .
                '%</a>';
        }
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>80.97%</font>';
        }
        echo '</td>';
        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_RPROP_ALL_4 / $RPROP_ALL) * 100, 2) .
                '%</a>';
        }
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>58.46%</font>';
        }
        echo '</td>';
        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";

        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_RPROP_ALL_5 / $RPROP_ALL) * 100, 2) .
                '%</a>';
        }
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>37.06%</font>';
        }
        echo '</td>';

        // lemma

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_LEMMA_ALL_3 / $LEMMA_ALL) * 100, 2) .
                '%</a>';
        }
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";

        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_LEMMA_ALL_4 / $LEMMA_ALL) * 100, 2) .
                '%</a>';
        }
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";

        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-ALL'"
            ) . '%';
        }
        else
        {
            echo number_format(($FLAGGED_LEMMA_ALL_5 / $LEMMA_ALL) * 100, 2) .
                '%</a>';
        }
        echo '</td>';

        echo '</tr>';

        // MECCAN SURAS

        echo '<tr>';

        echo "<td align=center $thick_right_line>Meccan</td>";

        // root

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MECCAN_3 / $ROOTS_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MECCAN_4 / $ROOTS_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MECCAN_5 / $ROOTS_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        // root-plus-particles/pronouns

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_RPROP_MECCAN_3 / $RPROP_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>68.85%</font>';
        }
        echo '</td>';
        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo '' .
                number_format(
                    ($FLAGGED_RPROP_MECCAN_4 / $RPROP_MECCAN) * 100,
                    2
                ) .
                '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>43.55%</font>';
        }
        echo '</td>';
        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo '' .
                number_format(
                    ($FLAGGED_RPROP_MECCAN_5 / $RPROP_MECCAN) * 100,
                    2
                ) .
                '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>25.71%</font>';
        }
        echo '</td>';

        // lemma

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MECCAN_3 / $LEMMA_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MECCAN_4 / $LEMMA_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MECCAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MECCAN_5 / $LEMMA_MECCAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo '</tr>';

        // MEDINAN SURAS

        echo '<tr>';

        echo "<td align=center $thick_right_line>Medinan</td>";

        // root

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MEDINAN_3 / $ROOTS_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MEDINAN_4 / $ROOTS_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOT_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_ROOTS_MEDINAN_5 / $ROOTS_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a></td>';

        // root-plus-particles/pronouns

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_RPROP_MEDINAN_3 / $RPROP_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>81.94%</font>';
        }
        echo '</td>';
        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_RPROP_MEDINAN_4 / $RPROP_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>60.06%</font>';
        }
        echo '</td>';
        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `ROOTPLUS_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_RPROP_MEDINAN_5 / $RPROP_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        if ($show_figures_from_book)
        {
            echo '<br><font color=gray size=-1>39.11%</font>';
        }
        echo '</td>';

        // lemma

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_3` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MEDINAN_3 / $LEMMA_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_4` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MEDINAN_4 / $LEMMA_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";
        if ($PRE_RENDER)
        {
            echo db_return_one_record_one_field(
                "SELECT `LEMMA_5` FROM `RENDER-FORMULAIC-DENSITY-SUMMARIES` WHERE `ID`='TOTAL-MEDINAN'"
            ) . '%';
        }
        else
        {
            echo number_format(
                ($FLAGGED_LEMMA_MEDINAN_5 / $LEMMA_MEDINAN) * 100,
                2
            ) . '%';
        }
        echo '</a>';
        echo '</td>';

        echo '</tr>';

        echo '</table>';

        // AVERAGE DATASET

        echo '<br>';

        echo "<table class='hoverTable'>";

        echo '<tr>';
        echo '<th colspan=10 align=center bgcolor=#c0c0c0><b>Average Formulaic Densities</b></th>';

        echo '</tr>';

        // table header rows

        echo '<tr>';
        echo "<th rowspan=2 $thick_right_line><b>Provenance</b></th>";
        echo "<th colspan=3 $thick_right_line align=center><b>Root Formulae</b></th>";
        echo "<th colspan=3 $thick_right_line align=center><b>Root-Plus-Particle/Pronoun Formulae</b></th>";
        echo '<th colspan=3 align=center><b>Lemma Formulae</b></th>';
        echo '</tr>';

        echo '<tr>';
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo "<th align=center $thick_right_line><b>Length: 5</b></th>";
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo "<th align=center $thick_right_line><b>Length: 5</b></th>";
        echo '<th align=center><b>Length: 3</b></th>';
        echo '<th align=center><b>Length: 4</b></th>';
        echo '<th align=center><b>Length: 5</b></th>';
        echo '</tr>';

        // ALL AVERAGES
        echo '<tr>';
        echo "<td align=center $thick_right_line>All Suras</td>";

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-4-ROOT`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-5-ROOT`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-3-ROOT-ALL`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-4-ROOT-ALL`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-5-ROOT-ALL`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        // LEMMA AVERAGES
        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-3-LEMMA`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-4-LEMMA`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                'SELECT AVG(`FORMULAIC-DENSITY-5-LEMMA`) FROM `QURAN-FULL-PARSE`'
            ),
            2
        );
        echo '%</a></td>';

        echo '</tr>';

        // MECCAN AVERAGES
        echo '<tr>';
        echo "<td align=center $thick_right_line>Meccan</td>";

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Meccan'"
            ),
            2
        );
        echo '%</a></td>';

        echo '</tr>';

        // MEDINAN AVERAGES
        echo '<tr>';
        echo "<td align=center $thick_right_line>Medinan</td>";

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-ROOT`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center $thick_right_line><a class=linky href='formulaic_density_by_sura.php?TYPE=ROOT-ALL&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-ROOT-ALL`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=3'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-3-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=4'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-4-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo "<td align=center><a class=linky href='formulaic_density_by_sura.php?TYPE=LEMMA&L=5'>";
        echo number_format(
            db_return_one_record_one_field(
                "SELECT AVG(`FORMULAIC-DENSITY-5-LEMMA`) FROM `QURAN-FULL-PARSE` WHERE `Provenance`='Medinan'"
            ),
            2
        );
        echo '%</a></td>';

        echo '</tr>';

        echo '</table>';

        // print footer

        include 'library/footer.php';
        ?>
</body>

</html>