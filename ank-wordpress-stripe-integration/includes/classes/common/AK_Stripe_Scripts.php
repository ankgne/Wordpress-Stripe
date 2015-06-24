<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Scripts
 *
 * @author com
 */
class AK_Stripe_Scripts {

    function ak_stripe_load_payment_processing_scripts() {
        $publishable = AK_STRIPE_PUBLISHABLE_KEY;
        wp_enqueue_script('jquery');
        wp_enqueue_script('ak-stripe', 'https://js.stripe.com/v2/');
        wp_enqueue_script('ak-stripe-processing', STRIPE_BASE_URL . 'includes/js/AK-stripe-processing.js');
        wp_enqueue_script('ak-stripe-payment', STRIPE_BASE_URL . 'includes/js/jquery.payment.js', array('jquery'));
        wp_localize_script('ak-stripe-processing', 'stripe_vars', array(
            'publishable_key' => $publishable,
            'ajaxurl' => admin_url('admin-ajax.php'),
                )
        );
        wp_enqueue_style('ak-stripe-payment-form', STRIPE_BASE_URL . 'includes/css/ak-stripe-payment-form.css');
        wp_register_style('ak-stripe-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', false, NULL, 'all');
        wp_enqueue_style('ak-stripe-bootstrap-css');
    }

    function ak_stripe_load_thickbox_popup_script() {
        wp_enqueue_script('ak-stripe-thickbox-js', STRIPE_BASE_URL . 'includes/js/thickbox.js'); //customized tb_showIframe
        wp_localize_script('ak-stripe-thickbox-js', 'thickboxL10n', array(
            'next' => __('Next &gt;'),
            'prev' => __('&lt; Prev'),
            'image' => __('Image'),
            'of' => __('of'),
            'close' => __('Close'),
            'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
            'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
        ));
        wp_enqueue_style('thickbox'); // using the default CSS of wordpress
    }
    
    function ak_stripe_load_admin_script(){
        wp_enqueue_script('ak-stripe-admin-setting-script', STRIPE_BASE_URL . 'includes/js/ak-stripe-admin-setting.js', array('jquery'));
    }

}
