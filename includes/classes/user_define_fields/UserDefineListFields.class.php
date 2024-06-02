<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * A class to handle extra variables used in posts
 */
namespace X2board\Includes\Classes;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

require_once X2B_PATH . 'includes\classes\user_define_fields\UserDefineFields.class.php';

if (!class_exists('\\X2board\\Includes\\Classes\\UserDefineListFields')) {

	class UserDefineListFields extends UserDefineFields {

		private $_a_available_list_columns = array();
        private $_a_default_list_columns = array( 'no', 'title', 'nick_name','readed_count','regdate_dt');

		/**
		 * Get instance of UserDefineListFields (singleton)
		 *
		 * @return UserDefineListFields
		 */
		public static function getInstance() {
			return new UserDefineListFields();
		}

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->_a_available_list_columns = array( 'no' => __('Intro no field', 'x2board'), 
													  'title' => __('Intro title field', 'x2board'),
													  'regdate_dt' => __('Intro regdate_dt field', 'x2board'),
													  'last_update_dt' => __('Intro last_update_dt field', 'x2board'),
													  'nick_name' => __('Intro nick_name field', 'x2board'),
													  'last_updater' => __('Intro last_updater field', 'x2board'),
													  'readed_count' => __('Intro readed_count field', 'x2board'),
													  'voted_count' => __('Intro voted_count field', 'x2board'),
													  'blamed_count' => __('Intro blamed_count field', 'x2board'),
													  'thumbnail' => __('Intro thumbnail field', 'x2board'),
													  'summary' => __('Intro summary field', 'x2board'),
													  'comment_status' => __('Intro comment_status field', 'x2board')
													);  // 'user_id', 'user_name', 
		}

        public function get_list_config( $a_list_config_param ) {
            // force to build init list config if theres no configuration
// $a_list_config_param = null;
			if(!$a_list_config_param || count($a_list_config_param) <= 0) {
                $a_list_config = array();
                foreach($this->_a_default_list_columns as $s_field_type) {
                    $o_field_tmp = new \stdClass();
                    $o_field_tmp->idx = -1;
                    $o_field_tmp->var_name = null;
                    $o_field_tmp->var_type = $s_field_type;
                    $o_field_tmp->eid = $s_field_type;
                    $a_list_config[$o_field_tmp->eid] = $o_field_tmp;
                }
                return $a_list_config;
			}
            else {
                return $a_list_config_param;
            }
		}

        /**
		 * 필드 유형 확인.
		 * @return string
		 */
		public function get_field_type($s_field_type) {
			if(isset($this->_a_default_fields[$s_field_type])) {
				return 'default';
			}
            if(in_array($s_field_type, $this->_a_default_list_columns)) {
                return 'default';
            }
			return 'extend';
		}

		/**
		 * 
		 * @return array
		 */
		public function get_all_user_define_field_info() {
			return array_merge($this->_a_default_fields, $this->_a_extends_fields);
		}

        /**
		 * 
		 * @return array
		 */
		public function get_virtual_list_field_info() {
			return $this->_a_available_list_columns;
		}
	}
}
/* End of file AdminUserDefineFields.class.php */