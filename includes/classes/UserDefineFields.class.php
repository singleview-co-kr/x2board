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

		private $_a_default_fields = array();
		private $_a_extends_fields = array();


		/**
		 * sequence of board
		 * @var int
		 */
		private $_n_board_id = null;

		/**
		 * Current module's Set of UserDefineItem
		 * @var UserDefineItem[]
		 */
		private $_a_key = null;

		/**
		 * Get instance of ExtraVar (singleton)
		 *
		 * @param int $board_id
		 * @return UserDefineFields
		 */
		public static function getInstance() {
			return new UserDefineFields();
		}

		/**
		 * Constructor
		 *
		 * @param int $board_id Sequence of board
		 * @return void
		 */
		public function __construct() {			
			$this->_a_default_fields = array(
				'title' => array(
					'field_type' => 'title',
					'field_label' => __('Title', 'x2board'),
					'field_name' => '',
					'class' => 'x2board-attr-title',
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
					'class' => 'x2board-attr-option',
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
					'class' => 'x2board-attr-nick-name',
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
					'class' => 'x2board-attr-category',
					'meta_key' => 'category',
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
		 * 사용자 정의 필드 중 기본 필드 반환
		 * @return array
		 */
		public function get_default_fields() {
			return $this->_a_default_fields;
		}

		/**
		 * 사용자 정의 필드 중 확장 필드 반환
		 * @return array
		 */
		public function get_extended_fields() {
			return $this->_a_extends_fields;
		}

		/**
		 * Register a key of user define fields to display on /skins/post.html
		 * 
		 * @param object[] $extra_keys Array of extra variable. A value of array is object that contains board_id, idx, name, default, desc, is_required, search, value, eid.
		 * @return void
		 */
		// function setExtraVarKeys($extra_keys) {
		public function set_user_define_keys_2_display($a_user_define_field) {
			if(!is_array($a_user_define_field) || count($a_user_define_field) < 1) {
				return;
			}
			foreach($a_user_define_field as $val) {
				$s_old_val = isset($val->value) ? $val->value : null;
				$obj = new UserDefineItem($val->board_id, $val->idx, $val->name, $val->type, $val->default, $val->desc, $val->is_required, $val->search, $s_old_val, $val->eid);
				$this->_a_key[$val->idx] = $obj;
			}
		}

		/**
		 * set board id
		 * 
		 * @param $n_board_id
		 * @return void
		 */
		public function set_board_id($n_board_id) {
			$this->_n_board_id = $n_board_id;
		}

		/**
		 * Convert and register Kboard formatted user define fields to display on /skins/editor_post.html
		 * 
		 * @param object[] $extra_keys Array of extra variable. A value of array is object that contains board_id, idx, name, default, desc, is_required, search, value, eid.
		 * @return void
		 */
		public function set_user_define_keys_2_submit($a_user_define_field) {
			if(!is_array($a_user_define_field) || count($a_user_define_field) < 1) {
				return;
			}

			$n_idx = 1;
			foreach( $a_user_define_field as $s_field_type => $a_kb_field ) {
// var_dump($a_kb_field);
				$s_search = isset($a_kb_field['search']) ? $a_kb_field['search'] : null;
				$s_old_val = isset($a_kb_field['value']) ? $a_kb_field['value'] : null;

				$o_misc_info = new \stdClass();
				$o_misc_info->s_placeholder = isset($a_kb_field['placeholder']) ? $a_kb_field['placeholder'] : null;
				$o_misc_info->s_default_css_class = isset($a_kb_field['class']) ? $a_kb_field['class'] : null;
				$o_misc_info->s_permission = isset($a_kb_field['permission']) ? $a_kb_field['permission'] : null;
				$o_misc_info->s_secret_permission = isset($a_kb_field['secret_permission']) ? $a_kb_field['secret_permission'] : null;
				$o_misc_info->a_secret = isset($a_kb_field['secret']) ? $a_kb_field['secret'] : null;
				$o_misc_info->s_notice_permission = isset($a_kb_field['notice_permission']) ? $a_kb_field['notice_permission'] : null;
				$o_misc_info->a_notice = isset($a_kb_field['notice']) ? $a_kb_field['notice'] : null;
				$o_misc_info->s_allow_comment_permission = isset($a_kb_field['allow_comment_permission']) ? $a_kb_field['allow_comment_permission'] : null;
				$o_misc_info->a_allow_comment = isset($a_kb_field['allow_comment']) ? $a_kb_field['allow_comment'] : null;
				$o_misc_info->a_row = isset($a_kb_field['row']) ? $a_kb_field['row'] : null;

				$o_user_define_key = new UserDefineItem($this->_n_board_id, 
										  $n_idx++, 
										  $a_kb_field['field_name'], 
										  $a_kb_field['field_type'], 
										  $a_kb_field['default_value'], 
										  $a_kb_field['description'], 
										  $a_kb_field['required'], 
										  $s_search,
										  $s_old_val, 
										  $a_kb_field['meta_key'],
										  $o_misc_info);
				$this->_a_key[$n_idx] = $o_user_define_key;
			}
		}

		/**
		 * Returns an array of UserDefineItem
		 *
		 * @return UserDefineItem[]
		 */
		// function getExtraVars() {
		public function get_user_define_vars() {
			return $this->_a_key;
		}
	}
}

/**
 * Each value of the extra vars
 *
 * @author XEHub (developers@xpressengine.com)
 */
if (!class_exists('\\X2board\\Includes\\Classes\\UserDefineItem')) {

	class UserDefineItem {

		// 스킨에서 사용 할 사용자 정의 옵션 input, textarea, select 이름의 prefix를 정의한다.
		// const SKIN_OPTION_PREFIX = 'x2board_option_';

		/**
		 * Sequence of board
		 * @var int
		 */
		var $board_id = 0;

		/**
		 * Index of extra variable
		 * @var int
		 */
		var $idx = 0;

		/**
		 * Name of extra variable
		 * @var string
		 */
		var $name = 0;

		/**
		 * Type of extra variable
		 * @var string text, homepage, email_address, tel, textarea, checkbox, date, select, radio, kr_zip
		 */
		var $type = 'text';

		/**
		 * Default values
		 * @var string[]
		 */
		var $default = null;

		/**
		 * Description
		 * @var string
		 */
		var $desc = '';

		/**
		 * Whether required or not requred this extra variable
		 * @var string Y, N
		 */
		var $is_required = 'N';

		/**
		 * Whether can or can not search this extra variable
		 * @var string Y, N
		 */
		var $search = 'N';

		/**
		 * Value
		 * @var string
		 */
		var $value = null;

		/**
		 * Unique id of extra variable in module
		 * @var string
		 */
		var $eid = '';

		/**
		 * Default css class
		 * @var string
		 */
		var $default_css_class = null;

		/**
		 * Placeholder
		 * @var string
		 */
		var $placeholder = null;

		/**
		 * Permission
		 * @var string
		 */
		var $permission = null;

		/**
		 * Permission
		 * @var string
		 */
		var $secret_permission = null;

		/**
		 * Permission
		 * @var string
		 */
		var $secret = null;

		/**
		 * Permission
		 * @var string
		 */
		var $notice_permission = null;

		/**
		 * Permission
		 * @var string
		 */
		var $notice = null;

		/**
		 * Permission
		 * @var string
		 */
		var $allow_comment_permission = null;

		/**
		 * Permission
		 * @var string
		 */
		var $allow_comment = null;

		/**
		 * select box, option info
		 * @var string
		 */
		var $row = null;

		/**
		 * Constructor
		 *
		 * @param int $board_id Sequence of board
		 * @param int $idx Index of extra variable
		 * @param string $type Type of extra variable. text, homepage, email_address, tel, textarea, checkbox, date, sleect, radio, kr_zip
		 * @param string[] $default Default values
		 * @param string $desc Description
		 * @param string $is_required Whether required or not requred this extra variable. Y, N
		 * @param string $search Whether can or can not search this extra variable
		 * @param string $value Value
		 * @param string $eid Unique id of extra variable in module
		 * @return void
		 */
		public function __construct($board_id, $idx, $name, $type = 'text', $default = null, $desc = '', 
									$is_required = 'N', $search = 'N', $value = null, $eid = '', 
									$o_misc_info = null) {
			if(!$idx) {
				return;
			}

			$this->board_id = $board_id;
			$this->idx = $idx;
			$this->name = $name;
			$this->type = $type;
			$this->default = $default;
			$this->desc = $desc;
			$this->is_required = $is_required;
			$this->search = $search;
			$this->value = $value;
			$this->eid = $eid;
			
			if($o_misc_info) {
				if( $o_misc_info->s_placeholder ) {
					$this->placeholder = $o_misc_info->s_placeholder;
				}

				if( $o_misc_info->s_default_css_class ) {
					$this->default_css_class = $o_misc_info->s_default_css_class;
				}
				
				if( $o_misc_info->s_permission ) {
					$this->permission = $o_misc_info->s_permission;
				}
	
				if( $o_misc_info->s_secret_permission ) {
					$this->secret_permission = $o_misc_info->s_secret_permission;
				}
				if( $o_misc_info->a_secret ) {
					$this->secret = $o_misc_info->a_secret;
				}
				if( $o_misc_info->s_notice_permission ) {
					$this->notice_permission = $o_misc_info->s_notice_permission;
				}
				if( $o_misc_info->a_notice ) {
					$this->notice = $o_misc_info->a_notice;
				}
				if( $o_misc_info->s_allow_comment_permission ) {
					$this->allow_comment_permission = $o_misc_info->s_allow_comment_permission;
				}
				if( $o_misc_info->a_allow_comment ) {
					$this->allow_comment = $o_misc_info->a_allow_comment;
				}

				if( $o_misc_info->a_row ) {
					$this->row = $o_misc_info->a_row;
				}
			}
		}

		/**
		 * Sets Value
		 *
		 * @param string $value The value to set
		 * @return void
		 */
		public function setValue($value) {
			$this->value = $value;
		}

		/**
		 * Returns a given value converted based on its type
		 *
		 * @param string $type Type of variable
		 * @param string $value Value
		 * @return string Returns a converted value
		 */
		private function _getTypeValue($type, $value) {
			$value = trim($value);
			if(!isset($value)) {
				return;
			}

			switch($type) {
				case 'homepage' :
					if($value && !preg_match('/^([a-z]+):\/\//i', $value)) {
						$value = 'http://' . $value;
					}
					return \X2board\Includes\escape($value, false);

				case 'tel' :
					if(is_array($value)) {
						$values = $value;
					}
					elseif(strpos($value, '|@|') !== FALSE) {
						$values = explode('|@|', $value);
					}
					elseif(strpos($value, ',') !== FALSE) {
						$values = explode(',', $value);
					}

					$values = array_values($values);
					for($i = 0, $c = count($values); $i < $c; $i++) {
						$values[$i] = trim(\X2board\Includes\escape($values[$i], false));
					}
					return $values;

				case 'checkbox' :
				case 'radio' :
				case 'select' :
					if(is_array($value)) {
						$values = $value;
					}
					elseif(strpos($value, '|@|') !== FALSE) {
						$values = explode('|@|', $value);
					}
					elseif(strpos($value, ',') !== FALSE) {
						$values = explode(',', $value);
					}
					else {
						$values = array($value);
					}

					$values = array_values($values);
					for($i = 0, $c = count($values); $i < $c; $i++) {
						$values[$i] = trim(\X2board\Includes\escape($values[$i], false));
					}
					return $values;

				case 'kr_zip' :
					if(is_array($value)) {
						$values = $value;
					}
					elseif(strpos($value, '|@|') !== false) {
						$values = explode('|@|', $value);
					}
					else {
						$values = array($value);
					}

					$values = array_values($values);
					for($i = 0, $c = count($values); $i < $c; $i++) {
						$values[$i] = trim(\X2board\Includes\escape($values[$i], false));
					}
					return $values;

				//case 'date' :
				//case 'email_address' :
				//case 'text' :
				//case 'textarea' :
				default :
					return \X2board\Includes\escape($value, false);
			}
		}

		/**
		 * Returns a value for HTML
		 *
		 * @return string Returns a value expressed in HTML.
		 */
		public function getValue() {	
			return $this->_getTypeValue($this->type, $this->value);
		}

		/**
		 * Returns a value for HTML
		 *
		 * @return string Returns a value expressed in HTML.
		 */
		public function getValueHTML() {
			$value = $this->_getTypeValue($this->type, $this->value);

			switch($this->type) {
				case 'homepage' :
					return ($value) ? (sprintf('<a href="%s" target="_blank">%s</a>', \X2board\Includes\escape($value, false), strlen($value) > 60 ? substr($value, 0, 40) . '...' . substr($value, -10) : $value)) : "";

				case 'email_address' :
					return ($value) ? sprintf('<a href="mailto:%s">%s</a>', \X2board\Includes\escape($value, false), $value) : "";

				case 'tel' :
					return sprintf('%s-%s-%s', $value[0], $value[1], $value[2]);
					
				case 'textarea' :
					return nl2br($value);
					
				case 'date' :
					return zdate($value, "Y-m-d");

				case 'checkbox' :
				case 'select' :
				case 'radio' :
					if(is_array($value)) {
						return implode(',', $value);
					}
					return $value;

				case 'kr_zip' :
					if(is_array($value)) {
						return implode(' ', $value);
					}
					return $value;

				// case 'text' :
				default :
					return $value;
			}
		}

		/**
		 * Returns a form based on its type
		 *
		 * @return string Returns a form html.
		 */
		public function getFormHTML() {
			// static $id_num = 1000;

			$type = $this->type;
			$s_name = esc_html($this->name);
			$value = $this->_getTypeValue($this->type, $this->value);
			$s_default_value = ($this->_getTypeValue($this->type, $this->default));  // esc_attr
// error_log(print_r($this, true));
			$column_name = $this->eid; //'extra_vars' . $this->idx;
			$s_meta_key = esc_attr($this->eid);
			// $tmp_id = $column_name . '-' . $id_num++;

			$s_required = $this->is_required == '1' ? 'required' : null;
			$s_default_class = $this->default_css_class ? $this->default_css_class : '';
			$s_custom_class = isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : '';
			
			$o_post = \X2board\Includes\Classes\Context::get('post');
// var_dump($this->_getTypeValue($this->type, $this->default));
// var_dump($this);
			$buff = array();
			switch($type) {
				// default fields
				case 'title':
					$buff[] = '<div class="x2board-attr-row '.$s_default_class.' required">';
					$buff[] = 	'<label class="attr-name" for="'.$s_meta_key.'"><span class="field-name">'.$s_name.'</span> <span class="attr-required-text">*</span></label>';
					$buff[] = 	'<div class="attr-value">';
					$s_value = $o_post->title ? esc_attr($o_post->title) : $s_default_value;
					if($this->placeholder) {
						$s_placeholder = 'placeholder="'.esc_attr($this->placeholder).'"';
					}
					else {
						$s_placeholder = null;
					}
					$buff[] = 		'<input type="text" id="'.$s_meta_key.'" name="title" class="required" value="'.$s_value.'" '.$s_placeholder.'>';
							// if(isset($field['description']) && $field['description']){
							// 	'<div class="description">'.esc_html($field['description']).'</div>';
							// }
					$buff[] = 	'</div>';
					$buff[] = '</div>';
					break;
				case 'nick_name':
					if(!is_user_logged_in()) {
						$buff[] = '<div class="x2board-attr-row '.$s_default_class.' required">';
						$buff[] = 	'<label class="attr-name" for="x2board-input-member-display"><span class="field-name">'.$s_name.'</span> <span class="attr-required-text">*</span></label>';
						$s_value = $o_post->nick_name ? esc_attr($o_post->nick_name) : $s_default_value;
						if($this->placeholder) {
							$s_placeholder = 'placeholder="'.esc_attr($this->placeholder).'"';
						}
						else {
							$s_placeholder = null;
						}
						$buff[] = 	'<div class="attr-value"><input type="text" id="x2board-input-nick-name" name="nick_name" class="required" value="'.$s_value.' '.$s_placeholder.'"></div>';
						$buff[] = '</div>';
						$buff[] = '<div class="x2board-attr-row x2board-attr-password">';
						$buff[] = 	'<label class="attr-name" for="x2board-input-password">'.__('Password', 'x2board').' <span class="attr-required-text">*</span></label>';
						$buff[] = 	'<div class="attr-value"><input type="password" id="x2board-input-password" name="password" value="" placeholder="'.__('Password', 'x2board').'..."></div>';
						$buff[] = '</div>';
					}
					break;
				case 'category':
					$buff[] = '<div class="x2board-attr-row '.$s_default_class.' '.$s_required.'">';
					$buff[] = '<label class="attr-name" for="'.$s_meta_key.'"><span class="field-name">'.$s_name.'</span></label>';
					$buff[] = '<div class="attr-value">';
					$buff[] = 	'<div class="x2board-tree-category-wrap">';
					$buff[] = 		'<select id="category_id" name="category_id" class="category">';
					$buff[] = 			'<option value="">'.__('Category select', 'x2board').'</option>';
					$category_list = $this->_get_post_category_list();
					foreach($category_list as $cat_id=>$option_val) {
						if($option_val->grant && $option_val->selected || $o_post->category_id == $cat_id){
							$s_selected = 'selected="selected"';
						}
						else {
							$s_selected = null;
						}
						if(!$option_val->grant) {
							$s_disabled = 'disabled="disabled"';
						}
						else {
							$s_disabled = null;
						}
						$buff[] = 		'<option value="'.$cat_id.'" '.$s_selected.' '.$s_disabled.'>';

						$buff[] = 		str_repeat("&nbsp;&nbsp;",$option_val->depth). $option_val->title. '('.$option_val->post_count.')';
						$buff[] = 		'</option>';
					}
					$buff[] = 		'</select>';
					$buff[] = 	'</div>';
							// if(isset($field['description']) && $field['description']){
							// 	'<div class="description">'.esc_html($field['description']).'</div>';
							// }
					$buff[] = '</div>';
					$buff[] = '</div>';
					break;
				case 'content':
					$o_editor_view = \X2board\Includes\getView('editor');
					$buff[] = $o_editor_view->get_post_editor_html($o_post->post_id, $this->placeholder);//$o_editor_conf);
					unset($o_editor_view);
					break;
				case 'attach':
					$o_module_info = \X2board\Includes\Classes\Context::get('current_module_info');;
					$s_accept_file_types = str_replace(" ", "", $o_module_info->file_allowed_filetypes);
					$s_accept_file_types = str_replace(",", "|", $s_accept_file_types);
					$n_file_max_attached_count = intval($o_module_info->file_max_attached_count);
					$n_file_allowed_filesize_mb = intval($o_module_info->file_allowed_filesize_mb);
					unset($o_module_info);
					wp_enqueue_style("x2board-jquery-fileupload", X2B_URL . 'common/jquery.fileupload/css/jquery.fileupload.css', [], X2B_VERSION);
					// wp_enqueue_style("x2board-jquery-fileupload-ui", X2B_URL . 'common/jquery.fileupload/css/jquery.fileupload-ui.css', [], X2B_VERSION);
					wp_enqueue_script('x2board-jquery-ui-widget', X2B_URL . 'common/jquery.fileupload/js/vendor/jquery.ui.widget.js', ['jquery'], X2B_VERSION, true);
					wp_enqueue_script('x2board-jquery-iframe-transport', X2B_URL . 'common/jquery.fileupload/js/jquery.iframe-transport.js', ['jquery'], X2B_VERSION, true);
					wp_enqueue_script('x2board-fileupload', X2B_URL . 'common/jquery.fileupload/js/jquery.fileupload.js', ['jquery'], X2B_VERSION, true);
					wp_enqueue_script('x2board-fileupload-process', X2B_URL . 'common/jquery.fileupload/js/jquery.fileupload-process.js', ['jquery'], X2B_VERSION, true);
					wp_enqueue_script('x2board-fileupload-caller', X2B_URL . 'common/jquery.fileupload/file-upload.js', ['jquery'], X2B_VERSION, true);
					
					$buff[] = '<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount="'.$n_file_max_attached_count.'" data-accpet_file_types="'.$s_accept_file_types.'" data-max_each_file_size_mb="'.$n_file_allowed_filesize_mb.'">';
					$buff[] = '<ul class="file-list list-unstyled mb-0">';
					foreach($o_post->get_uploaded_files() as $_ => $o_file_value) {
						$buff[] = '<li class="file my-1 row">';
						$buff[] = 	'<div class="file-name col-md-3">';
						$buff[] = 		'<img src="'.$o_file_value->thumbnail_abs_url.'" class="attach_thumbnail">';
						$buff[] = $o_file_value->source_filename;
						$buff[] = 	'</div>';
						$buff[] = 	'<div class="del-button col-md-1">';
						if( $o_file_value->file_type !== 'image'){
							$s_disabled = 'disabled="disabled"';
						}
						else {
							$s_disabled = null;
						}
						$buff[] = 		'<button type="button" class="btn btn-sm btn-danger file-embed" data-thumbnail_abs_url="'.$o_file_value->thumbnail_abs_url.'" '.$s_disabled.'><i class="fa fa-plus"></i></button>';
						$buff[] = 		'<button type="button" class="btn btn-sm btn-danger file-delete" data-file_id="'.$o_file_value->file_id.'"><i class="far fa-trash-alt"></i></button>';
						$buff[] = 	'</div>';
						$buff[] = 	'<div class="progress col-md-7 my-auto px-0">';
						// $buff[] = 		'<div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 100%;"></div>';
						$buff[] = 	'</div>';
						$buff[] = '</li>';
					}
					$buff[] = '</ul>';
					break;
				case 'option':
					$secret_checked_forced = true;
					$buff[] = '<div class="x2board-attr-row '.$s_default_class.'">';
					$buff[] = 	'<label class="attr-name" for="'.$s_meta_key.'"><span class="field-name">'.$s_name.'</span></label>';
					$buff[] = 	'<div class="attr-value">';
					if($this->_is_this_accessible($this->secret_permission, $this->secret)) {
						if($secret_checked_forced && !$this->_is_this_accessible()) {
							$s_checked_disabled = 'checked disabled';
						}
						else {
							$s_checked_disabled = null;
						}

						if($o_post->is_secret) {
							$s_checked = 'checked';
						}
						else {
							$s_checked = null;
						}
						$buff[] = '<label class="attr-value-option"><input type="checkbox" name="secret" value="true" onchange="x2board_toggle_password_field(this)" '.$s_checked_disabled.' '.$s_checked.'> '. __('Secret', 'x2board').'</label>';
					}
					if($this->_is_this_accessible($this->notice_permission, $this->notice)) {
						if($o_post->is_notice) {
							$s_checked = 'checked';
						}
						else {
							$s_checked = null;
						}
						$buff[] = '<label class="attr-value-option"><input type="checkbox" name="notice" value="true" '.$s_checked.'> '. __('Notice', 'x2board').'</label>';
					}
					if($this->_is_this_accessible($this->allow_comment_permission, $this->allow_comment)) {
						if($o_post->title) {
							$s_checked = 'checked';
						}
						else {
							$s_checked = null;
						}
						$buff[] = '<label class="attr-value-option"><input type="checkbox" name="allow_comment" value="true" '.$s_checked.'> '.__('Comment', 'x2board').'</label>';
					}
							// if(isset($field['description']) && $field['description']){
							// 	'<div class="description">'.esc_html($field['description']).'</div>';
							// }
					$buff[] = '</div>';
					$buff[] = '</div>';
					if(!$this->_is_this_accessible()) {
						$buff[] = '<div style="overflow:hidden;width:0;height:0;">';
						$buff[] = 	'<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="text" name="fake-autofill-fields">';
						$buff[] = 	'<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="password" name="fake-autofill-fields">';
						$buff[] = '</div>';
						// $buff[] = '<!-- 비밀글 비밀번호 필드 시작 -->';
						if(!$o_post->is_secret) {
							$s_style = 'style="display:none"';
						}
						else {
							$s_style = null;
						}
						$buff[] = '<div class="x2board-attr-row x2board-attr-password secret-password-row" '.$s_style.'>';
						$buff[] = 	'<label class="attr-name" for="x2board-input-password">'. __('Password', 'x2board').' <span class="attr-required-text">*</span></label>';
						$buff[] = 	'<div class="attr-value"><input type="password" id="x2board-input-password" name="password" value="" placeholder="'. __('Password', 'x2board').'..."></div>';
						$buff[] = '</div>';
						// $buff[] = '<!-- 비밀글 비밀번호 필드 끝 -->';
					}
					break;
				case 'search':
						if(isset($field['hidden']) && $field['hidden'] == '1') {
							$buff[] = '<input type="hidden" name="allow_search" value="'.$s_default_value.'">';
						}
						else {
							$buff[] = '<div class="x2board-attr-row '.$s_default_class.'">';
							$buff[] = 	'<label class="attr-name" for="x2board-select-wordpress-search"><span class="field-name">'.$s_name.'</span></label>';
							$buff[] = 	'<div class="attr-value">';
							$buff[] = 		'<select id="x2board-select-wordpress-search" name="allow_search">';
							if($o_post->allow_search == '1') {
								$selected_1 = 'selected';
							}
							else {
								$selected_1 = null;
							}
							$buff[] = 		'<option value="1" '.$selected_1.'>'.__('Public', 'x2board').'</option>';
							if($o_post->allow_search == '2') {
								$selected_2 = 'selected';
							}
							else {
								$selected_2 = null;
							}
							$buff[] = 		'<option value="2" '.$selected_2.'>'. __('Only title (secret post)', 'x2board').'</option>';
							if($o_post->allow_search == '3') {
								$selected_3 = 'selected';
							}
							else {
								$selected_3 = null;
							}
							$buff[] = 		'<option value="3" '.$selected_3.'>'.__('Exclusion', 'x2board').'</option>';
							$buff[] = 	'</select>';
									// if(isset($field['description']) && $field['description']){
									// 	'<div class="description">'.esc_html($field['description']).'</div>';
									// }
							$buff[] = '</div>';
							$buff[] = '</div>';
						}
					break;
				// extended user define fields
				case 'text':
					if(isset($field['hidden']) && $field['hidden']) {
						$s_value = $o_post->{$s_meta_key} ? esc_attr($o_post->{$s_meta_key}) : $s_default_value;
						$buff[] = '<input type="hidden" id="'.$s_meta_key.'" class="'.$s_required.'" name="'.$s_meta_key.'" value="'.$s_value.'">';
					}
					else {
						$buff[] = '<div class="x2board-attr-row '.$s_default_class.' meta-key-'.$s_meta_key.' '.$s_custom_class.' '.$s_required.'">';
						if($s_required) {
							$s_tmp_required = '<span class="attr-required-text">*</span>';
						}
						else {
							$s_tmp_required = null;
						}
						$buff[] = 	'<label class="attr-name" for="'.$s_meta_key.'"><span class="field-name">'.$s_name.'</span> '.$s_tmp_required.'</label>';
						$buff[] = 	'<div class="attr-value">';
						$s_value = $o_post->{$s_meta_key} ? esc_attr($o_post->{$s_meta_key}) : $s_default_value;
						if($this->placeholder) {
							$s_placeholder = 'placeholder="'.esc_attr($this->placeholder).'"';
						}
						else {
							$s_placeholder = null;
						}
						$buff[] = 		'<input type="text" id="'.$s_meta_key.'" class="'.$s_required.'" name="'.$s_meta_key.'" value="'.$s_value.'" '.$s_placeholder.'>';
							// if(isset($field['description']) && $field['description']){
							// 	'<div class="description">'.esc_html($field['description']).'</div>';
							// }
						$buff[] = 	'</div>';
						$buff[] = '</div>';
					}
					break;
				case 'select':
					$has_default_values = true;
					if( $has_default_values ) {
						$buff[] = '<div class="x2board-attr-row '.$s_default_class.' meta-key-'.$s_meta_key.' '.$s_custom_class.' '.$s_required.'">';
						if($s_required) {
							$s_tmp_required = '<span class="attr-required-text">*</span>';
						}
						else {
							$s_tmp_required = null;
						}
						$buff[] = 	'<label class="attr-name" for="'.$s_meta_key.'"><span class="field-name">'.$s_name.'</span>'.$s_tmp_required.'</label>';
						$buff[] = 	'<div class="attr-value">';
						$buff[] = 		'<select id="'.$s_meta_key.'" name="'.$s_meta_key.'"class="'.$s_required.'">';
						$buff[] = 		'<option value="">'.__('Select', 'x2board').'</option>';
						foreach($this->row as $option_key=>$option_value) {
							if(isset($option_value['label']) && $option_value['label']) {
								if($o_post->{$s_meta_key}) {
									if($o_post->{$s_meta_key} == $option_value['label']) {
										$s_selected = 'selected';
									}
									else {
										$s_selected = null;
									}
									$buff[] = '<option value="'.esc_attr($option_value['label']).'" '.$s_selected.'>'.esc_html($option_value['label']).'</option>';
								}
								else {
									if($this->default && $this->default==$option_key) {
										$s_selected = 'selected';
									}
									else {
										$s_selected = null;
									}
									$buff[] = '<option value="'.esc_attr($option_value['label']).'" '.$s_selected.'>'.esc_html($option_value['label']).'</option>';
								}
							}
						}
						$buff[] = 		'</select>';
							// if(isset($field['description']) && $field['description']){
							// 	'<div class="description">'.esc_html($field['description']).'</div>';
							// }
						$buff[] = 	'</div>';
						$buff[] = '</div>';
					}
					break;
				default:
					var_dump($type);
			}
			unset($o_post);
			if($this->desc) {
				// $oModuleController = getController('module');
				// $oModuleController->replaceDefinedLangCode($this->desc);
				$buff[] = '<p>' . htmlspecialchars($this->desc, ENT_COMPAT | ENT_HTML401, 'UTF-8', false) . '</p>';
			}
			return join(PHP_EOL, $buff);
		}

		/**
		 * 입력 필드 이름을 반환한다.
		 * \includes\modules\board\skins\sketchbook5\editor-fields.php에서 사용
		 * @param string $name
		 * @return string
		 */
		// public function getOptionFieldName($name){
		// private function _get_option_field_name( $s_name ) {
		// 	return self::SKIN_OPTION_PREFIX . sanitize_key($s_name);
		// }

		private function _is_this_accessible($permission = null, $roles = null) {
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y') {  // allow everything to an admin
				unset($o_logged_info);
				return true;
			}
			$o_grant = \X2board\Includes\Classes\Context::get('grant');
			if( $o_grant->manager ) {  // allow everything to a manager
				unset($o_grant);
				return true;
			}
			unset($o_grant);
			switch($permission) {
				case 'all': 
					return true;
				case 'author': 
					return is_user_logged_in() ? true : false;
				case 'roles':
					if(is_user_logged_in()) {
						if(array_intersect($roles, (array)$o_logged_info->roles)){
							unset($o_logged_info);
							return true;
						}
					}
					unset($o_logged_info);
					return false;
				default: 
					unset($o_logged_info);
					return true;
			}
		}
		
		private function _get_post_category_list() {
			return \X2board\Includes\Classes\Context::get('category_list');
		}
	}
}
/* End of file UserDefineFields.class.php */