<?php
/**
 * Flow meta
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Meta Boxes setup
 */
class Cartflows_Pro_Ab_Test_Flow_Meta {


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

		add_action( 'cartflows_wcf_step_wrap_top', array( $this, 'wcf_pro_meta_steps' ), 10, 4 );
		add_action( 'cartflows_after_flow_settings_meta', array( $this, 'wcf_ab_test_setting_popup' ), 10 );
		add_action( 'cartflows_wcf_step_wrap_bottom', array( $this, 'show_archived_steps' ), 10, 4 );

	}

	/**
	 * Show ab test header for step.
	 *
	 * @param array $data       Step data.
	 * @param bool  $ab_test_ui Is ab test ui enabled.
	 * @param bool  $ab_test    Is ab test enabled.
	 * @param array $args       Ab test arguments.
	 */
	public function wcf_pro_meta_steps( $data, $ab_test_ui, $ab_test, $args ) {

		if ( $ab_test_ui ) {

			$ab_test_variations = $args['ab_test_variations'];
			$start_ab_test      = isset( $data['ab-test-start'] ) && $data['ab-test-start'] ? true : false;
			$start_ab_test_text = $start_ab_test ? __( 'Stop Spilt Test', 'cartflows-pro' ) : __( 'Start Spilt Test', 'cartflows-pro' );

			include CARTFLOWS_PRO_AB_TEST_DIR . 'view/view-ab-test-step-head.php';
		}

	}

	/**
	 * Add Ab test button to step actions
	 */
	public function wcf_ab_test_setting_popup() {

		require CARTFLOWS_PRO_AB_TEST_DIR . 'view/view-ab-test-step-settings-popup.php';
	}

	/**
	 * Add Ab test button to step actions
	 *
	 * @param array $data       Step data.
	 * @param bool  $ab_test_ui Is ab test ui enabled.
	 * @param bool  $ab_test    Is ab test enabled.
	 * @param array $args       Ab test arguments.
	 */
	public function show_archived_steps( $data, $ab_test_ui, $ab_test, $args ) {

		$control_id                  = $args['control_id'];
		$ab_test_variations          = $args['ab_test_variations'];
		$ab_test_archived_variations = $args['ab_test_archived_variations'];

		if ( $ab_test_ui && is_array( $ab_test_archived_variations ) && ! empty( $ab_test_archived_variations ) ) {
			?>
			<div class="wcf-archived-wrapper">
				<span id="wcf-archived-button"><?php esc_html_e( 'Archived Steps', 'cartflows-pro' ); ?>
				<i class="dashicons dashicons-arrow-right" ></i></span>
				<div class="wcf-archived-steps" style="display:none">
					<?php
					foreach ( $ab_test_archived_variations as $in => $variation ) {

						$inner_step_id  = $variation['id'];
						$action_buttons = $this->get_archived_step_action_buttons( $control_id, $inner_step_id );

						include CARTFLOWS_PRO_AB_TEST_DIR . 'view/view-ab-test-archieved-step.php';
					}
					?>
				</div>
			</div>
			<?php
		}

	}

	/**
	 * Get step action buttons
	 *
	 * @param int $control_id Control id.
	 * @param int $inner_step_id step id.
	 * @return array.
	 */
	public function get_archived_step_action_buttons( $control_id, $inner_step_id ) {

		$action_buttons = array(
			'restore' => array(
				'link'      => '#',
				'class'     => 'wcf-step-archive-restore wcf-action-button wp-ui-text-highlight',
				'tooltip'   => esc_html__( 'Restore', 'cartflows-pro' ),
				'icon'      => 'dashicons-trash',
				'icon_html' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="wcf_restore" x="0px" y="0px" viewBox="0 0 426.667 426.667" style="width: 19px; vertical-align: middle; fill: currentColor;" xml:space="preserve"><g>
					<path d="M256,0H85.333C61.867,0,42.88,19.2,42.88,42.667L42.667,384c0,23.467,18.987,42.667,42.453,42.667h256.213    C364.8,426.667,384,407.467,384,384V128L256,0z M213.333,341.333c-43.733,0-81.173-26.347-97.707-64h36.48    c13.547,19.307,35.84,32,61.12,32c41.28,0,74.667-33.387,74.667-74.667c0-41.28-33.387-74.667-74.667-74.667    c-28.907,0-53.653,16.533-66.027,40.64l34.133,34.027H96v-85.333l27.733,27.733C142.72,147.627,175.68,128,213.333,128    C272.213,128,320,175.787,320,234.667S272.213,341.333,213.333,341.333z"/>
				</g></svg>',
				'label'     => esc_html__( 'Restore', 'cartflows-pro' ),
				'attr'      => array(
					'data-id'         => $inner_step_id,
					'data-control-id' => $control_id,
				),
			),
			'delete'  => array(
				'link'    => '#',
				'class'   => 'wcf-step-archive-delete wcf-action-button wp-ui-text-highlight',
				'tooltip' => esc_html__( 'Delete', 'cartflows-pro' ),
				'icon'    => 'dashicons-trash',
				'label'   => esc_html__( 'Delete', 'cartflows-pro' ),
				'attr'    => array(
					'data-id'         => $inner_step_id,
					'data-control-id' => $control_id,
				),
			),
		);

		return $action_buttons;
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Ab_Test_Flow_Meta::get_instance();
