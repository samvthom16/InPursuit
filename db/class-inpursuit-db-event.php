<?php
/*
* Model: EVENT
*/

class INPURSUIT_DB_EVENT extends INPURSUIT_DB_POST_BASE{

	function __construct(){

		parent::__construct();

		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setPostTypeOptions( array(
			'name' 					=> 'Events',
			'singular_name' => 'Event',
			'slug' 					=> $this->getPostType(),
			'description' 	=> 'Holds our events specific data',
			'menu_icon'			=> 'dashicons-format-video',
			'supports'			=> array( 'title', 'editor', 'thumbnail', 'author' )
		) );

		//add_action( 'rest_api_init', array( $this, 'addRestData' ) );

	}

	/*
	function getHistory( $args ){
		$page = isset( $args[ 'page' ]  ) ? $args[ 'page' ] : 1;
		$per_page = isset( $args[ 'per_page' ]  ) ? $args[ 'per_page' ] : 10;


		$post_type = $this->getPostType();

		require_once('class-inpursuit-db-event-member-relation.php');
		$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		$wp_util = INPURSUIT_WP_UTIL::getInstance();

		global $wpdb;
		$posts_table = $wpdb->posts;
		$event_member_table = $event_member_db->getTable();


		$subquery = " FROM $posts_table WHERE post_status='publish' AND post_type='$post_type'";

		if( isset( $args['id'] ) ){
			$member_id = $args['id'];
			$subquery .= " AND ID IN (SELECT event_id FROM $event_member_table WHERE member_id=$member_id)";
		}
		//$subquery = " FROM " . $wpdb->posts . " WHERE ID IN (SELECT event_id FROM " . $event_member_db->getTable() . " WHERE member_id=$member_id) AND post_status='publish' AND post_type='$post_type' ORDER BY post_date DESC";
		$subquery .= " ORDER BY post_date DESC";

		$mainquery = "SELECT *" . $subquery . $this->_limit_query( $page, $per_page );
		$countquery = "SELECT count(*)" . $subquery;

		$data = array();


		$rows = $wpdb->get_results( $mainquery );
		$total_count = $wpdb->get_var( $countquery );
		$total_pages = ceil( $total_count/$per_page );
		foreach( $rows as $row ){
			$post = array(
				'title'				=> array( 'rendered' => $row->post_title ),
				'date'				=> $row->post_date,
				'date_gmt'		=> $row->post_date_gmt,
				'terms'				=> $wp_util->getAllTermsForPost( $row->ID ),
			);
			array_push( $data, $post );
		}


		return array(
			'data'				=> $data,
			'total'				=> $total_count,
			'total_pages'	=> $total_pages
		);
	}
	*/

	function statsForEventType( $event_type_id, $after_date, $before_date ){
		global $wpdb;
		$db_event_member_relation = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance()->getTable();
		$db_posts = $wpdb->posts;
		$db_term_relation = $wpdb->term_relationships;
		$db_term_taxonomy = $wpdb->term_taxonomy;

		$query = "SELECT count(member_id) as total, event_id FROM $db_event_member_relation WHERE event_id IN
			(SELECT ID FROM $db_posts WHERE (post_date BETWEEN '$after_date' AND '$before_date') AND ID IN
				(SELECT object_id FROM $db_term_relation WHERE term_taxonomy_id IN
					(SELECT term_taxonomy_id FROM $db_term_taxonomy WHERE term_id=$event_type_id) ) ) GROUP BY event_id";


		return $this->get_results( $query );
	}

	function _totalStats( $grouped_stats ){
		$total = 0;
		foreach( $grouped_stats as $stat ){
			$total += $stat->total;
		}
		return $total;
	}

	function _average( $num, $denom ){
		return $denom ? $num/$denom : 0;
	}

	function _growth( $total, $prev ){
		return $total ? ( $total - $previous ) * 100 / $total : 0;
	}

	function totalStatsForEventType( $event_type_id ){
		$grouped_stats = $this->statsForEventType( $event_type_id, date( 'Y-m-d', strtotime( '-180 days' ) ), date( 'Y-m-d' ) );
		$prev_grouped_stats = $this->statsForEventType( $event_type_id, date( 'Y-m-d', strtotime( '-360 days' ) ), date( 'Y-m-d', strtotime( '-180 days' ) ) );

		$previous_members = $this->_totalStats( $prev_grouped_stats );
		$previous_events = count( $prev_grouped_stats );
		$previous_average = $this->_average( $previous_members, $previous_events );

		$total_members = $this->_totalStats( $grouped_stats );
		$total_events = count( $grouped_stats );
		$total_average = $this->_average( $total_members, $total_events );

		return array(
			'previous_members' 	=> $previous_members,
			'previous_events' 	=> $previous_events,
			'previous_average'	=> $previous_average,
			'total_members'			=> $total_members,
			'total_events'			=> $total_events,
			'total_average'			=> $total_average,
			'growth'						=> $this->_growth( $total_average, $previous_average )
		);
	}

	function numberOfRegisteredMembers( $event_id ){
		$registered_members = 1;
		$event_date = explode( ',', get_the_time( 'Y,m,d', $event_id ) );

		$args = array(
			'post_type' => 'inpursuit-members',
				'date_query' => array(
						array(
								'before'    => array(
										'year'  => $event_date[0],
										'month' => $event_date[1],
										'day'   => $event_date[2],
								),
								'inclusive' => true,
						),
				),
				'posts_per_page' => -1,
		);
		$members_query = new WP_Query( $args );
		if( isset( $members_query->post_count ) && $members_query->post_count ){
			$registered_members = $members_query->post_count;
		}
		return $registered_members;
	}

	function numberOfParticipatingMembers( $event_id ){
		$total_attending = 0;
		$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		$participating_members = $event_member_db->getMembersIDForEvent( $event_id );
		if( is_array( $participating_members ) ){
			$total_attending = count( $participating_members );
		}
		return $total_attending;
	}

	function attendantsPercentage( $event_id ){
		$registered_members = $this->numberOfRegisteredMembers( $event_id );
		$total_attending = $this->numberOfParticipatingMembers( $event_id );

		$percentage = 0;
		if( $registered_members > 0 ){
			$percentage = ceil( ($total_attending / $registered_members) * 100 );
		}

		if( $percentage > 100 ) $percentage = 100;

		return $percentage;
	}

}
INPURSUIT_DB_EVENT::getInstance();
