<?php
/**
 * Base Offer meta.
 *
 * @package cartflows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Meta Boxes setup
 */
class Cartflows_Pro_Base_Offer_Meta {


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
	 * Template Type
	 *
	 * @var $template_type
	 */
	private static $template_type = null;

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

		/* Init Metabox */
		add_action( 'load-post.php', array( $this, 'init_metabox' ) );
		add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
	}

	/**
	 * Init Metabox
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'setup_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	/**
	 *  Setup Metabox
	 */
	public function setup_meta_box() {

		if ( ! wcf_pro()->is_woo_active ) {
			return;
		}

		if ( _is_wcf_base_offer_type() ) {
			add_meta_box(
				'wcf-offer-settings',                // Id.
				__( 'Offer Page Settings', 'cartflows-pro' ), // Title.
				array( $this, 'markup_meta_box' ),      // Callback.
				wcf()->utils->get_step_post_type(),                 // Post_type.
				'normal',                               // Context.
				'high'                                  // Priority.
			);
		}
	}

	/**
	 * Metabox Markup
	 *
	 * @param  object $post Post object.
	 * @return void
	 */
	public function markup_meta_box( $post ) {

		wp_nonce_field( 'save-nonce-offer-step-meta', 'nonce-offer-step-meta' );

		$stored = get_post_meta( $post->ID );

		$offers_meta = self::get_meta_option( $post->ID );

		// Set stored and override defaults.
		foreach ( $stored as $key => $value ) {
			if ( array_key_exists( $key, $offers_meta ) ) {
				self::$meta_option[ $key ]['default'] = ( isset( $stored[ $key ][0] ) ) ? maybe_unserialize( $stored[ $key ][0] ) : '';
			} else {
				self::$meta_option[ $key ]['default'] = ( isset( $stored[ $key ][0] ) ) ? $stored[ $key ][0] : '';
			}
		}

		// Get defaults.
		$meta    = self::get_meta_option( $post->ID );
		$options = array();

		foreach ( $meta as $key => $value ) {

			$options[ $key ] = $meta[ $key ]['default'];
		}

		do_action( 'wcf_offer_settings_markup_before' );
		$this->offer_metabox_html( $options, $post->ID );
		do_action( 'wcf_offer_settings_markup_after' );
	}

	/**
	 * Page Header Tabs
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	public function offer_metabox_html( $options, $post_id ) {

		$step_type  = get_post_meta( $post_id, 'wcf-step-type', true );
		$flow_id    = get_post_meta( $post_id, 'wcf-flow-id', true );
		$active_tab = 'wcf-offer-general';

			$tabs = array(
				array(
					'title' => __( 'Shortcodes', 'cartflows-pro' ),
					'id'    => 'wcf-offer-shortcodes',
					'class' => 'wcf-offer-shortcodes' === $active_tab ? 'wcf-tab wp-ui-text-highlight active' : 'wcf-tab',
					'icon'  => 'dashicons-editor-code',
				),
				array(
					'title' => __( 'Select Product', 'cartflows-pro' ),
					'id'    => 'wcf-offer-general',
					'class' => 'wcf-offer-general' === $active_tab ? 'wcf-tab wp-ui-text-highlight active' : 'wcf-tab',
					'icon'  => 'dashicons-info',
				),
				array(
					'title' => __( 'Conditional Redirect ', 'cartflows-pro' ),
					'id'    => 'wcf-conditions',
					'class' => 'wcf-conditionals' === $active_tab ? 'wcf-tab wp-ui-text-highlight active' : 'wcf-tab',
					'icon'  => 'dashicons-randomize',
				),
				array(
					'title' => __( 'Custom Script', 'cartflows-pro' ),
					'id'    => 'wcf-offer-custom-script-header',
					'class' => 'wcf-offer-custom-script-header' === $active_tab ? 'wcf-tab wp-ui-text-highlight active' : 'wcf-tab',
					'icon'  => 'dashicons-format-aside',
				),
			);

			$tabs = apply_filters( 'cartflows_offer_panel_tabs', $tabs, $active_tab );

			?>
		<div class="wcf-offer-table wcf-metabox-wrap widefat">
			<div class="wcf-table-container">
				<div class="wcf-column-left">
					<div class="wcf-tab-wrapper">
						<?php
						foreach ( $tabs as $key => $tab ) {

							?>
							<div class="<?php echo esc_attr( $tab['class'] ); ?>" data-tab="<?php echo esc_attr( $tab['id'] ); ?>">
								<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
								<span class="wcf-tab-title"><?php echo esc_html( $tab['title'] ); ?></span>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="wcf-column-right">

					<?php
					if ( ! cartflows_pro_is_active_license() ) {

						echo wcf()->meta->get_description_field(
							array(
								'name'    => 'wcf-upgrade-to-pro',
								/* translators: %s: link */
								'content' => '<i>' . sprintf( esc_html__( 'Activate %1$sCartFlows Pro%2$s license to access Upsell/Downsell options.', 'cartflows-pro' ), '<a href="' . CARTFLOWS_PRO_LICENSE_URL . '" target="_blank">', '</a>' ) . '</i>',
							)
						);
					} else {
						?>

					<div class="wcf-offer-shortcodes wcf-tab-content active widefat">
						<?php

						echo wcf_get_page_builder_notice();

						$offer_yes_link = '';
						$offer_no_link  = '';

						if ( 'upsell' === $step_type ) {

							$offer_yes_link = wcf()->utils->get_linking_url(
								array( 'class' => 'wcf-up-offer-yes' )
							);

							$offer_no_link = wcf()->utils->get_linking_url(
								array( 'class' => 'wcf-up-offer-no' )
							);
						}

						if ( 'downsell' === $step_type ) {

							$offer_yes_link = wcf()->utils->get_linking_url(
								array( 'class' => 'wcf-down-offer-yes' )
							);

							$offer_no_link = wcf()->utils->get_linking_url(
								array( 'class' => 'wcf-down-offer-no' )
							);
						}

							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Accept Offer Link', 'cartflows-pro' ),
									'name'    => 'wcf-offer-yes',
									'content' => $offer_yes_link,
								)
							);

							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Decline Offer Link', 'cartflows-pro' ),
									'name'    => 'wcf-offer-no',
									'content' => $offer_no_link,
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Variation', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pv-shortcode',
									'content' => '[cartflows_offer_product_variation]',
									'help'    => __( 'Add this shortcode to your offer page for variation selection. If product is variable, it will show variations.', 'cartflows-pro' ),
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Quantity', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pq-shortcode',
									'content' => '[cartflows_offer_product_quantity]',
									'help'    => __( 'Add this shortcode to your offer page for quantity selection.', 'cartflows-pro' ),
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Title', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pt-shortcode',
									'content' => '[cartflows_offer_product_title]',
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Description', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pd-shortcode',
									'content' => '[cartflows_offer_product_desc]',
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Short Description', 'cartflows-pro' ),
									'name'    => 'wcf-offer-psd-shortcode',
									'content' => '[cartflows_offer_product_short_desc]',
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Price', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pp-shortcode',
									'content' => '[cartflows_offer_product_price]',
									'help'    => __( 'This shortcode will show the product\'s single quantity price.', 'cartflows-pro' ),
								)
							);
							echo wcf()->meta->get_shortcode_field(
								array(
									'label'   => __( 'Product Image', 'cartflows-pro' ),
									'name'    => 'wcf-offer-pp-shortcode',
									'content' => '[cartflows_offer_product_image]',
								)
							);
						?>
					</div>
					<div class="wcf-offer-general wcf-tab-content active widefat">

						<?php

						echo wcf()->meta->get_product_selection_field(
							array(
								'label'                  => __( 'Select Product', 'cartflows-pro' ),
								'name'                   => 'wcf-offer-product',
								'value'                  => $options['wcf-offer-product'],
								'excluded_product_types' => array( 'grouped' ),
							)
						);

						echo wcf()->meta->get_number_field(
							array(
								'label' => __( 'Product Quantity', 'cartflows-pro' ),
								'name'  => 'wcf-offer-quantity',
								'value' => $options['wcf-offer-quantity'],
								'attr'  => array(
									'placeholder' => 1,
									'min'         => 1,
								),
							)
						);

						echo wcf()->meta->get_select_field(
							array(
								'label'   => __( 'Discount Type', 'cartflows-pro' ),
								'options' => array(
									''                 => esc_html__( 'Original', 'cartflows-pro' ),
									'discount_percent' => esc_html__( 'Discount Percentage', 'cartflows-pro' ),
									'discount_price'   => esc_html__( 'Discount Price', 'cartflows-pro' ),
								),
								'name'    => 'wcf-offer-discount',
								'value'   => $options['wcf-offer-discount'],
							)
						);

						echo wcf()->meta->get_number_field(
							array(
								'label' => __( 'Discount value', 'cartflows-pro' ),
								'name'  => 'wcf-offer-discount-value',
								'value' => $options['wcf-offer-discount-value'],
								'attr'  => array(
									'step' => 'any',
								),
							)
						);

						echo wcf()->meta->get_number_field(
							array(
								'label' => __( 'Flat Shipping Rate', 'cartflows-pro' ),
								'name'  => 'wcf-offer-flat-shipping-value',
								'value' => $options['wcf-offer-flat-shipping-value'],
								'attr'  => array(
									'step' => 'any',
								),
							)
						);

						echo wcf()->meta->get_description_field(
							array(
								'name'    => 'wcf-discount-price-notice',
								'content' => esc_html__( 'Select product and save once to see prices', 'cartflows-pro' ),
							)
						);

						echo wcf()->meta->get_display_field(
							array(
								'label'   => __( 'Original Price', 'cartflows-pro' ),
								'name'    => 'wcf-original-price',
								'content' => $this->get_original_price( $options, $post_id ),
							)
						);

						echo wcf()->meta->get_display_field(
							array(
								'label'   => __( 'Discount Price', 'cartflows-pro' ),
								'name'    => 'wcf-discount-price',
								'content' => $this->get_discount_price( $options, $post_id ),
							)
						);

						echo wcf()->meta->get_description_field(
							array(
								'name'    => 'wcf-discount-price-note',
								'content' => esc_html__( 'Note: If you have selected variable product, lowest price variation will be shown here.', 'cartflows-pro' ),
							)
						);

						echo wcf()->meta->get_hr_line_field( array() );

						echo wcf()->meta->get_section(
							array(
								'label' => __( 'Offer Order Settings', 'cartflows-pro' ),
							)
						);

						// Show cancal order option if Create seperate order for upsell option enabled.
						if ( ! wcf_pro()->utils->is_separate_offer_order() ) {

							echo wcf()->meta->get_description_field(
								array(
									'name'    => 'wcf-order-replace-note',
									'content' => sprintf(
										/* translators: %1$1s, %2$2s Link to meta */
										__( 'Do you want to cancel the main order on the purchae of upsell/downsell offer?<br><br>Please set the "Create a new child order" option in the %1$1sOffer Global Settings%2$2s to use the cancel primary order option.', 'cartflows-pro' ),
										'<a href="' . Cartflows_Pro_Helper::get_setting_page_url() . '" target="_blank">',
										'</a>'
									),
								)
							);

						} else {
							echo wcf()->meta->get_checkbox_field(
								array(
									'label' => __( 'Cancel Main Order?', 'cartflows-pro' ),
									'name'  => 'wcf-replace-main-order',
									'value' => $options['wcf-replace-main-order'],
									'help'  => __( 'If this option is enabled, it will cancel the main order on the purchase of upsell/downsell offer.', 'cartflows-pro' ),
								)
							);

							echo wcf()->meta->get_description_field(
								array(
									'name'    => 'wcf-order-replace-note',
									'content' => sprintf(
										/* translators: %1$1s, %2$2s Link to meta */
										__( 'Note: If "Cancel Main Order?" option is enabled then on the purchase of upsell/downsell offer it will charge the difference of main order total and this product. %1$1sLearn More »%2$2s', 'cartflows-pro' ),
										'<a href="https://cartflows.com/docs/replace-main-checkout-order-with-upsell-downsell" target="_blank">',
										'</a>'
									),
								)
							);
						}

						echo wcf()->meta->get_hr_line_field( array() );

						?>
					</div>

					<div class="wcf-conditions wcf-tab-content active widefat">
						<?php
						echo wcf_pro()->meta->get_optgroup_field(
							array(
								'label'           => __( 'Offer - Yes Next Step', 'cartflows-pro' ),
								'optgroup'        => array(
									'upsell'   => esc_html__( 'Upsell &lpar;Woo&rpar;', 'cartflows-pro' ),
									'downsell' => esc_html__( 'Downsell &lpar;Woo&rpar;', 'cartflows-pro' ),
									'thankyou' => esc_html__( 'Thankyou &lpar;Woo&rpar;', 'cartflows-pro' ),
								),
								'name'            => 'wcf-yes-next-step',
								'value'           => $options['wcf-yes-next-step'],
								'data-flow-id'    => $flow_id,
								'data-exclude-id' => $post_id,
							)
						);

						echo wcf_pro()->meta->get_optgroup_field(
							array(
								'label'           => __( 'Offer - No Next Step', 'cartflows-pro' ),
								'optgroup'        => array(
									'upsell'   => esc_html__( 'Upsell &lpar;Woo&rpar;', 'cartflows-pro' ),
									'downsell' => esc_html__( 'Downsell &lpar;Woo&rpar;', 'cartflows-pro' ),
									'thankyou' => esc_html__( 'Thankyou &lpar;Woo&rpar;', 'cartflows-pro' ),
								),
								'name'            => 'wcf-no-next-step',
								'value'           => $options['wcf-no-next-step'],
								'data-flow-id'    => $flow_id,
								'data-exclude-id' => $post_id,
							)
						);
						?>
					</div>
					<div class="wcf-offer-custom-script-header wcf-tab-content active widefat">
						<?php
						echo wcf()->meta->get_area_field(
							array(
								'label' => __( 'Custom Script', 'cartflows-pro' ),
								'name'  => 'wcf-custom-script',
								'value' => htmlspecialchars( $options['wcf-custom-script'], ENT_COMPAT, 'utf-8' ),
								'help'  => esc_html__( 'Custom script lets you add your own custom script on front end of this flow page.', 'cartflows-pro' ),
							)
						);
						?>
					</div>
						<?php do_action( 'cartflows_offer_panel_tab_content', $options, $post_id ); ?>

					<?php } ?>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Get original price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	public function get_original_price( $options, $post_id ) {

		$offer_product = $options['wcf-offer-product'];

		$custom_price = __( 'Product is not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {

			$custom_price = __( 'Product does not exist', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {

				$custom_price = $product->get_price();

				/* Product Quantity */
				$product_qty = intval( $options['wcf-offer-quantity'] );

				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}

	/**
	 * Get discount price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	public function get_discount_price( $options, $post_id ) {

		$offer_product = $options['wcf-offer-product'];

		$custom_price = __( 'Product is not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {

			$custom_price = __( 'Product does not exist', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {

				$custom_price = $product->get_price();

				/* Product Quantity */
				$product_qty = intval( $options['wcf-offer-quantity'] );

				/* Offer Discount */
				$discount_type = $options['wcf-offer-discount'];

				if ( ! empty( $discount_type ) ) {

					$discount_value = floatval( $options['wcf-offer-discount-value'] );

					if ( 'discount_percent' === $discount_type ) {

						if ( $discount_value > 0 ) {
							$custom_price = $custom_price - ( ( $custom_price * $discount_value ) / 100 );
						}
					} elseif ( 'discount_price' === $discount_type ) {

						if ( $discount_value > 0 ) {
							$custom_price = $custom_price - $discount_value;
						}
					}
				}

				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}


	/**
	 * Get metabox options
	 *
	 * @param int $post_id post ID.
	 * @return array
	 */
	public static function get_meta_option( $post_id ) {

		if ( null === self::$meta_option ) {

			/**
			 * Set metabox options
			 */
			self::$meta_option = wcf_pro()->options->get_offer_fields( $post_id );
		}

		return self::$meta_option;
	}

	/**
	 * Metabox Save
	 *
	 * @param  number $post_id Post ID.
	 * @return void
	 */
	public function save_meta_box( $post_id ) {

		if ( ! cartflows_pro_is_active_license() ) {
			return;
		}

		// Checks save status.
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );

		$is_valid_nonce = ( isset( $_POST['nonce-offer-step-meta'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce-offer-step-meta'] ) ), 'save-nonce-offer-step-meta' ) ) ? true : false;

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		wcf_pro()->options->save_offer_fields( $post_id );
	}
}


/**
 * Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Base_Offer_Meta::get_instance();
