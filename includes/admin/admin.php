<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://webberzone.com
 * @since 2.6.0
 *
 * @package    Contextual Related Posts
 * @subpackage Admin
 */

 if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

/*  Plugins Activation Hook */
function x2b_activate() {
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
	`created` char(14) NOT NULL,
	PRIMARY KEY (`board_id`)
	) {$charset_collate};");
}

register_activation_hook( X2B__FILE__, 'x2b_activate' );

/* All Plugins Loaded Hook */
function x2b_loaded_action() {
// error_log(print_r('plugins_loaded_action', true));
	add_option('x2b_version', X2B_VERSION, null, 'no');
	// 관리자에게 manage_x2board 권한 추가
	$admin_role = get_role('administrator');
	if(!$admin_role->has_cap('manage_x2board')){
		$admin_role->add_cap('manage_x2board', true);
	}
}

add_action( 'plugins_loaded', 'x2b_loaded_action' );

/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 2.6.0
 *
 * @global $x2b_settings_page, $x2b_settings_tools
 * @return void
 */
function x2b_add_admin_pages_links() {

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
	add_menu_page(X2B_PAGE_TITLE, 'X2Board', 'manage_x2board', 'x2b_admin', 'disp_admin_idx', 'dashicons-admin-post', $_wp_last_object_menu);
	add_submenu_page('x2b_admin', X2B_PAGE_TITLE, __('대시보드', 'x2board'), 'manage_x2board', 'x2b_disp_admin_idx', 'disp_admin_idx' );
	add_submenu_page('x2b_admin', X2B_PAGE_TITLE, __('게시판 목록', 'x2board'), 'manage_x2board', 'x2b_disp_admin_boards', 'disp_admin_boards' );
	add_submenu_page('x2b_admin', X2B_PAGE_TITLE, __('게시판 생성', 'x2board'), 'manage_x2board', 'x2b_disp_admin_insert_board', array( 'X2board\Includes\Admin', 'disp_admin_insert_board' ) );
}
add_action( 'admin_menu', 'x2b_add_admin_pages_links', 99 );

/**
 * Admin Index Page.
 *
 * @return void
 */
function disp_admin_idx() {  
	require_once X2B_PATH . 'includes/admin/tpl/index.php';
}

/**
 * 게시판 목록 페이지
 */
function disp_admin_boards(){
	$o_board_admin_view = new \X2board\Includes\Modules\Board\boardAdminView();
	$o_board_admin_view->dispBoardAdminListBoard();
	unset($o_board_admin_view);
}

/**
 * Add rating links to the admin dashboard
 *
 * @since 2.6.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function x2b_admin_footer( $footer_text ) {
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
add_filter( 'admin_footer_text', 'x2b_admin_footer' );


/**
 * Enqueue Admin JS
 *
 * @since 2.9.0
 *
 * @param string $hook The current admin page.
 */
function x2b_load_admin_scripts( $hook ) {

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
add_action( 'admin_enqueue_scripts', 'x2b_load_admin_scripts' );


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
