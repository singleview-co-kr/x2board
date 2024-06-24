<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postItem class
 * post object
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 * @version 0.1
 */
namespace X2board\Includes\Modules\Post;

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\postItem')) {

	require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
	require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

	class postItem extends \X2board\Includes\Classes\BaseObject	{
		/**
		 * post number
		 * @var int
		 */
		// var $document_srl = 0;
		private $_n_wp_post_id = 0;
		/**
		 * Language code
		 * @var string
		 */
		// var $lang_code = null;
		/**
		 * column list
		 * @var array
		 */
		// var $columnList = array();
		private $_a_columnList = array();
		
		/**
		 * upload file list
		 * @var array
		 */
		// var $uploadedFiles = array();
		private $_a_uploaded_file = array();

		/**
		 * memory for WP_Filesystem_Direct
		 * @var array
		 */
		private $_o_fileSystemDirect = null;

		/**
		 * Constructor
		 * @param int $post_id
		 * @param bool $load_extra_vars
		 * @param array columnList
		 * @return void
		 */
		function __construct($post_id = 0, $load_extra_vars = true, $columnList = array()) {
			$this->_n_wp_post_id = $post_id;
			$this->_a_columnList = $columnList;
			$this->_load_from_db($load_extra_vars);

			if(!isset($_SESSION['x2b_post_management'])) {
				$_SESSION['x2b_post_management'] = array();
			}
		}

		// public function setAttribute($attribute, $load_extra_vars=true) {
		public function set_attr($attribute, $load_extra_vars=true) {
			global $G_X2B_CACHE;
			if(!isset($attribute->post_id)) {
				$this->_n_wp_post_id = null;
				return;
			}
			$this->_n_wp_post_id = $attribute->post_id;
			// $this->lang_code = $attribute->lang_code;
			$this->adds($attribute);

			$o_post_model = \X2board\Includes\getModel('post');
			$s_secret_tag = $o_post_model->get_config_status('secret');
			unset($o_post_model);
			
			// set is_secret as boolean
			if( $this->get('status') == $s_secret_tag ) {
				$this->add('is_secret', true);
			}
			else {
				$this->add('is_secret', false);
			}

			// convert is_notice to boolean
			if( $this->get('is_notice') == 'Y' ) {
				$this->add('is_notice', true);
			}
			else {
				$this->add('is_notice', false);
			}

			// set allow_comment as boolean
			if( $this->get('comment_status') == 'ALLOW' ) {
				$this->add('allow_comment', true);
			}
			else {
				$this->add('allow_comment', false);
			}

			// Tags
			if($this->get('tags')) {
				$tag_list = explode(',', $this->get('tags'));
				$tag_list = array_map('trim', $tag_list);
				$this->add('tag_list', $tag_list);
			}

			// append if any extended user field exists
			$o_post_model = \X2board\Includes\getModel('post');
			$a_extended_user_field = $o_post_model->get_post_user_define_vars_from_DB(array($this->_n_wp_post_id));
			unset($o_post_model);
			foreach( $a_extended_user_field as $_ => $o_user_field) {
				$this->add($o_user_field->eid, $o_user_field->value);
			}

			if($this->get('category_id')) {
				$n_category_id = intval($this->get('category_id'));
				$n_board_id = intval($this->get('board_id'));

				$o_category_model = \X2board\Includes\getModel('category');
// var_dump($this->module_info);					
				// \X2board\Includes\Classes\Context::set('category_type', $o_post_model->get_category_header_type()); //$this->module_srl));
				$s_title = $o_category_model->get_category_name($n_board_id, $n_category_id);
// var_dump($s_title);
				unset($o_category_model);
			}
			else {
				$s_title = null;
			}
			$this->add('category_title', $s_title);
// var_dump($this->category_title);
			// $oDocumentModel = getModel('document');
			if($load_extra_vars) {
				$G_X2B_CACHE['POST_LIST'][$attribute->post_id] = $this;
				// $oDocumentModel->setToAllDocumentExtraVars();
			}
			$G_X2B_CACHE['POST_LIST'][$this->_n_wp_post_id] = $this;
		}

		// function getCommentCount() {
		public function get_comment_count() {
			return $this->get('comment_count');
		}

		// function getComments() {
		public function get_comments() {
			if(!$this->get_comment_count()) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			if( !$this->is_granted() && $this->is_secret() ) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			// cpage is a number of comment pages
			/////////////////////////////////////////////////////
			// caution URI key name is [cpage], internally cloned into [%%post_id%%_cpage]
			/////////////////////////////////////////////////////
			$cpageStr = sprintf('%d_cpage', $this->_n_wp_post_id);  // 17_cpage
			$cpage = \X2board\Includes\Classes\Context::get($cpageStr);

			if(!$cpage) {
				$cpage = \X2board\Includes\Classes\Context::get('cpage');
			}

			// Get a list of comments
			$o_comment_model = \X2board\Includes\getModel('comment');
			$output = $o_comment_model->get_comment_list($this->_n_wp_post_id, $cpage); //, $is_admin);
			if(!$output->toBool() || !count($output->data)) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			// Create commentItem object from a comment list
			// If admin priviledge is granted on parent posts, you can read its child posts.
			$accessible = array();
			$comment_list = array();
			foreach($output->data as $key => $val) {
				$oCommentItem = new \X2board\Includes\Modules\Comment\commentItem();
				$oCommentItem->set_attr($val);
				// If permission is granted to the post, you can access it temporarily
				if($oCommentItem->is_granted()) {
					$accessible[$val->comment_id] = true;
				}
				// If the comment is set to private and it belongs child post, it is allowable to read the comment for who has a admin privilege on its parent post
				if($val->parent_comment_id>0 && $val->is_secret == 'Y' && !$oCommentItem->isAccessible() && $accessible[$val->parent_comment_id]===true) {
					$oCommentItem->setAccessible();
				}
				$comment_list[$val->comment_id] = $oCommentItem;
			}
			// Variable setting to be displayed on the skin
			/////////////////////////////////////////////////////
			// caution URI key name is [cpage], internally cloned into [%%post_id%%_cpage]
			/////////////////////////////////////////////////////
			\X2board\Includes\Classes\Context::set($cpageStr, $output->page_navigation->n_cur_page);
			\X2board\Includes\Classes\Context::set('cpage', $output->page_navigation->n_cur_page);
// var_dump($output->page_navigation->n_cur_page);
			if($output->total_page > 1) {
				$this->comment_page_navigation = $output->page_navigation;
			}

			// Call trigger (after)
			// $output = ModuleHandler::triggerCall('document.getComments', 'after', $comment_list);
			return $comment_list;
		}

		public function is_new() {
			$b_new = false;
			if($this->post_id){
				$n_expiration_sec = 86400; // kboard_new_document_notify_time();
				if( $n_expiration_sec > 1 && (current_time('timestamp') - strtotime($this->regdate_dt)) <= $n_expiration_sec ){
					$b_new = true;
				}
			}
			return $b_new;
		}

		// getNickName
		public function get_nick_name() {
			return htmlspecialchars($this->get('nick_name'), ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
		}

		// getRegdate
		public function get_regdate($format = 'Y.m.d H:i:s') {
			$dt_regdate = date_create($this->get('regdate_dt'));
			$s_regdate = date_format($dt_regdate, $format);
			unset($dt_regdate);
			return $s_regdate;
			// return \X2board\Includes\zdate($this->get('regdate_dt'), $format);
		}

		// function setDocument($post_id, $load_extra_vars = true)	{
		public function set_post($post_id, $load_extra_vars = true)	{
			$this->_n_wp_post_id = $post_id;
			$this->_load_from_db($load_extra_vars);
		}
		
		/**
		 * Get data from database, and set the value to postItem object
		 * @param bool $load_extra_vars should be false not to reset $this->_n_wp_post_id for writing a new post case
		 * @return void
		 */
		// function _loadFromDB($load_extra_vars = true) {
		private function _load_from_db($load_extra_vars = true) {
			if(!$this->_n_wp_post_id || !$load_extra_vars) {
				return;
			} 

			$post_item = false;  // $document_item = false;
			// $cache_put = false;
			// $columnList = array();
			// $this->_a_columnList = array();

			// cache controll
			// $oCacheHandler = CacheHandler::getInstance('object');
			// if($oCacheHandler->isSupport())
			// {
			// 	$cache_key = 'document_item:' . getNumberingPath($this->_n_wp_post_id) . $this->_n_wp_post_id;
			// 	$post_item = $oCacheHandler->get($cache_key);
			// 	if($post_item !== false)
			// 	{
			// 		$columnList = array('readed_count', 'voted_count', 'blamed_count', 'comment_count', 'trackback_count');
			// 	}
			// }
			global $wpdb;
			$o_post = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}x2b_posts` WHERE `post_id`={$this->_n_wp_post_id}");
			// $output = executeQuery('document.getDocument', $args, $columnList);
// var_dump($o_post);
			// if($post_item === false) {
				$post_item = $o_post;  //$output->data;

				//insert in cache
				// if($document_item && $oCacheHandler->isSupport())
				// {
				// 	$oCacheHandler->put($cache_key, $document_item);
				// }
			// }
			// else {
			// 	$post_item->readed_count = $output->data->readed_count;
			// 	$post_item->voted_count = $output->data->voted_count;
			// 	$post_item->blamed_count = $output->data->blamed_count;
			// 	$post_item->comment_count = $output->data->comment_count;
			// 	$post_item->trackback_count = $output->data->trackback_count;
			// }
			$this->set_attr($post_item, $load_extra_vars);
		}

		// function isExists() {
		public function is_exists() {
			return $this->_n_wp_post_id ? true : false;
		}

		// function isGranted() {
		public function is_granted() {
// var_dump($_SESSION['x2b_own_post']);
			if(isset($_SESSION['x2b_own_post'][$this->_n_wp_post_id])) {
				return $this->grant_cache = true;
			}
			if($this->grant_cache !== null)	{
				return $this->grant_cache;
			}

			if(!\X2board\Includes\Classes\Context::get('is_logged')) {
				return $this->grant_cache = false;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y') {
				unset($o_logged_info);
				return $this->grant_cache = true;
			}
			unset($o_logged_info);
			// $oModuleModel = getModel('module');
			// $grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($this->get('module_srl')), $logged_info);
			// if($grant->manager) return $this->grant_cache = true;

			// if($this->get('member_srl') && abs($this->get('member_srl')) == $o_logged_info->member_srl)	{
			// 	return $this->grant_cache = true;
			// }
			return $this->grant_cache = false;
		}

		// function setGrant()
		public function set_grant() {
// var_dump($this->_n_wp_post_id);
			$_SESSION['x2b_own_post'][$this->_n_wp_post_id] = true;
			$this->grant_cache = true;
		}

		// function getStatus()
		public function get_status() {
			$s_cur_post_status = $this->get('status');
			if(!$s_cur_post_status) {
				$o_post_class = \X2board\Includes\getClass('post');
				$s_default_status = $o_post_class->get_default_status();
				unset($o_post_class);
				return $s_default_status;
			}
			return $s_cur_post_status;
		}

		// function isNotice()
		public function is_notice() {
			return $this->get('is_notice') == 'Y' ? true : false;
		}

		// function isSecret()
		public function is_secret() {
			$o_post_model = \X2board\Includes\getModel('post');
			$s_secret_tag = $o_post_model->get_config_status('secret');
			unset($o_post_model);
			return $this->get('status') == $s_secret_tag ? true : false;
		}

		/**
		 * Update readed count
		 * @return void
		 */
		public function update_readed_count() {
			$o_post_controller = \X2board\Includes\getController('post');
			if($o_post_controller->update_readed_count($this)) {
				$readed_count = $this->get('readed_count');
				$this->add('readed_count', $readed_count+1);
			}
		}

		public function get_title($cut_size = 0, $tail='...') {
			if(!$this->_n_wp_post_id) return;

			$title = $this->get_title_text($cut_size, $tail);

			$attrs = array();
			$this->add('title_color', trim($this->get('title_color')));
			if($this->get('title_bold')=='Y') {
				$attrs[] = "font-weight:bold;";
			}
			if($this->get('title_color') && $this->get('title_color') != 'N') {
				$attrs[] = "color:#".$this->get('title_color');
			}

			if(count($attrs)) {
				$s_title = sprintf("<span style=\"%s\">%s</span>", implode(';',$attrs), htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false));
			}
			else {
				$s_title = htmlspecialchars($title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
			}
			unset($attrs);
			return esc_attr(wp_strip_all_tags($s_title));
		}

		// getTitleText
		public function get_title_text($cut_size = 0, $tail='...') {
			if(!$this->_n_wp_post_id) {
				return;
			}
			if($cut_size) {
				$title = \X2board\Includes\cut_str($this->get('title'), $cut_size, $tail);
			}
			else {
				$title = $this->get('title');
			}
			return $title;
		}

		// getIpaddress
		public function get_ip_addr() {
			if($this->is_granted()) {
				return $this->get('ipaddress');
			}
			return '*' . strstr($this->get('ipaddress'), '.');
		}

		// getContent
		public function get_content($add_popup_menu = false, $add_content_info = false, $resource_realpath = false, $add_xe_content_class = false, $stripEmbedTagException = false) {
			if(!$this->_n_wp_post_id) {
				return;
			}

			if($this->is_secret() && !$this->is_granted() && !$this->is_accessible()) {
				return __('msg_secret_post', X2B_DOMAIN);  //Context::getLang('msg_is_secret');
			}

			$result = $this->_check_accessible_from_status();
			if($result) {
				$_SESSION['accessible'][$this->_n_wp_post_id] = true;
			}			

			$s_content = $this->get('content');
			if(!$stripEmbedTagException) {
				\X2board\Includes\stripEmbedTagForAdmin($s_content, $this->get('post_author'));
			}

			// Define a link if using a rewrite module
			// $oContext = &Context::getInstance();
			// if($oContext->allow_rewrite)
			// {
			// 	$content = preg_replace('/<a([ \t]+)href=("|\')\.\/\?/i',"<a href=\\2". Context::getRequestUri() ."?", $content);
			// }
			// To display a pop-up menu
			// if($add_popup_menu)
			// {
			// 	$content = sprintf(
			// 		'%s<div class="document_popup_menu"><a href="#popup_menu_area" class="document_%d" onclick="return false">%s</a></div>',
			// 		$content,
			// 		$this->_n_wp_post_id, Context::getLang('cmd_document_do')
			// 	);
			// }
			// If additional content information is set
			// if($add_content_info)
			// {
				$n_post_author_id = $this->get('post_author');
				if($n_post_author_id < 0) {
					$n_post_author_id = 0;
				}
				$s_content = sprintf(
					'<!--BeforePost(%d,%d)--><div class="post_%d_%d x2b_content">%s</div><!--AfterPost(%d,%d)-->',
					$this->_n_wp_post_id, $n_post_author_id,
					$this->_n_wp_post_id, $n_post_author_id,
					$s_content,
					$this->_n_wp_post_id, $n_post_author_id,
					$this->_n_wp_post_id, $n_post_author_id
				);
				// Add x2b_content class although accessing content is not required
			// }
			// else
			// {
			// 	if($add_xe_content_class) $content = sprintf('<div class="xe_content">%s</div>', $content);
			// }
			// Change the image path to a valid absolute path if resource_realpath is true
			if($resource_realpath) {
				$s_content = preg_replace_callback('/<img([^>]+)>/i',array($this,'replaceResourceRealPath'), $s_content);
			}
			return $s_content;
		}

		// function isAccessible() {
		public function is_accessible() {
			return $_SESSION['accessible'][$this->_n_wp_post_id]==true?true:false;
		}

		/**
		 * Check accessible by document status
		 * @param array $matches
		 * @return mixed
		 */
		// private function _checkAccessibleFromStatus() {
		private function _check_accessible_from_status() {
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y') {
				return true;
			}

			$status = $this->get('status');
			if(empty($status)) {
				return false;
			}

			$o_post_model = \X2board\Includes\getModel('post');
			$configStatusList = $o_post_model->get_status_list();
			if($status == $configStatusList['public']) {  // || $status == $configStatusList['publish']) {
				return true;
			}
			else if($status == $configStatusList['secret']) {  // $status == $configStatusList['private'] || 
				if($this->get('post_author') == $o_logged_info->ID)
					return true;
			}
			return false;
		}

		public function is_allow_reply() {
			return false;
		}

		// function allowComment()
		public function allow_comment()	{
			// if post is not exists. so allow comment status is true ??? 뭔소리?
			if(!$this->is_exists()) return true;
			return $this->get('comment_status') == 'ALLOW' ? true : false;
		}

		/**
		 * Check whether to have a permission to write comment
		 * Authority to write a comment and to write a post is separated
		 * @return bool
		 */
		// isEnableComment
		public function is_enable_comment()	{
			$o_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			$n_forbid_comment_old_post_days = intval($o_module_info->comment_forbid_to_leave_comment_old_post_days);
			$b_allow_comment_for_admin_for_old_post = $o_module_info->allow_comment_for_admin_for_old_post == 'Y' ? true : false;
			unset($o_module_info);

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y' && $b_allow_comment_for_admin_for_old_post ) { // allow admin to write comment
				$b_check_comment_privilege = false;
			}
			else {
				$b_check_comment_privilege = true;
			}
			unset($o_logged_info);

			if( $b_check_comment_privilege && $n_forbid_comment_old_post_days > 0 ) {
				$dt_target = date_create($this->get('regdate_dt'));
				$dt_start = new \DateTime(date('Y-m-d'));
				$dt_interval = date_diff($dt_start, $dt_target);
				unset($dt_start);
				unset($dt_target);
				if( $dt_interval->days > $n_forbid_comment_old_post_days ) {
					return false;
				}
			}

			// Return false if not authorized, if a secret post, if the post is set not to allow any comment
			if (!$this->allow_comment()) {
				return false;
			}
			if(!$this->is_granted() && $this->is_secret()) {
				return false;
			}
			return true;
		}

		/**
		 * 게시글에 표시할 첨부파일을 반환한다.
		 * @return object
		 */
		// function getUploadedFiles($sortIndex = 'file_srl')
		public function get_uploaded_files($sortIndex = 'file_id') {
			if(!$this->_n_wp_post_id) { // if write new post
				return array();
			}
			if($this->is_secret() && !$this->is_granted()) {
				return array();
			}
			if(!$this->get('uploaded_count')) {
				return array();
			}
			if(!isset($this->_a_uploaded_file[$sortIndex])) {
				$o_file_model = \X2board\Includes\getModel('file');
				$this->_a_uploaded_file[$sortIndex] = $o_file_model->get_files($this->_n_wp_post_id, $sortIndex, true);  // array(),
				unset($o_file_model);
			}
			return $this->_a_uploaded_file[$sortIndex];
		}

		/**
		 * for post.php skin usage
		 * @return object
		 */
		// function isExtraVarsExists()
		public function is_user_define_extended_vars_exists() {
			if(!$this->get('board_id')) {
				return false;
			}
			// $o_post_model = \X2board\Includes\getModel('post');
			// $extra_keys = $o_post_model->get_user_define_keys($this->get('board_id'));
			$a_user_define_extended_field = $this->get_user_define_extended_fields();
			return count($a_user_define_extended_field) ? true : false;
		}

		/**
		 * for post.php skin usage
		 * differ with \includes\modules\post\post.model.php::get_user_define_extended_fields()
		 * this method returns list of the designated post 
		 * @return object
		 */
		// function getExtraVars()
		public function get_user_define_extended_fields() {
			if(!$this->get('board_id') || !$this->_n_wp_post_id) {
				return null;
			}
			$o_post_model = \X2board\Includes\getModel('post');
			$inserted_extra_vars = $o_post_model->get_user_define_vars($this->_n_wp_post_id);
			unset($o_post_model);

			$o_post_user_define_fields = new \X2board\Includes\Classes\GuestUserDefineFields();
			$a_default_fields = $o_post_user_define_fields->get_default_fields();
			unset($o_post_user_define_fields);

			$a_ignore_field_type = array_keys($a_default_fields);
			unset($a_default_fields);
			$a_user_define_extended_fields = array();
			foreach($inserted_extra_vars as $n_seq=>$o_field){
				$field_type = (isset($o_field->type) && $o_field->type) ? $o_field->type : '';
				if(in_array($field_type, $a_ignore_field_type) ){ // ignore default fields
					continue;
				}
				if( is_null($o_field->value) ) {
					continue;
				}
				$a_user_define_extended_fields[$o_field->idx] = $o_field;
			}
			unset($inserted_extra_vars);
			unset($a_ignore_field_type);
			return $a_user_define_extended_fields;
		}

		/**
		 * Return the value obtained from getExtraImages with image tag
		 * @param int $time_check
		 * @return string
		 */
		
		// function printExtraImages($time_check = 43200)
		public function print_extra_images($time_check = 43200) {
			if(!$this->_n_wp_post_id) {
				return;
			}
			$buffs = $this->_get_extra_images($time_check);
			if(!count($buffs)) {
				return;
			}

			$s_path = sprintf('%s%s',X2B_URL, "includes/modules/post/tpl/icons/");
			$buff = array();
			foreach($buffs as $key => $val) {
				$buff[] = sprintf('<img src="%s%s.gif" alt="%s" title="%s" style="margin-right:2px;" />', $s_path, $val, $val, $val);
			}
			return implode('', $buff);
		}

		/**
		 * Functions to display icons for new post, latest update, secret(private) post, image/video/attachment
		 * Determine new post and latest update by $time_interval
		 * @param int $time_interval
		 * @return array
		 */
		// function getExtraImages($time_interval = 43200)
		private function _get_extra_images($time_interval = 43200) {
			if(!$this->_n_wp_post_id) {
				return;
			}
			// variables for icon list
			$buffs = array();

			$check_files = false;

			// Check if secret post is
			if($this->is_secret()) {
				$buffs[] = "secret";
			}

			// Set the latest time
			$time_check = date("YmdHis", $_SERVER['REQUEST_TIME']-$time_interval);

			// Check new post
			if($this->get('regdate_dt')>$time_check) {
				$buffs[] = "new";
			}
			else if($this->get('last_update_dt')>$time_check) {
				$buffs[] = "update";
			}

			// Check the attachment
			if($this->has_uploaded_files()) {
				$buffs[] = "file";
			}

			return $buffs;
		}

		// function hasUploadedFiles()
		public function has_uploaded_files() {
			if(!$this->_n_wp_post_id) {
				return;
			}
			if($this->is_secret() && !$this->is_granted()) {
				return false;
			}
			return $this->get('uploaded_count') ? true : false;
		}

		// function isCarted()
		public function is_carted() {
			if(isset($_SESSION['x2b_post_management'][$this->_n_wp_post_id])) {
				return $_SESSION['x2b_post_management'][$this->_n_wp_post_id];
			}
			return false;
		}

		// function addCart()
		public function add_cart() {
			$_SESSION['x2b_post_management'][$this->_n_wp_post_id] = true;
		}

		// function removeCart()
		public function remove_cart() {
			unset($_SESSION['x2b_post_management'][$this->_n_wp_post_id]);
		}

		// function isEditable()
		public function is_editable() {
			if($this->is_granted() || !$this->get('post_author')) return true;
			return false;
		}

		// function getSummary($str_size = 50, $tail = '...')
		public function get_summary($str_size = 50, $tail = '...') {
			$content = $this->get_content(FALSE, FALSE);
			
			$content = nl2br($content);

			// For a newlink, inert a whitespace
			$content = preg_replace('!(<br[\s]*/{0,1}>[\s]*)+!is', ' ', $content);

			// Replace tags such as </p> , </div> , </li> and others to a whitespace
			$content = str_replace(array('</p>', '</div>', '</li>', '-->'), ' ', $content);

			// Remove Tags
			$content = preg_replace('!<([^>]*?)>!is', '', $content);

			// Replace < , >, "
			$content = str_replace(array('&lt;', '&gt;', '&quot;', '&nbsp;'), array('<', '>', '"', ' '), $content);

			// Delete  a series of whitespaces
			$content = preg_replace('/ ( +)/is', ' ', $content);

			// Truncate string
			$content = trim(\X2board\Includes\cut_str($content, $str_size, $tail));

			// Replace back < , <, "
			$content = str_replace(array('<', '>', '"'),array('&lt;', '&gt;', '&quot;'), $content);

			return $content;
		}

		// function getPermanentUrl()
		public function get_permanent_url() {
			return esc_url(\X2board\Includes\Classes\Context::get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$this->_n_wp_post_id));
		}

		/**
		 * Return author's profile image
		 * @return string
		 */
		// function getProfileImage()
		public function get_profile_image() {
			if(!$this->is_exists() || !$this->get('post_author')) {
				return;
			}
			return get_avatar( $this->get('post_author'), 32 );
		}

		// function getExtraEidValue($eid)
		public function get_user_define_eid_value($eid) {
			$extra_vars = $this->get_user_define_extended_fields();
			if($extra_vars)	{
				// Handle extra variable(eid)
				foreach($extra_vars as $idx => $key) {
					$extra_eid[$key->eid] = $key;
				}
			}
			if(isset($extra_eid)) {
				if(is_array($extra_eid) && array_key_exists($eid,$extra_eid)) {
					return $extra_eid[$eid]->getValue();
				}
			}
			return '';
		}

		// function getExtraValueHTML($idx)
		public function get_user_define_value_HTML($s_eid) {
			$extra_vars = $this->get_user_define_extended_fields();
			if(is_array($extra_vars) && array_key_exists($s_eid,$extra_vars)) {
				return $extra_vars[$s_eid]->getValueHTML();
			}
			else {
				return '';
			}
		}

		// function thumbnailExists($width = 80, $height = 0, $type = '')
		public function check_thumbnail($width = 80, $height = 0, $type = '') {
			if(!$this->_n_wp_post_id) {
				return false;
			}
			if(!$this->get_thumbnail($width, $height, $type)) {
				return false;
			}
			return true;
		}

		// function getThumbnail($width = 80, $height = 0, $thumbnail_type = '')
		public function get_thumbnail($width = 80, $height = 0, $thumbnail_type = '') {
			// Return false if the post doesn't exist
			if(!$this->_n_wp_post_id) return false;

			if($this->is_secret() && !$this->is_granted()) {
				return false;
			}

			// If not specify its height, create a square
			if(!$height) {
				$height = $width;
			}

			// Return false if neither attachement nor image files in the post
			if(!$this->get('uploaded_count')) {
				$content = $this->get('content');
				if(!$content) {
					// $args = new \stdClass();
					// $args->document_srl = $this->_n_wp_post_id;
					// $output = executeQuery('document.getDocument', $args, array('content'));
					// SELECT `content` FROM `xe_documents` as `documents` WHERE `document_srl` = ?
					global $wpdb;
					$o_row = $wpdb->get_row("SELECT `content` FROM `{$wpdb->prefix}x2b_posts` WHERE `post_id`={$this->_n_wp_post_id}");
					if($o_row->content) {  // $output->toBool() && $output->data) {
						$content = $o_row->content;
						$this->add('content', $o_row->content);
					}
					unset($o_row);
				}
				if(!preg_match("!<img!is", $content)) {
					return false;
				}
			}

			// Get thumbnai_type information from post module's configuration
			if(!in_array($thumbnail_type, array('crop','ratio'))) {
				$o_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
				$thumbnail_type = $o_module_info->thumbnail_type;
				unset($o_module_info);
			}

			// Define thumbnail information
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$s_rand_dir = \X2board\Includes\getNumberingPath($this->_n_wp_post_id, 3);
			// $thumbnail_path = sprintf('files/thumbnails/%s',\X2board\Includes\getNumberingPath($this->_n_wp_post_id, 3));
			$thumbnail_path = wp_get_upload_dir()['basedir'].DIRECTORY_SEPARATOR.X2B_DOMAIN.DIRECTORY_SEPARATOR.'thumbnails'.
								DIRECTORY_SEPARATOR.$n_board_id.DIRECTORY_SEPARATOR.$s_rand_dir;
			$thumbnail_file = sprintf('%s%dx%d.%s.jpg', $thumbnail_path, $width, $height, $thumbnail_type);
			$thumbnail_lockfile = sprintf('%s%dx%d.%s.lock', $thumbnail_path, $width, $height, $thumbnail_type);

			$thumbnail_url = wp_get_upload_dir()['baseurl'].'/'.X2B_DOMAIN.'/thumbnails/'.$n_board_id.'/'.$s_rand_dir;
			$thumbnail_url = sprintf('%s%dx%d.%s.jpg', $thumbnail_url, $width, $height, $thumbnail_type);

			// Return false if thumbnail file exists and its size is 0. Otherwise, return its path
			if(file_exists($thumbnail_file) || file_exists($thumbnail_lockfile)) {
				if(filesize($thumbnail_file) < 1) {
					return FALSE;
				}
				else {
					return $thumbnail_url . '?' . date('YmdHis', filemtime($thumbnail_file));
				}
			}

			if( !file_exists( $thumbnail_path ) ) {
				if(!wp_mkdir_p( $thumbnail_path ) ){
					return FALSE;
				}
			}

			// Create lockfile to prevent race condition
			// FileHandler::writeFile($thumbnail_lockfile, '', 'w');
			global $wp_filesystem;
			$wp_filesystem->put_contents( $thumbnail_lockfile, '',
										  FS_CHMOD_FILE // predefined mode settings for WP files
										);

			// Target File
			$source_file = null;
			$is_tmp_file = false;

			// Find an iamge file among attached files if exists
			if($this->has_uploaded_files()) {
				$file_list = $this->get_uploaded_files();
				$first_image = null;
				foreach($file_list as $file) {
					if($file->direct_download !== 'Y') {
						continue;
					}

					if($file->cover_image === 'Y' && file_exists($file->uploaded_filename)) {
						$source_file = $file->uploaded_filename;
						break;
					}

					if($first_image) {
						continue;
					}

					if(preg_match("/\.(jpe?g|png|gif|bmp)$/i", $file->source_filename)) {
						if(file_exists($file->uploaded_filename)) {
							$first_image = $file->uploaded_filename;
						}
					}
				}

				if(!$source_file && $first_image) {
					$source_file = $first_image;
				}
			}
			// If not exists, file an image file from the content
			$is_tmp_file = false;
			if(!$source_file) {
				$random = new  \X2board\Includes\Classes\Security\Password();

				preg_match_all("!<img[^>]*src=(?:\"|\')([^\"\']*?)(?:\"|\')!is", $content, $matches, PREG_SET_ORDER);

				foreach($matches as $target_image) {
					$target_src = trim($target_image[1]);
					// if(preg_match('/\/(common|modules|widgets|addons|layouts|m\.layouts)\//i', $target_src)) continue;

					if(!preg_match('/^(http|https):\/\//i',$target_src)) {
						$target_src = Context::getRequestUri().$target_src;
					}

					$target_src = htmlspecialchars_decode($target_src);

					$tmp_file = _XE_PATH_ . 'files/cache/tmp/' . $random->createSecureSalt(32, 'hex');
					FileHandler::getRemoteFile($target_src, $tmp_file);
					if(!file_exists($tmp_file)) continue;

					$imageinfo = getimagesize($tmp_file);
					list($_w, $_h) = $imageinfo;
					if($imageinfo === false || ($_w < ($width * 0.3) && $_h < ($height * 0.3))) {
						FileHandler::removeFile($tmp_file);
						continue;
					}

					$source_file = $tmp_file;
					$is_tmp_file = true;
					break;
				}
			}

			$output_file = null;
			if($source_file) {
				$output_file = \X2board\Includes\Classes\FileHandler::create_image_file($source_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type);
			}

			// Remove source file if it was temporary
			if($is_tmp_file) {
				FileHandler::removeFile($source_file);
			}

			// Remove lockfile
			// FileHandler::removeFile($thumbnail_lockfile);
			
			if(is_null($this->_o_fileSystemDirect) ) {
				$this->_o_fileSystemDirect = new \WP_Filesystem_Direct(false);
			}
			$this->_o_fileSystemDirect->delete($thumbnail_lockfile);
			// unset($fileSystemDirect);

			// Create an empty file if thumbnail generation failed
			if(!$output_file) {
				// FileHandler::writeFile($thumbnail_file, '','w');
				$wp_filesystem->put_contents( $thumbnail_file, '',
											  FS_CHMOD_FILE // predefined mode settings for WP files
											);
			}
			return $thumbnail_url . '?' . date('YmdHis', filemtime($thumbnail_file));
		}




		

		




		




		








///////////////////
		
		
		

		/*
		function useNotify()
		{
			return $this->get('notify_message')=='Y' ? true : false;
		}

		function doCart()
		{
			if(!$this->_n_wp_post_id) return false;
			if($this->isCarted()) $this->removeCart();
			else $this->addCart();
		}

		function getUserID()
		{
			return htmlspecialchars($this->get('user_id'), ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
		}

		function getUserName()
		{
			return htmlspecialchars($this->get('user_name'), ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
		}

		function getLastUpdater()
		{
			return htmlspecialchars($this->get('last_updater'), ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
		}*/

		// function getContentText($strlen = 0)
		// {
		// 	if(!$this->_n_wp_post_id) return;

		// 	if($this->isSecret() && !$this->isGranted() && !$this->isAccessible()) return Context::getLang('msg_is_secret');

		// 	$result = $this->_check_accessible_from_status();
		// 	if($result) $_SESSION['accessible'][$this->_n_wp_post_id] = true;

		// 	$content = $this->get('content');
		// 	$content = preg_replace_callback('/<(object|param|embed)[^>]*/is', array($this, '_checkAllowScriptAccess'), $content);
		// 	$content = preg_replace_callback('/<object[^>]*>/is', array($this, '_addAllowScriptAccess'), $content);

		// 	if($strlen) return cut_str(strip_tags($content),$strlen,'...');

		// 	return htmlspecialchars($content);
		// }

		/**
		 * Return transformed content by Editor codes
		 * @param bool $add_popup_menu
		 * @param bool $add_content_info
		 * @param bool $resource_realpath
		 * @param bool $add_xe_content_class
		 * @return string
		 */
		/*function getTransContent($add_popup_menu = true, $add_content_info = true, $resource_realpath = false, $add_xe_content_class = true)
		{
			$oEditorController = getController('editor');

			$content = $this->getContent($add_popup_menu, $add_content_info, $resource_realpath, $add_xe_content_class);
			$content = $oEditorController->transComponent($content);

			return $content;
		}*/

		/*function getExtraValue($idx)
		{
			$extra_vars = $this->getExtraVars();
			if(is_array($extra_vars) && array_key_exists($idx,$extra_vars))
			{
				return $extra_vars[$idx]->getValue();
			}
			else
			{
				return '';
			}
		}

		function getExtraEidValueHTML($eid)
		{
			$extra_vars = $this->getExtraVars();
			// Handle extra variable(eid)
			foreach($extra_vars as $idx => $key)
			{
				$extra_eid[$key->eid] = $key;
			}
			
			if(is_array($extra_eid) && array_key_exists($eid,$extra_eid))
			{
				return $extra_eid[$eid]->getValueHTML();
			}
			else
			{
				return '';
			}
		}

		public function getExtraVarsValue($key)
		{
			$extra_vals = unserialize($this->get('extra_vars'));
			$val = $extra_vals->$key;
			return $val;
		}*/

		/**
		 * Return Editor html
		 * @return string
		 */
		/*function getEditor()
		{
			$module_srl = $this->get('module_srl');
			if(!$module_srl) $module_srl = Context::get('module_srl');

			$oEditorModel = getModel('editor');
			return $oEditorModel->getModuleEditor('document', $module_srl, $this->_n_wp_post_id, 'document_srl', 'content');
		}*/

		/**
		 * Return comment editor's html
		 * @return string
		 */
		/*function getCommentEditor()
		{
			if(!$this->isEnableComment()) return;

			$oEditorModel = getModel('editor');
			return $oEditorModel->getModuleEditor('comment', $this->get('module_srl'), $comment_srl, 'comment_srl', 'content');
		}*/

		/**
		 * Change an image path in the content to absolute path
		 * @param array $matches
		 * @return mixed
		 */
		/*function replaceResourceRealPath($matches)
		{
			return preg_replace('/src=(["\']?)files/i','src=$1'.Context::getRequestUri().'files', $matches[0]);
		}*/

		// function isLocked()
		// public function is_locked() {
		// 	if(!$this->is_exists()) {
		// 		return false;
		// 	}
		// 	return $this->get('comment_status') == 'ALLOW' ? false : true;
		// 	// return $this->get('allow_comment') == 'Y' ? false : true;
		// }

		// function getRegdateTime()
		// {
		// 	$regdate = $this->get('regdate');
		// 	$year = substr($regdate,0,4);
		// 	$month = substr($regdate,4,2);
		// 	$day = substr($regdate,6,2);
		// 	$hour = substr($regdate,8,2);
		// 	$min = substr($regdate,10,2);
		// 	$sec = substr($regdate,12,2);
		// 	return mktime($hour,$min,$sec,$month,$day,$year);
		// }

		// function getRegdateGM()
		// {
		// 	return $this->getRegdate('D, d M Y H:i:s').' '.$GLOBALS['_time_zone'];
		// }

		// function getRegdateDT()
		// {
		// 	return $this->getRegdate('Y-m-d').'T'.$this->getRegdate('H:i:s').substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3,2);
		// }

		// function getUpdate($format = 'Y.m.d H:i:s')
		// {
		// 	return zdate($this->get('last_update'), $format);
		// }

		// function getUpdateTime()
		// {
		// 	$year = substr($this->get('last_update'),0,4);
		// 	$month = substr($this->get('last_update'),4,2);
		// 	$day = substr($this->get('last_update'),6,2);
		// 	$hour = substr($this->get('last_update'),8,2);
		// 	$min = substr($this->get('last_update'),10,2);
		// 	$sec = substr($this->get('last_update'),12,2);
		// 	return mktime($hour,$min,$sec,$month,$day,$year);
		// }

		// function getUpdateGM()
		// {
		// 	return gmdate("D, d M Y H:i:s", $this->getUpdateTime());
		// }

		// function getUpdateDT()
		// {
		// 	return $this->getUpdate('Y-m-d').'T'.$this->getUpdate('H:i:s').substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3,2);
		// }

		/**
		 * Send notify message to document owner
		 * insert_comment()가 실행되면 부모 post 작성자에게 통지하는 기능
		 * @param string $type
		 * @param string $content
		 * @return void
		 */
		// function notify($type, $content)
		// {
		// 	if(!$this->_n_wp_post_id) return;
		// 	// return if it is not useNotify
		// 	if(!$this->useNotify()) return;
		// 	// Pass if an author is not a logged-in user
		// 	if(!$this->get('member_srl')) return;
		// 	// Return if the currently logged-in user is an author
		// 	$logged_info = Context::get('logged_info');
		// 	if($logged_info->member_srl == $this->get('member_srl')) return;
		// 	// List variables
		// 	if($type) $title = "[".$type."] ";
		// 	$title .= cut_str(strip_tags($content), 10, '...');
		// 	$content = sprintf('%s<br /><br />from : <a href="%s" target="_blank">%s</a>',$content, getFullUrl('','document_srl',$this->_n_wp_post_id), getFullUrl('','document_srl',$this->_n_wp_post_id));
		// 	$receiver_srl = $this->get('member_srl');
		// 	$sender_member_srl = $logged_info->member_srl;
		// 	// Send a message
		// 	$oCommunicationController = getController('communication');
		// 	$oCommunicationController->sendMessage($sender_member_srl, $receiver_srl, $title, $content, false);
		// }

		// function _addAllowScriptAccess($m)
		// {
		// 	if($this->allowscriptaccessList[$this->allowscriptaccessKey] == 1)
		// 	{
		// 		$m[0] = $m[0].'<param name="allowscriptaccess" value="never"></param>';
		// 	}
		// 	$this->allowscriptaccessKey++;
		// 	return $m[0];
		// }

		// function _checkAllowScriptAccess($m)
		// {
		// 	if($m[1] == 'object')
		// 	{
		// 		$this->allowscriptaccessList[] = 1;
		// 	}

		// 	if($m[1] == 'param')
		// 	{
		// 		if(stripos($m[0], 'allowscriptaccess'))
		// 		{
		// 			$m[0] = '<param name="allowscriptaccess" value="never"';
		// 			if(substr($m[0], -1) == '/')
		// 			{
		// 				$m[0] .= '/';
		// 			}
		// 			$this->allowscriptaccessList[count($this->allowscriptaccessList)-1]--;
		// 		}
		// 	}
		// 	else if($m[1] == 'embed')
		// 	{
		// 		if(stripos($m[0], 'allowscriptaccess'))
		// 		{
		// 			$m[0] = preg_replace('/always|samedomain/i', 'never', $m[0]);
		// 		}
		// 		else
		// 		{
		// 			$m[0] = preg_replace('/\<embed/i', '<embed allowscriptaccess="never"', $m[0]);
		// 		}
		// 	}
		// 	return $m[0];
		// }

		// function allowTrackback()
		// {
		// 	static $allow_trackback_status = null;
		// 	if(is_null($allow_trackback_status))
		// 	{
				
		// 		// Check the tarckback module exist
		// 		if(!getClass('trackback'))
		// 		{
		// 			$allow_trackback_status = false;
		// 		}
		// 		else
		// 		{
		// 			// If the trackback module is configured to be disabled, do not allow. Otherwise, check the setting of each module.
		// 			$oModuleModel = getModel('module');
		// 			$trackback_config = $oModuleModel->getModuleConfig('trackback');
					
		// 			if(!$trackback_config)
		// 			{
		// 				$trackback_config = new stdClass();
		// 			}
					
		// 			if(!isset($trackback_config->enable_trackback)) $trackback_config->enable_trackback = 'Y';
		// 			if($trackback_config->enable_trackback != 'Y') $allow_trackback_status = false;
		// 			else
		// 			{
		// 				$module_srl = $this->get('module_srl');
		// 				// Check settings of each module
		// 				$module_config = $oModuleModel->getModulePartConfig('trackback', $module_srl);
		// 				if($module_config->enable_trackback == 'N') $allow_trackback_status = false;
		// 				else if($this->get('allow_trackback')=='Y' || !$this->isExists()) $allow_trackback_status = true;
		// 			}
		// 		}
		// 	}
		// 	return $allow_trackback_status;
		// }

		// function getLangCode()
		// {
		// 	return $this->get('lang_code');
		// }

		// function isExistsHomepage()
		// {
		// 	if(trim($this->get('homepage'))) return true;
		// 	return false;
		// }

		// function getHomepageUrl()
		// {
		// 	$url = trim($this->get('homepage'));
		// 	if(!$url) return;

		// 	if(strncasecmp('http://', $url, 7) !== 0 && strncasecmp('https://', $url, 8) !== 0)  $url = 'http://' . $url;

		// 	return escape($url, false);
		// }

		// function getMemberSrl()
		// {
		// 	return $this->get('member_srl');
		// }

		// function getTrackbackUrl()
		// {
		// 	if(!$this->_n_wp_post_id) return;

		// 	// Generate a key to prevent spams
		// 	$oTrackbackModel = getModel('trackback');
		// 	if($oTrackbackModel) return $oTrackbackModel->getTrackbackUrl($this->_n_wp_post_id, $this->getDocumentMid());
		// }

		// function allowTrackback()
		// {
		// 	static $allow_trackback_status = null;
		// 	if(is_null($allow_trackback_status))
		// 	{
				
		// 		// Check the tarckback module exist
		// 		if(!getClass('trackback'))
		// 		{
		// 			$allow_trackback_status = false;
		// 		}
		// 		else
		// 		{
		// 			// If the trackback module is configured to be disabled, do not allow. Otherwise, check the setting of each module.
		// 			$oModuleModel = getModel('module');
		// 			$trackback_config = $oModuleModel->getModuleConfig('trackback');
					
		// 			if(!$trackback_config)
		// 			{
		// 				$trackback_config = new stdClass();
		// 			}
					
		// 			if(!isset($trackback_config->enable_trackback)) $trackback_config->enable_trackback = 'Y';
		// 			if($trackback_config->enable_trackback != 'Y') $allow_trackback_status = false;
		// 			else
		// 			{
		// 				$module_srl = $this->get('module_srl');
		// 				// Check settings of each module
		// 				$module_config = $oModuleModel->getModulePartConfig('trackback', $module_srl);
		// 				if($module_config->enable_trackback == 'N') $allow_trackback_status = false;
		// 				else if($this->get('allow_trackback')=='Y' || !$this->isExists()) $allow_trackback_status = true;
		// 			}
		// 		}
		// 	}
		// 	return $allow_trackback_status;
		// }

		// function getTrackbackCount()
		// {
		// 	return $this->get('trackback_count');
		// }

		// function getTrackbacks()
		// {
		// 	if(!$this->_n_wp_post_id) return;

		// 	if(!$this->allowTrackback() || !$this->get('trackback_count')) return;

		// 	$oTrackbackModel = getModel('trackback');
		// 	return $oTrackbackModel->getTrackbackList($this->_n_wp_post_id, $is_admin);
		// }

		/**
		 * Return author's signiture
		 * @return string
		 */
		// function getSignature()
		// {
		// 	// Pass if a document doesn't exist
		// 	if(!$this->isExists() || !$this->get('member_srl')) return;
		// 	// Get signature information
		// 	$oMemberModel = getModel('member');
		// 	$signature = $oMemberModel->getSignature($this->get('member_srl'));
		// 	// Check if a maximum height of signiture is set in the member module
		// 	if(!isset($GLOBALS['__member_signature_max_height']))
		// 	{
		// 		$oModuleModel = getModel('module');
		// 		$member_config = $oModuleModel->getModuleConfig('member');
		// 		$GLOBALS['__member_signature_max_height'] = $member_config->signature_max_height;
		// 	}
		// 	if($signature)
		// 	{
		// 		$max_signature_height = $GLOBALS['__member_signature_max_height'];
		// 		if($max_signature_height) $signature = sprintf('<div style="max-height:%dpx;overflow:auto;overflow-x:hidden;height:expression(this.scrollHeight > %d ? \'%dpx\': \'auto\')">%s</div>', $max_signature_height, $max_signature_height, $max_signature_height, $signature);
		// 	}

		// 	return $signature;
		// }

		// function getTranslationLangCodes()
		// {
		// 	$obj = new stdClass;
		// 	$obj->document_srl = $this->_n_wp_post_id;
		// 	// -2 is an index for content. We are interested if content has other translations.
		// 	$obj->var_idx = -2;
		// 	$output = executeQueryArray('document.getDocumentTranslationLangCodes', $obj);

		// 	if (!$output->data)
		// 	{
		// 		$output->data = array();
		// 	}
		// 	// add original page's lang code as well
		// 	$origLangCode = new stdClass;
		// 	$origLangCode->lang_code = $this->getLangCode();
		// 	$output->data[] = $origLangCode;

		// 	return $output->data;
		// }

		/**
		 * Returns the document's mid in order to construct SEO friendly URLs
		 * @return string
		 */
		// function getDocumentMid()
		// {
		// 	$model = getModel('module');
		// 	$module = $model->getModuleInfoByModuleSrl($this->get('module_srl'));
		// 	return $module->mid;
		// }

		/**
		 * Returns the document's type (document/page/wiki/board/etc)
		 * @return string
		 */
		// function getDocumentType()
		// {
		// 	$model = getModel('module');
		// 	$module = $model->getModuleInfoByModuleSrl($this->get('module_srl'));
		// 	return $module->module;
		// }

		/**
		 * Returns the document's alias
		 * @return string
		 */
		// function getDocumentAlias()
		// {
		// 	$oDocumentModel = getModel('document');
		// 	return $oDocumentModel->getAlias($this->_n_wp_post_id);
		// }

		/**
		 * Returns the document's actual title (browser_title)
		 * @return string
		 */
		// function getModuleName()
		// {
		// 	$model = getModel('module');
		// 	$module = $model->getModuleInfoByModuleSrl($this->get('module_srl'));
		// 	return $module->browser_title;
		// }

		// function getBrowserTitle()
		// {
		// 	return $this->getModuleName();
		// }
	}
}