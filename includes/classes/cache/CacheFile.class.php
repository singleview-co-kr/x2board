<?php
namespace X2board\Includes\Classes;
/* Copyright (C) XEHub <https://www.xehub.io> */

/**
 * Cache class for file
 *
 * Filedisk Cache Handler
 *
 * @author XEHub (developers@xpressengine.com)
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Classes\\CacheFile')) {

	class CacheFile extends \X2board\Includes\Classes\CacheBase {
		/**
		 * Path that value to stored
		 * @var string
		 */
		private $_s_cache_dir = 'store/';

		/**
		 * Get instance of CacheFile
		 *
		 * @return CacheFile instance of CacheFile
		 */
		public static function getInstance() {
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['__CacheFile__'])) {
				$G_X2B_CACHE['__CacheFile__'] = new CacheFile();
			}
			return $G_X2B_CACHE['__CacheFile__'];
		}

		/**
		 * Constructor
		 *
		 * @return void
		 */
		function __construct() {
			$this->_s_cache_dir = wp_get_upload_dir()['basedir'].DIRECTORY_SEPARATOR.X2B_CACHE_PATH.DIRECTORY_SEPARATOR . $this->_s_cache_dir;
			if( !file_exists( $this->_s_cache_dir ) ) {
				wp_mkdir_p( $this->_s_cache_dir );
			}
		}

		/**
		 * Get cache file name by key
		 *
		 * @param string $key The key that will be associated with the item.
		 * @return string Returns cache file path
		 */
		function getCacheFileName($key) {
			$path_string = preg_replace("/[^a-z0-9-_:\.]+/i", '_', $key);
			return $this->_s_cache_dir . str_replace(':', DIRECTORY_SEPARATOR, $path_string) . '.php';
		}

		/**
		 * Return whether support or not support cache
		 *
		 * @return true
		 */
		function isSupport() {
			return true;
		}

		/**
		 * Cache a variable in the data store
		 *
		 * @param string $key Store the variable using this name.
		 * @param mixed $obj The variable to store
		 * @param int $valid_time Not used
		 * @return void
		 */
		// $this->handler->put('key_group_versions', $this->keyGroupVersions, 0);
		function put($key, $obj, $valid_time = 0) {
			$cache_file = $this->getCacheFileName($key);
			$data = serialize($obj);
			$data = str_replace('\\', '\\\\', $data);
			$data = str_replace('\'', '\\\'', $data);
			$content = array();
			$content[] = '<?php';
			$content[] = 'if(!defined(\'ABSPATH\')) { exit(); }';
			$content[] = 'return \'' . $data . '\';';

			// FileHandler::writeFile($cache_file, implode(PHP_EOL, $content));
			require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
			require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
			$o_wp_filesystem = new \WP_Filesystem_Direct(false);

			// check if directory exists
			$s_cache_path = str_replace(basename($cache_file), '', $cache_file);
			if( !file_exists( $s_cache_path ) ) {
				wp_mkdir_p( $s_cache_path );
			}

			$o_wp_filesystem->put_contents($cache_file, implode(PHP_EOL, $content));
			unset($o_wp_filesystem);
			if(function_exists('opcache_invalidate')) {
				@opcache_invalidate($cache_file, true);
			}
		}

		/**
		 * Return whether cache is valid or invalid
		 *
		 * @param string $key Cache key
		 * @param int $modified_time Not used
		 * @return bool Return true on valid or false on invalid.
		 */
		function isValid($key, $modified_time = 0) {
			$cache_file = $this->getCacheFileName($key);

			if(file_exists($cache_file)) {
				if($modified_time > 0 && filemtime($cache_file) < $modified_time) {
					FileHandler::removeFile($cache_file);
					return false;
				}
				return true;
			}
			return false;
		}

		/**
		 * Fetch a stored variable from the cache
		 *
		 * @param string $key The $key used to store the value.
		 * @param int $modified_time Not used
		 * @return false|mixed Return false on failure. Return the string associated with the $key on success.
		 */
		function get($key, $modified_time = 0) {
			$cache_file = $this->getCacheFileName($key);
			if( !file_exists( $cache_file ) ) {
				return false;
			}

			if($modified_time > 0 && filemtime($cache_file) < $modified_time) {
				// FileHandler::removeFile($cache_file);
				wp_delete_file($cache_file);
				return false;
			}
			$content = include($cache_file);
			return unserialize($content);
		}

		/**
		 * Delete variable from the cache(private)
		 *
		 * @param string $_key Used to store the value.
		 * @return void
		 */
		function _delete($_key) {
			$cache_file = $this->getCacheFileName($_key);
			if(function_exists('opcache_invalidate')) {
				@opcache_invalidate($cache_file, true);
			}
			FileHandler::removeFile($cache_file);
		}

		/**
		 * Delete variable from the cache
		 *
		 * @param string $key Used to store the value.
		 * @return void
		 */
		function delete($key) {
			$this->_delete($key);
		}

		/**
		 * Truncate all existing variables at the cache
		 *
		 * @return bool Returns true on success or false on failure.
		 */
		function truncate() {
			FileHandler::removeFilesInDir($this->_s_cache_dir);
		}
	}
}
/* End of file CacheFile.class.php */