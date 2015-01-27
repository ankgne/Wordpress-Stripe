<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-frontend
 *
 * @author com
 */
include(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Scripts.php');
include(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php');
include(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Payment_Form.php');


class AK_Stripe_Frontend {   
//put your code here
    function __construct() {
        $ak_stripe_scripts = new AK_Stripe_Scripts();
        global $ak_stripe_payment_form;
        $ak_stripe_payment_form = new AK_Stripe_Payment_Form();
        //global $ak_stripe_process_payment;

    }


}
$ak_Stripe_Frontend = new AK_Stripe_Frontend();