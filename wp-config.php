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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dev_woo_test' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'qwerty' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '?|P93xh.WkqIq8o>%pJ#^k-|^qdzx;`?+#p({E,h)aBG!G/+rM}._+2%5A4PF0]}');
define('SECURE_AUTH_KEY',  'T(<m%VjZ<$kjt{#G1NkWyk|eS6$;u2p864&J<%sJ4@R0g>3`!x-M2!3|kfuCa`!]');
define('LOGGED_IN_KEY',    ',o[&tqv3G:XIQ|*E{*`9QnU&>4Y-W{45mGL{2#<%g3Y^ga=C{sT|SUV-n?h&H>l,');
define('NONCE_KEY',        '_=t,oo0xFcbvt=cetI1.pxzpEOzs+T)3#/+knOp}d6<(KSbs{UaSU-LwB8V6%/ R');
define('AUTH_SALT',        'bVEx;A<shw-ekkbu}m]Ab`3XI9h8X>5?K<yK|<Sl]O+m[>8snh`ceOuhG$v.MdQh');
define('SECURE_AUTH_SALT', 'eJCa5!hoev&]J%3k&/9Z|,jod1F&Ync}#zYJ]<8v}K-@F:0+jlA9PghWv`<s2KE(');
define('LOGGED_IN_SALT',   '<o=XC~STOv8xcCK/TOHAl|xY.u>Py62]`9^2|.79#!#@|nJOct)1Y:5i,vQYQ?#x');
define('NONCE_SALT',       'qA,. w^xqo|TZDP+-.uq4:<Cif6yU0ZK8v>9`F/+WPC/qrX%v73gfKo_|e/rtWui');


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
define('WP_DEBUG', true);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
