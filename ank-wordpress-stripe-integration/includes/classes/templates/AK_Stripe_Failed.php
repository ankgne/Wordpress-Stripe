<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();


do_action('ak_stripe_fail_before_main_content');
?>


<div class="row">

    <div class="col-md-12 col-xs-12 main-column">
        <h1 class="entry-title">Payment Failed</h1>
        <?php do_action('ak_stripe_fail_main_content'); ?>
    </div>

</div>
<?php
do_action('ak_stripe_fail_after_main_content');
get_sidebar();
get_footer();
