<?php

require 'wp-config-basics.php';

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
// set in wp-config-basics.php

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Id!hn6D>^$T++AV:J<c|!g>nc|Orj5,-7;dyt|<6AK5.W+fM%</8bTJq)6+Qc|{+');
define('SECURE_AUTH_KEY',  'gey$kY23AXSaX#Il}fIqs_3iD*y3gH$F=.<2{@fgB8AlG~_&>C;6RH5 ||dB+7Jp');
define('LOGGED_IN_KEY',    '0ZUS`V#_vEu;([>N 5@uDTb%,6:1z9Y_&VRyD|P<> d]y-Q=Q(<-$_v)(DDJapxo');
define('NONCE_KEY',        'a8rQaxm_`^@$g.-TKGkec*V56c,6eAf|^UGWj8^vsvD}O<B[y8aTGNOC.@0@u$HZ');
define('AUTH_SALT',        'kc?qS=0,<|E9k[o!5&uYgKl13pwAN4M5d+uB(Zac-8$[4d}RJM%;uN!h<+-},WNZ');
define('SECURE_AUTH_SALT', 'Xz-GLR-(Btn=x&4zZ+L+T(R_q?_#9inPO-Jq=<?.mqhLFB^`x]1P^ v|5^n[5:ad');
define('LOGGED_IN_SALT',   '_HCS]+t-v3Nc[)4R[ON4mzqc@>wMt?96*)A3CvJOzEteL>57E);}Q{+~d D[66/d');
define('NONCE_SALT',       ']4&a`o-3vu00p:zDDg3A4-GL{>A0MPEMc>)43e5t1z`3iQp/[_H-ary#mn<{R4|a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* Multisite */
//define( 'WP_ALLOW_MULTISITE', true );

//define('MULTISITE', true);
//define('SUBDOMAIN_INSTALL', false);

// Domain and path are set in wp-config-basics.php

define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

//define('FORCE_SSL_ADMIN', true);
//define('FORCE_SSL_ADMIN', true);
//define('FORCE_SSL_LOGIN', true);
//define('FORCE_SSL_ADMIN', true);
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
       $_SERVER['HTTPS']='on';

// BP_GROUPS_DEFAULT_EXTENSION doesn't always work and causes 404 errors when a group doesn't have a forum. Therefore, I commented it out and 
// found another way to change the default group tag
//https://buddypress.org/support/topic/changing-default-group-tab/
//https://buddypress.org/support/topic/bp_groups_default_extension/
//define( 'BP_GROUPS_DEFAULT_EXTENSION', 'forum' );
//define('WP_HOME','https://dd.edtechx.org/blog/');
//define('WP_SITEURL','https://dd.edtechx.org/blog/');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define('CONCATENATE_SCRIPTS', false);
define('FS_METHOD', 'direct');

define('DISABLE_WP_CRON', true);

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
