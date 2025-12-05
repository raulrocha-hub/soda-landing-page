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
//define( 'DB_NAME', 'u145728103_Nbb6K' );
define( 'DB_NAME', 'sodaperfeita' );
/** Database username */
//define( 'DB_USER', 'u145728103_D0eBa' );
define( 'DB_USER', 'root' );
/** Database password */
//define( 'DB_PASSWORD', 'dt4CXup0QW' );
define( 'DB_PASSWORD', '' );
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
define( 'AUTH_KEY',          'sHkm=3qu.7@a0` %rC(FU1UKwbXaxSq-xk +f:rQk7 96[#<33+zO <bC8ZEm_T3' );
define( 'SECURE_AUTH_KEY',   'E-}{a`o-ti~d$[e9c8Y0p>=zaTN#*O4Z_l,*^kesK)@La[dr!m5YxBu#H]UjQvl0' );
define( 'LOGGED_IN_KEY',     'm/4#Do1v{W8vM.*pgydEY^:O1eB%[~>PsKfyc*UB6;Zg@yomTpcU7/C1k-#}srZF' );
define( 'NONCE_KEY',         '1mNWLERIx&4uaOk,+U4S^K4Q0vTkMSRelDmo>:LY?yb 6pR b7Y*q-7.b9{u{*G/' );
define( 'AUTH_SALT',         'Grv&FeEy*ITk!.M@#XUXh3ts@sD*wmzZErdQ?A~Ij<z%:0bG}T/]=m=O8%3no.=(' );
define( 'SECURE_AUTH_SALT',  '@2wvp@AI!,2&+t$(b(+ r(Zg/f51hgmSNW=uX)ZA1_Q3!Sr]Ewc.Y@~M9pP{-^1a' );
define( 'LOGGED_IN_SALT',    '!^-`SuC/>F6SQcmb;+H|BjRI.6#<WO~>m_gR^4Pn1d<cW` VClFEL h5X@*M)z+^' );
define( 'NONCE_SALT',        'Ts4y6bJ/d,dbx/p(Dj-bz0@#a|!||t8)k4SSZGqBYk$buS(@5SI[QMcwY[Q1+o0o' );
define( 'WP_CACHE_KEY_SALT', 'w@MJbHM7BDfW282<@,_Q7PUjK%NII!K`=%jbC?FJkt@.#+99P! zP =]N7y/ct8{' );


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
define( 'COOKIEHASH', '2c156c4da2ef6bf9e9fed5a083a78584' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
