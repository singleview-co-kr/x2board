<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */
/**
 * High class of the category module
 * @author singleview.co.kr
 */
namespace X2board\Includes\Modules\Post;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\postAdminModel')) {

	class postAdminModel {
		private $_a_default_fields = array();
		private $_a_extends_fields = array();

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// var_dump('categoryAdminModel');
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
				unset($o_current_user);
				wp_die(__('You do not have permission.', 'x2board'));
			}
			unset($o_current_user);

			$this->_a_default_fields = array(
				'title' => array(
					'field_type' => 'title',
					'field_label' => __('Title', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-title',
					'meta_key' => 'title',
					'permission' => 'all',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'description' => '',
					'close_button' => ''
				),
				'option' => array(
					'field_type' => 'option',
					'field_label' => __('Options', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-option',
					'meta_key' => 'option',
					'secret_permission' => '',
					'secret' => array(),
					'notice_permission' => 'roles',
					'notice'=> array('administrator'),
					'allow_comment_permission' => 'roles',
					'allow_comment'=> array('administrator'),
					'description' => '',
					'close_button' => 'yes'
				),
				'nick_name' => array(
					'field_type' => 'nick_name',
					'field_label' => __('Nickname', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-nick-name',
					'meta_key' => 'nick_name',
					'permission' => '',
					'default_value' => '',
					'placeholder' => '',
					'description' => '',
					'close_button' => ''
				),
				'category' => array(
					'field_type' => 'category',
					'field_label' => __('Category', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-tree-category',
					'meta_key' => 'category',
					'permission' => '',
					'roles' => array(),
					'option_field' => true,
					'description' => '',
					'close_button' => 'yes'
				),
				'captcha' => array(
					'field_type' => 'captcha',
					'field_label' => __('Captcha', 'x2board'),
					'class' => 'kboard-attr-captcha',
					'meta_key' => 'captcha',
					'description' => '',
					'close_button' => 'yes'
				),
				'content' => array(
					'field_type' => 'content',
					'field_label' => __('Content', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-content',
					'meta_key' => 'content',
					'placeholder' => '',
					'description' => '',
					'required' => '',
					'close_button' => 'yes'
				),
				'attach' => array(
					'field_type' => 'attach',
					'field_label' => __('Attachment', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-attach',
					'meta_key' => 'attach',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'close_button' => 'yes'
				),
				'search' => array(
					'field_type' => 'search',
					'field_label' => __('WP Search', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-search',
					'meta_key' => 'search',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'description' => '',
					'hidden' => '',
					'close_button' => ''
				)
			);

			$this->_a_extends_fields = array(
				'text' => array(
					'field_type' => 'text',
					'field_label' => __('Text/Hidden', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-text',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'description' => '',
					'required' => '',
					'show_document' => '',
					'hidden' => '',
					'close_button' => 'yes'
				),
				'select' => array(
					'field_type' => 'select',
					'field_label' => __('Select Box', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-select',
					'custom_class' => '',
					'meta_key' => '',
					'row' => array(),
					'default_value' => '',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'close_button' => 'yes'
				),
				'radio' => array(
					'field_type' => 'radio',
					'field_label' => __('Radio Button', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-radio',
					'custom_class' => '',
					'meta_key' => '',
					'row' => array(),
					'default_value' => '',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'close_button' => 'yes'
				),
				'checkbox' => array(
					'field_type' => 'checkbox',
					'field_label' => __('Checkbox', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-checkbox',
					'custom_class' => '',
					'meta_key' => '',
					'row' => array(),
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'required' => '',
					'show_document' => '',
					'close_button' => 'yes'
				),
				'textarea' => array(
					'field_type' => 'textarea',
					'field_label' => __('Textarea', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-textarea',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				),
				'wp_editor' => array(
					'field_type' => 'wp_editor',
					'field_label' => __('WP Editor', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-wp-editor',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				),
				'html' => array(
					'field_type' => 'html',
					'field_label' => __('HTML', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-html',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes',
					'html' => ''
				),
				'shortcode' => array(
					'field_type' => 'shortcode',
					'field_label' => __('Shortcode', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-shortcode',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes',
					'shortcode' => ''
				),
				'date' => array(
					'field_type' => 'date',
					'field_label' => __('Date Select', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-date',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				),
				'time' => array(
					'field_type' => 'time',
					'field_label' => __('Time Select', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-time',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				),
				'email' => array(
					'field_type' => 'email',
					'field_label' => __('Email', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-email',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'hidden' => '',
					'close_button' => 'yes'
				),
				'address' => array(
					'field_type' => 'address',
					'field_label' => __('Address', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-address',
					'custom_class' => '',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'placeholder' => '',
					'required' => '',
					'show_document' => '',
					'description' => '',
					'close_button' => 'yes'
				),
				/*
				'color' => array(
					'field_type' => 'color',
					'field_label' => __('Color Select', 'x2board'),
					'field_name' => '',
					'class' => 'kboard-attr-color',
					'meta_key' => '',
					'permission' => '',
					'roles' => array(),
					'default_value' => '',
					'description' => '',
					'show_document' => '',
					'close_button' => 'yes'
				)
				*/
			);
		}

		/**
		 * 설정된 사용자 입력 필드를 반환한다.
		 * @return array
		 */
		public function get_user_define_fields() {
			return $this->_a_default_fields;
		}

		/**
		 * 확장 필드를 반환한다.
		 * @return array
		 */
		public function get_extended_fields() {
			return $this->_a_extends_fields;
		}

		/**
		 * 입력 필드에 여러 줄을 입력하는 필드인지 확인한다.
		 * @param string $fields_type
		 * @return string
		 */
		// public function isMultiLineFields($fields_type){
		public function is_multiline_fields($s_fields_type) {
			// $multi_line_fields = apply_filters('kboard_multi_line_fields_fields', array('html', 'shortcode'), $this->board);
			if(in_array($s_fields_type, array('html', 'shortcode'))){
				return true;
			}
			return false;
		}
	}
}