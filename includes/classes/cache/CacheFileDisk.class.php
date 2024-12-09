<?php
namespace X2board\Includes\Classes;

/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * Filedisk Cache Handler
 * Warning!!! This class is not internally used by x2board
 * This class is not autoloaded by x2board
 * 
 * @author singleview.co.kr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\X2board\\Includes\\Classes\\CacheFileDisk' ) ) {
	class CacheFileDisk {
		/**
		 * Path that value to stored
		 *
		 * @var string
		 */
		private $_s_cache_dir         = null;
		private $_s_cache_file        = null;
		private $_n_keep_alive_second = 3600;  // 1 hr

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
		}

		public function set_storage_label( $s_storage_name = 'search' ) {
			$this->_s_cache_dir = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_CACHE_PATH . DIRECTORY_SEPARATOR . $s_storage_name;
			if ( ! file_exists( $this->_s_cache_dir ) ) {
				wp_mkdir_p( $this->_s_cache_dir );
			}
			$this->_s_cache_file = null;  // init
		}

		public function set_cache_key( $s_key ) {
			$this->_s_cache_file = $this->_get_cache_filename( $s_key );
		}

		/**
		 * Fetch a stored variable from the cache
		 *
		 * @param int    $n_keep_alive_second
		 * @return false|mixed Return false on failure. Return the string associated with the $key on success.
		 */
		public function set_keep_alive_second( $n_keep_alive_second = 0 ) {
			$this->_n_keep_alive_second = $n_keep_alive_second;
		}
		
		/**
		 * Fetch a stored variable from the cache
		 *
		 * @param int    $n_keep_alive_second
		 * @return false|mixed Return false on failure. Return the string associated with the $key on success.
		 */
		public function get( $n_keep_alive_second = 0 ) {
			if ( ! file_exists( $this->_s_cache_file ) ) {
				return false;
			}
			if ( $this->_n_keep_alive_second > 0 && ( time() - filemtime( $this->_s_cache_file ) > $this->_n_keep_alive_second ) ) {
				wp_delete_file( $this->_s_cache_file );
				return false;
			}
			$s_content = include $this->_s_cache_file;
// error_log(print_r('load cache: ' . $this->_s_cache_dir, true));
			return unserialize( $s_content );
		}

		/**
		 * Cache a variable in the data store
		 * $this->handler->put('key_group_versions', $this->keyGroupVersions, 0);
		 *
		 * @param mixed  $obj The variable to store
		 * @return void
		 */
		public function put( $o_obj ) {
			$s_data      = serialize( $o_obj );
			$s_data      = str_replace( '\\', '\\\\', $s_data );
			$s_data      = str_replace( '\'', '\\\'', $s_data );
			$a_content   = array();
			$a_content[] = '<?php';
			$a_content[] = 'if(!defined(\'ABSPATH\')) { exit(); }';
			$a_content[] = 'return \'' . $s_data . '\';';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$o_wp_filesystem = new \WP_Filesystem_Direct( false );
			$o_wp_filesystem->put_contents( 
				$this->_s_cache_file, 
				implode( PHP_EOL, $a_content ), 
				(0664 & ~ umask()) // avoid PHP warning - Use of undefined constant FS_CHMOD_FILE
			);
			if ( function_exists( 'opcache_invalidate' ) ) {
				@opcache_invalidate( $this->_s_cache_file, true );
			}
			unset( $o_wp_filesystem );
			unset( $a_content );
		}

		/**
		 * Return whether cache is valid or invalid
		 *
		 * @param int    $n_keep_alive_second
		 * @return bool Return true on valid or false on invalid.
		 */
		public function is_Valid( $n_keep_alive_second = 0 ) {
			if ( file_exists( $this->_s_cache_file ) ) {
				if ( $n_keep_alive_second > 0 && ( time() - filemtime( $this->_s_cache_file ) > $n_keep_alive_second ) ) {
					wp_delete_file( $this->_s_cache_file );
					return false;
				}
				return true;
			}
			return false;
		}
		
		/**
		 * Cache a variable in the data store
		 * $this->handler->put('key_group_versions', $this->keyGroupVersions, 0);
		 *
		 * @param mixed  $obj The variable to store
		 * @return void
		 */
		public function reset() {
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$o_wp_filesystem = new \WP_Filesystem_Direct( false );
			$o_wp_filesystem->rmdir( $this->_s_cache_dir, true);
			unset( $o_wp_filesystem );
		}

		/**
		 * Get cache file name by key
		 *
		 * @param string $key The key that will be associated with the item.
		 * @return string Returns cache file path
		 */
		private function _get_cache_filename( $key ) {
			return $this->_s_cache_dir . DIRECTORY_SEPARATOR . md5( $key ) . '.php';
		}
	}
}
/* End of file CacheFile.class.php */
