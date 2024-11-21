<?php
/*
	Uninstalling x2board
*/

// if uninstall.php is not called by WordPress, die
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

// Load plugin file.
require_once 'x2board.php';

$a_options_remove = array(
	X2B_DOMAIN . '_timezone_gap',
	X2B_REWRITE_OPTION_TITLE
);

$s_select_query = "SELECT `board_id` FROM `{$wpdb->prefix}x2b_mapper`";
$a_board_id = $wpdb->get_results( $s_select_query );
foreach( $a_board_id as $_ => $o_board ) {
	$a_options_remove[] = X2B_DOMAIN . '_settings_board_' . $o_board->board_id;
	$a_options_remove[] = X2B_DOMAIN . '_settings_skin_vars_' . $o_board->board_id;

}
unset( $a_board_id );

// Remove wp_options
foreach ( $a_options_remove as $uninstall_option ) {
	delete_option( $uninstall_option );
}
unset( $a_options_remove );

// Remove tbl
$a_table_drop = array(
	'x2b_sequence',
	'x2b_mapper',
	'x2b_posts',
	'x2b_comments',
	'x2b_comments_list',
	'x2b_files',
	'x2b_categories',
	'x2b_user_define_keys',
	'x2b_user_define_vars',
);
foreach ( $a_table_drop as $s_tbl_name ) {
	$s_table_drop = "DROP TABLE IF EXISTS `{$wpdb->prefix}{$s_tbl_name}`;";
	$wpdb->query( $s_table_drop );
}
unset( $a_table_drop );

// Remove files
require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
$o_wp_filesystem = new \WP_Filesystem_Direct( false );
$s_file_storage_path = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_DOMAIN;
$o_wp_filesystem->rmdir( $s_file_storage_path, true);
unset( $o_wp_filesystem );

// Remove WP post and comment
$a_x2b_posts = get_posts( array( 'post_type' => X2B_DOMAIN, 'fields' => 'ids', 'numberposts' => -1 ) );
foreach ( $a_x2b_posts as $n_wp_post_id) {
	wp_delete_post( $n_wp_post_id, true );  // very slow command
}
unset( $a_x2b_posts );
wp_cache_flush();
