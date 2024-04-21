<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * Model class of the file module
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\File\\fileModel')) {

	class fileModel extends file
	{
		/**
		 * Initialization
		 * @return void
		 */
		function init() {}

		/**
		 * Return messages for file upload and it depends whether an admin is or not
		 *
		 * @param int $attached_size
		 * @return string
		 */
		// function getUploadStatus($attached_size = 0)
		public function get_upload_status($attached_size = 0) {
			$file_config = $this->get_upload_config();
			// Display upload status
			$upload_status = sprintf(
				'%s : %s/ %s<br /> %s : %s (%s : %s)',
				__('allowed_attach_size', 'x2board'), //Context::getLang('allowed_attach_size'),
				\X2board\Includes\Classes\FileHandler::filesize($attached_size),
				\X2board\Includes\Classes\FileHandler::filesize($file_config->allowed_attach_size*1024*1024),
				__('allowed_filesize', 'x2board'), //Context::getLang('allowed_filesize'),
				\X2board\Includes\Classes\FileHandler::filesize($file_config->allowed_filesize*1024*1024),
				__('allowed_filetypes', 'x2board'), //Context::getLang('allowed_filetypes'),
				$file_config->allowed_filetypes
			);
			return $upload_status;
		}

		/**
		 * Returns a grant of file
		 *
		 * @param object $file_info The file information to get grant
		 * @param object $member_info The member information to get grant
		 * @return object Returns a grant of file
		 */
		// function getFileGrant($file_info, $member_info)
		public function get_file_grant($o_file_info) { //, $member_info) {
			if(!$o_file_info) {
				return null;
			}
			$file_grant = new \stdClass();
			if($_SESSION['__X2B_UPLOADING_FILES_INFO__'][$o_file_info->file_id]) {
				$file_grant->is_deletable = true;
				return $file_grant;
			}

			// $oModuleModel = \X2board\Includes\getModel('module');
			// $grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($file_info->module_srl), $member_info);
			$grant = \X2board\Includes\Classes\Context::get('grant');

			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($o_file_info->upload_target_id);
			unset($o_post_model);
			if($o_post->is_exists()) {
				$document_grant = $o_post->is_granted();
			}
			unset($o_post);

			$member_info = \X2board\Includes\Classes\Context::get('logged_info');
			$file_grant->is_deletable = ($document_grant || $member_info->is_admin == 'Y' || $member_info->ID == $o_file_info->author || $grant->manager);
			unset($member_info);
			unset($document_grant);
			return $file_grant;
		}

		/**
		 * Return number of attachments which belongs to a specific document
		 *
		 * @param int $upload_target_srl The sequence to get a number of files
		 * @return int Returns a number of files
		 */
		// function getFilesCount($upload_target_srl)
		public function get_files_count($n_upload_target_id) {
			global $wpdb;
			$n_file_cnt = $wpdb->get_var("SELECT count(*) as `file_cnt` FROM `{$wpdb->prefix}x2b_files` WHERE `upload_target_id`={$n_upload_target_id}");
			return $n_file_cnt;
			// $args = new stdClass();
			// $args->upload_target_srl = $n_upload_target_id;
			// $output = executeQuery('file.getFilesCount', $args);
			// return (int)$output->data->count;
		}

		/**
		 * Return all files which belong to a specific document
		 *
		 * @param int $upload_target_srl The sequence of target to get file list
		 * @param array $columnList The list of columns to get from DB
		 * @param string $sortIndex The column that used as sort index
		 * @return array Returns array of object that contains file information. If no result returns null.
		 */
		// function getFiles($upload_target_srl, $columnList = array(), $sortIndex = 'file_srl', $ckValid = false)
		public function get_files($upload_target_id, $columnList = array(), $sortIndex = 'file_id', $ckValid = false)	{
			$args = new \stdClass();
			$args->upload_target_id = $upload_target_id;
			$args->sort_index = $sortIndex;
			if($ckValid) {
				$args->isvalid = 'Y';
			}

			global $wpdb;
   			// $rows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts");
			$a_file_list = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `upload_target_id`={$upload_target_id} AND `isvalid` = 'Y' ORDER BY `file_id` ASC");
// var_dump($a_files);
			
			// $output = executeQueryArray('file.getFiles', $args, $columnList);
			// if(!$output->data) {
			// 	return;
			// }

			//$file_list = $output->data;

			// if($file_list && !is_array($file_list)) {
			// 	$file_list = array($file_list);
			// }

			foreach ($a_file_list as &$file) {
				$file->source_filename = stripslashes($file->source_filename);
				$file->source_filename = htmlspecialchars($file->source_filename);
				$file->download_url = $this->_get_download_url($file->file_id, $file->sid); //, $file->board_id);
			}
			return $a_file_list;
		}

		/**
		 * Get a download path
		 *
		 * @param int $file_srl The sequence of file to get url
		 * @param string $sid
		 * @return string Returns a url
		 */
		// function getDownloadUrl($file_srl, $sid, $module_srl="")
		private function _get_download_url($file_id, $sid) {
			return get_the_permalink().'?cmd='.X2B_CMD_PROC_DOWNLOAD_FILE.'&board_id='.get_the_ID().'&file_id='.$file_id.'&sid='.$sid;
			// return sprintf('?module=%s&amp;cmd=%s&amp;file_id=%s&amp;sid=%s&amp;module_srl=%s', 'file', 'procFileDownload', $file_srl, $sid, $module_srl);
		}

		/**
		 * Get file information
		 *
		 * @param int $file_srl The sequence of file to get information
		 * @param array $columnList The list of columns to get from DB
		 * @return BaseObject|object|array If error returns an instance of BaseObject. If result set is one returns a object that contins file information. If result set is more than one returns array of object.
		 */
		// function getFile($file_srl, $columnList = array())
		public function get_file($file_id, $columnList = array()) {
			// $args = new stdClass();
			// $args->file_id = $file_id;
			global $wpdb;
			$o_file = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `file_id`={$file_id} AND `isvalid` = 'Y'");
			if( $o_file === false ){
				unset($o_file);
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
			// $output = executeQueryArray('file.getFile', $args, $columnList);
			// if(!$output->toBool()) {
			// 	return $output;
			// }

			// old version compatibility
			// if(count($output->data) == 1) {
				// $file = $output->data[0];
				$o_file->download_url = $this->_get_download_url($o_file->file_id, $o_file->sid, $o_file->board_id);
				return $o_file;
			// }
			// else {
			// 	$fileList = array();
			// 	if(is_array($output->data)) {
			// 		foreach($output->data as $key=>$value) {
			// 			$file = $value;
			// 			$file->download_url = $this->getDownloadUrl($file->file_id, $file->sid, $file->board_id);
			// 			$fileList[] = $file;
			// 		}
			// 	}
			// 	return $fileList;
			// }
		}

		/**
		 * Return configurations of the attachement (it automatically checks if an administrator is)
		 *
		 * @return object Returns a file configuration of current module. If user is admin, returns PHP's max file size and allow all file types.
		 */
		// function getUploadConfig()
		public function get_upload_config() {
			$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			// Get the current board_id if board_id doesn't exist
			// if(!$n_board_id) {
			// 	$o_current_board_info = \X2board\Includes\Classes\Context::get('current_module_info');
			// 	$n_board_id = $o_current_board_info->board_id;
			// 	unset($o_current_board_info);
			// }
			$o_file_config = $this->_get_file_config($n_board_id);

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y') {
				$iniPostMaxSize = \X2board\Includes\Classes\FileHandler::returnbytes(ini_get('post_max_size'));
				$iniUploadMaxSize = \X2board\Includes\Classes\FileHandler::returnbytes(ini_get('upload_max_filesize'));
				$size = min($iniPostMaxSize, $iniUploadMaxSize) / 1048576;
				$o_file_config->allowed_attach_size = $size;
				$o_file_config->allowed_filesize = $size;
				$o_file_config->allowed_filetypes = '*.*';
			}
			return $o_file_config;
		}

		private function _get_default_config() {

			$o_default_config = new \stdClass();
			$o_default_config->allowed_filesize = $this->_n_allowed_filesize;
			$o_default_config->allowed_attach_size = $this->_n_allowed_attach_size;
			$o_default_config->allowed_filetypes = $this->_s_allowed_filetypes;
			$o_default_config->allow_outlink = $this->_s_allow_outlink;
			$o_default_config->allow_outlink_format = $this->_s_allow_outlink_format;
			$o_default_config->allow_outlink_site = $this->_s_allow_outlink_site;
			return $o_default_config;
		}

		/**
		 * Get file configurations
		 *
		 * @param int $n_board_id If set this, returns specific board's configuration. Otherwise returns global configuration.
		 * @return object Returns configuration.
		 */
		// function getFileConfig($module_srl = null)
		private function _get_file_config($n_board_id = null) {
			// Get configurations (using module model object)
			// $oModuleModel = getModel('module');

			$file_module_config = $this->_get_default_config();  // $oModuleModel->getModuleConfig('file');

			$file_config = new \stdClass();
			// if($module_srl) $file_config = $oModuleModel->getModulePartConfig('file',$module_srl);
			// if(!$file_config) $file_config = $file_module_config;

			if($n_board_id) {
				$o_current_board_info = \X2board\Includes\Classes\Context::get('current_module_info');
// var_Dump($o_current_board_info);
				$file_config->allowed_filesize = $o_current_board_info->file_allowed_filesize_mb;
				$file_config->allowed_attach_size = $o_current_board_info->file_allowed_attach_size_mb;
				$file_config->allowed_filetypes = $o_current_board_info->file_allowed_filetypes;
				$file_config->download_grant = $o_current_board_info->file_download_grant;
				$file_config->allow_outlink = $o_current_board_info->file_allow_outlink;
				$file_config->allow_outlink_site = $o_current_board_info->file_allow_outlink_site;
				$file_config->allow_outlink_format = $o_current_board_info->file_allow_outlink_format;
			}

			$config = new \stdClass();

			if($file_config) {
				$config->allowed_filesize = $file_config->allowed_filesize;
				$config->allowed_attach_size = $file_config->allowed_attach_size;
				$config->allowed_filetypes = $file_config->allowed_filetypes;
				$config->download_grant = $file_config->download_grant;
				$config->allow_outlink = $file_config->allow_outlink;
				$config->allow_outlink_site = $file_config->allow_outlink_site;
				$config->allow_outlink_format = $file_config->allow_outlink_format;
			}
			// Property for all files comes first than each property
			if(!$config->allowed_filesize) {
				$config->allowed_filesize = $file_module_config->allowed_filesize;
			}
			if(!$config->allowed_attach_size) {
				$config->allowed_attach_size = $file_module_config->allowed_attach_size;
			}
			if(!$config->allowed_filetypes) {
				$config->allowed_filetypes = $file_module_config->allowed_filetypes;
			}
			if(!$config->allow_outlink) {
				$config->allow_outlink = $file_module_config->allow_outlink;
			}
			if(!$config->allow_outlink_site) {
				$config->allow_outlink_site = $file_module_config->allow_outlink_site;
			}
			if(!$config->allow_outlink_format) {
				$config->allow_outlink_format = $file_module_config->allow_outlink_format;
			}
			if(!$config->download_grant) {
				$config->download_grant = $file_module_config->download_grant;
			}
			// Default setting if not exists
			if(!$config->allowed_filesize) {
				$config->allowed_filesize = '2';
			}
			if(!$config->allowed_attach_size) {
				$config->allowed_attach_size = '3';
			}
			if(!$config->allowed_filetypes) {
				$config->allowed_filetypes = '*.*';
			}
			if(!$config->allow_outlink) {
				$config->allow_outlink = 'Y';
			}
			if(!$config->download_grant) {
				$config->download_grant = array();
			}

			$size = ini_get('upload_max_filesize');
			$unit = strtolower($size[strlen($size) - 1]);
			$size = (float)$size;
			if($unit == 'g') {
				$size *= 1024;
			}
			if($unit == 'k') {
				$size /= 1024;
			}

			if($config->allowed_filesize > $size) {	
				$config->allowed_filesize = $size;
			}
			if($config->allowed_attach_size > $size) {
				$config->allowed_attach_size = $size;
			}
			return $config;
		}



		
///////////////////////////////////////

		/**
		 * Return a file list attached in the document
		 *
		 * It is used when a file list of the upload_target_srl is requested for creating/updating a document.
		 * Attempt to replace with sever-side session if upload_target_srl is not yet determined
		 *
		 * @return void
		 */
		// function getFileList()
		// {
		// 	$oModuleModel = getModel('module');

		// 	$mid = Context::get('mid');
		// 	$editor_sequence = Context::get('editor_sequence');
		// 	$upload_target_srl = Context::get('upload_target_srl');
		// 	if(!$upload_target_srl) $upload_target_srl = $_SESSION['x2b_upload_info'][$editor_sequence]->upload_target_srl;

		// 	if($upload_target_srl)
		// 	{
		// 		$oDocumentModel = getModel('document');
		// 		$oCommentModel = getModel('comment');
		// 		$logged_info = Context::get('logged_info');

		// 		$oDocument = $oDocumentModel->getDocument($upload_target_srl);

		// 		// comment 권한 확인
		// 		if(!$oDocument->isExists())
		// 		{
		// 			$oComment = $oCommentModel->getComment($upload_target_srl);
		// 			if($oComment->isExists() && $oComment->isSecret() && !$oComment->isGranted())
		// 			{
		// 				return new BaseObject(-1, 'msg_not_permitted');
		// 			}

		// 			$oDocument = $oDocumentModel->getDocument($oComment->get('document_srl'));
		// 		}

		// 		// document 권한 확인
		// 		if($oDocument->isExists() && $oDocument->isSecret() && !$oDocument->isGranted())
		// 		{
		// 			return new BaseObject(-1, 'msg_not_permitted');
		// 		}

		// 		// 모듈 권한 확인
		// 		if($oDocument->isExists())
		// 		{
		// 			$grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($oDocument->get('module_srl')), $logged_info);
		// 			if(!$grant->access)
		// 			{
		// 				return new BaseObject(-1, 'msg_not_permitted');
		// 			}
		// 		}

		// 		$tmp_files = $this->getFiles($upload_target_srl);
		// 		if(!$tmp_files) $tmp_files = array();

		// 		foreach($tmp_files as $file_info)
		// 		{
		// 			if(!$file_info->file_srl) continue;

		// 			$obj = new stdClass;
		// 			$obj->file_srl = $file_info->file_srl;
		// 			$obj->source_filename = $file_info->source_filename;
		// 			$obj->file_size = $file_info->file_size;
		// 			$obj->disp_file_size = FileHandler::filesize($file_info->file_size);
		// 			if($file_info->direct_download=='N') $obj->download_url = $this->getDownloadUrl($file_info->file_srl, $file_info->sid, $file_info->module_srl);
		// 			else $obj->download_url = str_replace('./', '', $file_info->uploaded_filename);
		// 			$obj->direct_download = $file_info->direct_download;
		// 			$obj->cover_image = ($file_info->cover_image === 'Y') ? true : false;
		// 			$files[] = $obj;
		// 			$attached_size += $file_info->file_size;
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$upload_target_srl = 0;
		// 		$attached_size = 0;
		// 		$files = array();
		// 	}
		// 	// Display upload status
		// 	$upload_status = $this->getUploadStatus($attached_size);
		// 	// Check remained file size until upload complete
		// 	//$config = $oModuleModel->getModuleInfoByMid($mid);	//perhaps config varialbles not used

		// 	$file_config = $this->getUploadConfig();
		// 	$left_size = $file_config->allowed_attach_size*1024*1024 - $attached_size;
		// 	// Settings of required information
		// 	$attached_size = FileHandler::filesize($attached_size);
		// 	$allowed_attach_size = FileHandler::filesize($file_config->allowed_attach_size*1024*1024);
		// 	$allowed_filesize = FileHandler::filesize($file_config->allowed_filesize*1024*1024);
		// 	$allowed_filetypes = $file_config->allowed_filetypes;
		// 	$this->add("files",$files);
		// 	$this->add("editor_sequence",$editor_sequence);
		// 	$this->add("upload_target_srl",$upload_target_srl);
		// 	$this->add("upload_status",$upload_status);
		// 	$this->add("left_size",$left_size);
		// 	$this->add('attached_size', $attached_size);
		// 	$this->add('allowed_attach_size', $allowed_attach_size);
		// 	$this->add('allowed_filesize', $allowed_filesize);
		// 	$this->add('allowed_filetypes', $allowed_filetypes);
		// }

		/**
		 * Return file configuration of the module
		 *
		 * @param int $module_srl The sequence of module to get configuration
		 * @return object
		 */
		// function getFileModuleConfig($module_srl)
		// {
		// 	return $this->getFileConfig($module_srl);
		// }
	}
}
/* End of file file.model.php */