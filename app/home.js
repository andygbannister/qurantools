// These functions should be name-spaced into the qt javascript object to avoid
// polluting the global namespace and getting weird bugs in the future...

function setKeyboardDirectionStyles(keyboard_direction) {
  if (keyboard_direction == 'RTL') {
    $('#keyboard').css('flex-direction', 'row-reverse');
    $('#keyboard .column').css('border-width', '1px 1px 1px 0px');
    $('#keyboard .column:last-child').css('border-left-width', '1px');
  } else {
    $('#keyboard').css('flex-direction', 'row');
    $('#keyboard .column').css('border-width', '1px 0px 1px 1px');
    $('#keyboard .column:last-child').css('border-right-width', '1px');
  }
}

function flip_keyboard() {
  keyboard_direction = keyboard_direction == 'LTR' ? 'RTL' : 'LTR';
  setKeyboardDirectionStyles(keyboard_direction);
}

function theCursorPosition(ofThisInput) {
  // set a fallback cursor location
  var theCursorLocation = 0;

  // find the cursor location via IE method...
  if (document.selection) {
    ofThisInput.focus();
    var theSelectionRange = document.selection.createRange();
    theSelectionRange.moveStart('character', -ofThisInput.value.length);
    theCursorLocation = theSelectionRange.text.length;
  } else if (ofThisInput.selectionStart || ofThisInput.selectionStart == '0') {
    // or the FF way
    theCursorLocation = ofThisInput.selectionStart;
  }
  return theCursorLocation;
}

function ChooseContact(data) {
  document.getElementById('inputText').value =
    document.getElementById('inputText').value + data.value;
}

function AddText(data) {
  // move focus back to the text field
  document.getElementById('inputText').focus();

  // get the caret position
  cursor_position = theCursorPosition(inputText);

  // get the contents of the field
  theText = document.getElementById('inputText').value;

  // insert the string at the cursor position
  theText = [
    theText.slice(0, cursor_position),
    data,
    theText.slice(cursor_position)
  ].join('');

  // save it back to the field
  document.getElementById('inputText').value = theText;

  // hack for a bug that sees the placeholder not get wiped out when focus set programmatically
  document.getElementById('inputText').placeholder = '';
  document.getElementById('inputText').placeholder =
    'Verse, range, or search command';
}

function backspace(data) {
  document.getElementById('inputText').value = document
    .getElementById('inputText')
    .value.slice(0, -1);
  document.getElementById('inputText').focus();
}

// replace the contents of the search box with text

function overwriteInputText(data) {
  document.getElementById('inputText').value = data;
  document.getElementById('inputText').focus();
}

function LoadHistory(data) {
  overwriteInputText(data);
}

function LoadBookmark(data) {
  document.getElementById('inputText').value = data;
  document.getElementById('inputText').focus();
}

function hide_messages() {
  $('#message').hide();
}

function delete_single_history_item(item, timecode) {
  if (confirm("Delete history item '" + item + "'?")) {
    window.location.assign('home.php?DH=' + timecode);
  }
}

function delete_all_history() {
  if (confirm('Delete all history items?')) {
    window.location.assign('home.php?DH=ALL');
  }
}

function delete_single_bookmark(item, timecode) {
  if (confirm("Delete the bookmark '" + item + "'?")) {
    window.location.assign('home.php?DB=' + timecode);
  }
}

function delete_all_bookmarks() {
  if (confirm('Delete all bookmarks?')) {
    window.location.assign('home.php?DB=ALL');
  }
}

function calculatedExpanderHeight() {
  var tipHeight = $('.tpd-tooltip').height();
  var tpdTooltipTop = $('.tpd-tooltip').offset().top;
  var tippedExpanderTop = $('#tipped-expander').offset().top;
  var footerHeight = 40; // It's actually about 60px, but lets leave 20px at the bottom
  var footerTopMargin = parseInt($('.footer-content-holder').css('margin-top'));
  var expanderLessTooltip = tippedExpanderTop - tpdTooltipTop;
  var expanderHeight =
    tipHeight -
    (tippedExpanderTop - tpdTooltipTop) -
    footerHeight -
    footerTopMargin;
  return expanderHeight;
}

// The code in this .ready block is all about setting up the Tipped.js stuff for the pop-ups on this page.
$(document).ready(function() {
  Tipped.create('.yellow-tooltip', {
    position: 'bottommiddle',
    maxWidth: 420,
    skin: 'lightyellow',
    showDelay: 1000
  });

  // standard options used for all the pop-ups created with Tipped.js
  var standardPopUpTippedOptions = {
    position: 'bottommiddle',
    radius: false,
    skin: 'qt',
    size: 'large',
    showOn: false,
    hideOn: false,
    hideOthers: true,
    close: true,
    zIndex: 5 // ensure that these pop-ups are below the main menu and flyouts
  };

  /**
   * The following `.create` code relies on the ECMA6 spread operator (...). It
   * should work on pretty much all modern web browsers, but if we have issues,
   * then adding babel or something like that might be needed.
   * https://babeljs.io/
   * Or we could just create the options variable manually
   * If this file was an external JS file, then we could use the brunch-babel npm
   * package to precompile it and turn it into vanilla ES5 javascript
   * https://github.com/babel/babel-brunch
   */
  Tipped.create('#bookmarks-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'bookmarks-container',
    title: 'Bookmarks'
  });

  Tipped.create('#history-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'history-container',
    title: 'History'
  });

  Tipped.create('#tags-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'tags-container',
    title: 'Tags'
  });

  Tipped.create('#search-commands-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'search-commands-container',
    title: 'Search Commands'
  });

  Tipped.create('#roots-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'roots-container',
    title: 'Roots'
  });

  Tipped.create('#verse-picker-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'verse-picker-container',
    title: 'Choose a Sura'
  });

  Tipped.create('#keyboard-anchor', {
    ...standardPopUpTippedOptions,
    inline: 'keyboard-container',
    title: 'Arabic/Transliteration Keyboard'
  });

  Tipped.create('#extra-chars-anchor', {
    ...standardPopUpTippedOptions,
    hideOthers: false,
    skin: 'light',
    ajax: { url: 'ajax/ajax_buckwalter_list.php' }
  });

  if (bookmarkDisplaySetting != 'none') {
    Tipped.show('#bookmarks-anchor');
  }

  if (historyDisplaySetting != 'none') {
    Tipped.show('#history-anchor');
  }

  $('.pop-up-anchor').on('click', function(e) {
    // show/hide the clicked on tip
    Tipped.toggle($(e.target));

    // If showing: hide the menu bar search box and results, show menu so that pop-ups don't obscure it
    if ($('#search-bar').is(':visible')) {
      hideSearchBar();
      showMenu();
    }

    // the setTimeout is needed to ensure that the previous toggle has completed
    // before we recompute the page size... An ugly hack because there are not
    // listeners/callback hooks on the the show/hide events telling us when they have
    // completed. If the timeout is not long enough, then weird results ensue.
    setTimeout(function() {
      // ensure page is long enough so that footer always shows
      if (Tipped.visible($(e.target))) {
        $('#tipped-expander').height(calculatedExpanderHeight());

        // reset page height after pop-up is no longer showing
        $('.tpd-close-icon').on('click', function() {
          $('#tipped-expander').height(0);
          // we could put something here to reset the verse/aya picker if needed but it may not be necessary
        });
      } else {
        $('#tipped-expander').height(0);
      }
    }, 250);
  });

  $('#keyboard-anchor').on('click', function() {
    setKeyboardDirectionStyles(keyboard_direction);
  });
});
