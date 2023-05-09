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
		$comment_table = $this->getTable();
		$query = "SELECT ID, comment as text, post_id, user_id, modified_on as post_date, 'comment' as type FROM $comment_table";

		/*
		* SEARCH QUERY
		*/
		if( isset( $args['search'] ) && $args['search'] ){
			$search = $args['search'];
			$query .= " WHERE comment LIKE '%$search%'";
		}
		if( isset( $args['member_id'] ) && $args['member_id'] ){
			$member_id = $args['member_id'];
			$query .= " WHERE post_id=$member_id";
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
