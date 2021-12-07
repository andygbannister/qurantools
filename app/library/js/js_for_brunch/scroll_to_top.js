// Code to show or hide the scroll to top button -->

// When the user scrolls down 50px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 20) {
        $("#scrollTopFloatingButton").show();
    } else {
        $("#scrollTopFloatingButton").hide();
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}