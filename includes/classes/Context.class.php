<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

namespace X2board\Includes\Classes;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Classes\\Context')) {
	/**
	 * Manages Context such as request arguments/environment variables
	 * It has dual method structure, easy-to use methods which can be called as self::methodname(),and methods called with static object.
	 *
	 * @author XEHub (developers@xpressengine.com)
	 */
	class Context {
		/**
		 * Conatins request parameters and environment variables
		 * @var object
		 */
		public $context = NULL;

		/**
		 * build an UTF8 decoded URL for custom router only
		 * @var object
		 */
		private $_s_page_permlink = null;

		/**
		 * variables from GET or form submit
		 * @var mixed
		 */
		public $get_vars = NULL;

		/**
		 * Pattern for request vars check
		 * @var array
		 */
		private $_a_patterns = array(
				'/<\?/iUsm',
				'/<\%/iUsm',
				'/<script\s*?language\s*?=\s*?("|\')?\s*?php\s*("|\')?/iUsm'
				);
		
		/**
		 * Pattern for request vars check
		 * @var array
		 */
		private $_a_ignore_request = array(
				'woocommerce-login-nonce', '_wpnonce',
				'woocommerce-reset-password-nonce',
				'woocommerce-edit-address-nonce',
				'save-account-details-nonce'
				);

		/**
		 * Check init
		 * @var bool FALSE if init fail
		 */
		public $isSuccessInit = TRUE;

		/**
		 * returns static context object (Singleton). It's to use Context without declaration of an object
		 *
		 * @return object Instance
		 */
		public static function &getInstance() {
			static $theInstance = null;
			if(!$theInstance) {
				$theInstance = new Context();
			}
			return $theInstance;
		}

		/**
		 * Cunstructor
		 *
		 * @return void
		 */
		public function __construct() {
			$this->get_vars = new \stdClass();
			$this->context = new \stdClass();
		}

		/**
		 * Initialization, it sets DB information, request arguments and so on.
		 *
		 * @see This function should be called only once
		 * @return void
		 */
		public function init($s_cmd_type) {
			$this->setRequestMethod('');
			$this->_setRequestArgument();

			$o_logged_info = wp_get_current_user();
			$o_logged_info->is_admin = current_user_can('manage_options') ? 'Y' : 'N';
			$this->set( 'is_logged', is_user_logged_in() );
			$this->set( 'logged_info', $o_logged_info );

			// time translation for \X2board\Includes\zdate()
			$this->set( 'unit_week', array( "Monday"=> "월", "Tuesday" => "화", "Wednesday" => "수", 
											"Thursday" => "목", "Friday" => "금", "Saturday" => "토", "Sunday" =>"일" ) );
			$this->set( 'unit_meridiem', array( "am"=> "오전", "pm" => "오후", "AM" => "오전", "PM" => "오후" ) );

			// WP stores small-letter URL like wp-%ed%8e%98%ec%9d%b4%ec%a7%80-%ec%a0%9c%eb%aa%a9-2
			// router needs capitalized URL like wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2
			if(get_post()){
				$this->_s_page_permlink = site_url().'/'.urlencode(urldecode(get_post($this->get('board_id'))->post_name));
			}

			if( $s_cmd_type == 'proc' ) {  // load controller priority
				$s_cmd = isset( $_REQUEST['cmd'])?$_REQUEST['cmd'] : '';
				$s_cmd_prefix = substr( $s_cmd, 0, 4 );
				if( $s_cmd_prefix === 'proc' ) {  
					$o_controller = \X2board\Includes\getController('board');
					$n_board_id = sanitize_text_field(intval($_REQUEST['board_id']));
					$o_controller->setModuleInfo($n_board_id);
					$next_page_url = $o_controller->get('s_wp_redirect_url');
					if ( wp_redirect( $next_page_url ) ) {
						unset($o_controller);
						exit;  // required to execute wp_redirect()
					}
					wp_redirect(home_url());
					unset($o_controller);
					exit;
				}
				wp_redirect(home_url());
				exit;  // required to execute wp_redirect()
			}  ///////// end of proc mode ////////////////////// 
			elseif($s_cmd_type === 'admin_import' ) {
				$o_controller = \X2board\Includes\getModule('board', 'controller');
				$o_controller->setModuleInfo(intval($_POST['board_id']));
				unset($o_controller);
			}
			else {  ///////// begin of view mode ////////////////////// 
				$s_cmd = self::get('cmd');
				$s_cmd_prefix = substr( $s_cmd, 0, 4 );
				if( $s_cmd_prefix === '' || $s_cmd_prefix === 'view' ) {  // load view
					// pretty url is for view only
					$this->_convert_pretty_command_uri();
					$o_view = \X2board\Includes\getModule('board');
					$o_view->setModuleInfo($this->get('board_id'));
					unset($o_view);
				}
			}
		}

		/**
		 * return board id oriented permalink
		 * WP stores small-letter URL like wp-%ed%8e%98%ec%9d%b4%ec%a7%80-%ec%a0%9c%eb%aa%a9-2
		 * router needs capitalized URL like wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2
		 * @return void
		 */
		public static function get_the_permalink() {
			$o_self = self::getInstance();
			// $this->_s_page_permlink set in $this->init();
			$s_page_permlink = $o_self->_s_page_permlink;
			unset($o_self);
			return $s_page_permlink;
		}

		/**
		 * handle request arguments for GET/POST
		 *
		 * @return void
		 */
		private function _setRequestArgument() {
			if(!count($_REQUEST)) {
				return;
			}
			$requestMethod = $this->getRequestMethod();
			foreach($_REQUEST as $key => $val) {
				if($val === '' || self::get($key) || in_array($key, $this->_a_ignore_request) ) {
					continue;
				}

				$key = htmlentities($key);
				$val = $this->_filter_request_var($key, $val, false, ($requestMethod == 'GET'));

				if($requestMethod == 'GET' && isset($_GET[$key])) {
					$set_to_vars = TRUE;
				}
				elseif($requestMethod == 'POST' && isset($_POST[$key])) {
					$set_to_vars = TRUE;
				}
				else {
					$set_to_vars = FALSE;
				}

				if($set_to_vars) {
					$this->_recursiveCheckVar($val);
				}

				$this->set($key, $val, $set_to_vars);
			}
			// check rewrite conf for pretty post URL
			$a_board_rewrite_settings = get_option( X2B_REWRITE_OPTION_TITLE );
			if(isset( $a_board_rewrite_settings[$this->get('board_id')])) {
				$set_to_vars = TRUE;
				if( get_query_var( 'post_id' ) ) {  // post_id from custom route detected, find the code blocks by X2B_REWRITE_OPTION_TITLE
					$this->set( 'post_id', get_query_var( 'post_id' ), $set_to_vars);
				}
				$this->set( 'use_rewrite', 'Y', $set_to_vars);
			}
			unset($a_board_rewrite_settings);
		}

		/**
		 * pretty uri를  command query로 재설정함
		 * pretty URL ?post/3 represents ?mod=post&post_id=3
		 * http://127.0.0.1/wp-x2board?post/168
		 * $_SERVER['REQUEST_URI']['path'] = /wp-x2board
		 * $_SERVER['REQUEST_URI']['query'] = post/168
		 */
		private function _convert_pretty_command_uri() {
			$a_cascaded_search_cmd = array('p' => 'page', 'cat' => 'category', 'tag' => 'tag', 
										   'search' => 'search_target', 'q' => 'search_keyword', 
										   'sort' => 'sort_field', 't' => 'sort_type');
			$a_query_param = array( 'cmd'=>null, 'page'=>null,
									'post_id'=>null, 'comment_id'=>null, 
									'search_target'=>null, 'search_keyword'=>null, 
									'sort_field'=>null, 'sort_type'=>null, 
									'tag'=>null, 'category'=>null
								);
			$request_uri = wp_parse_url( $_SERVER['REQUEST_URI'] );
			if( isset($request_uri['query'] ) )	{
				$s_uri = trim($request_uri['query']);
				if( preg_match( "/^[-\w.]+\/[0-9]*$/m", $s_uri ) ) { // ex) post/1234
					$a_uri = explode('/', sanitize_text_field( $s_uri ) );
					$s_cmd = trim($a_uri[0]);
					$n_val = intval($a_uri[1]);
					switch($s_cmd) {
						case 'p':
							// $a_query_param['cmd'] = X2B_CMD_VIEW_LIST;
							$a_query_param['page'] = $n_val;  // page_no
							break;
						case X2B_CMD_VIEW_POST:         // old_post_id
						case X2B_CMD_VIEW_MODIFY_POST:  // old_post_id
						case X2B_CMD_VIEW_DELETE_POST:  // old_post_id
						case X2B_CMD_VIEW_REPLY_POST:   // parent_post_id
						case X2B_CMD_VIEW_WRITE_COMMENT:    // parent_post_id
							$a_query_param['cmd'] = $s_cmd;
							$a_query_param['post_id'] = $n_val;
							break;
						case X2B_CMD_VIEW_MODIFY_COMMENT:   // old_comment_id
						case X2B_CMD_VIEW_DELETE_COMMENT:    // old_comment_id
							$a_query_param['cmd'] = $s_cmd;
							$a_query_param['comment_id'] = $n_val;
							break;
					}
					unset($a_uri);
				}
				elseif( preg_match( "/^[-\w.]+$/m", $s_uri ) ) { // ex) X2B_CMD_VIEW_WRITE_POST
					$s_cmd = sanitize_text_field( trim($s_uri) );
					if( $s_cmd == X2B_CMD_VIEW_WRITE_POST) {
						$a_query_param['cmd'] = $s_cmd;	
					}
				}
				// elseif( preg_match( "/^[-\w.]+\/[0-9]+\/[0-9]*$/m", $s_uri ) ) { // ex) reply_comment/123/456
				// 	$a_uri = explode('/', sanitize_text_field( $s_uri ) );
				// 	$s_cmd = trim($a_uri[0]);
				// 	if( $s_cmd == X2B_CMD_VIEW_REPLY_COMMENT) {
				// 		$a_query_param['cmd'] = $s_cmd;	
				// 		$a_query_param['post_id'] = intval($a_uri[1]);  // parent_post_id
				// 		$a_query_param['comment_id'] = intval($a_uri[2]);  // parent_comment_id
				// 	}
				// 	unset($a_uri);
				// }
				// elseif( preg_match( "/^[-\w.]+\/[-\w.]*$/m", $s_uri ) ) { // ex) cat/category_value   한글 숫자 영문 혼합 preg_match 불가능
				// 	$a_uri = explode('/', sanitize_text_field( $s_uri ) );
				// 	$s_cmd = trim($a_uri[0]);
				// 	switch($s_cmd) {
				// 		case 'cat':
				// 			// $a_query_param['cmd'] = X2B_CMD_VIEW_LIST;
				// 			$a_query_param['category'] = trim($a_uri[1]);
				// 			break;
				// 		case 'tag':
				// 			// $a_query_param['cmd'] = X2B_CMD_VIEW_LIST;
				// 			$a_query_param['tag'] = trim($a_uri[1]);
				// 			break;
				// 	}
				// 	unset($a_uri);
				// }
				elseif( preg_match( "/^[-\w.]+\/[-\w.]+\/[-\w.]+\/[-\w.]*$/m", $s_uri ) ) { // ex) search/search_field/q/search_value
					$a_uri = explode('/', sanitize_text_field( $s_uri ) );
					$s_cmd = trim($a_uri[0]);
					$s_query = trim($a_uri[2]);
					if( $s_cmd == 'search' && $s_query == 'q' ) {  // q means query
						$a_query_param['search_target'] = trim($a_uri[1]);
						$a_query_param['search_keyword'] = trim($a_uri[3]);
					}
					elseif( $s_cmd == 'sort' && $s_query == 't' ) {  // t means type
						$a_query_param['search_target'] = trim($a_uri[1]);
						$a_query_param['sort_type'] = trim($a_uri[3]);
					}
					unset($a_uri);
				}
				else { // cascaded search   ex) cat/category_value 
					$a_uri = explode('/', $s_uri );
					foreach( $a_uri as $n_idx => $s_val ) {
						if( $n_idx % 2 == 0 ) {
							if( isset( $a_cascaded_search_cmd[$s_val] ) ){
								$s_cmd = wp_unslash( $a_cascaded_search_cmd[$s_val]);
								$a_query_param[$s_cmd] = wp_unslash( $a_uri[$n_idx+1]);
							}
						}
					}
					unset($a_uri);					
				}
			}
			// all command should be set to avoid error on skin rendering
			foreach($a_query_param as $s_qry_name => $s_qry_val ) {
				if( is_null(self::get( $s_qry_name) ) ){  // 기존 값이 없으면 쓰기, do not unset any value from conventional URI
					self::set( $s_qry_name, $s_qry_val );
				}
			}
			unset($a_cascaded_search_cmd);
			unset($a_query_param);
			unset($request_uri);
		}

		/**
		 * 
		 */
		private function _recursiveCheckVar($val) {
			if(is_string($val)) {
				foreach($this->_a_patterns as $pattern) {
					if(preg_match($pattern, $val)) {
						$this->isSuccessInit = FALSE;
						return;
					}
				}
			}
			else if(is_array($val)) {
				foreach($val as $val2) {
					$this->_recursiveCheckVar($val2);
				}
			}
		}

		/**
		 * Return request method
		 * @return string Request method type. (Optional - GET|POST|XMLRPC|JSON)
		 */
		public static function getRequestMethod() {
			$o_self = self::getInstance();
			$s_request_method = $o_self->request_method;
			unset($o_self);
			return $s_request_method;
		}

		/**
		 * Finalize using resources, such as DB connection
		 *
		 * @return void
		 */
		public static function close() {
			// session_write_close();
		}

		/**
		 * Set a context value with a key
		 *
		 * @param string $key Key
		 * @param mixed $val Value
		 * @param mixed $set_to_get_vars If not FALSE, Set to get vars.
		 * @return void
		 */
		public static function set($key, $val, $set_to_get_vars = 0) {
			$o_self = self::getInstance();
			$o_self->context->{$key} = $val;
			if($set_to_get_vars === FALSE) {
				unset($o_self);
				return;
			}
			if($val === NULL || $val === '') {
				unset($o_self->get_vars->{$key});
				unset($o_self);
				return;
			}
			if($set_to_get_vars || !isset($o_self->get_vars->{$key})) {
				$o_self->get_vars->{$key} = $val;
			}	
			unset($o_self);
		}

		/**
		 * Return key's value
		 *
		 * @param string $key Key
		 * @return string Key
		 */
		public static function get($key) {
			$o_self = self::getInstance();
			if(!isset($o_self->context->{$key})) {
				unset($o_self);
				return null;
			}
			$o_rst = $o_self->context->{$key};
			unset($o_self);
			return $o_rst;
		}

		/**
		 * Get one more vars in object vars with given arguments(key1, key2, key3,...)
		 *
		 * @return object
		 */
		public static function gets() {
			$num_args = func_num_args();
			if($num_args < 1) {
				return;
			}
			$o_self = self::getInstance();
			$args_list = func_get_args();
			$output = new \stdClass();
			foreach($args_list as $v) {
				$output->{$v} = $o_self->get($v);
			}
			unset($o_self);
			return $output;
		}

		/**
		 * Return all data for \X2board\Includes\Classes\Skin::load()
		 *
		 * @return object All context data
		 */
		public static function getAll4Skin() {
			$o_self = self::getInstance();
			$a_rst = (array)$o_self->context;
			unset($o_self);
			return $a_rst;
		}

		/**
		 * Return values from the GET/POST/XMLRPC
		 *
		 * @return BaseObject Request variables.
		 */
		public static function getRequestVars() {
			$o_self = self::getInstance();
			if($o_self->get_vars) {
				$o_tmp = clone($o_self->get_vars);
				return $o_tmp;
			}
			unset($o_self);
			return new \stdClass;
		}

		/**
		 * Determine request method
		 *
		 * @param string $type Request method. (Optional - GET|POST|XMLRPC|JSON)
		 * @return void
		 */
		public static function setRequestMethod($type = '') {
			$o_self = self::getInstance();
			($type && $o_self->request_method = $type) or
			(isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'json') && $o_self->request_method = 'JSON') or ($o_self->request_method = $_SERVER['REQUEST_METHOD']);
			unset($o_self);
		}

		/**
		 * Make URL with args_list upon request URL
		 * warning: this method is for GET request only as this requires $this->_convert_pretty_command_uri() executed, if POST not work
		 * getUrl($num_args = 0, $args_list = array(), $domain = null, $encode = TRUE, $autoEncode = FALSE) {
		 * @param int $num_args Arguments nums
		 * @param array $args_list Argument list for set url
		 * @param string $domain Domain
		 * @param bool $encode If TRUE, use url encode.
		 * @param bool $autoEncode If TRUE, url encode automatically, detailed. Use this option, $encode value should be TRUE
		 * @return string URL
		 */
		public static function get_url($num_args = 0, $args_list = array(), $domain = null, $encode = TRUE, $autoEncode = FALSE) {
			static $current_info = null;
			$domain = get_site_url().'/';
			// if $domain is set, compare current URL. If they are same, remove the domain, otherwise link to the domain.
			if($domain)	{
				$domain_info = parse_url($domain);
				if(is_null($current_info)) {
					if( !isset($_SERVER['HTTPS']) ) {
						$_SERVER['HTTPS'] = null;
					}
					$current_info = parse_url(($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . \X2board\Includes\get_script_path());
				}

				if($domain_info['host'] . $domain_info['path'] == $current_info['host'] . $current_info['path']) {
					$domain = null;
				}
				else {
					$domain = preg_replace('/^(http|https):\/\//i', '', trim($domain));
					if(substr_compare($domain, '/', -1) !== 0) {
						$domain .= '/';
					}
				}
			}
			
			$get_vars = array();
			$o_self = self::getInstance();

			// If there is no GET variables or first argument is '' to reset variables
			if(!$o_self->get_vars || $args_list[0] == '') {
				// rearrange args_list
				if(is_array($args_list) && $args_list[0] == '') {
					array_shift($args_list);
				}
			}
			elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
				// Otherwise, make GET variables into array
				$get_vars = get_object_vars($o_self->gets('cmd', 'post_id', 'page', 'category','search_target','search_keyword'));
				// 이 조건문 작동하면 ?cmd=view_post&post_id=17&cpage=2#17_comment 와 같은 댓글 페이지 처리가 안됨
				// if( isset( $get_vars['cmd'] ) && $get_vars['cmd'] == X2B_CMD_VIEW_POST &&
				// 	isset( $get_vars['post_id'] ) && intval($get_vars['post_id']) > 0 ) {  // regarding view_post/10 as /10; view_post cmd malfunctions on title link of the view post UX 
						// $get_vars['cmd'] = null;
					// }
			}
			else { // POST method
				if(!!$o_self->get_vars->cmd) $get_vars['cmd'] = $o_self->get_vars->cmd;
				if(!!$o_self->get_vars->page) $get_vars['page'] = $o_self->get_vars->page;
				if(!!$o_self->get_vars->search_target) $get_vars['search_target'] = $o_self->get_vars->search_target;
				if(!!$o_self->get_vars->search_keyword) $get_vars['search_keyword'] = $o_self->get_vars->search_keyword;
			}

			if( isset($get_vars['search_target']) && is_null($get_vars['search_target'])){
				unset($get_vars['search_target']);
			}
			if( isset($get_vars['search_keyword']) && is_null($get_vars['search_keyword'])){
				unset($get_vars['search_keyword']);
			}

			// arrange args_list
			for($i = 0, $c = count((array)$args_list); $i < $c; $i += 2) {
				$key = $args_list[$i];
				$val = trim($args_list[$i + 1]);
				// If value is not set, remove the key
				if( $key != 'cmd') {  // keep cmd set always
					if(!isset($val) || !strlen($val)) {
						unset($get_vars[$key]);
						continue;
					}
				}
				// set new variables
				$get_vars[$key] = $val;
			}

			// organize URL
			$query = '';
			if(count($get_vars) > 0) {
				$cmd = isset( $get_vars['cmd'] ) ? $get_vars['cmd'] : '';
				$page = isset( $get_vars['page'] ) ? $get_vars['page'] : '';
				$post_id = isset( $get_vars['post_id'] ) ? $get_vars['post_id'] : '';
				$s_category_title = isset( $get_vars['category'] ) ? $get_vars['category'] : '';

				$target_map = array(
					'cmd' => $o_self->_s_page_permlink.( strlen($cmd) > 0 ? '?'.$cmd : '' ),  // X2B_CMD_VIEW_LIST equals with blank cmd
					'page' => $o_self->_s_page_permlink.'?p/'.$page,
					'post_id' => $o_self->_s_page_permlink.'?'.X2B_CMD_VIEW_POST.'/'.$post_id,
					'cmd.post_id.search_keyword.search_target' => $o_self->_s_page_permlink.'?'.X2B_CMD_VIEW_POST.'/'.$post_id,
					'cmd.post_id' => '', // reserved for pretty post url  // $self->_s_page_permlink.'?'.$cmd.'/'.$post_id,
					'cmd.page' => $o_self->_s_page_permlink.'?p/'.$page,
					'category.cmd.post_id' => $o_self->_s_page_permlink.'?cat/'.$s_category_title,
				);
				// cmd.comment_id.page.post_id..
				$a_check_query = array( 'cmd', 'post_id', 'category', 'tag', 'search_keyword', 'search_target' );
				foreach( $a_check_query as $key_name ) {  // remove if null to avoid $target_map malfunction
					if( array_key_exists($key_name, $get_vars) && is_null($get_vars[$key_name]) ) {
						unset($get_vars[$key_name]);
					}
				}
	
				if( array_key_exists('page', $get_vars) ) {
					if( is_null($get_vars['page']) || $get_vars['page'] == 1 ) {
						unset($get_vars['page']);
					}
				}
				
				$var_keys = array_keys($get_vars);
				sort($var_keys);						
				$target = join('.', $var_keys);
				$query = isset( $target_map[$target] ) ? $target_map[$target] : null;
				// try best to provie prettier post URL as possible
				if( self::get('use_rewrite') == 'Y' ) {
					if( $target == 'cmd.post_id' ) {
						$query = $cmd == X2B_CMD_VIEW_POST ? $o_self->_s_page_permlink.'/'.$post_id : $query .='?'.$cmd.'/'.$post_id;
					}
				}
			}
			if(!$query)	{
				$queries = array();
				foreach($get_vars as $key => $val) {
					if(is_array($val) && count($val) > 0) {
						foreach($val as $k => $v) {
							$queries[] = $key . '[' . $k . ']=' . urlencode($v);
						}
					}
					elseif(!is_array($val))	{
						$queries[] = $key . '=' . urlencode($val);
					}
				}

				$query = $o_self->_s_page_permlink;
				$n_cnt_queires = count($queries);
				if($n_cnt_queires > 0) {
					$query .= '?' . join('&', $queries);
				}
			}
			unset($o_self);

			if(!$encode) {
				return $query;
			}

			if(!$autoEncode) {
				return htmlspecialchars($query, ENT_COMPAT | ENT_HTML401, 'UTF-8', FALSE);
			}
			wp_die('bottom part of \X2board\Includes\Classes\get_url() executed specially');
		}

		/**
		 * Get lang_type
		 *
		 * @return string Language type
		 */
		public static function getLangType() {
			$a_locale = array('ko_KR' => 'ko', 'en_GB'=>'en');
			if( !isset($a_locale[get_locale()]) ) {
				wp_die(__('msg_undefined_locale', X2B_DOMAIN));
			}
			return $a_locale[get_locale()];
		}

		/**
		 * Filter request variable
		 * _filterRequestVar($key, $val, $do_stripslashes = true, $remove_hack = false)
		 * @see Cast variables, such as _srl, page, and cpage, into interger
		 * @param string $key Variable key
		 * @param string $val Variable value
		 * @param string $do_stripslashes Whether to strip slashes
		 * @return mixed filtered value. Type are string or array
		 */
		private function _filter_request_var($key, $val, $do_stripslashes = true, $remove_hack = false) {
			if(!($isArray = is_array($val))) {
				$val = array($val);
			}

			$result = array();
			foreach($val as $k => $v) {
				$k =  \X2board\Includes\escape($k);
				$result[$k] = $v;

				if( $_SERVER['SCRIPT_NAME'] == '/wp-admin/admin.php' ) {  // for admin screen
					$result[$k] = \X2board\Includes\escape($result[$k], false);
				}
				elseif($key === 'page' || $key === 'cpage' ) {
					$result[$k] = !preg_match('/^[0-9,]+$/', $result[$k]) ? (int) $result[$k] : $result[$k];	
				}
			}
			return $isArray ? $result : $result[0];
		}
	}  // END CLASS
}
/* End of file Context.class.php */