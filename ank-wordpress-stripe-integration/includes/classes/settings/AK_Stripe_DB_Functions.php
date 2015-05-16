<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_DB_Functions
 *
 * @author com
 */
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class AK_Stripe_DB_Functions {

    //put your code here

    /* Creates stripe database table in local DB on plugin activation */
    static function ak_create_stripe_plan_db() {
        global $wpdb;
        $table_name = $wpdb->prefix . "ak_stripe_plans";

        $ak_stripe_plans_query = "CREATE TABLE $table_name (
         id INT NOT NULL AUTO_INCREMENT,
         plan_id VARCHAR(128)NOT NULL,
         plan_name VARCHAR(128)NOT NULL,
         amount INT NOT NULL,
         currency VARCHAR(5) NOT NULL,
         bill_frequency VARCHAR(10) NOT NULL,
         trial_days TINYINT,
         statement_desc VARCHAR(128),
         create_time INT,
         UNIQUE KEY id (id)
         );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');  // WP does not automcatically include this file so manually include this file to use dbDelta
        dbDelta($ak_stripe_plans_query);
    }

    /* drop database table and options being used by this plugin */

    function ak_stripe_uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . "ak_stripe_plans";

        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('stripe_settings');
    }

    /* Inserts stripe plan details in local db */

    function ak_insert_stripe_plan_db(array $plan_data) {
        global $wpdb;

        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $table_name = $wpdb->prefix . "ak_stripe_plans";

        $wpdb->insert($table_name, array(
            'plan_id' => $plan_data[0],
            'plan_name' => $plan_data[1],
            'amount' => $plan_data[2],
            'currency' => "USD",
            'bill_frequency' => "$plan_data[3]",
            'trial_days' => $plan_data[4],
            'statement_desc' => $plan_data[5],
            'create_time' => $plan_data[6],
        ));
    }

    
    function ak_stripe_delete_plan_db($plan_id) {
        global $wpdb;

        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $table_name = $wpdb->prefix . "ak_stripe_plans";

        $result_code=$wpdb->delete($table_name, array(
            'plan_id' => $plan_id,
        ));
        
        return $result_code; //false on failure and on success number of rows deleted.
    }
    
    /* Retrieves stripe plan details from local db */
    function ak_retrieve_stripe_plan_db() {
        global $wpdb;

        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $table_name = $wpdb->prefix . "ak_stripe_plans";
        $plans = $wpdb->get_results('SELECT * FROM ' . $table_name . ' ORDER BY create_time DESC');
        return $plans;
    }

}
