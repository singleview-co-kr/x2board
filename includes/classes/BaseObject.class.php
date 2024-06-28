<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * Every modules inherits from BaseObject class. It includes error, message, and other variables for communicatin purpose.
 *
 * @author XEHub (developers@xpressengine.com)
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Classes\\BaseObject' ) ) {

	class BaseObject {
		/**
		 * Error code. If `0`, it is not an error.
		 *
		 * @var int
		 */
		var $error = 0;

		/**
		 * Error message. If `success`, it is not an error.
		 *
		 * @var string
		 */
		var $message = 'success';

		/**
		 * An additional variable
		 *
		 * @var array
		 */
		var $variables = array();

		/**
		 * Constructor
		 *
		 * @param int    $error Error code
		 * @param string $message Error message
		 * @return void
		 */
		public function __construct( $error = 0, $message = 'success' ) {
			$this->setError( $error );
			$this->setMessage( $message );
		}

		/**
		 * Setter to set error code
		 *
		 * @param int $error error code
		 * @return void
		 */
		public function setError( $error = 0 ) {
			$this->error = $error;
		}

		/**
		 * Getter to retrieve error code
		 *
		 * @return int Returns an error code
		 */
		public function getError() {
			return $this->error;
		}

		/**
		 * Setter to set set the error message
		 *
		 * @param string $message Error message
		 * @return bool Alaways returns true.
		 */
		public function setMessage( $message = 'success', $type = null ) {
			$this->message = $message;
			// TODO This method always returns True. We'd better remove it
			return true;
		}

		/**
		 * Getter to retrieve an error message
		 *
		 * @return string Returns message
		 */
		public function getMessage() {
			return $this->message;
		}

		/**
		 * Setter to set a key/value pair as an additional variable
		 *
		 * @param string $key A variable name
		 * @param mixed $val A value for the variable
		 * @return void
		 */
		// public function __set($key, $val) {
		// $this->variables[$key] = $val;
		// }

		/**
		 * Setter to set a key/value pair as an additional variable
		 *
		 * @param string $key A variable name
		 * @param mixed  $val A value for the variable
		 * @return void
		 */
		public function add( $key, $val ) {
			$this->variables[ $key ] = $val;
		}

		/**
		 * Method to set multiple key/value pairs as an additional variables
		 *
		 * @param BaseObject|array $object Either object or array containg key/value pairs to be added
		 * @return void
		 */
		public function adds( $object ) {
			if ( is_object( $object ) ) {
				$object = get_object_vars( $object );
			}

			if ( is_array( $object ) ) {
				foreach ( $object as $key => $val ) {
					$this->variables[ $key ] = $val;
				}
			}
		}

		/**
		 * Method to retrieve a corresponding value to a given key
		 *
		 * @param string $key
		 * @return string Returns value to a given key
		 */
		public function __get( $key ) {
			return $this->get( $key );
		}

		/**
		 * Method to retrieve a corresponding value to a given key
		 *
		 * @param string $key
		 * @return string Returns value to a given key
		 */
		public function get( $key ) {
			if ( isset( $this->variables[ $key ] ) ) {
				return $this->variables[ $key ];
			}
			return null;
		}

		/**
		 * Method to retrieve an object containing a key/value pairs
		 *
		 * @return BaseObject Returns an object containing key/value pairs
		 */
		public function gets() {
			$args   = func_get_args();
			$output = new \stdClass();
			foreach ( $args as $arg ) {
				$output->{$arg} = $this->get( $arg );
			}
			return $output;
		}

		/**
		 * Method to retrieve an array of key/value pairs
		 *
		 * @return array
		 */
		public function getVariables() {
			return $this->variables;
		}

		/**
		 * Method to return either true or false depnding on the value in a 'error' variable
		 *
		 * @return bool Retruns true : error isn't 0 or false : otherwise.
		 */
		function to_bool() {
			// TODO This method is misleading in that it returns true if error is 0, which should be true in boolean representation.
			return ( $this->error == 0 );
		}
	}
}

if ( version_compare( PHP_VERSION, '7.2', '<' ) && ! class_exists( 'Object', false ) ) {
	class_alias( 'BaseObject', 'Object' );
}
/* End of file BaseObject.class.php */
