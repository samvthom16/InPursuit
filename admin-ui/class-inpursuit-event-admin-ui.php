<?php

class INPURSUIT_EVENT_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	function __construct(){
		$this->setPostType( 'inpursuit-events' );

		$this->setMetaBoxes( array(
			array(
				'id'				=> 'inpursuit-event-members',
				'title'			=> 'Members',
				'supports'	=>	array('editor')
			),
		) );



		parent::__construct();
	}

	

}

INPURSUIT_EVENT_ADMIN_UI::getInstance();
