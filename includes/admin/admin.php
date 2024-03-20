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

/*  Plugins Activation Hook */
function activate() {
	global $wpdb;
	
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset_collate = $wpdb->get_charset_collate();
	
	dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_sequence` (
	`seq` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`seq`)
	) {$charset_collate};");
	
	dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_mapper` (
	`board_id` bigint(20) unsigned NOT NULL,
	`wp_page_id` bigint(20) unsigned NOT NULL,
	`board_name` varchar(127) NOT NULL,
	`create_date` datetime NOT NULL,
	PRIMARY KEY (`board_id`)
	) {$charset_collate};");

	dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_post` (
	`post_id` bigint(20) unsigned NOT NULL,
	`board_id` bigint(20) unsigned NOT NULL,
	`parent_post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
	`category_id` bigint(20) unsigned DEFAULT 0,
	`ua` char(1) NOT NULL,
	`post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
	`nick_name` varchar(127) NOT NULL,
	`title` varchar(127) NOT NULL,
	`title_bold` char(1) NOT NULL DEFAULT 'N',
	`title_color` varchar(7),
	`content` longtext NOT NULL,
	`email_address` varchar(25),
	`password` varchar(60) NOT NULL,
	`comment_count` int(10) unsigned NOT NULL,
	`readed_count` int(10) unsigned NOT NULL,
	`like` int(10) unsigned NOT NULL,
	`unlike` int(10) unsigned NOT NULL,
	`is_notice` char(1) NOT NULL DEFAULT 'N',
	`is_secret` char(1) NOT NULL DEFAULT 'N',
	`allow_search` char(1) NOT NULL DEFAULT 'Y',
	`allow_comment` char(1) NOT NULL DEFAULT 'Y',
	`post_status` varchar(10),
	`vote_count` int(11) NOT NULL,
	`uploaded_count` smallint(2) NOT NULL,
	`ipaddress` varchar(128) NOT NULL,
	`list_order` bigint(20) NOT NULL,
	`update_order` bigint(20) NOT NULL,
	`tags` varchar(256),
	`regdate` datetime NOT NULL,
	`last_update` datetime NOT NULL,
	PRIMARY KEY (`post_id`),
	KEY `idx_board_id` (`board_id`),
	KEY `idx_parent_post_id` (`parent_post_id`),
	KEY `idx_category_id` (`category_id`),
	KEY `idx_is_notice` (`is_notice`),
	KEY `idx_post_author` (`post_author`),
	KEY `idx_readed_count` (`readed_count`),
	KEY `idx_post_status` (`post_status`),
	KEY `idx_vote_count` (`vote_count`),
	KEY `idx_list_order` (`list_order`),
	KEY `idx_update_order` (`update_order`),
	KEY `idx_regdate` (`regdate`),
	KEY `idx_last_update` (`last_update`)
	) {$charset_collate};");

	dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_category` (
	`category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`board_id` bigint(20) unsigned NOT NULL,
	`parent_id` bigint(20) NOT NULL DEFAULT 0,
	`category_name` varchar(250) DEFAULT NULL,
	`expand` char(1) DEFAULT 'N',
	`post_count` mediumint(9) unsigned NOT NULL DEFAULT 0,
	`list_order` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '표시 순서',
	`group_srls` text DEFAULT NULL COMMENT '수정 권한 회원 그룹 번호',
	`color` varchar(11) DEFAULT NULL,
	`is_default` char(1) DEFAULT NULL COMMENT '새글 작성 시 기본 선택',
	`deleted` char(1) NOT NULL DEFAULT 'N',
	`regdate` varchar(14) DEFAULT NULL,
	`last_update` varchar(14) DEFAULT NULL,
	PRIMARY KEY (`category_id`),
	KEY `board_id` (`board_id`),
	KEY `deleted_by_board` (`board_id`,`deleted`)
	) {$charset_collate};");
}

register_activation_hook( X2B__FILE__, 'X2board\Includes\Admin\activate' );

/* Plugins Loaded Hook */
function plugin_loaded() {
// error_log(print_r('x2b_plugin_loaded', true));
	add_option('x2b_version', X2B_VERSION, null, 'no');
}

add_action( 'plugins_loaded', 'X2board\Includes\Admin\plugin_loaded' );

/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 2.6.0
 *
 * @global $x2b_settings_page, $x2b_settings_tools
 * @return void
 */
function add_admin_pages_links() {

	// $crp_settings_page = add_options_page(
	// 	esc_html__( 'Contextual Related Posts', 'contextual-related-posts' ),
	// 	esc_html__( 'Related Posts', 'contextual-related-posts' ),
	// 	'manage_options',
	// 	'crp_options_page',
	// 	'crp_options_page'
	// );
	// add_action( "load-$crp_settings_page", 'crp_settings_help' ); // Load the settings contextual help.

	// $crp_settings_tools = add_management_page(
	// 	esc_html__( 'Contextual Related Posts Tools', 'contextual-related-posts' ),
	// 	esc_html__( 'Related Posts Tools', 'contextual-related-posts' ),
	// 	'manage_options',
	// 	'crp_tools_page',
	// 	'crp_tools_page'
	// );
	// add_action( "load-$crp_settings_tools", 'crp_settings_help' );
	global $_wp_last_object_menu;
	$_wp_last_object_menu++;
	// visible admin page
	add_menu_page(X2B_PAGE_TITLE, 'X2Board', 'manage_x2board', 'x2b_disp_idx', 'X2board\Includes\Admin\disp_admin_board', 'dashicons-admin-post', $_wp_last_object_menu);
	add_submenu_page('x2b_disp_idx', X2B_PAGE_TITLE, __('대시보드', 'x2board'), 'manage_x2board', 'x2b_disp_idx', 'X2board\Includes\Admin\disp_admin_board' );
	add_submenu_page('x2b_disp_idx', X2B_PAGE_TITLE, __('게시판 목록', 'x2board'), 'manage_x2board', 'x2b_disp_board_list', 'X2board\Includes\Admin\disp_admin_board' );
	add_submenu_page('x2b_disp_idx', X2B_PAGE_TITLE, __('게시판 생성', 'x2board'), 'manage_x2board', 'x2b_disp_board_insert', 'X2board\Includes\Admin\disp_admin_board' );
	// hidden admin page
	add_submenu_page(null, X2B_PAGE_TITLE, __('게시판 관리', 'x2board'), 'manage_x2board', 'x2b_disp_board_update', 'X2board\Includes\Admin\disp_admin_board' );
}
add_action( 'admin_menu', 'X2board\Includes\Admin\add_admin_pages_links', 99 );


/* Plugins Loaded Hook */
function admin_init() {
// error_log(print_r('x2b_admin_init', true));
	// 관리자에게 manage_x2board 권한 추가
	$admin_role = get_role('administrator');
	if(!$admin_role->has_cap('manage_x2board')){
		$admin_role->add_cap('manage_x2board', true);
	}
	add_action('admin_post_x2b_proc_insert_board', 'X2board\Includes\Admin\proc_admin_board' );
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
		wp_die(__('requested module does not have '.$calling_method.'()', 'x2board'));
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
	if(!method_exists( $o_module, $calling_method )) {
		wp_die(__('requested module does not have '.$calling_method.'()', 'x2board'));
	}
	$o_module->$calling_method();
	unset($o_module);
}


/**
 * Add rating links to the admin dashboard
 *
 * @since 2.6.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function footer( $footer_text ) {
	$current_screen = get_current_screen();
	
	if ( substr( $current_screen->id, 0, 12 ) === "x2board_page" ) {

		$text = sprintf(
			/* translators: 1: Contextual Related Posts website, 2: Plugin reviews link. */
			__( 'Thank you for using <a href="%1$s" target="_blank">X2 Board</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'x2board' ),
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
 * @since 2.9.0
 *
 * @param string $hook The current admin page.
 */
function load_scripts( $hook ) {

	wp_register_script(
		X2B_DOMAIN . '-admin-scripts',
		X2B_URL . 'includes/admin/js/admin-scripts.min.js',
		array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-datepicker' ),
		X2B_VERSION,
		true
	);
	wp_register_style(
		X2B_DOMAIN . '-admin-style',
		X2B_URL . 'includes/admin/css/admin.css',
		array(),
		X2B_VERSION
	);

	// if ( in_array( $hook, array( $crp_settings_page, $crp_settings_tools ), true ) ) {
	if ( substr( $hook, 0, 12 ) === "x2board_page" ) {
		wp_enqueue_script( X2B_DOMAIN . '-admin-scripts' );
		// wp_enqueue_script( 'crp-suggest-js' );
		// wp_enqueue_script( 'plugin-install' );
		wp_enqueue_style( X2B_DOMAIN . '-admin-style' );
		add_thickbox();

		// wp_enqueue_code_editor(
		// 	array(
		// 		'type'       => 'text/html',
		// 		'codemirror' => array(
		// 			'indentUnit' => 2,
		// 			'tabSize'    => 2,
		// 		),
		// 	)
		// );
		// wp_localize_script(
		// 	'crp-admin-js',
		// 	'crp_admin_data',
		// 	array(
		// 		'security' => wp_create_nonce( 'crp-admin' ),
		// 	)
		// );

	}
}
add_action( 'admin_enqueue_scripts', 'X2board\Includes\Admin\load_scripts' );


/**
 * This function enqueues scripts and styles in the Customizer.
 *
 * @since 2.9.0
 */
// function x2b_customize_controls_enqueue_scripts() {
// 	wp_enqueue_script( 'customize-controls' );
// 	wp_enqueue_script( 'x2b-suggest-js' );
// 	wp_enqueue_style( 'x2b-admin-customizer-css' );
// }
// add_action( 'customize_controls_enqueue_scripts', 'x2b_customize_controls_enqueue_scripts', 99 );


/**
 * This function enqueues scripts and styles on widgets.php.
 *
 * @since 2.9.0
 *
 * @param string $hook The current admin page.
 */
// function x2b_enqueue_scripts_widgets( $hook ) {
// 	if ( 'widgets.php' !== $hook ) {
// 		return;
// 	}
// 	wp_enqueue_script( 'x2b-suggest-js' );
// 	wp_enqueue_style( 'x2b-admin-customizer-css' );
// }
// add_action( 'admin_enqueue_scripts', 'x2b_enqueue_scripts_widgets', 99 );

/**
 * Adds minor CSS styles to the admin menu.
 *
 * @since 3.1.1
 */
// function x2b_admin_css() {

// 	if ( ! is_customize_preview() ) {
// 		$css = '
// 			<style type="text/css">
// 				a.crp_button {
// 					background: green;
// 					padding: 10px;
// 					color: white;
// 					text-decoration: none;
// 					text-shadow: none;
// 					border-radius: 3px;
// 					transition: all 0.3s ease 0s;
// 					border: 1px solid green;
// 				}
// 				a.crp_button:hover {
// 					box-shadow: 3px 3px 10px #666;
// 				}
// 			</style>';

// 		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// 	}
// }
// add_action( 'admin_head', 'x2b_admin_css' );
