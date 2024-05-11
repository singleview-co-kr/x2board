<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  editorView
 * @author XEHub (developers@xpressengine.com)
 * @brief view class of the editor module
 */
namespace X2board\Includes\Modules\Editor;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Editor\\editorView')) {

	class editorView extends editor	{

		function __construct() {
// var_dump('editor view class __construct');
		}
		
		/**
		 * @brief Initialization
		 */
		function init() {}

		/**
		 * 기본값이나 저장된 값이 있는지 확인한다.
		 * @param array|string $value
		 * @param string $label
		 * @return boolean
		 */
		// public function isSavedOption($value, $label){
		public function is_saved_option($value, $label) {
			if(is_array($value) && in_array($label, $value)){
				return true;
			}
			else if($value == $label){
				return true;
			}
			return false;
		}

		/**
		 * post 에디터 HTML 출력
		 * @param 
		 */
		public static function get_post_editor_html($n_current_post_id, $s_placeholder =null) {   // $s_content_type, $s_required =null, 
			$o_editor_conf = new \stdClass();
			// if( is_null( $n_current_post_id ) ) {
			// 	wp_die( __('invalid current post id', 'x2board') );
			// }

			$o_current_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			
			$o_editor_conf->s_content_field_name = 'content'; // $o_editor_conf_in_arg->s_content_field_name;
			$o_editor_conf->s_editor_type = isset( $o_current_module_info->post_editor_skin ) ? $o_current_module_info->post_editor_skin : 'ckeditor';
			$o_editor_conf->n_editor_height = isset( $o_current_module_info->post_editor_height ) ? $o_current_module_info->post_editor_height : 500;
			// $o_editor_conf->s_required = isset( $s_required ) ? $s_required : '';
			$o_editor_conf->s_placeholder = isset( $s_placeholder ) ? $s_placeholder : __('Type what you think', 'x2board');
			// $o_editor_conf->s_editor_uid = isset( $o_editor_conf_in_arg->s_editor_uid ) ? $o_editor_conf_in_arg->s_editor_uid : '';
			$o_editor_conf->n_textarea_rows = isset( $o_current_module_info->textarea_rows ) ? $o_current_module_info->textarea_rows : 50;

			$o_post = \X2board\Includes\Classes\Context::get('post');
			$o_editor_conf->s_content = $o_post->content;
			unset($o_post);

			ob_start();
			if($o_editor_conf->s_editor_type == 'ckeditor') {
				$o_editor_conf->n_board_id = \X2board\Includes\Classes\Context::get('board_id');				
				$o_editor_conf->module_type = 'post'; //$s_content_type;				
				$o_editor_conf->upload_target_id = $n_current_post_id;
				$o_editor_conf->primary_key_name = 'post_id';
				$o_editor_conf->post_editor_skin = $o_editor_conf->s_editor_type;
				$o_editor_conf->post_editor_height = $o_current_module_info->post_editor_height;
				$o_editor_conf->upload_file_grant = $o_current_module_info->upload_file_grant;
				$o_editor_conf->enable_html_grant = $o_current_module_info->enable_html_grant;

				$o_editor_conf->comment_editor_skin = $o_editor_conf->s_editor_type;
				$o_editor_conf->comment_editor_height = $o_current_module_info->comment_editor_height;
				$o_editor_conf->comment_upload_file_grant = $o_current_module_info->comment_upload_file_grant;
				$o_editor_conf->enable_comment_html_grant = $o_current_module_info->enable_comment_html_grant;
				
				$o_editor_conf->enable_autosave = $o_current_module_info->enable_autosave;
				$o_editor_conf->enable_default_component_grant = $o_current_module_info->enable_default_component_grant != -1 ? $o_current_module_info->enable_default_component_grant : null;
				$o_editor_conf->enable_component_grant = $o_current_module_info->enable_component_grant != -1 ? $o_current_module_info->enable_component_grant : null;

				$o_editor_model = \X2board\Includes\getModel('editor');
				echo $o_editor_model->get_board_editor($o_editor_conf); // $s_content_type, $n_current_post_id, 'comment_id', $o_editor_conf->s_content_field_name);
				unset($o_editor_model);
			}
			elseif($o_editor_conf->s_editor_type == 'wordpress'){
				$o_grant = \X2board\Includes\Classes\Context::get('grant');
				wp_editor($o_editor_conf->s_content, $o_editor_conf->s_content_field_name, array('media_buttons'=>$o_grant->is_admin, 'editor_height'=>$o_editor_conf->n_editor_height));
				// wp_editor($content, $editor_uid, array('media_buttons'=>$o_grant->is_admin, 'textarea_name'=>$content_field_name, 'editor_height'=>$editor_height));  //  'editor_class'=>'comment-textarea'
				unset($o_grant);
			}
			else{
				echo sprintf('<textarea id="%s" class="editor-textarea required" name="%s" placeholder="%s">%s</textarea>', 
							 esc_attr($o_editor_conf->s_content_field_name), 
							//  esc_attr($o_editor_conf->s_required), 
							 esc_attr($o_editor_conf->s_content_field_name), 
							 esc_attr($o_editor_conf->s_placeholder), 
							 esc_textarea($o_editor_conf->s_content));
				// echo '<textarea class="comment-textarea" cols="50" rows="'.$textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$content_field_name.'" placeholder="'.__('Add a comment', 'x2board').'..." required>'.esc_textarea($content).'</textarea>';
			}
			$s_editor_html = ob_get_clean();
			unset($o_editor_conf);
			return $s_editor_html;
		}

		/**
		 * comment 에디터 HTML 출력
		 */
		public static function get_comment_editor_html() {
			$n_current_post_id = 123123;
			$o_editor_conf = new \stdClass();
			// if( is_null( $n_current_post_id ) ) {
			// 	wp_die( __('invalid current post id', 'x2board') );
			// }

			$o_current_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			
			$o_editor_conf->s_content_field_name = 'content'; // $o_editor_conf_in_arg->s_content_field_name;
			
			$o_editor_conf->s_editor_type = isset( $o_current_module_info->comment_editor_skin ) ? $o_current_module_info->comment_editor_skin : 'ckeditor';
			$o_editor_conf->n_editor_height = isset( $o_current_module_info->comment_editor_height ) ? $o_current_module_info->comment_editor_height : 300;
		
			$o_editor_conf->s_required = isset( $s_required ) ? $s_required : '';
			$o_editor_conf->s_placeholder = isset( $s_placeholder ) ? $s_placeholder : __('Type what you think', 'x2board');
			// $o_editor_conf->s_editor_uid = isset( $o_editor_conf_in_arg->s_editor_uid ) ? $o_editor_conf_in_arg->s_editor_uid : '';
			$o_editor_conf->n_textarea_rows = isset( $o_current_module_info->textarea_rows ) ? $o_current_module_info->textarea_rows : 50;

			$o_the_comment = \X2board\Includes\Classes\Context::get('o_the_comment');
			if( $o_the_comment ){
				$o_editor_conf->s_content = $o_the_comment->content;
			}
			unset($o_the_comment);

			// ob_start();
			if($o_editor_conf->s_editor_type == 'ckeditor') {
				$o_editor_conf->n_board_id = \X2board\Includes\Classes\Context::get('board_id');				
				$o_editor_conf->module_type = 'comment'; //$s_content_type;				
				$o_editor_conf->upload_target_id = $n_current_post_id;
				$o_editor_conf->primary_key_name = 'comment_id';

				$o_editor_conf->comment_editor_skin = $o_editor_conf->s_editor_type;
				$o_editor_conf->comment_editor_height = $o_current_module_info->comment_editor_height;
				$o_editor_conf->comment_upload_file_grant = $o_current_module_info->comment_upload_file_grant;
				$o_editor_conf->enable_comment_html_grant = $o_current_module_info->enable_comment_html_grant;
				
				$o_editor_conf->enable_autosave = $o_current_module_info->enable_autosave;
				$o_editor_conf->enable_html_grant = $o_current_module_info->enable_html_grant;
				$o_editor_conf->upload_file_grant = $o_current_module_info->upload_file_grant;

				$o_editor_conf->enable_default_component_grant = $o_current_module_info->enable_default_component_grant;
				$o_editor_conf->enable_component_grant = $o_current_module_info->enable_component_grant;

				$o_editor_model = \X2board\Includes\getModel('editor');
				echo $o_editor_model->get_board_editor($o_editor_conf);
				unset($o_editor_model);
			}
			else{
				echo sprintf('<textarea id="%s" class="editor-textarea %s" name="%s" placeholder="%s">%s</textarea>', 
							 esc_attr($o_editor_conf->s_content_field_name), 
							 esc_attr($o_editor_conf->s_required), 
							 esc_attr($o_editor_conf->s_content_field_name), 
							 esc_attr($o_editor_conf->s_placeholder), 
							 esc_textarea($o_editor_conf->s_content));
				// echo '<textarea class="comment-textarea" cols="50" rows="'.$textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$content_field_name.'" placeholder="'.__('Add a comment', 'x2board').'..." required>'.esc_textarea($content).'</textarea>';
			}
			// $s_editor_html = ob_get_clean();
			unset($o_editor_conf);
			// return $s_editor_html;
		}

		/**
		 * @brief convert editor component codes to be returned and specify content style.
		 * Originally called from DisplayHandler.class.php::printContent()
		 */
		// function triggerEditorComponentCompile(&$content) {
		public function render_editor_css() {
			$o_board_info = \X2board\Includes\Classes\Context::get('current_module_info');
			$s_content_style = $o_board_info->content_style;
			unset($o_board_info);
			if($s_content_style) {
				$s_path = X2B_PATH . 'includes/modules/editor/styles/'.$s_content_style.'/';
				if(is_dir($s_path) && file_exists($s_path . 'style.ini')) {
					global $G_X2B_CACHE;
					$ini = file($s_path.'style.ini');
					for($i = 0, $c = count($ini); $i < $c; $i++) {
						$file = trim($ini[$i]);
						if(!$file) {
							continue;
						}
						if(isset($G_X2B_CACHE['__editor_css__'][$file])) {
							return null;
						}
						if(substr_compare($file, '.css', -4) === 0)	{
							if(!isset($G_X2B_CACHE['__editor_css__'][$file])) {
								$G_X2B_CACHE['__editor_css__'][$file] = true;
								wp_enqueue_style('x2board-editor-style', X2B_URL.'/includes/modules/editor/styles/'.$s_content_style.'/'.$file, array(), X2B_VERSION, 'all');
								return;
							}
						}
					}
				}
			}
			return null;
		}

		/**
		 * @brief Action to get a request to display compoenet pop-up
		 */
		// function dispEditorPopup()
		// {
		// 	// add a css file
		// 	Context::loadFile($this->module_path."tpl/css/editor.css", true);
		// 	// List variables
		// 	$editor_sequence = Context::get('editor_sequence');
		// 	$component = Context::get('component');

		// 	$site_module_info = Context::get('site_module_info');
		// 	$site_srl = (int)$site_module_info->site_srl;
		// 	// Get compoenet object
		// 	$oEditorModel = getModel('editor');
		// 	$oComponent = &$oEditorModel->getComponentObject($component, $editor_sequence, $site_srl);
		// 	if(!$oComponent->toBool())
		// 	{
		// 		Context::set('message', sprintf(Context::getLang('msg_component_is_not_founded'), $component));
		// 		$this->setTemplatePath($this->module_path.'tpl');
		// 		$this->setTemplateFile('component_not_founded');
		// 	}
		// 	else
		// 	{
		// 		// Get the result after executing a method to display popup url of the component
		// 		$popup_content = $oComponent->getPopupContent();
		// 		Context::set('popup_content', $popup_content);
		// 		// Set layout to popup_layout
		// 		$this->setLayoutFile('popup_layout');
		// 		// Set a template
		// 		$this->setTemplatePath($this->module_path.'tpl');
		// 		$this->setTemplateFile('popup');
		// 	}
		// }

		/**
		 * @brief Get component information
		 */
		// function dispEditorComponentInfo()
		// {
		// 	$component_name = Context::get('component_name');

		// 	$site_module_info = Context::get('site_module_info');
		// 	$site_srl = (int)$site_module_info->site_srl;

		// 	$oEditorModel = getModel('editor');
		// 	$component = $oEditorModel->getComponent($component_name, $site_srl);

		// 	if(!$component->component_name) {
		// 		$this->stop('msg_invalid_request');
		// 		return;
		// 	}

		// 	Context::set('component', $component);

		// 	$this->setTemplatePath($this->module_path.'tpl');
		// 	$this->setTemplateFile('view_component');
		// 	$this->setLayoutFile("popup_layout");
		// }

		/**
		 * @brief Add a form for editor addition setup
		 */
		// function triggerDispEditorAdditionSetup(&$obj)
		// {
		// 	$current_module_srl = Context::get('module_srl');
		// 	$current_module_srls = Context::get('module_srls');

		// 	if(!$current_module_srl && !$current_module_srls)
		// 	{
		// 		// Get information of the current module
		// 		$current_module_info = Context::get('current_module_info');
		// 		$current_module_srl = $current_module_info->module_srl;
		// 		if(!$current_module_srl) return new BaseObject();
		// 	}
		// 	// Get editors settings
		// 	$oEditorModel = getModel('editor');
		// 	$editor_config = $oEditorModel->getEditorConfig($current_module_srl);

		// 	Context::set('editor_config', $editor_config);

		// 	$oModuleModel = getModel('module');
		// 	// Get a list of editor skin
		// 	$editor_skin_list = FileHandler::readDir(_XE_PATH_.'modules/editor/skins');
		// 	Context::set('editor_skin_list', $editor_skin_list);

		// 	$skin_info = $oModuleModel->loadSkinInfo($this->module_path,$editor_config->editor_skin);
		// 	Context::set('editor_colorset_list', $skin_info->colorset);
		// 	$skin_info = $oModuleModel->loadSkinInfo($this->module_path,$editor_config->comment_editor_skin);
		// 	Context::set('editor_comment_colorset_list', $skin_info->colorset);

		// 	$contents = FileHandler::readDir(_XE_PATH_.'modules/editor/styles');
		// 	$content_style_list = array();
		// 	for($i=0,$c=count($contents);$i<$c;$i++)
		// 	{
		// 		$style = $contents[$i];
		// 		$info = $oModuleModel->loadSkinInfo($this->module_path,$style,'styles');
		// 		$content_style_list[$style] = new stdClass();
		// 		$content_style_list[$style]->title = $info->title;
		// 	}			
		// 	Context::set('content_style_list', $content_style_list);
		// 	// Get a group list
		// 	$oMemberModel = getModel('member');
		// 	$site_module_info = Context::get('site_module_info');
		// 	$group_list = $oMemberModel->getGroups($site_module_info->site_srl);
		// 	Context::set('group_list', $group_list);

		// 	//Security
		// 	$security = new Security();
		// 	$security->encodeHTML('group_list..title');
		// 	$security->encodeHTML('group_list..description');
		// 	$security->encodeHTML('content_style_list..');
		// 	$security->encodeHTML('editor_comment_colorset_list..title');			

		// 	// Set a template file
		// 	$oTemplate = &TemplateHandler::getInstance();
		// 	$tpl = $oTemplate->compile($this->module_path.'tpl', 'editor_module_config');
		// 	$obj .= $tpl;

		// 	return new BaseObject();
		// }

		// function dispEditorPreview()
		// {
		// 	$this->setTemplatePath($this->module_path.'tpl');
		// 	$this->setTemplateFile('preview');
		// }

		// function dispEditorSkinColorset()
		// {
		// 	$skin = Context::get('skin');
		// 	$oModuleModel = getModel('module');
		// 	$skin_info = $oModuleModel->loadSkinInfo($this->module_path,$skin);
		// 	$colorset = $skin_info->colorset;
		// 	Context::set('colorset', $colorset);
		// }

		// function dispEditorConfigPreview()
		// {
		// 	$oEditorModel = getModel('editor');
		// 	$config = $oEditorModel->getEditorConfig();

		// 	$mode = Context::get('mode');

		// 	if($mode != 'main')
		// 	{
		// 		$option_com = new stdClass();
		// 		$option_com->allow_fileupload = false;
		// 		$option_com->content_style = $config->content_style;
		// 		$option_com->content_font = $config->content_font;
		// 		$option_com->content_font_size = $config->content_font_size;
		// 		$option_com->enable_autosave = false;
		// 		$option_com->enable_default_component = true;
		// 		$option_com->enable_component = true;
		// 		$option_com->disable_html = false;
		// 		$option_com->height = $config->comment_editor_height;
		// 		$option_com->skin = $config->comment_editor_skin;
		// 		$option_com->content_key_name = 'dummy_content';
		// 		$option_com->primary_key_name = 'dummy_key';
		// 		$option_com->content_style = $config->comment_content_style;
		// 		$option_com->colorset = $config->sel_comment_editor_colorset;
		// 		$editor = $oEditorModel->getEditor(0, $option_com);
		// 	}
		// 	else
		// 	{
		// 		$option = new stdClass();
		// 		$option->allow_fileupload = false;
		// 		$option->content_style = $config->content_style;
		// 		$option->content_font = $config->content_font;
		// 		$option->content_font_size = $config->content_font_size;
		// 		$option->enable_autosave = false;
		// 		$option->enable_default_component = true;
		// 		$option->enable_component = true;
		// 		$option->disable_html = false;
		// 		$option->height = $config->editor_height;
		// 		$option->skin = $config->editor_skin;
		// 		$option->content_key_name = 'dummy_content';
		// 		$option->primary_key_name = 'dummy_key';
		// 		$option->colorset = $config->sel_editor_colorset;
		// 		$editor = $oEditorModel->getEditor(0, $option);
		// 	}

		// 	Context::set('editor', $editor);

		// 	$this->setLayoutFile('popup_layout');
		// 	$this->setTemplatePath($this->module_path.'tpl');
		// 	$this->setTemplateFile('config_preview');
		// }
		
		/**
		 * Add a form fot comment setting on the additional setting of module
		 * @param string $obj
		 * @return string
		 */
		/*private function _ob_get_comment_editor($vars=array()) {
			$vars = array_merge(array(
				'content_field_name' => '',
				// 'board' => '',
				'editor_uid' => '',
				'content' => '',
				'editor_height' => '',
				'textarea_rows' => ''
			), $vars);
			// $vars = apply_filters('kboard_comments_content_editor_vars', $vars);
			extract($vars, EXTR_SKIP);
			// var_dump(\X2board\Includes\Classes\Context::getAll4Skin());
			$o_grant = \X2board\Includes\Classes\Context::get('grant');

			ob_start();
			if(false) { // $board->use_editor == 'yes') {
				wp_editor($content, $editor_uid, array('media_buttons'=>$o_grant->is_admin, 'textarea_name'=>$content_field_name, 'editor_height'=>$editor_height));  //  'editor_class'=>'comment-textarea'
			}
			else {
				echo '<textarea class="comment-textarea" cols="50" rows="'.$textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$content_field_name.'" placeholder="'.__('Add a comment', 'x2board').'..." required>'.esc_textarea($content).'</textarea>';
			}
			unset($o_grant);

			$s_editor = ob_get_clean();
			return apply_filters('x2board_comment_editor', $s_editor);
		}*/

		/**
		 * editor스킨의 hidden field 출력
		 */
		/*public function ob_get_comment_hidden_fields() { 
			ob_start();
			wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_COMMENT, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');

			$header = array();
			$a_header['cmd'] = X2B_CMD_PROC_WRITE_COMMENT;
			$a_header['board_id'] = get_the_ID();

			$o_post = \X2board\Includes\Classes\Context::get('post');
			if(isset($o_post)) {  // insert a root comment
				if($o_post->post_id) {  // this is mandatory
					$a_header['parent_post_id'] = $o_post->post_id;
				}			
				unset($o_post);
				$a_header['content'] = null;
			}
			else {  // insert a child comment
				$o_the_comment = \X2board\Includes\Classes\Context::get('o_the_comment');
				$a_header['parent_post_id'] = $o_the_comment->get('post_id');
				$a_header['parent_comment_id'] = $o_the_comment->get('parent_comment_id');
				$a_header['comment_id'] = $o_the_comment->get('comment_id');
				$a_header['content'] = htmlspecialchars($o_the_comment->get('content'));
			}
			
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			$s_field = ob_get_clean();
			// do_action('x2b_skin_editor_header_after', $content, $board);
			return apply_filters('x2board_comment_field', $s_field);
		}*/
		/**
		 * modify comment editor
		 * /includes/no_namespace.helper.php::x2b_write_comment_content_editor()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		// public function write_comment_content_editor() { 
		// 	$s_editor_html = null;
		// 	$o_comment = \X2board\Includes\Classes\Context::get('o_comment');
		// 	if($o_comment->comment_id) {  // update a old comment
				/*$o_editor_conf = new \stdClass();
				$o_editor_conf->editor_type = 'textarea';
				$o_editor_conf->s_content_field_name = 'comment_content';
				// $o_post = \X2board\Includes\Classes\Context::get('post');
				// $o_editor_conf->s_editor_uid = 'comment_content_'.$o_post->post_id;
				// unset($o_post);
				$o_editor_conf->s_content = $o_comment->content;
				$o_editor_conf->n_editor_height = 400;*/
				// $s_editor_html = $this->get_editor_html('comment', 1234, null, __('Add a comment', 'x2board').'...' );//$o_editor_conf);
				// unset($o_editor_conf);
		// 	}
		// 	unset($o_comment);
		// 	return $s_editor_html;
		// }
		
		/**
		 * 입력 필드를 사용할 수 있는 권한인지 확인한다.
		 * @param string $name
		 * @return boolean
		 */
		// public function isUseFields($permission, $roles){
		/*private function _is_available_user_field($permission, $roles) {
			// $board = $this->board;
			// if($board->isAdmin()){
			// 	return true;
			// }
			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
	// var_dump($o_logged_info->roles);		
			if($o_logged_info->is_admin == 'Y') {
				return true;
			}
			switch($permission){
				case 'all': 
					return true;
				case 'author': 
					return is_user_logged_in() ? true : false;
				case 'roles':
					if(is_user_logged_in()){
						if(array_intersect($roles, (array)$o_logged_info->roles)){
							return true;
						}
					}
					return false;
				default: 
					return true;
			}
		}*/

		/*public function get_comment_editor() {
			$o_rst = new \stdClass();
			$o_rst->s_comment_editor_html = null;//$this->get_editor_html('comment', 1234, null, __('Add a comment', 'x2board').'...' ); //$o_editor_conf);
			// unset($o_editor_conf);

			// $o_rst->s_comment_hidden_field_html = $this->_ob_get_comment_hidden_fields();
			unset($o_post);
			return $o_rst; 
		}*/

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		/*public static function write_comment_hidden_fields() {
			$a_header = array();
			$a_header['board_id'] = get_the_ID();
			$a_header['parent_post_id'] = \X2board\Includes\Classes\Context::get('post_id');
			$o_comment = \X2board\Includes\Classes\Context::get('o_comment');
// var_dump($o_comment);
			if($o_comment->comment_id) {  // update a old comment
				$a_header['cmd'] = X2B_CMD_PROC_MODIFY_COMMENT;
				$a_header['comment_id'] = $o_comment->comment_id;
			}
			else { // write a new comment
				$a_header['cmd'] = X2B_CMD_PROC_WRITE_COMMENT; 
			}
			unset($o_comment);
			wp_nonce_field('x2b_'.$a_header['cmd'], 'x2b_'.$a_header['cmd'].'_nonce');
			// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			// do_action('x2b_skin_editor_header_after', $content, $board);
		}*/
	}
}
/* End of file editor.view.php */