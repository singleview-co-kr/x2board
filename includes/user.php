<?php
/**
 * The user-specific functionality of the plugin.
 *
 * @author  https://singleview.co.kr/
 */

namespace X2board\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! defined( 'X2B_CMD_VIEW_LIST' ) ) {
	// define GET view cmd
	define( 'X2B_CMD_VIEW_LIST', 'view_list' );
	define( 'X2B_CMD_VIEW_POST', 'view_post' );
	define( 'X2B_CMD_VIEW_WRITE_POST', 'view_write_post' );
	define( 'X2B_CMD_VIEW_MODIFY_POST', 'view_modify_post' );
	define( 'X2B_CMD_VIEW_DELETE_POST', 'view_delete_post' );
	define( 'X2B_CMD_VIEW_REPLY_POST', 'view_reply_post' );
	define( 'X2B_CMD_VIEW_WRITE_COMMENT', 'view_write_comment' );
	define( 'X2B_CMD_VIEW_REPLY_COMMENT', 'view_reply_comment' );
	define( 'X2B_CMD_VIEW_MODIFY_COMMENT', 'view_modify_comment' );
	define( 'X2B_CMD_VIEW_DELETE_COMMENT', 'view_delete_comment' );
	define( 'X2B_CMD_VIEW_MESSAGE', 'view_message' );
	// define( 'X2B_CMD_VIEW_MANAGE_POST', 'view_manage_post' );

	// define POST controller cmd
	// this method should be registered into \x2board\includes\func.inc.php::init_proc_cmd()
	define( 'X2B_CMD_PROC_WRITE_POST', 'proc_write_post' );
	define( 'X2B_CMD_PROC_VERIFY_PASSWORD', 'proc_verify_password' );
	define( 'X2B_CMD_PROC_MODIFY_POST', 'proc_modify_post' );
	define( 'X2B_CMD_PROC_DELETE_POST', 'proc_delete_post' );
	define( 'X2B_CMD_PROC_WRITE_COMMENT', 'proc_write_comment' );
	define( 'X2B_CMD_PROC_DELETE_COMMENT', 'proc_delete_comment' );
	define( 'X2B_CMD_PROC_DOWNLOAD_FILE', 'proc_download_file' );
	define( 'X2B_CMD_PROC_OUTPUT_FILE', 'proc_output_file' );

	// define AJAX controller cmd
	define( 'X2B_CMD_PROC_AJAX_FILE_UPLOAD', 'proc_ajax_file_upload' );
	define( 'X2B_CMD_PROC_AJAX_FILE_DELETE', 'proc_ajax_file_delete' );
	define( 'X2B_CMD_PROC_AJAX_POST_ADD_CART', 'proc_ajax_post_add_cart' );
	define( 'X2B_CMD_PROC_AJAX_RENDER_MANAGE_X2B_POST', 'proc_ajax_render_manage_x2b_post' );
	define( 'X2B_CMD_PROC_AJAX_MANAGE_POST', 'proc_ajax_manage_post' );
}

if ( ! defined( 'X2B_AJAX_SECURITY' ) ) {
	define( 'X2B_AJAX_SECURITY', X2B_DOMAIN . '_ajax_security' );
}

if ( ! defined( 'X2B_JS_HANDLER_USER' ) ) {
	define( 'X2B_JS_HANDLER_USER', X2B_DOMAIN . '-script-user' );
}

if ( ! defined( 'X2B_JQUERY_VALIDATION' ) ) {
	define( 'X2B_JQUERY_VALIDATION', X2B_DOMAIN . '-jquery-validate-min' );
}

if ( ! defined( 'X2B_CACHE_PATH' ) ) {
	define( 'X2B_CACHE_PATH', X2B_DOMAIN . DIRECTORY_SEPARATOR . 'cache' );
}

if ( ! defined( 'X2B_ATTACH_FILE_PATH' ) ) {
	define( 'X2B_ATTACH_FILE_PATH', X2B_DOMAIN . DIRECTORY_SEPARATOR . 'attach' );
}

global $G_X2B_CACHE;
require_once X2B_PATH . 'includes/func.inc.php';
add_action( 'init', '\X2board\Includes\init_proc_cmd', 5 );
add_action( 'init', '\X2board\Includes\init_custom_route', 5 );
add_action( 'template_redirect', '\X2board\Includes\register_content_filter' );
add_action( 'wp_enqueue_scripts', '\X2board\Includes\enqueue_user_scripts', 999 );
add_action( 'plugins_loaded', '\X2board\Includes\plugin_loaded' );
add_action( X2B_DOMAIN . 'notify_new', '\X2board\Includes\notify_via_slack', 10, 3 );
add_filter( 'document_title', '\X2board\Includes\change_browser_title', 10, 2 );
add_shortcode( X2B_DOMAIN, '\X2board\Includes\launch_shortcode' );
