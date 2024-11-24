<?php
/**
 * Generate your product options html.
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<li class='wcf-field-item-edit-inactive wcf-field-item'>
	<div class='wcf-product-option-fields' data-product-id='<?php echo $input_data['product_id']; ?>'>
		<div class="wcf-product-field-item-bar">
			<div class="wcf-product-field-item-handle">
				<span class="item-title">
					<span class="wcf-field-item-title"><?php echo $input_data['product_name']; ?></span>
				</span>
				<span class="item-controls">
					<a class="item-edit" id="edit-64" href="javascript:void(0);" aria-label="My account. Menu item 1 of 5."><span class="screen-reader-text">Edit</span></a>
				</span>
			</div>
		</div>

		<div class="wcf-product-field-item-settings" style="display: none;">
			<div class="wcf-field-product-title-field">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Product Name', 'cartflows-pro' ),
							'name'  => 'wcf-product-options-data[' . $id . '][product_name]',
							'value' => $input_data['input_product_name'],
							'help'  => esc_html__( 'It will change the product name on the checkout page.', 'cartflows-pro' ),
						)
					);
					echo wcf()->meta->get_description_field(
						array(
							'name'    => '',
							/* translators: %s: link */
							'content' => '<i>' . sprintf( esc_html__( 'Use {{product_name}} and {{quantity}} to dynamically fetch respective product details.', 'cartflows-pro' ), '<a href="https://cartflows.com/" target="_blank">', '</a>' ) . '</i>',
						)
					);
					?>
			</div>

			<div class="wcf-field-subtext-field">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Subtext', 'cartflows-pro' ),
							'name'  => 'wcf-product-options-data[' . $id . '][product_subtext]',
							'value' => $input_data['input_subtext'],
							'help'  => esc_html__( 'It will add the text below the product name on checkout.', 'cartflows-pro' ),
						)
					);
					echo wcf()->meta->get_description_field(
						array(
							'name'    => '',
							/* translators: %s: link */
							'content' => '<i>' . sprintf( esc_html__( 'Use {{quantity}}, {{discount_value}}, {{discount_percent}} to dynamically fetch respective product details.', 'cartflows-pro' ), '<a href="https://cartflows.com/" target="_blank">', '</a>' ) . '</i>',
						)
					);
					?>
			</div>

			<div class="wcf-field-enable-highlight-option-field">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Enable Highlight', 'cartflows-pro' ),
							'name'  => 'wcf-product-options-data[' . $id . '][enable_highlight]',
							'value' => $input_data['input_enable_highlight'],
							'help'  => esc_html__( 'It will Highlight the product on checkout page with Highlight string.', 'cartflows-pro' ),
						)
					);
					?>
			</div>

			<div class="wcf-field-highlight-text-field">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Highlight Text', 'cartflows-pro' ),
							'name'  => 'wcf-product-options-data[' . $id . '][highlight_text]',
							'value' => $input_data['input_highlight_text'],
						)
					);
					?>
			</div>

			<div class="wcf-product-option-unique-id-field">
				<input name="wcf-product-options-data[<?php echo $id; ?>][unique_id]" type="hidden" class="wcf-product-options-unique-id" value="<?php echo $input_data['unique_id']; ?>" >
			</div>
		</div>
	</div>
</li>
