<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * A class to handle extra variables used in posts
 */
namespace X2board\Includes\Classes;
if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

/**
 * Each value of the extra vars
 *
 * @author XEHub (developers@xpressengine.com)
 */
if (!class_exists('\\X2board\\Includes\\Classes\\AdminUnchosenDefaultUserDefineFieldsItem')) {

	class AdminUnchosenDefaultUserDefineFieldsItem extends UserDefineFields {

		protected $_s_field_type = null;
		protected $_s_field_label = null;
		protected $_s_default_value = null;
		protected $_s_description = null;
		protected $_s_required = null;

		// optional
		protected $_s_option_field = null;
		protected $_a_roles = array();
		protected $_s_email_permission = null;
		// protected $_s_secret_permission = null;
		protected $_s_notice_permission = null;
		protected $_a_notice = array();
		protected $_s_placeholder = null;
		protected $_s_show_document = null;
		protected $_s_hidden = null;
		
		/**
		 * Constructor
		 *
		 * @param string $a_single_field field information array
		 * @return void
		 */
		public function __construct($a_single_field) {
			parent::__construct();

// var_dump($a_single_field);
			// default fields
			$this->_s_field_type = $a_single_field['field_type'];
			$this->_s_field_label = $a_single_field['field_label'];
			$this->_s_description = $a_single_field['description'];

			// optional fields
			if(isset($a_single_field['default_value'])) {
				$this->_s_default_value = $a_single_field['default_value'];
			}
			if(isset($a_single_field['required'])) {
				$this->_s_required = $a_single_field['required'];
			}
			if(isset($a_single_field['option_field'])) {
				$this->_s_option_field = $a_single_field['option_field'];
			}
			if(isset($a_single_field['placeholder'])) {
				$this->_s_placeholder = $a_single_field['placeholder'];
			}
			if(isset($a_single_field['roles']) && is_array($a_single_field['roles']) ) {
				$this->_a_roles = $a_single_field['roles'];
			}
			if(isset($a_single_field['email_permission']) ) {
				$this->_s_email_permission = $a_single_field['email_permission'];
			}
			// if(isset($a_single_field['secret_permission']) ) {
			// 	$this->_s_secret_permission = $a_single_field['secret_permission'];
			// }
			if(isset($a_single_field['notice_permission']) ) {
				$this->_s_notice_permission = $a_single_field['notice_permission'];
			}
			if(isset($a_single_field['notice']) && is_array($a_single_field['notice']) ) {
				$this->_a_notice = $a_single_field['notice'];
			}
			if(isset($a_single_field['show_document']) ) {
				$this->_s_show_document = $a_single_field['show_document'];
			}
			if(isset($a_single_field['hidden']) ) {
				$this->_s_hidden = $a_single_field['hidden'];
			}	
		}

		/**
		 * Returns a form based on its type
		 *
		 * @return string Returns a widget html.
		 */
		public function get_widget_html() {
// var_dump('unchosen default widget of '.$this->_s_field_type);
			$s_html = null;
			$s_html .= 				'<li class="default '.$this->_s_field_type.'">
										<div class="x2board-extends-fields">
											<div class="x2board-fields-title toggle x2board-field-handle">
												<button type="button">'.
												esc_html($this->_s_field_label).
												'<span class="fields-up">▲</span>
													<span class="fields-down">▼</span>
												</button>
											</div>
											<div class="x2board-fields-toggle">
												<button type="button" class="fields-remove" title="'.__('cmd_remove', X2B_DOMAIN).'">X</button>
											</div>
										</div>
										<div class="x2board-fields-content">';
			$s_html .=						'<input type="hidden" class="field_data field_type" value="'.esc_attr($this->_s_field_type).'">';
			$s_html .= 						'<input type="hidden" class="field_data field_label" value="'.esc_attr($this->_s_field_label).'">';
			if(!is_null($this->_s_option_field)) {  // if(isset($item['option_field'])) {
				$s_html .= 					'<input type="hidden" class="field_data option_field" value="'.esc_attr($this->_s_option_field).'">';
			}
			$s_html .=					 	'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_field_label">'.__('lbl_field_label', X2B_DOMAIN).'</label>
												<div class="attr-value">
													<input type="text" id="'.$this->_s_field_type.'_field_label" class="field_data field_name" placeholder="'.esc_attr($this->_s_field_label).'">
												</div>
											</div>';
			if(!is_null($this->_a_roles)) {  // if(isset($item['roles'])) {
				$s_html .= 					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_roles">'.__('msg_whom_to_show', X2B_DOMAIN).'</label>
												<div class="attr-value">
													<select id="'.$this->_s_field_type.'_roles" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">
														<option value="all" selected>'.__('opt_role_all_users', X2B_DOMAIN).'</option>
														<option value="author">'.__('opt_role_loggedin_users', X2B_DOMAIN).'</option>
														<option value="roles">'.__('opt_role_customize', X2B_DOMAIN).'</option>
													</select>
													<div class="x2board-permission-read-roles-view x2board-hide">';
				foreach(get_editable_roles() as $roles_key=>$roles_value) {
					$s_mandatory = $roles_key == 'administrator' ? 'onclick="return false" checked' : '';
					$s_html .=							'<label><input type="checkbox" class="field_data roles_checkbox" value="'.$roles_key.'" '.$s_mandatory.'> '. _x($roles_value['name'], 'User role').'</label>';
				}
				$s_html .=							'</div>
												</div>
											</div>';
			}
			
			/*if(!is_null($this->_s_secret_permission)) { // if(isset($item['secret_permission'])) {
				$s_html .=					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_secret">'.__('Secret post', X2B_DOMAIN).'</label>
												<div class="attr-value">
													<select id="'.$this->_s_field_type.'_secret" class="field_data secret-roles" onchange="x2board_fields_permission_roles_view(this)">
														<option value="all">'.__('opt_role_all_users', X2B_DOMAIN).'</option>';
				$s_selected = $this->_s_secret_permission == 'author' ? 'selected' : '';
				$s_html .=								'<option value="author" '.$s_selected.'  >'.__('opt_role_loggedin_users', X2B_DOMAIN).'</option>';
				$s_selected = $this->_s_secret_permission == 'roles' ? 'selected' : '';
				$s_html .=								'<option value="roles" '.$s_selected.'>'.__('opt_role_customize', X2B_DOMAIN).'</option>
													</select>';
				$s_hide = $this->_s_secret_permission != 'roles' ? 'x2board-hide' : '';
				$s_html .=							'<div class="x2board-permission-read-roles-view '.$s_hide.'">';
				foreach(get_editable_roles() as $roles_key=>$roles_value) {
					$s_mandatory = $roles_key=='administrator' ? 'onclick="return false" checked' : '';
					$s_html .=							'<label><input type="checkbox" class="field_data secret_checkbox" value="'.$roles_key.'" '.$s_mandatory.'> '._x($roles_value['name'], 'User role').'</label>';
				}
				$s_html .=							'</div>
												</div>
											</div>';
			}*/
			if(!is_null($this->_s_notice_permission)) { // if(isset($item['notice_permission'])) {
				$s_html .=					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'-notice">'.__('lbl_notice', X2B_DOMAIN).'</label>
												<div class="attr-value">
													<select id="'.$this->_s_field_type.'-notice" class="field_data notice-roles" onchange="x2board_fields_permission_roles_view(this)">
														<option value="all">'.__('opt_role_all_users', X2B_DOMAIN).'</option>';
				$s_selected = $this->_s_notice_permission == 'author' ? 'selected' : '';
				$s_html .=								'<option value="author" '.$s_selected.'>'.__('opt_role_loggedin_users', X2B_DOMAIN).'</option>';
				$s_selected = $this->_s_notice_permission == 'roles' ? 'selected' : '';
				$s_html .=								'<option value="roles" '.$s_selected.'>'.__('opt_role_customize', X2B_DOMAIN).'</option>
													</select>';
				$s_hide = $this->_s_notice_permission != 'roles' ? 'x2board-hide' : '';
				$s_html .=							'<div class="x2board-permission-read-roles-view '.$s_hide.'">';
				foreach(get_editable_roles() as $roles_key=>$roles_value) {
					$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
					$s_checked = ( $roles_key=='administrator' || in_array($roles_key, $this->_a_notice)) ? 'checked' : '';
					$s_html .=							'<label><input type="checkbox" class="field_data notice_checkbox" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
				}
				$s_html .=							'</div>
												</div>
											</div>';
			}
			if(!is_null($this->_s_default_value)) {
				$s_html .=					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_default_value">'.__('lbl_default_value', X2B_DOMAIN).'</label>
												<div class="attr-value">';
				/*if($this->_s_field_type == 'search') {
					$s_html .=						'<select id="'.$this->_s_field_type.'_default_value" class="field_data default_value">
														<option value="1">'.__('Title and content', X2B_DOMAIN).'</option>
														<option value="2">'.__('Title (secret post)', X2B_DOMAIN).'</option>
														<option value="3">'.__('Hide from search', X2B_DOMAIN).'</option>
													</select>';
				}
				else {*/
					$s_html .=						'<input type="text" class="field_data default_value">';
				// }
				$s_html .=						'</div>
											</div>';
			}
			if(!is_null($this->_s_placeholder)) { // if(isset($item['placeholder'])) {
				$s_html .=					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_placeholder">Placeholder</label>';
				$s_placeholder = $this->_s_placeholder ? $this->_s_placeholder : '';
				$s_html .=						'<div class="attr-value"><input type="text" id="'.$this->_s_field_type.'_placeholder" class="field_data placeholder" value="'.$s_placeholder.'"></div>
											</div>';
			}
			if(!is_null($this->_s_description)) { //if(isset($item['description'])) {
				$s_html .=					'<div class="attr-row">
												<label class="attr-name" for="'.$this->_s_field_type.'_description">설명</label>
												<div class="attr-value">
													<input type="text" id="'.$this->_s_field_type.'_description" class="field_data field_description" value="'.$this->_s_description.'">
												</div>
											</div>';
			}
			// if(isset($item['required']) || isset($item['show_document']) || isset($item['hidden'])) {
			if(!is_null($this->_s_required) || !is_null($this->_s_show_document) || !is_null($this->_s_hidden)) {
				$s_html .=					'<div class="attr-row">';
				if(!is_null($this->_s_required)) { // if(isset($item['required'])) {
					$s_checked = $this->_s_required == '1' ? 'checked' : '';
					$s_html .=					'<label>
													<input type="hidden" class="field_data required" value="">
													<input type="checkbox" class="field_data required" value="1" '.$s_checked.'>'.__('lbl_required', X2B_DOMAIN).'
												</label>';
				}
				if(!is_null($this->_s_show_document )) { // if(isset($item['show_document'])) {
					$s_checked = $this->_s_show_document == '1' ?  'checked' : '';
					$s_html .=					'<label>
													<input type="hidden" class="field_data show_document" value="">
													<input type="checkbox" class="field_data show_document" value="1" '.$s_checked.'>'.__('lbl_display_on_content', X2B_DOMAIN).'
												</label>';
				}
				if(!is_null($this->_s_hidden)) {  // if(isset($item['hidden'])) {
					$s_checked = $this->_s_hidden == '1' ?  'checked' : '';
					$s_html .=					'<label>
													<input type="hidden" class="field_data hidden" value="">
													<input type="checkbox" class="field_data hidden" value="1" '.$s_checked.'>'.__('lbl_hiding', X2B_DOMAIN).'
												</label>';
				}
				$s_html .=					'</div>';
			}
			$s_html .=					'</div>
									</li>';
			return $s_html; //join(PHP_EOL, $buff);
		}
	}
}
/* End of file AdminUnchosenDefaultUserDefineFieldsItem.class.php */