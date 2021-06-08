<?php
/*
* Model: MEMBER DATES
*/

class INPURSUIT_DB_MEMBER_DATES extends INPURSUIT_DB_BASE{

	private $event_types;

	function __construct(){
		$this->setTableSlug( 'member_dates' );

		$this->setEventTypes( array(
			'birthday'	=> 'Birthday',
			'wedding'		=> 'Wedding',
		) );

		parent::__construct();
	}

	function getEventTypes(){ return $this->event_types; }
	function setEventTypes( $event_types ){ $this->event_types = $event_types; }

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			member_id BIGINT(20) NOT NULL,
			event_type VARCHAR(20),
			event_date DATETIME DEFAULT CURRENT_TIMESTAMP,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
			FOREIGN KEY (member_id) REFERENCES $wpdb->posts(ID)
		) $charset_collate;";
		$this->query( $sql );
	}

	function getForMember( $member_id ){
		global $wpdb;
		$data = array();
		$table = $this->getTable();
		$query = $wpdb->prepare( "SELECT ID, event_type, event_date FROM $table WHERE member_id = %d;", array( $member_id ) );
		$rows = $wpdb->get_results( $query );
		foreach( $rows as $row ){
			$data[ $row->event_type ] = $row;
		}
		return $data;
	}

	function updateToMember( $member_id, $event_dates ){

		// GET EVENTS DATA THAT ARE ALREADY EXISTING IN THE DB
		$old_event_dates = $this->getForMember( $member_id );

		foreach( $event_dates as $event_type => $event_date ){
			$new_event_date = array(
				'event_type'	=> $event_type,
				'event_date'	=> $event_date,
				'member_id'		=> $member_id
			);

			// IF OLD DATA IS PRESENT THEN UPDATE OTHERWISE INSERT THE DATA
			if( isset( $old_event_dates[ $event_type ] ) && isset( $old_event_dates[ $event_type ]->ID ) ){
				$id = $old_event_dates[ $event_type ]->ID;
				$this->update( $id, $new_event_date, array( '%s', '%s' ) );
			}
			else{
				$this->insert( $new_event_date );
			}
		}
	}



}

INPURSUIT_DB_MEMBER_DATES::getInstance();
