<?php
/**
 * Analytics popup.
 *
 * @package cartflows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div id="wcf-analytics-popup-wrap" class="wcf-templates-popup-overlay">
	<div class="wcf-analytics-reports-content">
		<span class="spinner"></span>
		<div class="wcf-analytics-header">
			<div class="wcf-template-logo-wrap">
			<span class="wcf-cartflows-logo-img">
				<span class="cartflows-icon"></span>
			</span>
			</div>
			<div class="wcf-analytics-report-title">
				<?php esc_html_e( 'Analytics Report', 'cartflows-pro' ); ?>
			</div>
			<div class="wcf-popup-close-wrap">
				<span class="close-icon"><span class="wcf-cartflow-icons dashicons dashicons-no"></span></span>
			</div>
		</div>
		<div class="wcf-analytics-reports-wrap">
		</div>
	</div>
</div>
