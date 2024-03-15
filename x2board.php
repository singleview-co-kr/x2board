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

if(!defined('ABSPATH')) exit;

if ( ! defined( 'X2B_VERSION' ) ) {
    define('X2B_VERSION', '0.0.1');
}
if ( ! defined( 'X2B_PAGE_TITLE' ) ) {
    define('X2B_PAGE_TITLE', __('x2Board', 'x2board'));
}
if ( ! defined( 'X2B_DOMAIN' ) ) {
    define('X2B_DOMAIN', 'x2board');
}
if ( ! defined( 'X2B__FILE__' ) ) {
    define('X2B__FILE__', __FILE__);
    define('X2B_PLUGIN_BASE', plugin_basename(X2B__FILE__));
    define('X2B_PATH', plugin_dir_path(X2B__FILE__));
    define('X2B_URL', plugins_url('/', X2B__FILE__));
}

/*
 *----------------------------------------------------------------------------
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

// require_once X2B_PATH . 'includes/admin/default-settings.php';
//  require_once X2B_PATH . 'includes/admin/register-settings.php';
//  require_once X2B_PATH . 'includes/plugin-activator.php';
//  require_once X2B_PATH . 'includes/i10n.php';
//  require_once X2B_PATH . 'includes/class-crp-query.php';
//  require_once X2B_PATH . 'includes/main-query.php';
//  require_once X2B_PATH . 'includes/output-generator.php';
//  require_once X2B_PATH . 'includes/media.php';
//  require_once X2B_PATH . 'includes/tools.php';
//  require_once X2B_PATH . 'includes/header.php';
//  require_once X2B_PATH . 'includes/content.php';
//  require_once X2B_PATH . 'includes/modules/manual-posts.php';
//  require_once X2B_PATH . 'includes/modules/cache.php';
//  require_once X2B_PATH . 'includes/modules/shortcode.php';
//  require_once X2B_PATH . 'includes/modules/taxonomies.php';
//  require_once X2B_PATH . 'includes/modules/exclusions.php';
//  require_once X2B_PATH . 'includes/modules/class-crp-rest-api.php';
//  require_once X2B_PATH . 'includes/modules/class-crp-widget.php';
//  require_once X2B_PATH . 'includes/blocks/register-blocks.php';

/*
 *----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once X2B_PATH . 'includes/admin/admin.php';
    require_once X2B_PATH . 'includes/modules/board/board.admin.view.php';
	// require_once X2B_PATH . 'includes/admin/settings-page.php';
	// require_once X2B_PATH . 'includes/admin/save-settings.php';
	// require_once X2B_PATH . 'includes/admin/help-tab.php';
	// require_once X2B_PATH . 'includes/admin/modules/tools.php';
	// require_once X2B_PATH . 'includes/admin/modules/loader.php';
	// require_once X2B_PATH . 'includes/admin/modules/metabox.php';
	// require_once X2B_PATH . 'includes/admin/modules/class-bulk-edit.php';
} // End if.

/*
 *----------------------------------------------------------------------------
 * Deprecated functions
 *----------------------------------------------------------------------------
 */

// require_once X2B_PATH . 'includes/deprecated.php';

/**
 * Global variable holding the current settings for X2 board
 *
 * @since 1.8.10
 *
 * @var array
 */
global $x2b_settings;
$x2b_settings = x2b_get_settings();


/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  2.6.0
 * @return array X2 board settings
 */
function x2b_get_settings() {

	$settings = get_option( 'x2b_settings' );

	/**
	 * Settings array
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 2.0.0
	 * @param array $settings Settings array
	 */
	return apply_filters( 'x2b_get_settings', $settings );
}
