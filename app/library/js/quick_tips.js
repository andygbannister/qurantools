function show_quick_tip(event, quickTipId) {
  if (quickTipId) {
    $.post('ajax/ajax_quick_tips.php', {
      action: 'get_quick_tip',
      quick_tip_id: quickTipId
    }).done(function(data) {
      $('#quick-tips').html(data);
      $('#quick-tips').css('display', 'inline-table');
    });
  }
}

function hide_quick_tips(event) {
  event.preventDefault();
  $.post('ajax/ajax_quick_tips.php', { action: 'hide_quick_tips' }).done(
    function(data) {
      var message = 'You can turn quick tips back on in your preferences';
      // Using 'Tipped' would be a slightly nicer way to show this message, but needs some work to hide after a short delay
      // Tipped.create("a[href='preferences.php']", message).show();
      alert(message);
      $('#quick-tips').hide();
    }
  );
}
