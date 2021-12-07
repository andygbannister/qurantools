<?php

// A very basic version of the menu, basically non-functional
// Used by pages like maintenance.php so they can display a
// 'menu without any database access required

include "gtm_body.php";

?>
<div id="qt-menu" class="align-center">
    <ul>
        <li>
            <a href='/home.php'>
                <img class='qt-mini-logo' src='/images/qt-mini-logo.png' alt='Small QT Logo'>
            </a>
        </li>

        <li>
            <a href=# class='top-menu'>Browse</a>
        </li>

        <li>
            <a href='#' class='top-menu'>Charts</a>
        </li>

        <li>
            <a href='#' class='top-menu'>Formulae</a>
        </li>

        <!-- help menu -->

        <li><a href='#' class='top-menu'>Help</a>

            <ul>

                <li><a id='user-guide-link' href='/help/welcome-to-quran-tools.php' target='_blank'>User Guide</a></li>

                <li><a href='#'>About</a>
                    <ul>
                        <li><a href='/about.php'>About Qur&rsquo;an Tools</a></li>
                    </ul>
                    <span id='floatarrow'>▶︎</span>
                </li>

            </ul>

        </li>

    </ul>

</div>

</div>

<div class='main-menu-spacer'></div>