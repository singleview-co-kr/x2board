<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  boardView
 * @author XEHub (developers@xpressengine.com)
 * @brief  board module View class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardView')) {

	class boardView extends board {
		var $listConfig;
		var $columnList;

		private $_default_fields = array();
		private $_extends_fields = array();

		/**
		 * @brief initialization
		 * board module can be used in guest mode
		 **/
		public function init() {
			/**
			 * setup the module general information
			 **/
			if($this->module_info->list_count) {
				$this->list_count = $this->module_info->list_count;
			}
			if($this->module_info->search_list_count) {
				$this->search_list_count = $this->module_info->search_list_count;
			}
			if($this->module_info->page_count) {
				$this->page_count = $this->module_info->page_count;
			}

			$this->except_notice = $this->module_info->except_notice == 'N' ? FALSE : TRUE;
			
			/**
			 * check the consultation function, if the user is admin then swich off consultation function
			 * if the user is not logged, then disppear write post/write comment./ view post
			 **/
			if($this->module_info->consultation == 'Y' && !$this->grant->manager && !$this->grant->consultation_read) {
				$this->consultation = TRUE;
				if(!\X2board\Includes\Classes\Context::get('is_logged')) {
					$this->grant->list = FALSE;
					$this->grant->write_post = FALSE;
					$this->grant->write_comment = FALSE;
					$this->grant->view = FALSE;
				}
			}
			else {
				$this->consultation = FALSE;
			}

			$a_status = $this->_get_status_name_list();
			if(isset($a_status['SECRET'])) {
				$this->module_info->secret = 'Y';  // for notify_message checkbox on post/comment editor
			}
			unset($a_status);

			// editor 스킨의 사용자 입력 field 출력
			$o_post_model = \X2board\Includes\get_model('post');
			$this->_default_fields = $o_post_model->get_default_fields();
			$this->_extends_fields = $o_post_model->get_extended_fields();
			$a_user_input_field = $o_post_model->get_user_define_fields();
			\X2board\Includes\Classes\Context::set('field', $a_user_input_field);
			
			$n_count_category = $o_post_model->get_category_count();
			$b_category_activated = false;
			$b_comment_activated = false;
			foreach($a_user_input_field as $_ => $o_user_field ) {
				if($o_user_field->type == 'category' && $n_count_category) {
					$b_category_activated = true;
				}
				if($o_user_field->type == 'attach') {
					$b_comment_activated = true;
				}
			}

			unset($a_user_input_field);
			\X2board\Includes\Classes\Context::set('use_category', $b_category_activated ? 'Y': 'N');
			// set for comment attach feature
			\X2board\Includes\Classes\Context::set('use_comment_attach', $b_comment_activated);

			/**
			 * use context::set to setup extra variables
			 **/
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$a_extra_keys = $o_post_model->get_user_define_keys($n_board_id);
			\X2board\Includes\Classes\Context::set('extra_keys', $a_extra_keys);
			unset($o_post_model);

			/**
			 * add extra variables to order(sorting) target
			 **/
			$a_ignore_key = array('attach', 'category', 'search');
			if( is_array($a_extra_keys) ) {
				foreach($a_extra_keys as $val) {
					if( !in_array( $val->eid, $a_ignore_key) ){
						$this->a_order_target[] = $val->eid;
					}
				}
			}
			unset($a_ignore_key);

			// remove [post_id]_cpage from get_vars
			$o_args = \X2board\Includes\Classes\Context::get_request_vars();
			foreach($o_args as $name => $value) {
				if(preg_match('/[0-9]+_cpage/', $name)) {
					Context::set($name, '', TRUE);
					Context::set($name, $value);
				}
			}
			unset($o_args);

			/**
			 * setup the template path based on the skin
			 * the default skin is default
			 **/
			$s_template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			if(!is_dir($s_template_path)) {
				$this->module_info->skin = 'sketchbook5';  // default
				$s_template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			}
			$this->set_skin_path($s_template_path);
			\X2board\Includes\Classes\Context::set('skin_path_abs', $this->skin_path);
			\X2board\Includes\Classes\Context::set('skin_url', X2B_URL.'includes/modules/board/skins/'.$this->module_info->skin);

			// load textdomain for skin_vars
			// third parameter should be relative path to WP_PLUGIN_DIR
			load_plugin_textdomain(X2B_DOMAIN, false, X2B_DOMAIN . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'board' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . $this->module_info->skin . DIRECTORY_SEPARATOR . 'lang');

			// Avoid warning - Undefined variable: sort_index
			if(!\X2board\Includes\Classes\Context::get('sort_index')) {
				\X2board\Includes\Classes\Context::set('sort_index', null);
			}
			
			// remember!! current_module_info has been already set
			// \X2board\Includes\Classes\Context::set('module_info', $this->module_info);  

			$s_cmd = \X2board\Includes\Classes\Context::get('cmd');
			switch( $s_cmd ) {
				case X2B_CMD_VIEW_LIST:
				case X2B_CMD_VIEW_POST:
					$this->_disp_content();
				case X2B_CMD_VIEW_WRITE_POST:
				case X2B_CMD_VIEW_MODIFY_POST:
				case X2B_CMD_VIEW_DELETE_POST:
				case X2B_CMD_VIEW_MODIFY_COMMENT:
				case X2B_CMD_VIEW_REPLY_COMMENT:
				case X2B_CMD_VIEW_DELETE_COMMENT:
					$s_cmd = '_'.$s_cmd;
					$this->$s_cmd();
					break;
				case X2B_CMD_VIEW_MESSAGE:
					$s_msg = sanitize_text_field(\X2board\Includes\Classes\Context::get('message'));
					$this->_disp_message($s_msg);
					break;
				default:
					$this->_disp_content();
					break;
			}
		}

		/**
		 * @brief display board contents
		 * dispBoardContent
		 **/
		private function _disp_content() {
			/**
			 * check the access grant (all the grant has been set by the module object)
			 **/
			if(!$this->grant->access || !$this->grant->list) {
				return $this->_disp_message('msg_not_permitted');
			}

			$o_editor_view = \X2board\Includes\get_view('editor');
			echo $o_editor_view->render_editor_css();
			unset($o_editor_view);

			/**
			 * display the category list, and then setup the category list on context
			 **/
			$this->_disp_category_list();

			/**
			 * display the search options on the screen
			 * add extra vaiables to the search options
			 **/
			// use search options on the template (the search options key has been declared, based on the language selected)
			foreach($this->a_search_option as $s_opt) {
				$a_search_option[$s_opt] = __('lbl_'.$s_opt, X2B_DOMAIN);
			}

			$a_extra_keys = \X2board\Includes\Classes\Context::get('extra_keys');
			if($a_extra_keys) {
				foreach($a_extra_keys as $key => $val) {
					if(!isset($a_search_option[$val->eid]) && $val->search == 'Y') { // never change locale that is set just above
						$a_search_option[$val->eid] = $val->name;
					}
				}
			}
			\X2board\Includes\Classes\Context::set('search_option', $a_search_option);

			// display the requested post
			$this->_view_post();

			// list config, columnList setting
			$o_board_model = \X2board\Includes\get_model('board');
			$this->listConfig = $o_board_model->get_list_config(); //$this->module_info->module_srl);
			unset($o_board_model);
			if(!$this->listConfig) {
				$this->listConfig = array();
			}
			$this->_makeListColumnList();

			// display the notice list
			$this->_disp_notice_list();

			// display the post list
			$this->_disp_post_list();
			
			// setup the skin file
			echo $this->render_skin_file('list');
		}

		/**
		 * @brief display the post content view
		 **/
		// function dispBoardContentView(){
		private function _view_post() {
			// get the variable value
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$n_page = \X2board\Includes\Classes\Context::get('page');

			// generate post model object
			$o_post_model = \X2board\Includes\get_model('post');

			$o_post = null;
			/**
			 * if the post exists, then get the post information
			 **/
			if($n_post_id) {
				$o_post = $o_post_model->get_post($n_post_id, false, true);

				// if the post is existed
				if($o_post->is_exists()) { // if($o_post->isExists())
					// if the board is not consistent with wp page ID, always remember board_id is WP page ID
					if(intval($o_post->get('board_id')) !== \X2board\Includes\Classes\Context::get('board_id') ) {
						return $this->_disp_message( __('msg_invalid_request', X2B_DOMAIN) );
					}

					// check the manage grant
					if($this->grant->manager) {
						$o_post->set_grant();
					}
					// if the consultation function is enabled, and the post is not a notice
					if($this->consultation && !$o_post->is_notice()) {
						$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
						if(abs($o_post->get('post_author')) != $o_logged_info->ID) {
							$o_post = $o_post_model->get_post(0);
						}
						unset($o_logged_info);
					}
				}
				else { // if the post is not existed, then alert a warning message					
					\X2board\Includes\Classes\Context::set( 'post_id', '', true );
					$this->_alert_message( __('msg_not_founded', X2B_DOMAIN) );
				}
			}
			else {  // if the post is not existed, get an empty post
				$o_post = $o_post_model->get_post(0);
			}

			if($o_post->is_exists()) {  // check the post view grant
				if(!$this->grant->view && !$o_post->is_granted()) {
					$o_post = $o_post_model->get_post(0);
					\X2board\Includes\Classes\Context::set('post_id','',true);
					$this->_alert_message( __('msg_not_permitted', X2B_DOMAIN) );
				}
				else {
					// add the document title to the browser
					// Context::addBrowserTitle($oDocument->getTitleText());

					// update the post view count (if the post is not secret)
					if(!$o_post->is_secret() || $o_post->is_granted()) {
						$o_post->update_readed_count();
					}

					// disappear the post if it is secret
					if($o_post->is_secret() && !$o_post->is_granted()) {
						$o_post->add( 'content', __('msg_secret_post', X2B_DOMAIN) );
					}
				}
			}
			unset($o_post_model);

			// setup the post oject on context
			\X2board\Includes\Classes\Context::set('post', $o_post);
		}

		/**
		 * @brief display board content list
		 * dispBoardContentList()
		 **/
		private function _disp_post_list() {
			// check the grant
			if(!$this->grant->list) {
				\X2board\Includes\Classes\Context::set('post_list', array());
				\X2board\Includes\Classes\Context::set('total_count', 0);
				\X2board\Includes\Classes\Context::set('total_page', 1);
				\X2board\Includes\Classes\Context::set('page', 1);
				\X2board\Includes\Classes\Context::set('page_navigation', new PageHandler(0,0,1,10));
				return;
			}
			
			// setup module_srl/page number/ list number/ page count
			$o_args = new \stdClass();
			$o_args->wp_page_id = \X2board\Includes\Classes\Context::get('board_id'); //$this->board_id;
			$o_args->page = \X2board\Includes\Classes\Context::get('page');
			$o_args->list_count = $this->module_info->list_count;
			$o_args->page_count = $this->module_info->page_count;
			$o_args->search_target = null;
			$o_args->search_keyword = null;
			
			// get the search target and keyword
			if($this->grant->view) {
				$o_args->search_target = \X2board\Includes\Classes\Context::get('search_target');
				$o_args->search_keyword = \X2board\Includes\Classes\Context::get('search_keyword');
			}

			$a_search_option = \X2board\Includes\Classes\Context::get('search_option');
			if($a_search_option==FALSE) {
				$a_search_option = $this->a_search_option;  
			}
			if(isset($a_search_option[$o_args->search_target])==FALSE) {
				$o_args->search_target = '';
			}

			// if the category is enabled, then get the category
			if(\X2board\Includes\Classes\Context::get('use_category')) {
				$o_args->category_id = trim(urldecode(\X2board\Includes\Classes\Context::get('category')));
			}
			else {
				$o_args->category_id = null;
			}

			// setup the sort index and order index
			$o_args->sort_index = \X2board\Includes\Classes\Context::get('sort_index');
			$o_args->order_type = \X2board\Includes\Classes\Context::get('order_type');
			if(!in_array($o_args->sort_index, $this->a_order_target)) {
				$o_args->sort_index = $this->module_info->order_target ? $this->module_info->order_target : 'list_order';
			}
			if(!in_array($o_args->order_type, array('asc','desc'))) {
				$o_args->order_type = $this->module_info->order_type ? $this->module_info->order_type : 'asc';
			}

			$o_post_model = \X2board\Includes\get_model('post');
			// set the current page of posts
			$post_id = \X2board\Includes\Classes\Context::get('post_id');  //$g_a_x2b_query_param['post_id'];
			if(!$o_args->page && $post_id) {
				$o_post = $o_post_model->get_post($post_id);
				if($o_post->is_exists() && !$o_post->is_notice()) {
					$page = $o_post_model->get_post_page($o_post, $o_args);
					\X2board\Includes\Classes\Context::set('page', $page);
					$o_args->page = $page;
				}
			}

			// setup the list count to be serach list count, if the category or search keyword has been set
			if($o_args->category_id || $o_args->search_keyword) {
				$o_args->list_count = $this->search_list_count;
			}

			// if the consultation function is enabled,  the get the logged user information
			// if($this->consultation)
			// {
			// 	$logged_info = Context::get('logged_info');
			// 	$args->member_srl = $logged_info->member_srl;

			// 	if($this->module_info->use_anonymous === 'Y')
			// 	{
			// 		unset($args->member_srl);
			// 		$args->member_srls = $logged_info->member_srl . ',' . $logged_info->member_srl * -1;
			// 	}
			// }

			// setup the list config variable on context
			\X2board\Includes\Classes\Context::set('list_config', $this->listConfig);

			// setup post list variables on context
			$output = $o_post_model->get_post_list($o_args, $this->except_notice);
			unset($o_post_model);
			\X2board\Includes\Classes\Context::set('post_list', $output->data);
			\X2board\Includes\Classes\Context::set('total_count', $output->total_count);
			\X2board\Includes\Classes\Context::set('total_page', $output->total_page);
			\X2board\Includes\Classes\Context::set('page', $output->page);
			\X2board\Includes\Classes\Context::set('page_navigation', $output->page_navigation);
			return $output;
		}

		/**
		 * @brief display the category list
		 * dispBoardCategoryList()
		 **/
		private function _disp_category_list() {
			if(\X2board\Includes\Classes\Context::get('use_category')) { // check if the use_category option is enabled;  -1 deactivated
				if(!$this->grant->list) { // check the grant
					\X2board\Includes\Classes\Context::set('category_recursive', array());
					return;
				}
				$o_category_model = \X2board\Includes\get_model('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				$a_linear_category = $o_category_model->build_linear_category();
				unset($o_category_model);
				\X2board\Includes\Classes\Context::set('category_list', $a_linear_category);
				unset($a_linear_category);
			}
		}

		/**
		 * @brief display notice list (can be used by API)
		 * dispBoardNoticeList(){
		 **/
		private function _disp_notice_list() {
			// check the grant
			if(!$this->grant->list)	{
				\X2board\Includes\Classes\Context::set('notice_list', array());
				return;
			}

			$o_post_model = \X2board\Includes\get_model('post');
			$o_args = new \stdClass();
			$o_args->wp_page_id = \X2board\Includes\Classes\Context::get('board_id');
			$output = $o_post_model->get_notice_list($o_args, $this->columnList);
			unset($o_args);
			\X2board\Includes\Classes\Context::set('notice_list', $output->data);	
		}

		/**
		 * @brief
		 **/
		private function _makeListColumnList() {
			$configColumList = array_keys($this->listConfig);
			$tableColumnList = array('post_id', 'board_id', 'category_id', 'is_notice',  // , 'lang_code'
					'title', 'title_bold', 'title_color', 'content', 'readed_count', 'voted_count',
					'blamed_count', 'comment_count', 'trackback_count', 'uploaded_count', 'password',  // 'user_id',
					'nick_name', 'post_author', 'email_address', 'tags', // 'member_srl', 'user_name', 'homepage', 'extra_vars',
					'regdate_dt', 'last_update_dt', 'last_updater', 'ipaddress', 'list_order', 'update_order',
					'status', 'comment_status');  // 'allow_trackback', 'notify_message', 
			$this->columnList = array_intersect($configColumList, $tableColumnList);

			if(in_array('summary', $configColumList)) {
				array_push($this->columnList, 'content');
			}

			// default column list add
			$defaultColumn = array('post_id', 'board_id', 'category_id', 'post_author', 'last_update_dt', 'comment_count',  // 'lang_code', 
								   'uploaded_count', 'status', 'regdate_dt', 'title_bold', 'title_color');  // 'trackback_count', 

			//TODO guestbook, blog style supports legacy codes.
			// if($this->module_info->skin == 'x2_guestbook' || $this->module_info->default_style == 'blog') {
			// 	$defaultColumn = $tableColumnList;
			// }

			if (in_array('last_post', $configColumList)) {
				array_push($this->columnList, 'last_updater');
			}

			// add is_notice
			if ($this->except_notice) {
				array_push($this->columnList, 'is_notice');
			}
			$this->columnList = array_unique(array_merge($this->columnList, $defaultColumn));
		}

		/**
		 * @brief display post write form
		 **/
		private function _view_modify_post() {
			$this->_view_write_post();
		}

		/**
		 * @brief display post write form
		 **/
		private function _view_write_post() {
			// check grant
			if(!$this->grant->write_post) {
				return $this->_disp_message( __('msg_not_permitted', X2B_DOMAIN) );
			}

			/**
			 * check if the category user define field is enabled or not
			 **/
			if( \X2board\Includes\Classes\Context::get('use_category') ) {
				$o_category_model = \X2board\Includes\get_model('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				$a_linear_category = $o_category_model->build_linear_category();
				unset($o_category_model);
				\X2board\Includes\Classes\Context::set('category_list', $a_linear_category);
				unset($a_linear_category);
			}

			// GET parameter post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$o_post_model = \X2board\Includes\get_model('post');
			$o_post = $o_post_model->get_post(0, $this->grant->manager);
			$o_post->set_post($n_post_id);

			$o_post->add('board_id', \X2board\Includes\Classes\Context::get('board_id') );

			if($o_post->is_exists() && $this->module_info->protect_content=="Y" && 
				$o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_protect_content', X2B_DOMAIN) );
			}

			// if the post is not granted, then back to the password input form
			if($o_post->is_exists() && !$o_post->is_granted()) {
				echo $this->render_skin_file('input_password_form');
				return;
			}
			
			if(!$o_post->is_exists()) {
				$o_post->set_post(\X2board\Includes\get_next_sequence(), false); // reserve new post id for file appending
			}
			if(!$o_post->get('status')) {
				$o_post->add('status', $o_post_model->get_default_status());
			}

			$a_status_list = $this->_get_status_name_list();
			if(count($a_status_list) > 0) {
				\X2board\Includes\Classes\Context::set('status_list', $a_status_list);
			}
			unset($a_status_list);

			\X2board\Includes\Classes\Context::set('post', $o_post);
			unset($o_post);
			unset($o_post_model);

			// setup the skin file
			echo $this->render_skin_file('write_form');
		}

		/**
		 * @brief display comment wirte form
		 * dispBoardWriteComment()
		 **/
		private function _view_write_comment() {
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');

			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message(__('msg_not_permitted', X2B_DOMAIN));
			}

			// get the post information
			$o_post_model = \X2board\Includes\get_model('post');
			$o_post = $o_post_model->get_post($n_post_id);
			unset($o_post_model);
			if(!$o_post->is_exists()) {
				return $this->_disp_message(__('msg_invalid_request', X2B_DOMAIN));
			}

			// Check allow comment
			if(!$o_post->allow_comment()) {
				return $this->_disp_message(__('msg_not_allow_comment', X2B_DOMAIN));
			}

			// obtain the comment (create an empty comment post for comment_form usage)
			$o_comment_model = \X2board\Includes\get_model('comment');
			$o_source_comment = $o_comment = $o_comment_model->get_comment(0);
			unset($o_comment_model);
			$o_comment->add('parent_post_id', $n_post_id);

			// setup post variables on context
			\X2board\Includes\Classes\Context::set('o_post', $o_post);
			\X2board\Includes\Classes\Context::set('o_source_comment',$o_source_comment);
			\X2board\Includes\Classes\Context::set('o_the_comment',$o_comment);

			echo $this->render_skin_file('editor_comment');
		}

		/**
		 * @brief display the comment modification from
		 * dispBoardModifyComment()
		 **/
		private function _view_modify_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message(__('msg_not_permitted', X2B_DOMAIN));
			}

			// get the post_id and comment_id
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');

			// if the comment is not existed
			if(!$n_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN));
			}

			// get comment information
			$o_comment_model = \X2board\Includes\get_model('comment');
			$o_comment = $o_comment_model->get_comment($n_comment_id, $this->grant->manager);
			$o_source_comment = $o_comment_model->get_comment();

			unset($o_comment_model);
			// if the comment is not exited, alert an error message
			if(!$o_comment->is_exists()) {
				return $this->_disp_message(__('msg_invalid_request', X2B_DOMAIN));
			}

			// if the comment is not granted, then back to the password input form
			if(!$o_comment->is_granted()) {
				echo $this->render_skin_file('input_password_form');
				return;
			}

			// setup the comment variables on context
			\X2board\Includes\Classes\Context::set('o_source_comment', $o_source_comment);
			\X2board\Includes\Classes\Context::set('o_the_comment', $o_comment);

			echo $this->render_skin_file('comment_form');
		}

		/**
		 * @brief display board module deletion form
		 * _getStatusNameList(&$oDocumentModel)
		 **/
		private function _get_status_name_list() {
			$a_result_list = array();
			if(!empty($this->module_info->use_status)) {
				$o_post_model = \X2board\Includes\get_model('post');
				$a_status_name_list = $o_post_model->get_status_name_list();
				unset($o_post_model);
				if(is_array($this->module_info->use_status)) {
					foreach($this->module_info->use_status as $key => $value) {
						$a_result_list[$value] = $a_status_name_list[$value];
					}
				}
				unset($a_status_name_list);
			}
			return $a_result_list;
		}

		/**
		 * @brief display board module deletion form
		 * dispBoardDelete()
		 **/
		private function _view_delete_post() {
			// check grant
			if(!$this->grant->write_post) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');

			// if post exists, get the post information
			if($n_post_id) {
				$o_post_model = \X2board\Includes\get_model('post');
				$o_post = $o_post_model->get_post($n_post_id);
				unset($o_post_model);
			}

			// if the post is not existed, then back to the board content page
			if(!isset($o_post) || !$o_post->is_exists()) {
				return $this->_disp_content();
			}

			// if the post is not granted, then back to the password input form
			if(!$o_post->is_granted()) {
				echo $this->render_skin_file('input_password_form');
				return;
			}

			if($this->module_info->protect_content=="Y" && $o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return $this->_disp_message('msg_protect_content');
			}

			\X2board\Includes\Classes\Context::set('oPost', $o_post);

			echo $this->render_skin_file('delete_form');
		}

		/**
		 * @brief display comment replies page
		 * dispBoardReplyComment()
		 **/
		private function _view_reply_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the parent comment ID
			$parent_comment_id = \X2board\Includes\Classes\Context::get('comment_id');
			// if the parent comment is not existed
			if(!$parent_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN) );
			}

			// get the comment
			$o_comment_model = \X2board\Includes\get_model('comment');
			$o_source_comment = $o_comment_model->get_comment($parent_comment_id, $this->grant->manager);

			// if the comment is not existed, opoup an error message
			if(!$o_source_comment->is_exists()) {
				return $this->_disp_message('msg_invalid_request');
			}
			if( \X2board\Includes\Classes\Context::get('post_id') && 
				$o_source_comment->get('parent_post_id') != \X2board\Includes\Classes\Context::get('post_id')) {
				return $this->_disp_message('msg_invalid_request');
			}

			// Check allow comment
			$o_post_model = \X2board\Includes\get_model('post');
			$o_post = $o_post_model->get_post($o_source_comment->get('parent_post_id'));
			unset($o_post_model);
			if(!$o_post->allow_comment()) {
				unset($o_post);
				return $this->_disp_message('msg_not_allow_comment');
			}

			// get the comment information
			$o_child_comment = $o_comment_model->get_comment();
			unset($o_comment_model);
			$o_child_comment->add('board_id', $o_post->get('board_id'));
			unset($o_post);
			$o_child_comment->add('parent_post_id', $o_source_comment->get('parent_post_id'));
			$o_child_comment->add('parent_comment_id', $parent_comment_id);
			\X2board\Includes\Classes\Context::set('o_the_comment', $o_child_comment);

			// setup comment variables
			\X2board\Includes\Classes\Context::set('o_source_comment', $o_source_comment);
			unset($o_source_comment);

			echo $this->render_skin_file('comment_form');
		}

		/**
		 * @brief display the delete comment form
		 * dispBoardDeleteComment()
		 **/
		public function _view_delete_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the comment_srl to be deleted
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');

			// if the comment exists, then get the comment information
			if($n_comment_id) {
				$o_comment_model = \X2board\Includes\get_model('comment');
				$o_comment = $o_comment_model->get_comment($n_comment_id, $this->grant->manager);
				unset($o_comment_model);
			}

			// if the comment is not existed, then back to the board content page
			if(!$o_comment->is_exists() ) {
				return $this->_disp_content();
			}

			// if the comment is not granted, then back to the password input form
			if(!$o_comment->is_granted()) {
				return $this->render_skin_file('input_password_form');
			}

			\X2board\Includes\Classes\Context::set('o_the_comment',$o_comment);

			echo $this->render_skin_file('delete_comment_form');
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		public function write_post_hidden_fields() {
			$a_header = array();
			$a_header['board_id'] = \X2board\Includes\Classes\Context::get('board_id');
			$o_post = \X2board\Includes\Classes\Context::get('post');
			$a_header['post_id'] = $o_post->post_id;
			if($o_post->post_id) {  // update a old post
				$a_header['cmd'] = X2B_CMD_PROC_MODIFY_POST;
				$a_header['content'] = htmlspecialchars($o_post->content);
			}
			else { // write a new post
				$a_header['cmd'] = X2B_CMD_PROC_WRITE_POST; 
				$a_header['content'] = null;
			}
			unset($o_post);

			wp_nonce_field('x2b_'.$a_header['cmd'], 'x2b_'.$a_header['cmd'].'_nonce');
		
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 * getFieldLabel($field)
		 * @param array $field
		 * @return string
		 */
		private function _get_field_label($a_field){
			$field_type = $a_field['field_type'];
			if(isset($this->_default_fields[$field_type])){
				return $this->_default_fields[$field_type]['field_label'];
			}
			if(isset($this->_extends_fields[$field_type])){
				return $this->_extends_fields[$field_type]['field_label'];
			}
			return $a_field['field_label'];
		}

		/**
		 * editor스킨의 hidden field 출력
		 */
		public static function write_comment_hidden_fields($b_embedded_editor=false) { 
			wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_COMMENT, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');

			$header = array();
			$a_header['cmd'] = X2B_CMD_PROC_WRITE_COMMENT;
			$a_header['board_id'] = \X2board\Includes\Classes\Context::get('board_id');

			$o_post = \X2board\Includes\Classes\Context::get('post');
			if($b_embedded_editor) {  // for sketchbook5 embedded_editor editor
				$a_header['parent_post_id'] = $o_post->post_id;
				$a_header['parent_comment_id'] = null;
				$a_header['content'] = null;
			}
			else {
				if(isset($o_post)) {  // insert a root comment
					if($o_post->post_id) {  // this is mandatory
						$a_header['parent_post_id'] = $o_post->post_id;
						$a_header['editor_sequence'] = null;  // memory for a reserved editor_sequence to find comment id if uploading a file
					}
					$a_header['content'] = null;
				}
				else {   // insert a child comment  or update the comment
					$o_the_comment = \X2board\Includes\Classes\Context::get('o_the_comment');
					$a_header['parent_post_id'] = $o_the_comment->get('parent_post_id');
					$a_header['parent_comment_id'] = $o_the_comment->get('parent_comment_id');
					$a_header['comment_id'] = $o_the_comment->get('comment_id');
					$a_header['content'] = htmlspecialchars($o_the_comment->get('content'));
					unset($o_the_comment);
				}
			}
			unset($o_post);
			
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
		}

		/**
		 * @brief display board message
		 **/
		// function dispBoardMessage($s_msg) {
		private function _disp_message($s_msg) {
			\X2board\Includes\Classes\Context::set('message', $s_msg);
			// setup the skin file
			echo $this->render_skin_file('message');
		}

		/**
		 * @brief the method for displaying the warning messages
		 * display an error message if it has not  a special design
		 **/
		private function _alert_message($s_message) {
			echo sprintf('<script> jQuery(function(){ alert("%s"); } );</script>', $s_message);
		}

		/**
		 * @brief display tag list
		 **/
		/*function dispBoardTagList()
		{
			// check if there is not grant fot view list, then alert an warning message
			if(!$this->grant->list)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// generate the tag module model object
			$oTagModel = getModel('tag');

			$obj = new stdClass;
			$obj->mid = $this->module_info->mid;
			$obj->list_count = 10000;
			$output = $oTagModel->getTagList($obj);

			// automatically order
			if(count($output->data))
			{
				$numbers = array_keys($output->data);
				shuffle($numbers);

				if(count($output->data))
				{
					foreach($numbers as $k => $v)
					{
						$tag_list[] = $output->data[$v];
					}
				}
			}

			Context::set('tag_list', $tag_list);

			$oSecurity = new Security();
			$oSecurity->encodeHTML('tag_list.');

			$this->setTemplateFile('tag_list');
		}*/
	}
}