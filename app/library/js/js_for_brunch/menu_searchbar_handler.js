$(document).ready(function () {
  // when you click the search icon

  $("#menu-search").click(function (e) {
    showSearchBar();
    hideMenu();
    // Ensure pop-up tips on home page don't obscure the nav bar search box or results
    if ($(".tpd-tooltip").is(":visible")) {
      closeHomeTips();
    }
    e.preventDefault(); // prevent page scrolling
  });

  // when you click the close search icon

  $("#SEARCH_CLOSE_ICON").click(function (e) {
    hideSearchBar();
    showMenu();
    e.preventDefault(); // prevent page scrolling
  });

  // prevent top menu items scrolling the main page when clicked

  $(".top-menu").click(function (e) {
    e.preventDefault(); // prevent page scrolling
  });

  // prevent user logged in name menu scrolling the main page when clicked

  $("#LOGGED_IN_NAME").click(function (e) {
    e.preventDefault(); // prevent page scrolling
  });
});

// add a listener to detect for the escape key
document.addEventListener("keydown", checkMenuKeys, false);

// various flags
suggestionsPaletteOpen = false;
suggestionsHighlightedRow = 0;
trappedKey = false;

function checkMenuKeys(e) {
  e = e || window.event;

  // escape key — so close the toolbar

  if (e.keyCode == "27") {
    hideSearchBar();
    showMenu();
  }

  // ctrl + s => open search
  // NOTE: this doesn't work on Ubuntu Chrome - it opens a save page dialog instead

  if (e.ctrlKey) {
    if (e.keyCode == 83) {
      suggestionsHighlightedRow = 0;
      showSearchBar();
      hideMenu();
    }
  }

  // down arrow key
  if (e.keyCode == "40" && suggestionsPaletteOpen) {
    // we look and see if the row the user has tried to move to exists; if it does, we highlight it

    PreviousHighlightElementID = document.getElementById(
      "suggestion" + suggestionsHighlightedRow
    );

    suggestionsHighlightedRow++;

    elementID = document.getElementById(
      "suggestion" + suggestionsHighlightedRow
    );

    if (elementID != null) {
      elementID.style.backgroundColor = "#E0E0E0";

      // prevent the default function for the key
      e.preventDefault();
      trappedKey = true;

      // move the focus back to the search form
      miniSearchBox.focus();

      // unhighlight previous
      if (PreviousHighlightElementID != null) {
        PreviousHighlightElementID.style.backgroundColor = "#F0F0F0";
      }
    } else {
      suggestionsHighlightedRow--;

      // prevent the default function for the key
      e.preventDefault();
      trappedKey = true;
    }
  }

  // up arrow key
  if (
    e.keyCode == "38" &&
    suggestionsPaletteOpen &&
    suggestionsHighlightedRow > 0
  ) {
    // we look and see if the row the user has tried to move to exists; if it does, we highlight it

    PreviousHighlightElementID = document.getElementById(
      "suggestion" + suggestionsHighlightedRow
    );

    suggestionsHighlightedRow--;

    elementID = document.getElementById(
      "suggestion" + suggestionsHighlightedRow
    );

    if (elementID != null) {
      elementID.style.backgroundColor = "#E0E0E0";

      // prevent the default function for the key
      e.preventDefault();
      trappedKey = true;

      // move the focus back to the search form
      miniSearchBox.focus();

      // unhighlight previous
      if (PreviousHighlightElementID != null) {
        PreviousHighlightElementID.style.backgroundColor = "#F0F0F0";
      }
    } else {
      suggestionsHighlightedRow++;
      // prevent the default function for the key
      e.preventDefault();
      trappedKey = true;
    }
  }

  // enter key
  if (e.keyCode == "13" && suggestionsPaletteOpen) {
    // get the ID of the hidden URL for this row
    urlID = document.getElementById("SuggestedURL" + suggestionsHighlightedRow);

    if (elementID != null) {
      // on enter, we load up the contents of XXXX and then enter will get processed normally and the page will load
      document.getElementById("miniSearchBox").value = urlID.value;
    }
  }
}

// mouse has hovered over a row
function SuggestionHover(suggestedRowNumber) {
  PreviousHighlightElementID = document.getElementById(
    "suggestion" + suggestionsHighlightedRow
  );

  suggestionsHighlightedRow = suggestedRowNumber;

  elementID = document.getElementById("suggestion" + suggestionsHighlightedRow);

  // highlight where they have pointed

  if (elementID != null) {
    elementID.style.backgroundColor = "#E0E0E0";
  }

  // unhighlight previous highlight if it exists

  if (PreviousHighlightElementID != null) {
    PreviousHighlightElementID.style.backgroundColor = "#F0F0F0";
  }
}

function processInput() {
  if (trappedKey) {
    trappedKey = false;
    return;
  }

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      suggestionsHighlightedRow = 0;

      if (this.responseText == "NONE") {
        document.getElementById("search-suggestions").innerHTML = "";
        document.getElementById("search-suggestions").style.display = "none";
        suggestionsPaletteOpen = false;
        trappedKey = false;
      } else {
        document.getElementById("search-suggestions").innerHTML = this.responseText;
        document.getElementById("search-suggestions").style.display = "block";
        suggestionsPaletteOpen = true;
      }
    }
  };

  xhttp.open("POST", "/ajax/ajax_populate_search_toolbar.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("S=" + document.getElementById("miniSearchBox").value);
}

function hideSearchBar() {
  $('#search-bar').fadeOut();
  $("#search-suggestions").hide();
  document.getElementById("miniSearchBox").value = "";
}

function showSearchBar() {
  $("#search-bar").fadeIn();
  $('#search-bar').css('display', 'flex');
  miniSearchBox.focus();
  trappedKey = false;
}

function hideMenu() {
  $('#qt-menu').fadeOut();
}

/**
 * Hide pop-ups on home.php
 */
function closeHomeTips() {
  Tipped.hide($('.pop-up-anchor'));
}

function showMenu() {
  $('#qt-menu').fadeIn();
}
