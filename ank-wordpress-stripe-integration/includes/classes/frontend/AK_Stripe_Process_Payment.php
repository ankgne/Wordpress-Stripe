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

    function AK_stripe_listener() { ///create listenser to process payments
        //add_action('init', array($this, 'AK_stripe_process_payment'));
    }

    function setChargeObject($charge) {
        self::$charge_object = $charge;
        //print_r ($this->charge_object);
    }

    function getChargeObject() {

        //print_r ($this->charge_object);
        return self::$charge_object;
    }

    function ajax_ak_stripe_submit_payment() {
        if (!empty($_POST['post_id'])) {
        parse_str($_POST['ak_payment_form_elements'],$payment_form_data); // parse query string
        //$form_data = array($customername, $emailadress, $action, $redirect, $stripenounce, $stripe_token);
        $customer_detail = array($payment_form_data['customername'], $payment_form_data['emailadress']);
        $ak_stripe_payment_function = new AK_Stripe_Wrapper();
        $ak_stripe_payment_function->setToken($payment_form_data['stripeToken']);
        $ak_stripe_create_charge_return=$ak_stripe_payment_function->AK_CreateCharge($customer_detail);
        echo $ak_stripe_create_charge_return;
        }
        die();
    }

//    function ajax_ak_stripe_submit_payment() {
//    function AK_stripe_process_payment() {
//        if (isset($_POST['action']) && $_POST['action'] == 'stripe' && wp_verify_nonce($_POST['stripe_nonce'], 'stripe-nonce')) {
//            $customer_detail = array($_POST['customername'], $_POST['emailadress']);
//            $ak_stripe_payment_function = new AK_Stripe_Wrapper();
//            $ak_stripe_payment_function->setToken($_POST['stripeToken']);
//            $ak_stripe_payment_function->AK_CreateCharge($customer_detail);
//        }
//    }
//
}
