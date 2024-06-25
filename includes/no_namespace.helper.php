<?php
/**
 * The skin functionality of the plugin.
 *
 * @author  https://singleview.co.kr/
 * @version 0.0.1
 */
function x2b_get_url() {  // this function is same with func.inc.php::get_url()
    $n_num_args = func_num_args();
	$a_args_list = func_get_args();
	if($n_num_args) {
		$s_url = \X2board\Includes\Classes\Context::get_url($n_num_args, $a_args_list);
	}	
	else{ 
		$s_url = get_permalink();  // WP method
	}
	return esc_url(preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $s_url));
}

function x2b_write_post_input_fields() {
	$o_board_view = \X2board\Includes\getView('board');
	$o_board_view->write_post_hidden_fields();
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

function x2b_write_comment_hidden_fields_embeded_editor() {
	$o_board_view = \X2board\Includes\getView('board');
	$o_board_view->write_comment_hidden_fields(true);
	unset($o_board_view);
}

function x2b_write_comment_editor() {
	$o_editor_view = \X2board\Includes\getView('editor');
	$o_editor_view->get_comment_editor_html();
	unset($o_editor_view);
}

function x2b_write_comment_filebox() {
	$o_editor_view = \X2board\Includes\getView('editor');
	$o_comment = \X2board\Includes\Classes\Context::get('o_the_comment');
	if( $o_comment ) {
		$a_appended_file = $o_comment->get_uploaded_files();
	}
	else {
		$a_appended_file = array();
	}
	echo $o_editor_view->get_attach_ux_html($a_appended_file);
	unset($o_comment);
	unset($o_editor_view);
}

function x2b_zdate($str, $format) {
	return \X2board\Includes\zdate($str, $format);
}

function x2b_get_time_gap($date, $format = 'Y.m.d') {
	return \X2board\Includes\getTimeGap($date, $format);
}