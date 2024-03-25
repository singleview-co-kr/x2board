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
var_dump('board view init');			
			/**
			 * setup the module general information
			 **/
			// 	if($this->module_info->list_count) {
					$this->list_count = 20; //$this->module_info->list_count;
			// 	}
			// 	if($this->module_info->search_list_count) {
					$this->search_list_count = 20; //$this->module_info->search_list_count;
			// 	}
			// 	if($this->module_info->page_count) {
					$this->page_count = 10; //$this->module_info->page_count;
			// 	}

			$this->except_notice = false; // $this->module_info->except_notice == 'N' ? FALSE : TRUE;

			/**
			 * setup the template path based on the skin
			 * the default skin is default
			 **/
			$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
// var_dump($this->module_path);
			if(!is_dir($template_path)||!$this->module_info->skin) {
				$this->module_info->skin = 'sketchbook5';
				$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			}

			$this->set_skin_path($template_path);

			\X2board\Includes\Classes\Context::set('skin_path', X2B_URL.'includes/modules/board/skins/'.$this->s_skin);

			$s_cmd = \X2board\Includes\Classes\Context::get('cmd');
			switch( $s_cmd ) {
				case X2B_CMD_VIEW_LIST:
				case X2B_CMD_VIEW_POST:
					$this->_disp_content();
				case X2B_CMD_VIEW_WRITE_POST:
					$s_cmd = '_'.$s_cmd;
					$this->$s_cmd();
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
		private function _disp_content() { //_view_list()  // dispBoardContent
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
			// use search options on the template (the search options key has been declared, based on the language selected)
			// foreach($this->search_option as $opt) $search_option[$opt] = Context::getLang($opt);
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
			// Context::set('search_option', $search_option);

			// $oDocumentModel = getModel('document');
			// $statusNameList = $this->_getStatusNameList($oDocumentModel);
			// if(count($statusNameList) > 0)
			// {
			// 	Context::set('status_list', $statusNameList);
			// }

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

			// display the requested post
			$this->_view_post();  // $this->dispBoardContentView();

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
		private function _view_write_post()
		{
var_dump(X2B_CMD_VIEW_WRITE_POST);
			// check grant
			if(!$this->grant->write_post) {
				return $this->_disp_message( __('msg_not_permitted', 'x2board') );
			}

			$o_post_model = \X2board\Includes\getModel('post');

			/**
			 * check if the category option is enabled not not
			 **/
			if($this->module_info->use_category=='Y') {
				// get the user group information
				if(\X2board\Includes\Classes\Context::get('is_logged')) {
					$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
					$a_group_srls = array(); // array_keys($o_logged_info->group_list);
				}
				else {
					$a_group_srls = array();
				}
				// $group_srls_count = count($a_group_srls);

				// check the grant after obtained the category list
				$a_category_list = array();
				$n_normal_category_list = $o_post_model->get_category_list();
				if(count($n_normal_category_list)) {
					foreach($n_normal_category_list as $category_srl => $category) {
						$is_granted = TRUE;
						if($category->group_srls) {
							$category_group_srls = explode(',',$category->group_srls);
							$is_granted = FALSE;
							if(count(array_intersect($a_group_srls, $category_group_srls))) {
								$is_granted = TRUE;
							}
						}
						if($is_granted) {
							$a_category_list[$category_srl] = $category;
						}
					}
				}
				\X2board\Includes\Classes\Context::set('category_list', $a_category_list);
				unset($a_category_list);
			}

			// GET parameter post_id from request
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$o_post = $o_post_model->get_post(0, $this->grant->manager);
			$o_post->set_post($n_post_id);

			// if($oDocument->get('module_srl') == $oDocument->get('member_srl')) {
			if($o_post->get('board_id') == $o_post->get('post_author')) {
				$savedDoc = TRUE;
			}
			// $oDocument->add('module_srl', $this->module_srl);
// var_dump($this->grant->write_post);
			$o_post->add('board_id', \X2board\Includes\Classes\Context::get('board_id') ); // $this->board_id);

			if($o_post->is_exists() && $this->module_info->protect_content=="Y" && $o_post->get('comment_count')>0 && $this->grant->manager==false) {
				return new BaseObject(-1, __('msg_protect_content', 'x2board') );
			}

			// if the post is not granted, then back to the password input form
			if($o_post->is_exists()&&!$o_post->is_granted()) {
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
			if($o_post->is_exists() && !$savedDoc) {
				\X2board\Includes\Classes\Context::set('extra_keys', $o_post->get_extra_vars());
			}
			
			/**
			 * add JS filters
			 **/
			// if(Context::get('logged_info')->is_admin=='Y') Context::addJsFilter($this->module_path.'tpl/filter', 'insert_admin.xml');
			// else Context::addJsFilter($this->module_path.'tpl/filter', 'insert.xml');

			// $oSecurity = new Security();
			// $oSecurity->encodeHTML('category_list.text', 'category_list.title');

			\X2board\Includes\Classes\Context::set('post', $o_post);
			unset($o_post);

			$o_post_model = \X2board\Includes\getModel('post');
			$a_user_input_field = $o_post_model->get_user_input_fields();
// var_dump($a_user_input_field);
			unset($o_post_model);
			\X2board\Includes\Classes\Context::set('field', $a_user_input_field);

			wp_localize_script('x2board-script', 'kboard_current', array(
				'board_id'          => \X2board\Includes\Classes\Context::get('board_id'), // $this->board_id,
				'content_uid'       => \X2board\Includes\Classes\Context::get('post_id'),
				'tree_category'     => '',//unserialize($this->meta->tree_category),
				'mod'               => \X2board\Includes\Classes\Context::get('cmd'), //$this->mod,
				'use_editor' => ''  //$this->board->use_editor,
			));

			// setup the skin file
			echo $this->render_skin_file('editor');
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
					$this->alertMessage( __('msg_not_founded', 'x2board') );
				}
			}
			else {  // if the post is not existed, get an empty post
				$o_post = $o_post_model->get_post(0);
			}

			if($o_post->is_exists()) {  // check the document view grant
				if(!$this->grant->view && !$oDocument->is_granted()) {
					$o_post = $o_post_model->get_post(0);
					\X2board\Includes\Classes\Context::set('post_id','',true);
					$this->alertMessage('msg_not_permitted');
				}
				else {
					// add the document title to the browser
					// Context::addBrowserTitle($oDocument->getTitleText());

					// update the post view count (if the post is not secret)
					if(!$o_post->is_secret() || $o_post->is_granted()) {
						$o_post->update_readed_count();
					}

					$a_post_list = \X2board\Includes\Classes\Context::get('post_list');
					foreach( $a_post_list as $no => $o_post ) {
						if( $n_post_id == $o_post->post_id ) {
							\X2board\Includes\Classes\Context::set('cur_post_pos_in_list', $no);
							break;
						}
					} 
					// disappear the post if it is secret
					if($o_post->is_secret() && !$oDo_postocument->is_granted()) {
						$o_post->add( 'content', __('thisissecret', 'x2board') );
					}
				}
			}
			unset($o_post_model);

			// setup the document oject on context
			// $o_post->add('module_srl', $this->module_srl);
			\X2board\Includes\Classes\Context::set('post', $o_post);

			/**
			 * add javascript filters
			 **/
			// Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');
			// return new BaseObject();
			// setup the skin file
			// echo $this->render_skin_file('document');
		}

		/**
		 * @brief display board content list
		 **/
		private function _disp_post_list() {  // dispBoardContentList(){  
			// check the grant
			// if(!$this->grant->list)
			// {
			// 	Context::set('document_list', array());
			// 	Context::set('total_count', 0);
			// 	Context::set('total_page', 1);
			// 	Context::set('page', 1);
			// 	Context::set('page_navigation', new PageHandler(0,0,1,10));
			// 	return;
			// }
			
			$o_post_model = \X2board\Includes\getModel('post');
// var_dump($this->grant );

			// setup module_srl/page number/ list number/ page count
			$o_args = new \stdClass();
			// $o_args->module_srl = $this->module_srl;
			$o_args->wp_page_id = \X2board\Includes\Classes\Context::get('board_id'); //$this->board_id;
			$o_args->page = \X2board\Includes\Classes\Context::get('page');

			// $o_args->list_count = $this->list_count;
			// $o_args->page_count = $this->page_count;

			// get the search target and keyword
			// if($this->grant->view) {
			// 	$args->search_target = Context::get('search_target');
			// 	$args->search_keyword = Context::get('search_keyword');
			// }

			// $search_option = Context::get('search_option');
			// if($search_option==FALSE)
			// {
			// 	$search_option = $this->search_option;
			// }
			// if(isset($search_option[$args->search_target])==FALSE)
			// {
			// 	$args->search_target = '';
			// }

			// if the category is enabled, then get the category
			// if($this->module_info->use_category=='Y')
			// {
			// 	$args->category_srl = Context::get('category');
			// }

			// setup the sort index and order index
			// $args->sort_index = Context::get('sort_index');
			// $args->order_type = Context::get('order_type');
			// if(!in_array($args->sort_index, $this->order_target))
			// {
			// 	$args->sort_index = $this->module_info->order_target?$this->module_info->order_target:'list_order';
			// }
			// if(!in_array($args->order_type, array('asc','desc')))
			// {
			// 	$args->order_type = $this->module_info->order_type?$this->module_info->order_type:'asc';
			// }

			// set the current page of documents
			// $document_srl = Context::get('document_srl');
			$post_id = \X2board\Includes\Classes\Context::get('post_id');  //$g_a_x2b_query_param['post_id'];
			if(!$o_args->page && $post_id)
			{
				$o_post = $o_post_model->get_post($post_id);
				if($o_post->isExists() && !$o_post->isNotice())
				{
					$page = $o_post_model->getDocumentPage($o_post, $o_args);
					\X2board\Includes\Classes\Context::set('page', $page);
					$o_args->page = $page;
				}
			}

			// setup the list count to be serach list count, if the category or search keyword has been set
			// if($args->category_srl || $args->search_keyword)
			// {
			// 	$args->list_count = $this->search_list_count;
			// }

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
			// setup document list variables on context
			$output = $o_post_model->get_post_list($o_args, $this->except_notice);  //, TRUE, $this->columnList);
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
// var_dump($this->module_info);	
			if($this->module_info->use_category=='Y')  // check if the use_category option is enabled
			{
				if(!$this->grant->list) { // check the grant
					\X2board\Includes\Classes\Context::set('category_list', array());
					return;
				}
				$o_post_model = \X2board\Includes\getModel('post');
				\X2board\Includes\Classes\Context::set('category_type', $o_post_model->get_category_header_type()); //$this->module_srl));
				unset($o_post_model);
				// $oSecurity = new Security();
				// $oSecurity->encodeHTML('category_list.', 'category_list.childs.');
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
					'regdate', 'last_update', 'last_updater', 'ipaddress', 'list_order', 'update_order',
					'allow_trackback', 'notify_message', 'status', 'comment_status');
			$this->columnList = array_intersect($configColumList, $tableColumnList);

			if(in_array('summary', $configColumList)) array_push($this->columnList, 'content');

			// default column list add
			$defaultColumn = array('document_srl', 'module_srl', 'category_srl', 'lang_code', 'member_srl', 'last_update', 'comment_count', 'trackback_count', 'uploaded_count', 'status', 'regdate', 'title_bold', 'title_color');

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
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		public function write_post_hidden_fields() { 
			wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_POST, 'x2b_'.X2B_CMD_PROC_WRITE_POST.'_nonce');
			// if($o_post->post_id) {
			// 	wp_nonce_field("x2b_".X2B_CMD_PROC_MODIFY_POST."_".$o_post->post_id, 'x2b_'.X2B_CMD_PROC_MODIFY_POST.'_nonce');
			// }
			
			// do_action('x2b_skin_editor_header_before', $content, $board);
			$o_post = \X2board\Includes\Classes\Context::get('post');
			$header = array();
			$a_header['cmd'] = "proc_write_post"; // '<input type="hidden" name="action" value="x2b_write_post">';
			$a_header['board_id'] = get_the_ID(); // sprintf('<input type="hidden" name="board_id" value="%d">', $o_post->board_id);
			// $a_header['mod'] = "editor"; // '<input type="hidden" name="mod" value="editor">';
			if($o_post->post_id) {
				$a_header['post_id'] = $o_post->post_id; // sprintf('<input type="hidden" name="post_id" value="%d">', $o_post->post_id);
				$a_header['parent_post_id'] = $o_post->parent_post_id; // sprintf('<input type="hidden" name="parent_post_id" value="%d">', $o_post->parent_post_id);
			}
			
			// $a_header['post_author'] = $o_post->post_author; // sprintf('<input type="hidden" name="post_author" value="%d">', $o_post->post_author);
			// $a_header['nick_name'] = $o_post->nick_name; // sprintf('<input type="hidden" name="member_display" value="%s">', $o_post->nick_name);
			// $a_header['regdate'] = $o_post->regdate; // sprintf('<input type="hidden" name="date" value="%s">', $o_post->regdate);
			// $a_header['user_id'] = get_current_user_id(); // sprintf('<input type="hidden" name="user_id" value="%d">', get_current_user_id());
			unset($o_post);
			// $product_id = isset($_GET['woocommerce_product_tabs_inside']) ? intval($_GET['woocommerce_product_tabs_inside']) : '';
			// if($product_id){
			// 	$header['x2b_option_woocommerce_product_id'] = sprintf('<input type="hidden" name="x2b_option_woocommerce_product_id" value="%d">', $product_id);
			// }
			
			// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			// do_action('x2b_skin_editor_header_after', $content, $board);
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_prepare_single_user_field()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		public function write_post_prepare_single_user_field() { 
			$o_post_model = \X2board\Includes\getModel('post');
			$this->_default_fields = $o_post_model->default_fields;  // get_default_user_input_fields();
			$this->_extends_fields = $o_post_model->extends_fields;  // get_extended_user_input_fields();
			unset($o_post_model);
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_single_user_field()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		// public function getTemplate($field, $content='', $boardBuilder=''){
		public function write_post_single_user_field($a_field_info) { 
			$field = $a_field_info;
			$template = '';
			$permission = (isset($field['permission']) && $field['permission']) ? $field['permission'] : '';
			$roles = (isset($field['roles']) && $field['roles']) ? $field['roles'] : '';
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? sanitize_key($field['meta_key']) : '';
			
			if(!$this->_is_available_user_field($permission, $roles) && $meta_key){
				return;
			}
			// if(!$content){
				// $content = new KBContent();
			// }

			$field = apply_filters('kboard_get_template_field_data', $field); //, $content, $this->board);

			$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $this->_get_field_label($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$placeholder = (isset($field['placeholder']) && $field['placeholder']) ? $field['placeholder'] : '';
			$wordpress_search = '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			$html = (isset($field['html']) && $field['html']) ? $field['html'] : '';
			$shortcode = (isset($field['shortcode']) && $field['shortcode']) ? $field['shortcode'] : '';
			$row = false;

			$content = new \stdClass();
			$content->title = '';
			$content->nick_name = '';
			$content->content = '';
			$content->getCategoryList = array();
			$content->search = false;
			
			$board = new \stdClass(); 
			$board->viewUsernameField = true;
			$board->useCAPTCHA = false;
			$board->use_editor = '';
			
			if($field['field_type'] == 'content'){
				$board->editor_html = $this->get_editor_html(array(
										'board' => $board,
										'content' => $content,
										'required' => $required,
										'placeholder' => $placeholder,
										'editor_height' => '400',
									));
			}
			
			$default_value_list = array();
			if(isset($field['row']) && $field['row']){
				foreach($field['row'] as $item){
					if(isset($item['label']) && $item['label']){
						$row = true;
						if(isset($item['default_value']) && $item['default_value']){
							$default_value_list[] = $item['label'];
						}
					}
				}
			}
			
			if($default_value_list){
				$default_value = $default_value_list;
			}
			
			if($field['field_type'] == 'search'){
				if($content->search){
					$wordpress_search = $content->search;
				}
				else if(isset($field['default_value']) && $field['default_value']){
					$wordpress_search = $field['default_value'];
				}
			}
			
			
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
			if(strpos($html, '#{ESC_ATTR_VALUE}') !== false){
				$value = $content->option->{$meta_key} ? esc_attr($content->option->{$meta_key}) : esc_attr($default_value);
				$html = str_replace('#{ESC_ATTR_VALUE}', $value, $html);
			}
			if(strpos($html, '#{ESC_TEXTAREA_VALUE}') !== false){
				$value = $content->option->{$meta_key} ? esc_textarea($content->option->{$meta_key}) : esc_textarea($default_value);
				$html = str_replace('#{ESC_TEXTAREA_VALUE}', $value, $html);
			}
			if(strpos($html, '#{ESC_HTML_VALUE}') !== false){
				$value = $content->option->{$meta_key} ? esc_html($content->option->{$meta_key}) : esc_html($default_value);
				$html = str_replace('#{ESC_HTML_VALUE}', $value, $html);
			}
			
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

			// refer to the \Includes\Classes\ModuleObject::render_skin_file()
			$s_skin_file_abs_path = $this->skin_path . 'editor-fields.php';
			if( !file_exists( $s_skin_file_abs_path ) ) {
				echo sprintf(__('%s file does not exist.', 'x2board'), $s_skin_file_abs_path);
			}
			$s_skin_file_abs_path = apply_filters('kboard_skin_file_path', $s_skin_file_abs_path ); // , $skin_name, $file, $vars, $this);
			include $s_skin_file_abs_path;
		}

		/**
		 * 게시글 본문 에디터 코드를 반환한다.
		 * @param array $vars
		 * @return string
		 */
		// kboard_content_editor()
		public function get_editor_html($vars=array()){
			$vars = array_merge(array(
				'required' => '',
				'placeholder' => '',
				'editor_height' => '400',
			), $vars);
			
			// $vars = apply_filters('kboard_content_editor_vars', $vars);
			extract($vars, EXTR_SKIP);
			
			ob_start();
			if($board->use_editor == 'yes'){
				wp_editor($content->content, 'content', array('editor_height'=>$editor_height));
			}
			else{
				echo sprintf('<textarea id="content" class="editor-textarea %s" name="content" placeholder="%s">%s</textarea>', esc_attr($required), esc_attr($placeholder), esc_textarea($content->content));
			}
			$s_editor_html = ob_get_clean();
			return apply_filters('x2board_content_editor', $s_editor_html); //, $vars);
		}

		/**
		 * 입력 필드를 사용할 수 있는 권한인지 확인한다.
		 * @param string $name
		 * @return boolean
		 */
		// public function isUseFields($permission, $roles){
		private function _is_available_user_field($permission, $roles) {
			// $board = $this->board;
			// if($board->isAdmin()){
			// 	return true;
			// }
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
	// var_dump($o_logged_info->roles);		
			if($o_logged_info->is_admin == 'Y') {
				return true;
			}
			switch($permission){
				case 'all': 
					return true;
				case 'author': 
					return is_user_logged_in() ? true : false;
				case 'roles':
					if(is_user_logged_in()){
						if(array_intersect($roles, (array)$o_logged_info->roles)){
							return true;
						}
					}
					return false;
				default: 
					return true;
			}
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 * @param array $field
		 * @return string
		 */
		// public function getFieldLabel($field){
		private function _get_field_label($a_field){
			$field = $a_field;
			$field_type = $field['field_type'];
			
			$fields = apply_filters('kboard_admin_default_fields', $this->_default_fields); //, $this->board);
			if(isset($fields[$field_type])){
				return $fields[$field_type]['field_label'];
			}
			
			$fields = apply_filters('kboard_admin_extends_fields', $this->_extends_fields); //, $this->board);
			if(isset($fields[$field_type])){
				return $fields[$field_type]['field_label'];
			}
			
			return $field['field_label'];
		}

		// function _getStatusNameList(&$oDocumentModel)
		private function _get_status_name_list($o_post_model)
		{
			$resultList = array();
			if(!empty($this->module_info->use_status)) {
				$statusNameList = $o_post_model->getStatusNameList();
				$statusList = explode('|@|', $this->module_info->use_status);
				if(is_array($statusList)) {
					foreach($statusList as $key => $value) {
						$resultList[$value] = $statusNameList[$value];
					}
				}
			}
			return $resultList;
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
		 * @brief display the document comment list (can be used by API)
		 **/
		function dispBoardContentCommentList(){
			// check document view grant
			$this->dispBoardContentView();

			$oDocumentModel = getModel('document');
			$document_srl = Context::get('document_srl');
			$oDocument = $oDocumentModel->getDocument($document_srl);
			$comment_list = $oDocument->getComments();

			// setup the comment list
			if(is_array($comment_list))
			{
				foreach($comment_list as $key => $val)
				{
					if(!$val->isAccessible())
					{
						$val->add('content',Context::getLang('thisissecret'));
					}
				}
			}
			Context::set('comment_list',$comment_list);

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
		 * @brief display board module deletion form
		 **/
		function dispBoardDelete()
		{
			// check grant
			if(!$this->grant->write_document)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// get the document_srl from request
			$document_srl = Context::get('document_srl');

			// if document exists, get the document information
			if($document_srl)
			{
				$oDocumentModel = getModel('document');
				$oDocument = $oDocumentModel->getDocument($document_srl);
			}

			// if the document is not existed, then back to the board content page
			if(!$oDocument || !$oDocument->isExists())
			{
				return $this->dispBoardContent();
			}

			// if the document is not granted, then back to the password input form
			if(!$oDocument->isGranted())
			{
				return $this->setTemplateFile('input_password_form');
			}

			if($this->module_info->protect_content=="Y" && $oDocument->get('comment_count')>0 && $this->grant->manager==false)
			{
				return $this->_disp_message('msg_protect_content');
			}

			Context::set('oDocument',$oDocument);

			/**
			 * add JS filters
			 **/
			Context::addJsFilter($this->module_path.'tpl/filter', 'delete_document.xml');

			$this->setTemplateFile('delete_form');
		}

		/**
		 * @brief display comment wirte form
		 **/
		function dispBoardWriteComment()
		{
			$document_srl = Context::get('document_srl');

			// check grant
			if(!$this->grant->write_comment)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// get the document information
			$oDocumentModel = getModel('document');
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists())
			{
				return $this->_disp_message('msg_invalid_request');
			}

			// Check allow comment
			if(!$oDocument->allowComment())
			{
				return $this->_disp_message('msg_not_allow_comment');
			}

			// obtain the comment (create an empty comment document for comment_form usage)
			$oCommentModel = getModel('comment');
			$oSourceComment = $oComment = $oCommentModel->getComment(0);
			$oComment->add('document_srl', $document_srl);
			$oComment->add('module_srl', $this->module_srl);

			// setup document variables on context
			Context::set('oDocument',$oDocument);
			Context::set('oSourceComment',$oSourceComment);
			Context::set('oComment',$oComment);

			/**
			 * add JS filter
			 **/
			Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

			$this->setTemplateFile('comment_form');
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
		 * @brief display the comment modification from
		 **/
		function dispBoardModifyComment()
		{
			// check grant
			if(!$this->grant->write_comment)
			{
				return $this->_disp_message('msg_not_permitted');
			}

			// get the document_srl and comment_srl
			$document_srl = Context::get('document_srl');
			$comment_srl = Context::get('comment_srl');

			// if the comment is not existed
			if(!$comment_srl)
			{
				return new BaseObject(-1, 'msg_invalid_request');
			}

			// get comment information
			$oCommentModel = getModel('comment');
			$oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);

			// if the comment is not exited, alert an error message
			if(!$oComment->isExists())
			{
				return $this->_disp_message('msg_invalid_request');
			}

			// if the comment is not granted, then back to the password input form
			if(!$oComment->isGranted())
			{
				return $this->setTemplateFile('input_password_form');
			}

			// setup the comment variables on context
			Context::set('oSourceComment', $oCommentModel->getComment());
			Context::set('oComment', $oComment);

			/**
			 * add JS fitlers
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

		
		/**
		 * @brief the method for displaying the warning messages
		 * display an error message if it has not  a special design
		 **/
		function alertMessage($message)
		{
			$script =  sprintf('<script> jQuery(function(){ alert("%s"); } );</script>', Context::getLang($message));
			Context::addHtmlFooter( $script );
		}
	}
}