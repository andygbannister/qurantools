// This code should be replaced entirely by switching to the jQuery validate plugin
function checkPass(whichPassword, basicHeightOfContainer) {
  // the height of the container that wraps the password form, if we will be modifying it
  if (basicHeightOfContainer > 0) {
    actualHeightOfContainer = $('#PASSWORD_PANEL').height();
  }

  message1Shown = false;
  message2Shown = false;

  // save the IDs of the elements we will need

  fieldID1 = document.getElementById('pass1');
  warningID1 = document.getElementById('PasswordWarning1');

  fieldID2 = document.getElementById('pass2');
  warningID2 = document.getElementById('PasswordWarning2');

  // hide warning labels

  warningID1.style.display = 'none';
  warningID2.style.display = 'none';

  // length check

  if (fieldID1.value.length > 0 && fieldID1.value.length < 8) {
    warningID1.innerHTML = 'Passwords should be 8 characters or longer';
    warningID1.style.display = 'block';

    message1Shown = true;
  }

  if (fieldID2.value.length > 0 && fieldID2.value.length < 8) {
    warningID2.innerHTML = 'Passwords should be 8 characters or longer';
    warningID2.style.display = 'block';

    message2Shown = true;
  }

  // match check

  if (fieldID1.value.length > 0 && fieldID2.value.length > 0) {
    if (fieldID1.value != fieldID2.value) {
      if (whichPassword == 1) {
        warningID1.innerHTML = 'Passwords do not match';
        warningID1.style.display = 'block';

        message1Shown = true;
      } else {
        warningID2.innerHTML = 'Passwords do not match';
        warningID2.style.display = 'block';

        message2Shown = true;
      }
    }
  }
}
