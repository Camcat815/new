<?php
/**
 * Checkout Plugins - Stripe Payment element Gateway.
 *
 * @package cartflows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cartflows_Pro_Gateway_Cpsw_Stripe.
 */
class Cartflows_Pro_Gateway_Cpsw_Stripe_Element extends Cartflows_Pro_Cpsw_Stripe_Gateway_Helper {

	/**
	 * Member Variable
	 *
	 * @var Cartflows_Pro_Gateway_Cpsw_Stripe_Element
	 */
	private static $instance;

	/**
	 * Key name variable
	 *
	 * @var string
	 */
	public $key = 'cpsw_stripe_element';

	/**
	 * Refund supported variable
	 *
	 * @var is_api_refund
	 */
	public $is_api_refund = true;


	/**
	 *  Initiator
	 *
	 *  @return Cartflows_Pro_Gateway_Cpsw_Stripe_Element
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

		add_filter( 'cpsw_force_save_card', array( $this, 'tokenize_if_required' ) );

		add_action( 'cpsw_redirect_order_url', array( $this, 'redirect_using_wc_function' ), 10, 2 );

		add_action( 'wp_ajax_wcf_cpsw_create_payment_element_intent', array( $this, 'create_payment_intent' ) );
		add_action( 'wp_ajax_nopriv_wcf_cpsw_create_payment_element_intent', array( $this, 'create_payment_intent' ) );

		add_action( 'cartflows_offer_subscription_created', array( $this, 'add_subscription_payment_meta' ), 10, 3 );

		add_action( 'cartflows_offer_child_order_created_' . $this->key, array( $this, 'add_required_meta_to_child_order' ), 10, 3 );

		add_filter( 'cpsw_exclude_frontend_scripts', array( $this, 'allow_cpsw_scripts_on_offer_pages' ), 10, 1 ); // Allow stripe JS on CartFlows pages.
	}

	/**
	 * After payment process.
	 *
	 * @param object $order order data.
	 * @param array  $product product data.
	 * @return bool
	 */
	public function process_offer_payment( $order, $product ) {

		wcf()->logger->log( 'Started: ' . __CLASS__ . '::' . __FUNCTION__ );

		$is_successful = false;

		$nonce = isset( $_POST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, $product['action'] ) ) {
			wcf()->logger->log( 'Security nonce verification is : ' . $is_successful );
			return $is_successful;
		}

