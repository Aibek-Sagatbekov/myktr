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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'kazteleradio' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Ms@s19a01k11' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         '31AgbYY:;axXLC%#)GtI,R.XvBuCbJ^.GsTE>)WQU_n?>?|$R+TE0Q5hEYt$EoYF' );
define( 'SECURE_AUTH_KEY',  'Jv6w.`#6ixPazl.!e! o ]SD[EQ[<YV[z |C2JHR2QA]E]2N.X0y_KtjN)[sQzU/' );
define( 'LOGGED_IN_KEY',    '~~Ka6rY x4qXJch?,fOyV_*3IMQ)u?2`g,t/l**GZpo $c`f].$nyZ|WtRV+i`lH' );
define( 'NONCE_KEY',        'aD#DL+D-$`^nW$b!]7C@#VJ;.cxqsp<1 [v+dRXkuLi5 L%w,s/a^ s}|%Q|QR58' );
define( 'AUTH_SALT',        '])BE@-:<2@nK6mHZpf2iKALs=x5>}^/b]:SwUN?h>vN=MGGCuDO6=#<~,>RXyn:1' );
define( 'SECURE_AUTH_SALT', 'zBQ]Nl~*LmiO`:dLW{78HcW,w|s!H04hl(lKJD =M-/~RGIzy<; t7A&emIYFZGG' );
define( 'LOGGED_IN_SALT',   'nB}0X>j+Uzt-olFV-Ry@iJAu&>7r#k7(LR;Mh@~v^b}YfU900!wxP}tZ2%fd/s%[' );
define( 'NONCE_SALT',       'e%BJN=%a=?AB0dPcLaUm7-(/B6Z]+0r}G>qhL]c80Z,=g0[AC1Cm8:+<!jzooe&S' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
