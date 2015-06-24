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
require_once(STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Wrapper.php');
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Payment_Form.php');
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php');

class AK_Stripe_Frontend {

    private $ak_stripe_process_payment = '';

//put your code here
    function __construct() {
        $ak_stripe_scripts = new AK_Stripe_Scripts();
        $ak_stripe_scripts->ak_stripe_load_payment_processing_scripts();
        $ak_stripe_payment_form = new AK_Stripe_Payment_Form();
        $this->ak_stripe_process_payment = new AK_stripe_process_payment();
        add_filter('query_vars', array($this, 'ak_stripe_popup_add_var'));
        add_action('template_redirect', array($this, 'ak_stripe_popup_load_iframe'));
    }

    function ak_stripe_popup_add_var($vars) {
        $vars[] = 'ak-stripe-pop-up';
        return $vars;
    }

    function ak_stripe_popup_load_iframe() {
        if (get_query_var('ak-stripe-pop-up')) {
            require_once( STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Pop_Up.php' );
            exit;
        }
    }

}

$ak_Stripe_Frontend = new AK_Stripe_Frontend();
