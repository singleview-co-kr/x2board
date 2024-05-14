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
		// 검색 옵션
		public $a_search_option = array('title_content', 'title', 'content', 'comment', 'nick_name', 'user_id', 'tag');  // ,'user_name'
		// 정렬 옵션
		public $a_order_target = array('list_order', 'update_order', 'regdate_dt', 'voted_count', 'blamed_count', 'readed_count', 'comment_count', 'user_id');  // 'title', 'nick_name', 'user_name', 
		public $consultation = false;
		
		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() { }
	}
}