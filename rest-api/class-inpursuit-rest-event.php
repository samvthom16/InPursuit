<?php

class INPURSUIT_REST_EVENT extends INPURSUIT_REST_POST_BASE{

	function __construct(){

		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setAdminUI( INPURSUIT_EVENT_ADMIN_UI::getInstance() );

		$this->setFieldNames( array(
			'inpursuit-event-type' => 'event_type',
			'inpursuit-location'		=> 'location',
		) );

		add_filter( 'rest_inpursuit-events_query', array( $this, 'filterRestData' ), 10, 2 );

		parent::__construct();
	}

	function filterRestData( $args, $request ){
		$args['tax_query'] = array();
		$field_names = $this->getFieldNames();
		foreach( $field_names as $taxonomy => $new_field ){
			$term_id = $request->get_param( $new_field );
			if( $term_id ){
				array_push( $args['tax_query'], array(
					'taxonomy' => $taxonomy,
					'terms'    => $term_id,
				) );
			}
		}
		return $args;
	}

	function addRestData(){
		parent::addRestData();

		$this->registerRestField(
			'attendants_percentage',
			function( $post, $field_name, $request ){
				$event_db = INPURSUIT_DB_EVENT::getInstance();
				return $event_db->attendantsPercentage( $post['id'] );
			}
		);

	}
}

INPURSUIT_REST_EVENT::getInstance();
