<?php

class INPURSUIT_REST extends INPURSUIT_BASE{

	function __construct(){
		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}
	
	function addRestData(){

		register_rest_route( 'inpursuit/v1', '/history/(?P<id>\d+)', array(
    	'methods' => 'GET',
    	'callback' => function( WP_REST_Request $args ){

				$event_db = INPURSUIT_DB_EVENT::getInstance();
				$response_data = $event_db->getHistory( $args );

				$response = new WP_REST_Response( $response_data['data'] );
				$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
				$response->header( 'X-WP-Total', $response_data['total'] );

				return $response;
			},
			'permission_callback'	=> '__return_true'
  	) );

		register_rest_route( 'inpursuit/v1', '/history', array(
    	'methods' => 'GET',
    	'callback' => function( WP_REST_Request $args ){

				$event_db = INPURSUIT_DB_EVENT::getInstance();
				$response_data = $event_db->getHistory( $args );

				$response = new WP_REST_Response( $response_data['data'] );
				$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
				$response->header( 'X-WP-Total', $response_data['total'] );

				return $response;
			},
			'permission_callback'	=> '__return_true'
  	) );
	}
}
INPURSUIT_REST::getInstance();
