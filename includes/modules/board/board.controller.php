<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  boardController
 * @author XEHub (developers@xpressengine.com)
 * @brief  board module Controller class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardController')) {

	class boardController extends board {

		private $_s_wp_post_guid = null;

		/**
		 * @brief initialization
		 **/
		function init()	{

			// begin - define redirect url root
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			$o_post = get_post(intval($n_board_id));
			if( is_null($o_post) ) {
				wp_die(__('weird error occured in boardController::init()', 'x2board'));
			}
			$this->_s_wp_post_guid = $o_post->guid;
			unset($o_post);
			// end - define redirect url root

			$s_cmd = \X2board\Includes\Classes\Context::get('cmd');
			switch( $s_cmd ) {
				case X2B_CMD_PROC_WRITE_POST:
				case X2B_CMD_PROC_VERIFY_PASSWORD:
				case X2B_CMD_PROC_MODIFY_POST:
				case X2B_CMD_PROC_DELETE_POST:
				case X2B_CMD_PROC_WRITE_COMMENT:
				case X2B_CMD_PROC_DELETE_COMMENT:
				case X2B_CMD_PROC_AJAX_FILE_UPLOAD:
				case X2B_CMD_PROC_AJAX_FILE_DELETE:
				case X2B_CMD_PROC_DOWNLOAD_FILE:
				case X2B_CMD_PROC_OUTPUT_FILE:
					$s_cmd = '_'.$s_cmd;
					$this->$s_cmd();
					break;
				default:
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
					return;
			}	
		}

		/**
		 * @brief check download file
		 **/
		private function _proc_output_file() {
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->init(); // to init related $_SESSION
			// $o_appending_file_conf = new \stdClass();
			// foreach( $this->module_info as $s_key => $val ){
			// 	if( substr( $s_key, 0, 5 ) === "file_" ) {
			// 		$o_appending_file_conf->$s_key = $val;
			// 	}
			// }
			// \X2board\Includes\Classes\Context::set('appending_file_config', $o_appending_file_conf);
			$o_file_controller->proc_file_output();
			unset($o_file_controller);
		}

		/**
		 * @brief check download file
		 **/
		private function _proc_download_file() {
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->init(); // to init related $_SESSION
			$o_appending_file_conf = new \stdClass();
			foreach( $this->module_info as $s_key => $val ){
				if( substr( $s_key, 0, 5 ) === "file_" ) {
					$o_appending_file_conf->$s_key = $val;
				}
			}
			\X2board\Includes\Classes\Context::set('appending_file_config', $o_appending_file_conf);
			$o_file_controller->proc_file_download();
			unset($o_file_controller);
		}

		/**
		 * @brief upload file ajax
		 **/
		private function _proc_ajax_file_upload() {
			check_ajax_referer(X2B_AJAX_SECURITY, 'security');
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->init(); // to init related $_SESSION
			$upload_attach_files = $o_file_controller->proc_file_upload();
			unset($o_file_controller);
			wp_send_json(['result'=>'success', 'files'=>$upload_attach_files]);
		}

		/**
		 * @brief upload file ajax
		 **/
		private function _proc_ajax_file_delete() {
			check_ajax_referer(X2B_AJAX_SECURITY, 'security');
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->init(); // to init related $_SESSION
			$o_rst = $o_file_controller->proc_file_delete();
			unset($o_file_controller);
			if(!$o_rst->toBool()){
				wp_send_json(['result'=>'error', 'message'=>__('It is an invalid access.', 'x2board')]);
			}		
			wp_send_json(['result'=>'success']);		
		}

		/**
		 * @brief update post
		 **/
		private function _proc_modify_post() {
			$this->_proc_write_post();
		}

		/**
		 * @brief insert post
		 **/
		// function procBoardInsertDocument()
		private function _proc_write_post() {
			// check grant
			// if($this->module_info->module != "board") {
			// 	return new \X2board\Includes\Classes\BaseObject(-1, __('msg_invalid_request', 'x2board') );
			// }
			if(!$this->grant->write_post) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_not_permitted', 'x2board'));
				return;
			}

			// setup variables
			$obj = \X2board\Includes\Classes\Context::gets('board_id', 'post_id', 'title', 'content', 'nick_name', 
															'category_id','is_secret', 'is_notice', 'password', 
															'allow_comment',
															// 'status',  // for XE board skin compatible
														);
			if(is_null($obj->board_id) || intval($obj->board_id) <= 0) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
				return;
			}

