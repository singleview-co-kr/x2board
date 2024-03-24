<?php
/**
 * - DB parent class
 * - connect db with WP
 *
 * @author singleview.co.kr
 * @package /classes/db
 * @version 0.1
 */
namespace X2board\Includes\Classes;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Classes\\IpFilter')) {
	class DB {

		/**
		 * valid query type
		 * @var array
		 */
		private $_a_query_type = array( 'SELECT' );  //, 'INSERT', 'UPDATE', 'DELETE' );

		// static $isSupported = FALSE;

		/**
		 * priority of DBMS
		 * @var array
		 */
		// var $priority_dbms = array(
		// 	'mysqli' => 6,
		// 	'mysqli_innodb' => 5,
		// 	'mysql' => 4,
		// 	'mysql_innodb' => 3,
		// 	'cubrid' => 2,
		// 	'mssql' => 1
		// );

		/**
		 * count cache path
		 * @var string
		 */
		// var $count_cache_path = 'files/cache/db';

		/**
		 * operations for condition
		 * @var array
		 */
		// var $cond_operation = array(
		// 	'equal' => '=',
		// 	'more' => '>=',
		// 	'excess' => '>',
		// 	'less' => '<=',
		// 	'below' => '<',
		// 	'notequal' => '<>',
		// 	'notnull' => 'is not null',
		// 	'null' => 'is null',
		// );

		/**
		 * master database connection string
		 * @var array
		 */
		// var $master_db = NULL;

		/**
		 * array of slave databases connection strings
		 * @var array
		 */
		// var $slave_db = NULL;
		// var $result = NULL;

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
		// var $connection = '';

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
		 * transaction flag
		 * @var boolean
		 */
		// var $transaction_started = FALSE;
		// var $is_connected = FALSE;

		/**
		 * returns enable list in supported dbms list
		 * will be written by classes/DB/DB***.class.php
		 * @var array
		 */
		// static $supported_list = array();

		/**
		 * location of query cache
		 * @var string
		 */
		// var $cache_file = 'files/cache/queries/';

		/**
		 * stores database type: 'mysql','cubrid','mssql' etc. or 'db' when database is not yet set
		 * @var string
		 */
		// var $db_type;

		/**
		 * flag to decide if class prepared statements or not (when supported); can be changed from db.config.info
		 * @var string
		 */
		// var $use_prepared_statements;

		/**
		 * leve of transaction
		 * @var unknown
		 */
		// private $transactionNestedLevel = 0;

		/**
		 * returns instance of certain db type
		 * @param string $db_type type of db
		 * @return DB return DB object instance
		 */
		public static function getInstance() // $db_type = NULL)
		{
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

			// if(!$db_type)
			// {
			// 	$db_type = Context::getDBType();
			// }
			// if(!$db_type && Context::isInstalled())
			// {
			// 	return new BaseObject(-1, 'msg_db_not_setted');
			// }

			// if(!isset($GLOBALS['__DB__']))
			// {
			// 	$GLOBALS['__DB__'] = array();
			// }
			// if(!isset($GLOBALS['__DB__'][$db_type]))
			// {
			// 	$class_name = 'DB' . ucfirst($db_type);
			// 	$class_file = _XE_PATH_ . "classes/db/$class_name.class.php";
			// 	if(!file_exists($class_file))
			// 	{
			// 		return new BaseObject(-1, 'msg_db_not_setted');
			// 	}

			// 	// get a singletone instance of the database driver class
			// 	require_once($class_file);
			// 	$GLOBALS['__DB__'][$db_type] = call_user_func(array($class_name, 'create'));
			// 	$GLOBALS['__DB__'][$db_type]->db_type = $db_type;
			// }

			// return $GLOBALS['__DB__'][$db_type];
		}

		/**
		 * returns instance of db
		 * @return DB return DB object instance
		 */
		public static function create()
		{
			return new DB;
		}

		/**
		 * constructor
		 * @return void
		 */
		public function __construct()
		{
			// $this->count_cache_path = _XE_PATH_ . $this->count_cache_path;
			// $this->cache_file = _XE_PATH_ . $this->cache_file;
		}

		public function getNextSequence()
		{
			global $wpdb;
			$this->query = "INSERT INTO `{$wpdb->prefix}x2b_sequence` (seq) values ('0')";
			if ($wpdb->query($this->query) === FALSE) {
				wp_die($wpdb->last_error);
			} 		
			$sequence = $wpdb->insert_id;
			if($sequence % 10000 == 0)
			{
				$this->query = "delete from  `{$wpdb->prefix}x2b_sequence` where seq < ".$sequence;
				if ($wpdb->query($this->query) === FALSE) {
					wp_die($wpdb->last_error);
				} 
			}
			// $query = sprintf("insert into `%ssequence` (seq) values ('0')", $this->prefix);
			// $this->_query($query);
			// $sequence = $this->db_insert_id();
			// if($sequence % 10000 == 0)
			// {
			// 	$query = sprintf("delete from  `%ssequence` where seq < %d", $this->prefix, $sequence);
			// 	$this->_query($query);
			// }
			return $sequence;
		}

		/**
		 * Execute Query that result of the query xml file
		 * This function finds xml file or cache file of $query_id, compiles it and then execute it
		 * @param string $query_id query id (module.queryname)
		 * @param array|object $args arguments for query
		 * @param array $arg_columns column list. if you want get specific colums from executed result, add column list to $arg_columns
		 * @return object result of query
		 */
		function executeQuery($o_query, $arg_columns = NULL) // $query_id, $args = NULL, $arg_columns = NULL, $type = NULL)
		{
			// static $cache_file = array();

			// if(!$query_id)
			// {
			// 	return new BaseObject(-1, 'msg_invalid_queryid');
			// }
			// if(!$this->db_type)
			// {
			// 	return;
			// }

			$o_query->s_query_type = strtoupper($o_query->s_query_type);

			$this->_actDBClassStart();

			// $this->query_id = $query_id;

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
			$output = $this->_executeQuery($o_query, $arg_columns); //$cache_file[$query_id], $args, $query_id, $arg_columns, $type);
			$this->_actDBClassFinish();
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
		function _executeQuery($o_query, $arg_columns)  // $cache_file, $source_args, $query_id, $arg_columns, $type)
		{
			// global $lang;
			
			// if(!in_array($type, array('master','slave'))) $type = 'slave';

			// if(!file_exists($cache_file))
			// {
			// 	return new BaseObject(-1, 'msg_invalid_queryid');
			// }

			// if($source_args) {
			// 	$args = clone $source_args;
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
					case 'SELECT' :
						// $arg_columns = is_array($arg_columns) ? $arg_columns : array();
						// $output->setColumnList($arg_columns);
						// $connection = $this->_getConnection($type);
						$output = $this->_executeSelectAct($o_query); // , $connection);
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
		function _executeSelectAct($queryObject) { // , $connection = null, $with_values = true)
			// $o_query = $queryObject;
			if( !isset( $queryObject->s_columns ) ) {
				wp_die('invalid columns');
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

			// if( isset( $queryObject->page ) ) {
			// 	$n_star_pos = $queryObject->page * $queryObject->list_per_page;
			// 	$s_limit = "LIMIT ".$n_star_pos.", ".$queryObject->list_per_page;
			// }
			// else {
			// 	$s_limit = null;
			// }

			$a_result = NULL;  // $result = NULL;
			// $limit = $queryObject->getLimit();
			// if($limit && $limit->isPageHandler())
			if( isset( $queryObject->page ) ) {
				return $this->_queryPageLimit($queryObject, $a_result);  // $connection, $with_values
			}
			else {  // list query without pagination
				global $wpdb;
				$query = "SELECT {$queryObject->s_columns} FROM `{$wpdb->prefix}{$queryObject->s_table_name}` {$queryObject->s_where} {$queryObject->s_orderby}"; // {$s_limit}"; 
				//$query = $this->getSelectSql($queryObject, $with_values);
				// if(is_a($query, 'BaseObject'))
				// {
				// 	return;
				// }
			// 	$query .= (__DEBUG_QUERY__ & 1 && $queryObject->queryID) ? sprintf(' ' . $this->comment_syntax, $queryObject->queryID) : '';
			
				// 	$result = $this->_query($query, $connection);
				$a_result = $wpdb->get_results($query);
				// 	if($this->isError())
				// 	{
				// 		return $this->queryError($queryObject);
				// 	}

				$a_data = $this->_fetch($a_result);  // $data = $this->_fetch($result);
				unset($a_result);
				$o_buff = new \X2board\Includes\Classes\BaseObject();   // 	$buff = new BaseObject(); 
				$o_buff->data = $a_data;   // 	$buff->data = $data;

				// if($queryObject->usesClickCount())
				// {
				// 	$update_query = $this->getClickCountQuery($queryObject);
				// 	$this->_executeUpdateAct($update_query, $with_values);
				// }
				return $o_buff;
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
		private function _queryPageLimit($queryObject, $result)  // $connection, $with_values = true
		{
			// $limit = $queryObject->getLimit();
			// Total count
			// $temp_where = $queryObject->getWhereString($with_values, false);
			// $count_query = sprintf('select count(*) as "count" %s %s', 'FROM ' . $queryObject->getFromString($with_values), ($temp_where === '' ? '' : ' WHERE ' . $temp_where));

			// Check for distinct query and if found update count query structure
			/*$temp_select = $queryObject->getSelectString($with_values);
			$uses_distinct = stripos($temp_select, "distinct") !== false;
			$uses_groupby = $queryObject->getGroupByString() != '';
			if($uses_distinct || $uses_groupby)
			{
				$count_query = sprintf('select %s %s %s %s'
						, $temp_select == '*' ? '1' : $temp_select
						, 'FROM ' . $queryObject->getFromString($with_values)
						, ($temp_where === '' ? '' : ' WHERE ' . $temp_where)
						, ($uses_groupby ? ' GROUP BY ' . $queryObject->getGroupByString() : '')
				);

				// If query uses grouping or distinct, count from original select
				$count_query = sprintf('select count(*) as "count" from (%s) xet', $count_query);
			}*/

			// $count_query .= (__DEBUG_QUERY__ & 1 && $queryObject->queryID) ? sprintf(' ' . $this->comment_syntax, $queryObject->queryID) : '';
			// $result_count = $this->_query($count_query, $connection);
			// $count_output = $this->_fetch($result_count);
			// $total_count = (int) (isset($count_output->count) ? $count_output->count : NULL);
			global $wpdb;
			$this->query = "SELECT COUNT(*) as `post_cnt` FROM `{$wpdb->prefix}{$queryObject->s_table_name}` {$queryObject->s_where}";
			$o_post_cnt = $wpdb->get_row($this->query);
			if ($o_post_cnt === null) {
					return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
			} 
			else {
				$wpdb->flush();
			}
			$n_post_cnt = intval($o_post_cnt->post_cnt);
			$total_count = intval(isset($o_post_cnt->post_cnt) ? $o_post_cnt->post_cnt : NULL);

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
			$s_limit = "LIMIT ".$start_count.", ".$page_count;

			// $query = $this->getSelectPageSql($queryObject, $with_values, $start_count, $list_count);
			// $query .= (__DEBUG_QUERY__ & 1 && $queryObject->query_id) ? sprintf(' ' . $this->comment_syntax, $this->query_id) : '';
			// $result = $this->_query($query, $connection);
		
			$this->query = "SELECT {$queryObject->s_columns} FROM `{$wpdb->prefix}{$queryObject->s_table_name}` {$queryObject->s_where} {$queryObject->s_orderby} {$s_limit}";
			if ($wpdb->query($this->query) === FALSE) {
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
			} 
			else {
				$a_result = $wpdb->get_results($this->query);
				$wpdb->flush();
			}

			$virtual_no = $total_count - ($page - 1) * $list_count;
			$data = $this->_fetch($a_result, $virtual_no);
			$buff = new BaseObject();
			$buff->total_count = $total_count;
			$buff->total_page = $total_page;
			$buff->page = $page;
			$buff->data = $data;
			$buff->page_navigation = new PageHandler($total_count, $total_page, $page, $page_count);
			return $buff;
		}

		/**
		 * Fetch the result
		 * @param resource $result
		 * @param int|NULL $arrayIndexEndValue
		 * @return array
		 */
		private function _fetch($a_result, $arrayIndexEndValue = NULL)
		{
	// 		$n_post_cnt = intval($o_post_cnt->post_cnt);
	// 		$n_star_pos = $n_post_cnt - $n_star_pos;  // set guest-wise start idx of each x2b post

	// 		$a_post_list_ret = array();
	// 		foreach($a_post_list_tmp as $_ => $o_post) {
	// 			$a_post_list_ret[$n_star_pos--] = $o_post;
	// 		}
	// 		unset($a_post_list_tmp);
	// var_dump($a_post_list_ret);

			$output = array();
			// if(!$this->isConnected() || $this->isError() || !$result)
			// {
			// 	return $output;
			// }
			// while($tmp = $this->db_fetch_object($result)) {
			foreach($a_result as $_ => $tmp) {
				if($arrayIndexEndValue) {
					$output[$arrayIndexEndValue--] = $tmp;
				}
				else {
					$output[] = $tmp;
				}
			}
			// if(count($output) == 1)
			// {
			// 	if(isset($arrayIndexEndValue))
			// 	{
			// 		return $output;
			// 	}
			// 	else
			// 	{
			// 		return $output[0];
			// 	}
			// }
			// $this->db_free_result($result);
			return $output;
		}

		/**
		 * Start recording DBClass log
		 * @return void
		 */
		private function _actDBClassStart() {
			// $this->setError(0, 'success');
			$this->act_dbclass_start = \X2board\Includes\getMicroTime();
			$this->elapsed_dbclass_time = 0;
		}

		/**
		 * Finish recording DBClass log
		 * @return void
		 */
		private function _actDBClassFinish()
		{
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

	//////////////////////////////////////






		
		/**
		 * set query debug log
		 * @param array $log values set query debug
		 * @return void
		*/
		// private function _setQueryLog($log)
		// {
		// 	global $G_X2B_CACHE;
		// 	if(!isset($G_X2B_CACHE['__db_queries__'])) {
		// 		$G_X2B_CACHE['__db_queries__'] = array();
		// 	}
		// 	$G_X2B_CACHE['__db_queries__'][] = $log;
		// }

		/**
		 * start recording log
		 * @param string $query query string
		 * @return void
		 */
		// function actStart($query)
		// {
		// 	$this->setError(0, 'success');
		// 	$this->query = $query;
		// 	$this->act_start = \X2board\Includes\getMicroTime();
		// 	$this->elapsed_time = 0;
		// }

		/**
		 * finish recording log
		 * @return void
		 */
		// function actFinish()
		// {
		// 	if(!$this->query)
		// 	{
		// 		return;
		// 	}
		// 	$this->act_finish = \X2board\Includes\getMicroTime();
		// 	$elapsed_time = $this->act_finish - $this->act_start;
		// 	$this->elapsed_time = $elapsed_time;
		// 	$GLOBALS['__db_elapsed_time__'] += $elapsed_time;

		// 	$site_module_info = Context::get('site_module_info');
		// 	$log = array();
		// 	$log['query'] = $this->query;
		// 	$log['elapsed_time'] = $elapsed_time;
		// 	// $log['connection'] = $this->connection;
		// 	// $log['query_id'] = $this->query_id;
		// 	$log['page'] = $site_module_info->module;
		// 	$log['act'] = 'some_act'; // Context::get('act');
		// 	$log['time'] = date('Y-m-d H:i:s');

		// 	$bt = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();

		// 	foreach($bt as $no => $call)
		// 	{
		// 		if($call['function'] == 'executeQuery' || $call['function'] == 'executeQueryArray')
		// 		{
		// 			$call_no = $no;
		// 			$call_no++;
		// 			$log['called_file'] = $bt[$call_no]['file'].':'.$bt[$call_no]['line'];
		// 			$log['called_file'] = str_replace(_XE_PATH_ , '', $log['called_file']);
		// 			$call_no++;
		// 			$log['called_method'] = $bt[$call_no]['class'].$bt[$call_no]['type'].$bt[$call_no]['function'];
		// 			break;
		// 		}
		// 	}

		// 	// leave error log if an error occured (if __DEBUG_DB_OUTPUT__ is defined)
		// 	if($this->isError())
		// 	{
		// 		$log['result'] = 'Failed';
		// 		$log['errno'] = $this->errno;
		// 		$log['errstr'] = $this->errstr;

		// 		if(__DEBUG_DB_OUTPUT__ == 1)
		// 		{
		// 			$debug_file = _XE_PATH_ . "files/_debug_db_query.php";
		// 			$buff = array();
		// 			if(!file_exists($debug_file))
		// 			{
		// 				$buff[] = '<?php exit(); ?' . '>';
		// 			}
		// 			$buff[] = print_r($log, TRUE);
		// 			@file_put_contents($debug_file, implode("\n", $buff) . "\n\n", FILE_APPEND|LOCK_EX);
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$log['result'] = 'Success';
		// 	}

		// 	$this->_setQueryLog($log);

		// 	$log_args = new stdClass;
		// 	$log_args->query = $this->query;
		// 	$log_args->query_id = $this->query_id;
		// 	$log_args->caller = $log['called_method'] . '() in ' . $log['called_file'];
		// 	$log_args->connection = $log['connection'];
		// 	writeSlowlog('query', $elapsed_time, $log_args);
		// }

		/**
		 * set error
		 * @param int $errno error code
		 * @param string $errstr error message
		 * @return void
		 */
		// function setError($errno = 0, $errstr = 'success')
		// {
		// 	$this->errno = $errno;
		// 	$this->errstr = $errstr;
		// }

		/**
		 * Return error status
		 * @return boolean true: error, false: no error
		 */
		// function isError()
		// {
		// 	return ($this->errno !== 0);
		// }

		/**
		 * Returns object of error info
		 * @return object object of error
		 */
		// function getError()
		// {
		// 	$this->errstr = 'db class error'; //Context::convertEncodingStr($this->errstr);
		// 	return new BaseObject($this->errno, $this->errstr);
		// }

		/**
		 * Handles insertAct
		 * @param BaseObject $queryObject
		 * @param boolean $with_values
		 * @return resource
		 */
		// function _executeInsertAct($queryObject, $with_values = true) {
		// 	$query = $this->getInsertSql($queryObject, $with_values, true);
		// 	$query .= (__DEBUG_QUERY__ & 1 && $this->query_id) ? sprintf(' ' . $this->comment_syntax, $this->query_id) : '';
		// 	if(is_a($query, 'BaseObject'))
		// 	{
		// 		return;
		// 	}
		// 	return $this->_query($query);
		// }

		/**
		 * Handles updateAct
		 * @param BaseObject $queryObject
		 * @param boolean $with_values
		 * @return resource
		 */
		// function _executeUpdateAct($queryObject, $with_values = true) {
		// 	$query = $this->getUpdateSql($queryObject, $with_values, true);
		// 	if(is_a($query, 'BaseObject'))
		// 	{
		// 		if(!$query->toBool()) return $query;
		// 		else return;
		// 	}

		// 	$query .= (__DEBUG_QUERY__ & 1 && $this->query_id) ? sprintf(' ' . $this->comment_syntax, $this->query_id) : '';


		// 	return $this->_query($query);
		// }

		/**
		 * Handles deleteAct
		 * @param BaseObject $queryObject
		 * @param boolean $with_values
		 * @return resource
		 */
		// function _executeDeleteAct($queryObject, $with_values = true) {
		// 	$query = $this->getDeleteSql($queryObject, $with_values, true);
		// 	$query .= (__DEBUG_QUERY__ & 1 && $this->query_id) ? sprintf(' ' . $this->comment_syntax, $this->query_id) : '';
		// 	if(is_a($query, 'BaseObject'))
		// 	{
		// 		return;
		// 	}
		// 	return $this->_query($query);
		// }

		/**
		 * Execute the query
		 * this method is protected
		 * @param string $query
		 * @param resource $connection
		 * @return resource
		 */
		// function _query($query, $connection = NULL)
		// {
		// 	// if($connection == NULL)
		// 	// {
		// 	// 	$connection = $this->_getConnection('master');
		// 	// }
		// 	// Notify to start a query execution
		// 	$this->actStart($query);

		// 	// Run the query statement
		// 	$result = $this->__query($query, $connection);

		// 	// Notify to complete a query execution
		// 	$this->actFinish();
		// 	// Return result
		// 	return $result;
		// }

		/**
		 * Return select query string
		 * @param object $query
		 * @param boolean $with_values
		 * @return string
		 */
		// function getSelectSql($query, $with_values = TRUE)
		// {
		// 	$select = $query->getSelectString($with_values);
		// 	if($select == '')
		// 	{
		// 		return new BaseObject(-1, "Invalid query");
		// 	}
		// 	$select = 'SELECT ' . $select;

		// 	$from = $query->getFromString($with_values);
		// 	if($from == '')
		// 	{
		// 		return new BaseObject(-1, "Invalid query");
		// 	}
		// 	$from = ' FROM ' . $from;

		// 	$where = $query->getWhereString($with_values);
		// 	if($where != '')
		// 	{
		// 		$where = ' WHERE ' . $where;
		// 	}

		// 	$tableObjects = $query->getTables();
		// 	$index_hint_list = '';
		// 	foreach($tableObjects as $tableObject)
		// 	{
		// 		if(is_a($tableObject, 'CubridTableWithHint'))
		// 		{
		// 			$index_hint_list .= $tableObject->getIndexHintString() . ', ';
		// 		}
		// 	}
		// 	$index_hint_list = substr($index_hint_list, 0, -2);
		// 	if($index_hint_list != '')
		// 	{
		// 		$index_hint_list = 'USING INDEX ' . $index_hint_list;
		// 	}

		// 	$groupBy = $query->getGroupByString();
		// 	if($groupBy != '')
		// 	{
		// 		$groupBy = ' GROUP BY ' . $groupBy;
		// 	}

		// 	$orderBy = $query->getOrderByString();
		// 	if($orderBy != '')
		// 	{
		// 		$orderBy = ' ORDER BY ' . $orderBy;
		// 	}

		// 	$limit = $query->getLimitString();
		// 	if($limit != '')
		// 	{
		// 		$limit = ' LIMIT ' . $limit;
		// 	}

		// 	return $select . ' ' . $from . ' ' . $where . ' ' . $index_hint_list . ' ' . $groupBy . ' ' . $orderBy . ' ' . $limit;
		// }

		/**
		 * Return delete query string
		 * @param object $query
		 * @param boolean $with_values
		 * @param boolean $with_priority
		 * @return string
		 */
		// function getDeleteSql($query, $with_values = TRUE, $with_priority = FALSE)
		// {
		// 	$sql = 'DELETE ';

		// 	$sql .= $with_priority ? $query->getPriority() : '';
		// 	$tables = $query->getTables();

		// 	$sql .= $tables[0]->getAlias();

		// 	$from = $query->getFromString($with_values);
		// 	if($from == '')
		// 	{
		// 		return new BaseObject(-1, "Invalid query");
		// 	}
		// 	$sql .= ' FROM ' . $from;

		// 	$where = $query->getWhereString($with_values);
		// 	if($where != '')
		// 	{
		// 		$sql .= ' WHERE ' . $where;
		// 	}

		// 	return $sql;
		// }

		/**
		 * Return update query string
		 * @param object $query
		 * @param boolean $with_values
		 * @param boolean $with_priority
		 * @return string
		 */
		// function getUpdateSql($query, $with_values = TRUE, $with_priority = FALSE)
		// {
		// 	$columnsList = $query->getUpdateString($with_values);
		// 	if($columnsList == '')
		// 	{
		// 		return new BaseObject(-1, "Invalid query");
		// 	}

		// 	$tables = $query->getFromString($with_values);
		// 	if($tables == '')
		// 	{
		// 		return new BaseObject(-1, "Invalid query");
		// 	}

		// 	$where = $query->getWhereString($with_values);
		// 	if($where != '')
		// 	{
		// 		$where = ' WHERE ' . $where;
		// 	}

		// 	$priority = $with_priority ? $query->getPriority() : '';

		// 	return "UPDATE $priority $tables SET $columnsList " . $where;
		// }

		/**
		 * Return insert query string
		 * @param object $query
		 * @param boolean $with_values
		 * @param boolean $with_priority
		 * @return string
		 */
		// function getInsertSql($query, $with_values = TRUE, $with_priority = FALSE)
		// {
		// 	$tableName = $query->getFirstTableName();
		// 	$values = $query->getInsertString($with_values);
		// 	$priority = $with_priority ? $query->getPriority() : '';

		// 	return "INSERT $priority INTO $tableName \n $values";
		// }

		/**
		 * Given a SELECT statement that uses click count
		 * returns the corresponding update sql string
		 * for databases that don't have click count support built in
		 * (aka all besides CUBRID)
		 *
		 * Function does not check if click count columns exist!
		 * You must call $query->usesClickCount() before using this function
		 *
		 * @param $queryObject
		 */
		// function getClickCountQuery($queryObject)
		// {
		// 	$new_update_columns = array();
		// 	$click_count_columns = $queryObject->getClickCountColumns();
		// 	foreach($click_count_columns as $click_count_column)
		// 	{
		// 		$click_count_column_name = $click_count_column->column_name;

		// 		$increase_by_1 = new Argument($click_count_column_name, null);
		// 		$increase_by_1->setColumnOperation('+');
		// 		$increase_by_1->ensureDefaultValue(1);

		// 		$update_expression = new UpdateExpression($click_count_column_name, $increase_by_1);
		// 		$new_update_columns[] = $update_expression;
		// 	}
		// 	$queryObject->columns = $new_update_columns;
		// 	return $queryObject;
		// }

		/**
		 * returns list of supported dbms list
		 * this list return by directory list
		 * check by instance can creatable
		 * @return array return supported DBMS list
		 */
		// function getSupportedList()
		// {
		// 	$oDB = new DB();
		// 	return $oDB->_getSupportedList();
		// }

		/**
		 * returns enable list in supported dbms list
		 * this list return by child class
		 * @return array return enable DBMS list in supported dbms list
		 */
		// public static function getEnableList()
		// {
		// 	if(!self::$supported_list)
		// 	{
		// 		$oDB = new DB();
		// 		self::$supported_list = $oDB->_getSupportedList();
		// 	}

		// 	$enableList = array();
		// 	if(is_array(self::$supported_list))
		// 	{
		// 		foreach(self::$supported_list AS $key => $value)
		// 		{
		// 			if($value->enable)
		// 			{
		// 				$enableList[] = $value;
		// 			}
		// 		}
		// 	}
		// 	return $enableList;
		// }

		/**
		 * returns list of disable in supported dbms list
		 * this list return by child class
		 * @return array return disable DBMS list in supported dbms list
		 */
		// public static function getDisableList()
		// {
		// 	if(!self::$supported_list)
		// 	{
		// 		$oDB = new DB();
		// 		self::$supported_list = $oDB->_getSupportedList();
		// 	}

		// 	$disableList = array();
		// 	if(is_array(self::$supported_list))
		// 	{
		// 		foreach(self::$supported_list AS $key => $value)
		// 		{
		// 			if(!$value->enable)
		// 			{
		// 				$disableList[] = $value;
		// 			}
		// 		}
		// 	}
		// 	return $disableList;
		// }

		/**
		 * returns list of supported dbms list
		 * this method is private
		 * @return array return supported DBMS list
		 */
		// function _getSupportedList()
		// {
		// 	static $get_supported_list = '';
		// 	if(is_array($get_supported_list))
		// 	{
		// 		self::$supported_list = $get_supported_list;
		// 		return self::$supported_list;
		// 	}
		// 	$get_supported_list = array();
		// 	$db_classes_path = _XE_PATH_ . "classes/db/";
		// 	$filter = "/^DB([^\.]+)\.class\.php/i";
		// 	$supported_list = FileHandler::readDir($db_classes_path, $filter, TRUE);

		// 	// after creating instance of class, check is supported
		// 	for($i = 0; $i < count($supported_list); $i++)
		// 	{
		// 		$db_type = $supported_list[$i];

		// 		$class_name = sprintf("DB%s%s", strtoupper(substr($db_type, 0, 1)), strtolower(substr($db_type, 1)));
		// 		$class_file = sprintf(_XE_PATH_ . "classes/db/%s.class.php", $class_name);
		// 		if(!file_exists($class_file))
		// 		{
		// 			continue;
		// 		}

		// 		unset($oDB);
		// 		require_once($class_file);
		// 		$oDB = new $class_name(FALSE);

		// 		if(!$oDB)
		// 		{
		// 			continue;
		// 		}

		// 		$obj = new stdClass;
		// 		$obj->db_type = $db_type;
		// 		$obj->enable = $oDB->isSupported() ? TRUE : FALSE;

		// 		$get_supported_list[] = $obj;
		// 	}

		// 	// sort
		// 	// @usort($get_supported_list, array($this, '_sortDBMS'));

		// 	self::$supported_list = $get_supported_list;
		// 	return self::$supported_list;
		// }

		/**
		 * sort dbms as priority
		 */
		// function _sortDBMS($a, $b)
		// {
		// 	if(!isset($this->priority_dbms[$a->db_type]))
		// 	{
		// 		$priority_a = 0;
		// 	}
		// 	else
		// 	{
		// 		$priority_a = $this->priority_dbms[$a->db_type];
		// 	}

		// 	if(!isset($this->priority_dbms[$b->db_type]))
		// 	{
		// 		$priority_b = 0;
		// 	}
		// 	else
		// 	{
		// 		$priority_b = $this->priority_dbms[$b->db_type];
		// 	}

		// 	if($priority_a == $priority_b)
		// 	{
		// 		return 0;
		// 	}

		// 	return ($priority_a > $priority_b) ? -1 : 1;
		// }

		/**
		 * Return dbms supportable status
		 * The value is set in the child class
		 * @return boolean true: is supported, false: is not supported
		 */
		// function isSupported()
		// {
		// 	return self::$isSupported;
		// }

		/**
		 * Return connected status
		 * @param string $type master or slave
		 * @param int $indx key of server list
		 * @return boolean true: connected, false: not connected
		 */
		// function isConnected($type = 'master', $indx = 0)
		// {
		// 	if($type == 'master')
		// 	{
		// 		return $this->master_db["is_connected"] ? TRUE : FALSE;
		// 	}
		// 	else
		// 	{
		// 		return $this->slave_db[$indx]["is_connected"] ? TRUE : FALSE;
		// 	}
		// }

		/**
		 * Look for query cache file
		 * @param string $query_id query id for finding
		 * @param string $xml_file original xml query file
		 * @return string cache file
		 */
		// function checkQueryCacheFile($query_id, $xml_file)
		// {
		// 	// first try finding cache file
		// 	$cache_file = sprintf('%s%s%s.%s.%s.cache.php', _XE_PATH_, $this->cache_file, $query_id, __ZBXE_VERSION__, $this->db_type);

		// 	$cache_time = -1;
		// 	if(file_exists($cache_file))
		// 	{
		// 		$cache_time = filemtime($cache_file);
		// 	}

		// 	// if there is no cache file or is not new, find original xml query file and parse it
		// 	if($cache_time < filemtime($xml_file) || $cache_time < filemtime(_XE_PATH_ . 'classes/db/DB.class.php') || $cache_time < filemtime(_XE_PATH_ . 'classes/xml/XmlQueryParser.class.php'))
		// 	{
		// 		$oParser = new XmlQueryParser();
		// 		$oParser->parse($query_id, $xml_file, $cache_file);
		// 	}

		// 	return $cache_file;
		// }

		/**
		 * Returns counter cache data
		 * @param array|string $tables tables to get data
		 * @param string $condition condition to get data
		 * @return int count of cache data
		 */
		// function getCountCache($tables, $condition)
		// {
			// return FALSE;
	/*
			if(!$tables)
			{
				return FALSE;
			}
			if(!is_dir($this->count_cache_path))
			{
				return FileHandler::makeDir($this->count_cache_path);
			}

			$condition = md5($condition);

			if(!is_array($tables))
			{
				$tables_str = $tables;
			}
			else
			{
				$tables_str = implode('.', $tables);
			}

			$cache_path = sprintf('%s/%s%s', $this->count_cache_path, $this->prefix, $tables_str);
			FileHandler::makeDir($cache_path);

			$cache_filename = sprintf('%s/%s.%s', $cache_path, $tables_str, $condition);
			if(!file_exists($cache_filename))
			{
				return FALSE;
			}

			$cache_mtime = filemtime($cache_filename);

			if(!is_array($tables))
			{
				$tables = array($tables);
			}
			foreach($tables as $alias => $table)
			{
				$table_filename = sprintf('%s/cache.%s%s', $this->count_cache_path, $this->prefix, $table);
				if(!file_exists($table_filename) || filemtime($table_filename) > $cache_mtime)
				{
					return FALSE;
				}
			}

			$count = (int) FileHandler::readFile($cache_filename);
			return $count;
	*/
		// }

		/**
		 * Save counter cache data
		 * @param array|string $tables tables to save data
		 * @param string $condition condition to save data
		 * @param int $count count of cache data to save
		 * @return void
		 */
		// function putCountCache($tables, $condition, $count = 0)
		// {
		// 	return FALSE;
	/*
			if(!$tables)
			{
				return FALSE;
			}
			if(!is_dir($this->count_cache_path))
			{
				return FileHandler::makeDir($this->count_cache_path);
			}

			$condition = md5($condition);

			if(!is_array($tables))
			{
				$tables_str = $tables;
			}
			else
			{
				$tables_str = implode('.', $tables);
			}

			$cache_path = sprintf('%s/%s%s', $this->count_cache_path, $this->prefix, $tables_str);
			FileHandler::makeDir($cache_path);

			$cache_filename = sprintf('%s/%s.%s', $cache_path, $tables_str, $condition);

			FileHandler::writeFile($cache_filename, $count);
	*/
		// }

		/**
		 * Drop tables
		 * @param string $table_name
		 * @return void
		 */
		// function dropTable($table_name)
		// {
		// 	if(!$table_name)
		// 	{
		// 		return;
		// 	}
		// 	$query = sprintf("drop table %s%s", $this->prefix, $table_name);
		// 	$this->_query($query);
		// }

		/**
		 * Return index from slave server list
		 * @return int
		 */
		// function _getSlaveConnectionStringIndex()
		// {
		// 	$max = count($this->slave_db);
		// 	$indx = rand(0, $max - 1);
		// 	return $indx;
		// }

		/**
		 * Return connection resource
		 * @param string $type use 'master' or 'slave'. default value is 'master'
		 * @param int $indx if indx value is NULL, return rand number in slave server list
		 * @return resource
		 */
		// function _getConnection($type = 'master', $indx = NULL)
		// {
		// 	if($type == 'master')
		// 	{
		// 		if(!$this->master_db['is_connected'])
		// 		{
		// 			$this->_connect($type);
		// 		}
		// 		$this->connection = 'Master ' . $this->master_db['db_hostname'];
		// 		return $this->master_db["resource"];
		// 	}

		// 	if($indx === NULL)
		// 	{
		// 		$indx = $this->_getSlaveConnectionStringIndex($type);
		// 	}

		// 	if(!$this->slave_db[$indx]['is_connected'])
		// 	{
		// 		$this->_connect($type, $indx);
		// 	}

		// 	$this->connection = 'Slave ' . $this->slave_db[$indx]['db_hostname'];
		// 	return $this->slave_db[$indx]["resource"];
		// }

		/**
		 * check db information exists
		 * @return boolean
		 */
		// function _dbInfoExists()
		// {
		// 	if(!$this->master_db)
		// 	{
		// 		return FALSE;
		// 	}
		// 	if(count($this->slave_db) === 0)
		// 	{
		// 		return FALSE;
		// 	}
		// 	return TRUE;
		// }

		/**
		 * DB disconnection
		 * this method is protected
		 * @param resource $connection
		 * @return void
		 */
		// function _close($connection)
		// {
		// }

		/**
		 * DB disconnection
		 * @param string $type 'master' or 'slave'
		 * @param int $indx number in slave dbms server list
		 * @return void
		 */
		// function close($type = 'master', $indx = 0)
		// {
		// 	if(!$this->isConnected($type, $indx))
		// 	{
		// 		return;
		// 	}

		// 	if($type == 'master')
		// 	{
		// 		$connection = &$this->master_db;
		// 	}
		// 	else
		// 	{
		// 		$connection = &$this->slave_db[$indx];
		// 	}

		// 	$this->commit();
		// 	$this->_close($connection["resource"]);

		// 	$connection["is_connected"] = FALSE;
		// }

		/**
		 * DB transaction start
		 * this method is protected
		 * @return boolean
		 */
		// function _begin($transactionLevel = 0)
		// {
		// 	return TRUE;
		// }

		/**
		 * DB transaction start
		 * @return void
		 */
		// function begin()
		// {
		// 	if(!$this->isConnected())
		// 	{
		// 		return;
		// 	}

		// 	if($this->_begin($this->transactionNestedLevel))
		// 	{
		// 		$this->transaction_started = TRUE;
		// 		$this->transactionNestedLevel++;
		// 	}
		// }

		/**
		 * DB transaction rollback
		 * this method is protected
		 * @return boolean
		 */
		// function _rollback($transactionLevel = 0)
		// {
		// 	return TRUE;
		// }

		/**
		 * DB transaction rollback
		 * @return void
		 */
		// function rollback()
		// {
		// 	if(!$this->isConnected() || !$this->transaction_started)
		// 	{
		// 		return;
		// 	}
		// 	if($this->_rollback($this->transactionNestedLevel))
		// 	{
		// 		$this->transactionNestedLevel--;

		// 		if(!$this->transactionNestedLevel)
		// 		{
		// 			$this->transaction_started = FALSE;
		// 		}
		// 	}
		// }

		/**
		 * DB transaction commit
		 * this method is protected
		 * @return boolean
		 */
		// function _commit()
		// {
		// 	return TRUE;
		// }

		/**
		 * DB transaction commit
		 * @param boolean $force regardless transaction start status or connect status, forced to commit
		 * @return void
		 */
		// function commit($force = FALSE)
		// {
		// 	if(!$force && (!$this->isConnected() || !$this->transaction_started))
		// 	{
		// 		return;
		// 	}
		// 	if($this->transactionNestedLevel == 1 && $this->_commit())
		// 	{
		// 		$this->transaction_started = FALSE;
		// 		$this->transactionNestedLevel = 0;
		// 	}
		// 	else
		// 	{
		// 		$this->transactionNestedLevel--;
		// 	}
		// }

		/**
		 * Execute the query
		 * this method is protected
		 * @param string $query
		 * @param resource $connection
		 * @return void
		 */
		// function __query($query, $connection)
		// {

		// }

		/**
		 * DB info settings
		 * this method is protected
		 * @return void
		 */
		// function _setDBInfo()
		// {
		// 	$db_info = Context::getDBInfo();
		// 	$this->master_db = $db_info->master_db;
		// 	if($db_info->master_db["db_hostname"] == $db_info->slave_db[0]["db_hostname"]
		// 			&& $db_info->master_db["db_port"] == $db_info->slave_db[0]["db_port"]
		// 			&& $db_info->master_db["db_userid"] == $db_info->slave_db[0]["db_userid"]
		// 			&& $db_info->master_db["db_password"] == $db_info->slave_db[0]["db_password"]
		// 			&& $db_info->master_db["db_database"] == $db_info->slave_db[0]["db_database"]
		// 	)
		// 	{
		// 		$this->slave_db[0] = &$this->master_db;
		// 	}
		// 	else
		// 	{
		// 		$this->slave_db = $db_info->slave_db;
		// 	}
		// 	$this->prefix = $db_info->master_db["db_table_prefix"];
		// 	$this->use_prepared_statements = $db_info->use_prepared_statements;
		// }

		/**
		 * DB Connect
		 * this method is protected
		 * @param array $connection
		 * @return void
		 */
		// function __connect($connection)
		// {

		// }

		/**
		 * If have a task after connection, add a taks in this method
		 * this method is protected
		 * @param resource $connection
		 * @return void
		 */
		// function _afterConnect($connection)
		// {

		// }

		/**
		 * DB Connect
		 * this method is protected
		 * @param string $type 'master' or 'slave'
		 * @param int $indx number in slave dbms server list
		 * @return void
		 */
		// function _connect($type = 'master', $indx = 0)
		// {
		// 	if($this->isConnected($type, $indx))
		// 	{
		// 		return;
		// 	}

		// 	// Ignore if no DB information exists
		// 	if(!$this->_dbInfoExists())
		// 	{
		// 		return;
		// 	}

		// 	if($type == 'master')
		// 	{
		// 		$connection = &$this->master_db;
		// 	}
		// 	else
		// 	{
		// 		$connection = &$this->slave_db[$indx];
		// 	}

		// 	$result = $this->__connect($connection);
		// 	if($result === NULL || $result === FALSE)
		// 	{
		// 		$connection["is_connected"] = FALSE;
		// 		return;
		// 	}

		// 	// Check connections
		// 	$connection["resource"] = $result;
		// 	$connection["is_connected"] = TRUE;

		// 	// Save connection info for db logs
		// 	$this->connection = ucfirst($type) . ' ' . $connection["db_hostname"];

		// 	// regist $this->close callback
		// 	register_shutdown_function(array($this, "close"));

		// 	$this->_afterConnect($result);
		// }

		/**
		 * Returns a database specific parser instance
		 * used for escaping expressions and table/column identifiers
		 *
		 * Requires an implementation of the DB class (won't work if database is not set)
		 * this method is singleton
		 *
		 * @param boolean $force force load DBParser instance
		 * @return DBParser
		 */
		// public static function getParser($force = FALSE)
		// {
		// 	static $dbParser = NULL;
		// 	if(!$dbParser || $force)
		// 	{
		// 		$oDB = DB::getInstance();
		// 		$dbParser = $oDB->getParser();
		// 	}

		// 	return $dbParser;
		// }
	}
}
/* End of file DB.class.php */
