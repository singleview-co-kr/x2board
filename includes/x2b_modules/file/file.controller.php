<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * Controller class of the file module
 *
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\File\\fileController' ) ) {

	class fileController extends file {
		/**
		 * Initialization
		 *
		 * @return void
		 */
		function init() {
			if ( ! isset( $_SESSION['__X2B_UPLOADING_FILES_INFO__'] ) ) {
				$_SESSION['__X2B_UPLOADING_FILES_INFO__'] = array();
			}
			if ( ! isset( $_SESSION['x2b_upload_info'] ) ) {
				$_SESSION['x2b_upload_info'] = array();
			}
			if ( ! isset( $_SESSION['__X2B_FILE_KEY__'] ) ) {
				$_SESSION['__X2B_FILE_KEY__'] = array();
			}
			if ( ! isset( $_SESSION['__X2B_FILE_KEY_AND__'] ) ) {
				$_SESSION['__X2B_FILE_KEY_AND__'] = array();
			}
		}

		/**
		 * Upload enabled
		 * called in \includes\modules\board\board.view.php::write_post_hidden_fields()
		 * setUploadInfo($editor_sequence, $upload_target_srl=0)
		 *
		 * @param int $editor_sequence
		 * @param int $upload_target_id
		 * @return void
		 */
		public function set_upload_info( $n_editor_sequence, $n_upload_target_id ) {
			if ( ! isset( $_SESSION['x2b_upload_info'][ $n_editor_sequence ] ) ) {
				$_SESSION['x2b_upload_info'][ $n_editor_sequence ] = new \stdClass();
			}
			$_SESSION['x2b_upload_info'][ $n_editor_sequence ]->enabled          = true;
			$_SESSION['x2b_upload_info'][ $n_editor_sequence ]->upload_target_id = $n_upload_target_id;
		}

		/**
		 * check upload delete appending file permission
		 *
		 * @return void
		 */
		private function _check_attach_permission( $n_board_id ) {
			require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'user_define_fields' . DIRECTORY_SEPARATOR . 'UserDefineFields.class.php';
			$o_post_user_define_fields = new \X2board\Includes\Classes\UserDefineFields;
			$o_attach_field_info = $o_post_user_define_fields->get_default_field_info_by_field_type( 'attach' );
			unset( $o_post_user_define_fields );

			global $wpdb;
			$o_rst = $wpdb->get_row("SELECT `json_param` FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' AND `var_type` ='{$o_attach_field_info['field_type']}'");
			unset( $o_attach_field_info );
			$a_extra_param = unserialize( $o_rst->json_param );
			unset( $o_rst );

			require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'user_define_fields' . DIRECTORY_SEPARATOR . 'GuestUserDefineFields.class.php';
			$o_user_define_item_for_guest = new \X2board\Includes\Classes\UserDefineItemForGuest(
				null, null, null, null, null, null, null, null, null, null, null
			);

			$b_accessible = $o_user_define_item_for_guest->check_accessible( $a_extra_param['permission'], $a_extra_param['roles']);
			unset( $o_user_define_item_for_guest );
			unset( $a_extra_param );
			return $b_accessible;
		}

		/**
		 * Upload attachments in the editor
		 *
		 * Determine the upload target srl from editor_sequence and uploadTargetSrl variables.
		 * Create and return the UploadTargetSrl if not exists so that UI can use the value
		 * for sync.
		 * function procFileUpload() {
		 *
		 * @return void
		 */
		public function proc_file_upload() {
			$a_file_info = $_FILES['files'];
			// An error appears if not a normally uploaded file
			if ( ! is_uploaded_file( $a_file_info['tmp_name'] ) ) {
				exit();
			}

			// Basic variables setting
			$upload_target_id = null;
			$n_comment_id     = intval( \X2board\Includes\Classes\Context::get( 'comment_id' ) );
			if ( $n_comment_id ) {  // attachment of a old comment
				$editor_sequence  = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
				$upload_target_id = $n_comment_id;
			} else {
				$n_post_id = intval( \X2board\Includes\Classes\Context::get( 'post_id' ) );
				if ( $n_post_id ) {  // if upload from post editor
					$editor_sequence  = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
					$upload_target_id = $n_post_id;
				} else {  // attachment of a new comment
					$editor_sequence = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
				}
			}

			$a_uploaded_file_info = array(
				'is_success'          => false,
				'file_id'             => null,
				'thumbnail_abs_url'   => null,
				'file_type'           => null,
				'file_size'           => null,
				'reserved_comment_id' => null,
				'error'               => null,  // for ajax uploading error msg
			);

			$n_board_id = intval( \X2board\Includes\Classes\Context::get( 'board_id' ) );
			// deny if not allowed
			if ( ! $this->_check_attach_permission( $n_board_id ) ) {
				// $upload_attach_files['file_id'] = 1;
				$upload_attach_files['error'] = __('msg_not_permitted', X2B_DOMAIN);
				return $upload_attach_files;
			}

			// deny if there is neither upload permission nor information
			if ( ! $_SESSION['x2b_upload_info'][ $editor_sequence ]->enabled ) {
				// $upload_attach_files['file_id'] = 1;
				$upload_attach_files['error'] = __('msg_upload_file_failed', X2B_DOMAIN);
				return $upload_attach_files;
			}
			// Extract from session information if upload_target_id is not specified
			if ( ! $upload_target_id ) {
				$upload_target_id = $_SESSION['x2b_upload_info'][ $editor_sequence ]->upload_target_id;
			}
			// Create if upload_target_srl is not defined in the session information
			if ( ! $upload_target_id ) {
				$_SESSION['x2b_upload_info'][ $editor_sequence ]->upload_target_id = $upload_target_id = \X2board\Includes\get_next_sequence();
			}

			$output         = $this->insert_file( $a_file_info, $n_board_id, $upload_target_id );  // , $module_srl
			$s_download_url = $output->get( 'thumbnail_abs_url' );

			// if($file['size']){
			// 사진 메타데이터 추출
			// require_once(ABSPATH . 'wp-admin/includes/image.php');
			// $metadata = wp_read_image_metadata("{$this->abspath}{$this->path}/{$file_unique_name}");
			// if(!$metadata){
			// $metadata = array();
			// }

			if ( $output->error != '0' ) {
				$upload_attach_files['error'] = $output->message;
				return $upload_attach_files;
			}

			$a_uploaded_file_info['is_success']          = true;
			$a_uploaded_file_info['file_id']             = $output->get( 'file_id' );
			$a_uploaded_file_info['thumbnail_abs_url']   = $s_download_url;
			$a_uploaded_file_info['file_type']           = $output->get( 'file_type' );
			$a_uploaded_file_info['file_size']           = $output->get( 'file_size' );
			$a_uploaded_file_info['reserved_comment_id'] = $upload_target_id;

			$upload_attach_files   = array();  // reserved for multiple upload
			$upload_attach_files[] = $a_uploaded_file_info;
			return $upload_attach_files;
		}

		/**
		 * Add an attachement
		 *
		 * <pre>
		 * This method call trigger 'file.insertFile'.
		 *
		 * Before trigger object contains:
		 * - module_srl
		 * - upload_target_srl
		 *
		 * After trigger object contains:
		 * - file_srl
		 * - upload_target_srl
		 * - module_srl
		 * - direct_download
		 * - source_filename
		 * - uploaded_filename
		 * - donwload_count
		 * - file_size
		 * - comment
		 * - member_srl
		 * - sid
		 * </pre>
		 * insertFile($file_info, $module_srl, $upload_target_srl, $download_count = 0, $manual_insert = false)
		 *
		 * @param object $file_info PHP file information array
		 * @param int    $module_srl Sequence of module to upload file
		 * @param int    $upload_target_srl Sequence of target to upload file
		 * @param int    $download_count Initial download count
		 * @param bool   $manual_insert If set true, pass validation check
		 * @return BaseObject
		 */
		public function insert_file( $file_info, $n_board_id, $upload_target_id, $download_count = 0, $manual_insert = false ) {
			global $wpdb;
			// A workaround for Firefox upload bug
			if ( preg_match( '/^=\?UTF-8\?B\?(.+)\?=$/i', $file_info['name'], $match ) ) {
				$file_info['name'] = base64_decode( strtr( $match[1], ':', '/' ) );
			}

			$o_file_model = \X2board\Includes\get_model( 'file' );
			$logged_info  = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( ! $manual_insert ) {
				// Get the file configurations
				if ( $logged_info->is_admin != 'Y' ) {
					$o_current_module_info = \X2board\Includes\Classes\Context::get( 'current_module_info' );

					// check file type
					if ( isset( $o_current_module_info->file_allowed_filetypes ) && $o_current_module_info->file_allowed_filetypes !== '*.*' ) {
						$filetypes = explode( ',', $o_current_module_info->file_allowed_filetypes );
						$ext       = array();
						foreach ( $filetypes as $item ) {
							// $item  = explode( '.', $item );
							$ext[] = strtolower( trim($item) );
						}
						$uploaded_ext = explode( '.', $file_info['name'] );
						$uploaded_ext = strtolower( array_pop( $uploaded_ext ) );

						if ( ! in_array( $uploaded_ext, $ext ) ) {
							return $this->stop( __( 'msg_not_allowed_filetype', X2B_DOMAIN ) );
						}
						unset( $ext );
					}

					$allowed_filesize    = $o_current_module_info->file_allowed_filesize_mb * 1048576; // 1024 * 1024;
					$allowed_attach_size = $o_current_module_info->file_allowed_attach_size_mb * 1048576; // 1024 * 1024;
					unset( $o_current_module_info );
					// An error appears if file size exceeds a limit
					if ( $allowed_filesize < filesize( $file_info['tmp_name'] ) ) {
						return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_exceeds_limit_size', X2B_DOMAIN ) );
					}
					// Get total file size of all attachements (from DB)
					$o_file_info   = $wpdb->get_row("SELECT sum(`file_size`) as `attached_size` FROM `{$wpdb->prefix}x2b_files` WHERE `upload_target_id` = $upload_target_id");
					$attached_size = intval( $o_file_info->attached_size ) + filesize( $file_info['tmp_name'] );
					if ( $attached_size > $allowed_attach_size ) {
						return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_exceeds_limit_size', X2B_DOMAIN ) );
					}
				}
			}

			// https://github.com/xpressengine/xe-core/issues/1713
			$file_info['name'] = preg_replace( '/\.((ph(p|t|ar)?[0-9]?|p?html?|cgi|pl|exe|(?:a|j)sp|inc).*)$/i', '$0-x', $file_info['name'] );
			$file_info['name'] = \X2board\Includes\remove_hack_tag( $file_info['name'] );
			$file_info['name'] = str_replace( array( '<', '>' ), array( '%3C', '%3E' ), $file_info['name'] );
			$file_info['name'] = str_replace( '&amp;', '&', $file_info['name'] );

			// Get random number generator
			$o_random = new \X2board\Includes\Classes\Security\Password();

			$s_attach_path     = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_ATTACH_FILE_PATH;
			$s_attach_rand_dir = \X2board\Includes\get_numbering_path( $upload_target_id, 3 );
			// Set upload path by checking if the attachement is an image or other kinds of file
			$b_img_file = $o_file_model->is_image_file( $file_info['name'] );

			if ( $b_img_file ) {
				$s_path = $s_attach_path . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $n_board_id . DIRECTORY_SEPARATOR . $s_attach_rand_dir;

				// special character to '_'
				// change to random file name. because window php bug. window php is not recognize unicode character file name - by cherryfilter
				$ext       = substr( strrchr( $file_info['name'], '.' ), 1 );
				$_filename = $o_random->create_secure_salt( 32, 'hex' ) . '.' . $ext;
				$filename  = $s_path . $_filename;
				$idx       = 1;
				while ( file_exists( $filename ) ) {
					$filename = $path . preg_replace( '/\.([a-z0-9]+)$/i', '_' . $idx . '.$1', $_filename );
					++$idx;
				}
				$direct_download   = 'Y';
				$s_file_type       = 'image';
				$thumbnail_abs_url = $o_file_model->get_thumbnail_url( $s_file_type, $filename );
			} else {
				$s_path            = $s_attach_path . DIRECTORY_SEPARATOR . 'binaries' . DIRECTORY_SEPARATOR . $n_board_id . DIRECTORY_SEPARATOR . $s_attach_rand_dir;
				$filename          = $s_path . $o_random->create_secure_salt( 32, 'hex' );
				$direct_download   = 'N';
				$s_file_type       = 'binary';
				$thumbnail_abs_url = $o_file_model->get_thumbnail_url( $s_file_type, $filename );
			}
			unset( $o_file_model );

			if ( ! file_exists( $s_path ) ) {
				if ( ! wp_mkdir_p( $s_path ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_no_permission', X2B_DOMAIN ) );
				}
			}

			// Check uploaded file
			if ( ! $manual_insert && ! \X2board\Includes\check_uploaded_file( $file_info['tmp_name'], $file_info['name'] ) ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_upload_file_failed', X2B_DOMAIN ) );
			}

			// Move the file
			if ( $manual_insert ) {
				@copy( $file_info['tmp_name'], $filename );
				if ( ! file_exists( $filename ) ) {
					$a_path_parts = pathinfo($file_info['tmp_name']);
					if( isset( $a_path_parts['extension'] ) ) {
						$filename = $s_path . $o_random->create_secure_salt( 32, 'hex' ) . '.' . $a_path_parts['extension'];
					}
					else {
						$filename = $s_path . $o_random->create_secure_salt( 32, 'hex' );
					}
					unset($a_path_parts);
					@copy( $file_info['tmp_name'], $filename );
				}
			} elseif ( ! @move_uploaded_file( $file_info['tmp_name'], $filename ) ) {
					$filename = $s_path . $o_random->create_secure_salt( 32, 'hex' ) . '.' . $ext;
				if ( ! @move_uploaded_file( $file_info['tmp_name'], $filename ) ) {
					return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_upload_file_failed', X2B_DOMAIN ) );
				}
			}

			// file information
			$a_new_file['file_id']           = \X2board\Includes\get_next_sequence();
			$a_new_file['upload_target_id']  = $upload_target_id;
			$a_new_file['board_id']          = $n_board_id;
			$a_new_file['direct_download']   = $direct_download;
			$a_new_file['source_filename']   = $file_info['name'];
			$a_new_file['uploaded_filename'] = $filename;
			$a_new_file['download_count']    = $download_count;
			$a_new_file['file_size']         = @filesize( $filename );
			$a_new_file['author']            = $logged_info->ID;
			$a_new_file['sid']               = $o_random->create_secure_salt( 32, 'hex' );
			// pass true in a second parameter to tell it to use the GMT offset.
			$a_new_file['regdate']           = date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) );
			$a_new_file['ipaddress']         = \X2board\Includes\get_remote_ip();
			unset( $logged_info );

			$result = $wpdb->insert( "{$wpdb->prefix}x2b_files", $a_new_file );
			if ( $result < 0 || $result === false ) {
				unset( $a_new_file );
				unset( $result );
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			unset( $result );

			$_SESSION['__X2B_UPLOADING_FILES_INFO__'][ $a_new_file['file_id'] ] = true;

			$o_rst = new \X2board\Includes\Classes\BaseObject();
			$o_rst->add( 'file_id', $a_new_file['file_id'] );
			$o_rst->add( 'file_size', $a_new_file['file_size'] );
			$o_rst->add( 'file_type', $s_file_type );
			$o_rst->add( 'direct_download', $a_new_file['direct_download'] );
			$o_rst->add( 'thumbnail_abs_url', $thumbnail_abs_url );
			unset( $a_new_file );
			return $o_rst;
		}

		/**
		 * Delete an attachment from the editor
		 * procFileDelete()
		 *
		 * @return BaseObject
		 */
		public function proc_file_delete() {
			$n_comment_id = intval( \X2board\Includes\Classes\Context::get( 'comment_id' ) );
			if ( $n_comment_id ) {  // attachment of a old comment
				$editor_sequence  = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
				$upload_target_id = $n_comment_id;
			} else {
				$n_post_id = intval( \X2board\Includes\Classes\Context::get( 'post_id' ) );
				if ( $n_post_id ) {  // if upload from post editor
					$editor_sequence  = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
					$upload_target_id = $n_post_id;
				} else {  // attachment of a new comment
					$editor_sequence = \X2board\Includes\Classes\Context::get( 'editor_call_id' );
				}
			}

			$file_id = \X2board\Includes\Classes\Context::get( 'file_id' );
			// Exit a session if there is neither upload permission nor information
			if ( ! $_SESSION['x2b_upload_info'][ $editor_sequence ]->enabled ) {
				exit();
			}

			// $upload_target_id = $_SESSION['x2b_upload_info'][ $editor_sequence ]->upload_target_id;

			$logged_info  = \X2board\Includes\Classes\Context::get( 'logged_info' );
			$o_file_model = \X2board\Includes\get_model( 'file' );

			$ids = explode( ',', $file_id );
			if ( ! count( $ids ) ) {
				unset( $logged_info );
				unset( $o_file_model );
				return;
			}

			global $wpdb;
			for ( $i = 0;$i < count( $ids );$i++ ) {
				$n_id = intval( $ids[ $i ] );
				if ( ! $n_id ) {
					continue;
				}

				$o_file_info = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `file_id`={$n_id}" );
				if ( $o_file_info === null ) {
					continue;
				}
				if ( ! $o_file_info ) {
					continue;
				}

				$file_grant = $o_file_model->get_file_grant( $o_file_info );
				if ( ! $file_grant->is_deletable ) {
					continue;
				}

				if ( $upload_target_id && $file_id ) {
					$output = $this->delete_file( $o_file_info );
				}
			}
			unset( $logged_info );
			unset( $o_file_model );
			return $output;
		}

		/**
		 * Delete all attachments of a particular post or comment
		 * deleteFiles($upload_target_srl)
		 *
		 * @param int $upload_target_srl Upload target srl to delete files
		 * @return BaseObject
		 */
		public function delete_files( $upload_target_id ) {
			// Get a list of attachements
			$o_file_model = \X2board\Includes\get_model( 'file' );
			$a_file_list  = $o_file_model->get_files( $upload_target_id );
			unset( $o_file_model );
			// Success returned if no attachement exists
			if ( ! is_array( $a_file_list ) || ! count( $a_file_list ) ) {
				return new \X2board\Includes\Classes\BaseObject();
			}

			// Delete the file
			$a_path_to_unset = array();
			foreach ( $a_file_list as $_ => $o_file ) {
				$this->delete_file( $o_file );
				$path_info = pathinfo( $o_file->uploaded_filename );
				if ( ! in_array( $path_info['dirname'], $a_path_to_unset ) ) {
					$a_folder_info     = explode( $o_file->board_id, $path_info['dirname'] );
					$a_path_to_unset[] = $path_info['dirname'];
					unset( $a_folder_info );
				}
			}

			// Remove a file directory of the post or comment
			foreach ( $a_path_to_unset as $s_dir_path ) {
				\X2board\Includes\Classes\FileHandler::remove_blank_dir( $s_dir_path );
			}
			unset( $a_path_to_unset );
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Delete the single attachment
		 * deleteFile($file_srl)
		 *
		 * @param int $o_file_info a file object to delete
		 * @return BaseObject
		 */
		public function delete_file( $o_file_info ) {
			if ( ! $o_file_info->upload_target_id ) {
				return;
			}
			// Remove from the DB
			global $wpdb;
			$result = $wpdb->delete( "{$wpdb->prefix}x2b_files", array( 'file_id' => $o_file_info->file_id ) );
			if ( $result < 0 || $result === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			unset( $result );

			// If successfully deleted, remove the file
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$fileSystemDirect = new \WP_Filesystem_Direct( false );
			$fileSystemDirect->delete( $o_file_info->uploaded_filename );
			unset( $fileSystemDirect );
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Set the attachements of the upload_target_srl to be valid
		 * By changing its state to valid when a document is inserted, it prevents from being considered as a unnecessary file
		 * setFilesValid($upload_target_srl)
		 *
		 * @param int $upload_target_id
		 * @return BaseObject
		 */
		public function set_files_valid( $upload_target_id ) {
			global $wpdb;
			$result = $wpdb->update(
				"{$wpdb->prefix}x2b_files",
				array( 'isvalid' => 'Y' ),
				array( 'upload_target_id' => esc_sql( intval( $upload_target_id ) ) )
			);
			if ( $result < 0 || $result === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Download Attachment
		 *
		 * <pre>
		 * Receive a request directly
		 * file_srl: File sequence
		 * sid : value in DB for comparison, No download if not matched
		 *
		 * This method call trigger 'file.downloadFile'.
		 * before, after.
		 * Trigger object contains:
		 * - download_url
		 * - file_srl
		 * - upload_target_srl
		 * - upload_target_type
		 * - sid
		 * - module_srl
		 * - member_srl
		 * - download_count
		 * - direct_download
		 * - source_filename
		 * - uploaded_filename
		 * - file_size
		 * - comment
		 * - isvalid
		 * - regdate
		 * - ipaddress
		 * </pre>
		 * procFileDownload()
		 * return void
		 */
		public function proc_file_download() {
			$o_grant = \X2board\Includes\Classes\Context::get( 'grant' );
			if ( isset( $o_grant->access ) && $o_grant->access !== true ) {
				unset( $o_grant );
				wp_die( __( 'msg_not_permitted', X2B_DOMAIN ) );
			}
			unset( $o_grant );

			$file_id = \X2board\Includes\Classes\Context::get( 'file_id' );
			$sid     = \X2board\Includes\Classes\Context::get( 'sid' );

			// Get file information from the DB
			$columnList = array( 'file_id', 'sid', 'isvalid', 'source_filename', 'board_id', 'uploaded_filename', 'file_size', 'author', 'upload_target_id', 'upload_target_type' );

			$o_file_model = \X2board\Includes\get_model( 'file' );
			$file_obj     = $o_file_model->get_file( $file_id, $columnList );
			unset( $o_file_model );
			// If the requested file information is incorrect, an error that file cannot be found appears
			if ( $file_obj->file_id != $file_id || $file_obj->sid != $sid ) {
				wp_die( __( 'msg_file_not_found', X2B_DOMAIN ) );
			}
			// Notify that file download is not allowed when standing-by(Only a top-administrator is permitted)
			$logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $logged_info->is_admin != 'Y' && $file_obj->isvalid != 'Y' ) {
				wp_die( __( 'msg_not_permitted_download', X2B_DOMAIN ) );
			}
			unset( $logged_info );

			// File name
			$filename = $file_obj->source_filename;

			$o_appending_file_conf = \X2board\Includes\Classes\Context::get( 'appending_file_config' );

			// Not allow the file outlink
			if ( $o_appending_file_conf->file_allow_outlink == 'N' ) {
				// Handles extension to allow outlink
				if ( $o_appending_file_conf->file_allow_outlink_format ) {
					$allow_outlink_format_array = array();
					$allow_outlink_format_array = explode( ',', $o_appending_file_conf->file_allow_outlink_format );
					if ( ! is_array( $allow_outlink_format_array ) ) {
						$allow_outlink_format_array[0] = $o_appending_file_conf->file_allow_outlink_format;
					}

					foreach ( $allow_outlink_format_array as $val ) {
						$val = trim( $val );
						if ( preg_match( "/\.{$val}$/i", $filename ) ) {
							$o_appending_file_conf->file_allow_outlink = 'Y';
							break;
						}
					}
				}
				// Sites that outlink is allowed
				if ( $o_appending_file_conf->file_allow_outlink != 'Y' ) {
					$referer = parse_url( $_SERVER['HTTP_REFERER'] );
					if ( $referer['host'] != $_SERVER['HTTP_HOST'] ) {
						if ( $o_appending_file_conf->file_allow_outlink_site ) {
							$allow_outlink_site_array = array();
							$allow_outlink_site_array = explode( "\n", $o_appending_file_conf->file_allow_outlink_site );
							if ( ! is_array( $allow_outlink_site_array ) ) {
								$allow_outlink_site_array[0] = $o_appending_file_conf->file_allow_outlink_site;
							}

							foreach ( $allow_outlink_site_array as $val ) {
								$site = parse_url( trim( $val ) );
								if ( $site['host'] == $referer['host'] ) {
									$o_appending_file_conf->file_allow_outlink = 'Y';
									break;
								}
							}
						}
					} else {
						$o_appending_file_conf->file_allow_outlink = 'Y';
					}
				}
				if ( $o_appending_file_conf->file_allow_outlink != 'Y' ) {
					wp_die( __( 'msg_not_allowed_outlink', X2B_DOMAIN ) );
				}
			}

			// Check if a permission for file download is granted
			$downloadGrantCount = 0;
			if ( is_array( $o_appending_file_conf->file_download_grant ) ) {
				foreach ( $o_appending_file_conf->file_download_grant as $value ) {
					if ( $value ) {
						++$downloadGrantCount;
					}
				}
			}

			if ( is_array( $o_appending_file_conf->file_download_grant ) && $downloadGrantCount > 0 ) {
				if ( ! \X2board\Includes\Classes\Context::get( 'is_logged' ) ) {
					wp_die( __( 'msg_not_permitted_download', X2B_DOMAIN ) );
				}
			}

			// Increase download_count
			global $wpdb;
			$query = "UPDATE `{$wpdb->prefix}x2b_files` SET `download_count`=`download_count`+1 WHERE `file_id`='" . esc_sql( intval( $file_id ) ) . "'";
			if ( $wpdb->query( $query ) === false ) {
				wp_die( $wpdb->last_error );
			}

			$o_random = new \X2board\Includes\Classes\Security\Password();
			$file_key = $_SESSION['__X2B_FILE_KEY__'][ $file_id ] = $o_random->create_secure_salt( 32, 'hex' );
			unset( $o_random );
			$board_id = \X2board\Includes\Classes\Context::get( 'board_id' );
			header( 'Location: ' . \X2board\Includes\get_not_encoded_url( '', 'cmd', X2B_CMD_PROC_OUTPUT_FILE, 'board_id', $board_id, 'file_id', $file_id, 'file_key', $file_key ) );
			\X2board\Includes\Classes\Context::close();
			exit();
		}

		/**
		 * get file list
		 * procFileOutput()
		 *
		 * @return BaseObject
		 */
		public function proc_file_output() {
			$file_id  = \X2board\Includes\Classes\Context::get( 'file_id' );
			$file_key = \X2board\Includes\Classes\Context::get( 'file_key' );
			if ( strstr( $_SERVER['HTTP_USER_AGENT'], 'Android' ) ) {
				$is_android = true;
			} else {
				$is_android = false;
			}

			if ( $is_android && $_SESSION['__XE_FILE_KEY_AND__'][ $file_id ] ) {
				$session_key = '__X2B_FILE_KEY_AND__';
			} else {
				$session_key = '__X2B_FILE_KEY__';
			}
			$columnList = array( 'source_filename', 'uploaded_filename', 'file_size' );

			$o_file_model = \X2board\Includes\get_model( 'file' );
			$file_obj     = $o_file_model->get_file( $file_id, $columnList );
			unset( $o_file_model );

			$uploaded_filename = $file_obj->uploaded_filename;

			if ( ! file_exists( $uploaded_filename ) ) {
				wp_die( __( 'msg_file_not_found', X2B_DOMAIN ) );
			}

			if ( ! $file_key || $_SESSION[ $session_key ][ $file_id ] != $file_key ) {
				unset( $_SESSION[ $session_key ][ $file_id ] );
				wp_die( __( 'msg_invalid_request', X2B_DOMAIN ) );
			}

			$file_size = $file_obj->file_size;
			$filename  = $file_obj->source_filename;
			if ( preg_match( '#(?:Chrome|Edge)/(\d+)\.#', $_SERVER['HTTP_USER_AGENT'], $matches ) && $matches[1] >= 11 ) {
				if ( $is_android && preg_match( '#\bwv\b|(?:Version|Browser)/\d+#', $_SERVER['HTTP_USER_AGENT'] ) ) {
					$filename_param = 'filename="' . $filename . '"';
				} else {
					$filename_param = sprintf( 'filename="%s"; filename*=UTF-8\'\'%s', $filename, rawurlencode( $filename ) );
				}
			} elseif ( preg_match( '#(?:Firefox|Safari|Trident)/(\d+)\.#', $_SERVER['HTTP_USER_AGENT'], $matches ) && $matches[1] >= 6 ) {
				$filename_param = sprintf( 'filename="%s"; filename*=UTF-8\'\'%s', $filename, rawurlencode( $filename ) );
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) !== false ) {
				$filename       = rawurlencode( $filename );
				$filename_param = 'filename="' . preg_replace( '/\./', '%2e', $filename, substr_count( $filename, '.' ) - 1 ) . '"';
			} else {
				$filename_param = 'filename="' . $filename . '"';
			}

			if ( $is_android ) {
				if ( $_SESSION['__X2B_FILE_KEY__'][ $file_id ] ) {
					$_SESSION['__X2B_FILE_KEY_AND__'][ $file_id ] = $file_key;
				}
			}

			unset( $_SESSION[ $session_key ][ $file_id ] );

			\X2board\Includes\Classes\Context::close();

			$fp = fopen( $uploaded_filename, 'rb' );
			if ( ! $fp ) {
				wp_die( __( 'msg_file_not_found', X2B_DOMAIN ) );
			}

			header( 'Cache-Control: ' );
			header( 'Pragma: ' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );

			header( 'Content-Length: ' . (string) ( $file_size ) );
			header( 'Content-Disposition: attachment; ' . $filename_param );
			header( "Content-Transfer-Encoding: binary\n" );

			// if file size is lager than 10MB, use fread function (#18675748)
			if ( $file_size > 1024 * 1024 ) {
				while ( ! feof( $fp ) ) {
					echo fread( $fp, 1024 );
				}
				fclose( $fp );
			} else {
				fpassthru( $fp );
			}
			exit();
		}
	}
}
