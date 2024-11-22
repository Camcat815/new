<?php
/**
 * CartFlows Admin Legacy.
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cartflows_Pro_Legacy_Admin_Loader.
 */
class Cartflows_Pro_Legacy_Admin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		define( 'CARTFLOWS_PRO_LEGACY_ADMIN_DIR', CARTFLOWS_PRO_DIR . 'admin-legacy/' );
		define( 'CARTFLOWS_PRO_LEGACY_ADMIN_URL', CARTFLOWS_PRO_URL . 'admin-legacy/' );

		$this->setup_classes();
	}

	/**
	 * Include required classes.
	 */
	public function setup_classes() {

		include_once CARTFLOWS_PRO_LEGACY_ADMIN_DIR . 'modules/class-cartflows-pro-legacy-modules-meta.php';
	}
}

Cartflows_Pro_Legacy_Admin_Loader::get_instance();
