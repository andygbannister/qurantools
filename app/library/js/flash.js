function qt_close_flash() {
  $.ajax({
    method: 'POST',
    url: '/ajax/ajax_clear_flash.php'
  }).done(function(msg) {
    $('#flash').hide();
  });
}

$(document).ready(function() {
  $('#flash .close-button').click(function() {
    qt_close_flash();
  });
});
