jQuery(document).ready(function($) 
{
    $("#stripe-create-plan-success").hide();//hide by default 
    $("#stripe-create-plan-failure").hide();//hide by default 
    
    /* Stripe create plan function */
    jQuery(".ak-stripe-create-plan").click( function() {
         $("#stripe-create-plan-success").hide();//hide by default 
         $("#stripe-create-plan-failure").hide();//hide by default 
         $('#stripe-create-plan-success').html('<img id="loader-img" alt="" src="http://adrian-design.com/images/loading.gif" width="100" height="100" align="center" />');
        create_form_elements = $("#ak-stripe-create-plan-form").serialize();
    var data = {
        'action': 'create_stripe_plan',
	'post_id': 1,
        'datastring':create_form_elements
    };

    $.post(ajaxurl, data, function(response) {
        if (response=="success"){
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
    
    function fetch_stripe_plans (){
        //alert("hello");
        $(".stripe-retrive-plan-table").hide();
        var data = {
        'action': 'get_stripe_plan',
	'post_id': 1,
        'datastring':create_form_elements
        };

          $.post(ajaxurl, data, function(response) {
            $(".stripe-retrive-plan-table").html(response);  
            $(".stripe-retrive-plan-table").show();
        }); 
    }
    
});


