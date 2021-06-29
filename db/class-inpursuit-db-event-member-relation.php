<?php
/*
* RELATION TABLE BETWEEN EVENT AND MEMBER
*/

class INPURSUIT_DB_EVENT_MEMBER_RELATION extends INPURSUIT_DB_BASE{

	function __construct(){
		$this->setTableSlug( 'event_member_relation' );

		parent::__construct();
	}

	function getMembersIDForEvent( $event_id ){
		$table = $this->getTable();
		global $wpdb;
		return $wpdb->get_col( "SELECT member_id FROM $table WHERE event_id = $event_id;" );
	}

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$posts_table = $wpdb->prefix . 'posts' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			event_id BIGINT(20) UNSIGNED NOT NULL,
			member_id BIGINT(20) UNSIGNED NOT NULL,
			PRIMARY KEY(ID),
			FOREIGN KEY (event_id) REFERENCES $posts_table,
			FOREIGN KEY (member_id) REFERENCES $posts_table
		) $charset_collate;";
		$this->query( $sql );

	}

}

INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
