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
		// protected $_a_registered_components = array();
		
		public function __construct() {
// var_dump('editor high class');
			// foreach( array( "highlightjs", "emoticon", "image_link", "poll_maker", "image_gallery", 
			// 				"colorpicker_text", "colorpicker_bg", "url_link", "multimedia_link", 
			// 				"quotation", "table_maker" ) as $_ => $s_componemt_name ) {
				
			// 	$o_component = new \stdClass();
			// 	$o_component->component_name = $s_componemt_name;
			// 	$o_component->enabled = 'Y';
			// 	$o_component->extra_vars = null;
			// 	$o_component->list_order = null;
			// 	$this->_a_registered_components[] = $o_component;
			// }
		}
	}
}
/* End of file editor.class.php */