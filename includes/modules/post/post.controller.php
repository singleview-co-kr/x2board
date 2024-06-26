<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postController class
 * post the module's controller class
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 */
namespace X2board\Includes\Modules\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Post\\postController' ) ) {

	class postController extends post {

		private $_o_wp_filesystem = null;

		function __construct() {
			if ( ! isset( $_SESSION['x2b_banned_post'] ) ) {
				$_SESSION['x2b_banned_post'] = array();
			}
			if ( ! isset( $_SESSION['x2b_readed_post'] ) ) {
				$_SESSION['x2b_readed_post'] = array();
			}
			if ( ! isset( $_SESSION['x2b_own_post'] ) ) {
				$_SESSION['x2b_own_post'] = array();
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			$this->_o_wp_filesystem = new \WP_Filesystem_Direct( false );
		}

		/**
		 * Initialization
		 *
		 * @return void
		 */
		// function init() {}

		/**
		 * Insert new post
		 * insertDocument($obj, $manual_inserted = false, $isRestore = false, $isLatest = true)
		 *
		 * @param object $obj
		 * @param bool   $manual_inserted
		 * @param bool   $isRestore
		 * @return object
		 */
		public function insert_post( $obj, $manual_inserted = false ) {
			if ( ! $manual_inserted ) {  // check WP nonce if a guest inserts a new post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get( 'x2b_' . X2B_CMD_PROC_WRITE_POST . '_nonce' );
				if ( is_null( $wp_verify_nonce ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '1' );
				}
				if ( ! wp_verify_nonce( $wp_verify_nonce, 'x2b_' . X2B_CMD_PROC_WRITE_POST ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '2' );
				}
			}

			if ( ! isset( $obj->email_address ) ) {
				$obj->email_address = '';
			}

			// can modify regdate only manager
			$o_grant = \X2board\Includes\Classes\Context::get( 'grant' );
			if ( ! $o_grant->manager ) {
				unset( $obj->regdate_dt );
			}
			unset( $o_grant );

			// Register it if no given post_id exists
			if ( ! $obj->post_id ) {
				$obj->post_id = \X2board\Includes\getNextSequence();
			} elseif ( ! $manual_inserted && ! \X2board\Includes\checkUserSequence( $obj->post_id ) ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_not_permitted', X2B_DOMAIN ) );
			}

			// Set to 0 if the category_id doesn't exist
			if ( $obj->category_id ) {
				$o_category_model = \X2board\Includes\getModel( 'category' );
				$o_category_model->set_board_id( \X2board\Includes\Classes\Context::get( 'board_id' ) );
				$a_linear_category = $o_category_model->build_linear_category();
				unset( $o_category_model );
				if ( count( $a_linear_category ) > 0 && ! $a_linear_category[ $obj->category_id ]->grant ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_not_permitted', X2B_DOMAIN ) );
				}
				if ( count( $a_linear_category ) > 0 && ! $a_linear_category[ $obj->category_id ] ) {
					$obj->category_id = 0;
				}
				unset( $a_linear_category );
			}
			// Set the read counts and update order.
			// if(!$obj->readed_count) {
			// $obj->readed_count = 0;
			// }
			// if($isLatest) {
				$obj->update_order = $obj->list_order = $obj->post_id * -1;
			// }
			// else {
				// $obj->update_order = $obj->list_order;
			// }

			if ( ! isset( $obj->password_is_hashed ) ) {
				$obj->password_is_hashed = false;
			}
			// Check the status of password hash for manually inserting. Apply hashing for otherwise.
			if ( $obj->password && ! $obj->password_is_hashed ) {
				$obj->password = \X2board\Includes\getModel( 'member' )->hash_password( $obj->password );
			}
			// Insert member's information only if the member is logged-in and not manually registered.
			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( \X2board\Includes\Classes\Context::get( 'is_logged' ) && ! $manual_inserted ) {
				$obj->post_author = $o_logged_info->ID;

				// nick_name already encoded
				$obj->nick_name     = htmlspecialchars_decode( $o_logged_info->display_name );
				$obj->email_address = $o_logged_info->email_address;
			}
			// If the tile is empty, extract string from the contents.
			$obj->title = htmlspecialchars( $obj->title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
			settype( $obj->title, 'string' );
			if ( $obj->title == '' ) {
				$obj->title = cut_str( trim( strip_tags( nl2br( $obj->content ) ) ), 20, '...' );
			}
			// If no tile extracted from the contents, leave it untitled.
			if ( $obj->title == '' ) {
				$obj->title = __( 'lbl_untitled', X2B_DOMAIN );
			}
			// Remove XE's own tags from the contents.
			// $obj->content = preg_replace('!<\!--(Before|After)(Document|Comment)\(([0-9]+),([0-9]+)\)-->!is', '', $obj->content);
			if ( ! $manual_inserted ) {
				if ( $obj->use_editor != 'Y' ) {  // if(wp_is_mobile() && $obj->use_editor != 'Y') {
					if ( $obj->use_html != 'Y' ) {
						$obj->content = htmlspecialchars( $obj->content, ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
					}
					$obj->content = nl2br( $obj->content );
				}
			}
			// Remove iframe and script if not a top adminisrator in the session.
			if ( $o_logged_info->is_admin != 'Y' ) {
				$obj->content = \X2board\Includes\removeHackTag( $obj->content );
			}
			// An error appears if both log-in info and user name don't exist.
			if ( ! $o_logged_info->ID && ! $obj->nick_name ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '3' );
			}
			unset( $o_logged_info );

			// 카테고리 지정 권한없는 사람이 새글 작성했는데 강제 지정 카테고리가 설정되었다면
			// if( $pass_enforce_default_category ) {
			// $mandatory_cat_id = $category->getDefaultCategory();
			// if( $mandatory_cat_id ){
			// $this->category_id = $mandatory_cat_id;
			// }
			// }

			// sanitize
			$a_new_post                   = array();
			$a_new_post['board_id']       = \X2board\Includes\Classes\Context::get( 'board_id' );
			$a_new_post['post_id']        = intval( $obj->post_id );
			$a_new_post['password']       = $obj->password;
			$a_new_post['post_author']    = intval( $obj->post_author );
			$a_new_post['nick_name']      = sanitize_text_field( $obj->nick_name );
			$a_new_post['title']          = sanitize_text_field( $obj->title );
			$a_new_post['content']        = $obj->content;
			$a_new_post['readed_count']   = 0;
			$a_new_post['comment_count']  = 0;
			$a_new_post['voted_count']    = 0;
			$a_new_post['category_id']    = intval( $obj->category_id );
			$a_new_post['is_notice']      = isset( $obj->is_notice ) ? sanitize_text_field( $obj->is_notice ) : 'N';
			$a_new_post['update_order']   = intval( $obj->update_order );
			$a_new_post['list_order']     = intval( $obj->list_order );
			$a_new_post['status']         = sanitize_text_field( $obj->status );
			$a_new_post['comment_status'] = sanitize_text_field( $obj->comment_status );

			$s_cur_datetime = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
			if ( $manual_inserted ) {  // $obj->regdate_dt is set if import
				$a_new_post['regdate_dt']     = isset( $obj->regdate_dt ) ? $obj->regdate_dt : $s_cur_datetime;
				$a_new_post['last_update_dt'] = isset( $obj->last_update_dt ) ? $obj->last_update_dt : $s_cur_datetime;
				$a_new_post['ua']             = $obj->ua;
				$a_new_post['ipaddress']      = $obj->ipaddress;
			} else {
				$a_new_post['last_update_dt'] = $a_new_post['regdate_dt'] = $s_cur_datetime;
				// add user agent
				$a_new_post['ua']        = wp_is_mobile() ? 'M' : 'P';
				$a_new_post['ipaddress'] = \X2board\Includes\get_remote_ip();
			}

			$a_insert_key = array();
			$a_insert_val = array();
			foreach ( $a_new_post as $key => $value ) {
				// $this->{$key} = $value;
				$value          = esc_sql( $value );
				$a_insert_key[] = "`$key`";
				$a_insert_val[] = "'$value'";
			}

			global $wpdb;
			$query = "INSERT LOW_PRIORITY INTO `{$wpdb->prefix}x2b_posts` (" . implode( ',', $a_insert_key ) . ') VALUES (' . implode( ',', $a_insert_val ) . ')';
			if ( $wpdb->query( $query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}

			unset( $a_insert_key );
			unset( $a_insert_data );

			// Insert all extended user defined variables if the post successfully inserted.
			$o_post_model                  = \X2board\Includes\getModel( 'post' );
			$a_user_define_extended_fields = $o_post_model->get_user_define_extended_fields( $a_new_post['board_id'] );
			unset( $o_post_model );

			// do not store default field into tbl::x2b_user_define_vars
			if ( count( $a_user_define_extended_fields ) ) {
				foreach ( $a_user_define_extended_fields as $idx => $o_user_define_item ) {
					$o_user_input_value = \X2board\Includes\Classes\Context::get( $o_user_define_item->eid );
					if ( $o_user_input_value == null ) {
						continue;
					}
					$this->_insert_user_defined_value( $a_new_post['board_id'], $a_new_post['post_id'], $idx, $o_user_input_value, $o_user_define_item->eid );
				}
			}

			// Update the category if the category_id exists.
			if ( $obj->category_id ) {
				$o_category_controller = \X2board\Includes\getController( 'category' );
				$o_category_controller->set_board_id( $obj->board_id );
				$o_category_controller->update_category_count( $obj->category_id );
				unset( $o_category_controller );
			}

			if ( ! $manual_inserted ) {
				$this->_add_grant( $a_new_post['post_id'] );
			}

			$o_file_controller = \X2board\Includes\getController( 'file' );
			$o_file_controller->set_files_valid( $a_new_post['post_id'] );
			unset( $o_file_controller );
			$this->update_uploaded_count( array( $a_new_post['post_id'] ) );

			if ( $this->_insert_wp_post( $a_new_post ) === false ) {
				unset( $a_new_post );
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_wp_post_registration_failed', X2B_DOMAIN ) );
			}

			$o_rst = new \X2board\Includes\Classes\BaseObject();
			$o_rst->add( 'post_id', $a_new_post['post_id'] );
			$o_rst->add( 'category_id', $obj->category_id );
			unset( $a_new_post );
			return $o_rst;
		}

		/**
		 * Insert extra vaiable to the documents table
		 * insertDocumentExtraVar($module_srl, $document_srl, $var_idx, $value, $eid = null, $lang_code = '')
		 *
		 * @param int    $n_board_id
		 * @param int    $n_post_id
		 * @param int    $var_idx
		 * @param mixed  $value
		 * @param int    $eid
		 * @param string $lang_code
		 * @return BaseObject|void
		 */
		private function _insert_user_defined_value( $n_board_id, $n_post_id, $var_idx, $o_user_input_value, $eid = null, $lang_code = '' ) {
			if ( ! $n_board_id || ! $n_post_id || ! $var_idx || ! isset( $o_user_input_value ) ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '1' );
			}

			if ( is_array( $o_user_input_value ) ) {
				$value = implode( '|@|', sanitize_text_field( $o_user_input_value ) );
			} else {
				$value = sanitize_text_field( trim( $o_user_input_value ) );
			}

			$a_new_field              = array();
			$a_new_field['board_id']  = $n_board_id;
			$a_new_field['post_id']   = $n_post_id;
			$a_new_field['var_idx']   = $var_idx;
			$a_new_field['value']     = $value;
			$a_new_field['lang_code'] = '';
			$a_new_field['eid']       = $eid;
			global $wpdb;
			$result = $wpdb->insert( "{$wpdb->prefix}x2b_user_define_vars", $a_new_field );
			if ( $result < 0 || $result === false ) {
				unset( $a_new_field );
				unset( $result );
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			unset( $result );
		}

		/**
		 * Update read counts of the post
		 *
		 * @param postItem $post
		 * @return bool|void
		 */
		public function update_readed_count( &$o_post ) {
			// Pass if Crawler access
			if ( \X2board\Includes\is_crawler() ) {
				return false;
			}

			$n_post_id = $o_post->post_id;
			// Pass if read count is increaded on the session information
			if ( isset( $_SESSION['x2b_readed_post'][ $n_post_id ] ) ) {
				return false;
			}

			// Pass if the author's IP address is as same as visitor's.
			if ( $o_post->get( 'ipaddress' ) == \X2board\Includes\get_remote_ip() ) {  // $_SERVER['REMOTE_ADDR']) {
				$_SESSION['x2b_readed_post'][ $n_post_id ] = true;
				return false;
			}
			// Pass ater registering sesscion if the author is a member and has same information as the currently logged-in user.
			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			$n_post_author = $o_post->get( 'post_author' );
			if ( $n_post_author && $o_logged_info->ID == $n_post_author ) {
				$_SESSION['x2b_readed_post'][ $n_post_id ] = true;
				return false;
			}
			unset( $o_logged_info );

			// Update read counts
			global $wpdb;
			$query = "UPDATE `{$wpdb->prefix}x2b_posts` SET `readed_count`=`readed_count`+1 WHERE `post_id`='" . esc_sql( intval( $n_post_id ) ) . "'";
			if ( $wpdb->query( $query ) === false ) {
				return false;
			}

			// Register session
			if ( ! isset( $_SESSION['x2b_banned_post'][ $n_post_id ] ) ) {
				$_SESSION['x2b_readed_post'][ $n_post_id ] = true;
			}
			return true;
		}

		/**
		 * Update the post
		 * updateDocument($source_obj, $obj, $manual_updated = FALSE)
		 *
		 * @param object $source_obj
		 * @param object $obj
		 * @param bool   $manual_updated
		 * @return object
		 */
		public function update_post( $o_old_post, $o_new_obj, $manual_updated = false ) {
			if ( ! $manual_updated ) {  // check WP nonce if a guest update a old post
				$wp_verify_nonce = \X2board\Includes\Classes\Context::get( 'x2b_' . X2B_CMD_PROC_MODIFY_POST . '_nonce' );
				if ( is_null( $wp_verify_nonce ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '1' );
				}
				if ( ! wp_verify_nonce( $wp_verify_nonce, 'x2b_' . X2B_CMD_PROC_MODIFY_POST ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) . '2' );
				}
			}

			if ( ! $o_old_post->post_id || ! $o_new_obj->post_id ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) );
			}
			if ( ! $o_new_obj->status && $o_new_obj->is_secret == 'Y' ) {
				$o_new_obj->status = 'SECRET';
			}
			if ( ! $o_new_obj->status ) {
				$o_new_obj->status = 'PUBLIC';
			}
			if ( isset( $o_new_obj->is_secret ) ) {  // is_secret is not a DB field
				unset( $o_new_obj->is_secret );
			}

			$document_config = null;
			if ( ! $document_config ) {
				$document_config = new \stdClass();
			}
			if ( ! isset( $document_config->use_history ) ) {
				$document_config->use_history = 'N';
			}
			$bUseHistory = $document_config->use_history == 'Y' || $document_config->use_history == 'Trace';

			if ( $bUseHistory ) {
				$args               = new \stdClass();
				$args->history_srl  = \X2board\Includes\getNextSequence();
				$args->document_srl = $o_new_obj->document_srl;
				$args->module_srl   = $module_srl;
				if ( $document_config->use_history == 'Y' ) {
					$args->content = $o_old_post->get( 'content' );
				}
				$args->nick_name  = $o_old_post->get( 'nick_name' );
				$args->member_srl = $o_old_post->get( 'member_srl' );
				$args->regdate_dt = $o_old_post->get( 'last_update_dt' );
				$args->ipaddress  = \X2board\Includes\get_remote_ip(); // $_SERVER['REMOTE_ADDR'];
				$output           = executeQuery( 'document.insertHistory', $args );
			} else {
				$o_new_obj->ipaddress = $o_old_post->get( 'ipaddress' );
			}

			// can modify regdate only manager
			$o_grant = \X2board\Includes\Classes\Context::get( 'grant' );
			if ( ! $o_grant->manager ) {
				unset( $o_new_obj->regdate_dt );
			}
			unset( $o_grant );

			// Remove the columns for automatic saving
			unset( $o_new_obj->_saved_doc_srl );
			unset( $o_new_obj->_saved_doc_title );
			unset( $o_new_obj->_saved_doc_content );
			unset( $o_new_obj->_saved_doc_message );

			$o_post_model = \X2board\Includes\getModel( 'post' );
			// Set the category_srl to 0 if the changed category is not exsiting.
			if ( intval( $o_old_post->get( 'category_id' ) ) != intval( $o_new_obj->category_id ) ) {
				$o_category_model = \X2board\Includes\getModel( 'category' );
				$o_category_model->set_board_id( \X2board\Includes\Classes\Context::get( 'board_id' ) );
				$a_linear_category = $o_category_model->build_linear_category();
				unset( $o_category_model );

				if ( ! $a_linear_category[ $o_new_obj->category_id ] || ! $a_linear_category[ $o_new_obj->category_id ]->grant ) {
					$o_new_obj->category_id = 0;
				}
				unset( $a_linear_category );
			}
			// Change the update order
			$o_new_obj->update_order = \X2board\Includes\getNextSequence() * -1;
			// Hash the password if it exists
			if ( $o_new_obj->password ) {
				$o_new_obj->password = \X2board\Includes\getModel( 'member' )->hash_password( $o_new_obj->password );
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			// If an author is identical to the modifier or history is used, use the logged-in user's information.
			if ( \X2board\Includes\Classes\Context::get( 'is_logged' ) && ! $manual_updated ) {
				if ( $o_old_post->get( 'post_author' ) == $o_logged_info->ID ) {
					$o_new_obj->post_author   = $o_logged_info->ID;
					$o_new_obj->nick_name     = htmlspecialchars_decode( $o_logged_info->nick_name );
					$o_new_obj->email_address = $o_logged_info->email_address;
				}
			}

			// For the post written by logged-in user however no nick_name exists
			if ( $o_old_post->get( 'post_author' ) && ! $o_new_obj->nick_name ) {
				$o_new_obj->post_author   = $o_old_post->get( 'post_author' );
				$o_new_obj->nick_name     = $o_old_post->get( 'nick_name' );
				$o_new_obj->email_address = $o_old_post->get( 'email_address' );
			}
			// If the tile is empty, extract string from the contents.
			$o_new_obj->title = htmlspecialchars( $o_new_obj->title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
			settype( $o_new_obj->title, 'string' );
			if ( $o_new_obj->title == '' ) {
				$o_new_obj->title = cut_str( strip_tags( $o_new_obj->content ), 20, '...' );
			}
			// If no tile extracted from the contents, leave it untitled.
			if ( $o_new_obj->title == '' ) {
				$o_new_obj->title = __( 'lbl_untitled', X2B_DOMAIN ); // 'Untitled';
			}
			// Remove XE's own tags from the contents.
			// $o_new_obj->content = preg_replace('!<\!--(Before|After)(Document|Comment)\(([0-9]+),([0-9]+)\)-->!is', '', $o_new_obj->content);
			if ( ! isset( $o_new_obj->use_editor ) ) {
				$o_new_obj->use_editor = 'N';
				$o_new_obj->use_html   = 'N';
			}
			if ( $o_new_obj->use_editor != 'Y' ) {  // if(wp_is_mobile() && $o_new_obj->use_editor != 'Y') {
				if ( $o_new_obj->use_html != 'Y' ) {
					$o_new_obj->content = htmlspecialchars( $o_new_obj->content, ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
				}
				$o_new_obj->content = nl2br( $o_new_obj->content );
			}
			// Remove iframe and script if not a top adminisrator in the session.
			if ( $o_logged_info->is_admin != 'Y' ) {
				$o_new_obj->content = \X2board\Includes\removeHackTag( $o_new_obj->content );
			}

			// sanitize other user input fields, $o_new_obj->content has been sanitized enough
			$a_new_post   = array();
			$a_ignore_key = array( 'use_editor', 'content', 'use_html' );
			foreach ( $o_new_obj as $s_key => $s_val ) {
				if ( ! in_array( $s_key, $a_ignore_key ) && isset( $s_val ) ) {
					$a_new_post[ $s_key ] = esc_sql( $s_val );
				}
			}
			$a_new_post['content'] = $o_new_obj->content;  // esc_sql() converts new line to \r\n repeatedly

			global $wpdb;
			$result = $wpdb->update( "{$wpdb->prefix}x2b_posts", $a_new_post, array( 'post_id' => esc_sql( intval( $a_new_post['post_id'] ) ) ) );
			if ( $result < 0 || $result === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}

			if ( $this->_update_wp_post( $a_new_post ) === false ) {
				unset( $a_new_post );
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_wp_post_update_failed', X2B_DOMAIN ) );
			}
			unset( $a_ignore_key );

			// Remove all extended user defined variables
			$this->_delete_extended_user_defined_vars_all( $a_new_post['board_id'], $a_new_post['post_id'] );

			// store all extended user defined variables
			$o_post_model                  = \X2board\Includes\getModel( 'post' );
			$a_user_define_extended_fields = $o_post_model->get_user_define_extended_fields( $a_new_post['board_id'] );
			unset( $o_post_model );

			// do not store default field into tbl::x2b_user_define_vars
			if ( count( $a_user_define_extended_fields ) ) {
				foreach ( $a_user_define_extended_fields as $idx => $o_user_define_item ) {
					$o_user_input_value = \X2board\Includes\Classes\Context::get( $o_user_define_item->eid );
					if ( $o_user_input_value == null ) {
						continue;
					}
					$this->_insert_user_defined_value( $a_new_post['board_id'], $a_new_post['post_id'], $idx, $o_user_input_value, $o_user_define_item->eid );
				}
			}
			$o_file_controller = \X2board\Includes\getController( 'file' );
			$o_file_controller->set_files_valid( $a_new_post['post_id'] );
			unset( $o_file_controller );
			$this->update_uploaded_count( array( $a_new_post['post_id'] ) );
			unset( $a_new_post );

			// Update the category if the category_id exists.
			if ( $o_old_post->get( 'category_id' ) != $o_new_obj->category_id || $o_old_post->get( 'board_id' ) == $o_logged_info->ID ) {
				$o_category_controller = \X2board\Includes\getController( 'category' );
				$o_category_controller->set_board_id( $o_new_obj->board_id );
				if ( $o_old_post->get( 'category_id' ) != $o_new_obj->category_id ) {  // decrease post count from old category
					$o_category_controller->update_category_count( $o_old_post->get( 'category_id' ) );
				}
				if ( $o_new_obj->category_id ) {  // increase post count from old category
					$o_category_controller->update_category_count( $o_new_obj->category_id );
				}
				unset( $o_category_controller );
			}
			unset( $o_logged_info );
			unset( $o_old_post );

			// Remove the thumbnail file
			$s_post_thumbnail_dir = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . \X2board\Includes\getNumberingPath( $o_new_obj->post_id, 3 );
			$this->_o_wp_filesystem->delete( $s_post_thumbnail_dir );

			// remove from cache
			$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance( 'object' );
			if ( $o_cache_handler->isSupport() ) {
				// remove post item from cache
				$cache_key = 'post_item:' . \X2board\Includes\getNumberingPath( $o_new_obj->post_id ) . $o_new_obj->post_id;
				$o_cache_handler->delete( $cache_key );
			}
			unset( $o_cache_handler );
			$o_rst = new \X2board\Includes\Classes\BaseObject();
			$o_rst->add( 'post_id', $o_new_obj->post_id );
			$o_rst->add( 'category_id', $o_new_obj->category_id );
			unset( $o_new_obj );
			return $o_rst;
		}

		/**
		 * Update appended file count of the post
		 * updateUploaedCount($documentSrlList)
		 *
		 * @param
		 * @return void
		 */
		public function update_uploaded_count( $a_post_id ) {
			if ( is_array( $a_post_id ) ) {
				global $wpdb;
				$o_file_model = \X2board\Includes\getModel( 'file' );
				$a_post_id    = array_unique( $a_post_id );
				foreach ( $a_post_id as $_ => $n_post_id ) {
					$fileCount = $o_file_model->get_files_count( $n_post_id );
					$result    = $wpdb->update(
						"{$wpdb->prefix}x2b_posts",
						array( 'uploaded_count' => $fileCount ),
						array( 'post_id' => esc_sql( intval( $n_post_id ) ) )
					);
					if ( $result < 0 || $result === false ) {
						return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
					}
				}
				unset( $o_file_model );
			}
		}

		/**
		 * Deleting post
		 *
		 * @param int      $document_srl
		 * @param bool     $is_admin
		 * @param bool     $isEmptyTrash
		 * @param postItem $o_post
		 * @return object
		 */
		public function delete_post( $n_post_id, $is_admin = false, $isEmptyTrash = false, $o_post = null ) {
			if ( ! $isEmptyTrash ) {
				// get model object of the document
				$o_post_model = \X2board\Includes\getModel( 'post' );
				// Check if the documnet exists
				$o_post = $o_post_model->get_post( $n_post_id, $is_admin );
				unset( $o_post_model );
			} elseif ( $isEmptyTrash && $o_post == null ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_post', X2B_DOMAIN ) );
			}

			if ( ! $o_post->is_exists() || $o_post->post_id != $n_post_id ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_post', X2B_DOMAIN ) );
			}
			// Check if a permossion is granted
			if ( ! $o_post->is_granted() ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_not_permitted', X2B_DOMAIN ) );
			}

			// if empty trash, post already deleted, therefore post not delete
			if ( ! $isEmptyTrash ) { // Delete the post
				global $wpdb;
				$result = $wpdb->delete(
					$wpdb->prefix . 'x2b_posts',
					array( 'post_id' => $n_post_id ),
					array( '%d' ), // make sure the id format
				);
				if ( $result < 0 || $result === false ) {
					wp_die( $wpdb->last_error );
				}
			}

			$this->_delete_wp_post( $n_post_id );
			// $this->deleteDocumentAliasByDocument($n_post_id);

			$this->_delete_post_history( null, $n_post_id, null );
			// Update category information if the category_id exists.
			$n_board_id    = $o_post->get( 'board_id' );
			$n_category_id = $o_post->get( 'category_id' );
			if ( $n_category_id ) {
				// $this->updateCategoryCount($oDocument->get('module_srl'),$oDocument->get('category_srl'));
				$o_category_controller = \X2board\Includes\getController( 'category' );
				$o_category_controller->set_board_id( $n_board_id );
				$o_category_controller->update_category_count( $n_category_id );
				unset( $o_category_controller );
			}

			// Delete a declared list

			// Delete extended user defined variables
			$this->_delete_extended_user_defined_vars_all( $n_board_id, $n_post_id );

			// Call a trigger (after)
			$o_comment_controller = \X2board\Includes\getController( 'comment' );
			$o_rst                = $o_comment_controller->trigger_after_delete_post_comments( $n_post_id );
			if ( ! $o_rst->toBool() ) {
				wp_die( 'weird error occured in \includes\modules\comment\comment.controller.php::trigger_after_delete_post_comments()' );
			}
			unset( $o_comment_controller );

			// declared post, log delete
			$this->_delete_declared_posts( $n_board_id, $n_post_id );
			$this->_delete_post_readed_log( $n_board_id, $n_post_id );
			$this->_delete_post_voted_log( $n_board_id, $n_post_id );

			// Remove the thumbnail file
			$s_post_thumbnail_dir = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . \X2board\Includes\getNumberingPath( $n_post_id, 3 );
			$this->_o_wp_filesystem->delete( $s_post_thumbnail_dir );

			// Remove a attached file
			$o_file_controller = \X2board\Includes\getController( 'file' );
			$o_file_controller->delete_files( $n_post_id );
			unset( $o_file_controller );

			// remove from cache
			$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance( 'object' );
			if ( $o_cache_handler->isSupport() ) {
				$cache_key = 'post_item:' . \X2board\Includes\getNumberingPath( $n_post_id ) . $n_post_id;
				$o_cache_handler->delete( $cache_key );
			}
			unset( $o_cache_handler );
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Delete post history
		 * deleteDocumentHistory($history_srl, $document_srl, $module_srl)
		 *
		 * @param int $history_srl
		 * @param int $n_post_id
		 * @param int $n_board_id
		 * @return void
		 */
		// function
		private function _delete_post_history( $n_history_id, $n_post_id, $n_board_id ) {
			// $args = new stdClass();
			// $args->history_srl = $history_srl;
			// $args->module_srl = $module_srl;
			// $args->document_srl = $document_srl;
			// if(!$args->history_srl && !$args->module_srl && !$args->document_srl) return;
			// executeQuery("document.deleteHistory", $args);
			return;
		}

		/**
		 * Delete declared post, log
		 * _deleteDeclaredDocuments($documentSrls)
		 *
		 * @param string $post_ids (ex: 1, 2,56, 88)
		 * @return void
		 */
		private function _delete_declared_posts( $post_ids ) {
			error_log( print_r( 'should activate _delete_declared_posts()', true ) );
			return;
			// executeQuery('document.deleteDeclaredDocuments', $documentSrls);
			// executeQuery('document.deleteDocumentDeclaredLog', $documentSrls);
		}

		/**
		 * Delete readed log
		 * _deleteDocumentReadedLog($documentSrls)
		 *
		 * @param string $post_ids (ex: 1, 2,56, 88)
		 * @return void
		 */
		private function _delete_post_readed_log( $post_ids ) {
			return;
			// executeQuery('document.deleteDocumentReadedLog', $documentSrls);
		}

		/**
		 * Delete voted log
		 * _deleteDocumentVotedLog($documentSrls)
		 *
		 * @param string $post_ids (ex: 1, 2,56, 88)
		 * @return void
		 */
		private function _delete_post_voted_log( $post_ids ) {
			return;
			// executeQuery('document.deleteDocumentVotedLog', $documentSrls);
		}

		/**
		 * x2b post를 WP post에 복제해야 하는가?
		 *
		 * @param int $a_post_param
		 */
		private function _is_post_public( $s_post_status ) {
			$o_module_info = \X2board\Includes\Classes\Context::get( 'current_module_info' );

			if ( $o_module_info->grant_list == X2B_ALL_USERS ) {
				$o_post_class         = \X2board\Includes\getClass( 'post' );
				$s_post_status_public = $o_post_class->get_config_status( 'public' );
				unset( $o_post_class );
				if ( $s_post_status == $s_post_status_public ) {
					unset( $o_module_info );
					return true;
				}
			}
			unset( $o_module_info );
			return false;
		}

		/**
		 * x2b post를 WP post에 복제함
		 *
		 * @param int $a_post_param
		 */
		private function _insert_wp_post( $a_post_param ) {
			$s_title        = strip_tags( $a_post_param['title'] );
			$s_post_content = strip_tags( $a_post_param['content'] );
			$s_post_status  = $this->_is_post_public( $a_post_param['status'] ) ? 'publish' : 'private';
			$a_params       = array(
				'post_author'    => $a_post_param['post_author'],
				'post_title'     => $s_title,
				'post_content'   => $s_post_content,
				'post_status'    => $s_post_status,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_name'      => $a_post_param['post_id'],
				'post_parent'    => $a_post_param['board_id'],
				'post_type'      => X2B_DOMAIN,
				'post_date'      => $a_post_param['regdate_dt'],
			);
			$result         = wp_insert_post( $a_params, true );
			unset( $a_params );
			if ( is_wp_error( $result ) ) {
				wp_die( $result->get_error_message() );
			}
			return $result; // new WP post ID
		}

		/**
		 * x2b post를 WP post에 수정함
		 *
		 * @param int $a_post_param
		 */
		private function _update_wp_post( $a_post_param ) {
			$n_wp_post_id        = \X2board\Includes\get_wp_post_id_by_x2b_post_id( $a_post_param['post_id'] );
			$o_post              = get_post( intval( $n_wp_post_id ) );
			$o_post->post_author = $a_post_param['post_author'];

			if ( $this->_is_post_public( $a_post_param['status'] ) ) {
				$s_title        = strip_tags( $a_post_param['title'] );
				$s_post_content = strip_tags( $a_post_param['content'] );
				$s_post_status  = 'publish';
			} else {
				$s_title        = '';
				$s_post_content = '';
				$s_post_status  = 'private';
			}

			$o_post->post_title   = $s_title;
			$o_post->post_content = $s_post_content;
			$o_post->post_status  = $s_post_status;
			$result               = wp_update_post( $o_post );
			unset( $o_post );
			if ( is_wp_error( $result ) ) {
				wp_die( $result->get_error_message() );
				return false;
			}
			return $result; // old WP post ID
		}

		/**
		 * delete from WP post
		 *
		 * @param int $n_post_id
		 */
		private function _delete_wp_post( $n_x2b_post_id ) {
			$n_wp_post_id = \X2board\Includes\get_wp_post_id_by_x2b_post_id( $n_x2b_post_id );
			if ( has_post_thumbnail( $n_wp_post_id ) ) {
				$n_attachment_id = get_post_thumbnail_id( $n_wp_post_id );
				wp_delete_attachment( $n_attachment_id, true );
				delete_post_thumbnail( $n_wp_post_id );
			}
			wp_delete_post( $n_wp_post_id );
		}

		/**
		 * Increase the number of comments in the post
		 * updateCommentCount($document_srl, $comment_count, $last_updater, $comment_inserted = false)
		 * Update modified date, modifier, and order with increasing comment count
		 *
		 * @param int    $n_post_id
		 * @param int    $comment_count
		 * @param string $s_last_updater
		 * @param bool   $comment_inserted
		 * @return object
		 */
		public function update_comment_count( $n_post_id, $comment_count, $s_last_updater, $comment_inserted = false ) {
			$a_param = array();
			if ( $comment_inserted ) {
				$a_param['update_order'] = -1 * \X2board\Includes\getNextSequence();
				$a_param['last_updater'] = $s_last_updater;

				$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance( 'object' );
				if ( $o_cache_handler->isSupport() ) {
					// remove post item from cache
					$cache_key = 'post_item:' . \X2board\Includes\getNumberingPath( $n_post_id ) . $n_post_id;
					$o_cache_handler->delete( $cache_key );
				}
				unset( $o_cache_handler );
			}
			$a_param['comment_count']  = $comment_count;
			$a_param['last_update_dt'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

			$a_set = array();
			foreach ( $a_param as $key => $value ) {
				$a_set[] = "`$key` = '$value'";
			}
			unset( $a_param );

			// increase comment_count
			global $wpdb;
			$query = "UPDATE `{$wpdb->prefix}x2b_posts` SET " . implode( ',', $a_set ) . " WHERE `post_id` = $n_post_id";
			unset( $a_set );
			if ( $wpdb->query( $query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Grant a permisstion of the post
		 * addGrant($document_srl)
		 * Available in the current connection with session value
		 *
		 * @param int $document_srl
		 * @return void
		 */
		private function _add_grant( $n_post_id ) {
			$_SESSION['x2b_own_post'][ $n_post_id ] = true;
		}

		/**
		 * Remove values of extended user defined variable from the post
		 * deleteDocumentExtraVars($module_srl, $document_srl = null, $var_idx = null, $lang_code = null, $eid = null)
		 *
		 * @param int $n_board_id
		 * @param int $n_post_id
		 * @return
		 */
		private function _delete_extended_user_defined_vars_all( $n_board_id, $n_post_id ) {
			global $wpdb;
			$result = $wpdb->delete(
				$wpdb->prefix . 'x2b_user_define_vars',
				array(
					'board_id' => $n_board_id,
					'post_id'  => $n_post_id,
				),
				array( '%d', '%d' ),
			);
			if ( $result < 0 || $result === false ) {
				wp_die( $wpdb->last_error );
			}
		}

		/**
		 * @brief mask multibyte string
		 * param 원본문자열, 마스킹하지 않는 전단부 글자수, 마스킹하지 않는 후단부 글자수, 마스킹 마크 최대 표시수, 마스킹마크
		 * echo _mask_mb_str('abc12234pro', 3, 2); => abc******ro
		 */
		private function _mask_mb_str( $str, $len1, $len2 = 0, $limit = 0, $mark = '*' ) {
			$arr_str = preg_split( '//u', $str, -1, PREG_SPLIT_NO_EMPTY );
			$str_len = count( $arr_str );

			$len1 = abs( $len1 );
			$len2 = abs( $len2 );
			if ( $str_len <= ( $len1 + $len2 ) ) {
				return $str;
			}

			$str_head = '';
			$str_body = '';
			$str_tail = '';

			$str_head = join( '', array_slice( $arr_str, 0, $len1 ) );
			if ( $len2 > 0 ) {
				$str_tail = join( '', array_slice( $arr_str, $len2 * -1 ) );
			}

			$arr_body = array_slice( $arr_str, $len1, ( $str_len - $len1 - $len2 ) );

			if ( ! empty( $arr_body ) ) {
				$len_body = count( $arr_body );
				$limit    = abs( $limit );
				if ( $limit > 0 && $len_body > $limit ) {
					$len_body = $limit;
				}
				$str_body = str_pad( '', $len_body, $mark );
			}
			return $str_head . $str_body . $str_tail;
		}
		/**
		 * Secure personal private from an extra variable of the documents
		 * secureDocumentExtraVars($nModuleSrl, $nVarIdx, $sBeginYyyymmdd, $sEndYyyymmdd)
		 *
		 * @param int $module_srl
		 * @param int $var_idx
		 * @return BaseObject
		 */
		public function secure_post_user_defined_vars( $nModuleSrl, $nVarIdx, $sBeginYyyymmdd, $sEndYyyymmdd ) {
			if ( ! $nModuleSrl || ! $nVarIdx ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) );
			}

			$oArg                 = new stdClass();
			$oArg->module_srl     = $nModuleSrl;
			$oArg->var_idx        = $nVarIdx;
			$oArg->begin_yyyymmdd = $sBeginYyyymmdd . '000001';
			$oArg->end_yyyymmdd   = $sEndYyyymmdd . '235959';
			$oRst                 = executeQueryArray( 'document.getDocumentListWithExtraVarsPeriod', $oArg );
			unset( $oArg );
			if ( ! count( $oRst->data ) ) {
				return new \X2board\Includes\Classes\BaseObject();
			}

			foreach ( $oRst->data as $_ => $oSingleExtraVar ) {
				if ( strpos( $oSingleExtraVar->value, '|@|' ) ) {
					$aVal = explode( '|@|', $oSingleExtraVar->value );
					$nCnt = count( $aVal );
					if ( $nCnt == 3 ) {  // maybe cell phone info
						$aVal[2] = '*';
					} elseif ( $nCnt == 4 || $nCnt == 5 ) { // maybe addr info
						for ( $i = 2; $i <= $nCnt; $i++ ) {
							$aVal[ $i ] = '*';
						}
					}
					$oSingleExtraVar->value = implode( '|@|', $aVal );
				} else { // maybe cell phone info
					$oSingleExtraVar->value = $this->_mask_mb_str( $oSingleExtraVar->value, 3, 3 );
				}
			}
			$oArg = new stdClass();
			foreach ( $oRst->data as $_ => $oSingleExtraVar ) {
				$oArg->module_srl   = $oSingleExtraVar->module_srl;
				$oArg->document_srl = $oSingleExtraVar->document_srl;
				$oArg->var_idx      = $oSingleExtraVar->var_idx;
				$oArg->value        = $oSingleExtraVar->value;
				$oRst               = executeQuery( 'document.updateDocumentExtraVar', $oArg );
				if ( ! $oRst->toBool() ) {
					return $oRst;
				}
			}
			unset( $oArg );
			unset( $oRst );
			return new \X2board\Includes\Classes\BaseObject();
		}
	}
}
