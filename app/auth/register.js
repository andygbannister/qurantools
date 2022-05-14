$(function () {
    Tipped.create('.yellow-tooltip', {
        position: 'bottommiddle',
        maxWidth: 420,
        skin: 'lightyellow',
        showDelay: 1000
    });
});

$(document).ready(function () {

    $("form#register").validate({
        errorClass: "error-message",
        rules: {
            PASSWORD: {
                required: true,
                minlength: 8
            },
            PASSWORD_AGAIN: {
                equalTo: "#PASSWORD"
            },
            EMAIL: {
                required: true,
                email: true
            }
        },
        groups: {
            name: "FIRST_NAME LAST_NAME"
        },
        errorPlacement: qt.nameErrorPlacement
    });

    jQuery.validator.addMethod(
        "name-component",
        function (value, element) {
            return (
                $("#FIRST_NAME")
                .val()
                .trim().length +
                $("#LAST_NAME")
                .val()
                .trim().length >= qt.minimum_name_length
            );
        },
        'Your combined first and last name must be at least ' + qt.minimum_name_length + ' characters long'
    );
})