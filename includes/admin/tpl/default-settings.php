<?php
/**
 * Default settings.
 *
 * Functions to register the default settings of the plugin.
 *
 * @link https://singleview.co.kr 
 *
 * @package x2board
 */

namespace X2board\Includes\Admin\Tpl;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Retrieve the array of plugin settings
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
	 * @param array   $a_x2b_settings Settings array
	 */
	return apply_filters( 'x2b_registered_settings', $a_x2b_settings );
}

/**
 * Retrieve the array of General settings
 *
 * @return array General settings array
 */
function x2b_settings_general() {
	$settings = array(
		'x2board_title'					=> array(
			'id'      => 'board_title',
			'name'    => __( 'name_x2board_title', X2B_DOMAIN ),
			'desc'    => __( 'desc_x2board_title', X2B_DOMAIN ),
			'type'    => 'text',
			'options' => false,
		),
		'wp_page_title'					=> array(
			'id'      => 'wp_page_title',
			'name'    => __( 'name_wp_page_title', X2B_DOMAIN ),
			'desc'    => __( 'desc_wp_page_title', X2B_DOMAIN ),
			'type'    => 'text',
			'options' => false,
		),
		'board_skin'					=> array(
			'id'      => 'board_skin',
			'name'    => __( 'name_board_skin', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_skin', X2B_DOMAIN ),
			'type'    => 'select',
			'options' => x2b_get_board_skins(),
		),
		'board_list_count'		      => array(
			'id'      => 'board_list_count',
			'name'    => __( 'name_board_list_count', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_list_count', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_search_list_count'		      => array(
			'id'      => 'board_search_list_count',
			'name'    => __( 'name_board_search_list_count', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_search_list_count', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_page_count'		      => array(
			'id'      => 'board_page_count',
			'name'    => __( 'name_board_page_count', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_page_count', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '10',
		),
		'board_excerpted_title_length'		      => array(
			'id'      => 'board_excerpted_title_length',
			'name'    => __( 'name_board_excerpted_title_length', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_excerpted_title_length', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '20',
		),
		'board_header_text'     => array(
			'id'      => 'board_header_text',
			'name'    => __( 'name_board_header_text', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_header_text', X2B_DOMAIN ),
			'type'    => 'textarea',
			'options' => false, // __( 'No related posts found', X2B_DOMAIN ),
		),
		'board_footer_text'     => array(
			'id'      => 'board_footer_text',
			'name'    => __( 'name_board_footer_text', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_footer_text', X2B_DOMAIN ),
			'type'    => 'textarea',
			'options' => false,
		),
		'mobile_setup_header'					=> array(
			'id'      => 'mobile_setup_header',
			// 'name'    => __( 'Advanced setup', X2B_DOMAIN ),
			'desc'    => __( 'desc_mobile_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'board_mobile_use_editor'  => array(
			'id'      => 'board_mobile_use_editor',
			'name'    => __( 'name_board_mobile_use_editor', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_mobile_use_editor', X2B_DOMAIN ),
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
			// 'name'    => __( 'Advanced setup', X2B_DOMAIN ),
			'desc'    => __( 'desc_advanced_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'board_use_rewrite'  => array(
			'id'      => 'board_use_rewrite',
			'name'    => __( 'name_board_use_rewrite', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_use_rewrite', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_order_target'					=> array(
			'id'      => 'board_order_target',
			'name'    => __( 'name_board_order_target', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_order_target', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'list_order',
			'options' => array(
				'list_order'            => __( 'opt_list_order', X2B_DOMAIN ),  // 문서번호
				'update_order'        => __( 'opt_update_order', X2B_DOMAIN ),  // 최근 수정일
				'regdate_dt'            => __( 'opt_regdate_dt', X2B_DOMAIN ), // 등록일
				'voted_count'        => __( 'opt_voted_count', X2B_DOMAIN ),  // 추천 수
				'blamed_count'            => __( 'opt_blamed_count', X2B_DOMAIN ),  // 비추천 수
				'readed_count'        => __( 'opt_readed_count', X2B_DOMAIN ),  // 조회 수
				'comment_count'            => __( 'opt_comment_count', X2B_DOMAIN ),  // 댓글 수
				'title'        => __( 'opt_title', X2B_DOMAIN ),  // 제목
				'nick_name'            => __( 'opt_nick_name', X2B_DOMAIN ),  // 닉네임
				'user_id'            => __( 'opt_user_id', X2B_DOMAIN )  // 아이디
			),
		),
		'board_order_type'					=> array(
			'id'      => 'board_order_type',
			'name'    => __( 'name_board_order_type', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_order_type', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'asc',
			'options' => array(
				'asc'            => __( 'opt_asc', X2B_DOMAIN ),  // 오름차순
				'desc'        => __( 'opt_desc', X2B_DOMAIN ),  // 내림차순
			),
		),
		'board_except_notice'  => array(
			'id'      => 'board_except_notice',
			'name'    => __( 'name_board_except_notice', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_except_notice', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_use_anonymous'  => array(
			'id'      => 'board_use_anonymous',
			'name'    => __( 'name_board_use_anonymous', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_use_anonymous', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_consultation'  => array(
			'id'      => 'board_consultation',
			'name'    => __( 'name_board_consultation', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_consultation', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_protect_content'  => array(
			'id'      => 'board_protect_content',
			'name'    => __( 'name_board_protect_content', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_protect_content', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_use_status'                       => array(
			'id'      => 'board_use_status',
			'name'    => __( 'name_board_use_status', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_use_status', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'options' => array(
				'PUBLIC'            => __( 'opt_public', X2B_DOMAIN ),
				'SECRET'            => __( 'opt_secret', X2B_DOMAIN ),
			),
			'mandatory' => array(
				'PUBLIC'            => 'mandatory',
			),
		),
		'board_admin_mail'					=> array(
			'id'      => 'board_admin_mail',
			'name'    => __( 'name_board_admin_mail', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_admin_mail', X2B_DOMAIN ),
			'type'    => 'text',
			'options' => false,
		),
	);

	/**
	 * Filters the General settings array
	 *
	 * @param array $settings General settings array
	 */
	return apply_filters( 'x2b_settings_general', $settings );
}


/**
 * Retrieve the array of category settings
 *
 * @return array category settings array
 */
function x2b_settings_category() {

	$settings = array(
		/*'board_use_category'  => array(
			'id'      => 'board_use_category',
			'name'    => __( 'Use category', X2B_DOMAIN ),
			'desc'    => __( 'Select to activate board category', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_hide_category'  => array(
			'id'      => 'board_hide_category',
			'name'    => __( 'name_board_hide_category', X2B_DOMAIN ),
			'desc'    => __( 'desc_board_hide_category', X2B_DOMAIN ),
			'type'    => 'checkbox',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),*/
		'board_category_info'  => array(
			'id'      => 'board_category_info',
			'name'    => __( 'name_board_category_info', X2B_DOMAIN ),
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
	 * @param array $settings List settings array
	 */
	return apply_filters( 'x2b_settings_category', $settings );
}


/**
 * Retrieve the array of user define field settings
 *
 * @return array user define field settings array
 */
function x2b_settings_user_define_field() {

	$settings = array(
		'board_user_define_field'  => array(
			'id'      => 'board_user_define_field',
			'name'    => __( 'name_board_user_define_field', X2B_DOMAIN ),
			'type'    => 'wpuserfieldui',
			'options' => false,
			'checked_value' => array(
				'checked' => 'Y',
				'unchecked'   => 'N',
			),
		),
		'board_list_fields'  => array(
			'id'      => 'board_list_fields',
			'name'    => __( 'name_board_list_fields', X2B_DOMAIN ),
			'type'    => 'wplistfieldui',
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
	 * @param array $settings Output settings array
	 */
	return apply_filters( 'x2b_settings_user_define_field', $settings );
}


/**
 * Retrieve the array of permission settings
 *
 * @return array Thumbnail settings array
 */
function x2b_settings_permission() {

	$settings = array(
		'board_grant_access'               => array(
			'id'      => 'board_grant_access',
			'name'    => __( 'name_board_grant_access', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_grant_list'               => array(
			'id'      => 'board_grant_list',
			'name'    => __( 'name_board_grant_list', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_grant_view'               => array(
			'id'      => 'board_grant_view',
			'name'    => __( 'name_board_grant_view', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_grant_write_post'               => array(
			'id'      => 'board_grant_write_post',
			'name'    => __( 'name_board_grant_write_post', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_grant_write_comment'               => array(
			'id'      => 'board_grant_write_comment',
			'name'    => __( 'name_board_grant_write_comment', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ALL_USERS,
			'options' => x2b_get_grants(),
		),
		'board_grant_consultation_read'               => array(
			'id'      => 'board_grant_consultation_read',
			'name'    => __( 'name_board_grant_consultation_read', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ADMINISTRATOR,  // means Managers
			'options' => x2b_get_grants(),
		),
		'board_grant_manager'               => array(
			'id'      => 'board_grant_manager',
			'name'    => __( 'name_board_grant_manager', X2B_DOMAIN ),
			'type'    => 'grantselect',
			'default' => X2B_ADMINISTRATOR,  // means Managers
			'options' => x2b_get_grants(),
		),
	);

	/**
	 * Filters the Thumbnail settings array
	 *
	 * @param array $settings Thumbnail settings array
	 */
	return apply_filters( 'x2b_settings_permission', $settings );
}


/**
 * Retrieve the array of extra settings
 *
 * @return array extra settings array
 */
function x2b_settings_extra() {

	$settings = array(
		'comment_setup_header'					=> array(
			'id'      => 'comment_setup_header',
			'desc'    => __( 'desc_comment_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'comment_count'		      => array(
			'id'      => 'comment_count',
			'name'    => __( 'name_comment_count', X2B_DOMAIN ),
			'desc'    => __( 'desc_comment_count', X2B_DOMAIN ),
			'default' => '20',
			'type'    => 'number',
			'options' => '20',
		),
		'comment_use_vote_up'					=> array(
			'id'      => 'comment_use_vote_up',
			'name'    => __( 'name_comment_use_vote_up', X2B_DOMAIN ),
			'desc'    => __( 'desc_comment_use_vote_up', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'Y'            => __( 'opt_activate', X2B_DOMAIN ),  // 사용
				'S'        => __( 'opt_activate', X2B_DOMAIN ).' + '.__( 'opt_display', X2B_DOMAIN ),  // 사용+노출
				'N'            => __( 'opt_deactivate', X2B_DOMAIN ),  // 미사용
			),
		),
		'comment_use_vote_down'					=> array(
			'id'      => 'comment_use_vote_down',
			'name'    => __( 'name_comment_use_vote_down', X2B_DOMAIN ),
			'desc'    => __( 'desc_comment_use_vote_down', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'Y'            => __( 'opt_activate', X2B_DOMAIN ),  // 사용
				'S'        => __( 'opt_activate', X2B_DOMAIN ).' + '.__( 'opt_display', X2B_DOMAIN ),  // 사용+노출
				'N'            => __( 'opt_deactivate', X2B_DOMAIN ),  // 미사용
			),
		),
		'comment_use_validation'					=> array(
			'id'      => 'comment_use_validation',
			'name'    => __( 'name_comment_use_validation', X2B_DOMAIN ),
			'desc'    => __( 'desc_comment_use_validation', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'N'            => __( 'opt_activate', X2B_DOMAIN ),  // 미사용
				'Y'            => __( 'opt_deactivate', X2B_DOMAIN ),  // 사용
			),
		),
		'comment_forbid_to_leave_comment_old_post_days'					=> array(
			'id'      => 'comment_forbid_to_leave_comment_old_post_days',
			'name'    => __( 'name_comment_forbid_to_leave_comment_old_post_days', X2B_DOMAIN ),
			'desc'    => __( 'desc_comment_forbid_to_leave_comment_old_post_days', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '',
		),
		'allow_comment_for_admin_for_old_post'					=> array(
			'id'      => 'allow_comment_for_admin_for_old_post',
			'name'    => __( 'name_allow_comment_for_admin_for_old_post', X2B_DOMAIN ),
			'desc'    => __( 'desc_allow_comment_for_admin_for_old_post', X2B_DOMAIN ),
			'type'    => 'select',
			'default' => 'N',
			'options' => array(
				'N'            => __( 'opt_activate', X2B_DOMAIN ),  // 미사용
				'Y'            => __( 'opt_deactivate', X2B_DOMAIN ),  // 사용
			),
		),
		'post_editor_setup_header'					=> array(
			'id'      => 'post_editor_setup_header',
			'desc'    => __( 'desc_post_editor_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'post_editor_skin'					=> array(
			'id'      => 'post_editor_skin',
			'name'    => __( 'name_post_editor_skin', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_post_editor_skin', X2B_DOMAIN ),
			'type'    => 'select',
			'options' => x2b_get_editors(),
		),
		'post_editor_height'		    => array(
			'id'      => 'post_editor_height',
			'name'    => __( 'name_post_editor_height', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_post_editor_height', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '100',
		),
		'enable_html_grant'               => array(
			'id'      => 'enable_html_grant',
			'name'    => __( 'name_enable_html_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_enable_html_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'upload_file_grant'               => array(
			'id'      => 'upload_file_grant',
			'name'    => __( 'name_upload_file_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_upload_file_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'comment_editor_setup_header'					=> array(
			'id'      => 'comment_editor_setup_header',
			'desc'    => __( 'desc_comment_editor_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'comment_editor_skin'					=> array(
			'id'      => 'comment_editor_skin',
			'name'    => __( 'name_comment_editor_skin', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_comment_editor_skin', X2B_DOMAIN ),
			'type'    => 'select',
			'options' => x2b_get_editors(),
		),
		'comment_editor_height'		    => array(
			'id'      => 'comment_editor_height',
			'name'    => __( 'name_comment_editor_height', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_comment_editor_height', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '100',
		),
		'enable_comment_html_grant'               => array(
			'id'      => 'enable_comment_html_grant',
			'name'    => __( 'name_enable_comment_html_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_enable_comment_html_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'comment_upload_file_grant'               => array(
			'id'      => 'comment_upload_file_grant',
			'name'    => __( 'name_comment_upload_file_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_comment_upload_file_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => false,
			'options' => x2b_get_editable_roles(),
			'mandatory' => array(
				'administrator'            => 'mandatory',
			),
		),
		'common_editor_setup_header'					=> array(
			'id'      => 'common_editor_setup_header',
			'desc'    => __( 'desc_common_editor_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'content_style'					=> array(
			'id'      => 'content_style',
			'name'    => __( 'name_content_style', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_content_style', X2B_DOMAIN ),
			'type'    => 'select',
			'options' => x2b_get_content_styles(),
		),
		'content_font'					=> array(
			'id'      => 'content_font',
			'name'    => __( 'name_content_font', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_content_font', X2B_DOMAIN ),
			'type'    => 'text',
			'options' => false,
		),
		'content_font_size'		    => array(
			'id'      => 'content_font_size',
			'name'    => __( 'name_content_font_size', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_content_font_size', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => false,
		),
		'enable_autosave'  => array(
			'id'      => 'enable_autosave',
			'name'    => __( 'name_enable_autosave', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_enable_autosave', X2B_DOMAIN ),
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
			'name'    => __( 'name_enable_default_component_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_enable_default_component_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => '',
			'options' => x2b_get_editable_roles(),
		),
		'enable_component_grant'               => array(
			'id'      => 'enable_component_grant',
			'name'    => __( 'name_enable_component_grant', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_enable_component_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => '',
			'options' => x2b_get_editable_roles(),
		),
		'file_attachment_setup_header'	=> array(
			'id'      => 'file_attachment_setup_header',
			'desc'    => __( 'desc_file_attachment_setup_header', X2B_DOMAIN ),
			'type'    => 'header',
			'options' => false,
		),
		'file_allowed_filesize_mb'	=> array(
			'id'      => 'file_allowed_filesize_mb',
			'name'    => __( 'name_file_allowed_filesize_mb', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allowed_filesize_mb', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_allowed_attach_size_mb'	=> array(
			'id'      => 'file_allowed_attach_size_mb',
			'name'    => __( 'name_file_allowed_attach_size_mb', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allowed_attach_size_mb', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_max_attached_count'	=> array(
			'id'      => 'file_max_attached_count',
			'name'    => __( 'name_file_max_attached_count', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_max_attached_count', X2B_DOMAIN ),
			'type'    => 'number',
			'options' => '2',
		),
		'file_allowed_filetypes'     => array(
			'id'      => 'file_allowed_filetypes',
			'name'    => __( 'name_file_allowed_filetypes', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allowed_filetypes', X2B_DOMAIN ),
			'type'    => 'textarea',
			'options' => 'jpg, jpeg, gif, png, bmp, pjp, pjpeg, jfif, svg, webp, ico, zip, 7z, hwp, ppt, xls, doc, txt, pdf, xlsx, pptx, docx, torrent, smi, mp4, mp3',
		),
		'thumbnail_type'               => array(  // this param comes from /index.php?module=admin&act=dispAdminConfigGeneral
			'id'      => 'thumbnail_type',
			'name'    => __( 'name_thumbnail_type', X2B_DOMAIN ),
			// 'desc'    => __( 'desc_thumbnail_type', X2B_DOMAIN ),
			'type'    => 'radio',
			'default' => 'crop',
			'options' => array(
				'crop'      => __( 'opt_crop', X2B_DOMAIN ).'('.__( 'opt_default', X2B_DOMAIN ).')',
				'ratio'       => __( 'opt_ratio', X2B_DOMAIN ),
			),
		),
		'file_allow_outlink'               => array(
			'id'      => 'file_allow_outlink',
			'name'    => __( 'name_file_allow_outlink', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allow_outlink', X2B_DOMAIN ),
			'type'    => 'radio',
			'default' => 'Y',
			'options' => array(
				'Y'      => __( 'opt_allow', X2B_DOMAIN ),
				'N'       => __( 'opt_disallow', X2B_DOMAIN ),
			),
		),
		'file_allow_outlink_format'     => array(
			'id'      => 'file_allow_outlink_format',
			'name'    => __( 'name_file_allow_outlink_format', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allow_outlink_format', X2B_DOMAIN ),
			'type'    => 'textarea',
			'options' => false,
		),
		'file_allow_outlink_site'     => array(
			'id'      => 'file_allow_outlink_site',
			'name'    => __( 'name_file_allow_outlink_site', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_allow_outlink_site', X2B_DOMAIN ),
			'type'    => 'textarea',
			'options' => false,
		),
		'file_download_grant'               => array(
			'id'      => 'file_download_grant',
			'name'    => __( 'name_file_download_grant', X2B_DOMAIN ),
			'desc'    => __( 'desc_file_download_grant', X2B_DOMAIN ),
			'type'    => 'multicheck',
			'default' => false, // array( 'single' => 'single',),
			'options' => x2b_get_editable_roles(),
		),
	);

	/**
	 * Filters the Styles settings array
	 *
	 * @param array $settings Styles settings array
	 */
	return apply_filters( 'x2b_settings_extra', $settings );
}


/**
 * Retrieve the array of skin vars settings
 *
 * @return array Feed skin vars array
 */
function x2b_settings_skin_vars() {
	// First, we refer the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	// prepare for blank skin vars case
	$settings = array('skin_vars_setup_header'	=> array(
		'id'      => 'skin_vars_setup_header',
		'desc'    => __( 'desc_skin_vars_setup_header_no_file', X2B_DOMAIN ),
		'type'    => 'header',
		'options' => false,
	));

	if($A_X2B_ADMIN_BOARD_SETTINGS && isset($A_X2B_ADMIN_BOARD_SETTINGS['board_skin'])){
		$s_skin_vars_path = X2B_PATH . 'includes\modules\board\skins\\'.$A_X2B_ADMIN_BOARD_SETTINGS['board_skin'].'\skin_vars.php';
		if( file_exists($s_skin_vars_path) ) {
			require_once $s_skin_vars_path;
			$a_tmp_settings = array('skin_vars_setup_header'	=> array(
				'id'      => 'skin_vars_setup_header',
				'desc'    => __( 'desc_skin_vars_setup_header_file_registered', X2B_DOMAIN ),
				'type'    => 'header',
				'options' => false,
			));
			foreach($settings as $s_id => $a_skin_var ) { // this $settings from require_once()
				$a_skin_var['id'] = X2B_SKIN_VAR_IDENTIFIER.$a_skin_var['id'];
				$a_tmp_settings[X2B_SKIN_VAR_IDENTIFIER.$s_id] = $a_skin_var;
			}
			$settings = $a_tmp_settings;
			unset($a_tmp_settings);
		}
	}

	/**
	 * Filters the Feed settings array
	 *
	 * @param array $settings Feed settings array
	 */
	return apply_filters( 'x2b_settings_skin_vars', $settings );
}

/**
 * Get the various skins.
 *
 * @return array skins options.
 */
function x2b_get_board_skins() {
	$s_skin_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'board'.DIRECTORY_SEPARATOR.'skins';
	$a_skins = \X2board\Includes\Classes\FileHandler::readDir($s_skin_path_abs);

	$a_skin_info = array();
	foreach($a_skins as $_ => $s_skin_name) {
		$a_skin_info[$s_skin_name] = __( $s_skin_name, X2B_DOMAIN );
	}
	unset($a_skins);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_board_skins', $a_skin_info );
}


/**
 * Get the various skins.
 * @return array Style options.
 */
function x2b_get_editors() {

	$s_skin_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'editor'.DIRECTORY_SEPARATOR.'skins';
	$a_skins = \X2board\Includes\Classes\FileHandler::readDir($s_skin_path_abs);

	$a_skin_info = array();
	foreach($a_skins as $_ => $s_skin_name) {
		$a_skin_info[$s_skin_name] = __( $s_skin_name, X2B_DOMAIN );
	}
	unset($a_skins);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_editors', $a_skin_info );
}


/**
 * Get the various content styles.
 *
 * @return array Style options.
 */
function x2b_get_content_styles() {
	$s_style_path_abs = X2B_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'editor'.DIRECTORY_SEPARATOR.'styles';
	$a_styles = \X2board\Includes\Classes\FileHandler::readDir($s_style_path_abs);

	$a_style_info = array();
	foreach($a_styles as $_ => $s_style_name) {
		$a_style_info[$s_style_name] = $s_style_name;
	}
	unset($a_styles);

	/**
	 * Filter the array containing the skins to add your own.
	 *
	 * @param array $skins Different skins.
	 */
	return apply_filters( 'x2b_get_content_styles', $a_style_info );
}


/**
 * Get x2b grants.
 *
 * @return array Style options.
 */
function x2b_get_grants() {

	$a_roles = array();
	$a_roles[X2B_ALL_USERS] = __( 'opt_role_all_users', X2B_DOMAIN );
	$a_roles[X2B_LOGGEDIN_USERS] = __( 'opt_role_loggedin_users', X2B_DOMAIN );
	// $a_roles[X2B_REGISTERED_USERS] = __( 'Registered users', X2B_DOMAIN );
	$a_roles[X2B_ADMINISTRATOR] = __( 'opt_role_administrator', X2B_DOMAIN );
	$a_roles[X2B_CUSTOMIZE] = __( 'opt_role_customize', X2B_DOMAIN );
	
	/**
	 * Filter the array to allow privilege
	 *
	 * @param array $roles Different roles.
	 */
	return apply_filters( 'x2b_get_grants', $a_roles );
}

/**
 * Get the various skins.
 *
 * @return array Style options.
 */
function x2b_get_editable_roles() {
	if (!function_exists('get_editable_roles')) {
		require_once(ABSPATH . '/wp-admin/includes/user.php');
	}

	$a_roles = array();
	// $a_roles['all'] = __( 'All users', X2B_DOMAIN );
	// $a_roles['loggedin_user'] = __( 'Loggedin users', X2B_DOMAIN ); // maybe subscribers of WP
	foreach(get_editable_roles() as $roles_key=>$roles_value) {
		$a_roles[$roles_key] = $roles_value['name'];
	}	

	/**
	 * Filter the array to allow privilege
	 *
	 * @param array $roles Different roles.
	 */
	return apply_filters( 'x2b_get_editable_roles', $a_roles );
}