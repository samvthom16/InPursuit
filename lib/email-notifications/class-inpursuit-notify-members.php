<?php

class INPURSUIT_NOTIFY_MEMBERS extends INPURSUIT_BASE {

  public function __construct() {
    add_action( 'inpursuit_daily_greeting', [ $this, 'dailyGreetingCb'], 10);
    $this->scheduleJob();
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
