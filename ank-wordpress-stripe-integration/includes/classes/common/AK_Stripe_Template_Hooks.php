<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    /*success transaction template*/
    add_action( 'ak_stripe_success_before_main_content', 'ak_stripe_start_template_wrapper', 10 );
    add_action( 'ak_stripe_success_after_main_content', 'ak_stripe_end_template_wrapper', 10 );
    add_action( 'ak_stripe_success_main_content', 'ak_stripe_payment_success_content', 10 );
    
    /*failed transaction template*/
    add_action( 'ak_stripe_fail_before_main_content', 'ak_stripe_fail_start_template_wrapper', 10 );
    add_action( 'ak_stripe_fail_after_main_content', 'ak_stripe_fail_end_template_wrapper', 10 );
    add_action( 'ak_stripe_fail_main_content', 'ak_stripe_payment_fail_content', 10 );
    
?>
