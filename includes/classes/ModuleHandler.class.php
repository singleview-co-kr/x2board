<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class ModuleHandler
 * @author XEHub (developers@xpressengine.com)
 * Handling modules
 *
 * @remarks This class is to excute actions of modules.
 *          Constructing an instance without any parameterconstructor, it finds the target module based on Context.
 *          If there is no act on the found module, excute an action referencing action_forward.
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\X2board\\Includes\\Classes\\ModuleHandler' ) ) {

	class ModuleHandler {
		// extends Handler  -> blank abc
		var $module         = null; // < Module
		var $post_id        = null; // < WP post id
		var $module_info    = null; // < Module Info. Object
		var $error          = null; // < an error code.
		var $httpStatusCode = null; // < http status code.

		/**
		 * prepares variables to use in moduleHandler
		 *
		 * @param string $module name of module
		 * @param string $act name of action
		 * @param int    $mid
		 * @param int    $document_srl
		 * @param int    $module_srl
		 * @return void
		 * */
		function __construct( $module = '', $act = '', $mid = '', $post_id = '' ) {
			$o_context = Context::getInstance();
			if ( $o_context->isSuccessInit == false ) {
				// @see https://github.com/xpressengine/xe-core/issues/2304
				$this->error = 'msg_invalid_request';
				return;
			}
			unset( $o_context );
			$this->post_id = $post_id ? intval( $post_id ) : intval( Context::get( 'post_id' ) );
		}

		/**
		 * module auto loader
		 *
		 * @remarks if there exists a module instance created before, returns it.
		 * */
		public static function auto_load_modules() {
			$a_valid_types      = array( 'view', 'controller', 'model', 'class' );
			$s_modules_path_abs = X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'modules';

			$a_requested_modules = array();
			$a_modules           = \X2board\Includes\Classes\FileHandler::read_dir( $s_modules_path_abs );
			foreach ( $a_modules as $_ => $s_module_name ) {
				$s_single_module_path_abs = $s_modules_path_abs . DIRECTORY_SEPARATOR . $s_module_name;
				if ( ! is_dir( $s_single_module_path_abs ) ) {
					continue;
				}

				if ( ! isset( $a_requested_modules[ $s_module_name ] ) ) {
					$a_requested_modules[ $s_module_name ] = array();
				}

				$a_module_files = \X2board\Includes\Classes\FileHandler::read_dir( $s_single_module_path_abs );
				foreach ( $a_module_files as $__ => $s_module_file ) {
					$s_single_file_path_abs = $s_single_module_path_abs . DIRECTORY_SEPARATOR . $s_module_file;
					if ( is_dir( $s_single_file_path_abs ) ) {
						continue;
					}
					$a_file_info = explode( '.', basename( $s_module_file ) );

					if ( $a_file_info[1] == 'admin' ) {  // do not automatically load admin modules
						continue;
					}

					$s_module_type = $a_file_info[1];
					if ( in_array( $a_file_info[1], $a_valid_types ) ) {
						$a_requested_modules[ $s_module_name ][ $s_module_type ] = $s_module_file;
					}
				}
			}
			unset( $a_modules );

			// validate module components
			$a_valid_modules = array();
			foreach ( $a_requested_modules as $s_module_name => $a_module_info ) {
				if ( isset( $a_module_info['class'] ) ) {
					$a_valid_modules[ $s_module_name ] = $a_module_info;
				} else {
					error_log( X2B_DOMAIN . ' Warning! ' . $s_modules_path_abs . DIRECTORY_SEPARATOR . $s_module_name . DIRECTORY_SEPARATOR . $s_module_name . '.class.php is required.' );
				}
			}
			unset( $a_requested_modules );

			// load valid modules
			foreach ( $a_valid_modules as $s_module_name => $a_module_info ) {
				foreach ( $a_module_info as $s_module_type => $s_module_file ) {
					require_once $s_modules_path_abs . DIRECTORY_SEPARATOR . $s_module_name . DIRECTORY_SEPARATOR . $s_module_file;
				}
			}
			unset( $a_valid_modules );
		}

		/**
		 * It creates a module instance
		 *
		 * @param string $module module name
		 * @param string $type instance type, (e.g., view, controller, model)
		 * @param string $kind admin or svc
		 * @return ModuleObject module instance (if failed it returns null)
		 * @remarks if there exists a module instance created before, returns it.
		 * */
		public static function &get_module_instance( $s_module_name, $type = 'view' ) {
			global $G_X2B_CACHE;
			if ( __DEBUG__ == 3 ) {
				$start_time = \X2board\Includes\get_micro_time();
			}

			$parent_module = $s_module_name;
			$type          = strtolower( $type );
			$kind          = 'svc';  // no admin feature allowed
			$key           = $s_module_name . '.' . ( $kind != 'admin' ? '' : 'admin' ) . '.' . $type;

			$extend_module = null;
			if ( is_array( $G_X2B_CACHE['__MODULE_EXTEND__'] ) && array_key_exists( $key, $G_X2B_CACHE['__MODULE_EXTEND__'] ) ) {
				$s_module_name = $extend_module = $G_X2B_CACHE['__MODULE_EXTEND__'][ $key ];
			}

			// if there is no instance of the module in global variable, create a new one
			if ( ! isset( $G_X2B_CACHE['_loaded_module'][ $s_module_name ][ $type ][ $kind ] ) ) {
				self::_get_module_filepath( $s_module_name, $type, $kind, $class_path, $high_class_file, $class_file, $instance_name );

				if ( $extend_module && ( ! is_readable( $high_class_file ) || ! is_readable( $class_file ) ) ) {
					$s_module_name = $parent_module;
					self::_get_module_filepath( $s_module_name, $type, $kind, $class_path, $high_class_file, $class_file, $instance_name );
				}
				// Check if the base class and instance class exist
				if ( ! class_exists( '\\X2board\\Includes\\Modules\\' . ucfirst( $s_module_name ) . '\\' . $s_module_name, true ) ) {
					return null;
				}

				$s_instance_in_namespace = '\\X2board\\Includes\\Modules\\' . ucfirst( $s_module_name ) . '\\' . $instance_name;
				if ( ! class_exists( $s_instance_in_namespace, true ) ) {
					return null;
				}
				// Create an instance
				$oModule = new $s_instance_in_namespace();
				if ( ! is_object( $oModule ) ) {
					return null;
				}

				// Set variables to the instance
				$oModule->set_module( $s_module_name );
				$class_path = self::_get_real_path( $class_path );
				$oModule->set_module_path( $class_path );

				// If the module has a constructor, run it.
				if ( ! isset( $G_X2B_CACHE['_called_constructor'][ $instance_name ] ) ) {
					$G_X2B_CACHE['_called_constructor'][ $instance_name ] = true;
					if ( @method_exists( $oModule, $instance_name ) ) {
						$oModule->{$instance_name}();
					}
				}

				// Store the created instance into GLOBALS variable
				$G_X2B_CACHE['_loaded_module'][ $s_module_name ][ $type ][ $kind ] = $oModule;
			}

			if ( __DEBUG__ == 3 ) {
				$G_X2B_CACHE['__elapsed_class_load__'] += \X2board\Includes\get_micro_time() - $start_time;
			}
			// return the instance
			return $G_X2B_CACHE['_loaded_module'][ $s_module_name ][ $type ][ $kind ];
		}

		/**
		 * @remarks
		 * */
		private static function _get_module_filepath( $module, $type, $kind, &$classPath, &$highClassFile, &$classFile, &$instanceName ) {
			$classPath     = self::_get_module_path( $module );
			$highClassFile = sprintf( '%s%s.class.php', $classPath, $module );
			$highClassFile = self::_get_real_path( $highClassFile );

			$types = array( 'view', 'controller', 'model', 'class' );  // 'api','wap','mobile',
			if ( ! in_array( $type, $types ) ) {
				$type = $types[0];
			}
			if ( $type == 'class' ) {
				$instanceName = '%s';
				$classFile    = '%s%s.%s.php';
			} else {
				$instanceName = '%s%s';
				$classFile    = '%s%s.%s.php';
			}
			$instanceName = sprintf( $instanceName, $module, ucfirst( $type ) );
			$classFile    = self::_get_real_path( sprintf( $classFile, $classPath, $module, $type ) );
		}

		/**
		 * Changes path of target file, directory into absolute path
		 *
		 * @param string $source path to change into absolute path
		 * @return string Absolute path
		 */
		private static function _get_real_path( $source ) {
			if ( strlen( $source ) >= 2 && substr_compare( $source, './', 0, 2 ) === 0 ) {
				return X2B_PATH . substr( $source, 2 );
			}
			return $source;
		}

		/**
		 * returns module's path
		 *
		 * @param string $module module name
		 * @return string path of the module
		 * */
		private static function _get_module_path( $module ) {
			return './includes/modules/' . $module . '/';
		}

		/**
		 * get http status message by http status code
		 *
		 * @param string $code
		 * @return string
		 * */
		/*
		function _setHttpStatusMessage($code)
		{
			$statusMessageList = array(
				// 1×× Informational
				'100' => 'Continue',
				'101' => 'Switching Protocols',
				'102' => 'Processing',
				// 2×× Success
				'200' => 'OK',
				'201' => 'Created',
				'202' => 'Accepted',
				'203' => 'Non-authoritative Information',
				'204' => 'No Content',
				'205' => 'Reset Content',
				'206' => 'Partial Content',
				'207' => 'Multi-Status',
				'208' => 'Already Reported',
				'226' => 'IM Used',
				// 3×× Redirection
				'300' => 'Multiple Choices',
				'301' => 'Moved Permanently',
				'302' => 'Found',
				'303' => 'See Other',
				'304' => 'Not Modified',
				'305' => 'Use Proxy',
				'307' => 'Temporary Redirect',
				'308' => 'Permanent Redirect',
				// 4×× Client Error
				'400' => 'Bad Request',
				'401' => 'Unauthorized',
				'402' => 'Payment Required',
				'403' => 'Forbidden',
				'404' => 'Not Found',
				'405' => 'Method Not Allowed',
				'406' => 'Not Acceptable',
				'407' => 'Proxy Authentication Required',
				'408' => 'Request Timeout',
				'409' => 'Conflict',
				'410' => 'Gone',
				'411' => 'Length Required',
				'412' => 'Precondition Failed',
				'413' => 'Payload Too Large',
				'414' => 'Request-URI Too Long',
				'415' => 'Unsupported Media Type',
				'416' => 'Requested Range Not Satisfiable',
				'417' => 'Expectation Failed',
				'418' => 'I\'m a teapot',
				'421' => 'Misdirected Request',
				'422' => 'Unprocessable Entity',
				'423' => 'Locked',
				'424' => 'Failed Dependency',
				'426' => 'Upgrade Required',
				'428' => 'Precondition Required',
				'429' => 'Too Many Requests',
				'431' => 'Request Header Fields Too Large',
				'451' => 'Unavailable For Legal Reasons',
				// 5×× Server Error
				'500' => 'Internal Server Error',
				'501' => 'Not Implemented',
				'502' => 'Bad Gateway',
				'503' => 'Service Unavailable',
				'504' => 'Gateway Timeout',
				'505' => 'HTTP Version Not Supported',
				'506' => 'Variant Also Negotiates',
				'507' => 'Insufficient Storage',
				'508' => 'Loop Detected',
				'510' => 'Not Extended',
				'511' => 'Network Authentication Required',
			);
			$statusMessage = $statusMessageList[$code];
			if(!$statusMessage)
			{
				$statusMessage = 'HTTP ' . $code;
			}

			Context::set('http_status_code', $code);
			Context::set('http_status_message', $statusMessage);
		}*/
	}
}
/* End of file ModuleHandler.class.php */
