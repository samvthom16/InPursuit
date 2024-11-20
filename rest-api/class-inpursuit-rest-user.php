<?php

class INPURSUIT_REST_USER extends INPURSUIT_BASE{
    function __construct(){
		add_action( 'rest_api_init', array( $this, 'addUserCustomFields' ) );
	}
    function addUserCustomFields(){
        register_rest_field(
            'user',
            'limit_access',
            array(
                'get_callback'    => array( $this, 'get_limit_access_field' ),
                'update_callback' => array( $this, 'update_limit_access_field' )
            )
        );
    }

    function get_limit_access_field($user, $field_name, $request) {
        $user_db = INPURSUIT_DB_USER::getInstance();

        $selected_groups = $user_db->getLimitedGroups( $user['id'] );
        return $selected_groups;
    }
    
    
    function update_limit_access_field($value, $user, $field_name) {

        $user_db = INPURSUIT_DB_USER::getInstance();
		$taxonomy = $user_db->getTaxonomy();

		if( is_array( $value ) ){
			return update_user_meta( $user->ID, $taxonomy, $value );
		}
        return false;
    }
}

INPURSUIT_REST_USER::getInstance();