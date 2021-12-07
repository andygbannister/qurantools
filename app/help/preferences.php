<?php

// GNU GPL License Page.

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
    window_title("Help: Preferences");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Preferences</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 The preferences screen (accessible by clicking on 
	<strong>Preferences</strong>&nbsp;in the “My Profile” menu at the top of any screen) allows you to customise several aspects of how Qur’an Tools appears and behaves. Each option is described below:
</p>
<p>
	<img src="images/prefs.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
</p>
<table cellpadding="4" cellspacing="4">
<tbody>
<tr>
	<td valign="top">
		 1
	</td>
	<td>
		<strong>User Name</strong>. This is the name that you’d like to be known by within Qur’an Tools -- for instance it appears on the top right of the main <a href="menu-bar.php">menu bar</a>, or when you communicate with us. It does not need to be the same as your login email address. If you wish to change your user name at any point, just click "Change User Name" and enter a new name.
	</td>
</tr>
<tr>
	<td valign="top">
		 2
	</td>
	<td>
		<strong>Password</strong>. If you wish to change your password, simply click "Change Password" and provide a new password. It is good Internet security practice not to use the same password for multiple websites, so we recommend you use a unique password for Qur’an Tools. (If you have trouble remembering multiple passwords, consider using a <a href="http://uk.pcmag.com/password-managers-products/4296/guide/the-best-password-managers-of-2017">password manager</a>).
	</td>
</tr>
<tr>
	<td valign="top">
		 3
	</td>
	<td>
		<strong>Account Credit</strong>. The current date your Qur’an Tools account is in credit to. Once your account expires, you will no longer be able to log in until you purchase further credit. (Or, to avoid interuption to your service, you may want to consider an annual or recurring subscription.)
	</td>
</tr>
<tr>
	<td valign="top">
		 4
	</td>
	<td>
		<strong>Search Result Highlight Colour</strong>. When you perform a search, Qur’an Tools highlights each matching word for you; by default, this is in yellow, but you can change that to any colour you prefer. Simply click in the coloured bar and choose a new colour from the colour palette that pops up.).
	</td>
</tr>
<tr>
	<td valign="top">
		 5
	</td>
	<td>
		<strong>Arabic/Transliteration Cursor Highlight Colour</strong>. When you point at an Arabic word (or transliterated word) in the <a href="the-verse-browser-in-detail.php">verse browser</a>, Qur’an Tools highlights it (and its matching Arabic or transliteration). This option lets you change the colour of that highlight “cursor”.<br>
	</td>
</tr>
<tr>
	<td valign="top">
		 6
	</td>
	<td id="TRANSLATION">
		<strong>Default Qur’an Translation</strong>. Selects your default English translation of the Qur’an. Four English translations are currently built into Qur’an Tools.
	</td>
</tr>
<tr>
	<td valign="top">
		 7
	</td>
	<td>
		<strong>Verses to Show per Page</strong>. If you look up a long selection of verses (or a search returns a long selection), Qur’an Tools will only show a certain number per "page" (and you can use the page navigator at the bottom of the verse browser to move between pages). This preference option allows you to choose how many verses you would like to see per page.
	</td>
</tr>
<tr>
	<td id="pref8" valign="top">
		 8
	</td>
	<td>
		<strong>Default Mode in Verse Browser</strong>. When viewing verses in the verse browser, you can view them in <em>Reader Mode</em>, <em>Interlinear Mode</em>, or <em>Parse</em>&nbsp;<em>Mode</em>, switching between those modes <a href="the-verse-browser-in-detail#pref4mode">using the buttons at the top of the verse browser</a>. This preference option lets you choose which mode the verse browser opens in by default.
	</td>
</tr>
<tr>
	<td valign="top">
		 9
	</td>
	<td>
		<strong>Use Italics for Transliteration</strong>. By default, Qur’an Tools displays transliterated Arabic <em>in Italics</em>. If you would prefer Qur’an Tools to use regular text for transliteration, simply change this option to "No".
	</td>
</tr>
<tr id="pref9">
	<td valign="top">
		 10
	</td>
	<td>
		<strong>Hide Transliteration in Verse Browser</strong>. Turn this on and Qur’an Tools will hide the transliteration column when browsing Qur’an verses. All that you will see will be the Arabic and your chosen translation.
	</td>
</tr>
<tr>
	<td valign="top">
		 11
	</td>
	<td>
		<strong>Show Glosses Underneath Formulae</strong>. Turn this on and when Qur’an Tools shows a formula, such as in the <a href="list-all-formulae.php">formula list</a>, a brief gloss of the formula (or a gloss of each root/word in it) will be shown under the formula, like this:<br>
		<p>
			<img src="images/formgloss.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		 Due to the wide range of meanings for many Arabic roots, these glosses are, in many cases, merely a help or hint toward the meaning of the formula; if you want to be absolutely sure, just click the formula to see it in context in each verse where it appears, along with a full translation of each verse.
	</td>
</tr>
<tr>
	<td valign="top">
		 12
	</td>
	<td>
		<strong>Show Quick Tips</strong>. By default, Qur’an Tools shows “quick tips” on the <a href="home-page.php">home page</a>, like this:<br>
		<p>
			<img src="images/qtips.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; ">
		</p>
		 You can dismiss this by clicking the close icon in any quick tip &mdash; or by using this setting here in preferences. (And, conversely, just turn this setting to “Yes” to turn quick tips back on).
	</td>
</tr>
<tr>
	<td valign="top">
		 13
	</td>
	<td>
		<strong>Arabic Keyboard Direction</strong>. The home page of Qur’an Tools includes a pop up Arabic keyboard for easy entering of Arabic letters. By default, it shows the Arabic alphabet from left to right -- if you would prefer this to be right to left, simply change the preference here.
	</td>
</tr>
<tr>
	<td valign="top">
		 14
	</td>
	<td>
		<strong>Page Navigator Position</strong>. On Qur’an Tools screens with more than one page, a page navigator tool will appear, helping you navigator between pages. By default, this floats at the bottom right of the screen; if you prefer, you can instead have it appear at the bottom of a page (and thus only be visible when you scroll to the bottom of the page).
	</td>
</tr>
<tr>
	<td valign="top">
		 15
	</td>
	<td>
		<strong>Reset Preferences to Default</strong>. Resets all the preferences to their default, i.e. to the settings they had when you first created your account.
	</td>
</tr>
</tbody>
</table>
<hr id="horizontalrule" style="margin-top:0px" '="">
<p style="text-align: center;">
	<strong><em> Preference changes are automatically saved whenever you make them (Qur’an Tools will briefly flash the message ‘Preference changes saved’ at the bottom of the screen to let you know it has done this).</em></strong>
</p>
</p>


                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>