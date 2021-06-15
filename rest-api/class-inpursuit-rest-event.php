<?php

class INPURSUIT_REST_EVENT extends INPURSUIT_REST_POST_BASE{

	function __construct(){

		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setAdminUI( INPURSUIT_EVENT_ADMIN_UI::getInstance() );

		parent::__construct();
	}

}

INPURSUIT_REST_EVENT::getInstance();
