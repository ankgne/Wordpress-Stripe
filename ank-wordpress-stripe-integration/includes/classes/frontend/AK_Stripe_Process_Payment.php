<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Process_Payment
 *
 * @author com
 */
class AK_Stripe_Process_Payment {

    //put your code here
    public static $charge_object;

    function __construct() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
    }

    function setChargeObject($charge) {
        self::$charge_object = $charge;
        //print_r ($this->charge_object);
    }

    function getChargeObject() {
        return self::$charge_object;
    }

    /**
     * 
     */
    function ajax_ak_stripe_submit_payment() {
        ak_stripe_log(__METHOD__ . " Start of ajax_ak_stripe_submit_payment ");
        ak_stripe_log(__METHOD__ . " ====================================== ");
        ak_stripe_log(__METHOD__ . " Post ID is: " . $_POST['post_id']);

        if (!empty($_POST['post_id'])) {
            if ("1" === $_POST['post_id']) { // ajax request came from payment form
                parse_str($_POST['datastring'], $payment_form_data); // parse query strings
            } elseif ("100" === $_POST['post_id']) { // ajax request came from checkout form (embedded form)
                check_ajax_referer('ak-stripe-pop-up-ajax-nonce', 'security'); //validate nonce
                $payment_form_data = $_POST['datastring'];
                ak_stripe_log($_POST['datastring']);
            }

            $payment_detail = array(isset($payment_form_data['customername']) ? $payment_form_data['customername'] : '', $payment_form_data['emailadress'], $payment_form_data['amount'] ? $payment_form_data['amount'] : '');
            
            $ak_stripe_payment_function = new AK_Stripe_Wrapper();
            
            $ak_stripe_payment_function->setToken($payment_form_data['stripeToken']);
            
            $ak_stripe_create_charge_return = $ak_stripe_payment_function->AK_CreateCharge($payment_detail);
            
            $ak_insert_stripe_payment_data = new AK_Stripe_DB_Functions();

            if ($ak_stripe_create_charge_return[0] == "success") {
                
                ak_stripe_log(__METHOD__ . " Success Leg ");
                
                $this->setChargeObject($ak_stripe_create_charge_return[1]['id']);
                
                $payment_data = $this->ak_stripe_payment_detail($ak_stripe_create_charge_return[1], $payment_form_data);
                
                $payment_data[] = "success";
                
                $ak_insert_stripe_payment_data->ak_stripe_insert_payment_data(array_merge($payment_detail, $payment_data));
                
                $ak_stripe_create_charge_return = array("success", $this->getChargeObject());                
                
            } else {
                ak_stripe_log(__METHOD__ . " Failure Leg ");
                
                if (isset($ak_stripe_create_charge_return[1]['charge'])) { // charge ID is not received in case of invalid_request_error (for example when the amount is less than .50 cents)
                    
                    ak_stripe_log(__METHOD__ . " charge ID is set ");
                    
                    $this->setChargeObject($ak_stripe_create_charge_return[1]['charge']);
                    
                    $ak_stripe_retrieve_charge_return = $ak_stripe_payment_function->AK_RetrieveCharge($this->getChargeObject());
                    $payment_data = $this->ak_stripe_payment_detail($ak_stripe_retrieve_charge_return, $payment_form_data);
                    
                    $payment_data[] = "failed";
                    
                    $ak_insert_stripe_payment_data->ak_stripe_insert_payment_data(array_merge($payment_detail, $payment_data));
                    
                    $ak_stripe_create_charge_return = array("fail", $ak_stripe_create_charge_return[1]['type'], $ak_stripe_create_charge_return[1]['code'], $this->getChargeObject());
                }
                else{
                    ak_stripe_log(__METHOD__ . " charge ID is not set ");
                    
                    $ak_stripe_create_charge_return[1]['code']=$ak_stripe_create_charge_return[1]['message']; //no code is retuned in case of invalid_request_error so send a custom code
                    
                    ak_stripe_log(__METHOD__ . " Code is " . $ak_stripe_create_charge_return[1]['code']);
                    
                    $ak_stripe_create_charge_return = array("fail", $ak_stripe_create_charge_return[1]['type'], $ak_stripe_create_charge_return[1]['code'], null);
                }        
            }
            ak_stripe_log($ak_stripe_create_charge_return);
            ak_stripe_log(__METHOD__ . " End of ajax_ak_stripe_submit_payment ");
            ak_stripe_log(__METHOD__ . " ==================================== ");
            wp_send_json($ak_stripe_create_charge_return);
        }
        die();
    }

    function ak_stripe_payment_detail($ak_stripe_charge_return, $payment_form_data) {
        $payment_detail[] = $ak_stripe_charge_return['currency']; // insert current in array
        $payment_detail[] = $this->getChargeObject(); // insert charge ID in array
        $payment_detail[] = $ak_stripe_charge_return['livemode'] ? "Live" : "Test"; // insert mode of api in array
        $payment_detail[] = $ak_stripe_charge_return['created']; // payment time
        $payment_detail[] = $payment_form_data['companyname'];
        $payment_detail[] = $payment_form_data['productdescription'];
        return $payment_detail;
    }

}
