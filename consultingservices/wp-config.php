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
define( 'DB_NAME', 'consultingservices' );

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
define( 'AUTH_KEY',         'ds4H%GX6>-yu))0=e$I=jq# TY=Zdl1:-r(xt8IqQ]~<u+f3fQ$);@UqIUi?},Zg' );
define( 'SECURE_AUTH_KEY',  'B]#1U&Sl7-K$B1V>Rot3Q{))wP$+^aXF+6G!R{E0~>?Ym]E`r$*t^)kff$]quf{c' );
define( 'LOGGED_IN_KEY',    'KEsVXsg2G1n/rWn<`VVgL:oP1lfeO_i5;-{TZl2E[@~!FQFlX=<KA!5X3B<6MdfI' );
define( 'NONCE_KEY',        'i<)St;;,fAo*~s-SJXtmwWWAnB9hg:$*l[w@I31b41T*~Ptvu~Jj+1f7cy7N)LXQ' );
define( 'AUTH_SALT',        '(!+KyA0xfI_rM61(`wOu4efJ/DN)(wnw=CPHZRr2WZy9e?as&<N^s2#mp^NG]{yQ' );
define( 'SECURE_AUTH_SALT', '`7hsT6Y{ifrd_BSpUq*.Mbb6w5FVp=v]q{rYAXfZeYK.|X`,Z]7~-!#%:7H6TYj]' );
define( 'LOGGED_IN_SALT',   'v@!@S56E(qf9^o]o?,=IUSPJfbPKNBU}u,1M}!9=uVv)3&Y&:1X(lgA=vD.HSqA~' );
define( 'NONCE_SALT',       'heg%iC5@IldHy|@;r~W*WLo|5k)m{TPu&*7n%P[KuWU|n7l,[1fYl:u~{9,0cEZ)' );

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
