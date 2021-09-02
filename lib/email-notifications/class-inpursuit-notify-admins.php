<?php

class INPURSUIT_NOTIFY_ADMINS extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'inpursuit_comment_created', array( $this, 'notifyComment' ) );
    add_action( 'inpursuit_members_notified', array( $this, 'notifyGreeting' ) );
  }

  // SEND EMAIL TO ALL THE ADMINSTRATORS
  function notifyComment( $comment_details ){
    $member_name = get_post_field( 'post_title', $comment_details['post_id'] );
    $commentor_name = get_the_author_meta( 'display_name', $comment_details['user_id'] );
    $comment_body = $comment_details['comment']; // Comment body

    ob_start();
    include( 'templates/comment.php' );
    $body = ob_get_contents();
    ob_end_clean();

    $this->sendEmail( $this->getEmailsOfAdmins(), 'Comment Notification', $body );
  }

  function notifyGreeting( $greeting ){
    ob_start();
    include( 'templates/greeting.php' );
    $body = ob_get_contents();
    ob_end_clean();

    $this->sendEmail( $this->getEmailsOfAdmins(), 'Greeting Notification', $body );
  }

  function sendEmail( $to, $subject, $body ){
    $subject .= " From " . get_bloginfo( 'name' );
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
