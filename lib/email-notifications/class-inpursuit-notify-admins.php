<?php

class INPURSUIT_NOTIFY_ADMINS extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'inpursuit_comment_created', array( $this, 'notifyComment' ) );
    add_action( 'inpursuit_members_notified', array( $this, 'notifyGreeting' ) );
  }

  function notifyComment( $comment_details ){
    $this->notify( $comment_details, 'comment' );
  }

  function notifyGreeting( $greeting ){
    $this->notify( $greeting, 'greeting' );
  }

  function notify( $data, $template ){
    ob_start();
    include( "templates/$template.php" );
    $body = ob_get_contents();
    ob_end_clean();
    $this->sendEmail( $this->getEmailsOfAdmins(), $template, $body );
  }

  function sendEmail( $to, $subject, $body ){
    $subject .= " Notification From " . get_bloginfo( 'name' );
    $mailer = INPURSUIT_MAILER::getInstance();
    return $mailer->sendEmail( $to, $subject, $body );
  }

  function getEmailsOfAdmins(){
    $user_emails = array();

    // QUERY FOR ADMINSTRATORS
    $admins = get_users( array(
      'fields'    => array( 'user_email' ),
      'role__in'  => array('administrator'),
    ) );

    foreach ( $admins as $admin ) {
      array_push( $user_emails, $admin->user_email );
    }

    return $user_emails;
  }

}

INPURSUIT_NOTIFY_ADMINS::getInstance();
