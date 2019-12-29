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
define( 'DB_NAME', 'datasiteone' );

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
define( 'AUTH_KEY',         'PD&+i0sAYZX$!KYZOx>=_5h]/&sx/Zo7!2]E~C6[gE,peDx`6sQm`$,Z{-z@MJm<' );
define( 'SECURE_AUTH_KEY',  'u,<!A~2b$iMA`2=wbwo.?klWb_cF tbIlQ5~Y&#0;~$&t_yom9u 3H&WYbM+W:==' );
define( 'LOGGED_IN_KEY',    '=QNPrIaF:z(#<CWpxMc/Q:g<Z0!PYl<Q)*-`3E^eat^XH4M34`Y,ud&*b)OR^&h-' );
define( 'NONCE_KEY',        'FK(y{qnE`>PNT:Xd+Urp.!7cY=[w%qtZ~^izT7|W6`BY;T$!m4(|bf.+K@O2=}/l' );
define( 'AUTH_SALT',        'BDhw1p-%4MJXXEWmb`_EMK<oo7ghUwnC|V]>Rk|rQpqmn^uh<#h`JAf(=qtV#ehk' );
define( 'SECURE_AUTH_SALT', 'BXrz^a`=qR^amraXJ|O8jl(o@dwE<MR`-V!<X.Qkm?WOU/^1K&PK9qIwK{oal/3n' );
define( 'LOGGED_IN_SALT',   'ReRh_lQkxpP`0?LOXV2I+^zNHZC9]ajqnLLM}aiWmo>Fl9{Dn*X9,IEJxQvFxdPz' );
define( 'NONCE_SALT',       '_J01`NDf*Xf>>C5Q_,Er8U)~]!&x<X_+mHWKP3M..~F4<3Qse:CzQG5ID5eq!4o_' );

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
