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

    function ak_stripe_load_generic_scripts() {
        wp_register_style('ak-stripe-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', false, NULL, 'all');
        wp_enqueue_style('ak-stripe-bootstrap-css');
        wp_enqueue_style('ak-stripe-payment-form', STRIPE_BASE_URL . 'includes/css/ak-stripe-payment-form.css');
    }

    function ak_stripe_load_payment_processing_scripts($form_type, $form_id) {
        $publishable = AK_STRIPE_PUBLISHABLE_KEY;
        wp_enqueue_script('jquery');
        wp_enqueue_script('ak-stripe', 'https://js.stripe.com/v2/');
        wp_enqueue_script('ak-stripe-checkout', 'https://checkout.stripe.com/checkout.js');
        wp_enqueue_script('ak-stripe-processing', STRIPE_BASE_URL . 'includes/js/AK-stripe-processing.js', array('ak-stripe-checkout'));
        wp_enqueue_script('ak-stripe-payment', STRIPE_BASE_URL . 'includes/js/jquery.payment.js', array('jquery'));

        if ("pop-up" === $form_type) { // embedded pop-up
            wp_enqueue_script('ak-stripe-pop-validate', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js', array('jquery'));
            wp_enqueue_script('ak-stripe-pop-validate2', 'http://jqueryvalidation.org/files/dist/additional-methods.min.js', array('jquery'));
            $form_name = get_post_meta($form_id, 'ak-stripe-form-name', true);
            $form_description = get_post_meta($form_id, 'ak-stripe-form-description', true);
            $form_payment_type = get_post_meta($form_id, 'ak-stripe-payment-type', true);
            $form_payment_amount = get_post_meta($form_id, 'ak-stripe-payment-amount', true);
            $form_payment_button = get_post_meta($form_id, 'ak-stripe-payment-button', true);
            $form_include_amount = get_post_meta($form_id, 'ak-stripe-include-amount', true);
            $form_include_billing_address = get_post_meta($form_id, 'ak-stripe-include-billing-address', true);
            $form_include_shipping_address = get_post_meta($form_id, 'ak-stripe-include-shipping-address', true);
            $ak_stripe_ajax_nonce = wp_create_nonce("ak-stripe-pop-up-ajax-nonce");
            if ("set_amount" === $form_payment_type) {
                // do not do anthing
            } else { // custom_amount
                //get value via jquery TODO
            }

            wp_localize_script('ak-stripe-processing', 'stripe_vars', array(
                'publishable_key' => $publishable,
                'form_name' => $form_name,
                'form_description' => $form_description,
                'form_payment_amount' => $form_payment_amount,
                'form_payment_button_text' => $form_payment_button,
                'form_include_amount' => $form_include_amount,
                'form_include_billing_address' => $form_include_billing_address,
                'form_include_shipping_address' => $form_include_shipping_address,
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ak_stripe_ajax_nonce' => $ak_stripe_ajax_nonce,
                'ak_stripe_form_id' => $form_id,
                    )
            );
        } else { // page form
            wp_localize_script('ak-stripe-processing', 'stripe_vars', array(
                'publishable_key' => $publishable,
                'ajaxurl' => admin_url('admin-ajax.php'),
                    )
            );
        }


        wp_enqueue_style('ak-stripe-payment-form', STRIPE_BASE_URL . 'includes/css/ak-stripe-payment-form.css');
        $this->ak_stripe_load_generic_scripts();
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

    function ak_stripe_load_admin_script() {
        wp_enqueue_script('ak-stripe-admin-setting-script', STRIPE_BASE_URL . 'includes/js/ak-stripe-admin-setting.js', array('jquery'));
    }

}
