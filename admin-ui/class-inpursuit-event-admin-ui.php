<?php

class INPURSUIT_EVENT_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	function __construct(){
		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setMetaBoxes( array(
			array(
				'id'				=> 'inpursuit-event-members',
				'title'			=> 'Members',
				'supports'	=>	array('editor')
			),
		) );

		$this->setTaxonomiesForDropdown( array(
			'inpursuit-event-type' 	=> 'Event Type',
			'inpursuit-location' 		=> 'Location',
		) );

		parent::__construct();
	}



}

INPURSUIT_EVENT_ADMIN_UI::getInstance();
