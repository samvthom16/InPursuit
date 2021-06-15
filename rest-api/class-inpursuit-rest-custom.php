<?php

class INPURSUIT_REST extends INPURSUIT_REST_BASE{

	function getHistoryCallback( WP_REST_Request $args ){
		$event_db = INPURSUIT_DB_EVENT::getInstance();
		$response_data = $event_db->getHistory( $args );

		$response = new WP_REST_Response( $response_data['data'] );
		$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
		$response->header( 'X-WP-Total', $response_data['total'] );

		return $response;
	}

	function getSettingsCallback( WP_REST_Request $args ){
		global $inpursuit_vars;

		$taxonomies = $inpursuit_vars['taxonomies'];
		foreach( $taxonomies as $key => $taxonomy ){
			$taxonomies[$key]['terms'] = get_terms( array(
				'taxonomy' 		=> $taxonomy['slug'],
				'hide_empty' 	=> false,
				'fields'			=> 'id=>name'
			) );
		}

		$data = array(
			'name' 				=> get_bloginfo( 'name' ),
			'taxonomies'	=> $taxonomies
		);

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'history', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'history/(?P<id>\d+)', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'settings', array( $this, 'getSettingsCallback' ) );
	}


}
INPURSUIT_REST::getInstance();