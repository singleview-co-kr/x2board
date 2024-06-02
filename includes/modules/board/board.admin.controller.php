<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

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

	require_once X2B_PATH . 'includes\classes\user_define_fields\UserDefineListFields.class.php';

	class boardAdminController {

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// var_dump('boardAdminController');
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_'.X2B_DOMAIN) ) {
				unset($o_current_user);
				wp_die(__('You do not have permission.', 'x2board'));
			}
			unset($o_current_user);
		}

		/**
		 * @brief delete board
		 **/
		public function proc_delete_board() {
			check_admin_referer( X2B_CMD_ADMIN_PROC_UPDATE_BOARD );  // check nounce
			if( isset($_POST['delete_board']) ) {
				// delete all post related
				
				// delete x2board mapper info
				global $wpdb;
				$wpdb->delete(
					"{$wpdb->prefix}x2b_mapper",
					array(
						'board_id'   => $_POST['board_id'],
					),
					array('%d')
				);

				// delete WP page
				wp_delete_post(intval($_POST['board_id']));
			}
			wp_redirect(admin_url('admin.php?page=x2b_disp_board_list'));
		}

		/**
		 * @brief update board
		 * https://wpguide.usefulparadigm.com/posts/245
		 **/
		public function proc_update_board() {
			check_admin_referer( X2B_CMD_ADMIN_PROC_UPDATE_BOARD );  // check nounce

			require_once X2B_PATH . 'includes\classes\FileHandler.class.php';
			require_once X2B_PATH . 'includes\admin\tpl\default-settings.php';
			require_once X2B_PATH . 'includes\admin\tpl\register-settings.php';

			// begin - remove unnecessary params
			unset( $_POST['_wpnonce']);
			unset( $_POST['_wp_http_referer']);
			unset( $_POST['action']);
			unset( $_POST['submit']);
			// end - remove unnecessary params
			
			// begin - do not handle category related params
			unset($_POST['update_category_name']);
			unset($_POST['current_category_name']);
			unset($_POST['category_id']);
			unset($_POST['parent_id']);
			unset($_POST['new_category']);
			unset($_POST['tree_category']);
			// end - do not handle category related params

			$_POST = stripslashes_deep($_POST);

			// handle user define [fields] specially
			if( isset($_POST['fields'] ) ) {
				$this->_proc_user_define_fields();
				unset( $_POST['fields']);
			}

			// handle list config [fields] specially
			if( isset($_POST['board_list_fields'] ) ) {
				$a_list_config = $this->_proc_list_fields_config();
				unset( $_POST['board_list_fields']);
				$_POST['board_list_fields'] = $a_list_config;
				unset($a_list_config);
			}

			$n_board_id = intval(sanitize_text_field($_POST['board_id'] ));
			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings( $n_board_id );
			if( $o_rst->b_ok === false ) {
				return false;
			}

			// handle [board_title] specially
			if( $_POST['board_title'] != $o_rst->a_board_settings['board_title'] ) {
				$update = array(
					'data' => array ( 'board_title' => esc_sql(sanitize_text_field($_POST['board_title'] )) ),
					'where' => array ( 'board_id' => esc_sql($n_board_id) ),
				);
				global $wpdb;
				$wpdb->update ( "{$wpdb->prefix}x2b_mapper", $update['data'], $update['where'] );
			}

			// handle [wp_page_title] specially
			if( $_POST['wp_page_title'] != $o_rst->a_board_settings['wp_page_title'] ) {
				$a_update_page = array(
					'ID'         => $n_board_id,
					'post_title' => sanitize_text_field($_POST['wp_page_title'] ),
				);
				wp_update_post( $a_update_page );
				unset( $a_update_page );
			}

			// handle access grant configuration specially
			$a_grant_name = array('board_grant_access', 'board_grant_list', 'board_grant_view', 'board_grant_write_post',
								  'board_grant_write_comment', 'board_grant_consultation_read', 'board_grant_manager');
			foreach( $a_grant_name as $s_grant_name) {
				if($_POST[$s_grant_name] == X2B_CUSTOMIZE) {
					$_POST['board_grant'][$s_grant_name] = $_POST['grant'][$s_grant_name][X2B_CUSTOMIZE];
				}
				unset($_POST['grant'][$s_grant_name]);
			}

			// handle skin vars configuration specially
			$this->_save_skin_vars($o_rst->s_x2b_setting_skin_vars_title);

			// do not save params below
			unset( $_POST['board_id']);
			unset( $_POST['board_title']);
			unset( $_POST['wp_page_title']);
			$s_board_use_rewrite = $_POST['board_use_rewrite'];
			unset( $_POST['board_use_rewrite']);

			// update WP option to x2board_settings_board_[board_id]
			update_option( $o_rst->s_x2b_setting_board_title, $_POST );

			// handle [board_use_rewrite] specially
			$o_post = get_post( $n_board_id );
			$a_board_rewrite_settings = get_option( X2B_REWRITE_OPTION_TITLE );
			if( $s_board_use_rewrite == 'Y' ){
				if( !is_array($a_board_rewrite_settings) ) {
					$a_board_rewrite_settings = array();
				}
				$a_board_rewrite_settings[$n_board_id] = $o_post->post_name;
			}
			else {
				if( is_array($a_board_rewrite_settings) ) {
					unset( $a_board_rewrite_settings[$n_board_id] );
				}
			}
			update_option( X2B_REWRITE_OPTION_TITLE, $a_board_rewrite_settings );
			wp_redirect(admin_url('admin.php?page=x2b_disp_board_update&board_id=' . $n_board_id ));
		}

		private function _proc_list_fields_config() {
			$o_user_define_list_fields = new \X2board\Includes\Classes\UserDefineListFields();
			$a_list_config = array();
			$n_extended_idx = 1;
			foreach( $_POST['board_list_fields'] as $s_eid => $a_list_config_field) {
				$o_field_tmp = new \stdClass();
				$o_field_tmp->eid = $a_list_config_field['eid'];
				$o_field_tmp->var_name = $a_list_config_field['var_name'];
				$o_field_tmp->var_type = $a_list_config_field['var_type'];

				$s_field_type = $o_user_define_list_fields->get_field_type($a_list_config_field['var_type']);
				if($s_field_type == 'default'){
					$o_field_tmp->idx = -1;
				}
				else {
					$o_field_tmp->idx = $n_extended_idx++;
				}
				$a_list_config[$o_field_tmp->eid] = $o_field_tmp;
			}
			unset($o_user_define_list_fields);
			return $a_list_config;
		}

		/**
		 * decide file type
		 *
		 * @param int $s_file_name
		 * @return bool
		 */
		private function _save_skin_vars($s_x2b_setting_skin_vars_title) {
			$n_board_id = intval(sanitize_text_field($_POST['board_id'] ));

			// handle image type skin vars specially
			$n_prefix_len = strlen(X2B_SKIN_VAR_IDENTIFIER);
		
			$a_valid_img_file = array();
			foreach($_FILES as $s_skin_var_id => $a_file_info) {
				// ignore if anormally uploaded file
				if(!is_uploaded_file($a_file_info['tmp_name'])) {
					continue;
				}
				if(substr($s_skin_var_id, 0, $n_prefix_len) != X2B_SKIN_VAR_IDENTIFIER ) {
					continue;
				}
				$s_skin_var_id = str_replace(X2B_SKIN_VAR_IDENTIFIER, '', $s_skin_var_id);
				$a_valid_img_file[$s_skin_var_id] = $a_file_info;
			}

			$s_attach_path = wp_get_upload_dir()['basedir'].DIRECTORY_SEPARATOR.X2B_ADMIN_ATTACH_FILE_PATH;
			$s_attach_url = wp_get_upload_dir()['baseurl'].'/'.X2B_ADMIN_ATTACH_FILE_URL;
			$s_skin_dir = md5($_POST['board_skin']);

			$s_path = $s_attach_path.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$n_board_id.DIRECTORY_SEPARATOR.$s_skin_dir;
			$s_url = $s_attach_url.'/images/'.$n_board_id.'/'.$s_skin_dir;

			$a_skin_var = array();
			$a_old_skin_vars = get_option( $s_x2b_setting_skin_vars_title );
			if( count($a_valid_img_file) ) {  // update file info if requested
				// Get random number generator
				require_once X2B_PATH . 'includes/classes/security/Password.class.php';
				$o_random = new \X2board\Includes\Classes\Security\Password();
				foreach($a_valid_img_file as $s_skin_var_id => $a_file_info) {
					if( !$this->_is_image_file($a_file_info['name']) ) {
						continue;
					}
					
					// special character to '_'
					// change to random file name. because window php bug. window php is not recognize unicode character file name - by cherryfilter
					$ext = substr(strrchr($a_file_info['name'],'.'),1);
					$_filename = $o_random->create_secure_salt(12, 'hex').'.'.$ext;
					$filename  = $s_path.DIRECTORY_SEPARATOR.$_filename;
					$idx = 1;
					while(file_exists($filename)) {
						$filename = $path.preg_replace('/\.([a-z0-9]+)$/i','_'.$idx.'.$1',$_filename);
						$idx++;
					}
					
					if( !file_exists( $s_path ) ) {
						if(!wp_mkdir_p( $s_path ) ){
							wp_die(__('msg_not_permitted_create', 'x2board') );
						}
					}
					
					// Move the file
					if(!@move_uploaded_file($a_file_info['tmp_name'], $filename)) {
						$_filename = $o_random->create_secure_salt(12, 'hex').'.'.$ext;
						$filename  = $s_path.DIRECTORY_SEPARATOR.$_filename;
						if(!@move_uploaded_file($a_file_info['tmp_name'], $filename)) {
							wp_die(__('msg_file_upload_error', 'x2board') );
						}
					}

					$a_skin_var[$s_skin_var_id] = array('abs_path'=>$filename, 'full_url'=>$s_url.'/'.$_filename);
					// delete old file
					wp_delete_file($a_old_skin_vars[$s_skin_var_id]['abs_path']);
				}
				unset($o_random);
			}
			else {  // keep old file info if no change
				foreach($a_old_skin_vars as $s_skin_var_id => $o_val) {
					if(is_array($o_val)) {
						if(isset($o_val['abs_path']) && isset($o_val['full_url'])) {
							$a_skin_var[$s_skin_var_id] = $o_val;
						}
					}
				}
			}
			unset($a_valid_img_file);

			// delete old image skin var, if requested
			if( isset($_POST['delete_old_file'])) {
				$a_files_to_delete = $_POST['delete_old_file'];
				foreach($a_files_to_delete as $s_skin_var_id => $s_file_abs_path) {
					wp_delete_file($s_file_abs_path);
					if(substr($s_skin_var_id, 0, $n_prefix_len) == X2B_SKIN_VAR_IDENTIFIER ) {
						$s_skin_var_id = str_replace(X2B_SKIN_VAR_IDENTIFIER, '', $s_skin_var_id);
						if(isset($a_skin_var[$s_skin_var_id])) {
							unset($a_skin_var[$s_skin_var_id]);
						}
					}
				}
			}
			unset($a_old_skin_vars);

			foreach( $_POST as $s_skin_var_id => $o_value) {
				if(substr($s_skin_var_id, 0, $n_prefix_len) == X2B_SKIN_VAR_IDENTIFIER ) {
					$s_skin_var_id = str_replace(X2B_SKIN_VAR_IDENTIFIER, '', $s_skin_var_id);
					$a_skin_var[$s_skin_var_id] = $o_value;
				}
			}

			// save option to x2board_settings_skin_vars_[board_id]
			update_option( $s_x2b_setting_skin_vars_title, $a_skin_var );

			// remove skin vars configuration from $_POST
			foreach( $_POST as $s_skin_var_id => $o_value) {
				if(substr($s_skin_var_id, 0, $n_prefix_len) == X2B_SKIN_VAR_IDENTIFIER ) {
					unset($_POST[$s_skin_var_id]);
				}
			}
		}

		/**
		 * decide file type
		 *
		 * @param int $s_file_name
		 * @return bool
		 */
		private function _is_image_file($s_file_name) {
			return preg_match("/\.(jpe?g|gif|png|wm[va]|mpe?g|avi|flv|mp[1-4]|as[fx]|wav|midi?|moo?v|qt|r[am]{1,2}|m4v)$/i", $s_file_name);
		}

		/**
		 * @brief proc user define fields 
		 * admin: 'field_name' => db: var_name  관리자 화면에서 [필드 레이블] 입력란은 field_name에 저장함
		 * admin: 'field_type' => db: var_type
		 * admin: 'meta_key' => db: eid
		 * admin: 'default_value' => db: var_default
		 * admin: 'description' => db: var_desc
		 * admin: 'required' => db: var_is_required
		 * 
		 * admin: 'field_label' => db: ??  관리자 화면에서 용도 불명, 사용자 화면에서 기본 필드명 표시위한 용도
		 **/
		private function _proc_user_define_fields() {
			global $wpdb;
			$n_board_id = intval(sanitize_text_field($_POST['board_id'] ));
			// reset board user define keys
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_user_define_keys',  // table name with dynamic prefix
				array('board_id' => $n_board_id),  // which id need to delete
				array('%d'), 							// make sure the id format
			);
			if( $result < 0 || $result === false ){
				wp_die($wpdb->last_error );
			}

			// save field information
			$n_var_seq = 1;
			foreach( $_POST['fields'] as $s_uid => $a_field) {
				// build extra param for json
				$a_tmp_field = $a_field;
				unset($a_tmp_field['field_label']);
				unset($a_tmp_field['field_type']);
				unset($a_tmp_field['field_name']);
				unset($a_tmp_field['meta_key']);
				unset($a_tmp_field['default_value']);
				unset($a_tmp_field['description']);
				unset($a_tmp_field['required']);
				$s_json_param = serialize($a_tmp_field);
				unset($a_tmp_field);

				if( isset($a_field['description']) ) {
					$s_description = strlen($a_field['description']) ? $a_field['description'] : null;
				}
				else {
					$s_description = null;
				}

				if( isset($a_field['meta_key']) ) {
					$s_meta_key = strlen($a_field['meta_key']) ? $a_field['meta_key'] : $s_uid;
				}
				else {
					$s_meta_key = $s_uid;
				}

				if( isset($a_field['search']) ) {
					$s_search = $a_field['search'] == 'Y' ? 'Y' : 'N';
				}
				else {
					$s_search = 'N';
				}
			
				$result = $wpdb->insert(
					"{$wpdb->prefix}x2b_user_define_keys",
					array(
						'board_id'   => $n_board_id,
						'var_idx'   => $n_var_seq++,
						'var_name'   => strlen($a_field['field_name']) ? $a_field['field_name'] : $a_field['field_label'],
						'var_type'  => $a_field['field_type'],
						'eid'  => $s_meta_key, // strlen($a_field['meta_key']) ? $a_field['meta_key'] : $s_uid,  // $a_field['meta_key'],
						'var_default'  => isset($a_field['default_value']) ? $a_field['default_value'] : null,  // $a_field['default_value'],
						'var_desc'  => $s_description,  // $a_field['description'],
						'var_is_required'  => isset($a_field['required']) ? $a_field['required'] : 'N',
						'var_search'  => $s_search,  //$a_field['search'] == 'Y' ? 'Y' : 'N',
						'json_param'  => $s_json_param //serialize($a_tmp_field)
					),
					array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
				);
				if( $result < 0 || $result === false ){
					wp_die($wpdb->last_error );
				}
			}
			// delete user defined field cache
			require_once X2B_PATH . 'includes/classes/cache/CacheHandler.class.php';
			$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance('object', null, true);
			if($o_cache_handler->isSupport()) {
				$object_key = 'module_post_user_define_keys:' . $n_board_id;
				$cache_key = $o_cache_handler->getGroupKey('site_and_module', $object_key);
				$o_cache_handler->delete($cache_key);
			}
			unset($o_cache_handler);
