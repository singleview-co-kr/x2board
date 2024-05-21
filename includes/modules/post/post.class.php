<?php
/* Copyright (C) XEHub <https://www.xehub.io> 
   it was document.class.php */
/* WP port by singleview.co.kr */

/**
 * post class
 * @brief post the module's high class
 * {@internal Silently adds one extra Foo to compensate for lack of Foo }
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 * @version 0.1
 */
namespace X2board\Includes\Modules\Post;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

require_once X2B_PATH . 'includes/modules/post/post.item.php';

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\post')) {

	class post extends \X2board\Includes\Classes\ModuleObject {
		/**
		 * Search option to use in admin page
		 * @var array
		 */
		var $search_option = array('title','content','title_content','user_name',); // /< Search options
		/**
		 * XE Status list
		 * @var array
		 */
		var $statusList = array('public'=>'PUBLIC', 'secret'=>'SECRET');  // ,'private'=>'PRIVATE', 'temp'=>'TEMP'

		function __construct() {
// var_dump('post claas');
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['POST_LIST'])) {
				$G_X2B_CACHE['POST_LIST'] = array();
			}
			
			if(!isset($_SESSION['x2b_own_post'])) {
				$_SESSION['x2b_own_post'] = array();
			}
		}

		/**
		 * Return default status
		 * @return string
		 */
		// function getDefaultStatus() {
		public function get_default_status() {
			return $this->statusList['public'];
		}

		/**
		 * Post Status List
		 * @return array
		 */
		// function getStatusList()
		public function get_status_list() {
			return $this->statusList;
		}

		/**
		 * Return status by key
		 * @return string
		 */
		// function getConfigStatus($key)
		public function get_config_status($key) {
			if(array_key_exists(strtolower($key), $this->statusList)) {
				return $this->statusList[$key];
			}
			return $this->get_default_status();
		}
	}
}
/* End of file post.class.php */