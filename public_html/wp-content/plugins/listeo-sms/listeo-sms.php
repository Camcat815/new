<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Listeo SMS Notifications & OTP Verification
 * Plugin URI:        https://listeo.pro
 * Description:       SMS notifications for Listeo
 * Version:           1.1.4
 * Author:            Purethemes
 * Author URI:        https://themeforest.net/user/purethemes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       listeo-sms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LISTEO_SMS_VERSION', '1.1.0' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-listeo-sms.php';

/**
 * Returns the main instance of listeo_core to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object listeo_core
 */
function Listeo_Sms()
{
	$instance = Listeo_Sms::instance(__FILE__, '1.0');

	/*if ( is_null( $instance->settings ) ) {
		$instance->settings =  Listeo_Core_Settings::instance( $instance );
	}*/


	return $instance;
}
$GLOBALS['listeo_sms'] = Listeo_Sms();

require __DIR__ . '/vendor/autoload.php';

Listeo_Sms();