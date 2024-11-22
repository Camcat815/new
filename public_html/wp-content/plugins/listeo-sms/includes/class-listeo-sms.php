<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://themeforest.net/user/purethemes
 * @since      1.0.0
 *
 * @package    Listeo_Twilio_Sms
 * @subpackage Listeo_Twilio_Sms/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Listeo_Twilio_Sms
 * @subpackage Listeo_Twilio_Sms/includes
 * @author     Lukas Girek <contact@purethemes.net>
 */
class Listeo_Sms {


	/**
	 * The single instance of Listeo_Core.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	private $notification;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LISTEO_SMS_VERSION' ) ) {
			$this->version = LISTEO_SMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'listeo-sms';

		add_action('admin_menu', array($this, 'add_menu_item'));
		add_filter('listeo_settings_fields', array($this, 'sms_settings'),12,1);
		add_action('wp_ajax_nopriv_listeoajaxsendotp', array($this, 'send_otp'));
		
		include('class-listeo-notifications.php');

		$this->notification = Listeo_Sms_Notification::instance();
	}

	
	public function send_otp()
	{
		$phone = $_POST['phone'];
		$otp = mt_rand(1000, 9999);

		$transient_key = 'otp_' . $phone; // Create a unique transient key
		delete_transient($transient_key); // Delete any existing transients
		set_transient($transient_key, $otp, 5 * MINUTE_IN_SECONDS); // Store OTP for 5 minutes
		$body  = get_option('listeo_otp_body', __('Hi, here is your verification code {otp} for {site_name}', 'listeo_core'));


		$tags 	= array(

			'{otp}',
			'{site_name}',
			'{site_url}',
		
		);

		$values  = array(
			$otp,
			get_bloginfo('name'),
			get_home_url(),
		);

		$message = str_replace($tags, $values, $body);

		$message = nl2br($message);
		$message = htmlspecialchars_decode($message, ENT_QUOTES);

		
		$status = $this->notification->send($phone, $message);
		$status = true;
		if($status){
			wp_send_json_success(array('message' => 'OTP sent successfully'));
		} else {
			wp_send_json_error(array('message' => 'OTP sending failed'));
		}
		
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Listeo_Twilio_Sms_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Main Listeo_Core Instance
	 *
	 * Ensures only one instance of Listeo_Core is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Listeo_Core()
	 * @return Main Listeo_Core instance
	 */
	public static function instance($file = '', $version = '1.2.1')
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($file, $version);
		}
		return self::$_instance;
	} // End instance ()



	public function add_menu_item()
	{

		add_submenu_page('listeo_settings', 'SMS Settings', 'SMS Settings', 'manage_options', 'listeo_settings&tab=sms_settings',  array($this, 'settings_page'));
	}

	public function settings_page()
	{
		echo "dupa";
	}

	public function sms_settings($settings)
	{
		
		$settings['sms_settings'] = array(
			'title'                 => __('<i class="fa fa-sms"></i> SMS Options', 'listeo_core'),
			// 'description'           => __( 'Settings for single listing view.', 'listeo_core' ),
			'fields'                => array(

				array(
					'label'      => __('Enable SMS notifications', 'listeo_core'),
					'description'      => __('Adding listings by users will require purchasing a Listing Package', 'listeo_core'),
					'id'        => 'enable_sms',
					'type'      => 'checkbox',
				),
				array(
                    'id'            => 'otp_status',
                    'label'         => __('Enable OTP verifivation for registration', 'listeo_core'),
                    'description'   => __('Enable phone number verification for user registration', 'listeo_core'),
                    'type'          => 'checkbox',
                ),
				array(
					'id'        => 'otp_body',
					'label'      => __('OTP message text', 'listeo_core'),
					'description' => '' . __('Available tags are: ') . '<strong>{otp},{site_name},{site_url}</strong>',
					'default'      => __('Hi, here is your verification code {otp} for {site_name}', 'listeo_core'),
					'type'      => 'textarea',
				),
				array(
					'label' =>  '',
					'description' =>  __('<h3>Providers</h3>', 'listeo_core'),
					'type' => 'title',
					'id'   => 'general_sms_providers_listeo'
				),
				array(
					'label'      => __('Debug Mode', 'listeo_core'),
					'description'      => __('If debug mode is enabled all SMS will be saved in debug.log file in wp-content, systme won\'t sent any actuall sms', 'listeo_core'),
					'id'        => 'sms_debug',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Twilio Account SID', 'listeo_core'),
					'description'      => __('Adding listings by users will require purchasing a Listing Package', 'listeo_core'),
					'id'        => 'twilio_sid',
					'type'      => 'text',
				),
				array(
					'label'      => __('Twilio Auth Token', 'listeo_core'),
					'description'      => __('Adding listings by users will require purchasing a Listing Package', 'listeo_core'),
					'id'        => 'twilio_auth_token',
					'type'      => 'text',
				),
				
				array(
					'label'      => __('Twillio Phone umber', 'listeo_core'),
					'description'      => __('A Twilio phone number you purchased at twilio.com/console', 'listeo_core'),
					'id'        => 'twilio_number',
					'placeholder' 		=> '+123456789',
					'type'      => 'text',
				),

				// array(
				// 	'label'      => __('SMS throttle', 'listeo_core'),
				// 	'description'      => __('Limit sending SMS to users', 'listeo_core'),
				// 	'id'        => 'sms_throttle',
				// 	'type'      => 'checkbox',
				// ),
				// array(
				// 	'label'      => __('SMS throttle offset', 'listeo_core'),
				// 	'description'      => __('Limit sending SMS to users', 'listeo_core'),
				// 	'id'        => 'sms_throttle_offset',
				// 	'type'      => 'text',
				// ),
				
				// ----------- new_listing_sms
				array(
					'label' => __('Notification about new listing', 'listeo_core'),
					'description' =>  __('Sends SMS to author about his listing submitted', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_listeo_core_listing_submitted',
					'description' => '' . __('Available tags are: ') . '<strong>{user_name},{user_mail},{listing_date},{listing_name},{listing_url},{site_name},{site_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about submitted listing', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_listeo_core_listing_submitted_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __('Hi {user_name}, there was listing submitted from your account', 'listeo_core'),
					'id'        => 'sms_listeo_core_listing_submitted_body',
					'type'      => 'textarea',
				),
				array(
					'label'      => __('Enable this notification also for Admin', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message to admin', 'listeo_core'),
					'id'        => 'sms_listeo_core_admin_listing_submitted_status',
					'type'      => 'checkbox',
				),
				// -----------
				// ----------- expired_listing_sms
				array(
					'label' => __('Notification about expiring listing', 'listeo_core'),
					'description' =>  __('Sends SMS to author about his listing expired', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_expired_listing_sms',
					'description' => '' . __('Available tags are: ') . '<strong>{user_name},{user_mail},{listing_date},{listing_name},{listing_url},{site_name},{site_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about expired listing', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_expired_listing_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __('Hi {user_name}, your listing has expired', 'listeo_core'),
					'id'        => 'sms_expired_listing_body',
					'type'      => 'textarea',
				),
				// -----------
				// ----------- expiring_soon_listing_sms
				array(
					'label' => __('Notification about upcoming expiration of listing', 'listeo_core'),
					'description' =>  __('Sends SMS to author about his listing expired', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_expiring_soon_listing_sms',
					'description' => '' . __('Available tags are: ') . '<strong>{user_name},{user_mail},{listing_date},{listing_name},{listing_url},{site_name},{site_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about upcoming expiration of listing', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_expiring_soon_listing_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __('Hi {user_name}, your listing will expire in 5 days', 'listeo_core'),
					'id'        => 'sms_expiring_soon_listing_body',
					'type'      => 'textarea',
				),
				// -----------


				
				// ----------- published_listing_sms
				array(
					'label' => __('Notification about published listing', 'listeo_core'),
					'description' =>  __('Sends SMS to author about his listing being published', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_published_listing_sms',
					'description' => '' . __('Available tags are: ') . '<strong>{user_name},{user_mail},{listing_date},{listing_name},{listing_url},{site_name},{site_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about publishing listing', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_published_listing_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __('Hi {user_name}, your listing was published', 'listeo_core'),
					'id'        => 'sms_published_listing_body',
					'type'      => 'textarea',
				),
				// -----------


				//---- notify_owner_review_sms
				array(
					'label' => __('Notification about new review on listing', 'listeo_core'),
					'description' =>  __('Sends SMS to author about his listing being reviewd', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_notify_owner_review_sms',
					'description' => '' . __('Available tags are: ') . '<strong>{user_name},{user_mail},{listing_date},{listing_name},{listing_url},{site_name},{site_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about new review listing', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_notify_owner_review_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}, There's new review added to your listing {listing_name}", 'listeo_core'),
					'id'        => 'sms_notify_owner_review_body',
					'type'      => 'textarea',
				),


				//-- sms_to_user_waiting_approval
				array(
					'label' => __('Notification about Booking waiting for approval ', 'listeo_core'),
					'description' =>  __('Sends SMS to guest about his booking waiting for approval', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_waiting_approval',
					'description' => '<br>' . __('Available tags are:') . '{user_mail},{user_name},{booking_date},{listing_name},{listing_url},{listing_address},{listing_phone},{listing_email},{site_name},{site_url},{dates},{details},
                        ,{dates},{user_message},{service},{details},{client_first_name},{client_last_name},{client_email},{client_phone},{billing_address},{billing_postcode},{billing_city},{billing_country},{price}',
				),
				array(
					'label'      => __('Enable notification about booking waiting for approval', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_waiting_approval_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}, Thank you for your booking request on {listing_name} for {dates}. Please wait for confirmation and further instruction", 'listeo_core'),
					'id'        => 'sms_to_user_waiting_approval_body',
					'type'      => 'textarea',
				),


				//-- sms_to_user_instant_approval
				array(
					'label' => __('Notification about instant booking approval ', 'listeo_core'),
					'description' =>  __('Sends SMS to guest about his booking approval', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_instant_approval',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about instant booking', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_instant_approval_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}, Thank you for your booking request on {listing_name} for {dates}. Please wait for confirmation and further instructions", 'listeo_core'),
					'id'        => 'sms_to_user_instant_approval_body',
					'type'      => 'textarea',
				),

				//-- sms_to_user_free_confirmed
				array(
					'label' => __('Notification about instant booking approval ', 'listeo_core'),
					'description' =>  __('Sends SMS to guest about his booking approval', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_free_confirmed',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about instant booking', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_free_confirmed_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! Thank you for your booking request on {listing_name} for {dates}. Please wait for confirmation and further instructions", 'listeo_core'),
					'id'        => 'sms_to_user_free_confirmed_body',
					'type'      => 'textarea',
				),

				//-- sms_to_user_pay_cash_confirmed

				array(
					'label' => __('Notification about booking paid in cash ', 'listeo_core'),
					'description' =>  __('Sends SMS to guest about his booking he needs to pay in cash', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_pay_cash_confirmed',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking paid in cash', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_pay_cash_confirmed_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! Your booking request on '{listing_name}' for {dates} was approved. See you soon!", 'listeo_core'),
					'id'        => 'sms_to_user_pay_cash_confirmed_body',
					'type'      => 'textarea',
				),

				// -- sms_to_owner_new_reservation
				array(
					'label' => __('Notification to owner about new booking ', 'listeo_core'),
					'description' =>  __('Sends SMS to owner about new booking', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_owner_new_reservation',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about new booking', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_owner_new_reservation_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! There is new booking on '{listing_name}' for {dates}.", 'listeo_core'),
					'id'        => 'sms_to_owner_new_reservation_body',
					'type'      => 'textarea',
				),

				// sms_to_owner_new_instant_reservation
				array(
					'label' => __('Notification to owner about new Instant booking ', 'listeo_core'),
					'description' =>  __('Sends SMS to owner about new instant booking', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_owner_new_instant_reservation',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about new instant booking', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_owner_new_instant_reservation_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! There is new instant booking on '{listing_name}' for {dates}.", 'listeo_core'),
					'id'        => 'sms_to_owner_new_instant_reservation_body',
					'type'      => 'textarea',
				),

				// sms_to_user_canceled
				array(
					'label' => __('Notification to user about his booking cancellation', 'listeo_core'),
					'description' =>  __('Sends SMS to guest about his booking cancellation', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_canceled',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking cancellation', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_canceled_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! Your booking on '{listing_name}' for {dates} was cancelled.", 'listeo_core'),
					'id'        => 'sms_to_user_canceled_body',
					'type'      => 'textarea',
				),

				// sms_to_owner_canceled
				array(
					'label' => __('Notification to owner about  booking cancellation', 'listeo_core'),
					'description' =>  __('Sends SMS to owner about  booking cancellation', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_owner_canceled',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking cancellation', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_owner_canceled_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}
                    The booking on '{listing_name}' for {dates} was cancelled.", 'listeo_core'),
					'id'        => 'sms_to_owner_canceled_body',
					'type'      => 'textarea',
				),

				// sms_to_user_pay
				array(
					'label' => __('Notification to user about booking waiting for payment', 'listeo_core'),
					'description' =>  __('Sends SMS to user about  booking cancellation', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_pay',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking waiting for payment', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_pay_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! The booking on '{listing_name}' for {dates} is waiting for your payment.", 'listeo_core'),
					'id'        => 'sms_to_user_pay_body',
					'type'      => 'textarea',
				),
				// sms_to_owner_paid
				array(
					'label' => __('Notification to owner about booking paid', 'listeo_core'),
					'description' =>  __('Sends SMS to owner about  booking paid', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_owner_paid',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking paid', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_owner_paid_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! The booking on '{listing_name}' for {dates} was paid.", 'listeo_core'),
					'id'        => 'sms_to_owner_paid_body',
					'type'      => 'textarea',
				),
				// sms_to_user_paid
				array(
					'label' => __('Notification to user about booking paid', 'listeo_core'),
					'description' =>  __('Sends SMS to user about  booking paid', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_paid',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking paid', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_paid_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! The booking on '{listing_name}' for {dates} was paid.", 'listeo_core'),
					'id'        => 'sms_to_user_paid_body',
					'type'      => 'textarea',
				),
				// user_waiting_payment_sms
				array(
					'label' => __('Notification to user about booking waiting for payment', 'listeo_core'),
					'description' =>  __('Sends SMS to user about  booking waiting for payment', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_user_waiting_payment_sms',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking waiting for payment', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'user_waiting_payment_sms_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! The booking on '{listing_name}' for {dates} is waiting for your payment.", 'listeo_core'),
					'id'        => 'user_waiting_payment_sms_body',
					'type'      => 'textarea',
				),
				// sms_to_user_upcoming_booking
				array(
					'label' => __('Notification to user about upcoming booking', 'listeo_core'),
					'description' =>  __('Sends SMS to user about  upcoming booking', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_upcoming_booking',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about upcoming booking', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_upcoming_booking_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! You have a booking on '{listing_name}' for {dates}. See you soon!", 'listeo_core'),
					'id'        => 'sms_to_user_upcoming_booking_body',
					'type'      => 'textarea',
				),
				// sms_to_user_review_reminder
				array(
					'label' => __('Reminder to user about reviewing the booking', 'listeo_core'),
					'description' =>  __('Sends SMS to user about  reviewing  booking', 'listeo_core'),
					'type' => 'title',
					'id'   => 'header_sms_to_user_review_reminder',
					'description' => '' . __('Available tags are: ') . '<strong>{user_mail},{user_name},{booking_date},{listing_name},{listing_url}</strong>',
				),
				array(
					'label'      => __('Enable notification about booking review ', 'listeo_core'),
					'description'      => __('Check this checkbox to enable sending this message', 'listeo_core'),
					'id'        => 'sms_to_user_review_reminder_status',
					'type'      => 'checkbox',
				),
				array(
					'label'      => __('Notification text', 'listeo_core'),
					'default'      => __("Hi {user_name}! How was your experience with '{listing_name}' for {dates}. Please share!", 'listeo_core'),
					'id'        => 'sms_to_user_review_reminder_body',
					'type'      => 'textarea',
				),
			),


		);

		return $settings;
	}



}
