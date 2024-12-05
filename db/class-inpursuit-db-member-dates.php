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

		$posts_table = $wpdb->prefix . 'posts' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			member_id BIGINT(20) UNSIGNED NOT NULL,
			event_type VARCHAR(20),
			event_date DATETIME DEFAULT CURRENT_TIMESTAMP,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
			FOREIGN KEY (member_id) REFERENCES $posts_table
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

	function age( $member_id ){
		global $wpdb;
		$table = $this->getTable();
		$query = $wpdb->prepare( "SELECT TIMESTAMPDIFF(YEAR, event_date, CURDATE()) AS age FROM $table WHERE member_id = %d AND event_type = %s", array(
			$member_id,
			'birthday'
		) );
		return $wpdb->get_var( $query );
	}

	/**
	 * Returns list of members for each events on current date.
	 *
	 **/
	public function getMembersEventForToday()
	{
		global $wpdb;
		$table = $this->getTable();
		$events = strtolower(implode("','", $this->getEventTypes()));

		$query = "SELECT member_id, event_type, event_date FROM $table WHERE event_type IN ('". $events ."') AND event_date=CURDATE();";

		$rows = $wpdb->get_results($query);

		$result = [];
		foreach ($rows as $row) {
			$result[$row->event_type][] = $row;
		}

		return $result;
	}

	/**
	 * Returns members array if their birthday or wedding-anniversary is either today or in the next 30 days.
	 * else returns an empty array
	 **/
	public function getNextOneMonthEvents( $args = [] ){
		global $wpdb;
		$result 	 = [];
		$table 		 = $this->getTable();
		$events 	 = strtolower(implode("','", $this->getEventTypes()));
		$page 		 = isset( $args['page'] ) && $args['page'] ? $args['page'] : 1;
		$per_page  = isset( $args['per_page'] ) && $args['per_page'] ? $args['per_page'] : 10;
		$offset		 = ( $page - 1 ) * $per_page;

		// FETCH MEMBERS IF EVENT_DATE IS VALID AND THE NEXT EVENT IS EITHER TODAY OR IN THE NEXT 30 DAYS
		// TIMESTAMPDIFF(YEAR, event_date, CURRENT_DATE) AS age
		$query = "
			SELECT ID, member_id, event_type, event_date,
			DATE_ADD(event_date, INTERVAL TIMESTAMPDIFF(YEAR, event_date, CURRENT_DATE) + 1 YEAR) AS next_event_date
			FROM $table
			WHERE event_type IN ('" . $events . "')
			AND UNIX_TIMESTAMP(event_date) IS NOT NULL
			AND DATE_ADD(event_date, INTERVAL TIMESTAMPDIFF(YEAR, event_date, CURRENT_DATE) + 1 YEAR)
			BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)
			ORDER BY next_event_date
		";

		$countquery = "SELECT count(*) as total FROM ($query) as temp;";
		$total 			= $wpdb->get_var( $countquery );
		$mainquery 	= $query . " LIMIT $offset, $per_page";
		$rows 			= $wpdb->get_results( $mainquery );

		foreach( $rows as $row ){
			$member = get_posts( array(
				'post_type'      => 'inpursuit-members',
				'posts_per_page' => 1,
				'include'   		 => $row->member_id,
				'post_status'    => 'publish'
			) );

			array_push( $result,  array(
				'id' 						 => intval( $row->ID ),
				'member_id' 		 => intval( $row->member_id ),
				'member_name' 	 => count($member) ? $member[0]->post_title : '',
				'featured_image' => INPURSUIT_DB::getInstance()->getFeaturedImageURL( $row->member_id ),
				'event_type' 		 => $row->event_type,
				'event_date' 		 => date('Y-m-d', strtotime( $row->next_event_date ) ),
				// 'original_date' => date('Y-m-d', strtotime( $row->event_date ) ),
			) );
		}

		$response = new WP_REST_Response( $result, 200 );
    $response->header( 'X-WP-TotalPages', ceil( $total/$per_page ) );
		$response->header( 'X-WP-Total', $total );
    return $response;
	}

}

INPURSUIT_DB_MEMBER_DATES::getInstance();
