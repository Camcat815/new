<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;
/**
 * listeo_core_listing class
 */
class Listeo_Sms_Notification
{

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0
	 */
	private static $_instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var Listeo_Twilio_Sms_Loader
	 * @since  1.0.0
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/** Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{


		// Notification about waiting payment	
		if(get_option('listeo_enable_sms')){

		
		add_action('listeo_core_listing_submitted', array($this, 'new_listing_sms'));
		add_action('listeo_core_listing_submitted', array($this, 'new_listing_sms_admin'));
		
		add_action('listeo_core_expired_listing', 	 array($this, 'expired_listing_sms'));
		add_action('listeo_core_expiring_soon_listing', array($this, 'expiring_soon_listing_sms'));

		add_action('pending_to_publish', array($this, 'published_listing_sms'));
		add_action('pending_payment_to_publish', array($this, 'published_listing_sms'));
		add_action('preview_to_publish', array($this, 'published_listing_sms'));
	

		add_action('comment_post', array($this, 'notify_owner_review_sms'));
	
		// //booking sms
		add_action('listeo_mail_to_user_waiting_approval', array($this, 'sms_to_user_waiting_approval'));
		add_action('listeo_mail_to_user_instant_approval', array($this, 'sms_to_user_instant_approval'));
		add_action('listeo_mail_to_user_free_confirmed', array($this, 'sms_to_user_free_confirmed'));
		add_action('listeo_mail_to_user_pay_cash_confirmed', array($this, 'sms_to_user_pay_cash_confirmed'));

		add_action('listeo_mail_to_owner_new_reservation', array($this, 'sms_to_owner_new_reservation'));
		add_action('listeo_mail_to_owner_new_instant_reservation', array($this, 'sms_to_owner_new_instant_reservation'));

		add_action('listeo_mail_to_user_canceled', array($this, 'sms_to_user_canceled'));
		add_action('listeo_mail_to_owner_canceled', array($this, 'sms_to_owner_canceled'));

		add_action('listeo_mail_to_user_pay', array($this, 'sms_to_user_pay'));
		add_action('listeo_mail_to_owner_paid', array($this, 'sms_to_owner_paid'));
		add_action('listeo_mail_to_user_paid', array($this, 'sms_to_user_paid'));


		add_action('listeo_expiring_booking', array($this, 'user_waiting_payment_sms'));
		add_action('listeo_mail_to_user_upcoming_booking', array($this, 'sms_to_user_upcoming_booking'));

		
		add_action('listeo_mail_to_user_past_booking', array($this, 'sms_to_user_review_reminder'));
		}
		//Notification about booking status change for guest booking
		//  reminder e-mail for the booking (1 hr in advance) or to add how long in advance to be sent
		// - review sms past reservations 24hrs so guest can submit review as review mail reminder
		// - Cancellation mail to owner if guest cancel booking as it’s only to user now
	}

	/**
	 * Send SMS notification to user about new listing
	 */
	

