<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * commentController class
 * controller class of the comment module
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/comment
 */
namespace X2board\Includes\Modules\Comment;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Comment\\commentController')) {

	class commentController extends comment {

		/**
		 * Initialization
		 * @return void
		 */
		// function init()	{ }

		/**
		 * Enter comments
		 * insertComment($obj, $manual_inserted = FALSE)
		 * @param object $obj
		 * @param bool $manual_inserted
		 * @return object
		 */
		public function insert_comment($obj, $manual_inserted = FALSE) {
			if(!$manual_inserted) {  // check WP nonce if a guest inserts a new post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get('x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
				if( is_null( $wp_verify_nonce ) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN).'1' );
				}
				if( !wp_verify_nonce($wp_verify_nonce, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN).'2' );
				}
			}

			if(!is_object($obj)) {
				$obj = new \stdClass();
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if(!$manual_inserted) {
				if( \X2board\Includes\Classes\Context::get('is_logged') ) {
					if($o_logged_info->is_admin == 'Y') {
						$is_admin = TRUE;
					}
					else {
						$is_admin = FALSE;
					}
				}
			}
			else {
				$is_admin = FALSE;
			}

			// check if comment's module is using comment validation and set the publish status to 0 (false)
			// for inserting query, otherwise default is 1 (true - means comment is published)
			$using_validation = $this->is_using_comment_validation();
			if(!$using_validation) {
				$obj->status = 1;
			}
			else {
				if($is_admin) {
					$obj->status = 1; // tag as a validated comment
				}
				else {
					$obj->status = 0;  // tag as a pending comment
				}
			}

			// check if a posting of the corresponding post_id exists
			$parent_post_id = $obj->parent_post_id;
			if(!$parent_post_id) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', X2B_DOMAIN) );
			}

			// if password exists, hash it.
			if(!$manual_inserted && $obj->password) {
				$obj->password = \X2board\Includes\getModel('member')->hash_password($obj->password);
			}

			// get the original posting
			if(!$manual_inserted) {
				// get a object of post model
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($parent_post_id);
				unset($o_post_model);

				if($parent_post_id != $o_post->post_id) {
					return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_post', X2B_DOMAIN) );
				}
				if(!$o_post->allow_comment()) {
					return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', X2B_DOMAIN) );
				}
				unset($o_post);

				// input the member's information if logged-in
				if(\X2board\Includes\Classes\Context::get('is_logged')) {
					$obj->comment_author = $o_logged_info->ID;
					$obj->nick_name = htmlspecialchars_decode($o_logged_info->display_name);
					$obj->email_address = $o_logged_info->user_email;
				}
			}

			// error display if neither of log-in info and user name exist.
			if(!$o_logged_info->ID && !$obj->nick_name) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', X2B_DOMAIN) );
			}

			if(!$obj->comment_id) {
				$obj->comment_id = \X2board\Includes\getNextSequence();
			}
			elseif(!$is_admin && !$manual_inserted && !\X2board\Includes\checkUserSequence($obj->comment_id)) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_not_permitted', X2B_DOMAIN) );
			}

			// determine the order
			$obj->list_order = \X2board\Includes\getNextSequence() * -1;

			// remove XE's own tags from the contents
			// $obj->comment_content = preg_replace('!<\!--(Before|After)(Document|Comment)\(([0-9]+),([0-9]+)\)-->!is', '', $obj->comment_content);

			if(wp_is_mobile() && $obj->use_editor != 'Y') {
				if($obj->use_html != 'Y') {
					$obj->content = htmlspecialchars($obj->content, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
				}
				$obj->content = nl2br($obj->content);
			}

			if(!isset($obj->regdate_dt)) {
				$obj->regdate_dt = date('Y-m-d H:i:s', current_time('timestamp')); //date("YmdHis");
			}

			// remove iframe and script if not a top administrator on the session.
			if($o_logged_info->is_admin != 'Y') {
				$obj->content = \X2board\Includes\removeHackTag($obj->content);
			}
			unset($o_logged_info);

			if(!isset($obj->is_secret)) {
				$obj->is_secret = 'N';
			}

			// Enter a list of comments first
			$list_args = new \stdClass();
			$list_args->comment_id = $obj->comment_id;
			$list_args->parent_post_id = $obj->parent_post_id;
			$list_args->board_id = $obj->board_id;
			$list_args->regdate_dt = $obj->regdate_dt;

			global $wpdb;
			// If parent comment doesn't exist, set data directly
			if(!$obj->parent_comment_id) {  // parent comment
				$list_args->head = $list_args->arrange = $obj->comment_id;
				$list_args->depth = 0;
				// If parent comment exists, get information of the parent comment
			}
			else {  // child comment
				// get information of the parent comment posting
				$s_columns = "`comments`.`parent_post_id`, `comments_list`.*";
				$s_from = "`{$wpdb->prefix}x2b_comments` as `comments` , `{$wpdb->prefix}x2b_comments_list` as `comments_list`";
				$s_where = "`comments`.`comment_id` = {$obj->parent_comment_id} and `comments`.`comment_id` = `comments_list`.`comment_id`";
				$s_query = "SELECT {$s_columns} FROM {$s_from} WHERE {$s_where}";
				if ($wpdb->query($s_query) === FALSE) {  // return if no parent comment exists
					return;
				} 
				else {
					$a_result = $wpdb->get_results($s_query);
					$wpdb->flush();
				}
							
				$parent = $a_result[0];

				$list_args->head = $parent->head;
				$list_args->depth = $parent->depth + 1;

				// if the depth of comments is less than 2, execute insert.
				if($list_args->depth < 2) {  // if the depth of comments is greater than 2, execute update.
					$list_args->arrange = $obj->comment_id;
				}
				else {  // get the top listed comment among those in lower depth and same head with parent's.
					// SELECT min(`comments_list`.`arrange`) as `arrange`  FROM `xe_comments_list` as `comments_list`   WHERE `comments_list`.`head` = ? and `comments_list`.`arrange` > ? and `comments_list`.`depth`
					$s_columns = "min(`comments_list`.`arrange`) as `arrange`";
					$s_from = "`{$wpdb->prefix}x2b_comments_list` as `comments_list`";
					$s_where = "`comments_list`.`head` = {$parent->head} and `comments_list`.`arrange` > {$parent->arrange} and `comments_list`.`depth`";
					$s_query = "SELECT {$s_columns} FROM {$s_from} WHERE {$s_where}";
					if ($wpdb->query($s_query) === FALSE) {  // return if no parent comment exists
						return;
					} 
					else {
						$a_rst = $wpdb->get_results($s_query);
						$wpdb->flush();
					}

					if($a_rst[0]->arrange) {
						$list_args->arrange = $a_rst[0]->arrange;
						// "UPDATE  `xe_comments_list` as `comments_list`  SET `arrange` = `arrange` + ?  WHERE `parent_post_id` = ? and `head` = ? and `arrange` >= ?"
						$result = $wpdb->update ( "{$wpdb->prefix}x2b_comments_list", 
												  array( 'arrange' => 'arrange + 1' ),
												  array( 'parent_post_id' => esc_sql(intval($list_args->parent_post_id)),
												  		 'head' => esc_sql(intval($list_args->head)),
														 'arrange' => esc_sql(intval($list_args->arrange)) ) 
												);
						if( $result < 0 || $result === false ){
							return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
						}
					}
					else {
						$list_args->arrange = $obj->comment_id;
					}
				}
			}

			$this->insert_comment_list($list_args);

			// sanitize
			$a_new_comment = array();
			$a_new_comment['board_id'] = $obj->board_id;
			$a_new_comment['parent_post_id'] = intval($obj->parent_post_id);
			$a_new_comment['content'] = $obj->content;  // sanitize_text_field eliminates all HTML tag
			$a_new_comment['parent_comment_id'] = intval($obj->parent_comment_id);
			$a_new_comment['comment_id'] = intval($obj->comment_id);
			$a_new_comment['password'] = $obj->password;
			$a_new_comment['comment_author'] = intval($obj->comment_author);
			$a_new_comment['email_address'] = sanitize_text_field($obj->email_address);
			$a_new_comment['nick_name'] = sanitize_text_field($obj->nick_name);
			$a_new_comment['status'] = intval($obj->status);
			$a_new_comment['list_order'] = intval($obj->list_order);
			$a_new_comment['regdate_dt'] = $obj->regdate_dt;
			$a_new_comment['last_update_dt'] = $a_new_comment['regdate_dt'];
			$a_new_comment['is_secret'] = sanitize_text_field($obj->is_secret);
			$a_new_comment['ua'] = wp_is_mobile() ? 'M' : 'P';  // add user agent
			$a_new_comment['ipaddress'] = \X2board\Includes\get_remote_ip();

			$a_insert_key = array();
			$a_insert_val = array();
			foreach($a_new_comment as $key=>$value){
				$value = esc_sql($value);
				$a_insert_key[] = "`$key`";
				$a_insert_val[] = "'$value'";
			}
			// insert comment
			$query = "INSERT INTO `{$wpdb->prefix}x2b_comments` (".implode(',', $a_insert_key).") VALUES (".implode(',', $a_insert_val).")";
			if ($wpdb->query($query) === FALSE) {
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
			} 
			unset($a_insert_key);
			unset($a_insert_data);

			// creat the comment model object
			$o_comment_model = \X2board\Includes\getModel('comment');
			// get the number of all comments in the posting
			$n_comment_count = $o_comment_model->get_comment_count($parent_post_id);
			unset($o_comment_model);
			
			// handle appended files
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->set_files_valid($obj->comment_id);
			unset($o_file_controller);
			$this->update_uploaded_count($obj->comment_id);

			// create the controller object of the document
			$o_post_controller = \X2board\Includes\getController('post');

			// Update the number of comments in the post
			if(!$using_validation) {
				$output = $o_post_controller->update_comment_count($parent_post_id, $n_comment_count, $obj->nick_name, TRUE);
			}
			else {
				if($is_admin) {
					$output = $o_post_controller->update_comment_count($parent_post_id, $n_comment_count, $obj->nick_name, TRUE);
				}
			}
			unset($o_post_controller);

			// grant autority of the comment
			if(!$manual_inserted) {
				$this->_add_grant($obj->comment_id);
			}

			// add x2b comment to wp comment
			$n_wp_comment_id = $this->_insert_wp_comment($a_new_comment);

			// register wp comment id into x2b comment
			$result = $wpdb->update ( "{$wpdb->prefix}x2b_comments",
									  array( 'wp_comment_id' => $n_wp_comment_id), 
									  array( 'comment_id' => esc_sql(intval($a_new_comment['comment_id'] )) ) );
			if( $result < 0 || $result === false ){
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
			unset($a_new_comment);

			// $this->sendEmailToAdminAfterInsertComment($obj);
			if( !isset($output)){
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			$output->add('comment_id', $obj->comment_id);
			unset($obj);
			return $output;
		}

		/**
		 * insert comment list to display hierarchy
		 * @param object $list_args
		 * @return object
		 */
		public function insert_comment_list($o_list_args) {
			global $wpdb;
			$a_new_comment_list = array();
			$a_new_comment_list['comment_id'] = $o_list_args->comment_id;
			$a_new_comment_list['parent_post_id'] = $o_list_args->parent_post_id;
			$a_new_comment_list['board_id'] = $o_list_args->board_id;
			$a_new_comment_list['regdate_dt'] = $o_list_args->regdate_dt;
			$a_new_comment_list['arrange'] = $o_list_args->arrange;
			$a_new_comment_list['head'] = $o_list_args->head;
			$a_new_comment_list['depth'] = $o_list_args->depth;

			$a_insert_key = array();
			$a_insert_val = array();
			foreach($a_new_comment_list as $key=>$value){
				$value = esc_sql($value);
				$a_insert_key[] = "`$key`";
				$a_insert_val[] = "'$value'";
			}
			unset($a_new_comment);

			$query = "INSERT INTO `{$wpdb->prefix}x2b_comments_list` (".implode(',', $a_insert_key).") VALUES (".implode(',', $a_insert_val).")";
			if ($wpdb->query($query) === FALSE) {
				wp_die($wpdb->last_error );
			} 
			unset($a_insert_key);
			unset($a_insert_data);
		}

		/**
		 * update the comment
		 * updateComment($obj, $is_admin = FALSE, $manual_updated = FALSE)
		 * @param object $obj
		 * @param bool $is_admin
		 * @param bool $manual_updated
		 * @return object
		 */
		public function update_comment($obj, $is_admin = FALSE, $manual_updated = FALSE) {
			if(!$manual_updated) {  // check WP nonce if a guest inserts a new post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get('x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
				if( is_null( $wp_verify_nonce ) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN).'1' );
				}
				if( !wp_verify_nonce($wp_verify_nonce, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN).'2' );
				}
			}

			if(!is_object($obj)) {
				$obj = new \stdClass();
			}

			// create a comment model object
			$o_comment_model = \X2board\Includes\getModel('comment');
			// get the original data
			$o_source_comment = $o_comment_model->get_comment($obj->comment_id);
			unset($o_comment_model);
			if(!$o_source_comment->comment_author) {
				$obj->comment_author = $o_source_comment->get('comment_author');
				$obj->user_name = $o_source_comment->get('user_name');
				$obj->nick_name = $o_source_comment->get('nick_name');
				$obj->email_address = $o_source_comment->get('email_address');
			}

			// check if permission is granted
			if(!$is_admin && !$o_source_comment->is_granted()) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', X2B_DOMAIN) );
			}

			if($obj->password) {
				$obj->password = \X2board\Includes\getModel('member')->hashPassword($obj->password);
			}

			$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			// set modifier's information if logged-in and posting author and modifier are matched.
			if(\X2board\Includes\Classes\Context::get('is_logged')) {
				if($o_source_comment->comment_author == $logged_info->ID) {
					$obj->comment_author = $logged_info->ID;
					$obj->nick_name = $logged_info->nick_name;
					$obj->email_address = $logged_info->email_address;
				}
			}

			// if nick_name of the logged-in author doesn't exist
			if($o_source_comment->get('comment_author') && !$obj->nick_name) {
				$obj->comment_author = $o_source_comment->get('comment_author');
				$obj->nick_name = $o_source_comment->get('nick_name');
				$obj->email_address = $o_source_comment->get('email_address');
			}

			if(!$obj->content) {
				$obj->content = $o_source_comment->get('content');
			}

			// remove XE's wn tags from contents
			// $obj->content = preg_replace('!<\!--(Before|After)(Document|Comment)\(([0-9]+),([0-9]+)\)-->!is', '', $obj->content);

			if(wp_is_mobile() && $obj->use_editor != 'Y') {
				if($obj->use_html != 'Y') {
					$obj->content = htmlspecialchars($obj->content, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
				}
				$obj->content = nl2br($obj->content);
			}

			// remove iframe and script if not a top administrator on the session
			if($logged_info->is_admin != 'Y') {
				$obj->content = \X2board\Includes\removeHackTag($obj->content);
			}
			unset($logged_info);

			// Update
			if(!isset($obj->last_update_dt)) {
				$obj->last_update_dt = date('Y-m-d H:i:s', current_time('timestamp')); //date("YmdHis");
			}		
			
			// sanitize other user input fields, $obj->content has been sanitized enough
			$a_new_comment = array();
			$a_ignore_key = array('board_id', 'content', 'use_html', 'use_editor', 'parent_post_id');
			foreach($obj as $s_key => $s_val ) {
				if( !in_array($s_key, $a_ignore_key) && isset($s_val) ) {
					$a_new_comment[$s_key] = esc_sql($s_val);
				}
			}
			$a_new_comment['content'] = $obj->content;  // esc_sql() converts new line to \r\n again and again

			global $wpdb;
			$result = $wpdb->update ( "{$wpdb->prefix}x2b_comments", $a_new_comment, array ( 'comment_id' => esc_sql(intval($a_new_comment['comment_id'] )) ) );
			if( $result < 0 || $result === false ){
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}

			// add wp_comment_id to update wp comment
			$a_new_comment['wp_comment_id'] = $o_source_comment->wp_comment_id;
			$this->_update_wp_comment($a_new_comment);
			unset($o_source_comment);
			unset($a_new_comment);
			unset($a_ignore_key);

			// handle appended files
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->set_files_valid($obj->comment_id);
			unset($o_file_controller);
			$this->update_uploaded_count($obj->comment_id);

			if( !isset($output)) {
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			$output->add('comment_id', $obj->comment_id);
			unset($obj);
			return $output;
		}
		
		/**
		 * Check if the board activates validating comment policy
		 * isModuleUsingPublishValidation($module_srl = NULL)
		 * @param int $module_srl
		 * @return bool
		 */
		public function is_using_comment_validation() { // $module_srl = NULL) {
			$o_current_module_config = \X2board\Includes\Classes\Context::get('current_module_info');
			$b_use_validation = FALSE;
			if(isset($o_current_module_config->comment_use_validation) && $o_current_module_config->comment_use_validation == "Y") {
				$b_use_validation = TRUE;
			}
			unset($o_current_module_config);
			return $b_use_validation;
		}

		public function update_uploaded_count($n_comment_id) {
			global $wpdb;
			$o_file_model = \X2board\Includes\getModel('file');
			$n_file_count = $o_file_model->get_files_count($n_comment_id);
			unset($o_file_model);
			$result = $wpdb->update( "{$wpdb->prefix}x2b_comments", 
										array( 'uploaded_count' => $n_file_count), 
										array ( 'comment_id' => esc_sql(intval($n_comment_id )) ) );
			if( $result < 0 || $result === false ){
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
		}

		/**
		 * Authorization of the comments
		 * available only in the current connection of the session value
		 * addGrant($comment_srl)
		 * @return void
		 */
		private function _add_grant($n_comment_id) {
			$_SESSION['x2b_own_comment'][$n_comment_id] = TRUE;
		}

		/**
		 * Trigger to delete its comments together with post deleted
		 * triggerDeleteDocumentComments(&$obj)
		 * @return BaseObject
		 */
		public function trigger_after_delete_post_comments($n_post_id) {
			if(!$n_post_id) {
				return new \X2board\Includes\Classes\BaseObject();
			}
			return $this->_delete_comments($n_post_id);
		}

		/**
		 * Remove all comments of the post
		 * @param int $document_srl
		 * do not execute $this->_delete_wp_comment() as wp_delete_post() deletes all belonged wp comments automatically
		 * deleteComments($document_srl, $obj = NULL)
		 * @return object
		 */
		private function _delete_comments($n_post_id, $obj = NULL) {
			if(is_object($obj)) {
				$o_post = new \X2board\Includes\Modules\Post\postItem();
				$o_post->set_attr($obj);
			}
			else {
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($n_post_id);
				unset($o_post_model);
			}
						
			if(!$o_post->is_exists() || !$o_post->is_granted()) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', X2B_DOMAIN) );
			}

			// get a list of comments and then execute a trigger(way to reduce the processing cost for delete all)
			// SELECT `comment_id`, `board_id`, `comment_author`, `parent_post_id`  FROM `xe_comments` as `comments`   WHERE `document_srl` in (?)    
			global $wpdb;
			$s_columns = "`comment_id`";
			$s_from = "`{$wpdb->prefix}x2b_comments`";
			$s_where = "`parent_post_id` = {$n_post_id}";
			$s_query = "SELECT {$s_columns} FROM {$s_from} WHERE {$s_where}";
			if ($wpdb->query($s_query) === FALSE) {  // return if no parent comment exists
				wp_die('weird error occured in \includes\modules\comment\comment.controller.php::_delete_comments()');
			} 
			else {
				$a_result = $wpdb->get_results($s_query);
				$wpdb->flush();
			}

			$commentSrlList = array();
			if(count((array)$a_result)) {
				foreach($a_result as $comment) {
					$commentSrlList[] = $comment->comment_id;
				}
			}

			// delete the comment
			// DELETE `comments` FROM `xe_comments` as `comments`  WHERE `document_srl` = ?
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments',
				array('parent_post_id'  => $n_post_id ),
				array('%d'), // make sure the id format
			);
			if( $result < 0 || $result === false ){
				wp_die($wpdb->last_error );
			}

			// Delete a list of comments
			// DELETE `comments_list` FROM `xe_comments_list` as `comments_list`  WHERE `document_srl` = ?
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments_list',
				array('parent_post_id'  => $n_post_id ),
				array('%d'),
			);
			if( $result < 0 || $result === false ){
				wp_die($wpdb->last_error );
			}

			//delete declared, declared_log, voted_log
			if(is_array($commentSrlList) && count($commentSrlList) > 0) {
				$args = join(',', $commentSrlList);
				$this->_delete_declared_comments($args);
				$this->_delete_voted_comments($args);
			}
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Delete comment
		 * deleteComment($comment_srl, $is_admin = FALSE, $isMoveToTrash = FALSE)
		 * @param int $comment_srl
		 * @param bool $is_admin
		 * @param bool $isMoveToTrash
		 * @return object
		 */
		public function delete_comment($n_comment_id, $is_admin = FALSE, $isMoveToTrash = FALSE) {
			// create the comment model object
			$o_comment_model = \X2board\Includes\getModel('comment');

			// check if comment already exists
			$o_comment = $o_comment_model->get_comment($n_comment_id);
			if(!$o_comment->is_exists() || $o_comment->comment_id != $n_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', X2B_DOMAIN) );
			}

			$n_parent_post_id = $o_comment->parent_post_id;

			// check if permission is granted
			if(!$is_admin && !$o_comment->is_granted()) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', X2B_DOMAIN) );
			}

			// check if child comment exists on the comment
			$childs = $o_comment_model->get_child_comments($n_comment_id);
			if(count((Array)$childs) > 0) {
				$deleteAllComment = TRUE;
				if(!$is_admin) {
					$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
					foreach($childs as $val) {
						if($val->comment_author != $logged_info->ID) {
							$deleteAllComment = FALSE;
							break;
						}
					}
					unset($logged_info);
				}

				if(!$deleteAllComment) {
					return new \X2board\Includes\Classes\BaseObject(-1, __('fail_to_delete_children_comment', X2B_DOMAIN) );
				}
				else {
					foreach($childs as $val) {
						$output = $this->delete_comment($val->comment_id, $is_admin, $isMoveToTrash); // recursive
						if(!$output->toBool()) {
							return $output;
						}
					}
				}
			}

			// DELETE `comments` FROM `xe_comments` as `comments`  WHERE `comment_srl` = ?
			global $wpdb;
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments',
				array('comment_id'  => $n_comment_id ),
				array('%d'),
			);
			if( $result < 0 || $result === false ){
				wp_die($wpdb->last_error );
			}

			// DELETE `comments_list` FROM `xe_comments_list` as `comments_list`  WHERE `comment_srl` = ?
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments_list',
				array('comment_id'  => $n_comment_id ),
				array('%d'),
			);
			if( $result < 0 || $result === false ){
				wp_die($wpdb->last_error );
			}	

			// update the number of comments
			$comment_count = $o_comment_model->get_comment_count($n_parent_post_id);									
			unset($o_comment_model);
			// only post is exists
			if(isset($comment_count)) {
				// create the controller object of the post
				$o_post_controller = \X2board\Includes\getController('post');
				// update comment count of the article posting
				$output = $o_post_controller->update_comment_count($n_parent_post_id, $comment_count, NULL, FALSE);
				unset($o_post_controller);
				if(!$output->toBool()) {
					return $output;
				}
			}

			if(!$isMoveToTrash) {
				$this->_delete_declared_comments($n_comment_id);
				$this->_delete_voted_comments($n_comment_id);
			} 
			else {
				// UPDATE  `xe_files` as `files`  SET `isvalid` = ?  WHERE `upload_target_srl` = ?
				$result = $wpdb->update( "{$wpdb->prefix}x2b_files", 
										  array( 'isvalid' => 'N' ),
										  array( 'upload_target_id' => esc_sql(intval($n_comment_id))) 
										);
			}
			// delete a matching WP comment
			$this->_delete_wp_comment($o_comment->wp_comment_id);
			unset($o_comment);
			
			$output = new \X2board\Includes\Classes\BaseObject();
			$output->add('post_id', $n_parent_post_id);
			return $output;
		}

		/**
		 * delete declared comment, log
		 * _deleteDeclaredComments($commentSrls)
		 * @param array|string $commentSrls : srls string (ex: 1, 2,56, 88)
		 * @return void
		 */
		private function _delete_declared_comments($commentSrls) {
			return;
			// executeQuery('comment.deleteDeclaredComments', $commentSrls);
			// DELETE `comment_declared` FROM `xe_comment_declared` as `comment_declared`  WHERE `comment_srl` in (?)
			// executeQuery('comment.deleteCommentDeclaredLog', $commentSrls);
			// DELETE `comment_declared_log` FROM `xe_comment_declared_log` as `comment_declared_log`  WHERE `comment_srl` in (?)
		}

		/**
		 * delete voted comment log
		 * _deleteVotedComments($commentSrls)
		 * @param array|string $commentSrls : srls string (ex: 1, 2,56, 88)
		 * @return void
		 */
		private function _delete_voted_comments($commentSrls) {
			return;
			// executeQuery('comment.deleteCommentVotedLog', $commentSrls);
			// DELETE `comment_voted_log` FROM `xe_comment_voted_log` as `comment_voted_log`  WHERE `comment_srl` in (?)
		}

		/**
		 * x2b comment를 WP comment에 복제함
		 * @param int $a_comment_param
		 */
		private function _insert_wp_comment($a_comment_param){
			$a_comment = array(
				'comment_post_ID' => \X2board\Includes\get_wp_post_id_by_x2b_post_id($a_comment_param['parent_post_id']),
				'comment_author_email' => $a_comment_param['email_address'],
				'comment_author_url' => '',
				'comment_content' => strip_tags( $a_comment_param['content'] ),
				'comment_author_IP' => $a_comment_param['ipaddress'],
				'comment_agent' => $a_comment_param['ua'],
				// 'comment_type'  => '',
				'comment_date' => $a_comment_param['regdate_dt'], // date('Y-m-d H:i:s'),
				'comment_date_gmt' => $a_comment_param['regdate_dt'], // date('Y-m-d H:i:s'),
				'comment_approved' => 1,
			);

			if(\X2board\Includes\Classes\Context::get('is_logged')) {
				$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
				$a_comment['user_id'] = $o_logged_info->ID;
				$a_comment['comment_author'] = $o_logged_info->user_login;
			}

			$result = wp_insert_comment($a_comment, true);
			unset($a_comment);
			if( is_wp_error( $result ) ) {
				wp_die( $result->get_error_message() );
				return false;
			}
			return $result; // new WP comment ID
		}

		/**
		 * x2b comment를 WP comment에 수정함
		 * @param int $a_comment_param
		 */
		private function _update_wp_comment($a_comment_param) {
			$a_comment = array(
				'comment_ID' => $a_comment_param['wp_comment_id'],
				'comment_content' => strip_tags( $a_comment_param['content'] ),
				// 'comment_author_IP' => $a_comment_param['ipaddress'],
				// 'comment_agent' => $a_comment_param['ua'],
				// 'comment_type'  => '',
				'comment_date' => $a_comment_param['last_update_dt'],
				'comment_date_gmt' => $a_comment_param['last_update_dt'],
			);
			$result = wp_update_comment($a_comment);
			unset($a_comment);
			if( is_wp_error( $result ) ) {
				wp_die( $result->get_error_message() );
				return false;
			}
			return $result;
		}

		/**
		 * delete from WP comment 
		 * @param int $n_wp_comment_id
		 */
		private function _delete_wp_comment($n_wp_comment_id) {
			wp_delete_comment($n_wp_comment_id, true); // true means enforce to delete, no trash
		}
	}
}