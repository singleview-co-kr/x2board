<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * commentController class
 * controller class of the comment module
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/comment
 * @version 0.1
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
		function init()	{ }

		/**
		 * Enter comments
		 * @param object $obj
		 * @param bool $manual_inserted
		 * @return object
		 */
		// function insertComment($obj, $manual_inserted = FALSE)
		public function insert_comment($obj, $manual_inserted = FALSE) {
			if(!$manual_inserted) {  // check WP nonce if a guest inserts a new post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get('x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
				if( is_null( $wp_verify_nonce ) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request1', 'x2board') );
				}
				if( !wp_verify_nonce($wp_verify_nonce, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request2', 'x2board') );
				}
			}

			if(!is_object($obj)) {
				$obj = new \stdClass();
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if(!$manual_inserted) {
				if( \X2board\Includes\Classes\Context::get('is_logged') ) {
					// $o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
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
			$using_validation = $this->isModuleUsingPublishValidation(); // $obj->module_srl);
			if(!$using_validation) {
				$obj->status = 1;
			}
			else {
				if($is_admin) {
					$obj->status = 1;
				}
				else {
					$obj->status = 0;
				}
			}
			// $obj->__isupdate = FALSE;

			// call a trigger (before)
			// $output = ModuleHandler::triggerCall('comment.insertComment', 'before', $obj);
			// if(!$output->toBool())
			// {
			// 	return $output;
			// }

			// check if a posting of the corresponding post_id exists
			$parent_post_id = $obj->parent_post_id;
			if(!$parent_post_id) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', 'x2board') );
			}

			// get a object of post model
			// $o_post_model = \X2board\Includes\getModel('post');

			// even for manual_inserted if password exists, hash it.
			if($obj->password) {
				$obj->password = \X2board\Includes\getModel('member')->hash_password($obj->password);
			}

			// get the original posting
			// if(!$manual_inserted) {
			if(!$manual_inserted) {
				// get a object of post model
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($parent_post_id);
				unset($o_post_model);

				if($parent_post_id != $o_post->post_id) {
					return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_document', 'x2board') );
				}
				if(!$o_post->allow_comment()) {
					return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', 'x2board') );
				}
				unset($o_post);

				// if($obj->homepage) {
				// 	$obj->homepage = escape($obj->homepage, false);
				// 	if(!preg_match('/^[a-z]+:\/\//i',$obj->homepage)) {
				// 		$obj->homepage = 'http://'.$obj->homepage;
				// 	}
				// }

				// input the member's information if logged-in
				if(\X2board\Includes\Classes\Context::get('is_logged')) {
					// $o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
					$obj->comment_author = $o_logged_info->ID;
					// user_id, user_name and nick_name already encoded
					// $obj->user_id = htmlspecialchars_decode($o_logged_info->user_id);
					// $obj->user_name = htmlspecialchars_decode($o_logged_info->user_nicename);
					$obj->nick_name = htmlspecialchars_decode($o_logged_info->display_name);
					$obj->email_address = $o_logged_info->user_email;
					// $obj->homepage = $o_logged_info->homepage;
					// unset($o_logged_info);
				}
			}

			// error display if neither of log-in info and user name exist.
			if(!$o_logged_info->ID && !$obj->nick_name) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_invalid_request', 'x2board') );
			}

			if(!$obj->comment_id) {
				$obj->comment_id = \X2board\Includes\getNextSequence();
			}
			elseif(!$is_admin && !$manual_inserted && !\X2board\Includes\checkUserSequence($obj->comment_id)) {
				return new \X2board\Includes\Classes\BaseObject( -1, __('msg_not_permitted', 'x2board') );
			}

			// determine the order
			$obj->list_order = \X2board\Includes\getNextSequence() * -1;

			// remove XE's own tags from the contents
			// $obj->comment_content = preg_replace('!<\!--(Before|After)(Document|Comment)\(([0-9]+),([0-9]+)\)-->!is', '', $obj->comment_content);

			// if(Mobile::isFromMobilePhone() && $obj->use_editor != 'Y') {
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

			// if(!$obj->notify_message) {
			// 	$obj->notify_message = 'N';
			// }

			if(!isset($obj->is_secret)) {
				$obj->is_secret = 'N';
			}

			// begin transaction
			// $oDB = DB::getInstance();
			// $oDB->begin();

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
				// $parent_args = new \stdClass();
				// $parent_args->comment_id = $obj->parent_comment_id;
				// $parent_output = executeQuery('comment.getCommentListItem', $parent_args);
				// return if no parent comment exists
				// if(!$parent_output->toBool() || !$parent_output->data) {
				// 	return;
				// }
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
							
				$parent = $a_result[0];  // $parent_output->data;

				$list_args->head = $parent->head;
				$list_args->depth = $parent->depth + 1;

				// if the depth of comments is less than 2, execute insert.
				if($list_args->depth < 2) {  // if the depth of comments is greater than 2, execute update.
					$list_args->arrange = $obj->comment_id;
				}
				else {  // get the top listed comment among those in lower depth and same head with parent's.
					// $p_args = new stdClass();
					// $p_args->head = $parent->head;
					// $p_args->arrange = $parent->arrange;
					// $p_args->depth = $parent->depth;
					// $output = executeQuery('comment.getCommentParentNextSibling', $p_args);
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
						// $output = executeQuery('comment.updateCommentListArrange', $list_args);
						// "UPDATE  `xe_comments_list` as `comments_list`  SET `arrange` = `arrange` + ?  WHERE `parent_post_id` = ? and `head` = ? and `arrange` >= ?"
						$result = $wpdb->update ( "{$wpdb->prefix}x2b_comments_list", 
												  array( 'arrange' => 'arrange' + 1 ),
												  array( 'parent_post_id' => esc_sql(intval($list_args->parent_post_id)),
												  		 'head' => esc_sql(intval($list_args->head)),
														 'arrange' => esc_sql(intval($list_args->arrange)) ) 
												);
// var_dump($result);												
						if( $result < 0 || $result === false ){
// var_dump($a_rst[0]->arrange);
// 
// exit;							
							return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
						}
					}
					else {
						$list_args->arrange = $obj->comment_id;
					}
				}
			}
// var_dump($list_args);
// exit;	
			$this->insert_comment_list($list_args);

			// sanitize
			$a_new_comment = array();
			$a_new_comment['board_id'] = $obj->board_id;
			$a_new_comment['parent_post_id'] = intval($obj->parent_post_id);
			$a_new_comment['content'] = $obj->content;  // sanitize_text_field eliminates all HTML tag
			$a_new_comment['parent_comment_id'] = intval($obj->parent_comment_id);
			$a_new_comment['comment_id'] = intval($obj->comment_id);
			// $a_new_comment['use_editor'] = sanitize_text_field($obj->use_editor);
			// $a_new_comment['use_html'] = sanitize_text_field($obj->use_html);
			$a_new_comment['password'] = $obj->password;
			// $a_new_comment['notify_message'] = sanitize_text_field($obj->notify_message);
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
				// $this->{$key} = $value;
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
			// $output = executeQuery('comment.insertComment', $obj);
			// if(!$output->toBool()) {
			// 	// $oDB->rollback();
			// 	return $output;
			// }

			// creat the comment model object
			$o_comment_model = \X2board\Includes\getModel('comment');

			// get the number of all comments in the posting
			$n_comment_count = $o_comment_model->get_comment_count($parent_post_id);
			unset($o_comment_model);
// var_dump($is_admin);
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
// exit;
			// call a trigger(after)
			// if($output->toBool()) {
			// 	$trigger_output = ModuleHandler::triggerCall('comment.insertComment', 'after', $obj);
			// 	if(!$trigger_output->toBool())
			// 	{
			// 		$oDB->rollback();
			// 		return $trigger_output;
			// 	}
			// }

			// commit
			// $oDB->commit();

			// if(!$manual_inserted) {
			// 	// send a message if notify_message option in enabled in the original article
			// 	$oDocument->notify(Context::getLang('comment'), $obj->comment_content);

			// 	// send a message if notify_message option in enabled in the original comment
			// 	if($obj->parent_srl)
			// 	{
			// 		$oParent = $oCommentModel->getComment($obj->parent_srl);
			// 		if($oParent->get('member_srl') != $oDocument->get('member_srl'))
			// 		{
			// 			$oParent->notify(Context::getLang('comment'), $obj->comment_content);
			// 		}
			// 	}
			// }
			
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
			//////////////////////////////////////////
			///////////// begin - temp exception
			if( !isset($output)){
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			///////////// end - temp exception
			//////////////////////////////////////////
			$output->add('comment_id', $obj->comment_id);
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
				// return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
				wp_die($wpdb->last_error );
			} 
			unset($a_insert_key);
			unset($a_insert_data);
			// $output = executeQuery('comment.insertCommentList', $list_args);
			// if(!$output->toBool()) {
			// 	return $output;
			// }
		}

		/**
		 * update the comment
		 * @param object $obj
		 * @param bool $is_admin
		 * @param bool $manual_updated
		 * @return object
		 */
		// function updateComment($obj, $is_admin = FALSE, $manual_updated = FALSE)
		public function update_comment($obj, $is_admin = FALSE, $manual_updated = FALSE) {
			// if(!$manual_updated && !checkCSRF()) {
			// 	return new BaseObject(-1, 'msg_invalid_request');
			// }
			if(!$manual_updated) {  // check WP nonce if a guest inserts a new post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get('x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
				if( is_null( $wp_verify_nonce ) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request1', 'x2board') );
				}
				if( !wp_verify_nonce($wp_verify_nonce, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request2', 'x2board') );
				}
			}

			if(!is_object($obj)) {
				$obj = new \stdClass();
			}

			// call a trigger (before)
			// $output = ModuleHandler::triggerCall('comment.updateComment', 'before', $obj);
			// if(!$output->toBool())
			// {
			// 	return $output;
			// }

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
				// $obj->homepage = $o_source_comment->get('homepage');
			}

			// check if permission is granted
			if(!$is_admin && !$o_source_comment->is_granted()) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', 'x2board') );
			}

			if($obj->password) {
				$obj->password = \X2board\Includes\getModel('member')->hashPassword($obj->password);
			}

			// if($obj->homepage) 
			// {
			// 	$obj->homepage = escape($obj->homepage);
			// 	if(!preg_match('/^[a-z]+:\/\//i',$obj->homepage))
			// 	{
			// 		$obj->homepage = 'http://'.$obj->homepage;
			// 	}
			// }

			// set modifier's information if logged-in and posting author and modifier are matched.
			if(\X2board\Includes\Classes\Context::get('is_logged')) {
				$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
				if($o_source_comment->comment_author == $logged_info->ID) {
					$obj->comment_author = $logged_info->ID;
					// $obj->user_name = $logged_info->user_name;
					$obj->nick_name = $logged_info->nick_name;
					$obj->email_address = $logged_info->email_address;
					// $obj->homepage = $logged_info->homepage;
				}
			}

			// if nick_name of the logged-in author doesn't exist
			if($o_source_comment->get('comment_author') && !$obj->nick_name) {
				$obj->comment_author = $o_source_comment->get('comment_author');
				// $obj->user_name = $o_source_comment->get('user_name');
				$obj->nick_name = $o_source_comment->get('nick_name');
				$obj->email_address = $o_source_comment->get('email_address');
				// $obj->homepage = $o_source_comment->get('homepage');
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
				$obj->content = removeHackTag($obj->content);
			}

			// begin transaction
			// $oDB = DB::getInstance();
			// $oDB->begin();

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
			// $output = executeQuery('comment.updateComment', $obj);
			// if(!$output->toBool())
			// {
			// 	$oDB->rollback();
			// 	return $output;
			// }

			// call a trigger (after)
			// if($output->toBool())
			// {
			// 	$trigger_output = ModuleHandler::triggerCall('comment.updateComment', 'after', $obj);
			// 	if(!$trigger_output->toBool())
			// 	{
			// 		$oDB->rollback();
			// 		return $trigger_output;
			// 	}
			// }

			// commit
			// $oDB->commit();
			if( !isset($output)) {
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			$output->add('comment_id', $obj->comment_id);
			unset($obj);
			return $output;
		}
		
		/**
		 * Check if module is using comment validation system
		 * @param int $module_srl
		 * @return bool
		 */
		// function isModuleUsingPublishValidation($module_srl = NULL)
		public function isModuleUsingPublishValidation() { // $module_srl = NULL) {
			return false;
			// if($module_srl == NULL)	{
			// 	return FALSE;
			// }
			// $oModuleModel = getModel('module');
			// $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			// $module_part_config = $oModuleModel->getModulePartConfig('comment', $module_info->module_srl);
			$module_part_config = new \stdClass();

			$use_validation = FALSE;
			if(isset($module_part_config->use_comment_validation) && $module_part_config->use_comment_validation == "Y") {
				$use_validation = TRUE;
			}
			return $use_validation;
		}

		/**
		 * Authorization of the comments
		 * available only in the current connection of the session value
		 * @return void
		 */
		// function addGrant($comment_srl)
		private function _add_grant($comment_id) {
			$_SESSION['own_comment'][$comment_id] = TRUE;
		}

		/**
		 * Trigger to delete its comments together with post deleted
		 * @return BaseObject
		 */
		// function triggerDeleteDocumentComments(&$obj)
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
		 * @return object
		 */
		// function deleteComments($document_srl, $obj = NULL)
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
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', 'x2board') );
			}

			// get a list of comments and then execute a trigger(way to reduce the processing cost for delete all)
			// $args = new stdClass();
			// $args->document_srl = $n_post_id;
			// $comments = executeQueryArray('comment.getAllComments', $args);
			// SELECT `comment_id`, `board_id`, `comment_author`, `parent_post_id`  FROM `xe_comments` as `comments`   WHERE `document_srl` in (?)    
			global $wpdb;
			$s_columns = "`comment_id`"; //, `board_id`, `comment_author`, `parent_post_id`";
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

			if(count((array)$a_result)) {
				$commentSrlList = array();
				foreach($a_result as $comment) {
					$commentSrlList[] = $comment->comment_id;
					// call a trigger (before)
					// $output = ModuleHandler::triggerCall('comment.deleteComment', 'before', $comment);
					// if(!$output->toBool()) {
					// 	continue;
					// }
					// call a trigger (after)
					// $output = ModuleHandler::triggerCall('comment.deleteComment', 'after', $comment);
					// if(!$output->toBool()) {
					// 	continue;
					// }
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
var_dump($wpdb->last_error);
				wp_die($wpdb->last_error );
			}

			// $args->document_srl = $n_post_id;
			// $output = executeQuery('comment.deleteComments', $args);
			// if(!$output->toBool()) {
			// 	return $output;
			// }

			// Delete a list of comments
			// DELETE `comments_list` FROM `xe_comments_list` as `comments_list`  WHERE `document_srl` = ?
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments_list',
				array('parent_post_id'  => $n_post_id ),
				array('%d'), // make sure the id format
			);
			if( $result < 0 || $result === false ){
var_dump($wpdb->last_error);
				wp_die($wpdb->last_error );
			}
			// $output = executeQuery('comment.deleteCommentsList', $args);

			//delete declared, declared_log, voted_log
			if(is_array($commentSrlList) && count($commentSrlList) > 0) {
				// $args = new stdClass();
				// $args->comment_srl = join(',', $commentSrlList);
				$args = join(',', $commentSrlList);
				$this->_delete_declared_comments($args);
				$this->_delete_voted_comments($args);
			}
