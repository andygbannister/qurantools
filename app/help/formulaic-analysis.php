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
    window_title("Help: Formulaic Analysis of the Qur’an");
    ?>
</head>

<body class='qt-site about' id='about-history'>
    <main class='qt-site-content'>

        <?php require "library/menu.php"; ?>

        <div class='page-content'>
            <section class='page-header'>
                <h2 class='page-title-text'><img src="/images/logos/qt_logo_only.png" class="qt-big-logo-header" alt="Large Qur'an Tools Logo"><br>Formulaic Analysis of the Qur’an</h2>
            </section> <!-- .page-content -->

            <section class='page-body'>




<p>
	
	<p>
	 Underlying the Arabic text of the Qur’an is an extensive network of formulaic diction &mdash; short repeated phrases that occur time and time again. Built into Qur’an Tools is a powerful suite of tools to enable you to explore, study, and analyse with these formula.
</p>
<h4> <strong>What is a Formula?</strong></h4>
<p>
	 A formula is simply a group of words that are repeated more than once in the Qur’an. For example, the&nbsp;formula 
	<em>'mn</em> + <em>‘ml</em> + <em>ṣlḥ </em>(i.e. the Arabic root&nbsp;<em>'mn </em>followed by the root&nbsp;<em>'ml</em>&nbsp;followed by the root&nbsp;<em>ṣlḥ</em>)<em>,&nbsp;</em>appears 61 times in the Qur’an. You can see every formula that occurs in the Qur’an by choosing ‘List All Formulae’ from the ‘Formulae’ menu in the Qur’an Tools menu bar.
</p>
<h4><a name="types"><strong>Formula Types</strong></a></h4>
<p>
	 The qur’anic formulae recorded in Qur’an Tools fall into three types:
</p>
<ul>
	<li><strong>Root Formulae</strong> &mdash; like&nbsp;<em>'mn</em>&nbsp;+&nbsp;<em>‘ml</em>&nbsp;+&nbsp;<em>ṣlḥ</em>&nbsp;above, a root formula is a set of Arabic roots that occur following each other (any particles or pronouns that appear in between are simply ignored).<br>&nbsp;</li>
	
	<li><strong>Roots plus Particles/Pronouns</strong> &mdash; in this case, the formula has a “slot” built into it where a particle or a pronoun must appear, in order for the formula’s occurence to be counted in the text. For example, this formula appears 37 times in the Qur’an:<br>
	<br>
	<em style="color:green;">jry + [PART/PRON] + tḥt + <em>nhr</em><br>
	</em><br>
	 The root 
	<em>jry</em>&nbsp;must be followed by a particle or pronoun, then by the root&nbsp;<em>tḥt</em>, then by the root <em>nhr</em>. This formula occurs in Q. 2:25, where we have:<br>
	<br>
	<em style="color:green;">tajrī [root: jry] min [particle] taḥtihā [root: tḥt] l‑anhāru [root: nhr</em>]<br>&nbsp;</li>

	<li><strong>Lemmata Formula</strong> &mdash; in this case, rather than Arabic roots, the formula consists of lemma &mdash; the <a href="word-lists-lemmata.php">inflected dictionary form</a> of the Arabic word. For example, here is a lemmata formula that occurs 50 times in the Qur’an:<br>
	<br>
	<em style="color:green;">alladhī + 'āmana + ʿamila + ṣāliḥāt</em></li>
</ul>
<h4><a name="length">Formula Length</a></h4>
<p>
	 As well as different types of formulae, there are also different lengths. Qur’an Tools records all formulae are 3, 4 or 5 items long. Here are some examples:
</p>
<table>
<tbody>
<tr>
	<td>
		<strong>Type</strong>
	</td>
	<td>
		<strong>Length: 3</strong>
	</td>
	<td>
		<strong>Length: 4</strong>
	</td>
	<td>
		<strong>Length: 5</strong>
	</td>
</tr>
<tr>
	<td>
		 Root
	</td>
	<td>
		<em>'mn + ‘ml + ṣlḥ </em>
	</td>
	<td>
		<em>jnn + jry + tḥt + nhr </em>
	</td>
	<td>
		<em>'rḍ + nẓr + kyf + kwn + ‘qb </em>
	</td>
</tr>
<tr>
	<td>
		 Root plus Particles/Pronouns
	</td>
	<td>
		<em>‘lm + [PART/PRON] + 'lh </em>
	</td>
	<td>
		<em>jry + [PART/PRON] + tḥt + nhr </em>
	</td>
	<td>
		<em>'lh + [PART/PRON] + kll + shy' + qdr </em>
	</td>
</tr>
<tr>
	<td>
		 Lemmata
	</td>
	<td>
		<em>ayyuhā + lladhī + 'āmana </em>
	</td>
	<td>
		<em>'inn + fī + dhālik + 'āyat </em>
	</td>
	<td>
		<em>fī + samā' + mā + fī + arḍ </em>
	</td>
</tr>
<tr>
	<td>
	</td>
	<td>
	</td>
	<td>
	</td>
	<td>
	</td>
</tr>
</tbody>
</table>
<h4> Working with Formula</h4>
<p>
	 Here are a few of the many things you can do with formulae in Qur’an Tools:
</p>
<ol>

	<li>See the formulaic density of the entire Qur’an (the percentage of the entire qur’anic text that is formulaic) at a glance using the <a href="formulaic-density-summaries.php">Formulaic Density Summary Table</a>.<br>&nbsp;</li>

	<li>View a chart of the formulaic density of individual suras of the Qur’an; choose ‘Charts’, then ‘Formulae’, then ‘<a href="formulaic-density-by-sura-chart.php">Formulaic Density per Sura</a>’.<br>&nbsp;</li>

	<li>Explore a list of all the formulae in the Qur’an by choosing ‘Formulae’ and then ‘List All Formulae’.<br>&nbsp;</li>

	<li>Easily see all the formulae in any passage in the Qur’an: for example, open sura 2 in the verse browser, then click the Formula button at the top left of the screen, choose the formulae type and length you want, then click OK.<br>&nbsp;<br>
	
	<img src="images/formula-example.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "><br>&nbsp;</li>

	<li>Quickly cross-reference all the formulae in a passage and see at a glance where else they appear in the Qur’an. For example, open sura 2 in the verse browser, then click the Formula button at the top left of the screen, then click ‘Cross Reference Formulae in Suras’.<br>&nbsp;<br>

	<img src="images/formula-example2.png" style="box-shadow: rgba(0, 0, 0, 0.5) 0px 8px 8px 3px; "></li>

</ol>
<h4>Formulaic Density and Orality</h4>
<p>
	 There is a strong argument that a text’s having a high formulaic density, as the Qur’an does, is a very significant indicator that the text (or portions thereof) were originally constructed, live-in-performance, by an oral performer. For more on this thesis, read Dr. Andrew Bannister’s essay,&nbsp; 
	<a href="https://www.academia.edu/9490706/Retelling_the_Tale_A_Computerised_Oral-Formulaic_Analysis_of_the_Qur_an" target="_blank">‘Retelling the Tale: A Computerised Oral-Formulaic Analysis of the Qur’an’</a>, or his book, <a href="an-oral-formulaic-study-of-the-quran.php" target="_blank"><em>An Oral-Formulaic Study of the Qur’an</em></a> (Lexington, 2017 [2015]).
</p>
</p>









                
            </section>

        </div>

        <?php

include "library/footer.php";

?>

</body>

</html>