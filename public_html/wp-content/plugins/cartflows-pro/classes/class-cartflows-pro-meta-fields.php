<?php
/**
 * Meta Fields.
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cartflows_Pro_Meta_Fields.
 */
class Cartflows_Pro_Meta_Fields {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wp_ajax_wcf_pro_add_custom_checkout_field', array( $this, 'add_pro_checkout_custom_field' ) );

		add_action( 'wp_ajax_wcf_pro_delete_custom_checkout_field', array( $this, 'delete_checkout_custom_field' ) );

		add_filter( 'cartflows_admin_js_localize', array( $this, 'localize_vars' ) );

	}

	/**
	 * Localize variables in admin
	 *
	 * @param array $vars variables.
	 */
	public function localize_vars( $vars ) {

		$ajax_actions = array(
			'wcf_pro_add_custom_checkout_field',
			'wcf_pro_delete_custom_checkout_field',
		);

		foreach ( $ajax_actions as $action ) {

			$vars[ $action . '_nonce' ] = wp_create_nonce( str_replace( '_', '-', $action ) );
		}

		return $vars;
	}


	/**
	 * Get Pro Checkout Field Repeater.
	 *
	 * @param array $field_data field data.
	 * @return string
	 */
	public function get_pro_checkout_field_repeater( $field_data ) {

		$field_name = isset( $field_data['saved_name'] ) ? $field_data['saved_name'] : 'wcf_field_order_';

		$value = array();

		$value[0] = array(
			'add_to' => '',
			'type'   => '',
			'label'  => '',
			'name'   => '',
		);

		$field_content = '';

		$field_content .= '<div class="wcf-field-row">';
		/**
		$field_content .= '<div class="wcf-field-row-heading">';
		$field_content .= '<label>' . esc_html( $field_data['label'] ) . '</label>';
		$field_content .= '</div>';
		*/
		$field_content .= '<div class="wcf-field-row-content">';
		$field_content .= '<div class="wcf-cpf-wrap">';

		foreach ( $value as $p_key => $p_data ) {
			$field_content .= '<div class="wcf-cpf-row" data-key="' . $p_key . '">';
			$field_content .= '<div class="wcf-cpf-row-header">';
			$field_content .= '<span class="wcf-cpf-row-title">Add New Custom Field</span>';
			$field_content .= '</div>';

			$field_content .= '<div class="wcf-cpf-row-standard-fields">';

			/* Add To */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-add_to">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Add to</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<select name="wcf-checkout-custom-fields[' . $p_key . '][add_to]" class="wcf-cpf-add_to">';
			$field_content .= '<option value="billing">Billing</option>';
			$field_content .= '<option value="shipping">Shipping</option>';
			$field_content .= '</select>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Type */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-type">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Type</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<select name="wcf-checkout-custom-fields[' . $p_key . '][type]" class="wcf-cpf-type">';
			$field_content .= '<option value="text">Text</option>';
			$field_content .= '<option value="textarea">Textarea</option>';
			$field_content .= '<option value="select">Select</option>';
			$field_content .= '<option value="checkbox">Checkbox</option>';
			$field_content .= '<option value="hidden">Hidden</option>';
			$field_content .= '</select>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Label */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-label">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Label <i>*</i></span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<input type="text" value="" name="wcf-checkout-custom-fields[' . $p_key . '][label]" class="wcf-cpf-label">';
			$field_content .= '<span id="wcf-cpf-label-error-msg"></span>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Default */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-default">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Default</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<input type="text" value="" name="wcf-checkout-custom-fields[' . $p_key . '][default]" class="wcf-cpf-default">';
			$field_content .= '<span id="wcf-cpf-default-error-msg"></span>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Placeholder */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-placeholder">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Placeholder</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<input type="text" value="" name="wcf-checkout-custom-fields[' . $p_key . '][placeholder]" class="wcf-cpf-placeholder">';
			$field_content .= '<span id="wcf-cpf-placeholder-error-msg"></span>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Options */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-options">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Options <i>*</i></span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<textarea value="" name="wcf-checkout-custom-fields[' . $p_key . '][label]" class="wcf-cpf-options" placeholder="Enter your options separated by comma."></textarea>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Width */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-width">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Width</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<select name="wcf-checkout-custom-fields[' . $p_key . '][width]" class="wcf-cpf-width">';
			$field_content .= '<option value="33">33%</option>';
			$field_content .= '<option value="50">50%</option>';
			$field_content .= '<option value="100" selected>100%</option>';
			$field_content .= '</select>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Required */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-required">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Required</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<input type="hidden" value="no" name="wcf-checkout-custom-fields[' . $p_key . '][required]" class="wcf-cpf-required">';
			$field_content .= '<input type="checkbox" value="yes" name="wcf-checkout-custom-fields[' . $p_key . '][required]" class="wcf-cpf-required">';
			$field_content .= '<span id="wcf-cpf-required-error-msg"></span>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			/* Optimized */
			$field_content .= '<div class="wcf-cpf-fields wcf-cpf-optimized">';
			$field_content .= '<span class="wcf-cpf-row-setting-label">Collapsible</span>';
			$field_content .= '<span class="wcf-cpf-row-setting-field">';
			$field_content .= '<input type="hidden" value="no" name="wcf-checkout-custom-fields[' . $p_key . '][optimized]" class="wcf-cpf-optimized">';
			$field_content .= '<input type="checkbox" value="yes" name="wcf-checkout-custom-fields[' . $p_key . '][optimized]" class="wcf-cpf-optimized">';
			$field_content .= '<span id="wcf-cpf-optimized-error-msg"></span>';
			$field_content .= '</span>';
			$field_content .= '</div>';

			$field_content .= '</div>';
			$field_content .= '</div>';
		}

		/* Add New Custom Field */
		$field_content .= '<div class="wcf-cpf-add-row">';
		$field_content .= '<div class="wcf-cpf-add-wrap">';
		$field_content .= '<button class="button button-secondary wcf-pro-custom-field-add" data-name="' . $field_name . '">' . __( 'Add New Field', 'cartflows-pro' ) . '</button>';
		$field_content .= '</div>';
		$field_content .= '</div>';
		/* End Add new custom field */

		$field_content .= '</div>';
		$field_content .= '</div>';
		$field_content .= '</div>';

		return $field_content;
	}

	/**
	 * [add_checkout_custom_field description]
	 *
	 * @hook wcf_add_checkout_custom_field
	 */
	public function add_pro_checkout_custom_field() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_ajax_referer( 'wcf-pro-add-custom-checkout-field', 'security' );

		$post_id       = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$add_to        = isset( $_POST['add_to'] ) ? sanitize_text_field( wp_unslash( $_POST['add_to'] ) ) : '';
		$type          = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$options       = isset( $_POST['options'] ) ? sanitize_text_field( wp_unslash( $_POST['options'] ) ) : '';
		$label         = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
		$name          = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( str_replace( ' ', '_', $_POST['label'] ) ) ) : ''; //phpcs:ignore
		$placeholder   = isset( $_POST['placeholder'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder'] ) ) : '';
		$optimized     = isset( $_POST['optimized'] ) ? sanitize_text_field( wp_unslash( $_POST['optimized'] ) ) : '';
		$width         = isset( $_POST['width'] ) ? sanitize_text_field( wp_unslash( $_POST['width'] ) ) : '';
		$default_value = isset( $_POST['default'] ) ? sanitize_text_field( wp_unslash( $_POST['default'] ) ) : '';
		$is_required   = isset( $_POST['required'] ) ? sanitize_text_field( wp_unslash( $_POST['required'] ) ) : '';

		$save_field_name = isset( $_POST['save_field_name'] ) ? sanitize_text_field( wp_unslash( $_POST['save_field_name'] ) ) : '';

		$field_markup = '';

		if ( '' !== $name ) {

			$fields = Cartflows_Helper::get_checkout_fields( $add_to, $post_id );

			$name = $add_to . '_' . sanitize_key( $name );

			$field_data = array(
				'type'        => $type,
				'label'       => $label,
				'placeholder' => $placeholder,
				'class'       => array( 'form-row-wide' ),
				'label_class' => array(),
				'required'    => $is_required,
				'custom'      => true,
				'default'     => $default_value,
				'options'     => $options,
				'optimized'   => $optimized,
			);

			if ( 'select' === $type ) {
				$options               = explode( ',', $options );
				$field_data['options'] = array_combine( $options, $options );
			}

			Cartflows_Pro_Helper::add_checkout_field( $add_to, $name, $post_id, $field_data );

			$key  = sanitize_key( $name );
			$name = 'wcf-' . $key;

			$field_args = array(
				'type'        => $type,
				'label'       => $label,
				'name'        => $name,
				'value'       => 'yes',
				'placeholder' => $placeholder,
				'width'       => $width,
				'after'       => 'Enable',
				'section'     => $add_to,
				'default'     => $default_value,
				'required'    => $is_required,
				'options'     => $options,
				'optimized'   => $optimized,
			);

			$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="billing" data-key="' . $key . '"> ';
			$field_args['after_html'] .= '<a class="wcf-pro-custom-field-remove wp-ui-text-notification">' . __( 'Remove', 'cartflows-pro' ) . '</a>';
			$field_args['after_html'] .= '</span>';

			$field_markup .= "<li class='wcf-field-item-edit-inactive wcf-field-item ui-sortable-handle'>";
			$field_markup .= $this->get_field_html_via_ajax( $field_args, $save_field_name . $add_to . '[' . $key . ']' );
			$field_markup .= '</li>';

			if ( 'billing' === $add_to ) {
				$add_to_class = 'billing-field-sortable';
				$section      = 'billing';
			} elseif ( 'shipping' === $add_to ) {
				$add_to_class = 'shipping-field-sortable';
				$section      = 'shipping';
			}

			$data = array(
				'field_data'   => $field_data,
				'field_args'   => $field_args,
				'add_to_class' => $add_to_class,
				'markup'       => $field_markup,
				'section'      => $section,
			);

			wp_send_json( $data );
		}

		wp_send_json( false );

	}

	/**
	 * Get field html via ajax.
	 *
	 * @param array  $field_args field args.
	 * @param string $field_key field key.
	 * @return false|string
	 */
	public function get_field_html_via_ajax( $field_args, $field_key ) {

		$value = $field_args['value'];

		$is_checkbox = false;
		$is_require  = false;
		$is_select   = false;

		$display = 'none';

		$field_content = '';

		if ( isset( $field_args['before'] ) ) {
			$field_content .= '<span>' . $field_args['before'] . '</span>';
		}

		if ( isset( $field_args['after'] ) ) {
			$field_content .= $field_args['after'];
		}

		$type        = isset( $field_args['type'] ) ? $field_args['type'] : '';
		$label       = isset( $field_args['label'] ) ? $field_args['label'] : '';
		$help        = isset( $field_args['help'] ) ? $field_args['help'] : '';
		$after_html  = isset( $field_args['after_html'] ) ? $field_args['after_html'] : '';
		$name        = isset( $field_args['name'] ) ? $field_args['name'] : '';
		$default     = isset( $field_args['default'] ) ? $field_args['default'] : '';
		$required    = isset( $field_args['required'] ) ? $field_args['required'] : '';
		$options     = isset( $field_args['options'] ) ? $field_args['options'] : '';
		$width       = isset( $field_args['width'] ) ? $field_args['width'] : '';
		$placeholder = isset( $field_args['placeholder'] ) ? $field_args['placeholder'] : '';
		$optimized   = isset( $field_args['optimized'] ) ? $field_args['optimized'] : '';
		$name_class  = 'field-' . $field_args['name'];

		if ( isset( $options ) && ! empty( $options ) ) {
			$options = implode( ',', $options );
		} else {
			$options = '';
		}

		if ( 'yes' == $required ) {
			$is_require = true;
		}

		if ( 'checkbox' == $type ) {
			$is_checkbox = true;
		}

		if ( 'select' == $type ) {
			$is_select = true;
			$display   = 'block';
		}

		/** $field_markup = wcf()->meta->get_only_checkbox_field( $field_args ); */
		ob_start();

		?>
		<div class="wcf-field-item-bar">
			<div class="wcf-field-item-handle ui-sortable-handle">
				<label class="dashicons 
				<?php
				if ( 'no' == $value ) {
					echo 'dashicons-hidden';
				} else {
					echo 'dashicons-visibility';}
				?>
				" for="<?php echo $field_args['name']; ?>"></label>
				<span class="item-title">
					<span class="wcf-field-item-title">
					<?php
					echo $label; if ( $is_require ) {
						?>
					<i>*</i> <?php } ?></span>
					<span class="is-submenu" style="display: none;">sub item</span>
				</span>
				<span class="item-controls">
					<span class="dashicons dashicons-menu"></span>
					<span class="item-order hide-if-js">
						<a href="#" class="item-move-up" aria-label="Move up">↑</a>
						|
						<a href="#" class="item-move-down" aria-label="Move down">↓</a>
					</span>
					<a class="item-edit" id="edit-64" href="javascript:void(0);" aria-label="My account. Menu item 1 of 5."><span class="screen-reader-text">Edit</span></a>
				</span>
			</div>
		</div>
		<div class="wcf-field-item-settings">
			<div class="wcf-field-item-settings-row-width">
				<?php
				echo wcf()->meta->get_select_field(
					array(
						'label'   => __( 'Field Width', 'cartflows-pro' ),
						'name'    => $field_key . '[width]',
						'value'   => $width,
						'options' => array(
							'33'  => esc_html__( '33%', 'cartflows-pro' ),
							'50'  => esc_html__( '50%', 'cartflows-pro' ),
							'100' => esc_html__( '100%', 'cartflows-pro' ),
						),
					)
				);
				?>
			</div>

			<div class="wcf-field-item-settings-label">
				<?php
				echo wcf()->meta->get_text_field(
					array(
						'label' => __( 'Field Label', 'cartflows-pro' ),
						'name'  => $field_key . '[label]',
						'value' => $label,
					)
				);

				?>
			</div>

			<div class="wcf-field-item-select-options" style="display:
			<?php
			if ( isset( $display ) ) {
				print $display; }
			?>
			;" >
				<?php
				echo wcf()->meta->get_text_field(
					array(
						'label' => __( 'Options', 'cartflows-pro' ),
						'name'  => $field_key . '[options]',
						'value' => $options,
					)
				);

				?>
			</div>

			<div class="wcf-field-item-settings-default">
				<?php
				if ( true == $is_checkbox ) {
					echo wcf()->meta->get_select_field(
						array(
							'label'   => __( 'Default', 'cartflows-pro' ),
							'name'    => $field_key . '[default]',
							'value'   => $default,
							'options' => array(
								'1' => esc_html__( 'Checked', 'cartflows-pro' ),
								'0' => esc_html__( 'Un-Checked', 'cartflows-pro' ),
							),
						)
					);
				} else {

					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Default', 'cartflows-pro' ),
							'name'  => $field_key . '[default]',
							'value' => $default,
						)
					);
				}
				?>
			</div>

			<div class="wcf-field-item-settings-placeholder" 
			<?php
			if ( true == $is_checkbox ) {
				?>
			style="display: none;" <?php } ?> >
				<?php
				echo wcf()->meta->get_text_field(
					array(
						'label' => __( 'Placeholder', 'cartflows-pro' ),
						'name'  => $field_key . '[placeholder]',
						'value' => $placeholder,
					)
				);
				?>
			</div>

			<div class="wcf-field-item-settings-required">
				<?php
				echo wcf()->meta->get_checkbox_field(
					array(
						'label' => __( 'Required', 'cartflows-pro' ),
						'name'  => $field_key . '[required]',
						'value' => $required,
					)
				);
				?>
			</div>

			<div class="wcf-field-item-settings-optimized">
				<?php
				echo wcf()->meta->get_checkbox_field(
					array(
						'label' => __( 'Collapsible', 'cartflows-pro' ),
						'name'  => $field_key . '[optimized]',
						'value' => $optimized,
					)
				);
				?>
			</div>

			<div class="wcf-field-item-settings-checkbox">
				<?php
				echo wcf()->meta->get_checkbox_field(
					array(
						'label' => __( 'Enable this field', 'cartflows-pro' ),
						'name'  => $field_key . '[enabled]',
						'value' => $value,
					)
				);
				?>
			</div>

			<?php
			if ( isset( $field_args['after_html'] ) ) {
				?>
				<div class="wcf-field-item-settings-row-delete-cf">
					<?php echo $field_args['after_html']; ?>
				</div>
				<?php
			}
			?>
		</div>

		<?php

		return ob_get_clean();
	}


	/**
	 * Delete checkout custom fields.
	 */
	public function delete_checkout_custom_field() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_ajax_referer( 'wcf-pro-delete-custom-checkout-field', 'security' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$key     = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';

		if ( '' !== $key ) {

			Cartflows_Pro_Helper::delete_checkout_field( $type, $key, $post_id );

			wp_send_json( true );

		}

		wp_send_json( false );

	}

	/**
	 * Get optgroup fields.
	 *
	 * @param array $field_data field data.
	 * @return mixed
	 */
	public function get_optgroup_field( $field_data ) {

		$saved_value = $field_data['value'];
		$flow_id     = $field_data['data-flow-id'];
		$exclude_id  = $field_data['data-exclude-id'];

		if ( is_array( $field_data['optgroup'] ) && ! empty( $field_data['optgroup'] ) ) {

			$flow_steps    = get_post_meta( $flow_id, 'wcf-steps', true );
			$control_steps = array();

			if ( is_array( $flow_steps ) ) {
				foreach ( $flow_steps as $f_index => $f_data ) {
					$control_steps[] = intval( $f_data['id'] );
				}
			}

			$field_content        = '<select name="' . $field_data['name'] . '">';
			$cartflows_steps_args = array(
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post_type'      => 'cartflows_step',
				'post_status'    => 'publish',
				'post__not_in'   => array( $exclude_id ),
				/** 'fields'           => 'ids', */
			);
			$field_content .= '<option class="wcf_steps_option" value="" ' . selected( $saved_value, '', false ) . ' >Default</option>';
			foreach ( $field_data['optgroup'] as $optgroup_key => $optgroup_value ) {
				$cartflows_steps_args['tax_query'] = array( // phpcs:ignore
					'relation' => 'AND',
					array(
						'taxonomy' => 'cartflows_step_type',
						'field'    => 'slug',
						'terms'    => $optgroup_key,
					),
					array(
						'taxonomy' => 'cartflows_step_flow',
						'field'    => 'slug',
						'terms'    => 'flow-' . $flow_id,

					),
				);
				$cartflows_steps_query = new WP_Query( $cartflows_steps_args );
				$cartflows_steps       = $cartflows_steps_query->posts;

				if ( ! empty( $cartflows_steps ) ) {

					$field_content .= '<optgroup label="' . wp_specialchars_decode( $optgroup_value ) . '"></optgroup>';
					foreach ( $cartflows_steps as $key => $cf_step ) {

						if ( ! in_array( $cf_step->ID, $control_steps, true ) ) {
							continue;
						}

						$field_content .= '<option class="wcf_steps_option" value="' . esc_attr( $cf_step->ID ) . '" ' . selected( $saved_value, $cf_step->ID, false ) . ' >&emsp;' . esc_attr( $cf_step->post_title ) . '</option>';
					}
					$field_content .= '</optgroup>';
				}
			}
		}

		$field_content .= '</select>';

		return wcf()->meta->get_field( $field_data, $field_content );
	}

	/**
	 * Get your product option fields.
	 *
	 * @param array $field_data field data.
	 * @return mixed
	 */
	public function get_your_product_option_field( $field_data ) {

		ob_start();

		include CARTFLOWS_PRO_DIR . 'includes/meta-fields/get-your-product-option-field.php';

		return ob_get_clean();
	}

	/**
	 * Get optgroup fields.
	 *
	 * @param string $id string.
	 * @param array  $input_data selected data.
	 * @return mixed
	 */
	public function generate_your_product_option_field_html( $id, $input_data ) {

		ob_start();

		include CARTFLOWS_PRO_DIR . 'includes/meta-fields/generate-your-product-option-html.php';

		return ob_get_clean();
	}
}
