<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * High class of the file module
 *
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Modules\File;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\File\\file' ) ) {
	// this is for poedit recognition
	__( 'lbl_valid', X2B_DOMAIN );
	__( 'lbl_pending', X2B_DOMAIN );

	class file extends \X2board\Includes\Classes\ModuleObject {

		protected $_n_allowed_filesize     = 50;
		protected $_n_allowed_attach_size  = 50;
		protected $_s_allowed_filetypes    = '*.*';
		protected $_s_allow_outlink        = 'Y';
		protected $_s_allow_outlink_format = null;
		protected $_s_allow_outlink_site   = null;
		protected $_s_download_grant       = null;

		function __construct() {}
	}
}
