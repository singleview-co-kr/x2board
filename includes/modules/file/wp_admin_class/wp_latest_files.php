<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  WpBoardList
 * @author singleview.co.kr
 * @brief  board module admin model class
 **/
namespace X2board\Includes\Modules\File\WpAdminClass;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\File\\WpAdminClass\\wpLatestFiles')) {

	class wpLatestFiles extends \WP_List_Table {
		private $_n_list_per_page = 20;
		private $_a_file_status = array('Y'=> 'valid', 'N'=> 'pending');
		private $_o_post_model = null;
		private $_o_comment_model = null;
		private $_o_file_model = null;
		public $items = null;  // list to display by WP_List_Table

		public function __construct(){
			parent::__construct();
			require_once X2B_PATH . 'includes/func.inc.php';
			\X2board\Includes\buildup_context_from_admin();
			$this->_o_post_model = \X2board\Includes\getModel('post');
			$this->_o_comment_model = \X2board\Includes\getModel('comment');
			$this->_o_file_model = \X2board\Includes\getModel('file');
		}

		/**
		 * @brief return latest posts for an admin dashboard
		 */
		public function get_latest() {
			global $wpdb;
			$s_columns = '`file_id`, `source_filename`, `regdate`';
			$s_tables = '`'.$wpdb->prefix.'x2b_files`';
			$s_orderby = "ORDER BY `file_id` desc";
			$s_limit = "LIMIT 0, 15";
			$s_query = "SELECT {$s_columns} FROM {$s_tables} {$s_orderby} {$s_limit}";
			if ($wpdb->query($s_query) === FALSE) {
				wp_die($wpdb->last_error);
			} 
			else {
				$a_file_result = $wpdb->get_results($s_query);
				$wpdb->flush();
			}
			return $a_file_result;
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
					self::delete_comment();
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
		public static function delete_comment() {
			$a_delete_file_id = esc_sql( $_POST['cart'] );
			if(count($a_delete_file_id) == 0 ) {
				return;
			}
			require_once X2B_PATH . 'includes/func.inc.php';
			\X2board\Includes\buildup_context_from_admin();
			// generate comment module controller object
			$o_file_controller = \X2board\Includes\getController('file');
			global $wpdb;
			// loop over the array of post IDs and delete them
			foreach( $a_delete_file_id as $n_file_id ) {
				$o_file = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}x2b_files` WHERE `file_id`={$n_file_id}");
				$o_file_controller->delete_file($o_file);
				unset($o_file);
			}
			unset($o_file_controller);
			unset($a_delete_file_id);
		}

		/**
		 * @brief 
		 * https://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
		 * https://supporthost.com/wp-list-table-tutorial/
		 **/
		public function prepare_latest_list(){
			$this->process_bulk_action();

			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);

			$n_cur_page = $this->get_pagenum();

			global $wpdb;
			$s_columns = '`file_id`, `board_id`, `upload_target_id`, `sid`, `source_filename`, `ipaddress`, `isvalid`, `author`, `file_size`, `regdate`';
			$s_tables = '`'.$wpdb->prefix.'x2b_files`';
			$s_orderby = "ORDER BY `file_id` DESC";
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
				'source_filename' => __('lbl_filename', X2B_DOMAIN),
				'file_size' => __('lbl_filesize', X2B_DOMAIN),
				'author' => __('lbl_nick_name', X2B_DOMAIN),
				'ipaddress' => __('lbl_ipaddress', X2B_DOMAIN),
				'regdate' => __('lbl_create_date', X2B_DOMAIN),
				'isvalid' => __('lbl_status', X2B_DOMAIN),
			);
		}

		/**
		 * @brief 	
		 **/
		protected function column_default( $o_item, $column_name ) {
			switch( $column_name ) {
				case 'wp_page_id':
					$o_post = get_post(intval($o_item->board_id)); 
					$s_wp_page_guid = $o_post->guid;
					$s_wp_page_title = $o_post->post_title;
					unset($o_post);
					$o_x2b_post = $this->_o_post_model->get_post($o_item->upload_target_id, true);
					if($o_x2b_post->is_exists()) {
						$s_title = ($o_x2b_post->get_title(20));
						unset($o_x2b_post);
						return '<A HREF="'.$s_wp_page_guid.'?'.X2B_CMD_VIEW_POST.'/'.$o_item->upload_target_id.'" target="_new">'.$s_wp_page_title.' - '.$s_title.'</A>';	
					}
					$o_x2b_comment = $this->_o_comment_model->get_comment($o_item->upload_target_id, true);
					if($o_x2b_comment->is_exists()) {
						$s_parent_post_id = ($o_x2b_comment->get('parent_post_id'));
						$s_comment_content = $o_x2b_comment->get_content();
						unset($o_x2b_comment);
						unset($o_x2b_post);
						return '<A HREF="'.$s_wp_page_guid.'?'.X2B_CMD_VIEW_POST.'/'.$s_parent_post_id.'#comment_'.$o_item->upload_target_id.'" target="_new">'.$s_wp_page_title.' - '.mb_substr(strip_tags($s_comment_content),0,50).'</A>';	
					}
					unset($o_x2b_comment);
					unset($o_x2b_post);
					return '<FONT COLOR="red">zombie file</FONT>';
				case 'source_filename':
					$o_post = get_post(intval($o_item->board_id)); 
					$s_wp_page_guid = $o_post->guid;
					unset($o_post);
					\X2board\Includes\Classes\Context::set('board_id', $o_item->board_id);
					$s_download_url =  $this->_o_file_model->get_download_url($o_item->file_id, $o_item->sid);
					return '<A HREF="'.$s_wp_page_guid.$s_download_url.'">'.mb_substr(strip_tags($o_item->source_filename),0,50).'</A>';	
				case 'file_size':
					return \X2board\Includes\Classes\FileHandler::filesize($o_item->file_size);
				case 'author':
					return $o_item->author > 0 ? get_userdata($o_item->author)->display_name : '';
				case 'isvalid':
					return __('lbl_'.$this->_a_file_status[$o_item->isvalid], X2B_DOMAIN);
				case 'ipaddress':
				case 'regdate':
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
				$o_item->file_id
			);
		}
	} // END CLASS
}