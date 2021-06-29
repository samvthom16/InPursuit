<?php

class INPURSUIT_REST_BASE extends INPURSUIT_BASE{

	function __construct(){
		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}

	function registerRoute( $route, $callback, $permission_callback = '__return_true' ){
		register_rest_route( 'inpursuit/v1', '/' . $route, array(
    	'methods' => 'GET',
    	'callback' => $callback,
			'permission_callback'	=> $permission_callback
  	) );
	}

	// WRAPPER FUNCTION TO REGISTER REST FIELD
	function registerRestField( $field_name, $get_callback, $update_callback = '__return_false', $schema = null ){
		$field_name = apply_filters( 'inpursuit_rest_field', $field_name );
		register_rest_field(
			$this->getPostType(),
			$field_name,
			array(
    		'get_callback'    => $get_callback,
    		'update_callback' => $update_callback,
    		'schema'          => $schema,
     	)
		);
	}

}
