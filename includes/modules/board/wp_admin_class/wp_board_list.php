<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  WpBoardList
 * @author singleview.co.kr
 * @brief  board module admin model class
 **/
namespace X2board\Includes\Modules\Board\WpAdminClass;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\WpAdminClass\\WpBoardList')) {

	class wpBoardList extends \WP_List_Table {
		private $_n_list_per_page = 20;
		public $items = null;  // list to display by WP_List_Table

		public function __construct(){
			parent::__construct();
			// https://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
			// https://supporthost.com/wp-list-table-tutorial/
			$this->prepare_board_list();
		}

		/**
		 * @brief return latest posts for an admin dashboard
		 */
		public function get_latests(&$a_board_permalink) {
			global $wpdb;
			$s_columns = '`post_id`, `board_id`, `title`, `nick_name`, `regdate_dt`';
			$s_tables = '`'.$wpdb->prefix.'x2b_posts`';
			$s_orderby = "ORDER BY `list_order` asc";
			$s_limit = "LIMIT 0, 15";
			$s_query = "SELECT {$s_columns} FROM {$s_tables} {$s_orderby} {$s_limit}";
			if ($wpdb->query($s_query) === FALSE) {
				wp_die($wpdb->last_error);
			} 
			else {
				$a_post_result = $wpdb->get_results($s_query);
				$wpdb->flush();
			}
			return $a_post_result;
		}

		/**
		 * @brief 
		 **/
		public function prepare_board_list(){
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			$keyword = isset($_GET['s'])?esc_attr($_GET['s']):'';
			
			$cur_page = $this->get_pagenum();
			global $wpdb;
			if($keyword){
				$keyword = esc_sql($keyword);
				$where = "`board_name` LIKE '%{$keyword}%'";
			}
			else{
				$where = '1=1';
			}
			$n_total = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE {$where}");
			$this->items = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}x2b_mapper` WHERE {$where} ORDER BY `board_id` DESC LIMIT " . ($cur_page-1)*$this->_n_list_per_page . ",{$this->_n_list_per_page}");
			
			$this->set_pagination_args(array('total_items'=>$n_total, 'per_page'=>$this->_n_list_per_page));
		}

		/**
		 * @brief 
		 **/
		public function get_columns(){
			return array(
					'cb' => '<input type="checkbox">',
					'wp_page_id' => __('lbl_installed_wp_page', X2B_DOMAIN),
					'board_name' => __('name_x2board_title', X2B_DOMAIN),
					'create_date' => __('lbl_create_date', X2B_DOMAIN),
			);
		}

		/**
		 * @brief 
		 **/
		protected function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'wp_page_id':
					$o_post = get_post(intval($item->wp_page_id)); 
					return '<A HREF='.$o_post->guid.' target="_blank">'.__('lbl_visit_page', X2B_DOMAIN).' - '.$o_post->post_title.'</A>';
				case 'board_name':
					$o_post = get_post(intval($item->wp_page_id)); 
					return '<A HREF='.admin_url( 'admin.php?page='.X2B_CMD_ADMIN_VIEW_BOARD_UPDATE.'&board_id='.$o_post->ID ).'>'.__('lbl_configure_board', X2B_DOMAIN).' - '.$item->board_title.'</A>';
				case 'create_date':
					return $item->$column_name;
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
		}
	} // END CLASS
}