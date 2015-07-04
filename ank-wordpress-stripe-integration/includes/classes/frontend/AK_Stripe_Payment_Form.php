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

    private $ak_stripe_scripts = '';

    //put your code here
    function __construct() {
        //handle shortcode
        $this->ak_stripe_scripts = new AK_Stripe_Scripts();
        add_shortcode('ak_stripe_payment_form', array($this, 'AK_stripe_payment_shortcode'));
    }

    public function AK_stripe_payment_shortcode($atts) {
        $atts = shortcode_atts(array('type' => '', 'id' => '', 'title' => ''), $atts, 'ak_stripe_payment_form');
        if ("page" === $atts['type']) {
            $this->AK_stripe_payment_form_page();
        } else if ("pop-up" === $atts['type']) {
            $this->AK_stripe_payment_form_popup_iframe();
        } else {
            $this->AK_stripe_payment_form_popup($atts['id']);
        }
    }

    //Function for displaying payment form as pop-up in iFrame
    public function AK_stripe_payment_form_popup_iframe() {
        //add_thickbox();
        $this->ak_stripe_scripts->ak_stripe_load_thickbox_popup_script();
        $ak_stripe_popup_url = add_query_arg(array('ak-stripe-pop-up' => 'true', 'TB_iframe' => 'true', 'height' => 480, 'width' => 320), home_url());
        //echo '<a class="thickbox btn-info ak-stripe-pop-up-button" id="ak-stripe-pop-up-button" title="' . esc_attr($options['stripe_header']) . '" href="' . esc_url($ak_stripe_popup_url) . '"><span>test' . esc_html($options['stripe_header']) . '</span></a>' . $payments;
        echo '<a class="thickbox btn-info ak-stripe-pop-up-button" id="ak-stripe-pop-up-button" href="' . esc_url($ak_stripe_popup_url) . '"><span>test</span></a>';
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

    //Function for displaying the payment form on page
    public function AK_stripe_payment_form_page() {
        $this->ak_stripe_scripts->ak_stripe_load_payment_processing_scripts("page-form", "");
        ?>

        <div class='form-row'>
            <div class='col-md-12'>
                <div id="ak-stripe-process-payment-success" class="alert-success "></div> <!-- for success message-->
            </div>
        </div>

        <div class="panel panel-default ak-stripe-payment-form">
            <div class="panel-heading">
                <h3 class="panel-title">Panel title</h3>
            </div>
            <div class="panel-body">
                <div class='form-row '>
                    <div class='col-md-12'>
                        <div id="ak-loaderImg_process-payment" style="display: none;"><img src=<?php echo STRIPE_BASE_URL . "/images/ajax-loader.gif" ?> /></div>
                    </div>
                </div>
                <div class='form-row'>
                    <div class='col-md-12'>
                        <div id="ak-stripe-process-payment-failure" class="alert-error" ></div> <!-- for failure message-->  
                    </div>
                </div>
                <form action="" method="POST" id="ak-stripe-payment-form" >
                    <div class="form-group col-xs-12">
                        <label for="emailadress">Email address</label>
                        <input name="emailadress" id="emailadress" type="text" size="40" autocomplete="on" class="card-email form-control"/>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="customername">Name on card</label>
                        <input name="customername" id="customername" type="text" size="40" autocomplete="on" class="card-name form-control"/>
                    </div>

                    <div class="form-group col-xs-12">
                        <label for="cardnumber">Card number</label>
                        <input id="cardnumber" type="text" size="40" autocomplete="on" class="card-number cc-number form-control"/>
                    </div>


                    <!--            <div class="input-group col-xs-12">
                                    <span class="input-group-addon">Amount in $</span>
                                    <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                    <span class="input-group-addon">.00</span>
                                </div>-->

                    <div class='form-row'>
                        <div class='col-xs-6 form-group cvc'>
                            <label for="cc-exp"><?php _e('Expiration', 'ak_stripe'); ?></label>
                            <input id="cc-exp" type="tel" class="form-control cc-exp" placeholder="MM/YYYY">
                        </div>
                        <div class='col-xs-6 form-group expiration'>
                            <label for="cvv"><?php _e('CVC', 'ak_stripe'); ?></label>
                            <input id="cvv" type="text" size="10" autocomplete="off" class="card-cvc cc-cvc form-control"/>
                        </div>
                    </div>

                    <div class='form-row col-xs-12'>
                        <!--                <div class='col-xs-6 form-group'>
                                            <button class='ak-stripe-button submit-button' id="ak-stripe-cancel-payment" type='submit'><?php _e('Cancel', 'ak_stripe'); ?></button>
                                        </div>            -->
                        <!--                <div class='col-xs-6 form-group'>-->
                        <button class='ak-stripe-button  submit-button' id="ak-stripe-submit-payment" type='submit'><?php _e('Submit', 'ak_stripe'); ?></button>
                        <!--                </div>                -->
                    </div>
                    <input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
                </form>
            </div>
        </div>
        <?php
    }

    function AK_stripe_payment_form_popup($form_id) {
        $this->ak_stripe_scripts->ak_stripe_load_payment_processing_scripts("pop-up", $form_id);
        ?>
        <form method="POST" action="" class="sc-checkout-form" id="ak_stripe_checkout_form_<?php echo $form_id; ?>">
            <input type="hidden" name="ak-stripe-checkout-name" value="<?php echo get_post_meta($form_id, 'ak-stripe-form-name', true); ?>">
            <input type="hidden" name="ak-stripe-checkout-description" value="<?php echo get_post_meta($form_id, 'ak-stripe-form-description', true); ?>">
            <input type="hidden" name="ak-stripe-checkout-amount" value="<?php echo get_post_meta($form_id, 'ak-stripe-payment-amount', true); ?>">
            <input type="hidden" name="ak-stripe-checkout-include-amount" value="<?php echo get_post_meta($form_id, 'ak-stripe-include-amount', true); ?>">            
            <input type="hidden" name="ak-stripe-checkout-include-billing-address" value="<?php echo get_post_meta($form_id, 'ak-stripe-include-billing-address', true); ?>">
            <input type="hidden" name="ak-stripe-checkout-include-shipping-address" value="<?php echo get_post_meta($form_id, 'ak-stripe-include-shipping-address', true); ?>">
                <input type="hidden" name="ak-stripe-checkout-payment-button" value="<?php echo get_post_meta($form_id, 'ak-stripe-payment-button', true); ?>"> 
                <input type="hidden" name="ak-stripe-checkout-form" value="<?php echo $form_id; ?>"> 
                <input type="hidden" name="ak_stripe_ajax_nonce" value="<?php echo wp_create_nonce("ak-stripe-pop-up-ajax-nonce"); ?>"> 
            <button class="ak-stripe-pop-up-button submit-button" id='AKStripeButton-<?php echo $form_id; ?>'><span><?php echo get_post_meta($form_id, 'ak-stripe-payment-button', true); ?></span></button>
        </form>















        <button class='ak-stripe-pop-up-button submit-button' id='AKStripeButton'>Pay with Card</button>
        <?php
    }

}
?>