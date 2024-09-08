<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * A class to handle extra variables used in posts
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\X2board\\Includes\\Classes\\UserDefineFields' ) ) {

	class UserDefineFields {

		protected $_a_default_fields   = array();
		protected $_a_extends_fields   = array();
		protected $_a_multiline_fields = array();

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			$this->_a_multiline_fields = array( 'html', 'shortcode', 'term_agree' );

			$this->_a_default_fields = array(
				'title'    => array(
					'field_type'      => 'title',
					'field_label'     => __( 'lbl_title', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-title',
					'meta_key'        => 'title',
					'search'          => 'Y',
					'permission'      => 'all',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_title', X2B_DOMAIN ), // admin's memo
					'close_button'    => '',
					'display_on_list' => true,
				),
				'option'   => array(
					'field_type'               => 'option',
					'field_label'              => __( 'lbl_option', X2B_DOMAIN ),
					'field_name'               => '',
					'class'                    => 'x2board-attr-option',
					'meta_key'                 => 'option',
					'search'                   => 'N',
					// 'secret_permission' => '',
					// 'secret' => array(),
					'notice_permission'        => 'roles',
					'notice'                   => array( 'administrator' ),
					'allow_comment_permission' => 'roles',
					'allow_comment'            => array( 'administrator' ),
					'description'              => '', // admin's memo
					'introduction'             => __( 'desc_user_define_field_option', X2B_DOMAIN ), // admin's memo
					'close_button'             => 'yes',
					'display_on_list'          => false,
				),
				/*
				'nick_name' => array(
					'field_type' => 'nick_name',
					'field_label' => __('Nickname', X2B_DOMAIN),
					'field_name' => '',
					'class' => 'x2board-attr-nick-name',
					'meta_key' => 'nick_name',
					'search' => 'Y',
					'permission' => '',
					'default_value' => '',
					'placeholder' => '',
					'description' => '', // admin's memo
					'introduction' => __('Intro nick_name field', X2B_DOMAIN), // admin's memo
					'close_button' => '',
					'display_on_list' => true
				),*/
				'category' => array(
					'field_type'      => 'category',
					'field_label'     => __( 'lbl_category', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-category',
					'meta_key'        => 'category',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'option_field'    => true,
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_category', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				// 'captcha' => array(
				// 'field_type' => 'captcha',
				// 'field_label' => __('Captcha', X2B_DOMAIN),
				// 'class' => 'x2board-attr-captcha',
				// 'meta_key' => 'captcha',
				// 'description' => '',
				// 'close_button' => 'yes'
				// ),
				'content'  => array(
					'field_type'       => 'content',
					'field_label'      => __( 'lbl_content', X2B_DOMAIN ),
					'field_name'       => '',
					'class'            => 'x2board-attr-content',
					'meta_key'         => 'content',
					'search'           => 'Y',
					'email_permission' => '',
					'email'            => array(),
					'placeholder'      => '',
					'description'      => '', // admin's memo
					'introduction'     => __( 'desc_user_define_field_content', X2B_DOMAIN ), // admin's memo
					'required'         => '',
					'close_button'     => 'yes',
					'display_on_list'  => false,
				),
				'attach'   => array(
					'field_type'      => 'attach',
					'field_label'     => __( 'lbl_attachment', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-attach',
					'meta_key'        => 'attach',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_attach', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				'tag'      => array(
					'field_type'      => 'tag',
					'field_label'     => __( 'lbl_tag', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-tag',
					'meta_key'        => 'tag',
					'search'          => 'Y',
					'placeholder'     => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_tag', X2B_DOMAIN ), // admin's memo
					'required'        => '',
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				/*
				'search' => array(
					'field_type' => 'search',
					'field_label' => __('WP Search', X2B_DOMAIN),
					'field_name' => '',
					'class' => 'x2board-attr-search',
					'meta_key' => 'search',
					'search' => 'N',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'description' => '', // admin's memo
					'introduction' => __('Intro search field', X2B_DOMAIN), // admin's memo
					'hidden' => '',
					'close_button' => '',
					'display_on_list' => true
				)*/
			);

			$this->_a_extends_fields = array(
				'text'      => array(
					'field_type'      => 'text',
					'field_label'     => __( 'lbl_text_hidden', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-text',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_text_hidden', X2B_DOMAIN ), // admin's memo
					'required'        => '',
					// 'show_document'   => '',
					'hidden'          => '',
					'close_button'    => 'yes',
					'display_on_list' => true,
				),
				'select'    => array(
					'field_type'      => 'select',
					'field_label'     => __( 'lbl_selectbox', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-select',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'row'             => array(),
					'default_value'   => '',
					'permission'      => '',
					'roles'           => array(),
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_selectbox', X2B_DOMAIN ), // admin's memo
					'required'        => '',
					// 'show_document'   => '',
					'close_button'    => 'yes',
					'display_on_list' => true,
				),
				'radio'     => array(
					'field_type'      => 'radio',
					'field_label'     => __( 'lbl_radio_button', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-radio',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'row'             => array(),
					'default_value'   => '',
					'permission'      => '',
					'roles'           => array(),
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_radio_button', X2B_DOMAIN ), // admin's memo
					'required'        => '',
					// 'show_document'   => '',
					'close_button'    => 'yes',
					'display_on_list' => true,
				),
				'checkbox'  => array(
					'field_type'      => 'checkbox',
					'field_label'     => __( 'lbl_checkbox', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-checkbox',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'row'             => array(),
					'permission'      => '',
					'roles'           => array(),
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_checkbox', X2B_DOMAIN ), // admin's memo
					'required'        => '',
					// 'show_document'   => '',
					'close_button'    => 'yes',
					'display_on_list' => true,
				),
				'textarea'  => array(
					'field_type'      => 'textarea',
					'field_label'     => __( 'lbl_textarea', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-textarea',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_textarea', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				'wp_editor' => array(
					'field_type'      => 'wp_editor',
					'field_label'     => __( 'lbl_wp_editor', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-wp-editor',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'Y',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_wp_editor', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				'term_agree'      => array(
					'field_type'      => 'term_agree',
					'field_label'     => __( 'lbl_term_condition', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-term-agree',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => null,
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_term_agree', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
					'term'            => '',
				),
				'html'      => array(
					'field_type'      => 'html',
					'field_label'     => __( 'lbl_html', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-html',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => null,
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_html', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
					'html'            => '',
				),
				'shortcode' => array(
					'field_type'      => 'shortcode',
					'field_label'     => __( 'lbl_shortcode', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-shortcode',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_shortcode', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
					'shortcode'       => '',
				),
				'date'      => array(
					'field_type'      => 'date',
					'field_label'     => __( 'lbl_date_select', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-date',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_date_select', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				'time'      => array(
					'field_type'      => 'time',
					'field_label'     => __( 'lbl_time_select', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-time',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_time_select', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				'email'     => array(
					'field_type'      => 'email',
					'field_label'     => __( 'lbl_email_address', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-email',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'N',
					'permission'      => '',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => '',
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_email_address', X2B_DOMAIN ), // admin's memo
					'hidden'          => '',
					'close_button'    => 'yes',
					'display_on_list' => true,
				),
				'kr_zip'   => array(
					'field_type'      => 'kr_zip',
					'field_label'     => __( 'lbl_kr_zip', X2B_DOMAIN ),
					'field_name'      => '',
					'class'           => 'x2board-attr-krzip',
					'custom_class'    => '',
					'meta_key'        => '',
					'search'          => 'N',
					'permission'      => 'all',
					'roles'           => array(),
					'default_value'   => '',
					'placeholder'     => '',
					'required'        => '',
					// 'show_document'   => null,
					'description'     => '', // admin's memo
					'introduction'    => __( 'desc_user_define_field_kr_zip', X2B_DOMAIN ), // admin's memo
					'close_button'    => 'yes',
					'display_on_list' => false,
				),
				/*
				'color' => array(
					'field_type' => 'color',
					'field_label' => __('Color Select', X2B_DOMAIN),
					'field_name' => '',
					// 'class' => 'x2board-attr-color',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'description' => '', // admin's memo
					// 'show_document' => '',
					'close_button' => 'yes',
					'display_on_list' => true
				)
				*/
			);
		}

		/**
		 * 기본 필드 정보 반환
		 *
		 * @return _a_default_fields
		 */
		public function get_default_field_info_by_field_type( $s_field_type ) {
			if ( isset( $this->_a_default_fields[ $s_field_type ] ) ) {
				return $this->_a_default_fields[ $s_field_type ];
			}
			return null;
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 *
		 * @return bool
		 */
		protected function _get_field_label() {
			return $this->_a_all_fields[ $this->_s_field_type ]['field_label'];
		}

		/**
		 * 필드 유형 확인.
		 * is_default_field()
		 *
		 * @return string
		 */
		protected function _get_field_type() {
			if ( isset( $this->_a_default_fields[ $this->_s_field_type ] ) ) {
				return 'default';
			}
			return 'extend';
		}

		/**
		 * 저장된 값이 있는지 체크한다.
		 *
		 * @return boolean
		 */
		protected function _is_value_exists() {
			foreach ( $this->_a_row as $key => $item ) {
				if ( isset( $item['label'] ) && $item['label'] ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * 입력 필드에 여러 줄을 입력하는 필드인지 확인한다.
		 *
		 * @return boolean
		 */
		protected function _is_multiline_fields() {
			if ( in_array( $this->_s_field_type, $this->_a_multiline_fields ) ) {
				return true;
			}
			return false;
		}
	}
}
/* End of file UserDefineFields.class.php */
