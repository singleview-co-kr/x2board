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
			'options' => array(
				'default'            => esc_html__( 'default', 'x2board' ),
				'sketchbook5'        => esc_html__( 'sketchbook5', 'x2board' ),
			),
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
 * Retrieve the array of user define field settings
 *
 * @since 2.6.0
 *
 * @return array user define field settings array
 */
function x2b_settings_user_define_field() {

	$settings = array(
		
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
			'desc'    => esc_html__( 'Category configuration', 'x2board' ),
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
				// 'options' => CRP_PLUGIN_URL . 'default.png',
				'size'    => 'large',
			),
		'board_access_default'					=> array(
			'id'      => 'board_access_default',
			'name'    => esc_html__( 'Access permission', 'x2board' ),
			'desc'    => esc_html__( '가입한 사용자는 분양형 가상 사이트에 가입한 로그인 사용자를 의미합니다.', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_list_default'					=> array(
			'id'      => 'board_list_default',
			'name'    => esc_html__( 'list permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_view_default'					=> array(
			'id'      => 'board_view_default',
			'name'    => esc_html__( 'view permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_write_post_default'					=> array(
			'id'      => 'board_write_post_default',
			'name'    => esc_html__( 'write post permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_write_comment_default'					=> array(
			'id'      => 'board_write_comment_default',
			'name'    => esc_html__( 'write comment permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_consultation_read_default'					=> array(
			'id'      => 'board_consultation_read_default',
			'name'    => esc_html__( 'Consultation read permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'-1'        => esc_html__( 'Loggedin users', 'x2board' ),
				// '-2'        => esc_html__( 'Registerred users', 'x2board' ),
				'-3'        => esc_html__( 'Managers', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
		),
		'board_manager_default'					=> array(
			'id'      => 'board_manager_default',
			'name'    => esc_html__( 'Manager permission', 'x2board' ),
			// 'desc'    => esc_html__( '', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'0'            => esc_html__( 'All users', 'x2board' ),
				'selected'        => esc_html__( 'Selected groups', 'x2board' ),
			),
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
			'options' => array(
				'admin'            => esc_html__( 'Admin', 'x2board' ),
				'manager'              => esc_html__( 'Manager', 'x2board' ),
				'other'    => esc_html__( 'Other', 'x2board' ),
			),
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
 * Get the various styles.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_styles() {

	$styles = array(
		array(
			'id'          => 'no_style',
			'name'        => esc_html__( 'No styles', 'x2board' ),
			'description' => esc_html__( 'Select this option if you plan to add your own styles', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'text_only',
			'name'        => esc_html__( 'Text only', 'x2board' ),
			'description' => esc_html__( 'Disable thumbnails and no longer include the default style sheet', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'rounded_thumbs',
			'name'        => esc_html__( 'Rounded thumbnails', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/rounded-thumbs.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Enabling this option will turn on the thumbnails. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'masonry',
			'name'        => esc_html__( 'Masonry', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/masonry.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Enables a masonry style layout similar to one made famous by Pinterest.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'grid',
			'name'        => esc_html__( 'Grid', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/grid.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Uses CSS Grid for display. Might not work on older browsers.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'thumbs_grid',
			'name'        => esc_html__( 'Rounded thumbnails with CSS grid', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/thumbs-grid.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Uses CSS grid. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'x2board' ) . '<br />',
		),
	);

	/**
	 * Filter the array containing the styles to add your own.
	 *
	 * @since 2.6.0
	 *
	 * @param array $styles Different styles.
	 */
	return apply_filters( 'x2b_get_styles', $styles );
}

/**
 * Get the various order settings.
 *
 * @since 2.8.0
 * @return array Order settings.
 */
function x2b_get_orderings() {

	$orderings = array(
		'relevance' => esc_html__( 'By relevance', 'x2board' ),
		'random'    => esc_html__( 'Randomly', 'x2board' ),
		'date'      => esc_html__( 'By date', 'x2board' ),
	);

	/**
	 * Filter the array containing the order settings.
	 *
	 * @since 2.8.0
	 *
	 * @param array $orderings Order settings.
	 */
	return apply_filters( 'x2b_get_orderings', $orderings );
}
