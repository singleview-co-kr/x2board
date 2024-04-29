<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postModel class
 * model class of the module post
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 * @version 0.1
 */
namespace X2board\Includes\Modules\Post;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\postModel')) {

	class postModel extends post {
		private $_a_default_fields = array();
		private $_a_extends_fields = array();
		private $_a_user_define_fields = array();

		// private $documentConfig = NULL;
		
		/**
		 * constructor
		 *
		 * @return void
		 */
		public function __construct() {
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['EXTRA_VARS'])) {
				$G_X2B_CACHE['EXTRA_VARS'] = array();
			}

			if(!isset($G_X2B_CACHE['X2B_USER_DEFINE_KEYS'])) {
				$G_X2B_CACHE['X2B_USER_DEFINE_KEYS'] = array();
			}

			$o_post_user_define_fields = \X2board\Includes\Classes\UserDefineFields::getInstance();
			$this->_a_default_fields = $o_post_user_define_fields->get_default_fields();
			$this->_a_extends_fields = $o_post_user_define_fields->get_extended_fields();
			unset($o_post_user_define_fields);
			$this->_set_user_define_fields();
		}

		/**
		 * Initialization
		 * @return void
		 */
		// function init() {}

		/**
		 * bringing the list of documents
		 * @param object $obj
		 * @param bool $except_notice
		 * @param bool $load_extra_vars
		 * @param array $columnList
		 * @return BaseObject
		 */
		// function getDocumentList($obj, $except_notice = false, $load_extra_vars=true, $columnList = array())
		public function get_post_list($obj, $except_notice = false, $load_extra_vars=true, $columnList = array()) {
			global $G_X2B_CACHE;
// var_dump($obj);
			$o_sort_check = $this->_set_sort_index($obj, $load_extra_vars);
			
			$obj->sort_index = $o_sort_check->sort_index;
			$obj->isExtraVars = $o_sort_check->isExtraVars;
			// unset($obj->use_alternate_output);
			$obj->columnList = $columnList;
			// Call trigger (before)
			// This trigger can be used to set an alternative output using a different search method
			// $output = ModuleHandler::triggerCall('document.getDocumentList', 'before', $obj);
			// if($output instanceof BaseObject && !$output->toBool())
			// {
			// 	return $output;
			// }

			$o_search_check = $this->_set_search_option($obj, $args, $query_id, $use_division);
// var_dump($args);
// var_dump($query_id);
			// if ($o_sort_check->isExtraVars && substr_count($obj->search_target,'extra_vars'))
			// {
			// 	$query_id = 'document.getDocumentListWithinExtraVarsExtraSort';
			// 	$args->sort_index = str_replace('documents.','',$args->sort_index);
			// 	$output = executeQueryArray($query_id, $args);
			// }
			// elseif ($o_sort_check->isExtraVars)
			// {
			// 	$output = executeQueryArray($query_id, $args);
			// }
			// else
			{
				$query_id = 'post.getPostList';   // basic document list query
				// document.getDocumentList query execution
				// Query_id if you have a group by clause getDocumentListWithinTag getDocumentListWithinComment or used again to perform the query because
				$groupByQuery = array('post.getPostListWithinComment' => 1, 'post.getPostListWithinTag' => 1, 'post.getPostListWithinExtraVars' => 1);
				if(isset($groupByQuery[$query_id]))	{
					$group_args = clone($args);
					$group_args->sort_index = 'documents.'.$args->sort_index;
					$output = executeQueryArray($query_id, $group_args);
					if(!$output->toBool()||!count($output->data)) return $output;

					foreach($output->data as $key => $val) {
						if($val->document_srl) $target_srls[] = $val->document_srl;
					}

					$page_navigation = $output->page_navigation;
					$keys = array_keys($output->data);
					$virtual_number = $keys[0];

					$target_args = new stdClass();
					$target_args->document_srls = implode(',',$target_srls);
					$target_args->list_order = $args->sort_index;
					$target_args->order_type = $args->order_type;
					$target_args->list_count = $args->list_count;
					$target_args->page = 1;
					$output = executeQueryArray('post.getPosts', $target_args);
					$output->page_navigation = $page_navigation;
					$output->total_count = $page_navigation->total_count;
					$output->total_page = $page_navigation->total_page;
					$output->page = $page_navigation->cur_page;
				}
				else { // basic document list query
					// $query_id = 'post.getPostList';
					global $wpdb;
					$o_query = new \stdClass();
					$o_query->s_tables = '`'.$wpdb->prefix.'x2b_posts`';
					$o_query->s_columns = "*";
					// $o_query->s_where = "WHERE `board_id`=".$obj->wp_page_id." AND `status` in ('SECRET', 'PUBLIC')"; // and `list_order` <= 2100000000";
					// $o_query->s_orderby = "ORDER BY `list_order` asc";
					$o_query->s_where = $o_search_check->s_where;
					$o_query->s_orderby = $o_search_check->s_orderby;
					$o_query->page = $obj->page;
					$o_query->list_count = $obj->list_count;
					$o_query->page_count = $obj->page_count;
					$output = \X2board\Includes\executeQueryArray($o_query, $columnList); // $query_id, $args, $columnList);
					unset($o_query);
				}
			}
			unset($o_sort_check);
			unset($o_search_check);

			// Return if no result or an error occurs
			if(!$output->toBool()||!count($output->data)) {
				return $output;
			}

			$idx = 0;
			$data = $output->data;
			unset($output->data);
			
			if(!isset($virtual_number))	{
				$keys = array_keys($data);
				$virtual_number = $keys[0];
			}

			if($except_notice) {
				foreach($data as $key => $attribute) {
					if($attribute->is_notice == 'Y') $virtual_number --;
				}
			}

			$output->data = array();
			foreach($data as $key => $attribute) {
				if($except_notice && $attribute->is_notice == 'Y') continue;
				$post_id = $attribute->post_id;
				if(!isset($G_X2B_CACHE['POST_LIST'][$post_id])) {
					$o_post = null;
					$o_post = new \X2board\Includes\Modules\Post\postItem();
					$o_post->set_attr($attribute, false);
					// if($is_admin) $oDocument->setGrant();  // never executed command
					$G_X2B_CACHE['POST_LIST'][$post_id] = $o_post;
				}

				$output->data[$virtual_number] = $G_X2B_CACHE['POST_LIST'][$post_id];
				$virtual_number--;
			}

			if($load_extra_vars) {
				$this->_set_to_all_post_extra_vars();
			}

			if(count($output->data)) {
				foreach($output->data as $number => $post) {
					$output->data[$number] = $G_X2B_CACHE['POST_LIST'][$post->post_id];
				}
			}
// var_dump($G_X2B_CACHE );
			// Call trigger (after)
			// This trigger can be used to modify search results
			// ModuleHandler::triggerCall('document.getDocumentList', 'after', $output);
			return $output;
		}

		/**
		 * Module_srl value, bringing the document's gongjisa Port
		 * @param object $obj
		 * @param array $columnList
		 * @return object|void
		 */
		// function getNoticeList($obj, $columnList = array())
		public function get_notice_list($obj, $columnList = array()) {
			// $args = new \stdClass();
			// $args->module_srl = $obj->module_srl;
			// $args->category_srl= $obj->category_srl;
			// $output = executeQueryArray('document.getNoticeList', $args, $columnList);

			global $wpdb;
			$o_query = new \stdClass();
			$o_query->s_query_type = 'select';
			// $o_query->s_table_name = 'x2b_posts';
			$o_query->s_tables = '`'.$wpdb->prefix.'x2b_posts`';
			// $o_query->s_columns = "`title`, `nick_name`, `regdate_dt`, `readed_count`, `is_notice`, `post_id`, `board_id`, `category_id`, `post_author`, `content`, `last_update_dt`, `comment_count`, `voted_count`, `uploaded_count`, `status`, `title_bold`, `title_color`, `tags`";
			$o_query->s_columns = "*";
			$o_query->s_where = "WHERE `board_id`=".$obj->wp_page_id." AND `is_notice`='Y' AND `status` in ('PUBLIC')"; // and `list_order` <= 2100000000";
			$o_query->s_orderby = "ORDER BY `list_order` desc";
			$output = \X2board\Includes\executeQueryArray($o_query, $columnList); // $query_id, $args, $columnList);
			unset($o_query);
			if(!$output->toBool()||!$output->data) 
				return $output;
			
			global $G_X2B_CACHE;
			$result = new \stdClass();
			foreach($output->data as $key => $val) {
				$post_id = $val->post_id;
				if(!$post_id) continue;

				if(!isset($G_X2B_CACHE['POST_LIST'][$post_id])) {
					$o_post = null;
					$o_post = new postItem();
					$o_post->set_attr($val, false);
					$G_X2B_CACHE['POST_LIST'][$post_id] = $o_post;
				}
				$result->data[$post_id] = $G_X2B_CACHE['POST_LIST'][$post_id];
			}
			$this->_set_to_all_post_extra_vars();
// var_dump($G_X2B_CACHE);
			foreach($result->data as $post_id => $val) {
				$result->data[$post_id] = $G_X2B_CACHE['POST_LIST'][$post_id];
			}
			return $result;
		}

		/**
		 * Import post
		 * @param int $post_id
		 * @param bool $is_admin
		 * @param bool $load_extra_vars
		 * @param array $columnList
		 * @return postItem
		 */
		// function getDocument($document_srl=0, $is_admin = false, $load_extra_vars=true, $columnList = array())
		public function get_post($n_post_id=0, $is_admin = false, $load_extra_vars=true, $columnList = array()) {
			if(!$n_post_id) {
				return new postItem();
			}
			global $G_X2B_CACHE;
			// if(!$GLOBALS['XE_DOCUMENT_LIST'][$post_id])
			if(!isset($G_X2B_CACHE['POST_LIST'][$n_post_id])) {
				$o_post = new postItem($n_post_id, $load_extra_vars, $columnList);				

				if(!$o_post->is_exists()) {
					return $o_post;
				}
				$G_X2B_CACHE['POST_LIST'][$n_post_id] = $o_post;
				if($load_extra_vars) {
					$this->_set_to_all_post_extra_vars();
				}
			}
			if($is_admin) {
				$G_X2B_CACHE['POST_LIST'][$n_post_id]->set_grant();
			} 
			return $G_X2B_CACHE['POST_LIST'][$n_post_id];
		}

		/**
		 * 게시판 사용자 포스트 작성 화면용 필드 정뵤 반환
		 * @return array
		 */
		public function get_default_fields() {
			// $this->_set_user_define_fields();
			$a_default_fields = $this->_a_default_fields;
			foreach($a_default_fields as $key=>$value) {
				if($this->_a_user_define_fields) {
					if(isset($this->_a_user_define_fields[$key])){
						unset($a_default_fields[$key]);
					}
				}
			}
			return $a_default_fields;
		}

		/**
		 * 확장 필드를 반환한다.
		 * @return array
		 */
		public function get_extended_fields() {
			return $this->_a_extends_fields;
		}

		/**
		 * Returns a list of user-defined fields, excluding default fields.
		 * differ with \includes\modules\post\post.item.php::get_user_define_extended_fields()
		 * this method returns list of the designated board 
		 * @return array
		 */
		public function get_user_define_extended_fields($n_board_id) {
			$a_user_define_keys = $this->get_user_define_keys($n_board_id);
			
			$o_post_user_define_fields = \X2board\Includes\Classes\UserDefineFields::getInstance();
			$a_default_fields = $o_post_user_define_fields->get_default_fields();
			unset($o_post_user_define_fields);
			$a_ignore_field_type = array_keys($a_default_fields);
			$a_user_define_extended_fields = array();
			foreach($a_user_define_keys as $n_seq=>$o_field){
				$field_type = (isset($o_field->type) && $o_field->type) ? $o_field->type : '';
				if(in_array($field_type, $a_ignore_field_type) ){ // ignore default fields
					continue;
				}
				$a_user_define_extended_fields[$n_seq] = $o_field;
			}
			return $a_user_define_extended_fields;
		}

		/**
		 * retrieve user define fields from DB
		 * admin: 'field_name' => db: var_name  관리자 화면에서 [필드 레이블] 입력란은 field_name에 저장함
		 * admin: 'field_type' => db: var_type
		 * admin: 'meta_key' => db: eid
		 * admin: 'default_value' => db: var_default
		 * admin: 'description' => db: var_desc
		 * admin: 'required' => db: var_is_required
		 * 
		 * admin: 'field_label' => db: ??  관리자 화면에서 용도 불명, 사용자 화면에서 기본 필드명 표시위한 용도
		 */
		private function _set_user_define_fields() { //$skin_fields){
			if( !empty($this->_a_user_define_fields ) ){
				return;
			}
// var_dump($_GET['board_id']);
			// $this->_n_board_id = intval(sanitize_text_field($_GET['board_id'] ));
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$s_columns = '`var_name`, `var_type`, `var_is_required`, `var_search`, `var_default`, `var_desc`, `eid`, `json_param`';
			global $wpdb;
			$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC");
// var_dump($a_temp);
			
			foreach( $a_temp as $_ => $o_field ) {
				$a_other_field = unserialize($o_field->json_param);

				$a_single_field['field_type'] = $o_field->var_type;
				// $a_single_field['field_label'] = $o_field->var_name;
				$a_single_field['field_name'] = $o_field->var_name;
				$a_single_field['meta_key'] = $o_field->eid;
				$a_single_field['default_value'] = $o_field->var_default;
				$a_single_field['description'] = $o_field->var_desc;
				$a_single_field['required'] = $o_field->var_is_required;
				if( isset( $this->_a_default_fields[$o_field->var_type] )){
					$a_single_field['class'] = $this->_a_default_fields[$o_field->var_type]['class']; //'x2board-class';	
				}
				elseif( isset( $this->_a_extends_fields[$o_field->var_type] )){
					$a_single_field['class'] = $this->_a_extends_fields[$o_field->var_type]['class']; //'x2board-class';	
				}

				$a_single_field = array_merge($a_single_field, $a_other_field);
				$this->_a_user_define_fields[$o_field->eid] = $a_single_field;

				unset($a_single_field);
				unset($a_other_field);
			}
			unset($a_temp);
// var_dump($this->_a_user_define_fields);
		}

		/**
		 * 관리자가 설정한 입력 필드를 반환한다.
		 * @return array
		 */
		// getSkinFields() {
		public function get_user_define_fields() {
			$a_fields = array();
			if($this->_a_user_define_fields) {
				$a_fields = $this->_a_user_define_fields;
			}
			else {
				$a_fields = $this->_a_default_fields;
			}
// var_dump($this->_a_user_define_fields);
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$o_user_define_field = \X2board\Includes\Classes\UserDefineFields::getInstance();
			$o_user_define_field->set_board_id($n_board_id);
			$o_user_define_field->set_user_define_keys_2_submit($a_fields);
			return $o_user_define_field->get_user_define_vars();
		}
	
		/**
		 * A particular post to get the value of the extra variable function
		 * @param int $n_post_id
		 * @return array
		 */
		// function getExtraVars($module_srl, $document_srl)
		public function get_user_define_vars( $n_post_id ) {
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['EXTRA_VARS'][$n_post_id])) {
				// Extended to extract the values of variables set
				// $o_post = $this->getDocument($board_id, false);
				$G_X2B_CACHE['POST_LIST'][$n_post_id] = $this->get_post($n_post_id, false); //$o_post;
				$this->_set_to_all_post_extra_vars();
			}
			global $G_X2B_CACHE;
