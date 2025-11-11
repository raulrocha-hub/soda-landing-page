<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sodaperfeita' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         't)%Av=hc)F9kfz/_QS>9D*~R_K}(wU&QC#]n#KR]MRHU$XBt=_EX5.gEWrv7qqnR' );
define( 'SECURE_AUTH_KEY',  'ND~(cnvvqN_PzHby^WrL@3Y&a2]t.fF#q2W$_Sc2=J`@(Aj7(L~.4Dul`{<iYhn_' );
define( 'LOGGED_IN_KEY',    'N/@c%:&d3an0M3@i#aZK+2$fSsZw/,~F:.=lk#l43iCv2KG+mf5Fp)E:TMrh:cqU' );
define( 'NONCE_KEY',        'Hvkq{%/($+aODhTg7Nb$v64t4[wdO$H=<z.8i!6Ug_~S},K2N<{@8c(t{Wv$rD{;' );
define( 'AUTH_SALT',        '3:M6/&)}])If13uEsyV]h 3txYZzy^NpXfWUz!wa3n[lg3&Yun3nT^tdtDWD!+KZ' );
define( 'SECURE_AUTH_SALT', 'YHaBu`qKZ3u~#DP-Zr)sg[R&.[9^UUSR}!XkHbtO|Wv-t-%E#RG_q/~N@G6@W0Gh' );
define( 'LOGGED_IN_SALT',   'yBBRjQ+X/i<5uM|dg)a.=%OY!H)mryN!kwCcuAemyQzAc:hm,cC24*)hW6eg%]^L' );
define( 'NONCE_SALT',       'z^5O{QI9f<@:Kr,T:f]5x3#_UzNmZx%h,q2~VK00*+mb.>!+5Jp+)uo6mVsHH8]C' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
// Ativar o modo DEBUG do WordPress
define( 'WP_DEBUG', true );

// Registrar erros em um arquivo de log
define( 'WP_DEBUG_LOG', true );

// Desativar a exibição de erros na tela
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

// Usar versões de desenvolvimento de scripts e CSS (opcional)
define( 'SCRIPT_DEBUG', true );
define( 'WP_AUTO_UPDATE_CORE', false );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
