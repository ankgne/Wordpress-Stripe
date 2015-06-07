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
                $ak_stripe_payment_function = new AK_Stripe_Wrapper();
                $charge_object = $ak_stripe_payment_function->AK_RetrieveCharge($_GET['ID']);
                $successMessage = sprintf(__('Thank you for your payment. Your transaction reference number is <span style=" font-weight: bold; "> %s </span>.', 'pippin_stripe'), $charge_object['balance_transaction']);
                echo '<p class="alert-success">' . $successMessage . '</p>';
            } else { // this condition will never occur
                echo '<p class="success">' . __('Thank you for your payment.', 'pippin_stripe') . '</p>';
            }
        } 
        else if(isset($_GET['payment']) && $_GET['payment'] == 'failed'){
            $successMessage = sprintf(__('Opps!!! %s .', 'pippin_stripe'), $_GET['error_message']);
                echo '<p class="alert-error">' . $successMessage . '</p>';
        }
        else {
            ?>
            <h2><?php _e('Submit a payment of $10', 'ak_stripe'); ?></h2>
            <div id="ak-stripe-process-payment-success" class="updated"></div> <!-- for success message-->
            <div id="ak-stripe-process-payment-failure" class="error" ></div> <!-- for failure message-->            

            <form action="" method="POST" id="ak-stripe-payment-form">
                <p class="form-group">
                    <?php _e("Card Holder's name", 'ak_stripe'); ?>
                    <br>
                    <input name="customername" type="text" size="40" autocomplete="on" class="card-name form-control"/>
                </p>
                <p class="form-group">
                    <?php _e("Email Address", 'ak_stripe'); ?>
                    <br>
                    <input name="emailadress" type="text" size="40" autocomplete="on" class="card-email form-control"/>
                </p>
                <p class="form-group">
                    <?php _e('Card Number', 'ak_stripe'); ?>
                    <br>
                    <input type="text" size="40" autocomplete="on" class="card-number cc-number form-control"/>
                </p>
                <p class="form-group">
                    <?php _e('Expiration (MM/YYYY)', 'ak_stripe'); ?>
                    <br>
                    <input id="cc-exp" type="tel" class="input-lg form-control cc-exp" placeholder="•• / ••">
                </p>
                <p class="form-group">
                    <?php _e('CVC', 'ak_stripe'); ?>
                    <br>
                    <input type="text" size="10" autocomplete="off" class="card-cvc cc-cvc form-control"/>
                </p>

                <p>
                    <?php _e('Payment Type:', 'ak_stripe'); ?>
                    <br>
                    <input type="radio" name="paymenttype" id="one_time_stipe" value="one_time_stipe"/><span><?php _e('One time payment', 'ak_stripe'); ?></span>
                    <br>
                    <input type="radio" name="paymenttype" id="recurring_stripe" value="recurring_stripe"/><span><?php _e('Recurring payment', 'ak_stripe'); ?></span>          
                </p>
                <input type="hidden" name="action" value="stripe"/>
                <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
                <input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
                <button type="submit" id="ak-stripe-submit-payment"><?php _e('Submit Payment', 'pippin_stripe'); ?></button>
            </form>
            <div class="payment-errors"></div>
            <?php
        }
    }
}
