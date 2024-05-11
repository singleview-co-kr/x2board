<?php
/**
 * Default settings.
 *
 * Functions to register the default settings of the plugin.
 *
 * @link https://singleview.co.kr 
 * @since 2.6.0
 *
 * @package x2board
 * @subpackage 
 */

namespace X2board\Includes\Admin\Tpl;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


/**
 * Retrieve the array of plugin settings
 *
 * @since 2.6.0
 *
 * @return array Settings array
 */
function x2b_get_registered_settings() {

	$a_x2b_settings = array(
		'general'   => x2b_settings_general(),
		'category'      => x2b_settings_category(),
		'user_define_field'    => x2b_settings_user_define_field(),
		'permission' => x2b_settings_permission(),
		'extra'    => x2b_settings_extra(),
		'skin_vars'      => x2b_settings_skin_vars(),
	);

	/**
	 * Filters the settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array   $a_x2b_settings Settings array
	 */
	return apply_filters( 'x2b_registered_settings', $a_x2b_settings );
}



/**
 * Retrieve the array of General settings
 *
 * @since 2.6.0
 *
 * @return array General settings array
 */
function x2b_settings_general() {
// error_log(print_r('x2b_settings_general', true));
	$settings = array(
		'x2board_title'					=> array(
			'id'      => 'board_title',
			'name'    => esc_html__( 'Board title', 'x2board' ),
			'desc'    => esc_html__( 'Enter the managerial title of the board. It is not visible to visitors.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'wp_page_title'					=> array(
			'id'      => 'wp_page_title',
			'name'    => esc_html__( 'WP page title', 'x2board' ),
			'desc'    => esc_html__( 'The title of the WP page is displayed as a browser title.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'board_skin'					=> array(
			'id'      => 'board_skin',
			'name'    => esc_html__( 'Board skin', 'x2board' ),
			'desc'    => esc_html__( 'Select the board skin', 'x2board' ),
			'type'    => 'select',
			'options' => x2b_get_board_skins(),
		),
		'board_list_count'		      => array(
			'id'      => 'board_list_count',
			'name'    => esc_html__( 'List per page', 'x2board' ),
			'desc'    => esc_html__( 'Specify the number of posts on a single page (default 20)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_search_list_count'		      => array(
			'id'      => 'board_search_list_count',
			'name'    => esc_html__( 'Search list per page', 'x2board' ),
			'desc'    => esc_html__( 'Specify the number of posts displayed when searching and categorizing. (default 20)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_page_count'		      => array(
			'id'      => 'board_page_count',
			'name'    => esc_html__( 'Displaying page count', 'x2board' ),
			'desc'    => esc_html__( 'Specify the number of page link at the bottom of the posts list. (default 10)', 'x2board' ),
			'type'    => 'number',
			'options' => '10',
		),
		'board_excerpted_title_length'		      => array(
			'id'      => 'board_excerpted_title_length',
			'name'    => esc_html__( 'Excerpted title length', 'x2board' ),
			'desc'    => esc_html__( 'Specify the number of extracted chars from the beginning of the post contents if empty title. (default 20)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_header_text'     => array(
			'id'      => 'board_header_text',
			'name'    => esc_html__( 'Board header html', 'x2board' ),
			'desc'    => esc_html__( 'Anything displayed at the top of the posts list (HTML tag available)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false, // esc_html__( 'No related posts found', 'x2board' ),
		),
		'board_footer_text'     => array(
			'id'      => 'board_footer_text',
			'name'    => esc_html__( 'Board footer html', 'x2board' ),
			'desc'    => esc_html__( 'Anything displayed at the bottom of the posts list (HTML tag available)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false,
		),
		'mobile_setup_header'					=> array(
			'id'      => 'mobile_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Mobile presentation setup', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'board_mobile_use_editor'  => array(
			'id'      => 'board_mobile_use_editor',
			'name'    => esc_html__( 'Mobile editor', 'x2board' ),
			'desc'    => esc_html__( 'Use WP editor for mobile', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'default' => 'N',
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'advanced_setup_header'					=> array(
			'id'      => 'advanced_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Configuration advanced setup', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'board_use_rewrite'  => array(
			'id'      => 'board_use_rewrite',
			'name'    => esc_html__( 'Activate pretty URL', 'x2board' ),
			'desc'    => esc_html__( 'Activate page_name/post_id URL to access the board. This doesn’t happen automatically after you save the configuration. You must flush permalinks. Go to WP Admin > Settings > Permalinks > Save.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_order_target'					=> array(
			'id'      => 'board_order_target',
			'name'    => esc_html__( 'Order field', 'x2board' ),
			'desc'    => esc_html__( 'Select a field to sort.', 'x2board' ),
			'type'    => 'select',
			'default' => 'list_order',
			'options' => array(
				'list_order'            => esc_html__( 'Latest Post', 'x2board' ),  // 문서번호
				'update_order'        => esc_html__( 'Latest updated', 'x2board' ),  // 최근 수정일
				'regdate_dt'            => esc_html__( 'Post date', 'x2board' ), // 등록일
				'voted_count'        => esc_html__( 'Recommended', 'x2board' ),  // 추천 수
				'blamed_count'            => esc_html__( 'Blamed', 'x2board' ),  // 비추천 수
				'readed_count'        => esc_html__( 'Readed', 'x2board' ),  // 조회 수
				'comment_count'            => esc_html__( 'Comment', 'x2board' ),  // 댓글 수
				'title'        => esc_html__( 'Title', 'x2board' ),  // 제목
				'nick_name'            => esc_html__( 'Nickname', 'x2board' ),  // 닉네임
				'user_name'        => esc_html__( 'User name', 'x2board' ),  // 이름
				'user_id'            => esc_html__( 'User ID', 'x2board' )  // 아이디
			),
		),
		'board_order_type'					=> array(
			'id'      => 'board_order_type',
			'name'    => esc_html__( 'Order type', 'x2board' ),
			'desc'    => esc_html__( 'Select a sort type.', 'x2board' ),
			'type'    => 'select',
			'default' => 'asc',
			'options' => array(
				'asc'            => esc_html__( 'Ascending', 'x2board' ),  // 오름차순
				'desc'        => esc_html__( 'Descending', 'x2board' ),  // 내림차순
			),
		),
		'board_except_notice'  => array(
			'id'      => 'board_except_notice',
			'name'    => esc_html__( 'Exclude notices', 'x2board' ),
			'desc'    => esc_html__( 'No duplicate notice post in a post list', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_use_anonymous'  => array(
			'id'      => 'board_use_anonymous',
			'name'    => esc_html__( 'Use Anonymous', 'x2board' ),
			'desc'    => esc_html__( 'Hide author personality of a board', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_consultation'  => array(
			'id'      => 'board_consultation',
			'name'    => esc_html__( 'Use consultation', 'x2board' ),
			'desc'    => esc_html__( '1 on 1 consultation for a registered member only', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_protect_content'  => array(
			'id'      => 'board_protect_content',
			'name'    => esc_html__( 'Protect post', 'x2board' ),
			'desc'    => esc_html__( 'A guest author cant update delete post if commented', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_use_status'                       => array(
			'id'      => 'board_use_status',
			'name'    => esc_html__( 'Post status', 'x2board' ),
			/* translators: 1: Code. */
			'desc'    => esc_html__( 'Available post status', 'x2board' ),
			'type'    => 'multicheck',
			'options' => array(
				'PUBLIC'            => esc_html__( 'PUBLIC', 'x2board' ),
				'SECRET'            => esc_html__( 'SECRET', 'x2board' ),
			),
			'mandatory' => array(
				'PUBLIC'            => 'mandatory',
			),
		),
		'board_admin_mail'					=> array(
			'id'      => 'board_admin_mail',
			'name'    => esc_html__( 'Admin mail address', 'x2board' ),
			'desc'    => esc_html__( 'Notify new post and comment via comma separted list ', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
	);

	/**
	 * Filters the General settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings General settings array
	 */
	return apply_filters( 'x2b_settings_general', $settings );
}


/**
 * Retrieve the array of category settings
 *
 * @since 2.6.0
 *
 * @return array category settings array
 */
function x2b_settings_category() {

	$settings = array(
		'board_use_category'  => array(
			'id'      => 'board_use_category',
			'name'    => esc_html__( 'Use category', 'x2board' ),
			'desc'    => esc_html__( 'Select to activate board category', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_hide_category'  => array(
			'id'      => 'board_hide_category',
			'name'    => esc_html__( 'Hide category', 'x2board' ),
			'desc'    => esc_html__( 'Select to hide board category', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_category_info'  => array(
			'id'      => 'board_category_info',
			'name'    => esc_html__( 'Category configuration', 'x2board' ),
			'desc'    => esc_html__( 'Define post category hierarchy', 'x2board' ),
			'type'    => 'wpsortableui',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
	);

	/**
	 * Filters the List settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings List settings array
	 */
	return apply_filters( 'x2b_settings_category', $settings );
}


/**
 * Retrieve the array of user define field settings
 *
 * @since 2.6.0
 *
 * @return array user define field settings array
 */
function x2b_settings_user_define_field() {

	$settings = array(
		'board_user_define_field'  => array(
			'id'      => 'board_user_define_field',
			'name'    => esc_html__( 'User define field', 'x2board' ),
			'desc'    => esc_html__( 'Select default and extended fields', 'x2board' ),
			'type'    => 'wpuserfieldui',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
	);

	/**
	 * Filters the Output settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Output settings array
	 */
	return apply_filters( 'x2b_settings_user_define_field', $settings );
}


/**
 * Retrieve the array of permission settings
 *
 * @since 2.6.0
 *
 * @return array Thumbnail settings array
 */
function x2b_settings_permission() {

	$settings = array(
		'board_admin_emails'      => array(
			'id'      => 'board_admin_emails',
			'name'    => esc_html__( 'Board admin email', 'x2board' ),
			'desc'    => esc_html__( 'Comma separated admin email addresses', 'x2board' ),
			'type'    => 'text',
			'default' => '',
			// 'options' => CRP_PLUGIN_URL . 'default.png',
			'size'    => 'large',
		),
		'board_access'               => array(
			'id'      => 'board_grant_access',
			'name'    => esc_html__( 'Access permission', 'x2board' ),
			// 'desc'    => esc_html__( '가입한 사용자는 분양형 가상 사이트에 가입한 로그인 사용자를 의미합니다.', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_list'               => array(
			'id'      => 'board_grant_list',
			'name'    => esc_html__( 'list permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_view'               => array(
			'id'      => 'board_grant_view',
			'name'    => esc_html__( 'view permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_write_post'               => array(
			'id'      => 'board_grant_write_post',
			'name'    => esc_html__( 'write post permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_write_comment'               => array(
			'id'      => 'board_grant_write_comment',
			'name'    => esc_html__( 'write comment permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_consultation_read'               => array(
			'id'      => 'board_grant_consultation_read',
			'name'    => esc_html__( 'Consultation read permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ADMINISTRATOR,  // means Managers
			'options' => x2b_get_grants(),
		),
		'board_manager'               => array(
			'id'      => 'board_grant_manager',
			'name'    => esc_html__( 'Manager permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'grantselect',
			'default' => X2B_ADMINISTRATOR,  // means Managers
			'options' => x2b_get_grants(),
		),
	);

	/**
	 * Filters the Thumbnail settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Thumbnail settings array
	 */
	return apply_filters( 'x2b_settings_permission', $settings );
}


/**
 * Retrieve the array of extra settings
 *
 * @since 2.6.0
 *
 * @return array extra settings array
 */
function x2b_settings_extra() {

	$settings = array(
		'comment_setup_header'					=> array(
			'id'      => 'comment_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Configure comment', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'comment_count'		      => array(
			'id'      => 'comment_count',
			'name'    => esc_html__( 'Number of comments', 'x2board' ),
			'desc'    => esc_html__( 'Set number of comments on a single list.', 'x2board' ),
			'default' => '20',
			'type'    => 'number',
			'options' => '20',
		),
		'comment_use_vote_up'					=> array(
			'id'      => 'comment_use_vote_up',
			'name'    => esc_html__( 'Comment vote up', 'x2board' ),
			'desc'    => esc_html__( 'Activate vote up for a comment.', 'x2board' ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'Y'            => esc_html__( 'Activate', 'x2board' ),  // 사용
				'S'        => esc_html__( 'Activate + display', 'x2board' ),  // 사용+노출
				'N'            => esc_html__( 'Dectivate', 'x2board' ),  // 미사용
			),
		),
		'comment_use_vote_down'					=> array(
			'id'      => 'comment_use_vote_down',
			'name'    => esc_html__( 'Comment vote down', 'x2board' ),
			'desc'    => esc_html__( 'Activate vote down for a comment.', 'x2board' ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'Y'            => esc_html__( 'Activate', 'x2board' ),  // 사용
				'S'        => esc_html__( 'Activate + display', 'x2board' ),  // 사용+노출
				'N'            => esc_html__( 'Dectivate', 'x2board' ),  // 미사용
			),
		),
		'comment_use_validation'					=> array(
			'id'      => 'comment_use_validation',
			'name'    => esc_html__( 'Use comment approval', 'x2board' ),
			'desc'    => esc_html__( 'Hide unapproved comment.', 'x2board' ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'N'            => esc_html__( 'Dectivate', 'x2board' ),  // 미사용
				'Y'            => esc_html__( 'Activate', 'x2board' ),  // 사용
			),
		),
		'post_editor_setup_header'					=> array(
			'id'      => 'post_editor_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Post wysiwyg editor', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'post_editor_skin'					=> array(
			'id'      => 'post_editor_skin',
			'name'    => esc_html__( 'Editor skin', 'x2board' ),
			'desc'    => esc_html__( 'Select the edtor skin', 'x2board' ),
			'type'    => 'select',
			'options' => x2b_get_editors(),
		),
		'post_editor_height'		    => array(
			'id'      => 'post_editor_height',
			'name'    => esc_html__( 'Editor height', 'x2board' ),
			'desc'    => esc_html__( 'Specify the editor height (default 500)', 'x2board' ),
			'type'    => 'number',
			'options' => '500',
		),
		'enable_html_grant'               => array(
			'id'      => 'enable_html_grant',
			'name'    => esc_html__( 'Allow post HTML edit', 'x2board' ),
			'desc'    => esc_html__( 'Allow post HTML to whom', 'x2board' ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'upload_file_grant'               => array(
			'id'      => 'upload_file_grant',
			'name'    => esc_html__( 'Allow post upload file', 'x2board' ),
			'desc'    => esc_html__( 'Allow post upload file to whom', 'x2board' ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'comment_editor_setup_header'					=> array(
			'id'      => 'comment_editor_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Comment wysiwyg editor', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'comment_editor_skin'					=> array(
			'id'      => 'comment_editor_skin',
			'name'    => esc_html__( 'Comment editor skin', 'x2board' ),
			'desc'    => esc_html__( 'Select the edtor skin', 'x2board' ),
			'type'    => 'select',
			'options' => x2b_get_editors(),
		),
		'comment_editor_height'		    => array(
			'id'      => 'comment_editor_height',
			'name'    => esc_html__( 'Comment editor height', 'x2board' ),
			'desc'    => esc_html__( 'Specify the editor height (default 500)', 'x2board' ),
			'type'    => 'number',
			'options' => '500',
		),
		'enable_comment_html_grant'               => array(
			'id'      => 'enable_comment_html_grant',
			'name'    => esc_html__( 'Allow comment HTML edit', 'x2board' ),
			'desc'    => esc_html__( 'Allow comment HTML to whom, No choice no restriction', 'x2board' ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'comment_upload_file_grant'               => array(
			'id'      => 'comment_upload_file_grant',
			'name'    => esc_html__( 'Allow comment upload file', 'x2board' ),
			'desc'    => esc_html__( 'Allow comment upload file to whom, No choice no restriction', 'x2board' ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'common_editor_setup_header'					=> array(
			'id'      => 'common_editor_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Common wysiwyg editor', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'content_style'					=> array(
			'id'      => 'content_style',
			'name'    => esc_html__( 'content style', 'x2board' ),
			'desc'    => esc_html__( 'Select the content style', 'x2board' ),
			'type'    => 'select',
			'options' => x2b_get_content_styles(),
		),
		'content_font'					=> array(
			'id'      => 'content_font',
			'name'    => esc_html__( 'Content font', 'x2board' ),
			'desc'    => esc_html__( 'Comma separated value. Ex) Tahoma, Geneva, sans-serif', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'content_font_size'		    => array(
			'id'      => 'content_font_size',
			'name'    => esc_html__( 'Content font size', 'x2board' ),
			'desc'    => esc_html__( 'Please include the units. Ex) 12px, 1em', 'x2board' ),
			'type'    => 'number',
			'options' => false,
		),
		'enable_autosave'  => array(
			'id'      => 'enable_autosave',
			'name'    => esc_html__( 'Enable autosave', 'x2board' ),
			'desc'    => esc_html__( 'Enable autosave', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
			'default' => array(	'Y' => 'Y',),
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'enable_default_component_grant'               => array(
			'id'      => 'enable_default_component_grant',
			'name'    => esc_html__( 'Allow default components', 'x2board' ),
			'desc'    => esc_html__( 'Allow default components to whom, No choice no restriction', 'x2board' ),
			'type'    => 'multicheck',
			'default' => '',
			'options' => x2b_get_editable_roles(),
		),
		'enable_component_grant'               => array(
			'id'      => 'enable_component_grant',
			'name'    => esc_html__( 'Allow components', 'x2board' ),
			'desc'    => esc_html__( 'Allow components to whom, No choice no restriction', 'x2board' ),
			'type'    => 'multicheck',
			'default' => '',
			'options' => x2b_get_editable_roles(),
		),


		'file_attachment_setup_header'	=> array(
			'id'      => 'file_attachment_setup_header',
			// 'name'    => esc_html__( 'Advanced setup', 'x2board' ),
			'desc'    => esc_html__( 'Configure file attachment', 'x2board' ),
			'type'    => 'header',
			'options' => false,
		),
		'file_allowed_filesize_mb'	=> array(
			'id'      => 'file_allowed_filesize_mb',
			'name'    => esc_html__( 'Max Mb size of each file', 'x2board' ),
			'desc'    => esc_html__( 'Specify maximum mega-byte size of an each file. Admin is exceptional', 'x2board' ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_allowed_attach_size_mb'	=> array(
			'id'      => 'file_allowed_attach_size_mb',
			'name'    => esc_html__( 'Max Mb size of all files', 'x2board' ),
			'desc'    => esc_html__( 'Specify maximum mega-byte size of all files a single post. Admin is exceptional', 'x2board' ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_max_attached_count'	=> array(
			'id'      => 'file_max_attached_count',
			'name'    => esc_html__( 'Max number of all files', 'x2board' ),
			'desc'    => esc_html__( 'Specify maximum number of appending files.', 'x2board' ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_allowed_filetypes'     => array(
			'id'      => 'file_allowed_filetypes',
			'name'    => esc_html__( 'Allowed file extensions', 'x2board' ),
			'desc'    => esc_html__( 'Specify appendable file extensions. comma separated', 'x2board' ),
			'type'    => 'textarea',
			'options' => 'jpg, jpeg, gif, png, bmp, pjp, pjpeg, jfif, svg, webp, ico, zip, 7z, hwp, ppt, xls, doc, txt, pdf, xlsx, pptx, docx, torrent, smi, mp4, mp3',
		),
		'file_allow_outlink'               => array(
			'id'      => 'file_allow_outlink',
			'name'    => esc_html__( 'Allow external download', 'x2board' ),
			'desc'    => esc_html__( 'Allow download of appended file from extenal site', 'x2board' ),
			'type'    => 'radio',
			'default' => 'Y',
			'options' => array(
				'Y'      => esc_html__( 'Allow', 'x2board' ),
				'N'       => esc_html__( 'Disallow', 'x2board' ),
			),
		),
		'file_allow_outlink_format'     => array(
			'id'      => 'file_allow_outlink_format',
			'name'    => esc_html__( 'External download allowed file extensions', 'x2board' ),
			'desc'    => esc_html__( 'Specify outlink-allowable file extensions. comma separated', 'x2board' ),
			'type'    => 'textarea',
			'options' => false,
		),
		'file_allow_outlink_site'     => array(
			'id'      => 'file_allow_outlink_site',
			'name'    => esc_html__( 'Download allowed external site', 'x2board' ),
			'desc'    => esc_html__( 'Specify trusted external sites regardless of [Allow external download] configuration. Separate lines to enter multiple domains. ex)http://www.domain.com', 'x2board' ),
			'type'    => 'textarea',
			'options' => false,
		),
		'file_download_grant'               => array(
			'id'      => 'file_download_grant',
			'name'    => esc_html__( 'Download allowed group', 'x2board' ),
			'desc'    => sprintf( esc_html__( 'Allow download for selected group only, No choice no restriction', 'x2board' ) ),
			'type'    => 'multicheck',
			'default' => false, // array(	'single' => 'single',),
			'options' => x2b_get_editable_roles(),
		),
	);

	/**
	 * Filters the Styles settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Styles settings array
	 */
	return apply_filters( 'x2b_settings_extra', $settings );
}


/**
 * Retrieve the array of skin vars settings
 *
 * @since 2.6.0
 *
 * @return array Feed skin vars array
 */
function x2b_settings_skin_vars() {

	$settings = array(
	);

	/**
	 * Filters the Feed settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Feed settings array
	 */
	return apply_filters( 'x2b_settings_skin_vars', $settings );
}


/**
 * Upgrade pre v2.5.0 settings.
 *
 * @since 2.6.0
 * @return array Settings array
 */
function x2b_upgrade_settings() {
	return array();
}


/**
 * Get the various skins.
 *
 * @since 2.6.0
 * @return array skins options.
 */
function x2b_get_board_skins() {

	$s_skin_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'board'.DIRECTORY_SEPARATOR.'skins';
	$a_skins = \X2board\Includes\Classes\FileHandler::readDir($s_skin_path_abs);

	$a_skin_info = array();
	foreach($a_skins as $_ => $s_skin_name) {
		$a_skin_info[$s_skin_name] = esc_html__( $s_skin_name, 'x2board' );
	}
	unset($a_skins);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @since 2.6.0
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_board_skins', $a_skin_info );
}


/**
 * Get the various skins.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_editors() {

	$s_skin_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'editor'.DIRECTORY_SEPARATOR.'skins';
	$a_skins = \X2board\Includes\Classes\FileHandler::readDir($s_skin_path_abs);

	$a_skin_info = array();
	foreach($a_skins as $_ => $s_skin_name) {
		$a_skin_info[$s_skin_name] = esc_html__( $s_skin_name, 'x2board' );
	}
	unset($a_skins);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @since 2.6.0
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_editors', $a_skin_info );
}


/**
 * Get the various content styles.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_content_styles() {

	$s_style_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'editor'.DIRECTORY_SEPARATOR.'styles';
	$a_styles = \X2board\Includes\Classes\FileHandler::readDir($s_style_path_abs);

	$a_style_info = array();
	foreach($a_styles as $_ => $s_style_name) {
		$a_style_info[$s_style_name] = esc_html__( $s_style_name, 'x2board' );
	}
	unset($a_styles);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @since 2.6.0
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_content_styles', $a_style_info );
}


/**
 * Get x2b grants.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_grants() {

	$a_roles = array();
	$a_roles[X2B_ALL_USERS] = esc_html__( 'All users', 'x2board' );
	$a_roles[X2B_LOGGEDIN_USERS] = esc_html__( 'Loggedin users', 'x2board' );
	// $a_roles[X2B_REGISTERED_USERS] = esc_html__( 'Registered users', 'x2board' );
	$a_roles[X2B_ADMINISTRATOR] = esc_html__( 'Administrator', 'x2board' );
	$a_roles[X2B_CUSTOMIZE] = esc_html__( 'Customize', 'x2board' );
	
	/**
	 * Filter the array to allow privilege
	 *
	 * @since 2.6.0
	 *
	 * @param array $roles Different roles.
	 */
	return apply_filters( 'x2b_get_grants', $a_roles );
}

/**
 * Get the various skins.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_editable_roles() {

	if (!function_exists('get_editable_roles')) {
		require_once(ABSPATH . '/wp-admin/includes/user.php');
	}

	$a_roles = array();
	// $a_roles['all'] = esc_html__( 'All users', 'x2board' );
	// $a_roles['loggedin_user'] = esc_html__( 'Loggedin users', 'x2board' ); // maybe subscribers of WP
	foreach(get_editable_roles() as $roles_key=>$roles_value) {
		$a_roles[$roles_key] = esc_html__( $roles_value['name'], 'x2board' );
	}	

	/**
	 * Filter the array to allow privilege
	 *
	 * @since 2.6.0
	 *
	 * @param array $roles Different roles.
	 */
	return apply_filters( 'x2b_get_editable_roles', $a_roles );
}