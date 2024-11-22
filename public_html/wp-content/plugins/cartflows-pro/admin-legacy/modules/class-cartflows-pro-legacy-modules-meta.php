<?php
/**
 * Modules Loader
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Initial Setup
 *
 * @since 1.0.0
 */
class Cartflows_Pro_Legacy_Modules_Meta {


	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		if ( wcf_pro()->is_woo_active ) {
			require_once CARTFLOWS_PRO_LEGACY_ADMIN_DIR . 'modules/checkout/class-cartflows-pro-checkout-meta.php';
			require_once CARTFLOWS_PRO_LEGACY_ADMIN_DIR . 'modules/offer/class-cartflows-pro-base-offer-meta.php';
			require_once CARTFLOWS_PRO_LEGACY_ADMIN_DIR . 'modules/optin/class-cartflows-pro-optin-meta.php';
		}

		add_action( 'save_post_cartflows_step', array( $this, 'delete_pro_dynamic_css' ), 10, 3 );
	}

	/**
	 * Delete the step dynamic css when cartflows_step post is update.
	 *
	 * @param  int    $post_id post id.
	 * @param  object $post post data.
	 * @param  bool   $update existing post.
	 */
	public function delete_pro_dynamic_css( $post_id, $post, $update ) {

		delete_post_meta( $post_id, 'wcf-pro-dynamic-css' );
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Legacy_Modules_Meta::get_instance();
