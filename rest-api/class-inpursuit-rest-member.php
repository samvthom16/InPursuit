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

		// Validate event_id if provided
		if( $event_id ) {
			$event_id = intval( $event_id );
			if( $event_id <= 0 ) {
				$event_id = null;
			}
		}

		// Validate show_event_attendants (should be 0 or 1)
		if( $show_flag ) {
			$show_flag = intval( $show_flag );
			if( $show_flag != 1 ) {
				$show_flag = 0;
			}
		}

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
				// Validate term_id is a positive integer
				$term_id = intval( $term_id );
				if( $term_id > 0 ) {
					array_push( $args['tax_query'], array(
						'taxonomy' => $taxonomy,
						'terms'    => $term_id,
					) );
				}
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

		// LAST ATTENDED EVENT DATE
		$this->registerRestField(
			'last_seen',
			function( $post, $field_name, $request ){
				global $wpdb;
				$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
				$relation_table  = $event_member_db->getTable();
				$posts_table     = $wpdb->posts;

				$post_date = $wpdb->get_var( $wpdb->prepare(
					"SELECT p.post_date FROM $posts_table p
					INNER JOIN $relation_table emr ON p.ID = emr.event_id
					WHERE emr.member_id = %d
					AND p.post_status = 'publish'
					AND p.post_type = %s
					ORDER BY p.post_date DESC
					LIMIT 1",
					intval( $post['id'] ),
					INPURSUIT_EVENTS_POST_TYPE
				) );

				return $post_date ? esc_html( get_date_from_gmt( $post_date ) ) : '';
			}
		);

		// ATTENDED BOOLEAN FIELD
		$this->registerRestField(
			'attended',
			function( $post, $field_name, $request ){
				$member_db = INPURSUIT_DB_MEMBER::getInstance();
				$event_id = $request->get_param( 'event_id' );

				// Validate event_id is a positive integer
				if( $event_id ) {
					$event_id = intval( $event_id );
					if( $event_id <= 0 ) {
						return false;
					}
				} else {
					return false;
				}

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
							"SELECT DATE(event_date) FROM {$wpdb->prefix}ip_member_dates WHERE member_id = %d AND event_type = %s ", intval( $post['id'] ), sanitize_key( $event )
					) );

					$special_events[ $event ]	=	$event_date ? esc_html( $event_date ) : '';

				}

				return $special_events;

			},
			function( $value, $post, $field_name, $request ){
				$params = $request->get_params();
				if(	array_key_exists("special_events", $params)	) {
					$special_events = $params["special_events"];

					// Validate special_events is an array
					if( !is_array( $special_events ) ) {
						return;
					}

					// Validate each event type and date
					$valid_event_types = array( 'birthday', 'wedding' );
					$validated_events = array();

					foreach( $special_events as $event_type => $event_date ) {
						// Validate event type is allowed
						if( !in_array( $event_type, $valid_event_types ) ) {
							continue;
						}

						// Validate date format (YYYY-MM-DD or similar valid date)
						if( !empty( $event_date ) && !strtotime( $event_date ) ) {
							continue;
						}

						$validated_events[ $event_type ] = $event_date;
					}

					// Only update if there are valid events
					if( !empty( $validated_events ) ) {
						$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
						$member_dates_db->updateToMember( $post->ID, $validated_events );
					}
				}
			}
		);

	}

	

}

INPURSUIT_REST_MEMBER::getInstance();
