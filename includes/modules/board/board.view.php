<?php
/**
 * @class  boardView
 * @author singleview.co.kr
 * @brief  board module View class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardView')) {

	class boardView extends board
	{
		var $listConfig;
		var $columnList;

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
			\X2board\Includes\Classes\Context::set('skin_url', X2B_URL.'includes/modules/board/skins/'.$this->module_info->skin);

			$s_cmd = \X2board\Includes\Classes\Context::get('cmd');
			switch( $s_cmd ) {
				case X2B_CMD_VIEW_LIST:
				case X2B_CMD_VIEW_POST:
					$this->_disp_content();
				case X2B_CMD_VIEW_WRITE_POST:
				case X2B_CMD_VIEW_MODIFY_POST:
				case X2B_CMD_VIEW_MODIFY_COMMENT:
				case X2B_CMD_VIEW_DELETE_POST:
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
		 * @brief display board message
		 **/
		// function dispBoardMessage($s_msg) {
		private function _disp_message($s_msg) {
			\X2board\Includes\Classes\Context::set('message', $s_msg);
			// setup the skin file
			echo $this->render_skin_file('message');
		}

		/**
		 * @brief display board contents
		 **/
		private function _disp_content() { // dispBoardContent
var_dump(X2B_CMD_VIEW_LIST);
			/**
			 * check the access grant (all the grant has been set by the module object)
			 **/
			// if(!$this->grant->access || !$this->grant->list)
			// {
			// 	return $this->_disp_message('msg_not_permitted');
			// }

			/**
			 * display the category list, and then setup the category list on context
			 **/
			$this->_disp_category_list();

			/**
			 * display the search options on the screen
			 * add extra vaiables to the search options
			 **/
			$a_search_option_lang_ko = array( "title_content" => "제목+내용", "title" => "제목",
										   "content" => "내용", "comment" => "댓글", "user_name" => "이름",
										   "user_id" => "아이디", "nick_name" => "닉네임", "tag" => "태그" ); 
			// use search options on the template (the search options key has been declared, based on the language selected)
			foreach($this->a_search_option as $opt) {
				$search_option[$opt] = $a_search_option_lang_ko[$opt];//Context::getLang($opt);
			}
			unset($a_search_option_lang_ko);
// var_dump($search_option);	
			// $extra_keys = Context::get('extra_keys');
			// if($extra_keys)
			// {
			// 	foreach($extra_keys as $key => $val)
			// 	{
			// 		if($val->search == 'Y') $search_option['extra_vars'.$val->idx] = $val->name;
			// 	}
			// }
			// remove a search option that is not public in member config
			// $memberConfig = getModel('module')->getModuleConfig('member');
			// foreach($memberConfig->signupForm as $signupFormElement)
			// {
			// 	if(in_array($signupFormElement->title, $search_option))
			// 	{
			// 		if($signupFormElement->isPublic == 'N')
			// 			unset($search_option[$signupFormElement->name]);
			// 	}
			// }
			\X2board\Includes\Classes\Context::set('search_option', $search_option);

			// $oDocumentModel = getModel('document');
			// $statusNameList = $this->_getStatusNameList($oDocumentModel);
			// if(count($statusNameList) > 0)
			// {
			// 	Context::set('status_list', $statusNameList);
			// }

			// display the requested post
			$this->_view_post();  // $this->dispBoardContentView();

			// list config, columnList setting
			// $oBoardModel = getModel('board');
			// $this->listConfig = $oBoardModel->getListConfig($this->module_info->module_srl);
			if(!$this->listConfig) {
				$this->listConfig = array();
			}
			$this->_makeListColumnList();

			// time translation for \X2board\Includes\zdate()
			$unit_week = array( "Monday"=> "월", "Tuesday" => "화", "Wednesday" => "수", "Thursday" => "목", "Friday" => "금", "Saturday" => "토", "Sunday" =>"일" );
			\X2board\Includes\Classes\Context::set( 'unit_week', $unit_week );
			$unit_week = array( "am"=> "오전", "pm" => "오후", "AM" => "오전", "PM" => "오후" );
			\X2board\Includes\Classes\Context::set( 'unit_meridiem', $unit_week );
			
			// display the notice list
			$output = $this->_disp_notice_list();
			// Return if no result or an error occurs
			if(!$output->toBool()) {
				return $this->_disp_message($output->getMessage());
			}

			// display the post list
			$output = $this->_disp_post_list();
			// Return if no result or an error occurs
			if(!$output->toBool()) {
				return $this->_disp_message($output->getMessage());
			}

			/**
			 * add javascript filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'search.xml');
			
			// setup the skin file
			echo $this->render_skin_file('list');
		}

		/**
		 * @brief display post write form
		 **/
		private function _view_modify_post() {
			$this->_view_write_post();
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
					// if the consultation function is enabled, and the document is not a notice
					if($this->consultation && !$o_post->is_notice()) {
						$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
// var_dump($o_logged_info->ID);
						if(abs($o_post->get('post_author')) != $o_logged_info->ID) {
							$o_post = $o_post_model->get_post(0);
						}
						unset($o_logged_info);
					}

					// if the document is TEMP saved, check Grant
					if($o_post->get_status() == 'TEMP') {
						if(!$o_post->is_granted()) {
							$o_post = $o_post_model->get_post(0);
						}
					}

				}
				else { // if the post is not existed, then alert a warning message					
					\X2board\Includes\Classes\Context::set( 'post_id', '', true );
					$this->_alert_message( __('msg_not_founded', 'x2board') );
				}
			}
			else {  // if the post is not existed, get an empty post
				$o_post = $o_post_model->get_post(0);
			}

			if($o_post->is_exists()) {  // check the document view grant
				if(!$this->grant->view && !$oDocument->is_granted()) {
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

			// setup the document oject on context
			// $o_post->add('module_srl', $this->module_srl);
			\X2board\Includes\Classes\Context::set('post', $o_post);

			if($o_post->is_exists()) {  // check comment list; which depends on \X2board\Includes\Classes\Context::get('post');
				$o_editor_view = \X2board\Includes\getView('editor');
				$o_comment_editor = $o_editor_view->get_comment_editor();
				unset($o_editor_view);
				\X2board\Includes\Classes\Context::set('comment_editor_html', $o_comment_editor->s_comment_editor_html );
				\X2board\Includes\Classes\Context::set('comment_hidden_field_html', $o_comment_editor->s_comment_hidden_field_html);
				unset($o_comment_editor);
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
		public function get_config() {
			
			return $this->module_info;
		}

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
			if($this->module_info->use_category=='Y') {
				$o_args->category_id = \X2board\Includes\Classes\Context::get('category');
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
			// set the current page of documents
			// $document_srl = Context::get('document_srl');
			$post_id = \X2board\Includes\Classes\Context::get('post_id');  //$g_a_x2b_query_param['post_id'];
			if(!$o_args->page && $post_id)
			{
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
			// Context::set('list_config', $this->listConfig);
// var_dump($o_args);			
			// setup document list variables on context
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
			if($this->module_info->use_category=='Y') { // check if the use_category option is enabled;  -1 deactivated
				if(!$this->grant->list) { // check the grant
					\X2board\Includes\Classes\Context::set('category_recursive', array());
					return;
				}
				$o_category_model = \X2board\Includes\getModel('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				\X2board\Includes\Classes\Context::set('category_recursive', $o_category_model->get_category_navigation()); // for category tab navigation
				unset($o_category_model);
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

			unset($o_args);
			\X2board\Includes\Classes\Context::set('notice_list', $output->data);	
			return $output;
		}

		private function _makeListColumnList()
		{
			$configColumList = array_keys($this->listConfig);
			$tableColumnList = array('document_srl', 'module_srl', 'category_srl', 'lang_code', 'is_notice',
					'title', 'title_bold', 'title_color', 'content', 'readed_count', 'voted_count',
					'blamed_count', 'comment_count', 'trackback_count', 'uploaded_count', 'password', 'user_id',
					'user_name', 'nick_name', 'member_srl', 'email_address', 'homepage', 'tags', 'extra_vars',
					'regdate_dt', 'last_update_dt', 'last_updater', 'ipaddress', 'list_order', 'update_order',
					'allow_trackback', 'notify_message', 'status', 'comment_status');
			$this->columnList = array_intersect($configColumList, $tableColumnList);

			if(in_array('summary', $configColumList)) array_push($this->columnList, 'content');

			// default column list add
			$defaultColumn = array('document_srl', 'module_srl', 'category_srl', 'lang_code', 'member_srl', 'last_update_dt', 'comment_count', 'trackback_count', 'uploaded_count', 'status', 'regdate_dt', 'title_bold', 'title_color');

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
			foreach($this->columnList as $no => $value)	{
				$this->columnList[$no] = 'post.' . $value;
			}
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
			 * check if the category option is enabled not not
			 **/
			if($this->module_info->use_category=='Y') {
				$o_category_model = \X2board\Includes\getModel('category');
				$o_category_model->set_board_id(\X2board\Includes\Classes\Context::get('board_id'));
				$a_linear_category = $o_category_model->build_linear_category();
// var_dump($normal_category_list);
				unset($o_category_model);
				\X2board\Includes\Classes\Context::set('category_list', $a_linear_category);
				unset($a_linear_category);
			}

			// GET parameter post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post(0, $this->grant->manager);
			$o_post->set_post($n_post_id);

			// if($oDocument->get('module_srl') == $oDocument->get('member_srl')) {
// var_dump($o_post->get('board_id'));
// var_dump($o_post->get('post_author'));
			if($o_post->get('board_id') == $o_post->get('post_author')) {
				$savedDoc = TRUE;
			}
			else {
				$savedDoc = FALSE;
			}
			// $oDocument->add('module_srl', $this->module_srl);
// var_dump($this->grant->write_post);
			$o_post->add('board_id', \X2board\Includes\Classes\Context::get('board_id') ); // $this->board_id);

			if($o_post->is_exists() && $this->module_info->protect_content=="Y" && 
				$o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_protect_content', 'x2board') );
			}

			// if the post is not granted, then back to the password input form
			if($o_post->is_exists() && !$o_post->is_granted()) {
				return $this->setTemplateFile('input_password_form');
			}
// var_dump($o_post->is_granted());
			if(!$o_post->is_exists()) {
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

			$statusList = $this->_get_status_name_list($o_post_model);
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

			// $o_post_model = \X2board\Includes\getModel('post');
			// $a_user_input_field = $o_post_model->get_user_define_fields();
			// \X2board\Includes\Classes\Context::set('field', $a_user_input_field);

			unset($o_post_model);
			
			// begin - for editor.view.php module usage	
			\X2board\Includes\Classes\Context::set('skin_path_abs', $this->skin_path);
			\X2board\Includes\Classes\Context::set('module_info', $this->module_info);
			// end - for editor.view.php module usage	

			// setup the skin file
			echo $this->render_skin_file('editor_post');
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

			// get the document information
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

			// obtain the comment (create an empty comment document for comment_form usage)
			$o_comment_model = \X2board\Includes\getModel('comment');
			$o_source_comment = $o_comment = $o_comment_model->get_comment(0);
			unset($o_comment_model);
			$o_comment->add('parent_post_id', $n_post_id);
			// $oComment->add('module_srl', $this->module_srl);

			// setup document variables on context
			\X2board\Includes\Classes\Context::set('o_post', $o_post);
			\X2board\Includes\Classes\Context::set('o_source_comment',$o_source_comment);
			\X2board\Includes\Classes\Context::set('o_comment',$o_comment);

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
				return $this->setTemplateFile('input_password_form');
			}

			// setup the comment variables on context
			// \X2board\Includes\Classes\Context::set('o_source_comment', $oCommentModel->getComment());
			\X2board\Includes\Classes\Context::set('o_source_comment', $o_source_comment);
			\X2board\Includes\Classes\Context::set('o_comment', $o_comment);

			/**
			 * add JS fitlers
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			// $this->setTemplateFile('comment_form');
			echo $this->render_skin_file('editor_comment');
		}

		// function _getStatusNameList(&$oDocumentModel)
		private function _get_status_name_list($o_post_model) {
			$resultList = array();
			if(!empty($this->module_info->use_status)) {
				$statusNameList = $o_post_model->get_status_name_list();
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

			// if document exists, get the document information
			if($n_post_id) {
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($n_post_id);
				unset($o_post_model);
			}

			// if the post is not existed, then back to the board content page
			if(!isset($o_post) || !$o_post->is_exists()) {
				return $this->dispBoardContent();
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

	
/////////////////////////////////////
		/**
		 * @brief  display the document file list (can be used by API)
		 **/
		function dispBoardContentFileList(){
			/**
			 * check the access grant (all the grant has been set by the module object)
			 **/
			if(!$this->grant->access)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// check document view grant
			$this->dispBoardContentView();

			// Check if a permission for file download is granted
			// Get configurations (using module model object)
			$oModuleModel = getModel('module');
			$file_module_config = $oModuleModel->getModulePartConfig('file',$this->module_srl);
			
			$downloadGrantCount = 0;
			if(is_array($file_module_config->download_grant))
			{
				foreach($file_module_config->download_grant AS $value)
					if($value) $downloadGrantCount++;
			}

			if(is_array($file_module_config->download_grant) && $downloadGrantCount>0)
			{
				if(!Context::get('is_logged')) return $this->stop('msg_not_permitted_download');
				$logged_info = Context::get('logged_info');
				if($logged_info->is_admin != 'Y')
				{
					$oModuleModel =& getModel('module');
					$columnList = array('module_srl', 'site_srl');
					$module_info = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl, $columnList);

					if(!$oModuleModel->isSiteAdmin($logged_info, $module_info->site_srl))
					{
						$oMemberModel =& getModel('member');
						$member_groups = $oMemberModel->getMemberGroups($logged_info->member_srl, $module_info->site_srl);

						$is_permitted = false;
						for($i=0;$i<count($file_module_config->download_grant);$i++)
						{
							$group_srl = $file_module_config->download_grant[$i];
							if($member_groups[$group_srl])
							{
								$is_permitted = true;
								break;
							}
						}
						if(!$is_permitted) return $this->stop('msg_not_permitted_download');
					}
				}
			}

			$oDocumentModel = getModel('document');
			$document_srl = Context::get('document_srl');
			$oDocument = $oDocumentModel->getDocument($document_srl);
			Context::set('oDocument', $oDocument);
			Context::set('file_list',$oDocument->getUploadedFiles());

			$oSecurity = new Security();
			$oSecurity->encodeHTML('file_list..source_filename');
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
		 * @brief display comment replies page
		 **/
		function dispBoardReplyComment()
		{
			// check grant
			if(!$this->grant->write_comment)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// get the parent comment ID
			$parent_srl = Context::get('comment_srl');

			// if the parent comment is not existed
			if(!$parent_srl)
			{
				return new BaseObject(-1, 'msg_invalid_request');
			}

			// get the comment
			$oCommentModel = getModel('comment');
			$oSourceComment = $oCommentModel->getComment($parent_srl, $this->grant->manager);

			// if the comment is not existed, opoup an error message
			if(!$oSourceComment->isExists())
			{
				return $this->_disp_message('msg_invalid_request');
			}
			if(Context::get('document_srl') && $oSourceComment->get('document_srl') != Context::get('document_srl'))
			{
				return $this->_disp_message('msg_invalid_request');
			}

			// Check allow comment
			$oDocumentModel = getModel('document');
			$oDocument = $oDocumentModel->getDocument($oSourceComment->get('document_srl'));
			if(!$oDocument->allowComment())
			{
				return $this->_disp_message('msg_not_allow_comment');
			}

			// get the comment information
			$oComment = $oCommentModel->getComment();
			$oComment->add('parent_srl', $parent_srl);
			$oComment->add('document_srl', $oSourceComment->get('document_srl'));

			// setup comment variables
			Context::set('oSourceComment',$oSourceComment);
			Context::set('oComment',$oComment);
			Context::set('module_srl',$this->module_info->module_srl);

			/**
			 * add JS filters
			 **/
			Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			$this->setTemplateFile('comment_form');
		}

		/**
		 * @brief display the delete comment  form
		 **/
		function dispBoardDeleteComment()
		{
			// check grant
			if(!$this->grant->write_comment)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// get the comment_srl to be deleted
			$comment_srl = Context::get('comment_srl');

			// if the comment exists, then get the comment information
			if($comment_srl)
			{
				$oCommentModel = getModel('comment');
				$oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);
			}

			// if the comment is not existed, then back to the board content page
			if(!$oComment->isExists() )
			{
				return $this->dispBoardContent();
			}

			// if the comment is not granted, then back to the password input form
			if(!$oComment->isGranted())
			{
				return $this->setTemplateFile('input_password_form');
			}

			Context::set('oComment',$oComment);

			/**
			 * add JS filters
			 **/
			Context::addJsFilter($this->module_path.'tpl/filter', 'delete_comment.xml');

			$this->setTemplateFile('delete_comment_form');
		}

		/**
		 * @brief the method for displaying the warning messages
		 * display an error message if it has not  a special design
		 **/
		private function _alert_message($s_message) {
			echo sprintf('<script> jQuery(function(){ alert("%s"); } );</script>', $s_message);
		}

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