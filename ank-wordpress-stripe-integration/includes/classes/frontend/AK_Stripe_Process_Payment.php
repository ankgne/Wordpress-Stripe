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

    function ajax_ak_stripe_submit_payment() {
        if (!empty($_POST['post_id'])) {
            parse_str($_POST['datastring'], $payment_form_data); // parse query string
            //$payment_form_data = array($customername, $emailadress, $stripenounce, $stripe_token);
            $payment_detail = array($payment_form_data['customername'], $payment_form_data['emailadress']);
            $ak_stripe_payment_function = new AK_Stripe_Wrapper();
            $ak_stripe_payment_function->setToken($payment_form_data['stripeToken']);
            $ak_stripe_create_charge_return = $ak_stripe_payment_function->AK_CreateCharge($payment_detail);
            if ($ak_stripe_create_charge_return[0] == "success") {
                $payment_detail[] = $ak_stripe_create_charge_return[1]['amount']; // insert amount in array
                $payment_detail[] = $ak_stripe_create_charge_return[1]['currency']; // insert current in array
                $payment_detail[] = $ak_stripe_create_charge_return[1]['balance_transaction']; // insert transaction ID in array
                $payment_detail[] = $ak_stripe_create_charge_return[1]['livemode']; // insert mode of api in array
                $payment_detail[] = $ak_stripe_create_charge_return[1]['created']; // payment time
                $ak_insert_stripe_payment_data = new AK_Stripe_DB_Functions();
                $ak_insert_stripe_payment_data->ak_stripe_insert_payment_data($payment_detail);
                $ak_stripe_create_charge_return=array("success",$ak_stripe_create_charge_return[1]['balance_transaction']);
            }
            //echo $ak_stripe_create_charge_return;
            wp_send_json($ak_stripe_create_charge_return);
        }
        die();
    }

}
