<?php

class INPURSUIT_REST_ANALYTICS extends INPURSUIT_REST_BASE{

	function getStatsForEventTypes( $period ){

		$data = array();

		$total_data = array();
		$terms = get_terms( array(
			'taxonomy' 		=> 'inpursuit-event-type',
			'hide_empty' 	=> false,
			'fields'			=> 'id=>name'
		) );

		$db = INPURSUIT_DB::getInstance();

		foreach( $terms as $term_id => $term_name ){

			$total_stats = $db->totalStatsForEventType( intval( $term_id ), intval( $period ) );

			if( $total_stats[ 'total_members' ] ){
				$row = array(
					'label'		=> esc_html( $term_name ),
					'total'		=> intval( $total_stats['total_average'] ),
					'growth'	=> floatval( $total_stats['growth'] )
				);

				$total_data[] = $row['total'];
				array_push( $data, $row );
			}
		}

		array_multisort( $total_data, SORT_DESC, $data );
		return $data;
	}

	function getAnalyticsCallback( WP_REST_Request $args ){

		$period = isset( $args['period'] ) ? intval( $args['period'] ) : 30;

		// Validate period is positive and within reasonable limits
		if ( $period < 1 ) {
			return new WP_Error( 'invalid_period', 'Period must be 1 or greater', array( 'status' => 400 ) );
		}
		if ( $period > 365 ) {
			return new WP_Error( 'invalid_period', 'Period must be 365 days or less', array( 'status' => 400 ) );
		}

		$data = array();

		$db = INPURSUIT_DB::getInstance();

		/*
		* STATS FOR ACTIVE MEMBERS
		*/
		$members_stats = $db->totalStatsForPostType( 'inpursuit-members', $period );
		$data[] = array(
			'label'		=> esc_html( 'Active Members' ),
			'total'		=> intval( $members_stats['total'] ),
			'growth' 	=> floatval( $members_stats['growth'] ),
		);

		/*
		* STATS FOR ARCHIVED MEMBERS
		*/
		$archive_members_stats = $db->totalStatsForPostType( 'inpursuit-members', $period, 'draft' );
		$data[] = array(
			'label'		=> esc_html( 'Archived Members' ),
			'total'		=> intval( $archive_members_stats['total'] ),
			'growth' 	=> floatval( $archive_members_stats['growth'] ),
		);


		/*
		* STATS FOR EACH EVENT TYPE
		*/
		$data = array_merge( $data, $this->getStatsForEventTypes( $period ) );

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'analytics', array( $this, 'getAnalyticsCallback' ), 'is_user_logged_in' );


	}


}
INPURSUIT_REST_ANALYTICS::getInstance();
