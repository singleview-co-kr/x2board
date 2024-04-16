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
	
	require_once X2B_PATH . 'includes\classes\UserDefineFields.class.php';

	class postAdminModel {
		private $_n_board_id = null;
		private $_a_default_fields = array();
		private $_a_extends_fields = array();
		private $_a_user_define_fields = array();

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

			// $o_post_extra_vars = new \X2board\Includes\Modules\Post\postExtraVars();
			$o_post_user_define_fields = new \X2board\Includes\Classes\UserDefineFields();
			$this->_a_default_fields = $o_post_user_define_fields->get_default_fields();
			$this->_a_extends_fields = $o_post_user_define_fields->get_extended_fields();
			unset($o_post_user_define_fields);

			$this->_set_user_define_fields();
		}

		/**
		 * 게시판 관리자의 사용자 정의 필드 목록 화면용 필드 정뵤 반환
		 * @return array
		 */
		// getDefaultFields() {
		public function get_default_fields() {
			$a_default_fields = $this->_a_default_fields;
			if(empty($this->_a_user_define_fields)) { // all default fields are selected if init case
				return array();
			}
			foreach($a_default_fields as $key=>$value) {
// var_dump($key);
				if($this->_a_user_define_fields) {
					if(isset($this->_a_user_define_fields[$key])){
						unset($a_default_fields[$key]);
					}
				}
				// else {
				// 	if(!isset($value['kboard_extends'])) {
						;//unset($a_default_fields[$key]);  ??????????????????
				// 	}
				// }
			}
			return $a_default_fields;
		}

		/**
		 * 확장 필드를 반환한다.
		 * @return array
		 */
		public function get_extended_fields() {
			return $this->_a_extends_fields;
		}

		/**
		 * 관리자가 설정한 입력 필드를 반환한다.
		 * @return array
		 */
		// getSkinFields() {
		public function get_user_define_fields() {
			$a_fields = array();
			if($this->_a_user_define_fields) {
				$a_fields = $this->_a_user_define_fields;
			}
			else {
				$a_fields = $this->_a_default_fields;
				// foreach($a_fields as $key=>$value) {
				// 	if(isset($value['x2board_extends'])){
				// 		unset($a_fields[$key]);
				// 	}
				// }
			}
			return $a_fields;
		}

		/**
		 * retrieve user define fields from DB
		 * admin: 'field_name' => db: var_name  관리자 화면에서 [필드 레이블] 입력란은 field_name에 저장함
		 * admin: 'field_type' => db: var_type
		 * admin: 'meta_key' => db: eid
		 * admin: 'default_value' => db: var_default
		 * admin: 'description' => db: var_desc
		 * admin: 'required' => db: var_is_required
		 * 
		 * admin: 'field_label' => db: ??  관리자 화면에서 용도 불명, 사용자 화면에서 기본 필드명 표시위한 용도
		 */
		private function _set_user_define_fields() { //$skin_fields){
			if( !empty($this->_a_user_define_fields ) ){
				return;
			}
// var_dump($_GET['board_id']);
			$this->_n_board_id = intval(sanitize_text_field($_GET['board_id'] ));
			$s_columns = '`var_name`, `var_type`, `var_is_required`, `var_search`, `var_default`, `var_desc`, `eid`, `json_param`';  // , `meta_key`
			global $wpdb;
			$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$this->_n_board_id}' ORDER BY `var_idx` ASC");
// var_dump($a_temp);
			
			foreach( $a_temp as $_ => $o_field ) {
				$a_other_field = unserialize($o_field->json_param);

				$a_single_field['field_type'] = $o_field->var_type;
				// $a_single_field['field_label'] = $o_field->var_name;
				$a_single_field['field_name'] = $o_field->var_name;
				$a_single_field['meta_key'] = $o_field->eid;
				$a_single_field['default_value'] = $o_field->var_default;
				$a_single_field['description'] = $o_field->var_desc;
				$a_single_field['required'] = $o_field->var_is_required;

				$a_single_field = array_merge($a_single_field, $a_other_field);
				$this->_a_user_define_fields[$o_field->eid] = $a_single_field;

				unset($a_single_field);
				unset($a_other_field);
			}
			unset($a_temp);
// var_dump($this->_a_user_define_fields);
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

		/**
		 * 기본 필드인지 확인한다.
		 * @param string $fields_type
		 * @return string
		 */
		// public function isDefaultFields($fields_type) {
		public function is_default_field($fields_type) {
			// $default_fields = apply_filters('kboard_admin_default_fields', $this->default_fields, $this->board);
			if(isset($this->_a_default_fields[$fields_type])) {
				return 'default';
			}
			return 'extends';
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 * @param array $field
		 * @return string
		 */
		// public function getFieldLabel($field){
		public function get_field_label($a_field_info){
			$s_field_type = $a_field_info['field_type'];
			// $fields = apply_filters('kboard_admin_default_fields', $this->default_fields, $this->board);
			if(isset($this->_a_default_fields[$s_field_type])){
				return $this->_a_default_fields[$s_field_type]['field_label'];
			}
			// $fields = apply_filters('kboard_admin_extends_fields', $this->extends_fields, $this->board);
			if(isset($this->_a_extends_fields[$s_field_type])){
				return $this->_a_extends_fields[$s_field_type]['field_label'];
			}
			return $a_field_info['field_label'];
		}

		/**
		 * 저장된 값이 있는지 체크한다.
		 * @param array $row
		 * @return boolean
		 */
		// public function valueExists($row){
		public function is_value_exists($row) {
			foreach($row as $key=>$item) {
				if(isset($item['label']) && $item['label']) {
					return true;
				}
			}
			return false;
		}
	}
}