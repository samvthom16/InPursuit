<?php
/*
* RELATION TABLE BETWEEN EVENT AND MEMBER
*/

class INPURSUIT_DB_MEMBER extends INPURSUIT_DB_BASE{



	function __construct(){
		//$this->setTableSlug( 'event_member_relation' );
		parent::__construct();

		$this->setPostTypeOptions( array(
			'name' => 'Members',
			'singular_name' => 'Member',
			'slug' => 'inpursuit-members',
			'description' => 'Holds our members and member specific data',
			'menu_icon'	=> 'dashicons-groups',
			'supports'	=> array( 'title', 'thumbnail' )
		) );
	}







}

INPURSUIT_DB_MEMBER::getInstance();
