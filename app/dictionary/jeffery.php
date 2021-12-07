<?php

require_once '../library/config.php';
require_once 'library/functions.php';
require_once 'auth/auth.php';

// WORK OUT IF WE ARE RUNNING IN LIGHTVIEW MODE (I.E. A POP OVER WINDOW)
$LIGHTVIEW = false;
if (isset($_GET["LIGHTVIEW"]))
{
    $LIGHTVIEW = ($_GET["LIGHTVIEW"] == "YES");
}

$PAGE = 1;
if (isset($_GET["PAGE"]))
{
    $PAGE = $_GET["PAGE"];
}
if ($PAGE < 1 || $PAGE > 325)
{
    $PAGE = 1;
}

    ?>
<html>
	<head>
		<?php
            include 'library/standard_header.php';
            window_title("Arthur Jeffery's \"Foreign Vocabulary of the Qur&rsquo;an\"");
        ?>
       
	</head>
	
	<script>
		
	// when page has loaded, we'll call up the page to display	
	
	$(document).ready(function() {
	GotoPage(
	<?php
        echo $PAGE;
      ?>
	);
	});
	
	// add a listener to detect for the arrow keys
	document.onkeydown = checkKey;
	
	// ensure window has focus (otherwise keys won't be caught)
	window.focus();
	
	currentPage = 0; // global variable that will save the page number
	
	function checkKey(e) 
	{
	    e = e || window.event;
	
		if (e.keyCode == '27') 
	    {
		    // escape key
		    parent.closeLightView();
		}
	
	    if (e.keyCode == '37') 
	    {
	       // left arrow
	       currentPage--;
	       GotoPage(currentPage);
	       e.preventDefault();
	    }
	    
	    if (e.keyCode == '39')
	    {
	       // right arrow
	       currentPage++;
	       GotoPage(currentPage);
	       e.preventDefault();
	    }
	}
	
	function GotoPage(LoadPage)
	{			
		if (LoadPage < 1) {LoadPage = 1;}
		if (LoadPage > 325) {LoadPage = 325;}
		
		currentPage = LoadPage;
		
 		$("#PDFView").load("../ajax/ajax_pdf_load.php", {FOLDER:'/dictionary/Jeffery', PREFIX:'foreign_vocabulary_quran_', PAGE:LoadPage});
	}
	
	</script>
	
	<body class='qt-site' style='background-color: white;'>
<main class='qt-site-content'>
	<?php

    // menubar

    // include "../library/menu.php";

    // When running in Lightview (pop up) mode, provide an icon to pop the window into its own discrete tab

    if ($LIGHTVIEW)
    {
        echo "<span style='float:right; margin-top:10px;' title='Open this PDF dictionary page in a new window'><a href='/dictionary/jeffery.php?PAGE=$PAGE' target='_blank'><img src='../images/expand.png'></a></span>";
    }
    else
    {
        include "../library/menu.php";
    }

    // this is the DIV where the PDF and its navigator will appear

    echo "<div ID=PDFView align=center style='margin-top:10px; background-color: white;'>";

    echo "</div>";

    // if running in our own window (i.e. not a Lightview pop up) then show the normal footer

    if (!$LIGHTVIEW)
    {
        include "../library/footer.php";
    }

?>
	</body>
	
</html>