<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'a82u42121112656');

/** MySQL database username */
define('DB_USER', 'a82u42121112656');

/** MySQL database password */
define('DB_PASSWORD', 'si5rD6RXFZdN#');

/** MySQL hostname */
define('DB_HOST', 'a82u42121112656.db.42121112.7a3.hostedresource.net:3312');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'd/x60JEMP-H(m8UAa5Kf');
define('SECURE_AUTH_KEY',  'S#3=rC+Kt#7B!GCQ49%N');
define('LOGGED_IN_KEY',    '_n*#jj)@!UF9#KIX9_tZ');
define('NONCE_KEY',        'rZVWc)2_HS9=PTSDHF@a');
define('AUTH_SALT',        'w#aDtTyIZ4tk2v6YQU$&');
define('SECURE_AUTH_SALT', 'm(kr$qdF5OFcT*2KYDOt');
define('LOGGED_IN_SALT',   'TGnG6HfWLDJw &w@0J/a');
define('NONCE_SALT',       'zP2=v_qJ%1yq)#+U1Rkf');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_4w5t8bdqk4_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
//define( 'WP_CACHE', true );
require_once( dirname( __FILE__ ) . '/gd-config.php' );
define( 'FS_METHOD', 'direct');
define('FS_CHMOD_DIR', (0705 & ~ umask()));
define('FS_CHMOD_FILE', (0604 & ~ umask()));


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');