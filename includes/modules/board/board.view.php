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

		private $_default_fields = array();  // get_default_user_input_fields();
		private $_extends_fields = array();
		// private $_n_current_post_id = null;  // hidden field에서 설정하고 editor module로 전달하기 위한 메모리
		// private $_n_current_comment_id = null;  // hidden field에서 설정하고 editor module로 전달하기 위한 메모리

		/**
		 * @brief initialization
		 * board module can be used in guest mode
		 **/
		public function init() {
// var_dump('board view init');	
// var_dump($this->module_info);
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

			$o_post_model = \X2board\Includes\getModel('post');
			$a_status = $this->_get_status_name_list();
			if(isset($a_status['SECRET'])) {
				$this->module_info->secret = 'Y';  // for notify_message checkbox on post/comment editor
			}
			unset($a_status);

			// editor 스킨의 사용자 입력 field 출력
			$o_post_model = \X2board\Includes\getModel('post');
			$this->_default_fields = $o_post_model->get_default_fields();  // get_default_user_input_fields();
			$this->_extends_fields = $o_post_model->get_extended_fields();  // get_extended_user_input_fields();
			$a_user_input_field = $o_post_model->get_user_define_fields();
			\X2board\Includes\Classes\Context::set('field', $a_user_input_field);
			
			$b_category_activated = false;
			$b_comment_activated = false;
			foreach($a_user_input_field as $_ => $o_user_field ) {
				if($o_user_field->type == 'category') {
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
			$o_args = \X2board\Includes\Classes\Context::getRequestVars();
// var_dump($o_args);
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
			if(!is_dir($s_template_path)||!$this->module_info->skin) {
				$this->module_info->skin = 'default';
				$s_template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			}
			$this->set_skin_path($s_template_path);
			\X2board\Includes\Classes\Context::set('skin_path_abs', $this->skin_path);
			\X2board\Includes\Classes\Context::set('skin_url', X2B_URL.'includes/modules/board/skins/'.$this->module_info->skin);

			// Avoid warning - Undefined variable: sort_index
			if(!\X2board\Includes\Classes\Context::get('sort_index')) {
				\X2board\Includes\Classes\Context::set('sort_index', null);
			}
			
			//current_module_info있으므로 절대 생성하지 말것
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
		// {
		// 	$oSecurity = new Security();
		// 	$oSecurity->encodeHTML('document_srl', 'comment_srl', 'vid', 'mid', 'page', 'category', 'search_target', 'search_keyword', 'sort_index', 'order_type', 'trackback_srl');

		// 	/**
		// 	 * setup the module general information
		// 	 **/

		// 	// $this->_getStatusNameListecret option backward compatibility
		// 	$oDocumentModel = getModel('document');

		// 	$statusList = $this->_getStatusNameList($oDocumentModel);
		// 	if(isset($statusList['SECRET']))
		// 	{
		// 		$this->module_info->secret = 'Y';
		// 	}

		// 	// use_category <=1.5.x, hide_category >=1.7.x
		// 	$count_category = count($oDocumentModel->getCategoryList($this->module_info->module_srl));
		// 	if($count_category)
		// 	{
		// 		if($this->module_info->hide_category)
		// 		{
		// 			$this->module_info->use_category = ($this->module_info->hide_category == 'Y') ? 'N' : 'Y';
		// 		}
		// 		else if($this->module_info->use_category)
		// 		{
		// 			$this->module_info->hide_category = ($this->module_info->use_category == 'Y') ? 'N' : 'Y';
		// 		}
		// 		else
		// 		{
		// 			$this->module_info->hide_category = 'N';
		// 			$this->module_info->use_category = 'Y';
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$this->module_info->hide_category = 'Y';
		// 		$this->module_info->use_category = 'N';
		// 	}

		// 	/**
		// 	 * check the consultation function, if the user is admin then swich off consultation function
		// 	 * if the user is not logged, then disppear write document/write comment./ view document
		// 	 **/
		// 	if($this->module_info->consultation == 'Y' && !$this->grant->manager && !$this->grant->consultation_read)
		// 	{
		// 		$this->consultation = TRUE;
		// 		if(!Context::get('is_logged'))
		// 		{
		// 			$this->grant->list = FALSE;
		// 			$this->grant->write_document = FALSE;
		// 			$this->grant->write_comment = FALSE;
		// 			$this->grant->view = FALSE;
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$this->consultation = FALSE;
		// 	}

		// 	/**
		// 	 * setup the template path based on the skin
		// 	 * the default skin is default
		// 	 **/
		// 	$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
		// 	if(!is_dir($template_path)||!$this->module_info->skin)
		// 	{
		// 		$this->module_info->skin = 'default';
		// 		$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
		// 	}
		// 	$this->set_skin_path($template_path);

		// 	/**
		// 	 * use context::set to setup extra variables
		// 	 **/
		// 	$oDocumentModel = getModel('document');
		// 	$extra_keys = $oDocumentModel->getExtraKeys($this->module_info->module_srl);
		// 	Context::set('extra_keys', $extra_keys);

		// 	/**
		// 	 * add extra variables to order(sorting) target
		// 	 **/
		// 	if (is_array($extra_keys))
		// 	{
		// 		foreach($extra_keys as $val)
		// 		{
		// 			$this->order_target[] = $val->eid;
		// 		}
		// 	}
		// 	/**
		// 	 * load javascript, JS filters
		// 	 **/
		// 	Context::addJsFilter($this->module_path.'tpl/filter', 'input_password.xml');
		// 	Context::addJsFile($this->module_path.'tpl/js/board.js');

		// 	// remove [document_srl]_cpage from get_vars
		// 	$args = Context::getRequestVars();
		// 	foreach($args as $name => $value)
		// 	{
		// 		if(preg_match('/[0-9]+_cpage/', $name))
		// 		{
		// 			Context::set($name, '', TRUE);
		// 			Context::set($name, $value);
		// 		}
		// 	}
		// 	// MID �������� ��û�ϸ�, skin���� $_SESSION �е��� ���
		// 	if( $this->module_info->allow_session_value_skin == 'Y' )
		// 		Context::set('session', $_SESSION );
		// }

		/**
		 * @brief display board contents
		 **/
		private function _disp_content() { // dispBoardContent
var_dump(X2B_CMD_VIEW_LIST);
			/**
			 * check the access grant (all the grant has been set by the module object)
			 **/
			if(!$this->grant->access || !$this->grant->list) {
				return $this->_disp_message('msg_not_permitted');
			}

			// call editor style on behalf of DisplayHandler.class.php::printContent() 
			// just once!!
			$o_editor_view = \X2board\Includes\getView('editor');
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
			foreach($this->a_search_option as $opt) {
				$a_search_option[$opt] = __($opt, 'x2board'); //Context::getLang($opt);
			}

			$a_extra_keys = \X2board\Includes\Classes\Context::get('extra_keys');
// var_Dump($a_extra_keys);			
			if($a_extra_keys) {
				foreach($a_extra_keys as $key => $val) {
					if($val->search == 'Y') {
						$a_search_option[$val->eid] = $val->name;
					}
				}
			}
// var_Dump($a_search_option);
			\X2board\Includes\Classes\Context::set('search_option', $a_search_option);

			// $oDocumentModel = getModel('document');
			// $statusNameList = $this->_getStatusNameList($oDocumentModel);
			// if(count($statusNameList) > 0)
			// {
			// 	Context::set('status_list', $statusNameList);
			// }

			// display the requested post
			$this->_view_post();  // $this->dispBoardContentView();

			// list config, columnList setting
			$o_board_model = \X2board\Includes\getModel('board');
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
			// Return if no result or an error occurs
			// if(!$output->toBool()) {
			// 	return $this->_disp_message($output->getMessage());
			// }

			/**
			 * add javascript filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'search.xml');
			
			// setup the skin file
			echo $this->render_skin_file('list');
		}

		/**
		 * @brief display the post content view
		 **/
		// function dispBoardContentView(){
		private function _view_post() {
var_dump(X2B_CMD_VIEW_POST);
			// get the variable value
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$n_page = \X2board\Includes\Classes\Context::get('page');

// var_dump($n_post_id, $n_page);
			// generate post model object
			$o_post_model = \X2board\Includes\getModel('post'); // $oDocumentModel = getModel('document');

			$o_post = null;
			/**
			 * if the post exists, then get the post information
			 **/
			if($n_post_id) {
				$o_post = $o_post_model->get_post($n_post_id, false, true);  // $oDocument = $o_post_model->getDocument($document_srl, false, true);

				// if the post is existed
				if($o_post->is_exists()) { // if($o_post->isExists())
// var_dump($o_post->get('board_id'));			
// var_dump(get_the_ID());			
					// if the board is not consistent with wp page ID
					if(intval($o_post->get('board_id')) !== get_the_ID() )	{  // board_id is WP page ID
						return $this->_disp_message( __('msg_invalid_request', 'x2board') ); // return $this->stop('msg_invalid_request');
					}

					// check the manage grant
					if($this->grant->manager) {
						$o_post->set_grant();
// var_Dump($_SESSION);						
					}
// var_dump($o_post->get('post_author'));
					// if the consultation function is enabled, and the post is not a notice
					if($this->consultation && !$o_post->is_notice()) {
						$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
// var_dump($o_logged_info->ID);
						if(abs($o_post->get('post_author')) != $o_logged_info->ID) {
							$o_post = $o_post_model->get_post(0);
						}
						unset($o_logged_info);
					}

					/*
					remove TEMP post status
					$s_temp_status = $o_post_model->get_config_status('temp');
					// if the post is TEMP saved, check Grant
					if($o_post->get_status() == $s_temp_status) {
						if(!$o_post->is_granted()) {
							$o_post = $o_post_model->get_post(0);
						}
					}*/
				}
				else { // if the post is not existed, then alert a warning message					
					\X2board\Includes\Classes\Context::set( 'post_id', '', true );
					$this->_alert_message( __('msg_not_founded', 'x2board') );
				}
			}
			else {  // if the post is not existed, get an empty post
				$o_post = $o_post_model->get_post(0);
			}

			if($o_post->is_exists()) {  // check the post view grant
				if(!$this->grant->view && !$o_post->is_granted()) {
					$o_post = $o_post_model->get_post(0);
					\X2board\Includes\Classes\Context::set('post_id','',true);
					$this->_alert_message( __('msg_not_permitted', 'x2board') );
				}
				else {
					// add the document title to the browser
					// Context::addBrowserTitle($oDocument->getTitleText());

					// update the post view count (if the post is not secret)
					if(!$o_post->is_secret() || $o_post->is_granted()) {
						$o_post->update_readed_count();
					}
					
					// begin - set index position of current post to find prev and next post
					// $a_post_list = \X2board\Includes\Classes\Context::get('post_list');
					// foreach( $a_post_list as $no => $o_post ) {
					// 	if( $n_post_id == $o_post->post_id ) {
					// 		\X2board\Includes\Classes\Context::set('cur_post_pos_in_list', $no);
					// 		break;
					// 	}
					// }
					// end - set index position of current post to find prev and next post

					// disappear the post if it is secret
					if($o_post->is_secret() && !$o_post->is_granted()) {
						$o_post->add( 'content', __('this_is_secret', 'x2board') );
					}
				}
			}
			unset($o_post_model);

			// setup the post oject on context
			// $o_post->add('module_srl', $this->module_srl);
			\X2board\Includes\Classes\Context::set('post', $o_post);

			if($o_post->is_exists()) {  // check comment list; which depends on \X2board\Includes\Classes\Context::get('post');
				// $o_editor_view = \X2board\Includes\getView('editor');
				// $o_comment_editor = $o_editor_view->get_comment_editor();
				// unset($o_editor_view);
				// \X2board\Includes\Classes\Context::set('comment_editor_html', $o_comment_editor->s_comment_editor_html );
				// \X2board\Includes\Classes\Context::set('comment_hidden_field_html', $o_comment_editor->s_comment_hidden_field_html);
				// unset($o_comment_editor);
			}

			/**
			 * add javascript filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');
			// return new BaseObject();
			// setup the skin file
			// echo $this->render_skin_file('document');
		}

		/**
		 * @brief get the board configuration
		 **/
		/*public function get_config() {
			return $this->module_info;
		}*/

		/**
		 * @brief display board content list
		 **/
		private function _disp_post_list() {  // dispBoardContentList(){  
			// check the grant
			if(!$this->grant->list) {
				\X2board\Includes\Classes\Context::set('post_list', array());
				\X2board\Includes\Classes\Context::set('total_count', 0);
				\X2board\Includes\Classes\Context::set('total_page', 1);
				\X2board\Includes\Classes\Context::set('page', 1);
				\X2board\Includes\Classes\Context::set('page_navigation', new PageHandler(0,0,1,10));
				return;
			}
			
			// $o_post_model = \X2board\Includes\getModel('post');
// var_dump($this->grant );

			// setup module_srl/page number/ list number/ page count
			$o_args = new \stdClass();
			// $o_args->module_srl = $this->module_srl;
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
// var_dump($this->module_info);
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
// var_dump($o_args);
			$o_post_model = \X2board\Includes\getModel('post');
			// set the current page of posts
			// $document_srl = Context::get('document_srl');
			$post_id = \X2board\Includes\Classes\Context::get('post_id');  //$g_a_x2b_query_param['post_id'];
			if(!$o_args->page && $post_id) {
// var_dump($o_args->page);
				$o_post = $o_post_model->get_post($post_id);
				if($o_post->is_exists() && !$o_post->is_notice()) {
// var_dump($o_post);					
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
// var_dump($this->listConfig);			
			// setup post list variables on context
			$output = $o_post_model->get_post_list($o_args, $this->except_notice);  //, TRUE, $this->columnList);
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
		 **/
		// function dispBoardCategoryList(){
		private function _disp_category_list() {
			if(\X2board\Includes\Classes\Context::get('use_category')) { // check if the use_category option is enabled;  -1 deactivated
				if(!$this->grant->list) { // check the grant
					\X2board\Includes\Classes\Context::set('category_recursive', array());
					return;
				}
				$o_category_model = \X2board\Includes\getModel('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				// \X2board\Includes\Classes\Context::set('category_recursive', $o_category_model->get_category_navigation()); // for category tab navigation
				$a_linear_category = $o_category_model->build_linear_category();
				unset($o_category_model);
				\X2board\Includes\Classes\Context::set('category_list', $a_linear_category);
				unset($a_linear_category);
			}
		}

		/**
		 * @brief display notice list (can be used by API)
		 **/
		// function dispBoardNoticeList(){
		private function _disp_notice_list() {
			// check the grant
			if(!$this->grant->list)	{
				\X2board\Includes\Classes\Context::set('notice_list', array());
				return;
			}

			$o_post_model = \X2board\Includes\getModel('post');
			$o_args = new \stdClass();
			$o_args->wp_page_id = get_the_ID();  // $this->module_srl;
			$output = $o_post_model->get_notice_list($o_args, $this->columnList);
// var_dump($this->columnList);			
			unset($o_args);
			\X2board\Includes\Classes\Context::set('notice_list', $output->data);	
		}

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

			// add table name
			// foreach($this->columnList as $no => $value)	{
			// 	$this->columnList[$no] = 'post.' . $value;
			// }
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
var_dump(X2B_CMD_VIEW_WRITE_POST);
			// check grant
			if(!$this->grant->write_post) {
				return $this->_disp_message( __('msg_not_permitted', 'x2board') );
			}

			/**
			 * check if the category user define field is enabled or not
			 **/
			if( \X2board\Includes\Classes\Context::get('use_category') ) {  // if($this->module_info->use_category=='Y') {
				$o_category_model = \X2board\Includes\getModel('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				$a_linear_category = $o_category_model->build_linear_category();
				unset($o_category_model);
				\X2board\Includes\Classes\Context::set('category_list', $a_linear_category);
				unset($a_linear_category);
			}
			// unset($a_user_input_field);

			// GET parameter post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post(0, $this->grant->manager);
			$o_post->set_post($n_post_id);

			// if($oDocument->get('module_srl') == $oDocument->get('member_srl')) {
// var_dump($o_post->get('post_author'));
			// if($o_post->get('board_id') == $o_post->get('post_author')) {
			// 	$savedDoc = TRUE;
			// }
			// else {
			// 	$savedDoc = FALSE;
			// }
			// $oDocument->add('module_srl', $this->module_srl);
// var_dump($this->grant->write_post);
			$o_post->add('board_id', \X2board\Includes\Classes\Context::get('board_id') ); // $this->board_id);

			if($o_post->is_exists() && $this->module_info->protect_content=="Y" && 
				$o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_protect_content', 'x2board') );
			}

			// if the post is not granted, then back to the password input form
			if($o_post->is_exists() && !$o_post->is_granted()) {
				echo $this->render_skin_file('input_password_form');
				return;
			}
			
			if(!$o_post->is_exists()) {
				$o_post->set_post(\X2board\Includes\getNextSequence(), false); // reserve new post id for file appending
				// $oModuleModel = getModel('module');
				// $point_config = $oModuleModel->getModulePartConfig('point',$this->module_srl);
				// unset($oModuleModel);
				// $logged_info = \X2board\Includes\Classes\Context::get('logged_info');
				// $oPointModel = getModel('point');
				// $pointForInsert = $point_config["insert_document"];
				// if($pointForInsert < 0)
				// {
				// 	if( !$logged_info )
				// 	{
				// 		return $this->_disp_message('msg_not_permitted');
				// 	}
				// 	else if (($oPointModel->getPoint($logged_info->member_srl) + $pointForInsert )< 0 )
				// 	{
				// 		return $this->_disp_message('msg_not_enough_point');
				// 	}
				// }
			}
			if(!$o_post->get('status')) {
				$o_post->add('status', $o_post_model->get_default_status());
			}

			$statusList = $this->_get_status_name_list();
			if(count($statusList) > 0) {
				\X2board\Includes\Classes\Context::set('status_list', $statusList);
			}

			// get Document status config value
			// \X2board\Includes\Classes\Context::set('document_srl',$document_srl);

			// apply xml_js_filter on header
			// $oDocumentController = getController('document');
			// $oDocumentController->addXmlJsFilter($this->module_info->module_srl);

			// if the post exists, then setup extra variabels on context
			// if($o_post->is_exists() && !$savedDoc) { // 포스트 수정 혹은 임시 저장 포스트의 확장 변수 가져오기
			// 	\X2board\Includes\Classes\Context::set('extra_keys', $o_post->get_extra_vars());
			// }
			
			/**
			 * add JS filters
			 **/
			// if(Context::get('logged_info')->is_admin=='Y') Context::addJsFilter($this->module_path.'tpl/filter', 'insert_admin.xml');
			// else Context::addJsFilter($this->module_path.'tpl/filter', 'insert.xml');

			// $oSecurity = new Security();
			// $oSecurity->encodeHTML('category_list.text', 'category_list.title');
// var_dump($o_post);
			\X2board\Includes\Classes\Context::set('post', $o_post);
			unset($o_post);
			unset($o_post_model);

			// setup the skin file
			echo $this->render_skin_file('write_form');
		}

		/**
		 * @brief display comment wirte form
		 **/
		// function dispBoardWriteComment()
		private function _view_write_comment() {
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');

			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message(__('msg_not_permitted', 'x2board'));
			}

			// get the post information
			// $oDocumentModel = getModel('document');
			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($n_post_id);
			unset($o_post_model);
			if(!$o_post->is_exists()) {
				return $this->_disp_message(__('msg_invalid_request', 'x2board'));
			}

			// Check allow comment
			if(!$o_post->allow_comment()) {
				return $this->_disp_message(__('msg_not_allow_comment', 'x2board'));
			}

			// obtain the comment (create an empty comment post for comment_form usage)
			$o_comment_model = \X2board\Includes\getModel('comment');
			$o_source_comment = $o_comment = $o_comment_model->get_comment(0);
			unset($o_comment_model);
			$o_comment->add('parent_post_id', $n_post_id);
			// $oComment->add('module_srl', $this->module_srl);

			// setup post variables on context
			\X2board\Includes\Classes\Context::set('o_post', $o_post);
			\X2board\Includes\Classes\Context::set('o_source_comment',$o_source_comment);
			\X2board\Includes\Classes\Context::set('o_the_comment',$o_comment);

			/**
			 * add JS filter
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			// $this->setTemplateFile('comment_form');
			echo $this->render_skin_file('editor_comment');
		}

		/**
		 * @brief display the comment modification from
		 **/
		// function dispBoardModifyComment()
		private function _view_modify_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message(__('msg_not_permitted', 'x2board'));
			}

			// get the post_id and comment_id
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');

			// if the comment is not existed
			if(!$n_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', 'x2board'));
			}

			// get comment information
			// $oCommentModel = getModel('comment');
			$o_comment_model = \X2board\Includes\getModel('comment');
			$o_comment = $o_comment_model->get_comment($n_comment_id, $this->grant->manager);
			$o_source_comment = $o_comment_model->get_comment();
// var_dump($o_comment);
// var_dump($o_source_comment);
			unset($o_comment_model);
			// if the comment is not exited, alert an error message
			if(!$o_comment->is_exists()) {
				return $this->_disp_message(__('msg_invalid_request', 'x2board'));
			}

			// if the comment is not granted, then back to the password input form
			if(!$o_comment->is_granted()) {
				// return $this->setTemplateFile('input_password_form');
				echo $this->render_skin_file('input_password_form');
				return;
			}

			// setup the comment variables on context
			// \X2board\Includes\Classes\Context::set('o_source_comment', $oCommentModel->getComment());
			\X2board\Includes\Classes\Context::set('o_source_comment', $o_source_comment);
			\X2board\Includes\Classes\Context::set('o_the_comment', $o_comment);

			/**
			 * add JS fitlers
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			// $this->setTemplateFile('comment_form');
			// echo $this->render_skin_file('editor_comment');
			echo $this->render_skin_file('comment_form');
		}

		// function _getStatusNameList(&$oDocumentModel)
		private function _get_status_name_list() {
			$resultList = array();
			if(!empty($this->module_info->use_status)) {
				$o_post_model = \X2board\Includes\getModel('post');
				$statusNameList = $o_post_model->get_status_name_list();
				unset($o_post_model);
				$statusList = $this->module_info->use_status;
				if(is_array($this->module_info->use_status)) {
					foreach($this->module_info->use_status as $key => $value) {
						$resultList[$value] = $statusNameList[$value];
					}
				}
			}
			return $resultList;
		}

		/**
		 * @brief display board module deletion form
		 **/
		// function dispBoardDelete()
		private function _view_delete_post() {
			// check grant
			if(!$this->grant->write_post) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');

			// if post exists, get the post information
			if($n_post_id) {
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($n_post_id);
				unset($o_post_model);
			}

			// if the post is not existed, then back to the board content page
			if(!isset($o_post) || !$o_post->is_exists()) {
				return $this->_disp_content();
			}

			// if the post is not granted, then back to the password input form
			if(!$o_post->is_granted()) {
				// return $this->setTemplateFile('input_password_form');
				echo $this->render_skin_file('input_password_form');
				return;
			}

			if($this->module_info->protect_content=="Y" && $o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return $this->_disp_message('msg_protect_content');
			}

			\X2board\Includes\Classes\Context::set('oPost', $o_post);

			/**
			 * add JS filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'delete_document.xml');
			// $this->setTemplateFile('delete_form');
			echo $this->render_skin_file('delete_form');
		}

		/**
		 * @brief display comment replies page
		 **/
		// function dispBoardReplyComment()
		private function _view_reply_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the parent comment ID
			$parent_comment_id = \X2board\Includes\Classes\Context::get('comment_id');
			// if the parent comment is not existed
			if(!$parent_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', 'x2board') );
			}

			// get the comment
			$o_comment_model = \X2board\Includes\getModel('comment');
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
			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($o_source_comment->get('parent_post_id'));
			unset($o_post_model);
			if(!$o_post->allow_comment()) {
				unset($o_post);
				return $this->_disp_message('msg_not_allow_comment');
			}

			// get the comment information
			$o_child_comment = $o_comment_model->get_comment();
// var_dump($o_child_comment);
			unset($o_comment_model);
			$o_child_comment->add('board_id', $o_post->get('board_id'));
			unset($o_post);
			$o_child_comment->add('parent_post_id', $o_source_comment->get('parent_post_id'));
			$o_child_comment->add('parent_comment_id', $parent_comment_id);
			\X2board\Includes\Classes\Context::set('o_the_comment', $o_child_comment);

			// setup comment variables
			\X2board\Includes\Classes\Context::set('o_source_comment', $o_source_comment);
			unset($o_source_comment);

			// setup module variables
			// \X2board\Includes\Classes\Context::set('module_info', $this->module_info);

			// $o_editor_view = \X2board\Includes\getView('editor');
			// $o_comment_editor = $o_editor_view->get_comment_editor_html();
			// \X2board\Includes\Classes\Context::set('comment_editor_html', $o_editor_view->ob_get_comment_editor_html() );
			// unset($o_editor_view);
			// \X2board\Includes\Classes\Context::set('comment_hidden_field_html', $this->ob_get_comment_hidden_fields());
		
			/**
			 * add JS filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			echo $this->render_skin_file('comment_form');
			// $this->setTemplateFile('comment_form');
		}

		/**
		 * @brief display the delete comment  form
		 **/
		// function dispBoardDeleteComment()
		public function _view_delete_comment() {
			// check grant
			if(!$this->grant->write_comment) {
				return $this->_disp_message('msg_not_permitted');
			}

			// get the comment_srl to be deleted
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');

			// if the comment exists, then get the comment information
			if($n_comment_id) {
				$o_comment_model = \X2board\Includes\getModel('comment');
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

			/**
			 * add JS filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'delete_comment.xml');

			echo $this->render_skin_file('delete_comment_form');
			// $this->setTemplateFile('delete_comment_form');
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		public function write_post_hidden_fields() {
			$a_header = array();
			$a_header['board_id'] = get_the_ID();
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

			// $this->_n_current_post_id = $a_header['post_id'];
			// }
			// $product_id = isset($_GET['woocommerce_product_tabs_inside']) ? intval($_GET['woocommerce_product_tabs_inside']) : '';
			// if($product_id){
			// 	$header['x2b_option_woocommerce_product_id'] = sprintf('<input type="hidden" name="x2b_option_woocommerce_product_id" value="%d">', $product_id);
			// }
			wp_nonce_field('x2b_'.$a_header['cmd'], 'x2b_'.$a_header['cmd'].'_nonce');
			
			// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			// do_action('x2b_skin_editor_header_after', $content, $board);
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 * @param array $field
		 * @return string
		 */
		// public function getFieldLabel($field){
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
			$a_header['board_id'] = get_the_ID();

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
			// $s_field = ob_get_clean();
			// do_action('x2b_skin_editor_header_after', $content, $board);
			// return apply_filters('x2board_comment_field', $s_field);
		}


	

		
		

		/**
		 * @brief display tag list
		 **/
		function dispBoardTagList()
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




	
/////////////////////////////////////
		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_input_fields()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		// public function getTemplate($field, $content='', $boardBuilder=''){
		/*public function write_post_single_user_field($a_field_info) { 
			$field = $a_field_info;
			$template = '';
			$permission = (isset($field['permission']) && $field['permission']) ? $field['permission'] : '';
			$roles = (isset($field['roles']) && $field['roles']) ? $field['roles'] : '';
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? sanitize_key($field['meta_key']) : '';
			
			// if(!$this->_is_available_user_field($permission, $roles) && $meta_key){
			// 	return;
			// }

			$field_name = strlen($field['field_name']) > 0 ? $field['field_name'] : $this->_get_field_label($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$placeholder = (isset($field['placeholder']) && $field['placeholder']) ? $field['placeholder'] : '';
			$wordpress_search = '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			$html = (isset($field['html']) && $field['html']) ? $field['html'] : '';
			$shortcode = (isset($field['shortcode']) && $field['shortcode']) ? $field['shortcode'] : '';
			
			$has_default_values = false;
			$a_default_value = array();
			if(isset($field['row']) && $field['row']){
				foreach($field['row'] as $item){
					if(isset($item['label']) && $item['label']){
						$has_default_values = true;
						if(isset($item['default_value']) && $item['default_value']){
							$a_default_value[] = $item['label'];
						}
					}
				}
			}
			if($a_default_value){
				$default_value = $a_default_value;
			}
			
			// if($field['field_type'] == 'content'){
// var_dump($this->module_info);	
				// $o_editor_conf = new \stdClass();
				// $o_editor_conf->s_editor_type = $this->module_info->post_editor_skin; //'textarea';
				// $o_editor_conf->s_required = $required;
				// $o_editor_conf->s_placeholder = $placeholder;
				// $o_editor_conf->n_editor_height = $this->module_info->post_editor_height; //400;
				// $o_editor_conf->s_content_field_name = 'content';

				// $o_post = \X2board\Includes\Classes\Context::get('post');
				// $o_editor_conf->s_content = $o_post->content;
				// unset($o_post);

				// $o_editor_view = \X2board\Includes\getView('editor');
				// $editor_html = $o_editor_view->get_post_editor_html($this->_n_current_post_id, $placeholder);//$o_editor_conf);
				// unset($o_editor_view);
				// unset($o_editor_conf);
			// }
			// elseif($field['field_type'] == 'attach') {
			// 	$o_module_info = $this->module_info;
			// 	$s_accept_file_types = str_replace(" ", "", $o_module_info->file_allowed_filetypes);
			// 	$s_accept_file_types = str_replace(",", "|", $s_accept_file_types);
			// 	$n_file_max_attached_count = intval($o_module_info->file_max_attached_count);
			// 	$n_file_allowed_filesize_mb = intval($o_module_info->file_allowed_filesize_mb);
			// 	unset($o_module_info);
			// 	wp_enqueue_style("x2board-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload.css', [], X2B_VERSION);
			// 	wp_enqueue_style("x2board-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload-ui.css', [], X2B_VERSION);
			// 	wp_enqueue_script('x2board-jquery-ui-widget', X2B_URL . '/assets/jquery.fileupload/js/vendor/jquery.ui.widget.js', ['jquery'], X2B_VERSION, true);
			// 	wp_enqueue_script('x2board-jquery-iframe-transport', X2B_URL . '/assets/jquery.fileupload/js/jquery.iframe-transport.js', ['jquery'], X2B_VERSION, true);
			// 	wp_enqueue_script('x2board-fileupload', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload.js', ['jquery'], X2B_VERSION, true);
			// 	wp_enqueue_script('x2board-fileupload-process', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload-process.js', ['jquery'], X2B_VERSION, true);
			// 	wp_enqueue_script('x2board-fileupload-caller', X2B_URL . '/assets/jquery.fileupload/file-upload.js', ['jquery'], X2B_VERSION, true);
			// }

			$post = \X2board\Includes\Classes\Context::get('post');	
			
			// if($field['field_type'] == 'search'){
			// 	if($content->search){
			// 		$wordpress_search = $content->search;
			// 	}
			// 	else if(isset($field['default_value']) && $field['default_value']){
			// 		$wordpress_search = $field['default_value'];
			// 	}
			// }
			
			// $order = new KBOrder();
			// $order->board = $this->board;
			// $order->board_id = $this->board->id;
			
			// $url = new KBUrl();
			// $url->setBoard($this->board);
			
			// $skin = KBoardSkin::getInstance();
			
			// if(!$boardBuilder){
			// 	$boardBuilder = new KBoardBuilder($this->board->id);
			// 	$boardBuilder->setSkin($this->board->skin);
			// 	if(wp_is_mobile() && $this->board->meta->mobile_page_rpp){
			// 		$builder->setRpp($this->board->meta->mobile_page_rpp);
			// 	}
			// 	else{
			// 		$boardBuilder->setRpp($this->board->page_rpp);
			// 	}
			// 	$boardBuilder->board = $this->board;
			// }
			// var_dump($this->board->skin)			;
			// if(strpos($html, '#{ESC_ATTR_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_attr($content->option->{$meta_key}) : esc_attr($default_value);
			// 	$html = str_replace('#{ESC_ATTR_VALUE}', $value, $html);
			// }
			// if(strpos($html, '#{ESC_TEXTAREA_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_textarea($content->option->{$meta_key}) : esc_textarea($default_value);
			// 	$html = str_replace('#{ESC_TEXTAREA_VALUE}', $value, $html);
			// }
			// if(strpos($html, '#{ESC_HTML_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_html($content->option->{$meta_key}) : esc_html($default_value);
			// 	$html = str_replace('#{ESC_HTML_VALUE}', $value, $html);
			// }
			
			// $parent = new KBContent();
			// $parent->initWithUID($content->parent_uid);
			
			// $vars = array(
			// 	'field' => $field,
			// 	'meta_key' => $meta_key,
			// 	'field_name' => $field_name,
			// 	'required' => $required,
			// 	'placeholder' => $placeholder,
			// 	'row' => $row,
			// 	'wordpress_search' => $wordpress_search,
			// 	'default_value' => $default_value,
			// 	'html' => $html,
			// 	'shortcode' => $shortcode,
			// 	'board' => $this->board,
			// 	'content' => $content,
			// 	'parent' => $parent,
			// 	// 'fields' => $this,
			// 	// 'order' => $order,
			// 	'url' => $url,
			// 	'skin' => $skin,
			// 	'skin_path' => $skin->url($this->board->skin),
			// 	'skin_dir' => $skin->dir($this->board->skin),
			// 	'boardBuilder' => $boardBuilder
			// );
			
			// ob_start();
			
			// do_action('kboard_skin_field_before', $field, $content, $this->board);
			// do_action("kboard_skin_field_before_{$meta_key}", $field, $content, $this->board);

			// if($skin->fileExists($this->board->skin, "editor-field-{$meta_key}.php")){
			// 	$field_html = $skin->load($this->board->skin, "editor-field-{$meta_key}.php", $vars);
			// }
			// else{
				// $field_html = $this->render($this->board->skin, 'editor-fields.php', $vars);
				// $field_html = $this->render('sketchbook5', 'editor-fields.php');
			// }
			
			// if(!$field_html){
			// 	$field_html = $skin->loadTemplate('editor-fields.php', $vars);
			// }
			
			// $skin_name = 'sketchbook5';
			// $file = 'editor-fields.php';
			// $current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
			// $current_file_path = apply_filters('kboard_skin_file_path', $current_file_path, $skin_name, $file);  //, $vars, $this);

			// if($current_file_path && file_exists($current_file_path)){
			// 	include $current_file_path;
			// }
			// else{
			// 	echo sprintf(__('%s file does not exist.', 'x2board'), $file);
			// }

			$s_skin_path = \X2board\Includes\Classes\Context::get('skin_path_abs'); // this sets on board.view.php::init()
			$s_skin_file_abs_path = $s_skin_path . '/editor-fields.php';
			if( !file_exists( $s_skin_file_abs_path ) ) {
				echo sprintf(__('%s file does not exist.', 'x2board'), $s_skin_file_abs_path);
			}
			include $s_skin_file_abs_path;
		}*/

		/**
		 * @brief  display the document file list (can be used by API)
		 **/
		// function dispBoardContentFileList(){
		// 	/**
		// 	 * check the access grant (all the grant has been set by the module object)
		// 	 **/
		// 	if(!$this->grant->access)
		// 	{
		// 		return $this->_disp_message('msg_not_permitted');
		// 	}

		// 	// check document view grant
		// 	$this->dispBoardContentView();

		// 	// Check if a permission for file download is granted
		// 	// Get configurations (using module model object)
		// 	$oModuleModel = getModel('module');
		// 	$file_module_config = $oModuleModel->getModulePartConfig('file',$this->module_srl);
			
		// 	$downloadGrantCount = 0;
		// 	if(is_array($file_module_config->download_grant))
		// 	{
		// 		foreach($file_module_config->download_grant AS $value)
		// 			if($value) $downloadGrantCount++;
		// 	}

		// 	if(is_array($file_module_config->download_grant) && $downloadGrantCount>0)
		// 	{
		// 		if(!Context::get('is_logged')) return $this->stop('msg_not_permitted_download');
		// 		$logged_info = Context::get('logged_info');
		// 		if($logged_info->is_admin != 'Y')
		// 		{
		// 			$oModuleModel =& getModel('module');
		// 			$columnList = array('module_srl', 'site_srl');
		// 			$module_info = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl, $columnList);

		// 			if(!$oModuleModel->isSiteAdmin($logged_info, $module_info->site_srl))
		// 			{
		// 				$oMemberModel =& getModel('member');
		// 				$member_groups = $oMemberModel->getMemberGroups($logged_info->member_srl, $module_info->site_srl);

		// 				$is_permitted = false;
		// 				for($i=0;$i<count($file_module_config->download_grant);$i++)
		// 				{
		// 					$group_srl = $file_module_config->download_grant[$i];
		// 					if($member_groups[$group_srl])
		// 					{
		// 						$is_permitted = true;
		// 						break;
		// 					}
		// 				}
		// 				if(!$is_permitted) return $this->stop('msg_not_permitted_download');
		// 			}
		// 		}
		// 	}

		// 	$oDocumentModel = getModel('document');
		// 	$document_srl = Context::get('document_srl');
		// 	$oDocument = $oDocumentModel->getDocument($document_srl);
		// 	Context::set('oDocument', $oDocument);
		// 	Context::set('file_list',$oDocument->getUploadedFiles());

		// 	$oSecurity = new Security();
		// 	$oSecurity->encodeHTML('file_list..source_filename');
		// }

		/**
		 * @brief display the document comment list (can be used by API)
		 **/
		// function dispBoardContentCommentList(){
		// 	// check document view grant
		// 	$this->dispBoardContentView();

		// 	$oDocumentModel = getModel('document');
		// 	$document_srl = Context::get('document_srl');
		// 	$oDocument = $oDocumentModel->getDocument($document_srl);
		// 	$comment_list = $oDocument->getComments();

		// 	// setup the comment list
		// 	if(is_array($comment_list))
		// 	{
		// 		foreach($comment_list as $key => $val)
		// 		{
		// 			if(!$val->isAccessible())
		// 			{
		// 				$val->add('content',Context::getLang('thisissecret'));
		// 			}
		// 		}
		// 	}
		// 	Context::set('comment_list',$comment_list);

		// }

		/**
		 * @brief display the delete trackback form
		 **/
		// function dispBoardDeleteTrackback()
		// {
		// 	$oTrackbackModel = getModel('trackback');

		// 	if(!$oTrackbackModel)
		// 	{
		// 		return;
		// 	}

		// 	// get the trackback_srl
		// 	$trackback_srl = Context::get('trackback_srl');

		// 	// get the trackback data
		// 	$columnList = array('trackback_srl');
		// 	$output = $oTrackbackModel->getTrackback($trackback_srl, $columnList);
		// 	$trackback = $output->data;

		// 	// if no trackback, then display the board content
		// 	if(!$trackback)
		// 	{
		// 		return $this->dispBoardContent();
		// 	}

		// 	//Context::set('trackback',$trackback);	//perhaps trackback variables not use in UI

		// 	/**
		// 	 * add JS filters
		// 	 **/
		// 	Context::addJsFilter($this->module_path.'tpl/filter', 'delete_trackback.xml');

		// 	$this->setTemplateFile('delete_trackback_form');
		// }
	}
}