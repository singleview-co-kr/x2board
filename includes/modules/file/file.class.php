<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * High class of the file module
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\File\\file')) {
	
	class file extends \X2board\Includes\Classes\ModuleObject {
		function __construct() {}
	}
}
/* End of file file.class.php */