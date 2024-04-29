<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  categoryController
 * @author XEHub (developers@xpressengine.com)
 * @brief  category module Controller class
 **/
namespace X2board\Includes\Modules\Category;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Category\\categoryController')) {

	class categoryController extends category {
		private $_n_board_id = null;

		/**
		 * Initialization
		 * @return void
		 */
		function init() {}

		public function set_board_id($n_board_id) {
			$this->_n_board_id = $n_board_id;
		}

		/**
		 * Update post_count in the category.
		 * @param int $category_srl
		 * @param int $document_count
		 * @return object
		 */
		// function updateCategoryCount($module_srl, $category_srl, $document_count = 0)
		public function update_category_count($n_category_id) {
			$n_post_count = $this->_get_post_count_by_category($n_category_id);
			global $wpdb;
			$result = $wpdb->update ( "{$wpdb->prefix}x2b_categories", 
									  array ( 'post_count' => esc_sql(intval($n_post_count)) ),
									  array ( 'category_id' => esc_sql(intval($n_category_id) )) );
			if( $result < 0 || $result === false ){
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error );
			}
			return new \X2board\Includes\Classes\BaseObject();
		}

		/**
		 * Wanted number of posts belonging to category
		 * @param int $category_srl
		 * @return int
		 */
		// function getCategoryDocumentCount($module_srl, $category_srl)
		private function _get_post_count_by_category($n_category_id) {
			global $wpdb;
			$count = $wpdb->get_var("SELECT count(*) as `count` FROM `{$wpdb->prefix}x2b_posts` WHERE `category_id`='$n_category_id' AND `board_id`='$this->_n_board_id'");
			return intval($count);
		}

	}
}
/* End of file category.controller.php */