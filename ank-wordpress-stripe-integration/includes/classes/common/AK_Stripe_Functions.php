<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ak_stripe_checkout_form_fields() {
    // Field Array
    global $post;
    $prefix = 'ak-stripe-';
    $custom_meta_fields = array(
        array(
            'label' => 'Form name:',
            'desc' => 'The name of your company or website.',
            'id' => $prefix . 'form-name',
            'type' => 'text',
            'column' => 'form_name',
            'default' => ''
        ),
        array(
            'label' => 'Form Description:',
            'desc' => 'A description of the product or service being sold.',
            'id' => $prefix . 'form-description',
            'type' => 'text',
            'column' => 'form_description',
            'default' => ''
        ),        
        array(
            'label' => 'Form Shortcode:',
            'desc' => 'Copy this code and paste it into your post or page.',
            'id' => $prefix . 'form-shortcode',
            'type' => 'text-readonly',
            'column' => 'form_shortcode',
            'default' => ''
        ),
        array(
            'label' => 'Payment Type:',
            'desc' => 'Choose to set a specific amount for this form, or allow customers to set custom amounts.',
            'id' => $prefix . 'payment-type',
            'type' => 'select',
            'options' => array(
                'one' => array(
                    'label' => 'Set Amount',
                    'value' => 'set_amount'
                ),
                'two' => array(
                    'label' => 'Custom Amount',
                    'value' => 'custom_amount'
                ),
            ),
            'column' => 'payment_type'
        ),
        array(
            'label' => 'Payment Amount:',
            'desc' => 'The amount this form will charge your customer, in cents. i.e. for $10.00 enter 1000. Default is 10.00 and minimum is $0.50',
            'id' => $prefix . 'payment-amount',
            'type' => 'text',
            'column' => 'payment_amount',
            'default' => '1000'
        ),
        array(
            'label' => 'Payment Button Text:',
            'desc' => 'The text on the payment button.Default is "Make your payment"',
            'id' => $prefix . 'payment-button',
            'type' => 'text',
            'column' => '',
            'default' => 'Make your payment'
        ),
        array(
            'label' => 'Include Amount on Button?',
            'desc' => 'For set amount forms, choose to show/hide the amount on the payment button',
            'id' => $prefix . 'include-amount',
            'type' => 'checkbox',
            'column' => ''
        ),
        array(
            'label' => 'Include Billing Address Field?',
            'desc' => 'Should this payment form also ask for the customers billing address?',
            'id' => $prefix . 'include-billing-address',
            'type' => 'checkbox',
            'column' => ''
        ),
        array(
            'label' => 'Include Shipping Address Field?',
            'desc' => 'Should this payment form also ask for the customers shipping address?',
            'id' => $prefix . 'include-shipping-address',
            'type' => 'checkbox',
            'column' => ''
        )
    );
    return $custom_meta_fields;
}

//Debugging function
if(!function_exists('ak_stripe_log')){
  function ak_stripe_log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}