<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * A class to handle extra variables used in posts
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once X2B_PATH . 'includes\classes\user_define_fields\UserDefineFields.class.php';

if ( ! class_exists( '\\X2board\\Includes\\Classes\\UserDefineListFields' ) ) {

	class UserDefineListFields extends UserDefineFields {

		private $_a_available_list_columns = array();
		private $_a_default_list_columns   = array( 'no', 'title', 'nick_name', 'readed_count', 'regdate_dt' );

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
			$this->_a_available_list_columns = array(
				'no'             => __( 'desc_user_define_field_no', X2B_DOMAIN ),
				'title'          => __( 'desc_user_define_field_title', X2B_DOMAIN ),
				'regdate_dt'     => __( 'desc_user_define_field_regdate_dt', X2B_DOMAIN ),
				'last_update_dt' => __( 'desc_user_define_field_last_update_dt', X2B_DOMAIN ),
				'nick_name'      => __( 'desc_user_define_field_nick_name', X2B_DOMAIN ),
				'last_updater'   => __( 'desc_user_define_field_last_updater', X2B_DOMAIN ),
				'readed_count'   => __( 'desc_user_define_field_readed_count', X2B_DOMAIN ),
				'voted_count'    => __( 'desc_user_define_field_voted_count', X2B_DOMAIN ),
				'blamed_count'   => __( 'desc_user_define_field_blamed_count', X2B_DOMAIN ),
				'thumbnail'      => __( 'desc_user_define_field_thumbnail', X2B_DOMAIN ),
				'summary'        => __( 'desc_user_define_field_summary', X2B_DOMAIN ),
				'comment_status' => __( 'desc_user_define_field_comment_status', X2B_DOMAIN ),
			);  // 'user_id', 'user_name',
		}

		/**
		 *
		 * @return
		 */
		public function get_list_config( $a_list_config_param ) {
			// force to build init list config if theres no configuration
			if ( ! $a_list_config_param || count( $a_list_config_param ) <= 0 ) {
				$a_list_config = array();
				foreach ( $this->_a_default_list_columns as $s_field_type ) {
					$o_field_tmp                        = new \stdClass();
					$o_field_tmp->idx                   = -1;
					$o_field_tmp->var_name              = null;
					$o_field_tmp->var_type              = $s_field_type;
					$o_field_tmp->eid                   = $s_field_type;
					$a_list_config[ $o_field_tmp->eid ] = $o_field_tmp;
				}
				return $a_list_config;
			} else {
				return $a_list_config_param;
			}
		}

		/**
		 * 필드 유형 확인.
		 *
		 * @return string
		 */
		public function get_field_type( $s_field_type ) {
			if ( isset( $this->_a_default_fields[ $s_field_type ] ) ) {
				return 'default';
			}
			if ( in_array( $s_field_type, $this->_a_default_list_columns ) ) {
				return 'default';
			}
			return 'extend';
		}

		/**
		 *
		 * @return array
		 */
		public function get_all_user_define_field_info() {
			return array_merge( $this->_a_default_fields, $this->_a_extends_fields );
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
