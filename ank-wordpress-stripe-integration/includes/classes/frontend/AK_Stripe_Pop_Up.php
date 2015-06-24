<!doctype html>

<html lang="en">

    <head>

        <meta charset="utf-8">
        <title><?php _e('Stripe Payment', 'wp-stripe'); ?></title>
        <link rel="stylesheet" href="<?php echo esc_url(STRIPE_BASE_URL) . 'includes/css/ak-stripe-payment-form.css'; ?>">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <script type="text/javascript">
            //<![CDATA[
            var stripe_vars = {"publishable_key": '<?php  echo esc_js(AK_STRIPE_PUBLISHABLE_KEY) ?>', "ajaxurl": '<?php echo admin_url('admin-ajax.php'); ?>'};
            //]]>;
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" ></script>
        <script src="https://js.stripe.com/v2/"></script>
        <script src="<?php echo esc_js(STRIPE_BASE_URL) . 'includes/js/AK-stripe-processing.js'; ?>" ></script>
        <script src="<?php echo esc_js(STRIPE_BASE_URL) . 'includes/js/jquery.payment.js'; ?>" ></script>

    </head>

    <body>
        <div class="ak-stripe-popup-content">
            <?php
            $ak_stripe_payment_popup_form = new AK_Stripe_Payment_Form();
            echo $ak_stripe_payment_popup_form->AK_stripe_payment_form_page();
            ?>
        </div>   

    </body>

</html>