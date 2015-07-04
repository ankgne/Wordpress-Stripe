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
    echo $template;
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
