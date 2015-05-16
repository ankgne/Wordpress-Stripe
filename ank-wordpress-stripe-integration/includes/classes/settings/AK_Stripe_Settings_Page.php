<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Settings
 *
 * @author com
 */
include(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Setting_Form.php');
include(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_Manage_Plans.php');

class AK_Stripe_Settings_Page {
    
    private $ak_stripe_setting_form = '';
    private $ak_stripe_manage_plans = '';

    //put your code here
    function __construct() {
        $this->ak_stripe_setting_form = new AK_Stripe_Setting_Form();
        $this->ak_stripe_manage_plans = new AK_Stripe_Manage_Plans();
        add_action('admin_menu', array($this, 'ak_stripe_settings_setup'));
        add_action('admin_init', array($this, 'ak_stripe_register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'ak_stripe_add_ajax_javascript_file'));
        add_action('wp_ajax_create_stripe_plan', array($this->ak_stripe_manage_plans, 'ajax_create_stripe_plan'));
        add_action('wp_ajax_get_stripe_plan', array($this->ak_stripe_manage_plans, 'get_stripe_plan'));
        add_action('wp_ajax_delete_stripe_plan', array($this->ak_stripe_manage_plans, 'ajax_delete_stripe_plan'));
    }

    function ak_stripe_settings_setup() {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $output_function, $icon_url, $position )
        add_menu_page(__('Stripe Settings', 'ank_stripe'), __('Stripe Settings', 'ank_stripe'), 'manage_options', 'stripe-settings', array($this->ak_stripe_setting_form, 'ak_stripe_render_options_page'));
        add_submenu_page('stripe-settings', __('Manage Stripe Plans', 'ank_stripe'), __('Manage Stripe Plans', 'ank_stripe'), 'manage_options', 'manage-stripe-plans', array($this->ak_stripe_manage_plans, 'ak_stripe_render_manage_plans_page'));
    }

    function ak_stripe_register_settings() {
        // creates our settings in the options table
        //register_setting($option_group, $option_name, $sanitise_callback)
        register_setting('stripe_settings_group', 'stripe_settings');
    }

    function ak_stripe_add_ajax_javascript_file() {
        wp_enqueue_script('ajax_custom_script', STRIPE_BASE_URL . 'includes/js/ajax-javascript.js', array('jquery'));
    }

}
