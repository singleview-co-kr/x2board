<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  board
 * @author XEHub (developers@xpressengine.com)
 * @brief  board module high class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\board')) {

	class board extends \X2board\Includes\Classes\ModuleObject {
		public $board_id = null;
		public $a_search_option = array(); ///< 검색 옵션
		public $a_order_target = array(); // 정렬 옵션

		public $s_skin = null; ///< skin name
		public $n_list_count = 0; ///< the number of documents displayed in a page
		public $n_page_count = 0; ///< page number
		public $a_category_list = NULL; ///< category list
		
		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() {
// var_dump('board high class');
			// $this->board_id = get_the_ID();  // x2board id is WP post ID
			$this->a_search_option = array('title_content','title','content','comment','user_name','nick_name','user_id','tag');
			$this->a_order_target = array('list_order', 'update_order', 'regdate_dt', 'voted_count', 'blamed_count', 'readed_count', 'comment_count', 'title', 'nick_name', 'user_name', 'user_id');
			$this->n_list_count = 20;
			$this->n_page_count = 10;
			$this->consultation = false;
			// $this->a_category_list = NULL;
		}
	}
}
