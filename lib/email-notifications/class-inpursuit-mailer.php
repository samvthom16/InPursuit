<?php

class INPURSUIT_MAILER extends INPURSUIT_BASE {

  var $setting_args;

  public function __construct() {
    add_action( 'inpursuit_cron_email', [ $this, 'cronEmailCb'], 10, 4 );

    $this->setSettingArgs( array(
      'page'    => 'inpursuit-email-fields',
      'section' => 'inpursuit_email_field_section',
      'args'    => array(
        'inpursuit_settings_email_from_name'    => array(
          'label'       => 'Email From Name',
          'placeholder' => ''
        ),
        'inpursuit_settings_email_from_address' => array(
          'label'       => 'Email From Address',
          'placeholder' => 'name@email.com'
        ),
        'inpursuit_settings_cron_time'          => array(
          'label'       => 'Time To Schedule Email',
          'placeholder' => 'HH:MM:SS'
        )
      )
    ) );

    $this->setupAdmin();
  }

  function getSettingArgs(){ return $this->setting_args; }
  function setSettingArgs( $setting_args ){ $this->setting_args = $setting_args; }

  function setupAdmin(){

    // ADD TABS IN THE ADMIN SETTINGS
    add_filter( 'inpursuit_settings_tabs', function( $tabs ){
      $settings = $this->getSettingArgs();
      array_push( $tabs, array(
        'slug' 			    => $settings['page'],
				'title'			    => 'Email Settings',
				'section-page' 	=> $settings['page']
      ) );
      return $tabs;
    } );

    // ADD SECTIONS IN THE ADMIN SETTINGS
    add_filter( 'inpursuit_settings_sections', function( $sections ){
      $settings = $this->getSettingArgs();
      array_push( $sections, array(
        'section-id' 	      => $settings['section'],
				'section-title'     => '',
				'section-callback'  => '',
				'page-slug'		      => $settings['page']
      ) );
      return $sections;
    } );

    // ADD SETTING ARGS IN THE ADMIN SETTINGS
    add_filter( 'inpursuit_settings_args', function( $settings_args ){

      $settings = $this->getSettingArgs();
      foreach( $settings['args'] as $slug => $atts ){
        $temp_setting_args = array(
          'page-slug' 		=> $settings['page'],
  				'setting-name' 	=> $slug,
  			  'type-args' 		=>  array(
  					'type' 							=> 'string',
  					'sanitize_callback' => 'sanitize_text_field',
  					'default' 					=> ''
  				)
        );
        array_push( $settings_args, $temp_setting_args );
      }
      return $settings_args;
    } );

    add_filter( 'inpursuit_settings_fields_args', function( $settings_fields_args ){

      $admin_settings = INPURSUIT_ADMIN_SETTINGS::getInstance();

      $settings = $this->getSettingArgs();
      foreach( $settings['args'] as $slug => $atts ){
        array_push( $settings_fields_args, array(
          'setting-name' 		=> $slug,
  				'field-title'  		=> $atts['label'],
  				'field-callback' 	=> array( $admin_settings, 'textFieldCb' ),
  				'page-slug'	   		=> $settings['page'],
  				'section-id'   		=> $settings['section'],
  				'field-args' 			=> [ 'label_for' => $slug, 'placeholder' => $atts['placeholder'] ],
        ) );
      }
      return $settings_fields_args;
    } );

  }

  /*
  *  action hook cb to be used in scheduling cron email events
  */
	function cronEmailCb( $to, $subject, $body, $header ){ wp_mail( $to, $subject, $body, $header ); }

  /*
  * Use this function when asyncronus wp_mail behaviour is required
  * Schedules each outgoing email as a cron job
  */
	function sendEmail( $to, $subject, $body, $header = array() ){

    // DEFAULT HEADER INFORMATION
    if( count( $header ) == 0 ){
      $from_name = get_option( 'inpursuit_settings_email_from_name' );
      $from_mail = get_option( 'inpursuit_settings_email_from_address' );

      $header = array(
        'Content-Type: text/html; charset=ISO-8859-1',
        "From: $from_name <$from_mail>"
      );
    }

		$args = array(
      'to'		  => $to,
      'subject'	=> $subject,
      'body'		=> $body,
      'header'	=> $header
    );

		if(!wp_next_scheduled( 'inpursuit_cron_email' )){
			wp_schedule_single_event( time(), 'inpursuit_cron_email', $args );
		}
	}
}

INPURSUIT_MAILER::getInstance();
