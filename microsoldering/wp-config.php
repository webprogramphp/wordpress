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
define( 'DB_NAME', 'microsoldering' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'nO=JG $wk/^&.jE&aI#^GM]N.D%Nxc3>gT=txfAnH~pSQP0,nJXGs$Vi1-?O)0!<' );
define( 'SECURE_AUTH_KEY',  '7YzH a$sje+KAcO8%cPzJ!HI<kajwMP?iHb9OCU/aK,$NMNQ76Sv}eRknm0I#J:m' );
define( 'LOGGED_IN_KEY',    'oeGR +>{uiHn[#6_S*l=Lu!j3(cg^pFBQ1|<MH?|xn#X#FW`Yiz)xvct2~58=H<&' );
define( 'NONCE_KEY',        'h$-Kv_2>HW1/4jo]hwbRe_6{V|@N~h@6)nTCd9zWMM;zrK%h:pPYZY*~D-Cw=!W#' );
define( 'AUTH_SALT',        'q,P30Uee{?)6)q,4u~)Eu<T?E8[?0TvS1VPd.5NUtrmK3pK#F3q?)vM*4:d1(j5#' );
define( 'SECURE_AUTH_SALT', 'UH$X^x:*~!vVjcBVkuXjT;L)M_)nj,c9^iS-,zNQfX48$j2d32c`mUq>8ty_0z.C' );
define( 'LOGGED_IN_SALT',   'DL@L>D*oKIjc(6n+w/l*87C]}Uxceo_|])4dk4s-!kKInpay<M&Q}vfaA~$_XaHA' );
define( 'NONCE_SALT',       'NKk@,>Wi$ZUE[!a}t+bWBA>_;!jv#1K`dr6ul=>&uHFqkyo=L{0JbA{k_|#M<fsH' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
