Stripe.setPublishableKey(stripe_vars.publishable_key); //receive publishable_key as input arguements

//callback function of stripe createtoken
function stripeResponseHandler(status, response) {
    if (response.error) {
        // show errors returned by Stripe
        jQuery("#ak-stripe-process-payment-failure").html(response.error.message);
        // re-enable the submit button
        jQuery('#ak-stripe-submit-payment').attr("disabled", false);
        $("#ak-loaderImg_process-payment").hide();
    } else {
        var form$ = jQuery("#ak-stripe-payment-form");
        // token contains id, last4, and card type
        var token = response['id'];

        // insert the token into the form so it gets submitted to the server
        form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        jQuery('#ak-stripe-submit-payment').attr("disabled", false);
        ak_payment_form_elements = form$.serialize();
        var data = {
            'action': 'ak_stripe_submit_payment',
            'post_id': 1,
            'datastring': ak_payment_form_elements
        };
        $.post(stripe_vars.ajaxurl, data, function (response) {
            $("#ak-loaderImg_process-payment").hide();
            $("#ak-stripe-payment-form").hide();
            $(".ak-stripe-payment-form").hide();
            if (response[0] === "success") {
                $("#ak-stripe-process-payment-success").html("<p><strong></strong></p>Thank you for your payment. Your payment transaction id is <strong>" + response[1] + "</strong></p>");
            }
            else {
                $("#ak-stripe-process-payment-failure").html('<p><strong>Error</strong></p>' + response[1] + '<p><strong></strong></p>');
            }
        });
        return false;
    }
}
jQuery(document).ready(function ($) {
    $('.cc-number').payment('formatCardNumber');
    $('.cc-exp').payment('formatCardExpiry');
    $('.cc-cvc').payment('formatCardCVC');

    $.fn.toggleInputError = function (erred) {
        this.parent('.form-group').toggleClass('has-error', erred);
        return this;
    };

    function validateEmail(email) {
        var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        var valid = emailReg.test(email);
        if (!valid) {
            return false;
        } else {
            return true;
        }
    }

    function ValidateName(name) {
        var valid = /^[A-Za-z\s]+$/.test(name);
        if (!valid) {
            return false;
        } else {
            return true;
        }
    }

    $("#ak-stripe-cancel-payment").click(function () {
        $('#ak-stripe-payment-form').trigger("reset");
        return false;
    });

    $("#ak-stripe-submit-payment").click(function () {
        $("#ak-loaderImg_process-payment").show();
        var split = ($('.cc-exp').val()).split('/');
        var cc_month = parseInt(split[0]);
        var cc_year = parseInt(split[1]);
        var cardType = $.payment.cardType($('.cc-number').val());
        $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
        $('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
        $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
        $('.cc-brand').text(cardType);
        $('.card-email').toggleInputError(!validateEmail($('.card-email').val()));
        $('.card-name').toggleInputError(!ValidateName($('.card-name').val()));
        $('.validation').removeClass('text-danger text-success');
        $('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');
        // disable the submit button to prevent repeated clicks
        //$('#ak-stripe-submit-payment').attr("disabled", "disabled");

        if ($("#ak-stripe-payment-form div").hasClass('has-error')) { // in case of any validation error do not submit the form
            $("#ak-loaderImg_process-payment").hide();
            return false;
        }
        else {
            Stripe.card.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: cc_month,
                exp_year: cc_year
            }, stripeResponseHandler);
            return false;
        }

    });


});
