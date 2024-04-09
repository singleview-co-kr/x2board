<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

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

		private $_default_fields = array();  // get_default_user_input_fields();
		private $_extends_fields = array();

		function __construct() {
// var_dump('editor view class __construct');
		}
		
		/**
		 * @brief Initialization
		 */
		function init() {}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_prepare_single_user_field()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		public function write_post_prepare_single_user_field() { 
			$o_post_model = \X2board\Includes\getModel('post');
			$this->_default_fields = $o_post_model->default_fields;  // get_default_user_input_fields();
			$this->_extends_fields = $o_post_model->extends_fields;  // get_extended_user_input_fields();
			unset($o_post_model);
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		public function write_post_hidden_fields() {
			$a_header = array();
			$a_header['board_id'] = get_the_ID();
			$o_post = \X2board\Includes\Classes\Context::get('post');
			if($o_post->post_id) {  // update a old post
				$a_header['cmd'] = X2B_CMD_PROC_MODIFY_POST;
				$a_header['post_id'] = $o_post->post_id;
				// $a_header['parent_post_id'] = $o_post->parent_post_id;
			}
			else { // write a new post
				$a_header['cmd'] = X2B_CMD_PROC_WRITE_POST; 
				$a_header['post_id'] = \X2board\Includes\getNextSequence(); // reserve new post id for file appending
			}
			unset($o_post);
			// }
			// $product_id = isset($_GET['woocommerce_product_tabs_inside']) ? intval($_GET['woocommerce_product_tabs_inside']) : '';
			// if($product_id){
			// 	$header['x2b_option_woocommerce_product_id'] = sprintf('<input type="hidden" name="x2b_option_woocommerce_product_id" value="%d">', $product_id);
			// }
			$o_file_controller = \X2board\Includes\getController('file');
			$o_file_controller->set_upload_info($a_header['post_id'], $a_header['post_id']);
			unset($o_file_controller);

			wp_nonce_field('x2b_'.$a_header['cmd'], 'x2b_'.$a_header['cmd'].'_nonce');
			
			// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			// do_action('x2b_skin_editor_header_after', $content, $board);
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_single_user_field()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		// public function getTemplate($field, $content='', $boardBuilder=''){
		public function write_post_single_user_field($a_field_info) { 
			$field = $a_field_info;
			$template = '';
			$permission = (isset($field['permission']) && $field['permission']) ? $field['permission'] : '';
			$roles = (isset($field['roles']) && $field['roles']) ? $field['roles'] : '';
			$meta_key = (isset($field['meta_key']) && $field['meta_key']) ? sanitize_key($field['meta_key']) : '';
			
			if(!$this->_is_available_user_field($permission, $roles) && $meta_key){
				return;
			}
			// if(!$content){
				// $content = new KBContent();
			// }

			// $field = apply_filters('kboard_get_template_field_data', $field); //, $content, $this->board);

			$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $this->_get_field_label($field);
			$required = (isset($field['required']) && $field['required']) ? 'required' : '';
			$placeholder = (isset($field['placeholder']) && $field['placeholder']) ? $field['placeholder'] : '';
			$wordpress_search = '';
			$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
			$html = (isset($field['html']) && $field['html']) ? $field['html'] : '';
			$shortcode = (isset($field['shortcode']) && $field['shortcode']) ? $field['shortcode'] : '';
			$row = false;

			// $content = new \stdClass();
			// $content->title = '';
			// $content->nick_name = '';
			// $content->content = '';
			// $content->getCategoryList = array();
			// $content->search = false;
			
			// $board = new \stdClass(); 
			// $board->viewUsernameField = true;
			// $board->useCAPTCHA = false;
			// $board->use_editor = '';
			
			if($field['field_type'] == 'content'){
				$o_editor_conf = new \stdClass();
				$o_editor_conf->editor_type = 'textarea';
				$o_post = \X2board\Includes\Classes\Context::get('post');
				$o_editor_conf->s_content = $o_post->content;
				unset($o_post);
				$o_editor_conf->s_required = $required;
				$o_editor_conf->s_placeholder = $placeholder;
				$o_editor_conf->n_editor_height = 400;
				$o_editor_conf->s_content_field_name = 'content';
				$editor_html = $this->_ob_get_editor_html($o_editor_conf);
				unset($o_editor_conf);
			}
			elseif($field['field_type'] == 'attach') {
				$o_module_info = \X2board\Includes\Classes\Context::get('module_info');
				$s_accept_file_types = str_replace(" ", "", $o_module_info->file_allowed_filetypes);
				$s_accept_file_types = str_replace(",", "|", $s_accept_file_types);
				$n_file_max_attached_count = intval($o_module_info->file_max_attached_count);
				$n_file_allowed_filesize_mb = intval($o_module_info->file_allowed_filesize_mb);
				unset($o_module_info);
				wp_enqueue_style("x2board-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload.css', [], X2B_VERSION);
				wp_enqueue_style("x2board-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload-ui.css', [], X2B_VERSION);
				wp_enqueue_script('x2board-jquery-ui-widget', X2B_URL . '/assets/jquery.fileupload/js/vendor/jquery.ui.widget.js', ['jquery'], X2B_VERSION, true);
				wp_enqueue_script('x2board-jquery-iframe-transport', X2B_URL . '/assets/jquery.fileupload/js/jquery.iframe-transport.js', ['jquery'], X2B_VERSION, true);
				wp_enqueue_script('x2board-fileupload', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload.js', ['jquery'], X2B_VERSION, true);
				wp_enqueue_script('x2board-fileupload-process', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload-process.js', ['jquery'], X2B_VERSION, true);
				wp_enqueue_script('x2board-fileupload-caller', X2B_URL . '/assets/jquery.fileupload/file-upload.js', ['jquery'], X2B_VERSION, true);
			}

			$post = \X2board\Includes\Classes\Context::get('post');
			
			// $default_value_list = array();
			// if(isset($field['row']) && $field['row']){
			// 	foreach($field['row'] as $item){
			// 		if(isset($item['label']) && $item['label']){
			// 			$row = true;
			// 			if(isset($item['default_value']) && $item['default_value']){
			// 				$default_value_list[] = $item['label'];
			// 			}
			// 		}
			// 	}
			// }
			
			// if($default_value_list){
			// 	$default_value = $default_value_list;
			// }
			
			// if($field['field_type'] == 'search'){
			// 	if($content->search){
			// 		$wordpress_search = $content->search;
			// 	}
			// 	else if(isset($field['default_value']) && $field['default_value']){
			// 		$wordpress_search = $field['default_value'];
			// 	}
			// }
			
			// $order = new KBOrder();
			// $order->board = $this->board;
			// $order->board_id = $this->board->id;
			
			// $url = new KBUrl();
			// $url->setBoard($this->board);
			
			// $skin = KBoardSkin::getInstance();
			
			// if(!$boardBuilder){
			// 	$boardBuilder = new KBoardBuilder($this->board->id);
			// 	$boardBuilder->setSkin($this->board->skin);
			// 	if(wp_is_mobile() && $this->board->meta->mobile_page_rpp){
			// 		$builder->setRpp($this->board->meta->mobile_page_rpp);
			// 	}
			// 	else{
			// 		$boardBuilder->setRpp($this->board->page_rpp);
			// 	}
			// 	$boardBuilder->board = $this->board;
			// }
			// var_dump($this->board->skin)			;
			// if(strpos($html, '#{ESC_ATTR_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_attr($content->option->{$meta_key}) : esc_attr($default_value);
			// 	$html = str_replace('#{ESC_ATTR_VALUE}', $value, $html);
			// }
			// if(strpos($html, '#{ESC_TEXTAREA_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_textarea($content->option->{$meta_key}) : esc_textarea($default_value);
			// 	$html = str_replace('#{ESC_TEXTAREA_VALUE}', $value, $html);
			// }
			// if(strpos($html, '#{ESC_HTML_VALUE}') !== false){
			// 	$value = $content->option->{$meta_key} ? esc_html($content->option->{$meta_key}) : esc_html($default_value);
			// 	$html = str_replace('#{ESC_HTML_VALUE}', $value, $html);
			// }
			
			// $parent = new KBContent();
			// $parent->initWithUID($content->parent_uid);
			
			// $vars = array(
			// 	'field' => $field,
			// 	'meta_key' => $meta_key,
			// 	'field_name' => $field_name,
			// 	'required' => $required,
			// 	'placeholder' => $placeholder,
			// 	'row' => $row,
			// 	'wordpress_search' => $wordpress_search,
			// 	'default_value' => $default_value,
			// 	'html' => $html,
			// 	'shortcode' => $shortcode,
			// 	'board' => $this->board,
			// 	'content' => $content,
			// 	'parent' => $parent,
			// 	// 'fields' => $this,
			// 	// 'order' => $order,
			// 	'url' => $url,
			// 	'skin' => $skin,
			// 	'skin_path' => $skin->url($this->board->skin),
			// 	'skin_dir' => $skin->dir($this->board->skin),
			// 	'boardBuilder' => $boardBuilder
			// );
			
			// ob_start();
			
			// do_action('kboard_skin_field_before', $field, $content, $this->board);
			// do_action("kboard_skin_field_before_{$meta_key}", $field, $content, $this->board);

			// if($skin->fileExists($this->board->skin, "editor-field-{$meta_key}.php")){
			// 	$field_html = $skin->load($this->board->skin, "editor-field-{$meta_key}.php", $vars);
			// }
			// else{
				// $field_html = $this->render($this->board->skin, 'editor-fields.php', $vars);
				// $field_html = $this->render('sketchbook5', 'editor-fields.php');
			// }
			
			// if(!$field_html){
			// 	$field_html = $skin->loadTemplate('editor-fields.php', $vars);
			// }
			
			// $skin_name = 'sketchbook5';
			// $file = 'editor-fields.php';
			// $current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
			// $current_file_path = apply_filters('kboard_skin_file_path', $current_file_path, $skin_name, $file);  //, $vars, $this);

			// if($current_file_path && file_exists($current_file_path)){
			// 	include $current_file_path;
			// }
			// else{
			// 	echo sprintf(__('%s file does not exist.', 'x2board'), $file);
			// }

			$s_skin_path = \X2board\Includes\Classes\Context::get('skin_path_abs'); // this sets on board.view.php::init()
			$s_skin_file_abs_path = $s_skin_path . '/editor-fields.php';
			if( !file_exists( $s_skin_file_abs_path ) ) {
				echo sprintf(__('%s file does not exist.', 'x2board'), $s_skin_file_abs_path);
			}
			include $s_skin_file_abs_path;
		}

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		public function write_comment_hidden_fields() {
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
		}
		
		public function get_comment_editor() {
			$o_editor_conf = new \stdClass();
			$o_editor_conf->editor_type = 'textarea';
			$o_editor_conf->s_content_field_name = 'comment_content';
			// $o_post = \X2board\Includes\Classes\Context::get('post');
			// $o_editor_conf->s_editor_uid = 'comment_content_'.$o_post->post_id;
			// unset($o_post);
			$o_editor_conf->s_content = null;
			$o_editor_conf->s_placeholder = __('Add a comment', 'x2board').'...';
			$o_editor_conf->n_editor_height = 200;
			$o_editor_conf->n_textarea_rows = 4;
			
			$o_rst = new \stdClass();
			$o_rst->s_comment_editor_html = $this->_ob_get_editor_html($o_editor_conf);
			unset($o_editor_conf);

			$o_rst->s_comment_hidden_field_html = $this->_ob_get_comment_hidden_fields();
			unset($o_post);
			return $o_rst; 
		}

		/**
		 * modify comment editor
		 * /includes/no_namespace.helper.php::x2b_write_comment_content_editor()를 통해서
		 * editor 스킨의 사용자 입력 field 출력
		 */
		public function write_comment_content_editor() { 
			$s_editor_html = null;
			$o_comment = \X2board\Includes\Classes\Context::get('o_comment');
			if($o_comment->comment_id) {  // update a old comment
				$o_editor_conf = new \stdClass();
				$o_editor_conf->editor_type = 'textarea';
				$o_editor_conf->s_content_field_name = 'comment_content';
				// $o_post = \X2board\Includes\Classes\Context::get('post');
				// $o_editor_conf->s_editor_uid = 'comment_content_'.$o_post->post_id;
				// unset($o_post);
				$o_editor_conf->s_content = $o_comment->content;
				$o_editor_conf->n_editor_height = 400;
				$s_editor_html = $this->_ob_get_editor_html($o_editor_conf);
				unset($o_editor_conf);
			}
			unset($o_comment);
			return $s_editor_html;
		}
		
		/**
		 * 입력 필드를 사용할 수 있는 권한인지 확인한다.
		 * @param string $name
		 * @return boolean
		 */
		// public function isUseFields($permission, $roles){
		private function _is_available_user_field($permission, $roles) {
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
		}

		/**
		 * 번역된 필드의 레이블을 반환한다.
		 * @param array $field
		 * @return string
		 */
		// public function getFieldLabel($field){
		private function _get_field_label($a_field){
			$field_type = $a_field['field_type'];
			if(isset($this->_default_fields[$field_type])){
				return $this->_default_fields[$field_type]['field_label'];
			}
			if(isset($this->_extends_fields[$field_type])){
				return $this->_extends_fields[$field_type]['field_label'];
			}
			return $a_field['field_label'];
		}
	
		/**
		 * editor스킨의 hidden field 출력
		 */
		private function _ob_get_comment_hidden_fields() { 
			ob_start();
			wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_COMMENT, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
			$o_post = \X2board\Includes\Classes\Context::get('post');

			$header = array();
			$a_header['cmd'] = X2B_CMD_PROC_WRITE_COMMENT;
			$a_header['board_id'] = get_the_ID();
			if($o_post->post_id) {  // this is mandatory
				$a_header['parent_post_id'] = $o_post->post_id;
			}			
			unset($o_post);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			$s_field = ob_get_clean();
			// do_action('x2b_skin_editor_header_after', $content, $board);
			return apply_filters('x2board_comment_field', $s_field);
		}

		/**
		 * post와 comment 에디터 HTML 반환
		 * @param array $vars
		 * @return string
		 */
		private function _ob_get_editor_html($o_editor_conf_in_arg) {
			$o_editor_conf = new \stdClass();
			if( is_null( $o_editor_conf_in_arg->s_content_field_name ) ) {
				wp_die( __('invalid editor field name - blank', 'x2board') );
			}
			$o_editor_conf->s_content_field_name = $o_editor_conf_in_arg->s_content_field_name;
			$o_editor_conf->s_editor_type = isset( $o_editor_conf_in_arg->s_editor_type ) ? $o_editor_conf_in_arg->s_editor_type : 'textarea';
			$o_editor_conf->s_content = isset( $o_editor_conf_in_arg->s_content ) ? $o_editor_conf_in_arg->s_content : null;
			$o_editor_conf->s_required = isset( $o_editor_conf_in_arg->s_required ) ? $o_editor_conf_in_arg->s_required : '';
			$o_editor_conf->s_placeholder = isset( $o_editor_conf_in_arg->s_placeholder ) ? $o_editor_conf_in_arg->s_placeholder : __('Type what you think', 'x2board');
			$o_editor_conf->s_editor_uid = isset( $o_editor_conf_in_arg->s_editor_uid ) ? $o_editor_conf_in_arg->s_editor_uid : '';
			$o_editor_conf->n_editor_height = isset( $o_editor_conf_in_arg->n_editor_height ) ? $o_editor_conf_in_arg->n_editor_height : 400;
			$o_editor_conf->n_textarea_rows = isset( $o_editor_conf_in_arg->n_textarea_rows ) ? $o_editor_conf_in_arg->n_textarea_rows : 50;
			
			// $o_grant = \X2board\Includes\Classes\Context::get('grant');

			ob_start();
			if($o_editor_conf->s_editor_type == 'wordpress'){
				wp_editor($o_editor_conf->s_content, $o_editor_conf->s_content_field_name, array('editor_height'=>$o_editor_conf->n_editor_height));
				// wp_editor($content, $editor_uid, array('media_buttons'=>$o_grant->is_admin, 'textarea_name'=>$content_field_name, 'editor_height'=>$editor_height));  //  'editor_class'=>'comment-textarea'
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
			$s_editor_html = ob_get_clean();
			unset($o_editor_conf);
			return $s_editor_html;
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

	}
}
/* End of file editor.view.php */