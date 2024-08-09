<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * A class to handle extra variables used in posts
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Each value of the extra vars
 *
 * @author XEHub (developers@xpressengine.com)
 */
if ( ! class_exists( '\\X2board\\Includes\\Classes\\AdminExtendedUserDefineFieldsItem' ) ) {

	class AdminExtendedUserDefineFieldsItem extends UserDefineFields {

		protected $_s_field_type  = null;
		protected $_s_field_label = null;

		// optional
		protected $_a_row           = array();
		protected $_s_default_value = null;
		protected $_s_description   = null;
		protected $_s_required      = null;
		protected $_a_roles         = array();
		protected $_s_placeholder   = null;
		protected $_s_show_document = null;
		protected $_s_hidden        = null;
		protected $_s_custom_class  = null;

		/**
		 * Constructor
		 *
		 * @param string $a_single_field field information array
		 * @return void
		 */
		public function __construct( $a_single_field ) {
			parent::__construct();

			// default fields
			$this->_s_field_type  = $a_single_field['field_type'];
			$this->_s_field_label = $a_single_field['field_label'];

			// optional fields
			if ( isset( $a_single_field['required'] ) ) {
				$this->_s_required = $a_single_field['required'];
			}
			if ( isset( $a_single_field['description'] ) ) {
				$this->_s_description = $a_single_field['description'];
			}
			if ( isset( $a_single_field['placeholder'] ) ) {
				$this->_s_placeholder = $a_single_field['placeholder'];
			}
			if ( isset( $a_single_field['default_value'] ) ) {
				$this->_s_default_value = $a_single_field['default_value'];
			}
			if ( isset( $a_single_field['roles'] ) && is_array( $a_single_field['roles'] ) ) {
				$this->_a_roles = $a_single_field['roles'];
			}
			if ( isset( $a_single_field['show_document'] ) ) {
				$this->_s_show_document = $a_single_field['show_document'];
			}
			if ( isset( $a_single_field['hidden'] ) ) {
				$this->_s_hidden = $a_single_field['hidden'];
			}
			if ( isset( $a_single_field['row'] ) && is_array( $a_single_field['row'] ) ) {
				$this->_a_row = $a_single_field['row'];
			}
			if ( isset( $a_single_field['custom_class'] ) ) {
				$this->_s_custom_class = $a_single_field['custom_class'];
			}
		}

		/**
		 * Returns a form based on its type
		 *
		 * @return string Returns a widget html for unchosen fields.
		 */
		public function get_widget_html() {
			$s_html  = null;
			$s_html .= '<li class="extends ' . $this->_s_field_type . '">
									<div class="x2board-extends-fields">
										<div class="x2board-fields-title toggle x2board-field-handle">
											<button type="button">' .
											esc_html( $this->_s_field_label ) .
											'<span class="fields-up">▲</span>
											<span class="fields-down">▼</span>
											</button>
										</div>
										<div class="x2board-fields-toggle">
											<button type="button" class="fields-remove" title="' . __( 'cmd_remove', X2B_DOMAIN ) . '">X</button>
										</div>
									</div>
									<div class="x2board-fields-content">
										<input type="hidden" class="field_data field_type" value="' . esc_attr( $this->_s_field_type ) . '">
										<input type="hidden" class="field_data field_label" value="' . esc_attr( $this->_s_field_label ) . '">';
			if ( $this->_is_multiline_fields() ) {
				$s_html .= '<div class="attr-row">
											<label class="attr-name">' . __( 'lbl_field_label', X2B_DOMAIN ) . '</label>
											<div class="attr-value"><input type="text" class="field_data field_name" placeholder="' . esc_attr( $this->_s_field_label ) . '"></div>
										</div>';
				$s_html .= '<div class="attr-row">
											<label class="attr-name">' . __( 'lbl_meta_key', X2B_DOMAIN ) . '</label>
											<div class="attr-value"><input type="text" class="field_data meta_key" placeholder="meta_key"></div>
											<div class="description">※ ' . __( 'about_meta_key', X2B_DOMAIN ) . '</div>
										</div>';
				$s_html .= '<div class="attr-row">
							<label class="attr-name">' . $this->_s_field_label . '</label>
							<div class="attr-value">';
				if ( $this->_s_field_type == 'html' ) {
					$s_html .= '<textarea class="field_data html" rows="5"></textarea>';
				} elseif ( $this->_s_field_type == 'shortcode' ) {
					$s_html .= '<textarea class="field_data shortcode" rows="5"></textarea>';
				}
				$s_html .= '</div>
						</div>';
				if ( isset( $this->_s_show_document ) ) {
					$s_html .= '<input type="hidden" class="field_data show_document" value="">
							<label><input type="checkbox" class="field_data show_document" value="1">' . __( 'lbl_display_on_content', X2B_DOMAIN ) . '</label>';
				}
			} else {
				$s_html .= '<div class="attr-row">
							<label class="attr-name">' . __( 'lbl_field_label', X2B_DOMAIN ) . '</label>
							<div class="attr-value"><input type="text" class="field_data field_name" placeholder="' . esc_attr( $this->_s_field_label ) . '"></div>
						</div>';
				$s_html .= '<div class="attr-row">
							<label class="attr-name">' . __( 'lbl_meta_key', X2B_DOMAIN ) . '</label>
							<div class="attr-value"><input type="text" class="field_data meta_key" placeholder="meta_key"></div>
							<div class="description">※ ' . __( 'about_meta_key', X2B_DOMAIN ) . '</div>
						</div>';
				if ( ! empty( $this->_a_row ) ) {
					$uniq_id = 'php_' . uniqid();
					$s_html .= '<div class="x2board-radio-reset">
								<div class="attr-row option-wrap">
									<div class="attr-name option">
										<label for="' . $uniq_id . '">' . __( 'lbl_label', X2B_DOMAIN ) . '</label>
									</div>
									<div class="attr-value">
										<input type="text" id="' . $uniq_id . '" class="field_data option_label">
										<button type="button" class="' . $this->_s_field_type . '" onclick="add_option(this)">+</button>
										<button type="button" class="' . $this->_s_field_type . '" onclick="remove_option(this)">-</button>
										<label>';
					if ( $this->_s_field_type == 'checkbox' ) {
						$s_html .= '<input type="checkbox" name="' . $this->_s_field_type . '" class="field_data default_value" value="1">';
					} else {
						$s_html .= '<input type="radio" name="' . $this->_s_field_type . '" class="field_data default_value" value="1">';
					}
					$s_html .= __( 'lbl_default_value', X2B_DOMAIN );
					$s_html .= '</label>';
					if ( $this->_s_field_type == 'radio' || $this->_s_field_type == 'select' ) {
						$s_html .= '<span style="vertical-align:middle;cursor:pointer;" onclick="x2board_radio_reset(this)">· ' . __( 'cmd_reset', X2B_DOMAIN ) . '</span>';
					}
					$s_html .= '</div>
									</div>
								</div>';
				}
				if ( ! is_null( $this->_a_roles ) ) {
					$s_html .= '<div class="attr-row">
									<label class="attr-name">' . __( 'msg_whom_to_show', X2B_DOMAIN ) . '</label>
									<div class="attr-value">
										<select class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">
											<option value="all" selected>' . __( 'opt_role_all_users', X2B_DOMAIN ) . '</option>
											<option value="author">' . __( 'opt_role_loggedin_users', X2B_DOMAIN ) . '</option>
											<option value="roles">' . __( 'opt_role_customize', X2B_DOMAIN ) . '</option>
										</select>
										<div class="x2board-permission-read-roles-view x2board-hide">';
					foreach ( get_editable_roles() as $roles_key => $roles_value ) {
						$s_mandatory = $roles_key == 'administrator' ? 'onclick="return false" checked' : '';
						$s_html     .= '	<label><input type="checkbox" class="field_data roles_checkbox" value="' . $roles_key . '" ' . $s_mandatory . '  > ' . _x( $roles_value['name'], 'User role' ) . '</label>';
					}
					$s_html .= '</div>
								</div>
							</div>';
				}
				if ( ! is_null( $this->_s_default_value ) && ! empty( $this->_a_row ) ) {
					$s_html .= '<div class="attr-row">
								<label class="attr-name">' . __( 'lbl_default_value', X2B_DOMAIN ) . '</label>
								<div class="attr-value"><input type="text" class="field_data default_value"></div>
							</div>';
				}
				if ( ! is_null( $this->_s_placeholder ) ) {
					$s_html .= '<div class="attr-row">
								<label class="attr-name">Placeholder</label>
								<div class="attr-value"><input type="text" class="field_data placeholder"></div>
							</div>';
				}
				if ( ! is_null( $this->_s_description ) ) {
					$s_html .= '<div class="attr-row">
								<label class="attr-name">' . __( 'lbl_description', X2B_DOMAIN ) . '</label>
								<div class="attr-value">
									<input type="text" class="field_data field_description" value="' . $this->_s_description . '">
								</div>
							</div>';
				}
				if ( ! is_null( $this->_s_custom_class ) ) {
					$s_html .= '<div class="attr-row">
								<label class="attr-name">' . __( 'lbl_css_class', X2B_DOMAIN ) . '</label>
								<div class="attr-value"><input type="text" class="field_data custom_class"></div>
							</div>';
				}
				$s_html .= '<div class="attr-row">';
				if ( ! is_null( $this->_s_required ) ) {
					$s_html .= '<input type="hidden" class="field_data required" value="">
							<label><input type="checkbox" class="field_data required" value="1">' . __( 'lbl_required', X2B_DOMAIN ) . '</label>';
				}
				if ( isset( $this->_s_show_document ) ) {
					$s_html .= '<input type="hidden" class="field_data show_document" value="">
							<label><input type="checkbox" class="field_data show_document" value="1">' . __( 'lbl_display_on_content', X2B_DOMAIN ) . '</label>';
				}
				if ( isset( $this->_s_hidden ) ) {
					$s_hidden_filed_notifier = $this->_s_field_type == 'text' ? '(hidden)' : '';
					$s_html                 .= '<input type="hidden" class="field_data hidden" value="">
							<label><input type="checkbox" class="field_data hidden" value="1">' . __( 'lbl_hiding', X2B_DOMAIN ) . '' . $s_hidden_filed_notifier . '</label>';
				}
				$s_html .= '</div>';
			}
			$s_html .= '</div>
					</li>';
			return $s_html;
		}
	}
}
/* End of file AdminExtendedUserDefineFieldsItem.class.php */
