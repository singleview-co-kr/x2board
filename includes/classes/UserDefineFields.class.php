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
		public static function getInstance($board_id) {
			return new UserDefineFields($board_id);
		}

		/**
		 * Constructor
		 *
		 * @param int $board_id Sequence of board
		 * @return void
		 */
		function __construct($board_id=null) {
			$this->_n_board_id = $board_id;
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
				// 'captcha' => array(
				// 	'field_type' => 'captcha',
				// 	'field_label' => __('Captcha', 'x2board'),
				// 	'class' => 'kboard-attr-captcha',
				// 	'meta_key' => 'captcha',
				// 	'description' => '',
				// 	'close_button' => 'yes'
				// ),
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
		 * Register a key of user define fields
		 * 
		 * @param object[] $extra_keys Array of extra variable. A value of array is object that contains board_id, idx, name, default, desc, is_required, search, value, eid.
		 * @return void
		 */
		// function setExtraVarKeys($extra_keys) {
		function set_user_define_keys($a_user_define_field) {
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
		 * Returns an array of UserDefineItem
		 *
		 * @return UserDefineItem[]
		 */
		// function getExtraVars() {
		function get_user_define_vars() {
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
		function __construct($board_id, $idx, $name, $type = 'text', $default = null, $desc = '', $is_required = 'N', $search = 'N', $value = null, $eid = '') {
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
		}

		/**
		 * Sets Value
		 *
		 * @param string $value The value to set
		 * @return void
		 */
		function setValue($value) {
			$this->value = $value;
		}

		/**
		 * Returns a given value converted based on its type
		 *
		 * @param string $type Type of variable
		 * @param string $value Value
		 * @return string Returns a converted value
		 */
		function _getTypeValue($type, $value) {
			$value = trim($value);
			if(!isset($value)) {
				return;
			}

			switch($type) {
				case 'homepage' :
					if($value && !preg_match('/^([a-z]+):\/\//i', $value)) {
						$value = 'http://' . $value;
					}
					return escape($value, false);

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
						$values[$i] = trim(escape($values[$i], false));
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
						$values[$i] = trim(escape($values[$i], false));
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
						$values[$i] = trim(escape($values[$i], false));
					}
					return $values;

				//case 'date' :
				//case 'email_address' :
				//case 'text' :
				//case 'textarea' :
				default :
					return escape($value, false);
			}
		}

		/**
		 * Returns a value for HTML
		 *
		 * @return string Returns a value expressed in HTML.
		 */
		function getValue() {	
			return $this->_getTypeValue($this->type, $this->value);
		}

		/**
		 * Returns a value for HTML
		 *
		 * @return string Returns a value expressed in HTML.
		 */
		function getValueHTML() {
			$value = $this->_getTypeValue($this->type, $this->value);

			switch($this->type) {
				case 'homepage' :
					return ($value) ? (sprintf('<a href="%s" target="_blank">%s</a>', escape($value, false), strlen($value) > 60 ? substr($value, 0, 40) . '...' . substr($value, -10) : $value)) : "";

				case 'email_address' :
					return ($value) ? sprintf('<a href="mailto:%s">%s</a>', escape($value, false), $value) : "";

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
		function getFormHTML() {
			static $id_num = 1000;

			$type = $this->type;
			$name = $this->name;
			$value = $this->_getTypeValue($this->type, $this->value);
			$default = $this->_getTypeValue($this->type, $this->default);
			$column_name = 'extra_vars' . $this->idx;
			$tmp_id = $column_name . '-' . $id_num++;

			$buff = array();
			switch($type) {
				case 'homepage' :
					$buff[] = '<input type="text" name="' . $column_name . '" value="' . $value . '" class="homepage" />';
					break;
				case 'email_address' :
					$buff[] = '<input type="text" name="' . $column_name . '" value="' . $value . '" class="email_address" />';
					break;
				case 'tel' :
					$buff[] = '<input type="text" name="' . $column_name . '[]" value="' . $value[0] . '" size="4" maxlength="4" class="tel" />';
					$buff[] = '<input type="text" name="' . $column_name . '[]" value="' . $value[1] . '" size="4" maxlength="4" class="tel" />';
					$buff[] = '<input type="text" name="' . $column_name . '[]" value="' . $value[2] . '" size="4" maxlength="4" class="tel" />';
					break;
				case 'textarea' :
					$buff[] = '<textarea name="' . $column_name . '" rows="8" cols="42">' . $value . '</textarea>';
					break;
				case 'checkbox' :   // multiple choice
					$buff[] = '<ul>';
					foreach($default as $v) {
						$checked = '';
						if($value && in_array(trim($v), $value)) {
							$checked = ' checked="checked"';
						}

						// Temporary ID for labeling
						$tmp_id = $column_name . '-' . $id_num++;
						$buff[] ='  <li><input type="checkbox" name="' . $column_name . '[]" id="' . $tmp_id . '" value="' . htmlspecialchars($v, ENT_COMPAT | ENT_HTML401, 'UTF-8', false) . '" ' . $checked . ' /><label for="' . $tmp_id . '">' . $v . '</label></li>';
					}
					$buff[] = '</ul>';
					break;
				case 'select' :  // single choice
					$buff[] = '<select name="' . $column_name . '" class="select">';
					foreach($default as $v) {
						$selected = '';
						if($value && in_array(trim($v), $value)) {
							$selected = ' selected="selected"';
						}
						$buff[] = '  <option value="' . $v . '" ' . $selected . '>' . $v . '</option>';
					}
					$buff[] = '</select>';
					break;
				// radio
				case 'radio' :
					$buff[] = '<ul>';
					foreach($default as $v) {
						$checked = '';
						if($value && in_array(trim($v), $value)) {
							$checked = ' checked="checked"';
						}

						// Temporary ID for labeling
						$tmp_id = $column_name . '-' . $id_num++;
						$buff[] = '<li><input type="radio" name="' . $column_name . '" id="' . $tmp_id . '" ' . $checked . ' value="' . $v . '"  class="radio" /><label for="' . $tmp_id . '">' . $v . '</label></li>';
					}
					$buff[] = '</ul>';
					break;
				// date
				case 'date' :
					// datepicker javascript plugin load
					// Context::loadJavascriptPlugin('ui.datepicker');

					$buff[] = '<input type="hidden" name="' . $column_name . '" value="' . $value . '" />'; 
					$buff[] =	'<input type="text" id="date_' . $column_name . '" value="' . zdate($value, 'Y-m-d') . '" class="date" />';
					$buff[] =	'<input type="button" value="' . Context::getLang('cmd_delete') . '" class="btn" id="dateRemover_' . $column_name . '" />';
					// $buff[] =	'<script type="text/javascript">';
					// $buff[] = '//<![CDATA[';
					// $buff[] =	'(function($){';
					// $buff[] =	'$(function(){';
					// $buff[] =	'  var option = { dateFormat: "yy-mm-dd", changeMonth:true, changeYear:true, gotoCurrent:false, yearRange:\'-100:+10\', onSelect:function(){';
					// $buff[] =	'    $(this).prev(\'input[type="hidden"]\').val(this.value.replace(/-/g,""))}';
					// $buff[] =	'  };';
					// $buff[] =	'  $.extend(option,$.datepicker.regional[\'' . Context::getLangType() . '\']);';
					// $buff[] =	'  $("#date_' . $column_name . '").datepicker(option);';
					// $buff[] =	'  $("#date_' . $column_name . '").datepicker("option", "dateFormat", "yy-mm-dd");';
					// $buff[] =	'  $("#dateRemover_' . $column_name . '").click(function(){';
					// $buff[] =	'    $(this).siblings("input").val("");';
					// $buff[] =	'    return false;';
					// $buff[] =	'  })';
					// $buff[] =	'});';
					// $buff[] =	'})(jQuery);';
					// $buff[] = '//]]>';
					// $buff[] = '</script>';
					break;
				case "kr_zip" :  // address
					if(($oKrzipModel = getModel('krzip')) && method_exists($oKrzipModel , 'getKrzipCodeSearchHtml' )) {
						$buff[] =  $oKrzipModel->getKrzipCodeSearchHtml($column_name, $value);
					}
					break;
				default :
					$buff[] =' <input type="text" name="' . $column_name . '" value="' . ($value ? $value : $default) . '" class="text" />';
			}
			if($this->desc) {
				// $oModuleController = getController('module');
				// $oModuleController->replaceDefinedLangCode($this->desc);
				$buff[] = '<p>' . htmlspecialchars($this->desc, ENT_COMPAT | ENT_HTML401, 'UTF-8', false) . '</p>';
			}
			return join(PHP_EOL, $buff);
		}
	}
}
/* End of file UserDefineFields.class.php */