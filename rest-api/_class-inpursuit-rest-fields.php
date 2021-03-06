<?php

class INPURSUIT_REST_FIELDS extends INPURSUIT_BASE{

  function __construct(){
		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}

	function addRestData(){

		$inpursuit_member_type = 'inpursuit-members';
    $inpursuit_event_type = 'inpursuit-events';

		/*
		$member_admin_ui = INPURSUIT_MEMBER_ADMIN_UI::getInstance();
		$taxonomies = $member_admin_ui->getTaxonomiesForDropdown();

		foreach( $taxonomies as $slug => $label ){
			register_rest_field( $inpursuit_member_type, $slug, array(
	        'get_callback'    => array( $this, 'getCallbackForTerm' ),
	        'update_callback'	=> array( $this, 'updateCallbackForTerm' ),
	      )
	    );
		}

		*/

		// CPT INPURSUIT MEMBERS

		/*
    register_rest_field( $inpursuit_member_type, 'group', array(
        'get_callback'    => function( $object, $field_name, $request ){
          return wp_get_object_terms( $object['id'], 'inpursuit-group', array( 'fields' => 'names' ) );
        },
        'update_callback'	=> function( $value, $post, $field_name, $request, $object_type ){
          wp_set_object_terms( $post->ID, $value, 'inpursuit-group' );
        }
      )
    );

    register_rest_field( $inpursuit_member_type, 'profession', array(
        'get_callback'    => function( $object, $field_name, $request ){
          return wp_get_object_terms( $object['id'], 'inpursuit-profession', array( 'fields' => 'names' ) );
        },
        'update_callback'	=> function( $value, $post, $field_name, $request, $object_type ){
          wp_set_object_terms( $post->ID, $value, 'inpursuit-profession' );
        }
      )
    );

    register_rest_field( $inpursuit_member_type, 'member_status', array(
        'get_callback'    => function( $object, $field_name, $request ){
          return wp_get_object_terms( $object['id'], 'inpursuit-status', array( 'fields' => 'names' ) );
        },
        'update_callback'	=> function( $value, $post, $field_name, $request, $object_type ){
          wp_set_object_terms( $post->ID, $value, 'inpursuit-status' );
        }
      )
    );
		*/

    register_rest_field( $inpursuit_member_type, 'location', array(
        'get_callback'    => array( $this, 'get_inpursuit_location' ),
        'update_callback' => array( $this, 'set_inpursuit_location' )
      )
    );

    register_rest_field( $inpursuit_member_type, 'email', array(
        'get_callback'    => array( $this, 'get_inpursuit_post_meta' )
      )
    );

    register_rest_field( $inpursuit_member_type, 'phone', array(
      'get_callback'    => array( $this, 'get_inpursuit_post_meta' )
      )
    );

    register_rest_field( $inpursuit_member_type, 'birthday', array(
      'get_callback'    => array( $this, 'get_inpursuit_events_meta' ),
      'update_callback'	=> array( $this, 'set_inpursuit_events_meta' )
      )
    );

    register_rest_field( $inpursuit_member_type, 'wedding', array(
      'get_callback'    => array( $this, 'get_inpursuit_events_meta' ),
      'update_callback'	=> array( $this, 'set_inpursuit_events_meta' )
      )
    );

    // CPT INPURSUIT EVENTS
    register_rest_field( $inpursuit_event_type, 'event_type', array(
        'get_callback'    => function( $object, $field_name, $request ){
          return wp_get_object_terms( $object['id'], 'inpursuit-event-type', array( 'fields' => 'names' ) );
        },
        'update_callback'	=> function( $value, $post, $field_name, $request, $object_type ){
          wp_set_object_terms( $post->ID, $value, 'inpursuit-event-type' );
        }
      )
    );

    register_rest_field( $inpursuit_event_type, 'location', array(
        'get_callback'    => array( $this, 'get_inpursuit_location' ),
        'update_callback' => array( $this, 'set_inpursuit_location' )
      )
    );


  }

  /* GETTER AND SETTER FUNCTIONS */
  function get_inpursuit_location( $object,  $field_name, $request ){
    return wp_get_object_terms( $object['id'], 'inpursuit-location', array( 'fields' => 'names' ) );
  }

  function set_inpursuit_location( $value, $post, $field_name, $request, $object_type ){
    wp_set_object_terms( $post->ID, $value, 'inpursuit-location' );
  }

  function get_inpursuit_post_meta( $object,  $field_name, $request ){
    return get_post_meta( $object['id'], $field_name, array( 'fields' => 'names' ) );
  }

  function set_inpursuit_post_meta( $value, $post, $field_name, $request, $object_type ){
    update_post_meta( $post->ID, $field_name, $value );
  }

  function get_inpursuit_events_meta( $object,  $field_name, $request ){

    global $wpdb;
    $event_date = $wpdb->get_var( $wpdb->prepare(
        "SELECT event_date FROM {$wpdb->prefix}ip_member_dates WHERE member_id = %d AND event_type = %s ", $object['id'], $field_name
    ) );

    return $event_date;
  }

  function set_inpursuit_events_meta( $value, $post, $field_name, $request, $object_type ){
    update_post_meta( $post->ID, "event_dates[$field_name]", $value );
  }


}

INPURSUIT_REST_FIELDS::getInstance();
