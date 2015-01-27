<?php
function pippin_stripe_payment_form() {
	if(isset($_GET['payment']) && $_GET['payment'] == 'paid') {
            global $test;
            print_r ($test);
		echo '<p class="success">' . __('Thank you for your payment. Your reciept number is', 'pippin_stripe') . '</p>';
                
                
	} else { ?>
		<h2><?php _e('Submit a payment of $10', 'pippin_stripe'); ?></h2>
		<form action="" method="POST" id="stripe-payment-form">
			<div class="form-row">
				<label><?php _e('Card Number', 'pippin_stripe'); ?></label>
				<input type="text" size="20" autocomplete="off" class="card-number"/>
			</div>
			<div class="form-row">
				<label><?php _e('CVC', 'pippin_stripe'); ?></label>
				<input type="text" size="4" autocomplete="off" class="card-cvc"/>
			</div>
			<div class="form-row">
				<label><?php _e('Expiration (MM/YYYY)', 'pippin_stripe'); ?></label>
				<input type="text" size="2" class="card-expiry-month"/>
				<span> / </span>
				<input type="text" size="4" class="card-expiry-year"/>
			</div>
			<input type="hidden" name="action" value="stripe"/>
			<input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
			<input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
			<button type="submit" id="stripe-submit"><?php _e('Submit Payment', 'pippin_stripe'); ?></button>
		</form>
		<div class="payment-errors"></div>
		<?php
	}
}
add_shortcode('payment_form', 'pippin_stripe_payment_form');