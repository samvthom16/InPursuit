<?php
/*
* RELATION TABLE BETWEEN EVENT AND MEMBER
*/

class INPURSUIT_DB_MEMBER extends INPURSUIT_DB_BASE{



	function __construct(){

		parent::__construct();

		$this->setPostType( 'inpursuit-members' );

		$this->setPostTypeOptions( array(
			'name' 					=> 'Members',
			'singular_name' => 'Member',
			'slug' 					=> $this->getPostType(),
			'description' 	=> 'Holds our members and member specific data',
			'menu_icon'			=> 'dashicons-groups',
			'supports'			=> array( 'title', 'thumbnail' )
		) );

		add_filter( 'rest_inpursuit-members_query', array( $this, 'filterRestData' ), 10, 2 );

		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}

	function filterRestData( $args, $request ){
		$event_id = $request->get_param( 'event_id' );
		$show_flag = $request->get_param( 'show_event_attendants' );

		//$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();

		if( $event_id && $show_flag == 1 ){
			//$members_id_arr = $event_member_db->getMembersIDForEvent( $event_id );
			//if( is_array( $members_id_arr ) && count( $members_id_arr ) ){
			//	$args['post__in'] = $members_id_arr;
			//}

			$args['post__in'] = $this->getIDsForEvent( $event_id );



		}

		$args['order_by'] = 'post_title';
		$args['order'] = 'asc';

		return $args;
	}

	// OONLY GET MEMBER IDS FOR A PARTICULAR EVENT
	function getIDsForEvent( $event_id ){
		$members_id_arr = array();
		if( $event_id > 0 ){
			$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
			$rows = $event_member_db->getMembersIDForEvent( $event_id );
			if( is_array( $rows ) && count( $rows ) ){
				$members_id_arr = $rows;
			}
		}
		return $members_id_arr;
	}


	function addRestData(){
		register_rest_field(
			$this->getPostType(),
			'attended',
			array(
    		'get_callback'    => function( $post, $field_name, $request ){
					$event_id = $request->get_param( 'event_id' );
					$members_id_arr = $this->getIDsForEvent( $event_id );
					if( count( $members_id_arr ) && in_array( $post['id'], $members_id_arr ) ) return true;
					return false;
				},
    		'update_callback' => function( $value, $post, $field_name, $request ){
					$event_id = $request->get_param( 'event_id' );
					if( $event_id > 0 ){
						
						$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();

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
				},
    		'schema'          => null,
     	)
		);
	}




}

INPURSUIT_DB_MEMBER::getInstance();
