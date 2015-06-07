Stripe.setPublishableKey(stripe_vars.publishable_key);
function stripeResponseHandler(status, response) {
    if (response.error) {
        // show errors returned by Stripe
        jQuery(".payment-errors").html(response.error.message);
        // re-enable the submit button
        jQuery('#ak-stripe-submit-payment').attr("disabled", false);
    } else {
        var form$ = jQuery("#ak-stripe-payment-form");
        // token contains id, last4, and card type
        var token = response['id'];

        // insert the token into the form so it gets submitted to the server
        form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        // and submit
        //form$.get(0).submit();
        ak_payment_form_elements = form$.serialize();
        alert("test");
        var data = {
            'action': 'ak_stripe_submit_payment',
            'post_id': 1,
            'datastring':ak_payment_form_elements
        };
        
        
         $.post(stripe_vars.ajaxurl, data, function(response) {
             alert(response);
            if (response[0]==="success"){
                $("#stripe-create-plan-success").html("<p><strong></strong></p>New Plan successfully created<p><strong></strong></p>");
                $("#stripe-create-plan-success").show();
                
                //fetch_stripe_plans();
            }
            else {
                $("#stripe-create-plan-failure").html('<p><strong></strong></p>' + response + '<p><strong></strong></p>');
                $("#stripe-create-plan-failure").show();   
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

    $("#ak-stripe-payment-form").submit(function (event) {

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

        if ($("#ak-stripe-payment-form p").hasClass('has-error')) { // in case of any validation error do not submit the form
            return false;
        }
        else {
            Stripe.card.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: cc_month,
                exp_year: cc_year
            }, stripeResponseHandler);
        }

    });



});