// var_dump($n_post_id);
// var_dump($G_X2B_CACHE['EXTRA_VARS'][$n_post_id]);			
			if(isset($G_X2B_CACHE['EXTRA_VARS'][$n_post_id])) {

				if(is_array($G_X2B_CACHE['EXTRA_VARS'][$n_post_id])) {
					ksort($G_X2B_CACHE['EXTRA_VARS'][$n_post_id]);
				} 
				return $G_X2B_CACHE['EXTRA_VARS'][$n_post_id];
			}
			return null;
		}	

		/**
		 * Extra variables for each article will not be processed bulk select and apply the macro city
		 * @return void
		 */
		// function setToAllDocumentExtraVars()
		private function _set_to_all_post_extra_vars() {
			global $G_X2B_CACHE;
			static $checked_posts = array();
			$_post_list = &$G_X2B_CACHE['POST_LIST'];

			// X2B POST_LIST all posts that the object referred to the global variable settings
			if(count($_post_list) <= 0) {
				return;
			}
// var_dump('_set_to_all_post_extra_vars');			
			// Find all called the document object variable has been set extension
			$post_ids = array();
			foreach($_post_list as $key => $val) {
// var_dump($key);
// var_dump($val);
				if(!$val->post_id || isset($checked_posts[$val->post_id])) {
					continue;
				}
				$checked_posts[$val->post_id] = true;
				$post_ids[] = $val->post_id;
			}
			// If the document number, return detected
			if(!count($post_ids)) {
				return;
			}

			// Expand variables mijijeongdoen article about a current visitor to the extension of the language code, the search variable
			$a_rst = $this->get_post_user_define_vars_from_DB($post_ids);
			// unset($post_ids);

			$extra_vars = array();
			if($a_rst !== false && $a_rst) {
				foreach($a_rst as $_ => $o_val) {
					if(!isset($o_val->value)) {
						continue;
					}
					if(!isset($extra_vars[$o_val->board_id][$o_val->post_id][$o_val->var_idx][0])) {
						$extra_vars[$o_val->board_id][$o_val->post_id][$o_val->var_idx][0] = trim($o_val->value);
					}
					$o_val->lang_code = 'ko';
					$extra_vars[$o_val->post_id][$o_val->var_idx][$o_val->lang_code] = trim($o_val->value);
				}
			}

			$user_lang_code = 'ko'; //Context::getLangType();
			for($i=0,$c=count($post_ids);$i<$c;$i++) {
				$n_post_id = $post_ids[$i];
				unset($vars);
				if(!$_post_list[$n_post_id] || !is_object($_post_list[$n_post_id]) || !$_post_list[$n_post_id]->is_exists()) {
					continue;
				}
				$n_board_id = $_post_list[$n_post_id]->get('board_id');
				$extra_keys = $this->get_user_define_keys($n_board_id);
				
				if(isset($extra_vars[$n_post_id])) {
					$vars = $extra_vars[$n_post_id];  // user define field의 실제 입력값 추출
					$post_lang_code = 'ko'; //$_post_list[$n_post_id]->get('lang_code');
					// Expand the variable processing
					if(count($extra_keys)) {
						foreach($extra_keys as $n_idx => $key) {
							$extra_keys[$n_idx] = clone($key);
							if(isset($vars[$n_idx])) {
								$val = $vars[$n_idx];
								// var_dump($val);	
								if(isset($val[$user_lang_code])) {
									$v = $val[$user_lang_code];
								}
								else if(isset($val[$post_lang_code])) {
									$v = $val[$post_lang_code];
								}
								else if(isset($val[0])) {
									$v = $val[0];
								}							
							}
							else {
								$v = null;
							}
							$extra_keys[$n_idx]->value = $v;
						}
					}
				}
				
				unset($evars);
				// $evars = new ExtraVar($n_board_id);
				// $evars->setExtraVarKeys($extra_keys);
				$evars = new \X2board\Includes\Classes\UserDefineFields(); //$n_board_id);
// var_dump($extra_keys);
				$evars->set_user_define_keys_2_display($extra_keys);
				
				// Title Processing
				// if($vars[-1][$user_lang_code]) {
				// 	$_post_list[$n_post_id]->add('title',$vars[-1][$user_lang_code]);
				// }
				// Information processing
				// if($vars[-2][$user_lang_code]) {
				// 	$_post_list[$n_post_id]->add('content',$vars[-2][$user_lang_code]);
				// }
				// $GLOBALS['EXTRA_VARS'][$n_post_id] = $evars->getExtraVars();
				$G_X2B_CACHE['EXTRA_VARS'][$n_post_id] = $evars->get_user_define_vars();
// var_dump($n_post_id);
// var_dump($G_X2B_CACHE['EXTRA_VARS'][$n_post_id]);
			}
		}

		/**
		 * Return document extra information from database
		 * @param array $documentSrls
		 * @return object
		 */
		// function getDocumentExtraVarsFromDB($documentSrls)
		public function get_post_user_define_vars_from_DB($a_post_id) {
			if(!is_array($a_post_id) || count($a_post_id) == 0) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', 'x2board') );
			}
			// $args = new stdClass();
			// $args->document_srl = $documentSrls;
			// $output = executeQueryArray('document.getDocumentExtraVars', $args);
			global $wpdb;
			$s_tables = '`'.$wpdb->prefix.'x2b_user_define_vars`';
			$s_where = '`board_id` >= -1 and `post_id` in ('.implode(',', $a_post_id).') and `var_idx` >= -2';
			$a_temp = $wpdb->get_results("SELECT * FROM {$s_tables} WHERE {$s_where}");
			if ($a_temp === null) {
				wp_die($wpdb->last_error);
			} 
			else {
				$wpdb->flush();
			}
			return $a_temp;
		}

		/**
		 * Import page of the post
		 * @param posttItem $o_post
		 * @param object $opt
		 * @return int
		 */
		// function getDocumentPage($o_post, $opt)
		public function get_post_page($o_post, $o_in_args) {
			$o_sort_check = $this->_set_sort_index($o_in_args, TRUE);
			$o_in_args->sort_index = $o_sort_check->sort_index;
			$o_in_args->isExtraVars = $o_sort_check->isExtraVars;

			$o_search_check = $this->_set_search_option($o_in_args, $args, $query_id, $use_division);

			if($o_sort_check->isExtraVars) {
				return 1;
			}
			else {
				if($o_sort_check->sort_index === 'list_order' || $o_sort_check->sort_index === 'update_order') {
					if($args->order_type === 'desc') {
						$args->{'rev_' . $o_sort_check->sort_index} = $o_post->get($o_sort_check->sort_index);
					}
					else {
						$args->{$o_sort_check->sort_index} = $o_post->get($o_sort_check->sort_index);
					}
				}
				elseif($o_sort_check->sort_index === 'regdate_dt') {

					if($args->order_type === 'asc') {
						$args->{'rev_' . $o_sort_check->sort_index} = $o_post->get($o_sort_check->sort_index);
					}
					else {
						$args->{$o_sort_check->sort_index} = $o_post->get($o_sort_check->sort_index);
					}
				}
				else {
					return 1;
				}
			}
// var_dump($query_id);
// var_dump($o_in_args);
			// total number of the article search page
			$query_id .= 'Page';  // $output = executeQuery($query_id . 'Page', $args);
			global $wpdb;
			$s_tables = '`'.$wpdb->prefix.'x2b_posts`';
			$s_query = "SELECT COUNT(*) as `rec_cnt` FROM {$s_tables}";
			
			if( $query_id == 'post.getPostListPage' ) {
				// SELECT count(`document_srl`) as `count` FROM `xe_documents` as `documents` WHERE `module_srl` in (?) and `status` in (?,?) and ( `list_order` <= ? ) 
				if( isset($args->list_order) ) {
					$o_search_check->s_where .= " AND `list_order` <= ".$args->list_order;
				}
			}

			$s_query .= " {$o_search_check->s_where} {$o_search_check->s_orderby}";
			$o_rec_cnt = $wpdb->get_row($s_query);
			if ($o_rec_cnt === null) {
				wp_die($wpdb->last_error);
			} 
			else {
				$wpdb->flush();
			}
			$count = intval($o_rec_cnt->rec_cnt);  // $count = $output->data->count;
			$n_page = (int)(($count-1)/$o_in_args->list_count)+1;
			return $n_page;
		}

		/**
		 * Setting sort index
		 * @param object $obj
		 * @param bool $load_extra_vars
		 * @return object
		 */
		// private function _setSortIndex($obj, $load_extra_vars)
		private function _set_sort_index($obj, $load_extra_vars) {
			$sortIndex = $obj->sort_index;
			$isExtraVars = false;
			$a_sortable_field = array('list_order','regdate_dt','last_update_dt','update_order','readed_count',
									  'voted_count','comment_count','uploaded_count','title',
									  'category_id');
			if(!in_array($sortIndex, $a_sortable_field)) {
				// get module_srl extra_vars list
				if ($load_extra_vars) {
					// $o_extra_args = new \stdClass();
					// $o_extra_args->module_srl = $obj->module_srl;
					$extra_output = executeQueryArray('post.getGroupsExtraVars', $extra_args);
					if (!$extra_output->data || !$extra_output->toBool()) {
						$sortIndex = 'list_order';
					}
					else {
						$check_array = array();
						foreach($extra_output->data as $val) {
							$check_array[] = $val->eid;
						}
						if(!in_array($sortIndex, $check_array)) {
							$sortIndex = 'list_order';
						}
						else {
							$isExtraVars = true;
						}
					}
				}
				else {
					$sortIndex = 'list_order';
				}
			}
			unset($a_sortable_field);
			$o_rst = new \stdClass();
			$o_rst->sort_index = $sortIndex;
			$o_rst->isExtraVars = $isExtraVars;
			return $o_rst;
		}

		/**
		 * 게시물 목록의 검색 옵션을 Setting함(2011.03.08 - cherryfilter)
		 * page변수가 없는 상태에서 page 값을 알아오는 method(getDocumentPage)는 검색하지 않은 값을 return해서 검색한 값을 가져오도록 검색옵션이 추가 됨.
		 * 검색옵션의 중복으로 인해 private method로 별도 분리
		 * @param object $searchOpt
		 * @param object $args
		 * @param string $query_id
		 * @param bool $use_division
		 * @return void
		 */
		private function _set_search_option($searchOpt, &$args, &$query_id, &$use_division) {
			// Variable check
// var_dump($searchOpt);		
			$args = new \stdClass();
			$args->category_id = $searchOpt->category_id ? $searchOpt->category_id : null;
			$args->order_type = $searchOpt->order_type;
			$args->page = $searchOpt->page ? $searchOpt->page : 1;
			$args->list_count = $searchOpt->list_count?$searchOpt->list_count:20;
			$args->page_count = $searchOpt->page_count?$searchOpt->page_count:10;
			$args->start_date = isset($searchOpt->start_date) ? $searchOpt->start_date : null;
			$args->end_date = isset($searchOpt->end_date) ? $searchOpt->end_date : null;
			// $args->member_srl = $searchOpt->member_srl;
			// $args->member_srls = $searchOpt->member_srls;
			$args->sort_index = $searchOpt->sort_index;

			// Check the target and sequence alignment
			$orderType = array('desc' => 1, 'asc' => 1);
			if(!isset($orderType[$args->order_type])) {
				$args->order_type = 'asc';
			}

			// If that came across mid module_srl instead of a direct module_srl guhaejum
			// if($searchOpt->mid) {
			// 	$oModuleModel = getModel('module');
			// 	$args->module_srl = $oModuleModel->getModuleSrlByMid($obj->mid);
			// 	unset($searchOpt->mid);
			// }

			// Module_srl passed the array may be a check whether the array
			// if(is_array($searchOpt->module_srl)) {
			// 	$args->module_srl = implode(',', $searchOpt->module_srl);
			// }
			// else {
			// 	$args->module_srl = $searchOpt->module_srl;
			// }

			// Except for the test module_srl
			// if(is_array($searchOpt->exclude_module_srl)) {
			// 	$args->exclude_module_srl = implode(',', $searchOpt->exclude_module_srl);
			// }
			// else {
			// 	$args->exclude_module_srl = $searchOpt->exclude_module_srl;
			// }
			$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			// only admin document list, temp document showing
			if(isset($searchOpt->statusList)) {
				$args->statusList = $searchOpt->statusList;
			}
			else {
				// if($logged_info->is_admin == 'Y' ) && !$searchOpt->module_srl) {
				// 	$args->statusList = array($this->getConfigStatus('secret'), $this->getConfigStatus('public'), $this->getConfigStatus('temp'));
				// }
				// else {
					$args->statusList = array($this->get_config_status('secret'), $this->get_config_status('public'));
				// }
			}

			// Category is selected, further sub-categories until all conditions
			if($args->category_id) {
var_dump('plz define category search');
				// $category_list = $this->getCategoryList($args->module_srl);
				// $category_info = $category_list[$args->category_id];
				// $category_info->childs[] = $args->category_id;
				// $args->category_id = implode(',',$category_info->childs);
			}

			// Used to specify the default query id (based on several search options to query id modified)
			$query_id = 'post.getPostList';

			// If the search by specifying the document division naeyonggeomsaekil processed for
			$use_division = false;

			// Search options
			$search_target = $searchOpt->search_target;
			$search_keyword = $searchOpt->search_keyword;

			if($search_target && $search_keyword) {
				switch($search_target)
				{
					case 'title' :
					case 'content' :
						if($search_keyword) {
							$search_keyword = str_replace(' ','%',$search_keyword);
						}
						$args->{"s_".$search_target} = $search_keyword;
						$use_division = true;
						break;
					case 'title_content' :
						if($search_keyword) {
							$search_keyword = str_replace(' ','%',$search_keyword);
						}
						$args->s_title = $search_keyword;
						$args->s_content = $search_keyword;
						$use_division = true;
						break;
					// case 'user_id' :
					// 	if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
					// 	$args->s_user_id = $search_keyword;
					// 	$args->sort_index = 'documents.'.$args->sort_index;
					// 	break;
					// case 'user_name' :
					case 'nick_name' :
					case 'email_address' :
					// case 'homepage' :
						if($search_keyword) {
							$search_keyword = str_replace(' ','%',$search_keyword);
						}
						$args->{"s_".$search_target} = $search_keyword;
						break;
					case 'is_notice' :
						if($search_keyword=='N') {
							$args->{"s_".$search_target} = 'N';
						}
						elseif($search_keyword=='Y') {
							$args->{"s_".$search_target} = 'Y';
						}
						else {
							$args->{"s_".$search_target} = '';
						}
						break;
					case 'is_secret' :
						if($search_keyword=='N') {
							$args->statusList = array($this->getConfigStatus('public'));
						}
						elseif($search_keyword=='Y') {
							$args->statusList = array($this->getConfigStatus('secret'));
						}
						elseif($search_keyword=='temp') {
							$args->statusList = array($this->getConfigStatus('temp'));
						}
						break;
					case 'post_authors' :// case 'member_srl' :
					case 'readed_count' :
					case 'voted_count' :
					case 'comment_count' :
					// case 'trackback_count' :
					case 'uploaded_count' :
						$args->{"s_".$search_target} = (int)$search_keyword;
						break;
					case 'post_authors' :// case 'member_srls' :
						$args->{"s_".$search_target} = (int)$search_keyword;

						if($logged_info->member_srl) {
							$srls = explode(',', $search_keyword);
							foreach($srls as $srl) {
								if(abs($srl) != $logged_info->member_srl) {
									break; // foreach
								}
								$args->{"s_".$search_target} = $search_keyword;
								break; // foreach
							}
						}
						break;
					case 'blamed_count' :
						$args->{"s_".$search_target} = (int)$search_keyword * -1;
						break;
					case 'regdate_dt' : // case 'regdate' :
					case 'last_update_dt' :
					case 'ipaddress' :
						$args->{"s_".$search_target} = $search_keyword;
						break;
					case 'comment' :
						$args->s_comment = $search_keyword;
						$query_id = 'post.getPostListWithinComment';
						$use_division = true;
						break;
					case 'tag' :
						$args->s_tags = str_replace(' ','%',$search_keyword);
						$query_id = 'post.getPostListWithinTag';
						break;
					// case 'extra_vars':
					// 	$args->var_value = str_replace(' ', '%', $search_keyword);
					// 	$query_id = 'document.getDocumentListWithinExtraVars';
					// 	break;
					default :
						if(strpos($search_target,'extra_vars')!==false) {
							$args->var_idx = substr($search_target, strlen('extra_vars'));
							$args->var_value = str_replace(' ','%',$search_keyword);
							$args->sort_index = 'documents.'.$args->sort_index;
							$query_id = 'post.getPostListWithExtraVars';
						}
						break;
				}
			}
			unset($logged_info);

			if( $searchOpt->isExtraVars) {
				$query_id = 'post.getPostListExtraSort';
			}
			else {  // basic list
// var_dump('ddd');
				/**
				 * list_order asc sort of division that can be used only when
				 */
				if($args->sort_index != 'list_order' || $args->order_type != 'asc') {
					$use_division = false;
				}

				/**
				 * If it is true, use_division changed to use the document division
				 */
				if($use_division) {
					// Division begins
					$division = (int)Context::get('division');

					// order by list_order and (module_srl===0 or module_srl may count), therefore case table full scan
					// if($args->sort_index == 'list_order' && ($args->exclude_module_srl === '0' || count(explode(',', $args->module_srl)) > 5)) {
					// 	$listSqlID = 'document.getDocumentListUseIndex';
					// 	$divisionSqlID = 'document.getDocumentDivisionUseIndex';
					// }
					// else {
						$listSqlID = 'post.getPostList';
						$divisionSqlID = 'post.getPostDivision';
					// }

					// If you do not value the best division top
					if(!$division) {
						$division_args = new stdClass();
						// $division_args->module_srl = $args->module_srl;
						// $division_args->exclude_module_srl = $args->exclude_module_srl;
						$division_args->list_count = 1;
						$division_args->sort_index = $args->sort_index;
						$division_args->order_type = $args->order_type;
						$division_args->statusList = $args->statusList;

						$output = executeQuery($divisionSqlID, $division_args, array('list_order'));
						if($output->data) {
							$item = array_pop($output->data);
							$division = $item->list_order;
						}
						$division_args = null;
					}

					// The last division
					$last_division = (int)Context::get('last_division');

					// Division after division from the 5000 value of the specified Wanted
					if(!$last_division) {
						$last_division_args = new stdClass();
						// $last_division_args->module_srl = $args->module_srl;
						// $last_division_args->exclude_module_srl = $args->exclude_module_srl;
						$last_division_args->list_count = 1;
						$last_division_args->sort_index = $args->sort_index;
						$last_division_args->order_type = $args->order_type;
						$last_division_args->list_order = $division;
						$last_division_args->page = 5001;

						$output = executeQuery($divisionSqlID, $last_division_args, array('list_order'));
						if($output->data) {
							$item = array_pop($output->data);
							$last_division = $item->list_order;
						}
					}

					// Make sure that after last_division article
					if($last_division) {
						$last_division_args = new stdClass();
						// $last_division_args->module_srl = $args->module_srl;
						// $last_division_args->exclude_module_srl = $args->exclude_module_srl;
						$last_division_args->list_order = $last_division;
						$output = executeQuery('post.getPostDivisionCount', $last_division_args);
						if($output->data->count<1) $last_division = null;
					}

					$args->division = $division;
					$args->last_division = $last_division;
					Context::set('division', $division);
					Context::set('last_division', $last_division);
				}
			}
// var_dump($args->list_order);	
			$o_query_rst = new \stdClass();
			$o_query_rst->s_where = "WHERE `board_id`=".get_the_ID();
			if( isset( $args->statusList ) && is_array($args->statusList) ) {
				// var_dump(implode("', '" , $args->statusList));
				$o_query_rst->s_where .= " AND `status` in ('".implode("', '" , $args->statusList)."')"; // and `list_order` <= 2100000000";
			}

			if( isset($args->sort_index) ) {
				$o_query_rst->s_orderby = " ORDER BY `".$args->sort_index."` ".$args->order_type;
			}
// var_dump($o_query_rst);	
			return $o_query_rst;
		}

		/**
		 * Return status name list
		 * @return array
		 */
		// function getStatusNameList()
		public function get_status_name_list() {
			global $lang;
			if(!isset($lang->status_name_list)) {
				return array_flip($this->get_status_list());
			}
			return $lang->status_name_list;
		}

		/**
		 * Function to retrieve the key values of the extended variable document
		 * $Form_include: writing articles whether to add the necessary extensions of the variable input form
		 * @param int $board_id
		 * @return array
		 */
		// function getExtraKeys($module_srl)
		public function get_user_define_keys($n_board_id) {
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][$n_board_id])) {
				$a_keys = false;
				$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance('object', null, true);
				if($o_cache_handler->isSupport()) {
					// $object_key = 'module_document_extra_keys:' . $n_board_id;
					$object_key = 'module_post_user_define_keys:' . $n_board_id;
					$cache_key = $o_cache_handler->getGroupKey('site_and_module', $object_key);
					$a_keys = $o_cache_handler->get($cache_key);
				}
				// $o_user_define_fields = \X2board\Includes\Classes\UserDefineFields::getInstance($n_board_id); // 호출 효율성 위해서 아래로 이동

				if($a_keys === false) {  // _set_user_define_fields()과 동일한 DB 호출  -> 캐쉬화해야 함
					$s_columns = '`board_id` as `board_id`, `var_idx` as `idx`, `var_name` as `name`, `var_type` as `type`, `var_is_required` as `is_required`, `var_search` as `search`, `var_default` as `default`, `var_desc` as `desc`, `eid` as `eid`  ';
					global $wpdb;
					$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC");

					// correcting index order
					// DB에서 가져온 첫번째 var_idx가 1보다 클 때 idx를 수정함
					// XE는 변수 순서를 바꿀 때마다 ajax 갱신해서 문제가 될 수 있지만
					// WP는 변경된 순서를 일괄 컴파일해서 저장하므로 문제가 될 가능성이 낮음
					/*$isFixed = FALSE;
					if(is_array($a_temp)) {
						$prevIdx = 0;
						foreach($a_temp as $no => $value) {
							// case first
							if($prevIdx == 0 && $value->idx != 1) {
								// $args = new stdClass();
								// $args->module_srl = $n_board_id;
								// $args->var_idx = $value->idx;
								// $args->new_idx = 1;
								// executeQuery('document.updateDocumentExtraKeyIdx', $args);
								// executeQuery('document.updateDocumentExtraVarIdx', $args);
								$prevIdx = 1;
								$isFixed = TRUE;
								continue;
							}

							// case others
							if($prevIdx > 0 && $prevIdx + 1 != $value->idx) {
								// $args = new stdClass();
								// $args->module_srl = $module_srl;
								// $args->var_idx = $value->idx;
								// $args->new_idx = $prevIdx + 1;
								// executeQuery('document.updateDocumentExtraKeyIdx', $args);
								// executeQuery('document.updateDocumentExtraVarIdx', $args);
								$prevIdx += 1;
								$isFixed = TRUE;
								continue;
							}
							$prevIdx = $value->idx;
						}
					}
					if($isFixed) {
						$output = executeQueryArray('document.getDocumentExtraKeys', $obj);
					}*/

					$o_user_define_fields = \X2board\Includes\Classes\UserDefineFields::getInstance(); //$n_board_id);
					$o_user_define_fields->set_user_define_keys_2_display($a_temp);
					$a_keys = $o_user_define_fields->get_user_define_vars();
					unset($o_user_define_fields);
					if(!$a_keys) {
						$a_keys = array();
					}

					if($o_cache_handler->isSupport()) {
						$o_cache_handler->put($cache_key, $a_keys);
					}
				}
				unset($o_cache_handler);
				$G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][$n_board_id] = $a_keys;
			}
			return $G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][$n_board_id];
		}


		