// var_dump($result);
// exit;			
		}		

		/**
		 * @brief insert board
		 * https://wpguide.usefulparadigm.com/posts/245
		 **/
		public function proc_insert_board() {
			check_admin_referer( X2B_CMD_ADMIN_PROC_INSERT_BOARD );  // check nounce
			$_POST = stripslashes_deep($_POST);
var_dump($_POST);			
exit;
			// insert wp page
			$a_x2b_settings = $_POST['x2b_settings'];
			$s_wp_page_title = isset($a_x2b_settings['wp_page_title']) ? esc_sql(sanitize_text_field($a_x2b_settings['wp_page_title'])) : '';

			$o_cur_admin = wp_get_current_user();
			$x2b_page  = array( 'post_title'     => $s_wp_page_title,
								'post_type'      => 'page',
								'post_name'      => $s_wp_page_title,
								'post_content'   => X2B_PAGE_IDENTIFIER, // 'Keep this mark, x2board-installed',
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
			$s_x2board_title = isset($a_x2b_settings[X2B_DOMAIN.'_title']) ? esc_sql(sanitize_text_field($a_x2b_settings[X2B_DOMAIN.'_title'])) : '';
			$this->_insert_new_board($n_page_id, $a_x2b_settings[X2B_DOMAIN.'_title']);
// var_dump($n_page_id);			
			unset($a_x2b_settings);
			
exit();			
			if ( $n_page_id ) {
				wp_redirect(admin_url('admin.php?page='.X2B_CMD_ADMIN_VIEW_BOARD_UPDATE.'&board_id='.$n_page_id));
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
		 * @brief create new category ajax
		 **/
		public function proc_insert_category() {
// error_log(print_r('proc_insert_category', true));
			require_once X2B_PATH . 'includes\modules\category\category.admin.controller.php';
			$o_cat_admin_controller = new \X2board\Includes\Modules\Category\categoryAdminController();
			$_POST = stripslashes_deep($_POST);
			$n_board_id = isset($_POST['board_id'])?$_POST['board_id']:'';
			$new_cat_name = isset($_POST['new_cat_name']) ? sanitize_text_field($_POST['new_cat_name']) : '';
			$n_new_cat_id = $o_cat_admin_controller->create_new_category($n_board_id, $new_cat_name);
			unset($o_cat_admin_controller);
			wp_send_json(array('new_cat_id'=>$n_new_cat_id));
		}

		/**
		 * @brief update name or remove old category ajax
		 **/
		public function proc_manage_category() {
// error_log(print_r('proc_manage_category', true));
			require_once X2B_PATH . 'includes\modules\category\category.admin.controller.php';
			$o_cat_admin_controller = new \X2board\Includes\Modules\Category\categoryAdminController();
			$_POST = stripslashes_deep($_POST);	
			if( !isset($_POST['board_id']) || !isset($_POST['tree_category']) ){
				wp_send_json(array('table_body'=>''));	
			}
			$n_board_id = intval($_POST['board_id']);
			$s_table_body = $o_cat_admin_controller->update_category($n_board_id, $_POST['tree_category']);			
			unset($o_cat_admin_controller);
			wp_send_json(array('table_body'=>$s_table_body));
		}

		/**
		 * @brief reorder whole category ajax
		 **/
		public function proc_reorder_category() {
// error_log(print_r('proc_reorder_category', true));
			require_once X2B_PATH . 'includes\modules\category\category.admin.controller.php';
			$o_cat_admin_controller = new \X2board\Includes\Modules\Category\categoryAdminController();
			$_POST = stripslashes_deep($_POST);
			$a_tree_category = isset($_POST['tree_category_serialize'])?json_decode($_POST['tree_category_serialize']):'';
			$n_board_id = isset($_POST['board_id'])?$_POST['board_id']:'';
			$s_table_body = $o_cat_admin_controller->reorder_category($n_board_id, $a_tree_category);			
			unset($o_cat_admin_controller);
			wp_send_json(array('table_body'=>$s_table_body));
		}
			
		/**
		 * Insert new Board
		 * @return void 
		 */
		private function _insert_new_board( $n_page_id, $s_x2board_title ) {
			global $wpdb;
			$wpdb->insert(
				"{$wpdb->prefix}x2b_mapper",
				array(
					'board_id'   => $n_page_id,
					'wp_page_id'   => $n_page_id,
					'board_title'   => $s_x2board_title,
					'create_date'  => current_time('mysql')
				),
				array('%d', '%d', '%s', '%s')
			);
		}
	}
}
