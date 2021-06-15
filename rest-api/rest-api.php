<?php

$inc_files = array(
	'class-inpursuit-rest-post-base.php',
	'class-inpursuit-rest-member.php',
	'class-inpursuit-rest-event.php',
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}


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


		register_rest_route( 'inpursuit/v1', '/settings', array(
    	'methods' => 'GET',
    	'callback' => function( WP_REST_Request $args ){

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
			},
			'permission_callback'	=> '__return_true'
  	) );
	}
}
INPURSUIT_REST::getInstance();
