<?php
/*
* Model: COMMENTS_CATEGORY
*/

class INPURSUIT_DB_COMMENTS_CATEGORY extends INPURSUIT_DB_BASE {

	function __construct(){
		$this->setTableSlug( 'comments_category' );
		parent::__construct();
	}

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			term_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name LONGTEXT NOT NULL,
			PRIMARY KEY(term_id)
		) $charset_collate;";

		$this->query( $sql );

	}

	// GET SINGLE ROW USING UNIQUE ID
	function get_row( $term_id ){
		global $wpdb;
		$table = $this->getTable();
		$query = "SELECT * FROM $table WHERE term_id = $term_id;";
		return $wpdb->get_row( $query );
	}

	function _orderby_query(){
		return " ORDER BY term_id ";
	}

	function getResultsQuery( $args ){
		$table = $this->getTable();
		$query = "SELECT * FROM $table";

		return $query;
	}

	function comment_category_name_exists( $category_name ){
		global $wpdb;
		$table = $this->getTable();
		$category_name = strtolower($category_name);
		$count_query = "SELECT COUNT(*) FROM $table WHERE LOWER(name) = '$category_name' ";

		if( $this->get_var( $count_query ) ){
			return true;
		}

		return false;

	}

	function sanitize( $request ){
		return array(
			'name'	=> isset( $request['name'] ) ? $request['name'] : ''
		);
	}

}

INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();