// var_dump($this->module_info->excerpted_title_length);
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');

			$obj->post_author = $o_logged_info->ID;

			$o_comment_class = \X2board\Includes\getClass('comment');
			if( $obj->allow_comment == 'Y' ) {
				$obj->comment_status = $o_comment_class->get_status_by_key('allow'); //'ALLOW';
			}
			else {
				$obj->comment_status = $o_comment_class->get_status_by_key('deny'); // 'DENY';
			}
			unset($obj->allow_comment);
			unset($o_comment_class);
			
			// $obj->module_srl = $this->module_srl;
			if($obj->is_notice!='Y'||!$this->grant->manager) {
				$obj->is_notice = 'N';
			}
// var_dump($obj);
// exit;
			// $oModuleModel = getModel('module');
			// $module_config = $oModuleModel->getModuleInfoByModuleSrl($obj->module_srl);

/////////// tmporary test block begin /////////////
$module_config = new \stdClass();
$module_config->mobile_use_editor = 'Y';
/////////// tmporary test block end /////////////

			if($module_config->mobile_use_editor === 'Y') {
				if(!isset($obj->use_editor)) $obj->use_editor = 'Y';
				if(!isset($obj->use_html)) $obj->use_html = 'Y';
			}
			else {
				if(!isset($obj->use_editor)) $obj->use_editor = 'N';
				if(!isset($obj->use_html)) $obj->use_html = 'N';
			}

			settype($obj->title, "string");
			$n_excerpted_title_length = $this->module_info->excerpted_title_length ? (int)$this->module_info->excerpted_title_length : 20;
			if($obj->title == '') {
				$obj->title = cut_str(trim(strip_tags(nl2br($obj->content))),$n_excerpted_title_length,'...');
			}
			//setup post title to 'Untitled'
			if($obj->title == '') {
				$obj->title = __('Untitled', 'x2board'); //'Untitled';
			}

			// unset post style if the user is not the post manager
			if(!$this->grant->manager) {
				unset($obj->title_color);
				unset($obj->title_bold);
			}

			// generate post module model object
			$o_post_model = \X2board\Includes\getModel('post');
			// check if the post is existed
			$o_post = $o_post_model->get_post($obj->post_id, $this->grant->manager);
			unset($o_post_model);

			// update the post if it is existed
			$is_update = false;
			if($o_post->is_exists() && $o_post->post_id == $obj->post_id) {
				$is_update = true;
			}

			// if use anonymous is true
			if($this->module_info->use_anonymous == 'Y') {
				$this->module_info->admin_mail = '';
				$obj->notify_message = 'N';
				if($is_update===false) {
					$obj->post_author = 0;//-1*$o_logged_info->ID;
				}
				// $obj->email_address = $obj->homepage = $obj->user_id = '';
				// $obj->user_name = $obj->nick_name = 'anonymous';
				$obj->email_address = '';
				$obj->nick_name = __('Anonymous', 'x2board'); //'anonymous';
				$bAnonymous = true;
				if($is_update===false) {
					$o_post->add('post_author', $obj->post_author);
				}
			}
			else {
				$bAnonymous = false;
			}
			unset($o_logged_info);
			
			$o_post_model = \X2board\Includes\getModel('post');
			$s_secret_status = $o_post_model->get_config_status('secret');
			$s_public_status = $o_post_model->get_config_status('public');
			unset($o_post_model);
			
			$obj->status = $obj->is_secret == 'Y' ? $s_secret_status : $s_public_status;  // PUBLIC SECRET TEMP
			if($obj->is_secret == 'Y' || strtoupper($obj->status) == $s_secret_status) {
				$use_status = $this->module_info->use_status; // explode('|@|', $this->module_info->use_status);
				if(!is_array($use_status) || !in_array($s_secret_status, $use_status)) {
					unset($obj->is_secret);
					$obj->status = $s_public_status;
				}
			}

			// update the post if it is existed
			if($is_update) {
				if(!$o_post->is_granted()) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_not_permitted', 'x2board'));
					return;
				}

				if($this->module_info->use_anonymous == 'Y') {
					$obj->post_author = abs($o_post->get('post_author')) * -1;
					$o_post->add('post_author', $obj->post_author);
				}

				if($this->module_info->protect_content=="Y" && $o_post->get('comment_count')>0 && $this->grant->manager==false) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_protected_content', 'x2board'));
					return;
				}

				if(!$this->grant->manager) {
					// notice & post style same as before if not manager
					$obj->is_notice = $o_post->get('is_notice');
					$obj->title_color = $o_post->get('title_color');
					$obj->title_bold = $o_post->get('title_bold');
				}
				
				// modify list_order if post status is temp
				if($o_post->get('status') == 'TEMP') {
					$obj->last_update_dt = $obj->regdate_dt = date('YmdHis');
					$obj->update_order = $obj->list_order = (getNextSequence() * -1);
				}
				// generate post module의 controller object
				$o_post_controller = \X2board\Includes\getController('post');
				$output = $o_post_controller->update_post($o_post, $obj, true);
				unset($o_post_controller);
				$msg_code = 'success_updated';
			} 
			else {  // insert a new post otherwise
				$o_post_controller = \X2board\Includes\getController('post');
				$output = $o_post_controller->insert_post($obj, $bAnonymous);
				unset($o_post_controller);

				$msg_code = 'success_registed';
				$obj->post_id = $output->get('post_id');
// var_dump($output);
				// send an email to admin user
				if($output->toBool() && $this->module_info->admin_mail) {
					// $oModuleModel = getModel('module');
					// $member_config = $oModuleModel->getModuleConfig('member');
					
					// $oMail = new Mail();
					// $oMail->setTitle($obj->title);
					// $oMail->setContent( sprintf("From : <a href=\"%s\">%s</a><br/>\r\n%s", getFullUrl('','post_id',$obj->post_id), getFullUrl('','post_id',$obj->post_id), $obj->content));
					// $oMail->setSender($obj->user_name ? $obj->user_name : 'anonymous', $obj->email_address ? $obj->email_address : $member_config->webmaster_email);

					// $target_mail = explode(',',$this->module_info->admin_mail);
					// for($i=0;$i<count($target_mail);$i++)
					// {
					// 	$email_address = trim($target_mail[$i]);
					// 	if(!$email_address) continue;
					// 	$oMail->setReceiptor($email_address, $email_address);
					// 	$oMail->send();
					// }
				}
			}
			if(!$output->toBool()) {  // if there is an error
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.$output->getMessage());
			}
			else { // if s_wp_redirect_url is not added, automatically redirect to home_url
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?'.X2B_CMD_VIEW_POST.'/'.$output->get('post_id'));
			}
		}

		/**
		 * @brief insert comment
		 **/
		// function procBoardInsertComment()
		private function _proc_write_comment() {
var_dump(X2B_CMD_PROC_WRITE_COMMENT);

			// check grant
			if(!$this->grant->write_comment) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_not_permitted', 'x2board'));
				return;
			}
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');

			// get the relevant data for inserting comment
			// $obj = Context::getRequestVars();
			// $obj->module_srl = $this->module_srl;
			$obj = \X2board\Includes\Classes\Context::gets( 'board_id', 'parent_post_id', 
															'content', 'password', 'nick_name',
															'parent_comment_id', 'comment_id', 
															'is_secret',
															'use_editor', 'use_html' );

			// if(!$this->module_info->use_status) {
			// 	$this->module_info->use_status = 'PUBLIC';
			// }
			// if(!is_array($this->module_info->use_status)) {
			// 	$this->module_info->use_status = explode('|@|', $this->module_info->use_status);
			// }

			if(in_array('SECRET', $this->module_info->use_status)) {
				$this->module_info->secret = 'Y';
			}
			else {
				unset($obj->is_secret);
				$this->module_info->secret = 'N';
			}
	
			if($this->module_info->mobile_use_editor === 'Y') {
				if(!isset($obj->use_editor)) {
					$obj->use_editor = 'Y';
				}
				if(!isset($obj->use_html)) {
					$obj->use_html = 'Y';
				}
			}
			else {
				if(!isset($obj->use_editor)) {
					$obj->use_editor = 'N';
				}
				if(!isset($obj->use_html)) {
					$obj->use_html = 'N';
				}
			}
