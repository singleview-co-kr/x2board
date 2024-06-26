<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * comment
 * comment module's high class
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/comment
 */
namespace X2board\Includes\Modules\Comment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

require_once X2B_PATH . 'includes/modules/comment/comment.item.php';

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Comment\\comment' ) ) {

	class comment extends \X2board\Includes\Classes\ModuleObject {

		private $a_status_option = array(
			'allow' => 'ALLOW',
			'deny'  => 'DENY',
		);

		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() {
			if ( ! isset( $_SESSION['x2b_own_comment'] ) ) {
				$_SESSION['x2b_own_comment'] = array();
			}
			if ( ! isset( $_SESSION['x2b_accessibled_comment'] ) ) {
				$_SESSION['x2b_accessibled_comment'] = array();
			}
		}

		/**
		 * Return status comment by key
		 *
		 * @return string
		 */
		public function get_status_by_key( $key ) {
			if ( array_key_exists( strtolower( $key ), $this->a_status_option ) ) {
				return $this->a_status_option[ $key ];
			}
			return $this->a_status_option['allow'];
		}
	}
}
