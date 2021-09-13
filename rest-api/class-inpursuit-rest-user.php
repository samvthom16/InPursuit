<?php

class INPURSUIT_REST_USER extends INPURSUIT_REST_POST_BASE{

	function __construct(){

		$post_type = 'user';
		$this->setPostType( $post_type );

		parent::__construct();
	}

	function addRestData(){

		// LIMIT USER ACCESS
		$this->registerRestField(
			'group',
			function( $post, $field_name, $request ){
				$user_db = INPURSUIT_DB_USER::getInstance();
				$selected_groups = $user_db->getLimitedGroups( $post['id'] );
				return $selected_groups;
			},
			function( $value, $post, $field_name, $request ){

				// CONVERT ARRAY OF INTEGER(S) TO ARRAY OF STRING(S)
				$meta_value = array_map( function( $el ) { return (string) $el; }, $value );

				if( $post->ID > 0 ){
					update_user_meta( $post->ID, 'inpursuit-group', $meta_value );
				}
			},
			array(
       'description'   => 'Limit User Access',
 			 'type'          => 'array',
 			 'context'       =>  array( 'view', 'edit' )
      )

		);

	}

}

INPURSUIT_REST_USER::getInstance();