	function new_listing_sms($post_id)
	{
		$post = get_post($post_id);
		if ($post->post_type !== 'listing') {
			return;
		}

		if (!get_option('listeo_sms_listeo_core_listing_submitted_status')) {
			return;
		}

		$is_send = get_post_meta($post->ID, 'listeo_sms_listeo_core_listing_submitted_status_sent', true);
		if ($is_send) {
			return;
		}

		$author   	= 	get_userdata($post->post_author);
		$phone = $this->get_user_phone($author->ID);
		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $post->post_author) == 'on') {
			return;
		}

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $author->user_email,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);

		
		$body 	 = get_option('listeo_sms_listeo_core_listing_submitted_body');
	
		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, 'listeo_sms_listeo_core_listing_submitted_status_sent', '1');
		
		self::send($phone, $body);
	}


	function new_listing_sms_admin($post_id) {
		
		$post = get_post($post_id);
		
		if ($post->post_type !== 'listing') {
			return;
		}
		
		if (!get_option('listeo_sms_listeo_core_admin_listing_submitted_status')) {
			return;
		}

		$is_send = get_post_meta($post->ID, 'listeo_sms_listeo_core_admin_listing_submitted_status_sent', true);
		
		if ($is_send) {
			return;
		}

		$admin_phone = get_option('listeo_sms_admin_phone');
		if(!$admin_phone){
			return;
		}

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $author->user_email,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);


		$body 	 = esc_html__('New listing {listing_name} has been submitted on your site', 'listeo_core');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, 'listeo_sms_listeo_core_admin_listing_submitted_status_sent', '1');

		self::send($admin_phone, $body);
	}


	/**
	 * Send SMS notification to user about expired listing
	 */
	
	function expired_listing_sms($post_id)
	{
		$post = get_post($post_id);
		if ($post->post_type !== 'listing') {
			return;
		}

		$token = 'listeo_sms_expired_listing';

		if (!get_option($token.'_status')) {
			return;
		}


		$is_send = get_post_meta($post->ID, $token . '_status_sent', true);
		
		if ($is_send) {
			return;
		}

		$author   	= 	get_userdata($post->post_author);
		$phone 		= 	$this->get_user_phone($author->ID);
		if (get_the_author_meta('sms_notifications', $post->post_author) == 'on') {
			return;
		}
		if(!$phone){
			return;
		}

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $author->user_email,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);

		
		$body 	 = get_option($token . '_body');
	
		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, $token . '_status_sent', '1');
		
		self::send($phone, $body);
	}

	/**
	 * Send SMS notification to user about expired soon listing
	 */
	

	function expiring_soon_listing_sms($post_id)
	{
		$post = get_post($post_id);
		if ($post->post_type !== 'listing') {
			return;
		}

		$token = 'listeo_sms_expiring_soon_listing';

		if (!get_option($token.'_status')) {
			return;
		}


		$is_send = get_post_meta($post->ID, $token . '_status_sent', true);
		
		if ($is_send) {
			return;
		}

		$author   	= 	get_userdata($post->post_author);
		$phone 		= 	$this->get_user_phone($author->ID);

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $post->post_author) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $author->user_email,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);

		
		$body 	 = get_option($token . '_body');
	
		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, $token . '_status_sent', '1');
		
		self::send($phone, $body);
	}

	/**
	 * Send SMS notification to published listing
	 */


	function published_listing_sms($post_id)
	{

		
		$post = get_post($post_id);
		if ($post->post_type !== 'listing') {
			return;
		}

		$token = 'listeo_sms_published_listing';

		if (!get_option($token.'_status')) {
			return;
		}


		$is_send = get_post_meta($post->ID, $token . '_status_sent', true);
		
		if ($is_send) {
			return;
		}

		$author   	= 	get_userdata($post->post_author);
		$phone 		= 	$this->get_user_phone($author->ID);

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $post->post_author) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $author->user_email,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);

		
		$body 	 = get_option($token . '_body');
	
		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, $token . '_status_sent', '1');
		
		self::send($phone, $body);
	}


	/**
	 * Send SMS notify_owner_review_sms
	 */


	function notify_owner_review_sms($post_id)
	{
		$post = get_post($post_id);
		if ($post->post_type !== 'listing') {
			return;
		}

		$token = 'listeo_sms_notify_owner_review';

		if (!get_option($token.'_status')) {
			return;
		}


		$is_send = get_post_meta($post->ID, $token . '_status_sent', true);
		
		if ($is_send) {
			return;
		}

		$author   	= 	get_userdata($post->post_author);
		$phone 		= 	$this->get_user_phone($author->ID);

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $post->post_author) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $phone,
			'listing_date' => $post->post_date,
			'listing_name' => $post->post_title,
			'listing_url'  => get_permalink($post->ID),
			'site_name' 	=> get_bloginfo('name'),
			'site_url' 	=> get_bloginfo('name'),
		);

		
		$body 	 = get_option($token . '_body');
	
		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
		update_post_meta($post->ID, $token . '_status_sent', '1');
		
		self::send($phone, $body);
	}


	/**
	 * Send SMS sms_to_user_waiting_approval
	 */
	function sms_to_user_waiting_approval($args)
	{
	

		$token = 'listeo_sms_to_user_waiting_approval';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
	
		self::send($phone, $body);
	}


	/**
	 * Send SMS sms_to_user_instant_approval
	 */
	function sms_to_user_instant_approval($args)
	{
	

		$token = 'listeo_sms_to_user_instant_approval';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');
	
		self::send($phone, $body);
	}



	/**
	 * Send SMS sms_to_user_free_confirmed
	 */
	function sms_to_user_free_confirmed($args)
	{


		$token = 'listeo_sms_to_user_free_confirmed';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}

	//sms_to_user_pay_cash_confirmed
	function sms_to_user_pay_cash_confirmed($args)
	{


		$token = 'listeo_sms_to_user_pay_cash_confirmed';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}


	//sms_to_user_pay_cash_confirmed
	function sms_to_owner_new_reservation($args)
	{


		$token = 'listeo_sms_to_owner_new_reservation';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

	
		$phone = $this->get_user_phone($booking['owner_id']);
		
		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['owner_id']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['owner_id']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}

	//sms_to_user_pay_cash_confirmed
	function sms_to_owner_new_instant_reservation($args)
	{


		$token = 'listeo_sms_to_owner_new_instant_reservation';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

	
		$phone = $this->get_user_phone($booking['owner_id']);
		
		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['owner_id']) == 'on') {
			return;
		}	
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['owner_id']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}


	//sms_to_user_canceled
	function sms_to_user_canceled($args)
	{


		$token = 'listeo_sms_to_user_canceled';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}

	//sms_to_user_pay_cash_confirmed
	function sms_to_owner_canceled($args)
	{


		$token = 'listeo_sms_to_owner_canceled';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];


		$phone = $this->get_user_phone($booking['owner_id']);

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['owner_id']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['owner_id']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}

	//sms_to_user_pay_status

	function sms_to_user_pay($args)
	{


		$token = 'listeo_sms_to_user_pay';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}



	// function sms_to_owner_paid

	function sms_to_owner_paid($args) {

		$token = 'listeo_sms_to_owner_paid';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $this->get_user_phone($booking['owner_id']);
		
		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['owner_id']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}


	//sms_to_user_paid

	function sms_to_user_paid($args)
	{


		$token = 'listeo_sms_to_user_paid';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');

		$body 	 = $this->replace_shortcode($args, $body);
		//update_post_meta($post->ID, 'new_listing_email_notification', 'sent');

		self::send($phone, $body);
	}



	/**
	 * Send SMS notification to user about waiting payment
	 * TODO to fixnac
	 */
	public function user_waiting_payment_sms($booking_id)
	{

		$token = 'listeo_user_waiting_payment_sms';

		if (!get_option($token . '_status')) {
			return;
		}

		$booking_data	 = $this->get_booking_data_by_id($booking_id);
		// get order meta field for listing id

		$booking	 = wc_get_order($booking_id);

		$listing_id  = get_post_meta($booking_id, 'listing_id', true);
		$listing 	 = get_post($listing_id);
		$author 	 = get_userdata($listing->post_author);

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);
		$body 	 = get_option($token . '_body');

		$body = $this->replace_shortcode($args, $body);
		$to = $author->billing_phone;
		self::send($to, $body);
	}


	public function sms_to_user_upcoming_booking() {

		$token = 'listeo_user_waiting_payment_sms';

		if (!get_option($token . '_status')) {
			return;
		}
		$booking_data	 = $this->get_booking_data_by_id($booking_id);
		// get order meta field for listing id

		$booking	 = wc_get_order($booking_id);

		$listing_id  = get_post_meta($booking_id, 'listing_id', true);
		$listing 	 = get_post($listing_id);
		$author 	 = get_userdata($listing->post_author);

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if (!$phone) {
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);
		$body 	 = get_option($token . '_body');
		$body = $this->replace_shortcode($args, $body);
		
		self::send($phone, $body);

	}


	public function sms_to_user_review_reminder($args){
		$token = 'listeo_sms_to_user_review_reminder';

		if (!get_option($token . '_status')) {
			return;
		}


		$booking_data = $this->get_booking_data_sms($args['booking']);

		$booking = $args['booking'];

		$phone = $booking_data['client_phone'];


		if (!$phone) {
			$phone = $this->get_user_phone($booking['bookings_author']);
		}

		if(!$phone){
			return;
		}
		if (get_the_author_meta('sms_notifications', $booking['bookings_author']) == 'on') {
			return;
		}
		$args = array(
			'user_name' 	=> get_the_author_meta('display_name', $booking['bookings_author']),
			'booking_date' => $booking['created'],
			'listing_name' => get_the_title($booking['listing_id']),
			'listing_url'  => get_permalink($booking['listing_id']),
			'listing_address'  => get_post_meta($booking['listing_id'], '_address', true),
			'listing_latitude'  => get_post_meta($booking['listing_id'], '_geolocation_lat', true),
			'listing_longitude'  => get_post_meta($booking['listing_id'], '_geolocation_long', true),
			'listing_phone'  => get_post_meta($booking['listing_id'], '_phone', true),
			'listing_email'  => get_post_meta($booking['listing_id'], '_email', true),
			'dates' => (isset($booking_data['dates'])) ? $booking_data['dates'] : '',
			'details' => (isset($booking_data['details'])) ? $booking_data['details'] : '',
			'service' => (isset($booking_data['service'])) ? $booking_data['service'] : '',
			'tickets' => (isset($booking_data['tickets'])) ? $booking_data['tickets'] : '',
			'adults' => (isset($booking_data['adults'])) ? $booking_data['adults'] : '',
			'children' => (isset($booking_data['children'])) ? $booking_data['children'] : '',
			'user_message' => (isset($booking_data['user_message'])) ? $booking_data['user_message'] : '',
			'client_first_name' => (isset($booking_data['client_first_name'])) ? $booking_data['client_first_name'] : '',
			'client_last_name' => (isset($booking_data['client_last_name'])) ? $booking_data['client_last_name'] : '',
			'client_email' => (isset($booking_data['client_email'])) ? $booking_data['client_email'] : '',
			'client_phone' => (isset($booking_data['client_phone'])) ? $booking_data['client_phone'] : '',
			'billing_address' => (isset($booking_data['billing_address'])) ? $booking_data['billing_address'] : '',
			'billing_postcode' => (isset($booking_data['billing_postcode'])) ? $booking_data['billing_postcode'] : '',
			'billing_city' => (isset($booking_data['billing_city'])) ? $booking_data['billing_city'] : '',
			'billing_country' => (isset($booking_data['billing_country'])) ? $booking_data['billing_country'] : '',
			'price' => (isset($booking['price'])) ? $booking['price'] : '',
			'expiring' => (isset($booking['expiring'])) ? $booking['expiring'] : '',
		);


		$body 	 = get_option($token . '_body');
		$body = $this->replace_shortcode($args, $body);
		
		self::send($phone, $body);

		
	}





	public function get_user_phone($user_id)
	{
		$phone = get_user_meta($user_id, 'phone', true);
		if (!$phone) {
			$phone = get_user_meta($user_id, 'billing_phone', true);
		}
		return $phone;
	}





	function get_booking_data_emails($args){

		$listing_type = get_post_meta($args['listing_id'],'_listing_type',true);
		$booking_data = array();
		
		switch ($listing_type) {
			case 'rental':
				$booking_data['dates'] = date_i18n(get_option( 'date_format' ), strtotime($args['date_start'])) .' - '. date_i18n(get_option( 'date_format' ), strtotime($args['date_end'])); 
				break;
			case 'service':
				
					$meta_value_date = explode(' ', $args['date_start'],2); 
					// if(!in_array($date_format,array('F j, Y','Y-m-d','m/d/Y','d/m/Y'))) {
					// 	$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
					// }
					$date_format = get_option( 'date_format' );
			
					//$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
					$meta_value_stamp_obj = DateTime::createFromFormat('Y-m-d', $meta_value_date[0]);
					if($meta_value_stamp_obj){
						$meta_value_stamp = $meta_value_stamp_obj->getTimestamp();
					} else {
						$meta_value_stamp = false;
					}
					
					$meta_value = date_i18n(get_option( 'date_format' ),$meta_value_stamp);
					
					//echo strtotime(end($meta_value_date));
					//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
					if( isset($meta_value_date[1]) ) { 
						$time = str_replace('-','',$meta_value_date[1]);
						$meta_value .= esc_html__(' at ','listeo_core'); 
						$meta_value .= date(get_option( 'time_format' ), strtotime($time));

					}
						
					$booking_data['dates'] = $meta_value;
				break;
			case 'event':
					//$booking_data['dates'] = date(get_option( 'date_format' ), strtotime($args['date_start'])).' '.esc_html__(' at ','listeo_core').' '.date(get_option( 'time_format' ), strtotime($args['date_start']));
					$meta_value = get_post_meta($args['listing_id'],'_event_date', true);
					$meta_value_timestamp = get_post_meta($args['listing_id'], '_event_date_timestamp', true);
				
					$meta_value_date = explode(' ', $meta_value,2); 

					if (!empty($meta_value_timestamp)) {
						$meta_value = date_i18n(get_option('date_format'), $meta_value_timestamp);
						
						if (isset($meta_value_date[1])) {
							$time = str_replace('-', '', $meta_value_date[1]);
							$meta_value .= esc_html__(' at ', 'listeo_core');
							$meta_value .= date_i18n(get_option('time_format'), strtotime($time));
						}
						$booking_data['dates'] = $meta_value;
					} else {

						$meta_value_ = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date[0]);
						//var_dump($meta_value);
						if (!is_string($meta_value_)) {
							$meta_value_stamp = $meta_value_->getTimestamp();
							$meta_value = date_i18n(get_option('date_format'), $meta_value_stamp);
						} else {
							$meta_value = $meta_value_date[0];
						}

						//echo strtotime(end($meta_value_date));
						//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
						if (isset($meta_value_date[1])) {
							$time = str_replace('-', '', $meta_value_date[1]);
							$meta_value .= esc_html__(' at ', 'listeo_core');
							$meta_value .= date_i18n(get_option('time_format'), strtotime($time));
						}
						$booking_data['dates'] = $meta_value;
					}
				break;
			
			default:
				# code...
				break;
		}
		
		if( isset($args['expiring']) ) {
			$booking_data['expiring'] = $args['expiring'];
		}
		$booking_details = '';
		$details = json_decode($args['comment']);
		if (isset($details->childrens) && $details->childrens > 0) {
			$booking_data['children'] = sprintf( _n( '%d Child', '%s Children', $details->childrens, 'listeo_core' ), $details->childrens );
			$booking_details .= $booking_data['children'];
		}
		if (isset($details->adults) && $details->adults > 0) {
			$booking_data['adults'] = sprintf( _n( '%d Guest', '%s Guests', $details->adults, 'listeo_core' ), $details->adults );
			$booking_details .= $booking_data['adults'];
		}
		if (isset($details->tickets) && $details->tickets > 0) {
			$booking_data['tickets'] = sprintf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'listeo_core' ), $details->tickets );
			$booking_details .= $booking_data['tickets'];
		}
		
		if (isset($details->service)) {
			$booking_data['service'] = listeo_get_extra_services_html($details->service);
		}
		
		//client data
		if (isset($details->first_name)) {
			$booking_data['client_first_name'] = $details->first_name;
		}
		if (isset($details->last_name)) {
			$booking_data['client_last_name'] = $details->last_name;
		}
		if (isset($details->email)) {
			$booking_data['client_email'] = $details->email;
		}
		if (isset($details->phone)) {
			$booking_data['client_phone'] = $details->phone;
		}


		if( isset($details->billing_address_1) ) {
			$booking_data['billing_address'] = $details->billing_address_1;
		}
		if( isset($details->billing_postcode) ) {
			$booking_data['billing_postcode'] = $details->billing_postcode;
		}
		if( isset($details->billing_city) ) {
			$booking_data['billing_city'] = $details->billing_city;
		}
		if( isset($details->billing_country) ) {
			$booking_data['billing_country'] = $details->billing_country;
		}

		if( isset($details->message) ) {
			$booking_data['user_message'] = $details->message;
			$booking_data['message'] = $details->message;
		}


		if( isset($details->price) ) {
			$booking_data['price'] = $details->price;
		}



		$booking_data['details'] = $booking_details;
		
		return $booking_data;
		
	}

	/**
	 * general function to send email to agent with specify subject, body content
	 */
	public static function send($to, $body)
	{
		
		if(get_option('listeo_sms_debug')){
			error_log('SMS Notifier: Sending SMS to ' . $to . ' with body: ' . $body);
		} else {

			$account_sid = get_option('listeo_twilio_sid');
			$auth_token = get_option('listeo_twilio_auth_token');
			$twilio_number = get_option('listeo_twilio_number');
			$client = new Twilio\Rest\Client($account_sid, $auth_token);

			try {
				
				$client->messages->create(
					$to,
					array(
						'from' => $twilio_number,
						'body' => $body
					)
				);
				return true;
			} catch (Exception $e) {
				
				error_log('SMS Notifier Error: ' . $e->getMessage());
				return false;
			}
		}
	
	}

 function get_booking_data_by_id($booking_id){
		global $wpdb;

		$booking_id = sanitize_text_field($booking_id);

		$booking_data = $wpdb->get_row('SELECT * FROM `'  . $wpdb->prefix .  'bookings_calendar` WHERE `id`=' . esc_sql($booking_id), 'ARRAY_A');
		$filtered_data = $this->get_booking_data_sms($booking_data);
		return $filtered_data;
      
 }

	function get_booking_data_sms($args)
	{

		$listing_type = get_post_meta($args['listing_id'], '_listing_type', true);
		$booking_data = array();

		switch ($listing_type) {
			case 'rental':
				$booking_data['dates'] = date_i18n(get_option('date_format'), strtotime($args['date_start'])) . ' - ' . date_i18n(get_option('date_format'), strtotime($args['date_end']));
				break;
			case 'service':

				$meta_value_date = explode(' ', $args['date_start'], 2);
				// if(!in_array($date_format,array('F j, Y','Y-m-d','m/d/Y','d/m/Y'))) {
				// 	$meta_value_date[0] = str_replace('/','-',$meta_value_date[0]);
				// }
				$date_format = get_option('date_format');

				//$meta_value = date_i18n(get_option( 'date_format' ), strtotime($meta_value_date[0])); 
				$meta_value_stamp_obj = DateTime::createFromFormat('Y-m-d', $meta_value_date[0]);
				if ($meta_value_stamp_obj) {
					$meta_value_stamp = $meta_value_stamp_obj->getTimestamp();
				} else {
					$meta_value_stamp = false;
				}

				$meta_value = date_i18n(get_option('date_format'), $meta_value_stamp);

				//echo strtotime(end($meta_value_date));
				//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
				if (isset($meta_value_date[1])) {
					$time = str_replace('-', '', $meta_value_date[1]);
					$meta_value .= esc_html__(' at ', 'listeo_core');
					$meta_value .= date(get_option('time_format'), strtotime($time));
				}

				$booking_data['dates'] = $meta_value;
				break;
			case 'event':
				//$booking_data['dates'] = date(get_option( 'date_format' ), strtotime($args['date_start'])).' '.esc_html__(' at ','listeo_core').' '.date(get_option( 'time_format' ), strtotime($args['date_start']));
				$meta_value = get_post_meta($args['listing_id'], '_event_date', true);
				$meta_value_timestamp = get_post_meta($args['listing_id'], '_event_date_timestamp', true);

				$meta_value_date = explode(' ', $meta_value, 2);

				if (!empty($meta_value_timestamp)) {
					$meta_value = date_i18n(get_option('date_format'), $meta_value_timestamp);

					if (isset($meta_value_date[1])) {
						$time = str_replace('-', '', $meta_value_date[1]);
						$meta_value .= esc_html__(' at ', 'listeo_core');
						$meta_value .= date_i18n(get_option('time_format'), strtotime($time));
					}
					$booking_data['dates'] = $meta_value;
				} else {

					$meta_value_ = DateTime::createFromFormat(listeo_date_time_wp_format_php(), $meta_value_date[0]);
					//var_dump($meta_value);
					if (!is_string($meta_value_)) {
						$meta_value_stamp = $meta_value_->getTimestamp();
						$meta_value = date_i18n(get_option('date_format'), $meta_value_stamp);
					} else {
						$meta_value = $meta_value_date[0];
					}

					//echo strtotime(end($meta_value_date));
					//echo date( get_option( 'time_format' ), strtotime(end($meta_value_date)));
					if (isset($meta_value_date[1])) {
						$time = str_replace('-', '', $meta_value_date[1]);
						$meta_value .= esc_html__(' at ', 'listeo_core');
						$meta_value .= date_i18n(get_option('time_format'), strtotime($time));
					}
					$booking_data['dates'] = $meta_value;
				}
				break;

			default:
				# code...
				break;
		}

		if (isset($args['expiring'])) {
			$booking_data['expiring'] = $args['expiring'];
		}
		$booking_details = '';
		$details = json_decode($args['comment']);
		if (isset($details->childrens) && $details->childrens > 0) {
			$booking_data['children'] = sprintf(_n('%d Child', '%s Children', $details->childrens, 'listeo_core'), $details->childrens);
			$booking_details .= $booking_data['children'];
		}
		if (isset($details->adults) && $details->adults > 0) {
			$booking_data['adults'] = sprintf(_n('%d Guest', '%s Guests', $details->adults, 'listeo_core'), $details->adults);
			$booking_details .= $booking_data['adults'];
		}
		if (isset($details->tickets) && $details->tickets > 0) {
			$booking_data['tickets'] = sprintf(_n('%d Ticket', '%s Tickets', $details->tickets, 'listeo_core'), $details->tickets);
			$booking_details .= $booking_data['tickets'];
		}

		if (isset($details->service)) {
			$booking_data['service'] = listeo_get_extra_services_html($details->service);
		}

		//client data
		if (isset($details->first_name)) {
			$booking_data['client_first_name'] = $details->first_name;
		}
		if (isset($details->last_name)) {
			$booking_data['client_last_name'] = $details->last_name;
		}
		if (isset($details->email)) {
			$booking_data['client_email'] = $details->email;
		}
		if (isset($details->phone)) {
			$booking_data['client_phone'] = $details->phone;
		}


		if (isset($details->billing_address_1)) {
			$booking_data['billing_address'] = $details->billing_address_1;
		}
		if (isset($details->billing_postcode)) {
			$booking_data['billing_postcode'] = $details->billing_postcode;
		}
		if (isset($details->billing_city)) {
			$booking_data['billing_city'] = $details->billing_city;
		}
		if (isset($details->billing_country)) {
			$booking_data['billing_country'] = $details->billing_country;
		}

		if (isset($details->message)) {
			$booking_data['user_message'] = $details->message;
			$booking_data['message'] = $details->message;
		}


		if (isset($details->price)) {
			$booking_data['price'] = $details->price;
		}



		$booking_data['details'] = $booking_details;

		return $booking_data;
	}

	public function replace_shortcode($args, $body)
	{

		$tags =  array(
			'user_mail' 	=> "",
			'user_name' 	=> "",
			'booking_date' => "",
			'listing_name' => "",
			'listing_url' => '',
			'listing_address' => '',
			'listing_latitude'  => '',
			'listing_longitude'  => '',
			'listing_phone' => '',
			'listing_email' => '',
			'site_name' => '',
			'site_url'	=> '',
			'payment_url'	=> '',
			'expiration'	=> '',
			'dates'	=> '',
			'children'	=> '',
			'adults'	=> '',
			'user_message'	=> '',
			'tickets'	=> '',
			'service'	=> '',
			'details'	=> '',
			'login'	=> '',
			'password'	=> '',
			'first_name'	=> '',
			'last_name'	=> '',
			'login_url'	=> '',
			'sender'	=> '',
			'conversation_url'	=> '',
			'client_first_name' => '',
			'client_last_name' => '',
			'client_email' => '',
			'client_phone' => '',
			'billing_address' => '',
			'billing_postcode' => '',
			'billing_city' => '',
			'billing_country' => '',
			'price' => '',
			'expiring' => '',
		);
		$tags = array_merge($tags, $args);

		extract($tags);

		$tags 	= array(
			'{user_mail}',
			'{user_name}',
			'{booking_date}',
			'{listing_name}',
			'{listing_url}',
			'{listing_address}',
			'{listing_latitude}',
			'{listing_longitude}',
			'{listing_phone}',
			'{listing_email}',
			'{site_name}',
			'{site_url}',
			'{payment_url}',
			'{expiration}',
			'{dates}',
			'{children}',
			'{adults}',
			'{user_message}',
			'{tickets}',
			'{service}',
			'{details}',
			'{login}',
			'{password}',
			'{first_name}',
			'{last_name}',
			'{login_url}',
			'{sender}',
			'{conversation_url}',
			'{client_first_name}',
			'{client_last_name}',
			'{client_email}',
			'{client_phone}',
			'{billing_address}',
			'{billing_postcode}',
			'{billing_city}',
			'{billing_country}',
			'{price}',
			'{expiring}',
		);

		$values  = array(
			$user_mail,
			$user_name,
			$booking_date,
			$listing_name,
			$listing_url,
			$listing_address,
			$listing_latitude,
			$listing_longitude,
			$listing_phone,
			$listing_email,
			get_bloginfo('name'),
			get_home_url(),
			$payment_url,
			$expiration,
			$dates,
			$children,
			$adults,
			$user_message,
			$tickets,
			$service,
			$details,
			$login,
			$password,
			$first_name,
			$last_name,
			$login_url,
			$sender,
			$conversation_url,
			$client_first_name,
			$client_last_name,
			$client_email,
			$client_phone,
			$billing_address,
			$billing_postcode,
			$billing_city,
			$billing_country,
			$price,
			$expiring,
		);

		$message = str_replace($tags, $values, $body);

		$message = nl2br($message);
		$message = htmlspecialchars_decode($message, ENT_QUOTES);

		return $message;
	}
}
