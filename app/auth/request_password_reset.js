$('form#reset-password').validate({
  errorClass: 'error-message',
  onfocusout: false,
  rules: {
    RESET_EMAIL_ADDRESS: {
      required: true,
      email: true
    },
    errorPlacement: qt.nameErrorPlacement
  },

  submitHandler: function(form, event) {
    event.preventDefault();

    // needs for recaptacha ready
    grecaptcha.ready(function() {
      // do request for recaptcha token
      // response is promise with passed token
      grecaptcha
        .execute(qt.google_recaptcha_site_key, {
          action: qt.google_recaptcha_action
        })
        .then(
          function(token) {
            // add token to form
            // $('form#reset-password').prepend(
            $(form).prepend(
              '<input type="hidden" name="g-recaptcha-response" value="' +
                token +
                '">'
            );
            form.submit();
          },
          function(reason) {
            // something went wrong
          }
        );
    });
  }
});
