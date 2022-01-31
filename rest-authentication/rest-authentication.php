<?php
/*
Plugin Name: Flutter Authentication
Description: A simple plugin for flutter authentication
Version: 1.0.0
Author: Stephen Anil, Sputznik
Text Domain: flutter-auth
*/

defined( 'ABSPATH' ) or die( 'Hey you cannot access this plugin, you silly human' );

class INPURSUIT_REST_AUTHENTICATION extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'wp_ajax_auth_with_flutter', array( $this, 'authentication' ) );
    add_action( 'wp_ajax_nopriv_auth_with_flutter', array( $this, 'authentication' ) );
    add_action( 'rest_api_init', array( $this, 'signup' ) );

    // ENABLES APPLICATION_PASSWORD SECTION
    add_filter( 'wp_is_application_passwords_available', '__return_true' );

  }

  function authentication(){

    $data = array();

    $username = base64_decode($_REQUEST['ukey']);
    $password = base64_decode($_REQUEST['pkey']);

    if( !empty( $username ) && !empty( $password ) ){

      /*
      $user = wp_signon( array(
        'user_login'    => $username,
        'user_password' => $password
      ) );
      */

      $user = wp_authenticate_username_password( null, $username, $password );

      if( is_wp_error( $user ) ){
        $data = $user;
      }

      else if( class_exists('WP_Application_Passwords') ){
        $app = new WP_Application_Passwords;

        $local_time  = current_datetime();
        $current_time = $local_time->getTimestamp() + $local_time->getOffset();

        $unique_app_name = 'inpursuit_app_'.$current_time;

        list( $new_password, $new_item ) = $app->create_new_application_password( $user->ID, array( 'name'=> $unique_app_name ) );

        // APPLICATION_PASSWORD
        $data['new_password'] = $new_password;

        if( isset( $user->data ) ){
          $data['user'] = $user->data;
        }

      }

    }

    print_r( wp_json_encode( $data ) );

    wp_die();

  }

  /* USER REGISTRATION ENDPOINT */
  function signup(){
    register_rest_route('inpursuit/v1', 'register', array(
      'methods' => 'POST',
      'callback' => array( $this, 'user_registration_callback' ),
      'permission_callback' => '__return_true'
    )	);
  }

  function user_registration_callback( $request = null ) {

  	$response 	= array();
  	$parameters = $request->get_params();
  	$email 			= sanitize_text_field( $parameters['email'] );
  	$username 	= sanitize_text_field( $parameters['username'] );
  	$password 	= sanitize_text_field( $parameters['password'] );

  	$error = new WP_Error();
  	if ( empty( $username ) ) {
  		$error->add( 400, __("Username is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}
  	if ( empty( $email ) ) {
  		$error->add( 401, __("Email is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}
  	if ( empty( $password ) ) {
  		$error->add( 404, __("Password is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

  	$user_id = username_exists( $username );

  	// SHOWS ERROR IF USER ALREADY EXISTS
  	if ( !$user_id && email_exists( $email ) == false ) {
  		$user_id = wp_create_user( $username, $password, $email );
  		if ( !is_wp_error( $user_id ) ) {
  			$user = get_user_by('id', $user_id);

  			// SET USER ROLE
  			$user->set_role('administrator');

  			$response['code'] = 200;
  			$response['message'] = __("User '" . $username . "' Registration was Successful", "wp-rest-user");

  		} else {
  			return $user_id;
  		}
  	} else {
  		$error->add( 406, __("Email/Username already exists", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

  	return new WP_REST_Response( $response, 123 );

  }

}

INPURSUIT_REST_AUTHENTICATION::getInstance();
