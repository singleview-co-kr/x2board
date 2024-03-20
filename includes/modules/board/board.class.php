<?php
/**
 * @class  board
 * @author singleview.co.kr
 * @brief  board module high class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\board')) {

	class board extends \X2board\Includes\Classes\ModuleObject
	{
		var $a_search_option = array('title_content','title','content','comment','user_name','nick_name','user_id','tag'); ///< 검색 옵션
		var $a_order_target = array('list_order', 'update_order', 'regdate', 'voted_count', 'blamed_count', 'readed_count', 'comment_count', 'title', 'nick_name', 'user_name', 'user_id'); // 정렬 옵션

		var $s_skin = "sketchbook5"; ///< skin name
		var $n_list_count = 20; ///< the number of documents displayed in a page
		var $n_page_count = 10; ///< page number
		var $a_category_list = NULL; ///< category list

		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() {
			$o_grant = new \stdClass();

			$o_grant->is_site_admin = true;
			$o_grant->manager = true; 
			$o_grant->access = true;
			$o_grant->is_admin = true;
			$o_grant->list = true;
			$o_grant->view = true; 
			$o_grant->write_post = true;
			$o_grant->write_comment = true;
			// $o_grant->consultation_read = true;

			$o_module_info = new \stdClass();
			$o_module_info->skin_vars = new \stdClass();
			$o_module_info->use_category = 'Y';
			$o_module_info->list = true;
			$this->setModuleInfo($o_module_info, $o_grant);
		}
	}
}