// var_dump($this->module_info);
// var_dump($obj);
// exit;
			// check if the post is existed
			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($obj->parent_post_id);
			if(!$o_post->is_exists()) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_not_found', 'x2board'));
				return;
			}
			unset($o_post_model);
			
			// For anonymous use, remove writer's information and notifying information
			if($this->module_info->use_anonymous == 'Y') {
				$this->module_info->admin_mail = '';
				// $obj->notify_message = 'N';
				$obj->comment_author = -1*$o_logged_info->ID;
				$obj->email_address = ''; // $obj->homepage = $obj->user_id = '';
				// $obj->user_name = $obj->nick_name = 'anonymous';
				$obj->nick_name = 'anonymous';
				$bAnonymous = true;
			}
			else {
				$bAnonymous = false;
			}

			// generate comment  module model object
			$o_comment_model = \X2board\Includes\getModel('comment');

			// generate comment module controller object
			$o_comment_controller = \X2board\Includes\getController('comment');

			// check the comment is existed
			// if the comment is not existed, then generate a new sequence
			if(!$obj->comment_id) {
				$obj->comment_id = \X2board\Includes\getNextSequence();
				$o_comment = new \stdClass();
				$o_comment->comment_id = -1;  // means non-existing comment
			} else {
				$o_comment = $o_comment_model->get_comment($obj->comment_id, $this->grant->manager);	
			}
			
			// if comment_id is not existed, then insert the comment
			if( $o_comment->comment_id != $obj->comment_id ) {
				if( $obj->parent_comment_id ) {  // parent_comment_id is existed
					$o_parent_comment = $o_comment_model->get_comment($obj->parent_comment_id);
					if(!$o_parent_comment->comment_id) {		
						$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
						return;
					}
					$output = $o_comment_controller->insert_comment($obj, $bAnonymous);
				} 
				else {  // parent_comment_id is not existed
					$output = $o_comment_controller->insert_comment($obj, $bAnonymous);
				}
			} 
			else {  // update the comment if it is not existed
				if(!$o_comment->is_granted()) {  // check the grant
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_not_permitted', 'x2board'));
					return;
				}
				$output = $o_comment_controller->update_comment($obj, $this->grant->manager);
			}

			if(!$output->toBool()) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.$output->getMessage());
				return;
			}

			// if(Context::get('xeVirtualRequestMethod') !== 'xml')
			// {
			// 	$this->setMessage('success_registed');
			// }
			// $this->add('mid', Context::get('mid'));
			// $this->add('post_id', $obj->post_id);
			// $this->add('comment_id', $obj->comment_id);
			
			// if s_wp_redirect_url is not added, automatically redirect to home_url
			$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?'.X2B_CMD_VIEW_POST.'/'.$obj->parent_post_id.'#comment_id-'.$obj->comment_id);
		}

		/**
		 * @brief delete the post
		 **/
		// function procBoardDeleteDocument()
		private function _proc_delete_post() {
			// get the post_id
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');

			// if the post_id is not existed
			if(!$n_post_id) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_post', 'x2board'));
				return;
			}

			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($n_post_id);
			unset($o_post_model);
			// check protect content
			if($this->module_info->protect_content=="Y" && $o_post->get('comment_count')>0 && $this->grant->manager==false) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_protected_content', 'x2board'));
				return;
			}

			// generate post module controller object
			$o_post_controller = \X2board\Includes\getController('post');

			// delete the post
			$output = $o_post_controller->delete_post($n_post_id, $this->grant->manager);
			unset($o_post_controller);
			if(!$output->toBool()) {
				unset($output);
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.$output->getMessage());
				return;
			}
			unset($output);

			// alert an message
			// if s_wp_redirect_url is not added, automatically redirect to home_url
			$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?'.X2B_CMD_VIEW_POST.'/p/'.\X2board\Includes\Classes\Context::get('page'));
			// $this->setRedirectUrl(getNotEncodedUrl('', 'mid', Context::get('mid'), 'act', '', 'page', \X2board\Includes\Classes\Context::get('page'), 'document_srl', ''));
			// $this->add('mid', Context::get('mid'));
			// $this->add('page', Context::get('page'));
			// if(Context::get('xeVirtualRequestMethod') !== 'xml')
			// {
			// 	$this->setMessage('success_deleted');
			// }
		}

		/**
		 * @brief delete the comment
		 **/
		// function procBoardDeleteComment()
		private function _proc_delete_comment() {
			// get the comment_id
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');
			if(!$n_comment_id) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
				return;
			}
