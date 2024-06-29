<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class PageHandler
 * @author XEHub (developers@xpressengine.com)
 * handles page navigation
 *
 * @remarks Getting total counts, number of pages, current page number, number of items per page,
 *          this class implements methods and contains variables for page navigation
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Classes\\PageHandler' ) ) {

	class PageHandler // extends Handler <- blank abc
 {	                                                  
		public $n_total_count = 0; // < number of total items
		public $n_total_page  = 0; // < number of total pages
		public $n_cur_page    = 0; // < current page number
		public $n_page_count  = 10; // < number of page links displayed at one time
		public $n_first_page  = 1; // < first page number
		public $n_last_page   = 1; // < last page number
		private $_n_point     = 0; // < increments per getNextPage()

		/**
		 * constructor
		 *
		 * @param int $total_count number of total items
		 * @param int $total_page number of total pages
		 * @param int $cur_page current page number
		 * @param int $page_count number of page links displayed at one time
		 * @return void
		 */
		function __construct( $total_count, $total_page, $cur_page, $page_count = 10 ) {
			$this->n_total_count = $total_count;
			$this->n_total_page  = $total_page;
			$this->n_cur_page    = $cur_page;
			$this->n_page_count  = $page_count;
			$this->_n_point      = 0;

			$first_page = $cur_page - intval( $page_count / 2 );
			if ( $first_page < 1 ) {
				$first_page = 1;
			}

			if ( $total_page > $page_count && $first_page + $page_count - 1 > $total_page ) {
				$first_page -= $first_page + $page_count - 1 - $total_page;
			}

			$last_page = $total_page;
			if ( $last_page > $total_page ) {
				$last_page = $total_page;
			}

			$this->n_first_page = $first_page;
			$this->n_last_page  = $last_page;

			if ( $total_page < $this->n_page_count ) {
				$this->n_page_count = $total_page;
			}
		}

		/**
		 * request next page
		 *
		 * @return int next page number
		 */
		function getNextPage() {
			$page = $this->n_first_page + $this->_n_point++;
			if ( $this->_n_point > $this->n_page_count || $page > $this->n_last_page ) {
				$page = 0;
			}
			return $page;
		}

		/**
		 * return number of page that added offset.
		 *
		 * @param int $offset
		 * @return int
		 */
		function getPage( $offset ) {
			return max( min( $this->n_cur_page + $offset, $this->n_total_page ), '' );
		}
	}
}
/* End of file PageHandler.class.php */
