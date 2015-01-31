<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AK_Stripe_Manage_Plans
 *
 * @author com
 */
require_once(STRIPE_BASE_DIR . '/includes/classes/settings/AK_Stripe_DB_Functions.php');
require_once(STRIPE_BASE_DIR . '/includes/classes/frontend/AK_Stripe_Payment_Functions.php');
class AK_Stripe_Manage_Plans {
    public function ak_stripe_render_manage_plans_page() {
	global $stripe_options; // extract values in main file
	?>
	<div class="wrap">
		<h2><?php _e('Manage Stripe Plans', 'ank_stripe'); ?></h2>
		<form method="post" action="" id="ak-stripe-create-plan-form">
                        <h3 class="title"><?php _e('Create New Plan', 'ank_stripe'); ?></h3>
                        <div id="stripe-create-plan-success" class="updated"></div> <!-- for success message-->
                        <div id="stripe-create-plan-failure" class="error" ></div> <!-- for failure message-->
			<table class="form-table stripe-create-plan-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('ID', 'ank_stripe'); ?>
						</th>
						<td>
                                                <input id="planID" name="planID" type="text" value=""/>    
							<label class="description" for="planID"><?php _e('Unique string of your choice that will be used to identify this plan when subscribing a customer. This could be an identifier like "gold" .', 'ank_stripe'); ?></label>
						</td>
					</tr>
                                        
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Name', 'ank_stripe'); ?>
						</th>
						<td>
                                                <input id="planname" name="planname" type="text" value=""/>    
							<label class="description" for="planID"><?php _e('Name of the plan, to be displayed on invoices and in the web interface.', 'ank_stripe'); ?></label>
						</td>
					</tr>
                                        
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Amount', 'ank_stripe'); ?>
						</th>
						<td>
                                                <input id="planamount" name="planamount" type="text" value=""/>    
							<label class="description" for="planID"><?php _e('A positive integer in cents (or 0 for a free plan) representing how much to charge (on a recurring basis).', 'ank_stripe'); ?></label>
						</td>
					</tr> 
                                        
                                        <tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Interval (Billing Frequency)', 'ank_stripe'); ?>
						</th>
						<td>
                                                <select id="interval" name="interval">
                                                    <option value="day">Daily</option>
                                                    <option value="week">Weekly</option>
                                                    <option value="month">Monthly</option>
                                                    <option value="year">Yearly</option>
                                                </select>
							<label class="description" for="interval"><?php _e('Specifies billing frequency. Either day, week, month or year.', 'ank_stripe'); ?></label>
						</td>
					</tr>                                        
                                        
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Trial Period', 'ank_stripe'); ?>
						</th>
						<td>
                                                <input id="trailperiod" name="trailperiod" type="text" value=""/>    
							<label class="description" for="trailperiod"><?php _e('Specifies a trial period in (an integer number of) days. If you include a trial period, the customer will not be billed for the first time until the trial period ends. If the customer cancels before the trial period is over, customer will never be billed at all', 'ank_stripe'); ?></label>
						</td>
					</tr>
                                        
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('Statement Description', 'ank_stripe'); ?>
						</th>
						<td>
                                                <input id="stmtdesc" name="stmtdesc" type="text" value=""/>    
							<label class="description" for="stmtdesc"><?php _e("An arbitrary string to be displayed on your customer's credit card statement. This may be up to 22 characters. As an example, if your website is RunClub and the item you're charging for is your Silver Plan, you may want to specify a statement_descriptor of 
RunClub Silver Plan. ", 'ank_stripe'); ?></label>
						</td>
					</tr>                                             
				</tbody>
			</table>	
                        <input type="hidden" name="action" value="create-stripe-plan"/>
			<input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>    
			<p class="submit">
				<input type="submit" class="button-primary ak-stripe-create-plan" value="<?php _e('Create Plan', 'ank_stripe'); ?>" />
			</p>
 
                </form>
        <h3 class="title"><?php _e('Existing Stripe Plans', 'ank_stripe'); ?></h3>
	<?php
        $this->get_stripe_plan();
        
}


/*Called from ajax-javascript.js*/
function ajax_create_stripe_plan() 
{
    if(!empty($_POST['post_id']))
    {
        $post = get_post( $_POST['post_id'] );
        parse_str($_POST['datastring']); // parse query string
        $form_data=array($planID,$planname,$planamount,$interval,$trailperiod,$stmtdesc);
        $ak_create_plan = new AK_Stripe_Payment_Functions();
        $create_plan_return_code=$ak_create_plan->AK_CreatePlan($form_data);
        if ($create_plan_return_code[0]=="success"){
            $form_data[]=$create_plan_return_code[1]->created; // insert plan createtime in array
            $ak_insert_stripe_plan=new AK_Stripe_DB_Functions();
            $ak_insert_stripe_plan->ak_insert_stripe_plan_db($form_data);
            
        }     
        echo $create_plan_return_code[0];
        
        //print_r (explode("&",$_POST['datastring']));
    }

    die();
}

function get_stripe_plan() 
{
    ?>
                <form  method="post" action="" id="ak-stripe-delete-plan-form">
                <table class="wp-list-table widefat fixed posts stripe-retrive-plan-table">
                        <thead>
                                <tr>
                                        <th><?php _e('Plan ID', 'ank_stripe'); ?></th>
                                        <th><?php _e('Plan Name', 'ank_stripe'); ?></th>
                                        <th><?php _e('Amount', 'ank_stripe'); ?></th>
                                        <th><?php _e('Billing Frequency', 'ank_stripe'); ?></th>
                                        <th><?php _e('Trial', 'ank_stripe'); ?></th>
                                        <th><?php _e('Create Time (in UTC)', 'ank_stripe'); ?></th>
                                        <th><?php _e('Select to delete', 'ank_stripe'); ?></th>
                                </tr>
                        </thead>
                        <tfoot>
                                <tr>
                                        <th><?php _e('Plan ID', 'ank_stripe'); ?></th>
                                        <th><?php _e('Plan Name', 'ank_stripe'); ?></th>
                                        <th><?php _e('Amount', 'ank_stripe'); ?></th>
                                        <th><?php _e('Billing Frequency', 'ank_stripe'); ?></th>
                                        <th><?php _e('Trial', 'ank_stripe'); ?></th>
                                        <th><?php _e('Create Time (in UTC)', 'ank_stripe'); ?></th>
                                        <th><?php _e('Select to delete', 'ank_stripe'); ?></th>
                                </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        //$ak_list_plan = new AK_Stripe_Payment_Functions();
                        //$plan_list=$ak_list_plan->AK_ListAllPlan();
                        $ak_list_plan = new AK_Stripe_DB_Functions();
                        $plan_list=$ak_list_plan->ak_retrieve_stripe_plan_db();
                        if( !empty( $plan_list ) ) :
                                  foreach( $plan_list as $row ) : ?>
                                        <tr>
                                                <td><?php echo $row->plan_id; ?></td>
                                                <td><?php echo $row->plan_name; ?></td>
                                                <td><?php echo number_format(($row->amount)/100,2) . " " . strtoupper($row->currency); ?></td>
                                                <td><?php echo $row->bill_frequency; ?></td>
                                                
                                                <td><?php echo (is_null($row->trial_days)?"No Trial" : ($row->trial_days) . " " . ($row->trial_days=="1"?"day" : "days")) ; ?></td>
                                                <td><?php echo date_i18n('Y-m-d H:i:s', $row->create_time ); ?></td>
                                                <td><?php echo "<input type='checkbox' name='check_list[]' value='$row->plan_id'>"; ?></td>
                                                
                                        </tr>
                                        <?php
                                endforeach;
                        else : ?>
                                <tr>
                                        <td colspan="3"><?php _e('No data found', 'ank_stripe'); ?></td>
                                </tr>
                                <?php 
                        endif; 
                        ?>	
                        </tbody>
                </table>
                    	<p class="submit">
				<input type="submit" class="button-primary ak-stripe-delete-plan-button" value="<?php _e('Delete Plan', 'ank_stripe'); ?>" />
                                <input type="submit" class="button-primary ak-stripe-delete-plans-button" value="<?php _e('Delete Plans', 'ank_stripe'); ?>" />
			</p>
                </form>
    <?php            
    }
function ajax_delete_stripe_plan() 
{
    if(!empty($_POST['datastring']))
    {
        $ak_delete_plan = new AK_Stripe_Payment_Functions();    
        foreach ($_POST['datastring'] as $plan_id){
            $result=$ak_delete_plan->AK_DeletePlan($plan_id);
            if($result->deleted){
                continue;
            }
        }
        

        
        //print_r (explode("&",$_POST['datastring']));
    }

    die();
}    
}
