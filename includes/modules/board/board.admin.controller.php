<?php
/* Copyright (C) <https://singleview.co.kr> */

/**
 * @class  boardAdminController
 * @author singleview.co.kr
 * @brief  board module admin controller class
 **/
namespace X2board\Includes\Modules\Board;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardAdminController')) {

	class boardAdminController {

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// error_log(print_r('boardAdminController', true));
		}

		/**
		 * @brief insert borad module
		 * https://wpguide.usefulparadigm.com/posts/245
		 **/
		public function proc_insert_board($args = null) {
			check_admin_referer( 'x2b_proc_admin_insert_board' );  // check nounce
			if( !current_user_can('manage_x2board') ) {
				wp_die(__('You do not have permission.', 'x2board'));
			}
			
			$_POST = stripslashes_deep($_POST);
// var_dump($_POST);			
// exit;
			// insert wp page
			$a_x2b_settings = $_POST['x2b_settings'];
			$s_wp_page_title = isset($a_x2b_settings['wp_page_title']) ? esc_sql(sanitize_text_field($a_x2b_settings['wp_page_title'])) : '';

			$o_cur_admin = wp_get_current_user();
			$x2b_page  = array( 'post_title'     => $s_wp_page_title,
								'post_type'      => 'page',
								'post_name'      => $s_wp_page_title,
								'post_content'   => 'This is x2board installed page.',
								'post_status'    => 'publish',  // 'pending'    
								'comment_status' => 'closed',
								'ping_status'    => 'closed',
								'post_author'    => $o_cur_admin->ID,
								'menu_order'     => 0,
								// 'guid'           => site_url() . "/my-page-req1"
							);
			unset($o_cur_admin);
// var_dump($x2b_page);
			$n_page_id = wp_insert_post( $x2b_page, FALSE ); // Get Post ID - FALSE to return 0 instead of wp_error.
			
			// insert x2board
			$s_x2board_title = isset($a_x2b_settings['x2board_title']) ? esc_sql(sanitize_text_field($a_x2b_settings['x2board_title'])) : '';
			$this->_insertNewBoard($n_page_id, $a_x2b_settings['x2board_title']);
// var_dump($n_page_id);			
			unset($a_x2b_settings);
			
exit();			
			if ( $page_id ) {
				wp_redirect(admin_url('admin.php?page=x2b_disp_admin_update_board&board_id='.$n_page_id));
				exit;    
			}    
			
			exit();
			
			// generate module model/controller object
			// $oModuleController = getController('module');
			// $oModuleModel = getModel('module');

			// // setup the board module infortmation
			// $args = Context::getRequestVars();
			// $args->module = 'board';
			// $args->mid = $args->board_name;
			// if(is_array($args->use_status)) $args->use_status = implode('|@|', $args->use_status);
			// unset($args->board_name);

			// // setup extra_order_target
			// $extra_order_target = array();
			// if($args->module_srl)
			// {
			// 	$oDocumentModel = getModel('document');
			// 	$module_extra_vars = $oDocumentModel->getExtraKeys($args->module_srl);
			// 	foreach($module_extra_vars as $oExtraItem)
			// 	{
			// 		$extra_order_target[$oExtraItem->eid] = $oExtraItem->name;
			// 	}
			// }

			// // setup other variables
			// if($args->except_notice != 'Y') $args->except_notice = 'N';
			// if($args->use_anonymous != 'Y') $args->use_anonymous = 'N';
			// if($args->consultation != 'Y') $args->consultation = 'N';
			// if($args->protect_content!= 'Y') $args->protect_content = 'N';
			// if(!in_array($args->order_target,$this->order_target) && !array_key_exists($args->order_target, $extra_order_target)) $args->order_target = 'list_order';
			// if(!in_array($args->order_type, array('asc', 'desc'))) $args->order_type = 'asc';

			// // if there is an existed module
			// if($args->module_srl) {
			// 	$module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
			// 	if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
			// }

			// // insert/update the board module based on module_srl
			// if(!$args->module_srl) {
			// 	$args->hide_category = 'N';
			// 	$output = $oModuleController->insertModule($args);
			// 	$msg_code = 'success_registed';
			// } else {
			// 	$args->hide_category = $module_info->hide_category;
			// 	$output = $oModuleController->updateModule($args);
			// 	$msg_code = 'success_updated';
			// }

			// if(!$output->toBool()) return $output;

			// // setup list config
			// $list = explode(',',Context::get('list'));
			// if(count($list))
			// {
			// 	$list_arr = array();
			// 	foreach($list as $val)
			// 	{
			// 		$val = trim($val);
			// 		if(!$val) continue;
			// 		if(substr($val,0,10)=='extra_vars') $val = substr($val,10);
			// 		$list_arr[] = $val;
			// 	}
			// 	$oModuleController = getController('module');
			// 	$oModuleController->insertModulePartConfig('board', $output->get('module_srl'), $list_arr);
			// }

			// $this->setMessage($msg_code);
			// if (Context::get('success_return_url')){
			// 	changeValueInUrl('mid', $args->mid, $module_info->mid);
			// 	$this->setRedirectUrl(Context::get('success_return_url'));
			// }else{
			// 	$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispBoardAdminBoardInfo', 'module_srl', $output->get('module_srl')));
			// }
		}

		/**
		 * Insert new Board
		 * @return void 
		 */
		private function _insertNewBoard( $n_page_id, $s_x2board_title ) {
			global $wpdb;
			$wpdb->insert(
				"{$wpdb->prefix}x2b_mapper",
				array(
					'board_id'   => $n_page_id,
					'wp_page_id'   => $n_page_id,
					'board_name'   => $s_x2board_title,
					'create_date'  => current_time('mysql')
				),
				array('%d', '%d', '%s', '%s')
			);
		}
		
		/**
		 * Board info update in basic setup page
		 * @return void
		 */
		public function procBoardAdminUpdateBoardFroBasic()
		{
			$args = Context::getRequestVars();

			// for board info
			$args->module = 'board';
			$args->mid = $args->board_name;
			if(is_array($args->use_status))
			{
				$args->use_status = implode('|@|', $args->use_status);
			}
			unset($args->board_name);

			if(!in_array($args->order_target, $this->order_target))
			{
				$args->order_target = 'list_order';
			}
			if(!in_array($args->order_type, array('asc', 'desc')))
			{
				$args->order_type = 'asc';
			}

			$oModuleController = getController('module');
			$output = $oModuleController->updateModule($args);

			// for grant info, Register Admin ID
			$oModuleController->deleteAdminId($args->module_srl);
			if($args->admin_member)
			{
				$admin_members = explode(',',$args->admin_member);
				for($i=0;$i<count($admin_members);$i++)
				{
					$admin_id = trim($admin_members[$i]);
					if(!$admin_id) continue;
					$oModuleController->insertAdminId($args->module_srl, $admin_id);
				}
			}
		}

		/**
		 * @brief delete the board module
		 **/
		public function procBoardAdminDeleteBoard() {
			$module_srl = Context::get('module_srl');

			// get the current module
			$oModuleController = getController('module');
			$output = $oModuleController->deleteModule($module_srl);
			if(!$output->toBool()) return $output;

			$this->add('module','board');
			$this->add('page',Context::get('page'));
			$this->setMessage('success_deleted');
		}

		public function procBoardAdminSaveCategorySettings()
		{
			$module_srl = Context::get('module_srl');
			$mid = Context::get('mid');

			$oModuleModel = getModel('module');
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if($module_info->mid != $mid)
			{
				return new BaseObject(-1, 'msg_invalid_request');
			}

			$module_info->hide_category = Context::get('hide_category') == 'Y' ? 'Y' : 'N';
			$oModuleController = getController('module'); /* @var $oModuleController moduleController */
			$output = $oModuleController->updateModule($module_info);
			if(!$output->toBool())
			{
				return $output;
			}

			$this->setMessage('success_updated');
			if (Context::get('success_return_url'))
			{
				$this->setRedirectUrl(Context::get('success_return_url'));
			}
			else
			{
				$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispBoardAdminCategoryInfo', 'module_srl', $output->get('module_srl')));
			}
		}
	}
}
