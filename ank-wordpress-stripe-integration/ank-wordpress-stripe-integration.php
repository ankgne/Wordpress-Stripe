<?php
/*
Plugin Name: Ank WordPress Stripe Integration
Plugin URI: 
Description: A plugin to illustrate how to integrate Stripe and WordPress
Author: Ankur Khurana
Author URI: 
COntributors: 
Version: 0.1
*/
 
/**********************************
* constants and globals
**********************************/
 
if(!defined('STRIPE_BASE_URL')) {
	define('STRIPE_BASE_URL', plugin_dir_url(__FILE__));
}
if(!defined('STRIPE_BASE_DIR')) {
	define('STRIPE_BASE_DIR', dirname(__FILE__));
}
include_once dirname( __FILE__ ) . '/includes/classes/settings/AK_Stripe_DB_Functions.php';
register_activation_hook( __FILE__, array( 'AK_Stripe_DB_Functions','ak_create_stripe_plan_db' ));
register_uninstall_hook(__FILE__, array( 'AK_Stripe_DB_Functions','ak_stripe_uninstall' ));


$stripe_options = get_option('stripe_settings');

if(is_admin()) { // if user is admin
	// load admin includes
	include(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Settings_Page.php');
        $ak_stripe_settings_page=new AK_Stripe_Settings_Page();
        
} else { 
    //echo STRIPE_BASE_DIR;
        include(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Frontend.php');	
}