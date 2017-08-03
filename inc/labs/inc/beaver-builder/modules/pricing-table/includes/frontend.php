<?php
/**
 * LifterLMS Course/Membership Pricing Table Module HTML
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

$product_id = $module->get_product_id( $settings );
if ( ! $product_id ) {
	return;
}
?>

<div class="llms-lab-pricing-table">
	<?php do_action( 'llms_lab_bb_before_pricing_table', $product_id ); ?>
	[lifterlms_pricing_table product="<?php echo $product_id; ?>"]
	<?php do_action( 'llms_lab_bb_after_pricing_table', $product_id ); ?>
</div>
