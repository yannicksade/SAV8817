var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            function format(state) {
                if (!state.id) return state.text; // optgroup
                return "<img class='flag' src='" +$('#w-flag').val()+ state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }

            $(".country_list").select2({
                placeholder: "Select",
                allowClear: true,
                formatResult: format,
                width: 'auto',
                formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
            var formWizard = $('#form_wizard');
            var form = $('form.submit_form', formWizard);
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    "apm_utilisateur_avm_registration[username]" :{
                        minlength: 4
                    },

                    "apm_utilisateur_avm_registration[plainPassword][first]": {//password
                        minlength: 5

                    },
                    "apm_utilisateur_avm_registration[plainPassword][second]": {
                        minlength: 5,
                        equalTo: ".submit_form_password"
                    },
                    "apm_utilisateur_avm_registration[email]":{
                        email:true
                    },
                    //profile
                    "apm_utilisateur_avm_registration[nom]" :{
                        minlength: 5,
                        maxlength: 255
                    },

                   "apm_utilisateur_avm_registration[telephone]": {
                       digits: true,
                       minlength: 5,
                       maxlength: 10
                    },

                    "apm_utilisateur_avm_registration[genre]": {
                    },

                    "apm_utilisateur_avm_registration[adresse]": {
                        minlength: 5,
                        maxlength: 10
                    },
                    "apm_utilisateur_avm_registration[pays]": {
                    },
                    //payment
                    card_name: {
                        required: true
                    },
                    card_number: {
                        minlength: 15,
                        maxlength: 17,
                        required: true
                    },
                    card_cvc: {
                        digits: true,
                        required: true,
                        minlength: 3,
                        maxlength: 4
                    },
                    card_expiry_date: {
                        required: true
                    },
                    'payment[]': {
                        required: true,
                        minlength: 1
                    }
                }, //rules not need when on symfony

                messages: { // custom messages for radio buttons and checkboxes
                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.validator.format("Please select at least one option")
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") === "genre") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
                    } else if (element.attr("name") === "payment[]") { // for uniform checkboxes, insert the after the given container
                        error.insertAfter("#form_payment_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    if (label.attr("for") === "gender" || label.attr("for") === "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
                        label
                            .closest('.form-group').removeClass('has-error').addClass('has-success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid') // mark the current input as valid and display OK icon
                            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            var displayConfirm = function () {
                $('#tab4 .form-control-static', form).each(function () {
                    var input = $('[name="' + $(this).attr("data-display") + '"]', form);
                    if (input.is(":radio")) {
                        input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
                    }
                    if (input.is(":text") || input.is("textarea")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    } else if ($(this).attr("data-display") === 'payment[]') {
                        var payment = [];
                        $('[name="payment[]"]:checked', form).each(function () {
                            payment.push($(this).attr('data-title'));
                        });
                        $(this).html(payment.join("<br>"));
                    }
                });
            };

            var handleTitle = function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', formWizard).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', formWizard).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current === 1) {
                    formWizard.find('.button-previous').hide();
                } else {
                    formWizard.find('.button-previous').show();
                }

                if (current >= total) {
                    formWizard.find('.button-next').hide();
                    formWizard.find('.button-submit').show();
                    displayConfirm();
                } else {
                    formWizard.find('.button-next').show();
                    formWizard.find('.button-submit').hide();
                }
                App.scrollTo($('.page-title'));
            };

            // default form wizard
            formWizard.bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {

                    success.hide();
                    error.hide();
                    if (form.valid() === false) {
                        return false;
                    }

                    handleTitle(tab, navigation, clickedIndex);
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() === false) {
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    formWizard.find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            formWizard.find('.button-previous').hide();
            $('.button-submit', formWizard).hide();

            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('.country_list', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });

        }

    };

}();

jQuery(document).ready(function () {
    FormWizard.init();
});