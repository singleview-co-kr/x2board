<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
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
		var $statusList = array('private'=>'PRIVATE', 'public'=>'PUBLIC', 'secret'=>'SECRET', 'temp'=>'TEMP');

		/**
		 * KBoard allow search list
		 * @var array
		 */
		var $allowSearchKboard = array('1'=>'PUBLIC', '2'=>'SECRET', '3'=>'PRIVATE');
		// 1 -> 'Public', 2 -> 'Only title (secret post)', 3 -> 'Exclusion'

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

		/**
		 * Return status by key
		 * @return string
		 */
		public function convert_kb_allow_search_2_xe_status($key) {
			if(array_key_exists(strtolower($key), $this->allowSearchKboard)) {
				return $this->allowSearchKboard[$key];
			}
			return false;
		}
	}
}
/* End of file post.class.php */
