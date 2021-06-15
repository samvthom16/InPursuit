<?php
/*
* RELATION TABLE BETWEEN EVENT AND MEMBER
*/

class INPURSUIT_DB_MEMBER extends INPURSUIT_DB_POST_BASE{

	function __construct(){

		parent::__construct();

		$this->setPostType( INPURSUIT_MEMBERS_POST_TYPE );

		$this->setPostTypeOptions( array(
			'name' 					=> 'Members',
			'singular_name' => 'Member',
			'slug' 					=> $this->getPostType(),
			'description' 	=> 'Holds our members and member specific data',
			'menu_icon'			=> 'dashicons-groups',
			'supports'			=> array( 'title', 'thumbnail', 'author' )
		) );

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


}

INPURSUIT_DB_MEMBER::getInstance();
