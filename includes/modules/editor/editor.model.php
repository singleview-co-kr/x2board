<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  editorModel
 * @author XEHub (developers@xpressengine.com)
 * @brief model class of the editor odule
 */
namespace X2board\Includes\Modules\Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Editor\\editorModel' ) ) {

	class editorModel extends editor {
		var $loaded_component_list = array();
		/**
		 * @brief Return the editor
		 *
		 * Editor internally generates editor_sequence from 1 to 30 for temporary use.
		 * That means there is a limitation that more than 30 editors cannot be displayed on a single page.
		 *
		 * However, editor_sequence can be value from get_next_sequence() in case of the modified or the auto-saved for file upload
		 */

		/**
		 * constructor
		 *
		 * @return void
		 */
		public function __construct() {
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['__editor_module_config__'] ) ) {
				$G_X2B_CACHE['__editor_module_config__'] = array();
			}
		}

		/**
		 * @brief Translate WP post type caller, pattern: sv_%d_sv
		 * refer to \common\tpl\insert_wp_post_type.php
		 */
		public function convert_wp_post_type_caller( &$s_content = 0 ) {
			$s_pattern = '/sv_{1}[0-9]+_{1}sv/m';  // pattern: sv_%d_sv
			$a_matches = array();
			$n_matches = preg_match_all( $s_pattern, $s_content, $a_matches );
			if ( $n_matches > 0 ) {
				$a_replace = array();
				foreach( $a_matches[0] as $n_idx => $s_code ) {
					$a_code = explode( '_', $s_code );
					$o_wp_post_info = get_post( intval( $a_code[1] ) );
					$s_wp_post_html = '<a href="' . $o_wp_post_info->guid . '"><div class="container_wp_post_type">';
					if( has_post_thumbnail( intval( $a_code[1] ) ) ) {
						$s_wp_post_html .= '<img src="' . get_the_post_thumbnail_url( intval( $a_code[1] ), array(100,100) ) . '" alt="Sample Image">';
					}
					$s_wp_post_html .= '<div class="text"><b>' . $o_wp_post_info->post_title . '</b></div></div></a>';
					$a_replace[$s_code] = $s_wp_post_html;
					unset( $a_code );
					unset( $o_wp_post_info );
				}
				foreach( $a_replace as $s_code => $s_html ) {
					$s_content = str_replace( $s_code, $s_html, $s_content );
				}
				unset( $a_replace );
			}
			unset( $a_matches );
		}

		/**
		 * @brief Return editor template which contains settings of each board
		 * Result of getBoardEditor() is as same as getEditor(). But getBoardEditor()uses additional settings of each board to generate an editor
		 *
		 * 2 types of editors supported; document and comment.
		 * 2 types of editors can be used on a single board. For instance each for original post and reply port.
		 * getModuleEditor($type = 'document', $module_srl, $upload_target_srl, $primary_key_name, $content_key_name)
		 */
		public function get_board_editor( $o_editor_config ) {
			$o_config              = new \stdClass();
			$o_config->module_type = $o_editor_config->module_type;
			$upload_target_id      = $o_editor_config->upload_target_id;
			$primary_key_name      = $o_editor_config->primary_key_name;
			$content_key_name      = $o_editor_config->s_content_field_name;

			$o_config->content_style       = null;
			$o_config->content_font        = null;
			$o_config->content_font_size   = null;
			$o_config->sel_editor_colorset = null;

			$o_current_module_info = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$o_config->enable_default_component_grant = $o_current_module_info->enable_default_component_grant != -1 ? $o_current_module_info->enable_default_component_grant : null; // 기본 컴포넌트 사용 권한  $o_editor_config->enable_default_component_grant;
			$o_config->enable_component_grant         = $o_current_module_info->enable_component_grant != -1 ? $o_current_module_info->enable_component_grant : null; // 확장 컴포넌트 사용 권한 $o_editor_config->enable_component_grant;

			// Configurations listed according to a type
			if ( $o_config->module_type == 'post' ) {
				$o_config->editor_skin       = $o_current_module_info->post_editor_skin;
				// $o_config->upload_file_grant = $o_current_module_info->upload_file_grant;
				$o_config->enable_html_grant = $o_current_module_info->enable_html_grant;
				$o_config->editor_height     = $o_current_module_info->post_editor_height;
				$o_config->enable_autosave   = $o_current_module_info->enable_autosave;
			} else {
				$o_config->editor_skin       = $o_current_module_info->comment_editor_skin;
				// $o_config->upload_file_grant = $o_current_module_info->comment_upload_file_grant;
				$o_config->enable_html_grant = $o_current_module_info->enable_comment_html_grant;
				$o_config->editor_height     = $o_current_module_info->comment_editor_height;
				$o_config->enable_autosave   = 'N';
			}
			unset( $o_current_module_info );
			$logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			// Check a group_list of the currently logged-in user for permission check
			if ( \X2board\Includes\Classes\Context::get( 'is_logged' ) ) {
				$group_list = $logged_info->caps;
			} else {
				$group_list = array();
			}
			// Pre-set option variables of editor
			$option                    = new \stdClass();
			$option->module_type       = $o_config->module_type;
			$option->skin              = $o_config->editor_skin;
			$option->content_style     = $o_config->content_style;
			$option->content_font      = $o_config->content_font;
			$option->content_font_size = $o_config->content_font_size;
			$option->colorset          = $o_config->sel_editor_colorset;
			// Permission check for file upload
			// $option->allow_fileupload = false;
			// if ( $logged_info->is_admin == 'Y' ) {
			// 	$option->allow_fileupload = true;
			// } elseif ( count( (array) $o_config->upload_file_grant ) ) {
			// 	foreach ( $group_list as $s_group_name => $_ ) {  // $group_list = Array(  [administrator] => 1 )
			// 		if ( isset( $o_config->upload_file_grant[ $s_group_name ] ) && $o_config->upload_file_grant[ $s_group_name ] ) {
			// 			$option->allow_fileupload = true;
			// 			break;
			// 		}
			// 	}
			// } else {
			// 	$option->allow_fileupload = true;
			// }
			// Permission check for using default components
			$option->enable_default_component = false;
			if ( $logged_info->is_admin == 'Y' ) {
				$option->enable_default_component = true;
			} elseif ( count( (array) $o_config->enable_default_component_grant ) ) {
				foreach ( $group_list as $s_group_name => $_ ) {  // $group_list = Array(  [administrator] => 1 )
					if ( isset( $o_config->enable_default_component_grant[ $s_group_name ] ) && $o_config->enable_default_component_grant[ $s_group_name ] ) {
						$option->enable_default_component = true;
						break;
					}
				}
			} else {
				$option->enable_default_component = true;
			}
			// Permisshion check for using extended components
			$option->enable_component = false;
			if ( $logged_info->is_admin == 'Y' ) {
				$option->enable_component = true;
			} elseif ( count( (array) $o_config->enable_component_grant ) ) {
				foreach ( $group_list as $s_group_name => $_ ) {  // $group_list = Array(  [administrator] => 1 )
					if ( isset( $o_config->enable_component_grant[ $s_group_name ] ) && $o_config->enable_component_grant[ $s_group_name ] ) {
						$option->enable_component = true;
						break;
					}
				}
			} else {
				$option->enable_component = true;
			}
			// HTML editing privileges
			$enable_html = false;
			if ( $logged_info->is_admin == 'Y' ) {
				$enable_html = true;
			} elseif ( count( (array) $o_config->enable_html_grant ) ) {
				foreach ( $group_list as $s_group_name => $_ ) {  // $group_list = Array(  [administrator] => 1 )
					if ( isset( $o_config->enable_html_grant[ $s_group_name ] ) && $o_config->enable_html_grant[ $s_group_name ] ) {
						$enable_html = true;
						break;
					}
				}
			} else {
				$enable_html = true;
			}
			unset( $logged_info );

			if ( $enable_html ) {
				$option->disable_html = false;
			} else {
				$option->disable_html = true;
			}
			// Set Height
			$option->height = $o_config->editor_height;
			// Set an option for Auto-save
			$option->enable_autosave = $o_config->enable_autosave == 'Y' ? true : false;
			// Other settings
			$option->primary_key_name = $primary_key_name;
			$option->content_key_name = $content_key_name;
			unset( $group_list );
			unset( $o_config );
			return $this->_get_editor( $upload_target_id, $option );
		}

		/**
		 * @brief Return the editor template
		 * You can call upload_target_id when modifying content
		 * The upload_target_id is used for a routine to check if an attachment exists
		 * getEditor($upload_target_srl = 0, $option = null)
		 */
		private function _get_editor( $upload_target_id = 0, $option = null ) {
			/**
			 * Editor's default options
			 */
			// if ( ! $option->allow_fileupload ) {
			// 	$allow_fileupload = false;
			// } else {
			// 	$allow_fileupload = true;
			// }

			// content_style setting
			if ( ! $option->content_style ) {
				$option->content_style = 'ckeditor_light';
			}
			\X2board\Includes\Classes\Context::set( 'content_style', $option->content_style );

			$content_style_path = $this->module_path . 'styles/' . $option->content_style;
			\X2board\Includes\Classes\Context::set( 'content_style_path', $content_style_path );

			$a_content_style_path = explode( 'wp-content', $content_style_path );
			$s_content_style_url  = str_replace( '\\', '/', $a_content_style_path[1] );
			unset( $a_content_style_path );
			\X2board\Includes\Classes\Context::set( 'content_style_url', '/wp-content' . $s_content_style_url );

			// Default font setting
			\X2board\Includes\Classes\Context::set( 'content_font', addslashes( (string)$option->content_font ) );
			\X2board\Includes\Classes\Context::set( 'content_font_size', $option->content_font_size );

			// Option setting to allow auto-save
			if ( ! $option->enable_autosave ) {
				$enable_autosave = false;
			} elseif ( \X2board\Includes\Classes\Context::get( $option->primary_key_name ) ) {
				$enable_autosave = false;
			} else {
				$enable_autosave = true;
			}
			// Option setting to allow the default editor component
			if ( ! $option->enable_default_component ) {
				$enable_default_component = false;
			} else {
				$enable_default_component = true;
			}
			// Option setting to allow other extended components
			if ( ! $option->enable_component ) {
				$enable_component = false;
			} else {
				$enable_component = true;
			}
			// Setting for html-mode
			if ( $option->disable_html ) {
				$html_mode = false;
			} else {
				$html_mode = true;
			}
			// Set Height
			if ( ! $option->height ) {
				$editor_height = 300;
			} else {
				$editor_height = $option->height;
			}
			if ( wp_is_mobile() ) {
				$editor_height = 150;
			}
			// Skin Setting
			$skin = $option->skin;
			if ( ! $skin ) {
				$skin = 'ckeditor';
			}

			$colorset = $option->colorset;
			if ( ! $colorset ) {
				$colorset = 'moono';
			}
			\X2board\Includes\Classes\Context::set( 'colorset', $colorset );
			\X2board\Includes\Classes\Context::set( 'skin', $skin );
			\X2board\Includes\Classes\Context::set( 'module_type', $option->module_type );

			// if($skin=='dreditor')
			// {
			// $this->loadDrComponents();
			// }

			/**
			 * Check the automatic backup feature (do not use if the post is edited)
			 */
			$enable_autosave = false;
			error_log( print_r( 'should activate includes\modules\editor\editor.model.php::_get_saved_post() ' . __FILE__ . ':' . __LINE__, true ) );
			if ( $enable_autosave ) {
				// Extract auto-saved data
				$saved_doc = $this->_get_saved_post( $upload_target_id );
				// Context setting auto-saved data
				\X2board\Includes\Classes\Context::set( 'saved_doc', $saved_doc );
			}
			\X2board\Includes\Classes\Context::set( 'enable_autosave', $enable_autosave );

			/**
			 * Extract editor's unique number (in order to display multiple editors on a single page)
			 */
			// if(isset($option->editor_sequence)) {
			// $editor_sequence = $option->editor_sequence;
			// }
			// else {
			if ( ! isset( $_SESSION['_x2b_editor_sequence_'] ) ) {
				$_SESSION['_x2b_editor_sequence_'] = 1;
			}
				$editor_sequence = $_SESSION['_x2b_editor_sequence_']++;
			// }

			/**
			 * Upload setting by using configuration of the file module internally
			 */
			$files_count = 0;
			// if ( $allow_fileupload ) {
				$oFileModel = \X2board\Includes\get_model( 'file' );
				// Get upload configuration to set on SWFUploader
				$file_config                      = $oFileModel->get_upload_config();
				$file_config->allowed_attach_size = $file_config->allowed_attach_size * 1048576; // 1024*1024;
				$file_config->allowed_filesize    = $file_config->allowed_filesize * 1048576; // 1024*1024;

				\X2board\Includes\Classes\Context::set( 'file_config', $file_config );
				// Configure upload status such as file size
				$upload_status = $oFileModel->get_upload_status();
				\X2board\Includes\Classes\Context::set( 'upload_status', $upload_status );
				// Upload enabled (internally caching)
				$oFileController = \X2board\Includes\get_controller( 'file' );
				$oFileController->set_upload_info( $editor_sequence, $upload_target_id );
				unset( $oFileController );
				// Check if the file already exists
				if ( $upload_target_id ) {
					$files_count = $oFileModel->get_files_count( $upload_target_id );
				}
				unset( $oFileModel );
			// }
			// \X2board\Includes\Classes\Context::set('files_count', (int)$files_count);

			// \X2board\Includes\Classes\Context::set('allow_fileupload', $allow_fileupload);
			// Set editor_sequence value
			\X2board\Includes\Classes\Context::set( 'editor_sequence', $editor_sequence );

			// Set the post number to upload_target_id for file attachments
			// If a new post, upload_target_id = 0. The value becomes changed when file attachment is requested
			// \X2board\Includes\Classes\Context::set('upload_target_srl', $upload_target_id);
			// Set the primary key valueof the post or comments
			\X2board\Includes\Classes\Context::set( 'editor_primary_key_name', $option->primary_key_name );
			// Set content column name to sync contents
			\X2board\Includes\Classes\Context::set( 'editor_content_key_name', $option->content_key_name );

			/**
			 * Check editor component
			 */
			// $site_module_info = \X2board\Includes\Classes\Context::get('site_module_info');
			$site_srl = 0; // (int)$site_module_info->site_srl;
			if ( $enable_component ) {
				if ( ! \X2board\Includes\Classes\Context::get( 'component_list' ) ) {
					$component_list = $this->_get_component_list( true, $site_srl );
					\X2board\Includes\Classes\Context::set( 'component_list', $component_list );
				}
			}
			\X2board\Includes\Classes\Context::set( 'enable_component', $enable_component );
			\X2board\Includes\Classes\Context::set( 'enable_default_component', $enable_default_component );

			/**
			 * Variable setting if html_mode is available
			 */
			\X2board\Includes\Classes\Context::set( 'html_mode', $html_mode );

			/**
			 * Set a height of editor
			 */
			\X2board\Includes\Classes\Context::set( 'editor_height', $editor_height );

			/**
			 * Set a skin path to pre-compile the template
			 */
			$tpl_path = sprintf( '%sskins/%s/', $this->module_path, $skin );
			$tpl_file = 'editor.php';

			if ( ! file_exists( $tpl_path . $tpl_file ) ) {
				$skin     = 'ckeditor';
				$tpl_path = sprintf( '%sskins/%s/', $this->module_path, $skin );
			}
			$this->set_skin_path( $tpl_path );
			\X2board\Includes\Classes\Context::set( 'editor_path', $tpl_path );
			ob_start();
			echo $this->render_skin_file( 'editor' );
			$s_editor_html = ob_get_clean();
			return $s_editor_html;
		}

		/**
		 * @brief Return a component list (DB Information included)
		 * getComponentList($filter_enabled = true, $site_srl=0, $from_db=false)
		 */
		private function _get_component_list( $filter_enabled = true, $site_srl = 0, $from_db = false ) {
			$o_emoticon                 = new \stdClass();
			$o_emoticon->author         = array(
				'name'          => 'XEHub',
				'email_address' => 'developers@xpressengine.com',
				'homepage'      => 'https://www.xehub.io',
			);
			$o_emoticon->extra_vars     = new \stdClass();
			$o_emoticon->component_name = 'emoticon';
			$o_emoticon->title          = '이모티콘 출력';
			$o_emoticon->description    = '이모티콘을 에디터에 삽입할 수 있습니다.';
			$o_emoticon->version        = '1.7';
			$o_emoticon->date           = '2013-11-27';
			$o_emoticon->homepage       = null;
			$o_emoticon->license        = null;
			$o_emoticon->license_link   = null;
			$o_emoticon->enabled        = 'Y';
			$o_emoticon->icon           = true;
			$o_emoticon->component_icon = true;

			$o_image_link                 = new \stdClass();
			$o_image_link->author         = array(
				'name'          => 'XEHub',
				'email_address' => 'developers@xpressengine.com',
				'homepage'      => 'https://www.xehub.io',
			);
			$o_image_link->extra_vars     = new \stdClass();
			$o_image_link->component_name = 'image_link';
			$o_image_link->title          = '이미지 추가';
			$o_image_link->description    = '에디터에 이미지를 추가하거나 속성을 변경할 수 있습니다.';
			$o_image_link->version        = '1.7';
			$o_image_link->date           = '2013-11-27';
			$o_image_link->homepage       = null;
			$o_image_link->license        = null;
			$o_image_link->license_link   = null;
			$o_image_link->enabled        = 'Y';
			$o_image_link->icon           = true;
			$o_image_link->component_icon = true;

			$o_poll_maker                 = new \stdClass();
			$o_poll_maker->author         = array(
				'name'          => 'XEHub',
				'email_address' => 'developers@xpressengine.com',
				'homepage'      => 'https://www.xehub.io',
			);
			$o_poll_maker->extra_vars     = new \stdClass();
			$o_poll_maker->component_name = 'poll_maker';
			$o_poll_maker->title          = '설문조사';
			$o_poll_maker->description    = '글 작성시에 설문조사를 첨부할 수 있습니다. 설문조사 컴포넌트는 설문조사 모듈의 설정에 영향을 받습니다.';
			$o_poll_maker->version        = '1.7';
			$o_poll_maker->date           = '2013-11-27';
			$o_poll_maker->homepage       = null;
			$o_poll_maker->license        = null;
			$o_poll_maker->license_link   = null;
			$o_poll_maker->enabled        = 'Y';
			$o_poll_maker->icon           = true;
			$o_poll_maker->component_icon = true;

			$o_image_gallery                 = new \stdClass();
			$o_image_gallery->author         = array(
				'name'          => 'XEHub',
				'email_address' => 'developers@xpressengine.com',
				'homepage'      => 'https://www.xehub.io',
			);
			$o_image_gallery->extra_vars     = new \stdClass();
			$o_image_gallery->component_name = 'image_gallery';
			$o_image_gallery->title          = '이미지 갤러리';
			$o_image_gallery->description    = '첨부된 이미지파일을 이용하여 슬라이드/목록형 이미지 갤러리를 만들 수 있습니다.';
			$o_image_gallery->version        = '1.7';
			$o_image_gallery->date           = '2013-11-27';
			$o_image_gallery->homepage       = null;
			$o_image_gallery->license        = null;
			$o_image_gallery->license_link   = null;
			$o_image_gallery->enabled        = 'Y';
			$o_image_gallery->icon           = true;
			$o_image_gallery->component_icon = true;

			$o_component_list = new \stdClass();

			$o_component_list->emoticon      = $o_emoticon;
			$o_component_list->image_link    = $o_emoticon;
			$o_component_list->poll_maker    = $o_image_link;
			$o_component_list->image_gallery = $o_image_gallery;
			return $o_component_list;
		}

		/**
		 * @brief create objects of the component
		 */
		/*
		function getComponentObject($component, $editor_sequence = 0, $site_srl = 0)
		{
			if(!preg_match('/^[a-zA-Z0-9_-]+$/',$component) || !preg_match('/^[0-9]+$/', $editor_sequence . $site_srl)) return;

			if(!$this->loaded_component_list[$component][$editor_sequence])
			{
				// Create an object of the component and execute
				$class_path = sprintf('%scomponents/%s/', $this->module_path, $component);
				$class_file = sprintf('%s%s.class.php', $class_path, $component);
				if(!file_exists($class_file)) return new BaseObject(-1, sprintf(Context::getLang('msg_component_is_not_founded'), $component));
				// Create an object after loading the class file
				require_once($class_file);
				$oComponent = new $component($editor_sequence, $class_path);
				if(!$oComponent) return new BaseObject(-1, sprintf(Context::getLang('msg_component_is_not_founded'), $component));
				// Add configuration information
				$component_info = $this->getComponent($component, $site_srl);
				$oComponent->setInfo($component_info);
				$this->loaded_component_list[$component][$editor_sequence] = $oComponent;
			}

			return $this->loaded_component_list[$component][$editor_sequence];
		}*/

		/**
		 * @brief Get xml and db information of the component
		 */
		/*
		function getComponent($component_name, $site_srl = 0)
		{
			$args = new stdClass();
			$args->component_name = $component_name;

			if($site_srl)
			{
				$args->site_srl = $site_srl;
				$output = executeQuery('editor.getSiteComponent', $args);
			}
			else
			{
				$output = executeQuery('editor.getComponent', $args);
			}
			$component = $output->data;

			if(!$output->data) return false;

			$component_name = $component->component_name;

			unset($xml_info);
			$xml_info = $this->getComponentXmlInfo($component_name);
			$xml_info->enabled = $component->enabled;

			$xml_info->target_group = array();

			$xml_info->mid_list = array();

			if($component->extra_vars)
			{
				$extra_vars = unserialize($component->extra_vars);

				if($extra_vars->target_group)
				{
					$xml_info->target_group = $extra_vars->target_group;
					unset($extra_vars->target_group);
				}

				if($extra_vars->mid_list)
				{
					$xml_info->mid_list = $extra_vars->mid_list;
					unset($extra_vars->mid_list);
				}

				if($xml_info->extra_vars)
				{
					foreach($xml_info->extra_vars as $key => $val)
					{
						$xml_info->extra_vars->{$key}->value = $extra_vars->{$key};
					}
				}
			}

			return $xml_info;
		}*/
	}
}
/* End of file editor.model.php */
