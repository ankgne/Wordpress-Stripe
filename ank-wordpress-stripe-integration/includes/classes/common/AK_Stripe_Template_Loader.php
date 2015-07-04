<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Template_Loader
 *
 * @author com
 */
class AK_Stripe_Template_Loader {

    public static function init() {

        add_filter('template_include', array(__CLASS__, 'template_loader'));
    }

    public static function template_loader($template) {
        if (isset($_GET['_wpnonce']) && isset($_GET['ak-stripe-payment-status'])) { // request coming from stripe checkout page            
            $nonce = $_GET['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'ak-stripe-pop-up-ajax-nonce'))
                die("Security check");
            $ak_stripe_scripts = new AK_Stripe_Scripts();
            $ak_stripe_scripts->ak_stripe_load_generic_scripts();
            if ('success' === $_GET['ak-stripe-payment-status']) {
                $template_file_path=ak_stripe_locate_template("AK_Stripe_Thankyou.php");
                require_once( $template_file_path );
            } else {
                require_once( STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Failed.php' );
            }
        } else {
            return $template;
        }
    }

}

AK_Stripe_Template_Loader::init();
