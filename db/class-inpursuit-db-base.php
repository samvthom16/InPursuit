<?php
/*
* ABSTRACT MODEL THAT HANDLES SQL QUERIES AND ACTS AS A WRAPPER FOR WPDB
*/

class INPURSUIT_DB_BASE extends INPURSUIT_BASE{

	private $table;
	private $table_slug;
	private $post_type_options;
	private $post_type;

	function __construct(){

		// SET TABLE SLUG
		$this->setTable( $this->getTablePrefix() . $this->getTableSlug() );

		// CREATE TABLE
		$this->create();

		// REMOVE TABLE FROM PRODUCTION
		add_action( 'inpursuit_db_drop', array( $this, 'drop_table' ) );

		// ALTER TABLE IN PRODUCTION
		add_action( 'inpursuit_db_alter', array( $this, 'alter_table' ) );

		add_action( 'init', array( $this, 'registerPostType' ) );

	}


	/* GETTER AND SETTER FUNCTIONS */
	function setPostTypeOptions( $post_type_options ){ $this->post_type_options = $post_type_options; }
	function getPostTypeOptions(){ return $this->post_type_options; }

	function setPostType( $post_type ){ $this->post_type = $post_type; }
	function getPostType(){ return $this->post_type; }

	function setTable( $table ){ $this->table = $table; }
	function getTable(){ return $this->table; }
	function setTableSlug( $slug ){ $this->table_slug = $slug; }
	function getTableSlug(){ return $this->table_slug; }
	function getTablePrefix(){
		global $wpdb;
		return $wpdb->prefix.'ip_';
	}

	/*
	function getResponseDB(){
		require_once( 'class-space-db-response.php' );
		return SPACE_DB_RESPONSE::getInstance();
	}
	function getPageQuestionRelationDB(){
		require_once('class-space-db-page-question-relation.php');
		return SPACE_DB_PAGE_QUESTION_RELATION::getInstance();
	}
	function getPageDB(){
		require_once('class-space-db-page.php');
		return SPACE_DB_PAGE::getInstance();
	}
	function getQuestionDB(){
		require_once('class-space-db-question.php');
		return SPACE_DB_QUESTION::getInstance();
	}
	function getChoiceDB(){
		require_once('class-space-db-choice.php');
		return SPACE_DB_CHOICE::getInstance();
	}
	function getGuestDB(){
		require_once('class-space-db-guest.php');
		return SPACE_DB_GUEST::getInstance();
	}
	*/

	/* GETTER AND SETTER FUNCTIONS */

	// WRAPPER AROUND WPDB->QUERY
	function query( $sql ){
		global $wpdb;
		return $wpdb->query( $sql );
	}

	// WRAPPER AROUND WPDB->INSERT
	function insert( $data ){
		global $wpdb;
		$wpdb->insert( $this->getTable(), $data );
		return $wpdb->insert_id;
	}

	function insert_rows( $row_arrays = array() ) {
		global $wpdb;

		// Setup arrays for Actual Values, and Placeholders
		$values = array();
		$place_holders = array();
		$query = "";
		$query_columns = "";


		$query .= "INSERT INTO {$this->getTable()} (";

		foreach( $row_arrays as $count => $row_array ){

			foreach( $row_array as $key => $value ) {

				// ADDING THE SCHEMA COLUMNS OF THE TABLE
				if( $count == 0 ) {
					if( $query_columns ){
						$query_columns .= ",".$key."";
					} else {
						$query_columns .= "".$key."";
					}
				}

				$values[] =  $value;

				if( is_numeric( $value ) ) {
					if( isset( $place_holders[$count] ) ) {
						$place_holders[$count] .= ", '%d'";
					} else {
						$place_holders[$count] .= "( '%d'";
					}
				} else {
					if(isset($place_holders[$count])) {
						$place_holders[$count] .= ", '%s'";
					}
					else {
						$place_holders[$count] .= "( '%s'";
					}
				}
			}

			// mind closing the GAP
			$place_holders[$count] .= ")";
		}

		$query .= " $query_columns ) VALUES ";

		$query .= implode( ', ', $place_holders );

		return $wpdb->query( $this->prepare( $query, $values ) );

	}

	function update( $id, $data, $format = array() ){
		global $wpdb;
		return $wpdb->update( $this->getTable(), $data, array( 'ID' => $id ), $format );
	}

	// WRAPPER AROUND WPDB->GET_RESULTS
	function get_results( $sql ){
		global $wpdb;
		return $wpdb->get_results( $sql );
	}

	// WRAPPER AROUND WPDB->GET_VAR
	function get_var( $sql ){
		global $wpdb;
		return $wpdb->get_var( $sql );
	}

	// WRAPPER AROUND WPDB->GET_CHARSET_COLLATE
	function get_charset_collate(){
		global $wpdb;
		return $wpdb->get_charset_collate();
	}

	// GET SINGLE ROW USING UNIQUE ID
	function get_row( $ID ){
		global $wpdb;
		$table = $this->getTable();
		$query = "SELECT * FROM $table WHERE ID = $ID;";
		return $wpdb->get_row( $query );
	}

	// WRAPPER AROUND USING WPDB->ESC_LIKE
	function esc_like( $term ){
		global $wpdb;
		return $wpdb->esc_like( $term );
	}

