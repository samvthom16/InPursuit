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

}
INPURSUIT_DB_COMMENT::getInstance();
