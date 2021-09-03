<?php

class INPURSUIT_NOTIFY_MEMBERS extends INPURSUIT_BASE {

  var $setting_args;

  public function __construct() {
    add_action( 'inpursuit_daily_greeting', [ $this, 'dailyGreetingCb'], 10);
    $this->scheduleJob();

    $this->setSettingArgs( array(
      'page'    => 'inpursuit-email-templates',
      'section' => 'inpursuit_email_template_section',
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
				'title'			    => 'Email Templates',
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

      // ADD EVENT DATE TYPES
  		$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
  		$event_types = $member_dates_db->getEventTypes();

      foreach( $event_types as $event_slug => $event_title ){
        $setting_name = 'inpursuit_settings_subject_' . $event_slug;
  			array_push( $settings_args, array(
  				'page-slug' 		=> $settings['page'],
  				'setting-name' 	=> $setting_name,
  			  'type-args' 		=> array(
  					'type' 							=> 'string',
  					'sanitize_callback' => 'sanitize_textarea_field',
  					'default' 					=> ''
  				)
  			) );

        $setting_name = 'inpursuit_settings_template_' . $event_slug;
  			array_push( $settings_args, array(
  				'page-slug' 		=> $settings['page'],
  				'setting-name' 	=> $setting_name,
  			  'type-args' 		=> array(
  					'type' 							=> 'string',
  					'sanitize_callback' => 'sanitize_textarea_field',
  					'default' 					=> ''
  				)
  			) );
      }

      return $settings_args;
    } );

    add_filter( 'inpursuit_settings_fields_args', function( $settings_fields_args ){
      $settings = $this->getSettingArgs();

      // ADD EVENT DATE TYPES
  		$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
      $admin_settings = INPURSUIT_ADMIN_SETTINGS::getInstance();
  		$event_types = $member_dates_db->getEventTypes();

      foreach( $event_types as $event_slug => $event_title ){
        $setting_name = 'inpursuit_settings_subject_' . $event_slug;
        array_push( $settings_fields_args, array(
  				'setting-name' 		=> $setting_name,
  				'field-title' 	 	=> $event_title . ' Email Subject',
  				'field-callback' 	=> [ $admin_settings, 'textFieldCb' ],
  				'page-slug'	   		=> $settings['page'],
  				'section-id'   		=> 'inpursuit_email_template_section',
  				'field-args' 			=> [ 'label_for' => $setting_name ],
  			) );

        $setting_name = 'inpursuit_settings_template_' . $event_slug;
        array_push( $settings_fields_args, array(
  				'setting-name' 		=> $setting_name,
  				'field-title' 	 	=> $event_title . ' Email Template',
  				'field-callback' 	=> [ $admin_settings, 'textareaFieldCb' ],
  				'page-slug'	   		=> $settings['page'],
  				'section-id'   		=> 'inpursuit_email_template_section',
  				'field-args' 			=> [ 'label_for' => $setting_name ],
  			) );
      }

      return $settings_fields_args;
    } );

  }

  /*
  * Schedule daily cron job for greetings email
  */
  public function scheduleJob(){
    if( !wp_next_scheduled( 'inpursuit_daily_greeting' ) ){
      $settings_time = get_option('inpursuit_settings_cron_time');
      if( $settings_time == '' ) {
        $timestamp = current_time( 'timestamp' );
      }
      else {
        $date_time = date('Y-m-d') . ' ' .$settings_time . ' +05:30';
        $dt = new DateTime($date_time);
        $timestamp = $dt->getTimestamp();
      }
      wp_schedule_event( $timestamp, 'daily', 'inpursuit_daily_greeting' );
    }
  }

  /*
  * action hook callback to be used in scheduling greetings
  */
  public function dailyGreetingCb(){ $this->scheduleGreetings(); }

  public function scheduleGreetings() {
    $member_model = INPURSUIT_DB_MEMBER_DATES::getInstance();
    $members_events = $member_model->getMembersEventForToday();
    foreach ( $members_events as $event => $members ) {
      $this->sendGreetings($members, $event);
    }
  }

  public function sendGreetings( $members, $event ){
    $mailer = INPURSUIT_MAILER::getInstance();
    foreach ( $members as $member ) {
      $greeting = $this->prepareGreeting($member, $event);
      if( isset( $greeting['to'] ) ) {
        $mailer->sendEmail( $greeting['to'], $greeting['subject'], $greeting['body'] );
        do_action( 'inpursuit_members_notified', $greeting );
      }
    }
  }

  public function prepareGreeting( $member, $event ){

    // GET MEMBER EMAIL ADDRESS
    $member_meta = get_post_meta( $member->member_id );
    $member_email = $member_meta['email'][0];

    // GET TEMPLATE BODY AND SUBJECT FROM THE SETTINGS SECTION
    $template = get_option( 'inpursuit_settings_template_' . strtolower( $event ) );
    $subject = get_option( 'inpursuit_settings_subject_' . strtolower( $event ) );

    // exit with empty array if member doesn't has email address
    if( ( $member_email == '' ) || ( $template == '' ) || ( $subject == '' ) ) return [];

    $member_name = get_the_title( $member->member_id );
    $template_vars = [ '$name' => $member_name ];
    $body = strtr( $template, $template_vars );
    $body = str_replace( "\r\n", "<br />", $body );

    $greeting = [
      'to'        => $member_email,
      'subject'   => $subject,
      'body'      => $body,
    ];
    return $greeting;
  }
}

INPURSUIT_NOTIFY_MEMBERS::getInstance();
