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

    private static $plans_table_name = "ak_stripe_plans";
    private static $customer_table_name = "ak_stripe_customer";
    private static $payment_table_name = "ak_stripe_payment";

    private static function ak_stripe_table_name($table_name) {
        global $wpdb;
        if ("customer" === $table_name) {
            return $wpdb->prefix . self::$customer_table_name;
        } else if ("plan" === $table_name) {
            return $wpdb->prefix . self::$plans_table_name;
        } else if ("payment" === $table_name) {
            return $wpdb->prefix . self::$payment_table_name;
        } else {
            return false;
        }
    }

    //put your code here

    /* Creates stripe database table in local DB on plugin activation */
    static function ak_create_stripe_db_tables() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');  // WP does not automcatically include this file so manually include this file to use dbDelta
        $plans_table_name = self::ak_stripe_table_name("plan");
        $customer_table_name = self::ak_stripe_table_name("customer");
        $payment_table_name = self::ak_stripe_table_name("payment");


        $ak_stripe_customer_query = "CREATE TABLE $customer_table_name (
         id INT NOT NULL AUTO_INCREMENT,
         customer_id VARCHAR(128)NOT NULL,
         customer_name VARCHAR(128)NOT NULL,
         email_id VARCHAR(128) NOT NULL,
         charge_id VARCHAR(128) NOT NULL,
         UNIQUE KEY id (id)
         );";

        dbDelta($ak_stripe_customer_query);

        $ak_stripe_plans_query = "CREATE TABLE  $plans_table_name(
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

        dbDelta($ak_stripe_plans_query);

        $ak_stripe_payment_query = "CREATE TABLE  $payment_table_name(
         id INT NOT NULL AUTO_INCREMENT,
         customer_name VARCHAR(128)NOT NULL,
         customer_email_address VARCHAR(128)NOT NULL,
         transaction_id VARCHAR(128)NOT NULL, /*balance_transaction*/
         currency VARCHAR(5) NOT NULL,
         amount INT NOT NULL,
         livemode VARCHAR(12),
         create_time INT,
         UNIQUE KEY id (id)
         );";

        dbDelta($ak_stripe_payment_query);
    }

    /* drop database table and options being used by this plugin */

    function ak_stripe_uninstall() {
        global $wpdb;
        $plans_table_name = self::ak_stripe_table_name("plan");
        $customer_table_name = self::ak_stripe_table_name("customer");

        $wpdb->query("DROP TABLE IF EXISTS $plans_table_name");
        $wpdb->query("DROP TABLE IF EXISTS $customer_table_name");

        delete_option('stripe_settings');
    }

    /* Inserts stripe plan details in local db */

    function ak_insert_stripe_plan_db(array $plan_data) {
        global $wpdb;
        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $plans_table_name = self::ak_stripe_table_name("plan");
        $wpdb->insert($plans_table_name, array(
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
        $plans_table_name = self::ak_stripe_table_name("plan");

        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $result_code = $wpdb->delete($plans_table_name, array(
            'plan_id' => $plan_id,
        ));

        return $result_code; //false on failure and on success number of rows deleted.
    }

    /* Retrieves stripe plan details from local db */

    function ak_retrieve_stripe_plan_db() {
        global $wpdb;
        $plans_table_name = self::ak_stripe_table_name("plan");

        if (!current_user_can('manage_options')) { // ensure that correct user is inserting values in db
            wp_die('You are not allowed to be on this page.');
        }

        $plans = $wpdb->get_results('SELECT * FROM ' . $plans_table_name . ' ORDER BY create_time DESC');
        return $plans;
    }

    function ak_stripe_insert_customer_data(array $customer_data) {
        global $wpdb;

        $customer_table_name = self::ak_stripe_table_name("customer");
        try {
            $wpdb->insert($customer_table_name, array(
                'customer_id' => $customer_data[0],
                'customer_name' => $customer_data[1],
                'email_id' => $customer_data[2],
            ));
        } Catch (Exception $e) {
            return $e->getMessage(); // TODO
        }
    }

    function ak_stripe_delete_customer_data($customer_id) {
        global $wpdb;
        $customer_table_name = self::ak_stripe_table_name("customer");

        $result_code = $wpdb->delete($customer_table_name, array(
            'customer_id' => $customer_id,
        ));

        return $result_code; //false on failure and on success number of rows deleted.
    }

    /* Retrieves stripe plan details from local db */

    function ak_stripe_retrieve_customer_data() {
        global $wpdb;
        $customer_table_name = self::ak_stripe_table_name("customer");

        $customer = $wpdb->get_results('SELECT * FROM ' . $customer_table_name . ' ORDER BY create_time DESC');
        return $customer;
    }

// Payment table functions

    /* Inserts Stripe payment data in local db */
    function ak_stripe_insert_payment_data(array $payment_data) {
        global $wpdb;

        $ak_payment_table_name = self::ak_stripe_table_name("payment");
        try {
            $wpdb->insert($ak_payment_table_name, array(
                'customer_name' => $payment_data[0],
                'customer_email_address' => $payment_data[1],
                'transaction_id' => $payment_data[4],
                'currency' => $payment_data[3],
                'amount' => $payment_data[2],
                'livemode' => $payment_data[5],
                'create_time' => $payment_data[6],
                
            ));
        } Catch (Exception $e) {
            return $e->getMessage(); // TODO
        }
    }

    /* Retrieves Stripe payment data from local db */

    function ak_stripe_retrieve_payment_data() {
        global $wpdb;
        $customer_table_name = self::ak_stripe_table_name("payment");

        $customer = $wpdb->get_results('SELECT * FROM ' . $customer_table_name . ' ORDER BY create_time DESC');
        return $customer;
    }

}
