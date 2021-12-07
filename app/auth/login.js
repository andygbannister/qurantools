$(function() {
  Tipped.create('.yellow-tooltip', {
    position: 'bottommiddle',
    maxWidth: 420,
    skin: 'lightyellow',
    showDelay: 1000
  });
});

$('form#login').validate({
  errorClass: 'error-message',
  rules: {
    EMAIL_ADDRESS: {
      required: true,
      email: true
    },
    PASSWORD: {
      required: true
    }
  }
});
