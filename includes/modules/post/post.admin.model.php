<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postAdminModel class
 * Post the module's admin model class
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 * @version 0.1
 */
namespace X2board\Includes\Modules\Post;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\postAdminModel')) {
	
	require_once X2B_PATH . 'includes\classes\user_define_fields\AdminUserDefineFields.class.php';

	class postAdminModel {
		private $_a_unchosen_user_default_fields = array();
		private $_a_user_define_fields = array();
		private $_a_extended_fields = array();

		/**
		 * @brief constructor
		 **/
		public function __construct(){
// var_dump('categoryAdminModel');
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_x2board') ) {
				unset($o_current_user);
				wp_die(__('You do not have permission.', 'x2board'));
			}
			unset($o_current_user);
			$this->_build_user_define_fields();
		}

		/**
		 * retrieve user define fields from DB
		 */
		private function _build_user_define_fields() {
			$n_board_id = intval(sanitize_text_field($_GET['board_id'] ));
			$s_columns = '`var_name`, `var_type`, `var_is_required`, `var_search`, `var_default`, `var_desc`, `eid`, `json_param`';  // , `meta_key`
			global $wpdb;
			$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC");
// var_dump($a_temp);
			
			foreach( $a_temp as $_ => $o_field ) {
				$a_other_field = unserialize($o_field->json_param);

				$a_single_field['field_type'] = $o_field->var_type;
				$a_single_field['field_name'] = $o_field->var_name;
				$a_single_field['meta_key'] = $o_field->eid;
				$a_single_field['search'] = $o_field->var_search;
				$a_single_field['default_value'] = $o_field->var_default;
				$a_single_field['description'] = $o_field->var_desc;
				$a_single_field['required'] = $o_field->var_is_required;

				$a_single_field = array_merge($a_single_field, $a_other_field);
				$this->_a_user_define_fields[$o_field->eid] = $a_single_field;

				unset($a_single_field);
				unset($a_other_field);
			}
			unset($a_temp);

			$o_post_user_define_fields = \X2board\Includes\Classes\AdminUserDefineFields::getInstance();
			$o_post_user_define_fields->set_user_define_fields_from_db($this->_a_user_define_fields);
			$this->a_user_define_fields = $o_post_user_define_fields->get_user_define_fields();
			$this->_a_unchosen_user_default_fields = $o_post_user_define_fields->get_unchosen_default_fields();
			$this->_a_extended_fields = $o_post_user_define_fields->get_extended_fields();
// var_dump($this->_a_unchosen_user_default_fields);
			unset($o_post_user_define_fields);
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
						<!---div class="x2board-fields-message">
							일부 스킨에서는 입력필드 설정이 적용되지 않습니다.
						</div --->
						<div class="x2board-fields-left">
							<h3 class="x2board-fields-h3">'.__('Available field', 'x2board').'</h3>
							<ul class="x2board-fields">
								<li class="x2board-fields-default left">
									<button type="button" class="x2board-fields-header">'.
										__('Basic field', 'x2board').
										'<span class="fields-up">▲</span>
										<span class="fields-down">▼</span>
									</button>
									<ul class="x2board-fields-list x2board-fields-content">';
			$s_html .= $this->_render_unchosen_default_fields();
			$s_html .=				'</ul>
								</li>
								<li class="x2board-fields-extension left">
								<button type="button" class="x2board-fields-header">'.
								__('Extended fields', 'x2board').
									'<span class="fields-up">▲</span>
									<span class="fields-down">▼</span>
								</button>
								<ul class="x2board-fields-list x2board-fields-content">';
			
			if($this->_a_extended_fields) {
				$s_html .= $this->_render_user_extended_fields();
			}

			$s_html .= 		'</ul>
						</li>
					</ul>
				</div>
				<div class="x2board-fields-right">
					<div class="x2board-fields x2board-sortable-fields">
						<h3 class="x2board-fields-h3">'.__('User define fields presentation', 'x2board').'</h3>
						<div class="description">'.__('Drag from the left section to activate', 'x2board').'</div>
						<ul class="x2board-skin-fields x2board-fields-sortable connected-sortable">';
			$s_html .= $this->_render_user_define_fields();
			$s_html .= 	'</ul>
						<div class="description"><button type="button" class="button button-small" onclick="x2board_skin_fields_reset()">'.__('Reset configuration', 'x2board').'</button></div>
					</div>
				</div>
			</div>';
			echo $s_html;
		}

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		private function _render_unchosen_default_fields() {
			$s_html = null;
			foreach($this->_a_unchosen_user_default_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		private function _render_user_extended_fields() {
			$s_html =	null;
			foreach($this->_a_extended_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		private function _render_user_define_fields() {
			$s_html = null;
			foreach($this->a_user_define_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}
	}
}