//////////////////////////////
		
		/**
		 * Bringing multiple documents (or paging)
		 * @param array|string $document_srls
		 * @param bool $is_admin
		 * @param bool $load_extra_vars
		 * @param array $columnList
		 * @return array value type is documentItem
		 */
		function getDocuments($document_srls, $is_admin = false, $load_extra_vars=true, $columnList = array())
		{
			if(is_array($document_srls))
			{
				$list_count = count($document_srls);
				$document_srls = implode(',',$document_srls);
			}
			else
			{
				$list_count = 1;
			}
			$args = new stdClass();
			$args->document_srls = $document_srls;
			$args->list_count = $list_count;
			$args->order_type = 'asc';

			$output = executeQuery('document.getDocuments', $args, $columnList);
			$document_list = $output->data;
			if(!$document_list) return;
			if(!is_array($document_list)) $document_list = array($document_list);

			$document_count = count($document_list);
			foreach($document_list as $key => $attribute)
			{
				$document_srl = $attribute->document_srl;
				if(!$document_srl) continue;

				if(!$GLOBALS['XE_DOCUMENT_LIST'][$document_srl])
				{
					$oDocument = null;
					$oDocument = new documentItem();
					$oDocument->setAttribute($attribute, false);
					if($is_admin) $oDocument->setGrant();
					$GLOBALS['XE_DOCUMENT_LIST'][$document_srl] = $oDocument;
				}

				$result[$attribute->document_srl] = $GLOBALS['XE_DOCUMENT_LIST'][$document_srl];
			}

			if($load_extra_vars) {
				$this->_set_to_all_post_extra_vars();
			}

			$output = null;
			if(count($result))
			{
				foreach($result as $document_srl => $val)
				{
					$output[$document_srl] = $GLOBALS['XE_DOCUMENT_LIST'][$document_srl];
				}
			}

			return $output;
		}

		/**
		 * The total number of documents that are bringing
		 * @param int $module_srl
		 * @param object $search_obj
		 * @return int
		 */
		function getDocumentCount($module_srl, $search_obj = NULL)
		{
			if(is_null($search_obj)) $search_obj = new stdClass();
			$search_obj->module_srl = $module_srl;

			$output = executeQuery('document.getDocumentCount', $search_obj);
			// Return total number of
			$total_count = $output->data->count;
			return (int)$total_count;
		}

		/**
		 * the total number of documents that are bringing
		 * @param object $search_obj
		 * @return array
		 */
		function getDocumentCountByGroupStatus($search_obj = NULL)
		{
			$output = executeQuery('document.getDocumentCountByGroupStatus', $search_obj);
			if(!$output->toBool()) return array();

			return $output->data;
		}

		function getDocumentExtraVarsCount($module_srl, $search_obj = NULL)
		{
			// Additional search options
			$args->module_srl = $module_srl;

			$args->category_srl = $search_obj->category_srl;
			$args->var_idx = $search_obj->s_var_idx;
			$args->var_eid = $search_obj->s_var_eid;
			$args->var_value = $search_obj->s_var_value;
			$args->var_lang_code = Context::getLangType();

			$output = executeQuery('document.getDocumentExtraVarsCount', $args);
			// Return total number of
			$total_count = $output->data->count;
			return (int)$total_count;
		}

		/**
		 * Imported post monthly archive status
		 * @param object $obj
		 * @return object
		 */
		function getMonthlyArchivedList($obj)
		{
			if($obj->mid)
			{
				$oModuleModel = getModel('module');
				$obj->module_srl = $oModuleModel->getModuleSrlByMid($obj->mid);
				unset($obj->mid);
			}
			// Module_srl passed the array may be a check whether the array
			$args = new stdClass;
			if(is_array($obj->module_srl)) $args->module_srl = implode(',', $obj->module_srl);
			else $args->module_srl = $obj->module_srl;

			$output = executeQuery('document.getMonthlyArchivedList', $args);
			if(!$output->toBool()||!$output->data) return $output;

			if(!is_array($output->data)) $output->data = array($output->data);

			return $output;
		}

		/**
		 * Bringing a month on the status of the daily posts
		 * @param object $obj
		 * @return object
		 */
		function getDailyArchivedList($obj)
		{
			if($obj->mid)
			{
				$oModuleModel = getModel('module');
				$obj->module_srl = $oModuleModel->getModuleSrlByMid($obj->mid);
				unset($obj->mid);
			}
			// Module_srl passed the array may be a check whether the array
			$args = new stdClass;
			if(is_array($obj->module_srl)) $args->module_srl = implode(',', $obj->module_srl);
			else $args->module_srl = $obj->module_srl;
			$args->regdate_dt = $obj->regdate_dt;

			$output = executeQuery('document.getDailyArchivedList', $args);
			if(!$output->toBool()) return $output;

			if(!is_array($output->data)) $output->data = array($output->data);

			return $output;
		}

		/**
		 * Wanted to set document information
		 * @return object
		 */
		function getDocumentConfig()
		{
			if($this->documentConfig === NULL)
			{
				$oModuleModel = getModel('module');
				$config = $oModuleModel->getModuleConfig('document');

				if (!$config)
				{
					$config = new stdClass();
				}
				$this->documentConfig = $config;
			}
			return $this->documentConfig;
		}

		/**
		 * Common:: Module extensions of variable management
		 * Expansion parameter management module in the document module instance, when using all the modules available
		 * @param int $module_srl
		 * @return string
		 */
		function getExtraVarsHTML($module_srl)
		{
			// Bringing existing extra_keys
			$extra_keys = $this->getExtraKeys($module_srl);
			Context::set('extra_keys', $extra_keys);
			$security = new Security();
			$security->encodeHTML('extra_keys..', 'selected_var_idx');

			// Get information of module_grants
			$oTemplate = &TemplateHandler::getInstance();
			return $oTemplate->compile($this->module_path.'tpl', 'extra_keys');
		}

		/**
		 * Return docuent number by document title
		 * @param int $module_srl
		 * @param string $title
		 * @return int|void
		 */
		function getDocumentSrlByTitle($module_srl, $title)
		{
			if(!$module_srl || !$title) return null;
			$args = new stdClass;
			$args->module_srl = $module_srl;
			$args->title = $title;
			$output = executeQuery('document.getDocumentSrlByTitle', $args);
			if(!$output->data) return null;
			else
			{
				if(is_array($output->data)) return $output->data[0]->document_srl;
				return $output->data->document_srl;
			}
		}

		/**
		 * Return document's history list
		 * @param int $document_srl
		 * @param int $list_count
		 * @param int $page
		 * @return object
		 */
		function getHistories($document_srl, $list_count, $page)
		{
			$args = new stdClass;
			$args->list_count = $list_count;
			$args->page = $page;
			$args->document_srl = $document_srl;
			$output = executeQueryArray('document.getHistories', $args);
			return $output;
		}

		/**
		 * Return document's history
		 * @param int $history_srl
		 * @return object
		 */
		function getHistory($history_srl)
		{
			$args = new stdClass;
			$args->history_srl = $history_srl;
			$output = executeQuery('document.getHistory', $args);
			return $output->data;
		}

		/**
		 * Module_srl value, bringing the list of documents
		 * @param object $obj
		 * @return object
		 */
		function getTrashList($obj)
		{
			// Variable check
			$args = new stdClass;
			$args->category_srl = $obj->category_srl?$obj->category_srl:null;
			$args->sort_index = $obj->sort_index;
			$args->order_type = $obj->order_type?$obj->order_type:'desc';
			$args->page = $obj->page?$obj->page:1;
			$args->list_count = $obj->list_count?$obj->list_count:20;
			$args->page_count = $obj->page_count?$obj->page_count:10;
			// Search options
			$search_target = $obj->search_target;
			$search_keyword = $obj->search_keyword;
			if($search_target && $search_keyword)
			{
				switch($search_target)
				{
					case 'title' :
					case 'content' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->{"s_".$search_target} = $search_keyword;
						$use_division = true;
						break;
					case 'title_content' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->s_title = $search_keyword;
						$args->s_content = $search_keyword;
						break;
					case 'user_id' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->s_user_id = $search_keyword;
						$args->sort_index = 'documents.'.$args->sort_index;
						break;
					case 'user_name' :
					case 'nick_name' :
					case 'email_address' :
					case 'homepage' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->{"s_".$search_target} = $search_keyword;
						break;
					case 'is_notice' :
					case 'is_secret' :
						if($search_keyword=='N') {
							$args->statusList = array($this->getConfigStatus('public'));
						}
						elseif($search_keyword=='Y') {
							$args->statusList = array($this->getConfigStatus('secret'));
						}
						break;
					case 'member_srl' :
					case 'readed_count' :
					case 'voted_count' :
					case 'blamed_count' :
					case 'comment_count' :
					case 'trackback_count' :
					case 'uploaded_count' :
						$args->{"s_".$search_target} = (int)$search_keyword;
						break;
					case 'regdate_dt' :
					case 'last_update_dt' :
					case 'ipaddress' :
					case 'tag' :
						$args->{"s_".$search_target} = $search_keyword;
						break;
				}
			}

			$output = executeQueryArray('document.getTrashList', $args);
			if($output->data)
			{
				foreach($output->data as $key => $attribute)
				{
					$oDocument = null;
					$oDocument = new documentItem();
					$oDocument->setAttribute($attribute, false);
					$attribute = $oDocument;
				}
			}
			return $output;
		}

		/**
		 * vote up, vote down member list in Document View page
		 * @return void|BaseObject
		 */
		function getDocumentVotedMemberList()
		{
			$args = new stdClass;
			$document_srl = Context::get('document_srl');
			if(!$document_srl) return new BaseObject(-1,'msg_invalid_request');

			$point = Context::get('point');
			if($point != -1) $point = 1;

			$oDocumentModel = getModel('document');
			$columnList = array('document_srl', 'module_srl');
			$oDocument = $oDocumentModel->getDocument($document_srl, false, false, $columnList);
			$module_srl = $oDocument->get('module_srl');
			if(!$module_srl) return new BaseObject(-1, 'msg_invalid_request');

			$oModuleModel = getModel('module');
			$document_config = $oModuleModel->getModulePartConfig('document',$module_srl);
			if($point == -1)
			{
				if($document_config->use_vote_down!='S') return new BaseObject(-1, 'msg_invalid_request');
				$args->below_point = 0;
			}
			else
			{
				if($document_config->use_vote_up!='S') return new BaseObject(-1, 'msg_invalid_request');
				$args->more_point = 0;
			}

			$args->document_srl = $document_srl;

			$output = executeQueryArray('document.getVotedMemberList',$args);
			if(!$output->toBool()) return $output;

			$oMemberModel = getModel('member');
			if($output->data)
			{
				foreach($output->data as $k => $d)
				{
					$profile_image = $oMemberModel->getProfileImage($d->member_srl);
					$output->data[$k]->src = $profile_image->src;
				}
			}

			$this->add('voted_member_list',$output->data);
		}

		/**
		 * Get the total number of Document in corresponding with member_srl.
		 * @param int $member_srl
		 * @return int
		 */
		function getDocumentCountByMemberSrl($member_srl)
		{
			$args = new stdClass();
			$args->member_srl = $member_srl;
			$output = executeQuery('document.getDocumentCountByMemberSrl', $args);
			return (int) $output->data->count;
		}

		/**
		 * Get document list of the doc in corresponding woth member_srl.
		 * @param int $member_srl
		 * @param array $columnList
		 * @param int $page
		 * @param bool $is_admin
		 * @param int $count
		 * @return object
		 */
		function getDocumentListByMemberSrl($member_srl, $columnList = array(), $page = 0, $is_admin = FALSE, $count = 0 )
		{
			$args = new stdClass();
			$args->member_srl = $member_srl;
			$args->list_count = $count;
			$output = executeQuery('document.getDocumentListByMemberSrl', $args, $columnList);
			$document_list = $output->data;

			if(!$document_list) return array();
			if(!is_array($document_list)) $document_list = array($document_list);

			return $document_list;
		}

		/**
		 * get to the document extra image path.
		 * @return string
		 */
		// function getDocumentExtraImagePath()
		// {
		// 	$documentConfig = getModel('document')->getDocumentConfig();
		// 	if(Mobile::isFromMobilePhone())
		// 	{
		// 		$iconSkin = $documentConfig->micons;
		// 	}
		// 	else
		// 	{
		// 		$iconSkin = $documentConfig->icons;
		// 	}
		// 	$path = sprintf('%s%s',getUrl(), "modules/document/tpl/icons/$iconSkin/");

		// 	return $path;
		// }

		/**
		 * $this->default_fields 반환한다.
		 * @return array
		 */
		// public function get_default_user_input_fields() {
		// 	return $this->default_fields;
		// }
		
		/**
		 * $this->default_fields 반환한다.
		 * @return array
		 */
		// public function get_extended_user_input_fields() {
		// 	return $this->extends_fields;
		// }


		/**
		 * Bringing the Categories list the specific module
		 * Speed and variety of categories, considering the situation created by the php script to include a list of the must, in principle, to use
		 * @param int $module_srl
		 * @param array $columnList
		 * @return array
		 */
		// function getCategoryList()
		// function get_category_list($columnList = array()) {  // $module_srl, 
			// $module_srl = (int)$module_srl;
			// $n_board_id = \X2board\Includes\Classes\Context::get('board_id');

			// Category of the target module file swollen
			// $filename = sprintf("%sfiles/cache/document_category/%s.php", _XE_PATH_, $module_srl);
			// If the target file to the cache file regeneration category
			// if(!file_exists($filename))	{
			// 	$oDocumentController = getController('document');
			// 	if(!$oDocumentController->makeCategoryFile($module_srl)) return array();
			// }
			// include($filename);

			// Cleanup of category
			// $post_category = array();
			// $this->_arrangeCategory($post_category, $menu->list, 0);
		// 	return $post_category;
		// }

		/**
		 * Category within a primary method to change the array type
		 * @param array $document_category
		 * @param array $list
		 * @param int $depth
		 * @return void
		 */
		// private function _arrangeCategory(&$document_category, $list, $depth)
		// {
		// 	if(!count((array)$list)) return;
		// 	$idx = 0;
		// 	$list_order = array();
		// 	foreach($list as $key => $val)
		// 	{
		// 		$obj = new stdClass;
		// 		$obj->mid = $val['mid'];
		// 		$obj->module_srl = $val['module_srl'];
		// 		$obj->category_srl = $val['category_srl'];
		// 		$obj->parent_srl = $val['parent_srl'];
		// 		$obj->title = $obj->text = $val['text'];
		// 		$obj->description = $val['description'];
		// 		$obj->expand = $val['expand']=='Y'?true:false;
		// 		$obj->color = $val['color'];
		// 		$obj->document_count = $val['document_count'];
		// 		$obj->depth = $depth;
		// 		$obj->child_count = 0;
		// 		$obj->childs = array();
		// 		$obj->grant = $val['grant'];

		// 		if(Context::get('mid') == $obj->mid && Context::get('category') == $obj->category_srl) $selected = true;
		// 		else $selected = false;

		// 		$obj->selected = $selected;

		// 		$list_order[$idx++] = $obj->category_srl;
		// 		// If you have a parent category of child nodes apply data
		// 		if($obj->parent_srl)
		// 		{
		// 			$parent_srl = $obj->parent_srl;
		// 			$document_count = $obj->document_count;
		// 			$expand = $obj->expand;
		// 			if($selected) $expand = true;

		// 			while($parent_srl)
		// 			{
		// 				$document_category[$parent_srl]->document_count += $document_count;
		// 				$document_category[$parent_srl]->childs[] = $obj->category_srl;
		// 				$document_category[$parent_srl]->child_count = count($document_category[$parent_srl]->childs);
		// 				if($expand) $document_category[$parent_srl]->expand = $expand;

		// 				$parent_srl = $document_category[$parent_srl]->parent_srl;
		// 			}
		// 		}

		// 		$document_category[$key] = $obj;

		// 		if(count($val['list'])) $this->_arrangeCategory($document_category, $val['list'], $depth+1);
		// 	}
		// 	$document_category[$list_order[0]]->first = true;
		// 	$document_category[$list_order[count($list_order)-1]]->last = true;
		// }

		/**
		 * Imported Category of information
		 * @param int $category_srl
		 * @param array $columnList
		 * @return object
		 */
		// function getCategory($category_srl, $columnList = array())
		// {
		// 	$args =new stdClass();
		// 	$args->category_srl = $category_srl;
		// 	$output = executeQuery('document.getCategory', $args, $columnList);

		// 	$node = $output->data;
		// 	if(!$node) return;

		// 	if($node->group_srls)
		// 	{
		// 		$group_srls = explode(',',$node->group_srls);
		// 		unset($node->group_srls);
		// 		$node->group_srls = $group_srls;
		// 	}
		// 	else
		// 	{
		// 		unset($node->group_srls);
		// 		$node->group_srls = array();
		// 	}
		// 	return $node;
		// }

		/**
		 * Check whether the child has a specific category
		 * @param int $category_srl
		 * @return bool
		 */
		// function getCategoryChlidCount($category_srl)
		// {
		// 	$args = new stdClass();
		// 	$args->category_srl = $category_srl;
		// 	$output = executeQuery('document.getChildCategoryCount',$args);
		// 	if($output->data->count > 0) return true;
		// 	return false;
		// }

		/**
		 * Wanted number of documents belonging to category
		 * @param int $module_srl
		 * @param int $category_srl
		 * @return int
		 */
		// function getCategoryDocumentCount($module_srl, $category_srl)
		// {
		// 	$args = new stdClass;
		// 	$args->module_srl = $module_srl;
		// 	$args->category_srl = $category_srl;
		// 	$output = executeQuery('document.getCategoryDocumentCount', $args);
		// 	return (int)$output->data->count;
		// }

		/**
		 * Php cache files in the document category return information
		 * @param int $module_srl
		 * @return string
		 */
		// function getCategoryPhpFile($module_srl)
		// {
		// 	$php_file = sprintf('files/cache/document_category/%s.php',$module_srl);
		// 	if(!file_exists($php_file))
		// 	{
		// 		$oDocumentController = getController('document');
		// 		$oDocumentController->makeCategoryFile($module_srl);
		// 	}
		// 	return $php_file;
		// }

		/**
		 * Get a list for a particular module
		 * @return void|BaseObject
		 */
		// function getDocumentCategories()
		// {
		// 	if(!Context::get('is_logged')) return new BaseObject(-1,'msg_not_permitted');
		// 	$module_srl = Context::get('module_srl');
		// 	$categories= $this->getCategoryList($module_srl);
		// 	$lang = Context::get('lang');
		// 	// No additional category
		// 	$output = "0,0,{$lang->none_category}\n";
		// 	if($categories)
		// 	{
		// 		foreach($categories as $category_srl => $category)
		// 		{
		// 			$output .= sprintf("%d,%d,%s\n",$category_srl, $category->depth,$category->title);
		// 		}
		// 	}
		// 	$this->add('categories', $output);
		// }

		
		/**
		 * Common:: Category parameter management module
		 * @param int $module_srl
		 * @return string
		 */
		/*function getCategoryHTML($module_srl)
		{
			$category_xml_file = $this->getCategoryXmlFile($module_srl);

			Context::set('category_xml_file', $category_xml_file);

			Context::loadJavascriptPlugin('ui.tree');

			// Get a list of member groups
			$oMemberModel = getModel('member');
			$group_list = $oMemberModel->getGroups($module_info->site_srl);
			Context::set('group_list', $group_list);

			$security = new Security();
			$security->encodeHTML('group_list..title');

			// Get information of module_grants
			$oTemplate = &TemplateHandler::getInstance();
			return $oTemplate->compile($this->module_path.'tpl', 'category_list');
		}*/

		/**
		 * skin/../list.php에서 카테고리 표시 판단
		 * @return string
		 */
		// public function get_category_header_type() {
		// 	if( $this->_is_category_active() )	{
		// 		return apply_filters('kboard_category_type', $this->meta->tree_category_header_type, $this);
		// 	}
		// 	return '';
		// }

		/**
		 * skin/default/list.php에서 카테고리 입력란 표시 판단
		 * @return string
		 */
		// private function _is_category_active(){
		// 	return false;
		// 	return isset($this->fields->getSkinFields()['category']) ? true : false;
		// }

		/**
		 * document checked the permissions on the session values
		 * @param int $document_srl
		 * @return void
		 */
		// function isGranted($n_post_id) {
		// 	return $_SESSION['own_post'][$n_post_id];
		// }

		/**
		 * Show pop-up menu of the selected posts
		 * Printing, scrap, recommendations and negative, reported the Add Features
		 * @return void
		 */
		// function getDocumentMenu()
		// {
		// 	// Post number and the current login information requested Wanted
		// 	$document_srl = Context::get('target_srl');
		// 	$mid = Context::get('cur_mid');
		// 	$logged_info = Context::get('logged_info');
		// 	$act = Context::get('cur_act');
		// 	// to menu_list "pyosihalgeul, target, url" put into an array
		// 	$menu_list = array();
		// 	// call trigger
		// 	ModuleHandler::triggerCall('document.getDocumentMenu', 'before', $menu_list);

		// 	$oDocumentController = getController('document');
		// 	// Members must be a possible feature
		// 	if($logged_info->member_srl)
		// 	{
		// 		$oDocumentModel = getModel('document');
		// 		$columnList = array('document_srl', 'module_srl', 'member_srl', 'ipaddress');
		// 		$oDocument = $oDocumentModel->getDocument($document_srl, false, false, $columnList);
		// 		$module_srl = $oDocument->get('module_srl');
		// 		$member_srl = $oDocument->get('member_srl');
		// 		if(!$module_srl) return new BaseObject(-1, 'msg_invalid_request');

		// 		$oModuleModel = getModel('module');
		// 		$document_config = $oModuleModel->getModulePartConfig('document',$module_srl);
		// 		if($document_config->use_vote_up!='N' && $member_srl!=$logged_info->member_srl)
		// 		{
		// 			// Add a Referral Button
		// 			$url = sprintf("doCallModuleAction('document','procDocumentVoteUp','%s')", $document_srl);
		// 			$oDocumentController->addDocumentPopupMenu($url,'cmd_vote','','javascript');
		// 		}

		// 		if($document_config->use_vote_down!='N' && $member_srl!=$logged_info->member_srl)
		// 		{
		// 			// Add button to negative
		// 			$url= sprintf("doCallModuleAction('document','procDocumentVoteDown','%s')", $document_srl);
		// 			$oDocumentController->addDocumentPopupMenu($url,'cmd_vote_down','','javascript');
		// 		}

		// 		// Adding Report
		// 		$url = sprintf("doCallModuleAction('document','procDocumentDeclare','%s')", $document_srl);
		// 		$oDocumentController->addDocumentPopupMenu($url,'cmd_declare','','javascript');

		// 		// Add Bookmark button
		// 		$url = sprintf("doCallModuleAction('member','procMemberScrapDocument','%s')", $document_srl);
		// 		$oDocumentController->addDocumentPopupMenu($url,'cmd_scrap','','javascript');
		// 	}
		// 	// Add print button
		// 	$url = getUrl('','module','document','act','dispDocumentPrint','document_srl',$document_srl);
		// 	$oDocumentController->addDocumentPopupMenu($url,'cmd_print','','printDocument');
		// 	// Call a trigger (after)
		// 	ModuleHandler::triggerCall('document.getDocumentMenu', 'after', $menu_list);
		// 	if($this->grant->manager)
		// 	{
		// 		$str_confirm = Context::getLang('confirm_move');
		// 		$url = sprintf("if(!confirm('%s')) return; var params = new Array(); params['document_srl']='%s'; params['mid']=current_mid;params['cur_url']=current_url; exec_xml('document', 'procDocumentAdminMoveToTrash', params)", $str_confirm, $document_srl);
		// 		$oDocumentController->addDocumentPopupMenu($url,'cmd_trash','','javascript');
		// 	}

		// 	// If you are managing to find posts by ip
		// 	if($logged_info->is_admin == 'Y')
		// 	{
		// 		$oDocumentModel = getModel('document');
		// 		$oDocument = $oDocumentModel->getDocument($document_srl);	//before setting document recycle

		// 		if($oDocument->isExists())
		// 		{
		// 			// Find a post equivalent to ip address
		// 			$url = getUrl('','module','admin','act','dispDocumentAdminList','search_target','ipaddress','search_keyword',$oDocument->getIpAddress());
		// 			$oDocumentController->addDocumentPopupMenu($url,'cmd_search_by_ipaddress',$icon_path,'TraceByIpaddress');

		// 			$url = sprintf("var params = new Array(); params['ipaddress_list']='%s'; exec_xml('spamfilter', 'procSpamfilterAdminInsertDeniedIP', params, completeCallModuleAction)", $oDocument->getIpAddress());
		// 			$oDocumentController->addDocumentPopupMenu($url,'cmd_add_ip_to_spamfilter','','javascript');
		// 		}
		// 	}
		// 	// Changing the language of pop-up menu
		// 	$menus = Context::get('document_popup_menu_list');
		// 	$menus_count = count($menus);
		// 	for($i=0;$i<$menus_count;$i++)
		// 	{
		// 		$menus[$i]->str = Context::getLang($menus[$i]->str);
		// 	}
		// 	// Wanted to finally clean pop-up menu list
		// 	$this->add('menus', $menus);
		// }

		/**
		 * Xml cache file of the document category return information
		 * @param int $module_srl
		 * @return string
		 */
		// function getCategoryXmlFile($module_srl)
		// {
		// 	$xml_file = sprintf('files/cache/document_category/%s.xml.php',$module_srl);
		// 	if(!file_exists($xml_file))
		// 	{
		// 		$oDocumentController = getController('document');
		// 		$oDocumentController->makeCategoryFile($module_srl);
		// 	}
		// 	return $xml_file;
		// }

		/**
		 * Certain categories of information, return the template guhanhu
		 * Manager on the page to add information about a particular menu from the server after compiling tpl compiled a direct return html
		 * @return void|BaseObject
		 */
		// function getDocumentCategoryTplInfo()
		// {
		// 	$oModuleModel = getModel('module');
		// 	$oMemberModel = getModel('member');
		// 	// Get information on the menu for the parameter settings
		// 	$module_srl = Context::get('module_srl');
		// 	$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
		// 	// Check permissions
		// 	$grant = $oModuleModel->getGrant($module_info, Context::get('logged_info'));
		// 	if(!$grant->manager) return new BaseObject(-1,'msg_not_permitted');

		// 	$category_srl = Context::get('category_srl');
		// 	$category_info = $this->getCategory($category_srl);
		// 	if(!$category_info)
		// 	{
		// 		return new BaseObject(-1, 'msg_invalid_request');
		// 	}

		// 	$this->add('category_info', $category_info);
		// }

		/**
		 * Return docuent data by alias
		 * @param string $mid
		 * @param string $alias
		 * @return int|void
		 */
		// function getDocumentSrlByAlias($mid, $alias)
		// {
		// 	if(!$mid || !$alias) return null;
		// 	$site_module_info = Context::get('site_module_info');
		// 	$args = new stdClass;
		// 	$args->mid = $mid;
		// 	$args->alias_title = $alias;
		// 	$args->site_srl = $site_module_info->site_srl;
		// 	$output = executeQuery('document.getDocumentSrlByAlias', $args);
		// 	if(!$output->data) return null;
		// 	else return $output->data->document_srl;
		// }

		/**
		 * Return docuent's alias
		 * @param int $document_srl
		 * @return string|void
		 */
		// function getAlias($document_srl)
		// {
		// 	if(!$document_srl) return null;
		// 	$args = new stdClass;
		// 	$args->document_srl = $document_srl;
		// 	$output = executeQueryArray('document.getAliases', $args);

		// 	if(!$output->data) return null;
		// 	else return $output->data[0]->alias_title;
		// }
	}
}