<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Custom_Post_Type
 *
 * @author com
 */
//require_once(STRIPE_BASE_DIR . '/includes/classes/common/AK_Stripe_Functions.php');
class AK_Stripe_Custom_Post_Type {

    //put your code here

    function __construct() {
        add_action('init', array($this, 'ak_stripe_register_post_type'));
        add_action('add_meta_boxes', array($this, 'ak_stripe_checkout_form_metabox'));
        add_action('save_post', array($this, 'ak_stripe_checkout_form_save'));
        add_filter('manage_edit-ak_stripe_checkout_columns', array($this, 'ak_stripe_checkout_add_custom_columns'));
        add_action('manage_ak_stripe_checkout_posts_custom_column', array($this, 'ak_stripe_checkout_render_custom_column'), 10, 2);
        add_filter('post_row_actions', array($this, 'ak_stripe_checkout_remove_view'), 10, 1);
        add_filter('get_sample_permalink_html', array($this, 'ak_stripe_checkout_hide_permalinks'));
        add_filter('pre_get_shortlink', array($this, 'ak_stripe_checkout_remove_shortlink'));
        add_filter('post_updated_messages', array($this, 'post_updated_messages'));
    }

    public function ak_stripe_register_post_type() {
        $labels = array(
            'name' => __('Stripe Checkout Forms', 'akpt'),
            'singular_name' => __('Stripe Checkout Form', 'akpt'),
            'add_new_item' => __('Add New Checkout Form', 'akpt'),
            'all_items' => __('Checkout Forms', 'akpt'),
            'edit_item' => __('Edit Checkout Form', 'akpt'),
            'new_item' => __('New Checkout Form', 'akpt'),
            'view_item' => __('View Checkout Form', 'akpt'),
            'not_found' => __('No Checkout Forms Found', 'akpt'),
            'not_found_in_trash' => __('No Checkout Forms Found in Trash', 'akpt')
        );

        $supports = array(
            'title',
        );

//        $supports = false;


        $args = array(
            'label' => __('Stripe Checkout Forms', 'akpt'),
            'labels' => $labels,
            'description' => __('Available Checkout Forms', 'akpt'),
            'public' => true, //to remove permalink
            'show_in_menu' => 'stripe-settings',
            'has_archive' => true,
            'rewrite' => true,
            'exclude_from_search' => 'true',
            'supports' => $supports
        );

        register_post_type('ak_stripe_checkout', $args);
    }

    function ak_stripe_checkout_form_metabox() {
        add_meta_box(
                'ak-stripe-checkout-form-metabox', __('Checkout Form Details', 'akpt'), array($this, 'ak_stripe_render_checkout_form_metabox'), 'ak_stripe_checkout', 'normal', 'core'
        );
    }

    function ak_stripe_render_checkout_form_metabox($post) {
        // generate a nonce field
        wp_nonce_field(basename(__FILE__), 'ak-stripe-checkout-form-nonce');
        $custom_meta_fields = ak_stripe_checkout_form_fields();
        // Begin the field table and loop
        echo '<table class="form-table">';
        foreach ($custom_meta_fields as $field) {
            // get value of this field if it exists for this post
            $meta = get_post_meta($post->ID, $field['id'], true);
            // begin a table row with
            echo '<tr>
                <th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
                <td>';
            switch ($field['type']) {

                case 'text':
                    if ($meta == "") {
                        $meta = $field['default'];
                    }
                    echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30" />
        <br /><span class="description">' . $field['desc'] . '</span>';
                    break;
                case 'checkbox':
                    echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '', '/>
        <label for="' . $field['id'] . '">' . $field['desc'] . '</label>';
                    break;
                case 'text-readonly':
                    echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="50" onfocus="this.select();" readonly="readonly"/>
        <br /><span class="description">' . $field['desc'] . '</span>';
                    break;
                case 'select':
                    echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
                    }
                    echo '</select><br /><span class="description">' . $field['desc'] . '</span>';
                    break;
            } //end switch
            echo '</td></tr>';
        } // end foreach
        echo '<input type="hidden" id="ak_stripe_post_ID" name="post_ID" value="' . $post->ID . '">';
        echo '</table>'; // end table
    }

    function ak_stripe_checkout_form_save($post_id) {
        $custom_meta_fields = ak_stripe_checkout_form_fields();

        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = ( isset($_POST['ak-stripe-checkout-form-nonce']) && ( wp_verify_nonce($_POST['ak-stripe-checkout-form-nonce'], basename(__FILE__)) ) ) ? true : false;

        // exit depending on the save status or if the nonce is not valid
        if ($is_autosave || $is_revision || !$is_valid_nonce) {
            return;
        }

        // loop through fields and save the data
        foreach ($custom_meta_fields as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        } // end foreach
    }

    function ak_stripe_checkout_add_custom_columns($columns) {
        unset($columns['date']);

        return array_merge($columns, array('form_name' => __('Form Name', 'akpt'),
            'form_shortcode' => __('Form Shortcode', 'akpt'),
            'payment_amount' => __('Payment Amount', 'akpt'),
            'payment_type' => __('Payment Type', 'akpt'),
            'date' => __('Published date', 'akpt'),
        ));
    }

    function ak_stripe_checkout_render_custom_column($column, $post_id) {
        $custom_meta_fields = ak_stripe_checkout_form_fields();
        foreach ($custom_meta_fields as $field) {
            if ($column == $field['column']) {
                echo get_post_meta($post_id, $field['id'], true);
            }
        }
    }

    function ak_stripe_checkout_remove_view($actions) {
        if (get_post_type() === 'ak_stripe_checkout')
            unset($actions['view']);
        return $actions;
    }

    function ak_stripe_checkout_hide_permalinks($in) {
        if (get_post_type() === 'ak_stripe_checkout') {
            $in = '';
        }
        return $in;
    }

    function ak_stripe_checkout_remove_shortlink() {
        if (get_post_type() === 'ak_stripe_checkout') {
            return '';
        }
    }

    function post_updated_messages($messages) {
        if (get_post_type() === 'ak_stripe_checkout') {
            $messages['post'][6] = sprintf(__('Stripe checkout form published', 'akpt'));
            $messages['post'][1] = __('Stripe checkout form updated', 'akpt');
            return $messages;
        }
        return $messages;
    }

}