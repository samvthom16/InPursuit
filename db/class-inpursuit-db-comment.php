<?php
/*
* Model: COMMENT
*/

class INPURSUIT_DB_COMMENT extends INPURSUIT_DB_BASE{

	function __construct(){
		$this->setTableSlug( 'comments' );
		parent::__construct();
	}

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$posts_table = $wpdb->prefix . 'posts' . '(ID)';
		$users_table = $wpdb->prefix . 'users' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			comment LONGTEXT,
			post_id BIGINT(20) UNSIGNED NOT NULL,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			modified_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
			FOREIGN KEY (post_id) REFERENCES $posts_table,
			FOREIGN KEY (user_id) REFERENCES $users_table
		) $charset_collate;";
		$this->query( $sql );

	}

	function getResultsQuery( $args ){
		global $wpdb;
		$comment_table = $this->getTable();
		$query = "SELECT ID, comment as text, post_id, user_id, modified_on as post_date, 'comment' as type FROM $comment_table WHERE 1=1";
		$values = array();

		/*
		* SEARCH QUERY - Using LIKE with proper escaping
		*/
		if( isset( $args['search'] ) && $args['search'] ){
			$search = $this->esc_like( $args['search'] );
			$query .= " AND comment LIKE %s";
			$values[] = '%' . $search . '%';
		}
		if( isset( $args['member_id'] ) && $args['member_id'] ){
			$member_id = intval( $args['member_id'] );
			$query .= " AND post_id = %d";
			$values[] = $member_id;
		}
		if( isset( $args['user_id'] ) && $args['user_id'] ){
			$user_id = intval( $args['user_id'] );
			$query .= " AND user_id = %d";
			$values[] = $user_id;
		}
		if( isset( $args['comment_ids'] ) && is_array( $args['comment_ids'] ) && count( $args['comment_ids'] ) ){
			$ids = array_map( 'intval', $args['comment_ids'] );
			$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
			$query .= " AND ID IN ($placeholders)";
			$values = array_merge( $values, $ids );
		}

		// Prepare and return the query with values for use by getResults()
		if( count( $values ) > 0 ){
			return $this->prepare( $query, $values );
		}
		return $query;
	}

	function sanitize( $request ){
		$data = array(
			'comment'	=> isset( $request['comment'] ) ? $request['comment'] : '',
			'post_id'	=> isset( $request['post'] ) ? $request['post'] : 0,
			'user_id'	=> get_current_user_id()
		);
		return $data;
	}



}
INPURSUIT_DB_COMMENT::getInstance();
