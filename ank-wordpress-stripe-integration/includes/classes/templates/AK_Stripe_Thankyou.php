<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();


ak_stripe_start_template_wrapper();
?>


<div class="row">

    <div class="col-md-12 col-xs-12 main-column">
        <h1 class="entry-title">Payment Success</h1>
        <div class="alert alert-success" role="alert">
            <?php
            echo ("Congratulations. Your payment went through!");
            ?>
        </div>

        <?php
        echo "Here's what you purchased:";
        echo "<br>";
        echo get_post_meta($_GET['form-id'], 'ak-stripe-form-description', true);
        echo "<br>";
        if (get_post_meta($_GET['form-id'], 'ak-stripe-form-name', true)) {
            echo wpautop("From:" . get_post_meta($_GET['form-id'], 'ak-stripe-form-name', true));
        }


        $ak_stripe_retreive_charge = new AK_Stripe_Wrapper();
        $ak_stripe_retrieve_charge_return = $ak_stripe_retreive_charge->AK_RetrieveCharge($_GET['chargeid']);
        $ak_stripe_payment_amount = $ak_stripe_retrieve_charge_return['amount'] / 100;
        $ak_stripe_payment_currency = $ak_stripe_retrieve_charge_return['currency'];
        $ak_stripe_transaction_id = $ak_stripe_retrieve_charge_return['id'];

        echo wpautop("Total Paid: " . $ak_stripe_payment_amount . " " . $ak_stripe_payment_currency);
        echo wpautop("Your transaction ID is: " . $ak_stripe_transaction_id);
        ?>

    </div>

</div>
<?php
ak_stripe_end_template_wrapper();
get_sidebar();
get_footer();