		if ( isset( $_POST['cpsw_intent_id'] ) ) {

			$stored_intent_id = $order->get_meta( 'wcf_cpsw_intent_id_' . $product['step_id'] );
			$intent_id        = isset( $_POST['cpsw_intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['cpsw_intent_id'] ) ) : '';

			wcf()->logger->log( 'Posted Intent ID : ' . $intent_id . 'Stored Intent ID : ' . $stored_intent_id );

			$confirm_intent = ( $intent_id === $stored_intent_id ) ? true : false;

			if ( $confirm_intent ) {

				$is_successful  = true;
				$payment_method = isset( $_POST['cpsw_payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['cpsw_payment_method'] ) ) : '';

				$order->update_meta_data( 'cartflows_offer_txn_resp_' . $product['step_id'], $intent_id );
				$order->update_meta_data( '_cartflows_offer_txn_cpsw_source_id_' . $product['step_id'], $payment_method );
				$order->update_meta_data( '_cartflows_offer_txn_cpsw_customer_id_' . $product['step_id'], $order->get_meta( '_cpsw_customer_id', true ) );
				$order->save();

				if ( ! wcf_pro()->utils->is_separate_offer_order() ) {
					$this->update_payout_details( $order, $intent_id, true );
				}
			}
		}

		wcf()->logger->log( 'Ended : ' . __CLASS__ . '::' . __FUNCTION__ );

		return $is_successful;
	}

	/**
	 * Verify 3DS and create intent accordingly.
	 *
	 * @return void
	 */
	public function create_payment_intent() {

		$nonce = isset( $_POST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wcf_cpsw_create_element_payment_intent' ) ) {
			return;
		}

		wcf()->logger->log( 'Started : ' . __CLASS__ . '::' . __FUNCTION__ );

		$variation_id = '';
		$input_qty    = '';

		if ( isset( $_POST['variation_id'] ) ) {
			$variation_id = intval( $_POST['variation_id'] );
		}

		if ( isset( $_POST['input_qty'] ) && ! empty( $_POST['input_qty'] ) ) {
			$input_qty = intval( $_POST['input_qty'] );
		}

		$step_id  = isset( $_POST['step_id'] ) ? intval( $_POST['step_id'] ) : 0;
		$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 0;
		$order    = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order ) {
			wp_send_json(
				array(
					'result'  => 'fail',
					'message' => 'Order not found',
				)
			);
		}
		$offer_product = wcf_pro()->utils->get_offer_data( $step_id, $variation_id, $input_qty, $order_id );

		if ( isset( $offer_product['price'] ) && ( floatval( 0 ) === floatval( $offer_product['price'] )
				|| '' === trim( $offer_product['price'] ) ) ) {

			wcf()->logger->log( 'Zero value product added' );

			wp_send_json(
				array(
					'result'  => 'fail',
					'message' => '0 value product',
				)
			);
		} else {

			$gateway  = $this->get_wc_gateway();
			$request  = $this->create_payment_intent_request_args( $order, $offer_product );
			$response = $gateway->create_payment_for_saved_payment_method( $request );

			wcf()->logger->log( 'Gateway Object : ' . wcf_print_r( $gateway, true ) );

			wcf()->logger->log( 'CPSW Stripe payment intent api response ' . wcf_print_r( $response, true ) );

			if ( $response['success'] && isset( $response['data'] ) ) {

				$response = $response['data'];
	   
				
				$order->update_meta_data( 'wcf_cpsw_intent_id_' . $step_id, $response->id );
				$order->save();
				

				$client_secret = $response->client_secret;

				$payment_mode    = get_option( 'cpsw_mode', 'test' );
				$livemode        = $payment_mode && 'live' === $payment_mode ? true : false;
				$publishable_key = $livemode ? get_option( 'cpsw_pub_key' ) : get_option( 'cpsw_test_pub_key' );

				wcf()->logger->log( 'CPSW Stripe payment intent client secret key ' . $client_secret );

				wp_send_json(
					array(
						'result'        => 'success',
						'client_secret' => $client_secret,
						'cpsw_pk'       => $publishable_key,
					)
				);
			} else {
				wp_send_json(
					array(
						'result'  => 'fail',
						'message' => 'Payment Failed',
					)
				);
			}
		}
	}


	/**
	 * Create payment intent args.
	 *
	 * @param WC_Order|WC_Order_Refund $order    The order that is being paid for.
	 * @param array                    $product  The product data.
	 * @return array
	 */
	public function create_payment_intent_request_args( $order, $product ) {

		wcf()->logger->log( 'Started : ' . __CLASS__ . '::' . __FUNCTION__ );

		wcf()->logger->log( 'CPSW create_payment_intent_request_args : ' . wcf_print_r( $order, true ) );

		$gateway = $this->get_wc_gateway();

		$order_source_id   = $order->get_meta( '_cpsw_source_id' );
		$order_customer_id = $order->get_meta( '_cpsw_customer_id' );

		if ( empty( $order_source_id ) || empty( $order_customer_id ) ) {

			$order_source = $gateway->prepare_order_source( $order );

			wcf()->logger->log( 'CPSW Newly Prepared Order Source : ' . wcf_print_r( $order_source, true ) );

			$order_source_id   = $order_source->source;
			$order_customer_id = $order_source->customer;
		}

		wcf()->logger->log( 'CPSW Order Source ID : ' . $order_source_id );
		wcf()->logger->log( 'CPSW Customer ID : ' . $order_customer_id );

		/* translators: %1s site name */
		$description = sprintf( __( '%1$s - Order %2$s_%3$s - One Click Payment', 'cartflows-pro' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_id(), $product['step_id'] );
		/* translators: %1s order number */
		$statement_descriptor = apply_filters( 'cartflows_offer_' . $this->key . '_statement_descriptor', sprintf( __( 'Order %1$s_%2$s OTO', 'cartflows-pro' ), $order->get_order_number(), $product['step_id'] ) );

		wcf()->logger->log( 'Ended : ' . __CLASS__ . '::' . __FUNCTION__ );

		return array(
			'payment_method'              => $order_source_id,
			'payment_method_types'        => array( 'card' ),
			'amount'                      => $gateway->get_formatted_amount( $product['price'] ),
			'currency'                    => strtolower( $order->get_currency() ),
			'description'                 => $description,
			'statement_descriptor'        => $gateway->clean_statement_descriptor( $statement_descriptor ),
			'statement_descriptor_suffix' => $gateway->clean_statement_descriptor( $statement_descriptor ),
			'customer'                    => $order_customer_id,

		);
	}

	/**
	 * Allow gateways to declare whether they support offer refund
	 *
	 * @return bool
	 */
	public function is_api_refund() {
	  
		return (bool) $this->is_api_refund;
	}

	/**
	 * Process offer refund
	 *
	 * @param object $order Order Object.
	 * @param array  $offer_data offer data.
	 *
	 * @return string/bool.
	 */
	public function process_offer_refund( $order, $offer_data ) {

		$response_id = false;
		$gateway     = $this->get_wc_gateway();

		$transaction_id = $offer_data['transaction_id'];
		$refund_amount  = $offer_data['refund_amount'];
		$refund_reason  = $offer_data['refund_reason'];

		$response = $gateway->create_refund_request( $order, $refund_amount, $refund_reason, $transaction_id );

		if ( isset( $response['success'] ) && $response['success'] ) {
			$response_id = $response['data']->id;
			$this->update_payout_details( $order, $transaction_id, false );
		}

		return $response_id;
	}

	/**
	 * Get WooCommerce payment geteway.
	 *
	 * @return object
	 */
	public function get_wc_gateway() {

		$gateways = wc()->payment_gateways->payment_gateways();

		return $gateways[ $this->key ];
	}

	/**
	 * Save the parent payment meta to child order.
	 *
	 * @param object     $order Object of order.
	 * @param string|int $intent_id id.
	 * @param bool       $initiate is intent initiate call.
	 * @return void
	 */
	public function update_payout_details( $order, $intent_id, $initiate = false ) {
		$gateway = $this->get_wc_gateway();
		$gateway->update_stripe_balance( $order, $intent_id, $initiate );
	}

	/**
	 * Save the parent payment meta to child order.
	 *
	 * @param object $parent_order Object of order.
	 * @param object $child_order Object of order.
	 * @param int    $transaction_id transaction id.
	 * @return void
	 */
	public function add_required_meta_to_child_order( $parent_order, $child_order, $transaction_id ) {

		// In order to refund the upsell childe order, stripe checks if charge is captured or not.Hence need to add below key.
		$child_order->update_meta_data( '_cpsw_charge_captured', 'yes' );

		$intent_secret = array(
			'id' => $transaction_id,
		);

		$child_order->update_meta_data( '_cpsw_intent_secret', $intent_secret );
		$child_order->save();

		$this->update_payout_details( $child_order, $transaction_id, true );
	}
	
}

/**
 *  Prepare if class 'Cartflows_Pro_Gateway_Cpsw_Stripe_Element' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Gateway_Cpsw_Stripe_Element::get_instance();
