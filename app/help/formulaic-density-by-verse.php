<?php

session_start();
session_regenerate_id();

require_once '../library/config.php';
require_once '../library/functions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <?php
    require '../library/standard_header.php';
    window_title('Help: Formulaic Density by Verse');
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require 'library/menu.php'; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Density by Verse</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>

                <p>
                    The
                    <strong>Formulaic Density by Verse</strong>&nbsp;report, found by clicking on any sura number in the
                    <a href="/help/formulaic-density-and-usage-statistics-per-sura.php">Formulaic
                        Density and Usage Statistics per Sura</a> report, lists the formulaic statistics of each
                    individual verse in a sura. It looks like this:
                </p>
                <p>
                    <img src="images/formulae-per-verse.png" border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                </p>
                <table style="background-color: rgb(255, 255, 255);" cellpadding="4" cellspacing="4">
                    <tbody>
                        <tr>
                            <td valign="top">
                                1
                            </td>
                            <td>
                                <strong>Formula Length and Type</strong>. You can choose&nbsp;the type of formulae that
                                are shown in the report. (The <em>length</em> is the number of Arabic words in a
                                formula; the <em>type</em> allows you to choose from any of the three formula types that
                                Qur’an Tools understands). <a href="formulaic-analysis.php">Learn more about formula
                                    lengths and types here</a>.<br>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                2
                            </td>
                            <td>
                                <strong>View as a Chart</strong>.&nbsp;Click this button to see the information in the
                                report displayed as a chart. (You can also click the <img src="images/st.gif" class="noBdr" style="display: inline; margin: 0px;"> button at the top of the
                                page).<br>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                3
                            </td>
                            <td>
                                <strong>List All Formulae in This Selection</strong>. Click to show a list of all the
                                formulae in the sura.
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                4
                            </td>
                            <td>
                                <strong>Reference</strong>. Sura and verse information. Click on the reference to open
                                the verse in the verse browser, with the formulae coloured blue, like this:
                                <p>
                                    <img src="images/blue-verse-info.png" border=1 style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                5
                            </td>
                            <td>
                                <strong>Roots</strong>. The number of Arabic roots in each sura. (You can sort by this,
                                and indeed <em>any</em>&nbsp;column, by using the&nbsp;<img src="images/arrows.png" class="noBdr" style="display: inline; margin: 0px;">&nbsp;buttons&nbsp;at the top of
                                each column).
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                6
                            </td>
                            <td>
                                <strong>Roots Part of a Formulae</strong>. Shows the number of Arabic roots in this sura
                                that are part of a formulaic phrase. (Click on any number to open the verse in the verse
                                browser, with the formulae coloured blue).<br>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                7
                            </td>
                            <td>
                                <strong>Formulae Density</strong>. The formulaic density of this verse (the percentage
                                of words in it that are part of a formulae). You can see a chart of this column by
                                clicking the&nbsp;<img src="images/st.gif" class="noBdr" style="display: inline; margin-top: 0px; margin-bottom: 0px;"> icon at the top of
                                the column.
                            </td>
                        </tr>
                    </tbody>
                </table>
                </p>










            </section>

        </div>

        <?php include 'library/footer.php'; ?>

</body>

</html>