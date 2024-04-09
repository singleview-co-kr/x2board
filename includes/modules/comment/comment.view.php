<?php
/**
 * commentView class
 * comment module's view class
 *
 * @author singleview.co.kr
 * @package /modules/comment
 * @version 0.1
 */
namespace X2board\Includes\Modules\Comment;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Comment\\commentView')) {
	
	class commentView extends comment
	{

		/**
		 * Initialization
		 * @return void
		 */
		public function init()	{ }

		/**
		 * /includes/no_namespace.helper.php::x2b_write_post_hidden_fields()를 통해서
		 * editor스킨의 hidden field 출력
		 */
		/*public function ob_get_hidden_fields() { 
			ob_start();
			wp_nonce_field('x2b_'.X2B_CMD_PROC_WRITE_COMMENT, 'x2b_'.X2B_CMD_PROC_WRITE_COMMENT.'_nonce');
			//wp_nonce_field('kboard-comments-execute-'.$post->post_id, 'kboard-comments-execute-nonce', !wp_doing_ajax())
			// if($o_post->post_id) {
			// 	wp_nonce_field("x2b_".X2B_CMD_PROC_MODIFY_POST."_".$o_post->post_id, 'x2b_'.X2B_CMD_PROC_MODIFY_POST.'_nonce');
			// }
			
			// do_action('x2b_skin_editor_header_before', $content, $board);
			$o_post = \X2board\Includes\Classes\Context::get('post');
			$header = array();
			$a_header['cmd'] = X2B_CMD_PROC_WRITE_COMMENT;
			$a_header['board_id'] = get_the_ID();
			if($o_post->post_id) {  // this is mandatory
				$a_header['parent_post_id'] = $o_post->post_id;
			}			
			unset($o_post);
			// $header = apply_filters('x2b_skin_editor_header', $header, $content, $board);
			foreach( $a_header as $s_field_name => $s_field_value ) {
				echo '<input type="hidden" name="'.$s_field_name.'" value="'.$s_field_value.'">' . "\n";
			}
			unset($a_header);
			$s_field = ob_get_clean();
			// do_action('x2b_skin_editor_header_after', $content, $board);
			return apply_filters('x2board_comment_field', $s_field);
		}*/

		/**
		 * Add a form fot comment setting on the additional setting of module
		 * @param string $obj
		 * @return string
		 */
		/*public function ob_get_editor($vars=array()) {
			$vars = array_merge(array(
				'input_id' => '',
				'board' => '',
				'editor_uid' => '',
				'content' => '',
				'editor_height' => '',
				'textarea_rows' => ''
			), $vars);
			$vars = apply_filters('kboard_comments_content_editor_vars', $vars);
			extract($vars, EXTR_SKIP);
			// var_dump(\X2board\Includes\Classes\Context::getAll4Skin());
			$o_grant = \X2board\Includes\Classes\Context::get('grant');

			ob_start();
			if(false) { // $board->use_editor == 'yes') {
				wp_editor($content, $editor_uid, array('media_buttons'=>$o_grant->is_admin, 'textarea_name'=>$input_id, 'editor_height'=>$editor_height));  //  'editor_class'=>'comment-textarea'
			}
			else {
				echo '<textarea class="comment-textarea" cols="50" rows="'.$textarea_rows.'" style="overflow: hidden; min-height: 4em; height: 46px; width: 100%;" name="'.$input_id.'" placeholder="'.__('Add a comment', 'kboard-comments').'..." required>'.esc_textarea($content).'</textarea>';
			}
			unset($o_grant);

			$s_editor = ob_get_clean();
			return apply_filters('x2board_comment_editor', $s_editor);
		}*/
	}
}
/* End of file comment.view.php */