// var_dump($n_comment_id);
// exit;
			// generate comment controller object
			$o_comment_controller = \X2board\Includes\getController('comment');
			$output = $o_comment_controller->delete_comment($n_comment_id, $this->grant->manager);
			
			unset($o_comment_controller);
			if(!$output->toBool()) {
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.$output->getMessage());
				return;
			}

			// $this->add('mid', \X2board\Includes\Classes\Context::get('mid'));
			// $this->add('page', \X2board\Includes\Classes\Context::get('page'));
			// $this->add('post_id', $output->get('post_id'));
			$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?'.X2B_CMD_VIEW_POST.'/'.$output->get('post_id'));
			// if(Context::get('xeVirtualRequestMethod') !== 'xml')
			// {
			// 	$this->setMessage('success_deleted');
			// }
		}

		/**
		 * @brief check the password for post and comment
		 **/
		// function procBoardVerificationPassword()
		private function _proc_verify_password() {
			// get the id number of the post and the comment
			$s_password = \X2board\Includes\Classes\Context::get('password');
			$n_post_id = \X2board\Includes\Classes\Context::get('post_id');
			$n_comment_id = \X2board\Includes\Classes\Context::get('comment_id');
			$o_member_model = \X2board\Includes\getModel('member');

			if($n_comment_id) {  // if the comment exists
				// get the comment information
				$o_comment_model = \X2board\Includes\getModel('comment');
				$o_comment = $o_comment_model->get_comment($n_comment_id);
				unset($o_comment_model);
				if(!$o_comment->is_exists()) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
					return;
				}

				// compare the comment password and the user input password
				if(!$o_member_model->validate_password($o_comment->get('password'), $s_password)) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_password', 'x2board'));
					return;
				}
				$o_comment->set_grant();
				unset($o_comment);
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MODIFY_COMMENT.'&post_id='.$n_post_id.'&comment_id='.$n_comment_id);
			} else {  // get the post information
				$o_post_model = \X2board\Includes\getModel('post');
				$o_post = $o_post_model->get_post($n_post_id);
				unset($o_post_model);
				if(!$o_post->is_exists()) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_request', 'x2board'));
					return;
				}

				// compare the post password and the user input password
				if(!$o_member_model->validate_password($o_post->get('password'), $s_password)) {
					$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?cmd='.X2B_CMD_VIEW_MESSAGE.'&message='.__('msg_invalid_password', 'x2board'));
					return;
				}
				$o_post->set_grant();
				unset($o_post);
				$this->add('s_wp_redirect_url', $this->_s_wp_post_guid.'?'.X2B_CMD_VIEW_MODIFY_POST.'/'.$n_post_id);
			}
			unset($o_member_model);	
		}

////////////////////////////////
		/**
		 * @brief vote
		 **/
		function procBoardVoteDocument()
		{
			// generate document module controller object
			$oDocumentController = getController('document');

			$document_srl = Context::get('document_srl');
			return $oDocumentController->updateVotedCount($document_srl);
		}
	}
}