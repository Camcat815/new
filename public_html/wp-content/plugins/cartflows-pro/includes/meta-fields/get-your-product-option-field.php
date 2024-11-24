<?php
/**
 * Get your product options field.
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script type="text/html" id="tmpl-wcf-product-option-repeater">
	<?php
		$template_data = array(
			'product_name'           => '{{original_product_name}}',
			'product_id'             => '{{original_product_id}}',
			'key'                    => '{{key}}',
			'input_product_name'     => '{{input_product_name}}',
			'input_subtext'          => '{{input_subtext}}',
			'input_enable_highlight' => '{{input_enable_highlight}}',
			'input_highlight_text'   => '{{input_highlight_text}}',
			'unique_id'              => '{{unique_id}}',
		);
		echo $this->generate_your_product_option_field_html( '{{unique_id}}', $template_data );
		?>
</script>

<div class="wcf-field-row field-wcf-products-fields">
<?php

$product_options_html = '';
$checkout_products    = wcf_pro()->utils->get_selected_product_options_data( '', $field_data['value'] );

?>
<ul id='wcf-product-options-fields' class='product-options-fields' >
<?php
if ( empty( $checkout_products ) ) {
	echo __( 'No products are selected', 'cartflows-pro' );
} else {

	foreach ( $checkout_products as $key => $value ) {

		if ( ! isset( $value['product'] ) || empty( $value['product'] ) ) {
			return;
		}

		$product = wc_get_product( $value['product'] );

		if ( ! is_object( $product ) ) {
			continue;
		}

		$product_id   = $product->get_id();
		$product_name = $product->get_name();
		$unique_id    = isset( $value['unique_id'] ) ? $value['unique_id'] : '';

		$selected_data = array(
			'product_name'           => $product_name,
			'product_id'             => $product_id,
			'key'                    => $key,
			'input_product_name'     => isset( $value['product_name'] ) ? $value['product_name'] : $product_name,
			'input_subtext'          => isset( $value['product_subtext'] ) ? $value['product_subtext'] : '',
			'input_enable_highlight' => isset( $value['enable_highlight'] ) ? $value['enable_highlight'] : '',
			'input_highlight_text'   => isset( $value['highlight_text'] ) ? $value['highlight_text'] : '',
			'unique_id'              => $unique_id,
		);

		$product_options_html .= $this->generate_your_product_option_field_html( $unique_id, $selected_data );
	}

	echo $product_options_html;
}
?>

</ul>
</div>
