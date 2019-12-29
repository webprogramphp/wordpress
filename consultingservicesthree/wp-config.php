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
define( 'DB_NAME', 'consultingservicesthree' );

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
define( 'AUTH_KEY',         'DX(EY3>M}F9nFK-K14.lIO+)c1Uix~Br8pp;PLV/Z.%*zH3J?fu,J3N|Q>^kqL=/' );
define( 'SECURE_AUTH_KEY',  'gr,%J7Mz_4B0AO X93ypuW]^avjDXz-eU4]R1lEhAk!~Rla`d$/yP>4 yqG?*pB~' );
define( 'LOGGED_IN_KEY',    'OS9|sU#5<$#g*=h*Q@D>WAV1jA2Ng`[$|Au5-j3,+H5],oP8E,woo_lLL(k]69pt' );
define( 'NONCE_KEY',        'zKvbkP1XJn|4T0d(HbY,w#&IN K/[k`b<Y00J?}$m2q tWm&?47BVeZce,C22R4f' );
define( 'AUTH_SALT',        'lSMP?<I:_Trv]F^qRWq/*~LFCdlSHa]^2lD[{!B:c_o%9-~m4dvS-$%T[3QiK[dU' );
define( 'SECURE_AUTH_SALT', '<2A9_8n*0o!SE+E=qhGJ]$*{4vG?hrefPk>kZ+DW~W4B>LxWab `<`}._N)_06uG' );
define( 'LOGGED_IN_SALT',   '?f}a<T?2A5o@93bmR| zytZG)@NuIQe_),COB9HQXteF_.$zBHi$Ghh_@GR*yK H' );
define( 'NONCE_SALT',       '8P/Ah]f96g-+asp.CLpaR+E|$RyZPOO6+h>$_+j?1lkj{M&u.eTZt!oM]|xVM$ t' );

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
