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
    window_title("Help: Arabic Letters, Transliterations and Encodings");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Arabic Letters, Transliterations and Encodings</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>
<p>
	
	<p>
	 Qur’an Tools offers you three different ways to input Arabic letters when you’re entering search terms on the home page. You can use the Arabic itself, or the English transliteration, or the&nbsp; 
	<a href="https://en.wikipedia.org/wiki/Buckwalter_transliteration" target="_blank">Buckwalter encoding</a>. (The latter was a system invented by <a href="http://www.qamus.org">Tim Buckwalter</a> that tries to capture every aspect of qur’anic orthography).
</p>
<p>
	 The table below shows every Arabic letter along with its transliteration and also its Buckwalter equivalents. If you have trouble remembering these when entering search terms, use the pop up keyboard on the home page:
</p>
<p>
	<img src="images/arabic-keyboard.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<h3>Arabic Letters and Their Transliterations and Buckwalter Encodings</h3>
<table border=1 cellpadding="4" cellspacing="0">
<tbody>
<tr>
	<td style="text-align: center;">
		<strong>Arabic</strong>
	</td>
	<td style="text-align: center;">
		<strong>Transliteration</strong>
	</td>
	<td style="text-align: center;">
		<strong>Buckwalter</strong>
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ا
	</td>
	<td style="text-align: center;">
		 '
	</td>
	<td style="text-align: center;">
		 A
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ب
	</td>
	<td style="text-align: center;">
		 b
	</td>
	<td style="text-align: center;">
		 b
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ت
	</td>
	<td style="text-align: center;">
		 t
	</td>
	<td style="text-align: center;">
		 t
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ث
	</td>
	<td style="text-align: center;">
		 th
	</td>
	<td style="text-align: center;">
		 v
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ج
	</td>
	<td style="text-align: center;">
		 j
	</td>
	<td style="text-align: center;">
		 j
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ح
	</td>
	<td style="text-align: center;">
		 ḥ
	</td>
	<td style="text-align: center;">
		 H
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 خ
	</td>
	<td style="text-align: center;">
		 kh
	</td>
	<td style="text-align: center;">
		 x
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 د
	</td>
	<td style="text-align: center;">
		 d
	</td>
	<td style="text-align: center;">
		 d
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ذ
	</td>
	<td style="text-align: center;">
		 dh
	</td>
	<td style="text-align: center;">
		 *
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ر
	</td>
	<td style="text-align: center;">
		 r
	</td>
	<td style="text-align: center;">
		 r
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ز
	</td>
	<td style="text-align: center;">
		 z
	</td>
	<td style="text-align: center;">
		 z
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 س
	</td>
	<td style="text-align: center;">
		 s
	</td>
	<td style="text-align: center;">
		 s
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ش
	</td>
	<td style="text-align: center;">
		 sh
	</td>
	<td style="text-align: center;">
		 $
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ص
	</td>
	<td style="text-align: center;">
		 ṣ
	</td>
	<td style="text-align: center;">
		 S
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ض
	</td>
	<td style="text-align: center;">
		 ḍ
	</td>
	<td style="text-align: center;">
		 D
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ط
	</td>
	<td style="text-align: center;">
		 ṭ
	</td>
	<td style="text-align: center;">
		 T
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ظ
	</td>
	<td style="text-align: center;">
		 ẓ
	</td>
	<td style="text-align: center;">
		 Z
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ع
	</td>
	<td style="text-align: center;">
		 ‘
	</td>
	<td style="text-align: center;">
		 E
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 غ
	</td>
	<td style="text-align: center;">
		 gh
	</td>
	<td style="text-align: center;">
		 g
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ف
	</td>
	<td style="text-align: center;">
		 f
	</td>
	<td style="text-align: center;">
		 f
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ق
	</td>
	<td style="text-align: center;">
		 q
	</td>
	<td style="text-align: center;">
		 q
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ك
	</td>
	<td style="text-align: center;">
		 k
	</td>
	<td style="text-align: center;">
		 k
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ل
	</td>
	<td style="text-align: center;">
		 l
	</td>
	<td style="text-align: center;">
		 l
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 م
	</td>
	<td style="text-align: center;">
		 m
	</td>
	<td style="text-align: center;">
		 m
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ن
	</td>
	<td style="text-align: center;">
		 n
	</td>
	<td style="text-align: center;">
		 n
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ه
	</td>
	<td style="text-align: center;">
		 h
	</td>
	<td style="text-align: center;">
		 h
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 و
	</td>
	<td style="text-align: center;">
		 w
	</td>
	<td style="text-align: center;">
		 w
	</td>
</tr>
<tr>
	<td style="text-align: center;">
		 ي
	</td>
	<td style="text-align: center;">
		 y
	</td>
	<td style="text-align: center;">
		 y
	</td>
</tr>
</tbody>
</table>
</p>









                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>