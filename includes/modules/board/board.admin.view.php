<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  boardAdminView
 * @author singleview.co.kr
 * @brief  board module admin view class
 **/
namespace X2board\Includes\Modules\Board;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardAdminView')) {

	class boardAdminView {

		public function __construct(){
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
				unset($o_current_user);
				wp_die(__('msg_no_permission', X2B_DOMAIN));
			}
			unset($o_current_user);
		}
		
		/**
		 * @brief initialization
		 *
		 * board module can be divided into general use and admin use.\n
		 **/
		// function init() {
		// 	// check module_srl is existed or not
		// 	$module_srl = Context::get('module_srl');
		// 	if(!$module_srl && $this->module_srl) {
		// 		$module_srl = $this->module_srl;
		// 		Context::set('module_srl', $module_srl);
		// 	}

		// 	// generate module model object
		// 	$oModuleModel = getModel('module');

		// 	// get the module infomation based on the module_srl
		// 	if($module_srl) {
		// 		$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
		// 		if(!$module_info) {
		// 			Context::set('module_srl','');
		// 			$this->act = 'list';
		// 		} else {
		// 			ModuleModel::syncModuleToSite($module_info);
		// 			$this->module_info = $module_info;
		// 			$this->module_info->use_status = explode('|@|', $module_info->use_status);
		// 			Context::set('module_info',$module_info);
		// 		}
		// 	}

		// 	if($module_info && $module_info->module != 'board') return $this->stop("msg_invalid_request");

		// 	// get the module category list
		// 	$module_category = $oModuleModel->getModuleCategories();
		// 	Context::set('module_category', $module_category);

		// 	$security = new Security();
		// 	$security->encodeHTML('module_info.');
		// 	$security->encodeHTML('module_category..');

		// 	// setup template path (board admin panel templates is resided in the tpl folder)
		// 	$template_path = sprintf("%stpl/",$this->module_path);
		// 	$this->setTemplatePath($template_path);

		// 	// install order (sorting) options
		// 	foreach($this->order_target as $key) $order_target[$key] = Context::getLang($key);
		// 	$order_target['list_order'] = Context::getLang('document_srl');
		// 	$order_target['update_order'] = Context::getLang('last_update');
		// 	Context::set('order_target', $order_target);
		// }

		/**
		 * @brief display the x2board dashboard
		 **/
		public function disp_idx() {
			require_once X2B_PATH . 'includes\classes\FileHandler.class.php';
			require_once X2B_PATH . 'includes\admin\tpl\default-settings.php';
			require_once X2B_PATH . 'includes\admin\tpl\register-settings.php';

			require_once X2B_PATH . 'includes\modules\board\board.admin.model.php';
			$o_board_admin_model = new \X2board\Includes\Modules\Board\boardAdminModel();

			$a_latest_posts = $o_board_admin_model->get_latest_posts();
			$a_latest_comments = $o_board_admin_model->get_latest_comments();

			$a_latest_files = $o_board_admin_model->get_latest_files();
			unset($o_board_admin_model);

			require_once X2B_PATH . 'includes/admin/tpl/dashboard.php';
		}

		/**
		 * Display the latest posts UX
		 **/
		public function disp_latest_post() {
			require_once X2B_PATH . 'includes\modules\board\board.admin.model.php';
			$o_board_admin_model = new \X2board\Includes\Modules\Board\boardAdminModel();
			$o_latest = $o_board_admin_model->get_latest_posts_wp_list();
			require_once X2B_PATH . 'includes/admin/tpl/latest_list.php';
			unset($o_latest);
			unset($o_board_admin_model);
		}

		/**
		 * Display the latest posts UX
		 **/
		public function disp_latest_comment() {
			require_once X2B_PATH . 'includes\modules\board\board.admin.model.php';
			$o_board_admin_model = new \X2board\Includes\Modules\Board\boardAdminModel();
			$o_latest = $o_board_admin_model->get_latest_comments_wp_list();
			require_once X2B_PATH . 'includes/admin/tpl/latest_list.php';
			unset($o_latest);
			unset($o_board_admin_model);
		}

		/**
		 * Display the latest posts UX
		 **/
		public function disp_latest_file() {
			require_once X2B_PATH . 'includes\modules\board\board.admin.model.php';
			$o_board_admin_model = new \X2board\Includes\Modules\Board\boardAdminModel();
			$o_latest = $o_board_admin_model->get_latest_files_wp_list();
			require_once X2B_PATH . 'includes/admin/tpl/latest_list.php';
			unset($o_latest);
			unset($o_board_admin_model);
		}

		public function disp_control_panel() {
			require_once X2B_PATH . 'includes/admin/tpl/control_panel.php';
		}

		/**
		 * Display the board import UX
		 **/
		public function disp_board_import() {
			wp_register_script(
				X2B_DOMAIN . '-tab-scripts',
				X2B_URL . 'includes/admin/js/x2board-setting-script.js',
				array( 'jquery' ),
				X2B_VERSION,
				true
			);
			wp_enqueue_script( X2B_DOMAIN . '-tab-scripts' );

			require_once X2B_PATH . 'includes\modules\import\import.admin.model.php';
			$o_import_admin = new \X2board\Includes\Modules\Import\importAdminModel();
			$n_cur_auto_increment = $o_import_admin->get_x2b_sequence();
			unset($o_import_admin);

			require_once X2B_PATH . 'includes\modules\board\wp_admin_class\wp_board_list.php';
			$o_board_list = new \X2board\Includes\Modules\Board\WpAdminClass\wpBoardList();
			require_once X2B_PATH . 'includes/admin/tpl/board_import.php';
			unset($o_board_list);
		}

		/**
		 * @brief display the board module admin contents
		 **/
		public function disp_board_list() {
			require_once X2B_PATH . 'includes\modules\board\wp_admin_class\wp_board_list.php';
			$o_board_list = new \X2board\Includes\Modules\Board\WpAdminClass\wpBoardList();
			$s_create_board_url = esc_url( admin_url( "admin.php?page=".X2B_CMD_ADMIN_VIEW_BOARD_INSERT ) );
			include_once X2B_PATH .'includes/admin/tpl/board_list.php';
			unset($o_board_list);
		}

		/**
		 * 서버에 설정된 최대 업로드 크기를 반환한다.
		 * @link http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
		 * @return int
		 */
		private function _get_upload_max_size(){
			static $max_size = -1;
			if($max_size < 0){
				$max_size = $this->_parse_size(ini_get('post_max_size'));
				$upload_max = $this->_parse_size(ini_get('upload_max_filesize'));
				if($upload_max > 0 && $upload_max < $max_size){
					$max_size = $upload_max;
				}
			}
			return $max_size;
		}

		/**
		 * 바이트로 크기를 변환한다.
		 * @link http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
		 * @return int
		 */
		private function _parse_size($size){
			$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
			$size = preg_replace('/[^0-9\.]/', '', $size);
			if($unit){
				return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
			}
			else{
				return round($size);
			}
		}

		/**
		 * 업로드 가능한 파일 크기를 반환한다.
		 */
		private function _get_uploading_file_size(){
			return intval(get_option('x2b_limit_file_size', $this->_get_upload_max_size()));
		}

		/**
		 * 새글 알림 시간을 반환한다.
		 * @return int
		 */
		private function _new_post_notify_time(){
			return get_option('x2b_new_post_notify_time', '86400');
		}

		/**
		 * 아이프레임 화이트리스트를 반환한다.
		 * @param boolean $to_array
		 */
		private function _get_iframe_whitelist($to_array=false){
			/*
			* 허가된 도메인 호스트 (화이트리스트)
			*/
			$whitelist = 'google.com' . PHP_EOL;
			$whitelist .= 'www.google.com' . PHP_EOL;
			$whitelist .= 'youtube.com' . PHP_EOL;
			$whitelist .= 'www.youtube.com' . PHP_EOL;
			$whitelist .= 'maps.google.com' . PHP_EOL;
			$whitelist .= 'maps.google.co.kr' . PHP_EOL;
			$whitelist .= 'docs.google.com' . PHP_EOL;
			$whitelist .= 'tv.naver.com' . PHP_EOL;
			$whitelist .= 'serviceapi.nmv.naver.com' . PHP_EOL;
			$whitelist .= 'serviceapi.rmcnmv.naver.com' . PHP_EOL;
			$whitelist .= 'videofarm.daum.net' . PHP_EOL;
			$whitelist .= 'tv.kakao.com' . PHP_EOL;
			$whitelist .= 'player.vimeo.com' . PHP_EOL;
			$whitelist .= 'w.soundcloud.com' . PHP_EOL;
			$whitelist .= 'slideshare.net' . PHP_EOL;
			$whitelist .= 'www.slideshare.net' . PHP_EOL;
			$whitelist .= 'channel.pandora.tv' . PHP_EOL;
			$whitelist .= 'mgoon.com' . PHP_EOL;
			$whitelist .= 'www.mgoon.com' . PHP_EOL;
			$whitelist .= 'tudou.com' . PHP_EOL;
			$whitelist .= 'www.tudou.com' . PHP_EOL;
			$whitelist .= 'player.youku.com' . PHP_EOL;
			$whitelist .= 'videomega.tv' . PHP_EOL;
			$whitelist .= 'mtab.clickmon.co.kr' . PHP_EOL;
			$whitelist .= 'tab2.clickmon.co.kr';
			
			$iframe_whitelist_data = get_option(X2B_IFRAME_WHITELIST);
			$iframe_whitelist_data = trim($iframe_whitelist_data);
			
			if(!$iframe_whitelist_data){
				$iframe_whitelist_data = $whitelist;
			}
			
			if($to_array){
				$iframe_whitelist_data = explode(PHP_EOL, $iframe_whitelist_data);
				return array_map('trim', $iframe_whitelist_data);
			}
			return $iframe_whitelist_data;
		}

		/**
		 * 작성자 금지단어를 반환한다.
		 * @param string $to_array
		 */
		private function _get_forbidden_nickname($to_array=false){
			$name_filter = get_option('x2b_name_filter', '관리자, 운영자, admin, administrator');
			
			if($to_array){
				$name_filter = explode(',', $name_filter);
				return array_map('trim', $name_filter);
			}
			return $name_filter;
		}

		/**
		 * 본문/제목/댓글 금지단어를 반환한다.
		 * @param string $to_array
		 */
		private function _get_forbidden_word_in_contents($to_array=false){
			$content_filter = get_option('x2b_content_filter', '');
			if($to_array){
				$content_filter = explode(',', $content_filter);
				return array_map('trim', $content_filter);
			}
			return $content_filter;
		}

		/**
		 * @brief display the selected board configuration
		 **/
		public function disp_board_update() {
			$this->disp_board_insert();
		}

		/**
		 * @brief display the board insert form
		 **/
		public function disp_board_insert() {
			require_once X2B_PATH . 'includes\classes\FileHandler.class.php';
			require_once X2B_PATH . 'includes\admin\tpl\settings-page.php';
			require_once X2B_PATH . 'includes\admin\tpl\default-settings.php';
			require_once X2B_PATH . 'includes\admin\tpl\register-settings.php';
			require_once X2B_PATH . 'includes\modules\board\board.admin.model.php';
			require_once X2B_PATH . 'includes\modules\category\category.admin.model.php';
			require_once X2B_PATH . 'includes\modules\post\post.admin.model.php';

			wp_register_script(
				X2B_DOMAIN . '-tab-scripts',
				X2B_URL . 'includes/admin/js/x2board-config.js',
				array( 'jquery', 'jquery-ui-tabs' ),
				X2B_VERSION,
				true
			);
			// begin - for admin sortable UI
			wp_register_script(
				X2B_DOMAIN . '-sortable-scripts',
				X2B_URL . 'includes/admin/js/x2board-category-sortable.js', 
				array(),
				X2B_VERSION,
				true
			);
			wp_register_script(
				X2B_DOMAIN . '-nested-sortable', 
				X2B_URL . 'includes/admin/js/jquery.mjs.nestedSortable.js', 
				array('jquery', 'jquery-ui-sortable'), 
				'2.1a'
			);
			// end - for admin sortable UI
		
			// begin - for admin user define field UI
			wp_register_script(
				X2B_DOMAIN . '-user-field-scripts',
				X2B_URL . 'includes/admin/js/x2board-user-field.js', 
				array(),
				X2B_VERSION,
				true
			);
			// end - for admin user define field UI
		
			// begin - for admin list config field UI
			wp_register_script(
				X2B_DOMAIN . '-list-config-field-scripts',
				X2B_URL . 'includes/admin/js/x2board-list-field.js', 
				array(),
				X2B_VERSION,
				true
			);
			// end - for admin list config field UI

			wp_enqueue_script( X2B_DOMAIN . '-tab-scripts' );
			wp_enqueue_script( X2B_DOMAIN . '-sortable-scripts' );
			wp_enqueue_script( X2B_DOMAIN . '-nested-sortable' );
			wp_enqueue_script( X2B_DOMAIN . '-user-field-scripts' );
			wp_enqueue_script( X2B_DOMAIN . '-list-config-field-scripts' );
	
			\X2board\Includes\Admin\Tpl\x2b_register_settings();
			\X2board\Includes\Admin\Tpl\x2b_options_page();
		}

		/**
		 * @brief display the board mdoule delete page
		 **/
		public function dispBoardAdminDeleteBoard() {
			// if(!Context::get('module_srl')) return $this->dispBoardAdminContent();
			// if(!in_array($this->module_info->module, array('admin', 'board','blog','guestbook'))) {
			// 	return $this->alertMessage('msg_invalid_request');
			// }

			// $module_info = Context::get('module_info');

			// $oDocumentModel = getModel('document');
			// $document_count = $oDocumentModel->getDocumentCount($module_info->module_srl);
			// $module_info->document_count = $document_count;

			// Context::set('module_info',$module_info);

			// $security = new Security();
			// $security->encodeHTML('module_info..mid','module_info..module','module_info..document_count');

			// // setup the template file
			// $this->setTemplateFile('board_delete');
		}

		/**
		 * @brief board module message
		 **/
		// function alertMessage($message) {
		// 	$script =  sprintf('<script> xAddEventListener(window,"load", function() { alert("%s"); } );</script>', Context::getLang($message));
		// 	Context::addHtmlHeader( $script );
		// }
	} // END CLASS
} 