<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  boardController
 * @author XEHub (developers@xpressengine.com)
 * @brief  board module Controller class
 **/
namespace X2board\Includes\Modules\Board;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Board\\boardController' ) ) {

	class boardController extends board {

		private $_s_page_permlink = null;

		/**
		 * @brief initialization
		 **/
		function init() {
			// begin - define redirect url root
			$n_board_id = \X2board\Includes\Classes\Context::get( 'board_id' );
			$o_post     = get_post( intval( $n_board_id ) );
			if ( is_null( $o_post ) ) {
				wp_die( __( 'msg_error_board_controller_init', X2B_DOMAIN ) );
			}
            $this->_s_page_permlink = site_url() . '/' . urlencode( urldecode( $o_post->post_name ) );
			unset( $o_post );
			// end - define redirect url root

			$s_cmd = \X2board\Includes\Classes\Context::get( 'cmd' );
			switch ( $s_cmd ) {
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
				case X2B_CMD_PROC_AJAX_POST_ADD_CART:
				case X2B_CMD_PROC_AJAX_MANAGE_POST:
					$s_cmd = '_' . $s_cmd;
					$this->$s_cmd();
					break;
				default:
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
					return;
			}
		}

		/**
		 * @brief check download file
		 **/
		private function _proc_output_file() {
			$o_file_controller = \X2board\Includes\get_controller( 'file' );
			$o_file_controller->init(); // to init related $_SESSION
			$o_file_controller->proc_file_output();
			unset( $o_file_controller );
		}

		/**
		 * @brief check download file
		 **/
		private function _proc_download_file() {
			$o_file_controller = \X2board\Includes\get_controller( 'file' );
			$o_file_controller->init(); // to init related $_SESSION
			$o_appending_file_conf = new \stdClass();
			foreach ( $this->module_info as $s_key => $val ) {
				if ( substr( $s_key, 0, 5 ) === 'file_' ) {
					$o_appending_file_conf->$s_key = $val;
				}
			}
			\X2board\Includes\Classes\Context::set( 'appending_file_config', $o_appending_file_conf );
			$o_file_controller->proc_file_download();
			unset( $o_file_controller );
		}

		/**
		 * @brief upload file ajax
		 **/
		private function _proc_ajax_file_upload() {
			check_ajax_referer( X2B_AJAX_SECURITY, 'security' );
			$o_file_controller = \X2board\Includes\get_controller( 'file' );
			$o_file_controller->init(); // to init related $_SESSION
			$upload_attach_files = $o_file_controller->proc_file_upload();
			unset( $o_file_controller );
			$s_bool = $upload_attach_files[0]['is_success'] ? 'success' : 'fail';
			unset( $upload_attach_files[0]['is_success'] );
			wp_send_json(
				array(
					'result' => $s_bool,
					'files'  => $upload_attach_files,
				)
			);
		}

		/**
		 * @brief upload file ajax
		 **/
		private function _proc_ajax_file_delete() {
			check_ajax_referer( X2B_AJAX_SECURITY, 'security' );
			$o_file_controller = \X2board\Includes\get_controller( 'file' );
			$o_file_controller->init(); // to init related $_SESSION
			$o_rst = $o_file_controller->proc_file_delete();
			unset( $o_file_controller );
			if ( ! $o_rst->to_bool() ) {
				wp_send_json(
					array(
						'result'  => 'error',
						'message' => __( 'msg_invalid_request', X2B_DOMAIN ),
					)
				);
			}
			wp_send_json( array( 'result' => 'success' ) );
		}

		/**
		 * @brief request cart post via ajax
		 **/
		private function _proc_ajax_post_add_cart() {
			check_ajax_referer( X2B_AJAX_SECURITY, 'security' );
			$o_post_controller = \X2board\Includes\get_controller( 'post' );
			$o_post_controller->add_cart_post_ajax();
			unset( $o_post_controller );
			wp_send_json( array( 'result' => 'success' ) );
		}

		/**
		 * @brief cart post ajax
		 **/
		private function _proc_ajax_manage_post() {
			check_ajax_referer( X2B_AJAX_SECURITY, 'security' );
			$s_mode = \X2board\Includes\Classes\Context::get( 'mode' );
			$n_board_id = intval( \X2board\Includes\Classes\Context::get( 'board_id' ) );
			if( $s_mode == 'get_category_by_board_id' ) {
				$o_category_model = \X2board\Includes\get_model( 'category' );
				$o_category_model->set_board_id( $n_board_id );
				$a_linear_category = $o_category_model->build_linear_category();
				unset( $o_category_model );
				$a_buff = array();
				if( count( $a_linear_category ) ) {
					$a_buff[] = '<option value="">' . __( 'lbl_select_category', X2B_DOMAIN ) . '</option>';
					foreach ( $a_linear_category as $cat_id => $option_val ) {
						$a_buff[] = '<option value="' . $cat_id . '">' . str_repeat( '&nbsp;&nbsp;', $option_val->depth ) . $option_val->title . '</option>';
					}
				}
				unset( $a_linear_category );
				wp_send_json(
					array(
						'result'   => 'success',
						'category' => $a_buff,
					)
				);
				unset( $a_buff );
			}
			else {
				$o_post_controller = \X2board\Includes\get_controller( 'post' );
				$o_post_controller->manage_carted_post_ajax();
				unset( $o_post_controller );
				wp_send_json(
					array(
						'result'   => 'success',
					)
				);
			}
		}

		/**
		 * @brief update post
		 **/
		private function _proc_modify_post() {
			$this->_proc_write_post();
		}

		/**
		 * @brief insert post
		 * procBoardInsertDocument()
		 **/
		private function _proc_write_post() {
			// check grant
			if ( ! $this->grant->write_post ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
				return;
			}

			// setup variables
			$obj = \X2board\Includes\Classes\Context::gets(
				'board_id',
				'post_id',
				'title',
				' title_bold',
				'is_notice',
				'content',
				'nick_name',
				'category_id',
				'password',
				'use_editor',
				'allow_comment',
				'status',  // for XE board skin compatible
				// 'is_secret',
			);
			if ( is_null( $obj->board_id ) || intval( $obj->board_id ) <= 0 ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
				return;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );

			$obj->post_author = $o_logged_info->ID;

			$o_comment_class = \X2board\Includes\get_class( 'comment' );
			if ( $obj->allow_comment == 'N' ) {
				$obj->comment_status = $o_comment_class->get_status_by_key( 'deny' ); // 'DENY';
			} else {
				$obj->comment_status = $o_comment_class->get_status_by_key( 'allow' ); // 'ALLOW';
			}
			unset( $obj->allow_comment );
			unset( $o_comment_class );

			if ( $obj->is_notice != 'Y' || ! $this->grant->manager ) {
				$obj->is_notice = 'N';
			}
			if ( $this->module_info->mobile_use_editor === 'Y' ) {
				if ( ! isset( $obj->use_editor ) ) {
					$obj->use_editor = 'Y';
				}
				if ( ! isset( $obj->use_html ) ) {
					$obj->use_html = 'Y';
				}
			} else {
				if ( ! isset( $obj->use_editor ) ) {
					$obj->use_editor = 'N';
				}
				if ( ! isset( $obj->use_html ) ) {
					$obj->use_html = 'N';
				}
			}

			settype( $obj->title, 'string' );
			if ( $obj->title == '' ) {
				$obj->title = \X2board\Includes\cut_str( trim( strip_tags( nl2br( $obj->content ) ) ), (int) $this->module_info->excerpted_title_length, '...' );
			}
			// setup post title to 'Untitled'
			if ( $obj->title == '' ) {
				$obj->title = __( 'lbl_untitled', X2B_DOMAIN );
			}

			// unset post style if the user is not the post manager
			if ( ! $this->grant->manager ) {
				unset( $obj->title_color );
				unset( $obj->title_bold );
			}

			// generate post module model object
			$o_post_model = \X2board\Includes\get_model( 'post' );
			// check if the post is existed
			$o_post = $o_post_model->get_post( $obj->post_id, $this->grant->manager );
			unset( $o_post_model );

			// update the post if it is existed
			$is_update = false;
			if ( $o_post->is_exists() && $o_post->post_id == $obj->post_id ) {
				$is_update = true;
			}

			// if use anonymous is true
			if ( $this->module_info->use_anonymous == 'Y' ) {
				$this->module_info->admin_mail = '';
				// $obj->notify_message           = 'N';
				if ( $is_update === false ) {
					$obj->post_author = 0;// -1*$o_logged_info->ID;
				}
				$obj->email_address = '';
				$obj->nick_name     = __( 'lbl_anonymous', X2B_DOMAIN );
				$bAnonymous         = true;
				if ( $is_update === false ) {
					$o_post->add( 'post_author', $obj->post_author );
				}
			} else {
				$bAnonymous = false;
			}
			unset( $o_logged_info );

			$o_post_model    = \X2board\Includes\get_model( 'post' );
			$s_secret_status = $o_post_model->get_config_status( 'secret' );
			$s_public_status = $o_post_model->get_config_status( 'public' );
			unset( $o_post_model );

			if ( strtoupper( $obj->status ) == $s_secret_status ) {
				$use_status = $this->module_info->use_status;
				if ( ! is_array( $use_status ) || ! in_array( $s_secret_status, $use_status ) ) {
					$obj->status = $s_public_status;
				}
			}
			if ( ! isset( $obj->status ) ) {
				$obj->status = $s_public_status;
			}

			// generate controller object of post module
			$o_post_controller = \X2board\Includes\get_controller( 'post' );
			// update the post if it is existed
			if ( $is_update ) {
				if ( ! $o_post->is_granted() ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
					return;
				}

				if ( $this->module_info->use_anonymous == 'Y' ) {
					$obj->post_author = abs( $o_post->get( 'post_author' ) ) * -1;
					$o_post->add( 'post_author', $obj->post_author );
				}

				if ( $this->module_info->protect_content == 'Y' && $o_post->get( 'comment_count' ) > 0 && $this->grant->manager == false ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_protected_content' );
					return;
				}

				if ( ! $this->grant->manager ) { // notice & post style same as before if not manager
					$obj->is_notice   = $o_post->get( 'is_notice' );
					$obj->title_color = $o_post->get( 'title_color' );
					$obj->title_bold  = $o_post->get( 'title_bold' );
				}
				$output   = $o_post_controller->update_post( $o_post, $obj, true );
				$msg_code = 'success_updated';
			} else {  // insert a new post otherwise
				$output   = $o_post_controller->insert_post( $obj );
				$msg_code = 'success_registed';
				// send a notification via slack when a guest write a new post
				if( ! current_user_can( 'manage_' . X2B_DOMAIN ) ) {
					// if( class_exists('\Slack_Notifications\Notifications\Notification_Type') ) {
					if ( is_plugin_active( 'dorzki-notifications-to-slack/slack-notifications.php' ) ) {  // plugin is activated
						if( isset( $this->module_info->notify_slack['post'] ) && $this->module_info->notify_slack['post'] == 'post' ) {
							$obj->notify_type = 'post';
							$obj->post_link = $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/' . $output->get( 'post_id' );
							do_action( X2B_DOMAIN . 'notify_new', $obj );
						}
					}
				}
			}
			unset( $o_post );
			unset( $obj );
			unset( $o_post_controller );

			if ( ! $output->to_bool() ) {  // if there is an error
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=' . $output->getMessage() );
			} else { // if s_wp_redirect_url is not added, automatically redirect to home_url
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/' . $output->get( 'post_id' ) );
			}
			unset( $output );
		}

		/**
		 * @brief insert comment
		 * procBoardInsertComment()
		 **/
		private function _proc_write_comment() {
			// check grant
			if ( ! $this->grant->write_comment ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
				return;
			}
			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );

			// get the relevant data for inserting comment
			$obj = \X2board\Includes\Classes\Context::gets(
				'board_id',
				'parent_post_id',
				'content',
				'password',
				'nick_name',
				'parent_comment_id',
				'comment_id',
				'editor_sequence',
				'is_secret',
				'use_editor',
				'use_html'
			);

			if ( in_array( 'SECRET', $this->module_info->use_status ) ) {
				$this->module_info->secret = 'Y';
			} else {
				unset( $obj->is_secret );
				$this->module_info->secret = 'N';
			}

			if ( $this->module_info->mobile_use_editor === 'Y' ) {
				if ( ! isset( $obj->use_editor ) ) {
					$obj->use_editor = 'Y';
				}
				if ( ! isset( $obj->use_html ) ) {
					$obj->use_html = 'Y';
				}
			} else {
				if ( ! isset( $obj->use_editor ) ) {
					$obj->use_editor = 'N';
				}
				if ( ! isset( $obj->use_html ) ) {
					$obj->use_html = 'N';
				}
			}

			// check if the post is existed
			$o_post_model = \X2board\Includes\get_model( 'post' );
			$o_post       = $o_post_model->get_post( $obj->parent_post_id );
			if ( ! $o_post->is_exists() ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_found' );
				return;
			}
			unset( $o_post_model );

			// check if new comment is allowed
			if ( ! $o_post->is_enable_comment() ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_comment_not_allowed' );
				return;
			}

			// For anonymous use, remove writer's information and notifying information
			if ( $this->module_info->use_anonymous == 'Y' ) {
				$this->module_info->admin_mail = '';
				$obj->comment_author           = 0; // -1*$o_logged_info->ID;
				$obj->email_address            = '';
				$obj->nick_name                = 'anonymous';
				$bAnonymous                    = true;
			} else {
				$bAnonymous = false;
			}

			// generate comment  module model object
			$o_comment_model = \X2board\Includes\get_model( 'comment' );

			// generate comment module controller object
			$o_comment_controller = \X2board\Includes\get_controller( 'comment' );

			// check the comment is existed
			// if the comment is not existed, then generate a new sequence
			if ( ! $obj->comment_id ) {
				$obj->comment_id = null;
				if ( $obj->editor_sequence ) {
					$obj->comment_id = $obj->editor_sequence;
					if ( $_SESSION['x2b_upload_info'][ $obj->editor_sequence ]->enabled ) { // this is from \includes\modules\file\file.controller.php::proc_file_upload();
						$obj->comment_id = $_SESSION['x2b_upload_info'][ $obj->editor_sequence ]->upload_target_id;
					}
				}
				$o_comment             = new \stdClass();
				$o_comment->comment_id = -1;  // means non-existing comment
			} else {
				$o_comment = $o_comment_model->get_comment( $obj->comment_id, $this->grant->manager );
			}

			// if comment_id is not existed, then insert the comment
			if ( $o_comment->comment_id != $obj->comment_id ) {
				if ( $obj->parent_comment_id ) {  // parent_comment_id is existed
					$o_parent_comment = $o_comment_model->get_comment( $obj->parent_comment_id );
					if ( ! $o_parent_comment->comment_id ) {
						$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
						return;
					}
					$output = $o_comment_controller->insert_comment( $obj, $bAnonymous );
				} else {  // parent_comment_id is not existed
					$output = $o_comment_controller->insert_comment( $obj, $bAnonymous );
				}

				// send a notification via slack when a guest write a new comment
				if( ! current_user_can( 'manage_' . X2B_DOMAIN ) ) {
					// if( class_exists('\Slack_Notifications\Notifications\Notification_Type') ) {
					if ( is_plugin_active( 'dorzki-notifications-to-slack/slack-notifications.php' ) ) {  // plugin is activated
						if( isset( $this->module_info->notify_slack['comment'] ) && $this->module_info->notify_slack['comment'] == 'comment' ) {
							$obj->title = $o_post->get_title_text();
							$obj->notify_type = 'comment';
							$obj->post_link = $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/' . $obj->parent_post_id . '#comment_id-' . $obj->comment_id ;
							do_action( X2B_DOMAIN . 'notify_new', $obj );
						}
					}
				}
			} else {  // update the comment if it is not existed
				if ( ! $o_comment->is_granted() ) {  // check the grant
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
					return;
				}
				$output = $o_comment_controller->update_comment( $obj, $this->grant->manager );
			}

			if ( ! $output->to_bool() ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=' . $output->getMessage() );
				return;
			}

			// if s_wp_redirect_url is not added, automatically redirect to home_url
			$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/' . $obj->parent_post_id . '#comment_id-' . $obj->comment_id );
		}

		/**
		 * @brief delete the post
		 * procBoardDeleteDocument()
		 **/
		private function _proc_delete_post() {
			// get the post_id
			$n_post_id = \X2board\Includes\Classes\Context::get( 'post_id' );

			// if the post_id is not existed
			if ( ! $n_post_id ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_post' );
				return;
			}

			$o_post_model = \X2board\Includes\get_model( 'post' );
			$o_post       = $o_post_model->get_post( $n_post_id );
			unset( $o_post_model );
			// check protect content
			if ( $this->module_info->protect_content == 'Y' && $o_post->get( 'comment_count' ) > 0 && $this->grant->manager == false ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_protected_content' );
				return;
			}

			// generate post module controller object
			$o_post_controller = \X2board\Includes\get_controller( 'post' );

			// delete the post
			$output = $o_post_controller->delete_post( $n_post_id, $this->grant->manager );
			unset( $o_post_controller );
			if ( ! $output->to_bool() ) {
				unset( $output );
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=' . $output->getMessage() );
				return;
			}
			unset( $output );

			// if s_wp_redirect_url is not added, automatically redirect to home_url
			$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/p/' . \X2board\Includes\Classes\Context::get( 'page' ) );
		}

		/**
		 * @brief delete the comment
		 * procBoardDeleteComment()
		 **/
		private function _proc_delete_comment() {
			// get the comment_id
			$n_comment_id = \X2board\Includes\Classes\Context::get( 'comment_id' );
			if ( ! $n_comment_id ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
				return;
			}
			// generate comment controller object
			$o_comment_controller = \X2board\Includes\get_controller( 'comment' );
			$output               = $o_comment_controller->delete_comment( $n_comment_id, $this->grant->manager );
			unset( $o_comment_controller );
			if ( ! $output->to_bool() ) {
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=' . $output->getMessage() );
				return;
			}

			$n_parent_post_id = $output->get( 'parent_post_id' );
			unset( $output );
			// if s_wp_redirect_url is not added, automatically redirect to home_url
			$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?' . X2B_CMD_VIEW_POST . '/' . $n_parent_post_id );
		}

		/**
		 * @brief check the password for post and comment
		 * procBoardVerificationPassword()
		 **/
		private function _proc_verify_password() {
			// get the id number of the post and the comment
			$s_password     = \X2board\Includes\Classes\Context::get( 'password' );
			$n_post_id      = \X2board\Includes\Classes\Context::get( 'post_id' );
			$n_comment_id   = \X2board\Includes\Classes\Context::get( 'comment_id' );
			$o_member_model = \X2board\Includes\get_model( 'member' );

			if ( $n_comment_id ) {  // if the comment exists
				// get the comment information
				$o_comment_model = \X2board\Includes\get_model( 'comment' );
				$o_comment       = $o_comment_model->get_comment( $n_comment_id );
				unset( $o_comment_model );
				if ( ! $o_comment->is_exists() ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
					return;
				}

				// compare the comment password and the user input password
				if ( ! $o_member_model->validate_password( $o_comment->get( 'password' ), $s_password ) ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
					return;
				}
				$o_comment->set_grant();
				unset( $o_comment );
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MODIFY_COMMENT . '&post_id=' . $n_post_id . '&comment_id=' . $n_comment_id );
			} else {  // get the post information
				$o_post_model = \X2board\Includes\get_model( 'post' );
				$o_post       = $o_post_model->get_post( $n_post_id );
				unset( $o_post_model );
				if ( ! $o_post->is_exists() ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_invalid_request' );
					return;
				}

				// compare the post password and the user input password
				if ( ! $o_member_model->validate_password( $o_post->get( 'password' ), $s_password ) ) {
					$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?cmd=' . X2B_CMD_VIEW_MESSAGE . '&message=msg_not_permitted' );
					return;
				}
				$o_post->set_grant();
				unset( $o_post );
				$this->add( 's_wp_redirect_url', $this->_s_page_permlink . '?' . X2B_CMD_VIEW_MODIFY_POST . '/' . $n_post_id );
			}
			unset( $o_member_model );
		}
	}
}
