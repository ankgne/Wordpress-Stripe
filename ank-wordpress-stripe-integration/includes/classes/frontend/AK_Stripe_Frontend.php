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
require_once(STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Scripts.php');
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Payment_Form.php');
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php');

class AK_Stripe_Frontend {

    private $ak_stripe_process_payment = '';

//put your code here
    function __construct() {
            $ak_stripe_payment_form = new AK_Stripe_Payment_Form();
            $this->ak_stripe_process_payment = new AK_stripe_process_payment();
        }
    }

add_action( 'init', 'ak_stripe_control_init', 11 );
function ak_stripe_control_init(){
    $ak_Stripe_Frontend = new AK_Stripe_Frontend();
}