// exit;			
			return new \X2board\Includes\Classes\BaseObject(); // $output;
		}

		/**
		 * Delete comment
		 * @param int $comment_srl
		 * @param bool $is_admin
		 * @param bool $isMoveToTrash
		 * @return object
		 */
		// function deleteComment($comment_srl, $is_admin = FALSE, $isMoveToTrash = FALSE)
		public function delete_comment($n_comment_id, $is_admin = FALSE, $isMoveToTrash = FALSE) {
			// create the comment model object
			$o_comment_model = \X2board\Includes\getModel('comment');

			// check if comment already exists
			$o_comment = $o_comment_model->get_comment($n_comment_id);
// var_dump($o_comment);
// exit;
			if($o_comment->comment_id != $n_comment_id) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', 'x2board') );
			}

			$n_parent_post_id = $o_comment->parent_post_id;

			// call a trigger (before)
			// $output = ModuleHandler::triggerCall('comment.deleteComment', 'before', $comment);
			// if(!$output->toBool())
			// {
			// 	return $output;
			// }

			// check if permission is granted
			if(!$is_admin && !$o_comment->is_granted()) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', 'x2board') );
			}

			// check if child comment exists on the comment
			$childs = $o_comment_model->get_child_comments($n_comment_id);
// var_dump($childs);
// exit;
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
					return new \X2board\Includes\Classes\BaseObject(-1, __('fail_to_delete_have_children', 'x2board') );
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

			// begin transaction
			// $oDB = DB::getInstance();
			// $oDB->begin();

			// Delete
			// $args = new stdClass();
			// $args->comment_srl = $n_comment_id;
			// $output = executeQuery('comment.deleteComment', $args);
			// if(!$output->toBool()) {
			// 	$oDB->rollback();
			// 	return $output;
			// }

			// DELETE `comments` FROM `xe_comments` as `comments`  WHERE `comment_srl` = ?
			global $wpdb;
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments',
				array('comment_id'  => $n_comment_id ),
				array('%d'), // make sure the id format
			);
			if( $result < 0 || $result === false ){
// var_dump($wpdb->last_error);
				wp_die($wpdb->last_error );
			}

			// $output = executeQuery('comment.deleteCommentList', $args);
			// DELETE `comments_list` FROM `xe_comments_list` as `comments_list`  WHERE `comment_srl` = ?
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_comments_list',
				array('comment_id'  => $n_comment_id ),
				array('%d'), // make sure the id format
			);
			if( $result < 0 || $result === false ){
// var_dump($wpdb->last_error);
				wp_die($wpdb->last_error );
			}	

			// update the number of comments
			$comment_count = $o_comment_model->get_comment_count($n_parent_post_id);									
			unset($o_comment_model);
			// only document is exists
			if(isset($comment_count)) {
				// create the controller object of the document
				$o_post_controller = \X2board\Includes\getController('post');
				// update comment count of the article posting
				$output = $o_post_controller->update_comment_count($n_parent_post_id, $comment_count, NULL, FALSE);
				unset($o_post_controller);
				if(!$output->toBool()) {
					// $oDB->rollback();
					return $output;
				}
			}
