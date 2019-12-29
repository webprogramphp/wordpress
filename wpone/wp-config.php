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
define('DB_NAME', 'wpdatabaseone');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'FM*Qk-Z{@C{N@^kS+K_0M:w)Df1UYOIyK]o*ekjay`A0K8e:mcns],Q3k$wICr3Y');
define('SECURE_AUTH_KEY',  'uvI%r0uv9Be8(f(l%&%{CN]Kn:|N0eB`-f5O3<Gpz!RdVedi*9~4u#_QJyESnh,Z');
define('LOGGED_IN_KEY',    'eS$PL6YNH{eEAAM5@.```&pk aK6)ir{<0ptSKUsl#,+8xA6NK+>sgsM@jK/cx<r');
define('NONCE_KEY',        '?Xl _E;Ylll[x(+o<^&Oizk0?8$2qR*J#AVa8Pj3*ZR&z&zn4I$eeT`em<Lt//(e');
define('AUTH_SALT',        'X%_c@f>a4zOvJKGOZ.+sSP-?`SGTdvEqcaEm(uH`=x?kYt^zyw:n>_#;RY.`dJfH');
define('SECURE_AUTH_SALT', '7..WV]]@z ^=oNY$:ZI**ll@kJ|Yb(>9sA2yxfy{Z0m=%?-jB]SEn}c%Ya8~UiqR');
define('LOGGED_IN_SALT',   '3a4-EOWH]o.ZfFa1K`jkOID}Fv9Q_)pz6}-mue0>aHySt.>Y)ZKS6U<.HP?@?=S$');
define('NONCE_SALT',       '5Ned#.w(t]p;u8I@B}>yox-au+Fct=}S4`a0n+Ti6QLeS=cBOvl^[HvDUCNmJ[=C');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_MEMORY_LIMIT','256M');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
