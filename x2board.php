<?php
/**
 * Plugin Name:       x2board WP
 * Description:       A qualified Korean style bbs plugin evolved from the XE2 board module
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.0.1
 * Author:            singleview.co.kr
 * Author URI: https://singleview.co.kr/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       x2board
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if ( !defined( 'X2B_VERSION' ) ) {
    define('X2B_VERSION', '0.0.1');
}
if ( !defined( 'X2B_DOMAIN' ) ) {
    define('X2B_DOMAIN', 'x2board');
}
if ( !defined( 'X2B_ADMIN_PAGE_TITLE' ) ) {
    define('X2B_ADMIN_PAGE_TITLE', X2B_DOMAIN);
}
if ( !defined( 'X2B_PAGE_IDENTIFIER' ) ) {
    define('X2B_PAGE_IDENTIFIER', 'Keep this mark, x2board-installed');
}

if ( !defined( 'X2B_SKIN_VAR_IDENTIFIER' ) ) {
    define('X2B_SKIN_VAR_IDENTIFIER', 'svs32_');  // svs is abbreviation of skin vars, 32 is meaningless seldom used string
}

if ( !defined( 'X2B_REWRITE_OPTION_TITLE' ) ) {
    define('X2B_REWRITE_OPTION_TITLE', X2B_DOMAIN.'_settings_rewrite');
    define('X2B_IFRAME_WHITELIST', X2B_DOMAIN.'_iframe_whitelist');
    define('X2B_ENDORSE_PLUGIN', X2B_DOMAIN.'_endorse_plugin');
}

if ( !defined( 'X2B__FILE__' ) ) {
    define('X2B__FILE__', __FILE__);
    define('X2B_PLUGIN_BASE', plugin_basename(X2B__FILE__));
    define('X2B_PATH', plugin_dir_path(X2B__FILE__));
    define('X2B_URL', plugins_url('/', X2B__FILE__));
    define('X2B_MODULES_NAME', 'modules');
}

if ( !defined( 'X2B_ALL_USERS' ) ) {  // for grant privileges
    define('X2B_ALL_USERS', '0');
    define('X2B_LOGGEDIN_USERS', '-1');
    // define('X2B_REGISTERED_USERS', '-2');
    define('X2B_ADMINISTRATOR', '-3');
    define('X2B_CUSTOMIZE', 'roles');
}

/*
 *----------------------------------------------------------------------------
 * Guest Service Functionality
 *----------------------------------------------------------------------------
 */
if ( !is_admin() || !defined( 'WP_CLI' ) ) {
	require_once X2B_PATH . 'includes/user.php';
}

/*
 *----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin.php';
} // End if.