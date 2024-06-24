<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  boardAdminModel
 * @author singleview.co.kr
 * @brief  board module admin model class
 **/
namespace X2board\Includes\Modules\Board;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardAdminModel')) {
	
	require_once X2B_PATH . 'includes\classes\user_define_fields\UserDefineListFields.class.php';

	class boardAdminModel {
		private $_a_on_list_config = array();
		private $_a_unchosen_list_fields = array();
		private $_a_field_introduction = array();

		private $_a_board_permalink = array();

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// var_dump('boardAdminModel');
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
				unset($o_current_user);
				wp_die(__('msg_no_permission', X2B_DOMAIN));
			}
			unset($o_current_user);
		}

		/**
		 * @brief return latest files for an admin dashboard
		 */
		public function get_latest_files() {
			require_once X2B_PATH . 'includes\modules\file\wp_admin_class\wp_latest_files.php';
			$o_latest_file = new \X2board\Includes\Modules\File\WpAdminClass\wpLatestFiles();
			$a_file_result = $o_latest_file->get_latest();
			unset($o_latest_file);
			foreach($a_file_result as $_ => $o_file) {
				$o_file->source_filename = \X2board\Includes\cut_str(\strip_tags($o_file->source_filename), 30, '...');
				$o_file->s_regdate = date_format(date_create($o_file->regdate), 'Y-m-d H:i:s'); 
			}
			return $a_file_result;
		}

		/**
		 * @brief return latest files for an admin dashboard
		 */
		public function get_latest_files_wp_list() {
			require_once X2B_PATH . 'includes\modules\file\wp_admin_class\wp_latest_files.php';
			$o_latest_file = new \X2board\Includes\Modules\File\WpAdminClass\wpLatestFiles();
			$o_latest_file->prepare_latest_list();
			return $o_latest_file;
		}


		/**
		 * @brief return latest comments for an admin dashboard
		 */
		public function get_latest_comments() {
			require_once X2B_PATH . 'includes\modules\comment\wp_admin_class\wp_latest_comments.php';
			$o_latest_comment = new \X2board\Includes\Modules\Comment\WpAdminClass\wpLatestComments();
			$a_comment_result = $o_latest_comment->get_latest();
			unset($o_latest_comment);

			foreach($a_comment_result as $_ => $o_comment) {
				if(!isset($this->_a_board_permalink[$o_comment->board_id])) {
					$this->_get_board_info($o_comment);
				}

				$s_page_permlink = $this->_a_board_permalink[$o_comment->board_id]->s_page_permlink;
				$s_use_rewrite = $this->_a_board_permalink[$o_comment->board_id]->s_use_rewrite;

				if( $s_use_rewrite == 'Y' ) { // try best to provie prettier post URL as possible
					$s_comment_permlink = $s_page_permlink.'/'.$o_comment->parent_post_id;
				}
				else {  // http://127.0.0.1/wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2?view_post/4147
					$s_comment_permlink .= $s_page_permlink.'?'.X2B_CMD_VIEW_POST.'/'.$o_comment->parent_post_id;
				}

				$s_comment_permlink .= '#comment_'.$o_comment->comment_id;

				$o_comment->s_comment_permlink = $s_comment_permlink;
				$o_comment->content = \X2board\Includes\cut_str(\strip_tags($o_comment->content), 30, '...');
				$o_comment->s_regdate = date_format(date_create($o_comment->regdate_dt), 'Y-m-d H:i:s'); 
			}
			return $a_comment_result;
		}

		/**
		 * @brief return latest comments for an admin dashboard
		 */
		public function get_latest_comments_wp_list() {
			require_once X2B_PATH . 'includes\modules\comment\wp_admin_class\wp_latest_comments.php';
			$o_latest_comment = new \X2board\Includes\Modules\Comment\WpAdminClass\wpLatestComments();
			$o_latest_comment->prepare_latest_list();
			return $o_latest_comment;
		}

		/**
		 * @brief return latest posts for an admin dashboard
		 */
		public function get_latest_posts() {
			require_once X2B_PATH . 'includes\modules\post\wp_admin_class\wp_latest_posts.php';
			$o_latest_post = new \X2board\Includes\Modules\Post\WpAdminClass\wpLatestPosts();
			$a_post_result = $o_latest_post->get_latest();
			unset($o_latest_post);

			foreach($a_post_result as $_ => $o_post) {
				if(!isset($this->_a_board_permalink[$o_post->board_id])) {
					$this->_get_board_info($o_post);
				}

				$s_page_permlink = $this->_a_board_permalink[$o_post->board_id]->s_page_permlink;
				$s_use_rewrite = $this->_a_board_permalink[$o_post->board_id]->s_use_rewrite;

				if( $s_use_rewrite == 'Y' ) { // try best to provie prettier post URL as possible
					$s_post_permlink = $s_page_permlink.'/'.$o_post->post_id;
				}
				else {  // http://127.0.0.1/wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2?view_post/4147
					$s_post_permlink .= $s_page_permlink.'?'.X2B_CMD_VIEW_POST.'/'.$o_post->post_id;
				}

				$o_post->s_post_permlink = $s_post_permlink;
				$o_post->title = \X2board\Includes\cut_str(\strip_tags($o_post->title), 30, '...');
				$o_post->s_regdate = date_format(date_create($o_post->regdate_dt), 'Y-m-d H:i:s');
			}
			return $a_post_result;
		}

		/**
		 * @brief return latest posts for an admin dashboard
		 */
		public function get_latest_posts_wp_list() {
			require_once X2B_PATH . 'includes\modules\post\wp_admin_class\wp_latest_posts.php';
			$o_latest_post = new \X2board\Includes\Modules\Post\WpAdminClass\wpLatestPosts();
			$o_latest_post->prepare_latest_list();
			return $o_latest_post;
		}

		/**
		 * @brief memorize board configuration info
		 */
		private function _get_board_info($o_post) {
			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings( $o_post->board_id );
			if( $o_rst->b_ok === false ) {
				return false;
			}
			$s_page_permlink = site_url().'/'.urlencode(urldecode(get_post($o_post->board_id)->post_name));
			$s_use_rewrite = $o_rst->a_board_settings['board_use_rewrite'];
			$o_board_info = new \stdClass();
			$o_board_info->s_page_permlink = $s_page_permlink;
			$o_board_info->s_use_rewrite = $s_use_rewrite;
			$this->_a_board_permalink[$o_post->board_id] = $o_board_info;
			unset($o_rst);
		}

		/**
		 * 
		 */
		public function build_user_define_list_fields() {
			$o_post_user_define_list_fields = \X2board\Includes\Classes\UserDefineListFields::getInstance();

			// retrieve all available fields for a list
			$a_virtual_vars = $o_post_user_define_list_fields->get_virtual_list_field_info();
			foreach( $a_virtual_vars as $s_field_type => $s_introduction ) {
				$o_single_field = new \stdClass();
				$o_single_field->eid = $s_field_type;
				$o_single_field->var_name = $s_field_type;
				$o_single_field->var_type = $s_field_type;
				$this->_a_unchosen_list_fields[$s_field_type] = $o_single_field;

				// retrieve introduction for each virtual list field
				$this->_a_field_introduction[$s_field_type] = $s_introduction;
			}
			unset($a_virtual_vars);

			// retrieve all user defined fields
			$n_board_id = intval(sanitize_text_field($_GET['board_id'] ));
			$s_columns = '`var_name`, `var_type`, `var_desc`, `eid`';  // , `meta_key`
			global $wpdb;
			$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC");

			$a_all_field_info = $o_post_user_define_list_fields->get_all_user_define_field_info();

			// retrieve introduction for each user define list field
			foreach( $a_all_field_info as $s_field_type => $o_field ) {
				if($o_field['display_on_list']) {
					$this->_a_field_introduction[$s_field_type] = $o_field['introduction'];
				}
			}
			foreach( $a_temp as $_ => $o_field ) {
				if($a_all_field_info[$o_field->var_type]['display_on_list']) {
					$o_single_field = new \stdClass();
					$o_single_field->eid = $o_field->eid;
					$o_single_field->var_name = $o_field->var_name;
					$o_single_field->var_type = $o_field->var_type;
					
					$this->_a_unchosen_list_fields[$o_field->eid] = $o_single_field;					
				}
			}
			unset($a_all_field_info);
			unset($a_temp);

			// retrieve all choosed fields for a list
			global $A_X2B_ADMIN_BOARD_SETTINGS;
			$this->_a_on_list_config = $o_post_user_define_list_fields->get_list_config($A_X2B_ADMIN_BOARD_SETTINGS['board_list_fields']);

			// exclude already choosed fields from all available fields for a list
			foreach( $this->_a_on_list_config as $_ => $o_field ) {
				if($this->_a_unchosen_list_fields[$o_field->eid]) {
					unset($this->_a_unchosen_list_fields[$o_field->eid]);
				}
			}
			unset($o_post_user_define_list_fields);
		}

		/**
		 * WP user field UI Callback
		 *
		 * Renders WP user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @return void
		 */
		public function render_user_field_ui() {
			$s_html = '<div class="x2board-fields-wrap">
						<div class="x2board-fields-message">'.__('about_list_field_configuration', X2B_DOMAIN).'</div>
						<div class="x2board-fields-left">
							<h3 class="x2board-fields-h3">'.__('lbl_available_user_define_fields', X2B_DOMAIN).'</h3>
							<ul class="x2board-fields">
								<li class="x2board-list-config-fields-default left">
									<button type="button" class="x2board-fields-header">'.
										__('lbl_unlisted_field', X2B_DOMAIN).
										'<span class="fields-up">▲</span>
										<span class="fields-down">▼</span>
									</button>
									<ul class="x2board-list-config-fields-list x2board-fields-content">';
			$s_html .= $this->_render_off_list_fields();
			$s_html .=				'</ul>
								</li>
							</ul>
						</div>
						<div class="x2board-fields-right">
							<div class="x2board-fields x2board-sortable-fields">
								<h3 class="x2board-fields-h3">'.__('lbl_listed_fields', X2B_DOMAIN).'</h3>
								<div class="description">'.__('lbl_drag_from_left', X2B_DOMAIN).'</div>
								<ul class="x2board-skin-fields x2board-list-config-fields-sortable connected-list-config-sortable">';
					$s_html .= $this->_render_on_list_fields();
					$s_html .= 	'</ul>
								<div class="description"><button type="button" class="button button-small" onclick="x2board_skin_fields_reset()">'.__('cmd_reset_configuration', X2B_DOMAIN).'</button></div>
							</div>
						</div>
					</div>';
			echo $s_html;
		}

		/**
		 * @brief return the default list configration value
		 **/
		// function getDefaultListConfig($module_srl)
		public function _render_off_list_fields() {
			$s_html = null;
			foreach($this->_a_unchosen_list_fields as $s_eid => $o_field_info) {
				$s_html .= '<li class="default '.$s_eid.'">
								<div class="x2board-extends-fields">
									<div class="x2board-fields-title toggle x2board-list-config-field-handle">
										<button type="button">'.
										esc_html($o_field_info->var_name).
										'<span class="fields-up">▲</span>
											<span class="fields-down">▼</span>
										</button>
									</div>
									
								</div>
								<div class="x2board-fields-content">';
				$s_html .=			'<input type="hidden" class="field_data eid" value="'.esc_attr($o_field_info->eid).'">';
				$s_html .=			'<input type="hidden" class="field_data var_type" value="'.esc_attr($o_field_info->var_type).'">';
				$s_html .=			'<input type="hidden" class="field_data var_name" value="'.esc_attr($o_field_info->var_name).'">';
				$s_html .=			'<div class="attr-row">
										<label class="attr-name" for="'.$s_eid.'_field_label">'.__('lbl_field_introduction', X2B_DOMAIN).'</label>
										<div class="attr-value">'.$this->_a_field_introduction[$o_field_info->var_type].'</div>
									</div>
								</div>
							</li>';
			}
			return $s_html;
		}

		/**
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		private function _render_on_list_fields() {
			$s_html = null;			
			foreach($this->_a_on_list_config as $_=>$o_list_field) {
				$s_html .= '<li class="default '.$o_list_field->var_type.'">
								<div class="x2board-extends-fields">
									<div class="x2board-fields-title toggle x2board-list-config-field-handle">
										<button type="button">'.
										esc_html($o_list_field->var_type).
										'<span class="fields-up">▲</span>
											<span class="fields-down">▼</span>
										</button>
									</div>
									<div class="x2board-fields-toggle">
										<button type="button" class="fields-list-config-remove" title="'.__('cmd_remove', X2B_DOMAIN).'">X</button>
									</div>
								</div>
								<div class="x2board-fields-content">';
				$s_html .= 			'<input type="hidden" id="'.$o_list_field->var_type.'_eid" name="board_list_fields['.$o_list_field->eid.'][eid]" class="field_data eid" value="'.$o_list_field->eid.'">';
				$s_html .= 			'<input type="hidden" id="'.$o_list_field->var_type.'_var_type" name="board_list_fields['.$o_list_field->eid.'][var_type]" class="field_data var_type" value="'.$o_list_field->var_type.'">';
				$s_html .= 			'<input type="hidden" id="'.$o_list_field->var_type.'_var_name" name="board_list_fields['.$o_list_field->eid.'][var_name]" class="field_data var_name" value="'.$o_list_field->var_name.'">';
				$s_html .=			'<div class="attr-row">
										<label class="attr-name" for="'.$o_list_field->var_type.'_field_label">'.__('lbl_field_introduction', X2B_DOMAIN).'</label>
										<div class="attr-value">'.$this->_a_field_introduction[$o_list_field->var_type].'</div>
									</div>
								</div>
							</li>';
			}
			return $s_html;
		}
	}
}