$("form#new-email-form").validate({
  errorClass: "error-message",
  rules: {
    NEW_EMAIL: {
      email: true
    }
  }
});

function save_preference(prefName, prefValue, userID, aToken) {
  $("#floating-message").load("ajax/ajax_update_preferences.php", {
    P: prefName,
    V: prefValue,
    U: userID,
    T: aToken
  });

  $("#floating-message").show();

  setTimeout(function() {
    $("#floating-message").hide();
  }, 1200);
}
