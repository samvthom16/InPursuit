<?php

class INPURSUIT_NOTIFY_ADMINS extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'inpursuit_comment_created', array( $this, 'notifyComment' ) );
    add_action( 'inpursuit_members_notified', array( $this, 'notifyGreeting' ) );
    add_action( 'inpursuit_comment_updated', array( $this, 'notifyCommentUpdate' ) );
    add_action( 'inpursuit_comment_rescheduled', array( $this, 'notifyCommentReschedule' ) );
    add_action( 'inpursuit_comment_deleted', array( $this, 'notifyCommentDelete' ) );
  }

  function notifyCommentDelete( $comment_details ){
    $this->notify( $comment_details, 'deleted', get_bloginfo( 'name' )." has deleted the comment" );
  }

  function notifyCommentReschedule( $comment_details ){
    $this->notify( $comment_details, 'rescheduled', get_bloginfo( 'name' )." has rescheduled the comment");
  }

  function notifyCommentUpdate( $comment_details ){
    $this->notify( $comment_details, 'updated', get_bloginfo( 'name' )." has updated the comment" );
  } 

  function notifyComment( $comment_details ){
    $this->notify( $comment_details, 'comment', "Comment Notification From " . get_bloginfo( 'name' ));
  }

  function notifyGreeting( $greeting ){
    $this->notify( $greeting, 'greeting',"Greeting Notification From " . get_bloginfo( 'name' ) );
  }

  function notify( $data, $template, $subject ){
    ob_start();
    include( "templates/$template.php" );
    $body = ob_get_contents();
    ob_end_clean();
    $this->sendEmail( $this->getEmailsOfAdmins(), $subject, $body );
  }

  function sendEmail( $to, $subject, $body ){
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
