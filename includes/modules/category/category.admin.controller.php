<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

 /**
 * @class  categoryAdminController
 * @author singleview.co.kr
 * @brief  category module admin controller class
 **/
namespace X2board\Includes\Modules\Category;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Category\\categoryAdminController')) {

	class categoryAdminController {
		private $_board_id = 0;
		private $_empty_tree = false;  // 트리 카테고리 비우기 toggle
		private $_tree_category_old = [];
		private $_tree_category_new = null;
		private $_tree_category_recursive = null;

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// error_log(print_r('categoryAdminController', true));
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
				unset($o_current_user);
				wp_die(__('You do not have permission.', 'x2board'));
			}
			unset($o_current_user);
		}

		/**
		 * 새로운 카테고리를 생성하고 id를 반환한다.
		 * @param int $category_id
		 * @param string $new_cat_name
		 * @param int $parent_id; only if XML dump via XE2Import.class.php
		 */
		public function create_new_category($n_board_id, $new_cat_name, $n_parent_id=0) {
			global $wpdb;
			// new cat information
			$a_new_cat = array();
			$a_new_cat['category_id'] = \X2board\Includes\getNextSequence();
			$a_new_cat['board_id'] = esc_sql($n_board_id);
			$a_new_cat['title'] = esc_sql($new_cat_name);
			$a_new_cat['regdate_dt'] = date('YmdHis', current_time('timestamp'));
			$a_new_cat['last_update_dt'] = $a_new_cat['regdate_dt'];
			$result = $wpdb->insert("{$wpdb->prefix}x2b_categories", $a_new_cat);
			if( $result < 0 || $result === false ){
				unset($a_new_cat);
				unset($result);
				error_log(print_r($wpdb->last_error, true));
				return false;
			}
			unset($result);
			$n_new_cat_id = $a_new_cat['category_id'];
			unset($a_new_cat);
			
			// set new category position
			$a_data = array();
			$a_data['list_order'] = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}x2b_categories` WHERE `board_id`='$n_board_id' AND `deleted`='N'");
			$wpdb->flush();
			if( $n_parent_id ){
				$a_data['parent_id'] = $n_parent_id;
			}
			$result = $wpdb->update( "{$wpdb->prefix}x2b_categories", $a_data,
									 array( 'category_id' => esc_sql(intval($n_new_cat_id)) ) );
			unset($a_data);
			if( $result < 0 || $result === false ) {
				error_log(print_r($wpdb->last_error, true));
				return false;
			}
			return $n_new_cat_id;
		}

		/**
		 * 기존 카테고리 이름 변경 / 삭제 후 HTML 반환
		 * @param int $category_id
		 * @param string $new_cat_name
		 * @param int $parent_id; only if XML dump via XE2Import.class.php
		 */
		public function update_category($n_board_id, $s_serialized_category) {
// error_log(print_r('update_category', true));
			if($n_board_id <= 0 ) {
				return '';
			}
			$this->_board_id = $n_board_id;
			if(isset($s_serialized_category)){
				$this->_change_tree_attr($s_serialized_category);
			}
			$this->_get_old_category();
			$s_table_body = $this->_build_tree_category_sortable_html();
			$this->_save_category();
			return $s_table_body;
		}

		/**
		 * 전체 카테고리 순서 변경 후 HTML 반환
		 * @param int $category_id
		 * @param string $new_cat_name
		 * @param int $parent_id; only if XML dump via XE2Import.class.php
		 */
		public function reorder_category($n_board_id, $a_tree_category) {
// error_log(print_r('update_category', true));
			if($n_board_id <= 0 ) {
				return '';
			}
			$this->_board_id = $n_board_id;
			if(!is_array($a_tree_category)){
				return '';
			}
			$this->_get_old_category();
			$this->_change_tree_shape($a_tree_category);

			$s_table_body = $this->_build_tree_category_sortable_html();
			$this->_save_category();
			return $s_table_body;
		}

		/**
		 * 트리 형태 변경
		 * @param string $string
		 * @param array $result
		 * @return boolean
		 */
		private function _change_tree_shape($tree_category) {
			$sortable_category = [];
			foreach($tree_category as $item){
				if(isset($item->id) && $item->id){
					foreach($this->_tree_category_old as $key=>$value){
						if($item->id == $value['id']){
							$value['parent_id'] = $item->parent_id;
							$sortable_category[$item->id] = $value;
						}
					}
				}
			}
			$this->_set_new_tree_category($sortable_category);
			return true;
		}

		/**
		 * 카테고리 상하 관계를 DB에 저장함
		 * $this->_build_tree_category_sortable_html()를 먼저 실행해야 함
		 * @return none
		 */
		private function _save_category(){
			if( !is_array($this->_tree_category_recursive) )
				return;
			$list_order = 0;
			$this->__save_category($list_order, $this->_tree_category_recursive);

			// 관리자 UX의 특성 때문에 한개씩만 삭제됨
			if(count($this->_tree_category_old)) {
				global $wpdb;
				foreach($this->_tree_category_old as $key=>$val) {
					$wpdb->query("UPDATE `{$wpdb->prefix}x2b_categories` SET `deleted`= 'Y' WHERE `category_id`='{$val['id']}'");
				}
			}
		}

		/**
		 * 관리자 페이지의 계층형 카테고리를 DB에 저장한다.
		 * @param array $tree_category
		 * @param number $level
		 * @return none
		 */
		private function __save_category(&$list_order, $tree_category, $level=0){
			if($tree_category) {
				global $wpdb;
				foreach($tree_category as $key=>$value) {
					$a_data['title'] = $value['title'];
					$a_data['parent_id'] = $value['parent_id'] ? $value['parent_id'] : 0;
					$a_data['list_order'] = $list_order;
					$a_data['is_default'] = $value['is_default'];
					$a_data['last_update_dt'] = date('YmdHis', current_time('timestamp'));
					$result = $wpdb->update( "{$wpdb->prefix}x2b_categories", $a_data,
											 array( 'category_id' => esc_sql(intval($value['id'])) ) );
					unset($a_data);
					if( $result < 0 || $result === false ) {
						wp_die($wpdb->last_error);
					}
					
					$list_order++;
					unset($this->_tree_category_old[$value['id']]);
					
					if(isset($value['children']) && $value['children']){
						$this->__save_category($list_order, $value['children'], $level+1);
					}
				}
			}
		}

		/**
		 * WP 관리자 UX용 계층형 카테고리를 반환한다.
		 */
		private function _build_tree_category_sortable_html() {
			// allocae memorty to save in the DB via _save_category()
			$this->_tree_category_recursive = $this->_construct_tree_category(); 
			$html = null;
			$this->__build_tree_category_sortable_html($html, $this->_tree_category_recursive);
			return $html;
		}

		/**
		 * DB의 메뉴 구조를 계층형으로 변환한다.
		 * \includes\modules\category\category.model.php::_construct_tree_category() 동기화
		 */
		private function _construct_tree_category() {
			$tree_category_source = [];
			if($this->_tree_category_old){
				$tree_category_source = $this->_tree_category_old;
			}
			if($this->_tree_category_new || $this->_empty_tree){  // new info has priority
				$tree_category_source = $this->_tree_category_new;
			}
// error_log(print_r($this->_tree_category_new, true));
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
		 * 재귀적으로 구성된 하위 카테고리를 반환한다.
		 * \includes\modules\category\category.model.php::_get_tree_category_recurisve() 동기화
		 * @param string $parent_id
		 * @return array
		 */
		private function _get_tree_category_recurisve($tree_category_source, $parent_id){
			$new_category = [];
			foreach($tree_category_source as $item){
				if(isset($item['parent_id']) && $parent_id == $item['parent_id']){
					$children = $this->_get_tree_category_recurisve($tree_category_source, $item['id']);
					if($children) {
						$item['children'] = $children;
					}
					$new_category[$item['id']] = $item;
				}
			}
			return $new_category;
		}

		/**
		 * 관리자 페이지의 계층형 카테고리를 HTML로 그린다.
		 * @param array $tree_category
		 * @param number $level
		 * @return none
		 */
		private function __build_tree_category_sortable_html(&$html, $tree_category, $level=0){
			if($tree_category){
				foreach($tree_category as $key=>$value){
					if( $value['is_default'] == 'Y' ){
						$default_select = '('.esc_html__( 'Default category', 'x2board' ).')';
					}
					else {
						$default_select = '';
					}
					$html .= '<li id="tree_category_'.$value['id'].'" style="display: list-item;">'.
						'<div id="tree-category-'.$value['id'].'" class="menu-item-bar"><div data-id="'.$value['id'].'" class="menu-item-handle ui-sortable-handle" onclick="x2board_category_edit(\''.$value['id'].'\', \''.$value['title'].'\', \''.$value['parent_id'].'\', \''.$value['is_default'].'\')">'.
						'<span class="item-title">'.$value['title'].' '.$default_select.'</span>'.
						'<input type="hidden" id="tree-category-id-'.$value['id'].'" name="tree_category['.$value['id'].'][id]" value="'.$value['id'].'">'.
						'<input type="hidden" id="tree-category-default-'.$value['id'].'" name="tree_category['.$value['id'].'][is_default]" value="'.$value['is_default'].'">'.
						'<input type="hidden" id="tree-category-name-'.$value['id'].'" name="tree_category['.$value['id'].'][title]" value="'.$value['title'].'">'.
						'<input type="hidden" id="tree-category-parent-'.$value['id'].'" class="x2board-tree-category-parents" name="tree_category['.$value['id'].'][parent_id]" value="'.$value['parent_id'].'">'.
						'</div></div>';
					if(isset($value['children']) && $value['children']){
						$html .= '<ul>';
						$this->__build_tree_category_sortable_html($html, $value['children'], $level+1);
						$html .= '</ul>';
					}
					$html .= '</li>';
				}
			}
		}

		/**
		 * 기존 계층형 카테고리 정보를 구성한다.
		 * \includes\modules\category\category.model.php::_get_old_category() 동기화
		 */
		private function _get_old_category() {
			global $wpdb;
			$results = $wpdb->get_results("SELECT `category_id`, `title`, `parent_id`, `is_default`, `post_count` FROM `{$wpdb->prefix}x2b_categories` WHERE `board_id`='{$this->_board_id}' AND `deleted`='N' ORDER BY `list_order` ASC");
			$wpdb->flush();

			$a_category = [];
			foreach($results as $row){
				$n_category_id = intval($row->category_id);
				$a_category[$n_category_id] = ['id' => $n_category_id, 
											 'parent_id' => $row->parent_id ? intval($row->parent_id) : null, //$parent_id, 
											 'title' => $row->title, 
											 'post_count' => intval($row->post_count), //$document_count, 
											 'is_default' => $row->is_default ? $row->is_default : null, //$is_default
											];
			}
			unset($results);
			$this->_tree_category_old = $a_category;
		}

		/**
		 * 트리 형태가 유지되는 변경 / 트리 비우기
		 * @param string $string
		 * @param int $change_cat_id
		 * @param string|bool $set_default_cat
		 * @return boolean
		 * https://github.com/shukebeta/parse-str-unlimited
		 */
		private function _change_tree_attr($string) {
			$result = [];
			if($string === '') {  // emptry tree category
				$result['tree_category'] = [];
			}
			else {
				// find the pairs "name=value"
				$pairs = explode('&', $string);
				$params = array();
				foreach ($pairs as $pair) {
					// use the original parse_str() on each element
					parse_str($pair, $params);
					$k = key($params);
					if(!isset($result[$k])) {
						$result += $params;
					} else {
						$result[$k] = $this->_array_merge_recursive_distinct($result[$k], $params[$k]);
					}
				}
			}
			$this->_set_new_tree_category($result['tree_category']);
			return true;
		}

		/**
		 * 새로운 계층형 카테고리 정보를 구성한다.
		 * @param array $tree_category
		 */
		private function _set_new_tree_category($tree_category) {
			if(is_array($tree_category)){
				if( count($tree_category) == 0 ) {
					$this->_empty_tree =  true;
				}
				$this->_tree_category_new = $tree_category;
			}
		}

		// better recursive array merge function listed on the array_merge_recursive PHP page in the comments
		private function _array_merge_recursive_distinct(array $array1, array $array2) {
			$merged = $array1;
			foreach ($array2 as $key => &$value) {
				if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
					$merged[$key] = $this->_array_merge_recursive_distinct($merged[$key], $value);
				} else {
					$merged[$key] = $value;
				}
			}
			return $merged;
		}
	}
}