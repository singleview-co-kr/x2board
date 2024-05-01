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

require_once X2B_PATH . 'includes\classes\user_define_fields\UserDefineFields.class.php';
require_once X2B_PATH . 'includes\classes\user_define_fields\AdminUserDefineFieldsItem.class.php';
require_once X2B_PATH . 'includes\classes\user_define_fields\AdminUnchosenDefaultUserDefineFieldsItem.class.php';
require_once X2B_PATH . 'includes\classes\user_define_fields\AdminExtendedUserDefineFieldsItem.class.php';

if (!class_exists('\\X2board\\Includes\\Classes\\AdminUserDefineFields')) {

	class AdminUserDefineFields extends UserDefineFields {

		/**
		 * sequence of board
		 * @var int
		 */
		private $_a_unchosen_default_fields = array();
		private $_a_user_define_fields = array();

		/**
		 * Current module's Set of UserDefineItemForAdmin
		 * @var UserDefineItemForAdmin[]
		 */
		private $_a_conveted_user_define_fields = array();

		/**
		 * Get instance of AdminUserDefineFields (singleton)
		 *
		 * @return AdminUserDefineFields
		 */
		public static function getInstance() {
			return new AdminUserDefineFields();
		}

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * 관리자가 설정한 입력 필드를 DB에서 가져옴
		 * @return void
		 */
		public function set_user_define_fields_from_db($a_user_define_field) {
			if($a_user_define_field) {
				$this->_a_user_define_fields = $a_user_define_field;
			}
			else {
				$this->_a_user_define_fields = $this->_a_default_fields;
			}
			$this->_set_unchosen_default_fields();
		}
		
		/**
		 * 게시판 관리자의 사용자 정의 필드 목록 화면용 필드 정보 반환
		 * @return void
		 */
		private function _set_unchosen_default_fields() {
			if(empty($this->_a_user_define_fields)) { // all default fields are selected if init case
				return array();
			}
			$a_default_fields = $this->_a_default_fields;
			foreach($a_default_fields as $key=>$value) {
				if($this->_a_user_define_fields) {
					if(isset($this->_a_user_define_fields[$key])){
						unset($a_default_fields[$key]);
					}
				}
			}
			$this->_a_unchosen_default_fields = $a_default_fields;
			unset($a_default_fields);
		}

		/**
		 * Convert and register Kboard formatted user define fields to display on /skins/editor_post.html
		 * 
		 * @return array a_conveted_user_define_fields
		 */
		public function get_user_define_fields() {
			if(!is_array($this->_a_user_define_fields) || count($this->_a_user_define_fields) < 1) {
				return;
			}
			foreach( $this->_a_user_define_fields as $s_meta_key => $a_single_field ) {
				$o_user_define_key = new AdminUserDefineFieldsItem($a_single_field);
				$a_conveted_user_define_fields[] = $o_user_define_key;
			}
			return $a_conveted_user_define_fields;
		}

		/**
		 * 게시판 관리자의 사용자 정의 필드 목록 화면용 필드 정보 반환
		 * @return array
		 */
		public function get_unchosen_default_fields() {
			$a_unchosen_default_fields = $this->_a_default_fields;
			if(empty($this->_a_user_define_fields)) { // all default fields are selected if init case
				return array();
			}
			foreach($a_unchosen_default_fields as $key=>$value) {
				if($this->_a_user_define_fields) {
					if(isset($this->_a_user_define_fields[$key])){
						unset($a_unchosen_default_fields[$key]);
					}
				}
			}
			$a_conveted_user_define_fields = array();
			foreach( $a_unchosen_default_fields as $s_meta_key => $a_single_field ) {
				$o_user_define_key = new AdminUnchosenDefaultUserDefineFieldsItem($a_single_field);
				$a_conveted_user_define_fields[] = $o_user_define_key;
			}
			return $a_conveted_user_define_fields;
		}

		/**
		 * 게시판 관리자의 사용자 정의 필드 목록 화면용 필드 정보 반환
		 * @return array
		 */
		public function get_extended_fields() {
			foreach($this->_a_extends_fields as $s_meta_key => $a_single_field ) {
				$o_user_extended_key = new AdminExtendedUserDefineFieldsItem($a_single_field);
				$a_conveted_user_extended_fields[] = $o_user_extended_key;
			}
			return $a_conveted_user_extended_fields;
		}
	}
}
/* End of file AdminUserDefineFields.class.php */