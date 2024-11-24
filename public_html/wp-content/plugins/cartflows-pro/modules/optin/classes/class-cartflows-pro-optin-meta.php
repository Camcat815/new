<?php
/**
 * Optin post meta
 *
 * @package cartflows
 */

/**
 * Meta Boxes setup
 */
class Cartflows_Pro_Optin_Meta {



	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Meta Option
	 *
	 * @var $meta_option
	 */
	private static $meta_option = null;

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

		add_filter( 'cartflows_optin_meta_options', array( $this, 'meta_fields' ), 10, 2 );

		add_action( 'cartflows_optin_custom_fields_tab_content', array( $this, 'tab_content_custom_fields' ), 10, 2 );
		/** Action
		add_action( 'cartflows_checkout_style_tab_content', array( $this, 'tab_style_product_bump' ), 10, 2 );.
		*/
	}

	/**
	 * Order Bump meta fields
	 *
	 * @param array $fields checkout fields.
	 * @param int   $post_id post ID.
	 */
	public function meta_fields( $fields, $post_id ) {

		if ( ! cartflows_pro_is_active_license() && is_admin() ) {
			return $fields;
		}

		/* Custom Fields Options*/
		$fields['wcf-optin-enable-custom-fields'] = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-optin-fields-billing'] = array(
			'default'  => Cartflows_Pro_Helper::get_optin_default_fields(),
			'sanitize' => 'FILTER_CARTFLOWS_PRO_OPTIN_FIELDS',
		);

		return $fields;
	}


	/**
	 * Tab Content Custom Fields.
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	public function tab_content_custom_fields( $options, $post_id ) {

		if ( ! cartflows_pro_is_active_license() ) {

			echo wcf()->meta->get_description_field(
				array(
					'name'    => 'wcf-upgrade-to-pro',
					/* translators: %s: link */
					'content' => '<i>' . sprintf( esc_html__( 'Activate %1$sCartFlows Pro%2$s license to access Custom Fields feature.', 'cartflows-pro' ), '<a href="' . CARTFLOWS_PRO_LICENSE_URL . '" target="_blank">', '</a>' ) . '</i>',
				)
			);

			return;
		}

		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-optin-enable-custom-fields',
				'value' => $options['wcf-optin-enable-custom-fields'],
				'after' => __( 'Enable Custom Field Editor', 'cartflows-pro' ),
			)
		);

		$this->tab_custom_fields_editor( $options, $post_id );
	}


	/**
	 * Fetch default width of checkout fields by key.
	 *
	 * @param string $field_key field key.
	 * @return int
	 */
	public function get_default_optin_field_width( $field_key ) {

		$default_width = 100;
		switch ( $field_key ) {
			case 'billing_first_name':
			case 'billing_last_name':
				$default_width = 50;
				break;
			default:
				$default_width = 100;
				break;
		}

		return $default_width;
	}

	/**
	 * Tab Custom Fields Options
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	public function tab_custom_fields_editor( $options, $post_id ) {

		echo '<div class="wcf-optin-custom-fields-editor">';
			/*Display Billing Checkout Custom Fields Box*/
			echo wcf()->meta->get_section(
				array(
					'label' => __( 'Form Fields', 'cartflows-pro' ),
				)
			);

			$all_billing_fields = '';

			$ordered_billing_fields = wcf()->options->get_optin_meta_value( $post_id, 'wcf-optin-fields-billing' );

		if ( isset( $ordered_billing_fields ) && ! empty( $ordered_billing_fields ) ) {
			$billing_fields = $ordered_billing_fields;

		} else {
			$billing_fields = Cartflows_Pro_Helper::get_optin_fields( 'billing', $post_id );
		}

			echo "<ul id='wcf-billing-field-sortable' class='billing-field-sortable wcf-field-row' >";
			$i = 0;

		foreach ( $billing_fields as $key => $value ) {

			$field_args = $this->prepare_field_arguments( $key, $value, $post_id, 'billing' );

			$all_billing_fields .= "<li class='wcf-field-item-edit-inactive wcf-field-item'>";

			$all_billing_fields .= $this->get_field_html( $field_args, $options, 'wcf-optin-fields-billing[' . $key . ']' );

			$all_billing_fields .= '</li>';
		}

			echo $all_billing_fields;

			echo '</ul>';

		echo '</div>';

		echo '<div style="clear: both;"></div>';

		echo '<div class="wcf-custom-field-box">';

			echo wcf_pro()->meta->get_pro_checkout_field_repeater(
				array(
					'saved_name' => 'wcf-optin-fields-',
				)
			);
		echo '</div>';
	}


	/**
	 * Prepare HTML data for billing and shipping fields.
	 *
	 * @param string  $field checkout field key.
	 * @param string  $field_data checkout field object.
	 * @param integer $post_id chcekout post id.
	 * @param string  $type checkout field type.
	 * @return array
	 */
	public function prepare_field_arguments( $field, $field_data, $post_id, $type ) {

		$field_name = '';
		if ( isset( $field_data['label'] ) ) {
			$field_name = $field_data['label'];
		}

		if ( isset( $field_data['width'] ) ) {
			$width = $field_data['width'];
		} else {
			$width = $this->get_default_optin_field_width( $field );
		}

		if ( isset( $field_data['enabled'] ) ) {
			$is_enabled = true === $field_data['enabled'] ? 'yes' : 'no';
		} else {
			$is_enabled = 'yes';
		}

		$field_args = array(
			'type'        => ( isset( $field_data['type'] ) && ! empty( $field_data['type'] ) ) ? $field_data['type'] : '',
			'label'       => $field_name,
			'name'        => 'wcf-' . $field,
			'placeholder' => isset( $field_data['placeholder'] ) ? $field_data['placeholder'] : '',
			'width'       => $width,
			'enabled'     => $is_enabled,
			'after'       => 'Enable',
			'section'     => $type,
			'default'     => isset( $field_data['default'] ) ? $field_data['default'] : '',
			'required'    => ( isset( $field_data['required'] ) && true == $field_data['required'] ) ? 'yes' : 'no',
			'optimized'   => ( isset( $field_data['optimized'] ) && true == $field_data['optimized'] ) ? 'yes' : 'no',
			'options'     => ( isset( $field_data['options'] ) && ! empty( $field_data['options'] ) ) ? implode( ',', $field_data['options'] ) : '',
		);

		if ( 'billing' === $type ) {
			if ( isset( $field_data['custom'] ) && $field_data['custom'] ) {
				$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="billing" data-key="' . $field . '">';
				$field_args['after_html'] .= '<a class="wcf-pro-custom-field-remove wp-ui-text-notification">' . __( 'Remove', 'cartflows-pro' ) . '</a>';
				$field_args['after_html'] .= '</span>';
			}
		}

		return $field_args;
	}

	/**
	 * Get field html.
	 *
	 * @param array  $field_args field arguments.
	 * @param array  $options options.
	 * @param string $key checkout key.
	 * @return string
	 */
	public function get_field_html( $field_args, $options, $key ) {

		$is_checkbox = false;
		$is_require  = false;
		$is_select   = false;
		$display     = 'none';

		if ( 'checkbox' == $field_args['type'] ) {
			$is_checkbox = true;
		}

		if ( 'yes' == $field_args['required'] ) {
			$is_require = true;
		}

		if ( 'yes' == $field_args['optimized'] ) {
			$is_optimized = true;
		}

		if ( 'select' == $field_args['type'] ) {
			$is_select = true;
			$display   = 'block';
		}

		/** $field_markup = wcf()->meta->get_only_checkbox_field( $field_args ); */
		ob_start();
		?>
		<div class="wcf-field-item-bar 
		<?php
		if ( 'no' == $field_args['enabled'] ) {
			echo 'disable';
		}
		?>
		">
			<div class="wcf-field-item-handle ui-sortable-handle">
				<label class="dashicons 
				<?php
				if ( 'no' == $field_args['enabled'] ) {
					echo 'dashicons-hidden';
				} else {
					echo 'dashicons-visibility';
				}
				?>
				" for="<?php echo $key . '[enabled]'; ?>"></label>
				<span class="item-title">
					<span class="wcf-field-item-title"><?php echo $field_args['label']; ?> 
					<?php
					if ( $is_require ) {
						echo '<i>*</i>';
					}
					?>
					</span>
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
			<div class="wcf-field-item-settings-checkbox">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Enable this field', 'cartflows-pro' ),
							'name'  => $key . '[enabled]',
							'value' => $field_args['enabled'],
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-row-width">
				<?php
					echo wcf()->meta->get_select_field(
						array(
							'label'   => __( 'Field Width', 'cartflows-pro' ),
							'name'    => $key . '[width]',
							'value'   => $field_args['width'],
							'options' => array(
								'33'  => __( '33%', 'cartflows-pro' ),
								'50'  => __( '50%', 'cartflows-pro' ),
								'100' => __( '100%', 'cartflows-pro' ),
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
							'name'  => $key . '[label]',
							'value' => $field_args['label'],
						)
					);

				?>
			</div>

			<div class="wcf-field-item-settings-select-options" style="display:
			<?php
			if ( isset( $display ) ) {
				print $display;
			}
			?>
			;">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Options', 'cartflows-pro' ),
							'name'  => $key . '[options]',
							'value' => $field_args['options'],
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
							'name'    => $key . '[default]',
							'value'   => $field_args['default'],
							'options' => array(
								'1' => __( 'Checked', 'cartflows-pro' ),
								'0' => __( 'Un-Checked', 'cartflows-pro' ),
							),
						)
					);
				} else {
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Default', 'cartflows-pro' ),
							'name'  => $key . '[default]',
							'value' => $field_args['default'],
						)
					);
				}
				?>
			</div>
			<div class="wcf-field-item-settings-placeholder" 
			<?php
			if ( true == $is_checkbox || true == $is_select ) {
				?>
			<?php } ?> >
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Placeholder', 'cartflows-pro' ),
							'name'  => $key . '[placeholder]',
							'value' => $field_args['placeholder'],
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-require">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Required', 'cartflows-pro' ),
							'name'  => $key . '[required]',
							'value' => $field_args['required'],
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-optimized">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Collapsible', 'cartflows-pro' ),
							'name'  => $key . '[optimized]',
							'value' => $field_args['optimized'],
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
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Optin_Meta::get_instance();
