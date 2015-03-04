<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'doctor');

/** MySQL database username */
define('DB_USER', 'doctor');

/** MySQL database password */
define('DB_PASSWORD', 'Aguilar01');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'H>W0|6o<grKIfn[iA?s56-;F86[_Zh<DCq4A1o~^<:(W-*U=$-=.AB6c0:4[{+ac');
define('SECURE_AUTH_KEY',  ';`:|.npxy]Gyu*]mqE|._<(FwYhy~CB<A-}Pgk_[*?F0x5cHbKT(+`8@/);+tJUF');
define('LOGGED_IN_KEY',    'f[:C^-Hn!>wepb%$6?%$:@dbbrVEZD_TNm_k8GL3va:3~a-][;ts.heJ)*I8QAWp');
define('NONCE_KEY',        '<*kD<v3,tZmHXN22~vA3-9j5Bimq,=h+RuNH7o|EgtPxh)La#Cc]lzH:>ZTSv0}`');
define('AUTH_SALT',        '`gS4m,Ht@}5IYmXH`CI)N9dbfNOG|h,rjBXnqjrO}b2b)V+Ss@|bY$4/gkv68vEI');
define('SECURE_AUTH_SALT', 'Fk7D~Me6Xg;<M=^x~`v+fR,,4gxs6LCD)`a6lf2aJKwG2=h./sL}RMBIKj)utEI]');
define('LOGGED_IN_SALT',   '&!Amj 4@vHTO5%V.4h42+-4[JL73oUCrU(Ix}Nt3HVj`Y>i0aut<67Y$_Ut6A-i2');
define('NONCE_SALT',       '}8?ap4hU+*m`gVlg6dk]Q+W(Z0$r]44;+}!NsFHq @,yom:5Cz%6MT+k7mxg&hTZ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
