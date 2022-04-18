<?php

class INPURSUIT_REST_OTP_AUTH extends INPURSUIT_BASE{

  function __construct(){

    add_action( 'rest_api_init', function(){
      register_rest_route( 'inpursuit/v1', 'verify', array(
        'methods' 						=> 'POST',
        'callback' 						=> array( $this, 'sendMailOTP' ),
        'permission_callback' => '__return_true'
      )	);

      register_rest_route( 'inpursuit/v1', 'authentication', array(
        'methods' 						=> 'POST',
        'callback'	 					=> array( $this, 'getAuthDetails' ),
        'permission_callback' => '__return_true'
      )	);
    } );
  }

  function sendMailOTP( $request = null ){
    $response = array();
    $parameters = $request->get_params();

    $error = new WP_Error();

    if ( isset( $parameters['email_address'] ) && isset( $parameters['email_otp'] ) ) {

      $email_otp 	    = base64_decode( sanitize_text_field( $parameters['email_otp'] ) );
      $email_address 	= base64_decode( sanitize_text_field( $parameters['email_address'] ) );

      $user = get_user_by( 'email', $email_address );

      if( $user ){
        $message = "Following is your OTP: $email_otp";
        wp_mail( $email_address, 'OTP for INPURSUIT', $message );
        return new WP_REST_Response( array( 'message'=> "Authentication Successful" ), 123 );
      }
      else{
        $error->add( 400, __("Email does not exist.", 'wp-rest-user'), array( 'status' => 400 ) );
        return $error;
      }

    }

    $error->add( 400, __("Something went wrong", 'wp-rest-user'), array( 'status' => 400 ) );
  	return $error;

  }

  function getAuthDetails( $request = null ){
    $parameters = $request->get_params();
    $response = array();

    $error = new WP_Error();

    if( isset( $parameters['email_address'] ) ){
      $email_address 	=  base64_decode( sanitize_text_field( $parameters['email_address'] ) );

      if( !class_exists('WP_Application_Passwords') ){
        $error->add( 400, __("WP Application Passwords is disabled.", 'wp-rest-user'), array( 'status' => 400 ) );
        return $error;
      }

      $user = get_user_by( 'email', $email_address );

      if( !$user ){
        $error->add( 400, __("Email does not exist.", 'wp-rest-user'), array( 'status' => 400 ) );
        return $error;
      }

      $app = new WP_Application_Passwords;
      $local_time  = current_datetime();
      $current_time = $local_time->getTimestamp() + $local_time->getOffset();

      $unique_app_name = 'inpursuit_app_'.$current_time;
      list( $new_password, $new_item ) = $app->create_new_application_password( $user->ID, array( 'name'=> $unique_app_name ) );

      // APPLICATION_PASSWORD
      $response['password'] = $new_password;
      if( isset( $user->data ) ){
        $response['user'] = $user->data;
        return new WP_REST_Response( $response, 123 );
      }
      else{
        $error->add( 400, __("User data missing!", 'wp-rest-user'), array( 'status' => 400 ) );
      }

    }

    $error->add( 400, __("Something went wrong", 'wp-rest-user'), array( 'status' => 400 ) );
    return $error;
  }

}

INPURSUIT_REST_OTP_AUTH::getInstance();
