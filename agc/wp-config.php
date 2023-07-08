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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'agc' );

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
define( 'AUTH_KEY',         '}hgm3~`7#Lk.C Z-0`YCE4|#00[7Bog|9nhN?{M;VVD_zp+%qygq({P6 soxDM$i' );
define( 'SECURE_AUTH_KEY',  'F*sH;!Tx@DI>hR9k-$U,$Ob%>tyi=~0y_<)s6-k*m-*8m7!CSA t]]}6hbEF1*<,' );
define( 'LOGGED_IN_KEY',    '91kA*.R]h H,zA=.u9xs_8$5!78256+!1>&4<+ieCVf_XZ/$?G+NBb^pI1czxj-i' );
define( 'NONCE_KEY',        'NPV%~*3GwxRY=;O(1$,%ic:>fm EIsm?Zg28T3J#;Jn*T%OYg=^Q^:KFW#!cfZM-' );
define( 'AUTH_SALT',        'Fu*/,{F#!P@=|SmodPG=:%5j-E<{3C$z/~b,0#xu(UPe*wTpEVZDzfCuM.NNAju|' );
define( 'SECURE_AUTH_SALT', '-prl:XR!7i6%4E$@@:F!-+x?oo-0=wVXKpx-w,igkH?l!e@9,Tt)[L3&4gC@sl7$' );
define( 'LOGGED_IN_SALT',   'HQ;Swu+qZ3rHAk,f#`]t]!I>?Q[Wo6Ela(pfg;)0a&*A<AA~;y$S N{mIQq&@W-3' );
define( 'NONCE_SALT',       '`<=~mTWDYBwu=i< FHO|<3C0Ws:,:y!+uTh>A[s8~pE|epXWQ4v ppc6Cyu1 *%2' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
