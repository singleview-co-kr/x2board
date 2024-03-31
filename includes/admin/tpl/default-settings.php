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
			'desc'    => esc_html__( '관리용 게시판 제목을 입력하세요. 방문자에게는 표시되지 않아요.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'wp_page_title'					=> array(
			'id'      => 'wp_page_title',
			'name'    => esc_html__( 'WP page title', 'x2board' ),
			'desc'    => esc_html__( 'WP 페이지 제목은 브라우저 제목으로 표시되요.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'board_skin'					=> array(
			'id'      => 'board_skin',
			'name'    => esc_html__( 'Board skin', 'x2board' ),
			'desc'    => esc_html__( '게시판 스킨을 선택하세요.', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'default'            => esc_html__( 'default', 'x2board' ),
				'sketchbook5'        => esc_html__( 'sketchbook5', 'x2board' ),
			),
		),
		'board_list_per_page'		      => array(
			'id'      => 'board_list_per_page',
			'name'    => esc_html__( 'List per page', 'x2board' ),
			'desc'    => esc_html__( '한 페이지에 표시될 글 수를 지정할 수 있습니다. (기본 20개)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_search_list_count'		      => array(
			'id'      => 'board_search_list_count',
			'name'    => esc_html__( 'Search list per page', 'x2board' ),
			'desc'    => esc_html__( '검색, 카테고리 선택 등을 할 경우 표시될 글 수를 지정할 수 있습니다. (기본 20개)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_page_count'		      => array(
			'id'      => 'board_page_count',
			'name'    => esc_html__( 'Displaying page count', 'x2board' ),
			'desc'    => esc_html__( '목록 하단, 페이지를 이동하는 링크 수를 지정할 수 있습니다. (기본 10개)', 'x2board' ),
			'type'    => 'number',
			'options' => '10',
		),
		'board_excerpted_title_length'		      => array(
			'id'      => 'board_excerpted_title_length',
			'name'    => esc_html__( 'Excerpted title length', 'x2board' ),
			'desc'    => esc_html__( '빈 제목 입력되면 본문 첫줄에서 추출하는 문자열 수를 지정합니다. (기본 20자)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_header_text'     => array(
			'id'      => 'board_header_text',
			'name'    => esc_html__( 'Board header html', 'x2board' ),
			'desc'    => esc_html__( '콘텐츠 상단에 표시되는 내용입니다. (HTML 태그 사용 가능)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false, // esc_html__( 'No related posts found', 'x2board' ),
		),
		'board_footer_text'     => array(
			'id'      => 'board_footer_text',
			'name'    => esc_html__( 'Board footer html', 'x2board' ),
			'desc'    => esc_html__( '콘텐츠 하단에 표시되는 내용입니다. (HTML 태그 사용 가능)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false,
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
				'regdate'            => esc_html__( 'Post date', 'x2board' ), // 등록일
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
		// 'limit'                  => array(
		// 	'id'      => 'limit',
		// 	'name'    => esc_html__( 'Number of posts to display', 'x2board' ),
		// 	'desc'    => esc_html__( 'Maximum number of posts that will be displayed in the list. This option is used if you do not specify the number of posts in the widget or shortcodes', 'x2board' ),
		// 	'type'    => 'number',
		// 	'options' => '6',
		// 	'min'     => '0',
		// 	'size'    => 'small',
		// ),
		// 'ordering'               => array(
		// 	'id'      => 'ordering',
		// 	'name'    => esc_html__( 'Order posts', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'radio',
		// 	'default' => 'relevance',
		// 	'options' => crp_get_orderings(),
		// ),
	
		// 'post_types'             => array(
		// 	'id'      => 'post_types',
		// 	'name'    => esc_html__( 'Post types to include', 'x2board' ),
		// 	'desc'    => esc_html__( 'At least one option should be selected above. Select which post types you want to include in the list of posts. This field can be overridden using a comma separated list of post types when using the manual display.', 'x2board' ),
		// 	'type'    => 'posttypes',
		// 	'options' => 'post,page',
		// ),
		// 'same_taxes'             => array(
		// 	'id'      => 'same_taxes',
		// 	'name'    => esc_html__( 'Only from same', 'x2board' ),
		// 	'desc'    => esc_html__( 'Limit the related posts only to the categories, tags, and/or taxonomies of the current post.', 'x2board' ),
		// 	'type'    => 'taxonomies',
		// 	'options' => '',
		// ),
		// 'related_meta_keys'      => array(
		// 	'id'      => 'related_meta_keys',
		// 	'name'    => esc_html__( 'Related Meta Keys', 'x2board' ),
		// 	'desc'    => esc_html__( 'Enter a comma-separated list of meta keys. Posts that match the same value of the meta key are displayed before the other related posts', 'x2board' ),
		// 	'type'    => 'csv',
		// 	'options' => '',
		// 	'size'    => 'large',
		// ),
		// 'exclude_post_ids'       => array(
		// 	'id'      => 'exclude_post_ids',
		// 	'name'    => esc_html__( 'Post/page IDs to exclude', 'x2board' ),
		// 	'desc'    => esc_html__( 'Comma-separated list of post or page IDs to exclude from the list. e.g. 188,320,500', 'x2board' ),
		// 	'type'    => 'numbercsv',
		// 	'options' => '',
		// ),
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
		// 'post_thumb_op'      => array(
		// 	'id'      => 'post_thumb_op',
		// 	'name'    => esc_html__( 'Location of the post thumbnail', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'radio',
		// 	'default' => 'text_only',
		// 	'options' => array(
		// 		'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'x2board' ),
		// 		'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'x2board' ),
		// 		'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'x2board' ),
		// 		'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'x2board' ),
		// 	),
		// ),
		// 'thumb_size'         => array(
		// 	'id'      => 'thumb_size',
		// 	'name'    => esc_html__( 'Thumbnail size', 'x2board' ),
		// 	'desc'    => esc_html__( 'You can choose from existing image sizes above or create a custom size. If you have chosen Custom size above, then enter the width, height and crop settings below. For best results, use a cropped image. If you change the width and/or height below, existing images will not be automatically resized.' ) . '<br />' . sprintf(
		// 		/* translators: 1: OTF Regenerate plugin link, 2: Force regenerate plugin link. */
		// 		esc_html__( 'I recommend using %1$s or %2$s to regenerate all image sizes.', 'x2board' ),
		// 		'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=otf-regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">OTF Regenerate Thumbnails</a>',
		// 		'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">Regenerate Thumbnails</a>'
		// 	),
		// 	'type'    => 'thumbsizes',
		// 	'default' => 'crp_thumbnail',
		// 	'options' => crp_get_all_image_sizes(),
		// ),
		// 'thumb_height'       => array(
		// 	'id'      => 'thumb_height',
		// 	'name'    => esc_html__( 'Thumbnail height', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'number',
		// 	'options' => '150',
		// 	'min'     => '0',
		// 	'size'    => 'small',
		// ),
		// 'thumb_create_sizes' => array(
		// 	'id'      => 'thumb_create_sizes',
		// 	'name'    => esc_html__( 'Generate thumbnail sizes', 'x2board' ),
		// 	'desc'    => esc_html__( 'If you select this option and Custom size is selected above, the plugin will register the image size with WordPress to create new thumbnails. Does not update old images as explained above.', 'x2board' ),
		// 	'type'    => 'checkbox',
		// 	'options' => true,
		// ),
		// 'thumb_html'         => array(
		// 	'id'      => 'thumb_html',
		// 	'name'    => esc_html__( 'Thumbnail size attributes', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'radio',
		// 	'default' => 'html',
		// 	'options' => array(
		// 		/* translators: %s: Code. */
		// 		'css'  => sprintf( esc_html__( 'Use CSS to set the width and height: e.g. %s', 'x2board' ), '<code>style="max-width:250px;max-height:250px"</code>' ),
		// 		/* translators: %s: Code. */
		// 		'html' => sprintf( esc_html__( 'Use HTML attributes to set the width and height: e.g. %s', 'x2board' ), '<code>width="250" height="250"</code>' ),
		// 		'none' => esc_html__( 'No width or height set. You will need to use external styles to force any width or height of your choice.', 'x2board' ),
		// 	),
		// ),
		// 'scan_images'        => array(
		// 	'id'      => 'scan_images',
		// 	'name'    => esc_html__( 'Get first image', 'x2board' ),
		// 	'desc'    => esc_html__( 'The plugin will fetch the first image in the post content if this is enabled. This can slow down the loading of your page if the first image in the followed posts is large in file-size.', 'x2board' ),
		// 	'type'    => 'checkbox',
		// 	'options' => true,
		// ),
		// 'thumb_default'      => array(
		// 	'id'      => 'thumb_default',
		// 	'name'    => esc_html__( 'Default thumbnail', 'x2board' ),
		// 	'desc'    => esc_html__( 'Enter the full URL of the image that you wish to display if no thumbnail is found. This image will be displayed below.', 'x2board' ),
		// 	'type'    => 'text',
		// 	'options' => CRP_PLUGIN_URL . 'default.png',
		// 	'size'    => 'large',
		// ),
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
		// 'crp_styles' => array(
		// 	'id'      => 'crp_styles',
		// 	'name'    => esc_html__( 'Related Posts style', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'radiodesc',
		// 	'default' => 'rounded_thumbs',
		// 	'options' => crp_get_styles(),
		// ),
		// 'custom_css' => array(
		// 	'id'          => 'custom_css',
		// 	'name'        => esc_html__( 'Custom CSS', 'x2board' ),
		// 	/* translators: 1: Opening a tag, 2: Closing a tag, 3: Opening code tage, 4. Closing code tag. */
		// 	'desc'        => sprintf( esc_html__( 'Do not include %3$sstyle%4$s tags. Check out the %1$sFAQ%2$s for available CSS classes to style.', 'x2board' ), '<a href="' . esc_url( 'http://wordpress.org/plugins/x2board/faq/' ) . '" target="_blank">', '</a>', '<code>', '</code>' ),
		// 	'type'        => 'css',
		// 	'options'     => '',
		// 	'field_class' => 'codemirror_css',
		// ),
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
		// 'feed_options_desc'  => array(
		// 	'id'   => 'feed_options_desc',
		// 	'name' => '<strong>' . esc_html__( 'About this tab', 'x2board' ) . '</strong>',
		// 	'desc' => esc_html__( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab. It is recommended to not display thumbnails as there is no easy way to style the related posts in the feed.', 'x2board' ),
		// 	'type' => 'descriptive_text',
		// ),
		// 'limit_feed'         => array(
		// 	'id'      => 'limit_feed',
		// 	'name'    => esc_html__( 'Number of posts to display', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'number',
		// 	'options' => '5',
		// 	'min'     => '0',
		// 	'size'    => 'small',
		// ),
		// 'show_excerpt_feed'  => array(
		// 	'id'      => 'show_excerpt_feed',
		// 	'name'    => esc_html__( 'Show post excerpt', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'checkbox',
		// 	'options' => false,
		// ),
		// 'post_thumb_op_feed' => array(
		// 	'id'      => 'post_thumb_op_feed',
		// 	'name'    => esc_html__( 'Location of the post thumbnail', 'x2board' ),
		// 	'desc'    => '',
		// 	'type'    => 'radio',
		// 	'default' => 'text_only',
		// 	'options' => array(
		// 		'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'x2board' ),
		// 		'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'x2board' ),
		// 		'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'x2board' ),
		// 		'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'x2board' ),
		// 	),
		// ),
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
