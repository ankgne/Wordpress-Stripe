jQuery(document).ready(function($) 
{
    $("#stripe-create-plan-success").hide();//hide by default 
    $("#stripe-create-plan-failure").hide();//hide by default 
    
    $(".ak-stripe-delete-plan-button").hide();//hide delete button by default 
    $(".ak-stripe-delete-plans-button").hide();//hide delete button by default 
    
    
    /* when the checkbox on the Manage stripe page is checked*/
    var countChecked = function() {
        var n = $( "input:checked" ).length;
        if (n==1){ 
            $(".ak-stripe-delete-plan-button").show();
            $(".ak-stripe-delete-plans-button").hide();
            }
        else if (n>1){
            $(".ak-stripe-delete-plans-button").show();
            $(".ak-stripe-delete-plan-button").hide();
        }
        else {
            $(".ak-stripe-delete-plan-button").hide();
            $(".ak-stripe-delete-plans-button").hide();
        }
    };
    $( ".stripe-retrive-plan-table input[type=checkbox]" ).on( "click", countChecked );
    /*********************************************************************************/
 
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
    
    
     /* Stripe create plan function */
    jQuery(".ak-stripe-delete-plan-button , .ak-stripe-delete-plans-button").click( function() {
        
        var plan_ids = new Array();
        $( "input[name='check_list[]']:checked" ).each( function() {
                plan_ids.push( $( this ).val() );
        } );
        //delete_form_elements = $("#ak-stripe-delete-plan-form").serialize();
        alert(plan_ids);
       
        var data = {
            'action': 'delete_stripe_plan',
            'post_id': 1,
            'datastring':plan_ids
        };

        $.post(ajaxurl, data, function(response) {
            alert (response);
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


