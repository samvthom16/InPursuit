<?php

class INPURSUIT_REST_ANALYTICS extends INPURSUIT_REST_BASE{

	function getStatsForEventTypes(){
		$data = array();

		$total_data = array();
		$terms = get_terms( array(
			'taxonomy' 		=> 'inpursuit-event-type',
			'hide_empty' 	=> false,
			'fields'			=> 'id=>name'
		) );

		$db = INPURSUIT_DB::getInstance();

		foreach( $terms as $term_id => $term_name ){

			$total_stats = $db->totalStatsForEventType( $term_id );

			if( $total_stats[ 'total_members' ] ){
				$row = array(
					'label'		=> $term_name,
					'total'		=> $total_stats['total_average'],
					'growth'	=> $total_stats['growth']
				);

				$total_data[] = $row['total'];
				array_push( $data, $row );
			}
		}

		array_multisort( $total_data, SORT_DESC, $data );
		return $data;
	}

	function getAnalyticsCallback( WP_REST_Request $args ){

		$data = array();

		$db = INPURSUIT_DB::getInstance();

		/*
		* STATS FOR ACTIVE MEMBERS
		*/
		$members_stats = $db->totalStatsForPostType( 'inpursuit-members' );
		$data[] = array(
			'label'		=> 'Active Members',
			'total'		=> $members_stats['total'],
			'growth' 	=> $members_stats['growth'],
		);

		/*
		* STATS FOR ARCHIVED MEMBERS
		*/
		$archive_members_stats = $db->totalStatsForPostType( 'inpursuit-members', 'draft' );
		$data[] = array(
			'label'		=> 'Archived Members',
			'total'		=> $archive_members_stats['total'],
			'growth' 	=> $archive_members_stats['growth'],
		);

		/*
		* STATS FOR EVENTS
		*/
		$events_stats = $db->totalStatsForPostType( 'inpursuit-events' );
		$data[] = array(
			'label'		=> 'All Events',
			'total'		=> $events_stats['total'],
			'growth' 	=> $events_stats['growth'],
		);

		/*
		* STATS FOR EACH EVENT TYPE
		*/
		$data = array_merge( $data, $this->getStatsForEventTypes() );

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'analytics', array( $this, 'getAnalyticsCallback' ) );


	}


}
INPURSUIT_REST_ANALYTICS::getInstance();
