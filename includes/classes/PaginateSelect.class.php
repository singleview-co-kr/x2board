<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * - This was DB parent class
 * - customize class for paginated select only
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /classes/db
 * @version 0.1
 */
namespace X2board\Includes\Classes;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Classes\\PaginateSelect')) {

	class PaginateSelect {

		/**
		 * valid query type
		 * @var array
		 */
		private $_a_query_type = array( 'SELECT' );  //, 'INSERT', 'UPDATE', 'DELETE' );

		/**
		 * count cache path
		 * @var string
		 */
		// var $count_cache_path = 'files/cache/db';

		/**
		 * error code (0 means no error)
		 * @var int
		 */
		// var $errno = 0;

		/**
		 * error message
		 * @var string
		 */
		// var $errstr = '';

		/**
		 * query string of latest executed query
		 * @var string
		 */
		var $query = '';

		/**
		 * elapsed time of latest executed query
		 * @var int
		 */
		var $elapsed_time = 0;

		/**
		 * elapsed time of latest executed DB class
		 * @var int
		 */
		var $elapsed_dbclass_time = 0;

		/**
		 * returns instance of certain db type
		 * @param string $db_type type of db
		 * @return DB return DB object instance
		 */
		public static function getInstance() { // $db_type = NULL)
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['__DB__'])) {
				$G_X2B_CACHE['__DB__'] = self::create();
			}
			if(!isset($G_X2B_CACHE['__dbclass_elapsed_time__'])) {
				$G_X2B_CACHE['__dbclass_elapsed_time__'] = null;
			}
			if(!isset($G_X2B_CACHE['__db_queries__'])) {
				$G_X2B_CACHE['__db_queries__'] = array();
			}
			return $G_X2B_CACHE['__DB__'];
		}

		/**
		 * returns instance of db
		 * @return DB return DB object instance
		 */
		public static function create() {
			return new PaginateSelect;
		}

		/**
		 * constructor
		 * @return void
		 */
		public function __construct() {
			// $this->count_cache_path = _XE_PATH_ . $this->count_cache_path;
			// $this->cache_file = _XE_PATH_ . $this->cache_file;
		}

		/**
		 * Execute Query that result of the query xml file
		 * This function finds xml file or cache file of $query_id, compiles it and then execute it
		 * @param string $query_id query id (module.queryname)
		 * @param array|object $args arguments for query
		 * @param array $arg_columns column list. if you want get specific colums from executed result, add column list to $arg_columns
		 * @return object result of query
		 */
		public function execute_query($o_query) {// $query_id, $args = NULL, $arg_columns = NULL, $type = NULL)
			// static $cache_file = array();

			// this class handles pagination select query only
			$o_query->s_query_type = 'SELECT';
			if( isset( $o_query->s_query_type ) ){
				$o_query->s_query_type = strtoupper($o_query->s_query_type);
			}

			$this->_start_chronometry();

			// if(!isset($cache_file[$query_id]) || !file_exists($cache_file[$query_id]))
			// {
			// 	$id_args = explode('.', $query_id);
			// 	if(count($id_args) == 2)
			// 	{
			// 		$target = 'modules';
			// 		$module = $id_args[0];
			// 		$id = $id_args[1];
			// 	}
			// 	elseif(count($id_args) == 3)
			// 	{
			// 		$target = $id_args[0];
			// 		$typeList = array('addons' => 1, 'widgets' => 1);
			// 		if(!isset($typeList[$target]))
			// 		{
			// 			$this->actDBClassFinish();
			// 			return;
			// 		}
			// 		$module = $id_args[1];
			// 		$id = $id_args[2];
			// 	}
			// 	if(!$target || !$module || !$id)
			// 	{
			// 		$this->actDBClassFinish();
			// 		return new BaseObject(-1, 'msg_invalid_queryid');
			// 	}

			// 	$xml_file = sprintf('%s%s/%s/queries/%s.xml', _XE_PATH_, $target, $module, $id);
			// 	if(!file_exists($xml_file))
			// 	{
			// 		$this->actDBClassFinish();
			// 		return new BaseObject(-1, 'msg_invalid_queryid');
			// 	}

			// 	// look for cache file
			// 	$cache_file[$query_id] = $this->checkQueryCacheFile($query_id, $xml_file);
			// }
			// execute query
			$output = $this->_execute_query($o_query); //$cache_file[$query_id], $args, $query_id, $arg_columns, $type);
			$this->_finish_chronometry();
			return $output;
		}

		/**
		 * Execute query and return the result
		 * @param string $cache_file cache file of query
		 * @param array|object $source_args arguments for query
		 * @param string $query_id query id
		 * @param array $arg_columns column list. if you want get specific colums from executed result, add column list to $arg_columns
		 * @return object result of query
		 */
		function _execute_query($o_query) {// $cache_file, $source_args, $query_id, $arg_columns, $type)
			// if(!file_exists($cache_file))
			// {
			// 	return new BaseObject(-1, 'msg_invalid_queryid');
			// }

			// $output = include($cache_file);

			// if((is_a($output, 'BaseObject') || is_subclass_of($output, 'BaseObject')) && !$output->toBool()) {
			// 	return $output;
			// }
			
			if(in_array($o_query->s_query_type, $this->_a_query_type)) {
				// execute select query only
				switch($o_query->s_query_type) {
					// case 'INSERT' :
					// 	$output = $this->_executeInsertAct($o_query);
					// 	break;
					// case 'UPDATE' :
					// 	$output = $this->_executeUpdateAct($o_query);
					// 	break;
					// case 'DELETE' :
					// 	$output = $this->_executeDeleteAct($o_query);
					// 	break;
					case 'SELECT' :  // this class handles pagination select query only
					default:
						$output = $this->_execute_select_act($o_query);
						break;
				}
			}		

			if(!is_a($output, '\X2board\Includes\Classes\BaseObject') && !is_subclass_of($output, '\X2board\Includes\Classes\BaseObject')) {
				$output = new \X2board\Includes\Classes\BaseObject();
			}
			$output->add('_query', $this->query);
			$output->add('_elapsed_time', sprintf("%0.5f", $this->elapsed_time));
			return $output;
		}

		/**
		 * Handle selectAct
		 * In order to get a list of pages easily when selecting \n
		 * it supports a method as navigation
		 * @param BaseObject $queryObject
		 * @param resource $connection
		 * @param boolean $with_values
		 * @return BaseObject
		 */
		function _execute_select_act($queryObject) { // , $connection = null, $with_values = true)
// var_dump($queryObject);
			if( !isset( $queryObject->s_columns ) ) {
				wp_die('invalid columns');
			}
			if( !isset( $queryObject->page ) ) {
				$queryObject->page = 1;
			}
			if( !isset( $queryObject->s_where ) ) {
				$queryObject->s_where = null;
			}
			if( !isset( $queryObject->s_orderby ) ) {
				$queryObject->s_orderby = null;
			}
			if( !isset( $queryObject->s_groupby ) ) {
				$queryObject->s_groupby = null;
			}
			if( !isset( $queryObject->list_count ) ) {
				$queryObject->list_count = null;
			}
			if( !isset( $queryObject->page_count ) ) {
				$queryObject->page_count = null;
			}

			// if( isset( $queryObject->page ) ) {
			// 	$n_star_pos = $queryObject->page * $queryObject->list_per_page;
			// 	$s_limit = "LIMIT ".$n_star_pos.", ".$queryObject->list_per_page;
			// }
			// else {
			// 	$s_limit = null;
			// }

			// $a_result = NULL;  // $result = NULL;
			// $limit = $queryObject->getLimit();
			// if($limit && $limit->isPageHandler())
			if( isset( $queryObject->page ) ) {
				return $this->_query_page_limit($queryObject);  // $connection, $a_result, $with_values
			}
			else {  // list query without pagination
				wp_die('pagination query has been executed without page param!');
			}
		}

		/**
		 * If select query execute, return page info
		 * @param BaseObject $queryObject
		 * @param resource $result
		 * @param resource $connection
		 * @param boolean $with_values
		 * @return BaseObject BaseObject with page info containing
		 */
		// private function _queryPageLimit($queryObject) { // $connection, $result, $with_values = true
		private function _query_page_limit($queryObject) { // $connection, $result, $with_values = true
			global $wpdb;
			$this->query = "SELECT COUNT(*) as `rec_cnt` FROM {$queryObject->s_tables} {$queryObject->s_where} {$queryObject->s_groupby}";
			$o_rec_cnt = $wpdb->get_row($this->query);
			$wpdb->flush();
			if($o_rec_cnt === null) {
				$n_rec_cnt = 0;
				$total_count = 0;
			}
			else {
				$n_rec_cnt = intval($o_rec_cnt->rec_cnt);
				$total_count = intval(isset($o_rec_cnt->rec_cnt) ? $o_rec_cnt->rec_cnt : NULL);
			}
			unset($o_rec_cnt);

			$list_count = $queryObject->list_count; // $limit->list_count->getValue();
			if(!$list_count) {
				$list_count = 20;
			}
			$page_count = $queryObject->page_count; // $limit->page_count->getValue();
			if(!$page_count) {
				$page_count = 10;
			}
			$page = $queryObject->page; // $limit->page->getValue();
			if(!$page || $page < 1) {
				$page = 1;
			}
			// total pages
			if($total_count) {
				$total_page = (int) (($total_count - 1) / $list_count) + 1;
			}
			else {
				$total_page = 1;
			}

			// check the page variables
			if($page > $total_page) {  // If requested page is bigger than total number of pages, return empty list
				$buff = new BaseObject();
				$buff->total_count = $total_count;
				$buff->total_page = $total_page;
				$buff->page = $page;
				$buff->data = array();
				$buff->page_navigation = new PageHandler($total_count, $total_page, $page, $page_count);
				return $buff;
			}
			$start_count = ($page - 1) * $list_count;
			$s_limit = "LIMIT ".$start_count.", ".$list_count;

			$this->query = "SELECT {$queryObject->s_columns} FROM {$queryObject->s_tables} {$queryObject->s_where} {$queryObject->s_groupby} {$queryObject->s_orderby} {$s_limit}";
			if ($wpdb->query($this->query) === FALSE) {
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
			} 
			else {
				$a_result = $wpdb->get_results($this->query);
				$wpdb->flush();
			}

			$virtual_no = $total_count - ($page - 1) * $list_count;
			$data = $this->_fetch($a_result, $virtual_no);
			$o_buff = new BaseObject();
			$o_buff->total_count = $total_count;
			$o_buff->total_page = $total_page;
			$o_buff->page = $page;
			$o_buff->data = $data;
			$o_buff->page_navigation = new PageHandler($total_count, $total_page, $page, $page_count);
			return $o_buff;
		}

		/**
		 * Fetch the result
		 * @param resource $result
		 * @param int|NULL $arrayIndexEndValue
		 * @return array
		 */
		private function _fetch($a_result, $arrayIndexEndValue = NULL) {
			$output = array();
			foreach($a_result as $_ => $tmp) {
				if($arrayIndexEndValue) {
					$output[$arrayIndexEndValue--] = $tmp;
				}
				else {
					$output[] = $tmp;
				}
			}
			return $output;
		}

		/**
		 * Start recording DBClass log
		 * @return void
		 * _actDBClassStart
		 */
		private function _start_chronometry() {
			// $this->setError(0, 'success');
			$this->act_dbclass_start = \X2board\Includes\getMicroTime();
			$this->elapsed_dbclass_time = 0;
		}

		/**
		 * Finish recording DBClass log
		 * _actDBClassFinish
		 * @return void
		 */
		private function _finish_chronometry() {
			if(!$this->query) {
				return;
			}
			
			global $G_X2B_CACHE;
			$this->act_dbclass_finish = \X2board\Includes\getMicroTime();
			$elapsed_dbclass_time = $this->act_dbclass_finish - $this->act_dbclass_start;
			$this->elapsed_dbclass_time = $elapsed_dbclass_time;
			$G_X2B_CACHE['__dbclass_elapsed_time__'] += $elapsed_dbclass_time;
			$G_X2B_CACHE['__db_queries__'] = $this->query;
		}
	}
}
/* End of file DB.class.php */