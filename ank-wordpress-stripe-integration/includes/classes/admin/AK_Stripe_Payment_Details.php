<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Payment_Details
 *
 * @author com
 */
class AK_Stripe_Payment_Details {

    public function ak_stripe_render_payment_details_page() {
        global $stripe_options; // extract values in main file
        ?>
        <div class="wrap">
            <h2><?php _e('Stripe Payment Details', 'ank_stripe'); ?></h2>
            <form  method="post" action="" id="ak-stripe-delete-plan-form">
                <div id="loaderImg_retrieve_plan"><img src=<?php echo STRIPE_BASE_URL . "/images/ajax-loader.gif" ?> alt="loader-image" style="display: block; margin-left: auto; margin-right: auto;"/></div>
                <div id="stripe-retrieve-plan-success" class="updated"></div> <!-- for success message-->
                <div id="stripe-retrieve-plan-failure" class="error" ></div> <!-- for failure message-->
                <table class="wp-list-table widefat fixed posts stripe-retrive-plan-table">
                    <thead>
                        <tr>
                            <th><?php _e('Payment Status', 'ank_stripe'); ?></th>
                            <th><?php _e('Transaction ID', 'ank_stripe'); ?></th>
                            <th><?php _e('Product Description', 'ank_stripe'); ?></th>
                            <th><?php _e('Customer name', 'ank_stripe'); ?></th>
                            <th><?php _e('Customer email', 'ank_stripe'); ?></th>
                            <th><?php _e('Amount', 'ank_stripe'); ?></th>
                            <th><?php _e('Live Mode', 'ank_stripe'); ?></th>
                            <th><?php _e('Transaction Time (Local Time)', 'ank_stripe'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?php _e('Payment Status', 'ank_stripe'); ?></th>
                            <th><?php _e('Transaction ID', 'ank_stripe'); ?></th>
                            <th><?php _e('Product Description', 'ank_stripe'); ?></th>
                            <th><?php _e('Customer name', 'ank_stripe'); ?></th>
                            <th><?php _e('Customer email', 'ank_stripe'); ?></th>
                            <th><?php _e('Amount', 'ank_stripe'); ?></th>
                            <th><?php _e('Live Mode', 'ank_stripe'); ?></th>
                            <th><?php _e('Transaction Time (Local Time)', 'ank_stripe'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        //$ak_list_plan = new AK_Stripe_Wrapper();
                        //$plan_list=$ak_list_plan->AK_ListAllPlan();
                        $ak_list_payment = new AK_Stripe_DB_Functions();
                        $payment_list = $ak_list_payment->ak_stripe_retrieve_payment_data();
                        if (!empty($payment_list)) :
                            foreach ($payment_list as $row) :
                                ?>
                                <tr>
                                    <td><?php echo $row->payment_status; ?></td>
                                    <td><?php echo $row->transaction_id; ?></td>
                                    <td><?php echo $row->product_description; ?></td>
                                    <td><?php echo $row->customer_name; ?></td>
                                    <td><?php echo $row->customer_email_address; ?></td>
                                    <td><?php echo number_format(($row->amount) / 100, 2) . " " . strtoupper($row->currency); ?></td>
                                    <td><?php echo $row->livemode; ?></td>                                                    
                                    <td><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $row->create_time ), 'F j, Y H:i:s' );?></td>
                                    
                                </tr>
                                
                                <?php
                                
                                
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td colspan="3"><?php _e('No data found', 'ank_stripe'); ?></td>
                            </tr>
                        <?php
                        
                        endif;
                        ?>	
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary ak-stripe-delete-plan-button" value="<?php _e('Delete Plan', 'ank_stripe'); ?>" style="display: none;" />
                    <input type="submit" class="button-primary ak-stripe-delete-plans-button" value="<?php _e('Delete Plans', 'ank_stripe'); ?>"  style="display: none;" />
                </p>
            </form>
            <?php
            die();
        }

    }
    