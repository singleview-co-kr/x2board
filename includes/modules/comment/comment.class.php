<?php
/**
 * comment
 * comment module's high class
 *
 * @author singleview.co.kr
 * @package /modules/comment
 * @version 0.1
 */
namespace X2board\Includes\Modules\Comment;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

require_once X2B_PATH . 'includes/modules/comment/comment.item.php';

if (!class_exists('\\X2board\\Includes\\Modules\\Comment\\comment')) {

	class comment extends \X2board\Includes\Classes\ModuleObject {

		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() {
var_dump('comment high class __construct');
			if(!isset($_SESSION['x2b_own_comment'])) {
				$_SESSION['x2b_own_comment'] = array();
			}
			if(!isset($_SESSION['x2b_accessibled_comment'])) {
				$_SESSION['x2b_accessibled_comment'] = array();
			}
		}
	}
}
/* End of file comment.class.php */