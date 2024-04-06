<?php
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
// var_dump('boardAdminController');
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
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

			// require_once X2B_PATH . 'includes\admin\tpl\settings-page.php';
			require_once X2B_PATH . 'includes\admin\tpl\default-settings.php';
			require_once X2B_PATH . 'includes\admin\tpl\register-settings.php';

			$_POST = stripslashes_deep($_POST);
// var_dump($_POST);
			$n_board_id = intval(sanitize_text_field($_POST['board_id'] ));
			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings( $n_board_id );

// var_dump($o_rst->s_x2b_setting_title);
			if( $o_rst->b_ok === false ) {
				return false;
			}
			
			// handle [board_title] specially
			if( $_POST['board_title'] != $o_rst->a_board_settings['board_title'] ) {
				$update = array(
					'data' => array ( 'board_title' => esc_sql(sanitize_text_field($_POST['board_title'] )) ),
					'where' => array ( 'board_id' => esc_sql(intval($n_board_id )) ),
				);
				global $wpdb;
				$wpdb->update ( "{$wpdb->prefix}x2b_mapper", $update['data'], $update['where'] );
			}

			// handle [wp_page_title] specially
			if( $_POST['wp_page_title'] != $o_rst->a_board_settings['wp_page_title'] ) {
				$a_update_page = array(
					'ID'         => intval(sanitize_text_field($n_board_id )),
					'post_title' => sanitize_text_field($_POST['wp_page_title'] ),
				);
				wp_update_post( $a_update_page );
				unset( $a_update_page );
			}
			unset( $_POST['_wpnonce']);
			unset( $_POST['_wp_http_referer']);
			unset( $_POST['action']);
			unset( $_POST['board_id']);
			unset( $_POST['board_title']);
			unset( $_POST['wp_page_title']);
			unset( $_POST['submit']);

// var_dump($_POST);
			update_option( $o_rst->s_x2b_setting_title, $_POST );
// exit;
			wp_redirect(admin_url('admin.php?page=x2b_disp_board_update&board_id=' . $n_board_id ));
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
			$s_x2board_title = isset($a_x2b_settings['x2board_title']) ? esc_sql(sanitize_text_field($a_x2b_settings['x2board_title'])) : '';
			$this->_insert_new_board($n_page_id, $a_x2b_settings['x2board_title']);
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
