<?php
namespace X2board\Includes\Classes;
/* Copyright (C) XEHub <https://www.xehub.io> */

/**
 * A class to handle extra variables used in posts, member and others
 *
 * @author XEHub (developers@xpressengine.com)
 */ 
if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Classes\\UserDefineFields')) {

	class UserDefineFields {

		protected $_a_default_fields = array();
		protected $_a_extends_fields = array();
		protected $_a_multiline_fields = array();

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			$this->_a_multiline_fields = array('html', 'shortcode');

			$this->_a_default_fields = array(
				'title' => array(
					'field_type' => 'title',
					'field_label' => __('Title', 'x2board'),
					'field_name' => '',
					'class' => 'x2board-attr-title',
					'meta_key' => 'title',
					'search' => 'Y',
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
					'class' => 'x2board-attr-option',
					'meta_key' => 'option',
					'search' => 'N',
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
					'class' => 'x2board-attr-nick-name',
					'meta_key' => 'nick_name',
					'search' => 'Y',
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
					'class' => 'x2board-attr-category',
					'meta_key' => 'category',
					'search' => 'N',
					'permission' => '',
					'roles' => array(),
					'option_field' => true,
					'description' => '',
					'close_button' => 'yes'
				),
				// 'captcha' => array(
				// 	'field_type' => 'captcha',
				// 	'field_label' => __('Captcha', 'x2board'),
				// 	'class' => 'x2board-attr-captcha',
				// 	'meta_key' => 'captcha',
				// 	'description' => '',
				// 	'close_button' => 'yes'
				// ),
				'content' => array(
					'field_type' => 'content',
					'field_label' => __('Content', 'x2board'),
					'field_name' => '',
					'class' => 'x2board-attr-content',
					'meta_key' => 'content',
					'search' => 'Y',
					'placeholder' => '',
					'description' => '',
					'required' => '',
					'close_button' => 'yes'
				),
				'attach' => array(
					'field_type' => 'attach',
					'field_label' => __('Attachment', 'x2board'),
					'field_name' => '',
					'class' => 'x2board-attr-attach',
					'meta_key' => 'attach',
					'search' => 'N',
					'permission' => '',
					'roles' => array(),
					'description' => '',
					'close_button' => 'yes'
				),
				'search' => array(
					'field_type' => 'search',
					'field_label' => __('WP Search', 'x2board'),
					'field_name' => '',
					'class' => 'x2board-attr-search',
					'meta_key' => 'search',
					'search' => 'N',
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
					'class' => 'x2board-attr-text',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-select',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-radio',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-checkbox',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-textarea',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-wp-editor',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'Y',
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
					'class' => 'x2board-attr-html',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					'class' => 'x2board-attr-shortcode',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					'class' => 'x2board-attr-date',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					'class' => 'x2board-attr-time',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					'class' => 'x2board-attr-email',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					'class' => 'x2board-attr-address',
					'custom_class' => '',
					'meta_key' => '',
					'search' => 'N',
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
					// 'class' => 'x2board-attr-color',
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
		 * 번역된 필드의 레이블을 반환한다.
		 * @return bool
		 */
		protected function _get_field_label(){
			return $this->_a_all_fields[$this->_s_field_type]['field_label'];
		}

		/**
		 * 필드 유형 확인.
		 * @return string
		 */
		// is_default_field
		protected function _get_field_type() {
			if(isset($this->_a_default_fields[$this->_s_field_type])) {
				return 'default';
			}
			return 'extend';
		}

		/**
		 * 저장된 값이 있는지 체크한다.
		 * @return boolean
		 */
		protected function _is_value_exists() {
			foreach($this->_a_row as $key=>$item) {
				if(isset($item['label']) && $item['label']) {
					return true;
				}
			}
			return false;
		}

		/**
		 * 입력 필드에 여러 줄을 입력하는 필드인지 확인한다.
		 * @return boolean
		 */
		protected function _is_multiline_fields() {
			if(in_array($this->_s_field_type, $this->_a_multiline_fields )){
				return true;
			}
			return false;
		}
	}
}
/* End of file UserDefineFields.class.php */