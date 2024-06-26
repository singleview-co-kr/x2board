<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */
/**
 * High class of the category module
 *
 * @author singleview.co.kr
 */
namespace X2board\Includes\Modules\Category;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Category\\category' ) ) {

	class category extends \X2board\Includes\Classes\ModuleObject {
		function __construct() {}
	}
}
