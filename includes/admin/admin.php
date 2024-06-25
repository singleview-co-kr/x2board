<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://singleview.co.kr
 * @since 2.6.0
 *
 * @package    x2board
 * @subpackage Admin
 */

namespace X2board\Includes\Admin;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if ( !defined( 'X2B_CMD_ADMIN_VIEW_IDX' ) ) {
    // define admin view cmd
    define('X2B_CMD_ADMIN_VIEW_IDX', 'x2b_disp_idx');
	define('X2B_CMD_ADMIN_VIEW_CP', 'x2b_disp_control_panel');
	define('X2B_CMD_ADMIN_VIEW_BOARD_LIST', 'x2b_disp_board_list');
	define('X2B_CMD_ADMIN_VIEW_BOARD_INSERT', 'x2b_disp_board_insert');
	define('X2B_CMD_ADMIN_VIEW_BOARD_UPDATE', 'x2b_disp_board_update');
	define('X2B_CMD_ADMIN_VIEW_BOARD_IMPORT', 'x2b_disp_board_import');
	define('X2B_CMD_ADMIN_VIEW_LATEST_POST', 'x2b_disp_latest_post');
	define('X2B_CMD_ADMIN_VIEW_LATEST_COMMENT', 'x2b_disp_latest_comment');
	define('X2B_CMD_ADMIN_VIEW_LATEST_FILE', 'x2b_disp_latest_file');
	
    // define admin controller cmd
    define('X2B_CMD_ADMIN_PROC_INSERT_BOARD', 'x2b_proc_insert_board');
	define('X2B_CMD_ADMIN_PROC_UPDATE_BOARD', 'x2b_proc_update_board');
	define('X2B_CMD_ADMIN_PROC_IMPORT_BOARD', 'x2b_proc_import_board');
	define('X2B_CMD_ADMIN_PROC_UPDATE_SEQ', 'x2b_proc_update_seq');
	define('X2B_CMD_ADMIN_PROC_INSERT_CATEGORY', 'x2b_proc_insert_category');  // ajax
	define('X2B_CMD_ADMIN_PROC_MANAGE_CATEGORY', 'x2b_proc_manage_category');  // ajax
	define('X2B_CMD_ADMIN_PROC_REORDER_CATEGORY', 'x2b_proc_reorder_category');  // ajax
}

if ( !defined( 'X2B_ADMIN_ATTACH_FILE_PATH' ) ) {
    define('X2B_ADMIN_ATTACH_FILE_PATH', X2B_DOMAIN.DIRECTORY_SEPARATOR.'admin');
	define('X2B_ADMIN_ATTACH_FILE_URL', X2B_DOMAIN.'/admin');
}

/*  Plugins Activation Hook */
function activate() {
	require_once X2B_PATH . 'includes/admin/schemas/schemas.php';
}

register_activation_hook( X2B__FILE__, 'X2board\Includes\Admin\activate' );

/* Plugins Loaded Hook */
function plugin_loaded() {
	// this block is to POedit recognize virtual locale elements
	__('lbl_title_content', X2B_DOMAIN);
	__('lbl_nick_name', X2B_DOMAIN);
	__('lbl_user_id', X2B_DOMAIN);
}

add_action( 'plugins_loaded', 'X2board\Includes\Admin\plugin_loaded' );

global $A_X2B_ADMIN_SETTINGS_PAGE;

/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 2.6.0
 *
 * @global 
 * @return void
 */
