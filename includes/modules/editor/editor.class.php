<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  editor
 * @author XEHub (developers@xpressengine.com)
 * @brief high class of the editor odule 
 */
namespace X2board\Includes\Modules\Editor;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Editor\\editor')) {
	
	class editor extends \X2board\Includes\Classes\ModuleObject {
		public function __construct() {}
	}
}