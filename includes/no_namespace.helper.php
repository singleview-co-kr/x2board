<?php
/**
 * The skin functionality of the plugin.
 *
 * @link  https://singleview.co.kr/
 * @since 2.6.0
 *
 * @package    x2board
 * @subpackage no_namespace.helper
 */

function x2b_include_skin($s_skin_filename) {
	$o_board_view = \X2board\Includes\getView('board');
	echo $o_board_view->render_skin_file($s_skin_filename);
	unset($o_board_view);
}

function x2b_get_url() {  // this function is same with func.inc.php::get_url()
    $n_num_args = func_num_args();
	$a_args_list = func_get_args();
	if($n_num_args) {
		$s_url = \X2board\Includes\Classes\Context::get_url($n_num_args, $a_args_list);
	}	
	else{ 
		$s_url = get_permalink();  // WP method
	}
	return preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $s_url);
}

function x2b_write_post_input_fields() {
	$o_board_view = \X2board\Includes\getView('board');
	$o_board_view->write_post_hidden_fields();
	$o_board_view->write_post_prepare_single_user_field();
	$a_field = \X2board\Includes\Classes\Context::get('field');
	if($a_field) {
		foreach( $a_field as $o_user_define_field ){
			echo $o_user_define_field->getFormHTML();
		}
	}
	unset($a_field);
	unset($o_board_view);
}

function x2b_write_comment_hidden_fields() {
	$o_board_view = \X2board\Includes\getView('board');
	$o_board_view->write_comment_hidden_fields();
	unset($o_board_view);
}

function x2b_write_comment_editor() {
	$o_editor_view = \X2board\Includes\getView('editor');
	$o_editor_view->get_comment_editor_html();
	unset($o_editor_view);
}

/* function x2b_is_manager() {
	if(is_user_logged_in() ) {
		$o_grant = \X2board\Includes\Classes\Context::get('grant');
		if( $o_grant->manager ){
			unset($o_grant);
			return true;
		}
		unset($o_grant);
		return false;
	}
	return false;
}*/