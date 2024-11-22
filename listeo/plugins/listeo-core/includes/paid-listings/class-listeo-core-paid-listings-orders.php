<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Orders
 */
class Listeo_Core_Orders {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}


	/**
	 * Constructor
	 */
	public function __construct() {
	// Statuses
		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_thankyou' ), 5 );

		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ) );
		//add_action( 'woocommerce_order_status_cancelled', array( $this, 'package_cancelled' ) );
	}

	/**
	 * Triggered when an order is paid
	 *
	 * @param  int $order_id
	 */
	public function order_paid( $order_id ) {
		// Get the order
	
	
		$order = wc_get_order( $order_id );

		if ( get_post_meta( $order_id, 'listeo_core_paid_listings_processed', true ) ) {
			return;
		}
		
		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );

			if ( $product &&  $product->is_type( 'listing_package' ) && $order->get_customer_id() ) {

				// Give packages to user
				$user_package_id = false;
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = listeo_core_give_user_package( $order->get_customer_id(), $product->get_id(), $order_id );
				}

				$this->attach_package_listings( $item, $order, $user_package_id );
				$this->maybe_claim_listings( $item );
			}
			
			if($product && $product->is_type('listing_package_subscription')) {
				$this->maybe_claim_listings($item);
			}
		}

		update_post_meta( $order_id, 'listeo_core_paid_listings_processed', true );
	}


	public function maybe_claim_listings( $item) {
	
		$claim_ids[] = isset($item['claim_id']) ? $item['claim_id'] : '';
		$claim_ids = array_unique( array_filter( array_map( 'absint', $claim_ids ) ) );
		foreach ($claim_ids as $claim_id) {
			
			$claim_id = absint($claim_id);
			if ($claim_id) {
				$listing_id = get_post_meta($claim_id, 'listing_id', true);
				$user_id = get_post_field('post_author', $claim_id);
		
				$listing = get_post($listing_id);
				if ($listing) {
					$listing->post_author = $user_id;
					
					$product_id = get_post_meta($listing_id,'_product_id',true);
					if ($product_id) {
						wp_delete_post($product_id);
						delete_post_meta($listing_id, '_product_id');
					}
					// change owner of listing
					wp_update_post($listing);
					update_post_meta($listing_id, '_verified', 'on');
				}
				// set claim status to completed
				update_post_meta($claim_id, 'status', 'completed');
			}
		}

	}
	/**
	 * Attached listings to the user package.
	 *
	 * @param array    $item
	 * @param WC_Order $order
	 * @param int      $user_package_id
	 */
	private function attach_package_listings( $item, $order, $user_package_id ) {
		global $wpdb;
		$listing_ids = (array) $wpdb->get_col( 
			$wpdb->prepare( 
				"SELECT post_id 
				FROM $wpdb->postmeta 
				WHERE meta_key=%s 
				AND meta_value=%s", '_cancelled_package_order_id', $order->get_id() ) );

		$listing_ids[] = isset( $item[ 'listing_id' ] ) ? $item[ 'listing_id' ] : '';
		$listing_ids   = array_unique( array_filter( array_map( 'absint', $listing_ids ) ) );

		foreach ( $listing_ids as $listing_id ) {
			if ( in_array( get_post_status( $listing_id ), array( 'pending_payment', 'expired' ) ) ) {
				listeo_core_approve_listing_with_package( $listing_id, $order->get_user_id(), $user_package_id );
				delete_post_meta( $listing_id, '_cancelled_package_order_id' );
			}

			if ( get_post_meta($listing_id, '_package_change', true) ){
				listeo_core_approve_listing_with_package( $listing_id, $order->get_user_id(), $user_package_id );
				$post_types_expiry = new Listeo_Core_Post_Types;
				$post_types_expiry->set_expiry(get_post($listing_id));
				delete_post_meta( $listing_id, '_package_change' );
			}
		}
	}


		/**
	 * Thanks page
	 *
	 * @param mixed $order_id
	 */
	public function woocommerce_thankyou( $order_id ) {
		global $wp_post_types;

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item ) {
			if ( isset( $item['listing_id'] )  ) {
				switch ( get_post_status( $item['listing_id'] ) ) {
					case 'pending' :
						echo wpautop( sprintf( __( '<strong>%s</strong> has been submitted successfully and will be visible once approved.', 'listeo_core' ), get_the_title( $item['listing_id'] ) ) );
					break;
					case 'pending_payment' :
					case 'expired' :
						echo wpautop( sprintf( __( '<strong>%s</strong> has been submitted successfully and will be visible once payment has been confirmed.', 'listeo_core' ), get_the_title( $item['listing_id'] ) ) );
					break;
					default :
						echo wpautop( sprintf( __( '<strong>%s</strong> has been submitted successfully.', 'listeo_core' ), get_the_title( $item['listing_id'] ) ) );
					break;
				}
			} 
		}// End foreach().
	}
}

Listeo_Core_Orders::get_instance();