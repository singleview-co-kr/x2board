<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  editorView
 * @author XEHub (developers@xpressengine.com)
 * @brief view class of the editor module
 */
namespace X2board\Includes\Modules\Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Editor\\editorView' ) ) {

	class editorView extends editor {

		function __construct() {}

		/**
		 * @brief Initialization
		 */
		// function init() {}

		/**
		 * 기본값이나 저장된 값이 있는지 확인한다.
		 * isSavedOption($value, $label)
		 *
		 * @param array|string $value
		 * @param string       $label
		 * @return boolean
		 */
		public function is_saved_option( $value, $label ) {
			if ( is_array( $value ) && in_array( $label, $value ) ) {
				return true;
			} elseif ( $value == $label ) {
				return true;
			}
			return false;
		}

		/**
		 * post 에디터 HTML 출력
		 *
		 * @param
		 */
		public static function get_post_editor_html( $n_current_post_id, $s_placeholder = null ) {
			// these are for all editors
			$s_content_field_name  = 'content';
			$o_current_module_info = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$s_editor_type         = isset( $o_current_module_info->post_editor_skin ) ? $o_current_module_info->post_editor_skin : 'ckeditor';
			$n_editor_height       = isset( $o_current_module_info->post_editor_height ) ? $o_current_module_info->post_editor_height : 50;
			unset( $o_current_module_info );

			// these are for WordPress and textarea
			$o_post    = \X2board\Includes\Classes\Context::get( 'post' );
			$s_content = $o_post->content;
			unset( $o_post );

			ob_start();

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				echo '<input type="button" id="btn_insert_wp_post_type" value="' . __( 'lbl_enter_wp_post_type', X2B_DOMAIN ) . '">';
			}
			unset( $o_logged_info );

			if ( $s_editor_type == 'ckeditor' ) {
				$o_editor_conf                       = new \stdClass();
				$o_editor_conf->module_type          = 'post';
				$o_editor_conf->upload_target_id     = $n_current_post_id;
				$o_editor_conf->primary_key_name     = 'post_id';
				$o_editor_conf->s_content_field_name = $s_content_field_name;

				echo '<input type="hidden" name="use_editor" value="Y">';
				$o_editor_model = \X2board\Includes\get_model( 'editor' );
				echo $o_editor_model->get_board_editor( $o_editor_conf );
				unset( $o_editor_model );
			} elseif ( $s_editor_type == 'WordPress' ) {
				$o_grant = \X2board\Includes\Classes\Context::get( 'grant' );
				echo '<input type="hidden" name="use_editor" value="Y">';
				wp_editor(
					$s_content,
					$s_content_field_name,
					array(
						'media_buttons' => $o_grant->is_admin,
						'editor_height' => $n_editor_height,
					)
				);
				// wp_editor($content, $editor_uid, array('media_buttons'=>$o_grant->is_admin, 'textarea_name'=>$content_field_name, 'editor_height'=>$editor_height));  //  'editor_class'=>'comment-textarea'
				unset( $o_grant );
			} else {
				echo '<input type="hidden" name="use_editor" value="N">';
				printf(
					'<textarea id="%s" class="editor-textarea required" name="%s" rows="%s" placeholder="%s">%s</textarea>',  // cols="50" rows="'.$n_textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$content_field_name.'" placeholder="'.__('Add a comment', X2B_DOMAIN).'..." required>'.esc_textarea($content).'</textarea>';
					esc_attr( $s_content_field_name ),
					esc_attr( $s_content_field_name ),
					$n_editor_height,
					isset( $s_placeholder ) ? esc_attr( $s_placeholder ) : __( 'msg_type_what_you_think', X2B_DOMAIN ),
					esc_textarea( strip_tags( $s_content ) )
				);
			}
			$s_editor_html = ob_get_clean();
			unset( $o_editor_conf );
			wp_enqueue_script( X2B_DOMAIN . '-post-validation', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/js/post_validation.js', array( X2B_JQUERY_VALIDATION ), X2B_VERSION, true );
			return $s_editor_html;
		}

		/**
		 * comment 에디터 HTML 출력
		 */
		public static function get_comment_editor_html() {
			$s_content_field_name  = 'content';
			$o_current_module_info = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$s_editor_type         = isset( $o_current_module_info->comment_editor_skin ) ? $o_current_module_info->comment_editor_skin : 'ckeditor';
			$n_editor_height       = isset( $o_current_module_info->comment_editor_height ) ? $o_current_module_info->comment_editor_height : 15;
			unset( $o_current_module_info );

			$o_the_comment = \X2board\Includes\Classes\Context::get( 'o_the_comment' );
			if ( $o_the_comment ) {
				$s_content = $o_the_comment->content;
			}
			unset( $o_the_comment );

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				echo '<input type="button" id="btn_insert_wp_post_type" value="' . __( 'lbl_enter_wp_post_type', X2B_DOMAIN ) . '" >';
			}
			unset( $o_logged_info );

			if ( $s_editor_type == 'ckeditor' ) {
				$o_editor_conf              = new \stdClass();
				$o_editor_conf->module_type = 'comment';
				// this can't be fixed until upload file or write comment
				$o_editor_conf->upload_target_id     = \X2board\Includes\Classes\Context::get( 'comment_id' );
				$o_editor_conf->primary_key_name     = 'comment_id';
				$o_editor_conf->s_content_field_name = $s_content_field_name;

				$o_editor_model = \X2board\Includes\get_model( 'editor' );
				echo $o_editor_model->get_board_editor( $o_editor_conf );
				unset( $o_editor_model );
				unset( $o_editor_conf );
			} else {
				echo '<input type="hidden" name="use_editor" value="N">';
				printf(
					'<textarea id="%s" class="editor-textarea required" name="%s" rows="%s" placeholder="%s" required>%s</textarea>',
					esc_attr( $s_content_field_name ),
					esc_attr( $s_content_field_name ),
					$n_editor_height,
					isset( $s_placeholder ) ? $s_placeholder : __( 'msg_type_what_you_think', X2B_DOMAIN ),
					esc_textarea( strip_tags( $s_content ) )
				);
				// echo '<textarea class="comment-textarea" cols="50" rows="'.$n_textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$content_field_name.'" placeholder="'.__('Add a comment', X2B_DOMAIN).'..." required>'.esc_textarea($content).'</textarea>';
			}
			wp_enqueue_script( X2B_DOMAIN . '-comment-validation', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/js/comment_validation.js', array( X2B_JQUERY_VALIDATION ), X2B_VERSION, true );
		}

		/**
		 * @brief render file appending UX HTML
		 */
		public function get_attach_ux_html( $a_appended_file ) {
			$o_module_info              = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$s_accept_file_types        = str_replace( ' ', '', $o_module_info->file_allowed_filetypes );
			$s_accept_file_types        = str_replace( ',', '|', $s_accept_file_types );
			$n_file_max_attached_count  = intval( $o_module_info->file_max_attached_count );
			$n_file_allowed_filesize_mb = intval( $o_module_info->file_allowed_filesize_mb );
			unset( $o_module_info );
			wp_enqueue_style( 'x2board-jquery-fileupload', X2B_URL . 'common/jquery.fileupload/css/jquery.fileupload.css', array(), X2B_VERSION );
			// wp_enqueue_style("x2board-jquery-fileupload-ui", X2B_URL . 'common/jquery.fileupload/css/jquery.fileupload-ui.css', [], X2B_VERSION);
			wp_enqueue_script( 'x2board-jquery-ui-widget', X2B_URL . 'common/jquery.fileupload/js/vendor/jquery.ui.widget.js', array( 'jquery' ), X2B_VERSION, true );
			wp_enqueue_script( 'x2board-jquery-iframe-transport', X2B_URL . 'common/jquery.fileupload/js/jquery.iframe-transport.js', array( 'jquery' ), X2B_VERSION, true );
			wp_enqueue_script( 'x2board-fileupload', X2B_URL . 'common/jquery.fileupload/js/jquery.fileupload.js', array( 'jquery' ), X2B_VERSION, true );
			wp_enqueue_script( 'x2board-fileupload-process', X2B_URL . 'common/jquery.fileupload/js/jquery.fileupload-process.js', array( 'jquery' ), X2B_VERSION, true );
			wp_enqueue_script( 'x2board-fileupload-caller', X2B_URL . 'common/jquery.fileupload/file-upload.js', array( 'jquery' ), X2B_VERSION, true );
			$n_editor_sequence = \X2board\Includes\Classes\Context::get( 'editor_sequence' ); // for appending file to a comment
			$buff              = array();
			$buff[]            = '<input type="file" name="files" id="file_software" class="file-upload" data-editor_call_id="' . $n_editor_sequence . '" data-maxfilecount="' . $n_file_max_attached_count . '" data-accept_file_types="' . $s_accept_file_types . '" data-max_each_file_size_mb="' . $n_file_allowed_filesize_mb . '">';
			$buff[]            = '<ul class="file-list list-unstyled mb-0">';
			foreach ( $a_appended_file as $_ => $o_file_value ) {
				$buff[] = '<li class="file my-1 row">';
				$buff[] = '<div class="file-name col-md-3">';
				$buff[] = '<img src="' . $o_file_value->thumbnail_abs_url . '" class="attach_thumbnail">';
				$buff[] = $o_file_value->source_filename;
				$buff[] = '</div>';
				$buff[] = '<div class="del-button col-md-1">';
				if ( $o_file_value->file_type !== 'image' ) {
					$s_disabled = 'disabled="disabled"';
				} else {
					$s_disabled = null;
				}
				$buff[] = '<button type="button" class="btn btn-sm btn-danger file-embed" data-thumbnail_abs_url="' . $o_file_value->thumbnail_abs_url . '" ' . $s_disabled . '><i class="fa fa-plus"></i></button>';
				$buff[] = '<button type="button" class="btn btn-sm btn-danger file-delete" data-file_id="' . $o_file_value->file_id . '"><i class="far fa-trash-alt"></i></button>';
				$buff[] = '</div>';
				$buff[] = '<div class="progress col-md-7 my-auto px-0">';
				// $buff[] =        '<div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 100%;"></div>';
				$buff[] = '</div>';
				$buff[] = '</li>';
			}
			$buff[] = '</ul>';
			return join( PHP_EOL, $buff );
		}

		/**
		 * @brief convert editor component codes to be returned and specify content style.
		 * Originally called from DisplayHandler.class.php::printContent()
		 * triggerEditorComponentCompile(&$content)
		 */
		public function render_editor_css() {
			$o_board_info    = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$s_content_style = $o_board_info->content_style;
			unset( $o_board_info );
			if ( $s_content_style ) {
				$s_path = X2B_PATH . 'includes/' . X2B_MODULES_NAME . '/editor/styles/' . $s_content_style . '/';
				if ( is_dir( $s_path ) && file_exists( $s_path . 'style.ini' ) ) {
					global $G_X2B_CACHE;
					$ini = file( $s_path . 'style.ini' );
					for ( $i = 0, $c = count( $ini ); $i < $c; $i++ ) {
						$file = trim( $ini[ $i ] );
						if ( ! $file ) {
							continue;
						}
						if ( isset( $G_X2B_CACHE['__editor_css__'][ $file ] ) ) {
							return null;
						}
						if ( substr_compare( $file, '.css', -4 ) === 0 ) {
							if ( ! isset( $G_X2B_CACHE['__editor_css__'][ $file ] ) ) {
								$G_X2B_CACHE['__editor_css__'][ $file ] = true;
								wp_enqueue_style( 'x2board-editor-style', X2B_URL . '/includes/' . X2B_MODULES_NAME . '/editor/styles/' . $s_content_style . '/' . $file, array(), X2B_VERSION, 'all' );
								return;
							}
						}
					}
				}
			}
			return null;
		}
	}
}
