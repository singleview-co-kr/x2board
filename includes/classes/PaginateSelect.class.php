<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * - This was DB parent class
 * - customize class for paginated select only
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /classes/db
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Classes\\PaginateSelect' ) ) {

	class PaginateSelect {

		// php8 compatible
		public $act_dbclass_finish;
		public $act_dbclass_start;

		/**
		 * valid query type
		 *
		 * @var array
		 */
		private $_a_query_type = array( 'SELECT' );  // , 'INSERT', 'UPDATE', 'DELETE' );

		/**
		 * error code (0 means no error)
		 *
		 * @var int
		 */
		// var $errno = 0;

		/**
		 * error message
		 *
		 * @var string
		 */
		// var $errstr = '';

		/**
		 * query string of latest executed query
		 *
		 * @var string
		 */
		var $query = '';

		/**
		 * elapsed time of latest executed query
		 *
		 * @var int
		 */
		var $elapsed_time = 0;

		/**
		 * elapsed time of latest executed DB class
		 *
		 * @var int
		 */
		var $elapsed_dbclass_time = 0;

		/**
		 * returns instance of certain db type
		 *
		 * @param string $db_type type of db
		 * @return DB return DB object instance
		 */
		public static function getInstance() {
			// $db_type = NULL)
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['__DB__'] ) ) {
				$G_X2B_CACHE['__DB__'] = self::create();
			}
			if ( ! isset( $G_X2B_CACHE['__dbclass_elapsed_time__'] ) ) {
				$G_X2B_CACHE['__dbclass_elapsed_time__'] = null;
			}
			if ( ! isset( $G_X2B_CACHE['__db_queries__'] ) ) {
				$G_X2B_CACHE['__db_queries__'] = array();
			}
			return $G_X2B_CACHE['__DB__'];
		}

		/**
		 * returns instance of db
		 *
		 * @return DB return DB object instance
		 */
		public static function create() {
			return new PaginateSelect();
		}

		/**
		 * constructor
		 *
		 * @return void
		 */
		public function __construct() { }

		/**
		 * Execute Query that result of the query xml file
		 * This function finds xml file or cache file of $query_id, compiles it and then execute it
		 *
		 * @param string       $query_id query id (module.queryname)
		 * @param array|object $args arguments for query
		 * @param array        $arg_columns column list. if you want get specific colums from executed result, add column list to $arg_columns
		 * @return object result of query
		 */
		public function execute_query( $o_query ) {
			// this class handles pagination select query only
			$o_query->s_query_type = 'SELECT';
			if ( isset( $o_query->s_query_type ) ) {
				$o_query->s_query_type = strtoupper( $o_query->s_query_type );
			}

			$this->_start_chronometry();

			// execute query
			$output = $this->_execute_query( $o_query );
			$this->_finish_chronometry();
			return $output;
		}

		/**
		 * Execute query and return the result
		 *
		 * @param string       $cache_file cache file of query
		 * @param array|object $source_args arguments for query
		 * @param string       $query_id query id
		 * @param array        $arg_columns column list. if you want get specific colums from executed result, add column list to $arg_columns
		 * @return object result of query
		 */
		function _execute_query( $o_query ) {
			if ( in_array( $o_query->s_query_type, $this->_a_query_type ) ) {
				// execute select query only
				switch ( $o_query->s_query_type ) {
					// case 'INSERT' :
					// $output = $this->_executeInsertAct($o_query);
					// break;
					// case 'UPDATE' :
					// $output = $this->_executeUpdateAct($o_query);
					// break;
					// case 'DELETE' :
					// $output = $this->_executeDeleteAct($o_query);
					// break;
					case 'SELECT':  // this class handles pagination select query only
					default:
						$output = $this->_execute_select_act( $o_query );
						break;
				}
			}

			if ( ! is_a( $output, '\X2board\Includes\Classes\BaseObject' ) && ! is_subclass_of( $output, '\X2board\Includes\Classes\BaseObject' ) ) {
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			$output->add( '_query', $this->query );
			$output->add( '_elapsed_time', sprintf( '%0.5f', $this->elapsed_time ) );
			return $output;
		}

		/**
		 * Handle selectAct
		 * In order to get a list of pages easily when selecting \n
		 * it supports a method as navigation
		 *
		 * @param BaseObject $queryObject
		 * @param resource   $connection
		 * @param boolean    $with_values
		 * @return BaseObject
		 */
		function _execute_select_act( $queryObject ) {
			if ( ! isset( $queryObject->s_columns ) ) {
				wp_die( 'invalid columns' );
			}
			if ( ! isset( $queryObject->page ) ) {
				$queryObject->page = 1;
			}
			if ( ! isset( $queryObject->s_where ) ) {
				$queryObject->s_where = null;
			}
			if ( ! isset( $queryObject->s_orderby ) ) {
				$queryObject->s_orderby = null;
			}
			if ( ! isset( $queryObject->s_groupby ) ) {
				$queryObject->s_groupby = null;
			}
			if ( ! isset( $queryObject->list_count ) ) {
				$queryObject->list_count = null;
			}
			if ( ! isset( $queryObject->page_count ) ) {
				$queryObject->page_count = null;
			}

			if ( isset( $queryObject->page ) ) {
				return $this->_query_page_limit( $queryObject );
			} else {  // list query without pagination
				wp_die( 'pagination query has been executed without page param!' );
			}
		}

		/**
		 * If select query execute, return page info
		 * _queryPageLimit($queryObject) { // $connection, $result, $with_values = true
		 *
		 * @param BaseObject $queryObject
		 * @param resource   $result
		 * @param resource   $connection
		 * @param boolean    $with_values
		 * @return BaseObject BaseObject with page info containing
		 */
		private function _query_page_limit( $queryObject ) {
			global $wpdb;
			$this->query = "SELECT COUNT(*) as `rec_cnt` FROM {$queryObject->s_tables} {$queryObject->s_where} {$queryObject->s_groupby}";
			$o_rec_cnt   = $wpdb->get_row( $this->query );
			$wpdb->flush();
			if ( $o_rec_cnt === null ) {
				$n_rec_cnt   = 0;
				$total_count = 0;
			} else {
				$n_rec_cnt   = intval( $o_rec_cnt->rec_cnt );
				$total_count = intval( isset( $o_rec_cnt->rec_cnt ) ? $o_rec_cnt->rec_cnt : null );
			}
			unset( $o_rec_cnt );

			$list_count = $queryObject->list_count;
			if ( ! $list_count ) {
				$list_count = 20;
			}
			$page_count = $queryObject->page_count;
			if ( ! $page_count ) {
				$page_count = 10;
			}
			$page = $queryObject->page;
			if ( ! $page || $page < 1 ) {
				$page = 1;
			}
			// total pages
			if ( $total_count ) {
				$total_page = (int) ( ( $total_count - 1 ) / $list_count ) + 1;
			} else {
				$total_page = 1;
			}

			// check the page variables
			if ( $page > $total_page ) {  // If requested page is bigger than total number of pages, return empty list
				$buff                  = new BaseObject();
				$buff->total_count     = $total_count;
				$buff->total_page      = $total_page;
				$buff->page            = $page;
				$buff->data            = array();
				$buff->page_navigation = new PageHandler( $total_count, $total_page, $page, $page_count );
				return $buff;
			}
			$start_count = ( $page - 1 ) * $list_count;
			$s_limit     = 'LIMIT ' . $start_count . ', ' . $list_count;

			$this->query = "SELECT {$queryObject->s_columns} FROM {$queryObject->s_tables} {$queryObject->s_where} {$queryObject->s_groupby} {$queryObject->s_orderby} {$s_limit}";
			if ( $wpdb->query( $this->query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			} else {
				$a_result = $wpdb->get_results( $this->query );
				$wpdb->flush();
			}

			$virtual_no              = $total_count - ( $page - 1 ) * $list_count;
			$data                    = $this->_fetch( $a_result, $virtual_no );
			$o_buff                  = new BaseObject();
			$o_buff->total_count     = $total_count;
			$o_buff->total_page      = $total_page;
			$o_buff->page            = $page;
			$o_buff->data            = $data;
			$o_buff->page_navigation = new PageHandler( $total_count, $total_page, $page, $page_count );
			return $o_buff;
		}

		/**
		 * Fetch the result
		 *
		 * @param resource $result
		 * @param int|NULL $arrayIndexEndValue
		 * @return array
		 */
		private function _fetch( $a_result, $arrayIndexEndValue = null ) {
			$output = array();
			foreach ( $a_result as $_ => $tmp ) {
				if ( $arrayIndexEndValue ) {
					$output[ $arrayIndexEndValue-- ] = $tmp;
				} else {
					$output[] = $tmp;
				}
			}
			return $output;
		}

		/**
		 * Start recording DBClass log
		 * _actDBClassStart()
		 *
		 * @return void
		 */
		private function _start_chronometry() {
			// $this->setError(0, 'success');
			$this->act_dbclass_start    = \X2board\Includes\get_micro_time();
			$this->elapsed_dbclass_time = 0;
		}

		/**
		 * Finish recording DBClass log
		 * _actDBClassFinish()
		 *
		 * @return void
		 */
		private function _finish_chronometry() {
			if ( ! $this->query ) {
				return;
			}

			global $G_X2B_CACHE;
			$this->act_dbclass_finish                 = \X2board\Includes\get_micro_time();
			$elapsed_dbclass_time                     = $this->act_dbclass_finish - $this->act_dbclass_start;
			$this->elapsed_dbclass_time               = $elapsed_dbclass_time;
			$G_X2B_CACHE['__dbclass_elapsed_time__'] += $elapsed_dbclass_time;
			$G_X2B_CACHE['__db_queries__']            = $this->query;
		}
	}
}
/* End of file DB.class.php */
