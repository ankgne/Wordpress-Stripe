jQuery(document).ready(function ($)
{
    $("#stripe-create-plan-success").hide();//hide by default 
    $("#stripe-create-plan-failure").hide();//hide by default  
    $("#stripe-retrieve-plan-success").hide();//hide by default  
    $("#stripe-retrieve-plan-failure").hide();//hide by default  
    $("#loaderImg_create_plan").hide();//hide by default 
    $("#loaderImg_retrieve_plan").hide();//hide by default 

    /* when the checkbox on the Manage stripe page is checked*/
    /* used delegate to handle jquery events in ajax content*/
    $('#ak-stripe-delete-plan-form').on('click', 'input[type=checkbox]', function () {
        var n = $("input:checked").length;
        if (n == 1) {
            $(".ak-stripe-delete-plan-button").show();
            $(".ak-stripe-delete-plans-button").hide();
        }
        else if (n > 1) {
            $(".ak-stripe-delete-plans-button").show();
            $(".ak-stripe-delete-plan-button").hide();
        }
        else {
            $(".ak-stripe-delete-plan-button").hide();
            $(".ak-stripe-delete-plans-button").hide();
        }
    });
    /*********************************************************************************/

    /* Stripe create plan function */
    jQuery(".ak-stripe-create-plan").click(function () {
        $("#stripe-create-plan-success").hide();//hide by default 
        $("#stripe-create-plan-failure").hide();//hide by default 
        $('#stripe-create-plan-success').html('<img id="loader-img" alt="" src="http://adrian-design.com/images/loading.gif" width="100" height="100" align="center" />');
        create_form_elements = $("#ak-stripe-create-plan-form").serialize();

        var data = {
            'action': 'create_stripe_plan',
            'post_id': 1,
            'datastring': create_form_elements
        };

        $("#loaderImg_create_plan").show();
        $(".stripe-create-plan-table").hide();

        $.post(ajaxurl, data, function (response) {
            $("#loaderImg_create_plan").hide();
            $(".stripe-create-plan-table").show();
            $('#ak-stripe-create-plan-form')[0].reset(); /*clear all the fields of create plan form*/
            if (response == "success") {
                $("#stripe-create-plan-success").html("<p><strong></strong></p>New Plan successfully created<p><strong></strong></p>");
                $("#stripe-create-plan-success").show();
                fetch_stripe_plans();
            }
            else {
                $("#stripe-create-plan-failure").html('<p><strong></strong></p>' + response + '<p><strong></strong></p>');
                $("#stripe-create-plan-failure").show();
            }


        });

        return false;
    });


    /* Stripe delete plan function */
    $('#ak-stripe-delete-plan-form').on('click', '.ak-stripe-delete-plan-button , .ak-stripe-delete-plans-button', function () {
        $("#stripe-retrieve-plan-success").hide();//hide when delete button is clicked
        $("#stripe-retrieve-plan-failure").hide();//hide when delete button is clicked
        var plan_ids = new Array();
        $("input[name='check_list[]']:checked").each(function () {
            plan_ids.push($(this).val());
        });
        //delete_form_elements = $("#ak-stripe-delete-plan-form").serialize();
        $("#loaderImg_retrieve_plan").show();
        $(".stripe-retrive-plan-table").hide();
        var data = {
            'action': 'delete_stripe_plan',
            'post_id': 1,
            'datastring': plan_ids
        };
        $.post(ajaxurl, data, function (response) {
            if ("success" === response) {
                var success_message = "";
                $.each(plan_ids, function (index, value) {
                    success_message = success_message + "<p><strong></strong></p>Successfully deleted plan ID - " + value + "<p><strong></strong></p>"
                });
                $("#stripe-retrieve-plan-success").html(success_message);
                $("#stripe-retrieve-plan-success").show();
                $("#loaderImg_retrieve_plan").hide();
                $(".stripe-retrive-plan-table").show();
                fetch_stripe_plans();
            }
            else {
                var error_message = response;
                $.each(plan_ids, function (index, value) {
                    error_message = error_message + "<p><strong></strong></p>Failed to delete plan ID - " + value + "<p><strong></strong></p>"

                });
                $("#stripe-retrieve-plan-failure").html(error_message);
                // alert( error_message);
                $("#stripe-retrieve-plan-failure").show();
                $("#loaderImg_retrieve_plan").hide();
                $(".stripe-retrive-plan-table").show();
            }


        });

        return false;
    });

    function fetch_stripe_plans() {
        $(".stripe-retrive-plan-table").hide();
        var data = {
            'action': 'get_stripe_plan'
        };
        $.post(ajaxurl, data, function (response) {
            $("#ak-stripe-delete-plan-form").html(response);
            //$("#ak-stripe-delete-plan-form").show();
            $("#loaderImg_retrieve_plan").hide();
            $("#stripe-create-plan-success").hide();//hide by default 
            $("#stripe-create-plan-failure").hide();//hide by default  
            $("#stripe-retrieve-plan-success").hide();//hide by default  
            $("#stripe-retrieve-plan-failure").hide();//hide by default  
        });
        return false;
    }


    jQuery("#ak-stripe-submit-payment_2").click(function () {
        alert("hell0");
        $("#ak-stripe-process-payment-success").hide();//hide by default 
        $("#ak-stripe-process-payment-failure").hide();//hide by default 
        $('#ak-stripe-process-payment-success').html('<img id="loader-img" alt="" src="http://adrian-design.com/images/loading.gif" width="100" height="100" align="center" />');
        create_form_elements = $("#ak-stripe-payment-form").serialize();
        alert(create_form_elements);

        var data = {
            'action': 'ak_stripe_submit_payment',
            'post_id': 1,
            'datastring': create_form_elements
        };

        $("#loaderImg_create_plan").show();

        $.post(ajaxurl, data, function (response) {
            $("#loaderImg_create_plan").hide();
            $('#ak-stripe-create-plan-form')[0].reset(); /*clear all the fields of create plan form*/
            if (response == "success") {
                $("#stripe-create-plan-success").html("<p><strong></strong></p>New Plan successfully created<p><strong></strong></p>");
                $("#stripe-create-plan-success").show();
                fetch_stripe_plans();
            }
            else {
                $("#stripe-create-plan-failure").html('<p><strong></strong></p>' + response + '<p><strong></strong></p>');
                $("#stripe-create-plan-failure").show();
            }
        });

        return false;
    });

    $('input#ak-stripe-form-name').change(function () {
        ak_stripe_checkout_add_short_code();
    });
    
    $('input#ak-stripe-payment-amount').change(function () {
        ak_stripe_checkout_convert_payment_amount();
    });

    function ak_stripe_checkout_add_short_code() {
        var title = $('input#ak-stripe-form-name').val();

        if (title){
            title = title.replace(/["'\[\]]/g, ''); ///prevents [] from name
        }

        $('input#ak-stripe-form-name').val(title);
        var postId = $('input#ak_stripe_post_ID').val();
        var tag = '[stripe-checkout-form id=' + postId + ' title=' + title + ']';
        $('input#ak-stripe-form-shortcode').val(tag);

    }
    
        function ak_stripe_checkout_convert_payment_amount() {
        var amount = $('input#ak-stripe-payment-amount').val();
        amount=(amount/100).toFixed(2);
        $('input#ak-stripe-payment-amount').val(amount);
    }
    
    
        if( 'custom_amount'===$('select#ak-stripe-payment-type').val()){
        $("input#ak-stripe-payment-amount").val('');//set blank
       $("input#ak-stripe-payment-amount").prop("readonly", true);
       $("input#ak-stripe-include-amount").prop("readonly", true);
        $("input#ak-stripe-include-amount").attr('disabled', 'disabled');
    }
    
    $('select#ak-stripe-payment-type').change(function() {
    if( 'custom_amount'===$('select#ak-stripe-payment-type').val()){
        $("input#ak-stripe-payment-amount").val('');//set blank
         $("input#ak-stripe-include-amount").val('');
       $("input#ak-stripe-payment-amount").prop("readonly", true);
       $("input#ak-stripe-include-amount").prop("readonly", true);
         $("input#ak-stripe-include-amount").attr('disabled', 'disabled');
       
    }
    else{
        $("input#ak-stripe-payment-amount").prop("readonly", false);
        $("input#ak-stripe-include-amount").prop("readonly", false);
         $("input#ak-stripe-include-amount").removeAttr("disabled");
    }

});


});

