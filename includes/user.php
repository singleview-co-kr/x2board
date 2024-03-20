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

global $G_X2B_CACHE;
/**
 * Content function with filter.
 *
 * @since 1.9
 */
function x2b_prepare_content_filter() {
	add_filter( 'the_content', 'X2board\Includes\x2b_content_filter' );
}
add_action( 'template_redirect', 'X2board\Includes\x2b_prepare_content_filter' );

/**
 * Filter for 'the_content' to display the requeste x2board.
 *
 * @since 1.0.1
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function x2b_content_filter( $content ) {

	global $post, $wpdb; // , $wp_filters;
	// global $g_a_x2b_query_param;  

	// Track the number of times this function  is called.
	static $filter_calls = 0;
	++$filter_calls;

	if(isset($post->post_content) && is_page($post->ID) ){
		if( $post->post_content === X2B_PAGE_IDENTIFIER ) {
			global $G_X2B_CACHE;
			$G_X2B_CACHE = array();

			if ( !defined( '__DEBUG__' ) ) {
				define('__DEBUG__', 0);
			}

			require_once X2B_PATH . 'includes/func.inc.php';
			// load common classes
			require_once X2B_PATH . 'includes/classes/Context.class.php';
			require_once X2B_PATH . 'includes/classes/BaseObject.class.php';
			require_once X2B_PATH . 'includes/classes/ModuleObject.class.php';
			require_once X2B_PATH . 'includes/classes/ModuleHandler.class.php';
			require_once X2B_PATH . 'includes/classes/DB.class.php';
			require_once X2B_PATH . 'includes/classes/Skin.class.php';
			require_once X2B_PATH . 'includes/classes/PageHandler.class.php';
			
			// load modules
			require_once X2B_PATH . 'includes/modules/board/board.class.php';
			require_once X2B_PATH . 'includes/modules/board/board.view.php';
			require_once X2B_PATH . 'includes/modules/post/post.class.php';
			require_once X2B_PATH . 'includes/modules/post/post.model.php';

			$o_context = \X2board\Includes\Classes\Context::getInstance();
			$o_context->init();
// var_dump($o_context->getAll4Skin());
			$o_context->render_view('board');
			$o_context->close();
			unset($o_context);
			//return $content . kboard_builder(array('id'=>$board_id));
		} 
	}
	return $content;

	// // Return if it's not in the loop or in the main query.
	// if ( ! ( in_the_loop() && is_main_query() && (int) get_queried_object_id() === (int) $post->ID ) ) {
	// 	return $content;
	// }

	// // Check if this is the last call of the_content.
	// if ( doing_filter( 'the_content' ) && isset( $wp_filters['the_content'] ) && (int) $wp_filters['the_content'] !== $filter_calls ) {
	// 	return $content;
	// }

	// // Return if this is a mobile device and disable on mobile option is enabled.
	// if ( wp_is_mobile() && x2b_get_option( 'disable_on_mobile' ) ) {
	// 	return $content;
	// }

	// // Return if this is an amp page and disable on amp option is enabled.
	// if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() && x2b_get_option( 'disable_on_amp' ) ) {
	// 	return $content;
	// }

	// // Check exclusions.
	// if ( x2b_exclude_on( $post, $x2b_settings ) ) {
	// 	return $content;    // Exit without adding related posts.
	// }

	// $add_to = x2b_get_option( 'add_to', false );

	// // Else add the content.
	// if ( ( ( is_single() ) && ! empty( $add_to['single'] ) ) ||
	// ( ( is_page() ) && ! empty( $add_to['page'] ) ) ||
	// ( ( is_home() ) && ! empty( $add_to['home'] ) ) ||
	// ( ( is_category() ) && ! empty( $add_to['category_archives'] ) ) ||
	// ( ( is_tag() ) && ! empty( $add_to['tag_archives'] ) ) ||
	// ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ! empty( $add_to['other_archives'] ) ) ) {

	// 	$x2b_code = get_crp( 'is_widget=0' );

	// 	return x2b_generate_content( $content, $x2b_code );

	// } else {
	// 	return $content;
	// }
}