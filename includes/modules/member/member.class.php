<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class  member
 * @author XEHub (developers@xpressengine.com)
 * high class of the member module
 */
namespace X2board\Includes\Modules\Member;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Member\\member' ) ) {

	class member extends \X2board\Includes\Classes\ModuleObject {
		/**
		 * constructor
		 *
		 * @return void
		 */
		function __construct() {}
	}
}
