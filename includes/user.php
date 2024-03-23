<?php
/**
 * The user-specific functionality of the plugin.
 *
 * @link  https://singleview.co.kr/
 * @since 2.6.0
 *
 * @package    x2board
 * @subpackage User
 */

namespace X2board\Includes;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if ( !defined( 'X2B_DISP_LIST' ) ) {
    // define view cmd
    define('X2B_CMD_VIEW_LIST', 'view_list');
    define('X2B_CMD_VIEW_POST', 'view_post');
    define('X2B_CMD_VIEW_WRITE_POST', 'view_write_post');
    define('X2B_CMD_VIEW_MODIFY_POST', 'view_modify_post');
    define('X2B_CMD_VIEW_DELETE_POST', 'view_delete_post');
    define('X2B_CMD_VIEW_REPLY_POST', 'view_reply_post');
    define('X2B_CMD_VIEW_WRITE_COMMENT', 'view_write_comment');
    define('X2B_CMD_VIEW_REPLY_COMMENT', 'view_reply_comment');
    define('X2B_CMD_VIEW_MODIFY_COMMENT', 'view_modify_comment');
    define('X2B_CMD_VIEW_DELETE_COMMENT', 'view_delete_comment');
    // define controller cmd
    define('X2B_CMD_PROC_WRITE_POST', 'proc_write_post');
    define('X2B_CMD_PROC_MODIFY_POST', 'proc_modify_post');
}

global $G_X2B_CACHE;
require_once X2B_PATH . 'includes/func.inc.php';
add_action( 'init', '\X2board\Includes\init_proc_cmd', 5);
add_action( 'template_redirect', '\X2board\Includes\register_content_filter' );
add_action( 'wp_enqueue_scripts', '\X2board\Includes\enqueue_user_scripts', 999 );
add_action( 'plugins_loaded', '\X2board\Includes\plugin_loaded');