	// WRAPPER AROUND WPDB->PREPARE
	function prepare( $query, $args ){
		global $wpdb;
		return $wpdb->prepare( $query, $args );
	}

	function getCount( $search = array( 'col_formats' => array(), 'col_values'	=> array(), 'operator' => 'LIKE' ) ){

		$where_query = $this->_where_query( $search['col_formats'], $search['operator'] );

		// QUERY TO GET TOTAL NUMBER OF ROWS
		$count_query = "SELECT COUNT(*)".$this->_from_query();
		if( $where_query ){
			$count_query .= $where_query;
		}
		$count_query .= ";";

		if( is_array( $search['col_values'] ) && count( $search['col_values'] ) ){
			$count_query = $this->prepare( $count_query, $search['col_values'] );
		}

		return $this->get_var( $count_query );
	}

	function results( $page, $per_page, $search = array( 'col_formats' => array(), 'col_values'	=> array(), 'operator' => 'LIKE' ) ){
		$data = array();

		$where_query = $this->_where_query( $search['col_formats'], $search['operator'] );

		$data['num_rows'] = $this->getCount( $search );

		$results_query = "SELECT *".$this->_from_query();

		// ADD CONDITIONS
		if( $where_query ){
			$results_query .= $where_query;
		}

		// QUERY TO GET PAGINATED RESPONSE
		$results_query .= $this->_limit_query( $page, $per_page ).";";

		if( is_array( $search['col_values'] ) && count( $search['col_values'] ) ){
			$results_query = $this->prepare( $results_query, $search['col_values'] );
		}

		$data['results'] = $this->get_results( $results_query );

		//echo $count_query."<br>";
		//echo $results_query."<br>";

		return $data;
	}

	function _from_query(){
		$table = $this->getTable();
		return " FROM $table";
	}

	function _where_query( $col_formats, $operator = "=" ){
		$query = "";
		if( is_array( $col_formats ) && count( $col_formats ) ){
			$query .= " WHERE";
			$i = 0;
			foreach( $col_formats as $col => $col_format ){
				if( $i ){ $query .= " AND"; }
				$query .= " $col $operator $col_format";
				$i++;
			}
		}

		return $query;
	}

	function _limit_query( $page, $per_page ){
		$offset = ( $page - 1 ) * $per_page;
		return " LIMIT $offset,$per_page";
	}

	function filter( $col_formats, $col_values, $order_by = 'ID', $order = 'ASC' ){

		// FORM THE QUERY
		$query = "SELECT * ".$this->_from_query().$this->_where_query( $col_formats );
		$query .= " ORDER BY $order_by $order";
		$query .= ";";
		return $this->get_results( $this->prepare( $query, $col_values ) );
	}

	// DELETE SPECIFIC ROW
	function delete_row( $ID ){
		$table = $this->getTable();
		$sql = "DELETE FROM $table WHERE ID = %d;";
		$this->query( $this->prepare( $sql, $ID ) );
	}

	// DELETE MULTIPLE ROWS WITH MATCHING ID
	function delete_rows( $ids_arr ){
		if( is_array( $ids_arr ) && count( $ids_arr ) ){
			$ids_str = implode( ',', $ids_arr );
			$table = $this->getTable();
			$query = "DELETE FROM $table WHERE ID IN ($ids_str);";
			$this->query( $query );
		}
	}

	function delete( $where, $where_format = null ){
		global $wpdb;
		return $wpdb->delete(
			$this->getTable(),
			$where,
			$where_format
		);

	}



	// TO BE IMPLEMENTED BY CHILD CLASSES - HANDLES TABLE CREATION
	function create(){}

	// TO BE IMPLEMENTED BY CHILD CLASSES - RETURNS DB DATA
	function sanitize( $data ){ return $data; }

	// TO BE IMPLEMENTED BY CHILD CLASSES IF NOT NEEDED
	function drop_table(){
		$table = $this->getTable();
		$query = "DROP TABLE IF EXISTS $table";

		$this->query( $query );

		echo "$table Table dropped.<br/>";
	}

	function alter_table(){}

	function _labelsArray( $options ){
		$name = $options['name'];
		$singular_name = $options['singular_name'];
		return array(
			'name'               => $name,
			'singular_name'      => $singular_name,
			'add_new'            => _x( 'Add New', $singular_name ),
			'add_new_item'       => __( "Add New $singular_name" ),
			'edit_item'          => __( "Edit $singular_name" ),
			'new_item'           => __( "New $singular_name" ),
			'all_items'          => __( "All $name" ),
			'view_item'          => __( "View $singular_name" ),
			'search_items'       => __( "Search $name" ),
			'not_found'          => __( "No $name found" ),
			'not_found_in_trash' => __( "No $name found in the Trash" ),
			'parent_item_colon'  => '',
			'menu_name'          => $name
		);
	}

	function registerPostType(){
		$options = $this->getPostTypeOptions();
		if( is_array( $options ) && count( $options ) && isset( $options['slug'] ) ){
			$args = array(
				'labels'        => $this->_labelsArray( $options ),
				'description'   => $options['description'],
				'public'        => true,
				'menu_icon'			=> $options['menu_icon'],
				'supports'			=> $options['supports'],
				'show_in_rest'	=> true
			);
			register_post_type( $options['slug'], $args );
		}
	}

	function registerTaxonomy(){
		//$taxonomies = $this->getTaxonomies();

	}

}