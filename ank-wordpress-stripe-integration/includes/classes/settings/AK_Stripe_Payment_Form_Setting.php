<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Setting_Form
 *
 * @author com
 */
class AK_Stripe_Payment_Form_Setting {

    //put your code here
    public function ak_stripe_render_payment_setting_form() {
        global $stripe_options; // extract values in main file
        ?>
        <div class="wrap">
            <h2><?php _e('Payment Form Setting', 'ank_stripe'); ?></h2>
            <form method="post" action="options.php">

                <?php settings_fields('ak_stripe_payment_form_group'); ?>

                <table class="form-table">
                    <tbody>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('Test Mode', 'ank_stripe'); ?>
                            </th>
                            <td>
                                <input id="ak_stripe_payment_form[test_mode]" name="ak_stripe_payment_form[test_mode]" type="checkbox" value="1" <?php checked(1, $stripe_options['test_mode']); ?> />
                                <label class="description" for="ak_stripe_payment_form[test_mode]"><?php _e('Check this to use the plugin in test mode.', 'ank_stripe'); ?></label>
                            </td>
                        </tr>
                    </tbody>
                </table>	

                <h3 class="title"><?php _e('API Keys', 'ank_stripe'); ?></h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('Live Secret', 'ank_stripe'); ?>
                            </th>
                            <td>
                                <input id="ak_stripe_payment_form[live_secret_key]" name="ak_stripe_payment_form[live_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_secret_key']; ?>"/>
                                <label class="description" for="ak_stripe_payment_form[live_secret_key]"><?php _e('Paste your live secret key.', 'ank_stripe'); ?></label>
                            </td>
                        </tr>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('Live Publishable', 'ank_stripe'); ?>
                            </th>
                            <td>
                                <input id="ak_stripe_payment_form[live_publishable_key]" name="ak_stripe_payment_form[live_publishable_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_publishable_key']; ?>"/>
                                <label class="description" for="ak_stripe_payment_form[live_publishable_key]"><?php _e('Paste your live publishable key.', 'ank_stripe'); ?></label>
                            </td>
                        </tr>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('Test Secret', 'ank_stripe'); ?>
                            </th>
                            <td>
                                <input id="ak_stripe_payment_form[test_secret_key]" name="ak_stripe_payment_form[test_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['test_secret_key']; ?>"/>
                                <label class="description" for="ak_stripe_payment_form[test_secret_key]"><?php _e('Paste your test secret key.', 'ank_stripe'); ?></label>
                            </td>
                        </tr>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('Test Publishable', 'ank_stripe'); ?>
                            </th>
                            <td>
                                <input id="ak_stripe_payment_form[test_publishable_key]" name="ak_stripe_payment_form[test_publishable_key]" class="regular-text" type="text" value="<?php echo $stripe_options['test_publishable_key']; ?>"/>
                                <label class="description" for="ak_stripe_payment_form[test_publishable_key]"><?php _e('Paste your test publishable key.', 'ank_stripe'); ?></label>
                            </td>
                        </tr>
                    </tbody>
                </table>	

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Options', 'mfwp_domain'); ?>" />
                </p>

            </form>
            <?php
        }

    }
    