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

<div class="wcf-ab-test-settings wcf-ab-test-settings-overlay wcf-popup-overlay" style="display: none;">
	<div class="wcf-ab-test-popup-content wcf-popup-content">
		<div class="wcf-ab-settings-header">
			<div class="wcf-template-logo-wrap">
				<span class="wcf-cartflows-logo-img">
					<span class="cartflows-logo-icon"></span>
				</span>
			</div>

			<span class="wcf-cartflows-title"><?php esc_html_e( 'A/B Test Settings', 'cartflows-pro' ); ?></span>

			<div class="wcf-popup-close-wrap">
				<span class="close-icon"><span class="wcf-cartflow-icons dashicons dashicons-no"></span></span>
			</div>
		</div>

		<div class="wcf-content-wrap">
		</div>

		<div class="wcf-ab-settings-footer">
			<div class="wcf-popup-actions-wrap">
				<a href="#" class="wcf-ab-test-cancel button button-large"><?php esc_html_e( 'Cancel', 'cartflows-pro' ); ?></a>
				<a href="#" class="wcf-ab-test-save button button-primary button-large"><?php esc_html_e( 'Save', 'cartflows-pro' ); ?></a>
			</div>
		</div>
	</div>
</div>
