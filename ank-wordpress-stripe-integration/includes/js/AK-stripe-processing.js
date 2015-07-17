Stripe.setPublishableKey(stripe_vars.publishable_key); //receive publishable_key as input arguements

var ak_stripe_pop_up_nonce = "";
var pop_up_button_id = "";
var form_id = "";
var page_id = "";
var form_payment_amount = "";
var form_description = "";
var form_name = "";

var handler = StripeCheckout.configure({
    key: stripe_vars.publishable_key,
    image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
    token: function (token, args) {
        // Use the token to create the charge with a server-side script.
        // You can access the token ID with `token.id`
        // args will contain all the billing and shipping info.
        // The billing info is automatically attached to the token and card.
        $("#" + pop_up_button_id).html("Processing Payment...");
        $("#" + pop_up_button_id).removeClass("ak-stripe-pop-up-button").addClass("ak-stripe-pop-up-button-processing");
        var data = {
            'action': 'ak_stripe_submit_payment',
            'post_id': 100,
            'security': ak_stripe_pop_up_nonce,
            'datastring': {stripeToken: token.id, customername: args.billing_name, emailadress: token.email, amount: form_payment_amount, companyname: form_name, productdescription: form_description}
        };
        $.post(stripe_vars.ajaxurl, data, function (response) {

            if (response[0] === "success") {
                window.location = window.location + "?_wpnonce=" + ak_stripe_pop_up_nonce + "&ak-stripe-payment-status=success&chargeid=" + response[1] + "&form-id=" + page_id;
            }
            else {
                window.location = window.location + "?_wpnonce=" + ak_stripe_pop_up_nonce + "&ak-stripe-payment-status=failed&type=" + response[1] + "&code=" + response[2] + "&chargeid=" + response[3];
            }
        });
        return false;

    }
});

//callback function of stripe createtoken being used for legacy stripe form
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
    jQuery.validator.addMethod("dollarsscents", function (value, element) {
        return this.optional(element) || /^\d{0,4}(\.\d{0,2})?$/i.test(value);
    }, "You must include two decimal places");


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
    $("[id^=AKStripeButton").click(function (e) {
        pop_up_button_id = ($(this).attr('id'));
        var temp_arr = pop_up_button_id.split('-');
        form_id = 'ak_stripe_checkout_form_' + temp_arr[1];
        $("#" + form_id).validate({
            rules: {
                "ak-stripe-custom-amount": {
                    required: true,
                    dollarsscents: true,
                    min: .50
                }
            },
            submitHandler: function (form) { // for demo
                ak_stripe_checkout_submit(pop_up_button_id, e);
            }
        });

    });

    function ak_stripe_checkout_submit(id, e) {
        pop_up_button_id = id;
        var temp_arr = pop_up_button_id.split('-');
        form_id = 'ak_stripe_checkout_form_' + temp_arr[1];
        page_id = temp_arr[1];
        if ($("form#" + form_id + ' input[name=ak-stripe-custom-amount]').hasClass("ak-stripe-custom-amount")) { //custom amount present in form
            form_payment_amount = $("form#" + form_id + ' input[name=ak-stripe-custom-amount]').val() * 100;
            form_payment_amount = Math.round(form_payment_amount);
        }
        else {
            form_payment_amount = $("form#" + form_id + ' input[name=ak-stripe-checkout-amount]').val() * 100;
            form_payment_amount = Math.round(form_payment_amount);
        }
        form_name = $("form#" + form_id + " input[name=ak-stripe-checkout-name]").val();
        form_description = $("form#" + form_id + " input[name=ak-stripe-checkout-description]").val();
        var form_payment_button_text = $("form#" + form_id + ' input[name=ak-stripe-checkout-payment-button]').val();
        var form_include_amount = $("form#" + form_id + ' input[name=ak-stripe-checkout-include-amount]').val();
        var form_include_billing_address = $("form#" + form_id + ' input[name=ak-stripe-checkout-include-billing-address]').val();
        var form_include_shipping_address = $("form#" + form_id + ' input[name=ak-stripe-checkout-include-shipping-address]').val();
        ak_stripe_pop_up_nonce = $("form#" + form_id + ' input[name=ak_stripe_ajax_nonce]').val();

        if (form_include_amount) {
            form_include_amount = new Boolean(true);
            ;
        } else {
            form_include_amount = new Boolean(false);
            ;
        }

        if (form_include_billing_address) {
            form_include_billing_address = new Boolean(true);
            ;
        } else {
            form_include_billing_address = new Boolean(false);
            ;
        }

        if (form_include_shipping_address) {
            form_include_shipping_address = new Boolean(true);
            ;
        } else {
            form_include_shipping_address = new Boolean(false);
            ;
        }

        if (form_include_amount) {
            form_payment_button_text = form_payment_button_text + ' {{amount}}';
            ;
        }
        // Open Checkout with further options
        handler.open({
            name: form_name,
            description: form_description,
            amount: form_payment_amount,
            //shippingAddress: form_include_address,
            shippingAddress: form_include_shipping_address,
            billingAddress: form_include_billing_address,
            panelLabel: form_payment_button_text
        });
        e.preventDefault();
    }
    ;


    function ak_stripe_checkout_convert_payment_amount(self) {
        var amount = $(self).val();
        amount = (amount / 100).toFixed(2);
        $(self).val(amount);
    }



});
