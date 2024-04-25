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
		$s_url = \X2board\Includes\Classes\Context::get_request_uri();
	}
	return preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $s_url);
}

function x2b_write_post_input_fields() {
	$o_board_view = \X2board\Includes\getView('board');
	$o_board_view->write_post_hidden_fields();
	$o_board_view->write_post_prepare_single_user_field();
	$a_field = \X2board\Includes\Classes\Context::get('field');
// var_dump($a_field);
	if($a_field) {
		foreach( $a_field as $a_field_info ){
			$o_board_view->write_post_single_user_field($a_field_info);
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

function x2b_is_this_accessible($permission = null, $roles = null) {
	$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
	if($o_logged_info->is_admin == 'Y') {  // allow everything to an admin
		unset($o_logged_info);
		return true;
	}
	$o_grant = \X2board\Includes\Classes\Context::get('grant');
	if( $o_grant->manager ) {  // allow everything to a manager
		unset($o_grant);
		return true;
	}
	unset($o_grant);
	switch($permission) {
		case 'all': 
			return true;
		case 'author': 
			return is_user_logged_in() ? true : false;
		case 'roles':
			if(is_user_logged_in()) {
				if(array_intersect($roles, (array)$o_logged_info->roles)){
					unset($o_logged_info);
					return true;
				}
			}
			unset($o_logged_info);
			return false;
		default: 
			unset($o_logged_info);
			return true;
	}
}

function x2b_get_post_category_list() {
	return \X2board\Includes\Classes\Context::get('category_list');
}