<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Payment_Form
 *
 * @author com
 */
class AK_Stripe_Payment_Form {

    //put your code here

    function __construct() {
        add_shortcode('payment_form', array($this, 'AK_stripe_payment_form'));
        $ak_stripe_process_payment = new AK_Stripe_Process_Payment();
        $ak_stripe_process_payment->AK_stripe_listener();
    }

    function AK_stripe_payment_form() {
        if (isset($_GET['payment']) && $_GET['payment'] == 'paid') {
            if (isset($_GET['ID'])) {
                $ak_stripe_payment_function = new AK_Stripe_Payment_Functions();
                $charge_object = $ak_stripe_payment_function->AK_RetrieveCharge($_GET['ID']);
                $successMessage = sprintf(__('Thank you for your payment. Your transaction reference number is %s.', 'pippin_stripe'), $charge_object['balance_transaction']);
                echo '<p class="success">' . $successMessage . '</p>';
            } else { // this condition will never occur
                echo '<p class="success">' . __('Thank you for your payment.', 'pippin_stripe') . '</p>';
            }
        } else {
            ?>
            <h2><?php _e('Submit a payment of $10', 'ak_stripe'); ?></h2>
            <form action="" method="POST" id="stripe-payment-form">
                <div class="form-row">
                    <label><?php _e('Card Number', 'ak_stripe'); ?></label>
                    <input type="text" size="20" autocomplete="off" class="card-number"/>
                </div>
                <div class="form-row">
                    <label><?php _e('CVC', 'ak_stripe'); ?></label>
                    <input type="text" size="4" autocomplete="off" class="card-cvc"/>
                </div>
                <div class="form-row">
                    <label><?php _e('Expiration (MM/YYYY)', 'ak_stripe'); ?></label>
                    <input type="text" size="2" class="card-expiry-month"/>
                    <span> / </span>
                    <input type="text" size="4" class="card-expiry-year"/>
                </div>
                <div class="form-row">
                    <label><?php _e('Payment Type:', 'ak_stripe'); ?></label>
                    <input type="radio" name="paymenttype" id="one_time_stipe" value="one_time_stipe"/><span><?php _e('One time payment', 'ak_stripe'); ?></span>
                    <input type="radio" name="paymenttype" id="recurring_stripe" value="recurring_stripe"/><span><?php _e('Recurring payment', 'ak_stripe'); ?></span>          
                </div>
                <input type="hidden" name="action" value="stripe"/>
                <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
                <input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
                <button type="submit" id="stripe-submit"><?php _e('Submit Payment', 'pippin_stripe'); ?></button>
            </form>
            <div class="payment-errors"></div>
            <?php
        }
    }

}
