<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * Controller class of the file module
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\File\\fileController')) {

	class fileController extends file {
		/**
		 * Initialization
		 * @return void
		 */
		function init() {
			if(!isset($_SESSION['__X2B_UPLOADING_FILES_INFO__'])) {
				$_SESSION['__X2B_UPLOADING_FILES_INFO__'] = array();
			}
			if(!isset($_SESSION['x2b_upload_info'])) {
				$_SESSION['x2b_upload_info'] = array();
			}
			if(!isset($_SESSION['__X2B_FILE_KEY__'])) {
				$_SESSION['__X2B_FILE_KEY__'] = array();
			}
			if(!isset($_SESSION['__X2B_FILE_KEY_AND__'])) {
				$_SESSION['__X2B_FILE_KEY_AND__'] = array();
			}
		}

		/**
		 * Upload enabled
		 * called in \includes\modules\board\board.view.php::write_post_hidden_fields()
		 *
		 * @param int $editor_sequence
		 * @param int $upload_target_id
		 * @return void
		 */
		// function setUploadInfo($editor_sequence, $upload_target_srl=0)
		public function set_upload_info($n_editor_sequence, $n_upload_target_id) {
			if(!isset($_SESSION['x2b_upload_info'][$n_editor_sequence])) {
				$_SESSION['x2b_upload_info'][$n_editor_sequence] = new \stdClass();
			}
			$_SESSION['x2b_upload_info'][$n_editor_sequence]->enabled = true;
			$_SESSION['x2b_upload_info'][$n_editor_sequence]->upload_target_id = $n_upload_target_id;
		}

		/**
		 * Upload attachments in the editor
		 *
		 * Determine the upload target srl from editor_sequence and uploadTargetSrl variables.
		 * Create and return the UploadTargetSrl if not exists so that UI can use the value
		 * for sync.
		 *
		 * @return void
		 */
		// public function procFileUpload() {
		public function proc_file_upload() {
			// \X2board\Includes\Classes\Context::setRequestMethod('JSON');
			// $file_info = \X2board\Includes\Classes\Context::get('Filedata');
			$a_file_info = $_FILES['files'];
// error_log(print_r($a_file_info, true));
// error_log(print_r($_POST, true));
// error_log(print_r(\X2board\Includes\Classes\Context::get('post_id'), true));
			// An error appears if not a normally uploaded file
			if(!is_uploaded_file($a_file_info['tmp_name'])) {
				exit();
			}

			// Basic variables setting
			$n_post_id = intval(\X2board\Includes\Classes\Context::get('post_id'));
			$editor_sequence = $n_post_id;  // Context::get('editor_sequence');
			// $upload_target_id = intval(\X2board\Includes\Classes\Context::get('uploadTargetSrl'));
			// if(!$upload_target_id) {
			$upload_target_id = $n_post_id;
			// }
			// $module_srl = $this->module_srl;
			$n_board_id = intval(\X2board\Includes\Classes\Context::get('board_id'));
// error_log(print_r($n_board_id, true));

			// Exit a session if there is neither upload permission nor information
			if( !$_SESSION['x2b_upload_info'][$editor_sequence]->enabled ) {
				exit();
			}
			// Extract from session information if upload_target_id is not specified
			if(!$upload_target_id) {
				$upload_target_id = $_SESSION['x2b_upload_info'][$editor_sequence]->upload_target_id;
			}
			// Create if upload_target_srl is not defined in the session information
			if(!$upload_target_id) {
				$_SESSION['x2b_upload_info'][$editor_sequence]->upload_target_id = $upload_target_id = \X2board\Includes\getNextSequence();
			}

			$output = $this->_insert_file($a_file_info, $n_board_id, $upload_target_id);  // , $module_srl
			// Context::setResponseMethod('JSON');
			if($output->get('direct_download') === 'Y') {
				// $this->add('download_url',$output->get('uploaded_filename'));
				$s_download_url = $output->get('thumbnail_abs_url');
			}
			else {
				$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
				$o_file_model = \X2board\Includes\getModel('file');
				// $this->add('download_url',$o_file_model->getDownloadUrl($output->get('file_id'), $output->get('sid'), $n_board_id, $n_board_id)); // $module_srl));
				$s_download_url = $o_file_model->getDownloadUrl($output->get('file_id'), $output->get('sid'), $n_board_id, $n_board_id);
				unset($o_file_model);
			}

			// if($file['size']){
			// 	// 사진 메타데이터 추출
			// 	require_once(ABSPATH . 'wp-admin/includes/image.php');
			// 	$metadata = wp_read_image_metadata("{$this->abspath}{$this->path}/{$file_unique_name}");
			// 	if(!$metadata){
			// 		$metadata = array();
			// 	}
				
			// 	$this->imageOrientation("{$this->abspath}{$this->path}/{$file_unique_name}");
				
			// 	return apply_filters('kboard_uploaded_file', array(
			// 		'stored_name' => $file_unique_name,
			// 		'original_name' => sanitize_file_name($file['name']),
			// 		'temp_name' => $file['tmp_name'],
			// 		'error' => $file['error'],
			// 		'type' => $file['type'],
			// 		'size' => $file['size'],
			// 		'path' => $this->path,
			// 		'metadata' => $metadata
			// 	), $this->name);
			// }
			// else{
			// 	return array(
			// 		'stored_name' => '',
			// 		'original_name' => '',
			// 		'temp_name' => '',
			// 		'error' => '',
			// 		'type' => '',
			// 		'size' => '',
			// 		'path' => '',
			// 		'metadata' => array()
			// 	);
			// }

			if($output->error != '0') {
				$this->stop($output->message);
			}

			$a_uploaded_file_info = array();
			$a_uploaded_file_info['file_id'] = $output->get('file_id');
			$a_uploaded_file_info['thumbnail_abs_url'] = $s_download_url;
			$a_uploaded_file_info['file_type'] = $output->get('file_type');
			$a_uploaded_file_info['file_size'] = $output->get('file_size');
			$a_uploaded_file_info['error'] = '';  // for ajax uploading error msg
			
			$upload_attach_files = array();  // reserved for multiple upload
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
		 *
		 * @param object $file_info PHP file information array
		 * @param int $module_srl Sequence of module to upload file
		 * @param int $upload_target_srl Sequence of target to upload file
		 * @param int $download_count Initial download count
		 * @param bool $manual_insert If set true, pass validation check
		 * @return BaseObject
		 */
		// function insertFile($file_info, $module_srl, $upload_target_srl, $download_count = 0, $manual_insert = false)
		private function _insert_file($file_info, $n_board_id, $upload_target_id, $download_count = 0, $manual_insert = false) {
			// Call a trigger (before)
			// $trigger_obj = new stdClass;
			// $trigger_obj->module_srl = $module_srl;
			// $trigger_obj->upload_target_srl = $upload_target_srl;
			// $output = ModuleHandler::triggerCall('file.insertFile', 'before', $trigger_obj);
			// if(!$output->toBool()) return $output;

			// A workaround for Firefox upload bug
			if(preg_match('/^=\?UTF-8\?B\?(.+)\?=$/i', $file_info['name'], $match))	{
				$file_info['name'] = base64_decode(strtr($match[1], ':', '/'));
			}

			$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if(!$manual_insert)	{
				// Get the file configurations
				if($logged_info->is_admin != 'Y') {
					$oFileModel = getModel('file');
					$config = $oFileModel->getFileConfig($module_srl);

					// check file type
					if(isset($config->allowed_filetypes) && $config->allowed_filetypes !== '*.*') {
						$filetypes = explode(';', $config->allowed_filetypes);
						$ext = array();
						foreach($filetypes as $item) {
							$item = explode('.', $item);
							$ext[] = strtolower($item[1]);
						}
						$uploaded_ext = explode('.', $file_info['name']);
						$uploaded_ext = strtolower(array_pop($uploaded_ext));

						if(!in_array($uploaded_ext, $ext)) {
							return $this->stop(__('msg_not_allowed_filetype', 'x2board'));
						}
					}

					$allowed_filesize = $config->allowed_filesize * 1024 * 1024;
					$allowed_attach_size = $config->allowed_attach_size * 1024 * 1024;
					// An error appears if file size exceeds a limit
					if($allowed_filesize < filesize($file_info['tmp_name'])) {
						return new \X2board\Includes\Classes\BaseObject(-1, __('msg_exceeds_limit_size', 'x2board') );
					}
					// Get total file size of all attachements (from DB)
					$size_args = new \stdClass;
					$size_args->upload_target_id = $upload_target_id;
					$output = executeQuery('file.getAttachedFileSize', $size_args);
					$attached_size = (int)$output->data->attached_size + filesize($file_info['tmp_name']);
					if($attached_size > $allowed_attach_size) {
						return new \X2board\Includes\Classes\BaseObject(-1, __('msg_exceeds_limit_size', 'x2board') );
					}
				}
			}

			// https://github.com/xpressengine/xe-core/issues/1713
			$file_info['name'] = preg_replace('/\.((ph(p|t|ar)?[0-9]?|p?html?|cgi|pl|exe|(?:a|j)sp|inc).*)$/i', '$0-x',$file_info['name']);
			$file_info['name'] = \X2board\Includes\removeHackTag($file_info['name']);
			$file_info['name'] = str_replace(array('<','>'),array('%3C','%3E'),$file_info['name']);
			$file_info['name'] = str_replace('&amp;', '&', $file_info['name']);

			// Get random number generator
			$o_random = new \X2board\Includes\Classes\Security\Password();

			$s_attach_path = wp_get_upload_dir()['basedir'].DIRECTORY_SEPARATOR.X2B_ATTACH_FILE_PATH;
			$s_attach_rand_dir = \X2board\Includes\getNumberingPath($upload_target_id,3);

			// Set upload path by checking if the attachement is an image or other kinds of file
			if(preg_match("/\.(jpe?g|gif|png|wm[va]|mpe?g|avi|flv|mp[1-4]|as[fx]|wav|midi?|moo?v|qt|r[am]{1,2}|m4v)$/i", $file_info['name'])) {
				// $path = sprintf("./files/attach/images/%s/%s", $n_board_id,\X2board\Includes\getNumberingPath($upload_target_id,3));
				$s_path = $s_attach_path.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$n_board_id.DIRECTORY_SEPARATOR.$s_attach_rand_dir;
				
				// special character to '_'
				// change to random file name. because window php bug. window php is not recognize unicode character file name - by cherryfilter
				$ext = substr(strrchr($file_info['name'],'.'),1);
				//$_filename = preg_replace('/[#$&*?+%"\']/', '_', $file_info['name']);
				$_filename = $o_random->create_secure_salt(32, 'hex').'.'.$ext;
				$filename  = $s_path.$_filename;
				$idx = 1;
				while(file_exists($filename)) {
					$filename = $path.preg_replace('/\.([a-z0-9]+)$/i','_'.$idx.'.$1',$_filename);
					$idx++;
				}
				$direct_download = 'Y';
				$s_file_type = 'image';
			}
			else { 
				// $path = sprintf("./files/attach/binaries/%s/%s", $n_board_id, \X2board\Includes\getNumberingPath($upload_target_id,3));
				$s_path = $s_attach_path.DIRECTORY_SEPARATOR.'binaries'.DIRECTORY_SEPARATOR.$n_board_id.DIRECTORY_SEPARATOR.$s_attach_rand_dir; //\X2board\Includes\getNumberingPath($upload_target_id,3);
				$filename = $s_path.$o_random->create_secure_salt(32, 'hex');
				$direct_download = 'N';
				$s_file_type = 'binary';
			}

			$s_url = explode('/wp-content/', $filename);
// error_log(print_r(get_site_url().'/wp-content/'.$s_url[1], true));
			// if(!FileHandler::makeDir($path)) {
			if( !file_exists( $s_path ) ) {
				if(!wp_mkdir_p( $s_path ) ){
					return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted_create', 'x2board') );
				}
			}

			// Check uploaded file
			if(!$manual_insert && !\X2board\Includes\checkUploadedFile($file_info['tmp_name'], $file_info['name'])) {
				return new \X2board\Includes\Classes\BaseObject(-1, __('msg_file_upload_error', 'x2board') );
			}
			
			// Move the file
			if($manual_insert) {
				@copy($file_info['tmp_name'], $filename);
				if(!file_exists($filename)) {
					$filename = $s_path.$o_random->create_secure_salt(32, 'hex').'.'.$ext;
					@copy($file_info['tmp_name'], $filename);
				}
			}
			else {
				if(!@move_uploaded_file($file_info['tmp_name'], $filename)) {
					$filename = $s_path.$o_random->create_secure_salt(32, 'hex').'.'.$ext;
					if(!@move_uploaded_file($file_info['tmp_name'], $filename)) {
						return new \X2board\Includes\Classes\BaseObject(-1, __('msg_file_upload_error', 'x2board') );
					}
				}
			}

			if( $s_file_type == 'image' ) {
				$thumbnail_abs_url = get_site_url().'/wp-content/'.$s_url[1];
			}
			else {
				$thumbnail_abs_url = plugins_url().'/x2board/images/file.png';
			}
	
			// file information
			$a_new_file['file_id'] = \X2board\Includes\getNextSequence();
			$a_new_file['upload_target_id'] = $upload_target_id;
			$a_new_file['board_id'] = $n_board_id;
			$a_new_file['direct_download'] = $direct_download;
			$a_new_file['source_filename'] = $file_info['name'];
			$a_new_file['uploaded_filename'] = $filename;
			$a_new_file['download_count'] = $download_count;
			$a_new_file['file_size'] = @filesize($filename);
			$a_new_file['author'] = $logged_info->ID; 
			$a_new_file['sid'] = $o_random->create_secure_salt(32, 'hex');
			$a_new_file['regdate'] = date('Y-m-d H:i:s', current_time('timestamp')); 
			$a_new_file['ipaddress'] = \X2board\Includes\get_remote_ip();
			// $output = executeQuery('file.insertFile', $args);
			// if(!$output->toBool()) {
			// 	return $output;
			// }
			global $wpdb;
			$result = $wpdb->insert("{$wpdb->prefix}x2b_files", $a_new_file);
			if( $result < 0 || $result === false ){
				unset($a_new_file);
				unset($result);
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
			unset($result);

			// Call a trigger (after)
			// $trigger_output = ModuleHandler::triggerCall('file.insertFile', 'after', $args);
			// if(!$trigger_output->toBool()) return $trigger_output;
			$_SESSION['__X2B_UPLOADING_FILES_INFO__'][$a_new_file['file_id']] = true;

			$o_rst = new \X2board\Includes\Classes\BaseObject();
			$o_rst->add('file_id', $a_new_file['file_id'] );
			$o_rst->add('file_size', $a_new_file['file_size'] );
			$o_rst->add('file_type', $s_file_type);
			$o_rst->add('direct_download', $a_new_file['direct_download']);
			$o_rst->add('thumbnail_abs_url', $thumbnail_abs_url);
			// $o_rst->add('sid', $o_args->sid);
			// $o_rst->add('source_filename', $o_args->source_filename);
			// $o_rst->add('upload_target_id', $o_args->upload_target_id);
			// $o_rst->add('uploaded_filename', $o_args->uploaded_filename);
			unset($a_new_file);
			return $o_rst;
		}

		/**
		 * Delete an attachment from the editor
		 *
		 * @return BaseObject
		 */
		// function procFileDelete() {
		public function proc_file_delete() {
			// Basic variable setting(upload_target_srl and module_srl set)
			$n_post_id = intval(\X2board\Includes\Classes\Context::get('post_id'));
			$editor_sequence = $n_post_id;  // $editor_sequence = Context::get('editor_sequence');
			// $upload_target_id = $n_post_id;
		
			$file_id = \X2board\Includes\Classes\Context::get('file_id');
			// $file_srls = \X2board\Includes\Classes\Context::get('file_srls');
			// if($file_srls) $file_srl = $file_srls;
			// Exit a session if there is neither upload permission nor information
// error_log(print_r($n_post_id, true));
// error_log(print_r($_SESSION['x2b_upload_info'], true));
			if(!$_SESSION['x2b_upload_info'][$editor_sequence]->enabled) {
				exit();
			}

			$upload_target_id = $_SESSION['x2b_upload_info'][$editor_sequence]->upload_target_id;

			$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			$o_file_model = \X2board\Includes\getModel('file');

			$ids = explode(',',$file_id);
			if(!count($ids)) {
				unset($logged_info);
				unset($o_file_model);
				return;
			}

			global $wpdb;
			for($i=0;$i<count($ids);$i++) {
				$n_id = intval($ids[$i]);
				if(!$n_id) {
					continue;
				}

				// $args = new \stdClass;
				// $args->file_id = $n_id;
				$o_file_info = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `file_id`={$n_id}");
				if ($o_file_info === null) {
					continue;
				} 
				// $output = executeQuery('file.getFile', $args);
				// if(!$output->toBool()) {
				// 	continue;
				// }

				// $file_info = $output->data;
				if(!$o_file_info) {
					continue;
				}

				$file_grant = $o_file_model->get_file_grant($o_file_info);//, $logged_info);

				if(!$file_grant->is_deletable) {
					continue;
				}

				if($upload_target_id && $file_id) {
					$output = $this->_delete_file($o_file_info);
				}
			}
			unset($logged_info);
			unset($o_file_model);
			return $output;
		}

		/**
		 * Delete the single attachment
		 *
		 * @param int $o_file_info a file object to delete
		 * @return BaseObject
		 */
		// function deleteFile($file_srl)
		private function _delete_file($o_file_info) {
			if(!$o_file_info->upload_target_id) {
				return;
			}

			// $ids = (is_array($file_id)) ? $file_id : explode(',', $file_id);
			// if(!count($ids)) {
			// 	return;
			// }

			
			$a_post_id = array();

			// foreach($ids as $id) {
				// $n_id = intval($id);
				// if(!$n_id) {
				// 	continue;
				// }

				// $args = new stdClass();
				// $args->file_srl = $n_id;
				// $output = executeQuery('file.getFile', $args);

				// if(!$output->toBool() || !$output->data) {
				// 	continue;
				// }

				// $file_info = $output->data;

				// if($file_info->upload_target_id) {
					$a_post_id[] = $o_file_info->upload_target_id;
				// }

				// $source_filename = $output->data->source_filename;
				// $uploaded_filename = $output->data->uploaded_filename;

				// Call a trigger (before)
				// $trigger_obj = $output->data;
				// $output = ModuleHandler::triggerCall('file.deleteFile', 'before', $trigger_obj);
				// if(!$output->toBool()) return $output;

				// Remove from the DB
				global $wpdb;
				$result = $wpdb->delete("{$wpdb->prefix}x2b_files", array( 'file_id' => $o_file_info->file_id ));
				if( $result < 0 || $result === false ){
					return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
				}
				unset($result);
				// $output = executeQuery('file.deleteFile', $args);
				// if(!$output->toBool()) return $output;

				// Call a trigger (after)
				// $trigger_output = ModuleHandler::triggerCall('file.deleteFile', 'after', $trigger_obj);
				// if(!$trigger_output->toBool()) return $trigger_output;

				// If successfully deleted, remove the file
				require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
				require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
				$fileSystemDirect = new \WP_Filesystem_Direct(false);
				$fileSystemDirect->delete($o_file_info->uploaded_filename);
				unset($fileSystemDirect);
				// FileHandler::removeFile($o_file_info->uploaded_filename);
			// }
			$o_post_controller = \X2board\Includes\getController('post');
			$o_post_controller->update_uploaded_count($a_post_id);
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Set the attachements of the upload_target_srl to be valid
		 * By changing its state to valid when a document is inserted, it prevents from being considered as a unnecessary file
		 *
		 * @param int $upload_target_id
		 * @return BaseObject
		 */
		// function setFilesValid($upload_target_srl)
		public function set_files_valid($upload_target_id) {
			global $wpdb;
			$result = $wpdb->update( "{$wpdb->prefix}x2b_files", 
									 array ( 'isvalid' => 'Y' ),
									 array ( 'upload_target_id' => esc_sql(intval($upload_target_id)) ) );
			if( $result < 0 || $result === false ) {
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
			return new \X2board\Includes\Classes\BaseObject();
			// $args = new stdClass();
			// $args->upload_target_id = $upload_target_id;
			// return executeQuery('file.updateFileValid', $args);
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
		 *
		 * return void
		 */
		// function procFileDownload()
		public function proc_file_download() {
			$o_grant = \X2board\Includes\Classes\Context::get('grant');
			if(isset($o_grant->access) && $o_grant->access !== true) {
				unset($o_grant);
				// return new \X2board\Includes\Classes\BaseObject(-1, __('msg_not_permitted', 'x2board') );
				wp_die(__('msg_not_permitted', 'x2board'));
			}
			unset($o_grant);

			$file_id = \X2board\Includes\Classes\Context::get('file_id');
			$sid = \X2board\Includes\Classes\Context::get('sid');
			
			// Get file information from the DB
			$columnList = array('file_id', 'sid', 'isvalid', 'source_filename', 'board_id', 'uploaded_filename', 'file_size', 'author', 'upload_target_id', 'upload_target_type');
			
			$o_file_model = \X2board\Includes\getModel('file');
			$file_obj = $o_file_model->get_file($file_id, $columnList);
			unset($o_file_model);
			// If the requested file information is incorrect, an error that file cannot be found appears
			if($file_obj->file_id!=$file_id || $file_obj->sid!=$sid) {
				// return $this->stop('msg_file_not_found');
				wp_die(__('msg_file_not_found', 'x2board'));
			}
			// Notify that file download is not allowed when standing-by(Only a top-administrator is permitted)
			$logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($logged_info->is_admin != 'Y' && $file_obj->isvalid!='Y') {
				// return $this->stop('msg_not_permitted_download');
				wp_die(__('msg_not_permitted_download', 'x2board'));
			}
			unset($logged_info);
			
			// File name
			$filename = $file_obj->source_filename;
			// $file_module_config = $o_file_model->getFileModuleConfig($file_obj->module_srl);

			$o_appending_file_conf = \X2board\Includes\Classes\Context::get('appending_file_config');
			
			// Not allow the file outlink
			if($o_appending_file_conf->file_allow_outlink == 'N') {
				// Handles extension to allow outlink
				if($o_appending_file_conf->file_allow_outlink_format) {
					$allow_outlink_format_array = array();
					$allow_outlink_format_array = explode(',', $o_appending_file_conf->file_allow_outlink_format);
					if(!is_array($allow_outlink_format_array)) {
						$allow_outlink_format_array[0] = $o_appending_file_conf->file_allow_outlink_format;
					}

					foreach($allow_outlink_format_array as $val) {
						$val = trim($val);
						if(preg_match("/\.{$val}$/i", $filename)) {
							$o_appending_file_conf->file_allow_outlink = 'Y';
							break;
						}
					}
				}
				// Sites that outlink is allowed
				if($o_appending_file_conf->file_allow_outlink != 'Y') {
					$referer = parse_url($_SERVER["HTTP_REFERER"]);
					if($referer['host'] != $_SERVER['HTTP_HOST']) {
						if($o_appending_file_conf->file_allow_outlink_site) {
							$allow_outlink_site_array = array();
							$allow_outlink_site_array = explode("\n", $o_appending_file_conf->file_allow_outlink_site);
							if(!is_array($allow_outlink_site_array)) {
								$allow_outlink_site_array[0] = $o_appending_file_conf->file_allow_outlink_site;
							}

							foreach($allow_outlink_site_array as $val) {
								$site = parse_url(trim($val));
								if($site['host'] == $referer['host']) {
									$o_appending_file_conf->file_allow_outlink = 'Y';
									break;
								}
							}
						}
					}
					else {
						$o_appending_file_conf->file_allow_outlink = 'Y';
					}
				}
				if($o_appending_file_conf->file_allow_outlink != 'Y') {
					// return $this->stop('msg_not_allowed_outlink');
					wp_die(__('msg_not_allowed_outlink', 'x2board'));
				}
			}

			// Check if a permission for file download is granted
			$downloadGrantCount = 0;
			if(is_array($o_appending_file_conf->file_download_grant)) {
				foreach($o_appending_file_conf->file_download_grant AS $value) {
					if($value) {
						$downloadGrantCount++;
					}
				}
					
			}

			if(is_array($o_appending_file_conf->file_download_grant) && $downloadGrantCount>0) {
				if(!\X2board\Includes\Classes\Context::get('is_logged')) {
					// return $this->stop('msg_not_permitted_download');
					wp_die(__('msg_not_permitted_download', 'x2board'));
				}

				// $logged_info = \X2board\Includes\Classes\Context::get('logged_info');
				// if($logged_info->is_admin != 'Y') {
				// 	$oModuleModel =& \X2board\Includes\getModel('module');
				// 	$columnList = array('module_srl', 'site_srl');
				// 	$module_info = $oModuleModel->getModuleInfoByModuleSrl($file_obj->module_srl, $columnList);

				// 	if(!$oModuleModel->isSiteAdmin($logged_info, $module_info->site_srl)) {
				// 		$oMemberModel =& \X2board\Includes\getModel('member');
				// 		$member_groups = $oMemberModel->getMemberGroups($logged_info->member_srl, $module_info->site_srl);
				// 		unset($oMemberModel);
				// 		$is_permitted = false;
				// 		for($i=0;$i<count($o_appending_file_conf->file_download_grant);$i++) {
				// 			$group_srl = $o_appending_file_conf->file_download_grant[$i];
				// 			if($member_groups[$group_srl]) {
				// 				$is_permitted = true;
				// 				break;
				// 			}
				// 		}
				// 		if(!$is_permitted) {
				// 			return $this->stop('msg_not_permitted_download');
				// 		}
				// 	}
				// }
			}
			
			// Call a trigger (before)
			// $output = ModuleHandler::triggerCall('file.downloadFile', 'before', $file_obj);
			// if(!$output->toBool()) return $this->stop(($output->message)?$output->message:'msg_not_permitted_download');


			// 다운로드 후 (가상)
			// Increase download_count
			// $args = new \stdClass();
			// $args->file_id = $file_id;
			// executeQuery('file.updateFileDownloadCount', $args);
			global $wpdb;
			$query = "UPDATE `{$wpdb->prefix}x2b_files` SET `download_count`=`download_count`+1 WHERE `file_id`='".esc_sql(intval($file_id))."'";
			if ($wpdb->query($query) === FALSE) {
				// return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
				wp_die($wpdb->last_error);
			} 

			// Call a trigger (after)
			// $output = ModuleHandler::triggerCall('file.downloadFile', 'after', $file_obj);
			
			$o_random = new \X2board\Includes\Classes\Security\Password();
			$file_key = $_SESSION['__X2B_FILE_KEY__'][$file_id] = $o_random->create_secure_salt(32, 'hex');
			unset($o_random);
			$board_id = \X2board\Includes\Classes\Context::get('board_id');
			header('Location: '.\X2board\Includes\getNotEncodedUrl('', 'cmd', X2B_CMD_PROC_OUTPUT_FILE,'board_id',$board_id,'file_id',$file_id,'file_key',$file_key));
			\X2board\Includes\Classes\Context::close();
			exit();
		}

		// public function procFileOutput() {
		public function proc_file_output() {
			$file_id = \X2board\Includes\Classes\Context::get('file_id');
			$file_key = \X2board\Includes\Classes\Context::get('file_key');
			if(strstr($_SERVER['HTTP_USER_AGENT'], "Android")) {
				$is_android = true;
			}

			if($is_android && $_SESSION['__XE_FILE_KEY_AND__'][$file_id]) {
				$session_key = '__X2B_FILE_KEY_AND__';
			}
			else {
				$session_key = '__X2B_FILE_KEY__';
			}
			$columnList = array('source_filename', 'uploaded_filename', 'file_size');

			$o_file_model = \X2board\Includes\getModel('file');
			$file_obj = $o_file_model->get_file($file_id, $columnList);
			unset($o_file_model);

			$uploaded_filename = $file_obj->uploaded_filename;

			if(!file_exists($uploaded_filename)) {
				// return $this->stop('msg_file_not_found');
				wp_die(__('msg_file_not_found', 'x2board'));
			}

			if(!$file_key || $_SESSION[$session_key][$file_id] != $file_key) {
				unset($_SESSION[$session_key][$file_id]);
				// return $this->stop('msg_invalid_request');
				wp_die(__('msg_invalid_request', 'x2board'));
			}

			$file_size = $file_obj->file_size;
			$filename = $file_obj->source_filename;
			
			if(preg_match('#(?:Chrome|Edge)/(\d+)\.#', $_SERVER['HTTP_USER_AGENT'], $matches) && $matches[1] >= 11) {
				if($is_android && preg_match('#\bwv\b|(?:Version|Browser)/\d+#', $_SERVER['HTTP_USER_AGENT'])) {
					$filename_param = 'filename="' . $filename . '"';
				}
				else {
					$filename_param = sprintf('filename="%s"; filename*=UTF-8\'\'%s', $filename, rawurlencode($filename));
				}
			}
			elseif(preg_match('#(?:Firefox|Safari|Trident)/(\d+)\.#', $_SERVER['HTTP_USER_AGENT'], $matches) && $matches[1] >= 6) {
				$filename_param = sprintf('filename="%s"; filename*=UTF-8\'\'%s', $filename, rawurlencode($filename));
			}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {
				$filename = rawurlencode($filename);
				$filename_param = 'filename="' . preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1) . '"';
			}
			else {
				$filename_param = 'filename="' . $filename . '"';
			}

			if($is_android) {
				if($_SESSION['__X2B_FILE_KEY__'][$file_id]) {
					$_SESSION['__X2B_FILE_KEY_AND__'][$file_id] = $file_key;
				}
			}

			unset($_SESSION[$session_key][$file_id]);

			\X2board\Includes\Classes\Context::close();

			$fp = fopen($uploaded_filename, 'rb');
			if(!$fp) {
				// return $this->stop('msg_file_not_found');
				wp_die(__('msg_file_not_found', 'x2board'));
			}

			header("Cache-Control: ");
			header("Pragma: ");
			header("Content-Type: application/octet-stream");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			header("Content-Length: " .(string)($file_size));
			header('Content-Disposition: attachment; ' . $filename_param);
			header("Content-Transfer-Encoding: binary\n");

			// if file size is lager than 10MB, use fread function (#18675748)
			if($file_size > 1024 * 1024) {
				while(!feof($fp)) echo fread($fp, 1024);
				fclose($fp);
			}
			else {
				fpassthru($fp);
			}
			exit();
		}


		
////////////////////////////////////
		/**
		 * get file list
		 *
		 * @return BaseObject
		 */
		// function procFileGetList()
		// {
		// 	if(!Context::get('is_logged')) return new BaseObject(-1,'msg_not_permitted');

		// 	$oModuleModel = getModel('module');

		// 	$logged_info = Context::get('logged_info');
		// 	if($logged_info->is_admin !== 'Y' && !$oModuleModel->isSiteAdmin($logged_info))
		// 	{
		// 		return new BaseObject(-1, 'msg_not_permitted');
		// 	}

		// 	$fileSrls = Context::get('file_srls');
		// 	if($fileSrls) $fileSrlList = explode(',', $fileSrls);

		// 	global $lang;
		// 	if(count($fileSrlList) > 0)
		// 	{
		// 		$oFileModel = getModel('file');
		// 		$fileList = $oFileModel->getFile($fileSrlList);
		// 		if(!is_array($fileList)) $fileList = array($fileList);

		// 		if(is_array($fileList))
		// 		{
		// 			foreach($fileList AS $key=>$value)
		// 			{
		// 				$value->human_file_size = FileHandler::filesize($value->file_size);
		// 				if($value->isvalid=='Y') $value->validName = $lang->is_valid;
		// 				else $value->validName = $lang->is_stand_by;
		// 			}
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$fileList = array();
		// 		$this->setMessage($lang->no_files);
		// 	}

		// 	$this->add('file_list', $fileList);
		// }

		/**
		 * Delete all attachments of a particular document
		 *
		 * @param int $upload_target_srl Upload target srl to delete files
		 * @return BaseObject
		 */
		// function deleteFiles($upload_target_srl)
		// {
		// 	// Get a list of attachements
		// 	$oFileModel = getModel('file');
		// 	$columnList = array('file_srl', 'uploaded_filename', 'module_srl');
		// 	$file_list = $oFileModel->getFiles($upload_target_srl, $columnList);
		// 	// Success returned if no attachement exists
		// 	if(!is_array($file_list)||!count($file_list)) return new BaseObject();

		// 	// Delete the file
		// 	$path = array();
		// 	$file_count = count($file_list);
		// 	for($i=0;$i<$file_count;$i++)
		// 	{
		// 		$this->deleteFile($file_list[$i]->file_srl);

		// 		$uploaded_filename = $file_list[$i]->uploaded_filename;
		// 		$path_info = pathinfo($uploaded_filename);
		// 		if(!in_array($path_info['dirname'], $path)) $path[] = $path_info['dirname'];
		// 	}

		// 	// Remove from the DB
		// 	$args = new stdClass();
		// 	$args->upload_target_srl = $upload_target_srl;
		// 	$output = executeQuery('file.deleteFiles', $args);
		// 	if(!$output->toBool()) return $output;
			
		// 	// Remove a file directory of the document
		// 	for($i=0, $c=count($path); $i<$c; $i++)
		// 	{
		// 		FileHandler::removeBlankDir($path[$i]);
		// 	}

		// 	return $output;
		// }

		/**
		 * Move an attachement to the other document
		 *
		 * @param int $source_srl Sequence of target to move
		 * @param int $target_module_srl New squence of module
		 * @param int $target_srl New sequence of target
		 * @return void
		 */
		// function moveFile($source_srl, $target_module_srl, $target_srl)
		// {
		// 	if($source_srl == $target_srl) return;

		// 	$oFileModel = getModel('file');
		// 	$file_list = $oFileModel->getFiles($source_srl);
		// 	if(!$file_list) return;

		// 	$file_count = count($file_list);
	
		// 	for($i=0;$i<$file_count;$i++)
		// 	{
		// 		unset($file_info);
		// 		$file_info = $file_list[$i];
		// 		$old_file = $file_info->uploaded_filename;
		// 		// Determine the file path by checking if the file is an image or other kinds
		// 		if(preg_match("/\.(asf|asf|asx|avi|flv|gif|jpeg|jpg|m4a|m4v|mid|midi|moov|mov|mp1|mp2|mp3|mp4|mpeg|mpg|ogg|png|qt|ra|ram|rm|rmm|wav|webm|webp|wma|wmv)$/i", $file_info->source_filename))
		// 		{
		// 			$path = sprintf("./files/attach/images/%s/%s/", $target_module_srl,$target_srl);
		// 			$new_file = $path.$file_info->source_filename;
		// 		}
		// 		else
		// 		{
		// 			$path = sprintf("./files/attach/binaries/%s/%s/", $target_module_srl, $target_srl);
		// 			$random = new Password();
		// 			$new_file = $path.$random->createSecureSalt(32, 'hex');
		// 		}
		// 		// Pass if a target document to move is same
		// 		if($old_file == $new_file) continue;
		// 		// Create a directory
		// 		FileHandler::makeDir($path);
		// 		// Move the file
		// 		FileHandler::rename($old_file, $new_file);
		// 		// Update DB information
		// 		$args = new stdClass;
		// 		$args->file_srl = $file_info->file_srl;
		// 		$args->uploaded_filename = $new_file;
		// 		$args->module_srl = $file_info->module_srl;
		// 		$args->upload_target_srl = $target_srl;
		// 		executeQuery('file.updateFile', $args);
		// 	}
		// }

		// public function procFileSetCoverImage()
		// {
		// 	$vars = Context::getRequestVars();
		// 	$logged_info = Context::get('logged_info');

		// 	if(!$vars->editor_sequence) return new BaseObject(-1, 'msg_invalid_request');

		// 	$upload_target_srl = $_SESSION['upload_info'][$vars->editor_sequence]->upload_target_srl;

		// 	$oFileModel = getModel('file');
		// 	$file_info = $oFileModel->getFile($vars->file_srl);

		// 	if(!$file_info) return new BaseObject(-1, 'msg_not_founded');

		// 	if(!$this->manager && !$file_info->member_srl === $logged_info->member_srl) return new BaseObject(-1, 'msg_not_permitted');

		// 	$args =  new stdClass();
		// 	$args->file_srl = $vars->file_srl;
		// 	$args->upload_target_srl = $upload_target_srl;

		// 	$oDB = &DB::getInstance();
		// 	$oDB->begin();
			
		// 	$args->cover_image = 'N';
		// 	$output = executeQuery('file.updateClearCoverImage', $args);
		// 	if(!$output->toBool())
		// 	{
		// 			$oDB->rollback();
		// 			return $output;
		// 	}

		// 	if($file_info->cover_image != 'Y')
		// 	{

		// 		$args->cover_image = 'Y';
		// 		$output = executeQuery('file.updateCoverImage', $args);
		// 		if(!$output->toBool())
		// 		{
		// 			$oDB->rollback();
		// 			return $output;
		// 		}

		// 	}

		// 	$oDB->commit();

		// 	$this->add('is_cover',$args->cover_image);

		// 	// 썸네일 삭제
		// 	$thumbnail_path = sprintf('files/thumbnails/%s', getNumberingPath($upload_target_srl, 3));
		// 	Filehandler::removeFilesInDir($thumbnail_path);
		// }

		/**
		 * A trigger to return numbers of attachments in the upload_target_srl (document_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerCheckAttached(&$obj)
		// {
		// 	$document_srl = $obj->document_srl;
		// 	if(!$document_srl) return new BaseObject();
		// 	// Get numbers of attachments
		// 	$oFileModel = getModel('file');
		// 	$obj->uploaded_count = $oFileModel->getFilesCount($document_srl);

		// 	return new BaseObject();
		// }

		/**
		 * A trigger to link the attachment with the upload_target_srl (document_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerAttachFiles(&$obj)
		// {
		// 	$document_srl = $obj->document_srl;
		// 	if(!$document_srl) return new BaseObject();

		// 	$output = $this->setFilesValid($document_srl);
		// 	if(!$output->toBool()) return $output;

		// 	return new BaseObject();
		// }

		/**
		 * A trigger to delete the attachment in the upload_target_srl (document_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerDeleteAttached(&$obj)
		// {
		// 	$document_srl = $obj->document_srl;
		// 	if(!$document_srl) return new BaseObject();

		// 	$output = $this->deleteFiles($document_srl);
		// 	return $output;
		// }

		/**
		 * A trigger to return numbers of attachments in the upload_target_srl (comment_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerCommentCheckAttached(&$obj)
		// {
		// 	$comment_srl = $obj->comment_srl;
		// 	if(!$comment_srl) return new BaseObject();
		// 	// Get numbers of attachments
		// 	$oFileModel = getModel('file');
		// 	$obj->uploaded_count = $oFileModel->getFilesCount($comment_srl);

		// 	return new BaseObject();
		// }

		/**
		 * A trigger to link the attachment with the upload_target_srl (comment_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerCommentAttachFiles(&$obj)
		// {
		// 	$comment_srl = $obj->comment_srl;
		// 	$uploaded_count = $obj->uploaded_count;
		// 	if(!$comment_srl || !$uploaded_count) return new BaseObject();

		// 	$output = $this->setFilesValid($comment_srl);
		// 	if(!$output->toBool()) return $output;

		// 	return new BaseObject();
		// }

		/**
		 * A trigger to delete the attachment in the upload_target_srl (comment_srl)
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerCommentDeleteAttached(&$obj)
		// {
		// 	$comment_srl = $obj->comment_srl;
		// 	if(!$comment_srl) return new BaseObject();

		// 	if($obj->isMoveToTrash) return new BaseObject();

		// 	$output = $this->deleteFiles($comment_srl);
		// 	return $output;
		// }

		/**
		 * A trigger to delete all the attachements when deleting the module
		 *
		 * @param object $obj Trigger object
		 * @return BaseObject
		 */
		// function triggerDeleteModuleFiles(&$obj)
		// {
		// 	$module_srl = $obj->module_srl;
		// 	if(!$module_srl) return new BaseObject();

		// 	$oFileController = getAdminController('file');
		// 	return $oFileController->deleteModuleFiles($module_srl);
		// }

		/**
		 * Iframe upload attachments
		 *
		 * @return BaseObject
		 */
		// function procFileIframeUpload()
		// {
		// 	// Basic variables setting
		// 	$editor_sequence = Context::get('editor_sequence');
		// 	$callback = Context::get('callback');
		// 	$module_srl = $this->module_srl;
		// 	$upload_target_srl = intval(Context::get('uploadTargetSrl'));
		// 	if(!$upload_target_srl) $upload_target_srl = intval(Context::get('upload_target_srl'));

		// 	// Exit a session if there is neither upload permission nor information
		// 	if(!$_SESSION['upload_info'][$editor_sequence]->enabled) exit();
		// 	// Extract from session information if upload_target_srl is not specified
		// 	if(!$upload_target_srl) $upload_target_srl = $_SESSION['upload_info'][$editor_sequence]->upload_target_srl;
		// 	// Create if upload_target_srl is not defined in the session information
		// 	if(!$upload_target_srl) $_SESSION['upload_info'][$editor_sequence]->upload_target_srl = $upload_target_srl = getNextSequence();

		// 	// Delete and then attempt to re-upload if file_srl is requested
		// 	$file_srl = Context::get('file_srl');
		// 	if($file_srl)
		// 	{
		// 		$oFileModel = getModel('file');
		// 		$logged_info = Context::get('logged_info');
		// 		$file_info = $oFileModel->getFile($file_srl);
		// 		$file_grant = $oFileModel->getFileGrant($file_info, $logged_info);
		// 		if($file_info->file_srl == $file_srl && $file_grant->is_deletable)
		// 		{
		// 			$this->deleteFile($file_srl);
		// 		}
		// 	}

		// 	$file_info = Context::get('Filedata');
		// 	// An error appears if not a normally uploaded file
		// 	if(is_uploaded_file($file_info['tmp_name'])) {
		// 		$output = $this->insertFile($file_info, $module_srl, $upload_target_srl);
		// 		Context::set('uploaded_fileinfo',$output);
		// 	}

		// 	Context::set('layout','none');

		// 	$this->setTemplatePath($this->module_path.'tpl');
		// 	$this->setTemplateFile('iframe');
		// }

		/**
		 * Image resize
		 *
		 * @return BaseObject
		 */
		// function procFileImageResize()
		// {
		// 	$file_srl = Context::get('file_srl');
		// 	$width = Context::get('width');
		// 	$height = Context::get('height');

		// 	if(!$file_srl || !$width)
		// 	{
		// 		return new BaseObject(-1,'msg_invalid_request');
		// 	}

		// 	$oFileModel = getModel('file');
		// 	$fileInfo = $oFileModel->getFile($file_srl);
		// 	if(!$fileInfo || $fileInfo->direct_download != 'Y')
		// 	{
		// 		return new BaseObject(-1,'msg_invalid_request');
		// 	}

		// 	$source_src = $fileInfo->uploaded_filename;
		// 	$output_src = $source_src . '.resized' . strrchr($source_src,'.');

		// 	if(!$height) $height = $width-1;

		// 	if(FileHandler::createImageFile($source_src,$output_src,$width,$height,'','ratio'))
		// 	{
		// 		$output = new stdClass();
		// 		$output->info = getimagesize($output_src);
		// 		$output->src = $output_src;
		// 	}
		// 	else
		// 	{
		// 		return new BaseObject(-1,'msg_invalid_request');
		// 	}

		// 	$this->add('resized_info',$output);
		// }

		/**
		 * Find the attachment where a key is upload_target_srl and then return java script code
		 *
		 * @deprecated
		 * @param int $editor_sequence
		 * @param int $upload_target_srl
		 * @return void
		 */
		// function printUploadedFileList($editor_sequence, $upload_target_srl)
		// {
		// 	return;
		// }

		// function triggerCopyModule(&$obj)
		// {
		// 	$oModuleModel = getModel('module');
		// 	$fileConfig = $oModuleModel->getModulePartConfig('file', $obj->originModuleSrl);

		// 	$oModuleController = getController('module');
		// 	if(is_array($obj->moduleSrlList))
		// 	{
		// 		foreach($obj->moduleSrlList AS $key=>$moduleSrl)
		// 		{
		// 			$oModuleController->insertModulePartConfig('file', $moduleSrl, $fileConfig);
		// 		}
		// 	}
		// }
		}
}
/* End of file file.controller.php */