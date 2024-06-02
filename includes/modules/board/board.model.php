<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  boardModel
 * @author singleview.co.kr
 * @brief  board module  Model class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardModel')) {

	class boardModel extends board { // module
		/**
		 * @brief initialization
		 **/
		function init()	{}
		
		/**
		 * @brief get the list configuration
		 **/
		// function getListConfig($module_srl)
		public function get_list_config() {
			$o_post_user_define_list_fields = new \X2board\Includes\Classes\UserDefineListFields();
			$o_current_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			$a_list_config = $o_post_user_define_list_fields->get_list_config($o_current_module_info->list_fields);
			unset($o_post_user_define_list_fields);
			unset($o_current_module_info);
			return $a_list_config;
		}
	}
}