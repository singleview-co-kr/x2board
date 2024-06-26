<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * Model class of the file module
 *
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\File\\fileModel' ) ) {

	class fileModel extends file {
		/**
		 * Initialization
		 *
		 * @return void
		 */
		function init() {}

		/**
		 * Return messages for file upload and it depends whether an admin is or not
		 * getUploadStatus($attached_size = 0)
		 *
		 * @param int $attached_size
		 * @return string
		 */
		public function get_upload_status( $attached_size = 0 ) {
			$file_config = $this->get_upload_config();
			// Display upload status
			$upload_status = sprintf(
				'%s : %s/ %s<br /> %s : %s (%s : %s)',
				__( 'lbl_allowed_attach_size', X2B_DOMAIN ),
				\X2board\Includes\Classes\FileHandler::filesize( $attached_size ),
				\X2board\Includes\Classes\FileHandler::filesize( $file_config->allowed_attach_size * 1024 * 1024 ),
				__( 'lbl_allowed_filesize', X2B_DOMAIN ),
				\X2board\Includes\Classes\FileHandler::filesize( $file_config->allowed_filesize * 1024 * 1024 ),
				__( 'lbl_allowed_filetypes', X2B_DOMAIN ),
				$file_config->allowed_filetypes
			);
			return $upload_status;
		}

		/**
		 * Returns a grant of file
		 * getFileGrant($file_info, $member_info)
		 *
		 * @param object $file_info The file information to get grant
		 * @param object $member_info The member information to get grant
		 * @return object Returns a grant of file
		 */
		public function get_file_grant( $o_file_info ) {
			if ( ! $o_file_info ) {
				return null;
			}
			$file_grant = new \stdClass();
			if ( $_SESSION['__X2B_UPLOADING_FILES_INFO__'][ $o_file_info->file_id ] ) {
				$file_grant->is_deletable = true;
				return $file_grant;
			}
			$grant = \X2board\Includes\Classes\Context::get( 'grant' );

			$o_post_model = \X2board\Includes\getModel( 'post' );
			$o_post       = $o_post_model->get_post( $o_file_info->upload_target_id );
			unset( $o_post_model );
			if ( $o_post->is_exists() ) {
				$document_grant = $o_post->is_granted();
			}
			unset( $o_post );

			$member_info              = \X2board\Includes\Classes\Context::get( 'logged_info' );
			$file_grant->is_deletable = ( $document_grant || $member_info->is_admin == 'Y' || $member_info->ID == $o_file_info->author || $grant->manager );
			unset( $member_info );
			unset( $document_grant );
			return $file_grant;
		}

		/**
		 * Return number of attachments which belongs to a specific document
		 * getFilesCount($upload_target_srl)
		 *
		 * @param int $upload_target_srl The sequence to get a number of files
		 * @return int Returns a number of files
		 */
		public function get_files_count( $n_upload_target_id ) {
			global $wpdb;
			$n_file_cnt = $wpdb->get_var( "SELECT count(*) as `file_cnt` FROM `{$wpdb->prefix}x2b_files` WHERE `upload_target_id`={$n_upload_target_id}" );
			return intval( $n_file_cnt );
		}

		/**
		 * Return all files which belong to a specific document
		 * getFiles($upload_target_srl, $columnList = array(), $sortIndex = 'file_srl', $ckValid = false)
		 *
		 * @param int    $upload_target_srl The sequence of target to get file list
		 * @param array  $columnList The list of columns to get from DB
		 * @param string $sortIndex The column that used as sort index
		 * @return array Returns array of object that contains file information. If no result returns null.
		 */
		public function get_files( $upload_target_id, $sortIndex = 'file_id', $showValidOnly = false ) {
			$s_where = '`upload_target_id`=' . $upload_target_id;
			if ( $showValidOnly ) {
				$s_where .= " AND `isvalid` = 'Y'";
			}
			global $wpdb;
			$a_file_list = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE {$s_where} ORDER BY `{$sortIndex}` ASC" );
			foreach ( $a_file_list as &$file ) {
				$file->source_filename   = htmlspecialchars( stripslashes( $file->source_filename ) );
				$file->download_url      = $this->get_download_url( $file->file_id, $file->sid );
				$file->file_type         = $this->is_image_file( $file->uploaded_filename ) ? 'image' : 'binary';
				$file->thumbnail_abs_url = $this->get_thumbnail_url( $file->file_type, $file->uploaded_filename );
			}
			return $a_file_list;
		}

		/**
		 * decide file type
		 *
		 * @param int $s_file_name
		 * @return bool
		 */
		public function is_image_file( $s_file_name ) {
			return preg_match( '/\.(jpe?g|gif|png|wm[va]|mpe?g|avi|flv|mp[1-4]|as[fx]|wav|midi?|moo?v|qt|r[am]{1,2}|m4v)$/i', $s_file_name );
		}

		/**
		 * decide file type
		 *
		 * @param int $s_file_name
		 * @return bool
		 */
		public function get_thumbnail_url( $s_file_type, $s_filename ) {
			if ( $s_file_type == 'image' ) {
				$s_wp_content_folder_name = str_replace( get_site_url(), '', content_url() ) . '/';  // would be '/wp-content/'
				$a_attachment_path        = explode( $s_wp_content_folder_name, $s_filename );
				$thumbnail_abs_url        = content_url() . '/' . $a_attachment_path[1];
				unset( $a_attachment_path );
			} else {
				$thumbnail_abs_url = plugins_url() . '/' . X2B_DOMAIN . '/assets/jquery.fileupload/img/file.png';
			}
			return $thumbnail_abs_url;
		}

		/**
		 * Get a download path
		 * getDownloadUrl($file_srl, $sid, $module_srl="")
		 *
		 * @param int    $file_srl The sequence of file to get url
		 * @param string $sid
		 * @return string Returns a url
		 */
		public function get_download_url( $file_id, $sid ) {
			$n_board_id = \X2board\Includes\Classes\Context::get( 'board_id' );
			return \X2board\Includes\Classes\Context::get_the_permalink() . '?cmd=' . X2B_CMD_PROC_DOWNLOAD_FILE . '&board_id=' . $n_board_id . '&file_id=' . $file_id . '&sid=' . $sid;
		}

		/**
		 * Get file information
		 * getFile($file_srl, $columnList = array())
		 *
		 * @param int   $file_srl The sequence of file to get information
		 * @param array $columnList The list of columns to get from DB
		 * @return BaseObject|object|array If error returns an instance of BaseObject. If result set is one returns a object that contins file information. If result set is more than one returns array of object.
		 */
		public function get_file( $file_id, $columnList = array() ) {
			global $wpdb;
			$o_file = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `file_id`={$file_id} AND `isvalid` = 'Y'" );
			if ( $o_file === false ) {
				unset( $o_file );
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			}
			$o_file->download_url = $this->get_download_url( $o_file->file_id, $o_file->sid, $o_file->board_id );
			return $o_file;
		}

		/**
		 * Return configurations of the attachement (it automatically checks if an administrator is)
		 * getUploadConfig()
		 *
		 * @return object Returns a file configuration of current module. If user is admin, returns PHP's max file size and allow all file types.
		 */
		public function get_upload_config() {
			$n_board_id    = \X2board\Includes\Classes\Context::get( 'board_id' );
			$o_file_config = $this->_get_file_config( $n_board_id );
			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				$iniPostMaxSize                     = \X2board\Includes\Classes\FileHandler::returnbytes( ini_get( 'post_max_size' ) );
				$iniUploadMaxSize                   = \X2board\Includes\Classes\FileHandler::returnbytes( ini_get( 'upload_max_filesize' ) );
				$size                               = min( $iniPostMaxSize, $iniUploadMaxSize ) / 1048576;
				$o_file_config->allowed_attach_size = $size;
				$o_file_config->allowed_filesize    = $size;
				$o_file_config->allowed_filetypes   = '*.*';
			}
			unset( $o_logged_info );
			return $o_file_config;
		}

		/**
		 *
		 * @return
		 */
		private function _get_default_config() {
			$o_default_config                       = new \stdClass();
			$o_default_config->allowed_filesize     = $this->_n_allowed_filesize;
			$o_default_config->allowed_attach_size  = $this->_n_allowed_attach_size;
			$o_default_config->allowed_filetypes    = $this->_s_allowed_filetypes;
			$o_default_config->allow_outlink        = $this->_s_allow_outlink;
			$o_default_config->allow_outlink_format = $this->_s_allow_outlink_format;
			$o_default_config->allow_outlink_site   = $this->_s_allow_outlink_site;
			return $o_default_config;
		}

		/**
		 * Get file configurations
		 * getFileConfig($module_srl = null)
		 *
		 * @param int $n_board_id If set this, returns specific board's configuration. Otherwise returns global configuration.
		 * @return object Returns configuration.
		 */
		private function _get_file_config( $n_board_id = null ) {
			// Get configurations
			$file_module_config = $this->_get_default_config();
			$file_config        = new \stdClass();

			if ( $n_board_id ) {
				$o_current_board_info              = \X2board\Includes\Classes\Context::get( 'current_module_info' );
				$file_config->allowed_filesize     = $o_current_board_info->file_allowed_filesize_mb;
				$file_config->allowed_attach_size  = $o_current_board_info->file_allowed_attach_size_mb;
				$file_config->allowed_filetypes    = $o_current_board_info->file_allowed_filetypes;
				$file_config->download_grant       = $o_current_board_info->file_download_grant;
				$file_config->allow_outlink        = $o_current_board_info->file_allow_outlink;
				$file_config->allow_outlink_site   = $o_current_board_info->file_allow_outlink_site;
				$file_config->allow_outlink_format = $o_current_board_info->file_allow_outlink_format;
			}

			$config = new \stdClass();
			if ( $file_config ) {
				$config->allowed_filesize     = $file_config->allowed_filesize;
				$config->allowed_attach_size  = $file_config->allowed_attach_size;
				$config->allowed_filetypes    = $file_config->allowed_filetypes;
				$config->download_grant       = $file_config->download_grant;
				$config->allow_outlink        = $file_config->allow_outlink;
				$config->allow_outlink_site   = $file_config->allow_outlink_site;
				$config->allow_outlink_format = $file_config->allow_outlink_format;
			}
			// Property for all files comes first than each property
			if ( ! $config->allowed_filesize ) {
				$config->allowed_filesize = $file_module_config->allowed_filesize;
			}
			if ( ! $config->allowed_attach_size ) {
				$config->allowed_attach_size = $file_module_config->allowed_attach_size;
			}
			if ( ! $config->allowed_filetypes ) {
				$config->allowed_filetypes = $file_module_config->allowed_filetypes;
			}
			if ( ! $config->allow_outlink ) {
				$config->allow_outlink = $file_module_config->allow_outlink;
			}
			if ( ! $config->allow_outlink_site ) {
				$config->allow_outlink_site = $file_module_config->allow_outlink_site;
			}
			if ( ! $config->allow_outlink_format ) {
				$config->allow_outlink_format = $file_module_config->allow_outlink_format;
			}
			if ( ! $config->download_grant ) {
				$config->download_grant = $file_module_config->download_grant;
			}
			// Default setting if not exists
			if ( ! $config->allowed_filesize ) {
				$config->allowed_filesize = '2';
			}
			if ( ! $config->allowed_attach_size ) {
				$config->allowed_attach_size = '3';
			}
			if ( ! $config->allowed_filetypes ) {
				$config->allowed_filetypes = '*.*';
			}
			if ( ! $config->allow_outlink ) {
				$config->allow_outlink = 'Y';
			}
			if ( ! $config->download_grant ) {
				$config->download_grant = array();
			}

			$size = ini_get( 'upload_max_filesize' );
			$unit = strtolower( $size[ strlen( $size ) - 1 ] );
			$size = (float) $size;
			if ( $unit == 'g' ) {
				$size *= 1024;
			}
			if ( $unit == 'k' ) {
				$size /= 1024;
			}

			if ( $config->allowed_filesize > $size ) {
				$config->allowed_filesize = $size;
			}
			if ( $config->allowed_attach_size > $size ) {
				$config->allowed_attach_size = $size;
			}
			return $config;
		}
	}
}
