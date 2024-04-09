<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */
/**
 * High class of the category module
 * @author singleview.co.kr
 */
namespace X2board\Includes\Modules\Editor;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Editor\\editor')) {
	
	class editor extends \X2board\Includes\Classes\ModuleObject {
		function __construct() {}
	}
}
/* End of file editor.class.php */