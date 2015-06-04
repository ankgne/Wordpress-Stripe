<?php

/*
  Plugin Name: Ank WordPress Stripe Integration
  Plugin URI:
  Description: A plugin to integrate Stripe and WordPress
  Author: Ankur Khurana
  Author URI:
  COntributors:
  Version: 0.1
 */

/* * ********************************
 * constants and globals
 * ******************************** */

if (!defined('STRIPE_BASE_URL')) {
    define('STRIPE_BASE_URL', plugin_dir_url(__FILE__));
}
if (!defined('STRIPE_BASE_DIR')) {
    define('STRIPE_BASE_DIR', dirname(__FILE__));
}
include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_DB_Functions.php';
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php');
register_activation_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_create_stripe_db_tables'));
register_uninstall_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_stripe_uninstall'));


$stripe_options = get_option('stripe_settings'); // get the options being used plugin in array


//        add_action('wp_ajax_nopriv_ak_stripe_submit_payment', array($this->ak_stripe_process_payment, 'ajax_ak_stripe_submit_payment'));



if (is_admin()) { // if user is admin
    // load admin includes
    require_once(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Settings_Page.php');
    add_action( 'wp_ajax_nopriv_ak_stripe_submit_payment', array(new AK_stripe_process_payment(), 'ajax_ak_stripe_submit_payment'));
} else {
    //echo STRIPE_BASE_DIR;
//    add_action( 'wp_ajax_view_site_description', 'view_site_description' );
//add_action( 'wp_ajax_nopriv_view_site_description', 'view_site_description' );
    require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Frontend.php');
}