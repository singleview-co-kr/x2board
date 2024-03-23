<?php
namespace X2board\Includes\Classes;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Skin {
	const SKIN_ABS_PATH = 'includes/modules/board/skins/';
	private static $instance;
	private $active = array();
	private $list;
	private $latestview_list;
	private $merged_list;

	private $_default_fields = null;  // load from post.model
	private $_extends_fields = null;  // load from post.model
	
	private function __construct(){
		$s_dir = X2B_PATH . self::SKIN_ABS_PATH;
		$s_url = X2B_URL . self::SKIN_ABS_PATH;
		if( $h_dir = @opendir($s_dir) ) {
			while(($name = readdir($h_dir)) !== false){
				if($name == '.' || $name == '..' || $name == 'readme.txt' || $name == '__MACOSX' || $name == '.git') continue;
				$skin = new \stdClass();
				$skin->name = $name;
				$skin->dir = $s_dir . $name;
				$skin->url = $s_url . $name;
				$this->list[$name] = $skin;
			}
		}
		if( is_resource($h_dir) ) {
			closedir($h_dir);
		}
		
		$this->list = apply_filters('x2board_skin_list', $this->list);
		$this->latestview_list = apply_filters('x2board_skin_latestview_list', $this->list);
		$this->merged_list = array_merge($this->list, $this->latestview_list);
		
		ksort($this->list);
		// ksort($this->latestview_list);
		ksort($this->merged_list);
	}
	
	/**
	 * singleton
	 * @return \X2board\Includes\Classes\Skin
	 */
	public static function getInstance(){
		if(!self::$instance) self::$instance = new Skin();
		return self::$instance;
	}

	/**
	 * 스킨을 불러온다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	public function render($skin_name, $file){  //, $vars=array()){
		ob_start();
		
		$current_file_path = '';

		if(isset($this->merged_list[$skin_name])){
			extract(\X2board\Includes\Classes\Context::getAll4Skin(), EXTR_SKIP);
// var_dump(\X2board\Includes\Classes\Context::getAll4Skin());
// var_dump($page_navigation);
			
// var_dump($this->merged_list[$skin_name]);
			// $is_ajax = false;
			// if(defined('DOING_AJAX') && DOING_AJAX){
			// 	if(file_exists("{$this->merged_list[$skin_name]->dir}/ajax-{$file}")){
			// 		$is_ajax = true;
			// 	}
			// }
			
			// $is_admin = false;
			// if(is_admin()){
			// 	if(file_exists("{$this->merged_list[$skin_name]->dir}/admin-{$file}")){
			// 		$is_admin = true;
			// 	}
			// }
			
			// if($is_ajax){
			// 	$current_file_path = "{$this->merged_list[$skin_name]->dir}/ajax-{$file}";
			// }
			// else if($is_admin){
			// 	$current_file_path = "{$this->merged_list[$skin_name]->dir}/admin-{$file}";
			// }
			// else{
				$current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
			// }
			
			$current_file_path = apply_filters('kboard_skin_file_path', $current_file_path, $skin_name, $file);  //, $vars, $this);
		}

		if($current_file_path && file_exists($current_file_path)){
			include $current_file_path;
		}
		else{
			echo sprintf(__('%s file does not exist.', 'kboard'), $file);
		}
		return ob_get_clean();
	}
	
	/**
	 * editor 스킨의 hidden field 출력
	 */
	public function write_post_hidden_fields() { 
		wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_POST, 'x2b_'.X2B_CMD_PROC_WRITE_POST.'_nonce');
		// if($o_post->post_id) {
		// 	wp_nonce_field("x2b_".X2B_CMD_PROC_MODIFY_POST."_".$o_post->post_id, 'x2b_'.X2B_CMD_PROC_MODIFY_POST.'_nonce');
		// }
		
		// do_action('x2b_skin_editor_header_before', $content, $board);
		$o_post = \X2board\Includes\Classes\Context::get('post');
		$header = array();
		$a_header['cmd'] = "proc_write_post"; // '<input type="hidden" name="action" value="x2b_write_post">';
		$a_header['board_id'] = get_the_ID(); // sprintf('<input type="hidden" name="board_id" value="%d">', $o_post->board_id);
		// $a_header['mod'] = "editor"; // '<input type="hidden" name="mod" value="editor">';
		if($o_post->post_id) {
			$a_header['post_id'] = $o_post->post_id; // sprintf('<input type="hidden" name="post_id" value="%d">', $o_post->post_id);
			$a_header['parent_post_id'] = $o_post->parent_post_id; // sprintf('<input type="hidden" name="parent_post_id" value="%d">', $o_post->parent_post_id);
		}
		
