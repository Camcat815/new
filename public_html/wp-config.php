<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u384784524_Xduk4' );

/** Database username */
define( 'DB_USER', 'u384784524_aaBtE' );

/** Database password */
define( 'DB_PASSWORD', '0XXFqqG6p4' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '|8sfP?e X}=5@74%bobhBQSa<6t)C,X4lAn$M(k)ab[SFd~:98.l|:N}M8#U?_NX' );
define( 'SECURE_AUTH_KEY',   'oTZIWgk^nSGK>ag&tWb^N32Dy8dV~,bcz^h~S.e>9@NZlR~L30Dd=TJEs*r@iqpD' );
define( 'LOGGED_IN_KEY',     '<dhbVu`S>*[Xkir2k~Hd_EASIR`k@Xwef;Oh:x/L.SR:.2c=}gV=8xJA,7/+`{[?' );
define( 'NONCE_KEY',         'lnC ]v|)CrR@R}`y<*>(kS9aU7m)Dk|O,&tD{f#x^<N^vl764P`TCp|!7(~MubSE' );
define( 'AUTH_SALT',         'H-%1i!p;fqD}9sZ>B/q4*FqC!ZS&=i=U-^^Tm^zYyLWV!:^P503^yS<Hf* htBVJ' );
define( 'SECURE_AUTH_SALT',  '&h@Ca}{kt49rVuA@r`H]#>`Go6d6l0<y]XRQ2cPy!E.rEZ}#cj4DtqB=x&*av!.5' );
define( 'LOGGED_IN_SALT',    'bC]n2mS/C/#FKM @B<Gyrm+8kwW@7Y5LP^^mc]](`WLq$-RbG0p.:nSZu=?X9qTB' );
define( 'NONCE_SALT',        '0W-hAWt-F~a&(%!O1+n%8DPf9Hq3^uM@y+{WhQmo&&nHhU($vU79f8EZ]Sr2sC`g' );
define( 'WP_CACHE_KEY_SALT', '*:.ixUM{? _e~+^L3PP(Dy99Vy;WtXh}&S0-5%$*T39HamqART{Se]ZDSM|ASSeB' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
