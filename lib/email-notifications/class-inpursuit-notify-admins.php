<?php

class INPURSUIT_NOTIFY_ADMINS extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'inpursuit_comment_created', array( $this, 'notifyComment' ) );
    add_action( 'inpursuit_members_notified', array( $this, 'notifyGreeting' ) );
    add_action( 'rest_after_insert_' . INPURSUIT_MEMBERS_POST_TYPE, array( $this, 'onMemberCreated' ), 10, 3 );
    add_action( 'rest_after_insert_' . INPURSUIT_EVENTS_POST_TYPE, array( $this, 'onEventCreated' ), 10, 3 );
  }

  function onMemberCreated( $post, $request, $creating ){
    if ( ! $creating ) return;
    $data = array(
      'post_id'    => $post->ID,
      'post_title' => $post->post_title,
      'post_date'  => $post->post_date,
      'edit_url'   => admin_url( 'post.php?action=edit&post=' . $post->ID ),
    );
    $this->notify( $data, 'new-member', 'New Member Added' );
  }

  function onEventCreated( $post, $request, $creating ){
    if ( ! $creating ) return;
    $data = array(
      'post_id'    => $post->ID,
      'post_title' => $post->post_title,
      'post_date'  => $post->post_date,
      'edit_url'   => admin_url( 'post.php?action=edit&post=' . $post->ID ),
    );
    $this->notify( $data, 'new-event', 'New Event Created' );
  }

  function notifyComment( $comment_details ){
    $this->notify( $comment_details, 'comment' );
  }

  function notifyGreeting( $greeting ){
    $this->notify( $greeting, 'greeting' );
  }

  function notify( $data, $template, $subject = null ){
    ob_start();
    include( "templates/$template.php" );
    $body = ob_get_contents();
    ob_end_clean();
    $this->sendEmail( $this->getEmailsOfAdmins(), $subject ?? $template, $body );
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