		// $a_header['post_author'] = $o_post->post_author; // sprintf('<input type="hidden" name="post_author" value="%d">', $o_post->post_author);
		// $a_header['nick_name'] = $o_post->nick_name; // sprintf('<input type="hidden" name="member_display" value="%s">', $o_post->nick_name);
		// $a_header['regdate'] = $o_post->regdate; // sprintf('<input type="hidden" name="date" value="%s">', $o_post->regdate);
		// $a_header['user_id'] = get_current_user_id(); // sprintf('<input type="hidden" name="user_id" value="%d">', get_current_user_id());
		unset($o_post);
		// $product_id = isset($_GET['woocommerce_product_tabs_inside']) ? intval($_GET['woocommerce_product_tabs_inside']) : '';
		// if($product_id){
		// 	$header['x2b_option_woocommerce_product_id'] = sprintf('<input type="hidden" name="x2b_option_woocommerce_product_id" value="%d">', $product_id);
		// }
		
		// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
		foreach( $a_header as $s_field_name => $s_field_value ) {
			echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
		}
		// do_action('x2b_skin_editor_header_after', $content, $board);
	}

	/**
	 * editor 스킨의 사용자 입력 field 출력
	 */
	public function write_post_prepare_single_user_field() { 
		$o_post_model = \X2board\Includes\getModel('post');
		$this->_default_fields = $o_post_model->default_fields;  // get_default_user_input_fields();
		$this->_extends_fields = $o_post_model->extends_fields;  // get_extended_user_input_fields();
		unset($o_post_model);
	}
	/**
	 * editor 스킨의 사용자 입력 field 출력
	 */
	// public function getTemplate($field, $content='', $boardBuilder=''){
	public function write_post_single_user_field($a_field_info) { 
		// foreach($board->fields()->getSkinFields() as $key=>$field) {
		// 	$board->fields()->getTemplate($field, $content, $boardBuilder);
		// }
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

		$field = apply_filters('kboard_get_template_field_data', $field); //, $content, $this->board);

		$field_name = (isset($field['field_name']) && $field['field_name']) ? $field['field_name'] : $this->_get_field_label($field);
		$required = (isset($field['required']) && $field['required']) ? 'required' : '';
		$placeholder = (isset($field['placeholder']) && $field['placeholder']) ? $field['placeholder'] : '';
		$wordpress_search = '';
		$default_value = (isset($field['default_value']) && $field['default_value']) ? $field['default_value'] : '';
		$html = (isset($field['html']) && $field['html']) ? $field['html'] : '';
		$shortcode = (isset($field['shortcode']) && $field['shortcode']) ? $field['shortcode'] : '';
		$row = false;

		$content = new \stdClass();
		$content->title = '';
		$content->nick_name = '';
		$content->content = '';
		$content->getCategoryList = array();
		$content->search = false;
		
		$board = new \stdClass(); 
		$board->viewUsernameField = true;
		$board->useCAPTCHA = false;
		$board->use_editor = '';
		
		if($field['field_type'] == 'content'){
			$board->editor_html = $this->get_editor_html(array(
									'board' => $board,
									'content' => $content,
									'required' => $required,
									'placeholder' => $placeholder,
									'editor_height' => '400',
								));
		}
		
		
		$default_value_list = array();
		if(isset($field['row']) && $field['row']){
			foreach($field['row'] as $item){
				if(isset($item['label']) && $item['label']){
					$row = true;
					if(isset($item['default_value']) && $item['default_value']){
						$default_value_list[] = $item['label'];
					}
				}
			}
		}
		
		if($default_value_list){
			$default_value = $default_value_list;
		}
		
		if($field['field_type'] == 'search'){
			if($content->search){
				$wordpress_search = $content->search;
			}
			else if(isset($field['default_value']) && $field['default_value']){
				$wordpress_search = $field['default_value'];
			}
		}
		
		
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
		if(strpos($html, '#{ESC_ATTR_VALUE}') !== false){
			$value = $content->option->{$meta_key} ? esc_attr($content->option->{$meta_key}) : esc_attr($default_value);
			$html = str_replace('#{ESC_ATTR_VALUE}', $value, $html);
		}
		if(strpos($html, '#{ESC_TEXTAREA_VALUE}') !== false){
			$value = $content->option->{$meta_key} ? esc_textarea($content->option->{$meta_key}) : esc_textarea($default_value);
			$html = str_replace('#{ESC_TEXTAREA_VALUE}', $value, $html);
		}
		if(strpos($html, '#{ESC_HTML_VALUE}') !== false){
			$value = $content->option->{$meta_key} ? esc_html($content->option->{$meta_key}) : esc_html($default_value);
			$html = str_replace('#{ESC_HTML_VALUE}', $value, $html);
		}
		
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
		
		$skin_name = 'sketchbook5';
		$file = 'editor-fields.php';
		$current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
		// $current_file_path = apply_filters('kboard_skin_file_path', $current_file_path, $skin_name, $file);  //, $vars, $this);

		if($current_file_path && file_exists($current_file_path)){
			include $current_file_path;
		}
		else{
			echo sprintf(__('%s file does not exist.', 'kboard'), $file);
		}
		
		// echo $field_html; //apply_filters('kboard_get_template_field_html', $field_html, $field, $content, $this->board);
		
		// do_action("kboard_skin_field_after_{$meta_key}", $field, $content, $this->board);
		// do_action('kboard_skin_field_after', $field, $content, $this->board);
		
		// $template = ob_get_clean();
		// return $template;
	}

	/**
	 * 게시글 본문 에디터 코드를 반환한다.
	 * @param array $vars
	 * @return string
	 */
	// kboard_content_editor()
	public function get_editor_html($vars=array()){
		$vars = array_merge(array(
			'required' => '',
			'placeholder' => '',
			'editor_height' => '400',
		), $vars);
		
		// $vars = apply_filters('kboard_content_editor_vars', $vars);
		extract($vars, EXTR_SKIP);
		
		ob_start();
		if($board->use_editor == 'yes'){
			wp_editor($content->content, 'content', array('editor_height'=>$editor_height));
		}
		else{
			echo sprintf('<textarea id="content" class="editor-textarea %s" name="content" placeholder="%s">%s</textarea>', esc_attr($required), esc_attr($placeholder), esc_textarea($content->content));
		}
		$s_editor_html = ob_get_clean();
		return apply_filters('x2board_content_editor', $s_editor_html); //, $vars);
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
		$field = $a_field;
		$field_type = $field['field_type'];
		
		$fields = apply_filters('kboard_admin_default_fields', $this->_default_fields); //, $this->board);
		if(isset($fields[$field_type])){
			return $fields[$field_type]['field_label'];
		}
		
		$fields = apply_filters('kboard_admin_extends_fields', $this->_extends_fields); //, $this->board);
		if(isset($fields[$field_type])){
			return $fields[$field_type]['field_label'];
		}
		
		return $field['field_label'];
	}
	
	/**
	 * 스킨의 editor_comment 폼에 필수 정보를 출력한다.
	 * @param KBContent $content
	 * @param KBoard $board
	 */
	public function editorHeaderComment($content, $board){
		if($content->uid){
			wp_nonce_field("kboard-comments-update-{$content->uid}", 'kboard-comments-update-nonce');
		}
		
		do_action('kboard_skin_editor_header_before', $content, $board);
		
		// action="http://127.0.0.1/?action=kboard_comment_update&uid=1312&kboard-comments-update-nonce=936d897058"
		$header = array();
		$header[] = '<input type="hidden" name="action" value="kboard_comment_update">';
		$header[] = sprintf('<input type="hidden" name="uid" value="%d">', $content->uid);
		$header = apply_filters('kboard_skin_editor_header', $header, $content, $board);
		
		foreach($header as $input){
			echo $input . "\n";
		}
		do_action('kboard_skin_editor_header_after', $content, $board);
	}
	
	/**
	 * 모든 스킨 리스트를 반환한다.
	 * @return array
	 */
	// public function getList(){
	// 	return $this->list ? $this->list : array();
	// }
	
	/**
	 * 최신글 모아보기 스킨 리스트를 반환한다.
	 */
	// public function getLatestviewList(){
	// 	return $this->latestview_list ? $this->latestview_list : array();
	// }
	
	/**
	 * 모든 스킨과 최신글 모아보기 스킨의 합쳐진 리스트를 반환한다.
	 * @return array
	 */
	// public function getMergedList(){
	// 	return $this->merged_list ? $this->merged_list : array();
	// }
	
	/**
	 * 스킨 파일이 있는지 확인한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return boolean
	 */
	// public function fileExists($skin_name, $file){
	// 	$file_exists = false;
	// 	$current_file_path = '';
		
	// 	if(isset($this->merged_list[$skin_name])){
	// 		$is_ajax = false;
	// 		if(defined('DOING_AJAX') && DOING_AJAX){
	// 			if(file_exists("{$this->merged_list[$skin_name]->dir}/ajax-{$file}")){
	// 				$is_ajax = true;
	// 			}
	// 		}
			
	// 		$is_admin = false;
	// 		if(is_admin()){
	// 			if(file_exists("{$this->merged_list[$skin_name]->dir}/admin-{$file}")){
	// 				$is_admin = true;
	// 			}
	// 		}
			
	// 		if($is_ajax){
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/ajax-{$file}";
	// 		}
	// 		else if($is_admin){
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/admin-{$file}";
	// 		}
	// 		else{
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
	// 		}
	// 	}
		
	// 	if($current_file_path && file_exists($current_file_path)){
	// 		$file_exists = true;
	// 	}
	// 	return $file_exists;
	// }
	
	/**
	 * 스킨의 functions.php 파일을 불러온다.
	 * @param string $skin_name
	 */
	// public function loadFunctions($skin_name){
	// 	if(isset($this->merged_list[$skin_name]) && file_exists("{$this->merged_list[$skin_name]->dir}/functions.php")){
	// 		include_once "{$this->merged_list[$skin_name]->dir}/functions.php";
	// 	}
	// }
	
	/**
	 * 템플릿 파일을 불러온다.
	 * @param string $file
	 * @param array $vars
	 * @return string
	 */
	// public function loadTemplate($file, $vars=array()){
	// 	ob_start();
		
	// 	$template_file_path = X2B_PATH . '/template/skin/' . $file;
		
	// 	if(file_exists($template_file_path)){
	// 		extract($vars, EXTR_SKIP);
			
	// 		include $template_file_path;
	// 	}
	// 	else{
	// 		echo sprintf(__('%s file does not exist.', 'kboard'), $file);
	// 	}
		
	// 	return ob_get_clean();
	// }
	
	/**
	 * 스킨 URL 주소를 반환한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	// public function url($skin_name, $file=''){
	// 	if(isset($this->merged_list[$skin_name])){
	// 		return "{$this->merged_list[$skin_name]->url}" . ($file ? "/{$file}" : '');
	// 	}
	// 	return '';
	// }
	
	/**
	 * 스킨 DIR 경로를 반환한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	// public function dir($skin_name, $file=''){
	// 	if(isset($this->merged_list[$skin_name])){
	// 		return "{$this->merged_list[$skin_name]->dir}" . ($file ? "/{$file}" : '');
	// 	}
	// 	return '';
	// }
	
	/**
	 * 사용 중인 스킨 리스트를 반환한다.
	 * @return array
	 */
	// public function getActiveList(){
	// 	global $wpdb;
		
	// 	$blog_id = get_current_blog_id();
	// 	if(!isset($this->active[$blog_id]) || !$this->active[$blog_id]){
	// 		$this->active[$blog_id] = array();
	// 		$results = $wpdb->get_results("SELECT `skin` FROM `{$wpdb->prefix}kboard_board_setting` UNION SELECT `skin` FROM `{$wpdb->prefix}kboard_board_latestview`");
			
	// 		foreach($results as $row){
	// 			$this->active[$blog_id][] = $row->skin;
	// 		}
	// 	}
		
	// 	return apply_filters('kboard_skin_active_list', $this->active[$blog_id]);
	// }
	
	// public function getOptionSearchFieldKey($key, $compare){ }
	// public function getOptionSearchFieldValue($key, $value){ }
}