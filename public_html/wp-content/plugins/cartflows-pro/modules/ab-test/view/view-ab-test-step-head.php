<?php
/**
 * Ab test step head
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( $ab_test_variations as $ab_in => $ab_variation ) {
	$ab_test_variations[ $ab_in ]['title'] = get_the_title( $ab_variation['id'] );
}

$json_ab_variations_data = htmlspecialchars( wp_json_encode( $ab_test_variations ), ENT_COMPAT, 'utf-8' );
?>

<div class="wcf-ab-test-head">
	<div class="wcf-step-left-content">
		<span class="dashicons dashicons-menu"></span>
		<span class="wcf-ab-test-title"><?php echo __( 'Split Test', 'cartflows-pro' ) . ' - ' . get_the_title( $data['id'] ); ?></span>
	</div>
	<div class="wcf-steps-action-buttons">
		<a href="#" class="wcf-start-split-test wcf-action-button <?php echo $start_ab_test ? 'wcf-stop-ab-test' : 'wcf-start-ab-test'; ?>" title="<?php echo $start_ab_test_text; ?>" target="_blank" data-id="<?php echo $data['id']; ?>"><span><?php echo $start_ab_test_text; ?></span></a>
		<a href="#" class="wcf-settings-split-test wcf-action-button wp-ui-text-highlight" title="<?php echo __( 'Split Test Settings', 'cartflows-pro' ); ?>" target="_blank" data-id="<?php echo $data['id']; ?>" data-json-ab-variation="<?php echo esc_attr( $json_ab_variations_data ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
	</div>
	<?php require CARTFLOWS_PRO_AB_TEST_DIR . 'view/view-ab-test-step-settings-content.php'; ?>
</div>

