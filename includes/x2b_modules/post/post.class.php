<?php
/*
Copyright (C) XEHub <https://www.xehub.io>
	it was document.class.php */
/* WP port by singleview.co.kr */

/**
 * post class
 *
 * @brief post the module's high class
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 */
namespace X2board\Includes\Modules\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . X2B_MODULES_NAME . DIRECTORY_SEPARATOR . 'post' . DIRECTORY_SEPARATOR . 'post.item.php';

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Post\\post' ) ) {
	// this is for poedit recognition
	__( 'lbl_public', X2B_DOMAIN );
	__( 'lbl_secret', X2B_DOMAIN );

	class post extends \X2board\Includes\Classes\ModuleObject {
		/**
		 * Search option to use in admin page
		 *
		 * @var array
		 */
		var $search_option = array( 'title', 'content', 'title_content', 'user_name' );
		/**
		 * XE Status list
		 *
		 * @var array
		 */
		var $statusList = array(
			'public' => 'PUBLIC',
			'secret' => 'SECRET',
			// ,'private'=>'PRIVATE', 'temp'=>'TEMP'
		);  

		function __construct() {
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['POST_LIST'] ) ) {
				$G_X2B_CACHE['POST_LIST'] = array();
			}

			if ( ! isset( $_SESSION['x2b_own_post'] ) ) {
				$_SESSION['x2b_own_post'] = array();
			}
		}

		/**
		 * Return default status
		 * getDefaultStatus()
		 *
		 * @return string
		 */
		public function get_default_status() {
			return $this->statusList['public'];
		}

		/**
		 * Post Status List
		 * getStatusList()
		 *
		 * @return array
		 */
		public function get_status_list() {
			return $this->statusList;
		}

		/**
		 * Return status by key
		 * getConfigStatus($key)
		 *
		 * @return string
		 */
		public function get_config_status( $key ) {
			if ( array_key_exists( strtolower( $key ), $this->statusList ) ) {
				return $this->statusList[ $key ];
			}
			return $this->get_default_status();
		}
	}
}
