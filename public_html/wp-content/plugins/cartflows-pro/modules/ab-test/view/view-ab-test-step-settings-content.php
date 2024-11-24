<?php
/**
 * Ab test settings popup
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wcf-content-wrap wcf-ab-test-content-wrap wcf-content-wrap-<?php echo $data['id']; ?>" style="display: none;">
	<div class="wcf-ab-settings-content" data-id="<?php echo $data['id']; ?>">
		<h3><?php echo __( 'Traffic', 'cartflows-pro' ); ?></h3>
		<?php foreach ( $ab_test_variations as $ab_in => $ab_variation ) { ?>

			<div class="wcf-ab-settings-fields wcf-traffic-field">
				<div class="wcf-step-name" title="<?php echo esc_attr( get_the_title( $ab_variation['id'] ) ); ?>">
					<?php echo wp_trim_words( get_the_title( $ab_variation['id'] ), 4 ); ?>
				</div>
				<div class="wcf-traffic-slider-wrap" data-variation-id="<?php echo $ab_variation['id']; ?>">
					<div class="wcf-traffic-range wcf-traffic-range-<?php echo $ab_variation['id']; ?>">
						<input type="range" min="0" max="100" value="<?php echo $ab_variation['traffic']; ?>" onchange="">
					</div>
					<div class="wcf-traffic-value wcf-traffic-value-<?php echo $ab_variation['id']; ?>">
						<input type="number" name="<?php echo 'wcf_ab_settings[traffic][' . $ab_variation['id'] . '][value]'; ?>" value="<?php echo $ab_variation['traffic']; ?>"> %
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
