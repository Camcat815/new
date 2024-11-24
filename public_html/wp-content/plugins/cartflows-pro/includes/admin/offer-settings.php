<?php
/**
 * General settings
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$settings = Cartflows_Pro_Helper::get_offer_global_settings();
?>

<!-- Upsell Offer orders -->
<div class="offer-global-settings-form postbox">
	<h2 class="hndle wcf-normal-cusror ui-sortable-handle">
		<span><?php esc_html_e( 'Offers Global Settings', 'cartflows-pro' ); ?></span>
	</h2>
	<div class="inside">
		<form method="post" class="wrap wcf-clear" action="" >
			<div class="form-wrap">
				<?php

					echo Cartflows_Admin_Fields::radio_field(
						array(
							'title'   => __( 'Upsell/Downsell Orders', 'cartflows-pro' ),
							'id'      => 'wcf_separate_offer_orders',
							'name'    => '_cartflows_offer_global_settings[separate_offer_orders]',
							'value'   => $settings['separate_offer_orders'],
							'options' => array(
								'separate' => array(
									'label'       => __( 'Create a new child order (Recommended)', 'cartflows-pro' ),
									'description' => __( 'This option create a new order for all accepted upsell/downsell offers. Main order will be parent order for them.', 'cartflows-pro' ),
								),
								'merge'    => array(
									'label'       => __( 'Add to main order', 'cartflows-pro' ),
									'description' => __( 'This option will merge all accepted upsell/downsell offers into main order.', 'cartflows-pro' ),
								),
							),
						)
					);
					?>
			</div>
			<?php submit_button( __( 'Save Changes', 'cartflows-pro' ), 'cartflows-offer-global-settings-save-btn button-primary button', 'submit', false ); ?>
			<?php wp_nonce_field( 'cartflows-offer-global-settings', 'cartflows-offer-global-settings-nonce' ); ?>
		</form>
		</div>
</div>
<!-- Upsell Offer Orders -->

