<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Template_Functions
 * This file contains the functions used by template files of plugin like thankyou.php and failed.php
 * @author com
 */
function ak_stripe_locate_template($template) {
    $check_dirs = array(
        trailingslashit(get_stylesheet_directory()) . '/templates',
        trailingslashit(get_template_directory()) . '/templates',
        trailingslashit(get_stylesheet_directory()),
        trailingslashit(get_template_directory()),
        trailingslashit(STRIPE_BASE_DIR) . '/includes/classes/templates/'
    );

    foreach ($check_dirs as $dir) {
        if (file_exists(trailingslashit($dir) . $template)) {
            return trailingslashit($dir) . $template;
        }
    }
}

if (!function_exists('ak_stripe_start_template_wrapper')) {

    function ak_stripe_start_template_wrapper() {
        $template = get_option('template');

        switch ($template) {
            case 'twentyeleven' :
                echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
                break;
            case 'twentytwelve' :
                echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve entry-content">';
                break;
            case 'twentythirteen' :
                echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
                break;
            case 'twentyfourteen' :
                echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="entry-header">';
                break;
            case 'twentyfifteen' :
                echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
                break;
            default :
                echo '<div id="container"><div id="content" role="main">';
                break;
        }
    }

}

if (!function_exists('ak_stripe_end_template_wrapper')) {

    function ak_stripe_end_template_wrapper() {
        $template = get_option('template');

        switch ($template) {
            case 'twentyeleven' :
                echo '</div></div>';
                break;
            case 'twentytwelve' :
                echo '</div></div>';
                break;
            case 'twentythirteen' :
                echo '</div></div>';
                break;
            case 'twentyfourteen' :
                echo '</div></div></div>';
                get_sidebar('content');
                break;
            case 'twentyfifteen' :
                echo '</div></div>';
                break;
            default :
                echo '</div></div>';
                break;
        }
    }

}

if (!function_exists('ak_stripe_payment_success_content')) {

    function ak_stripe_payment_success_content() {
        ?>
        <div class="alert alert-success" role="alert">
            <?php
            echo ("Congratulations. Your payment went through!");
            ?>
        </div>

        <?php
        echo "Here's what you purchased:";
        echo "<br>";
        echo get_post_meta($_GET['form-id'], 'ak-stripe-form-description', true);
        echo "<br>";
        if (get_post_meta($_GET['form-id'], 'ak-stripe-form-name', true)) {
            echo wpautop("From:" . get_post_meta($_GET['form-id'], 'ak-stripe-form-name', true));
        }


        $ak_stripe_retreive_charge = new AK_Stripe_Wrapper();
        $ak_stripe_retrieve_charge_return = $ak_stripe_retreive_charge->AK_RetrieveCharge($_GET['chargeid']);
        $ak_stripe_payment_amount = $ak_stripe_retrieve_charge_return['amount'] / 100;
        $ak_stripe_payment_currency = $ak_stripe_retrieve_charge_return['currency'];
        $ak_stripe_transaction_id = $ak_stripe_retrieve_charge_return['id'];

        echo wpautop("Total Paid: " . $ak_stripe_payment_amount . " " . $ak_stripe_payment_currency);
        echo wpautop("Your transaction ID is: " . $ak_stripe_transaction_id);
    }

}

if (!function_exists('ak_stripe_fail_start_template_wrapper')) {

    function ak_stripe_fail_start_template_wrapper() {
        $template = get_option('template');

        switch ($template) {
            case 'twentyeleven' :
                echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
                break;
            case 'twentytwelve' :
                echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve entry-content">';
                break;
            case 'twentythirteen' :
                echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
                break;
            case 'twentyfourteen' :
                echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="entry-header">';
                break;
            case 'twentyfifteen' :
                echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
                break;
            default :
                echo '<div id="container"><div id="content" role="main">';
                break;
        }
    }

}

if (!function_exists('ak_stripe_fail_end_template_wrapper')) {

    function ak_stripe_fail_end_template_wrapper() {
        $template = get_option('template');

        switch ($template) {
            case 'twentyeleven' :
                echo '</div></div>';
                break;
            case 'twentytwelve' :
                echo '</div></div>';
                break;
            case 'twentythirteen' :
                echo '</div></div>';
                break;
            case 'twentyfourteen' :
                echo '</div></div></div>';
                get_sidebar('content');
                break;
            case 'twentyfifteen' :
                echo '</div></div>';
                break;
            default :
                echo '</div></div>';
                break;
        }
    }

}


if (!function_exists('ak_stripe_payment_fail_content')) {

    function ak_stripe_payment_fail_content() {
        ?>
        <div class="alert alert-danger" role="alert">
            <?php
            $error_message = ak_stripe_exception_handling($_GET['type'], $_GET['code']);
            echo "Payment Error: " . $error_message;
            ?>
        </div>
        <?php
        if ($_GET['chargeid']) {
            echo wpautop("Your transaction ID is: " . $_GET['chargeid']);
        }
        //echo wpautop("Your transaction ID is: " . $_GET['chargeid']);        
    }

}

if (!function_exists('ak_stripe_exception_handling')) {

    function ak_stripe_exception_handling($error_type, $error_code) {
        if ("card_error" === $error_type) {
            switch ($error_code) {
                case 'invalid_number':
                    $error_message = 'The card number is not a valid credit card number. Please try another card';
                    break;
                case 'invalid_expiry_month':
                    $error_message = "The card's expiration month is invalid, perhaps try again.";
                    break;
                case 'invalid_expiry_year':
                    $error_message = "The card's expiration year is invalid, perhaps try again.";
                    break;
                case 'invalid_cvc':
                    $error_message = "The card's security code is invali, perhaps try again.";
                    break;
                case 'incorrect_number':
                    $error_message = "The card number is incorrect.";
                    break;
                case 'expired_card':
                    $error_message = "The card has expired.";
                    break;
                case 'incorrect_cvc':
                    $error_message = "The card's security code is incorrect.";
                    break;
                case 'incorrect_zip':
                    $error_message = "The card's zip code failed validation.";
                    break;
                case 'card_declined':
                    $error_message = "The card was declined. Please try another card.";
                    break;
                case 'missing':
                    $error_message = "There is no card on a customer that is being charged.";
                    break;
                case 'processing_error':
                    $error_message = "An error occurred while processing the card.";
                    break;
            }
        }
        if ("api_error" === $error_type) {
            $error_message = "Network problem, perhaps try again";
        }

        if ("invalid_request_error" === $error_type) {
            $error_message = "There is some internal error, perhaps try again";
        }

        if ("invalid_request_error" === $error_type && !is_numeric($error_code)) { //passing message in case of invalid_request_error
            $error_message = $error_code;
        }

        return $error_message;
    }

}
