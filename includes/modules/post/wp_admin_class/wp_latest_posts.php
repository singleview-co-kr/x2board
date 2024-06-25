<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  WpBoardList
 * @author singleview.co.kr
 * @brief  board module admin model class
 **/
namespace X2board\Includes\Modules\Post\WpAdminClass;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\WpAdminClass\\wpLatestPosts')) {

	class wpLatestPosts extends \WP_List_Table {
		private $_n_list_per_page = 20;
		private $_a_post_status = array();
		public $items = null;  // list to display by WP_List_Table

		/**
		 * @brief return latest posts for an admin dashboard
		 */
		public function get_latest() {
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
		 * https://stackoverflow.com/questions/62594534/wp-list-table-bulk-action-delete-button-is-not-working-no-submit
		 * https://stackoverflow.com/questions/65662640/bulk-action-in-wp-list-table
		 * https://stackoverflow.com/questions/63826636/executing-calling-jquery-from-wordpress-bulk-action-function
		 */
		protected function get_bulk_actions() {
			$actions = array(
				'delete' => __('cmd_delete', X2B_DOMAIN)
			);
			return $actions;
		}

		/**
		 * @brief Detect when a bulk action is being triggered...
		 */
		public function process_bulk_action() {
			// security check!
			if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
				$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
				$action = 'bulk-' . $this->_args['plural'];
				if ( ! wp_verify_nonce( $nonce, $action ) ) {
					wp_die( __('msg_invalid_request', X2B_DOMAIN) );
				}
			}

			$s_action = $this->current_action();
			switch ( $s_action ) {
				case 'delete':
					self::delete_post();
					break;
				default: // do nothing or something else
					return;
			}
			
			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url
			// wp_redirect( esc_url_raw( add_query_arg() ) );
			wp_redirect( esc_url_raw( $_POST['_wp_http_referer'] ) );
			exit;
		}

		/**
		 * @brief 
		 */
		public static function delete_post() {
			$a_delete_post_id = esc_sql( $_POST['cart'] );
			if(count($a_delete_post_id) == 0 ) {
				return;
			}
			require_once X2B_PATH . 'includes/func.inc.php';
			\X2board\Includes\buildup_context_from_admin();
			// generate post module controller object
			$o_post_controller = \X2board\Includes\getController('post');
			// loop over the array of post IDs and delete them
			foreach( $a_delete_post_id as $n_post_id ) {
				$o_post_controller->delete_post($n_post_id, true); // $this->grant->manager = true
			}
			unset($o_post_controller);
			unset($a_delete_post_id);
		}
		  
		/**
		 * @brief 
		 * https://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
		 * https://supporthost.com/wp-list-table-tutorial/
		 **/
		public function prepare_latest_list() {

			$this->process_bulk_action();

			require_once X2B_PATH . 'includes/func.inc.php';
			\X2board\Includes\load_modules();
			$o_post = new \X2board\Includes\Modules\Post\post();
			$this->_a_post_status = array_flip($o_post->get_status_list());
			unset($o_post);

			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);

			$n_cur_page = $this->get_pagenum();

			global $wpdb;
			$s_columns = '`post_id`, `board_id`, `title`, `nick_name`, `readed_count`, `ipaddress`, `regdate_dt`, `status`';
			$s_tables = '`'.$wpdb->prefix.'x2b_posts`';
			$s_orderby = "ORDER BY `list_order` ASC";
			$s_limit = "LIMIT " . ($n_cur_page-1)*$this->_n_list_per_page . ", {$this->_n_list_per_page}";
			
			$keyword = isset($_GET['s'])?esc_attr($_GET['s']):'';
			
			if($keyword){
				$keyword = esc_sql($keyword);
				$s_where = 'WHERE 1=1'; //"`board_name` LIKE '%{$keyword}%'";
			}
			else{
				$s_where = null;
			}

			$s_total_count_query = "SELECT COUNT(*) FROM {$s_tables} {$s_where}";
			if ($wpdb->query($s_total_count_query) === FALSE) {
				wp_die($wpdb->last_error);
			} 
			else {
				$n_total = $wpdb->get_var($s_total_count_query);
				$wpdb->flush();
			}

			$s_list_query = "SELECT {$s_columns} FROM {$s_tables} {$s_where} {$s_orderby} {$s_limit}";
			if ($wpdb->query($s_list_query) === FALSE) {
				wp_die($wpdb->last_error);
			} 
			else {
				$this->items = $wpdb->get_results($s_list_query);
				$wpdb->flush();
			}
			
			$this->set_pagination_args(array('total_items'=>$n_total, 'per_page'=>$this->_n_list_per_page));
		}

		/**
		 * @brief 
		 **/
		public function get_columns(){
			return array(
				'cb' => '<input type="checkbox">',
				'wp_page_id' => __('lbl_installed_wp_page', X2B_DOMAIN),
				'title' => __('lbl_title', X2B_DOMAIN),
				'nick_name' => __('lbl_nick_name', X2B_DOMAIN),
				'readed_count' => __('lbl_readed_count', X2B_DOMAIN),
				'ipaddress' => __('lbl_ipaddress', X2B_DOMAIN),
				'regdate_dt' => __('lbl_create_date', X2B_DOMAIN),
				'status' => __('lbl_status', X2B_DOMAIN),
			);
		}

		/**
		 * @brief 
		 * https://stackoverflow.com/questions/9278772/extending-wp-list-table-handling-checkbox-options-in-plugin-administration
		 **/
		protected function column_default( $o_item, $column_name ) {
			switch( $column_name ) {
				case 'wp_page_id':
					$o_post = get_post(intval($o_item->board_id)); 
					return '<A HREF="'.$o_post->guid.'" target="_new">'.$o_post->post_title.'</A>';
				case 'title':
					$o_post = get_post(intval($o_item->board_id)); 
					return '<A HREF="'.$o_post->guid.'?'.X2B_CMD_VIEW_POST.'/'.$o_item->post_id.'" target="_new">'.mb_substr(strip_tags($o_item->title),0,50).'</A>';	
				case 'status':
					return __('lbl_'.$this->_a_post_status[$o_item->$column_name], X2B_DOMAIN);
				case 'readed_count':
				case 'ipaddress':
				case 'nick_name':
				case 'regdate_dt':
					return $o_item->$column_name;
				default:
					return print_r( $o_item, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * @brief Displaying checkboxes!
		 **/
		protected function column_cb($o_item) {
			return sprintf(
				'<input type="checkbox" name="cart[]" value="%1$d" title="Check the post" onclick="">',
				$o_item->post_id
			);
		}
	} // END CLASS
}