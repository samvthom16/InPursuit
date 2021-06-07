<?php
/*
* Model: EVENT
*/

class INPURSUIT_DB_EVENT extends INPURSUIT_DB_BASE{

	function __construct(){

		parent::__construct();

		$this->setPostType( 'inpursuit-events' );

		$this->setPostTypeOptions( array(
			'name' 					=> 'Events',
			'singular_name' => 'Event',
			'slug' 					=> $this->getPostType(),
			'description' 	=> 'Holds our events specific data',
			'menu_icon'			=> 'dashicons-format-video',
			'supports'			=> array( 'title', 'editor', 'thumbnail' )
		) );

		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}


	function addRestData(){

		register_rest_route( 'inpursuit/v1', '/history/(?P<id>\d+)', array(
    	'methods' => 'GET',
    	'callback' => function( $args ){

				$page = 1;
				$per_page = 10;

				$member_id = $args['id'];
				$post_type = $this->getPostType();

				require_once('class-inpursuit-db-event-member-relation.php');
				$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();

				global $wpdb;
				$subquery = " FROM " . $wpdb->posts . " WHERE ID IN (SELECT event_id FROM " . $event_member_db->getTable() . " WHERE member_id=$member_id) AND post_status='publish' AND post_type='$post_type' ORDER BY post_date DESC";
				$mainquery = "SELECT *" . $subquery . $this->_limit_query( $page, $per_page );
				$countquery = "SELECT count(*)" . $subquery;

				$data = array();
				$rows = $wpdb->get_results( $mainquery );
				$total_count = $wpdb->get_var( $countquery );
				$total_pages = ceil( $total_count/$per_page );
				foreach( $rows as $row ){
					$post = array(
						'title'			=> array( 'rendered' => $row->post_title ),
						'date'			=> $row->post_date,
						'date_gmt'	=> $row->post_date_gmt
					);
					array_push( $data, $post );
				}

				$response = new WP_REST_Response( $data );

				// Add a custom header
				$response->header( 'X-WP-TotalPages', $total_pages );
				$response->header( 'X-WP-Total', $total_count );

				return $response;
			},
			'permission_callback'	=> '__return_true'
  	) );
	}
}
INPURSUIT_DB_EVENT::getInstance();