// var_Dump($isMoveToTrash);
// exit;	
			// call a trigger (after)
			// if($output->toBool())
			// {
			// 	$comment->isMoveToTrash = $isMoveToTrash;
			// 	$trigger_output = ModuleHandler::triggerCall('comment.deleteComment', 'after', $comment);
			// 	if(!$trigger_output->toBool())
			// 	{
			// 		$oDB->rollback();
			// 		return $trigger_output;
			// 	}
			// 	unset($comment->isMoveToTrash);
			// }

			if(!$isMoveToTrash) {
				$this->_delete_declared_comments($n_comment_id);
				$this->_delete_voted_comments($n_comment_id);
			} 
			else {
				// $args = new stdClass();
				// $args->upload_target_srl = $n_comment_id;
				// $args->isvalid = 'N';
				// $output = executeQuery('file.updateFileValid', $args);
				// UPDATE  `xe_files` as `files`  SET `isvalid` = ?  WHERE `upload_target_srl` = ?
				$result = $wpdb->update( "{$wpdb->prefix}x2b_files", 
										  array( 'isvalid' => 'N' ),
										  array( 'upload_target_id' => esc_sql(intval($n_comment_id))) 
										);
			}
			// delete a matching WP comment
			$this->_delete_wp_comment($o_comment->wp_comment_id);
			unset($o_comment);
			
			// commit
			// $oDB->commit();
			$output = new \X2board\Includes\Classes\BaseObject();
			$output->add('post_id', $n_parent_post_id);
			return $output;
		}

		/**
		 * delete declared comment, log
		 * @param array|string $commentSrls : srls string (ex: 1, 2,56, 88)
		 * @return void
		 */
		// function _deleteDeclaredComments($commentSrls)
		private function _delete_declared_comments($commentSrls) {
			// executeQuery('comment.deleteDeclaredComments', $commentSrls);
			// DELETE `comment_declared` FROM `xe_comment_declared` as `comment_declared`  WHERE `comment_srl` in (?)
			// executeQuery('comment.deleteCommentDeclaredLog', $commentSrls);
			// DELETE `comment_declared_log` FROM `xe_comment_declared_log` as `comment_declared_log`  WHERE `comment_srl` in (?)
		}

		/**
		 * delete voted comment log
		 * @param array|string $commentSrls : srls string (ex: 1, 2,56, 88)
		 * @return void
		 */
		// function _deleteVotedComments($commentSrls)
		private function _delete_voted_comments($commentSrls) {
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
				'comment_author_email' => $a_comment_param['email_address'],  //'dave@domain.com',
				'comment_author_url' => '',
				'comment_content' => strip_tags( $a_comment_param['content'] ), // 'Lorem ipsum dolor sit amet...',
				'comment_author_IP' => $a_comment_param['ipaddress'],  //'127.3.1.1',
				'comment_agent' => $a_comment_param['ua'], //$_SERVER['HTTP_USER_AGENT'],
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
				'comment_content' => strip_tags( $a_comment_param['content'] ), // 'Lorem ipsum dolor sit amet...',
				// 'comment_author_IP' => $a_comment_param['ipaddress'],  //'127.3.1.1',
				// 'comment_agent' => $a_comment_param['ua'], //$_SERVER['HTTP_USER_AGENT'],
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



		

/////////////////////////////////////////

		/**
		 * Remove all comment relation log
		 * @return BaseObject
		 */
		function deleteCommentLog($args)
		{
			$this->_deleteDeclaredComments($args);
			$this->_deleteVotedComments($args);
			return new BaseObject(0, 'success');
		}

		/**
		 * Get comment all list
		 * @return void
		 */
		function procCommentGetList()
		{
			if(!Context::get('is_logged'))
			{
				return new BaseObject(-1, 'msg_not_permitted');
			}

			$commentSrls = Context::get('comment_srls');
			if($commentSrls)
			{
				$commentSrlList = explode(',', $commentSrls);
			}

			if(count($commentSrlList) > 0)
			{
				$oCommentModel = getModel('comment');
				$commentList = $oCommentModel->getComments($commentSrlList);

				if(is_array($commentList))
				{
					foreach($commentList as $value)
					{
						$value->content = strip_tags($value->content);
					}
				}
			}
			else
			{
				global $lang;
				$commentList = array();
				$this->setMessage($lang->no_documents);
			}

			$oSecurity = new Security($commentList);
			$oSecurity->encodeHTML('..variables.', '..');

			$this->add('comment_list', $commentList);
		}

		/**
		 * Send email to module's admins after a new comment was interted successfully
		 * if Comments Approval System is used 
		 * @param object $obj 
		 * @return void
		 */
		// function sendEmailToAdminAfterInsertComment($obj)
		// {
		// 	$using_validation = $this->isModuleUsingPublishValidation($obj->module_srl);

		// 	$oDocumentModel = getModel('document');
		// 	$oDocument = $oDocumentModel->getDocument($obj->document_srl);

		// 	$oMemberModel = getModel("member");
		// 	if(isset($obj->member_srl) && !is_null($obj->member_srl))
		// 	{
		// 		$member_info = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);
		// 	}
		// 	else
		// 	{
		// 		$member_info = new stdClass();
		// 		$member_info->is_admin = "N";
		// 		$member_info->nick_name = $obj->nick_name;
		// 		$member_info->user_name = $obj->user_name;
		// 		$member_info->email_address = $obj->email_address;
		// 	}

		// 	$oCommentModel = getModel("comment");
		// 	$nr_comments_not_approved = $oCommentModel->getCommentAllCount(NULL, FALSE);

		// 	$oModuleModel = getModel("module");
		// 	$module_info = $oModuleModel->getModuleInfoByDocumentSrl($obj->document_srl);

		// 	// If there is no problem to register comment then send an email to all admin were set in module admin panel
		// 	if($module_info->admin_mail && $member_info->is_admin != 'Y')
		// 	{
		// 		$oMail = new Mail();
		// 		$oMail->setSender($obj->email_address, $obj->email_address);
		// 		$mail_title = "[XE - " . Context::get('mid') . "] A new comment was posted on document: \"" . $oDocument->getTitleText() . "\"";
		// 		$oMail->setTitle($mail_title);
		// 		$url_comment = getFullUrl('','document_srl',$obj->document_srl).'#comment_'.$obj->comment_srl;
		// 		if($using_validation)
		// 		{
		// 			$url_approve = getFullUrl('', 'module', 'admin', 'act', 'procCommentAdminChangePublishedStatusChecked', 'cart[]', $obj->comment_srl, 'will_publish', '1', 'search_target', 'is_published', 'search_keyword', 'N');
		// 			$url_trash = getFullUrl('', 'module', 'admin', 'act', 'procCommentAdminDeleteChecked', 'cart[]', $obj->comment_srl, 'search_target', 'is_trash', 'search_keyword', 'true');
		// 			$mail_content = "
		// 				A new comment on the document \"" . $oDocument->getTitleText() . "\" is waiting for your approval.
		// 				<br />
		// 				<br />
		// 				Author: " . $member_info->nick_name . "
		// 				<br />Author e-mail: " . $member_info->email_address . "
		// 				<br />From : <a href=\"" . $url_comment . "\">" . $url_comment . "</a>
		// 				<br />Comment:
		// 				<br />\"" . $obj->content . "\"
		// 				<br />Document:
		// 				<br />\"" . $oDocument->getContentText(). "\"
		// 				<br />
		// 				<br />
		// 				Approve it: <a href=\"" . $url_approve . "\">" . $url_approve . "</a>
		// 				<br />Trash it: <a href=\"" . $url_trash . "\">" . $url_trash . "</a>
		// 				<br />Currently " . $nr_comments_not_approved . " comments on \"" . Context::get('mid') . "\" module are waiting for approval. Please visit the moderation panel:
		// 				<br /><a href=\"" . getFullUrl('', 'module', 'admin', 'act', 'dispCommentAdminList', 'search_target', 'module', 'search_keyword', $obj->module_srl) . "\">" . getFullUrl('', 'module', 'admin', 'act', 'dispCommentAdminList', 'search_target', 'module', 'search_keyword', $obj->module_srl) . "</a>
		// 				";
		// 			$oMail->setContent($mail_content);
		// 		}
		// 		else
		// 		{
		// 			$mail_content = "
		// 				Author: " . $member_info->nick_name . "
		// 				<br />Author e-mail: " . $member_info->email_address . "
		// 				<br />From : <a href=\"" . $url_comment . "\">" . $url_comment . "</a>
		// 				<br />Comment:
		// 				<br />\"" . $obj->content . "\"
		// 				<br />Document:
		// 				<br />\"" . $oDocument->getContentText(). "\"
		// 				";
		// 			$oMail->setContent($mail_content);

		// 			// get email of thread's author
		// 			$document_author_email = $oDocument->variables['email_address'];

		// 			//get admin info
		// 			$logged_info = Context::get('logged_info');

		// 			//mail to author of thread - START
		// 			/**
		// 			 * @todo Removed code send email to document author.
		// 			*/
		// 			/*
		// 			if($document_author_email != $obj->email_address && $logged_info->email_address != $document_author_email)
		// 			{
		// 				$oMail->setReceiptor($document_author_email, $document_author_email);
		// 				$oMail->send();
		// 			}
		// 			*/
		// 			// mail to author of thread - STOP
		// 		}

		// 		// get all admins emails
		// 		$admins_emails = $module_info->admin_mail;
		// 		$target_mail = explode(',', $admins_emails);

		// 		// send email to all admins - START
		// 		for($i = 0; $i < count($target_mail); $i++)
		// 		{
		// 			$email_address = trim($target_mail[$i]);
		// 			if(!$email_address)
		// 			{
		// 				continue;
		// 			}

		// 			$oMail->setReceiptor($email_address, $email_address);
		// 			$oMail->send();
		// 		}
		// 		//  send email to all admins - STOP
		// 	}

		// 	$comment_srl_list = array(0 => $obj->comment_srl);
		// 	// call a trigger for calling "send mail to subscribers" (for moment just for forum)
		// 	ModuleHandler::triggerCall("comment.sendEmailToAdminAfterInsertComment", "after", $comment_srl_list);

		// 	/*
		// 	// send email to author - START
		// 	$oMail = new Mail();
		// 	$mail_title = "[XE - ".Context::get('mid')."] your comment on document: \"".$oDocument->getTitleText()."\" have to be approved";
		// 	$oMail->setTitle($mail_title);
		// 	//$mail_content = sprintf("From : <a href=\"%s?document_srl=%s&comment_srl=%s#comment_%d\">%s?document_srl=%s&comment_srl=%s#comment_%d</a><br/>\r\n%s  ", getFullUrl(''),$comment->document_srl,$comment->comment_srl,$comment->comment_srl, getFullUrl(''),$comment->document_srl,$comment->comment_srl,$comment->comment_srl,$comment>content);
		// 	$mail_content = "
		// 	Your comment #".$obj->comment_srl." on document \"".$oDocument->getTitleText()."\" have to be approved by admin of <strong><i>".  strtoupper($module_info->mid)."</i></strong> module before to be publish.
		// 	<br />
		// 	<br />Comment content:
		// 	".$obj->content."
		// 	<br />
		// 	";
		// 	$oMail->setContent($mail_content);
		// 	$oMail->setSender($obj->email_address, $obj->email_address);
		// 	$oMail->setReceiptor($obj->email_address, $obj->email_address);
		// 	$oMail->send();
		// 	// send email to author - START
		// 	*/
		// 	return;
		// }
	}
}
/* End of file comment.controller.php */