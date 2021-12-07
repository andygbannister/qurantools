// library of javascript functions
if (typeof qt == 'undefined') qt = {};

// used for placing error messages related to first and last names in jQuery
// validation forms
// But it has problems if there multiple page elements same class ".names"
qt.nameErrorPlacement = function(error, element) {
  if (element.attr('id') == 'FIRST_NAME' || element.attr('id') == 'LAST_NAME') {
    error.insertAfter('.names');
  } else {
    error.insertAfter(element);
  }
};

qt.showPageLoadingSpinner = function(
  showSpinner,
  spinnerSelector = '.loading',
  pageSelector = 'main'
) {
  if (showSpinner) {
    jQuery(spinnerSelector).css('display', 'block');
    jQuery(pageSelector).css('opacity', '.3');
  } else {
    jQuery(spinnerSelector).css('display', 'none');
    jQuery(pageSelector).css('opacity', '1');
  }
};

qt.showElementLoadingSpinner = function(
  showSpinner,
  elementSelector = '.spinner'
) {
  console.log({ elementSelector });
  if (showSpinner) {
    jQuery(elementSelector).css('display', 'block');
  } else {
    jQuery(elementSelector).css('display', 'none');
  }
};
