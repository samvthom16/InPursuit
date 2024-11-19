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

        register_rest_field('user', 'inpursuit_group_terms', [
            'get_callback'    => array($this, 'get_inpursuit_group_terms_for_user'),
        ]);
    }

    function get_limit_access_field($user, $field_name, $request) {
        $value = get_user_meta($user['id'], $field_name, true);
        return !empty($value) ? $value : [];
    }
    
    function update_limit_access_field($value, $user, $field_name) {
        if (is_array($value)) {
            update_user_meta($user->ID, $field_name, $value);
        }
        return true;
    }

    function get_inpursuit_group_terms_for_user($user) {
        $terms = get_terms([
            'taxonomy' => 'inpursuit-group',
            'hide_empty' => false,
        ]);
    
        $term_data = [];
        foreach ($terms as $term) {
            $term_data[] = [
                'id' => $term->term_id,
                'name' => $term->name,
            ];
        }
    
        return $term_data;
    }
}

INPURSUIT_REST_USER::getInstance();