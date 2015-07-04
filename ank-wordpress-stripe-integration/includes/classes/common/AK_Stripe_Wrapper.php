<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Wrapper
 *
 * @author com
 */
require_once(STRIPE_BASE_DIR . '/stripe/init.php');

class AK_Stripe_Wrapper {

    //put your code here
    private $secret_key;
    private $token;

    public function __construct() { // set the values of secret keys on instantiation
        $stripe_options = WC()->stripe_options;
        if (isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
            $this->setSecret_key($stripe_options['test_secret_key']);
        } else {
            $this->setSecret_key($stripe_options['live_secret_key']);
        }
    }

    public function getSecret_key() {
        return $this->secret_key;
    }

    public function setSecret_key($secret_key) {
        $this->secret_key = $secret_key;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function AK_setStripeApi() {
        try {
            $key = $this->getSecret_key();
            \Stripe\Stripe::setApiKey($key);
        } catch (Exception $e) {
            // redirect on failed payment
            //echo $e;    
            $redirect = add_query_arg('payment', 'failed', $_POST['redirect']);
        }
    }

    public function AK_CreateCharge(array $customer_detail) {
        try {
            $this->AK_setStripeApi();
            $charge = \Stripe\Charge::create(array(
                        'amount' => 1000, // $10 TODO
                        'currency' => 'usd', //TODO
                        'source' => $this->getToken(),
                        'metadata' => array("customer name" => $customer_detail[0], "email_address" => $customer_detail[1])
                            )
            );
            return $ak_create_payment_return_code = array("success", $charge);
        } catch (Exception $e) {
            return $ak_create_payment_return_code = array("fail",$e->getMessage());;
        }
        // redirect back to our previous page with the added query variable
        //wp_redirect($redirect);
        //exit;
    }

    public function AK_RetrieveCharge($card) {
        try {
            $this->AK_setStripeApi();
            return \Stripe\Charge::retrieve($card);
        } catch (Exception $e) {
            // redirect on failed payment
            //echo $e;    
            $redirect = add_query_arg('payment', 'failed', $_POST['redirect']);
        }
        // redirect back to our previous page with the added query variable
        wp_redirect($redirect);
        exit;
    }

    public function AK_CreatePlan(array $create_plan) {
        try {
            $this->AK_setStripeApi();
            $create_plan_return = \Stripe\Plan::create(array(
                        "amount" => ($create_plan[2]),
                        "interval" => ($create_plan[3]),
                        "name" => ($create_plan[1]),
                        "currency" => "USD",
                        "trial_period_days" => ($create_plan[4]),
                        "statement_descriptor" => ($create_plan[5]),
                        "id" => ($create_plan[0])));
            $create_plan_return_code = array("success", $create_plan_return);
            //print_r($obj->created);
            return $create_plan_return_code;
        } catch (Exception $e) {
            return $create_plan_return_code = array($e->getMessage());
        }
    }

    public function AK_ListAllPlan() {
        try {
            $this->AK_setStripeApi();

            return \Stripe\Plan::all(array("include[]" => total_count));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function AK_DeletePlan($plan_id) {
        try {
            $this->AK_setStripeApi();
            $plan = \Stripe\Plan::retrieve("$plan_id");
            return $plan->delete();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