function add_admin_pages_links() {
	global $A_X2B_ADMIN_SETTINGS_PAGE;
	$A_X2B_ADMIN_SETTINGS_PAGE = array();

	global $_wp_last_object_menu;
	$_wp_last_object_menu++;
	// visible admin page
	add_menu_page(X2B_ADMIN_PAGE_TITLE, X2B_DOMAIN, 'manage_x2board', X2B_CMD_ADMIN_VIEW_IDX, 'X2board\Includes\Admin\disp_admin_board', 'dashicons-admin-post', $_wp_last_object_menu);
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(X2B_CMD_ADMIN_VIEW_IDX, X2B_ADMIN_PAGE_TITLE, __('cmd_create_board', X2B_DOMAIN), 'manage_x2board', X2B_CMD_ADMIN_VIEW_BOARD_INSERT, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(X2B_CMD_ADMIN_VIEW_IDX, X2B_ADMIN_PAGE_TITLE, __('cmd_board_list', X2B_DOMAIN), 'manage_x2board', X2B_CMD_ADMIN_VIEW_BOARD_LIST, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(X2B_CMD_ADMIN_VIEW_IDX, X2B_ADMIN_PAGE_TITLE, __('cmd_import_board', X2B_DOMAIN), 'manage_x2board', X2B_CMD_ADMIN_VIEW_BOARD_IMPORT, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(X2B_CMD_ADMIN_VIEW_IDX, X2B_ADMIN_PAGE_TITLE, __('lbl_control_panel', X2B_DOMAIN), 'manage_x2board', X2B_CMD_ADMIN_VIEW_CP, 'X2board\Includes\Admin\disp_admin_board' );
	// hidden admin page
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(null, X2B_ADMIN_PAGE_TITLE, null, 'manage_x2board', X2B_CMD_ADMIN_VIEW_LATEST_POST, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(null, X2B_ADMIN_PAGE_TITLE, null, 'manage_x2board', X2B_CMD_ADMIN_VIEW_LATEST_COMMENT, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_submenu_page(null, X2B_ADMIN_PAGE_TITLE, null, 'manage_x2board', X2B_CMD_ADMIN_VIEW_LATEST_FILE, 'X2board\Includes\Admin\disp_admin_board' );
	$A_X2B_ADMIN_SETTINGS_PAGE[] = add_options_page(
		X2B_DOMAIN,
		'quick board',
		'manage_x2board',
		X2B_CMD_ADMIN_VIEW_BOARD_UPDATE,
		'X2board\Includes\Admin\disp_admin_board'
	);
}
add_action( 'admin_menu', 'X2board\Includes\Admin\add_admin_pages_links', 99 );


/* Plugins Loaded Hook */
function admin_init() {
	// 관리자에게 manage_x2board 권한 추가
	$admin_role = get_role('administrator');
	if(!$admin_role->has_cap('manage_x2board')){
		$admin_role->add_cap('manage_x2board', true);
	}

	add_action('admin_post_'.X2B_CMD_ADMIN_PROC_INSERT_BOARD, 'X2board\Includes\Admin\proc_admin_board' );
	add_action('admin_post_'.X2B_CMD_ADMIN_PROC_UPDATE_BOARD, 'X2board\Includes\Admin\proc_admin_board' );
	add_action('admin_post_'.X2B_CMD_ADMIN_PROC_IMPORT_BOARD, 'X2board\Includes\Admin\proc_admin_board' );
	add_action('admin_post_'.X2B_CMD_ADMIN_PROC_UPDATE_SEQ, 'X2board\Includes\Admin\proc_admin_board' );
	add_action('wp_ajax_'.X2B_CMD_ADMIN_PROC_INSERT_CATEGORY, 'X2board\Includes\Admin\proc_admin_board' );  // ajax for sortable category UI
	add_action('wp_ajax_'.X2B_CMD_ADMIN_PROC_MANAGE_CATEGORY, 'X2board\Includes\Admin\proc_admin_board' );  // ajax for sortable category UI
	add_action('wp_ajax_'.X2B_CMD_ADMIN_PROC_REORDER_CATEGORY, 'X2board\Includes\Admin\proc_admin_board' );  // ajax for sortable category UI	

	register_timezone_gap();
}

/**
 * register timezone gap for \includes\func.inc.php::zgap()
 *
 * @return void
 */
function register_timezone_gap() {
	// $time_zone = $GLOBALS['_time_zone'];
	// if($time_zone < 0) {
	// 	$to = -1;
	// }
	// else {
	// 	$to = 1;
	// }
	$min       = 60 * get_option('gmt_offset');
	$sign      = $min < 0 ? "-" : "+";
	$absmin    = abs($min);
	$time_zone = sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60);
	$to = $time_zone < 0 ? -1 : 1;
	$t_hour = $absmin/60 * $to; // substr($time_zone, 1, 2) * $to;
	$t_min = $absmin%60 * $to; // substr($time_zone, 3, 2) * $to;
	$server_time_zone = date("O");
	$so = $server_time_zone < 0 ? -1 : 1;
	$c_hour = substr($server_time_zone, 1, 2) * $so;
	$c_min = substr($server_time_zone, 3, 2) * $so;
	$g_min = $t_min - $c_min;
	$g_hour = $t_hour - $c_hour;
	$gap = $g_min * 60 + $g_hour * 60 * 60;
	update_option( X2B_DOMAIN.'_timezone_gap', $gap );
}
	
add_action( 'admin_init', 'X2board\Includes\Admin\admin_init' );

/**
 * Trigger Board Admin View.
 *
 * @return void
 */
function disp_admin_board() {
	$o_module = new \X2board\Includes\Modules\Board\boardAdminView();
	$calling_method = isset($_REQUEST['page']) ? str_replace( 'x2b_', '', sanitize_text_field($_REQUEST['page']) ) : '';
	if(!method_exists( $o_module, $calling_method )) {
		wp_die( sprintf( __( 'msg_call_invalid_module', X2B_DOMAIN ), $calling_method ) );
	}
	$o_module->$calling_method();
	unset($o_module);
}

/**
 * Trigger Board Admin control.
 */
function proc_admin_board(){
	$o_module = new \X2board\Includes\Modules\Board\boardAdminController();
	$calling_method = isset($_REQUEST['action']) ? str_replace( 'x2b_', '', sanitize_text_field($_REQUEST['action']) ) : '';

	if( $calling_method == 'proc_update_board' && isset($_REQUEST['delete_board']))	{
		$calling_method = 'proc_delete_board';
	}

	if(!method_exists( $o_module, $calling_method )) {
		wp_die( sprintf( __( 'msg_call_invalid_module', X2B_DOMAIN ), $calling_method ) );
	}
	$o_module->$calling_method();
	unset($o_module);
	exit; // to execute wp_redirect(admin_url());
}

/**
 * Add rating links to the admin dashboard
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function footer( $footer_text ) {
	global $A_X2B_ADMIN_SETTINGS_PAGE;
	$current_screen = get_current_screen();
	if( in_array( $current_screen->id, $A_X2B_ADMIN_SETTINGS_PAGE, true ) ) {
		$text = sprintf(
			__( 'msg_thank_you_using_x2board', X2B_DOMAIN ),
			'https://singleview.co.kr/x2board',
			'https://wordpress.org/support/plugin/x2board/reviews/#new-post'
		);
		return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';
	} else {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'X2board\Includes\Admin\footer' );

/**
 * Enqueue Admin JS
 *
 * @param string $hook The current admin page.
 */
function load_scripts( $hook ) {
	global $A_X2B_ADMIN_SETTINGS_PAGE;

	// dummy script container to load $a_ajax_info below
	wp_register_script(
		X2B_DOMAIN . '-ajax-scripts',
		null,
		array(),
		X2B_VERSION,
		true
	);

	wp_register_style(
		X2B_DOMAIN . '-admin-style',
		X2B_URL . 'includes/admin/css/admin.css',
		array(),
		X2B_VERSION
	);

	$a_ajax_info= array(
		'cmd_ajax_insert_category' => X2B_CMD_ADMIN_PROC_INSERT_CATEGORY,
		'cmd_ajax_manage_category' => X2B_CMD_ADMIN_PROC_MANAGE_CATEGORY,
		'cmd_ajax_reorder_category' => X2B_CMD_ADMIN_PROC_REORDER_CATEGORY,
	);

	if ( in_array( $hook, $A_X2B_ADMIN_SETTINGS_PAGE, true ) ) {
		wp_enqueue_style( X2B_DOMAIN . '-admin-style' );
		wp_enqueue_script( X2B_DOMAIN . '-ajax-scripts' );
		wp_localize_script (X2B_DOMAIN . '-ajax-scripts', 'x2board_admin_ajax_info', $a_ajax_info );
		add_thickbox();
	}
}
add_action( 'admin_enqueue_scripts', 'X2board\Includes\Admin\load_scripts' );