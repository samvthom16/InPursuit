<?php

class INPURSUIT_REST_MEMBER extends INPURSUIT_REST_POST_BASE{

	function __construct(){

		$post_type = INPURSUIT_MEMBERS_POST_TYPE;
		$this->setPostType( $post_type );

		$this->setFieldNames( array(
			'inpursuit-status'			=> 'member_status',
			'inpursuit-gender'			=> 'gender',
			'inpursuit-group'				=> 'group',
			'inpursuit-location'		=> 'location',
			'inpursuit-profession'	=> 'profession'
		) );

		$this->setAdminUI( INPURSUIT_MEMBER_ADMIN_UI::getInstance() );

		add_filter( 'rest_inpursuit-members_query', array( $this, 'filterRestData' ), 10, 2 );

		// ADD TAXONOMIES THAT ARE NOT DROPDOWN TO BE MADE AVAILABLE ON THE REST DATA
		add_filter( "inpursuit-rest-taxonomies-$post_type", function( $taxonomies ){
			$taxonomies['inpursuit-profession'] = 'Profession';
			return $taxonomies;
		} );

		parent::__construct();
	}



	// SHOW EVENT RELATED DATA
	// OPTION TO SHOW ONLY ATTENDANTS
	function filterRestData( $args, $request ){
		$event_id = $request->get_param( 'event_id' );
		$show_flag = $request->get_param( 'show_event_attendants' );

		//$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		if( $event_id && $show_flag == 1 ){
			$member_db = INPURSUIT_DB_MEMBER::getInstance();
			$args['post__in'] = $member_db->getIDsForEvent( $event_id );
		}

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

		// AUTHOR AGE
		$this->registerRestField(
			'age',
			function( $post, $field_name, $request ){
				$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
				$age = $member_dates_db->age( $post['id'] );
				return $age;
			}
		);

		// AUTHOR AGE
		$this->registerRestField(
			'last_seen',
			function( $post, $field_name, $request ){

				$event_db 			= INPURSUIT_DB::getInstance();
				$response_data 	= $event_db->getHistory( array(
					'id' 				=> $post['id'],
					'per_page' 	=> 1
				) );

				if(
					isset( $response_data['data'] ) &&
					is_array( $response_data['data'] ) &&
					count( $response_data['data'] ) &&
					isset( $response_data['data'][0]->post_date )
				)
					return get_date_from_gmt( $response_data['data'][0]->post_date );

				return '';
			}
		);

		// ATTENDED BOOLEAN FIELD
		$this->registerRestField(
			'attended',
			function( $post, $field_name, $request ){
				$member_db = INPURSUIT_DB_MEMBER::getInstance();
				$event_id = $request->get_param( 'event_id' );
				$members_id_arr = $member_db->getIDsForEvent( $event_id );
				if( count( $members_id_arr ) && in_array( $post['id'], $members_id_arr ) ) return true;
				return false;
			},
			function( $value, $post, $field_name, $request ){
				$member_db = INPURSUIT_DB_MEMBER::getInstance();
				$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();

				$event_id = $request->get_param( 'event_id' );
				if( $event_id > 0 ){

					// DELETE IF THERE ARE ANY PREVIOUS ENTRIES
					$event_member_db->delete( array(
						'event_id' 	=> $event_id,
						'member_id' => $post->ID
					) );

					if( $value ){
						// ADD AN ENTRY
						$event_member_db->insert( array(
							'event_id' 	=> $event_id,
							'member_id' => $post->ID
						) );
					}

				}
			}
		);

		// SPECIAL EVENTS
		$this->registerRestField(
			'special_events',
			function( $post, $field_name, $request ){

				global $wpdb;

				$special_events = array(
					'wedding'  		=> '',
					'birthday'    => ''
				);

				foreach ( $special_events as $event => $value	) {

					$event_date = $wpdb->get_var( $wpdb->prepare(
							"SELECT DATE(event_date) FROM {$wpdb->prefix}ip_member_dates WHERE member_id = %d AND event_type = %s ", $post['id'], $event
					) );

					$special_events[$event]	=	$event_date;

				}

				return $special_events;

			},
			function( $value, $post, $field_name, $request ){
				$params = $request->get_params();
				if(	array_key_exists("special_events", $params)	) {
					$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
					$member_dates_db->updateToMember( $post->ID, $params["special_events"] );
				}
			}
		);

	}

	

}

INPURSUIT_REST_MEMBER::getInstance();
