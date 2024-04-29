<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  categoryModel
 * @author XEHub (developers@xpressengine.com)
 * @brief  category module Model class
 **/
namespace X2board\Includes\Modules\Category;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Category\\categoryModel')) {

	class categoryModel extends category {
		private $_tree_category_old = array();
		private $_n_board_id = null;
		private $_a_category_id_title_map = array();

		/**
		 * Initialization
		 * @return void
		 */
		public function init() {}

		public function set_board_id($n_board_id) {
			$this->_n_board_id = $n_board_id;
		}

		/**
		 * Wanted number of posts belonging to category
		 * @param int $category_srl
		 * @return int
		 */
		public function get_category_name($n_board_id, $n_category_id) {
			global $wpdb;
			if(isset($this->_a_category_id_title_map[$n_category_id]) ){
				return $this->_a_category_id_title_map[$n_category_id];
			}
			$title = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}x2b_categories` WHERE `category_id`='$n_category_id' AND `board_id`='$n_board_id'");
			$this->_a_category_id_title_map[$n_category_id] = $title;
			return $title;
		}

		/**
		 * 방문자 UX용 계층형 카테고리를 반환한다.
		 */
		public function build_linear_category() {
			$this->_get_old_category();
			$tree_category_recursive = $this->_construct_tree_category();
			$a_category = [];
			$this->_arrange_category($a_category, $tree_category_recursive, 0);

			// get the user group information  <---- do not work
			// if(\X2board\Includes\Classes\Context::get('is_logged')) {
			// 	$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			// 	$a_group_srls = array(); // array_keys($o_logged_info->group_list);
			// }
			// else {
				// $a_group_srls = array();
			// }
			// check the grant after obtained the category list
			// if(count($a_category)) {
			// 	$a_category_list = array();
			// 	foreach($a_category as $category_id => $o_category) {
			// 		$is_granted = TRUE;
			// 		if($o_category->group_ids) {
			// 			$a_category_groups = explode(',',$o_category->group_ids);
			// 			$is_granted = FALSE;
			// 			if(count(array_intersect($a_group_srls, $a_category_groups))) {
			// 				$is_granted = TRUE;
			// 			}
			// 		}
			// 		if($is_granted) {
			// 			$a_category_list[$category_id] = $o_category;
			// 		}
			// 	}
			// }
// var_dump($a_category);
			return $a_category;
		}

		/**
		 * list-category-tree-tab.php 스킨 전용
		 * 검색 옵션의 하위 카테고리 데이터를 스킨으로 반환한다.
		 * @param string $category_name
		 * @return array $tree_category
		 */
		public function get_category_navigation() {
			$tree_category = array('parent'=>null, 'cur'=>null, 'children'=>null);
			
			$tree_category_copy = $this->build_linear_category();
			if( isset($_GET['category_id']) ) {
				$category_id = intval($_GET['category_id']);
			}
			else {
				$category_id = null;
			}
			// set parent category if exists
			if( $category_id ) {
				if(isset($tree_category_copy[$category_id])) {
					$parent_id = $tree_category_copy[$category_id]->parent_id;
					if($parent_id) {
						$tree_category['parent'] = $tree_category_copy[$parent_id];
					}
				}
			}
			// set current category
			if( $category_id ) {
				if(isset($tree_category_copy[$category_id])) {
					$tree_category_copy[$category_id]->selected = true;
					$tree_category['cur'] = $tree_category_copy[$category_id];
				}
			}
			else{
				$tree_category['cur'] = $tree_category['parent'];
				$tree_category['parent'] = null;
			}
			$children_cat = [];
			// set children category if exists
			foreach($tree_category_copy as $item){
				if( $category_id == $item->parent_id ) {
					$item->selected = false;
					$children_cat[] = $item;
				}
			}
			if(count($children_cat)) {
				$tree_category['children'] = $children_cat;
			}
			return $tree_category;
		}

		/**
		 * 기존 계층형 카테고리 정보를 구성한다.
		 * \includes\modules\category\category.admin.controller.php::_get_old_category() 동기화
		 */
		private function _get_old_category() {
			global $wpdb;
			$results = $wpdb->get_results("SELECT `category_id`, `board_id`, `title`, `group_ids`, `parent_id`, `expand`, `color`, `is_default`, `post_count` ".
										  "FROM `{$wpdb->prefix}x2b_categories` ".
										  "WHERE `board_id`='{$this->_n_board_id}' AND `deleted`='N' ".
										  "ORDER BY `list_order` ASC");
			$wpdb->flush();
			$a_category = [];
			foreach($results as $row){
				$n_category_id = intval($row->category_id);
				$a_category[$n_category_id] = ['id' => $n_category_id,
											   'board_id' => $row->board_id,
											   'parent_id' => $row->parent_id ? intval($row->parent_id) : null, //$parent_id, 
											   'group_ids' => $row->group_ids,
											   'title' => $row->title,
											   'expand' => $row->expand,
											   'color' => $row->color ? $row->color : null,
											   'post_count' => intval($row->post_count), //$post_count, 
											   'is_default' => $row->is_default ? $row->is_default : null, //$is_default
											];
			}
			unset($results);
			$this->_tree_category_old = $a_category;
		}

		/**
		 * DB의 메뉴 구조를 계층형으로 변환한다.
		 * \includes\modules\category\category.admin.controller.php::_construct_tree_category() 동기화
		 */
		private function _construct_tree_category() {
			$tree_category_source = [];
			if($this->_tree_category_old){
				$tree_category_source = $this->_tree_category_old;
			}

			$tree_category_recursive = [];
			foreach($tree_category_source as $item){
				if(!(isset($item['parent_id']) && $item['parent_id'])){
					$children = $this->_get_tree_category_recurisve($tree_category_source, $item['id']);
					if($children) 
						$item['children'] = $children;
					$tree_category_recursive[$item['id']] = $item;
				}
			}
			return $tree_category_recursive;
		}

		/**
		 * 선형으로 구성된 하위 카테고리를 반환한다.
		 * @param array $document_category
		 * @param array $list
		 * @param int $depth
		 * @return void
		 */
		private function _arrange_category(&$document_category, $list, $depth) {
			if(!count((array)$list)) return;
			$idx = 0;
			$list_order = [];
			foreach($list as $key => $val) {
				$obj = new \stdClass;
				$obj->board_id = $val['board_id'];
				$obj->category_id = $val['id'];
				$obj->parent_id = $val['parent_id'];
				$obj->title = $val['title'];
				$obj->expand = $val['expand']=='Y'?true:false;
				$obj->color = $val['color'];
				$obj->post_count = $val['post_count'];
				$obj->group_ids = $val['group_ids'];
				$obj->depth = $depth;
				$obj->child_count = 0;
				$obj->children = [];
				$obj->grant = true;
				$obj->selected = false;  // 목록에서 category 선택 후 글쓰기 할 때

				if( isset($_GET['category_id']) && $_GET['category_id'] == $val['id'] )
					$selected = true;
				else
					$selected = false;

				$obj->selected = $selected;

				$list_order[$idx++] = $obj->category_id;
				// If you have a parent category of child nodes apply data
				if($obj->parent_id)	{
					$parent_id = $obj->parent_id;
					$post_count = $obj->post_count;
					$expand = $obj->expand;
					if($selected) $expand = true;

					while($parent_id) {
						$document_category[$parent_id]->post_count += $post_count;
						$document_category[$parent_id]->children[] = $obj->category_id;
						$document_category[$parent_id]->child_count = count($document_category[$parent_id]->children);
						if($expand) $document_category[$parent_id]->expand = $expand;

						$parent_id = $document_category[$parent_id]->parent_id;
					}
				}

				$document_category[$key] = $obj;

				if(isset($val['children']) && count($val['children'])) $this->_arrange_category($document_category, $val['children'], $depth+1);
			}
			$document_category[$list_order[0]]->first = true;
			$document_category[$list_order[count($list_order)-1]]->last = true;
		}

		/**
		 * 재귀적으로 구성된 하위 카테고리를 반환한다.
		 * \includes\modules\category\category.admin.controller.php::_get_tree_category_recurisve() 동기화
		 * @param string $parent_id
		 * @return array
		 */
		private function _get_tree_category_recurisve($tree_category_source, $parent_id) {
			$new_category = [];
			foreach($tree_category_source as $item){
				if(isset($item['parent_id']) && $parent_id == $item['parent_id']){
					$children = $this->_get_tree_category_recurisve($tree_category_source, $item['id']);
					if($children) $item['children'] = $children;
					$new_category[$item['id']] = $item;
				}
			}
			return $new_category;
		}
	}
}
/* End of file category.model.php */