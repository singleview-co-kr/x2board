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
		/*public function get_list_config() {
			$o_post_user_define_fields = new \X2board\Includes\Classes\UserDefineListFields();
			$o_current_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			$a_list_config = $o_post_user_define_fields->get_list_config($o_current_module_info->list_fields);
			unset($o_post_user_define_fields);
			unset($o_current_module_info);
			return $a_list_config;
		}*/
		public function get_list_config() {
			$a_list_config = array() ;
			
			// add seq number into list config
			$o_field_no = new \stdClass();
			$o_field_no->idx = -1;
			$o_field_no->eid = 'no';
			$o_field_no->var_type = 'no';
			$o_field_no->var_name = 'no';
			$a_list_config['no'] = $o_field_no;

			// get the user define keys
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$o_post_model = \X2board\Includes\getModel('post');
			$inserted_extra_vars = $o_post_model->get_user_define_keys($n_board_id);
			unset($o_post_model);
			$o_post_user_define_fields = new \X2board\Includes\Classes\GuestUserDefineFields();

			$a_ignore_field = array('option', 'content', 'attach', 'category' );
			// extended user define field idx must be synced with \includes\modules\post\post.item.php::get_user_define_extended_fields()
			$n_extended_idx = 1;
			foreach($inserted_extra_vars as $key) {
				if(!in_array($key->type, $a_ignore_field)) {
					$o_field_tmp = new \stdClass();
					$o_field_tmp->eid = $key->eid;
					$o_field_tmp->var_name = $key->name;
					$o_field_tmp->var_type = $key->type;

					$s_field_type = $o_post_user_define_fields->get_field_type($key->type);
					if($s_field_type == 'default'){
						$o_field_tmp->idx = -1;
					}
					else {
						$o_field_tmp->idx = $n_extended_idx++;
					}
					$a_list_config[$key->type] = $o_field_tmp;
				}
			}
			unset($inserted_extra_vars);
			unset($o_post_user_define_fields);

			// add nick_name into list config
			$o_field_nick_name = new \stdClass();
			$o_field_nick_name->idx = -1;
			$o_field_nick_name->eid = 'nick_name';
			$o_field_nick_name->var_type = 'nick_name';
			$o_field_nick_name->var_name = 'nick_name';
			$a_list_config['nick_name'] = $o_field_nick_name;

			// add readed_count into list config
			$o_field_readed_count = new \stdClass();
			$o_field_readed_count->idx = -1;
			$o_field_readed_count->eid = 'readed_count';
			$o_field_readed_count->var_type = 'readed_count';
			$o_field_readed_count->var_name = 'readed_count';
			$a_list_config['readed_count'] = $o_field_readed_count;

			// add regdate_dt into list config
			$o_field_regdate_dt = new \stdClass();
			$o_field_regdate_dt->idx = -1;
			$o_field_regdate_dt->eid = 'regdate_dt';
			$o_field_regdate_dt->var_type = 'regdate_dt';
			$o_field_regdate_dt->var_name = 'regdate_dt';
			$a_list_config['regdate_dt'] = $o_field_regdate_dt;
			return $a_list_config;
		}
	}
}