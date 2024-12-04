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

	public function getNextOneMonthEvents($args = []) {
		global $wpdb;
		$indb = INPURSUIT_DB::getInstance();
		$table = $this->getTable();
		$events = strtolower(implode("','", $this->getEventTypes()));
	
		$page = isset($args['page']) ? $args['page'] : 1;
		$per_page = isset($args['per_page']) ? $args['per_page'] : 10;
	
		$args_members = array(
			'post_type'      => 'inpursuit-members',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
	
		$members = get_posts($args_members);
		$memberMap = [];
		foreach ($members as $member) {
			$memberMap[intval($member->ID)] = [
				'name' => $member->post_title,
				// 'featured_image' => get_the_post_thumbnail_url($member->ID) ?? null,
				'featured_image' => $indb->getFeaturedImageURL($member->ID),
			];
		}
	
		$query = "
			SELECT * 
			FROM $table
			WHERE event_type IN ('" . $events . "')
			AND (
				(MONTH(event_date) = MONTH(CURRENT_DATE) AND DAY(event_date) >= DAY(CURRENT_DATE)) 
				OR
				(MONTH(event_date) = MONTH(CURRENT_DATE + INTERVAL 1 MONTH) AND DAY(event_date) <= DAY(CURRENT_DATE + INTERVAL 30 DAY))
				OR 
				(MONTH(event_date) = MONTH(CURRENT_DATE) AND DAY(event_date) < DAY(CURRENT_DATE))
			)
		";
	
		$countquery = "
			SELECT COUNT(*) 
			FROM $table
			WHERE event_type IN ('" . $events . "')
			AND (
				(MONTH(event_date) = MONTH(CURRENT_DATE) AND DAY(event_date) >= DAY(CURRENT_DATE)) 
				OR
				(MONTH(event_date) = MONTH(CURRENT_DATE + INTERVAL 1 MONTH) AND DAY(event_date) <= DAY(CURRENT_DATE + INTERVAL 30 DAY))
				OR 
				(MONTH(event_date) = MONTH(CURRENT_DATE) AND DAY(event_date) < DAY(CURRENT_DATE))
			)
		";
		$total_count = $wpdb->get_var($countquery);
		$total_pages = ceil($total_count / $per_page);
	
		$offset = ($page - 1) * $per_page;
		$mainquery = $query . " LIMIT $offset, $per_page";
	
		$rows = $wpdb->get_results($mainquery);
	
		$result = [];
		foreach ($rows as $row) {
			$memberData = $memberMap[intval($row->member_id)] ?? ['name' => null, 'featured_image' => null];
			$result[] = [
				'id' => intval($row->ID),
				'member_id' => intval($row->member_id),
				'event_type' => $row->event_type,
				'event_date' => date('Y-m-d', strtotime($row->event_date)),
				'created_on' => date('Y-m-d', strtotime($row->created_on)),
				'member_name' => $memberData['name'],
				'featured_image' => $memberData['featured_image'],
			];
		}
	
		// return [
		// 	'data' => $result,
		// 	'total' => $total_count,
		// 	'total_pages' => $total_pages,
		// 	'page' => $page,
		// 	'per_page' => $per_page,
		// ];
		return $result;
	}
	


	public function _getNextOneMonthEvents(){
    global $wpdb;

    $table = $this->getTable();
    $events = strtolower(implode("','", $this->getEventTypes()));

    $query = "
        SELECT 
            events.ID AS id,
            events.member_id,
            events.event_type,
            DATE(events.event_date) AS event_date,
            DATE(events.created_on) AS created_on,
            members.post_title AS member_name
        FROM $table AS events
        LEFT JOIN {$wpdb->posts} AS members
        ON events.member_id = members.ID
        WHERE events.event_type IN ('$events')
          AND (
              (MONTH(events.event_date) = MONTH(CURRENT_DATE) AND DAY(events.event_date) >= DAY(CURRENT_DATE))
              OR
              (MONTH(events.event_date) = MONTH(CURRENT_DATE + INTERVAL 1 MONTH) AND DAY(events.event_date) <= DAY(CURRENT_DATE + INTERVAL 30 DAY))
              OR
              (MONTH(events.event_date) = MONTH(CURRENT_DATE) AND DAY(events.event_date) < DAY(CURRENT_DATE))
          )
          AND members.post_status = 'publish'
    ";

    $rows = $wpdb->get_results($query);

    
    $result = array_map(function ($row) {
        return [
            'id' => intval($row->id),
            'member_id' => intval($row->member_id),
            'event_type' => $row->event_type,
            'event_date' => $row->event_date,
            'created_on' => $row->created_on,
            'member_name' => $row->member_name,
        ];
    }, $rows);
	
    return $result;
	}
}

INPURSUIT_DB_MEMBER_DATES::getInstance();
