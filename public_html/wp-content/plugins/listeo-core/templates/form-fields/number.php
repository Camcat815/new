<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;

$default = (isset($field['default'])) ? $field['default'] : '' ;
?>
<div class="select-input disabled-first-option">
	<?php if ( isset( $field['unit'] ) ) { ?> <i class="data-unit"><?php echo $field['unit']; ?></i><?php } ?>
<input type="number" 
	class="input-text" 

	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
	<?php if ( isset( $field['autocomplete'] ) && false === $field['autocomplete'] ) { echo ' autocomplete="off"'; } ?> 
	id="<?php echo esc_attr( $key ); ?>" 
	step="any" 
	min="0"
	placeholder="<?php if(isset( $field['placeholder'] )) { echo esc_attr( $field['placeholder'] ); } ?>" 
	value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : $default; ?>" 
	maxlength="<?php echo ! empty( $field['maxlength'] ) ? $field['maxlength'] : ''; ?>" 
	<?php if(isset($field['atts'])){
		foreach ($field['atts'] as $key => $value) {
			echo $key.'="'.$value.'"';
		}
	} ?>
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> 
	<?php if ( isset( $field['unit'] ) ) echo 'data-unit="'.$field['unit'].'"'; ?> 

	/>
	</div>