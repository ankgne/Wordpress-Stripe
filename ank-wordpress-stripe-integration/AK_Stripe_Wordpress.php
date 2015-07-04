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

final class AK_Stripe_Wordpress {

    public $stripe_options;
    protected static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * AK_Stripe_Wordpress Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->init_hooks();
        $this->includes();
    }

    /**
     * Define Constants
     */
    private function define_constants() {
        if (!defined('STRIPE_BASE_URL')) {
            define('STRIPE_BASE_URL', plugin_dir_url(__FILE__));
        }
        if (!defined('STRIPE_BASE_DIR')) {
            define('STRIPE_BASE_DIR', dirname(__FILE__));
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_DB_Functions.php';
        include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Functions.php';
        include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Scripts.php';
        include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Wrapper.php';
        include_once STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php';
        if (is_admin()) { // if user is admin
            include_once STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Settings_Page.php';
            include_once STRIPE_BASE_DIR . '/includes/classes/admin/AK_Stripe_Custom_Post_Type.php';
            $ak_stripe_settings_page = new AK_Stripe_Settings_Page();
            $ak_stripe_custom_post_type = new AK_Stripe_Custom_Post_Type ();
            add_action('wp_ajax_nopriv_ak_stripe_submit_payment', array(new AK_stripe_process_payment(), 'ajax_ak_stripe_submit_payment'));
            add_action('wp_ajax_ak_stripe_submit_payment', array(new AK_stripe_process_payment(), 'ajax_ak_stripe_submit_payment'));
        } else {
            require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Frontend.php');
            include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Template_Loader.php';
        }
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_create_stripe_db_tables'));
        register_uninstall_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_stripe_uninstall'));
        add_action('after_setup_theme', array($this, 'include_template_functions'), 11);
        add_action('init', array($this, 'init'), 0);
    }

    public function init() {
        $this->stripe_options = get_option('stripe_settings'); // get the options being used plugin in array
// check to see if we are in test mode
        if (isset($this->stripe_options['test_mode']) && $this->stripe_options['test_mode']) {
            $publishable = $this->stripe_options['test_publishable_key'];
        } else {
            $publishable = $this->stripe_options['live_publishable_key'];
        }
        define('AK_STRIPE_PUBLISHABLE_KEY', $publishable);
    }
    
    
    public function include_template_functions(){
        if ( ! is_admin() ){ // load for frontend only
            include_once( STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Template_Functions.php' );
        }
    }

}

function WC() {
    return AK_Stripe_Wordpress::instance();
}

WC();
//new AK_Stripe_Wordpress();
//include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_DB_Functions.php';
//require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Process_Payment.php');
//include_once STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Template_Loader.php';
//include_once STRIPE_BASE_DIR . 'includes/classes/common/AK_Stripe_Template_Hooks.php';



//register_activation_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_create_stripe_db_tables'));
//register_uninstall_hook(__FILE__, array('AK_Stripe_DB_Functions', 'ak_stripe_uninstall'));


//$stripe_options = get_option('stripe_settings'); // get the options being used plugin in array
//if (isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
//$publishable = $stripe_options['test_publishable_key'];
//} else {
//$publishable = $stripe_options['live_publishable_key'];
//}
//define('AK_STRIPE_PUBLISHABLE_KEY', $publishable);



//if (is_admin()) { 
//require_once(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Settings_Page.php');
//require_once(STRIPE_BASE_DIR . '/includes/classes/admin/AK_Stripe_Custom_Post_Type.php');
//$ak_stripe_settings_page = new AK_Stripe_Settings_Page();
//$ak_stripe_custom_post_type = new AK_Stripe_Custom_Post_Type ();
//add_action('wp_ajax_nopriv_ak_stripe_submit_payment', array(new AK_stripe_process_payment(), 'ajax_ak_stripe_submit_payment'));
//add_action('wp_ajax_ak_stripe_submit_payment', array(new AK_stripe_process_payment(), 'ajax_ak_stripe_submit_payment'));
//} else {
//require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Frontend.php');
//}