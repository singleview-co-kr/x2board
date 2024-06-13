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

	class boardAdminView extends \WP_List_Table {
		private $_n_list_per_page = 20;
		public $items = null;  // list to display by WP_List_Table

		public function __construct(){
			parent::__construct();
// var_dump('boardAdminView');
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
		 * @brief display the board module admin contents
		 **/
		public function disp_idx() {
			require_once X2B_PATH . 'includes/admin/tpl/index.php';
		}

		/**
		 * Display the board import UX
		 **/
		public function disp_board_import() {
			$this->prepare_items();

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

			require_once X2B_PATH . 'includes/admin/tpl/board_import.php';
		}

		/**
		 * @brief display the board module admin contents
		 **/
		public function disp_board_list() {
			// https://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
			// https://supporthost.com/wp-list-table-tutorial/
			$this->prepare_items();
			$post_new_file = esc_url( admin_url( "admin.php?page=x2b_disp_board_insert" ) );
			include_once X2B_PATH .'includes/admin/tpl/board_list.php';
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


		public function prepare_items(){
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			$keyword = isset($_GET['s'])?esc_attr($_GET['s']):'';
			
			$cur_page = $this->get_pagenum();
			global $wpdb;
			if($keyword){
				$keyword = esc_sql($keyword);
				$where = "`board_name` LIKE '%{$keyword}%'";
			}
			else{
				$where = '1=1';
			}
			$n_total = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE {$where}");
			$this->items = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}x2b_mapper` WHERE {$where} ORDER BY `board_id` DESC LIMIT " . ($cur_page-1)*$this->_n_list_per_page . ",{$this->_n_list_per_page}");
			
			$this->set_pagination_args(array('total_items'=>$n_total, 'per_page'=>$this->_n_list_per_page));
		}

		/**
		 * @brief 
		 **/
		public function get_columns(){
			return array(
					'cb' => '<input type="checkbox">',
					// 'thumbnail' => __('썸네일', X2B_DOMAIN),
					'wp_page_id' => __('lbl_installed_wp_page', X2B_DOMAIN),
					'board_name' => __('name_x2board_title', X2B_DOMAIN),
					// 'skin' => __('스킨', X2B_DOMAIN),
					// 'permission_read' => __('읽기권한', X2B_DOMAIN),
					// 'permission_write' => __('쓰기권한', X2B_DOMAIN),
					// 'permission_comments_write' => __('댓글쓰기권한', X2B_DOMAIN),
					'create_date' => __('lbl_create_date', X2B_DOMAIN),
					// 'created' => __('생성일', X2B_DOMAIN),
			);
		}

		protected function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'wp_page_id':
					$o_post = get_post(intval($item->wp_page_id)); 
					return '<A HREF='.$o_post->guid.' target="_blank">'.__('lbl_visit_page', X2B_DOMAIN).' - '.$o_post->post_title.'</A>';
				case 'board_name':
					$o_post = get_post(intval($item->wp_page_id)); 
					return '<A HREF='.admin_url( 'admin.php?page='.X2B_CMD_ADMIN_VIEW_BOARD_UPDATE.'&board_id='.$o_post->ID ).'>'.__('lbl_configure_board', X2B_DOMAIN).' - '.$item->board_title.'</A>';
				case 'create_date':
					return $item->$column_name;
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
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
		 * @brief display the additional setup panel
		 * additonal setup panel is for connecting the service modules with other modules
		 **/
		public function dispBoardAdminBoardAdditionSetup() {
			// sice content is obtained from other modules via call by reference, declare it first
			// $content = '';

			// // get the addtional setup trigger
			// // the additional setup triggers can be used in many modules
			// $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'before', $content);
			// $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'after', $content);
			// Context::set('setup_content', $content);

			// // setup the template file
			// $this->setTemplateFile('addition_setup');
			require_once X2B_PATH . 'include/modules/board/tpl/setting-page.php';
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
		 * @brief display category information
		 **/
		public function dispBoardAdminCategoryInfo() {
			// $oDocumentModel = getModel('document');
			// $category_content = $oDocumentModel->getCategoryHTML($this->module_info->module_srl);
			// Context::set('category_content', $category_content);

			// Context::set('module_info', $this->module_info);
			// $this->setTemplateFile('category_list');
		}

		/**
		 * @brief display the grant information
		 **/
		public function dispBoardAdminGrantInfo() {
			// get the grant infotmation from admin module
			// $oModuleAdminModel = getAdminModel('module');
			// $grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
			// Context::set('grant_content', $grant_content);

			// $this->setTemplateFile('grant_list');
		}

		/**
		 * @brief display extra variables
		 **/
		public function dispBoardAdminExtraVars() {
			// $oDocumentModel = getModel('document');
			// $extra_vars_content = $oDocumentModel->getExtraVarsHTML($this->module_info->module_srl);
			// Context::set('extra_vars_content', $extra_vars_content);

			// $this->setTemplateFile('extra_vars');
		}

		/**
		 * @brief display the module skin information
		 **/
		public function dispBoardAdminSkinInfo() {
			// get the grant infotmation from admin module
			// $oModuleAdminModel = getAdminModel('module');
			// $skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);
			// Context::set('skin_content', $skin_content);

			// $this->setTemplateFile('skin_info');
		}

		/**
		 * Display the module mobile skin information
		 **/
		public function dispBoardAdminMobileSkinInfo() {
			// get the grant infotmation from admin module
			// $oModuleAdminModel = getAdminModel('module');
			// $skin_content = $oModuleAdminModel->getModuleMobileSkinHTML($this->module_info->module_srl);
			// Context::set('skin_content', $skin_content);

			// $this->setTemplateFile('skin_info');
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