$(document).ready(function() {
  if (typeof qt == 'undefined') qt = {};

  // from https://stackoverflow.com/questions/280759/jquery-validate-how-to-add-a-rule-for-regular-expression-validation
  $.validator.addMethod(
    'regex',
    function(value, element, regexp) {
      var re = new RegExp(regexp);
      return this.optional(element) || re.test(value);
    },
    'Please check your input.'
  );

  jQuery.validator.addMethod(
    'nameComponent',
    function(value, element) {
      return (
        $('#FIRST_NAME')
          .val()
          .trim().length +
          $('#LAST_NAME')
            .val()
            .trim().length >=
        qt.minimumFullNameLength
      );
    },
    'Combined first and last name must be at least ' +
      qt.minimumFullNameLength +
      ' characters long'
  );

  user_form_validation = {
    errorClass: 'error-message',
    rules: {
      FIRST_NAME: {
        nameComponent: true
      },
      LAST_NAME: {
        nameComponent: true
      },
      PASSWORD1: {
        required: true,
        minlength: 8
      },
      CONFIRM_PASSWORD: {
        required: true,
        minlength: 8,
        equalTo: 'form#new-user-form #pass1'
      }
    },
    groups: {
      name: 'FIRST_NAME LAST_NAME'
    },
    // errorPlacement: qt.nameErrorPlacement  // has problems with multiple elements on the page with the same class name
  };

  $('form#edit-user').validate(user_form_validation);
  $('form#new-user-form').validate(user_form_validation);

  // Set up the the table for DataTable
  $('#manage-users').DataTable({
    lengthMenu: [
      [50, 100, -1],
      [50, 100, 'All']
    ],
    stateSave: true,
    paging: true,
    fixedHeader: {
      headerOffset: 40
    },
    info: true,
    columnDefs: [
      {
        targets: 5,
        render: function(data, type, row, meta) {
          if (type === 'display') {
            switch (data) {
              case 'SUPERUSER':
                return "<img src='/images/superman.png' class='qt-icon' alt='Super-Admin' title='Super-Admin' valign=middle>";

              case 'ADMIN':
                return "<img src='/images/manager.png' class='qt-icon' alt='Admin' title='Admin' valign=middle>";

              case 'WORD_FIXER':
                return "<img src='/images/wordfixer.png' class='qt-icon' alt='Word Fixer' title='Word Fixer' valign=middle>";

              default:
                return data;
            }
          }
          return data;
        }
      },
      { targets: 6, orderable: false }
      // this doesn't appear to work properly, but is here for completeness
      //   { targets: 8, visible: false }
    ],
    // This dom config used to have a B in it for button initialisation. Although the Button component is installed, this B parameter throws an error. Since we are not using datatables buttons, it has been removed from there
    dom: '<"top"lfr>t<"bottom"ip>'
  });
});
