<?php

class INPURSUIT_MEMBER_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	var $metafields;

	function __construct(){
		$this->setPostType( 'inpursuit-members' );

		add_action( 'save_post', array( $this, 'saveEventDates' ), 10,3 );

		$this->setMetaBoxes( array(
			array(
				'id'		=> 'inpursuit-member-info',
				'title'		=> 'Additional Information',
				'context'	=> 'side'
			),
			array(
				'id'		=> 'inpursuit-member-history',
				'title'		=> 'History',
				'supports'	=>	array('editor')
			),
		) );

		$this->setMetaFields( array(
			'email'	=> 'Email Address',
			'phone'	=> 'Phone Number'
		) );

		$this->setTaxonomiesForDropdown( array(
			'inpursuit-status' 		=> 'Status',
			'inpursuit-gender' 		=> 'Gender',
			'inpursuit-group' 		=> 'Life Group',
			'inpursuit-location' 	=> 'Location',
		) );

		parent::__construct();
	}

	function getMetaFields(){ return $this->metafields; }
	function setMetaFields( $metafields ){ $this->metafields = $metafields; }

	/*
	function getTaxonomiesForDropdown(){
		return array(
			'inpursuit-status' 		=> 'Status',
			'inpursuit-gender' 		=> 'Gender',
			'inpursuit-group' 		=> 'Life Group',
			'inpursuit-location' 	=> 'Location',
		);
	}
	*/





	

	function saveEventDates( $post_id, $post, $update ){

		if( $post->post_type == $this->getPostType() && isset( $_POST['event_dates'] ) && count( $_POST['event_dates'] ) ){
			$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
			$member_dates_db->updateToMember( $post_id, $_POST['event_dates'] );
		}

		if( $post->post_type == $this->getPostType() && $_POST ){
			$metafields = $this->getMetaFields();
			foreach( $metafields as $slug => $title ){
				update_post_meta( $post_id, $slug, $_POST[ $slug ] );
			}
		}
	}


}

INPURSUIT_MEMBER_ADMIN_UI::getInstance();
